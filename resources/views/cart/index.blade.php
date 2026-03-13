@extends('layouts.app')

@section('title', 'Shopping Cart')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/cart.css') }}">
@endpush

@section('content')
<div class="container py-4">
    <div class="cart-container">
        <div class="cart-header">
            <h2>
                <i class="fas fa-shopping-cart me-2"></i>Shopping Cart
                @guest
                <span class="guest-badge">Guest</span>
                @endguest
            </h2>
            <span class="text-muted">{{ $cartCount }} item(s)</span>
        </div>

        @if($cartGrouped->isEmpty())
            <div class="empty-cart">
                <i class="fas fa-cart-arrow-down"></i>
                <h3>Your cart is empty</h3>
                <a href="{{ route('products.index') }}" class="btn btn-primary">
                    <i class="fas fa-arrow-left me-2"></i>Continue Shopping
                </a>
            </div>
        @else
            @foreach($cartGrouped as $vendorGroup)
            <div class="vendor-card">
                <div class="card-header">
                    <h5><i class="fas fa-store me-2"></i>{{ $vendorGroup['vendor']->name }}</h5>
                </div>
                <div class="card-body p-0">
                    @foreach($vendorGroup['items'] as $item)
                    <div class="cart-item">
                        <div class="item-image">
                            @if($item->product->image)
                                <img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product->name }}" onerror="this.src='https://via.placeholder.com/80x80?text=Product'">
                            @else
                                <img src="https://via.placeholder.com/80x80?text={{ urlencode($item->product->name) }}" alt="{{ $item->product->name }}">
                            @endif
                        </div>
                        <div class="item-details">
                            <div class="item-name">{{ $item->product->name }}</div>
                            <div class="item-vendor">{{ $item->product->vendor->name }}</div>
                        </div>
                        <div class="item-price">₹{{ number_format($item->product->price, 2) }}</div>
                        @auth
                        <div class="quantity-controls">
                            <button class="quantity-btn" onclick="updateQuantity({{ $item->id }}, -1)">
                                <i class="fas fa-minus"></i>
                            </button>
                            <input type="number" class="quantity-input" 
                                value="{{ $item->quantity }}" min="1" max="{{ $item->product->stock }}"
                                data-item-id="{{ $item->id }}"
                                onchange="updateQuantityFromInput(this)">
                            <button class="quantity-btn" onclick="updateQuantity({{ $item->id }}, 1)">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                        @else
                        <div class="quantity-controls">
                            <span class="badge bg-secondary">{{ $item->quantity }}</span>
                        </div>
                        @endauth
                        <div class="item-subtotal">₹{{ number_format($item->product->price * $item->quantity, 2) }}</div>
                        @auth
                        <button class="remove-btn" onclick="removeItem({{ $item->id }})">
                            <i class="fas fa-trash"></i>
                        </button>
                        @else
                        <button class="remove-btn" onclick="removeGuestItem({{ $item->product_id }})">
                            <i class="fas fa-trash"></i>
                        </button>
                        @endauth
                    </div>
                    @endforeach
                </div>
                <div class="card-body bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">{{ $vendorGroup['item_count'] }} item(s)</span>
                        <h5 class="mb-0">Vendor Total: <span class="text-success">₹{{ number_format($vendorGroup['total'], 2) }}</span></h5>
                    </div>
                </div>
            </div>
            @endforeach

            <div class="cart-summary">
                <div class="summary-row">
                    <span>Total Items</span>
                    <span>{{ $cartCount }}</span>
                </div>
                <div class="summary-row total">
                    <span>Cart Total</span>
                    <span class="text-success">₹{{ number_format($cartTotal, 2) }}</span>
                </div>
            </div>

            <div class="cart-actions">
                <a href="{{ route('products.index') }}" class="btn btn-continue">
                    <i class="fas fa-arrow-left me-2"></i>Continue Shopping
                </a>
                @auth
                <a href="{{ route('checkout.index') }}" class="btn btn-checkout">
                    <i class="fas fa-lock me-2"></i>Proceed to Checkout
                </a>
                @else
                <button class="btn btn-checkout" onclick="openLoginModal('checkout')">
                    <i class="fas fa-sign-in-alt me-2"></i>Login to Checkout
                </button>
                @endauth
            </div>

            @guest
            <div class="login-prompt">
                <h4><i class="fas fa-user-circle me-2"></i>Already have an account?</h4>
                <p class="mb-0">Login to checkout faster and track your orders</p>
                <button class="btn" onclick="openLoginModal('checkout')">
                    <i class="fas fa-sign-in-alt me-2"></i>Login Now
                </button>
            </div>
            @endguest
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/cart.js') }}"></script>
@endsection
