<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consignment_item_counts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('consignmentItemId');
            $table->unsignedBigInteger('batchId');
            $table->unsignedBigInteger('supplierId');
            $table->unsignedBigInteger('productId');
            $table->unsignedBigInteger('userId');

            $table->integer('beforeQty')->default(0);
            $table->integer('physicalQty')->default(0);
            $table->integer('soldDeltaQty')->default(0);

            $table->decimal('soldDeltaAmount', 15, 2)->default(0);
            $table->decimal('payableDeltaAmount', 15, 2)->default(0);
            $table->decimal('marginDeltaAmount', 15, 2)->default(0);

            $table->timestamp('countedAt');
            $table->text('note')->nullable();
            $table->timestamps();

            $table->foreign('consignmentItemId')->references('id')->on('consignment_items')->onDelete('cascade');
            $table->foreign('batchId')->references('id')->on('consignment_batches')->onDelete('cascade');
            $table->foreign('supplierId')->references('id')->on('suppliers')->onDelete('cascade');
            $table->foreign('productId')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('userId')->references('id')->on('users')->onDelete('cascade');

            $table->index(['supplierId', 'countedAt']);
            $table->index(['consignmentItemId', 'countedAt']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consignment_item_counts');
    }
};

