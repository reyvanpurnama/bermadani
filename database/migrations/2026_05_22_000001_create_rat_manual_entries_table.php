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
        Schema::create('rat_manual_entries', function (Blueprint $table) {
            $table->id();
            $table->string('table_key');
            $table->string('row_key');
            $table->string('field_key')->default('nilai');
            $table->unsignedInteger('year');
            $table->decimal('amount', 15, 2)->default(0);
            $table->string('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->unique(['table_key', 'row_key', 'field_key', 'year'], 'rat_manual_entries_unique');
            $table->index(['table_key', 'year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rat_manual_entries');
    }
};
