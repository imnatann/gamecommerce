<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_renders(): void
    {
        $response = $this->get(route('login'));
        $response->assertStatus(200);
    }

    public function test_valid_credentials_redirect_to_home(): void
    {
        $user = User::factory()->create([
            'email' => 'buyer@test.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post(route('login'), [
            'email' => 'buyer@test.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect();
        $this->assertAuthenticated();
    }

    public function test_invalid_credentials_return_validation_error(): void
    {
        User::factory()->create(['email' => 'buyer@test.com', 'password' => bcrypt('correct')]);

        $response = $this->post(route('login'), [
            'email' => 'buyer@test.com',
            'password' => 'wrong',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    public function test_authenticated_user_cannot_access_login(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('login'));
        $response->assertRedirect();
    }
}
