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
        Schema::table('products', function (Blueprint $table) {
            // Thay đổi cột color từ string thành json
            $table->dropColumn('color');
            $table->json('colors')->nullable();
            
            // Thêm cột quantity
            $table->integer('quantity')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Revert lại
            $table->dropColumn(['colors', 'quantity']);
            $table->string('color')->nullable();
        });
    }
};
