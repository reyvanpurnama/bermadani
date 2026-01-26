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

    // Create Member Form
    public $showCreateModal = false;
    public $newName = '';
    public $newPhone = '';
    public $newUnitKerja = '';

    protected $rules = [
        'newName' => 'required|string|max:255',
        'newPhone' => 'required|string|max:20|unique:members,phone',
    ];

    public function createMember()
    {
        $this->validate();

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            // 1. Create User (Dummy Email from Phone)
            // Format email: [Phone]@bermadani.id
            $email = $this->newPhone . '@bermadani.id';

            $user = \App\Models\User::create([
                'name' => $this->newName,
                'email' => $email,
                'password' => \Illuminate\Support\Facades\Hash::make('12345678'), // Default password
                'role' => 'MEMBER',
            ]);

            // 2. Generate Member Number (Retail format? Or standard?)
            // Let's use standard generation for now, maybe prefix R?
            // For now just standard random/sequential
            $lastMember = Member::latest('id')->first();
            $nextId = $lastMember ? $lastMember->id + 1 : 1;
            $nomorAnggota = 'R' . str_pad($nextId, 5, '0', STR_PAD_LEFT); // R00001 (Retail)

            // 3. Create Member
            Member::create([
                'user_id' => $user->id,
                'nomorAnggota' => $nomorAnggota,
                'phone' => $this->newPhone,
                'unitKerja' => $this->newUnitKerja,
                'isMemberKoperasi' => false, // IMPORTANT
                'status' => 'ACTIVE',
                'joinDate' => now(),
                'tier' => 'BRONZE',
                'points' => 0,
            ]);

            \Illuminate\Support\Facades\DB::commit();

            $this->showCreateModal = false;
            $this->reset(['newName', 'newPhone', 'newUnitKerja']);

            session()->flash('message', 'Member retail berhasil ditambahkan.');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            session()->flash('error', 'Gagal membuat member: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.retail-member-management', [
            'members' => $this->members,
        ])->layout('layouts.admin');
    }
}
