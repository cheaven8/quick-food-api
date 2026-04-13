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