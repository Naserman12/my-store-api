<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    // الحصول على السلة الحالية (إنشاء إذا لم تكن موجودة)
private function getOrCreateCart(Request $request)
{
    $user = $request->user();
    $sessionId = $request->header('X-Session-ID');


    // 👤 مستخدم
    if ($user) {
        return Cart::where('user_id', $user->id)->first();
    }
    

    // 👤 زائر
    if (!$sessionId) {
        $sessionId = uniqid('guest_', true);
    }

    return Cart::firstOrCreate([
        'session_id' => $sessionId
    ]);
}
// ===============================
// GET CART
// ===============================
public function index(Request $request){
            $cart = $this->getOrCreateCart($request);
            $cartItems = CartItem::with('product')
            ->where('cart_id', $cart->id)
            ->get();
            $total = $cartItems->sum(function ($item) {
            $price = $item->product->sale_price ?? $item->product->price;
            return $price * $item->quantity - $item->discount;
        });
        return response()->json([
            'success' => true,
            'items' => $cartItems,
            'total' => $total,
            'count' => $cartItems->sum('quantity'),
            'session_id' => $cart->session_id,
        ]);
}
// ===============================
// ADD TO CART
// ===============================
public function add(Request $request) {
   $validated =  $request->validate([
        'product_id' => 'required|exists:products,id',
        'quantity' => 'required|integer|min:1'
    ]);
    $cart = $this->getOrCreateCart($request);
    // تحديث الكمية إذا كان المنتج موجودًا بالفعل في السلة
    $existingItem = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $validated['product_id'])
            ->first();
            
            if ($existingItem) {
            $existingItem->quantity += $validated['quantity'];
            $existingItem->save();

            return response()->json([
            'success' => true,
              'already_in_cart' => true,
            'message' => 'المنتج موجود بالفعل في السلة. هل تريد الانتقال لصفحة الدفع؟',
            'item' => $existingItem,
            ]);
            }
                    // إضافة منتج جديد للسلة
        $cartItem = CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $request->product_id,
            'quantity' => $request->quantity,

        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم إضافة المنتج للسلة',
            'data' => $cartItem->load('product'),
        ], 201);
}
// ===============================
// UPDATE CART ITEM QUANTITY
// ===============================
public function update(Request $request, $id)
{
    $request->validate([
        'quantity' => 'required|integer|min:1'
    ]);
     $cart = $this->getOrCreateCart($request);
    $item = CartItem::where('id', $id)
            ->where('cart_id', $cart->id)
            ->findOrFail($id);

    $item->quantity = $request->quantity;
    $item->save();

    return response()->json([            
            'success' => true,
            'message' => 'تم تحديث الكمية',
            'data' => $item,
]);
}
// ===============================
// REMOVE FROM CART
// ===============================
public function remove(Request $request, $id)
{
    $cart = $this->getOrCreateCart($request);

    $cartItem = CartItem::where('id', $id)
        ->where('cart_id', $cart->id)
        ->firstOrFail();

    $cartItem->delete();

    return response()->json([
        'success' => true,
        'message' => 'تم حذف المنتج من السلة',
    ]);
}
// ===============================
// CLEAR CART
// ===============================
public function clear(Request $request)
{
    $cart = $this->getOrCreateCart($request);

    $cart->items()->delete();

    return response()->json([
        'message'=>'Cart cleared'
    ]);
}
}
