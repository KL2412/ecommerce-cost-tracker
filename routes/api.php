<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SaleController;
use Illuminate\Support\Facades\Route;

Route::get('/health', fn () => response()->json([
    'status' => 'ok',
]))->name('health');

Route::prefix('auth')->middleware('throttle:10,1')->group(function (): void {
    Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
    Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
});

Route::middleware('auth:api')->group(function (): void {
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::apiResource('purchases', PurchaseController::class)->only(['index', 'store']);
    Route::apiResource('sales', SaleController::class)->only(['index', 'store']);
});
