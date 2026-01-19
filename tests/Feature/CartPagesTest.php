<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_cart_page_is_accessible()
    {
        $response = $this->get('/cart');
        $response->assertStatus(200);
        $response->assertSee('Your Cart');
    }

    public function test_checkout_requires_authentication()
    {
        $response = $this->get('/checkout');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_view_checkout()
    {
        $user = \App\Models\User::factory()->create();
        $response = $this->actingAs($user)->get('/checkout');
        $response->assertStatus(200);
        $response->assertSee('Checkout');
    }
}
