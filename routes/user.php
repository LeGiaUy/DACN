<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\HomeController;
use App\Http\Controllers\User\ProductController;
use App\Http\Controllers\User\CategoryController;
use App\Http\Controllers\User\BrandController;

// Public routes for users
Route::get('/', [HomeController::class, 'index'])->name('user.home');
Route::get('/products', [ProductController::class, 'index'])->name('user.products.index');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('user.products.show');
Route::get('/categories', [CategoryController::class, 'index'])->name('user.categories.index');
Route::get('/categories/{category}', [CategoryController::class, 'show'])->name('user.categories.show');
Route::get('/brands', [BrandController::class, 'index'])->name('user.brands.index');
Route::get('/brands/{brand}', [BrandController::class, 'show'])->name('user.brands.show');
