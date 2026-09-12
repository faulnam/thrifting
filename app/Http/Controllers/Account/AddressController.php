<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Services\BiteshipService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AddressController extends Controller
{
    public function __construct(
        protected BiteshipService $biteshipService
    ) {}

    /**
     * Display a listing of the user's addresses.
     */
    public function index(): View
    {
        $addresses = Auth::user()->addresses()->orderByDesc('is_default')->latest()->get();

        return view('account.addresses.index', compact('addresses'));
    }

    /**
     * Show the form for creating a new address.
     */
    public function create(): View
    {
        return view('account.addresses.create');
    }

    /**
     * Store a newly created address in storage.
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'label' => 'nullable|string|max:50',
            'recipient_name' => 'required|string|max:100',
            'phone' => 'required|string|max:25',
            'province' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'district' => 'required|string|max:100',
            'postal_code' => 'required|string|max:10',
            'address_line' => 'required|string|max:500',
            'biteship_area_id' => 'nullable|string|max:100',
            'is_default' => 'nullable|boolean',
        ]);

        $user = Auth::user();
        $isFirstAddress = $user->addresses()->count() === 0;
        $isDefault = $request->boolean('is_default') || $isFirstAddress;

        $address = DB::transaction(function () use ($user, $validated, $isDefault) {
            if ($isDefault) {
                $user->addresses()->update(['is_default' => false]);
            }

            return $user->addresses()->create([
                'label' => $validated['label'] ?: 'Rumah',
                'recipient_name' => $validated['recipient_name'],
                'phone' => $validated['phone'],
                'province' => $validated['province'],
                'city' => $validated['city'],
                'district' => $validated['district'],
                'postal_code' => $validated['postal_code'],
                'address_line' => $validated['address_line'],
                'biteship_area_id' => $validated['biteship_area_id'] ?? null,
                'is_default' => $isDefault,
            ]);
        });

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Alamat berhasil ditambahkan.',
                'address' => $address,
            ]);
        }

        return redirect()->route('account.addresses.index')->with('success', 'Alamat berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified address.
     */
    public function edit(Address $address): View
    {
        if ($address->user_id !== Auth::id()) {
            abort(403, 'Akses tidak diizinkan.');
        }

        return view('account.addresses.edit', compact('address'));
    }

    /**
     * Update the specified address in storage.
     */
    public function update(Request $request, Address $address): RedirectResponse|JsonResponse
    {
        if ($address->user_id !== Auth::id()) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $validated = $request->validate([
            'label' => 'nullable|string|max:50',
            'recipient_name' => 'required|string|max:100',
            'phone' => 'required|string|max:25',
            'province' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'district' => 'required|string|max:100',
            'postal_code' => 'required|string|max:10',
            'address_line' => 'required|string|max:500',
            'biteship_area_id' => 'nullable|string|max:100',
            'is_default' => 'nullable|boolean',
        ]);

        $user = Auth::user();
        $isDefault = $request->boolean('is_default');

        DB::transaction(function () use ($user, $address, $validated, $isDefault) {
            if ($isDefault) {
                $user->addresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
            }

            $address->update([
                'label' => $validated['label'] ?: 'Rumah',
                'recipient_name' => $validated['recipient_name'],
                'phone' => $validated['phone'],
                'province' => $validated['province'],
                'city' => $validated['city'],
                'district' => $validated['district'],
                'postal_code' => $validated['postal_code'],
                'address_line' => $validated['address_line'],
                'biteship_area_id' => $validated['biteship_area_id'] ?? $address->biteship_area_id,
                'is_default' => $isDefault ? true : $address->is_default,
            ]);
        });

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Alamat berhasil diperbarui.',
                'address' => $address->fresh(),
            ]);
        }

        return redirect()->route('account.addresses.index')->with('success', 'Alamat berhasil diperbarui.');
    }

    /**
     * Remove the specified address from storage.
     */
    public function destroy(Address $address): RedirectResponse|JsonResponse
    {
        if ($address->user_id !== Auth::id()) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $wasDefault = $address->is_default;
        $address->delete();

        // If deleted address was default, set another address as default
        if ($wasDefault) {
            $nextAddress = Auth::user()->addresses()->first();
            if ($nextAddress) {
                $nextAddress->update(['is_default' => true]);
            }
        }

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Alamat berhasil dihapus.',
            ]);
        }

        return redirect()->route('account.addresses.index')->with('success', 'Alamat berhasil dihapus.');
    }

    /**
     * Set the address as default.
     */
    public function setDefault(Address $address): RedirectResponse|JsonResponse
    {
        if ($address->user_id !== Auth::id()) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $user = Auth::user();
        DB::transaction(function () use ($user, $address) {
            $user->addresses()->update(['is_default' => false]);
            $address->update(['is_default' => true]);
        });

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Alamat utama berhasil diubah.',
            ]);
        }

        return redirect()->route('account.addresses.index')->with('success', 'Alamat utama berhasil diubah.');
    }

    /**
     * Autocomplete areas endpoint for address form.
     */
    public function searchAreas(Request $request): JsonResponse
    {
        $query = (string) $request->query('query', $request->query('q', ''));
        if (mb_strlen(trim($query)) < 3) {
            return response()->json([
                'success' => false,
                'message' => 'Ketik minimal 3 karakter untuk mencari area kecamatan/kota.',
                'areas' => [],
            ]);
        }

        $areas = $this->biteshipService->searchAreas($query);

        return response()->json([
            'success' => true,
            'areas' => $areas,
        ]);
    }
}
