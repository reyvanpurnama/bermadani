<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Member;
use App\Models\Loan;
use App\Models\SimpananTransaction;
use Illuminate\Support\Facades\DB;

class RatReport extends Component
{
    public function render()
    {
        // 1. Evaluasi Simpanan
        $simpananPokok = SimpananTransaction::where('type', 'POKOK')->where('status', 'APPROVED')->sum(DB::raw('CASE WHEN transactionType = "SETOR" THEN amount ELSE -amount END'));
        $simpananWajib = SimpananTransaction::where('type', 'WAJIB')->where('status', 'APPROVED')->sum(DB::raw('CASE WHEN transactionType = "SETOR" THEN amount ELSE -amount END'));
        $simpananSukarela = SimpananTransaction::where('type', 'SUKARELA')->where('status', 'APPROVED')->sum(DB::raw('CASE WHEN transactionType = "SETOR" THEN amount ELSE -amount END'));
        $totalSimpanan = $simpananPokok + $simpananWajib + $simpananSukarela;

        // 2. Evaluasi Pinjaman
        $totalPinjamanTersalurkan = Loan::whereIn('status', ['ACTIVE', 'COMPLETED', 'OVERDUE'])->sum('amount');
        
        $kolektibilitasLancar = Loan::where('status', 'ACTIVE')->count();
        $kolektibilitasLancarRp = Loan::where('status', 'ACTIVE')->sum('remainingAmount');
        
        $kolektibilitasMacet = Loan::where('status', 'OVERDUE')->count();
        $kolektibilitasMacetRp = Loan::where('status', 'OVERDUE')->sum('remainingAmount');
        
        $totalPinjamanAktif = $kolektibilitasLancar + $kolektibilitasMacet;
        $nplRatio = $totalPinjamanAktif > 0 ? round(($kolektibilitasMacet / $totalPinjamanAktif) * 100, 2) : 0;

        // 3. Evaluasi Potongan Gaji (Payroll Projection)
        // Hitung estimasi Simpanan Wajib & Sukarela via Potong Gaji per bulan
        // Asumsi simwa base = 50000 (sesuaikan dengan config jika ada)
        $simwaDeductionMembers = Member::where('simwa_payment_method', 'SALARY_DEDUCTION')->where('status', 'ACTIVE')->count();
        $simwaDeductionEst = $simwaDeductionMembers * 50000; // Contoh 50rb
        
        $sukarelaDeductionEst = Member::where('sukarela_payment_method', 'SALARY_DEDUCTION')->where('status', 'ACTIVE')->sum('monthly_sukarela_amount');

        return view('livewire.admin.rat-report', [
            'simpanan' => [
                'pokok' => $simpananPokok,
                'wajib' => $simpananWajib,
                'sukarela' => $simpananSukarela,
                'total' => $totalSimpanan,
            ],
            'pinjaman' => [
                'tersalurkan' => $totalPinjamanTersalurkan,
                'lancar_count' => $kolektibilitasLancar,
                'lancar_rp' => $kolektibilitasLancarRp,
                'macet_count' => $kolektibilitasMacet,
                'macet_rp' => $kolektibilitasMacetRp,
                'npl_ratio' => $nplRatio,
            ],
            'payroll_est' => [
                'simwa' => $simwaDeductionEst,
                'sukarela' => $sukarelaDeductionEst,
                'total' => $simwaDeductionEst + $sukarelaDeductionEst,
            ]
        ])->layout('layouts.app'); // or wherever the admin layout is
    }
}
