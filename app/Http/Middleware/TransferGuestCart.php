<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class TransferGuestCart
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // After successful login, transfer guest cart to authenticated user
        if (Auth::check() && session()->has('cart')) {
            $user = Auth::user();
            $sessionCart = session()->get('cart', []);

            DB::transaction(function () use ($user, $sessionCart) {
                foreach ($sessionCart as $productId => $cartData) {
                    $product = Product::find($productId);
                    if (!$product) continue;

                    $cartItem = CartItem::firstOrNew([
                        'user_id' => $user->id,
                        'product_id' => $productId,
                    ]);

                    $quantity = $cartData['quantity'];
                    $cartItem->quantity = ($cartItem->exists ? $cartItem->quantity : 0) + $quantity;
                    $cartItem->save();
                }
            });

            // Clear the session cart after transfer
            session()->forget('cart');
        }

        return $response;
    }
}
