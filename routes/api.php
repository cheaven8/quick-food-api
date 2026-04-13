<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    // Customer Only
    Route::middleware('role:customer')->group(function () {
        Route::get('/my-orders', [OrderController::class, 'index']);
    });

    // Hotel Only
    Route::middleware('role:hotel')->group(function () {
        Route::post('/menu/add', [MenuController::class, 'store']);
        Route::patch('/hotel/toggle-status', [HotelController::class, 'toggle']);
    });

    // Admin Only
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/stats', [AdminController::class, 'stats']);
    });
});


// Public product routes (anyone can view)
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);
Route::get('/products/featured', [ProductController::class, 'getFeatured']);
Route::get('/products/categories', [ProductController::class, 'getCategories']);

// Hotel product management routes (only hotel users)
Route::middleware(['auth:sanctum', 'user.type:hotel'])->prefix('hotel')->group(function () {
    Route::get('/products', [ProductController::class, 'myProducts']);  // View my products
    Route::post('/products', [ProductController::class, 'store']);      // Create product
    Route::put('/products/{id}', [ProductController::class, 'update']); // Update product
    Route::delete('/products/{id}', [ProductController::class, 'destroy']); // Delete product
    Route::patch('/products/{id}/toggle-availability', [ProductController::class, 'toggleAvailability']); // Toggle available
    Route::patch('/products/{id}/toggle-featured', [ProductController::class, 'toggleFeatured']); // Toggle featured
});