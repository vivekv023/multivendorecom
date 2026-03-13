<?php

namespace App\Providers;

use App\Events\OrderPlaced;
use App\Events\PaymentSucceeded;
use App\Listeners\LogOrder;
use App\Listeners\PaymentNotification;
use App\Listeners\SendOrderNotification;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        OrderPlaced::class => [
            SendOrderNotification::class,
            LogOrder::class,
        ],
        PaymentSucceeded::class => [
            PaymentNotification::class,
        ],
    ];

    public function boot(): void
    {
        Event::listen(OrderPlaced::class, SendOrderNotification::class);
        Event::listen(OrderPlaced::class, LogOrder::class);
        Event::listen(PaymentSucceeded::class, PaymentNotification::class);
    }
}
