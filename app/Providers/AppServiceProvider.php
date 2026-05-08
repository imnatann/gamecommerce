<?php

namespace App\Providers;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Policies\OrderPolicy;
use App\Policies\ProductPolicy;
use App\Policies\SellerPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Daftarkan policy untuk model
        Gate::policy(Product::class, ProductPolicy::class);
        Gate::policy(Order::class, OrderPolicy::class);

        // Non-model abilities untuk seller panel
        Gate::define('manage-seller-panel', [SellerPolicy::class, 'manage']);
        Gate::define('access-seller-panel', [SellerPolicy::class, 'accessPanel']);
        Gate::define('submit-kyc', [SellerPolicy::class, 'submitKyc']);

        // Super admin bypass — dipanggil sebelum semua policy check
        // Return null (bukan false) untuk non-super-admin agar policy normal tetap dievaluasi
        Gate::before(function (User $user, string $ability): ?bool {
            if ($user->hasPlatformRole('super_admin')) {
                return true;
            }

            return null;
        });
    }
}
