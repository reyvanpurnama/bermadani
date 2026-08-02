<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('member_shu_distributions', function (Blueprint $table) {
            // Rename misleading column: was storing POKOK+WAJIB, not just WAJIB
            $table->renameColumn('simpanan_wajib_amount', 'total_simpanan_amount');
        });

        Schema::table('member_shu_distributions', function (Blueprint $table) {
            // Individual simpanan snapshots at cutoff date
            $table->decimal('simpanan_pokok_snapshot', 15, 2)->default(0)->after('total_simpanan_amount')
                ->comment('Snapshot Simpanan Pokok pada tanggal cutoff');
            $table->decimal('simpanan_wajib_snapshot', 15, 2)->default(0)->after('simpanan_pokok_snapshot')
                ->comment('Snapshot Simpanan Wajib pada tanggal cutoff');

            // SHU breakdown per component
            $table->decimal('jasa_simpanan_amount', 15, 2)->default(0)->after('shu_amount')
                ->comment('Nominal SHU dari komponen Jasa Simpanan');
            $table->decimal('jasa_usaha_amount', 15, 2)->default(0)->after('jasa_simpanan_amount')
                ->comment('Nominal SHU dari komponen Jasa Usaha/Transaksi');
            $table->decimal('total_transaksi_amount', 15, 2)->default(0)->after('jasa_usaha_amount')
                ->comment('Total belanja/transaksi anggota di minimarket (basis Jasa Usaha)');
        });
    }

    public function down(): void
    {
        Schema::table('member_shu_distributions', function (Blueprint $table) {
            $table->dropColumn([
                'simpanan_pokok_snapshot',
                'simpanan_wajib_snapshot',
                'jasa_simpanan_amount',
                'jasa_usaha_amount',
                'total_transaksi_amount',
            ]);
        });

        Schema::table('member_shu_distributions', function (Blueprint $table) {
            $table->renameColumn('total_simpanan_amount', 'simpanan_wajib_amount');
        });
    }
};
