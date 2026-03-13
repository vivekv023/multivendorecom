@extends('layouts.app')

@section('title', 'Checkout')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/checkout.css') }}">
@endpush

@section('content')
<div class="container py-4">
    @if($vendors->isEmpty())
        <div class="empty-cart-alert">
            <i class="fas fa-shopping-cart"></i>
            <h4>Your cart is empty</h4>
            <p class="text-muted">Add some products to get started</p>
            <a href="{{ route('products.index') }}" class="btn btn-primary">
                <i class="fas fa-arrow-left me-2"></i>Continue Shopping
            </a>
        </div>
    @else
    <div class="checkout-container">
        <div class="checkout-header">
            <h4 class="mb-0"><i class="fas fa-credit-card me-2"></i>Secure Checkout</h4>
        </div>
        
        <form action="{{ route('checkout.process') }}" method="POST" id="checkoutForm">
            @csrf
            <div class="row p-4">
                <div class="col-lg-8">
                    @foreach($vendors as $vendorGroup)
                    <div class="vendor-section">
                        <div class="vendor-header">
                            <i class="fas fa-store"></i>
                            <h5>{{ $vendorGroup['vendor']->name }}</h5>
                        </div>
                        <div class="vendor-body">
                            @foreach($vendorGroup['items'] as $item)
                            <div class="checkout-item">
                                <div class="item-image">
                                    @if($item->product->image)
                                        <img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product->name }}">
                                    @else
                                        <img src="https://via.placeholder.com/60x60?text=Product" alt="{{ $item->product->name }}">
                                    @endif
                                </div>
                                <div class="item-details">
                                    <div class="item-name">{{ $item->product->name }}</div>
                                    <div class="item-meta">Qty: {{ $item->quantity }} × ₹{{ number_format($item->product->price, 2) }}</div>
                                </div>
                                <div class="item-price">₹{{ number_format($item->product->price * $item->quantity, 2) }}</div>
                            </div>
                            @endforeach
                            <div class="d-flex justify-content-between align-items-center pt-2">
                                <span class="text-muted small">{{ $vendorGroup['item_count'] }} item(s)</span>
                                <span class="text-success fw-bold">₹{{ number_format($vendorGroup['total'], 2) }}</span>
                            </div>
                        </div>
                    </div>
                    @endforeach

                    <div class="section-card">
                        <div class="card-header">
                            <h5><i class="fas fa-shipping-fast me-2"></i>Shipping Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Shipping Address <span class="text-danger">*</span></label>
                                <textarea name="shipping_address" class="form-control @error('shipping_address') is-invalid @enderror" 
                                    rows="3" required placeholder="Enter your full shipping address">{{ old('shipping_address') }}</textarea>
                                @error('shipping_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-0">
                                <label class="form-label">Order Notes (Optional)</label>
                                <textarea name="notes" class="form-control" rows="2" placeholder="Any special instructions?">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="section-card">
                        <div class="card-header">
                            <h5><i class="fas fa-money-bill-wave me-2"></i>Payment Method</h5>
                        </div>
                        <div class="card-body">
                            <label class="payment-option d-block" onclick="selectPayment(this)">
                                <input type="radio" name="payment_method" value="cash_on_delivery" checked>
                                <i class="fas fa-money-bill-alt payment-icon"></i>
                                <span class="fw-500">Cash on Delivery</span>
                            </label>
                            <label class="payment-option d-block" onclick="selectPayment(this)">
                                <input type="radio" name="payment_method" value="credit_card">
                                <i class="fas fa-credit-card payment-icon"></i>
                                <span class="fw-500">Credit Card</span>
                            </label>
                            <label class="payment-option d-block" onclick="selectPayment(this)">
                                <input type="radio" name="payment_method" value="debit_card">
                                <i class="fas fa-credit-card payment-icon"></i>
                                <span class="fw-500">Debit Card</span>
                            </label>
                            <label class="payment-option d-block" onclick="selectPayment(this)">
                                <input type="radio" name="payment_method" value="paypal">
                                <i class="fab fa-paypal payment-icon"></i>
                                <span class="fw-500">PayPal</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="summary-card">
                        <div class="summary-header">
                            <h5><i class="fas fa-receipt me-2"></i>Order Summary</h5>
                        </div>
                        <div class="summary-body">
                            <div class="summary-row">
                                <span><i class="fas fa-store me-2"></i>Vendors</span>
                                <span>{{ $total_orders }}</span>
                            </div>
                            <div class="summary-row">
                                <span><i class="fas fa-box me-2"></i>Total Items</span>
                                <span>{{ $total_items }}</span>
                            </div>
                            <div class="summary-row">
                                <span>Subtotal</span>
                                <span>₹{{ number_format($subtotal, 2) }}</span>
                            </div>
                            <div class="summary-row">
                                <span>Shipping</span>
                                <span class="text-success">Free</span>
                            </div>
                            <div class="summary-row">
                                <span>Tax</span>
                                <span>₹0.00</span>
                            </div>
                            <div class="summary-row total">
                                <span>Total</span>
                                <span class="text-success">₹{{ number_format($subtotal, 2) }}</span>
                            </div>
                            
                            <button type="submit" class="btn btn-place-order w-100">
                                <i class="fas fa-check-circle me-2"></i>Place Order
                            </button>
                            
                            <div class="security-badges">
                                <span><i class="fas fa-lock"></i> Secure</span>
                                <span><i class="fas fa-shield-alt"></i> Protected</span>
                            </div>
                            
                            <div class="protection-card">
                                <h6><i class="fas fa-shield-alt me-2"></i>Order Protection</h6>
                                <ul class="protection-list">
                                    <li>Secure payment processing</li>
                                    <li>Order tracking available</li>
                                    <li>Easy returns & refunds</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/checkout.js') }}"></script>
@endsection
