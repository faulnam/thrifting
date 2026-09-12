<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductReviewController extends Controller
{
    public function store(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'title' => ['required', 'string', 'max:255'],
            'comment' => ['required', 'string', 'max:2000'],
        ]);

        $user = Auth::user();

        // Check if user already reviewed this product
        $existing = Review::where('product_id', $product->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existing) {
            return redirect()->back()->with('error', 'Anda sudah memberikan ulasan untuk produk ini sebelumnya.');
        }

        // Optional order_item linkage if purchased
        $orderItem = OrderItem::whereHas('order', function ($q) use ($user) {
            $q->where('user_id', $user->id)->whereIn('status', ['paid', 'processing', 'ready_to_ship', 'shipped', 'delivered', 'completed']);
        })->whereHas('variant', function ($q) use ($product) {
            $q->where('product_id', $product->id);
        })->latest()->first();

        Review::create([
            'product_id' => $product->id,
            'user_id' => $user->id,
            'order_item_id' => $orderItem?->id,
            'rating' => $validated['rating'],
            'title' => strip_tags($validated['title']),
            'comment' => strip_tags($validated['comment']),
            'is_approved' => false, // Requires admin moderation
        ]);

        return redirect()->back()->with('success', 'Terima kasih! Ulasan Anda berhasil dikirim dan sedang menunggu moderasi admin sebelum ditampilkan.');
    }
}
