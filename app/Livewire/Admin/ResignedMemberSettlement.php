<?php

namespace App\Livewire\Admin;

use App\Domains\Koperasi\Models\Loan;
use App\Domains\Koperasi\Models\Member;
use App\Domains\Koperasi\Models\MemberSettlement;
use Livewire\Component;
use Livewire\WithPagination;

class ResignedMemberSettlement extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = 'ALL'; // ALL, PENDING, SETTLED
    public string $unitKerjaFilter = 'ALL';

    // Modal state
    public bool $showProcessModal = false;
    public bool $showDetailModal = false;
    public ?int $selectedMemberId = null;

    // Settlement Form fields
    public string $payment_method = 'BANK_TRANSFER';
    public string $bank_name = '';
    public string $bank_account_number = '';
    public string $bank_account_holder = '';
    public string $settled_at = '';
    public string $notes = '';

    protected $paginationTheme = 'tailwind';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function openProcessModal(int $memberId): void
    {
        $this->selectedMemberId = $memberId;
        $member = Member::findOrFail($memberId);

        $existingSettlement = MemberSettlement::where('member_id', $memberId)->first();

        $this->bank_name            = $existingSettlement->bank_name ?? '';
        $this->bank_account_number  = $existingSettlement->bank_account_number ?? '';
        $this->bank_account_holder  = $existingSettlement->bank_account_holder ?? $member->name;
        $this->payment_method       = $existingSettlement->payment_method ?? 'BANK_TRANSFER';
        $this->settled_at           = date('Y-m-d');
        $this->notes                = $existingSettlement->notes ?? 'Pelunasan & Pengembalian Simpanan Anggota Keluar';

        $this->showProcessModal = true;
    }

    public function openDetailModal(int $memberId): void
    {
        $this->selectedMemberId = $memberId;
        $this->showDetailModal = true;
    }

    public function closeModals(): void
    {
        $this->showProcessModal = false;
        $this->showDetailModal = false;
        $this->selectedMemberId = null;
    }

    public function processSettlement(): void
    {
        $this->validate([
            'payment_method' => 'required|in:BANK_TRANSFER,CASH',
            'bank_name' => 'required_if:payment_method,BANK_TRANSFER|nullable|string|max:100',
            'bank_account_number' => 'required_if:payment_method,BANK_TRANSFER|nullable|string|max:50',
            'bank_account_holder' => 'required_if:payment_method,BANK_TRANSFER|nullable|string|max:150',
            'settled_at' => 'required|date',
            'notes' => 'nullable|string|max:500',
        ]);

        if (!$this->selectedMemberId) {
            return;
        }

        $member = Member::findOrFail($this->selectedMemberId);

        // Calculate breakdown
        $simpok   = (float) $member->simpananPokok;
        $simwa    = (float) $member->simpananWajib;
        $simsuka  = (float) $member->simpananSukarela;
        $gross    = $simpok + $simwa + $simsuka;

        // Calculate active loan balance
        $loanDeduction = (float) Loan::where('member_id', $member->id)
            ->whereIn('status', ['ACTIVE', 'APPROVED', 'OVERDUE'])
            ->sum('amount'); // Or sisa pinjaman calculation

        $netRefund = max(0, $gross - $loanDeduction);

        // Update or Create Settlement Record
        MemberSettlement::updateOrCreate(
            ['member_id' => $member->id],
            [
                'simpanan_pokok'        => $simpok,
                'simpanan_wajib'        => $simwa,
                'simpanan_sukarela'     => $simsuka,
                'total_gross_simpanan'  => $gross,
                'loan_deduction'        => $loanDeduction,
                'net_refund_amount'     => $netRefund,
                'status'                => 'SETTLED',
                'payment_method'        => $this->payment_method,
                'bank_name'             => $this->bank_name,
                'bank_account_number'   => $this->bank_account_number,
                'bank_account_holder'   => $this->bank_account_holder,
                'settled_at'            => $this->settled_at,
                'settled_by'            => auth()->id(),
                'notes'                 => $this->notes,
            ]
        );

        $this->closeModals();
        session()->flash('message', "Pelunasan simpanan untuk {$member->name} berhasil diproses!");
    }

    public function render()
    {
        // Query members who are RESIGNED or INACTIVE
        $query = Member::query()
            ->whereIn('status', ['INACTIVE', 'SUSPENDED'])
            ->with(['settlement']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('nomorAnggota', 'like', "%{$this->search}%")
                  ->orWhere('unitKerja', 'like', "%{$this->search}%");
            });
        }

        if ($this->unitKerjaFilter !== 'ALL') {
            $query->where('unitKerja', $this->unitKerjaFilter);
        }

        if ($this->statusFilter === 'PENDING') {
            $query->where(function ($q) {
                $q->whereDoesntHave('settlement')
                  ->orWhereHas('settlement', fn($sub) => $sub->where('status', 'PENDING'));
            });
        } elseif ($this->statusFilter === 'SETTLED') {
            $query->whereHas('settlement', fn($sub) => $sub->where('status', 'SETTLED'));
        }

        $resignedMembers = $query->orderBy('updated_at', 'desc')->orderBy('id', 'desc')->paginate(10);

        // Overview KPI calculations
        $allResignedQuery = Member::query()->whereIn('status', ['INACTIVE', 'SUSPENDED']);
        $totalResignedCount = (clone $allResignedQuery)->count();

        $totalSimpananGross = (clone $allResignedQuery)
            ->selectRaw('SUM(simpananPokok + simpananWajib + simpananSukarela) as total')
            ->value('total') ?? 0;

        $settledCount = MemberSettlement::where('status', 'SETTLED')->count();
        $pendingCount = max(0, $totalResignedCount - $settledCount);

        $selectedMember = $this->selectedMemberId ? Member::find($this->selectedMemberId) : null;
        $selectedSettlement = $selectedMember ? $selectedMember->settlement : null;

        return view('livewire.admin.resigned-member-settlement', [
            'members' => $resignedMembers,
            'totalResignedCount' => $totalResignedCount,
            'totalSimpananGross' => $totalSimpananGross,
            'settledCount' => $settledCount,
            'pendingCount' => $pendingCount,
            'selectedMember' => $selectedMember,
            'selectedSettlement' => $selectedSettlement,
        ])
            ->extends('layouts.admin')
            ->section('content');
    }
}
