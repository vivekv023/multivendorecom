@extends('layouts.app')

@section('title', 'Order Success')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/success.css') }}">
@endsection

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="success-container">
                <div class="success-icon">
                    <i class="fas fa-check"></i>
                </div>
                
                <h1 class="success-title">Thank You for Your Order!</h1>
                <p class="success-message">Your order has been placed successfully.</p>
                
                @if(session('success'))
                <div class="order-details">
                    <h5><i class="fas fa-receipt me-2"></i>Order Details</h5>
                    <p class="mb-0 text-success">{{ session('success') }}</p>
                </div>
                @endif
                
                <div class="success-actions">
                    <a href="{{ route('products.index') }}" class="btn btn-order">
                        <i class="fas fa-shopping-bag me-2"></i>Continue Shopping
                    </a>
                </div>
                
                <div class="features-row">
                    <div class="feature-item">
                        <i class="fas fa-truck d-block mb-2"></i>
                        <span>Free Shipping</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-shield-alt d-block mb-2"></i>
                        <span>Secure Payment</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-undo d-block mb-2"></i>
                        <span>Easy Returns</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-headset d-block mb-2"></i>
                        <span>24/7 Support</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
