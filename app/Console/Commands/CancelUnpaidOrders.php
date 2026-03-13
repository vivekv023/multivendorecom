<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CancelUnpaidOrders extends Command
{
    protected $signature = 'orders:cancel-unpaid 
                            {--minutes=30 : Minutes after which unpaid orders are cancelled}
                            {--dry-run : Run without making changes}';

    protected $description = 'Cancel unpaid orders after specified minutes';

    public function handle(): int
    {
        $minutes = (int) $this->option('minutes');
        $dryRun = $this->option('dry-run');
        
        $this->info("Checking for unpaid orders older than {$minutes} minutes...");
        
        $unpaidOrders = Order::where('status', 'pending')
            ->whereHas('payment', function ($query) {
                $query->where('status', 'pending');
            })
            ->where('created_at', '<=', now()->subMinutes($minutes))
            ->get();

        if ($unpaidOrders->isEmpty()) {
            $this->info('No unpaid orders found to cancel.');
            return Command::SUCCESS;
        }

        $this->info("Found {$unpaidOrders->count()} unpaid order(s) to cancel.");

        if ($dryRun) {
            $this->warn('DRY RUN - No changes will be made.');
            foreach ($unpaidOrders as $order) {
                $this->line("Would cancel order: {$order->order_number} (Created: {$order->created_at})");
            }
            return Command::SUCCESS;
        }

        $cancelled = 0;
        foreach ($unpaidOrders as $order) {
            try {
                \DB::transaction(function () use ($order) {
                    $order->update(['status' => 'cancelled']);
                    
                    foreach ($order->items as $item) {
                        $item->product->increment('stock', $item->quantity);
                    }
                    
                    if ($order->payment) {
                        $order->payment->update(['status' => 'refunded']);
                    }
                });

                Log::info('Order auto-cancelled due to unpaid status', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'vendor_id' => $order->vendor_id,
                    'cancelled_at' => now()->toISOString(),
                ]);

                $this->line("Cancelled order: {$order->order_number}");
                $cancelled++;
            } catch (\Exception $e) {
                $this->error("Failed to cancel order {$order->order_number}: {$e->getMessage()}");
                Log::error('Failed to cancel unpaid order', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->info("Successfully cancelled {$cancelled} order(s).");
        
        return Command::SUCCESS;
    }
}
