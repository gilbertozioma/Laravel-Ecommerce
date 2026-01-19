<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\OrderManagementController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;

Route::get('/', [ProductController::class, 'index'])->name('shop');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Pages

Route::get('/shop', [ProductController::class, 'index'])->name('shop');
Route::get('/shop/{product}', [ProductController::class, 'show'])->name('shop.show');

Route::get('/cart', [CartController::class, 'show'])->name('cart');
Route::get('/checkout', [CartController::class, 'checkout'])->name('checkout');

// Cart endpoints - accessible to guests
Route::get('/cart/count', [CartController::class, 'count']);
Route::get('/cart/items', [CartController::class, 'items']);
Route::post('/cart/add', [CartController::class, 'add']);
Route::post('/cart/{id}/quantity', [CartController::class, 'updateQuantity']);
Route::delete('/cart/{id}', [CartController::class, 'remove']);
Route::post('/cart/clear', [CartController::class, 'clear']);

// Place order - requires authentication
Route::post('/order/place', [CartController::class, 'placeOrder'])->middleware('auth');

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/orders', [OrderManagementController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderManagementController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/status', [OrderManagementController::class, 'updateStatus'])->name('orders.update-status');
    Route::get('/orders/export/csv', [OrderManagementController::class, 'export'])->name('orders.export');
});

require __DIR__ . '/auth.php';
