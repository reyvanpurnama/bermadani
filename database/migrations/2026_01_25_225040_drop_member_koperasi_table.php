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
        Schema::dropIfExists('member_koperasi');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tidak perlu recreate karena table ini duplicate
        // Table 'members' yang dipakai untuk Member Koperasi
    }
};
