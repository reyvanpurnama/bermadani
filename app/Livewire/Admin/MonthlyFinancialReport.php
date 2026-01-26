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
        ]);

        $fileName = "Laporan_Keuangan_Bulanan_{$this->selectedYear}_{$this->selectedMonth}.pdf";

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
        $totalAngsuranBmtItqan = 0;
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
                        ->where('startDate', '<=', $endDate);
                }
            ])
            ->get();

        // Process members with loans (angsuran) - group by member
        foreach ($membersWithLoans as $member) {
            $angsuranBermadani = 0;
            $angsuranBmtItqan = 0;

            foreach ($member->loans as $loan) {
                $monthlyPayment = $loan->monthlyPayment ?? 0;

                // Pisahkan berdasarkan loanSource
                if ($loan->loanSource === 'BMT_ITQAN') {
                    $angsuranBmtItqan += $monthlyPayment;
                } else {
                    // Default BERMADANI
                    $angsuranBermadani += $monthlyPayment;
                }
            }

            // SIMWA: cek preferensi pembayaran member
            $simwaAmount = 0;
            if ($member->hasSalaryDeductionSimwa()) {
                $simwaAmount = $member->monthly_simpanan_wajib ?? 50000;
            }

            // Sukarela: cek preferensi pembayaran member
            $sukarelaAmount = 0;
            if ($member->hasSalaryDeductionSukarela()) {
                $sukarelaAmount = $member->monthly_sukarela_amount ?? 0;
            }

            $reportItems[] = [
                'nama' => $member->name,
                'unit_kerja' => $member->unitKerja ?? '-',
                'angsuran_bermadani' => $angsuranBermadani,
                'angsuran_bmt_itqan' => $angsuranBmtItqan,
                'simwa' => $simwaAmount,
                'sukarela' => $sukarelaAmount,
                'total' => $angsuranBermadani + $angsuranBmtItqan + $simwaAmount + $sukarelaAmount,
                'has_loan' => true,
                'simwa_method' => $member->simwa_payment_method ?? 'SALARY_DEDUCTION',
                'sukarela_method' => $member->sukarela_payment_method ?? 'MANUAL',
            ];

            $totalAngsuranBermadani += $angsuranBermadani;
            $totalAngsuranBmtItqan += $angsuranBmtItqan;
            $totalSimwa += $simwaAmount;
            $totalSukarela += $sukarelaAmount;
            $processedMemberIds[] = $member->id;
        }

        // 2. Ambil semua member koperasi AKTIF yang SIMWA-nya potong gaji (tanpa pinjaman)
        $membersWithSalaryDeduction = Member::where('status', 'ACTIVE') // Exclude frozen/suspended members
            ->where('isMemberKoperasi', true)
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
            // SIMWA: cek preferensi pembayaran
            $simwaAmount = 0;
            if ($member->hasSalaryDeductionSimwa()) {
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
                'unit_kerja' => $member->unitKerja ?? '-',
                'angsuran_bermadani' => 0,
                'angsuran_bmt_itqan' => 0,
                'simwa' => $simwaAmount,
                'sukarela' => $sukarelaAmount,
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
                'total_angsuran_bermadani' => $totalAngsuranBermadani,
                'total_angsuran_bmt_itqan' => $totalAngsuranBmtItqan,
                'total_simwa' => $totalSimwa,
                'total_sukarela' => $totalSukarela,
                'grand_total' => $totalAngsuranBermadani + $totalAngsuranBmtItqan + $totalSimwa + $totalSukarela,
                'total_members' => count($reportItems)
            ]
        ];
    }

    public function render()
    {
        return view('livewire.admin.monthly-financial-report')->layout('layouts.admin');
    }
}
