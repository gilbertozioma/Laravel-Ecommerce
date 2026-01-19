<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Artisan;
use App\Mail\DailySalesReportMail;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;

class DailySalesReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_daily_sales_report_sends_mail()
    {
        Mail::fake();

        $user = \App\Models\User::factory()->create();
        $product = Product::factory()->create(['price' => 10]);

        $order = Order::create(['user_id' => $user->id, 'total' => 20, 'status' => 'completed', 'placed_at' => now()]);
        OrderItem::create(['order_id' => $order->id, 'product_id' => $product->id, 'quantity' => 2, 'price' => 10]);

        Artisan::call('app:send-daily-sales-report');

        Mail::assertSent(DailySalesReportMail::class);
    }
}
