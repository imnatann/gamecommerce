<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AuthRoutesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Semua Fortify routes yang wajib ada (prefix auth/).
     */
    public function test_fortify_named_routes_are_registered(): void
    {
        $requiredRoutes = [
            'login',
            'logout',
            'register',
            'password.request',
            'password.email',
            'password.reset',
            'password.update',
            'verification.notice',
            'verification.verify',
            'verification.send',
            'two-factor.login',
            'two-factor.enable',
            'two-factor.disable',
            'two-factor.confirm',
            'password.confirm',
        ];

        foreach ($requiredRoutes as $routeName) {
            $this->assertTrue(
                Route::has($routeName),
                "Route [{$routeName}] tidak terdaftar. Cek FortifyServiceProvider."
            );
        }
    }

    public function test_fortify_login_route_resolves_to_auth_prefix(): void
    {
        $loginUrl = route('login');
        $this->assertStringContainsString('/auth/login', $loginUrl);
    }

    public function test_fortify_logout_route_is_post_method(): void
    {
        // Verifikasi logout adalah POST, bukan GET (bug klasik)
        $routes = Route::getRoutes();
        $logoutRoute = $routes->getByName('logout');
        $this->assertNotNull($logoutRoute, 'Route logout tidak ditemukan');
        $this->assertContains('POST', $logoutRoute->methods());
    }

    public function test_seller_kyc_verify_route_is_registered(): void
    {
        // KRITIS: EnsureKycVerified middleware bergantung pada route ini
        $this->assertTrue(
            Route::has('seller.kyc.verify'),
            "Route [seller.kyc.verify] tidak terdaftar. EnsureKycVerified akan redirect loop."
        );
    }

    public function test_seller_kyc_store_route_is_registered(): void
    {
        $this->assertTrue(
            Route::has('seller.kyc.store'),
            "Route [seller.kyc.store] tidak terdaftar. KYC form submission tidak bisa diproses."
        );
    }

    public function test_locale_switch_route_is_registered(): void
    {
        $this->assertTrue(
            Route::has('locale.switch'),
            "Route [locale.switch] tidak terdaftar."
        );
    }

    public function test_login_page_returns_200(): void
    {
        $response = $this->get(route('login'));
        $response->assertStatus(200);
    }

    public function test_register_page_returns_200(): void
    {
        $response = $this->get(route('register'));
        $response->assertStatus(200);
    }

    public function test_two_factor_challenge_page_resolves(): void
    {
        // Route resolves — Fortify may redirect to login if no 2FA session active (302 is valid)
        $response = $this->get(route('two-factor.login'));
        $this->assertContains($response->status(), [200, 302]);
    }

    public function test_logout_get_request_is_not_found_or_not_allowed(): void
    {
        // GET /auth/logout tidak boleh ada (security: CSRF bypass via link click)
        $response = $this->get('/auth/logout');
        $this->assertContains($response->status(), [404, 405]);
    }
}
