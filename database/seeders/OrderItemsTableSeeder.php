<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;

class OrderItemsTableSeeder extends Seeder
{
    public function run(): void
    {
        $orders = Order::all();
        $products = Product::all();

        foreach ($orders as $order) {

            // عدد المنتجات داخل الطلب (بين 1 و 5)
            $itemsCount = rand(1, 5);

            // اختيار منتجات عشوائية بدون تكرار
            $selectedProducts = $products->random($itemsCount);

            foreach ($selectedProducts as $product) {

                $quantity = rand(1, 3);
                $unitPrice = $product->sale_price ?? $product->price;
                $totalPrice = $unitPrice * $quantity;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,

                    'product_name' => $product->name,
                    'product_image' => $product->image ?? 'storage/صورتي.png',

                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPrice,
                ]);
            }
        }
    }
}
