<?php

use App\Http\Controllers\Web\CheckoutController;
use App\Http\Controllers\Web\GamePageController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\LocaleController;
use App\Http\Controllers\Web\ProductPageController;
use App\Http\Controllers\Web\ProfileController;
use App\Http\Controllers\Web\SearchController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/search', [SearchController::class, 'index'])->name('search');

Route::get('/g/{slug}', [GamePageController::class, 'show'])->name('game.show');

Route::get('/d/{slug}/{id}', [ProductPageController::class, 'show'])->name('product.show');

Route::prefix('auth')->name('auth.')->group(function () {
    Route::get('/google', function () {
        return redirect()->route('login')
            ->with('status', 'Login Google belum dikonfigurasi.');
    })->name('google');

    Route::get('/2fa', fn () => redirect()->route('two-factor.login'))->name('2fa');
});

Route::middleware('auth')->group(function () {
    Route::get('/cart', [CheckoutController::class, 'cart'])->name('cart');
    Route::post('/cart/add', [CheckoutController::class, 'addToCart'])->name('cart.add');
    Route::delete('/cart/{itemId}', [CheckoutController::class, 'removeFromCart'])->name('cart.remove');
    Route::get('/checkout', [CheckoutController::class, 'checkout'])->name('checkout');
    Route::post('/checkout', [CheckoutController::class, 'buyNow'])->name('checkout.index');
    Route::post('/checkout/process', [CheckoutController::class, 'processCheckout'])->name('checkout.process');
    Route::get('/order/{orderId}', [CheckoutController::class, 'orderStatus'])->name('order.status');
    Route::get('/orders', [ProfileController::class, 'orders'])->name('orders.index');

    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('profile.index');
        Route::put('/update', [ProfileController::class, 'updateProfile'])->name('profile.update');
        Route::get('/orders', [ProfileController::class, 'orders'])->name('profile.orders');
        Route::get('/orders/{orderId}', [ProfileController::class, 'orderDetail'])->name('profile.order-detail');
        Route::get('/wallet', [ProfileController::class, 'wallet'])->name('profile.wallet');
        Route::get('/favorites', [ProfileController::class, 'favorites'])->name('profile.favorites');
        Route::post('/favorites/toggle', [ProfileController::class, 'toggleFavorite'])->name('profile.favorites.toggle');
    });
});

// Locale switcher — tidak memerlukan auth, session-only
Route::post('/locale/{locale}', [LocaleController::class, 'switch'])->name('locale.switch');

Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});
