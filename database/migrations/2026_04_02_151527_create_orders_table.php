<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained()
                  ->nullOnDelete();

            $table->string('order_number')->unique();

            $table->enum('status',[
                'pending',
                'processing',
                'shipped',
                'delivered',
                'cancelled'
            ])->default('pending');

            $table->decimal('subtotal',10,2)->default(0);
            $table->decimal('shipping_cost',10,2)->default(0);
            $table->decimal('tax_amount',10,2)->default(0);
            $table->decimal('discount_amount',10,2)->default(0);
            $table->decimal('total',10,2)->default(0);
            $table->string('currency',10)->default('SAR');

            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone',20)->nullable();

            $table->text('shipping_address')->nullable();
            $table->string('shipping_city',100)->nullable();
            $table->string('shipping_postal_code',20)->nullable();

            $table->text('notes')->nullable();
            $table->timestamp('paid_at')->nullable();

                        // تاريخ التوصيل
            $table->timestamp('delivery_date')->nullable();

            // طريقة الدفع
            $table->string('payment_method')->nullable();

            // رسوم الدفع
            $table->decimal('payment_fee', 10, 2)->default(0);

            // طريقة الشحن
            $table->string('shipping_method')->nullable();

            // رقم الفاتورة
            $table->string('invoice_number')->nullable()->unique();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
        
    }
};