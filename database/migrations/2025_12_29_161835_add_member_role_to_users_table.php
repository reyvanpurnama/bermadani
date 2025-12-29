<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add MEMBER to the role enum
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('SUPER_ADMIN','ADMIN','KASIR','SUPPLIER','USER','DEVELOPER','MEMBER') NOT NULL DEFAULT 'USER'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert MEMBER users to USER first
        DB::statement("UPDATE users SET role = 'USER' WHERE role = 'MEMBER'");
        
        // Remove MEMBER from enum
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('SUPER_ADMIN','ADMIN','KASIR','SUPPLIER','USER','DEVELOPER') NOT NULL DEFAULT 'USER'");
    }
};
