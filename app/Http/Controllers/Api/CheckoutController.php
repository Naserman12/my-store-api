<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusLog;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
private function getCart(Request $request)
{
    $user = $request->user();
    $sessionId = $request->header('X-Session-ID');

    // 👤 إذا مسجل دخول
    if ($user) {

        // 🔥 حاول تجيب سلة user أولًا
        $cart = Cart::where('user_id', $user->id)->first();

        if ($cart) return $cart;
    }

    // 👤 guest
    if ($sessionId) {
        return Cart::where('session_id', $sessionId)->first();
    }

    return null;
}
//         // checkout my orders
public function checkout(Request $request)
{
    $user = $request->user();
    $sessionId = $request->header('X-Session-ID');

           $cart = $this->getCart($request);

    if (!$cart || $cart->items->isEmpty()) {
        return response()->json([
            'user => ' => $user,
            'sessionId  => ' => $sessionId,
             'cart =>' => $cart,
            'items =>' => $cart?->items,
            'message' => 'Cart is Empty ❌'
        ], 400);
    }

    $data = $request->validate([
        'address_id' => 'nullable|exists:addresses,id',
        'customer_name' => 'required_without:address_id',
        'customer_phone' => 'required|string|min:9|max:15',
        'shipping_address' => 'required_without:address_id',
        'shipping_city' => 'required_without:address_id',
        'shipping_postal_code' => 'nullable',
        'payment_methods' => 'nullable|string',
    ]);

    DB::beginTransaction();

    try {

        $subtotal = 0;

        foreach ($cart->items as $item) {
            $price = $item->product->sale_price
                ?? $item->product->price;

            $subtotal += $price * $item->quantity;
        }

        $order = Order::create([
            'user_id' => optional($user)->id,
            'order_number' => 'ORD-' . time(),
            'subtotal' => $subtotal,
            'total' => $subtotal,
            'customer_name' => $data['customer_name'],
            'customer_phone' => $data['customer_phone'],
            'customer_email' => optional($user)->email,
            'shipping_address' => $data['shipping_address'],
            'status' => 'pending',
            'shipping_city' => $data['shipping_city'],
            'shipping_postal_code' => $data['shipping_postal_code']
        ]);
        // 🔥 أول خطوة في التايم لاين
            OrderStatusLog::create([
                'order_id' => $order->id,
                'status' => 'pending_payment',
                'note' => 'تم إنشاء الطلب',
            ]);

        foreach ($cart->items as $item) {
            $price = $item->product->sale_price
                ?? $item->product->price;

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item->product_id,
                'product_name' => $item->product->name,
                'product_image' => optional($item->product->images()->first())->image_url,
                'quantity' => $item->quantity,
                'unit_price' => $price,
                'total_price' => $price * $item->quantity
            ]);
        }
        Payment::create([
        'order_id' =>  $order->id,
        'status' => 'pending',
        'payment_method' => $request->payment_method,
        'amount' => $order->total,
         ]);
       if ($user) {
       sendNotification(
                $user->id,
                "📦 تم إنشاء طلبك",
                "طلبك رقم {$order->order_number} تم استلامه بنجاح"
            );
        }
        // 🔥 تفريغ السلة بعد إنشاء الطلب
    //    $cart->items()->delete();
        DB::commit();

        return response()->json([
            'deleted_cart_id' => $cart->id,
            'order' => $order->load('items'),
            'message' => 'Order created ✅',
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        throw $e;
    }
}

