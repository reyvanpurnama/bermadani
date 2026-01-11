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
        $totalAngsuran = 0;
        $totalSimwa = 0;
        $totalSukarela = 0;
        $processedMemberIds = [];

        // 1. Ambil semua member dengan pinjaman aktif
        $membersWithLoans = Member::whereHas('loans', function ($query) use ($endDate) {
            $query->where('status', 'ACTIVE')
                  ->where('startDate', '<=', $endDate);
        })
        ->with(['loans' => function ($query) use ($endDate) {
            $query->where('status', 'ACTIVE')
                  ->where('startDate', '<=', $endDate);
        }])
        ->get();

        // Process members with loans (angsuran)
        foreach ($membersWithLoans as $member) {
            foreach ($member->loans as $loan) {
                $monthlyPayment = $loan->monthlyPayment ?? 0;
                
                // Get SIMWA untuk bulan ini
                $simwaTransaction = SimpananTransaction::where('memberId', $member->id)
                    ->where('type', 'WAJIB')
                    ->where('billingMonth', $billingMonth)
                    ->whereIn('status', $validStatuses)
                    ->first();
                $simwaAmount = $simwaTransaction ? $simwaTransaction->amount : 50000; // Default 50k jika tidak ada

                // Get Sukarela untuk bulan ini
                $sukarelaTransaction = SimpananTransaction::where('memberId', $member->id)
                    ->where('type', 'SUKARELA')
                    ->where('transactionType', 'SETOR')
                    ->where('billingMonth', $billingMonth)
                    ->whereIn('status', $validStatuses)
                    ->first();
                $sukarelaAmount = $sukarelaTransaction ? $sukarelaTransaction->amount : 0;

                $reportItems[] = [
                    'nama' => $member->name,
                    'angsuran' => $monthlyPayment,
                    'simwa' => $simwaAmount,
                    'sukarela' => $sukarelaAmount,
                    'total' => $monthlyPayment + $simwaAmount + $sukarelaAmount,
                    'tenor_remaining' => ($loan->tenor ?? 0) - ($loan->paidInstallments ?? 0),
                    'has_loan' => true
                ];

                $totalAngsuran += $monthlyPayment;
                $totalSimwa += $simwaAmount;
                $totalSukarela += $sukarelaAmount;
                $processedMemberIds[] = $member->id;
            }
        }

        // 2. Ambil semua member yang bayar SIMWA di bulan ini (tanpa pinjaman)
        $membersWithSimwa = Member::where('isMemberKoperasi', true)
            ->whereNotIn('id', $processedMemberIds ?: [0])
            ->whereHas('simpananTransactions', function ($query) use ($billingMonth, $validStatuses) {
                $query->where('type', 'WAJIB')
                      ->where('billingMonth', $billingMonth)
                      ->whereIn('status', $validStatuses);
            })
            ->with(['simpananTransactions' => function ($query) use ($billingMonth, $validStatuses) {
                $query->where('billingMonth', $billingMonth)
                      ->whereIn('status', $validStatuses);
            }])
            ->get();

        foreach ($membersWithSimwa as $member) {
            // SIMWA amount
            $simwaAmount = $member->simpananTransactions
                ->where('type', 'WAJIB')
                ->sum('amount');

            // Sukarela amount
            $sukarelaAmount = $member->simpananTransactions
                ->where('type', 'SUKARELA')
                ->where('transactionType', 'SETOR')
                ->sum('amount');

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
