<?php

use App\Http\Controllers\Api\V1\Admin\AdminBannerController;
use App\Http\Controllers\Api\V1\Admin\AdminDashboardController;
use App\Http\Controllers\Api\V1\Admin\AdminDisputeController;
use App\Http\Controllers\Api\V1\Admin\AdminGameController;
use App\Http\Controllers\Api\V1\Admin\AdminUserController;
use App\Http\Controllers\Api\V1\Auth\ForgotPasswordController;
use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Controllers\Api\V1\Auth\RegisterController;
use App\Http\Controllers\Api\V1\Auth\TwoFactorController;
use App\Http\Controllers\Api\V1\Buyer\CartController;
use App\Http\Controllers\Api\V1\Buyer\OrderController;
use App\Http\Controllers\Api\V1\Buyer\ReviewController;
use App\Http\Controllers\Api\V1\Buyer\WishlistController;
use App\Http\Controllers\Api\V1\Catalog\CategoryController;
use App\Http\Controllers\Api\V1\Catalog\GameController;
use App\Http\Controllers\Api\V1\Catalog\ProductController;
use App\Http\Controllers\Api\V1\Catalog\SearchController;
use App\Http\Controllers\Api\V1\Seller\SellerEarningController;
use App\Http\Controllers\Api\V1\Seller\SellerOrderController;
use App\Http\Controllers\Api\V1\Seller\SellerProductController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    Route::prefix('auth')->group(function () {
        Route::post('/login', [LoginController::class, 'login']);
        Route::post('/register', [RegisterController::class, 'register']);
        Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink']);
        Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword']);
        Route::post('/verify-email', [RegisterController::class, 'verifyEmail']);
        Route::post('/2fa/verify', [TwoFactorController::class, 'verify']);

        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/logout', [LoginController::class, 'logout']);
            Route::post('/logout-all', [LoginController::class, 'logoutAll']);
            Route::post('/email/resend', [RegisterController::class, 'resendVerification']);
            Route::post('/2fa/enable', [TwoFactorController::class, 'enable']);
            Route::post('/2fa/disable', [TwoFactorController::class, 'disable']);
            Route::get('/2fa/recovery-codes', [TwoFactorController::class, 'recoveryCodes']);
            Route::post('/2fa/regenerate-recovery-codes', [TwoFactorController::class, 'regenerateRecoveryCodes']);
            Route::post('/change-password', [ForgotPasswordController::class, 'changePassword']);
        });
    });

    Route::prefix('catalog')->group(function () {
        Route::get('/games', [GameController::class, 'index']);
        Route::get('/games/popular', [GameController::class, 'popular']);
        Route::get('/games/{slug}', [GameController::class, 'show']);
        Route::get('/games/{slug}/products', [GameController::class, 'gameProducts']);
        Route::get('/products', [ProductController::class, 'index']);
        Route::get('/products/{id}', [ProductController::class, 'show']);
        Route::get('/products/{id}/reviews', [ProductController::class, 'reviews']);
        Route::get('/products/cheapest/{gameProductId}', [ProductController::class, 'cheapest']);
        Route::get('/categories', [CategoryController::class, 'index']);
        Route::get('/categories/{slug}', [CategoryController::class, 'show']);
        Route::get('/categories/{slug}/games', [CategoryController::class, 'games']);
        Route::get('/search', [SearchController::class, 'search']);
        Route::get('/search/suggestions', [SearchController::class, 'suggestions']);
        Route::get('/search/popular', [SearchController::class, 'popular']);
    });

    Route::prefix('banners')->group(function () {
        Route::get('/', [App\Http\Controllers\Api\V1\Catalog\GameController::class, 'popular']);
    });

    Route::middleware(['auth:sanctum', 'buyer'])->prefix('buyer')->group(function () {
        Route::prefix('cart')->group(function () {
            Route::get('/', [CartController::class, 'index']);
            Route::post('/add', [CartController::class, 'add']);
            Route::put('/{itemId}', [CartController::class, 'update']);
            Route::delete('/{itemId}', [CartController::class, 'remove']);
            Route::delete('/', [CartController::class, 'clear']);
        });

        Route::prefix('orders')->group(function () {
            Route::get('/', [OrderController::class, 'index']);
            Route::post('/', [OrderController::class, 'store']);
            Route::get('/{id}', [OrderController::class, 'show']);
            Route::post('/{id}/cancel', [OrderController::class, 'cancel']);
        });

        Route::prefix('reviews')->group(function () {
            Route::post('/', [ReviewController::class, 'store']);
            Route::put('/{id}', [ReviewController::class, 'update']);
            Route::delete('/{id}', [ReviewController::class, 'destroy']);
        });

        Route::prefix('wishlist')->group(function () {
            Route::get('/', [WishlistController::class, 'index']);
            Route::post('/toggle', [WishlistController::class, 'toggle']);
            Route::post('/check', [WishlistController::class, 'check']);
            Route::delete('/{id}', [WishlistController::class, 'destroy']);
        });
    });

    Route::middleware(['auth:sanctum', 'seller'])->prefix('seller')->group(function () {
        Route::prefix('products')->group(function () {
            Route::get('/', [SellerProductController::class, 'index']);
            Route::post('/', [SellerProductController::class, 'store']);
            Route::get('/{id}', [SellerProductController::class, 'show']);
            Route::put('/{id}', [SellerProductController::class, 'update']);
            Route::delete('/{id}', [SellerProductController::class, 'destroy']);
        });

        Route::prefix('orders')->group(function () {
            Route::get('/', [SellerOrderController::class, 'index']);
            Route::get('/{id}', [SellerOrderController::class, 'show']);
            Route::post('/{id}/deliver', [SellerOrderController::class, 'deliver']);
            Route::post('/{id}/process', [SellerOrderController::class, 'process']);
        });

        Route::prefix('earnings')->group(function () {
            Route::get('/overview', [SellerEarningController::class, 'overview']);
            Route::get('/history', [SellerEarningController::class, 'earningHistory']);
            Route::post('/withdraw', [SellerEarningController::class, 'requestWithdrawal']);
            Route::get('/chart', [SellerEarningController::class, 'earningsByPeriod']);
        });
    });

    Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index']);
        Route::get('/dashboard/revenue-chart', [AdminDashboardController::class, 'revenueChart']);

        Route::prefix('games')->group(function () {
            Route::get('/', [AdminGameController::class, 'index']);
            Route::post('/', [AdminGameController::class, 'store']);
            Route::get('/{id}', [AdminGameController::class, 'show']);
            Route::put('/{id}', [AdminGameController::class, 'update']);
            Route::delete('/{id}', [AdminGameController::class, 'destroy']);
        });

        Route::prefix('users')->group(function () {
            Route::get('/', [AdminUserController::class, 'index']);
            Route::get('/{id}', [AdminUserController::class, 'show']);
            Route::put('/{id}', [AdminUserController::class, 'update']);
            Route::post('/{id}/ban', [AdminUserController::class, 'ban']);
            Route::post('/{id}/unban', [AdminUserController::class, 'unban']);
            Route::post('/{id}/verify-kyc', [AdminUserController::class, 'verifyKyc']);
            Route::post('/{id}/reject-kyc', [AdminUserController::class, 'rejectKyc']);
            Route::post('/{id}/reset-password', [AdminUserController::class, 'resetPassword']);
        });

        Route::prefix('disputes')->group(function () {
            Route::get('/', [AdminDisputeController::class, 'index']);
            Route::get('/{id}', [AdminDisputeController::class, 'show']);
            Route::post('/{id}/resolve', [AdminDisputeController::class, 'resolve']);
            Route::post('/{id}/reply', [AdminDisputeController::class, 'reply']);
        });

        Route::prefix('banners')->group(function () {
            Route::get('/', [AdminBannerController::class, 'index']);
            Route::post('/', [AdminBannerController::class, 'store']);
            Route::get('/{id}', [AdminBannerController::class, 'show']);
            Route::put('/{id}', [AdminBannerController::class, 'update']);
            Route::delete('/{id}', [AdminBannerController::class, 'destroy']);
            Route::post('/{id}/toggle-active', [AdminBannerController::class, 'toggleActive']);
            Route::post('/reorder', [AdminBannerController::class, 'reorder']);
        });
    });
});