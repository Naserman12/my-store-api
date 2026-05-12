<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use Illuminate\Support\Str;

class ProductsTableSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name' => 'سماعة بلوتوث',
                'slug' => Str::slug('سماعة بلوتوث'),
                'description' => 'سماعة عالية الجودة مع عزل ضوضاء.',
                'price' => 120,
                'sale_price' => 99,
                'quantity' => 30,
                'sku' => 'SKU-1001',
                'image' => 'storage/صورتي.png',
                'category_id' => 1,
                'is_featured' => true,
                'is_hidden' => false,
            ],
            [
                'name' => 'كابل شحن سريع',
                'slug' => Str::slug('كابل شحن سريع'),
                'description' => 'كابل USB-C يدعم الشحن السريع.',
                'price' => 35,
                'sale_price' => null,
                'quantity' => 50,
                'sku' => 'SKU-1002',
                'image' => 'storage/صورتي.png',
                'category_id' => 1,
                'is_featured' => false,
                'is_hidden' => false,
            ],
            [
                'name' => 'ماوس لاسلكي',
                'slug' => Str::slug('ماوس لاسلكي'),
                'description' => 'ماوس مريح مع بطارية طويلة.',
                'price' => 80,
                'sale_price' => 60,
                'quantity' => 20,
                'sku' => 'SKU-1003',
                'image' => 'storage/صورتي.png',
                'category_id' => 1,
                'is_featured' => false,
                'is_hidden' => false,
            ],
            [
                'name' => 'غلاية كهربائية',
                'slug' => Str::slug('غلاية كهربائية'),
                'description' => 'غلاية سريعة الغليان بسعة 1.7 لتر.',
                'price' => 150,
                'sale_price' => null,
                'quantity' => 15,
                'sku' => 'SKU-1004',
                'image' => 'storage/صورتي.png',
                'category_id' => 3,
                'is_featured' => true,
                'is_hidden' => false,
            ],
            [
                'name' => 'لعبة تركيب للأطفال',
                'slug' => Str::slug('لعبة تركيب للأطفال'),
                'description' => 'لعبة تعليمية تنمي مهارات الطفل.',
                'price' => 45,
                'sale_price' => 30,
                'quantity' => 40,
                'sku' => 'SKU-1005',
                'image' => 'storage/صورتي.png',
                'category_id' => 5,
                'is_featured' => false,
                'is_hidden' => false,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
