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
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->enum('movementType', ['PURCHASE_IN', 'CONSIGNMENT_IN', 'CONSIGNMENT_RETURN', 'SALE_OUT', 'RETURN_IN', 'RETURN_OUT', 'EXPIRED_OUT', 'ADJUSTMENT', 'TRANSFER_IN', 'TRANSFER_OUT', 'RESTOCK']);
            $table->integer('quantity');
            $table->enum('referenceType', ['PURCHASE', 'CONSIGNMENT_BATCH', 'SALE', 'ADJUSTMENT', 'EXPIRY', 'STOCK_REQUEST'])->nullable();
            $table->string('referenceId')->nullable();
            $table->decimal('unitCost', 15, 2)->nullable();
            $table->text('note')->nullable();
            $table->timestamp('occurredAt')->useCurrent();
            $table->boolean('isProduction')->default(true);
            $table->timestamps();
            
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            
            $table->index(['product_id', 'occurredAt']);
            $table->index(['referenceType', 'referenceId']);
            $table->index('isProduction');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
