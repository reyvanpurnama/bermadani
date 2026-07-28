<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Member;
use App\Models\Loan;
use App\Models\SimpananTransaction;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RatReport extends Component
{
    public $selectedYear;
    public $availableYears = [];

    public function mount()
    {
        $this->selectedYear = Carbon::now()->year;
        
        // Dapatkan tahun paling awal ada transaksi, fallback ke tahun sekarang jika kosong
        $startYearSimpanan = SimpananTransaction::min(DB::raw('YEAR(created_at)'));
        $startYearLoan = Loan::min(DB::raw('YEAR(startDate)'));
        $startYearBank = \App\Models\BankTransaction::min(DB::raw('YEAR(transaction_date)'));
        
        $startYear = max(2020, min(
            $startYearSimpanan ?: $this->selectedYear, 
            $startYearLoan ?: $this->selectedYear,
            $startYearBank ?: $this->selectedYear
        ));
        
        $years = range($startYear, Carbon::now()->year);
        rsort($years);
        $this->availableYears = $years;
    }

    public function exportSimpananCsv()
    {
        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            
            // Header CSV
            fputcsv($handle, [
                'No. Anggota',
                'Nama',
                'Unit Kerja',
                'Status',
                'Simpanan Pokok',
                'Simpanan Wajib',
                'Simpanan Sukarela',
                'Total Simpanan'
            ]);
            
            // Data Anggota yang ikut koperasi
            $members = Member::where('isMemberKoperasi', true)->get();
            
            foreach ($members as $member) {
                $total = $member->simpananPokok + $member->simpananWajib + $member->simpananSukarela;
                fputcsv($handle, [
                    $member->nomorAnggota,
                    $member->name,
                    $member->unitKerja,
                    $member->status,
                    $member->simpananPokok,
                    $member->simpananWajib,
                    $member->simpananSukarela,
                    $total
                ]);
            }
            
            fclose($handle);
        }, 'rekap_keseluruhan_simpanan.csv');
    }

    public function exportMonthlyCsv()
    {
        $currentYear = $this->selectedYear;
        
        $monthlySimpanan = SimpananTransaction::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('SUM(CASE WHEN type = "POKOK" AND transactionType IN ("SETOR", "TRANSFER_IN") THEN amount ELSE 0 END) as total_pokok'),
            DB::raw('SUM(CASE WHEN type = "WAJIB" AND transactionType IN ("SETOR", "TRANSFER_IN") THEN amount ELSE 0 END) as total_wajib'),
            DB::raw('SUM(CASE WHEN type = "SUKARELA" AND transactionType IN ("SETOR", "TRANSFER_IN") THEN amount ELSE 0 END) as total_sukarela'),
            DB::raw('SUM(CASE WHEN transactionType IN ("SETOR", "TRANSFER_IN") THEN amount ELSE 0 END) as total_setor'),
            DB::raw('SUM(CASE WHEN transactionType IN ("TARIK", "TRANSFER_OUT") THEN amount ELSE 0 END) as total_tarik')
        )
        ->where('status', 'APPROVED')
        ->whereYear('created_at', $currentYear)
        ->groupBy('month')
        ->get()
        ->keyBy('month');

        $monthlyPinjaman = Loan::select(
            DB::raw('MONTH(startDate) as month'),
            DB::raw('SUM(amount) as total_pinjaman')
        )
        ->whereIn('status', ['ACTIVE', 'COMPLETED', 'OVERDUE'])
        ->whereYear('startDate', $currentYear)
        ->groupBy('month')
        ->get()
        ->keyBy('month');

        return response()->streamDownload(function () use ($monthlySimpanan, $monthlyPinjaman, $currentYear) {
            $handle = fopen('php://output', 'w');
            
            fputcsv($handle, ['Laporan Bulanan Simpan Pinjam Tahun ' . $currentYear]);
            fputcsv($handle, []);
            fputcsv($handle, [
                'Bulan',
                'Simpanan Pokok',
                'Simpanan Wajib',
                'Simpanan Sukarela',
                'Total Setoran Simpanan',
                'Penarikan Simpanan',
                'Penyaluran Pinjaman',
                'Keterangan Kepengurusan'
            ]);
            
            $months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            $totalPokok = 0;
            $totalWajib = 0;
            $totalSukarela = 0;
            $totalSetor = 0;
            $totalTarik = 0;
            $totalPinjam = 0;

            for ($i = 1; $i <= 12; $i++) {
                $pokok = $monthlySimpanan->get($i)->total_pokok ?? 0;
                $wajib = $monthlySimpanan->get($i)->total_wajib ?? 0;
                $sukarela = $monthlySimpanan->get($i)->total_sukarela ?? 0;
                $setor = $monthlySimpanan->get($i)->total_setor ?? 0;
                $tarik = $monthlySimpanan->get($i)->total_tarik ?? 0;
                $pinjam = $monthlyPinjaman->get($i)->total_pinjaman ?? 0;
                $ket = ($currentYear == 2025 && $i >= 5) ? 'Kepengurusan Baru' : '-';

                $totalPokok += $pokok;
                $totalWajib += $wajib;
                $totalSukarela += $sukarela;
                $totalSetor += $setor;
                $totalTarik += $tarik;
                $totalPinjam += $pinjam;

                fputcsv($handle, [
                    $months[$i - 1],
                    $pokok,
                    $wajib,
                    $sukarela,
                    $setor,
                    $tarik,
                    $pinjam,
                    $ket
                ]);
            }
            
            fputcsv($handle, [
                'TOTAL',
                $totalPokok,
                $totalWajib,
                $totalSukarela,
                $totalSetor,
                $totalTarik,
                $totalPinjam,
                ''
            ]);
            
            fclose($handle);
        }, 'rekap_bulanan_tahun_' . $currentYear . '.csv');
    }

    public function render()
    {
        // 1. Evaluasi Simpanan (Anggota Aktif)
        $simpananPokok = Member::where('status', 'ACTIVE')->sum('simpananPokok');
        $simpananWajib = Member::where('status', 'ACTIVE')->sum('simpananWajib');
        $simpananSukarela = Member::where('status', 'ACTIVE')->sum('simpananSukarela');
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
        $simwaDeductionMembers = Member::where('simwa_payment_method', 'SALARY_DEDUCTION')->where('status', 'ACTIVE')->count();
        $simwaDeductionEst = $simwaDeductionMembers * 50000;
        
        $sukarelaDeductionEst = Member::where('sukarela_payment_method', 'SALARY_DEDUCTION')->where('status', 'ACTIVE')->sum('monthly_sukarela_amount');

        // 4. Data Bulanan (Sesuai Tahun yg dipilih)
        $currentYear = $this->selectedYear;
        
        $monthlySimpanan = SimpananTransaction::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('SUM(CASE WHEN type = "POKOK" AND transactionType IN ("SETOR", "TRANSFER_IN") THEN amount ELSE 0 END) as total_pokok'),
            DB::raw('SUM(CASE WHEN type = "WAJIB" AND transactionType IN ("SETOR", "TRANSFER_IN") THEN amount ELSE 0 END) as total_wajib'),
            DB::raw('SUM(CASE WHEN type = "SUKARELA" AND transactionType IN ("SETOR", "TRANSFER_IN") THEN amount ELSE 0 END) as total_sukarela'),
            DB::raw('SUM(CASE WHEN transactionType IN ("SETOR", "TRANSFER_IN") THEN amount ELSE 0 END) as total_setor'),
            DB::raw('SUM(CASE WHEN transactionType IN ("TARIK", "TRANSFER_OUT") THEN amount ELSE 0 END) as total_tarik')
        )
        ->where('status', 'APPROVED')
        ->whereYear('created_at', $currentYear)
        ->groupBy('month')
        ->get()
        ->keyBy('month');

        $monthlyPinjaman = Loan::select(
            DB::raw('MONTH(startDate) as month'),
            DB::raw('SUM(amount) as total_pinjaman')
        )
        ->whereIn('status', ['ACTIVE', 'COMPLETED', 'OVERDUE'])
        ->whereYear('startDate', $currentYear)
        ->groupBy('month')
        ->get()
        ->keyBy('month');

        $monthlyData = [];
        $months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        for ($i = 1; $i <= 12; $i++) {
            $monthlyData[] = [
                'month' => $i,
                'month_name' => $months[$i - 1],
                'pokok' => $monthlySimpanan->get($i)->total_pokok ?? 0,
                'wajib' => $monthlySimpanan->get($i)->total_wajib ?? 0,
                'sukarela' => $monthlySimpanan->get($i)->total_sukarela ?? 0,
                'setoran' => $monthlySimpanan->get($i)->total_setor ?? 0,
                'penarikan' => $monthlySimpanan->get($i)->total_tarik ?? 0,
                'pinjaman' => $monthlyPinjaman->get($i)->total_pinjaman ?? 0,
                'is_kepengurusan_baru' => ($currentYear == 2025 && $i >= 5),
            ];
        }

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
            ],
            'monthlyData' => $monthlyData,
            'currentYear' => $currentYear
        ])->layout('layouts.admin');
    }
}
