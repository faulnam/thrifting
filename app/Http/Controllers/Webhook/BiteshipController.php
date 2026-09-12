<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class BiteshipController extends Controller
{
    /**
     * Handle incoming Biteship shipment tracking webhook.
     */
    public function handle(Request $request): JsonResponse
    {
        $payload = $request->all();

        $biteshipOrderId = $payload['order_id'] ?? ($payload['id'] ?? null);
        $trackingId = $payload['courier_tracking_id'] ?? ($payload['tracking_id'] ?? null);
        $waybillId = $payload['courier_waybill_id'] ?? ($payload['waybill_id'] ?? null);
        $status = strtolower((string) ($payload['status'] ?? ($payload['tracking']['status'] ?? '')));
        $note = $payload['tracking']['note'] ?? ($payload['note'] ?? "Status pengiriman diperbarui: {$status}");
        $occurredAt = $payload['tracking']['updated_at'] ?? now()->toDateTimeString();

        if (empty($biteshipOrderId) && empty($trackingId) && empty($waybillId)) {
            Log::warning('Biteship webhook received without order/tracking identifier.', ['payload' => $payload]);

            return response()->json(['success' => false, 'message' => 'Missing shipment identifier.'], 400);
        }

        try {
            return DB::transaction(function () use ($biteshipOrderId, $trackingId, $waybillId, $status, $note, $occurredAt) {
                /** @var Shipment|null $shipment */
                $shipment = Shipment::where(function ($query) use ($biteshipOrderId, $trackingId, $waybillId) {
                    if ($biteshipOrderId) {
                        $query->where('biteship_order_id', $biteshipOrderId);
                    }
                    if ($trackingId) {
                        $query->orWhere('tracking_id', $trackingId);
                    }
                    if ($waybillId) {
                        $query->orWhere('waybill_id', $waybillId);
                    }
                })
                    ->lockForUpdate()
                    ->first();

                if (! $shipment) {
                    Log::info("Biteship webhook: No local shipment matched identifier {$biteshipOrderId}.");

                    return response()->json(['success' => true, 'message' => 'Shipment not found locally, acknowledged.'], 200);
                }

                // Update waybill/tracking if available in webhook
                if ($waybillId) {
                    $shipment->update(['waybill_id' => $waybillId]);
                }
                if ($trackingId) {
                    $shipment->update(['tracking_id' => $trackingId]);
                }

                // 1. Idempotency Check: Prevent duplicate tracking entry for the same status & time
                $alreadyLogged = $shipment->trackings()
                    ->where('status', $status)
                    ->where('note', $note)
                    ->exists();

                if (! $alreadyLogged) {
                    $shipment->trackings()->create([
                        'status' => $status,
                        'note' => $note,
                        'occurred_at' => $occurredAt,
                    ]);
                }

                // 2. Map Biteship status to local shipment and order statuses
                $order = $shipment->order;

                switch ($status) {
                    case 'allocated':
                    case 'picking_up':
                        $shipment->update(['status' => 'requested']);
                        if ($order && $order->status === 'processing') {
                            $order->update(['status' => 'ready_to_ship']);
                        }
                        break;

                    case 'picked':
                    case 'picked_up':
                        $shipment->update(['status' => 'picked_up']);
                        if ($order && in_array($order->status, ['processing', 'ready_to_ship', 'paid'])) {
                            $order->update(['status' => 'shipped']);
                        }
                        break;

                    case 'dropping_off':
                    case 'on_process':
                        $shipment->update(['status' => 'on_process']);
                        if ($order && in_array($order->status, ['processing', 'ready_to_ship', 'paid'])) {
                            $order->update(['status' => 'shipped']);
                        }
                        break;

                    case 'delivered':
                        $shipment->update(['status' => 'delivered']);
                        if ($order && $order->status !== 'completed') {
                            $order->update(['status' => 'delivered']);
                        }
                        break;

                    case 'rejected':
                    case 'cancelled':
                    case 'courier_not_found':
                        $shipment->update(['status' => 'cancelled']);
                        break;
                }

                Log::info("Biteship webhook processed successfully for Shipment #{$shipment->id} ({$status})");

                return response()->json([
                    'success' => true,
                    'message' => 'Biteship webhook processed successfully (idempotent).',
                ]);
            });
        } catch (Throwable $e) {
            Log::error("Biteship webhook error: {$e->getMessage()}", [
                'payload' => $payload,
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error processing Biteship webhook.',
            ], 500);
        }
    }
}
