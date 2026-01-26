<?php

namespace App\Livewire\Admin;

use App\Models\Member;
use App\Services\MemberService;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;

class MemberManagement extends Component
{
    use WithPagination, WithFileUploads;

    public $activeTab = 'members'; // 'members' or 'auto-debit'

    public $search = '';
    public $filterStatus = '';
    public $filterTier = '';
    public $filterUnitKerja = '';
    public $filterJoinDate = '';

    // Import properties
    public $showImportModal = false;
    public $importFile;
    public $importSummary = null;

    protected $queryString = [
        'activeTab' => ['except' => 'members'],
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

    // ... (rest of methods until getMembersProperty)

    public function getMembersProperty()
    {
        return Member::query()
            ->where('isMemberKoperasi', true) // STRICT: Only Coop Members
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
            ->orderBy('name', 'asc')
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
