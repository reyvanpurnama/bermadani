<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\Member;
use App\Models\Loan;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Evaluasi dan penyesuaian data payroll & angsuran pinjaman berdasarkan feedback manajemen.
     */
    public function up(): void
    {
        // 1. Deni Saepul: Tandai pinjaman aktif sebagai COMPLETED (lunas)
        $deniMember = Member::where('name', 'like', '%Deni Saepul%')->first();
        if ($deniMember) {
            Loan::where('member_id', $deniMember->id)
                ->where('status', 'ACTIVE')
                ->update(['status' => 'COMPLETED']);
        }

        // 2. Bu Widhi (Widhi Netraning Pertiwi): Penyesuaian data anggota
        $widhiMember = Member::where('name', 'like', '%Widhi Netraning%')->first();

        // 3. Agus Sandi Pamungkas: Update potongan BMT Itqan ke Rp 1.205.850 (bukan Rp 1.500.000)
        $agusMember = Member::where('name', 'like', '%Agus Sandi%')->first();
        if ($agusMember) {
            Loan::where('member_id', $agusMember->id)
                ->where('loanSource', 'BMT_ITQAN')
                ->where('status', 'ACTIVE')
                ->update([
                    'monthlyPayment' => 1205850.00,
                    'simwa_amount' => 30000.00,
                ]);
            
            // Jika ada pinjaman aktif lain milik Agus Sandi, sesuaikan angsurannya
            Loan::where('member_id', $agusMember->id)
                ->where('status', 'ACTIVE')
                ->update([
                    'monthlyPayment' => 1205850.00,
                ]);
        }

        // 4. Asep Indra Sugiri: Potongan pinjaman BMT Itqan sebesar Rp 1.205.850 (sesuai konfirmasi manajemen)
        $asepMember = Member::where('name', 'like', '%Asep Indra%')->first();
        if ($asepMember) {
            Loan::where('member_id', $asepMember->id)
                ->where('status', 'ACTIVE')
                ->update([
                    'monthlyPayment' => 1205850.00,
                    'loanSource' => 'BMT_ITQAN',
                    'simwa_amount' => 30000.00,
                ]);
        }

        // 5. Indra Sasangka: Angsuran pinjaman Rp 1.098.000 (bukan Rp 1.068.000)
        $indraMember = Member::where('name', 'like', '%Indra Sasangka%')->first();
        if ($indraMember) {
            Loan::where('member_id', $indraMember->id)
                ->where('status', 'ACTIVE')
                ->update([
                    'monthlyPayment' => 1098000.00,
                ]);
        }

        // 6. Yusup Sopyan: Potongan pinjaman disesuaikan ke Rp 1.152.800 (sesuai skema BMT Itqan Rp 1.122.800 + Rp 30.000 Simwa)
        $yusupMember = Member::where('name', 'like', '%Yusup Sopyan%')->first();
        if ($yusupMember) {
            Loan::where('member_id', $yusupMember->id)
                ->where('status', 'ACTIVE')
                ->update([
                    'monthlyPayment' => 1152800.00,
                    'simwa_amount' => 30000.00,
                ]);
        }

        // 7. Hapus arsip snapshot laporan bulan 7 (Juli) jika ada, agar sistem secara otomatis melakukan perhitungan ulang (LIVE)
        \App\Models\FinancialReportSnapshot::whereIn('month', [7, '07'])->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse changes if necessary
    }
};
