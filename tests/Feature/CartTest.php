<?php

namespace Tests\Feature;

use App\Jobs\NotifyLowStock;
use App\Models\Product;
use App\Models\CartItem;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_add_to_cart()
    {
        Bus::fake();

        $user = \App\Models\User::factory()->create();
        $product = Product::factory()->create(['stock_quantity' => 3, 'low_stock_threshold' => 2]);

        $this->actingAs($user)
            ->postJson('/cart/add', ['product_id' => $product->id])
            ->assertStatus(200)
            ->assertJson(['status' => 'ok']);

        $this->assertDatabaseHas('cart_items', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        // stock should be decremented when adding to cart
        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock_quantity' => 2]);

        // low stock should be dispatched when remaining <= threshold
        Bus::assertDispatched(NotifyLowStock::class);
    }

    public function test_place_order_clears_cart_and_uses_reserved_stock()
    {
        $user = \App\Models\User::factory()->create();
        $product = Product::factory()->create(['stock_quantity' => 5, 'price' => 10]);

        // add twice
        $this->actingAs($user)->postJson('/cart/add', ['product_id' => $product->id]);
        $this->actingAs($user)->postJson('/cart/add', ['product_id' => $product->id]);

        // stock should be reduced by 2 now
        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock_quantity' => 3]);

        // place the order
        $this->actingAs($user)->postJson('/order/place')->assertStatus(200)->assertJson(['status' => 'ok']);

        $this->assertDatabaseHas('orders', ['user_id' => $user->id, 'total' => 20]);
        $this->assertDatabaseMissing('cart_items', ['user_id' => $user->id]);

        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock_quantity' => 3]);
    }
}
