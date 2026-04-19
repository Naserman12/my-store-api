<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;

class OrderController extends Controller
{
    //
    // checkout my orders

public function checkout(Request $request)
{
    $user = $request->user();

    $data = $request->validate([
        'address_id' => 'nullable|exists:addresses,id',
        'customer_name' => 'required',
        'customer_phone' => 'nullable|string',
        'shipping_address' => 'required',
        'shipping_city' => 'required',
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
    $cart = Cart::with('items.product')
        ->where('user_id',$user->id)
        ->first();

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
            'user_id' => $user->id,
            'order_number' => 'ORD-' . time(),
            'subtotal' => $subtotal,
            'total' => $subtotal,
            'customer_name' => $data['customer_name'],
            'customer_phone' => $data['customer_phone'],
            'customer_email' => $user->email,
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
    public function myOrders(Request $request)
{
    return $request->user()
        ->orders()
        ->with('items')
        ->latest()
        ->get();
}
public function show(Request $request, $id)
{
    return $request->user()
        ->orders()
        ->with('items')
        ->findOrFail($id);
}
}
