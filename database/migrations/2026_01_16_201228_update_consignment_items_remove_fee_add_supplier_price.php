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
        Schema::table('consignment_items', function (Blueprint $table) {
            // Remove fee-based columns
            $table->dropColumn(['feePercent', 'priceAfterFee']);
            
            // Add supplier price column
            $table->decimal('supplierPrice', 15, 2)->after('sellPrice')->comment('Harga dari supplier (buyPrice)');
        });
        
        // Update consignment_batches to remove feeAmount
        Schema::table('consignment_batches', function (Blueprint $table) {
            $table->dropColumn('feeAmount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('consignment_items', function (Blueprint $table) {
            $table->dropColumn('supplierPrice');
            $table->decimal('feePercent', 5, 2)->default(10);
            $table->decimal('priceAfterFee', 15, 2);
        });
        
        Schema::table('consignment_batches', function (Blueprint $table) {
            $table->decimal('feeAmount', 15, 2)->default(0);
        });
    }
};
