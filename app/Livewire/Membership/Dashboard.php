<?php

namespace App\Livewire\Membership;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Member;
use App\Models\Transaction;
use App\Models\SimpananTransaction;

#[Layout('layouts.membership')]
class Dashboard extends Component
{
    public $member;
    public $recentTransactions = [];
    public $recentSimpanan = [];
    public $showBalance = true;
    public $unreadCount = 0;

    public function mount()
    {
        $user = auth()->user();
        $this->member = Member::where('userId', $user->id)->first();

        if ($this->member) {
            $this->loadRecentTransactions();
            $this->loadRecentSimpanan();
            $this->unreadCount = SimpananTransaction::where('memberId', $this->member->id)
                ->where('isRead', false)
                ->where('transactionType', 'TRANSFER_IN')
                ->count();
        }
    }

    public function toggleBalance()
    {
        $this->showBalance = !$this->showBalance;
    }

    public function loadRecentTransactions()
    {
        $this->recentTransactions = Transaction::where('memberId', $this->member->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
    }

    public function loadRecentSimpanan()
    {
        $this->recentSimpanan = SimpananTransaction::where('memberId', $this->member->id)
            ->where('status', 'APPROVED')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.membership.dashboard');
    }
}
