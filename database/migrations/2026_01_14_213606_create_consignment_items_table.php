<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('consignment_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batchId')->constrained('consignment_batches')->onDelete('cascade');
            $table->foreignId('productId')->constrained('products')->onDelete('cascade');
            $table->integer('initialQty'); // Jumlah awal dititipkan
            $table->integer('soldQty')->default(0); // Jumlah terjual
            $table->integer('remainingQty')->default(0); // Sisa
            $table->decimal('sellPrice', 15, 2); // Harga jual
            $table->decimal('feePercent', 5, 2)->default(10); // Fee % koperasi
            $table->decimal('priceAfterFee', 15, 2); // Harga setelah fee (hak supplier per item)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consignment_items');
    }
};
