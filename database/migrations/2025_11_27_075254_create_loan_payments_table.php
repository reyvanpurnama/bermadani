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
        Schema::create('loan_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('loanId');
            $table->decimal('amount', 15, 2);
            $table->timestamp('paymentDate')->useCurrent();
            $table->text('description')->nullable();
            $table->timestamps();
            
            $table->foreign('loanId')->references('id')->on('loans')->onDelete('cascade');
            
            $table->index('loanId');
            $table->index('paymentDate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_payments');
    }
};
