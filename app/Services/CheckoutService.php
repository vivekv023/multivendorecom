<?php

namespace App\Services;

use App\Events\OrderPlaced;
use App\Events\PaymentSucceeded;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutService
{
    public function __construct(
        protected CartService $cartService
    ) {}

    public function processCheckout(array $data): array
    {
        $cart = $this->cartService->getCartWithItems();

        if (!$cart) {
            throw new \Exception('Cart is empty');
        }

        $items = is_array($cart) ? $cart['items'] : $cart->items;

        if ($items->isEmpty()) {
            throw new \Exception('Cart is empty');
        }

        $this->validateCartStock($cart);
        $groupedItems = $this->groupCartItemsByVendor($cart);

        $orders = [];
        $payments = [];

        DB::transaction(function () use ($groupedItems, $data, &$orders, &$payments) {
            foreach ($groupedItems as $vendorGroup) {
                $order = $this->createOrder($vendorGroup, $data);
                $this->createOrderItems($order, $vendorGroup['items']);
                $this->deductProductStock($vendorGroup['items']);
                
                $payment = $this->createPayment($order, $vendorGroup['total'], $data['payment_method'] ?? 'cash_on_delivery');
                
                $orders[] = $order;
                $payments[] = $payment;
            }

            $this->cartService->clearCart();
        });

        foreach ($orders as $order) {
            event(new OrderPlaced($order));
        }

        return $orders;
    }

    public function validateCartStock($cart): void
    {
        $items = is_array($cart) ? $cart['items'] : $cart->items;
        
        foreach ($items as $item) {
            $productId = $item->product->id;
            $product = Product::find($productId);
            
            if (!$product) {
                throw new \Exception("Product not found: {$productId}");
            }
            
            if ($item->quantity > $product->stock) {
                throw new \Exception(
                    "Insufficient stock for product: {$product->name}. " .
                    "Requested: {$item->quantity}, Available: {$product->stock}"
                );
            }
        }
    }

    public function groupCartItemsByVendor($cart): \Illuminate\Support\Collection
    {
        $items = is_array($cart) ? $cart['items'] : $cart->items;
        
        return $items->groupBy(function ($item) {
            $product = $item->product;
            return $product->vendor->id ?? $product->vendor_id;
        })->map(function ($items, $vendorId) {
            $firstItem = $items->first();
            $vendor = $firstItem->product->vendor;
            $total = $items->sum(function ($item) {
                return $item->product->price * $item->quantity;
            });

            return [
                'vendor' => is_object($vendor) ? $vendor : (object)['id' => $vendorId, 'name' => $vendor['name'] ?? 'Vendor'],
                'items' => $items,
                'total' => $total,
                'item_count' => $items->sum('quantity'),
            ];
        })->values();
    }

    protected function createOrder(array $vendorGroup, array $data): Order
    {
        $vendor = $vendorGroup['vendor'];
        $vendorId = is_object($vendor) ? $vendor->id : ($vendor['id'] ?? null);
        
        $userId = auth()->id();
        
        return Order::create([
            'user_id' => $userId,
            'vendor_id' => $vendorId,
            'order_number' => $this->generateOrderNumber(),
            'total_amount' => $vendorGroup['total'],
            'status' => 'pending',
            'shipping_address' => $data['shipping_address'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);
    }

    protected function createOrderItems(Order $order, $items): void
    {
        foreach ($items as $item) {
            $price = $item->product->price;
            $quantity = $item->quantity;
            $subtotal = $price * $quantity;

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item->product_id,
                'price' => $price,
                'quantity' => $quantity,
                'subtotal' => $subtotal,
            ]);
        }
    }

    protected function deductProductStock($items): void
    {
        foreach ($items as $item) {
            Product::where('id', $item->product_id)
                ->decrement('stock', $item->quantity);
        }
    }

    protected function createPayment(Order $order, float $amount, string $paymentMethod = 'cash_on_delivery'): Payment
    {
        $transactionId = $this->generateTransactionId();
        
        $isPaid = in_array($paymentMethod, ['credit_card', 'debit_card', 'paypal', 'bank_transfer']);
        
        $paymentData = [
            'order_id' => $order->id,
            'amount' => $amount,
            'status' => $isPaid ? 'paid' : 'pending',
            'payment_method' => $paymentMethod,
            'transaction_id' => $transactionId,
            'payment_details' => json_encode([
                'paid_at' => $isPaid ? now()->toISOString() : null,
                'payment_method' => $this->getPaymentMethodName($paymentMethod),
                'transaction_id' => $transactionId,
                'currency' => 'USD',
            ]),
        ];

        $payment = Payment::create($paymentData);

        if ($isPaid) {
            event(new PaymentSucceeded($payment));
        }

        return $payment;
    }

    protected function generateTransactionId(): string
    {
        return 'TXN-' . strtoupper(Str::random(16)) . '-' . time();
    }

    protected function getPaymentMethodName(string $method): string
    {
        $methods = [
            'cash_on_delivery' => 'Cash on Delivery',
            'credit_card' => 'Credit Card',
            'debit_card' => 'Debit Card',
            'paypal' => 'PayPal',
            'bank_transfer' => 'Bank Transfer',
        ];

        return $methods[$method] ?? ucfirst(str_replace('_', ' ', $method));
    }

    protected function generateOrderNumber(): string
    {
        return 'ORD-' . date('Ymd') . '-' . strtoupper(Str::random(6));
    }

    public function getCheckoutSummary()
    {
        $grouped = $this->cartService->getCartGroupedByVendor();
        
        $subtotal = $this->cartService->getCartTotal();
        $totalOrders = $grouped->count();
        $totalItems = $grouped->sum('item_count');

        return [
            'vendors' => $grouped,
            'subtotal' => $subtotal,
            'total_orders' => $totalOrders,
            'total_items' => $totalItems,
        ];
    }

    public static function getAvailablePaymentMethods(): array
    {
        return [
            'cash_on_delivery' => 'Cash on Delivery',
            'credit_card' => 'Credit Card',
            'debit_card' => 'Debit Card',
            'paypal' => 'PayPal',
            'bank_transfer' => 'Bank Transfer',
        ];
    }
}
