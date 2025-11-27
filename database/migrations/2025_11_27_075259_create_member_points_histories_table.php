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
        Schema::create('member_points_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('memberId');
            $table->unsignedBigInteger('transactionId')->nullable();
            $table->enum('type', ['EARNED', 'REDEEMED', 'EXPIRED', 'ADJUSTED']);
            $table->integer('points');
            $table->integer('balance');
            $table->text('description');
            $table->timestamp('expiresAt')->nullable();
            $table->timestamps();
            
            $table->foreign('memberId')->references('id')->on('members')->onDelete('cascade');
            $table->foreign('transactionId')->references('id')->on('transactions')->onDelete('set null');
            
            $table->index(['memberId', 'created_at']);
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('member_points_histories');
    }
};
