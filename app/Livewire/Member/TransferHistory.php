<?php

namespace App\Livewire\Member;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\Member;
use App\Models\SimpananTransaction;

#[Layout('layouts.member')]
class TransferHistory extends Component
{
    use WithPagination;

    public $member;
    public $filterType = ''; // '', 'TRANSFER_IN', 'TRANSFER_OUT'
    public $search = '';
    public $selectedTransfer = null;
    public $showReceiptModal = false;

    public function mount()
    {
        $user = auth()->user();
        $this->member = Member::where('userId', $user->id)->first();
    }

    public function setFilter($type)
    {
        $this->filterType = $type;
        $this->resetPage();
    }

    public function viewReceipt($transferId)
    {
        $this->selectedTransfer = SimpananTransaction::with(['member', 'relatedMember'])
            ->findOrFail($transferId);
        $this->showReceiptModal = true;
    }

    public function closeReceipt()
    {
        $this->showReceiptModal = false;
        $this->selectedTransfer = null;
    }

    public function render()
    {
        $transfers = collect();

        if ($this->member) {
            $query = SimpananTransaction::where('memberId', $this->member->id)
                ->whereIn('transactionType', ['TRANSFER_IN', 'TRANSFER_OUT'])
                ->where('status', 'APPROVED')
                ->with(['relatedMember'])
                ->orderBy('created_at', 'desc');

            if ($this->filterType) {
                $query->where('transactionType', $this->filterType);
            }

            if ($this->search) {
                $query->where(function($q) {
                    $q->whereHas('relatedMember', function($q2) {
                        $q2->where('name', 'like', '%' . $this->search . '%')
                           ->orWhere('nomorAnggota', 'like', '%' . $this->search . '%');
                    })
                    ->orWhere('transferReference', 'like', '%' . $this->search . '%');
                });
            }

            $transfers = $query->paginate(15);
        }

        return view('livewire.member.transfer-history', [
            'transfers' => $transfers,
        ]);
    }
}
