<?php

namespace App\Livewire\Admin;

use App\Models\Member;
use App\Services\MemberService;
use Livewire\Component;
use Livewire\WithPagination;

class RetailMemberManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $filterTier = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'filterTier' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function getMembersProperty()
    {
        return Member::query()
            ->where('isMemberKoperasi', false) // STRICT: Only Retail Members (Shoppers)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('nomorAnggota', 'LIKE', '%' . $this->search . '%')
                        ->orWhere('name', 'LIKE', '%' . $this->search . '%')
                        ->orWhere('email', 'LIKE', '%' . $this->search . '%')
                        ->orWhere('phone', 'LIKE', '%' . $this->search . '%');
                });
            })
            ->when($this->filterTier, fn($query) => $query->where('tier', $this->filterTier))
            ->with('user')
            ->orderBy('name', 'asc')
            ->paginate(15);
    }

    public function render()
    {
        return view('livewire.admin.retail-member-management', [
            'members' => $this->members,
        ]);
    }
}
