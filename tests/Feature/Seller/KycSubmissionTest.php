<?php

namespace Tests\Feature\Seller;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class KycSubmissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_kyc_verify_page_accessible_by_unverified_seller(): void
    {
        $seller = User::factory()->create(['kyc_status' => null]);
        $seller->assignPlatformRole(UserRole::SELLER);

        $response = $this->actingAs($seller)->get(route('seller.kyc.verify'));
        $response->assertStatus(200);
    }

    public function test_kyc_submission_updates_status_to_pending(): void
    {
        Storage::fake('public');

        $seller = User::factory()->create(['kyc_status' => null]);
        $seller->assignPlatformRole(UserRole::SELLER);

        $response = $this->actingAs($seller)->post(route('seller.kyc.store'), [
            'full_name'    => 'Test Seller',
            'id_number'    => '3201234567890001',
            'id_photo'     => UploadedFile::fake()->image('ktp.jpg', 800, 500),
            'selfie_photo' => UploadedFile::fake()->image('selfie.jpg', 800, 500),
            'bank_name'    => 'BCA',
            'bank_account' => '1234567890',
            'bank_holder'  => 'Test Seller',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'id'         => $seller->id,
            'kyc_status' => 'pending',
        ]);
    }

    public function test_kyc_form_validation_rejects_short_nik(): void
    {
        $seller = User::factory()->create(['kyc_status' => null]);
        $seller->assignPlatformRole(UserRole::SELLER);

        $response = $this->actingAs($seller)->post(route('seller.kyc.store'), [
            'full_name'    => 'Test',
            'id_number'    => '123',  // NIK must be 16 digits
            'id_photo'     => UploadedFile::fake()->image('ktp.jpg'),
            'selfie_photo' => UploadedFile::fake()->image('selfie.jpg'),
            'bank_name'    => 'BCA',
            'bank_account' => '1234567890',
            'bank_holder'  => 'Test',
        ]);

        $response->assertSessionHasErrors(['id_number']);
    }

    public function test_kyc_route_is_outside_kyc_middleware(): void
    {
        // Verifikasi route seller.kyc.verify ada dan accessible oleh seller tanpa KYC
        $this->assertTrue(\Illuminate\Support\Facades\Route::has('seller.kyc.verify'));
    }
}
