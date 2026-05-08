<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    public function view(User $user, Order $order): bool
    {
        return $user->id === $order->buyer_id
            || $order->items()->where('seller_id', $user->id)->exists()
            || $user->isAdmin();
    }

    public function updateStatus(User $user, Order $order): bool
    {
        return $order->items()->where('seller_id', $user->id)->exists()
            || $user->isAdmin();
    }

    public function dispute(User $user, Order $order): bool
    {
        $allowedStatuses = ['delivered', 'processing'];

        return $user->id === $order->buyer_id
            && in_array(
                $order->status instanceof \BackedEnum ? $order->status->value : $order->status,
                $allowedStatuses,
                true
            );
    }
}
