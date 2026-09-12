<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeroSlide;
use App\Services\ImageOptimizer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HeroSlideController extends Controller
{
    public function index(Request $request): View
    {
        $query = HeroSlide::query()->orderBy('page')->orderBy('order');

        if ($request->filled('page')) {
            $query->where('page', $request->page);
        }

        $slides = $query->paginate(15)->withQueryString();

        return view('admin.hero_slides.index', compact('slides'));
    }

    public function create(): View
    {
        return view('admin.hero_slides.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'page' => ['required', 'in:home,men,women,sale'],
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'cta_text' => ['nullable', 'string', 'max:100'],
            'cta_link' => ['nullable', 'string', 'max:255'],
            'order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
            'image_url' => ['nullable', 'string', 'max:500'],
        ]);

        $imagePath = 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=1600&q=80';
        if ($request->hasFile('image')) {
            $imagePath = '/storage/'.ImageOptimizer::optimizeAndStore($request->file('image'), 'hero_slides', 1920, 85);
        } elseif ($request->filled('image_url')) {
            $imagePath = trim($request->image_url);
        }

        $validated['image'] = $imagePath;
        $validated['order'] = $validated['order'] ?? 0;
        $validated['is_active'] = $request->boolean('is_active', true);
        unset($validated['image_url']);

        HeroSlide::create($validated);

        return redirect()->route('admin.hero-slides.index')
            ->with('success', 'Hero slide banner berhasil ditambahkan.');
    }

    public function edit(HeroSlide $heroSlide): View
    {
        return view('admin.hero_slides.edit', compact('heroSlide'));
    }

    public function update(Request $request, HeroSlide $heroSlide): RedirectResponse
    {
        $validated = $request->validate([
            'page' => ['required', 'in:home,men,women,sale'],
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'cta_text' => ['nullable', 'string', 'max:100'],
            'cta_link' => ['nullable', 'string', 'max:255'],
            'order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
            'image_url' => ['nullable', 'string', 'max:500'],
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = '/storage/'.ImageOptimizer::optimizeAndStore($request->file('image'), 'hero_slides', 1920, 85);
        } elseif ($request->filled('image_url')) {
            $validated['image'] = trim($request->image_url);
        }

        $validated['order'] = $validated['order'] ?? 0;
        $validated['is_active'] = $request->boolean('is_active');
        unset($validated['image_url']);

        $heroSlide->update($validated);

        return redirect()->route('admin.hero-slides.index')
            ->with('success', 'Hero slide banner berhasil diperbarui.');
    }

    public function destroy(HeroSlide $heroSlide): RedirectResponse
    {
        $heroSlide->delete();

        return redirect()->route('admin.hero-slides.index')
            ->with('success', 'Hero slide berhasil dihapus.');
    }
}
