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
        Schema::create('member_settlements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('members')->onDelete('cascade');
            
            // Savings breakdown snapshot
            $table->decimal('simpanan_pokok', 15, 2)->default(0);
            $table->decimal('simpanan_wajib', 15, 2)->default(0);
            $table->decimal('simpanan_sukarela', 15, 2)->default(0);
            $table->decimal('total_gross_simpanan', 15, 2)->default(0);

            // Deductions & net refund
            $table->decimal('loan_deduction', 15, 2)->default(0);
            $table->decimal('net_refund_amount', 15, 2)->default(0);

            // Settlement details
            $table->enum('status', ['PENDING', 'SETTLED'])->default('PENDING');
            $table->enum('payment_method', ['BANK_TRANSFER', 'CASH'])->default('BANK_TRANSFER');
            $table->string('bank_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('bank_account_holder')->nullable();

            $table->timestamp('settled_at')->nullable();
            $table->foreignId('settled_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['member_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('member_settlements');
    }
};
