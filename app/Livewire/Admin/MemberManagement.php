<?php

namespace App\Livewire\Admin;

use App\Imports\MemberImport;
use App\Models\Member;
use App\Services\MemberService;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
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
        'filterJoinDate' => ['except' => ''],
    ];

    protected $memberService;

    public function boot(MemberService $memberService)
    {
        $this->memberService = $memberService;
    }

    public function switchTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
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

    public function updatingFilterJoinDate()
    {
        $this->resetPage();
    }

    public function openImportModal()
    {
        $this->showImportModal = true;
    }

    public function closeImportModal()
    {
        $this->showImportModal = false;
        $this->reset(['importFile']);
    }

    public function importMembers()
    {
        $this->validate([
            'importFile' => 'required|file|mimes:xlsx,xls|max:10240',
        ]);

        try {
            $import = new MemberImport($this->memberService);
            Excel::import($import, $this->importFile->getRealPath());

            $this->importSummary = $import->getSummary();
            session()->flash('success', 'Import data anggota selesai diproses.');
            $this->reset(['importFile']);
            $this->resetPage();
        } catch (\Throwable $e) {
            session()->flash('error', 'Gagal import anggota: ' . $e->getMessage());
        }
    }

    public function downloadSignaturePdf()
    {
        $members = Member::query()
            ->where('status', 'ACTIVE')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('nomorAnggota', 'LIKE', '%' . $this->search . '%')
                        ->orWhere('name', 'LIKE', '%' . $this->search . '%')
                        ->orWhere('email', 'LIKE', '%' . $this->search . '%')
                        ->orWhere('phone', 'LIKE', '%' . $this->search . '%')
                        ->orWhere('unitKerja', 'LIKE', '%' . $this->search . '%');
                });
            })
            ->when($this->filterTier, fn($query) => $query->where('tier', $this->filterTier))
            ->when($this->filterUnitKerja, fn($query) => $query->where('unitKerja', $this->filterUnitKerja))
            ->when($this->filterJoinDate, fn($query) => $query->whereDate('joinDate', $this->filterJoinDate))
            ->select(['id', 'name'])
            ->orderBy('name', 'asc')
            ->get();

        if ($members->isEmpty()) {
            session()->flash('error', 'Tidak ada data anggota untuk diexport.');
            return null;
        }

        $pdf = Pdf::loadView('admin.reports.member-signature-pdf', [
            'members' => $members,
            'filters' => [
                'status' => 'ACTIVE',
                'memberType' => 'Koperasi + Retail',
                'tier' => $this->filterTier,
                'unitKerja' => $this->filterUnitKerja,
                'joinDate' => $this->filterJoinDate,
                'search' => $this->search,
            ],
            'generatedAt' => now()->format('d-m-Y H:i'),
        ])->setPaper('a4', 'portrait');

        $fileName = 'Daftar_Tanda_Tangan_Paket_Lebaran_' . now()->format('Ymd_His') . '.pdf';

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $fileName);
    }

    private function buildMembersQuery()
    {
        return Member::query()
            ->where('isMemberKoperasi', true)
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
            ->when($this->filterJoinDate, fn($query) => $query->whereDate('joinDate', $this->filterJoinDate));
    }

    public function getMembersProperty()
    {
        return $this->buildMembersQuery()
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

    public function suspendMember($memberId)
    {
        $member = Member::findOrFail($memberId);
        $member->update(['status' => 'SUSPENDED']);
        
        session()->flash('success', "Member {$member->name} berhasil dibekukan.");
    }

    public function activateMember($memberId)
    {
        $member = Member::findOrFail($memberId);
        $member->update(['status' => 'ACTIVE']);
        
        session()->flash('success', "Member {$member->name} berhasil diaktifkan kembali.");
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
