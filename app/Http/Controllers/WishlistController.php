<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class WishlistController extends Controller
{
    /**
     * Display the authenticated user's wishlist.
     */
    public function index(Request $request): View
    {
        /** @var User $user */
        $user = Auth::user();

        $wishlists = Wishlist::with(['product.images', 'product.variants', 'product.category'])
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(12);

        return view('account.wishlist', compact('wishlists'));
    }

    /**
     * Toggle product in user's wishlist.
     */
    public function toggle(Request $request, Product $product): JsonResponse|RedirectResponse
    {
        if (! Auth::check()) {
            // Save the intended return URL
            $previousUrl = url()->previous();
            if ($previousUrl && ! str_contains($previousUrl, '/login') && ! str_contains($previousUrl, '/register')) {
                $request->session()->put('url.intended', $previousUrl);
            }

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'requires_auth' => true,
                    'redirect_url' => route('login'),
                    'message' => 'Silakan masuk terlebih dahulu untuk menyimpan produk ke Wishlist.',
                ], 401);
            }

            return redirect()->route('login')->with('warning', 'Silakan masuk terlebih dahulu untuk menyimpan produk ke Wishlist.');
        }

        /** @var User $user */
        $user = Auth::user();

        $existing = Wishlist::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $inWishlist = false;
            $msg = 'Produk telah dihapus dari Wishlist Anda.';
        } else {
            Wishlist::create([
                'user_id' => $user->id,
                'product_id' => $product->id,
            ]);
            $inWishlist = true;
            $msg = 'Produk berhasil ditambahkan ke Wishlist!';
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'in_wishlist' => $inWishlist,
                'message' => $msg,
            ]);
        }

        return back()->with('success', $msg);
    }

    /**
     * Return list of wishlisted product IDs for the current user.
     */
    public function ids(Request $request): JsonResponse
    {
        if (! Auth::check()) {
            return response()->json([]);
        }

        $ids = Wishlist::where('user_id', Auth::id())->pluck('product_id')->toArray();

        return response()->json($ids);
    }
}
