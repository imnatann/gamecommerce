<?php

namespace App\Http\Controllers\Api\V1\Seller;

use App\Http\Controllers\Api\ApiBaseController;
use App\Enums\OrderStatus;
use App\Enums\WalletTransactionType;
use App\Models\Order;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SellerEarningController extends ApiBaseController
{
    public function overview(Request $request): JsonResponse
    {
        $sellerId = $request->user()->id;

        $totalEarnings = Order::whereHas('items.product', fn ($q) => $q->where('seller_id', $sellerId))
            ->where('status', OrderStatus::COMPLETED->value)
            ->sum('total_amount');

        $pendingEarnings = Order::whereHas('items.product', fn ($q) => $q->where('seller_id', $sellerId))
            ->whereIn('status', [OrderStatus::DELIVERED->value])
            ->sum('total_amount');

        $totalOrders = Order::whereHas('items.product', fn ($q) => $q->where('seller_id', $sellerId))
            ->count();

        $completedOrders = Order::whereHas('items.product', fn ($q) => $q->where('seller_id', $sellerId))
            ->where('status', OrderStatus::COMPLETED->value)
            ->count();

        $wallet = Wallet::firstOrCreate(['user_id' => $sellerId], ['balance' => 0]);

        $platformFee = config('gamecommerce.platform_fee', 5);

        return $this->successResponse([
            'total_earnings' => $totalEarnings * (1 - $platformFee / 100),
            'pending_earnings' => $pendingEarnings * (1 - $platformFee / 100),
            'available_balance' => $wallet->balance,
            'total_orders' => $totalOrders,
            'completed_orders' => $completedOrders,
            'platform_fee_percent' => $platformFee,
        ]);
    }

    public function earningHistory(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 20);
        $period = $request->query('period', 'all');

        $query = WalletTransaction::where('user_id', $request->user()->id)
            ->where('type', WalletTransactionType::EARNING->value)
            ->with(['order' => fn ($q) => $q->with(['buyer', 'items.product.game'])]);

        $query = match ($period) {
            'today' => $query->whereDate('created_at', today()),
            'week' => $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]),
            'month' => $query->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year),
            default => $query,
        };

        $transactions = $query->orderByDesc('created_at')->paginate($perPage);

        return $this->paginateResponse($transactions);
    }

    public function requestWithdrawal(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:10000',
            'bank_name' => 'required|string|max:100',
            'bank_account' => 'required|string|max:50',
            'bank_holder' => 'required|string|max:255',
        ]);

        $wallet = Wallet::where('user_id', $request->user()->id)->first();

        if (!$wallet || $wallet->balance < $validated['amount']) {
            return $this->errorResponse('Saldo tidak mencukupi.', 400);
        }

        $minWithdrawal = config('gamecommerce.min_withdrawal', 10000);
        if ($validated['amount'] < $minWithdrawal) {
            return $this->errorResponse("Minimal penarikan adalah Rp " . number_format($minWithdrawal) . ".", 400);
        }

        try {
            DB::beginTransaction();

            WalletTransaction::create([
                'user_id' => $request->user()->id,
                'type' => WalletTransactionType::WITHDRAWAL->value,
                'amount' => $validated['amount'],
                'balance_before' => $wallet->balance,
                'balance_after' => $wallet->balance - $validated['amount'],
                'description' => "Penarikan ke {$validated['bank_name']} - {$validated['bank_holder']}",
                'metadata' => [
                    'bank_name' => $validated['bank_name'],
                    'bank_account' => $validated['bank_account'],
                    'bank_holder' => $validated['bank_holder'],
                ],
            ]);

            $wallet->decrement('balance', $validated['amount']);

            DB::commit();

            return $this->successResponse(null, 'Permintaan penarikan berhasil diajukan.');
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->errorResponse('Gagal mengajukan penarikan.', 500);
        }
    }

    public function earningsByPeriod(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'period' => 'required|in:daily,weekly,monthly',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        $sellerId = $request->user()->id;
        $period = $validated['period'];
        $dateFrom = $validated['date_from'] ?? now()->subMonths(3)->toDateString();
        $dateTo = $validated['date_to'] ?? now()->toDateString();

        $dateFormat = match ($period) {
            'daily' => '%Y-%m-%d',
            'weekly' => '%Y-%u',
            'monthly' => '%Y-%m',
        };

        $earnings = Order::whereHas('items.product', fn ($q) => $q->where('seller_id', $sellerId))
            ->where('status', OrderStatus::COMPLETED->value)
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->selectRaw("DATE_FORMAT(created_at, '{$dateFormat}') as period, SUM(total_amount) as total, COUNT(*) as order_count")
            ->groupByRaw("DATE_FORMAT(created_at, '{$dateFormat}')")
            ->orderBy('period')
            ->get();

        return $this->successResponse($earnings);
    }
}