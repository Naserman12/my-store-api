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
        private function getCart(Request $request)
        {
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
    public function checkout(Request $request)
    {
        $request->validate([
            'customer_name' => 'required',
            'customer_email' => 'required|email',
            'customer_phone' => 'nullable',
            'shipping_address' => 'required'
        ]);

        $cart = $this->getCart($request);
        // dd(
        //     $request->header('X-Session-ID'),
        //     $cart
        // );
        if (!$cart || $cart->items()->count() == 0) {
            return response()->json([
                'message' => 'Cart is empty'
            ], 400);
        }

         DB::beginTransaction();

        try {

            $cart->load('items.product');

            /* =====================
               CALCULATE TOTALS
            ===================== */

            $subtotal = 0;

            foreach ($cart->items as $item) {

                $price = $item->product->sale_price
                    ?? $item->product->price;

                $subtotal += $price * $item->quantity;
            }

            $shipping = 0;
            $tax = 0;
            $discount = 0;

            $total = $subtotal + $shipping + $tax - $discount;

            /* =====================
               CREATE ORDER
            ===================== */

            $order = Order::create([
                'user_id' => optional($request->user())->id,
                'order_number' => 'ORD-' . strtoupper(uniqid()),

                'subtotal' => $subtotal,
                'shipping_cost' => $shipping,
                'tax_amount' => $tax,
                'discount_amount' => $discount,
                'total' => $total,

                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'customer_phone' => $request->customer_phone,
                'shipping_address' => $request->shipping_address,
            ]);

            /* =====================
               COPY ITEMS
            ===================== */

            foreach ($cart->items as $item) {

                $price = $item->product->sale_price
                    ?? $item->product->price;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product->id,

                    'product_name' => $item->product->name,
                    'product_image' =>
                        $item->product->images()
                        ->where('is_primary', true)
                        ->value('image_url'),

                    'quantity' => $item->quantity,
                    'unit_price' => $price,
                    'total_price' => $price * $item->quantity,
                ]);
            }

            /* =====================
               CLEAR CART
            ===================== */

            $cart->items()->delete();

            DB::commit();

            return response()->json([
                'message' => 'Order created successfully',
                'order_number' => $order->order_number
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
