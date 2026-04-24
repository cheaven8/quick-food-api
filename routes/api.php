<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\MenuController;
use App\Http\Controllers\Api\HotelController;
use App\Http\Controllers\Api\AdminController;

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
Route::get('/products/featured', [ProductController::class, 'getFeatured']);
Route::get('/products/categories', [ProductController::class, 'getCategories']);
Route::get('/products/{id}', [ProductController::class, 'show']);


// Hotel product management routes (only hotel users)
Route::middleware(['auth:sanctum', 'role:hotel'])->prefix('hotel')->group(function () {
    Route::get('/products', [ProductController::class, 'myProducts']);  // View my products
    Route::post('/products', [ProductController::class, 'store']);      // Create product
    Route::put('/products/{id}', [ProductController::class, 'update']); // Update product
    Route::delete('/products/{id}', [ProductController::class, 'destroy']); // Delete product
    Route::patch('/products/{id}/toggle-availability', [ProductController::class, 'toggleAvailability']); // Toggle available
    Route::patch('/products/{id}/toggle-featured', [ProductController::class, 'toggleFeatured']); // Toggle featured
});


Route::middleware(['auth:sanctum', 'role:customer'])->group(function () {

    // Create order
    Route::post('/orders', [OrderController::class, 'store']);

    // My orders
    Route::get('/orders/my', [OrderController::class, 'myOrders']);

    // View single order
    Route::get('/orders/{id}', [OrderController::class, 'show']);

    // Cancel order
    Route::patch('/orders/{id}/cancel', [OrderController::class, 'cancelOrder']);
});

Route::middleware(['auth:sanctum', 'role:hotel'])->prefix('hotel')->group(function () {

    // Hotel sees all orders for their hotel
    Route::get('/orders', [OrderController::class, 'hotelOrders']);

    // Update order status
    Route::patch('/orders/{id}/status', [OrderController::class, 'updateStatus']);
});