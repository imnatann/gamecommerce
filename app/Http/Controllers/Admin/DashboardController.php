<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalRevenue = Order::where('status', OrderStatus::COMPLETED)->sum('total_amount');
        $totalOrders = Order::count();
        $totalUsers = User::count();
        $totalProducts = Product::where('is_active', true)->count();

        $recentOrders = Order::with('buyer', 'items.product')
            ->latest()
            ->take(10)
            ->get();

        $recentUsers = User::latest()->take(10)->get();

        $ordersByMonth = Order::select(
            DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
            DB::raw('COUNT(*) as total'),
            DB::raw('SUM(CASE WHEN status = "completed" THEN total_amount ELSE 0 END) as revenue')
        )
            ->groupBy('month')
            ->orderBy('month')
            ->take(12)
            ->get();

        $pendingDisputes = \App\Models\Dispute::where('status', 'open')->count();

        return view('admin.dashboard', compact(
            'totalRevenue',
            'totalOrders',
            'totalUsers',
            'totalProducts',
            'recentOrders',
            'recentUsers',
            'ordersByMonth',
            'pendingDisputes'
        ));
    }
}