<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supplier_payouts', function (Blueprint $table) {
            $table->id();
            $table->string('payoutCode')->unique();
            $table->unsignedBigInteger('supplierId');
            $table->unsignedBigInteger('userId');
            $table->date('payoutDate');
            $table->decimal('grossDueAmount', 15, 2)->default(0);
            $table->decimal('paidAmount', 15, 2)->default(0);
            $table->decimal('outstandingAfter', 15, 2)->default(0);
            $table->text('note')->nullable();
            $table->timestamps();

            $table->foreign('supplierId')->references('id')->on('suppliers')->onDelete('cascade');
            $table->foreign('userId')->references('id')->on('users')->onDelete('cascade');

            $table->index(['supplierId', 'payoutDate']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_payouts');
    }
};

