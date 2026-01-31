<?php

namespace App\Livewire\Components;

use Livewire\Component;
use App\Models\Member;

class MemberSearchSelect extends Component
{
    public $query = '';
    public $results = [];
    public $selectedName = '';
    public $extraData = null; // To pass rawName or context
    public $joinedBefore = null; // YYYY-MM

    public function updatedQuery()
    {
        if (strlen($this->query) < 2) {
            $this->results = [];
            return;
        }

        $query = Member::query();

        if ($this->joinedBefore) {
            $cutoff = \Carbon\Carbon::parse($this->joinedBefore)->endOfMonth();
            $query->where('joinDate', '<=', $cutoff);
        }

        $this->results = $query->where(function ($q) {
            $q->where('name', 'like', '%' . $this->query . '%')
                ->orWhere('nomorAnggota', 'like', '%' . $this->query . '%');
        })
            ->limit(10)
            ->get(['id', 'name', 'nomorAnggota', 'joinDate']);
    }

    public function selectResult($id, $name)
    {
        $this->selectedName = $name;
        $this->query = '';
        $this->results = [];

        $this->dispatch('audit:member-mapped', [
            'memberId' => $id,
            'rawName' => $this->extraData
        ]);
    }

    public function render()
    {
        return view('livewire.components.member-search-select');
    }
}
