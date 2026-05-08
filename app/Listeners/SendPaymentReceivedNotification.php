<?php

namespace App\Listeners;

use App\Events\PaymentReceived;
use App\Mail\PaymentConfirmedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendPaymentReceivedNotification implements ShouldQueue
{
    public function __construct() {}

    public function handle(PaymentReceived $event): void
    {
        $order = $event->order;
        $payment = $event->payment;
        $buyer = $order->buyer;

        if ($buyer) {
            Mail::to($buyer)->send(new PaymentConfirmedMail($order, $payment));
        }

        $seller = $order->items->first()?->seller;
        if ($seller) {
            Log::info('Payment received notification sent to seller', [
                'order_id' => $order->id,
                'seller_id' => $seller->id,
                'payment_id' => $payment->id,
            ]);
        }

        Log::info('Payment received notification sent', [
            'order_id' => $order->id,
            'payment_id' => $payment->id,
        ]);
    }
}