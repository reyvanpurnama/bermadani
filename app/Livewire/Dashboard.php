<?php

namespace App\Livewire;

use App\Models\Category;
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
