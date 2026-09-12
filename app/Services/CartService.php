<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CartService
{
    public const FREE_SHIPPING_THRESHOLD = 500000; // Rp 500.000

    /**
     * Get or create the current active cart for a user or session.
     */
    public function getCart(?User $user, string $sessionId): Cart
    {
        if ($user) {
            return Cart::firstOrCreate(
                ['user_id' => $user->id],
                ['session_id' => $sessionId]
            );
        }

        return Cart::firstOrCreate(
            ['session_id' => $sessionId, 'user_id' => null],
            ['user_id' => null]
        );
    }

    /**
     * Add an item to the cart with strict stock availability validation.
     */
    public function addItem(Cart $cart, int $productVariantId, int $qty = 1): CartItem
    {
        if ($qty < 1) {
            throw ValidationException::withMessages([
                'qty' => 'Kuantitas minimal adalah 1.',
            ]);
        }

        $variant = ProductVariant::with('product')->find($productVariantId);

        if (! $variant || ! $variant->product || ! $variant->product->is_active) {
            throw ValidationException::withMessages([
                'product_variant_id' => 'Varian produk yang dipilih tidak ditemukan atau tidak aktif.',
            ]);
        }

        if ($variant->stock_quantity <= 0) {
            throw ValidationException::withMessages([
                'product_variant_id' => "Stok varian {$variant->color_name} (Ukuran {$variant->size}) saat ini sedang habis.",
            ]);
        }

        /** @var CartItem|null $existingItem */
        $existingItem = $cart->items()->where('product_variant_id', $productVariantId)->first();

        $currentQtyInCart = $existingItem ? $existingItem->qty : 0;
        $newTotalQty = $currentQtyInCart + $qty;

        if ($newTotalQty > $variant->stock_quantity) {
            $availableToAdd = max(0, $variant->stock_quantity - $currentQtyInCart);
            $msg = $availableToAdd > 0
                ? "Hanya tersisa {$availableToAdd} pasang tambahan untuk varian ini di keranjang Anda (Total stok: {$variant->stock_quantity})."
                : "Jumlah di keranjang Anda sudah mencapai batas maksimal stok yang tersedia ({$variant->stock_quantity} pasang).";

            throw ValidationException::withMessages([
                'qty' => $msg,
            ]);
        }

        if ($existingItem) {
            $existingItem->update(['qty' => $newTotalQty]);

            return $existingItem->fresh();
        }

        return $cart->items()->create([
            'product_variant_id' => $productVariantId,
            'qty' => $qty,
        ]);
    }

    /**
     * Update item quantity with real-time stock validation.
     */
    public function updateItemQty(CartItem $item, int $qty): ?CartItem
    {
        if ($qty <= 0) {
            $item->delete();

            return null;
        }

        $variant = $item->variant;

        if ($qty > $variant->stock_quantity) {
            throw ValidationException::withMessages([
                'qty' => "Kuantitas yang diminta ({$qty}) melebihi stok yang tersedia (Tersisa: {$variant->stock_quantity} pasang).",
            ]);
        }

        $item->update(['qty' => $qty]);

        return $item->fresh();
    }

    /**
     * Remove an item from the cart.
     */
    public function removeItem(CartItem $item): void
    {
        $item->delete();
    }

    /**
     * Merge guest session cart into user account cart upon login.
     * Combines quantities for identical variants and caps to available stock.
     */
    public function mergeSessionCartToUser(string $sessionId, User $user): Cart
    {
        return DB::transaction(function () use ($sessionId, $user) {
            $guestCart = Cart::with(['items.variant'])->where('session_id', $sessionId)->whereNull('user_id')->first();
            $userCart = Cart::firstOrCreate(['user_id' => $user->id]);

            if (! $guestCart || $guestCart->items->isEmpty()) {
                if ($guestCart) {
                    $guestCart->delete();
                }

                return $userCart;
            }

            foreach ($guestCart->items as $guestItem) {
                $variant = $guestItem->variant;
                if (! $variant || $variant->stock_quantity <= 0) {
                    continue; // Skip out of stock variants
                }

                $userItem = $userCart->items()->where('product_variant_id', $guestItem->product_variant_id)->first();

                if ($userItem) {
                    // Combine quantities capped at available stock
                    $combinedQty = min($userItem->qty + $guestItem->qty, $variant->stock_quantity);
                    $userItem->update(['qty' => $combinedQty]);
                } else {
                    $qtyToSet = min($guestItem->qty, $variant->stock_quantity);
                    $userCart->items()->create([
                        'product_variant_id' => $guestItem->product_variant_id,
                        'qty' => $qtyToSet,
                    ]);
                }
            }

            // Clean up guest cart
            $guestCart->items()->delete();
            $guestCart->delete();

            return $userCart->fresh(['items.variant.product.images']);
        });
    }

    /**
     * Generate structured cart summary for API & Drawer rendering.
     */
    public function getCartSummary(Cart $cart): array
    {
        $cart->loadMissing(['items.variant.product.images']);

        $itemsData = [];
        $subtotal = 0;
        $totalQty = 0;

        foreach ($cart->items as $item) {
            $variant = $item->variant;
            if (! $variant || ! $variant->product) {
                continue;
            }

            $product = $variant->product;
            $price = (float) ($variant->price_override ?? $product->base_price);
            $itemSubtotal = $price * $item->qty;
            $subtotal += $itemSubtotal;
            $totalQty += $item->qty;

            $primaryImg = $product->images->firstWhere('is_primary', true) ?? $product->images->first();
            $imgPath = $primaryImg?->url;

            $itemsData[] = [
                'id' => $item->id,
                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_slug' => $product->slug,
                'variant_id' => $variant->id,
                'color_name' => $variant->color_name,
                'color_hex' => $variant->color_hex,
                'size' => $variant->size,
                'sku' => $variant->sku,
                'price' => $price,
                'price_formatted' => 'Rp '.number_format($price, 0, ',', '.'),
                'qty' => $item->qty,
                'subtotal' => $itemSubtotal,
                'subtotal_formatted' => 'Rp '.number_format($itemSubtotal, 0, ',', '.'),
                'stock_quantity' => $variant->stock_quantity,
                'image_url' => $imgPath ?? 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=400&q=80',
            ];
        }

        $threshold = self::FREE_SHIPPING_THRESHOLD;
        $isFreeShipping = $subtotal >= $threshold;
        $remaining = max(0, $threshold - $subtotal);
        $percent = $threshold > 0 ? min(100, round(($subtotal / $threshold) * 100)) : 100;

        return [
            'id' => $cart->id,
            'total_qty' => $totalQty,
            'subtotal' => $subtotal,
            'subtotal_formatted' => 'Rp '.number_format($subtotal, 0, ',', '.'),
            'free_shipping_threshold' => $threshold,
            'free_shipping_threshold_formatted' => 'Rp '.number_format($threshold, 0, ',', '.'),
            'remaining_free_shipping' => $remaining,
            'remaining_free_shipping_formatted' => 'Rp '.number_format($remaining, 0, ',', '.'),
            'is_free_shipping' => $isFreeShipping,
            'free_shipping_percent' => $percent,
            'items' => $itemsData,
        ];
    }
}
