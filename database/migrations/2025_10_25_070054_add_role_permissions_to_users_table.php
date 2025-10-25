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
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('user')->after('email'); // user, admin, moderator
            $table->json('permissions')->nullable()->after('role'); // JSON array of permissions
            $table->boolean('is_active')->default(true)->after('permissions');
            $table->timestamp('last_login_at')->nullable()->after('is_active');
            $table->string('phone')->nullable()->after('last_login_at');
            $table->text('address')->nullable()->after('phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'role',
                'permissions', 
                'is_active',
                'last_login_at',
                'phone',
                'address'
            ]);
        });
    }
};
