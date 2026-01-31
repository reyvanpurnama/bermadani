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

    public function updatedQuery()
    {
        if (strlen($this->query) < 2) {
            $this->results = [];
            return;
        }

        $this->results = Member::where('name', 'like', '%' . $this->query . '%')
            ->orWhere('nomorAnggota', 'like', '%' . $this->query . '%')
            ->limit(5)
            ->get(['id', 'name', 'nomorAnggota']);
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
