<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function show(string $slug): View
    {
        $product = Product::where('slug', $slug)
            ->where('is_active', true)
            ->with([
                'category',
                'images',
                'variants' => fn ($q) => $q->where('is_active', true),
                'collections',
                'approvedReviews.user',
            ])
            ->firstOrFail();

        // Group variants by color
        $colors = [];
        foreach ($product->variants as $variant) {
            $cName = $variant->color_name;
            if (! isset($colors[$cName])) {
                $colors[$cName] = [
                    'color_name' => $cName,
                    'color_hex' => $variant->color_hex,
                    'sizes' => [],
                ];
            }
            $colors[$cName]['sizes'][] = [
                'id' => $variant->id,
                'sku' => $variant->sku,
                'size' => $variant->size,
                'stock' => $variant->stock,
                'price' => $variant->effective_price,
            ];
        }

        $colorsList = array_values($colors);

        // Related Products
        $relatedProducts = Product::where('is_active', true)
            ->where('id', '!=', $product->id)
            ->where('category_id', $product->category_id)
            ->with(['images', 'variants'])
            ->take(4)
            ->get();

        if ($relatedProducts->count() < 4) {
            $more = Product::where('is_active', true)
                ->where('id', '!=', $product->id)
                ->whereNotIn('id', $relatedProducts->pluck('id'))
                ->with(['images', 'variants'])
                ->take(4 - $relatedProducts->count())
                ->get();
            $relatedProducts = $relatedProducts->merge($more);
        }

        return view('products.show', compact('product', 'colorsList', 'relatedProducts'));
    }
}
