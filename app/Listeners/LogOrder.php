<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use Illuminate\Support\Facades\Log;

class LogOrder
{
    public function handle(OrderPlaced $event): void
    {
        $order = $event->order;
        
        Log::info('Order created', [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'vendor' => $order->vendor->name,
            'customer' => $order->user->name,
            'items_count' => $order->items->count(),
            'total' => $order->total_amount,
        ]);
    }
}
