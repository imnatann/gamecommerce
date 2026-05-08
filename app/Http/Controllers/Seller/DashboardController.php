<?php

namespace App\Http\Controllers\Seller;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $seller = Auth::user();

        $totalRevenue = OrderItem::where('seller_id', $seller->id)
            ->where('status', OrderStatus::COMPLETED)
            ->sum('price');

        $pendingOrders = OrderItem::where('seller_id', $seller->id)
            ->where('status', OrderStatus::PAID)
            ->count();

        $activeProducts = Product::where('seller_id', $seller->id)
            ->where('is_active', true)
            ->count();

        $totalProducts = Product::where('seller_id', $seller->id)->count();

        $recentOrders = OrderItem::where('seller_id', $seller->id)
            ->with(['order.buyer', 'product'])
            ->latest()
            ->take(10)
            ->get();

        $revenueByMonth = OrderItem::where('seller_id', $seller->id)
            ->where('status', OrderStatus::COMPLETED)
            ->select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
                DB::raw('SUM(price) as revenue')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->take(12)
            ->get()
            ->keyBy('month');

        return view('seller.dashboard', compact(
            'totalRevenue',
            'pendingOrders',
            'activeProducts',
            'totalProducts',
            'recentOrders',
            'revenueByMonth'
        ));
    }
}