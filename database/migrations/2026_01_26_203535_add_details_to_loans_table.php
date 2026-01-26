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
        Schema::table('loans', function (Blueprint $table) {
            $table->integer('paid_installments')->default(0)->after('tenor')->comment('Jumlah angsuran yang sudah dibayar');
            $table->string('account_number')->nullable()->after('member_id')->comment('Nomor Rekening (BMT)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropColumn(['paid_installments', 'account_number']);
        });
    }
};
