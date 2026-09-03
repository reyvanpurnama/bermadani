<?php

namespace App\Livewire\Admin\Reports;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Domains\Accounting\Services\FinancialStatementService;

#[Layout('layouts.admin')]
class FinancialStatements extends Component
{
    public int $selectedYear;
    public string $activeTab = 'neraca';

    public function mount()
    {
        $this->selectedYear = (int) date('Y');
    }

    public function setTab(string $tab)
    {
        if (in_array($tab, ['neraca', 'shu', 'perubahan_ekuitas', 'arus_kas'])) {
            $this->activeTab = $tab;
        }
    }

    public function render()
    {
        $service = app(FinancialStatementService::class);

        $reportData = [
            'neraca' => $service->getBalanceSheet($this->selectedYear),
            'shu' => $service->getIncomeStatement($this->selectedYear),
            'perubahan_ekuitas' => $service->getEquityChanges($this->selectedYear),
            'arus_kas' => $service->getCashFlowStatement($this->selectedYear),
        ];

        return view('livewire.admin.reports.financial-statements', [
            'reportData' => $reportData,
        ]);
    }
}
