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
        Schema::create('supplier_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplierId')->constrained('suppliers')->onDelete('cascade');
            $table->string('type'); // BATCH_REQUEST, PAYMENT, INFO, etc.
            $table->string('title');
            $table->text('message');
            $table->string('icon')->nullable();
            $table->string('actionUrl')->nullable();
            $table->boolean('isRead')->default(false);
            $table->timestamp('readAt')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_notifications');
    }
};
