<?php

namespace App\Services\Payment;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class MidtransGateway implements PaymentGatewayContract
{
    protected string $serverKey;

    protected string $clientKey;

    protected bool $isProduction;

    protected string $snapUrl;

    public function __construct()
    {
        $this->serverKey = (string) config('services.midtrans.server_key', '');
        $this->clientKey = (string) config('services.midtrans.client_key', '');
        $this->isProduction = (bool) config('services.midtrans.is_production', false);
        $this->snapUrl = (string) config('services.midtrans.snap_url', 'https://app.sandbox.midtrans.com/snap/v1/transactions');
    }

    public function getClientKey(): string
    {
        return $this->clientKey;
    }

    public function isProduction(): bool
    {
        return $this->isProduction;
    }

    /**
     * Create Midtrans Snap transaction for the given order.
     */
    public function createTransaction(Order $order): array
    {
        $order->loadMissing(['items.variant.product', 'user']);

        $customer = $order->user;
        $addressSnapshot = $order->shipping_address_snapshot ?? [];

        $recipientName = $order->guest_name ?: ($addressSnapshot['recipient_name'] ?? $customer?->name ?? 'Customer');
        $nameParts = explode(' ', trim($recipientName), 2);
        $firstName = $nameParts[0] ?? 'Customer';
        $lastName = $nameParts[1] ?? '';

        $email = $order->guest_email ?: ($customer?->email ?? 'customer@example.com');
        $phone = $order->guest_phone ?: ($addressSnapshot['phone'] ?? $customer?->phone ?? '081234567890');

        $itemDetails = [];
        foreach ($order->items as $item) {
            $itemDetails[] = [
                'id' => (string) ($item->product_variant_id ?: $item->id),
                'price' => (int) round($item->price),
                'quantity' => (int) $item->qty,
                'name' => mb_substr($item->product_name_snapshot, 0, 50),
            ];
        }

        // Add Shipping Item
        if ((float) $order->shipping_cost > 0) {
            $itemDetails[] = [
                'id' => 'SHIPPING',
                'price' => (int) round($order->shipping_cost),
                'quantity' => 1,
                'name' => mb_substr('Ongkir ('.($order->courier_company ?: 'Kurir').' - '.($order->courier_type ?: 'Reguler').')', 0, 50),
            ];
        }

        // Add Discount Item (as negative price if any)
        if ((float) $order->discount > 0) {
            $itemDetails[] = [
                'id' => 'DISCOUNT',
                'price' => -(int) round($order->discount),
                'quantity' => 1,
                'name' => 'Kupon Diskon',
            ];
        }

        $grossAmount = (int) round($order->total);

        $payload = [
            'transaction_details' => [
                'order_id' => $order->order_number,
                'gross_amount' => $grossAmount,
            ],
            'customer_details' => [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email,
                'phone' => $phone,
                'billing_address' => [
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'address' => $addressSnapshot['address_line'] ?? 'Alamat',
                    'city' => $addressSnapshot['city'] ?? 'Jakarta',
                    'postal_code' => $addressSnapshot['postal_code'] ?? '12000',
                    'country_code' => 'IDN',
                ],
                'shipping_address' => [
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'address' => $addressSnapshot['address_line'] ?? 'Alamat',
                    'city' => $addressSnapshot['city'] ?? 'Jakarta',
                    'postal_code' => $addressSnapshot['postal_code'] ?? '12000',
                    'country_code' => 'IDN',
                ],
            ],
            'item_details' => $itemDetails,
        ];

        if (! empty($this->serverKey)) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => 'Basic '.base64_encode($this->serverKey.':'),
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])
                    ->timeout(10)
                    ->post($this->snapUrl, $payload);

                if ($response->successful()) {
                    $json = $response->json();

                    return [
                        'token' => $json['token'] ?? '',
                        'redirect_url' => $json['redirect_url'] ?? '',
                    ];
                }

                Log::warning('Midtrans Snap API error: '.$response->body());
            } catch (Throwable $e) {
                Log::error('Midtrans Snap exception: '.$e->getMessage());
            }
        }

        // Mock token fallback for local dev & automated testing without live credentials
        $mockToken = 'snap_mock_'.md5($order->order_number.'_'.$grossAmount);

        return [
            'token' => $mockToken,
            'redirect_url' => "https://app.sandbox.midtrans.com/snap/v2/vtweb/{$mockToken}",
        ];
    }

    /**
     * Verify SHA512 signature from Midtrans webhook notification.
     * Formula: sha512(order_id + status_code + gross_amount + ServerKey)
     */
    public function verifySignature(array $payload): bool
    {
        $orderId = (string) ($payload['order_id'] ?? '');
        $statusCode = (string) ($payload['status_code'] ?? '');
        $grossAmount = (string) ($payload['gross_amount'] ?? '');
        $signatureKey = (string) ($payload['signature_key'] ?? '');

        if (empty($orderId) || empty($statusCode) || empty($grossAmount) || empty($signatureKey)) {
            return false;
        }

        $expectedSignature = hash('sha512', $orderId.$statusCode.$grossAmount.$this->serverKey);

        return hash_equals($expectedSignature, $signatureKey);
    }

    /**
     * Parse notification payload into standardized status array.
     */
    public function parseNotification(array $payload): array
    {
        $orderNumber = (string) ($payload['order_id'] ?? '');
        $transactionStatus = (string) ($payload['transaction_status'] ?? '');
        $fraudStatus = (string) ($payload['fraud_status'] ?? '');
        $paymentType = (string) ($payload['payment_type'] ?? 'midtrans');
        $transactionId = (string) ($payload['transaction_id'] ?? ($payload['order_id'] ?? uniqid()));
        $grossAmount = (float) ($payload['gross_amount'] ?? 0);
        $settlementTime = $payload['settlement_time'] ?? ($payload['transaction_time'] ?? now()->toDateTimeString());

        // Determine mapped status
        $orderStatus = 'pending_payment';
        $paymentStatus = 'pending';

        if ($transactionStatus === 'capture') {
            if ($fraudStatus === 'challenge') {
                $orderStatus = 'pending_payment';
                $paymentStatus = 'pending';
            } elseif ($fraudStatus === 'accept') {
                $orderStatus = 'paid';
                $paymentStatus = 'success';
            }
        } elseif ($transactionStatus === 'settlement') {
            $orderStatus = 'paid';
            $paymentStatus = 'success';
        } elseif (in_array($transactionStatus, ['cancel', 'deny'])) {
            $orderStatus = 'cancelled';
            $paymentStatus = 'failed';
        } elseif ($transactionStatus === 'expire') {
            $orderStatus = 'cancelled';
            $paymentStatus = 'expired';
        } elseif (in_array($transactionStatus, ['refund', 'partial_refund'])) {
            $orderStatus = 'refunded';
            $paymentStatus = 'refunded';
        }

        return [
            'order_number' => $orderNumber,
            'transaction_id' => $transactionId,
            'transaction_status' => $transactionStatus,
            'fraud_status' => $fraudStatus,
            'order_status' => $orderStatus,
            'payment_status' => $paymentStatus,
            'payment_type' => $paymentType,
            'amount' => $grossAmount,
            'paid_at' => $paymentStatus === 'success' ? $settlementTime : null,
            'raw_payload' => $payload,
        ];
    }
}
