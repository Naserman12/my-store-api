<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;

class CategoriesTableSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'الإلكترونيات',
                'slug' => Str::slug('الإلكترونيات'),
                'description' => 'أجهزة إلكترونية متنوعة',
                'parent_id' => null,
            ],
            [
                'name' => 'الملابس',
                'slug' => Str::slug('الملابس'),
                'description' => 'ملابس رجالية ونسائية',
                'parent_id' => null,
            ],
            [
                'name' => 'الأجهزة المنزلية',
                'slug' => Str::slug('الأجهزة المنزلية'),
                'description' => 'أدوات وأجهزة للمنزل',
                'parent_id' => null,
            ],
            [
                'name' => 'العناية الشخصية',
                'slug' => Str::slug('العناية الشخصية'),
                'description' => 'منتجات العناية بالجسم والشعر',
                'parent_id' => null,
            ],
            [
                'name' => 'الألعاب',
                'slug' => Str::slug('الألعاب'),
                'description' => 'ألعاب أطفال وألعاب إلكترونية',
                'parent_id' => null,
            ],
        ];

        foreach ($categories as $cat) {
            Category::create($cat);
        }
    }
}
