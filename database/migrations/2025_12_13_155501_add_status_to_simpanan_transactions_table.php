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
        Schema::table('simpanan_transactions', function (Blueprint $table) {
            // Status already exists, no need to add
            // Just ensure existing transactions are marked as APPROVED
        });
        
        // Update all existing transactions to APPROVED status
        DB::table('simpanan_transactions')->update(['status' => 'APPROVED']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('simpanan_transactions', function (Blueprint $table) {
            // Nothing to rollback
        });
    }
};
