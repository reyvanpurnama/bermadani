<?php

namespace App\Livewire\Admin;

use App\Models\CashierShift;
use App\Models\Transaction;
use App\Models\User;
use App\Models\ActivityLog;
use Livewire\Component;
use Livewire\WithPagination;

class KasirHistory extends Component
{
    use WithPagination;

    // Filters
    public $selectedKasir = '';
    public $dateFrom = '';
    public $dateTo = '';
    public $shiftStatus = '';
    public $activeTab = 'shifts';

    // Modal
    public $showDetailModal = false;
    public $selectedShift = null;
    public $shiftTransactions = [];

    // Summary
    public $todaySummary = [];

    protected $queryString = [
        'selectedKasir' => ['except' => ''],
        'dateFrom' => ['except' => ''],
        'dateTo' => ['except' => ''],
        'shiftStatus' => ['except' => ''],
        'activeTab' => ['except' => 'shifts'],
    ];

    public function mount()
    {
        $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
        $this->loadTodaySummary();
    }

    public function loadTodaySummary()
    {
        $today = now()->toDateString();
        
        $this->todaySummary = [
            'activeShifts' => CashierShift::where('status', 'OPEN')->count(),
            'completedShifts' => CashierShift::whereDate('check_in_at', $today)->where('status', 'CLOSED')->count(),
            'totalTransactions' => Transaction::whereDate('date', $today)->where('status', 'COMPLETED')->count(),
            'totalSales' => Transaction::whereDate('date', $today)->where('status', 'COMPLETED')->where('type', 'SALE')->sum('totalAmount'),
            'cashSales' => Transaction::whereDate('date', $today)->where('status', 'COMPLETED')->where('type', 'SALE')->where('paymentMethod', 'CASH')->sum('totalAmount'),
            'nonCashSales' => Transaction::whereDate('date', $today)->where('status', 'COMPLETED')->where('type', 'SALE')->where('paymentMethod', '!=', 'CASH')->sum('totalAmount'),
        ];
    }

    public function getKasirListProperty()
    {
        return User::whereIn('role', ['KASIR', 'ADMIN', 'SUPER_ADMIN'])
            ->where('isActive', true)
            ->orderBy('name')
            ->get();
    }

    public function getShiftsProperty()
    {
        return CashierShift::with('user')
            ->when($this->selectedKasir, fn($q) => $q->where('user_id', $this->selectedKasir))
            ->when($this->dateFrom, fn($q) => $q->whereDate('check_in_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn($q) => $q->whereDate('check_in_at', '<=', $this->dateTo))
            ->when($this->shiftStatus, fn($q) => $q->where('status', $this->shiftStatus))
            ->orderBy('check_in_at', 'desc')
            ->paginate(15);
    }

    public function getTransactionsProperty()
    {
        return Transaction::with(['user', 'member', 'items'])
            ->where('type', 'SALE')
            ->where('status', 'COMPLETED')
            ->when($this->selectedKasir, fn($q) => $q->where('userId', $this->selectedKasir))
            ->when($this->dateFrom, fn($q) => $q->whereDate('date', '>=', $this->dateFrom))
            ->when($this->dateTo, fn($q) => $q->whereDate('date', '<=', $this->dateTo))
            ->orderBy('date', 'desc')
            ->paginate(20);
    }

    public function getActivityLogsProperty()
    {
        return ActivityLog::with('user')
            ->whereIn('module', ['Shift', 'Transaction', 'Auth'])
            ->when($this->selectedKasir, fn($q) => $q->where('user_id', $this->selectedKasir))
            ->when($this->dateFrom, fn($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn($q) => $q->whereDate('created_at', '<=', $this->dateTo))
            ->orderBy('created_at', 'desc')
            ->paginate(25);
    }

    public function getKasirSummaryProperty()
    {
        $kasirIds = $this->selectedKasir 
            ? [$this->selectedKasir] 
            : User::whereIn('role', ['KASIR', 'ADMIN', 'SUPER_ADMIN'])->pluck('id')->toArray();

        return User::whereIn('id', $kasirIds)
            ->withCount([
                'cashierShifts as total_shifts' => function($q) {
                    $q->when($this->dateFrom, fn($q) => $q->whereDate('check_in_at', '>=', $this->dateFrom))
                      ->when($this->dateTo, fn($q) => $q->whereDate('check_in_at', '<=', $this->dateTo));
                },
                'cashierShifts as closed_shifts' => function($q) {
                    $q->where('status', 'CLOSED')
                      ->when($this->dateFrom, fn($q) => $q->whereDate('check_in_at', '>=', $this->dateFrom))
                      ->when($this->dateTo, fn($q) => $q->whereDate('check_in_at', '<=', $this->dateTo));
                },
            ])
            ->withSum([
                'cashierShifts as total_sales_sum' => function($q) {
                    $q->where('status', 'CLOSED')
                      ->when($this->dateFrom, fn($q) => $q->whereDate('check_in_at', '>=', $this->dateFrom))
                      ->when($this->dateTo, fn($q) => $q->whereDate('check_in_at', '<=', $this->dateTo));
                }
            ], 'total_sales')
            ->withSum([
                'cashierShifts as total_difference_sum' => function($q) {
                    $q->where('status', 'CLOSED')
                      ->when($this->dateFrom, fn($q) => $q->whereDate('check_in_at', '>=', $this->dateFrom))
                      ->when($this->dateTo, fn($q) => $q->whereDate('check_in_at', '<=', $this->dateTo));
                }
            ], 'difference')
            ->withCount([
                'transactions as total_transactions' => function($q) {
                    $q->where('type', 'SALE')
                      ->where('status', 'COMPLETED')
                      ->when($this->dateFrom, fn($q) => $q->whereDate('date', '>=', $this->dateFrom))
                      ->when($this->dateTo, fn($q) => $q->whereDate('date', '<=', $this->dateTo));
                }
            ])
            ->get();
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function viewShiftDetail($shiftId)
    {
        $this->selectedShift = CashierShift::with('user')->find($shiftId);
        
        if ($this->selectedShift) {
            $query = Transaction::with(['user', 'member', 'items.product'])
                ->where('type', 'SALE')
                ->where('status', 'COMPLETED')
                ->where('date', '>=', $this->selectedShift->check_in_at);
            
            if ($this->selectedShift->check_out_at) {
                $query->where('date', '<=', $this->selectedShift->check_out_at);
            }
            
            $this->shiftTransactions = $query->orderBy('date', 'desc')->get();
            $this->showDetailModal = true;
        }
    }

    public function closeDetailModal()
    {
        $this->showDetailModal = false;
        $this->selectedShift = null;
        $this->shiftTransactions = [];
    }

    public function resetFilters()
    {
        $this->selectedKasir = '';
        $this->shiftStatus = '';
        $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
        $this->resetPage();
    }

    public function updatedSelectedKasir()
    {
        $this->resetPage();
    }

    public function updatedDateFrom()
    {
        $this->resetPage();
    }

    public function updatedDateTo()
    {
        $this->resetPage();
    }

    public function updatedShiftStatus()
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.admin.kasir-history', [
            'shifts' => $this->shifts,
            'transactions' => $this->transactions,
            'activityLogs' => $this->activityLogs,
            'kasirList' => $this->kasirList,
            'kasirSummary' => $this->kasirSummary,
        ])->layout('layouts.admin');
    }
}
