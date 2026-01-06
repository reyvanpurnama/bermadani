<?php

namespace App\Livewire\Member;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Member;
use App\Models\SimpananTransaction;
use App\Models\Transaction;

#[Layout('layouts.member')]
class Dashboard extends Component
{
    public $member;
    public $recentTransactions = [];
    public $recentSimpanan = [];
    public $showBalance = true;

    public function mount()
    {
        $user = auth()->user();
        $this->member = Member::where('userId', $user->id)->first();

        if ($this->member) {
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
