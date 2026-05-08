<?php

namespace App\Repositories;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class OrderRepository implements OrderRepositoryInterface
{
    public function findByBuyer(int $buyerId, OrderStatus $status = null, int $perPage = 15): LengthAwarePaginator
    {
        $builder = Order::where('buyer_id', $buyerId)
            ->with(['items.product.game', 'payment']);

        if ($status) {
            $builder->where('status', $status->value);
        }

        return $builder->orderByDesc('created_at')->paginate($perPage);
    }

    public function findBySeller(int $sellerId, OrderStatus $status = null, int $perPage = 15): LengthAwarePaginator
    {
        $builder = Order::whereHas('items.product', fn ($q) => $q->where('seller_id', $sellerId))
            ->with(['items.product.game', 'buyer', 'payment']);

        if ($status) {
            $builder->where('status', $status->value);
        }

        return $builder->orderByDesc('created_at')->paginate($perPage);
    }

    public function findByStatus(OrderStatus $status, int $perPage = 15): LengthAwarePaginator
    {
        return Order::where('status', $status->value)
            ->with(['items.product.game', 'buyer', 'payment'])
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    public function getStats(int $userId = null): array
    {
        $cacheKey = $userId ? "orders.stats.{$userId}" : 'orders.stats.global';

        return Cache::remember($cacheKey, 300, function () use ($userId) {
            $baseQuery = $userId
                ? Order::whereHas('items.product', fn ($q) => $q->where('seller_id', $userId))
                : Order::query();

            $totalOrders = (clone $baseQuery)->count();
            $totalRevenue = (clone $baseQuery)->where('status', OrderStatus::COMPLETED->value)->sum('total');
            $pendingOrders = (clone $baseQuery)->where('status', OrderStatus::PENDING->value)->count();
            $disputedOrders = (clone $baseQuery)->where('status', OrderStatus::DISPUTED->value)->count();

            $recentOrders = (clone $baseQuery)
                ->with(['items.product.game', 'buyer'])
                ->orderByDesc('created_at')
                ->limit(10)
                ->get();

            $dailyRevenue = (clone $baseQuery)
                ->where('status', OrderStatus::COMPLETED->value)
                ->whereBetween('created_at', [now()->subDays(30), now()])
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total) as revenue'))
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            return [
                'total_orders' => $totalOrders,
                'total_revenue' => $totalRevenue,
                'pending_orders' => $pendingOrders,
                'disputed_orders' => $disputedOrders,
                'recent_orders' => $recentOrders,
                'daily_revenue' => $dailyRevenue,
            ];
        });
    }
}