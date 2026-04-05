<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    private function getCart(Request $request)
{
    if ($request->user()) {
        return Cart::firstOrCreate([
            'user_id' => $request->user()->id
        ]);
    }

    $sessionId = $request->header('X-Session-ID');

    return Cart::firstOrCreate([
        'session_id' => $sessionId
    ]);
}
// ===============================
// GET CART
// ===============================
public function index(Request $request)
{
    $cart = $this->getCart($request);

    $cart->load('items.product.images');

    return response()->json($cart);
}
// ===============================
// ADD TO CART
// ===============================
public function add(Request $request)
{
    $request->validate([
        'product_id' => 'required|exists:products,id',
        'quantity' => 'required|integer|min:1'
    ]);

    $cart = $this->getCart($request);

    $item = CartItem::updateOrCreate(
        [
            'cart_id' => $cart->id,
            'product_id' => $request->product_id
        ],
        [
            'quantity' => DB::raw("quantity + {$request->quantity}")
        ]
    );

    return response()->json([
        'message' => 'Added to cart'
    ]);
}
// ===============================
// UPDATE CART ITEM QUANTITY
// ===============================
public function update(Request $request, $id)
{
    $request->validate([
        'quantity' => 'required|integer|min:1'
    ]);

    $item = CartItem::findOrFail($id);

    $item->update([
        'quantity' => $request->quantity
    ]);

    return response()->json(['message'=>'Updated']);
}
// ===============================
// REMOVE FROM CART
// ===============================
public function remove($id)
{
    CartItem::findOrFail($id)->delete();

    return response()->json([
        'message'=>'Removed'
    ]);
}
// ===============================
// CLEAR CART
// ===============================
public function clear(Request $request)
{
    $cart = $this->getCart($request);

    $cart->items()->delete();

    return response()->json([
        'message'=>'Cart cleared'
    ]);
}
}
