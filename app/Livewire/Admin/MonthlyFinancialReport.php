<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Member;
use App\Models\Loan;
use App\Models\LoanPayment;
use App\Models\SimpananTransaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class MonthlyFinancialReport extends Component
{
    public $selectedMonth;
    public $selectedYear;
    public $reportData;
    public $showPreview = false;

    public function mount()
    {
        $this->selectedMonth = now()->format('m');
        $this->selectedYear = now()->format('Y');
    }

    public function generateReport()
    {
        $this->reportData = $this->collectReportData();
        $this->showPreview = true;
    }

    public function downloadPDF()
    {
        $data = $this->collectReportData();

        $pdf = Pdf::loadView('admin.reports.monthly-financial-pdf', [
            'data' => $data,
            'month' => $this->selectedMonth,
            'year' => $this->selectedYear,
            'monthName' => Carbon::createFromFormat('m', $this->selectedMonth)->locale('id')->translatedFormat('F'),
            'generatedAt' => now()->locale('id')->translatedFormat('d F Y H:i')
        ])->setPaper('a4', 'landscape'); // Set Landscape

        $fileName = "Laporan_Keuangan_Bulanan_{$this->selectedYear}_{$this->selectedMonth}.pdf";

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $fileName);
    }

    /**
     * Download simplified PDF for Campus Finance Unit
     * Only contains: No, Name, Unit Kerja, Total Amount
     */
    public function downloadSimplePDF()
    {
        $data = $this->collectReportData();

        $pdf = Pdf::loadView('admin.reports.payroll-simple-pdf', [
            'data' => $data,
            'month' => $this->selectedMonth,
            'year' => $this->selectedYear,
            'monthName' => Carbon::createFromFormat('m', $this->selectedMonth)->locale('id')->translatedFormat('F'),
            'generatedAt' => now()->locale('id')->translatedFormat('d F Y H:i')
        ])->setPaper('a4', 'portrait');

        $fileName = "Potongan_Gaji_Koperasi_{$this->selectedYear}_{$this->selectedMonth}.pdf";

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $fileName);
    }

    private function collectReportData()
    {
        // Format billingMonth sebagai YYYY-MM
        $billingMonth = $this->selectedYear . '-' . str_pad($this->selectedMonth, 2, '0', STR_PAD_LEFT);

        $startDate = Carbon::createFromDate($this->selectedYear, $this->selectedMonth, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        // Status yang dianggap valid untuk laporan
        $validStatuses = ['APPROVED', 'PENDING'];

        // Format data untuk laporan
        $reportItems = [];
        $totalAngsuranBermadani = 0;
        $totalAngsuranBmtItqan1 = 0;
        $totalAngsuranBmtItqan2 = 0;
        $totalSimwa = 0;
        $totalSukarela = 0;
        $processedMemberIds = [];

        // 1. Ambil semua member AKTIF dengan pinjaman aktif (angsuran selalu potong gaji)
        $membersWithLoans = Member::where('status', 'ACTIVE') // Exclude frozen/suspended members
            ->whereHas('loans', function ($query) use ($endDate) {
                $query->where('status', 'ACTIVE')
                    ->where('startDate', '<=', $endDate);
            })
            ->with([
                'loans' => function ($query) use ($endDate) {
                    $query->where('status', 'ACTIVE')
                        ->where('startDate', '<=', $endDate)
                        ->orderBy('startDate', 'asc'); // Order by start date untuk BMT ITQAN 1 & 2
                }
            ])
            ->get();

        // Process members with loans (angsuran) - group by member
        foreach ($membersWithLoans as $member) {
            // Bermadani loan data
            $angsuranBermadani = 0;
            $angsuranKeBermadani = 0;
            $tenorBermadani = 0;

            // BMT ITQAN 1 loan data
            $angsuranBmtItqan1 = 0;
            $simwaBmtItqan1 = 0; // Init
            $angsuranKeBmtItqan1 = 0;
            $tenorBmtItqan1 = 0;

            // BMT ITQAN 2 loan data
            $angsuranBmtItqan2 = 0;
            $simwaBmtItqan2 = 0; // Init
            $angsuranKeBmtItqan2 = 0;
            $tenorBmtItqan2 = 0;

            $bmtItqanCount = 0;

            foreach ($member->loans as $loan) {
                $monthlyPayment = $loan->monthlyPayment ?? 0;
                $simwaBmtAmount = $loan->simwa_amount ?? 0;
                $pureInstallment = max(0, $monthlyPayment - $simwaBmtAmount); // Angsuran murni tanpa simwa

                $tenor = $loan->tenor ?? 0;
                $paidInstallments = $loan->paid_installments ?? 0; // Fix: use snake_case column name
                $angsuranKe = $paidInstallments + 1;

                // Pisahkan berdasarkan loanSource
                if ($loan->loanSource === 'BMT_ITQAN') {
                    $bmtItqanCount++;
                    if ($bmtItqanCount == 1) {
                        // BMT ITQAN 1
                        $angsuranBmtItqan1 = $pureInstallment;
                        $simwaBmtItqan1 = $simwaBmtAmount; // Store Simwa Separately
                        $angsuranKeBmtItqan1 = $angsuranKe;
                        $tenorBmtItqan1 = $tenor;
                    } else {
                        // BMT ITQAN 2
                        $angsuranBmtItqan2 = $pureInstallment;
                        $simwaBmtItqan2 = $simwaBmtAmount;
                        $angsuranKeBmtItqan2 = $angsuranKe;
                        $tenorBmtItqan2 = $tenor;
                    }
                } else {
                    // BERMADANI
                    $angsuranBermadani = $monthlyPayment; // Bermadani no separate simwa in loan, it's global 50k
                    $angsuranKeBermadani = $angsuranKe;
                    $tenorBermadani = $tenor;
                }
            }

            // SIMWA: cek preferensi pembayaran member (HANYA KOPERASI)
            $simwaAmount = 0;
            if ($member->isMemberKoperasi && $member->hasSalaryDeductionSimwa()) {
                $simwaAmount = $member->monthly_simpanan_wajib ?? 50000;
            }

            // Sukarela: cek preferensi pembayaran member
            $sukarelaAmount = 0;
            if ($member->hasSalaryDeductionSukarela()) {
                $sukarelaAmount = $member->monthly_sukarela_amount ?? 0;
            }

            $total = $angsuranBermadani + $angsuranBmtItqan1 + $simwaBmtItqan1 + $angsuranBmtItqan2 + $simwaBmtItqan2 + $simwaAmount + $sukarelaAmount;

            $reportItems[] = [
                'nama' => $member->name,
                'unit_kerja' => (
                    $member->unitKerja &&
                    !in_array(strtolower($member->unitKerja), ['unknown', 'null', '-'])
                ) ? $member->unitKerja : '-',
                'simwa' => $simwaAmount,
                'sukarela' => $sukarelaAmount,
                'angsuran_bermadani' => $angsuranBermadani,
                'angsuran_ke_bermadani' => $angsuranKeBermadani,
                'tenor_bermadani' => $tenorBermadani,
                'angsuran_bmt_itqan_1' => $angsuranBmtItqan1,
                'simwa_bmt_itqan_1' => $simwaBmtItqan1, // Add to array
                'angsuran_ke_bmt_itqan_1' => $angsuranKeBmtItqan1,
                'tenor_bmt_itqan_1' => $tenorBmtItqan1,
                'angsuran_bmt_itqan_2' => $angsuranBmtItqan2,
                'simwa_bmt_itqan_2' => $simwaBmtItqan2, // Add to array
                'angsuran_ke_bmt_itqan_2' => $angsuranKeBmtItqan2,
                'tenor_bmt_itqan_2' => $tenorBmtItqan2,
                'total' => $total,
                'has_loan' => true,
                'simwa_method' => $member->simwa_payment_method ?? 'SALARY_DEDUCTION',
                'sukarela_method' => $member->sukarela_payment_method ?? 'MANUAL',
            ];

            $totalAngsuranBermadani += $angsuranBermadani;
            // Include Simwa BMT in the total accumulation for BMT Itqan columns
            $totalAngsuranBmtItqan1 += ($angsuranBmtItqan1 + $simwaBmtItqan1);
            $totalAngsuranBmtItqan2 += ($angsuranBmtItqan2 + $simwaBmtItqan2);

            $totalSimwa += $simwaAmount;
            $totalSukarela += $sukarelaAmount;
            $processedMemberIds[] = $member->id;
        }

        // 2. Ambil semua member koperasi AKTIF yang SIMWA-nya potong gaji (tanpa pinjaman)
        $membersWithSalaryDeduction = Member::where('status', 'ACTIVE') // Exclude frozen/suspended members
            // ->where('isMemberKoperasi', true) // REMOVED: Include all active members regardless of flexible boolean
            ->whereNotIn('id', $processedMemberIds ?: [0])
            ->where(function ($query) {
                // Member yang SIMWA atau Sukarela-nya potong gaji
                $query->where('simwa_payment_method', 'SALARY_DEDUCTION')
                    ->orWhere(function ($q) {
                    $q->where('sukarela_payment_method', 'SALARY_DEDUCTION')
                        ->where('monthly_sukarela_amount', '>', 0);
                });
            })
            ->get();

        foreach ($membersWithSalaryDeduction as $member) {
            // SIMWA: cek preferensi pembayaran (HANYA KOPERASI)
            $simwaAmount = 0;
            if ($member->isMemberKoperasi && $member->hasSalaryDeductionSimwa()) {
                $simwaAmount = $member->monthly_simpanan_wajib ?? 50000;
            }

            // Sukarela: cek preferensi pembayaran
            $sukarelaAmount = 0;
            if ($member->hasSalaryDeductionSukarela()) {
                $sukarelaAmount = $member->monthly_sukarela_amount ?? 0;
            }

            // Skip jika tidak ada yang perlu dipotong
            if ($simwaAmount == 0 && $sukarelaAmount == 0) {
                continue;
            }

            $reportItems[] = [
                'nama' => $member->name,
                'unit_kerja' => (
                    $member->unitKerja &&
                    !in_array(strtolower($member->unitKerja), ['unknown', 'null', '-'])
                ) ? $member->unitKerja : '-',
                'simwa' => $simwaAmount,
                'sukarela' => $sukarelaAmount,
                'angsuran_bermadani' => 0,
                'angsuran_ke_bermadani' => 0,
                'tenor_bermadani' => 0,
                'angsuran_bmt_itqan_1' => 0,
                'simwa_bmt_itqan_1' => 0, // Fix key
                'angsuran_ke_bmt_itqan_1' => 0,
                'tenor_bmt_itqan_1' => 0,
                'angsuran_bmt_itqan_2' => 0,
                'simwa_bmt_itqan_2' => 0, // Fix key
                'angsuran_ke_bmt_itqan_2' => 0,
                'tenor_bmt_itqan_2' => 0,
                'total' => $simwaAmount + $sukarelaAmount,
                'has_loan' => false,
                'simwa_method' => $member->simwa_payment_method ?? 'SALARY_DEDUCTION',
                'sukarela_method' => $member->sukarela_payment_method ?? 'MANUAL',
            ];

            $totalSimwa += $simwaAmount;
            $totalSukarela += $sukarelaAmount;
        }

        // Sort by name
        usort($reportItems, function ($a, $b) {
            return strcmp($a['nama'], $b['nama']);
        });

        return [
            'items' => $reportItems,
            'summary' => [
                'total_simwa' => $totalSimwa,
                'total_sukarela' => $totalSukarela,
                'total_angsuran_bermadani' => $totalAngsuranBermadani,
                'total_angsuran_bmt_itqan_1' => $totalAngsuranBmtItqan1,
                'total_angsuran_bmt_itqan_2' => $totalAngsuranBmtItqan2,
                'grand_total' => $totalAngsuranBermadani + $totalAngsuranBmtItqan1 + $totalAngsuranBmtItqan2 + $totalSimwa + $totalSukarela,
                'total_members' => count($reportItems)
            ]
        ];
    }

    public function render()
    {
        return view('livewire.admin.monthly-financial-report')->layout('layouts.admin');
    }
}
