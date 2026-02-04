<?php

namespace App\Livewire\Member;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Member;
use App\Models\SimpananTransaction;
use App\Models\Membership\TransferHistory; // unused
use App\Models\Loan;
use App\Models\Transaction;

#[Layout('layouts.member')]
class Dashboard extends Component
{
    public $member;
    public $recentTransactions = [];
    public $recentSimpanan = [];
    public $showBalance = true;
    public $unreadTransfers = [];
    public $unreadCount = 0;
    public $activeLoans = [];

    public function mount()
    {
        $user = auth()->user();
        $this->member = Member::where('userId', $user->id)->first();

        if ($this->member) {
            // Get Active Loans
            $this->activeLoans = Loan::where('member_id', $this->member->id)
                ->where('status', 'ACTIVE')
                ->latest('startDate')
                ->get();
            // Recent shopping transactions
            $this->recentTransactions = Transaction::where('memberId', $this->member->id)
                ->latest()
                ->take(5)
                ->get();

            // Recent simpanan activities
            $this->recentSimpanan = SimpananTransaction::where('memberId', $this->member->id)
                ->where('status', 'APPROVED')
                ->latest()
                ->take(5)
                ->get();

            // Check unread transfers (today only)
            $this->unreadTransfers = SimpananTransaction::where('memberId', $this->member->id)
                ->where('transactionType', 'TRANSFER_IN')
                ->where('isRead', false)
                ->whereDate('created_at', today())
                ->with('relatedMember')
                ->latest()
                ->get();

            $this->unreadCount = $this->unreadTransfers->count();
        }
    }

    public function toggleBalance()
    {
        $this->showBalance = !$this->showBalance;
    }

    public function render()
    {
        return view('livewire.member.dashboard');
    }
}
