<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Member;
use App\Models\Loan;
use App\Models\LoanPayment;
use App\Models\SimpananTransaction;
use App\Models\FinancialReportSnapshot;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MonthlyFinancialReport extends Component
{
    public $selectedMonth;
    public $selectedYear;
    public $reportData;
    public $showPreview = false;
    public $isExecuted = false;
    public $isSnapshot = false;

    public function mount()
    {
        $this->selectedMonth = now()->format('m');
        $this->selectedYear = now()->format('Y');
    }

    public function updatedSelectedMonth()
    {
        $this->resetState();
    }

    public function updatedSelectedYear()
    {
        $this->resetState();
    }

    private function resetState()
    {
        $this->showPreview = false;
        $this->isExecuted = false;
        $this->isSnapshot = false;
        $this->reportData = null;
    }

    public function generateReport()
    {
        // 1. Check for Snapshot (Archive)
        $snapshot = FinancialReportSnapshot::where('month', $this->selectedMonth)
            ->where('year', $this->selectedYear)
            ->first();

        if ($snapshot) {
            $this->reportData = $snapshot->data;
            $this->isSnapshot = true;
            $this->isExecuted = true; // Technically if snapshot exists, it was executed
        } else {
            // 2. No Snapshot -> Live Calculation
            $this->reportData = $this->collectReportData();
            $this->isSnapshot = false;
            $this->checkIfExecuted(); // Check old method (transaction based)
        }

        $this->showPreview = true;
    }

    private function checkIfExecuted()
    {
        $billingMonth = $this->selectedYear . '-' . str_pad($this->selectedMonth, 2, '0', STR_PAD_LEFT);

        $this->isExecuted = SimpananTransaction::where('billingMonth', $billingMonth)
            ->where('notes', 'like', '%Payroll%')
            ->exists();
    }

    public function executePayroll()
    {
        if ($this->isExecuted) {
            session()->flash('error', 'Potongan gaji untuk bulan ini sudah pernah dieksekusi.');
            return;
        }

        $data = $this->collectReportData();
        $billingMonth = $this->selectedYear . '-' . str_pad($this->selectedMonth, 2, '0', STR_PAD_LEFT);
        $monthName = Carbon::createFromFormat('m', $this->selectedMonth)->locale('id')->translatedFormat('F Y');

        // Set Transaction Date: 29th of the selected month (or last day if < 29) - 09:00 AM
        $lastDay = Carbon::createFromDate($this->selectedYear, $this->selectedMonth, 1)->endOfMonth()->day;
        $day = min(29, $lastDay);
        $transactionDate = Carbon::create($this->selectedYear, $this->selectedMonth, $day, 9, 0, 0);

        DB::beginTransaction();
        try {
            foreach ($data['items'] as $item) {
                $member = Member::find($item['member_id']);
                if (!$member)
                    continue;

                // 1. Simwa Koperasi
                if ($item['simwa'] > 0) {
                    $newBalance = ($member->simpananWajib ?? 0) + $item['simwa'];
                    SimpananTransaction::create([
                        'memberId' => $member->id,
                        'type' => 'WAJIB',
                        'transactionType' => 'SETOR',
                        'amount' => $item['simwa'],
                        'balanceAfter' => $newBalance,
                        'notes' => "Setoran Payroll (Simwa Koperasi) - $monthName",
                        'billingMonth' => $billingMonth,
                        'status' => 'APPROVED',
                        'processedBy' => auth()->id(),
                        'approvedBy' => auth()->id(),
                        'approvedAt' => $transactionDate,
                        'created_at' => $transactionDate,
                        'updated_at' => $transactionDate,
                    ]);
                    $member->increment('simpananWajib', $item['simwa']);
                }

                // 2. Sukarela
                if ($item['sukarela'] > 0) {
                    $newBalance = ($member->simpananSukarela ?? 0) + $item['sukarela'];
                    SimpananTransaction::create([
                        'memberId' => $member->id,
                        'type' => 'SUKARELA',
                        'transactionType' => 'SETOR',
                        'amount' => $item['sukarela'],
                        'balanceAfter' => $newBalance,
                        'notes' => "Setoran Payroll (Sukarela) - $monthName",
                        'billingMonth' => $billingMonth,
                        'status' => 'APPROVED',
                        'processedBy' => auth()->id(),
                        'approvedBy' => auth()->id(),
                        'approvedAt' => $transactionDate,
                        'created_at' => $transactionDate,
                        'updated_at' => $transactionDate,
                    ]);
                    $member->increment('simpananSukarela', $item['sukarela']);
                }

                // 3. Loans
                foreach ($item['loan_details'] as $l) {
                    $loan = Loan::find($l['loan_id']);
                    if (!$loan)
                        continue;

                    $loan->addPayment(
                        $l['installment'],
                        "Potongan Payroll $monthName (Angsuran ke-{$l['ke']})",
                        $transactionDate
                    );
                    $loan->increment('paid_installments');
                }
            }
            DB::commit();

            // Create Snapshot (Archive)
            try {
                FinancialReportSnapshot::updateOrCreate(
                    [
                        'month' => (int) $this->selectedMonth,
                        'year' => (int) $this->selectedYear
                    ],
                    [
                        'data' => $data,
                        'status' => 'EXECUTED',
                        'executed_by' => auth()->id()
                    ]
                );
            } catch (\Exception $e) {
                // Sillent fail or log? Snapshot failure shouldn't rollback financial transaction, 
                // but it's important. Let's log it.
                \Log::error('Failed to create payroll snapshot: ' . $e->getMessage());
            }

            $this->isExecuted = true;
            $this->isSnapshot = true; // Auto switch to snapshot view
            $this->reportData = $data; // Update local data to match snapshot just in case

            session()->flash('success', "✅ Berhasil membukukan potongan gaji bulan $monthName. Seluruh data simpanan dan pinjaman anggota telah diperbarui & diarsipkan.");
            // No need to regenerate report, we have the data
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal memproses pembukuan: ' . $e->getMessage());
        }
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
            $angsuranBermadani = 0;
            $angsuranKeBermadani = 0;
            $tenorBermadani = 0;
            $angsuranBmtItqan1 = 0;
            $simwaBmtItqan1 = 0;
            $angsuranKeBmtItqan1 = 0;
            $tenorBmtItqan1 = 0;
            $angsuranBmtItqan2 = 0;
            $simwaBmtItqan2 = 0;
            $angsuranKeBmtItqan2 = 0;
            $tenorBmtItqan2 = 0;
            $bmtItqanCount = 0;
            $loanDetails = [];

            foreach ($member->loans as $loan) {
                $monthlyPayment = (float) ($loan->monthlyPayment ?? 0);
                $simwaBmtAmount = (float) ($loan->simwa_amount ?? 0);
                $pureInstallment = max(0, $monthlyPayment - $simwaBmtAmount);
                $angsuranKe = ($loan->paid_installments ?? 0) + 1;

                $loanDetails[] = [
                    'loan_id' => $loan->id,
                    'installment' => $pureInstallment,
                    'simwa_bmt' => $simwaBmtAmount,
                    'ke' => $angsuranKe
                ];

                if ($loan->loanSource === 'BMT_ITQAN') {
                    $bmtItqanCount++;
                    if ($bmtItqanCount == 1) {
                        $angsuranBmtItqan1 = $pureInstallment;
                        $simwaBmtItqan1 = $simwaBmtAmount;
                        $angsuranKeBmtItqan1 = $angsuranKe;
                        $tenorBmtItqan1 = $loan->tenor;
                    } else {
                        $angsuranBmtItqan2 = $pureInstallment;
                        $simwaBmtItqan2 = $simwaBmtAmount;
                        $angsuranKeBmtItqan2 = $angsuranKe;
                        $tenorBmtItqan2 = $loan->tenor;
                    }
                } else {
                    $angsuranBermadani = $monthlyPayment;
                    $angsuranKeBermadani = $angsuranKe;
                    $tenorBermadani = $loan->tenor;
                }
            }

            $simwaAmount = ($member->isMemberKoperasi && $member->hasSalaryDeductionSimwa()) ? ($member->monthly_simpanan_wajib ?? 50000) : 0;
            $sukarelaAmount = $member->hasSalaryDeductionSukarela() ? ($member->monthly_sukarela_amount ?? 0) : 0;
            $total = $angsuranBermadani + $angsuranBmtItqan1 + $simwaBmtItqan1 + $angsuranBmtItqan2 + $simwaBmtItqan2 + $simwaAmount + $sukarelaAmount;

            $reportItems[] = [
                'member_id' => $member->id,
                'nama' => $member->name,
                'unit_kerja' => $member->unitKerja ?? '-',
                'simwa' => $simwaAmount,
                'sukarela' => $sukarelaAmount,
                'angsuran_bermadani' => $angsuranBermadani,
                'angsuran_ke_bermadani' => $angsuranKeBermadani,
                'tenor_bermadani' => $tenorBermadani,
                'angsuran_bmt_itqan_1' => $angsuranBmtItqan1,
                'simwa_bmt_itqan_1' => $simwaBmtItqan1,
                'angsuran_ke_bmt_itqan_1' => $angsuranKeBmtItqan1,
                'tenor_bmt_itqan_1' => $tenorBmtItqan1,
                'angsuran_bmt_itqan_2' => $angsuranBmtItqan2,
                'simwa_bmt_itqan_2' => $simwaBmtItqan2,
                'angsuran_ke_bmt_itqan_2' => $angsuranKeBmtItqan2,
                'tenor_bmt_itqan_2' => $tenorBmtItqan2,
                'total' => $total,
                'has_loan' => true,
                'loan_details' => $loanDetails,
            ];

            $totalAngsuranBermadani += $angsuranBermadani;
            $totalAngsuranBmtItqan1 += ($angsuranBmtItqan1 + $simwaBmtItqan1);
            $totalAngsuranBmtItqan2 += ($angsuranBmtItqan2 + $simwaBmtItqan2);
            $totalSimwa += $simwaAmount;
            $totalSukarela += $sukarelaAmount;
            $processedMemberIds[] = $member->id;
        }

        $membersWithSalaryDeduction = Member::where('status', 'ACTIVE')
            ->whereNotIn('id', $processedMemberIds ?: [0])
            ->where(function ($query) {
                $query->where('simwa_payment_method', 'SALARY_DEDUCTION')
                    ->orWhere(function ($q) {
                        $q->where('sukarela_payment_method', 'SALARY_DEDUCTION')->where('monthly_sukarela_amount', '>', 0);
                    });
            })->get();

        foreach ($membersWithSalaryDeduction as $member) {
            $simwaAmount = ($member->isMemberKoperasi && $member->hasSalaryDeductionSimwa()) ? ($member->monthly_simpanan_wajib ?? 50000) : 0;
            $sukarelaAmount = $member->hasSalaryDeductionSukarela() ? ($member->monthly_sukarela_amount ?? 0) : 0;
            if ($simwaAmount == 0 && $sukarelaAmount == 0)
                continue;

            $reportItems[] = [
                'member_id' => $member->id,
                'nama' => $member->name,
                'unit_kerja' => $member->unitKerja ?? '-',
                'simwa' => $simwaAmount,
                'sukarela' => $sukarelaAmount,
                'angsuran_bermadani' => 0,
                'angsuran_ke_bermadani' => 0,
                'tenor_bermadani' => 0,
                'angsuran_bmt_itqan_1' => 0,
                'simwa_bmt_itqan_1' => 0,
                'angsuran_ke_bmt_itqan_1' => 0,
                'tenor_bmt_itqan_1' => 0,
                'angsuran_bmt_itqan_2' => 0,
                'simwa_bmt_itqan_2' => 0,
                'angsuran_ke_bmt_itqan_2' => 0,
                'tenor_bmt_itqan_2' => 0,
                'total' => $simwaAmount + $sukarelaAmount,
                'has_loan' => false,
                'loan_details' => [],
            ];
            $totalSimwa += $simwaAmount;
            $totalSukarela += $sukarelaAmount;
        }

        // Sort: members with loans first, then by name alphabetically
        usort($reportItems, function ($a, $b) {
            // First priority: has_loan (true first)
            if ($a['has_loan'] !== $b['has_loan']) {
                return $a['has_loan'] ? -1 : 1;
            }
            // Second priority: alphabetical by name
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
