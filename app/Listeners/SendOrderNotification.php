<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use Illuminate\Support\Facades\Log;

class SendOrderNotification
{
    public function handle(OrderPlaced $event): void
    {
        $order = $event->order;
        
        Log::info('Order placed notification', [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'customer_id' => $order->user_id,
            'vendor_id' => $order->vendor_id,
            'total_amount' => $order->total_amount,
        ]);
    }
}
