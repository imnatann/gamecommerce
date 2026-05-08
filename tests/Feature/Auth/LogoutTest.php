<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_post_logout_redirects_to_login(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('logout'));

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_get_logout_is_not_allowed(): void
    {
        $user = User::factory()->create();
        // GET /auth/logout tidak ada, harus 404 atau 405
        $response = $this->actingAs($user)->get('/auth/logout');
        $this->assertContains($response->status(), [404, 405]);
    }
}
