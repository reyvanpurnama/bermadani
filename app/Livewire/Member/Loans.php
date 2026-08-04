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
    public $totalOutstanding = 0;
    public $totalMonthlyPayment = 0;

    // Modal state for repayment history
    public $showHistoryModal = false;
    public $selectedLoan = null;
    public $selectedLoanPayments = [];

    public function mount()
    {
        $this->loadLoans();
    }

    public function loadLoans()
    {
        $user = auth()->user();
        if (!$user) {
            return;
        }

        $member = Member::where('userId', $user->id)->first();

        if ($member) {
            $this->activeLoans = Loan::where('member_id', $member->id)
                ->where('status', 'ACTIVE')
                ->with('payments')
                ->latest('startDate')
                ->get();

            $this->completedLoans = Loan::where('member_id', $member->id)
                ->where('status', 'COMPLETED')
                ->with('payments')
                ->latest('endDate')
                ->take(10)
                ->get();

            $this->totalOutstanding = (float) $this->activeLoans->sum('remainingAmount');
            $this->totalMonthlyPayment = (float) $this->activeLoans->sum('monthlyPayment');
        }
    }

    public function openHistory($loanId)
    {
        $user = auth()->user();
        if (!$user) return;

        $member = Member::where('userId', $user->id)->first();
        if (!$member) return;

        $loan = Loan::where('id', $loanId)
            ->where('member_id', $member->id)
            ->with(['payments' => function ($q) {
                $q->latest('paymentDate');
            }])
            ->first();

        if ($loan) {
            $this->selectedLoan = $loan;
            $this->selectedLoanPayments = $loan->payments;
            $this->showHistoryModal = true;
        }
    }

    public function closeHistoryModal()
    {
        $this->showHistoryModal = false;
        $this->selectedLoan = null;
        $this->selectedLoanPayments = [];
    }

    public function render()
    {
        return view('livewire.member.loans');
    }
}
