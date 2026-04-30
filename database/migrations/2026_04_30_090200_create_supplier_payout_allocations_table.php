<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supplier_payout_allocations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('supplierPayoutId');
            $table->unsignedBigInteger('batchId');
            $table->unsignedBigInteger('consignmentItemId');
            $table->decimal('allocatedAmount', 15, 2)->default(0);
            $table->decimal('allocatedQtyEquivalent', 12, 4)->nullable();
            $table->timestamps();

            $table->foreign('supplierPayoutId')->references('id')->on('supplier_payouts')->onDelete('cascade');
            $table->foreign('batchId')->references('id')->on('consignment_batches')->onDelete('cascade');
            $table->foreign('consignmentItemId')->references('id')->on('consignment_items')->onDelete('cascade');

            $table->index(['consignmentItemId']);
            $table->index(['batchId']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_payout_allocations');
    }
};

