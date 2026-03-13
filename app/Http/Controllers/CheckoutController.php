<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Services\CartService;
use App\Services\CheckoutService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    protected CartService $cartService;
    protected CheckoutService $checkoutService;

    public function __construct(CartService $cartService, CheckoutService $checkoutService)
    {
        $this->cartService = $cartService;
        $this->checkoutService = $checkoutService;
    }

    public function index()
    {
        $cartGrouped = $this->cartService->getCartGroupedByVendor();
        
        if ($cartGrouped->isEmpty()) {
            return redirect()->route('products.index')->with('error', 'Your cart is empty');
        }

        $checkoutSummary = $this->checkoutService->getCheckoutSummary();

        return view('checkout.index', $checkoutSummary);
    }

    public function process(CheckoutRequest $request): RedirectResponse
    {
        try {
            $orders = $this->checkoutService->processCheckout($request->validated());

            $orderCount = count($orders);
            $totalAmount = array_sum(array_map(fn($o) => $o->total_amount, $orders));

            return redirect()->route('checkout.success')
                ->with('success', "Order placed successfully! {$orderCount} order(s) created. Total: ₹" . number_format($totalAmount, 2));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function success(): View
    {
        return view('checkout.success');
    }
}
