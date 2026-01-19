<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShopTest extends TestCase
{
    use RefreshDatabase;

    public function test_shop_page_displays_products()
    {
        $product = Product::factory()->create(['name' => 'Test Product']);

        $response = $this->get('/shop');

        $response->assertStatus(200);
        $response->assertSee('Test Product');
        $response->assertSee('Add to cart');
    }
}
