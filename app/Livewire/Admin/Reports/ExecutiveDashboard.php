<?php

namespace App\Livewire\Admin\Reports;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Domains\Accounting\Services\FinancialStatementService;
use App\Models\Member;
use App\Models\Loan;

#[Layout('layouts.admin')]
class ExecutiveDashboard extends Component
{
    public int $selectedYear;

    public function mount()
    {
        $this->selectedYear = (int) date('Y');
    }

    public function render()
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
        
        $totalLoans = (float) Loan::whereIn('status', ['ACTIVE', 'OVERDUE'])->sum('remainingAmount');
        $prevLoans = $totalLoans * 0.9;

        $totalAset = (float) ($balanceSheet['total_aset'] ?? 0);
        $prevAset = (float) ($prevBalanceSheet['total_aset'] ?? 0);
        
        $shu = (float) ($incomeStatement['shu_bersih'] ?? 0);
        $prevShu = (float) ($prevIncomeStatement['shu_bersih'] ?? 0);

        $kas = (float) ($balanceSheet['kas'] ?? 0);
        $prevKas = (float) ($prevBalanceSheet['kas'] ?? 0);

        $kpiData = [
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

        // Collect historical SHU
        $shuHistory = [];
        $years = [];
        for ($i = 4; $i >= 0; $i--) {
            $yr = $this->selectedYear - $i;
            $inc = $service->getIncomeStatement($yr);
            $shuHistory[] = (float) ($inc['shu_bersih'] ?? 0);
            $years[] = (string) $yr;
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
        $piutang = (float) ($balanceSheet['piutang_pembiayaan'] ?? 0);
        $asetTetap = (float) ($balanceSheet['aset_tetap'] ?? 0);
        $asetLain = max(0, $totalAset - $piutang - $kas - $asetTetap);

        // Income composition
        $margin = (float) ($incomeStatement['margin_pembiayaan'] ?? 0);
        $admin = (float) ($incomeStatement['pendapatan_administrasi'] ?? 0);
        $pendapatanLain = (float) ($incomeStatement['pendapatan_lain'] ?? 0);

        // Expense composition
        $gaji = (float) ($incomeStatement['beban_gaji'] ?? 0);
        $atk = (float) ($incomeStatement['beban_atk'] ?? 0);
        $listrik = (float) ($incomeStatement['beban_listrik'] ?? 0);
        $penyusutan = (float) ($incomeStatement['beban_penyusutan'] ?? 0);
        $bebanLain = (float) ($incomeStatement['beban_lain'] ?? 0);

        // Cash flow
        $cfOperasi = (float) ($cashFlowStatement['total_operasi'] ?? 0);
        $cfInvestasi = (float) ($cashFlowStatement['total_investasi'] ?? 0);
        $cfPendanaan = (float) ($cashFlowStatement['total_pendanaan'] ?? 0);

        $chartData = [
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
                'series' => [$cfOperasi, $cfInvestasi, $cfPendanaan]
            ]
        ];

        return view('livewire.admin.reports.executive-dashboard', [
            'kpiData' => $kpiData,
            'healthScorecard' => $healthScorecard,
            'chartData' => $chartData,
        ]);
    }
}
