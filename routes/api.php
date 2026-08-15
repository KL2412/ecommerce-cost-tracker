<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/health', fn () => response()->json([
    'status' => 'ok',
]))->name('health');

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
