<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rat_sessions', function (Blueprint $table) {
            // 5-pos allocation percentages (AD/ART Koperasi)
            $table->decimal('cadangan_percentage', 5, 2)->default(25.00)->after('member_allocation_percentage')
                ->comment('Alokasi Dana Cadangan (%)');
            $table->decimal('jasa_simpanan_percentage', 5, 2)->default(30.00)->after('cadangan_percentage')
                ->comment('Alokasi Jasa Simpanan Anggota (%)');
            $table->decimal('jasa_usaha_percentage', 5, 2)->default(25.00)->after('jasa_simpanan_percentage')
                ->comment('Alokasi Jasa Usaha/Transaksi Anggota (%)');
            $table->decimal('pengurus_percentage', 5, 2)->default(10.00)->after('jasa_usaha_percentage')
                ->comment('Alokasi Honorarium Pengurus (%)');
            $table->decimal('dana_sosial_percentage', 5, 2)->default(10.00)->after('pengurus_percentage')
                ->comment('Alokasi Dana Pendidikan & Sosial (%)');

            // Audit trail
            $table->unsignedBigInteger('finalized_by')->nullable()->after('created_by');
            $table->timestamp('finalized_at')->nullable()->after('finalized_by');
        });

        // Migrate existing status values: 'DRAFT' stays, 'FINALIZED' stays
        // New valid statuses: DRAFT, CONFIGURED, MEMBERS_LOCKED, FINALIZED, DISBURSING, COMPLETED
    }

    public function down(): void
    {
        Schema::table('rat_sessions', function (Blueprint $table) {
            $table->dropColumn([
                'cadangan_percentage',
                'jasa_simpanan_percentage',
                'jasa_usaha_percentage',
                'pengurus_percentage',
                'dana_sosial_percentage',
                'finalized_by',
                'finalized_at',
            ]);
        });
    }
};
