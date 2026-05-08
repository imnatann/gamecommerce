<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Api\ApiBaseController;
use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends ApiBaseController
{
    public function index(): JsonResponse
    {
        $stats = Cache::remember('admin.dashboard.stats', 300, fn () => [
            'total_revenue' => Order::where('status', OrderStatus::COMPLETED->value)->sum('total_amount'),
            'total_orders' => Order::count(),
            'total_users' => User::count(),
            'total_sellers' => User::role('seller')->count(),
            'total_products' => Product::where('is_active', true)->count(),
            'total_disputes' => \App\Models\Dispute::where('status', 'open')->count(),
        ]);

        $todayStats = Cache::remember('admin.dashboard.today', 60, fn () => [
            'today_revenue' => Order::where('status', OrderStatus::COMPLETED->value)
                ->whereDate('created_at', today())
                ->sum('total_amount'),
            'today_orders' => Order::whereDate('created_at', today())->count(),
            'today_users' => User::whereDate('created_at', today())->count(),
        ]);

        $orderStatusCounts = Cache::remember('admin.dashboard.order_statuses', 300, fn () => Order::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray());

        $topProducts = Cache::remember('admin.dashboard.top_products', 600, fn () => Product::with(['game', 'seller'])
            ->orderByDesc('sold_count')
            ->limit(10)
            ->get());

        $recentOrders = Order::with(['buyer', 'items.product.game'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return $this->successResponse([
            'stats' => $stats,
            'today_stats' => $todayStats,
            'order_status_counts' => $orderStatusCounts,
            'top_products' => $topProducts,
            'recent_orders' => $recentOrders,
        ]);
    }

    public function revenueChart(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'period' => 'nullable|in:daily,weekly,monthly',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        $period = $validated['period'] ?? 'daily';
        $dateFrom = $validated['date_from'] ?? now()->subDays(30)->toDateString();
        $dateTo = $validated['date_to'] ?? now()->toDateString();

        $dateFormat = match ($period) {
            'weekly' => '%Y-%u',
            'monthly' => '%Y-%m',
            default => '%Y-%m-%d',
        };

        $revenue = Order::where('status', OrderStatus::COMPLETED->value)
            ->whereBetween('created_at', [$dateFrom, $dateTo . ' 23:59:59'])
            ->selectRaw("DATE_FORMAT(created_at, '{$dateFormat}') as date, SUM(total_amount) as revenue, COUNT(*) as orders")
            ->groupByRaw("DATE_FORMAT(created_at, '{$dateFormat}')")
            ->orderBy('date')
            ->get();

        return $this->successResponse($revenue);
    }
}
