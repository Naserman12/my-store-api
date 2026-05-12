<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        // 1) مدير رئيسي
        User::create([
            'name' => 'Admin',
            'email' => 'admin@store.com',
            'password' => Hash::make('Admin1234'),
            'role' => 'admin',
            'phone' => '0500000000',
        ]);

        // 2) مدير ثاني (Abo Shakir)
        User::create([
            'name' => 'Abo Shakir',
            'email' => 'shakir@store.com',
            'password' => Hash::make('Admin1234'),
            'role' => 'admin',
            'phone' => '0555555555',
        ]);

        // 3) 10 مستخدمين عاديين
        for ($i = 1; $i <= 10; $i++) {
            User::create([
                'name' => "User $i",
                'email' => "user$i@example.com",
                'password' => Hash::make('password123'),
                'role' => 'customer',
                'phone' => '05800000' . $i,
            ]);
        }
    }
}
