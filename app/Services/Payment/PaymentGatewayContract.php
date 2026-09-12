<?php

namespace App\Services\Payment;

use App\Models\Order;

interface PaymentGatewayContract
{
    /**
     * Create a payment transaction for the given order and return payment details/tokens.
     */
    public function createTransaction(Order $order): array;

    /**
     * Verify the webhook notification signature.
     */
    public function verifySignature(array $payload): bool;

    /**
     * Parse notification webhook payload into standardized format.
     */
    public function parseNotification(array $payload): array;
}
