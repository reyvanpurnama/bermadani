<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('work_logs', function (Blueprint $table) {
            $table->id();

            // Developer user
            $table->unsignedBigInteger('userId');

            // Work details
            $table->date('date');
            $table->time('startTime')->nullable();
            $table->time('endTime')->nullable();
            $table->decimal('hoursWorked', 5, 2); // Max 999.99 hours
            $table->text('description');

            // Payment info
            $table->decimal('hourlyRate', 10, 2)->default(6000.00);
            $table->decimal('totalAmount', 12, 2); // hoursWorked * hourlyRate

            // Status workflow
            $table->enum('status', ['PENDING', 'APPROVED', 'REJECTED', 'PAID'])->default('PENDING');
            $table->unsignedBigInteger('approvedBy')->nullable();
            $table->timestamp('approvedAt')->nullable();
            $table->timestamp('paidAt')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            // Foreign keys
            $table->foreign('userId')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('approvedBy')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

            // Indexes
            $table->index(['userId', 'date']);
            $table->index(['userId', 'status']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_logs');
    }
};
