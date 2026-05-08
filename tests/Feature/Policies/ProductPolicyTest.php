<?php

namespace Tests\Feature\Policies;

use App\Enums\UserRole;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class ProductPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_seller_can_update_own_product(): void
    {
        $seller = User::factory()->create(['kyc_status' => 'verified']);
        $seller->assignPlatformRole(UserRole::SELLER);

        $product = Product::factory()->create(['seller_id' => $seller->id]);

        $this->actingAs($seller);
        $this->assertTrue(Gate::allows('update', $product));
    }

    public function test_seller_cannot_update_other_sellers_product(): void
    {
        $seller1 = User::factory()->create(['kyc_status' => 'verified']);
        $seller1->assignPlatformRole(UserRole::SELLER);
        $seller2 = User::factory()->create(['kyc_status' => 'verified']);
        $seller2->assignPlatformRole(UserRole::SELLER);

        $product = Product::factory()->create(['seller_id' => $seller2->id]);

        $this->actingAs($seller1);
        $this->assertFalse(Gate::allows('update', $product));
    }

    public function test_unverified_seller_cannot_create_product(): void
    {
        $seller = User::factory()->create(['kyc_status' => 'pending']);
        $seller->assignPlatformRole(UserRole::SELLER);

        $this->actingAs($seller);
        $this->assertFalse(Gate::allows('create', Product::class));
    }

    public function test_buyer_cannot_update_any_product(): void
    {
        $buyer = User::factory()->create();
        $seller = User::factory()->create(['kyc_status' => 'verified']);
        $seller->assignPlatformRole(UserRole::SELLER);
        $product = Product::factory()->create(['seller_id' => $seller->id]);

        $this->actingAs($buyer);
        $this->assertFalse(Gate::allows('update', $product));
    }
}
