<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Order;
use App\Services\BiteshipService;
use App\Services\CartService;
use App\Services\OrderService;
use App\Services\Payment\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class CheckoutController extends Controller
{
    public function __construct(
        protected CartService $cartService,
        protected BiteshipService $biteshipService,
        protected OrderService $orderService,
        protected PaymentService $paymentService
    ) {}

    /**
     * Display the multi-step checkout page.
     */
    public function index(Request $request): View|RedirectResponse
    {
        $sessionId = $request->session()->getId();
        $user = Auth::user();

        $cart = $this->cartService->getCart($user, $sessionId);
        $cartSummary = $this->cartService->getCartSummary($cart);

        if ($cartSummary['total_qty'] === 0) {
            return redirect()->route('cart.index')->with('error', 'Keranjang belanja Anda masih kosong.');
        }

        $addresses = $user ? $user->addresses()->orderByDesc('is_default')->latest()->get() : collect();
        $defaultAddress = $addresses->firstWhere('is_default', true) ?? $addresses->first();

        $checkoutState = Session::get('checkout_data', [
            'step' => 1,
            'address_id' => $defaultAddress?->id,
            'address_data' => $defaultAddress ? [
                'recipient_name' => $defaultAddress->recipient_name,
                'phone' => $defaultAddress->phone,
                'province' => $defaultAddress->province,
                'city' => $defaultAddress->city,
                'district' => $defaultAddress->district,
                'postal_code' => $defaultAddress->postal_code,
                'address_line' => $defaultAddress->address_line,
                'biteship_area_id' => $defaultAddress->biteship_area_id,
            ] : null,
            'shipping_rate' => null,
        ]);

        return view('checkout.index', compact('cart', 'cartSummary', 'addresses', 'defaultAddress', 'checkoutState'));
    }

    /**
     * Calculate Biteship shipping rates via AJAX.
     */
    public function calculateRates(Request $request): JsonResponse
    {
        $request->validate([
            'destination_area_id' => 'nullable|string',
        ]);

        $sessionId = $request->session()->getId();
        $user = Auth::user();
        $cart = $this->cartService->getCart($user, $sessionId);
        $cartSummary = $this->cartService->getCartSummary($cart);

        if ($cartSummary['total_qty'] === 0) {
            return response()->json([
                'success' => false,
                'error' => 'Keranjang kosong.',
                'error_code' => 'EMPTY_CART',
            ], 422);
        }

        $destinationAreaId = $request->input('destination_area_id') ?: 'IDNP6IDNC148IDND859';
        $result = $this->biteshipService->getRates($destinationAreaId, $cartSummary['items']);

        $isFreeShipping = $cartSummary['is_free_shipping'];

        return response()->json([
            'success' => $result['success'],
            'rates' => $result['rates'],
            'is_free_shipping' => $isFreeShipping,
            'free_shipping_threshold' => $cartSummary['free_shipping_threshold'],
            'error' => $result['error'],
            'error_code' => $result['error_code'],
        ]);
    }

    /**
     * Save shipping & address selection to checkout session.
     */
    public function saveShipping(Request $request): JsonResponse
    {
        $request->validate([
            'address' => 'required|array',
            'address.recipient_name' => 'required|string',
            'address.phone' => 'required|string',
            'address.province' => 'required|string',
            'address.city' => 'required|string',
            'address.district' => 'required|string',
            'address.postal_code' => 'required|string',
            'address.address_line' => 'required|string',
            'address.biteship_area_id' => 'nullable|string',
            'shipping' => 'required|array',
            'shipping.courier_company' => 'required|string',
            'shipping.courier_service_name' => 'required|string',
            'shipping.price' => 'required|numeric',
        ]);

        $checkoutData = Session::get('checkout_data', []);
        $checkoutData['address_data'] = $request->input('address');
        $checkoutData['shipping_rate'] = $request->input('shipping');
        $checkoutData['step'] = 3;

        Session::put('checkout_data', $checkoutData);

        return response()->json([
            'success' => true,
            'message' => 'Opsi pengiriman berhasil disimpan.',
            'next_step' => 3,
        ]);
    }

    /**
     * Place Order and create Midtrans Snap transaction token.
     */
    public function process(Request $request): JsonResponse
    {
        $request->validate([
            'address' => 'required|array',
            'address.recipient_name' => 'required|string',
            'address.phone' => 'required|string',
            'address.province' => 'required|string',
            'address.city' => 'required|string',
            'address.district' => 'required|string',
            'address.postal_code' => 'required|string',
            'address.address_line' => 'required|string',
            'address.biteship_area_id' => 'nullable|string',
            'shipping' => 'required|array',
            'shipping.courier_company' => 'required|string',
            'shipping.courier_service_name' => 'required|string',
            'shipping.price' => 'required|numeric',
            'notes' => 'nullable|string|max:500',
        ]);

        $sessionId = $request->session()->getId();
        $user = Auth::user();
        $cart = $this->cartService->getCart($user, $sessionId);

        try {
            // 1. Create order within atomic DB transaction and lock stock
            $order = $this->orderService->createOrderFromCart(
                $cart,
                $request->input('address'),
                $request->input('shipping'),
                $user,
                $request->input('guest_email'),
                $request->input('guest_name'),
                $request->input('guest_phone'),
                $request->input('notes')
            );

            // 2. Generate Midtrans Snap token
            $transactionResult = $this->paymentService->createTransaction($order);
            $snapToken = $transactionResult['token'] ?? '';
            $redirectUrl = $transactionResult['redirect_url'] ?? '';

            // Clean checkout session data
            Session::forget('checkout_data');

            // If test sandbox simulation is requested, immediately mark as paid
            if ($request->boolean('simulate_success')) {
                $payment = $order->payment;
                if ($payment) {
                    $payment->update([
                        'status' => 'success',
                        'payment_method' => 'simulation_sandbox',
                        'raw_payload' => ['simulated' => true, 'timestamp' => now()->toIso8601String()],
                        'paid_at' => now(),
                    ]);
                } else {
                    $order->payment()->create([
                        'gateway' => 'midtrans',
                        'gateway_reference' => 'SIM-'.$order->order_number,
                        'payment_method' => 'simulation_sandbox',
                        'status' => 'success',
                        'amount' => $order->total,
                        'paid_at' => now(),
                        'raw_payload' => ['simulated' => true, 'timestamp' => now()->toIso8601String()],
                    ]);
                }
                $order->update([
                    'status' => 'paid',
                ]);
            }

            return response()->json([
                'success' => true,
                'order_number' => $order->order_number,
                'order_id' => $order->id,
                'snap_token' => $snapToken,
                'redirect_url' => $redirectUrl,
                'simulated' => $request->boolean('simulate_success'),
                'success_url' => route('orders.success', $order->order_number),
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'errors' => $e->errors(),
            ], 422);
        } catch (Throwable $e) {
            Log::error('Checkout process exception: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses pesanan Anda: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Simulate successful payment for test / sandbox demonstration.
     */
    public function simulatePayment(Order $order): JsonResponse|RedirectResponse
    {
        $payment = $order->payment;
        if ($payment) {
            $payment->update([
                'status' => 'success',
                'payment_method' => 'simulation_sandbox',
                'raw_payload' => ['simulated' => true, 'timestamp' => now()->toIso8601String()],
                'paid_at' => now(),
            ]);
        } else {
            $order->payment()->create([
                'gateway' => 'midtrans',
                'gateway_reference' => 'SIM-'.$order->order_number,
                'payment_method' => 'simulation_sandbox',
                'status' => 'success',
                'amount' => $order->total,
                'paid_at' => now(),
                'raw_payload' => ['simulated' => true, 'timestamp' => now()->toIso8601String()],
            ]);
        }

        $order->update([
            'status' => 'paid',
        ]);

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Simulasi pembayaran berhasil! Status pesanan kini telah Terbayar.',
                'order_status' => $order->status,
                'success_url' => route('orders.success', $order->order_number),
            ]);
        }

        return redirect()->route('orders.success', $order->order_number)->with('success', 'Simulasi pembayaran berhasil! Status pesanan kini telah Terbayar.');
    }

    /**
     * Display order confirmation and success page.
     */
    public function success(string $order_number): View|RedirectResponse
    {
        $order = Order::with(['items.variant.product.images', 'payment', 'shipment', 'user'])
            ->where('order_number', $order_number)
            ->firstOrFail();

        // Optional authorization check: if user is logged in, ensure order belongs to them
        if (Auth::check() && $order->user_id && $order->user_id !== Auth::id()) {
            abort(403, 'Akses tidak diizinkan.');
        }

        // Get Snap Token for "Bayar Lagi" if still pending_payment
        $snapToken = null;
        if ($order->status === 'pending_payment') {
            $transaction = $this->paymentService->createTransaction($order);
            $snapToken = $transaction['token'] ?? null;
        }

        return view('checkout.success', compact('order', 'snapToken'));
    }

    /**
     * Retry payment / get new snap token for pending order.
     */
    public function retryPayment(Order $order): JsonResponse
    {
        if (Auth::check() && $order->user_id && $order->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        if ($order->isPaid()) {
            return response()->json(['success' => false, 'message' => 'Pesanan sudah dibayar.'], 400);
        }

        $transaction = $this->paymentService->createTransaction($order);

        return response()->json([
            'success' => true,
            'snap_token' => $transaction['token'],
            'redirect_url' => $transaction['redirect_url'],
        ]);
    }
}
