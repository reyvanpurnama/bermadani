<?php

namespace App\Livewire\Admin;

use App\Models\Member;
use App\Services\MemberService;
use Livewire\Component;
use Livewire\WithPagination;

class MemberManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $filterStatus = '';
    public $filterTier = '';
    public $filterUnitKerja = '';
    public $filterJoinDate = '';
    
    protected $queryString = [
        'search' => ['except' => ''],
        'filterStatus' => ['except' => ''],
        'filterTier' => ['except' => ''],
        'filterUnitKerja' => ['except' => ''],
    ];

    protected $memberService;

    public function boot(MemberService $memberService)
    {
        $this->memberService = $memberService;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function updatingFilterTier()
    {
        $this->resetPage();
    }

    public function updatingFilterUnitKerja()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset(['search', 'filterStatus', 'filterTier', 'filterUnitKerja', 'filterJoinDate']);
        $this->resetPage();
    }

    public function suspendMember($memberId)
    {
        try {
            $this->memberService->suspendMember($memberId, 'Suspended by admin');
            session()->flash('success', 'Anggota berhasil dibekukan.');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal membekukan anggota: ' . $e->getMessage());
        }
    }

    public function activateMember($memberId)
    {
        try {
            $this->memberService->activateMember($memberId);
            session()->flash('success', 'Anggota berhasil diaktifkan kembali.');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal mengaktifkan anggota: ' . $e->getMessage());
        }
    }

    public function getMembersProperty()
    {
        return Member::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('nomorAnggota', 'LIKE', '%' . $this->search . '%')
                      ->orWhere('name', 'LIKE', '%' . $this->search . '%')
                      ->orWhere('email', 'LIKE', '%' . $this->search . '%')
                      ->orWhere('phone', 'LIKE', '%' . $this->search . '%')
                      ->orWhere('unitKerja', 'LIKE', '%' . $this->search . '%');
                });
            })
            ->when($this->filterStatus, fn($query) => $query->where('status', $this->filterStatus))
            ->when($this->filterTier, fn($query) => $query->where('tier', $this->filterTier))
            ->when($this->filterUnitKerja, fn($query) => $query->where('unitKerja', $this->filterUnitKerja))
            ->when($this->filterJoinDate, fn($query) => $query->whereDate('joinDate', $this->filterJoinDate))
            ->with('user')
            ->latest('joinDate')
            ->paginate(15);
    }

    public function getStatsProperty()
    {
        return $this->memberService->getStats();
    }

    public function getUnitKerjaListProperty()
    {
        return Member::select('unitKerja')
            ->distinct()
            ->orderBy('unitKerja')
            ->pluck('unitKerja');
    }

    public function render()
    {
        return view('livewire.admin.member-management', [
            'members' => $this->members,
            'stats' => $this->stats,
            'unitKerjaList' => $this->unitKerjaList,
        ]);
    }
}
