<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('color')->nullable();
            $table->string('size')->nullable();
            $table->integer('quantity')->default(1);
            $table->decimal('price', 10, 2); // Lưu giá tại thời điểm thêm vào giỏ
            $table->timestamps();

            // Unique constraint: một user chỉ có một item cho cùng product + color + size
            $table->unique(['user_id', 'product_id', 'color', 'size'], 'unique_cart_item');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};

