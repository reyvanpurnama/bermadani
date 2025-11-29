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
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('member_id');
            $table->decimal('amount', 15, 2);
            $table->decimal('interestRate', 5, 2);
            $table->integer('tenor'); // in months
            $table->decimal('monthlyPayment', 15, 2);
            $table->decimal('remainingAmount', 15, 2);
            $table->enum('status', ['PENDING', 'APPROVED', 'REJECTED', 'ACTIVE', 'COMPLETED', 'OVERDUE'])->default('PENDING');
            $table->text('purpose')->nullable();
            $table->timestamp('approvedAt')->nullable();
            $table->string('approvedBy')->nullable();
            $table->timestamp('startDate')->useCurrent();
            $table->date('endDate'); // Changed to date type
            $table->text('description')->nullable();
            $table->timestamps();
            
            $table->foreign('member_id')->references('id')->on('members')->onDelete('cascade');
            
            $table->index(['member_id', 'status']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
