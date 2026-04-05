<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price',10,2)->default(0);
            $table->decimal('sale_price',10,2)->nullable();
            $table->integer('quantity')->default(0);
            $table->string('sku')->nullable()->unique();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_hidden')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};