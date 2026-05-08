<?php

use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DisputeController;
use App\Http\Controllers\Admin\GameController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VoucherController;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::get('/games', [GameController::class, 'index'])->name('games.index');
Route::post('/games', [GameController::class, 'store'])->name('games.store');
Route::put('/games/{game}', [GameController::class, 'update'])->name('games.update');
Route::delete('/games/{game}', [GameController::class, 'destroy'])->name('games.destroy');
Route::post('/games/{game}/toggle', [GameController::class, 'toggleActive'])->name('games.toggle');

Route::get('/users', [UserController::class, 'index'])->name('users.index');
Route::put('/users/{user}/role', [UserController::class, 'updateRole'])->name('users.update-role');
Route::post('/users/{user}/verify-kyc', [UserController::class, 'verifyKYC'])->name('users.verify-kyc');
Route::post('/users/{user}/ban', [UserController::class, 'ban'])->name('users.ban');
Route::post('/users/{user}/unban', [UserController::class, 'unban'])->name('users.unban');

Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
Route::post('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');

Route::get('/disputes', [DisputeController::class, 'index'])->name('disputes.index');
Route::get('/disputes/{dispute}', [DisputeController::class, 'show'])->name('disputes.show');
Route::post('/disputes/{dispute}/resolve', [DisputeController::class, 'resolve'])->name('disputes.resolve');

Route::get('/banners', [BannerController::class, 'index'])->name('banners.index');
Route::post('/banners', [BannerController::class, 'store'])->name('banners.store');
Route::put('/banners/{banner}', [BannerController::class, 'update'])->name('banners.update');
Route::delete('/banners/{banner}', [BannerController::class, 'destroy'])->name('banners.destroy');
Route::post('/banners/{banner}/toggle', [BannerController::class, 'toggleActive'])->name('banners.toggle');
Route::post('/banners/reorder', [BannerController::class, 'updateSortOrder'])->name('banners.reorder');

Route::get('/vouchers', [VoucherController::class, 'index'])->name('vouchers.index');
Route::post('/vouchers', [VoucherController::class, 'store'])->name('vouchers.store');
Route::put('/vouchers/{voucher}', [VoucherController::class, 'update'])->name('vouchers.update');
Route::delete('/vouchers/{voucher}', [VoucherController::class, 'destroy'])->name('vouchers.destroy');
Route::post('/vouchers/{voucher}/toggle', [VoucherController::class, 'toggleActive'])->name('vouchers.toggle');