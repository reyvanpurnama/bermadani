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
        Schema::create('financial_transactions', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['INCOME', 'EXPENSE']); // Pemasukan atau Pengeluaran
            $table->string('category'); // Listrik, Gaji, Modal, dll
            $table->decimal('amount', 15, 2); // Nominal
            $table->date('transactionDate'); // Tanggal transaksi
            $table->text('description')->nullable(); // Keterangan/catatan
            $table->string('proofFile')->nullable(); // Path bukti struk/foto
            $table->unsignedBigInteger('userId'); // Siapa yang input
            $table->timestamps();
            
            $table->foreign('userId')->references('id')->on('users')->onDelete('cascade');
            
            $table->index(['type', 'transactionDate']);
            $table->index('userId');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_transactions');
    }
};
