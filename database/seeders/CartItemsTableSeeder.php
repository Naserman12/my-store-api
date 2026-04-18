<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cart;
use App\Models\Product;
use App\Models\CartItem;

class CartItemsTableSeeder extends Seeder
{
    public function run(): void
    {
        $carts = Cart::all();
        $products = Product::all();

        foreach ($carts as $cart) {

            // عدد المنتجات داخل السلة (بين 1 و 4)
            $itemsCount = rand(1, 4);

            // اختيار منتجات عشوائية بدون تكرار
            $selectedProducts = $products->random($itemsCount);

            foreach ($selectedProducts as $product) {
                CartItem::create([
                    'cart_id' => $cart->id,
                    'product_id' => $product->id,
                    'quantity' => rand(1, 3),
                ]);
            }
        }
    }
}
