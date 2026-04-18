<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Str;

class OrdersTableSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        $statuses = [
            'pending',
            'processing',
            'shipped',
            'delivered',
            'cancelled'
        ];

        for ($i = 1; $i <= 15; $i++) {

            $user = $users->random();

            $subtotal = rand(50, 500);
            $shipping = rand(10, 40);
            $tax = $subtotal * 0.15;
            $discount = rand(0, 30);
            $total = $subtotal + $shipping + $tax - $discount;

            Order::create([
                'user_id' => $user->id,
                'order_number' => 'ORD-' . strtoupper(Str::random(8)),
                'status' => $statuses[array_rand($statuses)],

                'subtotal' => $subtotal,
                'shipping_cost' => $shipping,
                'tax_amount' => $tax,
                'discount_amount' => $discount,
                'total' => $total,
                'currency' => 'SAR',

                'customer_name' => $user->name,
                'customer_email' => $user->email,
                'customer_phone' => $user->phone ?? '0500000000',

                'shipping_address' => 'القصيم - بريدة - حي المنتزه',
                'shipping_city' => 'بريدة',
                'shipping_postal_code' => '51411',

                'notes' => 'تم إنشاء الطلب للتجربة.',
                'paid_at' => rand(0,1) ? now() : null,
            ]);
        }
    }
}
