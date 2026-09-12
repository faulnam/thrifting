<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProductVariantController extends Controller
{
    public function store(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'sku' => ['nullable', 'string', 'max:100', 'unique:product_variants,sku'],
            'color_name' => ['required', 'string', 'max:100'],
            'color_hex' => ['required', 'string', 'max:20'],
            'size' => ['required', 'string', 'max:20'],
            'stock' => ['required', 'integer', 'min:0'],
            'price_override' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['boolean'],
        ]);

        $sku = ! empty($validated['sku'])
            ? strtoupper(trim($validated['sku']))
            : strtoupper(Str::slug($product->name.'-'.$validated['color_name'].'-'.$validated['size']));

        // Check if SKU already exists, if so append random suffix
        if (ProductVariant::where('sku', $sku)->exists()) {
            $sku .= '-'.strtoupper(substr(uniqid(), -3));
        }

        $product->variants()->create([
            'sku' => $sku,
            'color_name' => $validated['color_name'],
            'color_hex' => $validated['color_hex'],
            'size' => $validated['size'],
            'stock' => $validated['stock'],
            'price_override' => $validated['price_override'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.products.edit', $product)->with('success', 'Varian ukuran/warna berhasil ditambahkan.');
    }

    public function update(Request $request, Product $product, ProductVariant $variant): RedirectResponse
    {
        $validated = $request->validate([
            'sku' => ['required', 'string', 'max:100', Rule::unique('product_variants', 'sku')->ignore($variant->id)],
            'color_name' => ['required', 'string', 'max:100'],
            'color_hex' => ['required', 'string', 'max:20'],
            'size' => ['required', 'string', 'max:20'],
            'stock' => ['required', 'integer', 'min:0'],
            'price_override' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['boolean'],
        ]);

        $variant->update([
            'sku' => strtoupper($validated['sku']),
            'color_name' => $validated['color_name'],
            'color_hex' => $validated['color_hex'],
            'size' => $validated['size'],
            'stock' => $validated['stock'],
            'price_override' => $validated['price_override'] ?? null,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.products.edit', $product)->with('success', 'Varian berhasil diperbarui.');
    }

    public function destroy(Product $product, ProductVariant $variant): RedirectResponse
    {
        $variant->delete();

        return redirect()->route('admin.products.edit', $product)->with('success', 'Varian berhasil dihapus.');
    }
}
