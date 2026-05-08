<?php

namespace App\Mail;

use App\Models\Dispute;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DisputeCreatedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Dispute $dispute) {}

    public function build(): self
    {
        return $this->subject('Dispute Baru - Pesanan #' . $this->dispute->order?->order_number)
            ->markdown('emails.dispute-created', [
                'dispute' => $this->dispute,
            ]);
    }
}