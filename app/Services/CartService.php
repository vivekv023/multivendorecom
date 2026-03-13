<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartService
{
    const GUEST_CART_KEY = 'guest_cart';

    public function getOrCreateCart(): ?Cart
    {
        $user = Auth::user();
        
        if (!$user) {
            return null;
        }

        return Cart::firstOrCreate(['user_id' => $user->id]);
    }

    public function isGuest(): bool
    {
        return !Auth::check();
    }

    public function getGuestCart(): array
    {
        return Session::get(self::GUEST_CART_KEY, []);
    }

    protected function saveGuestCart(array $cart): void
    {
        Session::put(self::GUEST_CART_KEY, $cart);
    }

    public function addToCart(Product $product, int $quantity = 1): array
    {
        $this->validateStock($product, $quantity);

        if (Auth::check()) {
            return $this->addToUserCart($product, $quantity);
        }

        return $this->addToGuestCart($product, $quantity);
    }

    protected function addToUserCart(Product $product, int $quantity): array
    {
        $cart = $this->getOrCreateCart();
        
        if (!$cart) {
            throw new \Exception('Please login to add items to cart');
        }

        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $product->id)
            ->first();

        if ($cartItem) {
            $newQuantity = $cartItem->quantity + $quantity;
            $this->validateStock($product, $newQuantity, true);
            $cartItem->update(['quantity' => $newQuantity]);
        } else {
            $cartItem = CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'quantity' => $quantity,
            ]);
        }

        return [
            'type' => 'user',
            'item' => $cartItem
        ];
    }

    protected function addToGuestCart(Product $product, int $quantity): array
    {
        $cart = $this->getGuestCart();
        $productId = $product->id;

        if (isset($cart[$productId])) {
            $newQuantity = $cart[$productId]['quantity'] + $quantity;
            $this->validateStock($product, $newQuantity, true);
            $cart[$productId]['quantity'] = $newQuantity;
        } else {
            $cart[$productId] = [
                'product_id' => $product->id,
                'quantity' => $quantity,
                'price' => $product->price,
                'name' => $product->name,
                'image' => $product->image,
                'vendor_id' => $product->vendor_id,
            ];
        }

        $this->saveGuestCart($cart);

        return [
            'type' => 'guest',
            'item' => $cart[$productId]
        ];
    }

    public function updateQuantity($cartItem, int $quantity): array
    {
        if (is_numeric($cartItem)) {
            return $this->updateGuestCartQuantity($cartItem, $quantity);
        }

        $product = $cartItem->product;
        
        if ($quantity <= 0) {
            $this->removeItem($cartItem);
            return ['success' => true];
        }

        $this->validateStock($product, $quantity, true);
        $cartItem->update(['quantity' => $quantity]);

        return [
            'success' => true,
            'item' => $cartItem
        ];
    }

    protected function updateGuestCartQuantity(int $productId, int $quantity): array
    {
        $cart = $this->getGuestCart();
        
        if (!isset($cart[$productId])) {
            throw new \Exception('Item not found in cart');
        }

        $product = Product::findOrFail($productId);
        
        if ($quantity <= 0) {
            unset($cart[$productId]);
        } else {
            $this->validateStock($product, $quantity, true);
            $cart[$productId]['quantity'] = $quantity;
        }

        $this->saveGuestCart($cart);

        return [
            'success' => true,
            'product_id' => $productId
        ];
    }

    public function removeItem($cartItem)
    {
        if (is_numeric($cartItem)) {
            return $this->removeGuestCartItem($cartItem);
        }

        return $cartItem->delete();
    }

    protected function removeGuestCartItem(int $productId)
    {
        $cart = $this->getGuestCart();
        
        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            $this->saveGuestCart($cart);
            return true;
        }

        return false;
    }

    public function getCart()
    {
        return $this->getOrCreateCart();
    }

    public function getCartWithItems()
    {
        if (Auth::check()) {
            $cart = $this->getOrCreateCart();
            if (!$cart) {
                return null;
            }
            return $cart->load(['items.product.vendor']);
        }

        return $this->getGuestCartWithProducts();
    }

    protected function getGuestCartWithProducts(): ?array
    {
        $cart = $this->getGuestCart();
        
        if (empty($cart)) {
            return null;
        }

        $productIds = array_keys($cart);
        $products = Product::with('vendor')->find($productIds)->keyBy('id');

        $items = [];
        foreach ($cart as $productId => $item) {
            if ($products->has($productId)) {
                $product = $products[$productId];
                $items[] = (object) [
                    'id' => $productId,
                    'product_id' => $productId,
                    'quantity' => $item['quantity'],
                    'product' => $product,
                ];
            }
        }

        return [
            'items' => collect($items),
        ];
    }

    public function getCartGroupedByVendor()
    {
        $cartData = $this->getCartWithItems();
        
        if (!$cartData) {
            return collect();
        }
        
        $items = is_array($cartData) ? $cartData['items'] : $cartData['items'];
        
        if ($items->isEmpty()) {
            return collect();
        }

        return $items->groupBy(function ($item) {
            return $item->product->vendor->id;
        })->map(function ($items, $vendorId) {
            $vendor = $items->first()->product->vendor;
            $total = $items->sum(function ($item) {
                return $item->product->price * $item->quantity;
            });

            return [
                'vendor' => $vendor,
                'items' => $items,
                'total' => $total,
                'item_count' => $items->sum('quantity'),
            ];
        });
    }

    public function getCartTotal()
    {
        if (Auth::check()) {
            $cart = $this->getCartWithItems();
            if (!$cart) {
                return 0;
            }
            $items = is_array($cart) ? $cart['items'] : $cart->items;
            return $items->sum(function ($item) {
                return $item->product->price * $item->quantity;
            });
        }

        $cart = $this->getGuestCart();
        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        return $total;
    }

    public function getCartItemCount()
    {
        if (Auth::check()) {
            $cart = $this->getCartWithItems();
            if (!$cart) {
                return 0;
            }
            $items = is_array($cart) ? $cart['items'] : $cart->items;
            return $items->sum('quantity');
        }

        $cart = $this->getGuestCart();
        return array_sum(array_column($cart, 'quantity'));
    }

    public function clearCart(): void
    {
        if (Auth::check()) {
            $cart = $this->getOrCreateCart();
            if ($cart) {
                $cart->items()->delete();
            }
        } else {
            Session::forget(self::GUEST_CART_KEY);
        }
    }

    public function validateStock(Product $product, int $quantity, bool $isUpdate = false): void
    {
        $currentQuantity = $isUpdate ? 0 : $product->stock;
        $requestedQuantity = $quantity;
        
        if ($requestedQuantity > $product->stock) {
            throw new \Exception("Requested quantity ({$requestedQuantity}) exceeds available stock ({$product->stock}) for product: {$product->name}");
        }
    }

    public function mergeGuestCartToUserCart(int $userId): void
    {
        $guestCart = $this->getGuestCart();
        
        if (empty($guestCart)) {
            return;
        }

        $cart = Cart::firstOrCreate(['user_id' => $userId]);

        foreach ($guestCart as $productId => $item) {
            $product = Product::find($productId);
            
            if (!$product) {
                continue;
            }

            $this->validateStock($product, $item['quantity']);

            $cartItem = CartItem::where('cart_id', $cart->id)
                ->where('product_id', $productId)
                ->first();

            if ($cartItem) {
                $newQuantity = $cartItem->quantity + $item['quantity'];
                $this->validateStock($product, $newQuantity, true);
                $cartItem->update(['quantity' => $newQuantity]);
            } else {
                CartItem::create([
                    'cart_id' => $cart->id,
                    'product_id' => $productId,
                    'quantity' => $item['quantity'],
                ]);
            }
        }

        Session::forget(self::GUEST_CART_KEY);
    }
}
