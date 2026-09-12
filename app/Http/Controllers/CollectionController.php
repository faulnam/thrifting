<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Collection;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CollectionController extends Controller
{
    public function show(Request $request, string $slug = 'all'): View
    {
        $collection = null;
        $category = null;
        $pageTitle = 'Semua Produk';
        $pageDescription = 'Koleksi sepatu dan pakaian berbahan alami berkelanjutan dari fifa.';

        $query = Product::where('is_active', true)
            ->with(['category', 'images', 'variants' => fn ($q) => $q->where('is_active', true)]);

        // 1. Check if slug matches special gender route /men or /women
        if ($slug === 'men' || $slug === 'women') {
            $gender = $slug;
            $pageTitle = ucfirst($gender)."'s Collection";
            $pageDescription = 'Koleksi sepatu dan pakaian nyaman ramah lingkungan untuk '.($gender === 'men' ? 'Pria' : 'Wanita').'.';

            $categoryIds = Category::where('gender', $gender)->pluck('id');
            $query->whereIn('category_id', $categoryIds);
        } elseif ($slug === 'sale') {
            $pageTitle = 'Sale & Special Offers';
            $pageDescription = 'Penawaran terbatas untuk produk-produk pilihan fifa.';
            $query->where(function ($q) {
                $q->whereNotNull('compare_at_price')
                    ->orWhereHas('collections', fn ($c) => $c->where('slug', 'sale'));
            });
        } elseif ($slug !== 'all') {
            // Check if collection exists
            $collection = Collection::where('slug', $slug)->where('is_active', true)->first();
            if ($collection) {
                $pageTitle = $collection->title;
                $pageDescription = $collection->description ?? $pageDescription;
                $query->whereHas('collections', fn ($c) => $c->where('collections.id', $collection->id));
            } else {
                // Check if category exists
                $category = Category::where('slug', $slug)->where('is_active', true)->first();
                if ($category) {
                    $pageTitle = $category->name;
                    $pageDescription = $category->description ?? $pageDescription;

                    // Include child categories if parent
                    $childIds = $category->children()->pluck('id')->toArray();
                    $allCategoryIds = array_merge([$category->id], $childIds);
                    $query->whereIn('category_id', $allCategoryIds);
                }
            }
        }

        // 2. Filters
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

        // 3. Sorting
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

        // 4. Available filter values for the UI
        $availableCategories = Category::whereNull('parent_id')->with('children')->get();
        $availableSizes = ProductVariant::distinct()->orderBy('size')->pluck('size');
        $availableColors = ProductVariant::select('color_name', 'color_hex')->distinct()->get()->unique('color_name');

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
}
