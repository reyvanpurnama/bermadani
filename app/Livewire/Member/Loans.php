<?php

namespace App\Livewire\Member;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Member;
use App\Models\Loan;

#[Layout('layouts.member')]
class Loans extends Component
{
    public $activeLoans = [];
    public $completedLoans = [];

    public function mount()
    {
        $user = auth()->user();
        if (!$user)
            return;

        $member = Member::where('userId', $user->id)->first();

        if ($member) {
            $this->activeLoans = Loan::where('member_id', $member->id)
                ->where('status', 'ACTIVE')
                ->latest('startDate')
                ->get();

            $this->completedLoans = Loan::where('member_id', $member->id)
                ->where('status', 'COMPLETED')
                ->latest('endDate')
                ->take(10)
                ->get();
        }
    }

    public function render()
    {
        return view('livewire.member.loans');
    }
}
