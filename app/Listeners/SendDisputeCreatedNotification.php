<?php

namespace App\Listeners;

use App\Events\DisputeCreated;
use App\Mail\DisputeCreatedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendDisputeCreatedNotification implements ShouldQueue
{
    public function __construct() {}

    public function handle(DisputeCreated $event): void
    {
        $dispute = $event->dispute;
        $order = $dispute->order;
        $buyer = $dispute->buyer;
        $seller = $dispute->seller;

        if ($seller) {
            Mail::to($seller)->send(new DisputeCreatedMail($dispute));
        }

        $admins = \App\Models\User::role('admin')->get();
        foreach ($admins as $admin) {
            Mail::to($admin)->send(new DisputeCreatedMail($dispute));
        }

        Log::info('Dispute created notification sent', [
            'dispute_id' => $dispute->id,
            'order_id' => $order?->id,
            'buyer_id' => $buyer?->id,
            'seller_id' => $seller?->id,
        ]);
    }
}