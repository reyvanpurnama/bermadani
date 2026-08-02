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
            $table->json('included_member_ids')->nullable()->after('excluded_member_ids');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rat_sessions', function (Blueprint $table) {
            $table->dropColumn(['included_member_ids']);
        });
    }
};
