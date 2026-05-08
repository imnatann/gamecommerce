<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TwoFactorTest extends TestCase
{
    use RefreshDatabase;

    public function test_two_factor_challenge_page_renders_for_guest(): void
    {
        $response = $this->get(route('two-factor.login'));
        $response->assertStatus(200);
    }

    public function test_two_factor_enable_requires_authentication(): void
    {
        $response = $this->post(route('two-factor.enable'));
        $response->assertRedirect(route('login'));
    }

    public function test_two_factor_disable_requires_authentication(): void
    {
        $response = $this->delete(route('two-factor.disable'));
        $response->assertRedirect(route('login'));
    }
}
