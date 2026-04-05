<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code',50)->unique();
            $table->enum('discount_type',['fixed','percentage']);
            $table->decimal('discount_value',10,2);
            $table->timestamp('expires_at')->nullable();
            $table->integer('usage_limit')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};