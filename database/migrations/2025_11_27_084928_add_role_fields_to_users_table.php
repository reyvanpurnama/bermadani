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
            $table->enum('role', ['SUPER_ADMIN', 'ADMIN', 'KASIR', 'SUPPLIER', 'USER', 'DEVELOPER'])
                  ->default('USER')
                  ->after('password');
            
            $table->boolean('isActive')->default(true)->after('role');
            $table->timestamp('lastLoginAt')->nullable()->after('isActive');
            $table->boolean('mustChangePassword')->default(true)->after('lastLoginAt');
            $table->timestamp('passwordChangedAt')->nullable()->after('mustChangePassword');
            
            // Indexes for performance
            $table->index('role');
            $table->index(['isActive', 'role']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['users_role_index']);
            $table->dropIndex(['users_isactive_role_index']);
            
            $table->dropColumn([
                'role',
                'isActive',
                'lastLoginAt',
                'mustChangePassword',
                'passwordChangedAt',
            ]);
        });
    }
};
