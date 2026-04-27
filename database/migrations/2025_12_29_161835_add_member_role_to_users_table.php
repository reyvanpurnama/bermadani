<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            Schema::table('users', function (Blueprint $table) {
                $table->string('role')->default('USER')->change();
            });

            return;
        }

        // Add MEMBER to the role enum
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('SUPER_ADMIN','ADMIN','KASIR','SUPPLIER','USER','DEVELOPER','MEMBER') NOT NULL DEFAULT 'USER'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            DB::table('users')->where('role', 'MEMBER')->update(['role' => 'USER']);

            return;
        }

        // Revert MEMBER users to USER first
        DB::statement("UPDATE users SET role = 'USER' WHERE role = 'MEMBER'");

        // Remove MEMBER from enum
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('SUPER_ADMIN','ADMIN','KASIR','SUPPLIER','USER','DEVELOPER') NOT NULL DEFAULT 'USER'");
    }
};
