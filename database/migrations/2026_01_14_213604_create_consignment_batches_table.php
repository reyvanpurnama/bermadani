<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('consignment_batches', function (Blueprint $table) {
            $table->id();
            $table->string('batchCode')->unique();
            $table->foreignId('supplierId')->constrained('suppliers')->onDelete('cascade');
            $table->enum('status', ['ACTIVE', 'PENDING_SETTLEMENT', 'SETTLED', 'CANCELLED'])->default('ACTIVE');
            $table->decimal('totalValue', 15, 2)->default(0); // Total nilai barang
            $table->decimal('totalSold', 15, 2)->default(0); // Total terjual
            $table->decimal('feeAmount', 15, 2)->default(0); // Fee koperasi
            $table->decimal('payableAmount', 15, 2)->default(0); // Hak supplier
            $table->timestamp('receivedAt')->nullable();
            $table->timestamp('settledAt')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consignment_batches');
    }
};
