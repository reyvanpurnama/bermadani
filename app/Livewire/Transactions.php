<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class Transactions extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $paymentFilter = '';
    public $dateFrom = '';
    public $dateTo = '';
    
    protected $queryString = ['search', 'statusFilter', 'paymentFilter'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingPaymentFilter()
    {
        $this->resetPage();
    }

    public function getTransactionsProperty()
    {
        $query = Transaction::with(['member', 'items.product', 'user'])
            ->where('type', 'SALE')
            ->where('isProduction', true);

        // Kasir can only see their own transactions
        if (auth()->user()->isKasir()) {
            $query->where('userId', auth()->id());
        }

        // Search by invoice number
        if ($this->search) {
            $query->where('invoiceNumber', 'like', '%' . $this->search . '%');
        }

        // Filter by status
        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        // Filter by payment method
        if ($this->paymentFilter) {
            $query->where('paymentMethod', $this->paymentFilter);
        }

        // Filter by date range
        if ($this->dateFrom) {
            $query->where('date', '>=', $this->dateFrom . ' 00:00:00');
        }

        if ($this->dateTo) {
            $query->where('date', '<=', $this->dateTo . ' 23:59:59');
        }

        return $query->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(10);
    }

    public function getStatsProperty()
    {
        $query = Transaction::where('type', 'SALE')
            ->where('status', 'COMPLETED')
            ->where('isProduction', true);

        // Kasir can only see their own stats
        if (auth()->user()->isKasir()) {
            $query->where('userId', auth()->id());
        }

        // Apply date filters if set
        if ($this->dateFrom) {
            $query->where('date', '>=', $this->dateFrom . ' 00:00:00');
        }

        if ($this->dateTo) {
            $query->where('date', '<=', $this->dateTo . ' 23:59:59');
        }

        $totalTransactions = $query->count();
        $totalRevenue = $query->sum('totalAmount');

        // Today's revenue
        $todayQuery = Transaction::where('type', 'SALE')
            ->where('status', 'COMPLETED')
            ->where('isProduction', true)
            ->where('date', '>=', today()->startOfDay())
            ->where('date', '<=', today()->endOfDay());

        // Kasir can only see their own today's revenue
        if (auth()->user()->isKasir()) {
            $todayQuery->where('userId', auth()->id());
        }

        $todayRevenue = $todayQuery->sum('totalAmount');

        // Average basket (average transaction amount)
        $averageBasket = $totalTransactions > 0 ? $totalRevenue / $totalTransactions : 0;

        return [
            'total_transactions' => $totalTransactions,
            'total_revenue' => $totalRevenue,
            'today_revenue' => $todayRevenue,
            'average_basket' => $averageBasket,
        ];
    }

    public function exportTransactions()
    {
        // TODO: Implement export functionality
        session()->flash('info', 'Fitur export akan segera tersedia.');
    }

    public function render()
    {
        return view('livewire.transactions', [
            'transactions' => $this->transactions,
            'stats' => $this->stats,
        ]);
    }
}
