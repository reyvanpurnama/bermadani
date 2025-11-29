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
        Schema::create('cashier_shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('opening_cash', 15, 2)->default(0); // Modal awal
            $table->datetime('check_in_at');
            $table->datetime('check_out_at')->nullable();
            $table->decimal('closing_cash', 15, 2)->nullable(); // Uang di laci saat tutup
            $table->decimal('expected_cash', 15, 2)->nullable(); // Seharusnya (opening + sales cash)
            $table->decimal('difference', 15, 2)->nullable(); // Selisih (closing - expected)
            $table->integer('total_transactions')->default(0);
            $table->decimal('total_sales', 15, 2)->default(0);
            $table->decimal('total_cash_sales', 15, 2)->default(0);
            $table->decimal('total_non_cash_sales', 15, 2)->default(0);
            $table->text('note')->nullable();
            $table->enum('status', ['OPEN', 'CLOSED'])->default('OPEN');
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
            $table->index('check_in_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cashier_shifts');
    }
};
