<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Collection;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CollectionController extends Controller
{
    public function index(): View
    {
        $collections = Collection::withCount('products')
            ->orderBy('order')
            ->get();

        return view('admin.collections.index', compact('collections'));
    }

    public function create(): View
    {
        $products = Product::where('is_active', true)->orderBy('name')->get();

        return view('admin.collections.create', compact('products'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:collections,slug'],
            'description' => ['nullable', 'string'],
            'order' => ['nullable', 'integer'],
            'is_active' => ['boolean'],
            'banner_image' => ['nullable', 'image', 'max:2048'],
            'products' => ['nullable', 'array'],
            'products.*' => ['exists:products,id'],
        ]);

        $slug = ! empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($validated['title']);

        $bannerPath = null;
        if ($request->hasFile('banner_image')) {
            $bannerPath = $request->file('banner_image')->store('collections', 'public');
        }

        $collection = Collection::create([
            'title' => $validated['title'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'order' => $validated['order'] ?? 0,
            'is_active' => $request->boolean('is_active', true),
            'banner_image' => $bannerPath,
        ]);

        if (! empty($validated['products'])) {
            $collection->products()->sync($validated['products']);
        }

        return redirect()->route('admin.collections.index')->with('success', 'Koleksi berhasil dibuat.');
    }

    public function edit(Collection $collection): View
    {
        $collection->load('products');
        $products = Product::where('is_active', true)->orderBy('name')->get();

        return view('admin.collections.edit', compact('collection', 'products'));
    }

    public function update(Request $request, Collection $collection): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('collections', 'slug')->ignore($collection->id)],
            'description' => ['nullable', 'string'],
            'order' => ['nullable', 'integer'],
            'is_active' => ['boolean'],
            'banner_image' => ['nullable', 'image', 'max:2048'],
            'products' => ['nullable', 'array'],
            'products.*' => ['exists:products,id'],
        ]);

        $bannerPath = $collection->banner_image;
        if ($request->hasFile('banner_image')) {
            $bannerPath = $request->file('banner_image')->store('collections', 'public');
        }

        $collection->update([
            'title' => $validated['title'],
            'slug' => Str::slug($validated['slug']),
            'description' => $validated['description'] ?? null,
            'order' => $validated['order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
            'banner_image' => $bannerPath,
        ]);

        $collection->products()->sync($validated['products'] ?? []);

        return redirect()->route('admin.collections.index')->with('success', 'Koleksi berhasil diperbarui.');
    }

    public function destroy(Collection $collection): RedirectResponse
    {
        $collection->products()->detach();
        $collection->delete();

        return redirect()->route('admin.collections.index')->with('success', 'Koleksi berhasil dihapus.');
    }
}
