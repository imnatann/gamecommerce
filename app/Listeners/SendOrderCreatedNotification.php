<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use App\Mail\OrderCreatedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendOrderCreatedNotification implements ShouldQueue
{
    public function __construct() {}

    public function handle(OrderCreated $event): void
    {
        $order = $event->order;
        $buyer = $order->buyer;

        if ($buyer) {
            Mail::to($buyer)->send(new OrderCreatedMail($order));
        }

        Log::info('Order created notification sent', [
            'order_id' => $order->id,
            'buyer_id' => $order->buyer_id,
        ]);
    }
}