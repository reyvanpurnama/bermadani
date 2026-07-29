<?php

namespace App\Livewire\Admin;

use App\Models\Loan;
use App\Models\LoanPayment;
use App\Models\Member;
use App\Models\RatManualEntry;
use App\Models\SimpananTransaction;
use Livewire\Component;
use Livewire\WithPagination;

class LoanAuditTool extends Component
{
    use WithPagination;

    public $activeTab = 'loans'; // 'loans', 'savings', 'cash_flow', 'rat_sync'
    public $search = '';
    public $statusFilter = 'ALL'; // 'ALL', 'ACTIVE', 'COMPLETED', 'OVERDUE'
    public $selectedYear = 2025;
    public $selectedCashFlowYear = 2026;
    public $selectedCashFlowMonth = null;

    // Modal state for loan detail payments
    public $showDetailModal = false;
    public $selectedLoan = null;
    public $selectedLoanPayments = [];

    // Modal state for member savings detail
    public $showMemberSavingsModal = false;
    public $selectedMember = null;
    public $selectedMemberTransactions = [];

    protected $queryString = ['activeTab', 'search', 'statusFilter', 'selectedYear', 'selectedCashFlowYear'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedSelectedCashFlowYear()
    {
        $this->selectedCashFlowMonth = null;
    }

    public function viewLoanDetails($loanId)
    {
        $this->selectedLoan = Loan::with(['member', 'payments' => function ($q) {
            $q->orderBy('paymentDate', 'desc');
        }])->find($loanId);

        if ($this->selectedLoan) {
            $this->selectedLoanPayments = $this->selectedLoan->payments;
            $this->showDetailModal = true;
        }
    }

    public function closeDetailModal()
    {
        $this->showDetailModal = false;
        $this->selectedLoan = null;
        $this->selectedLoanPayments = [];
    }

    public function viewMemberSavingsDetails($memberId)
    {
        $this->selectedMember = Member::with(['simpananTransactions' => function ($q) {
            $q->orderBy('created_at', 'desc')->limit(50);
        }])->find($memberId);

        if ($this->selectedMember) {
            $this->selectedMemberTransactions = $this->selectedMember->simpananTransactions;
            $this->showMemberSavingsModal = true;
        }
    }

    public function closeMemberSavingsModal()
    {
        $this->showMemberSavingsModal = false;
        $this->selectedMember = null;
        $this->selectedMemberTransactions = [];
    }

    public function selectCashFlowMonth($monthNum)
    {
        $this->selectedCashFlowMonth = (int) $monthNum;
    }

    public function buildCashFlowSummaries()
    {
        $year = (int) $this->selectedCashFlowYear;
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        // Fetch loan payments for the year grouped by month and loanSource
        $loanPaymentsByMonthAndSource = LoanPayment::query()
            ->join('loans', 'loan_payments.loanId', '=', 'loans.id')
            ->whereYear('loan_payments.paymentDate', $year)
            ->selectRaw('MONTH(loan_payments.paymentDate) as m, loans.loanSource, SUM(loan_payments.amount) as total')
            ->groupBy('m', 'loans.loanSource')
            ->get();

        $paymentsMonthSourceMap = [];
        foreach ($loanPaymentsByMonthAndSource as $row) {
            $paymentsMonthSourceMap[$row->m][$row->loanSource] = (float) $row->total;
        }

        // Fetch loan payments for the year grouped by month overall
        $loanPaymentsByMonth = LoanPayment::query()
            ->whereYear('paymentDate', $year)
            ->selectRaw('MONTH(paymentDate) as m, SUM(amount) as total')
            ->groupBy('m')
            ->pluck('total', 'm');

        // Fetch simpanan deposits (SETOR, TRANSFER_IN) grouped by month
        $simpananInflowByMonth = SimpananTransaction::query()
            ->where('status', 'APPROVED')
            ->whereIn('transactionType', ['SETOR', 'TRANSFER_IN'])
            ->whereYear('created_at', $year)
            ->selectRaw('MONTH(created_at) as m, SUM(amount) as total')
            ->groupBy('m')
            ->pluck('total', 'm');

        // Fetch simpanan withdrawals (TARIK, TRANSFER_OUT) grouped by month
        $simpananOutflowByMonth = SimpananTransaction::query()
            ->where('status', 'APPROVED')
            ->whereIn('transactionType', ['TARIK', 'TRANSFER_OUT'])
            ->whereYear('created_at', $year)
            ->selectRaw('MONTH(created_at) as m, SUM(amount) as total')
            ->groupBy('m')
            ->pluck('total', 'm');

        // Fetch all active loans up to this year for historical fallback reconstruction
        $allYearLoans = Loan::where('startDate', '<=', \Carbon\Carbon::createFromDate($year, 12, 31))->get();

        $summaries = [];
        $chartCategories = [];
        $chartAngsuran = [];
        $chartAngsuranBmt = [];
        $chartAngsuranBermadani = [];
        $chartSimpananMasuk = [];
        $chartSimpananKeluar = [];
        $chartNetCashflow = [];

        foreach ($months as $mNum => $mName) {
            $angsuran = (float) ($loanPaymentsByMonth[$mNum] ?? 0);
            $angsuranBmt = (float) ($paymentsMonthSourceMap[$mNum]['BMT_ITQAN'] ?? 0);
            $angsuranBermadani = (float) ($paymentsMonthSourceMap[$mNum]['BERMADANI'] ?? 0);

            // Reconstruct historical monthly installments if explicit LoanPayment logs don't exist for this year/month
            if ($angsuran <= 0) {
                $monthDate = \Carbon\Carbon::createFromDate($year, $mNum, 1)->endOfMonth();
                $reconBmt = 0;
                $reconBermadani = 0;

                foreach ($allYearLoans as $loan) {
                    $start = \Carbon\Carbon::parse($loan->startDate);
                    if ($start <= $monthDate) {
                        $monthsDiff = ($year - $start->year) * 12 + ($mNum - $start->month) + 1;
                        if ($monthsDiff >= 1 && $monthsDiff <= $loan->tenor) {
                            if ($loan->loanSource === 'BMT_ITQAN') {
                                $reconBmt += (float) ($loan->monthlyPayment ?? 0);
                            } else {
                                $reconBermadani += (float) ($loan->monthlyPayment ?? 0);
                            }
                        }
                    }
                }

                $angsuranBmt = $reconBmt;
                $angsuranBermadani = $reconBermadani;
                $angsuran = $reconBmt + $reconBermadani;
            }

            $percentBmt = $angsuran > 0 ? round(($angsuranBmt / $angsuran) * 100, 1) : 0;
            $percentBermadani = $angsuran > 0 ? round(($angsuranBermadani / $angsuran) * 100, 1) : 0;

            $simMasuk = (float) ($simpananInflowByMonth[$mNum] ?? 0);
            $simKeluar = (float) ($simpananOutflowByMonth[$mNum] ?? 0);

            $totalInflow = $angsuran + $simMasuk;
            $totalOutflow = $simKeluar;
            $netCashflow = $totalInflow - $totalOutflow;

            $monthKey = sprintf('%04d-%02d', $year, $mNum);

            $summaries[] = [
                'month_num' => $mNum,
                'month_key' => $monthKey,
                'month_name' => $mName,
                'angsuran' => $angsuran,
                'angsuran_bmt' => $angsuranBmt,
                'angsuran_bermadani' => $angsuranBermadani,
                'percent_bmt' => $percentBmt,
                'percent_bermadani' => $percentBermadani,
                'simpanan_masuk' => $simMasuk,
                'simpanan_keluar' => $simKeluar,
                'total_inflow' => $totalInflow,
                'total_outflow' => $totalOutflow,
                'net_cashflow' => $netCashflow,
            ];

            $chartCategories[] = substr($mName, 0, 3);
            $chartAngsuran[] = $angsuran;
            $chartAngsuranBmt[] = $angsuranBmt;
            $chartAngsuranBermadani[] = $angsuranBermadani;
            $chartSimpananMasuk[] = $simMasuk;
            $chartSimpananKeluar[] = $simKeluar;
            $chartNetCashflow[] = $netCashflow;
        }

        return [
            'summaries' => $summaries,
            'chartData' => [
                'categories' => $chartCategories,
                'angsuran' => $chartAngsuran,
                'angsuran_bmt' => $chartAngsuranBmt,
                'angsuran_bermadani' => $chartAngsuranBermadani,
                'simpanan_masuk' => $chartSimpananMasuk,
                'simpanan_keluar' => $chartSimpananKeluar,
                'net_cashflow' => $chartNetCashflow,
            ]
        ];
    }

    public function syncToRatReport()
    {
        $year = (int) $this->selectedYear;

        // Compute Total Pendapatan Jasa Pinjaman / Margin & Simpanan
        $totalLoanPayments = LoanPayment::sum('amount');
        $totalSimpananPokok = Member::sum('simpananPokok');
        $totalSimpananWajib = Member::sum('simpananWajib');
        $totalSimpananSukarela = Member::sum('simpananSukarela');

        // 1. Sync Pendapatan Jasa Pinjaman (Laba Rugi -> Pendapatan Multijasa)
        RatManualEntry::updateOrCreate(
            [
                'table_key' => 'laba_rugi',
                'row_key' => 'pendapatan_multijasa',
                'field_key' => 'nilai',
                'year' => $year,
            ],
            [
                'amount' => $totalLoanPayments,
                'notes' => "Auto-synced from Loan Audit Database (Year {$year})",
            ]
        );

        // 2. Sync Simpanan Pokok & Wajib (Neraca -> Ekuitas)
        RatManualEntry::updateOrCreate(
            [
                'table_key' => 'neraca',
                'row_key' => 'simpanan_pokok',
                'field_key' => 'nilai',
                'year' => $year,
            ],
            [
                'amount' => $totalSimpananPokok,
                'notes' => "Auto-synced from Real Members Database",
            ]
        );

        RatManualEntry::updateOrCreate(
            [
                'table_key' => 'neraca',
                'row_key' => 'simpanan_wajib',
                'field_key' => 'nilai',
                'year' => $year,
            ],
            [
                'amount' => $totalSimpananWajib,
                'notes' => "Auto-synced from Real Members Database",
            ]
        );

        // 3. Sync Simpanan Sukarela (Neraca -> Kewajiban)
        RatManualEntry::updateOrCreate(
            [
                'table_key' => 'neraca',
                'row_key' => 'simpanan_sukarela_wadiah',
                'field_key' => 'nilai',
                'year' => $year,
            ],
            [
                'amount' => $totalSimpananSukarela,
                'notes' => "Auto-synced from Real Members Database",
            ]
        );

        $formattedPayment = number_format($totalLoanPayments, 0, ',', '.');
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => "Berhasil sinkronisasi Angsuran Pinjaman (Rp {$formattedPayment}) & Simpanan Anggota ke Laporan RAT {$year}!",
        ]);
    }

    public function render()
    {
        // Global Real-Time KPIs
        $totalPlafonDisalurkan = Loan::sum('amount');
        $totalSisaPiutang = Loan::whereIn('status', ['ACTIVE', 'OVERDUE'])->sum('remainingAmount');
        $totalAngsuranTerbayar = LoanPayment::sum('amount');
        $totalLoanCount = Loan::count();
        $activeLoanCount = Loan::where('status', 'ACTIVE')->count();
        $overdueLoanCount = Loan::where('status', 'OVERDUE')->count();
        $completedLoanCount = Loan::where('status', 'COMPLETED')->count();

        // Total Simpanan KPIs
        $totalSimpananPokok = Member::sum('simpananPokok');
        $totalSimpananWajib = Member::sum('simpananWajib');
        $totalSimpananSukarela = Member::sum('simpananSukarela');
        $totalSemuaSimpanan = $totalSimpananPokok + $totalSimpananWajib + $totalSimpananSukarela;

        // Cash Flow Summaries
        $cashFlowData = $this->buildCashFlowSummaries();

        // Details for selected month (if chosen)
        $monthLoanPayments = collect();
        $monthSimpananTransactions = collect();

        if ($this->selectedCashFlowMonth) {
            $monthLoanPayments = LoanPayment::with('loan.member')
                ->whereYear('paymentDate', $this->selectedCashFlowYear)
                ->whereMonth('paymentDate', $this->selectedCashFlowMonth)
                ->orderBy('paymentDate', 'desc')
                ->get();

            // Reconstruct payments detail for historical months if explicit logs don't exist
            if ($monthLoanPayments->isEmpty()) {
                $mNum = (int) $this->selectedCashFlowMonth;
                $mYear = (int) $this->selectedCashFlowYear;
                $monthDate = \Carbon\Carbon::createFromDate($mYear, $mNum, 1)->endOfMonth();
                $activeLoans = Loan::with('member')
                    ->where('startDate', '<=', $monthDate)
                    ->get();

                $reconstructedPayments = collect();
                foreach ($activeLoans as $l) {
                    $start = \Carbon\Carbon::parse($l->startDate);
                    $monthsDiff = (($mYear - $start->year) * 12) + ($mNum - $start->month) + 1;

                    if ($monthsDiff >= 1 && $monthsDiff <= $l->tenor) {
                        $reconstructedPayments->push((object)[
                            'id' => 'RECON-' . $l->id,
                            'loanId' => $l->id,
                            'amount' => $l->monthlyPayment,
                            'paymentDate' => $monthDate->format('Y-m-d'),
                            'description' => "Potongan Payroll (Angsuran ke-{$monthsDiff})",
                            'loan' => $l,
                        ]);
                    }
                }
                $monthLoanPayments = $reconstructedPayments;
            }

            $monthSimpananTransactions = SimpananTransaction::with('member')
                ->where('status', 'APPROVED')
                ->whereYear('created_at', $this->selectedCashFlowYear)
                ->whereMonth('created_at', $this->selectedCashFlowMonth)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        // Query Loans for Tab 1
        $loansQuery = Loan::with(['member', 'payments']);

        if (!empty($this->search)) {
            $searchTerm = '%' . $this->search . '%';
            $loansQuery->where(function ($q) use ($searchTerm) {
                $q->whereHas('member', function ($mq) use ($searchTerm) {
                    $mq->where('name', 'like', $searchTerm)
                       ->orWhere('nomorAnggota', 'like', $searchTerm)
                       ->orWhere('phone', 'like', $searchTerm);
                })->orWhere('account_number', 'like', $searchTerm)
                  ->orWhere('purpose', 'like', $searchTerm);
            });
        }

        if ($this->statusFilter !== 'ALL') {
            $loansQuery->where('status', $this->statusFilter);
        }

        $loans = $loansQuery->orderBy('id', 'desc')->paginate(12, ['*'], 'loansPage');

        // Query Members Savings for Tab 2
        $membersQuery = Member::query();
        if (!empty($this->search)) {
            $searchTerm = '%' . $this->search . '%';
            $membersQuery->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                  ->orWhere('nomorAnggota', 'like', $searchTerm)
                  ->orWhere('phone', 'like', $searchTerm);
            });
        }
        $membersSavings = $membersQuery->orderBy('name', 'asc')->paginate(12, ['*'], 'savingsPage');

        // Source Distribution (BMT_ITQAN vs BERMADANI)
        $plafonBySource = Loan::query()
            ->selectRaw('loanSource, SUM(amount) as total_plafon, SUM(remainingAmount) as total_piutang, COUNT(*) as loan_count')
            ->groupBy('loanSource')
            ->get()
            ->keyBy('loanSource');

        $plafonBmtItqan = (float) ($plafonBySource['BMT_ITQAN']->total_plafon ?? 0);
        $plafonBermadani = (float) ($plafonBySource['BERMADANI']->total_plafon ?? 0);
        $totalPlafonAll = max(1, $plafonBmtItqan + $plafonBermadani);

        $piutangBmtItqan = (float) ($plafonBySource['BMT_ITQAN']->total_piutang ?? 0);
        $piutangBermadani = (float) ($plafonBySource['BERMADANI']->total_piutang ?? 0);

        // Payments calculated dynamically per selected year
        $paymentsBmtItqan = (float) collect($cashFlowData['summaries'])->sum('angsuran_bmt');
        $paymentsBermadani = (float) collect($cashFlowData['summaries'])->sum('angsuran_bermadani');
        $totalPaymentsAll = max(1, $paymentsBmtItqan + $paymentsBermadani);

        $sourceShareStats = [
            'plafon' => [
                'bmt' => $plafonBmtItqan,
                'bermadani' => $plafonBermadani,
                'percent_bmt' => round(($plafonBmtItqan / $totalPlafonAll) * 100, 2),
                'percent_bermadani' => round(($plafonBermadani / $totalPlafonAll) * 100, 2),
            ],
            'payments' => [
                'bmt' => $paymentsBmtItqan,
                'bermadani' => $paymentsBermadani,
                'percent_bmt' => round(($paymentsBmtItqan / $totalPaymentsAll) * 100, 2),
                'percent_bermadani' => round(($paymentsBermadani / $totalPaymentsAll) * 100, 2),
            ],
            'piutang' => [
                'bmt' => $piutangBmtItqan,
                'bermadani' => $piutangBermadani,
            ]
        ];

        return view('livewire.admin.loan-audit-tool', [
            'totalPlafonDisalurkan' => $totalPlafonDisalurkan,
            'totalSisaPiutang' => $totalSisaPiutang,
            'totalAngsuranTerbayar' => $totalAngsuranTerbayar,
            'totalLoanCount' => $totalLoanCount,
            'activeLoanCount' => $activeLoanCount,
            'overdueLoanCount' => $overdueLoanCount,
            'completedLoanCount' => $completedLoanCount,
            'totalSimpananPokok' => $totalSimpananPokok,
            'totalSimpananWajib' => $totalSimpananWajib,
            'totalSimpananSukarela' => $totalSimpananSukarela,
            'totalSemuaSimpanan' => $totalSemuaSimpanan,
            'cashFlowSummaries' => $cashFlowData['summaries'],
            'cashFlowChartData' => $cashFlowData['chartData'],
            'monthLoanPayments' => $monthLoanPayments,
            'monthSimpananTransactions' => $monthSimpananTransactions,
            'loans' => $loans,
            'membersSavings' => $membersSavings,
            'sourceShareStats' => $sourceShareStats,
        ])->layout('layouts.admin', ['title' => 'Audit Keuangan Pinjaman & Simpanan']);
    }
}
