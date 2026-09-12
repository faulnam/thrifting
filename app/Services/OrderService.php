<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Payment;
use App\Models\ProductVariant;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class OrderService
{
    /**
     * Create an order from a cart within a database transaction and strict row locking
     * to prevent stock race conditions and overselling.
     */
    public function createOrderFromCart(
        Cart $cart,
        array $addressData,
        array $shippingRate,
        ?User $user = null,
        ?string $guestEmail = null,
        ?string $guestName = null,
        ?string $guestPhone = null,
        ?string $notes = null
    ): Order {
        return DB::transaction(function () use (
            $cart,
            $addressData,
            $shippingRate,
            $user,
            $guestEmail,
            $guestName,
            $guestPhone,
            $notes
        ) {
            $cart->loadMissing(['items.variant.product']);

            if ($cart->items->isEmpty()) {
                throw ValidationException::withMessages([
                    'cart' => 'Keranjang belanja Anda kosong.',
                ]);
            }

            // 1. Lock and validate stock for each variant
            $orderItemsData = [];
            $subtotal = 0;

            foreach ($cart->items as $cartItem) {
                /** @var ProductVariant|null $variant */
                $variant = ProductVariant::with('product')
                    ->where('id', $cartItem->product_variant_id)
                    ->lockForUpdate()
                    ->first();

                if (! $variant || ! $variant->product || ! $variant->product->is_active) {
                    throw ValidationException::withMessages([
                        'cart' => 'Varian produk yang dipilih tidak ditemukan atau sudah tidak aktif.',
                    ]);
                }

                if ($variant->stock < $cartItem->qty) {
                    $avail = max(0, $variant->stock);
                    $productName = $variant->product->name;
                    throw ValidationException::withMessages([
                        'cart' => "Stok untuk produk '{$productName}' (Warna {$variant->color_name}, Ukuran {$variant->size}) tidak mencukupi. Tersisa: {$avail} pasang.",
                    ]);
                }

                // Decrement stock immediately inside lock
                $variant->decrement('stock', $cartItem->qty);

                $unitPrice = (float) ($variant->price_override ?? $variant->product->base_price);
                $itemSubtotal = $unitPrice * $cartItem->qty;
                $subtotal += $itemSubtotal;

                $orderItemsData[] = [
                    'product_variant_id' => $variant->id,
                    'product_name_snapshot' => $variant->product->name,
                    'variant_snapshot' => [
                        'color_name' => $variant->color_name,
                        'color_hex' => $variant->color_hex,
                        'size' => $variant->size,
                        'sku' => $variant->sku,
                        'weight_grams' => $variant->product->weight_grams,
                    ],
                    'price' => $unitPrice,
                    'qty' => $cartItem->qty,
                    'subtotal' => $itemSubtotal,
                ];
            }

            // Calculate shipping cost and free shipping eligibility
            $isFreeShipping = $subtotal >= CartService::FREE_SHIPPING_THRESHOLD;
            $rawShippingCost = (float) ($shippingRate['price'] ?? 0);
            $courierType = strtolower($shippingRate['type'] ?? $shippingRate['courier_service_code'] ?? 'reguler');
            $isRegulerService = $courierType === 'reguler' ||
                                str_contains(strtolower($shippingRate['courier_service_code'] ?? ''), 'reg') ||
                                str_contains(strtolower($shippingRate['courier_service_code'] ?? ''), 'siuntung') ||
                                str_contains(strtolower($shippingRate['courier_service_code'] ?? ''), 'ez');

            $actualShippingCost = ($isFreeShipping && $isRegulerService) ? 0 : $rawShippingCost;
            $discount = 0; // Can be expanded with coupon logic
            $total = $subtotal + $actualShippingCost - $discount;

            // 2. Create Order
            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'user_id' => $user?->id,
                'guest_email' => $user ? $user->email : $guestEmail,
                'guest_name' => $user ? $user->name : ($guestName ?: ($addressData['recipient_name'] ?? 'Guest')),
                'guest_phone' => $user ? $user->phone : ($guestPhone ?: ($addressData['phone'] ?? null)),
                'status' => 'pending_payment',
                'subtotal' => $subtotal,
                'discount' => $discount,
                'shipping_cost' => $actualShippingCost,
                'total' => $total,
                'shipping_address_snapshot' => $addressData,
                'courier_company' => $shippingRate['courier_name'] ?? ($shippingRate['courier_company'] ?? 'Kurir'),
                'courier_type' => $shippingRate['courier_service_name'] ?? ($shippingRate['courier_service_code'] ?? 'Reguler'),
                'notes' => $notes,
            ]);

            // 3. Create Order Items
            foreach ($orderItemsData as $itemData) {
                $order->items()->create($itemData);
            }

            // 4. Clean up cart items
            $cart->items()->delete();

            return $order->fresh(['items.variant.product']);
        });
    }

    /**
     * Restore product variant stocks when an order fails or expires.
     */
    public function restoreOrderStock(Order $order): void
    {
        $order->loadMissing('items');

        foreach ($order->items as $item) {
            if ($item->product_variant_id) {
                /** @var ProductVariant|null $variant */
                $variant = ProductVariant::where('id', $item->product_variant_id)->lockForUpdate()->first();
                if ($variant) {
                    $variant->increment('stock', $item->qty);
                    Log::info("Stock restored for variant {$variant->id} (Order {$order->order_number}): +{$item->qty}");
                }
            }
        }
    }

    /**
     * Mark an order as paid, create or update payment record, and create draft shipment.
     */
    public function markOrderPaid(Order $order, array $paymentData): Order
    {
        return DB::transaction(function () use ($order, $paymentData) {
            /** @var Order $lockedOrder */
            $lockedOrder = Order::where('id', $order->id)->lockForUpdate()->first();

            $lockedOrder->update([
                'status' => 'paid',
            ]);

            // Create or update Payment record
            Payment::updateOrCreate(
                [
                    'order_id' => $lockedOrder->id,
                    'gateway' => 'midtrans',
                ],
                [
                    'gateway_reference' => $paymentData['transaction_id'] ?? $lockedOrder->order_number,
                    'payment_method' => $paymentData['payment_type'] ?? 'midtrans',
                    'status' => 'success',
                    'amount' => $paymentData['amount'] ?? $lockedOrder->total,
                    'paid_at' => $paymentData['paid_at'] ?? now(),
                    'raw_payload' => $paymentData['raw_payload'] ?? null,
                ]
            );

            // Create draft Shipment record if not exists
            Shipment::firstOrCreate(
                ['order_id' => $lockedOrder->id],
                [
                    'courier_company' => $lockedOrder->courier_company,
                    'courier_type' => $lockedOrder->courier_type,
                    'status' => 'pending',
                    'rate_snapshot' => [
                        'shipping_cost' => $lockedOrder->shipping_cost,
                        'address' => $lockedOrder->shipping_address_snapshot,
                    ],
                ]
            );

            Log::info("Order {$lockedOrder->order_number} marked as PAID.");

            return $lockedOrder->fresh(['payment', 'shipment', 'items']);
        });
    }

    /**
     * Mark an order as failed/cancelled and restore inventory stock.
     */
    public function markOrderFailed(Order $order, string $status, array $paymentData): Order
    {
        return DB::transaction(function () use ($order, $status, $paymentData) {
            /** @var Order $lockedOrder */
            $lockedOrder = Order::where('id', $order->id)->lockForUpdate()->first();

            $previousStatus = $lockedOrder->status;

            $lockedOrder->update([
                'status' => $status,
            ]);

            // Restore inventory stock if previous status was not already cancelled
            if ($previousStatus !== 'cancelled') {
                $this->restoreOrderStock($lockedOrder);
            }

            // Create or update Payment record
            Payment::updateOrCreate(
                [
                    'order_id' => $lockedOrder->id,
                    'gateway' => 'midtrans',
                ],
                [
                    'gateway_reference' => $paymentData['transaction_id'] ?? $lockedOrder->order_number,
                    'payment_method' => $paymentData['payment_type'] ?? 'midtrans',
                    'status' => $paymentData['payment_status'] ?? 'failed',
                    'amount' => $paymentData['amount'] ?? $lockedOrder->total,
                    'raw_payload' => $paymentData['raw_payload'] ?? null,
                ]
            );

            Log::info("Order {$lockedOrder->order_number} marked as {$status}. Stock restored.");

            return $lockedOrder->fresh(['payment', 'items']);
        });
    }
}
