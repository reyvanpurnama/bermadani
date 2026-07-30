<?php

namespace App\Livewire\Admin;

use App\Models\FinancialTransaction;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class CashFlowVisualization extends Component
{
    public $selectedYear;
    public $availableYears = [];

    public function mount()
    {
        $years = FinancialTransaction::selectRaw('YEAR(transactionDate) as year')
            ->distinct()
            ->pluck('year')
            ->toArray();

        if (empty($years)) {
            $years = [(int) date('Y')];
        } else {
            sort($years);
        }

        $this->availableYears = $years;
        // Set default to 2025 if present, else max year
        $this->selectedYear = in_array(2025, $years) ? 2025 : end($years);
    }

    public function getSummaryProperty()
    {
        $transactions = FinancialTransaction::whereYear('transactionDate', $this->selectedYear)->get();

        $totalIncome = (float) $transactions->where('type', 'INCOME')->sum('amount');
        $totalExpense = (float) $transactions->where('type', 'EXPENSE')->sum('amount');
        $netCashFlow = $totalIncome - $totalExpense;

        $activeMonthsCount = $transactions->pluck('transactionDate')
            ->map(fn($date) => \Carbon\Carbon::parse($date)->format('Y-m'))
            ->unique()
            ->count();

        $activeMonthsCount = max(1, $activeMonthsCount);

        return [
            'totalIncome' => $totalIncome,
            'totalExpense' => $totalExpense,
            'netCashFlow' => $netCashFlow,
            'avgIncome' => $totalIncome / $activeMonthsCount,
            'avgExpense' => $totalExpense / $activeMonthsCount,
            'activeMonths' => $activeMonthsCount,
        ];
    }

    public function getChartDataProperty()
    {
        $monthNames = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
            5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu',
            9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
        ];

        // Fetch monthly data for selected year
        $monthlyRaw = FinancialTransaction::selectRaw('
                MONTH(transactionDate) as month_num,
                type,
                SUM(amount) as total_amount
            ')
            ->whereYear('transactionDate', $this->selectedYear)
            ->groupBy('month_num', 'type')
            ->orderBy('month_num')
            ->get();

        // Determine active range of months (e.g., Mei to Des)
        $activeMonths = $monthlyRaw->pluck('month_num')->unique()->sort()->values()->toArray();
        if (empty($activeMonths)) {
            $activeMonths = range(1, 12);
        }

        $categories = [];
        $incomeData = [];
        $expenseData = [];
        $netData = [];

        foreach ($activeMonths as $m) {
            $categories[] = $monthNames[$m] ?? "Bln {$m}";

            $inc = (float) ($monthlyRaw->where('month_num', $m)->where('type', 'INCOME')->first()?->total_amount ?? 0);
            $exp = (float) ($monthlyRaw->where('month_num', $m)->where('type', 'EXPENSE')->first()?->total_amount ?? 0);

            $incomeData[] = round($inc, 2);
            $expenseData[] = round($exp, 2);
            $netData[] = round($inc - $exp, 2);
        }

        // Fetch Income Breakdown by Category
        $incomeCategoriesRaw = FinancialTransaction::whereYear('transactionDate', $this->selectedYear)
            ->where('type', 'INCOME')
            ->selectRaw('category, SUM(amount) as total_amount')
            ->groupBy('category')
            ->orderByDesc('total_amount')
            ->get();

        // Fetch Expense Breakdown by Category
        $expenseCategoriesRaw = FinancialTransaction::whereYear('transactionDate', $this->selectedYear)
            ->where('type', 'EXPENSE')
            ->selectRaw('category, SUM(amount) as total_amount')
            ->groupBy('category')
            ->orderByDesc('total_amount')
            ->get();

        return [
            'trend' => [
                'categories' => $categories,
                'income' => $incomeData,
                'expense' => $expenseData,
                'net' => $netData,
            ],
            'incomeCategories' => [
                'labels' => $incomeCategoriesRaw->pluck('category')->toArray(),
                'series' => $incomeCategoriesRaw->pluck('total_amount')->map(fn($v) => (float) $v)->toArray(),
            ],
            'expenseCategories' => [
                'labels' => $expenseCategoriesRaw->pluck('category')->toArray(),
                'series' => $expenseCategoriesRaw->pluck('total_amount')->map(fn($v) => (float) $v)->toArray(),
            ],
        ];
    }

    public function render()
    {
        return view('livewire.admin.cash-flow-visualization', [
            'summary' => $this->summary,
            'chartData' => $this->chartData,
        ])->layout('layouts.admin');
    }
}
