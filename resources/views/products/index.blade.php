@extends('layouts.app')

@section('title', 'Products - MultiVendor Shop')

@section('content')
<div class="container py-4">
    <div class="search-container">
        <form action="{{ route('products.index') }}" method="GET" class="row g-3">
            <div class="col-md-10">
                <div class="search-wrapper">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" class="form-control search-input" placeholder="Search for products..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100 h-100">
                    <i class="fas fa-search me-2"></i>Search
                </button>
            </div>
        </form>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title mb-0">
            @if(request('search'))
                Search Results for "{{ request('search') }}"
            @else
                All Products
            @endif
        </h1>
        <span class="text-muted">{{ $products->total() }} products found</span>
    </div>

    @if($products->isEmpty())
        <div class="empty-state">
            <i class="fas fa-box-open"></i>
            <h3>No products found</h3>
            <p class="text-muted mb-0">Try adjusting your search or browse our collection</p>
            <a href="{{ route('products.index') }}" class="btn btn-primary mt-3">
                <i class="fas fa-arrow-left me-2"></i>View All Products
            </a>
        </div>
    @else
        <div class="row g-4">
            @foreach($products as $product)
            <div class="col-md-6 col-lg-4 col-xl-3">
                <div class="card product-card h-100 animate-card" style="opacity: 0;">
                    <div class="product-image-wrapper">
                        @if($product->image && file_exists(public_path('storage/' . $product->image)))
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" loading="lazy">
                        @elseif($product->image)
                            <img src="{{ $product->image }}" alt="{{ $product->name }}" loading="lazy" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="no-image" style="display:none;">
                                <i class="fas fa-image"></i>
                            </div>
                        @else
                            <div class="no-image">
                                <i class="fas fa-image"></i>
                            </div>
                        @endif
                        <div class="product-badges">
                            <span class="vendor-badge">{{ $product->vendor->name }}</span>
                            @if($product->stock > 10)
                                <span class="stock-badge in-stock"><i class="fas fa-check me-1"></i>In Stock</span>
                            @elseif($product->stock > 0)
                                <span class="stock-badge low-stock"><i class="fas fa-exclamation me-1"></i>Low Stock</span>
                            @else
                                <span class="stock-badge out-of-stock"><i class="fas fa-times me-1"></i>Out of Stock</span>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        <h5 class="product-title">{{ $product->name }}</h5>
                        <p class="product-description">{{ Str::limit($product->description, 100) }}</p>
                        
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="product-price">₹{{ number_format($product->price, 2) }}</span>
                            <span class="product-stock">
                                <i class="fas fa-box"></i> {{ $product->stock }} available
                            </span>
                        </div>
                        
                        <div class="product-actions">
                            <a href="{{ route('products.show', $product) }}" class="btn btn-view">
                                <i class="fas fa-eye me-1"></i>View
                            </a>
                            <button class="btn btn-add-cart add-to-cart-btn" data-product-id="{{ $product->id }}" {{ $product->stock <= 0 ? 'disabled' : '' }}>
                                <i class="fas fa-cart-plus me-1"></i>Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="pagination-wrapper">
            {{ $products->links() }}
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/products.js') }}"></script>
@endsection
