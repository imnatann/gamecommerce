<?php

namespace App\Http\Controllers\Seller;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $seller = Auth::user();

        $query = OrderItem::where('seller_id', $seller->id)
            ->with(['order.buyer', 'product']);

        if ($request->filled('status')) {
            $query->where('status', OrderStatus::from($request->status));
        }

        if ($request->filled('search')) {
            $query->whereHas('order', function ($q) use ($request) {
                $q->where('id', 'like', "%{$request->search}%");
            });
        }

        $orderItems = $query->latest()->paginate(20);

        $statusCounts = collect(OrderStatus::cases())->mapWithKeys(function ($status) use ($seller) {
            return [$status->value => OrderItem::where('seller_id', $seller->id)->where('status', $status)->count()];
        });

        return view('seller.orders', compact('orderItems', 'statusCounts'));
    }

    public function updateStatus(Request $request, OrderItem $orderItem)
    {
        if ($orderItem->seller_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'status' => 'required|string|in:processing,delivered,completed',
            'delivery_data' => 'nullable|array',
        ]);

        $newStatus = OrderStatus::from($request->status);

        if (!$orderItem->status->canTransitionTo($newStatus)) {
            return back()->with('error', 'Status tidak valid');
        }

        $orderItem->update([
            'status' => $newStatus,
            'delivery_data' => $request->delivery_data,
        ]);

        return back()->with('success', 'Status pesanan diperbarui');
    }
}