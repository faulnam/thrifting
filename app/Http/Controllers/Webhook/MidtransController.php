<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use App\Services\Payment\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class MidtransController extends Controller
{
    public function __construct(
        protected PaymentService $paymentService,
        protected OrderService $orderService
    ) {}

    /**
     * Handle incoming HTTP notification webhook from Midtrans.
     */
    public function handle(Request $request): JsonResponse
    {
        $payload = $request->all();

        // 1. Signature Verification
        if (! $this->paymentService->verifySignature($payload)) {
            Log::warning('Midtrans webhook invalid signature attempted.', [
                'ip' => $request->ip(),
                'payload' => $payload,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Invalid signature key.',
            ], 403);
        }

        $parsed = $this->paymentService->parseNotification($payload);
        $orderNumber = $parsed['order_number'];

        try {
            return DB::transaction(function () use ($orderNumber, $parsed) {
                /** @var Order|null $order */
                $order = Order::where('order_number', $orderNumber)
                    ->lockForUpdate()
                    ->first();

                if (! $order) {
                    Log::warning("Midtrans webhook: Order number {$orderNumber} not found.");

                    return response()->json([
                        'success' => false,
                        'message' => "Order {$orderNumber} not found.",
                    ], 404);
                }

                // 2. Idempotency Check
                if ($parsed['order_status'] === 'paid') {
                    if ($order->status === 'paid' || $order->isPaid()) {
                        Log::info("Midtrans webhook duplicate: Order {$orderNumber} is already PAID. Skipping side-effects.");

                        return response()->json([
                            'success' => true,
                            'message' => 'Order already processed (idempotent).',
                        ]);
                    }

                    $this->orderService->markOrderPaid($order, $parsed);
                } elseif ($parsed['order_status'] === 'cancelled') {
                    if ($order->status === 'cancelled') {
                        Log::info("Midtrans webhook duplicate: Order {$orderNumber} is already CANCELLED. Skipping duplicate stock restore.");

                        return response()->json([
                            'success' => true,
                            'message' => 'Order already cancelled (idempotent).',
                        ]);
                    }

                    $this->orderService->markOrderFailed($order, 'cancelled', $parsed);
                } elseif ($parsed['order_status'] === 'refunded') {
                    $order->update(['status' => 'refunded']);
                    if ($order->payment) {
                        $order->payment->update(['status' => 'refunded']);
                    }
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Webhook notification processed successfully.',
                ]);
            });
        } catch (Throwable $e) {
            Log::error("Midtrans webhook processing error: {$e->getMessage()}", [
                'payload' => $payload,
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error processing webhook.',
            ], 500);
        }
    }
}
