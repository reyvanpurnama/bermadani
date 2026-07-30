<?php

namespace App\Livewire\Admin;

use App\Models\Member;
use App\Models\MemberShuDistribution;
use App\Models\RatSession;
use App\Models\FinancialTransaction;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class RatSessionManagement extends Component
{
    use WithPagination;

    public $year = 2025;
    public $eventDate;
    public $title = 'RAT Koperasi Bermadani Tahun Buku 2025';
    public $totalNetProfit = 30499118;
    public $totalMemberShu = 30499118; // Default 100% laba bersih
    public $memberAllocationPercentage = 100;
    public $notes;

    public $selectedSessionId;
    public $searchMember = '';
    public $filterStatus = 'ALL'; // ALL, ACTIVE

    public function updatingSearchMember()
    {
        $this->resetPage();
    }

    public function mount()
    {
        $this->eventDate = date('Y-m-d');

        // Check if session for 2025 already exists
        $existing = RatSession::where('year', $this->year)->first();
        if ($existing) {
            $this->loadSession($existing);
        } else {
            try {
                // Auto fetch net profit for 2025 from financial_transactions if available
                $income = FinancialTransaction::whereYear('transactionDate', $this->year)->where('type', 'INCOME')->sum('amount');
                $expense = FinancialTransaction::whereYear('transactionDate', $this->year)->where('type', 'EXPENSE')->sum('amount');
                $net = (float) ($income - $expense);
                if ($net > 0) {
                    $this->totalNetProfit = $net;
                    $this->totalMemberShu = $net;
                    $this->memberAllocationPercentage = 100.0;
                } else {
                    $this->totalNetProfit = 30499118;
                    $this->totalMemberShu = 30499118;
                    $this->memberAllocationPercentage = 100.0;
                }
            } catch (\Throwable $e) {
                $this->totalNetProfit = 30499118;
                $this->totalMemberShu = 30499118;
                $this->memberAllocationPercentage = 100.0;
            }
        }
    }

    public function updatedTotalMemberShu($value)
    {
        $val = (float) ($value ?: 0);
        $net = (float) ($this->totalNetProfit ?: 0);
        if ($net > 0) {
            $this->memberAllocationPercentage = round(($val / $net) * 100, 2);
        }
    }

    public function updatedMemberAllocationPercentage($value)
    {
        $val = (float) ($value ?: 0);
        $net = (float) ($this->totalNetProfit ?: 0);
        $this->totalMemberShu = round($net * ($val / 100), 0);
    }

    public function updatedTotalNetProfit($value)
    {
        $val = (float) ($value ?: 0);
        $pct = (float) ($this->memberAllocationPercentage ?: 0);
        $this->totalMemberShu = round($val * ($pct / 100), 0);
    }

    public function loadSession(RatSession $session)
    {
        $this->selectedSessionId = $session->id;
        $this->year = $session->year;
        $this->eventDate = $session->event_date->format('Y-m-d');
        $this->title = $session->title;
        $this->totalNetProfit = (float) $session->total_net_profit;
        $this->totalMemberShu = (float) ($session->total_member_shu ?? $session->total_net_profit);
        $this->memberAllocationPercentage = (float) $session->member_allocation_percentage;
        $this->notes = $session->notes;
    }

    public function getActiveSessionProperty()
    {
        return $this->selectedSessionId ? RatSession::find($this->selectedSessionId) : null;
    }

    public function getShuSummaryProperty()
    {
        // Calculate active members total modal sendiri (Simpanan Pokok + Wajib)
        $activeMembers = Member::where('status', 'ACTIVE')->get();
        $totalSimwa = (float) $activeMembers->sum(function ($m) {
            return (float) ($m->simpananPokok ?? 0) + (float) ($m->simpananWajib ?? 0);
        });
        $totalSimwa = max(1, $totalSimwa); // avoid div by 0

        $totalMemberShu = (float) ($this->totalMemberShu ?: 0);
        $totalNetProfit = (float) ($this->totalNetProfit ?: 0);
        $retainedAmount = max(0, $totalNetProfit - $totalMemberShu);

        return [
            'activeMemberCount' => $activeMembers->count(),
            'totalSimwa' => $totalSimwa,
            'totalMemberShu' => $totalMemberShu,
            'retainedAmount' => $retainedAmount,
        ];
    }

    public function saveSession()
    {
        $this->validate([
            'year' => 'required|integer|min:2000|max:2100',
            'eventDate' => 'required|date',
            'title' => 'required|string|max:255',
            'totalNetProfit' => 'required|numeric|min:0',
            'totalMemberShu' => 'required|numeric|min:0',
            'memberAllocationPercentage' => 'required|numeric|min:0|max:100',
        ]);

        $summary = $this->shuSummary;

        $session = RatSession::updateOrCreate(
            ['year' => $this->year],
            [
                'event_date' => $this->eventDate,
                'title' => $this->title,
                'total_net_profit' => (float) $this->totalNetProfit,
                'member_allocation_percentage' => (float) $this->memberAllocationPercentage,
                'total_member_shu' => (float) $this->totalMemberShu,
                'total_simpanan_wajib_snapshot' => $summary['totalSimwa'],
                'status' => 'DRAFT',
                'notes' => $this->notes,
                'created_by' => auth()->id(),
            ]
        );

        $this->selectedSessionId = $session->id;

        // Generate / Update distributions for active members based on Modal Sendiri (Pokok + Wajib)
        $activeMembers = Member::where('status', 'ACTIVE')->get();
        $totalSimwa = $summary['totalSimwa'];
        $totalMemberShu = (float) $this->totalMemberShu;

        foreach ($activeMembers as $m) {
            $totalSimpananMember = (float) ($m->simpananPokok ?? 0) + (float) ($m->simpananWajib ?? 0);
            $portion = ($totalSimpananMember / $totalSimwa);
            $shuAmount = round($portion * $totalMemberShu, 2);

            MemberShuDistribution::updateOrCreate(
                [
                    'rat_session_id' => $session->id,
                    'member_id' => $m->id,
                ],
                [
                    'simpanan_wajib_amount' => $totalSimpananMember,
                    'portion_percentage' => round($portion * 100, 4),
                    'shu_amount' => $shuAmount,
                ]
            );
        }

        session()->flash('success', 'Draft Sesi RAT & Perhitungan SHU berhasil disimpan!');
    }

    public function finalizeSession()
    {
        if (!$this->selectedSessionId) {
            $this->saveSession();
        }

        $session = RatSession::find($this->selectedSessionId);
        if ($session) {
            $session->update(['status' => 'FINALIZED']);
            session()->flash('success', 'Sesi RAT berhasil disahkan & SHU resmi dipublikasikan ke portal anggota!');
        }
    }

    public function reopenSession()
    {
        if ($this->selectedSessionId) {
            RatSession::find($this->selectedSessionId)?->update(['status' => 'DRAFT']);
            session()->flash('info', 'Status Sesi RAT dikembalikan ke DRAFT.');
        }
    }

    public function toggleDisbursed($distributionId)
    {
        $dist = MemberShuDistribution::find($distributionId);
        if ($dist) {
            $newStatus = !$dist->is_disbursed;
            $dist->update([
                'is_disbursed' => $newStatus,
                'disbursed_at' => $newStatus ? now() : null,
            ]);
        }
    }

    public function render()
    {
        $session = $this->activeSession;
        $distributions = [];

        if ($session) {
            $query = MemberShuDistribution::with('member')
                ->where('rat_session_id', $session->id);

            if ($this->searchMember) {
                $query->whereHas('member', function ($q) {
                    $q->where('name', 'like', '%' . $this->searchMember . '%')
                      ->orWhere('nomorAnggota', 'like', '%' . $this->searchMember . '%')
                      ->orWhere('unitKerja', 'like', '%' . $this->searchMember . '%');
                });
            }

            $distributions = $query->orderByDesc('shu_amount')->paginate(15);
        } else {
            // Live preview if session not saved yet
            $query = Member::where('status', 'ACTIVE');
            if ($this->searchMember) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->searchMember . '%')
                      ->orWhere('nomorAnggota', 'like', '%' . $this->searchMember . '%')
                      ->orWhere('unitKerja', 'like', '%' . $this->searchMember . '%');
                });
            }
            $distributions = $query->orderByDesc('simpananWajib')->paginate(15);
        }

        return view('livewire.admin.rat-session-management', [
            'session' => $session,
            'distributions' => $distributions,
            'summary' => $this->shuSummary,
            'allSessions' => RatSession::orderByDesc('year')->get(),
        ])->layout('layouts.admin');
    }
}
