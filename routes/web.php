<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\CategoryController;
use App\Http\Controllers\Backend\BrandController;
use App\Http\Controllers\Backend\ProductController;
use App\Http\Controllers\Backend\ProductVariantController;
use App\Http\Controllers\Backend\UserController;
use App\Http\Controllers\Backend\OrderController as AdminOrderController;
use App\Http\Controllers\Backend\DashboardController as AdminDashboardController;
use App\Http\Controllers\Backend\BannerController;
use App\Http\Controllers\Api\CartController as ApiCartController;
use Inertia\Inertia;

Route::get('/dashboard', [AdminDashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

    Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Admin routes - chỉ admin mới có thể truy cập
    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        Route::get('dashboard/export-pdf', [AdminDashboardController::class, 'exportPdf'])->name('dashboard.export-pdf');
        Route::resource('categories', CategoryController::class);
        Route::resource('brands', BrandController::class);
        Route::resource('products', ProductController::class);
        Route::resource('product-variants', ProductVariantController::class);
        Route::resource('banners', BannerController::class);
        Route::post('banners/import', [BannerController::class, 'import'])->name('banners.import');
        Route::resource('users', UserController::class);
        Route::patch('users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle-active');
        
        // Orders
        Route::get('orders', [AdminOrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
        Route::patch('orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.update-status');
        Route::patch('orders/{order}/payment-status', [AdminOrderController::class, 'updatePaymentStatus'])->name('orders.update-payment-status');
    });

    // Session-authenticated API-style cart endpoints
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('cart', [ApiCartController::class, 'index'])->name('cart.index');
        Route::post('cart', [ApiCartController::class, 'store'])->name('cart.store');
        Route::put('cart/{id}', [ApiCartController::class, 'update'])->name('cart.update');
        Route::delete('cart/{id}', [ApiCartController::class, 'destroy'])->name('cart.destroy');
        Route::delete('cart', [ApiCartController::class, 'clear'])->name('cart.clear');
    });
});

// Include user routes
require __DIR__.'/user.php';

require __DIR__.'/auth.php';
