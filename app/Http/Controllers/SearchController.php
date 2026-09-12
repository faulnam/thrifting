<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    /**
     * Display full search results page.
     */
    public function index(Request $request): View
    {
        $q = trim((string) $request->input('q', $request->input('search', '')));
        $pageTitle = $q !== '' ? "Hasil Pencarian: \"{$q}\"" : 'Pencarian Produk';
        $pageDescription = $q !== ''
            ? "Menampilkan produk yang cocok dengan kata kunci \"{$q}\"."
            : 'Jelajahi berbagai pilihan sepatu dan pakaian ramah lingkungan fifa.';

        $query = Product::where('is_active', true)
            ->with(['category', 'images', 'variants' => fn ($varQuery) => $varQuery->where('is_active', true)]);

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('short_description', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%")
                    ->orWhere('material_info', 'like', "%{$q}%")
                    ->orWhere('sustainability_note', 'like', "%{$q}%")
                    ->orWhereHas('category', fn ($cat) => $cat->where('name', 'like', "%{$q}%"))
                    ->orWhereHas('variants', fn ($var) => $var->where('color_name', 'like', "%{$q}%")->orWhere('sku', 'like', "%{$q}%"));
            });
        }

        // Additional filters
        if ($request->filled('category')) {
            $catSlug = $request->input('category');
            $filterCat = Category::where('slug', $catSlug)->first();
            if ($filterCat) {
                $childIds = $filterCat->children()->pluck('id')->toArray();
                $allCatIds = array_merge([$filterCat->id], $childIds);
                $query->whereIn('category_id', $allCatIds);
            }
        }

        if ($request->filled('gender') && in_array($request->input('gender'), ['men', 'women', 'unisex'])) {
            $g = $request->input('gender');
            $categoryIds = Category::where('gender', $g)->pluck('id');
            $query->whereIn('category_id', $categoryIds);
        }

        if ($request->filled('color')) {
            $color = $request->input('color');
            $query->whereHas('variants', fn ($v) => $v->where('color_name', 'like', "%{$color}%"));
        }

        if ($request->filled('size')) {
            $size = $request->input('size');
            $query->whereHas('variants', fn ($v) => $v->where('size', $size)->where('stock', '>', 0));
        }

        if ($request->filled('min_price')) {
            $query->where('base_price', '>=', (float) $request->input('min_price'));
        }

        if ($request->filled('max_price')) {
            $query->where('base_price', '<=', (float) $request->input('max_price'));
        }

        // Sorting
        $sort = $request->input('sort', 'newest');
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('base_price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('base_price', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'newest':
            default:
                $query->latest();
                break;
        }

        $products = $query->paginate(12)->withQueryString();

        $collection = null;
        $category = null;
        $availableCategories = Category::whereNull('parent_id')->with('children')->get();
        $availableSizes = ProductVariant::distinct()->orderBy('size')->pluck('size');
        $availableColors = ProductVariant::select('color_name', 'color_hex')->distinct()->get()->unique('color_name');
        $slug = 'search';

        return view('collections.show', compact(
            'products',
            'collection',
            'category',
            'pageTitle',
            'pageDescription',
            'availableCategories',
            'availableSizes',
            'availableColors',
            'slug'
        ));
    }

    /**
     * Instant live search API endpoint returning JSON suggestions.
     */
    public function live(Request $request): JsonResponse
    {
        $q = trim((string) $request->input('q', ''));
        if (strlen($q) < 2) {
            return response()->json([
                'success' => true,
                'products' => [],
                'total' => 0,
            ]);
        }

        $products = Product::where('is_active', true)
            ->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%")
                    ->orWhereHas('category', fn ($cat) => $cat->where('name', 'like', "%{$q}%"))
                    ->orWhereHas('variants', fn ($var) => $var->where('color_name', 'like', "%{$q}%"));
            })
            ->with(['category', 'images', 'variants'])
            ->take(6)
            ->get();

        $results = $products->map(function (Product $product) {
            $primaryImage = $product->primaryImage ?? $product->images->first();
            $imageUrl = $primaryImage ? $primaryImage->url : asset('images/products/tree-runner-blue.png');

            return [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'url' => route('products.show', $product->slug),
                'category' => $product->category?->name ?? 'Shoes',
                'price_formatted' => 'Rp '.number_format((float) $product->base_price, 0, ',', '.'),
                'compare_at_price_formatted' => $product->compare_at_price ? 'Rp '.number_format((float) $product->compare_at_price, 0, ',', '.') : null,
                'image' => $imageUrl,
            ];
        });

        return response()->json([
            'success' => true,
            'products' => $results,
            'total' => $results->count(),
        ]);
    }
}
