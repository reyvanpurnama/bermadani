<?php

namespace App\Livewire\Admin;

use App\Models\Loan;
use App\Models\Member;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class LoanIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $filterStatus = 'ACTIVE';
    public $filterSource = '';
    public $sortBy = 'priority';
    public $sortDirection = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'filterStatus' => ['except' => 'ACTIVE'],
        'filterSource' => ['except' => ''],
        'sortBy' => ['except' => 'priority'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function updatingFilterSource(): void
    {
        $this->resetPage();
    }

    public function updatingSortBy(): void
    {
        $this->resetPage();
    }

    public function updatingSortDirection(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->filterStatus = 'ACTIVE';
        $this->filterSource = '';
        $this->sortBy = 'priority';
        $this->sortDirection = 'desc';
        $this->resetPage();
    }

    public function toggleSortDirection(): void
    {
        $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
    }

    private function loansQuery()
    {
        return Loan::query()
            ->with('member:id,name,nomorAnggota')
            ->when($this->search, function ($query) {
                $query->whereHas('member', function ($memberQuery) {
                    $memberQuery->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('nomorAnggota', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterStatus && $this->filterStatus !== 'ALL', fn($query) => $query->where('status', $this->filterStatus))
            ->when($this->filterSource, fn($query) => $query->where('loanSource', $this->filterSource));
    }

    public function getLoansProperty()
    {
        return $this->applySorting($this->loansQuery())->paginate(12);
    }

    private function applySorting($query)
    {
        $direction = $this->sortDirection === 'asc' ? 'asc' : 'desc';

        return match ($this->sortBy) {
            'member_name' => $query
                ->orderBy(
                    Member::select('name')
                        ->whereColumn('members.id', 'loans.member_id')
                        ->limit(1),
                    $direction
                )
                ->orderBy('startDate', 'desc'),
            'member_number' => $query
                ->orderBy(
                    Member::select('nomorAnggota')
                        ->whereColumn('members.id', 'loans.member_id')
                        ->limit(1),
                    $direction
                )
                ->orderBy('startDate', 'desc'),
            'amount' => $query->orderBy('amount', $direction)->orderBy('startDate', 'desc'),
            'monthly_payment' => $query->orderBy('monthlyPayment', $direction)->orderBy('startDate', 'desc'),
            'remaining_amount' => $query->orderBy('remainingAmount', $direction)->orderBy('startDate', 'desc'),
            'tenor' => $query->orderBy('tenor', $direction)->orderBy('startDate', 'desc'),
            'progress' => $query
                ->orderByRaw('COALESCE((paid_installments / NULLIF(tenor, 0)), 0) ' . $direction)
                ->orderBy('startDate', 'desc'),
            'status' => $query
                ->orderByRaw(
                    "CASE status
                        WHEN 'OVERDUE' THEN 1
                        WHEN 'ACTIVE' THEN 2
                        WHEN 'PENDING' THEN 3
                        WHEN 'COMPLETED' THEN 4
                        WHEN 'REJECTED' THEN 5
                        ELSE 6
                     END " . $direction
                )
                ->orderBy('startDate', 'desc'),
            'source' => $query
                ->orderByRaw(
                    "CASE loanSource
                        WHEN 'BERMADANI' THEN 1
                        WHEN 'BMT_ITQAN' THEN 2
                        ELSE 3
                     END " . $direction
                )
                ->orderBy('startDate', 'desc'),
            'start_date' => $query->orderBy('startDate', $direction)->orderByDesc('id'),
            'end_date' => $query
                ->orderByRaw('CASE WHEN endDate IS NULL THEN 1 ELSE 0 END')
                ->orderBy('endDate', $direction)
                ->orderBy('startDate', 'desc'),
            default => $query
                ->orderByRaw("CASE WHEN status = 'OVERDUE' THEN 0 ELSE 1 END")
                ->orderByDesc('startDate')
                ->orderByDesc('id'),
        };
    }

    public function getStatsProperty(): array
    {
        $activeStatuses = ['ACTIVE', 'OVERDUE'];

        return [
            'activeLoans' => Loan::whereIn('status', $activeStatuses)->count(),
            'outstandingTotal' => Loan::whereIn('status', $activeStatuses)->sum('remainingAmount'),
            'monthlyInstallmentTotal' => Loan::whereIn('status', $activeStatuses)->sum('monthlyPayment'),
            'activeDebtors' => Loan::whereIn('status', $activeStatuses)->distinct('member_id')->count('member_id'),
        ];
    }

    public function isOverdueWarning(Loan $loan): bool
    {
        if ($loan->status === 'COMPLETED') {
            return false;
        }

        if (!$loan->endDate) {
            return false;
        }

        return Carbon::parse($loan->endDate)->isPast();
    }

    public function getProgressPercentage(Loan $loan): int
    {
        $tenor = max(1, (int) ($loan->tenor ?? 1));
        $paidInstallments = max(0, (int) ($loan->paid_installments ?? 0));

        return (int) min(100, round(($paidInstallments / $tenor) * 100));
    }

    public function formatLoanSource(?string $source): string
    {
        return match ($source) {
            'BMT_ITQAN' => 'BMT Itqan',
            'BERMADANI' => 'Bermadani',
            default => '-'
        };
    }

    public function sourceBadgeClass(?string $source): string
    {
        return match ($source) {
            'BMT_ITQAN' => 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300 border-blue-100 dark:border-blue-800',
            'BERMADANI' => 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300 border-indigo-100 dark:border-indigo-800',
            default => 'bg-slate-50 text-slate-600 dark:bg-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-600',
        };
    }

    public function statusBadgeClass(?string $status): string
    {
        return match ($status) {
            'ACTIVE' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300 border-emerald-100 dark:border-emerald-800',
            'OVERDUE' => 'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300 border-amber-100 dark:border-amber-800',
            'COMPLETED' => 'bg-slate-50 text-slate-700 dark:bg-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-600',
            'PENDING' => 'bg-sky-50 text-sky-700 dark:bg-sky-900/30 dark:text-sky-300 border-sky-100 dark:border-sky-800',
            'REJECTED' => 'bg-rose-50 text-rose-700 dark:bg-rose-900/30 dark:text-rose-300 border-rose-100 dark:border-rose-800',
            default => 'bg-slate-50 text-slate-700 dark:bg-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-600',
        };
    }

    public function formatCompactCurrency(float|int|null $amount): string
    {
        $value = (float) ($amount ?? 0);
        $abs = abs($value);

        if ($abs >= 1000000000) {
            return 'Rp ' . $this->trimDecimal(number_format($value / 1000000000, 1, ',', '.')) . ' M';
        }

        if ($abs >= 1000000) {
            return 'Rp ' . $this->trimDecimal(number_format($value / 1000000, 1, ',', '.')) . ' jt';
        }

        if ($abs >= 1000) {
            return 'Rp ' . $this->trimDecimal(number_format($value / 1000, 1, ',', '.')) . ' rb';
        }

        return 'Rp ' . number_format($value, 0, ',', '.');
    }

    public function formatDateRangeShort($startDate, $endDate): string
    {
        $start = $startDate ? Carbon::parse($startDate)->format('d M y') : '-';
        $end = $endDate ? Carbon::parse($endDate)->format('d M y') : '-';

        return $start . ' - ' . $end;
    }

    private function trimDecimal(string $value): string
    {
        return str_ends_with($value, ',0') ? substr($value, 0, -2) : $value;
    }

    public function render()
    {
        return view('livewire.admin.loan-index', [
            'loans' => $this->loans,
            'stats' => $this->stats,
        ])->layout('layouts.admin');
    }
}
