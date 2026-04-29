<?php

namespace App\Livewire\Admin;

use App\Models\Loan;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class LoanShow extends Component
{
    public Loan $loan;

    public function mount(Loan $loan): void
    {
        $this->loan = $loan->load([
            'member:id,name,nomorAnggota,unitKerja,status',
            'payments' => fn($query) => $query->orderByDesc('paymentDate')->orderByDesc('id'),
        ]);
    }

    public function isOverdueWarning(): bool
    {
        if ($this->loan->status === 'COMPLETED') {
            return false;
        }

        if (!$this->loan->endDate) {
            return false;
        }

        return Carbon::parse($this->loan->endDate)->isPast();
    }

    public function getProgressPercentage(): int
    {
        $tenor = max(1, (int) ($this->loan->tenor ?? 1));
        $paidInstallments = max(0, (int) ($this->loan->paid_installments ?? 0));

        return (int) min(100, round(($paidInstallments / $tenor) * 100));
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

    public function formatLoanSource(?string $source): string
    {
        return match ($source) {
            'BMT_ITQAN' => 'BMT Itqan',
            'BERMADANI' => 'Bermadani',
            default => '-',
        };
    }

    public function formatCurrency(float|int|null $amount): string
    {
        return 'Rp ' . number_format((float) ($amount ?? 0), 0, ',', '.');
    }

    public function render()
    {
        $progress = $this->getProgressPercentage();
        $totalRecordedPayments = (float) $this->loan->payments->sum('amount');
        $lastPayment = $this->loan->payments->first();

        return view('livewire.admin.loan-show', [
            'progress' => $progress,
            'totalRecordedPayments' => $totalRecordedPayments,
            'lastPayment' => $lastPayment,
            'isOverdueWarning' => $this->isOverdueWarning(),
        ]);
    }
}
