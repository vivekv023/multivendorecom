<?php

namespace App\Listeners;

use App\Events\PaymentSucceeded;
use Illuminate\Support\Facades\Log;

class PaymentNotification
{
    public function handle(PaymentSucceeded $event): void
    {
        $payment = $event->payment;
        
        Log::info('Payment succeeded', [
            'payment_id' => $payment->id,
            'order_id' => $payment->order_id,
            'amount' => $payment->amount,
            'transaction_id' => $payment->transaction_id,
        ]);
    }
}
