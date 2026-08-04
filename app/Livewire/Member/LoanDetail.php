<?php

namespace App\Livewire\Member;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Member;
use App\Models\Loan;

#[Layout('layouts.member')]
class LoanDetail extends Component
{
    public Loan $loan;
    public $payments = [];

    public function mount(Loan $loan)
    {
        $user = auth()->user();
        if (!$user) {
            abort(403);
        }

        $member = Member::where('userId', $user->id)->first();
        if (!$member || $loan->member_id !== $member->id) {
            abort(403, 'Anda tidak memiliki akses ke data pembiayaan ini.');
        }

        $this->loan = $loan->load(['payments' => function ($query) {
            $query->latest('paymentDate');
        }]);

        $this->payments = $this->loan->payments;
    }

    public function render()
    {
        return view('livewire.member.loan-detail');
    }
}
