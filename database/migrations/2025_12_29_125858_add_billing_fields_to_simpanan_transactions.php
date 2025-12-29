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
        Schema::table('simpanan_transactions', function (Blueprint $table) {
            // Billing Month (for grouping bills)
            $table->string('billingMonth', 7)->nullable()->after('type'); // Format: Y-m (e.g., 2025-12)
            
            // Bill Status (separate from payment status)
            $table->enum('billStatus', ['DRAFT', 'APPROVED', 'CANCELLED'])->default('DRAFT')->after('status');
            
            // Payment tracking (calculated field)
            $table->decimal('paidAmount', 15, 2)->default(0)->after('amount');
            
            // Index for faster queries
            $table->index('billingMonth');
            $table->index('billStatus');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('simpanan_transactions', function (Blueprint $table) {
            $table->dropIndex(['billingMonth']);
            $table->dropIndex(['billStatus']);
            $table->dropColumn(['billingMonth', 'billStatus', 'paidAmount']);
        });
    }
};
