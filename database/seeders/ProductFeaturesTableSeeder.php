<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductFeature;

class ProductFeaturesTableSeeder extends Seeder
{
    public function run(): void
    {
        // جلب كل المنتجات
        $products = Product::all();

        foreach ($products as $product) {

            $features = [
                'جودة عالية',
                'ضمان لمدة سنة',
                'مصنوع من مواد ممتازة',
                'سهل الاستخدام',
                'تصميم أنيق'
            ];

            foreach ($features as $index => $feature) {
                ProductFeature::create([
                    'product_id' => $product->id,
                    'feature' => $feature,
                    'sort_order' => $index + 1,
                ]);
            }
        }
    }
}
