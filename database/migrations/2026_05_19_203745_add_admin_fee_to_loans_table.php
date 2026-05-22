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
        if (!Schema::hasColumn('loans', 'admin_fee')) {
            Schema::table('loans', function (Blueprint $table) {
                $table->decimal('admin_fee', 15, 2)->default(0)->after('monthlyPayment');
            });
        }

        if (!Schema::hasColumn('loans', 'is_admin_fee_paid')) {
            Schema::table('loans', function (Blueprint $table) {
                $table->boolean('is_admin_fee_paid')->default(false)->after('admin_fee');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('loans', 'is_admin_fee_paid')) {
            Schema::table('loans', function (Blueprint $table) {
                $table->dropColumn('is_admin_fee_paid');
            });
        }

        if (Schema::hasColumn('loans', 'admin_fee')) {
            Schema::table('loans', function (Blueprint $table) {
                $table->dropColumn('admin_fee');
            });
        }
    }
};
