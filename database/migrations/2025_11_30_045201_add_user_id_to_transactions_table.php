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
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignId('userId')->nullable()->after('memberId')->constrained('users')->nullOnDelete();
        });

        // Set existing transactions to first admin user
        $adminUser = \App\Models\User::where('role', 'SUPER_ADMIN')->first();
        if ($adminUser) {
            \DB::table('transactions')->whereNull('userId')->update(['userId' => $adminUser->id]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['userId']);
            $table->dropColumn('userId');
        });
    }
};
