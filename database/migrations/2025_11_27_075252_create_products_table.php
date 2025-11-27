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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('categoryId');
            $table->string('sku')->unique()->nullable();
            
            // Pricing & stock
            $table->decimal('buyPrice', 15, 2)->nullable();
            $table->decimal('sellPrice', 15, 2);
            $table->integer('stock')->default(0);
            $table->integer('threshold')->default(5);
            $table->string('unit')->default('pcs');
            
            // Ownership & status
            $table->enum('ownershipType', ['TOKO', 'TITIPAN', 'SUPPLIER'])->default('TOKO');
            $table->enum('status', ['ACTIVE', 'INACTIVE', 'SEASONAL'])->default('ACTIVE');
            $table->boolean('isConsignment')->default(false);
            $table->boolean('isActive')->default(true);
            
            // Supplier relation
            $table->unsignedBigInteger('supplierId')->nullable();
            $table->string('supplierContact')->nullable();
            
            // Profit sharing for consignment
            $table->decimal('profitShareRate', 5, 2)->default(90.00);
            
            // Stock management
            $table->enum('stockCycle', ['HARIAN', 'MINGGUAN', 'DUA_MINGGUAN'])->default('MINGGUAN');
            $table->decimal('avgCost', 15, 2)->nullable();
            $table->text('expiryPolicy')->nullable();
            $table->timestamp('lastRestockAt')->nullable();
            
            $table->timestamps();
            
            $table->foreign('categoryId')->references('id')->on('categories')->onDelete('restrict');
            $table->foreign('supplierId')->references('id')->on('suppliers')->onDelete('set null');
            
            $table->index(['categoryId', 'isActive']);
            $table->index(['supplierId', 'status']);
            $table->index('name');
            $table->index('stock');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
