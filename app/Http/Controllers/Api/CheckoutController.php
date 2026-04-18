<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    private function getCart(Request $request){
            if ($request->user()) {
                return Cart::where(
                    'user_id',
                    $request->user()->id
                )->first();
            }
            $sessionId = $request->header('X-Session-ID');
            if (!$sessionId) {
                return null;
            }
            return Cart::where(
                'session_id',
                $request->header('X-Session-ID')
            )->first();
        }
        // checkout my orders
 public function checkout(Request $request){

    $user = $request->user();
    $sessionId = $request->header('X-Session-ID');

    $data = $request->validate([
        'address_id' => 'nullable|exists:addresses,id',
        'customer_name' => 'required_without:address_id',
        'customer_phone' => 'nullable|string',
        'shipping_address' => 'required_without:address_id',
        'shipping_city' => 'required_without:address_id',
        'shipping_postal_code' => 'nullable'
    ]);

    if ($request->address_id) {

    $address = $user->addresses()
        ->findOrFail($request->address_id);

    $data['customer_name'] = $address->name;
    $data['shipping_address'] = $address->address;
    $data['shipping_city'] = $address->city;
    $data['shipping_postal_code'] = $address->postal_code;
    }
    if ($user) {
        $cart = Cart::with('items.product')
        ->where('user_id',$user->id)
        ->first();
    } else {
        $cart = Cart::with('items.product')
        ->where('session_id',$sessionId)
        ->first();
    }
    if (!$cart || $cart->items->isEmpty()) {
        return response()->json([
            'message' => 'Cart is empty'
        ], 422);
    }

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
            'shipping_city' => $data['shipping_city'],
            'shipping_postal_code' => $data['shipping_postal_code']
        ]);

        foreach ($cart->items as $item) {

            $price = $item->product->sale_price
                ?? $item->product->price;

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item->product_id,
                'product_name' => $item->product->name,
                'product_image' => optional(
                    $item->product->images()->first()
                )->image_url,

                'quantity' => $item->quantity,
                'unit_price' => $price,
                'total_price' => $price * $item->quantity
            ]);
        }

        // تفريغ السلة
        $cart->items()->delete();

        DB::commit();

        return response()->json([
            'message' => 'Order created',
            'order_id' => $order->id
        ]);

    } catch (\Exception $e) {

        DB::rollBack();
        throw $e;
    }
}
    // public function checkout(Request $request)
    // {
    //     $request->validate([
    //         'customer_name' => 'required',
    //         'customer_email' => 'required|email',
    //         'customer_phone' => 'nullable',
    //         'shipping_address' => 'required'
    //     ]);

    //     $cart = $this->getCart($request);
    //     // dd(
    //     //     $request->header('X-Session-ID'),
    //     //     $cart
    //     // );
    //     if (!$cart || $cart->items()->count() == 0) {
    //         return response()->json([
    //             'message' => 'Cart is empty'
    //         ], 400);
    //     }

    //      DB::beginTransaction();

    //     try {

    //         $cart->load('items.product');

    //         /* =====================
    //            CALCULATE TOTALS
    //         ===================== */

    //         $subtotal = 0;

    //         foreach ($cart->items as $item) {

    //             $price = $item->product->sale_price
    //                 ?? $item->product->price;

    //             $subtotal += $price * $item->quantity;
    //         }
    //         $shipping = 0;
    //         $tax = 0;
    //         $discount = 0;

    //         $total = $subtotal + $shipping + $tax - $discount;

    //         /* =====================
    //            CREATE ORDER
    //         ===================== */

    //         $order = Order::create([
    //             'user_id' => optional($request->user())->id,
    //             'order_number' => 'ORD-' . strtoupper(uniqid()),

    //             'subtotal' => $subtotal,
    //             'shipping_cost' => $shipping,
    //             'tax_amount' => $tax,
    //             'discount_amount' => $discount,
    //             'total' => $total,

    //             'customer_name' => $request->customer_name,
    //             'customer_email' => $request->customer_email,
    //             'customer_phone' => $request->customer_phone,
    //             'shipping_address' => $request->shipping_address,
    //         ]);

    //         /* =====================
    //            COPY ITEMS
    //         ===================== */

    //         foreach ($cart->items as $item) {

    //             $price = $item->product->sale_price
    //                 ?? $item->product->price;

    //             OrderItem::create([
    //                 'order_id' => $order->id,
    //                 'product_id' => $item->product->id,

    //                 'product_name' => $item->product->name,
    //                 'product_image' =>
    //                     $item->product->images()
    //                     ->where('is_primary', true)
    //                     ->value('image_url'),

    //                 'quantity' => $item->quantity,
    //                 'unit_price' => $price,
    //                 'total_price' => $price * $item->quantity,
    //             ]);
    //         }

    //         /* =====================
    //            CLEAR CART
    //         ===================== */

    //         $cart->items()->delete();

    //         DB::commit();

    //         return response()->json([
    //             'message' => 'Order created successfully',
    //             'order_number' => $order->order_number
    //         ]);

    //     } catch (\Exception $e) {

    //         DB::rollBack();

    //         return response()->json([
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }
}
