<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       PaymentMethod::insert([

    [
        'name' => 'الدفع عند الاستلام',
        'code' => 'cash',
        'type' => 'cash',
        'is_active' => true,
    ],
    [
        'name' => 'Paystack',
        'code' => 'paystack',
        'type' => 'gateway',
        'is_active' => true,
    ],
    [
        'name' => 'USDT TRC20',
        'code' => 'usdt_trc20',
        'type' => 'crypto',
        'is_active' => false,
    ]

]);
    }
}
