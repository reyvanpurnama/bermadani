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
        Schema::table('members', function (Blueprint $table) {
            $table->decimal('monthly_wajib_amount', 15, 2)->default(50000)->after('simpananSukarela');
            $table->date('last_wajib_debit_date')->nullable()->after('monthly_wajib_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn(['monthly_wajib_amount', 'last_wajib_debit_date']);
        });
    }
};
