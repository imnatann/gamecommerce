<?php

namespace App\Listeners;

use App\Enums\DeliveryType;
use App\Enums\OrderStatus;
use App\Events\ProductDelivered;
use App\Mail\ProductDeliveredMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendProductDeliveredNotification implements ShouldQueue
{
    public function __construct() {}

    public function handle(ProductDelivered $event): void
    {
        $order = $event->order;
        $buyer = $order->buyer;

        if ($buyer) {
            Mail::to($buyer)->send(new ProductDeliveredMail($order));
        }

        $hasInstantDelivery = $order->items->contains(function ($item) {
            return $item->product && $item->product->delivery_type === DeliveryType::INSTANT;
        });

        if ($hasInstantDelivery && $order->status === OrderStatus::DELIVERED) {
            $order->update(['status' => OrderStatus::COMPLETED]);
            Log::info('Order auto-completed (instant delivery)', [
                'order_id' => $order->id,
            ]);
        }

        Log::info('Product delivered notification sent', [
            'order_id' => $order->id,
        ]);
    }
}