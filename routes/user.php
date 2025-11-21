<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\HomeController;
use App\Http\Controllers\User\ProductController;
use App\Http\Controllers\User\CategoryController;
use App\Http\Controllers\User\BrandController;
use App\Http\Controllers\User\CartController;
use App\Http\Controllers\User\CheckoutController;
use App\Http\Controllers\User\OrderController as UserOrderController;
use App\Http\Controllers\User\AccountController;
use App\Http\Controllers\User\AboutController;
use App\Http\Controllers\User\ContactController;

// Public routes for users
Route::get('/', [HomeController::class, 'index'])->name('user.home');
Route::get('/about', [AboutController::class, 'index'])->name('user.about');
Route::get('/contact', [ContactController::class, 'index'])->name('user.contact');
Route::get('/products', [ProductController::class, 'index'])->name('user.products.index');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('user.products.show');
Route::get('/categories', [CategoryController::class, 'index'])->name('user.categories.index');
Route::get('/categories/{category}', [CategoryController::class, 'show'])->name('user.categories.show');
Route::get('/brands', [BrandController::class, 'index'])->name('user.brands.index');
Route::get('/brands/{brand}', [BrandController::class, 'show'])->name('user.brands.show');

// Authenticated routes for users
Route::middleware('auth')->group(function () {
    Route::get('/cart', [CartController::class, 'index'])->name('user.cart');
    
    // Checkout routes
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('user.checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('user.checkout.store');
    Route::get('/checkout/payment/{order}', [CheckoutController::class, 'payment'])->name('user.checkout.payment');
    Route::post('/checkout/payment/{order}', [CheckoutController::class, 'processPayment'])->name('user.checkout.processPayment');
    Route::get('/checkout/success/{order}', [CheckoutController::class, 'success'])->name('user.checkout.success');
    Route::get('/checkout/failed/{order}', [CheckoutController::class, 'failed'])->name('user.checkout.failed');

    // Orders
    Route::get('/orders', [UserOrderController::class, 'index'])->name('user.orders.index');
    Route::get('/orders/{order}', [UserOrderController::class, 'show'])->name('user.orders.show');
    Route::post('/orders/{order}/cancel', [UserOrderController::class, 'cancel'])->name('user.orders.cancel');
    
    // Account Management
    Route::get('/account', [AccountController::class, 'index'])->name('user.account.index');
});
