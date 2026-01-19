<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\CartItem;
use Illuminate\Support\Facades\DB;

class OrderService
{
    /**
     * Create an order for the given user based on their cart items.
     * Assumes stock was already reserved when items were added to the cart.
     */
    public static function placeOrderForUser($user)
    {
        return DB::transaction(function () use ($user) {
            $items = CartItem::with('product')->where('user_id', $user->id)->get();

            $total = 0;
            $order = Order::create([
                'user_id' => $user->id,
                'total' => 0,
                'status' => 'completed',
                'placed_at' => now(),
            ]);

            foreach ($items as $item) {
                $product = $item->product;
                $quantity = $item->quantity;
                $price = $product->price;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $price,
                ]);

                $total += $price * $quantity;
            }

            $order->total = $total;
            $order->save();

            // clear cart
            CartItem::where('user_id', $user->id)->delete();

            return $order;
        });
    }
}
