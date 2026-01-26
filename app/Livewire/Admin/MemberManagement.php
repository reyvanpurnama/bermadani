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

    public $memberTypeFilter = 'KOPERASI'; // Default to KOPERASI members only

    protected $queryString = [
        'activeTab' => ['except' => 'members'],
        'search' => ['except' => ''],
        'filterStatus' => ['except' => ''],
        'filterTier' => ['except' => ''],
        'filterUnitKerja' => ['except' => ''],
        'memberTypeFilter' => ['except' => 'KOPERASI'],
    ];

    protected $memberService;

    public function boot(MemberService $memberService)
    {
        $this->memberService = $memberService;
    }

    public function switchTab($tab)
    {
        $this->activeTab = $tab;
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

    public function openImportModal()
    {
        $this->showImportModal = true;
        $this->importFile = null;
        $this->importSummary = null;
    }

    public function closeImportModal()
    {
        $this->showImportModal = false;
        $this->importFile = null;
        $this->importSummary = null;
    }

    public function importMembers()
    {
        $this->validate([
            'importFile' => 'required|mimes:xlsx,xls|max:10240', // max 10MB
        ]);

        try {
            $filePath = $this->importFile->getRealPath();

            $this->importSummary = $this->memberService->importFromExcel($filePath);

            if ($this->importSummary['success'] > 0) {
                session()->flash(
                    'success',
                    "Import berhasil! {$this->importSummary['success']} anggota ditambahkan, " .
                    "{$this->importSummary['skipped']} dilewati, " .
                    "{$this->importSummary['errors']} error."
                );
            } else {
                session()->flash('error', 'Tidak ada anggota yang berhasil diimport.');
            }

            $this->reset(['importFile']);

        } catch (\Exception $e) {
            session()->flash('error', 'Gagal import: ' . $e->getMessage());
        }
    }

    public function getMembersProperty()
    {
        return Member::query()
            ->when($this->memberTypeFilter === 'KOPERASI', fn($q) => $q->where('isMemberKoperasi', true))
            ->when($this->memberTypeFilter === 'RETAIL', fn($q) => $q->where('isMemberKoperasi', false))
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
