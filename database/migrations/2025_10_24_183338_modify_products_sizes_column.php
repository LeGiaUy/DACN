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
        // Migration này dùng để chuyển từ cột string `size` sang cột JSON `sizes`.
        // Tuy nhiên trên database mới (như MySQL sau khi chuyển từ SQLite),
        // có thể bảng `products` KHÔNG còn cột `size` hoặc đã có sẵn `sizes`.
        //
        // Vì vậy ta cần kiểm tra trước khi thao tác để tránh lỗi "Can't DROP 'size'".

        if (Schema::hasTable('products')) {
            // Nếu còn cột `size` thì mới drop
            if (Schema::hasColumn('products', 'size')) {
                Schema::table('products', function (Blueprint $table) {
                    $table->dropColumn('size');
                });
            }

            // Nếu chưa có cột `sizes` thì thêm mới
            if (! Schema::hasColumn('products', 'sizes')) {
                Schema::table('products', function (Blueprint $table) {
                    $table->json('sizes')->nullable();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('products')) {
            // Nếu có `sizes` thì xóa, sau đó thêm lại `size` dạng string (nếu chưa có)
            if (Schema::hasColumn('products', 'sizes')) {
                Schema::table('products', function (Blueprint $table) {
                    $table->dropColumn('sizes');
                });
            }

            if (! Schema::hasColumn('products', 'size')) {
                Schema::table('products', function (Blueprint $table) {
                    $table->string('size')->nullable();
                });
            }
        }
    }
};
