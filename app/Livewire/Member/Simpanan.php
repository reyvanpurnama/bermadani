<?php

namespace App\Livewire\Member;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\Member;
use App\Models\SimpananTransaction;

#[Layout('layouts.member')]
class Simpanan extends Component
{
    use WithPagination;

    public $member;
    public $activeTab = 'all';
    public $filterType = '';

    public function mount()
    {
        $user = auth()->user();
        $this->member = Member::where('userId', $user->id)->first();
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
        $this->filterType = $tab === 'all' ? '' : strtoupper($tab);
        $this->resetPage();
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
