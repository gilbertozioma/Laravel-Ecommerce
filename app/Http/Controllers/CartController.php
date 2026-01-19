<?php

namespace App\Http\Controllers;

use App\Jobs\NotifyLowStock;
use App\Models\CartItem;
use App\Models\Product;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        if ($user) {
            $items = CartItem::with('product')->where('user_id', $user->id)->get();
        } else {
            // For guests, show empty cart (items will be loaded via AJAX)
            $items = collect();
        }
        return view('cart-page', ['items' => $items]);
    }

    public function checkout()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $items = CartItem::with('product')->where('user_id', $user->id)->get();
        return view('checkout-page', ['items' => $items]);
    }

    public function items()
    {
        $user = Auth::user();

        if (!$user) {
            // Return items from session for guests
            $sessionCart = session()->get('cart', []);
            $items = [];
            $subtotal = 0;

            foreach ($sessionCart as $productId => $cartData) {
                $product = Product::find($productId);
                if ($product) {
                    $itemTotal = $product->price * $cartData['quantity'];
                    $items[] = [
                        'id' => null,
                        'product_id' => $productId,
                        'product_name' => $product->name,
                        'product_price' => (float) $product->price,
                        'product_stock' => (int) $product->stock_quantity,
                        'quantity' => (int) $cartData['quantity'],
                        'total' => (float) $itemTotal,
                    ];
                    $subtotal += $itemTotal;
                }
            }

            return response()->json([
                'items' => $items,
                'subtotal' => (float) $subtotal,
            ]);
        }

        $items = CartItem::with('product')->where('user_id', $user->id)->get();
        return response()->json([
            'items' => $items->map(fn($item) => [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'product_name' => $item->product->name,
                'product_price' => (float) $item->product->price,
                'product_stock' => (int) $item->product->stock_quantity,
                'quantity' => (int) $item->quantity,
                'total' => (float) ($item->product->price * $item->quantity),
            ])->toArray(),
            'subtotal' => (float) $items->sum(fn($i) => $i->quantity * $i->product->price),
        ]);
    }

    public function count()
    {
        $user = Auth::user();
        if ($user) {
            $count = (int) CartItem::where('user_id', $user->id)->sum('quantity');
        } else {
            // Count items from session for guests
            $sessionCart = session()->get('cart', []);
            $count = 0;
            foreach ($sessionCart as $item) {
                if (isset($item['quantity'])) {
                    $count += (int) $item['quantity'];
                }
            }
        }
        return response()->json(['count' => (int) $count]);
    }

    public function add(Request $request)
    {
        $request->validate(['product_id' => 'required|integer|exists:products,id']);

        $user = Auth::user();
        if (! $user) {
            // For guests, store in session
            $sessionCart = session()->get('cart', []);
            $productId = $request->product_id;
            $product = Product::find($productId);

            if (!$product || $product->stock_quantity < 1) {
                return response()->json(['status' => 'out_of_stock', 'name' => $product?->name ?? 'Product'], 409);
            }

            if (isset($sessionCart[$productId])) {
                $sessionCart[$productId]['quantity']++;
            } else {
                $sessionCart[$productId] = [
                    'product_id' => $productId,
                    'quantity' => 1
                ];
            }

            session()->put('cart', $sessionCart);
            return response()->json(['status' => 'ok', 'product' => $product->name]);
        }

        // Debug helpers for tests
        file_put_contents(storage_path('logs/cart_debug.log'), "Cart add called for user={$user->id} product={$request->product_id}\n", FILE_APPEND);
        Log::info('CartController@add called', ['product_id' => $request->product_id, 'user_id' => $user->id]);

        $productName = null;

        DB::transaction(function () use ($request, $user, &$productName) {
            $product = Product::lockForUpdate()->findOrFail($request->product_id);
            Log::info('Locked product for update', ['product_id' => $product->id, 'stock' => $product->stock_quantity]);

            if ($product->stock_quantity < 1) {
                // no stock left
                return response()->json(['status' => 'out_of_stock', 'name' => $product->name], 409);
            }

            $cartItem = CartItem::firstOrNew([
                'user_id' => $user->id,
                'product_id' => $product->id,
            ]);

            $cartItem->quantity = ($cartItem->exists ? $cartItem->quantity : 0) + 1;
            $cartItem->save();

            $product->stock_quantity = $product->stock_quantity - 1;
            $product->save();

            file_put_contents(storage_path('logs/cart_debug.log'), "Product saved with stock={$product->stock_quantity}\n", FILE_APPEND);

            $productName = $product->name;

            if ($product->stock_quantity <= $product->low_stock_threshold) {
                NotifyLowStock::dispatch($product);
            }
        });

        // return success, client will dispatch events
        return response()->json(['status' => 'ok', 'product' => $productName]);
    }

    public function updateQuantity(Request $request, $id)
    {
        $request->validate(['quantity' => 'required|integer|min:1']);

        $user = Auth::user();
        if (!$user) {
            // For guests, update session cart
            $sessionCart = session()->get('cart', []);
            if (isset($sessionCart[$id])) {
                $sessionCart[$id]['quantity'] = max(1, (int) $request->quantity);
                session()->put('cart', $sessionCart);
                return response()->json(['status' => 'ok']);
            }
            return response()->json(['message' => 'not found'], 404);
        }

        $item = CartItem::findOrFail($id);
        $this->authorizeItemOwner($item);

        $quantity = max(1, (int) $request->quantity);

        DB::transaction(function () use ($item, $quantity) {
            $product = Product::lockForUpdate()->findOrFail($item->product_id);

            $maxAllowed = $item->quantity + $product->stock_quantity;
            $quantity = min($quantity, $maxAllowed);

            $delta = $quantity - $item->quantity;

            if ($delta > 0) {
                $product->stock_quantity = $product->stock_quantity - $delta;
                $product->save();
            } elseif ($delta < 0) {
                $product->stock_quantity = $product->stock_quantity + abs($delta);
                $product->save();
            }

            $item->quantity = $quantity;
            $item->save();
        });

        return response()->json(['status' => 'ok']);
    }

    public function remove($id)
    {
        $user = Auth::user();
        if (!$user) {
            // For guests, remove from session cart
            $sessionCart = session()->get('cart', []);
            unset($sessionCart[$id]);
            session()->put('cart', $sessionCart);
            return response()->json(['status' => 'ok']);
        }

        $item = CartItem::findOrFail($id);
        $this->authorizeItemOwner($item);

        DB::transaction(function () use ($item) {
            $product = Product::lockForUpdate()->findOrFail($item->product_id);

            $product->stock_quantity = $product->stock_quantity + $item->quantity;
            $product->save();

            $item->delete();
        });

        return response()->json(['status' => 'ok']);
    }

    public function clear()
    {
        $user = Auth::user();
        if (!$user) {
            // For guests, clear session cart
            session()->forget('cart');
            return response()->json(['status' => 'ok']);
        }

        DB::transaction(function () use ($user) {
            $items = CartItem::where('user_id', $user->id)->get();
            foreach ($items as $item) {
                $product = Product::lockForUpdate()->findOrFail($item->product_id);
                $product->stock_quantity = $product->stock_quantity + $item->quantity;
                $product->save();
                $item->delete();
            }
        });

        return response()->json(['status' => 'ok']);
    }

    public function placeOrder()
    {
        $user = Auth::user();
        if (! $user) return response()->json(['message' => 'unauthenticated'], 401);

        $items = CartItem::with('product')->where('user_id', $user->id)->get();
        if ($items->isEmpty()) return response()->json(['message' => 'empty'], 400);

        $order = OrderService::placeOrderForUser($user);

        return response()->json(['status' => 'ok', 'order_id' => $order->id]);
    }

    protected function authorizeItemOwner(CartItem $item)
    {
        $user = Auth::user();
        abort_unless($user && $item->user_id === $user->id, 403);
    }
}
