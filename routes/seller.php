<?php

use App\Http\Controllers\Seller\DashboardController;
use App\Http\Controllers\Seller\EarningsController;
use App\Http\Controllers\Seller\KycController;
use App\Http\Controllers\Seller\OrderController;
use App\Http\Controllers\Seller\ProductController;
use App\Http\Controllers\Seller\SettingsController;
use Illuminate\Support\Facades\Route;

// KYC verification — HARUS di luar middleware 'kyc' (accessible oleh unverified seller)
Route::get('/kyc', [KycController::class, 'index'])->name('kyc.verify');
Route::post('/kyc', [KycController::class, 'store'])->name('kyc.store');

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
Route::post('/products', [ProductController::class, 'store'])->name('products.store');
Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
Route::post('/products/{product}/toggle', [ProductController::class, 'toggleActive'])->name('products.toggle');

Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
Route::post('/orders/{orderItem}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');

Route::get('/earnings', [EarningsController::class, 'index'])->name('earnings');
Route::post('/earnings/withdraw', [EarningsController::class, 'requestWithdrawal'])->name('earnings.withdraw');

Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
Route::put('/settings/profile', [SettingsController::class, 'updateProfile'])->name('settings.profile');
Route::put('/settings/notifications', [SettingsController::class, 'updateNotifications'])->name('settings.notifications');