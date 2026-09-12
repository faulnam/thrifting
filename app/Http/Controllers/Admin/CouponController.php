<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CouponController extends Controller
{
    public function index(Request $request): View
    {
        $query = Coupon::query()->latest();

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where('code', 'like', "%{$search}%");
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $coupons = $query->paginate(15)->withQueryString();

        return view('admin.coupons.index', compact('coupons'));
    }

    public function create(): View
    {
        return view('admin.coupons.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:coupons,code'],
            'type' => ['required', 'in:percent,fixed'],
            'value' => ['required', 'numeric', 'min:0', $request->type === 'percent' ? 'max:100' : 'nullable'],
            'min_purchase' => ['nullable', 'numeric', 'min:0'],
            'max_discount' => ['nullable', 'numeric', 'min:0'],
            'starts_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'expires_at.after_or_equal' => 'Tanggal berakhir kupon tidak boleh lebih awal dari tanggal mulai.',
            'code.unique' => 'Kode kupon promo sudah terdaftar.',
        ]);

        $validated['code'] = strtoupper(trim($validated['code']));
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['starts_at'] = $validated['starts_at'] ?? now();
        $validated['expires_at'] = $validated['expires_at'] ?? now()->addYear();

        Coupon::create($validated);

        return redirect()->route('admin.coupons.index')
            ->with('success', "Kupon promo '{$validated['code']}' berhasil dibuat.");
    }

    public function edit(Coupon $coupon): View
    {
        return view('admin.coupons.edit', compact('coupon'));
    }

    public function update(Request $request, Coupon $coupon): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', Rule::unique('coupons', 'code')->ignore($coupon->id)],
            'type' => ['required', 'in:percent,fixed'],
            'value' => ['required', 'numeric', 'min:0', $request->type === 'percent' ? 'max:100' : 'nullable'],
            'min_purchase' => ['nullable', 'numeric', 'min:0'],
            'max_discount' => ['nullable', 'numeric', 'min:0'],
            'starts_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'expires_at.after_or_equal' => 'Tanggal berakhir kupon tidak boleh lebih awal dari tanggal mulai.',
            'code.unique' => 'Kode kupon promo sudah terdaftar.',
        ]);

        $validated['code'] = strtoupper(trim($validated['code']));
        $validated['is_active'] = $request->boolean('is_active');
        $validated['starts_at'] = $validated['starts_at'] ?? $coupon->starts_at ?? now();
        $validated['expires_at'] = $validated['expires_at'] ?? $coupon->expires_at ?? now()->addYear();

        $coupon->update($validated);

        return redirect()->route('admin.coupons.index')
            ->with('success', "Kupon promo '{$coupon->code}' berhasil diperbarui.");
    }

    public function destroy(Coupon $coupon): RedirectResponse
    {
        $code = $coupon->code;
        $coupon->delete();

        return redirect()->route('admin.coupons.index')
            ->with('success', "Kupon promo '{$code}' berhasil dihapus.");
    }

    public function toggle(Coupon $coupon): RedirectResponse
    {
        $coupon->update(['is_active' => ! $coupon->is_active]);

        $status = $coupon->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->back()
            ->with('success', "Kupon '{$coupon->code}' berhasil {$status}.");
    }
}
