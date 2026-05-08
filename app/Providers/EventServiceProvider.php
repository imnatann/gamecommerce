<?php

namespace App\Providers;

use App\Events\DisputeCreated;
use App\Events\OrderCreated;
use App\Events\PaymentReceived;
use App\Events\ProductDelivered;
use App\Listeners\SendDisputeCreatedNotification;
use App\Listeners\SendOrderCreatedNotification;
use App\Listeners\SendPaymentReceivedNotification;
use App\Listeners\SendProductDeliveredNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as BaseEventServiceProvider;

class EventServiceProvider extends BaseEventServiceProvider
{
    protected $listen = [
        OrderCreated::class => [
            SendOrderCreatedNotification::class,
        ],
        PaymentReceived::class => [
            SendPaymentReceivedNotification::class,
        ],
        ProductDelivered::class => [
            SendProductDeliveredNotification::class,
        ],
        DisputeCreated::class => [
            SendDisputeCreatedNotification::class,
        ],
    ];
}