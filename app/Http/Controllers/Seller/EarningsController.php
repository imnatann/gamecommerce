<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EarningsController extends Controller
{
    public function index(Request $request)
    {
        $seller = Auth::user();
        $wallet = $seller->wallet;

        $period = $request->get('period', 'month');

        $earningsQuery = WalletTransaction::where('wallet_id', $wallet->id ?? 0)
            ->where('type', 'in')
            ->where('description', 'like', '%penjualan%');

        if ($period === 'week') {
            $earningsQuery->where('created_at', '>=', now()->startOfWeek());
        } elseif ($period === 'month') {
            $earningsQuery->where('created_at', '>=', now()->startOfMonth());
        } elseif ($period === 'year') {
            $earningsQuery->where('created_at', '>=', now()->startOfYear());
        }

        $earnings = $earningsQuery->latest()->paginate(20);
        $withdrawals = WalletTransaction::where('wallet_id', $wallet->id ?? 0)
            ->where('type', 'out')
            ->where('description', 'like', '%withdraw%')
            ->latest()
            ->take(10)
            ->get();

        $totalEarnings = WalletTransaction::where('wallet_id', $wallet->id ?? 0)
            ->where('type', 'in')
            ->sum('amount');

        $totalWithdrawn = WalletTransaction::where('wallet_id', $wallet->id ?? 0)
            ->where('type', 'out')
            ->where('description', 'like', '%withdraw%')
            ->sum('amount');

        $availableBalance = ($wallet->balance ?? 0);

        return view('seller.earnings', compact(
            'wallet',
            'earnings',
            'withdrawals',
            'totalEarnings',
            'totalWithdrawn',
            'availableBalance',
            'period'
        ));
    }

    public function requestWithdrawal(Request $request)
    {
        $request->validate([
            'amount' => 'required|integer|min:10000',
            'bank_name' => 'required|string|max:255',
            'bank_account' => 'required|string|max:255',
            'bank_holder' => 'required|string|max:255',
        ]);

        $seller = Auth::user();
        $wallet = $seller->wallet;

        if (!$wallet || !$wallet->hasSufficientBalance($request->amount)) {
            return back()->with('error', 'Saldo tidak mencukupi');
        }

        DB::transaction(function () use ($wallet, $request) {
            $wallet->withdraw($request->amount, 'Withdrawal request', 'withdrawal');
        });

        return back()->with('success', 'Permintaan withdrawal berhasil diajukan');
    }
}