<?php

namespace App\Livewire\Membership;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\Member;
use App\Models\Transaction;

#[Layout('layouts.membership')]
class History extends Component
{
    use WithPagination;

    public $member;
    public $filterMonth = '';
    public $stats = [];

    public function mount()
    {
        $user = auth()->user();
        $this->member = Member::where('userId', $user->id)->first();
        $this->filterMonth = now()->format('Y-m');
        $this->loadStats();
    }

    public function updatedFilterMonth()
    {
        $this->loadStats();
        $this->resetPage();
    }

    public function loadStats()
    {
        if (!$this->member)
            return;

        $query = Transaction::where('memberId', $this->member->id);

        if ($this->filterMonth) {
            $query->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$this->filterMonth]);
        }

        $this->stats = [
            'totalSpent' => $query->sum('totalAmount'),
            'totalTransactions' => $query->count(),
        ];
    }

    public function render()
    {
        $transactions = collect();

        if ($this->member) {
            $query = Transaction::where('memberId', $this->member->id)
                ->orderBy('created_at', 'desc');

            if ($this->filterMonth) {
                $query->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$this->filterMonth]);
            }

            $transactions = $query->paginate(10);
        }

        return view('livewire.membership.history', [
            'transactions' => $transactions,
        ]);
    }
}
