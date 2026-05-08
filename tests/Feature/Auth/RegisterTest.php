<?php

namespace Tests\Feature\Auth;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_page_renders(): void
    {
        $response = $this->get(route('register'));
        $response->assertStatus(200);
    }

    public function test_valid_registration_creates_user_with_buyer_role(): void
    {
        $response = $this->post(route('register'), [
            'name'                  => 'Test Buyer',
            'email'                 => 'newbuyer@test.com',
            'password'              => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', ['email' => 'newbuyer@test.com']);

        $user = User::where('email', 'newbuyer@test.com')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->isBuyer());
    }

    public function test_registration_creates_wallet_for_new_user(): void
    {
        $this->post(route('register'), [
            'name'                  => 'Wallet Test',
            'email'                 => 'wallettest@test.com',
            'password'              => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $user = User::where('email', 'wallettest@test.com')->first();
        $this->assertNotNull($user?->wallet);
    }

    public function test_duplicate_email_fails_registration(): void
    {
        User::factory()->create(['email' => 'existing@test.com']);

        $response = $this->post(route('register'), [
            'name'                  => 'Dupe',
            'email'                 => 'existing@test.com',
            'password'              => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertSessionHasErrors(['email']);
    }
}
