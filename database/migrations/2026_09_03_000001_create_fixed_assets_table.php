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
        Schema::create('fixed_assets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('category', ['PERALATAN', 'KENDARAAN', 'BANGUNAN', 'LAINNYA']);
            $table->date('acquisition_date');
            $table->decimal('acquisition_cost', 15, 2);
            $table->integer('useful_life_months');
            $table->decimal('salvage_value', 15, 2)->default(0);
            $table->enum('depreciation_method', ['STRAIGHT_LINE'])->default('STRAIGHT_LINE');
            $table->enum('status', ['ACTIVE', 'DISPOSED', 'WRITTEN_OFF'])->default('ACTIVE');
            $table->date('disposed_at')->nullable();
            $table->decimal('disposed_value', 15, 2)->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('category');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fixed_assets');
    }
};
