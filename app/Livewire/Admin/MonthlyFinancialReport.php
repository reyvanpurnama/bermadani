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
        $startDate = Carbon::createFromDate($this->selectedYear, $this->selectedMonth, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        // 1. Ambil semua member aktif dengan pinjaman aktif
        $membersWithLoans = Member::whereHas('loans', function ($query) use ($startDate, $endDate) {
            $query->where('status', 'ACTIVE')
                  ->where('startDate', '<=', $endDate);
        })
        ->with(['loans' => function ($query) use ($startDate, $endDate) {
            $query->where('status', 'ACTIVE')
                  ->where('startDate', '<=', $endDate)
                  ->with(['payments' => function ($q) use ($startDate, $endDate) {
                      $q->whereBetween('paymentDate', [$startDate, $endDate]);
                  }]);
        }])
        ->get();

        // 2. Ambil semua member yang bayar SIMWA di bulan ini
        $membersWithSimwa = Member::where('isMemberKoperasi', true)
            ->whereHas('simpananTransactions', function ($query) use ($startDate, $endDate) {
                $query->where('type', 'WAJIB')
                      ->where('transactionType', 'SETOR')
                      ->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->with(['simpananTransactions' => function ($query) use ($startDate, $endDate) {
                $query->where('type', 'WAJIB')
                      ->where('transactionType', 'SETOR')
                      ->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->get();

        // 3. Format data untuk laporan
        $reportItems = [];
        $totalAngsuran = 0;
        $totalSimwa = 0;
        $totalSukarela = 0;

        // Process members with loans (angsuran)
        foreach ($membersWithLoans as $member) {
            foreach ($member->loans as $loan) {
                $monthlyPayment = $loan->monthlyPayment;
                $simwaAmount = 50000; // Default SIMWA
                $sukarelaAmount = 0;

                // Check if member has sukarela deduction this month
                $sukarelaTransaction = SimpananTransaction::where('memberId', $member->id)
                    ->where('type', 'SUKARELA')
                    ->where('transactionType', 'SETOR')
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->first();

                if ($sukarelaTransaction) {
                    $sukarelaAmount = $sukarelaTransaction->amount;
                }

                $reportItems[] = [
                    'nama' => $member->name,
                    'angsuran' => $monthlyPayment,
                    'simwa' => $simwaAmount,
                    'sukarela' => $sukarelaAmount,
                    'total' => $monthlyPayment + $simwaAmount + $sukarelaAmount,
                    'tenor_remaining' => $loan->tenor - $loan->payments->count(),
                    'has_loan' => true
                ];

                $totalAngsuran += $monthlyPayment;
                $totalSimwa += $simwaAmount;
                $totalSukarela += $sukarelaAmount;
            }
        }

        // Process members with SIMWA only (no loan)
        $memberIdsWithLoans = $membersWithLoans->pluck('id')->toArray();
        
        foreach ($membersWithSimwa as $member) {
            if (!in_array($member->id, $memberIdsWithLoans)) {
                $simwaAmount = $member->simpananTransactions->sum('amount');
                $sukarelaAmount = 0;

                // Check sukarela
                $sukarelaTransaction = SimpananTransaction::where('memberId', $member->id)
                    ->where('type', 'SUKARELA')
                    ->where('transactionType', 'SETOR')
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->first();

                if ($sukarelaTransaction) {
                    $sukarelaAmount = $sukarelaTransaction->amount;
                }

                $reportItems[] = [
                    'nama' => $member->name,
                    'angsuran' => 0,
                    'simwa' => $simwaAmount,
                    'sukarela' => $sukarelaAmount,
                    'total' => $simwaAmount + $sukarelaAmount,
                    'tenor_remaining' => 0,
                    'has_loan' => false
                ];

                $totalSimwa += $simwaAmount;
                $totalSukarela += $sukarelaAmount;
            }
        }

        // Sort by name
        usort($reportItems, function ($a, $b) {
            return strcmp($a['nama'], $b['nama']);
        });

        return [
            'items' => $reportItems,
            'summary' => [
                'total_angsuran' => $totalAngsuran,
                'total_simwa' => $totalSimwa,
                'total_sukarela' => $totalSukarela,
                'grand_total' => $totalAngsuran + $totalSimwa + $totalSukarela,
                'total_members' => count($reportItems)
            ]
        ];
    }

    public function render()
    {
        return view('livewire.admin.monthly-financial-report')->layout('layouts.admin');
    }
}
