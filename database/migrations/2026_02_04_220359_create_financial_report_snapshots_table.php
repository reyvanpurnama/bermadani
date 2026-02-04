<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('financial_report_snapshots', function (Blueprint $table) {
            $table->id();
            $table->integer('month');
            $table->integer('year');
            $table->json('data'); // Stores the full report data
            $table->string('status')->default('EXECUTED'); // EXECUTED, DRAFT (future proofing)
            $table->foreignId('executed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            // Ensure one snapshot per month/year
            $table->unique(['month', 'year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_report_snapshots');
    }
};
