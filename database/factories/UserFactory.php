<?php

namespace Database\Factories;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => '62' . fake()->numerify('8##########'),
            'password' => bcrypt('password'),
            'avatar' => null,
            'email_verified_at' => now(),
            'kyc_status' => 'pending',
            'is_banned' => false,
        ];
    }

    public function buyer(): static
    {
        return $this->afterCreating(function ($user) {
            $user->assignPlatformRole(UserRole::BUYER);
        });
    }

    public function seller(): static
    {
        return $this->afterCreating(function ($user) {
            $user->assignPlatformRole(UserRole::SELLER);
        });
    }

    public function kycVerified(): static
    {
        return $this->state(['kyc_status' => 'verified', 'kyc_verified_at' => now()]);
    }

    public function banned(): static
    {
        return $this->state(['is_banned' => true, 'banned_at' => now(), 'ban_reason' => 'Test ban']);
    }

    public function admin(): static
    {
        return $this->afterCreating(function ($user) {
            $user->assignPlatformRole(UserRole::ADMIN);
        });
    }
}
