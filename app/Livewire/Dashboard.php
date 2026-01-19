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
    public $dateFilter = 'this_month';
    public $startDate;
    public $endDate;

    public function mount()
    {
        $this->filter = 'today';
        $this->dateFilter = 'this_month';
        $this->startDate = now()->startOfMonth()->toDateString();
        $this->endDate = now()->endOfMonth()->toDateString();
    }

    public function setFilter($filter)
    {
        $this->filter = $filter;
    }

    public function setDateFilter($filter)
    {
        $this->dateFilter = $filter;
        
        match($filter) {
            'today' => $this->setDateRange(today(), today()),
            'yesterday' => $this->setDateRange(today()->subDay(), today()->subDay()),
            'this_week' => $this->setDateRange(now()->startOfWeek(), now()->endOfWeek()),
            'this_month' => $this->setDateRange(now()->startOfMonth(), now()->endOfMonth()),
            'last_month' => $this->setDateRange(now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()),
            'this_year' => $this->setDateRange(now()->startOfYear(), now()->endOfYear()),
            default => null,
        };
    }

    protected function setDateRange($start, $end)
    {
        $this->startDate = $start->toDateString();
        $this->endDate = $end->toDateString();
    }

    public function getTotalSalesProperty()
    {
        $query = Transaction::where('type', 'SALE')->where('status', 'COMPLETED');
        
        return match($this->filter) {
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
        
        return match($this->filter) {
            'today' => $query->whereDate('date', today())->count(),
            'week' => $query->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'month' => $query->whereMonth('date', now()->month)->whereYear('date', now()->year)->count(),
            'year' => $query->whereYear('date', now()->year)->count(),
            default => $query->whereDate('date', today())->count(),
        };
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
        
        return match($this->filter) {
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
        
        $cogs = match($this->filter) {
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
        
        if ($totalRevenue == 0) return 0;
        
        // Margin = (POS Sales - COGS) + Historical Sales
        // Asumsi: Historical Sales tidak ada COGS (margin 100%)
        $totalMargin = ($posSales - $cogs) + $historicalSales;
        
        return round(($totalMargin / $totalRevenue) * 100, 1);
    }

    public function getOperatingExpensesProperty()
    {
        // Ambil total pengeluaran dari financial_transactions berdasarkan filter
        // EXCLUDE: Pembayaran Supplier Konsinyasi (karena itu COGS, bukan operating expense)
        $query = FinancialTransaction::expense()
            ->where('category', '!=', 'Pembayaran Supplier Konsinyasi');
        
        $expenses = match($this->filter) {
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
        
        $income = match($this->filter) {
            'today' => $query->whereDate('transactionDate', today())->sum('amount'),
            'week' => $query->whereBetween('transactionDate', [now()->startOfWeek(), now()->endOfWeek()])->sum('amount'),
            'month' => $query->whereMonth('transactionDate', now()->month)->whereYear('transactionDate', now()->year)->sum('amount'),
            'year' => $query->whereYear('transactionDate', now()->year)->sum('amount'),
            default => $query->whereDate('transactionDate', today())->sum('amount'),
        };

        return $income ?? 0;
    }

    // COGS Konsinyasi (pembayaran ke supplier untuk barang konsinyasi yang sudah dibayar)
    public function getConsignmentCogsProperty()
    {
        $query = FinancialTransaction::expense()
            ->where('category', 'Pembayaran Supplier Konsinyasi');
        
        $cogs = match($this->filter) {
            'today' => $query->whereDate('transactionDate', today())->sum('amount'),
            'week' => $query->whereBetween('transactionDate', [now()->startOfWeek(), now()->endOfWeek()])->sum('amount'),
            'month' => $query->whereMonth('transactionDate', now()->month)->whereYear('transactionDate', now()->year)->sum('amount'),
            'year' => $query->whereYear('transactionDate', now()->year)->sum('amount'),
            default => $query->whereDate('transactionDate', today())->sum('amount'),
        };

        return $cogs ?? 0;
    }

    public function getOperatingMarginPercentProperty()
    {
        $sales = $this->totalSales;
        
        if ($sales == 0) return 0;
        
        return round(($this->operatingExpenses / $sales) * 100, 1);
    }

    public function getNetProfitProperty()
    {
        // Laba Bersih = Margin Kotor + Pemasukan Lain-lain - COGS Konsinyasi - Pengeluaran Operasional
        // COGS Konsinyasi = pembayaran ke supplier untuk barang konsinyasi
        return max(0, $this->grossProfit + $this->otherIncome - $this->consignmentCogs - $this->operatingExpenses);
    }

    // Saldo Kasir = Uang riil di kasir setelah semua transaksi
    public function getCashOnHandProperty()
    {
        // Total Income (POS Sales + Historical Sales + Other Income)
        $totalIncome = $this->totalSales + $this->historicalSales + $this->otherIncome;
        
        // Total Outflow (COGS Konsinyasi + Operating Expenses)
        $totalOutflow = $this->consignmentCogs + $this->operatingExpenses;
        
        // Cash on Hand = Total Income - Total Outflow
        return max(0, $totalIncome - $totalOutflow);
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

        if ($previousProfit == 0) return 0;

        return round((($currentProfit - $previousProfit) / abs($previousProfit)) * 100, 1);
    }

    public function getPreviousPeriodLabelProperty()
    {
        return match($this->filter) {
            'today' => 'vs Kemarin',
            'week' => 'vs Minggu Lalu',
            'month' => 'vs Bulan Lalu',
            'year' => 'vs Tahun Lalu',
            default => 'vs Kemarin',
        };
    }

    private function getCurrentPeriodRange()
    {
        return match($this->filter) {
            'today' => [today()->startOfDay(), today()->endOfDay()],
            'week' => [now()->startOfWeek(), now()->endOfWeek()],
            'month' => [now()->startOfMonth(), now()->endOfMonth()],
            'year' => [now()->startOfYear(), now()->endOfYear()],
            default => [today()->startOfDay(), today()->endOfDay()],
        };
    }

    private function getPreviousPeriodRange()
    {
        return match($this->filter) {
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
