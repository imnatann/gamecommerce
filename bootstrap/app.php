<?php

use App\Http\Middleware\EnsureAdmin;
use App\Http\Middleware\EnsureBuyer;
use App\Http\Middleware\EnsureKycVerified;
use App\Http\Middleware\EnsureNotBanned;
use App\Http\Middleware\EnsureSeller;
use App\Http\Middleware\SetLocale;
use App\Http\Middleware\TrackLastActivity;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware(['auth', 'seller'])
                ->prefix('seller')
                ->name('seller.')
                ->group(base_path('routes/seller.php'));

            Route::middleware(['auth', 'admin'])
                ->prefix('admin')
                ->name('admin.')
                ->group(base_path('routes/admin.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'buyer'          => EnsureBuyer::class,
            'seller'         => EnsureSeller::class,
            'admin'          => EnsureAdmin::class,
            'kyc'            => EnsureKycVerified::class,
            'locale'         => SetLocale::class,
            'track.activity' => TrackLastActivity::class,
            'ban.check'      => EnsureNotBanned::class,
        ]);

        $middleware->web(append: [
            SetLocale::class,
            TrackLastActivity::class,
            EnsureNotBanned::class,
        ]);

        $middleware->api(append: [
            EnsureFrontendRequestsAreStateful::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
