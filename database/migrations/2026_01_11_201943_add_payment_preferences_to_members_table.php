<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Payment preferences untuk potong gaji koperasi
     * - SIMWA (Simpanan Wajib) - wajib untuk anggota koperasi, default potong gaji
     * - Sukarela - opsional, member bisa pilih potong gaji atau bayar manual
     */
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            // Preferensi pembayaran SIMWA (Simpanan Wajib)
            // SALARY_DEDUCTION = potong gaji, MANUAL = bayar sendiri
            $table->enum('simwa_payment_method', ['SALARY_DEDUCTION', 'MANUAL'])
                  ->default('SALARY_DEDUCTION')
                  ->after('monthly_simpanan_wajib');
            
            // Preferensi pembayaran Sukarela
            $table->enum('sukarela_payment_method', ['SALARY_DEDUCTION', 'MANUAL'])
                  ->default('MANUAL')
                  ->after('simwa_payment_method');
            
            // Jumlah simpanan sukarela per bulan jika pilih potong gaji
            $table->decimal('monthly_sukarela_amount', 15, 2)
                  ->default(0)
                  ->after('sukarela_payment_method');
            
            // Tanggal persetujuan potong gaji (untuk audit trail)
            $table->date('salary_deduction_consent_date')
                  ->nullable()
                  ->after('monthly_sukarela_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn([
                'simwa_payment_method',
                'sukarela_payment_method', 
                'monthly_sukarela_amount',
                'salary_deduction_consent_date'
            ]);
        });
    }
};
