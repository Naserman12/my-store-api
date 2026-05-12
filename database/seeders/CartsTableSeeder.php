<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cart;
use App\Models\User;
use Illuminate\Support\Str;

class CartsTableSeeder extends Seeder
{
    public function run(): void
    {
        // 1) إنشاء سلة لكل مستخدم
        $users = User::all();

        foreach ($users as $user) {
            Cart::create([
                'user_id' => $user->id,
                'session_id' => null,
            ]);
        }

        // 2) إنشاء 5 سلات لزوار (session_id فقط)
        for ($i = 1; $i <= 5; $i++) {
            Cart::create([
                'user_id' => null,
                'session_id' => Str::uuid(),
            ]);
        }
    }
}
