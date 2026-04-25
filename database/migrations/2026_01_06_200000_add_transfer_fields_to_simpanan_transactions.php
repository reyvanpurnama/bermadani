<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // First, modify the enum to add new transaction types
        if (DB::connection()->getDriverName() === 'sqlite') {
            Schema::table('simpanan_transactions', function (Blueprint $table) {
                $table->string('transactionType')->change();
            });
        } else {
            DB::statement("ALTER TABLE simpanan_transactions MODIFY COLUMN transactionType ENUM('SETOR', 'TARIK', 'TRANSFER_IN', 'TRANSFER_OUT')");
        }

        Schema::table('simpanan_transactions', function (Blueprint $table) {
            // Related member for transfers (pengirim/penerima)
            $table->unsignedBigInteger('relatedMemberId')->nullable()->after('memberId');

            // Transfer reference to link pair transactions
            $table->string('transferReference', 32)->nullable()->after('status');

            // Foreign key for related member
            $table->foreign('relatedMemberId')
                ->references('id')
                ->on('members')
                ->onDelete('set null');

            // Index for transfer lookups
            $table->index('transferReference');
        });
    }

    public function down(): void
    {
        Schema::table('simpanan_transactions', function (Blueprint $table) {
            $table->dropForeign(['relatedMemberId']);
            $table->dropColumn(['relatedMemberId', 'transferReference']);
        });

        // Revert enum
        if (DB::connection()->getDriverName() === 'sqlite') {
            DB::table('simpanan_transactions')
                ->whereNotIn('transactionType', ['SETOR', 'TARIK'])
                ->update(['transactionType' => 'SETOR']);
        } else {
            DB::statement("ALTER TABLE simpanan_transactions MODIFY COLUMN transactionType ENUM('SETOR', 'TARIK')");
        }
    }
};
