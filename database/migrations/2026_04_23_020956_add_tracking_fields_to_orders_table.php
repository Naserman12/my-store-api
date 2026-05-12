<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {

            // تاريخ التوصيل
            $table->timestamp('delivery_date')->nullable()->after('created_at');

            // طريقة الدفع
            $table->string('payment_method')->nullable()->after('delivery_date');

            // رسوم الدفع
            $table->decimal('payment_fee', 10, 2)->default(0)->after('payment_method');

            // طريقة الشحن
            $table->string('shipping_method')->nullable()->after('payment_fee');

            // رقم الفاتورة
            $table->string('invoice_number')->nullable()->unique()->after('shipping_method');

        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {

            $table->dropColumn([
                'delivery_date',
                'payment_method',
                'payment_fee',
                'shipping_method',
                'invoice_number'
            ]);

        });
    }
};