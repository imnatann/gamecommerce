<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class VoucherController extends Controller
{
    public function index(Request $request)
    {
        $query = Voucher::query();

        if ($request->filled('search')) {
            $query->where('code', 'like', "%{$request->search}%");
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $vouchers = $query->latest()->paginate(20);

        return view('admin.vouchers', compact('vouchers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'nullable|string|max:50|unique:vouchers,code',
            'type' => 'required|string|in:percent,fixed,free_shipping',
            'discount_value' => 'required|integer|min:1',
            'min_purchase' => 'nullable|integer|min:0',
            'max_uses' => 'nullable|integer|min:1',
            'max_uses_per_user' => 'nullable|integer|min:1',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after:starts_at',
            'is_active' => 'boolean',
        ]);

        if (empty($validated['code'])) {
            $validated['code'] = strtoupper(Str::random(10));
        }

        $validated['is_active'] = $request->boolean('is_active', true);

        Voucher::create($validated);

        return redirect()->route('admin.vouchers.index')
            ->with('success', 'Voucher berhasil ditambahkan');
    }

    public function update(Request $request, Voucher $voucher)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:vouchers,code,' . $voucher->id,
            'type' => 'required|string|in:percent,fixed,free_shipping',
            'discount_value' => 'required|integer|min:1',
            'min_purchase' => 'nullable|integer|min:0',
            'max_uses' => 'nullable|integer|min:1',
            'max_uses_per_user' => 'nullable|integer|min:1',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after:starts_at',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $voucher->update($validated);

        return redirect()->route('admin.vouchers.index')
            ->with('success', 'Voucher berhasil diperbarui');
    }

    public function toggleActive(Voucher $voucher)
    {
        $voucher->update(['is_active' => !$voucher->is_active]);

        return back()->with('success', $voucher->is_active ? 'Voucher diaktifkan' : 'Voucher dinonaktifkan');
    }

    public function destroy(Voucher $voucher)
    {
        $voucher->delete();

        return redirect()->route('admin.vouchers.index')
            ->with('success', 'Voucher berhasil dihapus');
    }
}