<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('simpanan_transactions', function (Blueprint $table) {
            $table->id();
            
            // Foreign key to members table
            $table->unsignedBigInteger('memberId');
            
            // Simpanan type: POKOK, WAJIB, SUKARELA
            $table->enum('type', ['POKOK', 'WAJIB', 'SUKARELA']);
            
            // Transaction type: SETOR (deposit) or TARIK (withdrawal)
            $table->enum('transactionType', ['SETOR', 'TARIK']);
            
            // Amount of transaction
            $table->decimal('amount', 15, 2);
            
            // Balance after this transaction
            $table->decimal('balanceAfter', 15, 2);
            
            // Optional notes
            $table->text('notes')->nullable();
            
            // Path to uploaded proof (bukti transfer/setoran)
            $table->string('buktiPath')->nullable();
            
            // Who processed this transaction (admin user ID)
            $table->unsignedBigInteger('processedBy');
            
            // For withdrawals: approval system
            $table->enum('status', ['PENDING', 'APPROVED', 'REJECTED'])->default('APPROVED');
            $table->unsignedBigInteger('approvedBy')->nullable();
            $table->timestamp('approvedAt')->nullable();
            $table->text('rejectionReason')->nullable();
            
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('memberId')
                  ->references('id')
                  ->on('members')
                  ->onDelete('cascade');
                  
            $table->foreign('processedBy')
                  ->references('id')
                  ->on('users')
                  ->onDelete('restrict');
                  
            $table->foreign('approvedBy')
                  ->references('id')
                  ->on('users')
                  ->onDelete('restrict');
            
            // Indexes
            $table->index(['memberId', 'type']);
            $table->index(['memberId', 'created_at']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('simpanan_transactions');
    }
};
