<?php

namespace App\Livewire\Admin;

use App\Models\Member;
use App\Models\SimpananTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class MonthlyDebitApproval extends Component
{
    use WithPagination;

    public $selectedMonth;
    public $selectedTransactions = [];
    public $selectAll = false;

    protected $listeners = ['refreshComponent' => '$refresh'];

    public function mount()
    {
        // Default to current month
        $this->selectedMonth = now()->format('Y-m');
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedTransactions = $this->getPendingTransactions()
                ->pluck('id')
                ->toArray();
        } else {
            $this->selectedTransactions = [];
        }
    }

    public function getPendingTransactionsProperty()
    {
        $month = Carbon::createFromFormat('Y-m', $this->selectedMonth);

        return SimpananTransaction::with(['member.user'])
            ->where('status', 'PENDING')
            ->where('type', 'WAJIB')
            ->where('transactionType', 'SETOR')
            ->whereYear('created_at', $month->year)
            ->whereMonth('created_at', $month->month)
            ->where('notes', 'LIKE', 'Auto-debit simpanan wajib%')
            ->orderBy('created_at', 'desc')
            ->paginate(50);
    }

    public function getStatsProperty()
    {
        $month = Carbon::createFromFormat('Y-m', $this->selectedMonth);

        $pending = SimpananTransaction::where('status', 'PENDING')
            ->where('type', 'WAJIB')
            ->whereYear('created_at', $month->year)
            ->whereMonth('created_at', $month->month)
            ->where('notes', 'LIKE', 'Auto-debit simpanan wajib%')
            ->count();

        $approved = SimpananTransaction::where('status', 'APPROVED')
            ->where('type', 'WAJIB')
            ->whereYear('created_at', $month->year)
            ->whereMonth('created_at', $month->month)
            ->where('notes', 'LIKE', 'Auto-debit simpanan wajib%')
            ->count();

        $totalAmount = SimpananTransaction::where('status', 'PENDING')
            ->where('type', 'WAJIB')
            ->whereYear('created_at', $month->year)
            ->whereMonth('created_at', $month->month)
            ->where('notes', 'LIKE', 'Auto-debit simpanan wajib%')
            ->sum('amount');

        return [
            'pending' => $pending,
            'approved' => $approved,
            'totalAmount' => $totalAmount,
        ];
    }

    public function approveSelected()
    {
        if (empty($this->selectedTransactions)) {
            session()->flash('error', 'Pilih minimal 1 transaksi untuk disetujui');
            return;
        }

        DB::beginTransaction();
        try {
            foreach ($this->selectedTransactions as $transactionId) {
                $transaction = SimpananTransaction::find($transactionId);
                
                if ($transaction && $transaction->status === 'PENDING') {
                    // Update transaction status
                    $transaction->update([
                        'status' => 'APPROVED',
                        'approvedBy' => auth()->id(),
                        'approvedAt' => now(),
                    ]);

                    // Update member's simpananWajib balance
                    $member = $transaction->member;
                    $member->increment('simpananWajib', $transaction->amount);
                }
            }

            DB::commit();
            
            $count = count($this->selectedTransactions);
            session()->flash('success', "Berhasil menyetujui {$count} transaksi");
            
            $this->selectedTransactions = [];
            $this->selectAll = false;
            $this->dispatch('refreshComponent');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal menyetujui transaksi: ' . $e->getMessage());
        }
    }

    public function rejectSelected()
    {
        if (empty($this->selectedTransactions)) {
            session()->flash('error', 'Pilih minimal 1 transaksi untuk ditolak');
            return;
        }

        DB::beginTransaction();
        try {
            SimpananTransaction::whereIn('id', $this->selectedTransactions)
                ->where('status', 'PENDING')
                ->update([
                    'status' => 'REJECTED',
                    'approvedBy' => auth()->id(),
                    'approvedAt' => now(),
                    'rejectionReason' => 'Ditolak oleh admin',
                ]);

            DB::commit();
            
            $count = count($this->selectedTransactions);
            session()->flash('success', "Berhasil menolak {$count} transaksi");
            
            $this->selectedTransactions = [];
            $this->selectAll = false;
            $this->dispatch('refreshComponent');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal menolak transaksi: ' . $e->getMessage());
        }
    }

    public function approveAll()
    {
        $transactions = $this->getPendingTransactions()->pluck('id')->toArray();
        $this->selectedTransactions = $transactions;
        $this->approveSelected();
    }

    private function getPendingTransactions()
    {
        $month = Carbon::createFromFormat('Y-m', $this->selectedMonth);

        return SimpananTransaction::where('status', 'PENDING')
            ->where('type', 'WAJIB')
            ->where('transactionType', 'SETOR')
            ->whereYear('created_at', $month->year)
            ->whereMonth('created_at', $month->month)
            ->where('notes', 'LIKE', 'Auto-debit simpanan wajib%')
            ->get();
    }

    public function render()
    {
        return view('livewire.admin.monthly-debit-approval', [
            'transactions' => $this->pendingTransactions,
            'stats' => $this->stats,
        ])->layout('layouts.app');
    }
}
