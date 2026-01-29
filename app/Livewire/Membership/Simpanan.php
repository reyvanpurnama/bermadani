<?php

namespace App\Livewire\Membership;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\Member;
use App\Models\SimpananTransaction;

#[Layout('layouts.membership')]
class Simpanan extends Component
{
    use WithPagination;

    public $member;
    public $activeTab = 'all';
    public $filterType = '';
    public $showBalance = true;
    public $showReceiptModal = false;
    public $selectedTransfer;

    public function mount()
    {
        $user = auth()->user();
        $this->member = Member::where('userId', $user->id)->first();
        $this->markAsRead();
    }

    public function toggleBalance()
    {
        $this->showBalance = !$this->showBalance;
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
        $this->filterType = $tab === 'all' ? '' : strtoupper($tab);
        $this->resetPage();
    }

    public function markAsRead()
    {
        if ($this->member) {
            SimpananTransaction::where('memberId', $this->member->id)
                ->where('isRead', false)
                ->update(['isRead' => true]);
        }
    }

    public function viewReceipt($id)
    {
        $this->selectedTransfer = SimpananTransaction::with('relatedMember')->find($id);
        if ($this->selectedTransfer) {
            $this->showReceiptModal = true;
        }
    }

    public function closeReceipt()
    {
        $this->showReceiptModal = false;
        $this->selectedTransfer = null;
    }

    public function render()
    {
        $simpanan = collect();

        if ($this->member) {
            $query = SimpananTransaction::where('memberId', $this->member->id)
                ->where('status', 'APPROVED')
                ->orderBy('created_at', 'desc');

            if ($this->filterType && $this->filterType !== 'all') {
                $query->where('type', $this->filterType);
            }

            $simpanan = $query->paginate(10);
        }

        return view('livewire.membership.simpanan', [
            'simpanan' => $simpanan,
        ]);
    }
}
