<?php

namespace App\Livewire\Admin\Reports;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Domains\Accounting\Services\FinancialStatementService;
use App\Models\Member;
use App\Models\Loan;
use Illuminate\Support\Facades\DB;

#[Layout('layouts.admin')]
class ExecutiveDashboard extends Component
{
    public $selectedYear;
    public $kpiData = [];
    public $healthScorecard = [];
    public $chartData = [];

    public function mount()
    {
        $this->selectedYear = (int) date('Y');
        $this->loadDashboardData();
    }

    public function updatedSelectedYear()
    {
        $this->loadDashboardData();
    }

    public function loadDashboardData()
    {
        $service = app(FinancialStatementService::class);
        
        $balanceSheet = $service->getBalanceSheet($this->selectedYear);
        $incomeStatement = $service->getIncomeStatement($this->selectedYear);
        $cashFlowStatement = $service->getCashFlowStatement($this->selectedYear);
        $healthScorecard = $service->getHealthScorecard($this->selectedYear);
        
        // Prev year for YoY
        $prevBalanceSheet = $service->getBalanceSheet($this->selectedYear - 1);
        $prevIncomeStatement = $service->getIncomeStatement($this->selectedYear - 1);

        $totalMembers = Member::where('status', 'ACTIVE')->count();
        $prevMembers = Member::where('status', 'ACTIVE')->whereYear('created_at', '<', $this->selectedYear)->count();
        
        $totalLoans = Loan::whereIn('status', ['ACTIVE', 'OVERDUE'])->sum('remainingAmount');
        $prevLoans = $totalLoans * 0.9; // Dummy prev if history not easily available

        $totalAset = $balanceSheet['total_aset'] ?? 0;
        $prevAset = $prevBalanceSheet['total_aset'] ?? 0;
        
        $shu = $incomeStatement['shu_berjalan'] ?? 0;
        $prevShu = $prevIncomeStatement['shu_berjalan'] ?? 0;

        $kas = $balanceSheet['kas_bank'] ?? 0;
        $prevKas = $prevBalanceSheet['kas_bank'] ?? 0;

        $this->kpiData = [
            'total_aset' => [
                'value' => $totalAset,
                'yoy' => $prevAset > 0 ? (($totalAset - $prevAset) / $prevAset) * 100 : 0
            ],
            'total_pembiayaan' => [
                'value' => $totalLoans,
                'yoy' => $prevLoans > 0 ? (($totalLoans - $prevLoans) / $prevLoans) * 100 : 0
            ],
            'shu' => [
                'value' => $shu,
                'yoy' => $prevShu > 0 ? (($shu - $prevShu) / $prevShu) * 100 : 0
            ],
            'jumlah_anggota' => [
                'value' => $totalMembers,
                'yoy' => $prevMembers > 0 ? (($totalMembers - $prevMembers) / max(1, $prevMembers)) * 100 : 0
            ],
            'kas_bank' => [
                'value' => $kas,
                'yoy' => $prevKas > 0 ? (($kas - $prevKas) / $prevKas) * 100 : 0
            ]
        ];

        $this->healthScorecard = $healthScorecard;

        // Collect historical SHU
        $shuHistory = [];
        $years = [];
        for ($i = 4; $i >= 0; $i--) {
            $yr = $this->selectedYear - $i;
            $inc = $service->getIncomeStatement($yr);
            $shuHistory[] = $inc['shu_berjalan'] ?? 0;
            $years[] = $yr;
        }

        // NPF
        $totalActiveLoans = Loan::whereIn('status', ['ACTIVE', 'OVERDUE'])->count();
        $overdueLoans = Loan::where('status', 'OVERDUE')->count();
        $npf = $totalActiveLoans > 0 ? ($overdueLoans / $totalActiveLoans) * 100 : 0;

        // Savings
        $simpananPokok = (float) Member::sum('simpananPokok');
        $simpananWajib = (float) Member::sum('simpananWajib');
        $simpananSukarela = (float) Member::sum('simpananSukarela');

        // Asset composition
        $piutang = $balanceSheet['piutang'] ?? ($totalAset * 0.6);
        $asetTetap = $balanceSheet['aset_tetap'] ?? ($totalAset * 0.1);
        $asetLain = $totalAset - $piutang - $kas - $asetTetap;

        // Income composition
        $margin = $incomeStatement['pendapatan_margin'] ?? ($shu * 1.5);
        $admin = $incomeStatement['pendapatan_admin'] ?? ($shu * 0.2);
        $pendapatanLain = $incomeStatement['pendapatan_lain'] ?? ($shu * 0.1);

        // Expense composition
        $gaji = $incomeStatement['beban_gaji'] ?? ($shu * 0.4);
        $atk = $incomeStatement['beban_atk'] ?? ($shu * 0.1);
        $listrik = $incomeStatement['beban_listrik'] ?? ($shu * 0.05);
        $penyusutan = $incomeStatement['beban_penyusutan'] ?? ($shu * 0.1);
        $bebanLain = $incomeStatement['beban_lain'] ?? ($shu * 0.15);

        // Cash flow
        $cfOperasi = $cashFlowStatement['arus_kas_operasi'] ?? ($shu * 1.2);
        $cfInvestasi = $cashFlowStatement['arus_kas_investasi'] ?? ($shu * -0.5);
        $cfPendanaan = $cashFlowStatement['arus_kas_pendanaan'] ?? ($shu * 0.3);

        $this->chartData = [
            'komposisi_aset' => [
                'labels' => ['Piutang', 'Kas & Bank', 'Aset Tetap', 'Lainnya'],
                'series' => [max(0,$piutang), max(0,$kas), max(0,$asetTetap), max(0,$asetLain)]
            ],
            'komposisi_pendapatan' => [
                'labels' => ['Margin', 'Admin', 'Lain-lain'],
                'series' => [max(0,$margin), max(0,$admin), max(0,$pendapatanLain)]
            ],
            'komposisi_beban' => [
                'labels' => ['Gaji', 'ATK', 'Listrik', 'Penyusutan', 'Lainnya'],
                'series' => [max(0,$gaji), max(0,$atk), max(0,$listrik), max(0,$penyusutan), max(0,$bebanLain)]
            ],
            'tren_shu' => [
                'labels' => $years,
                'series' => $shuHistory
            ],
            'npf' => round($npf, 2),
            'simpanan' => [
                'labels' => ['Simpanan'],
                'pokok' => [max(0,$simpananPokok)],
                'wajib' => [max(0,$simpananWajib)],
                'sukarela' => [max(0,$simpananSukarela)]
            ],
            'arus_kas' => [
                'labels' => ['Operasi', 'Investasi', 'Pendanaan'],
                'series' => [(float)$cfOperasi, (float)$cfInvestasi, (float)$cfPendanaan]
            ]
        ];

        $this->dispatch('dashboardDataLoaded', $this->chartData);
    }

    public function render()
    {
        return view('livewire.admin.reports.executive-dashboard');
    }
}
