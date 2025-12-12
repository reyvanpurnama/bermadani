<?php

namespace App\Livewire\Admin;

use App\Models\Member;
use App\Models\Transaction;
use App\Models\SimpananTransaction;
use Livewire\Component;
use Livewire\WithPagination;

class MemberDetail extends Component
{
    use WithPagination;

    public $memberId;
    public $member;
    public $activeTab = 'trx';

    protected $queryString = ['activeTab'];

    public function mount($id)
    {
        $this->memberId = $id;
        $this->loadMember();
    }

    public function loadMember()
    {
        $this->member = Member::with(['user', 'simpananTransactions', 'transactions'])
            ->findOrFail($this->memberId);
    }

    public function switchTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function suspendMember()
    {
        $this->member->update([
            'status' => 'SUSPENDED'
        ]);

        session()->flash('message', 'Member berhasil diblokir.');
        $this->loadMember();
    }

    public function activateMember()
    {
        $this->member->update([
            'status' => 'ACTIVE'
        ]);

        session()->flash('message', 'Member berhasil diaktifkan kembali.');
        $this->loadMember();
    }

    public function getTransactionsProperty()
    {
        return Transaction::where('memberId', $this->memberId)
            ->with(['merchant'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    public function getSimpananTransactionsProperty()
    {
        return SimpananTransaction::where('memberId', $this->memberId)
            ->with(['processor'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    public function render()
    {
        $transactions = $this->transactions;
        $simpananTransactions = $this->simpananTransactions;

        return view('livewire.admin.member-detail', [
            'transactions' => $transactions,
            'simpananTransactions' => $simpananTransactions
        ])->layout('layouts.admin');
    }
}
