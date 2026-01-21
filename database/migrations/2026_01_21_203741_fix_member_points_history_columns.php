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
        Schema::table('member_points_histories', function (Blueprint $table) {
            $table->renameColumn('member_id', 'memberId');
            $table->renameColumn('transaction_id', 'transactionId');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('member_points_histories', function (Blueprint $table) {
            $table->renameColumn('memberId', 'member_id');
            $table->renameColumn('transactionId', 'transaction_id');
        });
    }
};
