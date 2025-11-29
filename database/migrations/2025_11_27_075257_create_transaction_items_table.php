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
        Schema::create('transaction_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transactionId');
            $table->unsignedBigInteger('productId');
            $table->integer('quantity');
            $table->decimal('unitPrice', 15, 2);
            $table->decimal('totalPrice', 15, 2);
            
            // Cost tracking for profit calculation
            $table->decimal('cogsPerUnit', 15, 2)->nullable();
            $table->decimal('totalCogs', 15, 2)->nullable();
            $table->decimal('grossProfit', 15, 2)->nullable();
            
            $table->boolean('isProduction')->default(true);
            $table->timestamps();
            
            $table->foreign('transactionId')->references('id')->on('transactions')->onDelete('cascade');
            $table->foreign('productId')->references('id')->on('products')->onDelete('restrict');
            
            $table->index('transactionId');
            $table->index('productId');
            $table->index('isProduction');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_items');
    }
};
