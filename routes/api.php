<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BrandsController;
use App\Http\Controllers\Api\CategoriesController;
use App\Http\Controllers\Api\ProductsController;
use App\Http\Controllers\Api\ProductVariantsController;
use App\Http\Controllers\Api\CartController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::name('api.')->group(function () {
    Route::apiResource('brands', BrandsController::class);
    Route::apiResource('categories', CategoriesController::class);
    Route::apiResource('products', ProductsController::class);
    Route::apiResource('product-variants', ProductVariantsController::class);
    Route::post('product-variants/bulk-update-quantity', [ProductVariantsController::class, 'bulkUpdateQuantity'])->name('product-variants.bulk-update-quantity');
    Route::get('product-variants/statistics', [ProductVariantsController::class, 'statistics'])->name('product-variants.statistics');
    
    // Cart routes moved to web.php under 'auth' middleware to use session-based auth
});