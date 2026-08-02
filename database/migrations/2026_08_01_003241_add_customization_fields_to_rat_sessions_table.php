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
        Schema::table('rat_sessions', function (Blueprint $table) {
            $table->date('join_date_cutoff')->nullable()->after('total_simpanan_wajib_snapshot');
            $table->json('excluded_member_ids')->nullable()->after('join_date_cutoff');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rat_sessions', function (Blueprint $table) {
            $table->dropColumn(['join_date_cutoff', 'excluded_member_ids']);
        });
    }
};
