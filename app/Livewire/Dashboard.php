<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\FinancialTransaction;
use App\Models\Member;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Dashboard extends Component
{
    public $filter = 'today';
    public $chartData = [];

    public function mount()
    {
        $this->filter = 'today';
        $this->calculateChartData();
    }

    public function updatedFilter()
    {
        $this->calculateChartData();
    }

    public function setFilter($filter)
    {
        $this->filter = $filter;
        $this->calculateChartData();
    }

    public function getTotalSalesProperty()
    {
        $query = Transaction::where('type', 'SALE')->where('status', 'COMPLETED');

        return match ($this->filter) {
            'today' => $query->whereDate('date', today())->sum('totalAmount'),
            'week' => $query->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()])->sum('totalAmount'),
            'month' => $query->whereMonth('date', now()->month)->whereYear('date', now()->year)->sum('totalAmount'),
            'year' => $query->whereYear('date', now()->year)->sum('totalAmount'),
            default => $query->whereDate('date', today())->sum('totalAmount'),
        };
    }

    public function getTotalTransactionsProperty()
    {
        $query = Transaction::where('type', 'SALE')->where('status', 'COMPLETED');

        return match ($this->filter) {
            'today' => $query->whereDate('date', today())->count(),
            'week' => $query->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'month' => $query->whereMonth('date', now()->month)->whereYear('date', now()->year)->count(),
            'year' => $query->whereYear('date', now()->year)->count(),
            default => $query->whereDate('date', today())->count(),
        };
    }

    public function calculateChartData()
    {
        $categories = [];
        $incomeData = [];
        $expenseData = [];

        if ($this->filter === 'today') {
            // Hourly breakdown (08:00 - 22:00)
            $hours = [8, 10, 12, 14, 16, 18, 20, 22];
            foreach ($hours as $h) {
                $categories[] = sprintf("%02d:00", $h);

                // POS Sales + Historical + Other Income
                $pos = Transaction::where('type', 'SALE')->where('status', 'COMPLETED')
                    ->whereDate('date', today())
                    ->whereTime('date', '>=', sprintf("%02d:00:00", $h))
                    ->whereTime('date', '<', sprintf("%02d:59:59", $h + 1))
                    ->sum('totalAmount');

                $manualIncome = FinancialTransaction::income()
                    ->whereDate('transactionDate', today())
                    ->whereTime('transactionDate', '>=', sprintf("%02d:00:00", $h))
                    ->whereTime('transactionDate', '<', sprintf("%02d:59:59", $h + 1))
                    ->sum('amount');

                $incomeData[] = $pos + $manualIncome;

                // Expenses
                $expense = FinancialTransaction::expense()
                    ->whereDate('transactionDate', today())
                    ->whereTime('transactionDate', '>=', sprintf("%02d:00:00", $h))
                    ->whereTime('transactionDate', '<', sprintf("%02d:59:59", $h + 1))
                    ->sum('amount');
                $expenseData[] = $expense;
            }
        } elseif ($this->filter === 'week') {
            // Daily breakdown (Mon - Sun)
            $start = now()->startOfWeek();
            for ($i = 0; $i < 7; $i++) {
                $day = $start->copy()->addDays($i);
                $categories[] = $day->format('D'); // Mon, Tue...

                $pos = Transaction::where('type', 'SALE')->where('status', 'COMPLETED')
                    ->whereDate('date', $day)->sum('totalAmount');
                $manualIncome = FinancialTransaction::income()->whereDate('transactionDate', $day)->sum('amount');
                $incomeData[] = $pos + $manualIncome;

                $expense = FinancialTransaction::expense()->whereDate('transactionDate', $day)->sum('amount');
                $expenseData[] = $expense;
            }
        } elseif ($this->filter === 'month') {
            // Daily breakdown (1 - End of Month)
            $start = now()->startOfMonth();
            $end = now()->endOfMonth();
            // Simplify: Group by weeks if too many days? No, days are fine for chart.
            // But strict requirement says "Week 1, Week 2..." in template? 
            // The template said: categories: ["Week 1", "Week 2", "Week 3", "Week 4"].
            // Let's implement Weeks 1-4/5 for cleanliness

            $current = $start->copy();
            $weekNum = 1;
            while ($current <= $end) {
                $weekEnd = $current->copy()->endOfWeek();
                if ($weekEnd > $end)
                    $weekEnd = $end;

                $categories[] = "Week " . $weekNum++;

                $pos = Transaction::where('type', 'SALE')->where('status', 'COMPLETED')
                    ->whereBetween('date', [$current, $weekEnd])->sum('totalAmount');
                $manualIncome = FinancialTransaction::income()
                    ->whereBetween('transactionDate', [$current, $weekEnd])->sum('amount');
                $incomeData[] = $pos + $manualIncome;

                $expense = FinancialTransaction::expense()
                    ->whereBetween('transactionDate', [$current, $weekEnd])->sum('amount');
                $expenseData[] = $expense;

                $current = $weekEnd->addDay()->startOfDay();
            }
        } elseif ($this->filter === 'year') {
            // Monthly breakdown (Jan - Dec)
            for ($i = 1; $i <= 12; $i++) {
                $categories[] = date("M", mktime(0, 0, 0, $i, 1)); // Jan, Feb...

                $pos = Transaction::where('type', 'SALE')->where('status', 'COMPLETED')
                    ->whereYear('date', now()->year)->whereMonth('date', $i)->sum('totalAmount');
                $manualIncome = FinancialTransaction::income()
                    ->whereYear('transactionDate', now()->year)->whereMonth('transactionDate', $i)->sum('amount');
                $incomeData[] = $pos + $manualIncome;

                $expense = FinancialTransaction::expense()
                    ->whereYear('transactionDate', now()->year)->whereMonth('transactionDate', $i)->sum('amount');
                $expenseData[] = $expense;
            }
        }

        $this->chartData = [
            'categories' => $categories,
            'income' => $incomeData,
            'expense' => $expenseData
        ];
    }

    // Profitability Metrics
    public function getGrossProfitProperty()
    {
        // Margin Kotor = Penjualan POS + Omset Historis (dari transaksi manual)
        return $this->totalSales + $this->historicalSales;
    }

    // Omset Penjualan Historis (dari transaksi manual)
    public function getHistoricalSalesProperty()
    {
        $query = FinancialTransaction::income()
            ->where('category', 'Omset Penjualan (Historis)');

        return match ($this->filter) {
            'today' => $query->whereDate('transactionDate', today())->sum('amount'),
            'week' => $query->whereBetween('transactionDate', [now()->startOfWeek(), now()->endOfWeek()])->sum('amount'),
            'month' => $query->whereMonth('transactionDate', now()->month)->whereYear('transactionDate', now()->year)->sum('amount'),
            'year' => $query->whereYear('transactionDate', now()->year)->sum('amount'),
            default => $query->whereDate('transactionDate', today())->sum('amount'),
        };
    }

    public function getTotalCogsProperty()
    {
        // COGS hanya untuk tracking margin, bukan pengeluaran riil
        $query = Transaction::where('type', 'SALE')
            ->where('status', 'COMPLETED')
            ->join('transaction_items', 'transactions.id', '=', 'transaction_items.transactionId');

        $cogs = match ($this->filter) {
            'today' => $query->whereDate('transactions.date', today())->sum('transaction_items.totalCogs'),
            'week' => $query->whereBetween('transactions.date', [now()->startOfWeek(), now()->endOfWeek()])->sum('transaction_items.totalCogs'),
            'month' => $query->whereMonth('transactions.date', now()->month)->whereYear('transactions.date', now()->year)->sum('transaction_items.totalCogs'),
            'year' => $query->whereYear('transactions.date', now()->year)->sum('transaction_items.totalCogs'),
            default => $query->whereDate('transactions.date', today())->sum('transaction_items.totalCogs'),
        };

        return $cogs ?? 0;
    }

    public function getGrossMarginPercentProperty()
    {
        $posSales = $this->totalSales;
        $historicalSales = $this->historicalSales;
        $totalRevenue = $posSales + $historicalSales;
        $cogs = $this->totalCogs;

        if ($totalRevenue == 0)
            return 0;

        // Margin = (POS Sales - COGS) + Historical Sales
        // Asumsi: Historical Sales tidak ada COGS (margin 100%)
        $totalMargin = ($posSales - $cogs) + $historicalSales;

        return round(($totalMargin / $totalRevenue) * 100, 1);
    }

    public function getOperatingExpensesProperty()
    {
        // Ambil total pengeluaran dari financial_transactions berdasarkan filter
        $query = FinancialTransaction::expense();

        $expenses = match ($this->filter) {
            'today' => $query->whereDate('transactionDate', today())->sum('amount'),
            'week' => $query->whereBetween('transactionDate', [now()->startOfWeek(), now()->endOfWeek()])->sum('amount'),
            'month' => $query->whereMonth('transactionDate', now()->month)->whereYear('transactionDate', now()->year)->sum('amount'),
            'year' => $query->whereYear('transactionDate', now()->year)->sum('amount'),
            default => $query->whereDate('transactionDate', today())->sum('amount'),
        };

        return $expenses ?? 0;
    }

    // Pemasukan Lain-lain (KECUALI Suntikan Modal & Omset Historis)
    public function getOtherIncomeProperty()
    {
        $query = FinancialTransaction::income()
            ->whereNotIn('category', ['Suntikan Modal', 'Omset Penjualan (Historis)']);

        $income = match ($this->filter) {
            'today' => $query->whereDate('transactionDate', today())->sum('amount'),
            'week' => $query->whereBetween('transactionDate', [now()->startOfWeek(), now()->endOfWeek()])->sum('amount'),
            'month' => $query->whereMonth('transactionDate', now()->month)->whereYear('transactionDate', now()->year)->sum('amount'),
            'year' => $query->whereYear('transactionDate', now()->year)->sum('amount'),
            default => $query->whereDate('transactionDate', today())->sum('amount'),
        };

        return $income ?? 0;
    }

    public function getOperatingMarginPercentProperty()
    {
        $sales = $this->totalSales;

        if ($sales == 0)
            return 0;

        return round(($this->operatingExpenses / $sales) * 100, 1);
    }

    public function getNetProfitProperty()
    {
        // Laba Bersih = Margin Kotor + Pemasukan Lain-lain - Pengeluaran Operasional
        return max(0, $this->grossProfit + $this->otherIncome - $this->operatingExpenses);
    }

    public function getProfitGrowthProperty()
    {
        // Get previous period data
        [$currentStart, $currentEnd] = $this->getCurrentPeriodRange();
        [$previousStart, $previousEnd] = $this->getPreviousPeriodRange();

        // Current net profit (sales + historical + other income - expenses)
        $currentSales = Transaction::where('type', 'SALE')
            ->where('status', 'COMPLETED')
            ->whereBetween('date', [$currentStart, $currentEnd])
            ->sum('totalAmount') ?? 0;

        $currentHistorical = FinancialTransaction::income()
            ->where('category', 'Omset Penjualan (Historis)')
            ->whereBetween('transactionDate', [$currentStart, $currentEnd])
            ->sum('amount') ?? 0;

        $currentOtherIncome = FinancialTransaction::income()
            ->whereNotIn('category', ['Suntikan Modal', 'Omset Penjualan (Historis)'])
            ->whereBetween('transactionDate', [$currentStart, $currentEnd])
            ->sum('amount') ?? 0;

        $currentExpenses = FinancialTransaction::expense()
            ->whereBetween('transactionDate', [$currentStart, $currentEnd])
            ->sum('amount') ?? 0;

        $currentProfit = $currentSales + $currentHistorical + $currentOtherIncome - $currentExpenses;

        // Previous net profit (sales + historical + other income - expenses)
        $previousSales = Transaction::where('type', 'SALE')
            ->where('status', 'COMPLETED')
            ->whereBetween('date', [$previousStart, $previousEnd])
            ->sum('totalAmount') ?? 0;

        $previousHistorical = FinancialTransaction::income()
            ->where('category', 'Omset Penjualan (Historis)')
            ->whereBetween('transactionDate', [$previousStart, $previousEnd])
            ->sum('amount') ?? 0;

        $previousOtherIncome = FinancialTransaction::income()
            ->whereNotIn('category', ['Suntikan Modal', 'Omset Penjualan (Historis)'])
            ->whereBetween('transactionDate', [$previousStart, $previousEnd])
            ->sum('amount') ?? 0;

        $previousExpenses = FinancialTransaction::expense()
            ->whereBetween('transactionDate', [$previousStart, $previousEnd])
            ->sum('amount') ?? 0;

        $previousProfit = $previousSales + $previousHistorical + $previousOtherIncome - $previousExpenses;

        if ($previousProfit == 0)
            return 0;

        return round((($currentProfit - $previousProfit) / abs($previousProfit)) * 100, 1);
    }

    public function getPreviousPeriodLabelProperty()
    {
        return match ($this->filter) {
            'today' => 'vs Kemarin',
            'week' => 'vs Minggu Lalu',
            'month' => 'vs Bulan Lalu',
            'year' => 'vs Tahun Lalu',
            default => 'vs Kemarin',
        };
    }

    private function getCurrentPeriodRange()
    {
        return match ($this->filter) {
            'today' => [today()->startOfDay(), today()->endOfDay()],
            'week' => [now()->startOfWeek(), now()->endOfWeek()],
            'month' => [now()->startOfMonth(), now()->endOfMonth()],
            'year' => [now()->startOfYear(), now()->endOfYear()],
            default => [today()->startOfDay(), today()->endOfDay()],
        };
    }

    private function getPreviousPeriodRange()
    {
        return match ($this->filter) {
            'today' => [today()->subDay()->startOfDay(), today()->subDay()->endOfDay()],
            'week' => [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()],
            'month' => [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()],
            'year' => [now()->subYear()->startOfYear(), now()->subYear()->endOfYear()],
            default => [today()->subDay()->startOfDay(), today()->subDay()->endOfDay()],
        };
    }

    // ALL-TIME METRICS (untuk card atas)
    public function getAllTimeSalesProperty()
    {
        $posSales = Transaction::where('type', 'SALE')
            ->where('status', 'COMPLETED')
            ->sum('totalAmount');

        $historicalSales = FinancialTransaction::income()
            ->where('category', 'Omset Penjualan (Historis)')
            ->sum('amount');

        return $posSales + $historicalSales;
    }

    public function getAllTimeExpensesProperty()
    {
        return FinancialTransaction::expense()->sum('amount');
    }

    // Pemasukan Lain-lain All-Time (KECUALI Suntikan Modal & Omset Historis)
    public function getAllTimeOtherIncomeProperty()
    {
        return FinancialTransaction::income()
            ->whereNotIn('category', ['Suntikan Modal', 'Omset Penjualan (Historis)'])
            ->sum('amount');
    }

    public function getAllTimeProfitProperty()
    {
        // All-time profit = Sales + Other Income - Expenses
        return max(0, $this->allTimeSales + $this->allTimeOtherIncome - $this->allTimeExpenses);
    }

    public function getFirstTransactionDateProperty()
    {
        // 1. Cek transaksi POS pertama
        $firstPosSale = Transaction::where('type', 'SALE')
            ->where('status', 'COMPLETED')
            ->orderBy('date')
            ->first();

        // 2. Cek transaksi manual (Omset Historis) pertama
        $firstHistoricalSale = FinancialTransaction::income()
            ->where('category', 'Omset Penjualan (Historis)')
            ->orderBy('transactionDate')
            ->first();

        // 3. Bandingkan mana yang lebih lama
        $posDate = $firstPosSale?->date;
        $historicalDate = $firstHistoricalSale?->transactionDate;

        if ($posDate && $historicalDate) {
            return $posDate->lt($historicalDate)
                ? $posDate->format('d M Y')
                : $historicalDate->format('d M Y');
        } elseif ($posDate) {
            return $posDate->format('d M Y');
        } elseif ($historicalDate) {
            return $historicalDate->format('d M Y');
        }

        return now()->format('d M Y');
    }

    public function getTotalProductsProperty()
    {
        return Product::where('isActive', true)->count();
    }

    public function getTotalMembersProperty()
    {
        return Member::where('status', 'ACTIVE')->count();
    }

    public function getLowStockProductsProperty()
    {
        return Product::with('category')
            ->where('isActive', true)
            ->whereColumn('stock', '<=', 'threshold')
            ->orderBy('stock')
            ->limit(5)
            ->get();
    }

    public function getTopProductsProperty()
    {
        return Product::select('products.id', 'products.name', 'products.categoryId', DB::raw('SUM(transaction_items.quantity) as total_sold'))
            ->join('transaction_items', 'products.id', '=', 'transaction_items.productId')
            ->join('transactions', 'transaction_items.transactionId', '=', 'transactions.id')
            ->where('transactions.type', 'SALE')
            ->where('transactions.status', 'COMPLETED')
            ->when($this->filter === 'today', fn($q) => $q->whereDate('transactions.date', today()))
            ->when($this->filter === 'week', fn($q) => $q->whereBetween('transactions.date', [now()->startOfWeek(), now()->endOfWeek()]))
            ->when($this->filter === 'month', fn($q) => $q->whereMonth('transactions.date', now()->month)->whereYear('transactions.date', now()->year))
            ->when($this->filter === 'year', fn($q) => $q->whereYear('transactions.date', now()->year))
            ->groupBy('products.id', 'products.name', 'products.categoryId')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->with('category')
            ->get();
    }

    public function getRecentTransactionsProperty()
    {
        return Transaction::with('member')
            ->where('type', 'SALE')
            ->orderByDesc('date')
            ->limit(10)
            ->get();
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}
