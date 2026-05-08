<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocaleSwitchTest extends TestCase
{
    use RefreshDatabase;

    public function test_post_locale_updates_session(): void
    {
        $response = $this->post(route('locale.switch', ['locale' => 'en']));

        $response->assertRedirect();
        $this->assertEquals('en', session('locale'));
    }

    public function test_post_locale_id_sets_indonesian(): void
    {
        $response = $this->post(route('locale.switch', ['locale' => 'id']));

        $response->assertRedirect();
        $this->assertEquals('id', session('locale'));
    }

    public function test_invalid_locale_returns_404(): void
    {
        $response = $this->post(route('locale.switch', ['locale' => 'fr']));
        $response->assertStatus(404);
    }

    public function test_locale_route_exists(): void
    {
        $this->assertTrue(\Illuminate\Support\Facades\Route::has('locale.switch'));
    }
}
