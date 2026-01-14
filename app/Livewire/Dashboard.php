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
    public $dateFilter = 'this_month';
    public $startDate;
    public $endDate;
    public $chartData = [];

    public function mount()
    {
        $this->dateFilter = 'this_month';
        $this->updateDateRange();
        $this->calculateChartData();
    }

    public function updatedDateFilter()
    {
        $this->updateDateRange();
        $this->calculateChartData();
    }

    public function updatedStartDate()
    {
        $this->dateFilter = 'custom';
        $this->calculateChartData();
    }

    public function updatedEndDate()
    {
        $this->dateFilter = 'custom';
        $this->calculateChartData();
    }

    private function updateDateRange()
    {
        switch ($this->dateFilter) {
            case 'today':
                $this->startDate = today()->format('Y-m-d');
                $this->endDate = today()->format('Y-m-d');
                break;
            case 'yesterday':
                $this->startDate = today()->subDay()->format('Y-m-d');
                $this->endDate = today()->subDay()->format('Y-m-d');
                break;
            case 'this_week':
                $this->startDate = now()->startOfWeek()->format('Y-m-d');
                $this->endDate = now()->endOfWeek()->format('Y-m-d');
                break;
            case 'this_month':
                $this->startDate = now()->startOfMonth()->format('Y-m-d');
                $this->endDate = now()->endOfMonth()->format('Y-m-d');
                break;
            case 'last_month':
                $this->startDate = now()->subMonth()->startOfMonth()->format('Y-m-d');
                $this->endDate = now()->subMonth()->endOfMonth()->format('Y-m-d');
                break;
            case 'this_year':
                $this->startDate = now()->startOfYear()->format('Y-m-d');
                $this->endDate = now()->endOfYear()->format('Y-m-d');
                break;
            case 'custom':
                // Do nothing, user sets manually
                break;
            default:
                $this->startDate = now()->startOfMonth()->format('Y-m-d');
                $this->endDate = now()->endOfMonth()->format('Y-m-d');
        }
    }

    public function getTotalSalesProperty()
    {
        return Transaction::where('type', 'SALE')
            ->where('status', 'COMPLETED')
            ->whereDate('date', '>=', $this->startDate)
            ->whereDate('date', '<=', $this->endDate)
            ->sum('totalAmount');
    }

    public function getTotalTransactionsProperty()
    {
        return Transaction::where('type', 'SALE')
            ->where('status', 'COMPLETED')
            ->whereDate('date', '>=', $this->startDate)
            ->whereDate('date', '<=', $this->endDate)
            ->count();
    }

    public function calculateChartData()
    {
        $categories = [];
        $incomeData = [];
        $expenseData = [];

        $start = \Carbon\Carbon::parse($this->startDate);
        $end = \Carbon\Carbon::parse($this->endDate);

        $diffInDays = $start->diffInDays($end);

        if ($diffInDays <= 1) {
            // Hourly breakdown (08:00 - 22:00)
            $hours = [8, 10, 12, 14, 16, 18, 20, 22];
            $baseDate = $start->format('Y-m-d');
            foreach ($hours as $h) {
                // Use ISO format for datetime axis
                $categories[] = sprintf("%s %02d:00:00", $baseDate, $h);

                // Aggregation logic for Hourly
                // Only consider transactions on that specific day within the hour range
                $pos = Transaction::where('type', 'SALE')->where('status', 'COMPLETED')
                    ->whereDate('date', $start)
                    ->whereTime('date', '>=', sprintf("%02d:00:00", $h))
                    ->whereTime('date', '<', sprintf("%02d:59:59", $h + 1))
                    ->sum('totalAmount');

                $manualIncome = FinancialTransaction::income()
                    ->whereDate('transactionDate', $start)
                    ->whereTime('transactionDate', '>=', sprintf("%02d:00:00", $h))
                    ->whereTime('transactionDate', '<', sprintf("%02d:59:59", $h + 1))
                    ->sum('amount');

                $incomeData[] = $pos + $manualIncome;

                $expense = FinancialTransaction::expense()
                    ->whereDate('transactionDate', $start)
                    ->whereTime('transactionDate', '>=', sprintf("%02d:00:00", $h))
                    ->whereTime('transactionDate', '<', sprintf("%02d:59:59", $h + 1))
                    ->sum('amount');
                $expenseData[] = $expense;
            }
        } elseif ($diffInDays <= 35) {
            // Daily breakdown
            $current = $start->copy();
            while ($current <= $end) {
                $categories[] = $current->format('Y-m-d');

                $pos = Transaction::where('type', 'SALE')->where('status', 'COMPLETED')
                    ->whereDate('date', $current)
                    ->sum('totalAmount');

                $manualIncome = FinancialTransaction::income()
                    ->whereDate('transactionDate', $current)
                    ->sum('amount');

                $incomeData[] = $pos + $manualIncome;

                $expense = FinancialTransaction::expense()
                    ->whereDate('transactionDate', $current)
                    ->sum('amount');
                $expenseData[] = $expense;

                $current->addDay();
            }
        } else {
            // Monthly breakdown
            $current = $start->copy()->startOfMonth();

            while ($current <= $end) {
                $monthEnd = $current->copy()->endOfMonth();
                // Clamp to actual start/end
                $clampStart = $current->gt($start) ? $current : $start;
                $clampEnd = $monthEnd->lt($end) ? $monthEnd : $end;

                if ($clampStart > $clampEnd) {
                    $current->addMonth();
                    continue;
                }

                $categories[] = $current->format('Y-m-01');

                $pos = Transaction::where('type', 'SALE')->where('status', 'COMPLETED')
                    ->whereDate('date', '>=', $clampStart)
                    ->whereDate('date', '<=', $clampEnd)
                    ->sum('totalAmount');

                $manualIncome = FinancialTransaction::income()
                    ->whereDate('transactionDate', '>=', $clampStart)
                    ->whereDate('transactionDate', '<=', $clampEnd)
                    ->sum('amount');

                $incomeData[] = $pos + $manualIncome;

                $expense = FinancialTransaction::expense()
                    ->whereDate('transactionDate', '>=', $clampStart)
                    ->whereDate('transactionDate', '<=', $clampEnd)
                    ->sum('amount');
                $expenseData[] = $expense;

                $current->addMonth();
            }
        }

        $granularity = ($diffInDays <= 1) ? 'hourly' : (($diffInDays <= 35) ? 'daily' : 'monthly');

        $this->chartData = [
            'categories' => $categories,
            'income' => $incomeData,
            'expense' => $expenseData,
            'granularity' => $granularity
        ];
    }

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

        return $query->whereDate('transactionDate', '>=', $this->startDate)
            ->whereDate('transactionDate', '<=', $this->endDate)
            ->sum('amount');
    }

    public function getTotalCogsProperty()
    {
        // COGS hanya untuk tracking margin, bukan pengeluaran riil
        $query = Transaction::where('type', 'SALE')
            ->where('status', 'COMPLETED')
            ->join('transaction_items', 'transactions.id', '=', 'transaction_items.transactionId');

        $cogs = $query->whereDate('transactions.date', '>=', $this->startDate)
            ->whereDate('transactions.date', '<=', $this->endDate)
            ->sum('transaction_items.totalCogs');

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

        $expenses = $query->whereDate('transactionDate', '>=', $this->startDate)
            ->whereDate('transactionDate', '<=', $this->endDate)
            ->sum('amount');

        return $expenses ?? 0;
    }

    // Pemasukan Lain-lain (KECUALI Suntikan Modal & Omset Historis)
    public function getOtherIncomeProperty()
    {
        $query = FinancialTransaction::income()
            ->whereNotIn('category', ['Suntikan Modal', 'Omset Penjualan (Historis)']);

        $income = $query->whereDate('transactionDate', '>=', $this->startDate)
            ->whereDate('transactionDate', '<=', $this->endDate)
            ->sum('amount');

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
        return match ($this->dateFilter) {
            'today' => 'vs Kemarin',
            'yesterday' => 'vs Hari Sebelumnya',
            'this_week' => 'vs Minggu Lalu',
            'this_month' => 'vs Bulan Lalu',
            'last_month' => 'vs Bulan Sebelumnya',
            'this_year' => 'vs Tahun Lalu',
            'custom' => 'vs Periode Sebelumnya',
            default => 'vs Periode Sebelumnya',
        };
    }

    private function getCurrentPeriodRange()
    {
        return [
            \Carbon\Carbon::parse($this->startDate),
            \Carbon\Carbon::parse($this->endDate)
        ];
    }

    private function getPreviousPeriodRange()
    {
        $start = \Carbon\Carbon::parse($this->startDate);
        $end = \Carbon\Carbon::parse($this->endDate);
        $diff = $start->diffInDays($end) + 1;

        return [
            $start->copy()->subDays($diff),
            $end->copy()->subDays($diff)
        ];
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
            ->whereDate('transactions.date', '>=', $this->startDate)
            ->whereDate('transactions.date', '<=', $this->endDate)
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
