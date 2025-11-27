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
        Schema::create('savings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('memberId');
            $table->enum('type', ['POKOK', 'WAJIB', 'SUKARELA', 'WITHDRAWAL']);
            $table->decimal('amount', 15, 2);
            $table->text('description')->nullable();
            $table->timestamp('date')->useCurrent();
            $table->timestamps();
            
            $table->foreign('memberId')->references('id')->on('members')->onDelete('cascade');
            
            $table->index(['memberId', 'date']);
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('savings');
    }
};
