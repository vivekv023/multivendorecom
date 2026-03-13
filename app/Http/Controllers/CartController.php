<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddToCartRequest;
use App\Models\CartItem;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function index()
    {
        $cartGrouped = $this->cartService->getCartGroupedByVendor();
        $cartTotal = $this->cartService->getCartTotal();
        $cartCount = $this->cartService->getCartItemCount();

        return view('cart.index', compact('cartGrouped', 'cartTotal', 'cartCount'));
    }

    public function add(AddToCartRequest $request): JsonResponse
    {
        try {
            $product = Product::findOrFail($request->product_id);
            $this->cartService->addToCart($product, $request->quantity);

            $cartCount = $this->cartService->getCartItemCount();

            return response()->json([
                'success' => true,
                'message' => 'Product added to cart successfully',
                'cart_count' => $cartCount,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function update(Request $request, CartItem $cartItem): JsonResponse
    {
        try {
            $this->cartService->updateQuantity($cartItem, $request->quantity);

            $cartTotal = $this->cartService->getCartTotal();
            $cartCount = $this->cartService->getCartItemCount();

            return response()->json([
                'success' => true,
                'message' => 'Cart updated successfully',
                'cart_total' => $cartTotal,
                'cart_count' => $cartCount,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function remove(CartItem $cartItem): JsonResponse
    {
        try {
            $this->cartService->removeItem($cartItem);

            $cartTotal = $this->cartService->getCartTotal();
            $cartCount = $this->cartService->getCartItemCount();

            return response()->json([
                'success' => true,
                'message' => 'Item removed from cart',
                'cart_total' => $cartTotal,
                'cart_count' => $cartCount,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function removeGuestItem(int $productId): JsonResponse
    {
        try {
            $this->cartService->removeItem($productId);

            $cartTotal = $this->cartService->getCartTotal();
            $cartCount = $this->cartService->getCartItemCount();

            return response()->json([
                'success' => true,
                'message' => 'Item removed from cart',
                'cart_total' => $cartTotal,
                'cart_count' => $cartCount,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function clear(): JsonResponse
    {
        try {
            $this->cartService->clearCart();

            return response()->json([
                'success' => true,
                'message' => 'Cart cleared successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function count(): JsonResponse
    {
        $cartCount = $this->cartService->getCartItemCount();

        return response()->json([
            'cart_count' => $cartCount,
        ]);
    }
}
