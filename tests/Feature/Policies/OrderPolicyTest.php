<?php

namespace Tests\Feature\Policies;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class OrderPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_buyer_can_view_own_order(): void
    {
        $buyer = User::factory()->create();
        $order = Order::factory()->create(['buyer_id' => $buyer->id]);

        $this->actingAs($buyer);
        $this->assertTrue(Gate::allows('view', $order));
    }

    public function test_seller_can_view_order_containing_their_item(): void
    {
        $buyer  = User::factory()->create();
        $seller = User::factory()->create();
        $seller->assignPlatformRole(\App\Enums\UserRole::SELLER);

        $order = Order::factory()->create(['buyer_id' => $buyer->id]);
        OrderItem::factory()->create(['order_id' => $order->id, 'seller_id' => $seller->id]);

        $this->actingAs($seller);
        $this->assertTrue(Gate::allows('view', $order));
    }

    public function test_other_user_cannot_view_unrelated_order(): void
    {
        $buyer     = User::factory()->create();
        $stranger  = User::factory()->create();
        $order     = Order::factory()->create(['buyer_id' => $buyer->id]);

        $this->actingAs($stranger);
        $this->assertFalse(Gate::allows('view', $order));
    }
}
