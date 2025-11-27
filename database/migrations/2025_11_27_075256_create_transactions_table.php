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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('invoiceNumber')->unique();
            $table->unsignedBigInteger('memberId')->nullable();
            $table->enum('type', ['SALE', 'PURCHASE', 'RETURN', 'INCOME', 'EXPENSE']);
            $table->decimal('totalAmount', 15, 2);
            $table->enum('paymentMethod', ['CASH', 'TRANSFER', 'CREDIT'])->default('CASH');
            $table->enum('status', ['PENDING', 'COMPLETED', 'CANCELLED'])->default('COMPLETED');
            $table->text('note')->nullable();
            $table->timestamp('date')->useCurrent();
            $table->boolean('isProduction')->default(true);
            $table->timestamps();
            
            $table->foreign('memberId')->references('id')->on('members')->onDelete('set null');
            
            $table->index(['memberId', 'date']);
            $table->index('status');
            $table->index('date');
            $table->index('paymentMethod');
            $table->index('isProduction');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
