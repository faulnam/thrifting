<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Services\HtmlSanitizer;
use App\Services\ImageOptimizer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BlogPostController extends Controller
{
    public function index(Request $request): View
    {
        $query = BlogPost::query()->latest('created_at');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where('title', 'like', "%{$search}%");
        }

        if ($request->filled('status')) {
            if ($request->status === 'published') {
                $query->where('is_published', true);
            } elseif ($request->status === 'draft') {
                $query->where('is_published', false);
            }
        }

        $posts = $query->paginate(15)->withQueryString();

        return view('admin.blog.index', compact('posts'));
    }

    public function create(): View
    {
        return view('admin.blog.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:blog_posts,slug'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'content' => ['required', 'string'],
            'cover_image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
            'cover_image_url' => ['nullable', 'string', 'max:500'],
            'is_published' => ['nullable', 'boolean'],
            'published_at' => ['nullable', 'date'],
        ]);

        $imagePath = 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=1200&q=80';
        if ($request->hasFile('cover_image')) {
            $imagePath = '/storage/'.ImageOptimizer::optimizeAndStore($request->file('cover_image'), 'blog', 1400, 85);
        } elseif ($request->filled('cover_image_url')) {
            $imagePath = trim($request->cover_image_url);
        }

        $validated['slug'] = $validated['slug'] ? Str::slug($validated['slug']) : Str::slug($validated['title']);
        $validated['content'] = HtmlSanitizer::clean($validated['content']);
        $validated['cover_image'] = $imagePath;
        $validated['is_published'] = $request->boolean('is_published');
        if ($validated['is_published'] && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }
        unset($validated['cover_image_url']);

        BlogPost::create($validated);

        return redirect()->route('admin.blog.index')
            ->with('success', "Artikel '{$validated['title']}' berhasil dibuat.");
    }

    public function edit(BlogPost $blog): View
    {
        $post = $blog;

        return view('admin.blog.edit', compact('post'));
    }

    public function update(Request $request, BlogPost $blog): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('blog_posts', 'slug')->ignore($blog->id)],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'content' => ['required', 'string'],
            'cover_image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
            'cover_image_url' => ['nullable', 'string', 'max:500'],
            'is_published' => ['nullable', 'boolean'],
            'published_at' => ['nullable', 'date'],
        ]);

        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = '/storage/'.ImageOptimizer::optimizeAndStore($request->file('cover_image'), 'blog', 1400, 85);
        } elseif ($request->filled('cover_image_url')) {
            $validated['cover_image'] = trim($request->cover_image_url);
        }

        $validated['slug'] = $validated['slug'] ? Str::slug($validated['slug']) : Str::slug($validated['title']);
        $validated['content'] = HtmlSanitizer::clean($validated['content']);
        $validated['is_published'] = $request->boolean('is_published');
        if ($validated['is_published'] && empty($blog->published_at) && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }
        unset($validated['cover_image_url']);

        $blog->update($validated);

        return redirect()->route('admin.blog.index')
            ->with('success', "Artikel '{$blog->title}' berhasil diperbarui.");
    }

    public function destroy(BlogPost $blog): RedirectResponse
    {
        $title = $blog->title;
        $blog->delete();

        return redirect()->route('admin.blog.index')
            ->with('success', "Artikel '{$title}' berhasil dihapus.");
    }
}
