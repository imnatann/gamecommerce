<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BanCheckTest extends TestCase
{
    use RefreshDatabase;

    public function test_banned_user_is_logged_out_and_redirected(): void
    {
        $user = User::factory()->create(['is_banned' => true]);

        $response = $this->actingAs($user)->get('/');

        // Setelah EnsureNotBanned middleware: harus di-logout dan redirect ke login
        $this->assertGuest();
        $response->assertRedirect(route('login'));
    }

    public function test_non_banned_user_can_access_home(): void
    {
        $user = User::factory()->create(['is_banned' => false]);
        $response = $this->actingAs($user)->get('/');
        $response->assertStatus(200);
    }

    public function test_banned_user_api_request_returns_403(): void
    {
        $user = User::factory()->create(['is_banned' => true]);

        $response = $this->actingAs($user)
            ->withHeaders(['Accept' => 'application/json'])
            ->get('/api/user');

        $response->assertStatus(403);
    }
}
