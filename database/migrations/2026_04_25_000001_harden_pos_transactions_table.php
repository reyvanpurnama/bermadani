<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('savings', 'member_id') && ! Schema::hasColumn('savings', 'memberId')) {
            Schema::table('savings', function (Blueprint $table) {
                $table->renameColumn('member_id', 'memberId');
            });
        }

        Schema::table('transactions', function (Blueprint $table) {
            if (! Schema::hasColumn('transactions', 'checkoutToken')) {
                $table->string('checkoutToken', 64)->nullable()->unique()->after('invoiceNumber');
            }

            if (! Schema::hasColumn('transactions', 'cashierShiftId')) {
                $table->foreignId('cashierShiftId')
                    ->nullable()
                    ->after('userId')
                    ->constrained('cashier_shifts')
                    ->nullOnDelete();
            }
        });

        if (DB::connection()->getDriverName() === 'sqlite') {
            Schema::table('transactions', function (Blueprint $table) {
                $table->string('paymentMethod')->default('CASH')->change();
            });
        } else {
            DB::statement("ALTER TABLE transactions MODIFY COLUMN paymentMethod ENUM('CASH','TRANSFER','CREDIT','SUKARELA') NOT NULL DEFAULT 'CASH'");
        }
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() !== 'sqlite') {
            DB::table('transactions')
                ->where('paymentMethod', 'SUKARELA')
                ->update(['paymentMethod' => 'CREDIT']);

            DB::statement("ALTER TABLE transactions MODIFY COLUMN paymentMethod ENUM('CASH','TRANSFER','CREDIT') NOT NULL DEFAULT 'CASH'");
        }

        Schema::table('transactions', function (Blueprint $table) {
            if (Schema::hasColumn('transactions', 'cashierShiftId')) {
                $table->dropConstrainedForeignId('cashierShiftId');
            }

            if (Schema::hasColumn('transactions', 'checkoutToken')) {
                $table->dropUnique(['checkoutToken']);
                $table->dropColumn('checkoutToken');
            }
        });

        if (Schema::hasColumn('savings', 'memberId') && ! Schema::hasColumn('savings', 'member_id')) {
            Schema::table('savings', function (Blueprint $table) {
                $table->renameColumn('memberId', 'member_id');
            });
        }
    }
};
