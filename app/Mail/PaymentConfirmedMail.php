<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentConfirmedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order, public ?Payment $payment = null) {}

    public function build(): self
    {
        return $this->subject('Pembayaran Dikonfirmasi - Pesanan #' . $this->order->order_number)
            ->markdown('emails.payment-confirmed', [
                'order' => $this->order,
            ]);
    }
}