<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductImage;

class ProductImagesTableSeeder extends Seeder
{
    public function run(): void
    {
        // جلب كل المنتجات
        $products = Product::all();

        foreach ($products as $product) {

            // 3 صور لكل منتج
            for ($i = 1; $i <= 3; $i++) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_url' => 'storage/صورتي.png',
                    'alt_text' => $product->name . " صورة رقم $i",
                    'sort_order' => $i,
                    'is_primary' => $i === 1, // الصورة الأولى رئيسية
                ]);
            }
        }
    }
}
