<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Product;
use App\Models\Wishlist;

class WishlistsTableSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $products = Product::all();

        foreach ($users as $user) {

            // عدد المنتجات في المفضلة (بين 1 و 5)
            $count = rand(1, 5);

            // اختيار منتجات عشوائية بدون تكرار
            $selectedProducts = $products->random($count);

            foreach ($selectedProducts as $product) {
                Wishlist::create([
                    'user_id' => $user->id,
                    'product_id' => $product->id,
                ]);
            }
        }
    }
}