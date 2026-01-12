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
        Schema::create('restock_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('productId');
            $table->unsignedBigInteger('supplierId');
            $table->unsignedBigInteger('requestedBy'); // Admin/Super Admin user ID
            $table->integer('requestedQty');
            $table->text('note')->nullable();
            $table->enum('status', ['PENDING', 'ACCEPTED', 'REJECTED', 'COMPLETED'])->default('PENDING');
            $table->integer('confirmedQty')->nullable();
            $table->text('supplierNote')->nullable();
            $table->timestamp('respondedAt')->nullable();
            $table->timestamp('completedAt')->nullable();
            $table->timestamps();
            
            $table->foreign('productId')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('supplierId')->references('id')->on('suppliers')->onDelete('cascade');
            $table->foreign('requestedBy')->references('id')->on('users')->onDelete('cascade');
            
            $table->index(['supplierId', 'status']);
            $table->index(['productId', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restock_requests');
    }
};
