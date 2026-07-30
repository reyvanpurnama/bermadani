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
    public $memberAllocationPercentage = 100;
    public $notes;

    public $selectedSessionId;
    public $searchMember = '';
    public $filterStatus = 'ALL'; // ALL, ACTIVE

    public function mount()
    {
        $this->eventDate = date('Y-m-d');

        // Check if session for 2025 already exists
        $existing = RatSession::where('year', $this->year)->first();
        if ($existing) {
            $this->loadSession($existing);
        } else {
            // Auto fetch net profit for 2025 from financial_transactions if available
            $income = FinancialTransaction::whereYear('transactionDate', $this->year)->where('type', 'INCOME')->sum('amount');
            $expense = FinancialTransaction::whereYear('transactionDate', $this->year)->where('type', 'EXPENSE')->sum('amount');
            $net = $income - $expense;
            if ($net > 0) {
                $this->totalNetProfit = (float) $net;
            }
        }
    }

    public function loadSession(RatSession $session)
    {
        $this->selectedSessionId = $session->id;
        $this->year = $session->year;
        $this->eventDate = $session->event_date->format('Y-m-d');
        $this->title = $session->title;
        $this->totalNetProfit = (float) $session->total_net_profit;
        $this->memberAllocationPercentage = (float) $session->member_allocation_percentage;
        $this->notes = $session->notes;
    }

    public function getActiveSessionProperty()
    {
        return $this->selectedSessionId ? RatSession::find($this->selectedSessionId) : null;
    }

    public function getShuSummaryProperty()
    {
        // Calculate active members simpanan wajib total
        $activeMembers = Member::where('status', 'ACTIVE')->get();
        $totalSimwa = (float) $activeMembers->sum('simpananWajib');
        $totalSimwa = max(1, $totalSimwa); // avoid div by 0

        $totalMemberShu = (float) $this->totalNetProfit * ((float) $this->memberAllocationPercentage / 100);

        return [
            'activeMemberCount' => $activeMembers->count(),
            'totalSimwa' => $totalSimwa,
            'totalMemberShu' => $totalMemberShu,
        ];
    }

    public function saveSession()
    {
        $this->validate([
            'year' => 'required|integer|min:2000|max:2100',
            'eventDate' => 'required|date',
            'title' => 'required|string|max:255',
            'totalNetProfit' => 'required|numeric|min:0',
            'memberAllocationPercentage' => 'required|numeric|min:0|max:100',
        ]);

        $summary = $this->shuSummary;

        $session = RatSession::updateOrCreate(
            ['year' => $this->year],
            [
                'event_date' => $this->eventDate,
                'title' => $this->title,
                'total_net_profit' => $this->totalNetProfit,
                'member_allocation_percentage' => $this->memberAllocationPercentage,
                'total_member_shu' => $summary['totalMemberShu'],
                'total_simpanan_wajib_snapshot' => $summary['totalSimwa'],
                'status' => 'DRAFT',
                'notes' => $this->notes,
                'created_by' => auth()->id(),
            ]
        );

        $this->selectedSessionId = $session->id;

        // Generate / Update distributions for active members
        $activeMembers = Member::where('status', 'ACTIVE')->get();
        $totalSimwa = $summary['totalSimwa'];
        $totalMemberShu = $summary['totalMemberShu'];

        foreach ($activeMembers as $m) {
            $simwa = (float) ($m->simpananWajib ?? 0);
            $portion = ($simwa / $totalSimwa);
            $shuAmount = round($portion * $totalMemberShu, 2);

            MemberShuDistribution::updateOrCreate(
                [
                    'rat_session_id' => $session->id,
                    'member_id' => $m->id,
                ],
                [
                    'simpanan_wajib_amount' => $simwa,
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
