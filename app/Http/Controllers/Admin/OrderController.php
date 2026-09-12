<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Shipment;
use App\Services\BiteshipService;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(
        protected BiteshipService $biteshipService,
        protected OrderService $orderService
    ) {}

    /**
     * Display a listing of customer orders with filters.
     */
    public function index(Request $request): View
    {
        $query = Order::with(['user', 'payment', 'shipment', 'items'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->filled('q')) {
            $search = trim($request->query('q'));
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('guest_name', 'like', "%{$search}%")
                    ->orWhere('guest_email', 'like', "%{$search}%")
                    ->orWhere('guest_phone', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $orders = $query->paginate(15)->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Display the detailed order information, payment snapshot, and shipment tracking.
     */
    public function show(Order $order): View
    {
        $order->loadMissing([
            'items.variant.product.images',
            'payment',
            'shipment.trackings',
            'user',
        ]);

        return view('admin.orders.show', compact('order'));
    }

    /**
     * Update order status manually.
     */
    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:pending_payment,paid,processing,ready_to_ship,shipped,delivered,completed,cancelled',
        ]);

        $newStatus = $validated['status'];
        $oldStatus = $order->status;

        if ($newStatus === $oldStatus) {
            return back()->with('info', 'Status pesanan tidak berubah.');
        }

        DB::transaction(function () use ($order, $newStatus, $oldStatus) {
            if ($newStatus === 'cancelled' && $oldStatus !== 'cancelled') {
                $this->orderService->restoreOrderStock($order);
            }

            $order->update(['status' => $newStatus]);
        });

        return back()->with('success', 'Status pesanan berhasil diperbarui menjadi: '.strtoupper($newStatus));
    }

    /**
     * Update admin internal notes.
     */
    public function updateNotes(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);

        $order->update(['notes' => $validated['notes']]);

        return back()->with('success', 'Catatan internal pesanan berhasil disimpan.');
    }

    /**
     * Trigger Biteship API to create delivery order.
     */
    public function processShipping(Order $order): RedirectResponse
    {
        if (! $order->isPaid() && $order->status !== 'processing') {
            return back()->with('error', 'Hanya pesanan yang sudah dibayar atau sedang diproses yang dapat dibuatkan pengiriman ke Biteship.');
        }

        $result = $this->biteshipService->createOrder($order);

        if (! $result['success']) {
            return back()->with('error', 'Gagal memproses pengiriman ke Biteship: '.($result['error'] ?? 'Terjadi kesalahan.'));
        }

        DB::transaction(function () use ($order, $result) {
            /** @var Shipment $shipment */
            $shipment = Shipment::firstOrCreate(
                ['order_id' => $order->id],
                [
                    'courier_company' => $order->courier_company ?: 'Kurir',
                    'courier_type' => $order->courier_type ?: 'Reguler',
                ]
            );

            $shipment->update([
                'biteship_order_id' => $result['biteship_order_id'],
                'tracking_id' => $result['tracking_id'] ?? $shipment->tracking_id,
                'waybill_id' => $result['waybill_id'] ?? $shipment->waybill_id,
                'status' => 'pending',
            ]);

            $order->update(['status' => 'processing']);

            // Record tracking entry
            $shipment->trackings()->create([
                'status' => 'order_created',
                'note' => 'Order pengiriman berhasil didaftarkan di Biteship.',
                'occurred_at' => now(),
            ]);
        });

        return back()->with('success', 'Order pengiriman berhasil dibuat di Biteship. Nomor Resi/Tracking ID: '.($result['tracking_id'] ?? $result['biteship_order_id']));
    }

    /**
     * Trigger Biteship API to request courier pickup.
     */
    public function requestPickup(Order $order): RedirectResponse
    {
        $shipment = $order->shipment;

        if (! $shipment || empty($shipment->biteship_order_id)) {
            return back()->with('error', 'Order pengiriman belum dibuat di Biteship. Klik "Proses Pengiriman" terlebih dahulu.');
        }

        $result = $this->biteshipService->requestPickup($shipment);

        if (! $result['success']) {
            return back()->with('error', 'Gagal request pickup: '.($result['error'] ?? 'Kurir tidak tersedia.'));
        }

        DB::transaction(function () use ($order, $shipment, $result) {
            $shipment->update([
                'status' => 'requested',
                'pickup_scheduled_at' => $result['pickup_scheduled_at'] ?? now(),
            ]);

            $order->update(['status' => 'ready_to_ship']);

            $shipment->trackings()->create([
                'status' => 'pickup_requested',
                'note' => 'Penjemputan paket telah dijadwalkan ke kurir.',
                'occurred_at' => now(),
            ]);
        });

        return back()->with('success', 'Request pickup kurir berhasil dikirim ke Biteship.');
    }
}
