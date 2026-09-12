<?php

namespace App\Services\Payment;

use App\Models\Order;
use App\Models\SiteSetting;

class PaymentService
{
    protected PaymentGatewayContract $gateway;

    public function __construct(?PaymentGatewayContract $gateway = null)
    {
        $this->gateway = $gateway ?: $this->resolveGateway();
    }

    /**
     * Resolve the active payment gateway (defaults to Midtrans).
     */
    protected function resolveGateway(): PaymentGatewayContract
    {
        $activeGateway = SiteSetting::get('payment_gateway_active', 'midtrans');

        return match ($activeGateway) {
            'midtrans' => app(MidtransGateway::class),
            default => app(MidtransGateway::class),
        };
    }

    public function getGateway(): PaymentGatewayContract
    {
        return $this->gateway;
    }

    public function setGateway(PaymentGatewayContract $gateway): self
    {
        $this->gateway = $gateway;

        return $this;
    }

    public function createTransaction(Order $order): array
    {
        return $this->gateway->createTransaction($order);
    }

    public function verifySignature(array $payload): bool
    {
        return $this->gateway->verifySignature($payload);
    }

    public function parseNotification(array $payload): array
    {
        return $this->gateway->parseNotification($payload);
    }
}
