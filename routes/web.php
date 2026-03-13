<?php

use App\Http\Controllers\AdminOrderController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\VendorAuthController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\ShareCartCount;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::middleware([ShareCartCount::class])->group(function () {
    Route::get('/', function () {
        return redirect()->route('products.index');
    });

    Route::get('/home', function () {
        return redirect()->route('products.index');
    });

    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::middleware('auth:vendor')->prefix('vendor')->name('vendor.')->group(function () {
        Route::get('/dashboard', [VendorAuthController::class, 'dashboard'])->name('dashboard');
        Route::put('/orders/{order}/status', [VendorAuthController::class, 'updateOrderStatus'])->name('orders.update-status');
    });

    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add')->middleware('web');
    Route::get('/cart/count', [CartController::class, 'count'])->name('cart.count');
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index')->middleware('web');
    Route::delete('/cart/product/{productId}', [CartController::class, 'removeGuestItem'])->name('cart.guest.remove')->middleware('web');

    Route::middleware('auth')->group(function () {
        Route::put('/cart/{cartItem}', [CartController::class, 'update'])->name('cart.update');
        Route::delete('/cart/{cartItem}', [CartController::class, 'remove'])->name('cart.remove');
        Route::delete('/cart', [CartController::class, 'clear'])->name('cart.clear');
    });

    Route::middleware('auth')->group(function () {
        Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
        Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');
        Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');
    });

    Route::middleware(['auth', AdminMiddleware::class])->prefix('admin')->group(function () {
        Route::get('/orders', [AdminOrderController::class, 'index'])->name('admin.orders.index');
        Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('admin.orders.show');
        Route::put('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('admin.orders.update-status');
    });
});
