<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Collection;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::with(['category', 'images', 'variants'])->withCount('variants');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->input('status') === 'active');
        }

        $products = $query->latest()->paginate(15)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create(): View
    {
        $categories = Category::orderBy('name')->get();
        $collections = Collection::where('is_active', true)->orderBy('title')->get();

        return view('admin.products.create', compact('categories', 'collections'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:products,slug'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'material_info' => ['nullable', 'string'],
            'sustainability_note' => ['nullable', 'string'],
            'base_price' => ['required', 'numeric', 'min:0'],
            'compare_at_price' => ['nullable', 'numeric'],
            'weight_grams' => ['required', 'integer', 'min:1'],
            'is_featured' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'collections' => ['nullable', 'array'],
            'collections.*' => ['exists:collections,id'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'max:2048'],
        ]);

        $slug = ! empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($validated['name']);

        // Cannot activate without image
        $hasImages = $request->hasFile('images');
        $isActive = $request->boolean('is_active', false);

        if ($isActive && ! $hasImages) {
            return back()->withErrors([
                'is_active' => 'Produk tidak dapat diaktifkan sebelum memiliki minimal 1 gambar (cover image).',
            ])->withInput();
        }

        $product = Product::create([
            'category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'slug' => $slug,
            'short_description' => $validated['short_description'] ?? null,
            'description' => $validated['description'] ?? null,
            'material_info' => $validated['material_info'] ?? null,
            'sustainability_note' => $validated['sustainability_note'] ?? null,
            'base_price' => $validated['base_price'],
            'compare_at_price' => $validated['compare_at_price'] ?? null,
            'weight_grams' => $validated['weight_grams'],
            'is_featured' => $request->boolean('is_featured', false),
            'is_active' => $isActive,
            'meta_title' => $validated['meta_title'] ?? $validated['name'],
            'meta_description' => $validated['meta_description'] ?? ($validated['short_description'] ?? null),
        ]);

        if (! empty($validated['collections'])) {
            $product->collections()->sync($validated['collections']);
        }

        // Upload images if provided
        if ($request->hasFile('images')) {
            $order = 1;
            foreach ($request->file('images') as $file) {
                $path = $file->store('products', 'public');
                $product->images()->create([
                    'image_path' => $path,
                    'order' => $order,
                    'is_primary' => $order === 1,
                ]);
                $order++;
            }
        }

        return redirect()->route('admin.products.edit', $product)->with('success', 'Produk berhasil dibuat.');
    }

    public function edit(Product $product): View
    {
        $product->load(['category', 'variants', 'images', 'collections']);
        $categories = Category::orderBy('name')->get();
        $collections = Collection::where('is_active', true)->orderBy('title')->get();

        return view('admin.products.edit', compact('product', 'categories', 'collections'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('products', 'slug')->ignore($product->id)],
            'short_description' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'material_info' => ['nullable', 'string'],
            'sustainability_note' => ['nullable', 'string'],
            'base_price' => ['required', 'numeric', 'min:0'],
            'compare_at_price' => ['nullable', 'numeric'],
            'weight_grams' => ['required', 'integer', 'min:1'],
            'is_featured' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'collections' => ['nullable', 'array'],
            'collections.*' => ['exists:collections,id'],
        ]);

        $isActive = $request->boolean('is_active');
        if ($isActive && $product->images()->count() === 0 && ! $request->hasFile('images')) {
            return back()->withErrors([
                'is_active' => 'Produk tidak dapat diaktifkan sebelum memiliki minimal 1 gambar (cover image).',
            ])->withInput();
        }

        $product->update([
            'category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'slug' => Str::slug($validated['slug']),
            'short_description' => $validated['short_description'] ?? null,
            'description' => $validated['description'] ?? null,
            'material_info' => $validated['material_info'] ?? null,
            'sustainability_note' => $validated['sustainability_note'] ?? null,
            'base_price' => $validated['base_price'],
            'compare_at_price' => $validated['compare_at_price'] ?? null,
            'weight_grams' => $validated['weight_grams'],
            'is_featured' => $request->boolean('is_featured'),
            'is_active' => $isActive,
            'meta_title' => $validated['meta_title'] ?? $validated['name'],
            'meta_description' => $validated['meta_description'] ?? ($validated['short_description'] ?? null),
        ]);

        $product->collections()->sync($validated['collections'] ?? []);

        return redirect()->route('admin.products.edit', $product)->with('success', 'Detail produk berhasil diperbarui.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->collections()->detach();
        $product->variants()->delete();
        $product->images()->delete();
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil dihapus.');
    }
}
