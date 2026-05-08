<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('buyer', 'items.product', 'payment');

        if ($request->filled('status')) {
            $query->where('status', OrderStatus::from($request->status));
        }

        if ($request->filled('search')) {
            $query->where('id', 'like', "%{$request->search}%")
                ->orWhereHas('buyer', fn($q) => $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%"));
        }

        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to);
        }

        $orders = $query->latest()->paginate(20);

        $orderCounts = collect(OrderStatus::cases())->mapWithKeys(function ($status) {
            return [$status->value => Order::where('status', $status)->count()];
        });

        return view('admin.orders', compact('orders', 'orderCounts'));
    }

    public function show(Order $order)
    {
        $order->load('buyer', 'items.product.seller', 'payment', 'dispute');

        return view('admin.orders', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|string',
        ]);

        $newStatus = OrderStatus::from($request->status);

        if (!$order->status->canTransitionTo($newStatus)) {
            return back()->with('error', 'Status tidak valid');
        }

        $order->update(['status' => $newStatus]);

        return back()->with('success', 'Status pesanan diperbarui');
    }
}