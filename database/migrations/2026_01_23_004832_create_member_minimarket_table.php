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
        Schema::create('member_minimarket', function (Blueprint $table) {
            $table->id();
            $table->foreignId('userId')->constrained('users')->onDelete('cascade');
            $table->string('memberNumber')->unique(); // MM-2026-0001
            $table->string('cardNumber')->unique(); // Barcode EAN-13
            $table->integer('points')->default(0);
            $table->decimal('totalSpent', 15, 2)->default(0);
            $table->timestamp('lastVisit')->nullable();
            $table->enum('status', ['ACTIVE', 'INACTIVE', 'BLOCKED'])->default('ACTIVE');
            $table->foreignId('registeredBy')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('member_minimarket');
    }
};