public function pay(Request $request)
{
    $request->validate([
        'order_id' => 'required|exists:orders,id',
        'payment_method' => 'required'
    ]);

    $order = Order::findOrFail($request->order_id);

    // 🔥 تحديث الحالة
    $order->update([
        'status' => 'processing',
        'payment_method' => $request->payment_method
    ]);

    Payment::create([
        'order_id' =>  $order->id,
        'status' => 'paid',
        'payment_method' => $request->payment_method,
        'amount' => $order->total,
    ]);
    // 🔥 تسجيل في التايم لاين
    OrderStatusLog::create([
        'order_id' => $order->id,
        'status' => 'paid',
        'note' => 'تم الدفع بنجاح 💳'
    ]);
    return response()->json([
        'message' => 'تم الدفع بنجاح',
        'order' => $order
    ]);
}
// public function updateStatus($status, $note = null)
// {
//     $this->update([
//         'status' => $status
//     ]);

//     OrderStatusLog::create([
//         'order_id' => $this->id,
//         'status' => $status,
//         'note' => $note
//     ]);
// }
//  public function checkout(Request $request){

//      $user = $request->user();
//      $sessionId = $request->header('X-Session-ID');
//      $cart = Cart::with('items.product')->where(function ($q) use ($user, $sessionId){
//             if ($user) {
//                 $q->where('user_id', $user->id);
//             }elseif($sessionId){
//                 $q->where('session_id', $sessionId);
//                 }
//                 })->first();
//                 // dd(['sessionId =>' => $request->header('X-Session-ID'),'user_id => ' => optional($request->user())->id]);
//                 if (!$cart) {
//         return response()->json(['message' => ' Cart not found'], 400);
//         }
//         $cartItems = CartItem::where('cart_id', $cart->id)->get();
//         if (!$cart || $cart->items->isEmpty()) {
//             return response()->json(['message' =>  'Cart is Empty ❌'], 400);
//             }
//         $data = $request->validate([
//         'address_id' => 'nullable|exists:addresses,id',
//         'customer_name' => 'required_without:address_id',
//         'customer_phone' => 'nullable|string',
//         'shipping_address' => 'required_without:address_id',
//         'shipping_city' => 'required_without:address_id',
//         'shipping_postal_code' => 'nullable'
//         ]);

//     if ($request->address_id) {

//     $address = $user->addresses()
//         ->findOrFail($request->address_id);

//     $data['customer_name'] = $address->user->name ?? 'غير معروف';
//     $data['customer_phone'] = $address->customer_phone ?? $address->user->phone ?? null;
//     $data['shipping_address'] = $address->address;  
//     $data['shipping_city'] = $address->city;
//     $data['shipping_postal_code'] = $address->postal_code;
//     }

//     DB::beginTransaction();

//     try {
//         $subtotal = 0;

//         foreach ($cart->items as $item) {
//         $price = $item->product->sale_price
//             ?? $item->product->price;

//         $subtotal += $price * $item->quantity;
//         }
//         $order = Order::create([
//             'user_id' => optional($user)->id,
//             'order_number' => 'ORD-' . time(),
//             'subtotal' => $subtotal,
//             'total' => $subtotal,
//             'customer_name' => $data['customer_name'],
//             'customer_phone' => $data['customer_phone'],
//             'customer_email' => optional($user)->email,
//             'shipping_address' => $data['shipping_address'],
//             'shipping_city' => $data['shipping_city'],
//             'shipping_postal_code' => $data['shipping_postal_code']
//         ]);

//         foreach ($cart->items as $item) {
//             $price = $item->product->sale_price
//                 ?? $item->product->price;

//             OrderItem::create([
//                 'order_id' => $order->id,
//                 'product_id' => $item->product_id,
//                 'product_name' => $item->product->name,
//                 'product_image' => optional(
//                     $item->product->images()->first()
//                 )->image_url,

//                 'quantity' => $item->quantity,
//                 'unit_price' => $price,
//                 'total_price' => $price * $item->quantity
//             ]);
//         }

//         // تفريغ السلة
//         $cart->items()->delete();

//         DB::commit();

//         return response()->json([
//             'message' => 'Order created',
//             'order_id' => $order->id
//         ]);

//     } catch (\Exception $e) {

//         DB::rollBack();
//         throw $e;
//     }
// }
}
