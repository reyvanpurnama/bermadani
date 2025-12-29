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
        Schema::create('simpanan_payments', function (Blueprint $table) {
            $table->id();
            
            // References
            $table->foreignId('billId')->constrained('simpanan_transactions')->onDelete('cascade');
            $table->foreignId('memberId')->constrained('members')->onDelete('cascade');
            
            // Payment Details
            $table->decimal('amount', 15, 2);
            $table->enum('paymentMethod', ['CASH', 'TRANSFER', 'AUTO_DEBIT'])->default('CASH');
            $table->date('paymentDate');
            $table->string('referenceNumber')->nullable(); // Nomor transfer/bukti
            $table->string('receiptNumber')->unique(); // Nomor kuitansi
            $table->text('notes')->nullable();
            
            // Bukti Transfer (attachment)
            $table->string('proofAttachment')->nullable();
            
            // Processing Info
            $table->foreignId('processedBy')->constrained('users')->onDelete('restrict');
            
            $table->timestamps();
            
            // Indexes
            $table->index('paymentDate');
            $table->index('receiptNumber');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('simpanan_payments');
    }
};
