@extends('layouts.app')

@section('title', $product->name . ' - MultiVendor Shop')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/product.css') }}">
@endpush

@section('content')
<div class="container py-4">
    <nav aria-label="breadcrumb" class="breadcrumb-nav">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Products</a></li>
            <li class="breadcrumb-item active">{{ $product->name }}</li>
        </ol>
    </nav>

    <div class="product-detail-wrapper">
        <div class="row">
            <div class="col-lg-6">
                <div class="product-gallery">
                    <div class="main-image-wrapper">
                        @if($product->image && file_exists(public_path('storage/' . $product->image)))
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" id="mainImage">
                        @elseif($product->image)
                            <img src="{{ $product->image }}" alt="{{ $product->name }}" id="mainImage">
                        @else
                            <div class="no-image-placeholder">
                                <i class="fas fa-image"></i>
                                <span>{{ $product->name }}</span>
                            </div>
                        @endif
                    </div>
                    @if($product->image)
                    <div class="thumbnail-grid">
                        <div class="thumbnail active">
                            @if(file_exists(public_path('storage/' . $product->image)))
                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                            @else
                                <img src="{{ $product->image }}" alt="{{ $product->name }}">
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="product-info">
                    <h1>{{ $product->name }}</h1>
                    
                    <div class="product-meta">
                        <span class="meta-item vendor">
                            <i class="fas fa-store"></i> {{ $product->vendor->name }}
                        </span>
                        @if($product->stock > 10)
                        <span class="meta-item stock in-stock">
                            <i class="fas fa-check-circle"></i> In Stock
                        </span>
                        @elseif($product->stock > 0)
                        <span class="meta-item stock low-stock">
                            <i class="fas fa-exclamation-circle"></i> Only {{ $product->stock }} left
                        </span>
                        @else
                        <span class="meta-item stock out-of-stock">
                            <i class="fas fa-times-circle"></i> Out of Stock
                        </span>
                        @endif
                    </div>
                    
                    <div class="price-section">
                        <span class="current-price">₹{{ number_format($product->price, 2) }}</span>
                    </div>
                    
                    <div class="product-description-box">
                        <h6>Description</h6>
                        <p>{{ $product->description }}</p>
                    </div>
                    
                    @if($product->stock > 0)
                    <div class="stock-info @if($product->stock > 10) in-stock @elseif($product->stock > 0) low-stock @else out-of-stock @endif">
                        <i class="fas @if($product->stock > 10) fa-check-circle @elseif($product->stock > 0) fa-exclamation-circle @else fa-times-circle @endif"></i>
                        <span>
                            @if($product->stock > 10)
                                {{ $product->stock }} items available in stock
                            @elseif($product->stock > 0)
                                Hurry! Only {{ $product->stock }} items left in stock
                            @else
                                This item is currently out of stock
                            @endif
                        </span>
                    </div>
                    
                    <div class="quantity-selector">
                        <span class="quantity-label">Quantity:</span>
                        <div class="quantity-controls">
                            <button class="quantity-btn" type="button" id="decreaseBtn">
                                <i class="fas fa-minus"></i>
                            </button>
                            <input type="number" id="quantity" class="quantity-input" value="1" min="1" max="{{ $product->stock }}" readonly>
                            <button class="quantity-btn" type="button" id="increaseBtn">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="action-buttons">
                        @auth
                        <button class="btn-add-to-cart" id="addToCartBtn">
                            <i class="fas fa-shopping-cart"></i> Add to Cart
                        </button>
                        <button class="btn-buy-now" id="buyNowBtn">
                            Buy Now
                        </button>
                        @else
                        <button class="btn-add-to-cart" onclick="openLoginModal('')">
                            <i class="fas fa-sign-in-alt"></i> Login to Purchase
                        </button>
                        @endauth
                    </div>
                    @else
                    <div class="alert alert-danger d-flex align-items-center" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <div>
                            This product is currently out of stock. Please check back later or browse similar products.
                        </div>
                    </div>
                    @endif
                    
                    <div class="product-features">
                        <div class="feature-item">
                            <i class="fas fa-truck"></i>
                            <span>Free Shipping</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-shield-alt"></i>
                            <span>Secure Payment</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-undo"></i>
                            <span>Easy Returns</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($relatedProducts->isNotEmpty())
    <div class="related-products">
        <h3 class="related-title">Related Products</h3>
        <div class="row g-4">
            @foreach($relatedProducts as $related)
            <div class="col-md-6 col-lg-3">
                <div class="card related-card h-100">
                    <div class="product-image-wrapper">
                        @if($related->image && file_exists(public_path('storage/' . $related->image)))
                            <img src="{{ asset('storage/' . $related->image) }}" alt="{{ $related->name }}">
                        @elseif($related->image)
                            <img src="{{ $related->image }}" alt="{{ $related->name }}">
                        @else
                            <div class="no-image">
                                <i class="fas fa-image"></i>
                            </div>
                        @endif
                    </div>
                    <div class="card-body">
                        <span class="vendor-badge mb-2 d-inline-block">{{ $related->vendor->name }}</span>
                        <h6 class="product-title">{{ $related->name }}</h6>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="product-price">₹{{ number_format($related->price, 2) }}</span>
                            <span class="product-stock">
                                <i class="fas fa-box"></i> {{ $related->stock }}
                            </span>
                        </div>
                        <a href="{{ route('products.show', $related) }}" class="btn btn-view w-100 mt-3">
                            <i class="fas fa-eye me-1"></i>View Details
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection

@php
    $maxStock = $product->stock;
    $productId = $product->id;
@endphp

@section('scripts')
<script src="{{ asset('js/product.js') }}"></script>
<script>
(function() {
    const productId = {{ $productId }};
    const addUrl = '{{ route("cart.add") }}';
    const csrfToken = '{{ csrf_token() }}';
    
    const addToCartBtn = document.getElementById('addToCartBtn');
    const buyNowBtn = document.getElementById('buyNowBtn');
    
    if (addToCartBtn) {
        addToCartBtn.addEventListener('click', function() {
            const quantity = document.getElementById('quantity').value;
            addToCart(productId, quantity, addUrl, csrfToken);
        });
    }
    
    if (buyNowBtn) {
        buyNowBtn.addEventListener('click', function() {
            const quantity = document.getElementById('quantity').value;
            handleBuyNow(productId, quantity, addUrl, csrfToken);
        });
    }
})();
</script>
@endsection
