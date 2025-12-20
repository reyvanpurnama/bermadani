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

    public $viewMode = 'list'; // 'list' or 'detail'
    public $filterYear;
    
    public $selectedMonth;
    public $selectedTransactions = [];
    public $selectAll = false;
    public $processing = false;
    
    // New property to toggle month view if needed, but default is hidden
    public $showMonthPicker = false;

    protected $listeners = ['refreshComponent' => '$refresh'];

    public function mount()
    {
        // Default to current month
        $this->selectedMonth = now()->format('Y-m');
        $this->filterYear = now()->year;
    }

    public function setViewMode($mode)
    {
        $this->viewMode = $mode;
    }

    public function selectMonth($month)
    {
        $this->selectedMonth = $month;
        $this->viewMode = 'detail';
    }

    public function getMonthlyHistoryProperty()
    {
        $history = [];
        // Show months for the selected year (1-12)
        // Or maybe up to current month + 1 if current year? 
        // Let's just show all 12 months for the year.
        
        for ($m = 12; $m >= 1; $m--) {
            $date = Carbon::create($this->filterYear, $m, 1);
            
            // Query stats for this month
            $query = SimpananTransaction::where('type', 'WAJIB')
                ->where('transactionType', 'SETOR')
                ->whereYear('created_at', $this->filterYear)
                ->whereMonth('created_at', $m);
                
            $totalCount = (clone $query)->count();
            $pendingCount = (clone $query)->where('status', 'PENDING')->count();
            $totalAmount = (clone $query)->sum('amount');
            
            // Determine status
            $status = 'EMPTY';
            if ($totalCount > 0) {
                $status = $pendingCount > 0 ? 'PENDING' : 'COMPLETED';
            }
            
            // Get approver info
            $approver = null;
            $approvedAt = null;
            if ($status === 'COMPLETED') {
                $lastTx = (clone $query)->whereNotNull('approvedBy')->latest('approvedAt')->first();
                if ($lastTx && $lastTx->approver) {
                    $approver = $lastTx->approver->name;
                    $approvedAt = $lastTx->approvedAt;
                }
            }
            
            $history[] = [
                'date' => $date->format('Y-m'),
                'monthName' => $date->translatedFormat('F'),
                'status' => $status,
                'totalAmount' => $totalAmount,
                'count' => $totalCount,
                'pending' => $pendingCount,
                'approver' => $approver,
                'approvedAt' => $approvedAt,
                'isFuture' => $date->endOfMonth()->isFuture(),
                'isPast' => $date->endOfMonth()->isPast(),
            ];
        }
        
        return $history;
    }

    public function toggleMonthPicker()
    {
        $this->showMonthPicker = !$this->showMonthPicker;
    }

    public function nextMonth()
    {
        $this->selectedMonth = Carbon::createFromFormat('Y-m', $this->selectedMonth)->addMonth()->format('Y-m');
    }

    public function prevMonth()
    {
        $this->selectedMonth = Carbon::createFromFormat('Y-m', $this->selectedMonth)->subMonth()->format('Y-m');
    }

    public function getDebitStatusProperty()
    {
        $month = Carbon::createFromFormat('Y-m', $this->selectedMonth);
        
        $total = SimpananTransaction::where('type', 'WAJIB')
            ->where('transactionType', 'SETOR')
            ->whereYear('created_at', $month->year)
            ->whereMonth('created_at', $month->month)
            ->count();

        if ($total === 0) {
            return 'EMPTY';
        }

        $pending = SimpananTransaction::where('type', 'WAJIB')
            ->where('transactionType', 'SETOR')
            ->whereYear('created_at', $month->year)
            ->whereMonth('created_at', $month->month)
            ->where('status', 'PENDING')
            ->count();

        if ($pending > 0) {
            return 'PENDING';
        }

        return 'COMPLETED';
    }

    public function generateDebit()
    {
        $this->processing = true;

        try {
            $month = Carbon::createFromFormat('Y-m', $this->selectedMonth);

            // Check if already generated
            $existing = SimpananTransaction::where('type', 'WAJIB')
                ->where('transactionType', 'SETOR')
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();

            if ($existing > 0) {
                session()->flash('warning', "Auto-debit untuk bulan {$month->format('F Y')} sudah pernah di-generate ({$existing} transaksi). Cek tab Pending di bawah.");
                $this->processing = false;
                return;
            }

            // Get all active members
            $members = Member::where('status', 'ACTIVE')
                ->whereDate('joinDate', '<=', $month->endOfMonth())
                ->get();

            if ($members->isEmpty()) {
                session()->flash('error', 'Tidak ada member aktif yang ditemukan.');
                $this->processing = false;
                return;
            }

            $processed = 0;

            DB::beginTransaction();

            foreach ($members as $member) {
                // Calculate new balance
                $currentBalance = $member->simpananWajib ?? 0;
                $newBalance = $currentBalance + $member->monthly_wajib_amount;

                // Create transaction
                SimpananTransaction::create([
                    'memberId' => $member->id,
                    'type' => 'WAJIB',
                    'transactionType' => 'SETOR',
                    'amount' => $member->monthly_wajib_amount,
                    'balanceAfter' => $newBalance,
                    'notes' => "Auto-debit simpanan wajib - {$month->format('F Y')}",
                    'processedBy' => auth()->id(),
                    'status' => 'PENDING',
                    'created_at' => $month,
                    'updated_at' => $month,
                ]);

                // Update last debit date
                $member->update([
                    'last_wajib_debit_date' => $month->format('Y-m-d')
                ]);

                $processed++;
            }

            DB::commit();

            session()->flash('success', "✅ Berhasil generate {$processed} transaksi auto-debit untuk {$month->format('F Y')}! Silahkan review dan approve.");
            $this->dispatch('refreshComponent');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal generate auto-debit: ' . $e->getMessage());
        } finally {
            $this->processing = false;
        }
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
            ->where('type', 'WAJIB')
            ->where('transactionType', 'SETOR')
            ->whereYear('created_at', $month->year)
            ->whereMonth('created_at', $month->month)
            ->orderBy('created_at', 'desc')
            ->paginate(50);
    }

    public function getStatsProperty()
    {
        $month = Carbon::createFromFormat('Y-m', $this->selectedMonth);

        $baseQuery = SimpananTransaction::where('type', 'WAJIB')
            ->whereYear('created_at', $month->year)
            ->whereMonth('created_at', $month->month);

        $pending = (clone $baseQuery)->where('status', 'PENDING')->count();
        $approved = (clone $baseQuery)->where('status', 'APPROVED')->count();
        
        // Total amount of ALL generated bills (Pending + Approved)
        $totalAmount = (clone $baseQuery)->whereIn('status', ['PENDING', 'APPROVED'])->sum('amount');

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
