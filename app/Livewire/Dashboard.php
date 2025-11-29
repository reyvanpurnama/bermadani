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

    public function mount()
    {
        $this->filter = 'today';
    }

    public function setFilter($filter)
    {
        $this->filter = $filter;
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
        $query = Transaction::where('type', 'SALE')
            ->where('status', 'COMPLETED')
            ->join('transaction_items', 'transactions.id', '=', 'transaction_items.transactionId');
        
        $grossProfit = match($this->filter) {
            'today' => $query->whereDate('transactions.date', today())->sum('transaction_items.grossProfit'),
            'week' => $query->whereBetween('transactions.date', [now()->startOfWeek(), now()->endOfWeek()])->sum('transaction_items.grossProfit'),
            'month' => $query->whereMonth('transactions.date', now()->month)->whereYear('transactions.date', now()->year)->sum('transaction_items.grossProfit'),
            'year' => $query->whereYear('transactions.date', now()->year)->sum('transaction_items.grossProfit'),
            default => $query->whereDate('transactions.date', today())->sum('transaction_items.grossProfit'),
        };

        return $grossProfit ?? 0;
    }

    public function getTotalCogsProperty()
    {
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
        $sales = $this->totalSales;
        $cogs = $this->totalCogs;
        
        if ($sales == 0) return 0;
        
        return round((($sales - $cogs) / $sales) * 100, 1);
    }

    public function getOperatingExpensesProperty()
    {
        // Ambil total pengeluaran dari financial_transactions berdasarkan filter
        $query = FinancialTransaction::expense();
        
        $expenses = match($this->filter) {
            'today' => $query->whereDate('transactionDate', today())->sum('amount'),
            'week' => $query->whereBetween('transactionDate', [now()->startOfWeek(), now()->endOfWeek()])->sum('amount'),
            'month' => $query->whereMonth('transactionDate', now()->month)->whereYear('transactionDate', now()->year)->sum('amount'),
            'year' => $query->whereYear('transactionDate', now()->year)->sum('amount'),
            default => $query->whereDate('transactionDate', today())->sum('amount'),
        };

        return $expenses ?? 0;
    }

    public function getOperatingMarginPercentProperty()
    {
        $sales = $this->totalSales;
        
        if ($sales == 0) return 0;
        
        return round(($this->operatingExpenses / $sales) * 100, 1);
    }

    public function getNetProfitProperty()
    {
        return max(0, $this->grossProfit - $this->operatingExpenses);
    }

    public function getProfitGrowthProperty()
    {
        // Get previous period data
        [$currentStart, $currentEnd] = $this->getCurrentPeriodRange();
        [$previousStart, $previousEnd] = $this->getPreviousPeriodRange();

        $currentProfit = Transaction::where('type', 'SALE')
            ->where('status', 'COMPLETED')
            ->whereBetween('date', [$currentStart, $currentEnd])
            ->join('transaction_items', 'transactions.id', '=', 'transaction_items.transactionId')
            ->sum('transaction_items.grossProfit') ?? 0;

        $previousProfit = Transaction::where('type', 'SALE')
            ->where('status', 'COMPLETED')
            ->whereBetween('date', [$previousStart, $previousEnd])
            ->join('transaction_items', 'transactions.id', '=', 'transaction_items.transactionId')
            ->sum('transaction_items.grossProfit') ?? 0;

        if ($previousProfit == 0) return 0;

        return round((($currentProfit - $previousProfit) / $previousProfit) * 100, 1);
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
