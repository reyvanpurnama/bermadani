<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Loan;
use App\Models\Member;
use App\Models\FinancialReportSnapshot;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Agus Sandi: Matikan pinjaman duplikat (ID 77)
        Loan::where('id', 77)->update(['status' => 'COMPLETED']);

        // 2. Asep Indra: Matikan pinjaman duplikat (ID 60)
        Loan::where('id', 60)->update(['status' => 'COMPLETED']);

        // 3. Yusup Sopyan: Ubah agar masuk kolom BMT Itqan (ID 63)
        Loan::where('id', 63)->update(['loanSource' => 'BMT_ITQAN']);

        // 4. Indra Sasangka: Sesuaikan nominal angsuran aktif menjadi Rp 1.098.000
        $indraMember = Member::where('name', 'like', '%Indra Sasangka%')->first();
        if ($indraMember) {
            Loan::where('member_id', $indraMember->id)
                ->where('status', 'ACTIVE')
                ->update(['monthlyPayment' => 1098000.00]);
        }

        // 5. Hapus arsip cache laporan Juli agar dihitung ulang secara LIVE
        FinancialReportSnapshot::whereIn('month', [7, '07'])->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse changes if necessary
    }
};
