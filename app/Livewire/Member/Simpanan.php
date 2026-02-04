<?php

namespace App\Livewire\Member;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\Member;
use App\Models\SimpananTransaction;
use Carbon\Carbon;

#[Layout('layouts.member')]
class Simpanan extends Component
{
    use WithPagination;

    public $member;
    public $activeTab = 'all';
    public $filterType = '';
    public $showBalance = true;
    public $unreadCount = 0;
    public $selectedTransfer = null;
    public $showReceiptModal = false;
    public $selectedYear;

    public function mount()
    {
        $user = auth()->user();
        $this->member = Member::where('userId', $user->id)->first();
        $this->selectedYear = date('Y');
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

        return view('livewire.member.simpanan', [
            'simpanan' => $simpanan,
        ]);
    }
}
