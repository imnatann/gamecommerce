<?php

namespace Tests\Feature\Policies;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class SellerPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_kyc_verified_seller_can_manage_panel(): void
    {
        $seller = User::factory()->create(['kyc_status' => 'verified']);
        $seller->assignPlatformRole(UserRole::SELLER);

        $this->actingAs($seller);
        $this->assertTrue(Gate::allows('manage-seller-panel'));
    }

    public function test_buyer_cannot_manage_seller_panel(): void
    {
        $buyer = User::factory()->create();
        $this->actingAs($buyer);
        $this->assertFalse(Gate::allows('manage-seller-panel'));
    }

    public function test_seller_with_pending_kyc_can_submit_kyc(): void
    {
        $seller = User::factory()->create(['kyc_status' => null]);
        $seller->assignPlatformRole(UserRole::SELLER);

        $this->actingAs($seller);
        $this->assertTrue(Gate::allows('submit-kyc'));
    }

    public function test_kyc_verified_seller_cannot_resubmit_kyc(): void
    {
        $seller = User::factory()->create(['kyc_status' => 'verified']);
        $seller->assignPlatformRole(UserRole::SELLER);

        $this->actingAs($seller);
        $this->assertFalse(Gate::allows('submit-kyc'));
    }
}
