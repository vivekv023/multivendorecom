<?php

namespace App\Providers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Vendor;
use App\Policies\AdminPolicy;
use App\Policies\CartItemPolicy;
use App\Policies\CartPolicy;
use App\Policies\VendorOrderPolicy;
use App\Policies\VendorProductPolicy;
use App\Repositories\Eloquent\OrderRepository;
use App\Repositories\Eloquent\ProductRepository;
use App\Repositories\Eloquent\VendorRepository;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use App\Repositories\Interfaces\VendorRepositoryInterface;
use App\Services\CartService;
use App\Services\CheckoutService;
use App\Services\OrderService;
use App\Services\ProductService;
use App\Services\VendorService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
        $this->app->bind(OrderRepositoryInterface::class, OrderRepository::class);
        $this->app->bind(VendorRepositoryInterface::class, VendorRepository::class);

        $this->app->singleton(CartService::class);
        
        $this->app->when(ProductService::class)
            ->needs('$productRepository')
            ->give(ProductRepositoryInterface::class);
            
        $this->app->when(ProductService::class)
            ->needs('$cartService')
            ->give(CartService::class);
            
        $this->app->when(OrderService::class)
            ->needs('$orderRepository')
            ->give(OrderRepositoryInterface::class);
            
        $this->app->when(VendorService::class)
            ->needs('$vendorRepository')
            ->give(VendorRepositoryInterface::class);
            
        $this->app->when(CheckoutService::class)
            ->needs('$cartService')
            ->give(CartService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // User Policies
        Gate::policy(Cart::class, CartPolicy::class);
        Gate::policy(CartItem::class, CartItemPolicy::class);
        Gate::policy(Order::class, \App\Policies\OrderPolicy::class);

        // Vendor Policies
        Gate::define('vendor.orders.view', [VendorOrderPolicy::class, 'view']);
        Gate::define('vendor.orders.update', [VendorOrderPolicy::class, 'update']);
        Gate::define('vendor.products.view', [VendorProductPolicy::class, 'view']);
        Gate::define('vendor.products.update', [VendorProductPolicy::class, 'update']);
        Gate::define('vendor.products.delete', [VendorProductPolicy::class, 'delete']);
        Gate::define('vendor.products.create', [VendorProductPolicy::class, 'create']);

        // Admin Policies
        Gate::define('admin.orders.viewAny', [AdminPolicy::class, 'viewAny']);
        Gate::define('admin.orders.view', [AdminPolicy::class, 'view']);
        Gate::define('admin.orders.update', [AdminPolicy::class, 'update']);
        Gate::define('admin.orders.delete', [AdminPolicy::class, 'delete']);
        Gate::define('admin.vendors.manage', [AdminPolicy::class, 'manageVendors']);
        Gate::define('admin.products.manage', [AdminPolicy::class, 'manageProducts']);
        Gate::define('admin.users.manage', [AdminPolicy::class, 'manageUsers']);
    }
}
