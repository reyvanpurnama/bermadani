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
    public $totalMemberShu = 15000000; // SHU Dibagikan Lembar 4 CALK Poin 8
    public $memberAllocationPercentage = 49.18;
    public $notes;

    public $joinDateCutoff;
    public $excludedMemberIds = [];
    public $includedMemberIds = [];

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
                    $this->totalMemberShu = 15000000;
                    $this->memberAllocationPercentage = round((15000000 / $net) * 100, 2);
                } else {
                    $this->totalNetProfit = 30499118;
                    $this->totalMemberShu = 15000000;
                    $this->memberAllocationPercentage = 49.18;
                }
            } catch (\Throwable $e) {
                $this->totalNetProfit = 30499118;
                $this->totalMemberShu = 15000000;
                $this->memberAllocationPercentage = 49.18;
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
        $net = (float) $session->total_net_profit;
        if ($net == 15000000 || $net <= 0) {
            $net = 30499118;
        }
        $this->totalNetProfit = $net;

        $shu = (float) ($session->total_member_shu ?? $net);
        if ($shu == 15000000 || $shu <= 0) {
            $shu = $net;
        }
        $this->totalMemberShu = $shu;
        $this->memberAllocationPercentage = (float) $session->member_allocation_percentage;
        $this->notes = $session->notes;
        $this->joinDateCutoff = $session->join_date_cutoff ? $session->join_date_cutoff->format('Y-m-d') : null;
        $this->excludedMemberIds = $session->excluded_member_ids ?? [];
        $this->includedMemberIds = $session->included_member_ids ?? [];
    }

    public function getActiveSessionProperty()
    {
        return $this->selectedSessionId ? RatSession::find($this->selectedSessionId) : null;
    }

    public function getShuSummaryProperty()
    {
        $session = $this->activeSession;

        if ($session && $session->status === 'FINALIZED') {
            $totalSimwa = (float) $session->total_simpanan_wajib_snapshot;
            // For finalized session, calculate the historic breakdown of eligible members
            $cutoff = $session->join_date_cutoff;
            $query = Member::whereIn('id', function($q) use ($session) {
                $q->select('member_id')->from('member_shu_distributions')
                  ->where('rat_session_id', $session->id)
                  ->where('portion_percentage', '>', 0);
            });
            $eligibleMembers = $query->get();

            $totalSimpok = (float) $eligibleMembers->sum(function ($m) use ($cutoff) {
                return $this->getMemberSavingsAtCutoff($m, 'POKOK', $cutoff);
            });
            $totalSimwaReal = (float) $eligibleMembers->sum(function ($m) use ($cutoff) {
                return $this->getMemberSavingsAtCutoff($m, 'WAJIB', $cutoff);
            });
            $activeCount = MemberShuDistribution::where('rat_session_id', $session->id)
                ->where('portion_percentage', '>', 0)
                ->count();
        } else {
            // Compute based on eligible members
            $eligibleMembers = Member::all()->filter(function ($m) {
                return $this->isMemberEligible($m->id, $m->joinDate, $m->status);
            });

            $totalSimpok = (float) $eligibleMembers->sum(function ($m) {
                return $this->getMemberSavingsAtCutoff($m, 'POKOK', $this->joinDateCutoff);
            });
            $totalSimwaReal = (float) $eligibleMembers->sum(function ($m) {
                return $this->getMemberSavingsAtCutoff($m, 'WAJIB', $this->joinDateCutoff);
            });
            $totalSimwa = $totalSimpok + $totalSimwaReal;
            $activeCount = $eligibleMembers->count();
        }

        $totalSimwa = max(1, $totalSimwa); // avoid div by 0

        $totalMemberShu = (float) ($this->totalMemberShu ?: 0);
        $totalNetProfit = (float) ($this->totalNetProfit ?: 0);
        $retainedAmount = max(0, $totalNetProfit - $totalMemberShu);

        return [
            'activeMemberCount' => $activeCount,
            'totalSimwa' => $totalSimwa,
            'totalSimpok' => $totalSimpok,
            'totalSimwaReal' => $totalSimwaReal,
            'totalMemberShu' => $totalMemberShu,
            'retainedAmount' => $retainedAmount,
        ];
    }

    public function isMemberEligible($memberId, $joinDate, $status = 'ACTIVE')
    {
        if (in_array($memberId, $this->includedMemberIds)) {
            return true;
        }
        if (in_array($memberId, $this->excludedMemberIds)) {
            return false;
        }
        if ($status !== 'ACTIVE') {
            return false;
        }
        if ($this->joinDateCutoff && $joinDate) {
            $joinDateTime = \Carbon\Carbon::parse($joinDate);
            $cutoffDateTime = \Carbon\Carbon::parse($this->joinDateCutoff)->endOfDay();
            if ($joinDateTime->gt($cutoffDateTime)) {
                return false;
            }
        }
        return true;
    }

    public function getMemberSavingsAtCutoff($member, $type, $cutoffDate = null)
    {
        $currentBalance = (float) ($type === 'POKOK' ? $member->simpananPokok : $member->simpananWajib);

        if (!$cutoffDate) {
            return $currentBalance;
        }

        $cutoffDateTime = \Carbon\Carbon::parse($cutoffDate)->endOfDay();

        // Sum deposits (SETOR) after the cutoff date
        $depositsAfter = \App\Models\SimpananTransaction::where('memberId', $member->id)
            ->where('status', 'APPROVED')
            ->where('transactionType', 'SETOR')
            ->where('type', $type)
            ->where('created_at', '>', $cutoffDateTime)
            ->sum('amount');

        // Sum withdrawals (TARIK) after the cutoff date
        $withdrawalsAfter = \App\Models\SimpananTransaction::where('memberId', $member->id)
            ->where('status', 'APPROVED')
            ->where('transactionType', 'TARIK')
            ->where('type', $type)
            ->where('created_at', '>', $cutoffDateTime)
            ->sum('amount');

        return max(0, $currentBalance - (float) $depositsAfter + (float) $withdrawalsAfter);
    }

    public function toggleMemberExclusion($memberId)
    {
        $session = $this->activeSession;
        if ($session && $session->status === 'FINALIZED') {
            return;
        }

        $member = Member::find($memberId);
        if (!$member) return;

        $wasEligible = $this->isMemberEligible($member->id, $member->joinDate, $member->status);

        if ($wasEligible) {
            $this->includedMemberIds = array_values(array_diff($this->includedMemberIds, [$memberId]));
            if (!in_array($memberId, $this->excludedMemberIds)) {
                $this->excludedMemberIds[] = $memberId;
            }
        } else {
            $this->excludedMemberIds = array_values(array_diff($this->excludedMemberIds, [$memberId]));
            if (!in_array($memberId, $this->includedMemberIds)) {
                $this->includedMemberIds[] = $memberId;
            }
        }

        if ($session) {
            $this->saveSession();
        }
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
            'joinDateCutoff' => 'nullable|date',
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
                'join_date_cutoff' => $this->joinDateCutoff ?: null,
                'excluded_member_ids' => $this->excludedMemberIds,
                'included_member_ids' => $this->includedMemberIds,
            ]
        );

        $this->selectedSessionId = $session->id;

        // Query all members
        $allMembers = Member::all();
        $totalSimwa = $summary['totalSimwa'];
        $totalMemberShu = (float) $this->totalMemberShu;

        foreach ($allMembers as $m) {
            $isEligible = $this->isMemberEligible($m->id, $m->joinDate, $m->status);

            $simpok = $this->getMemberSavingsAtCutoff($m, 'POKOK', $this->joinDateCutoff);
            $simwa = $this->getMemberSavingsAtCutoff($m, 'WAJIB', $this->joinDateCutoff);
            $totalSimpananMember = $simpok + $simwa;

            if ($isEligible) {
                $portion = ($totalSimpananMember / $totalSimwa);
                $shuAmount = round($portion * $totalMemberShu, 2);
            } else {
                $portion = 0;
                $shuAmount = 0;
            }

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
        $dist = MemberShuDistribution::with(['ratSession', 'member'])->find($distributionId);
        if ($dist) {
            $newStatus = !$dist->is_disbursed;
            
            $txId = $dist->financial_transaction_id;
            
            if ($newStatus) {
                // If marking as disbursed, create a FinancialTransaction
                $shuAmount = (float) $dist->shu_amount;
                if ($shuAmount > 0) {
                    $tx = \App\Models\FinancialTransaction::create([
                        'type' => 'EXPENSE',
                        'category' => 'Pembagian SHU',
                        'amount' => $shuAmount,
                        'transactionDate' => now()->toDateString(),
                        'description' => "Pencairan SHU RAT " . ($dist->ratSession?->year ?? '') . " untuk " . ($dist->member?->name ?? '') . " (" . ($dist->member?->nomorAnggota ?? '') . ")",
                        'userId' => auth()->id() ?? 1,
                    ]);
                    $txId = $tx->id;
                }
            } else {
                // If marking as undisbursed, delete the associated FinancialTransaction
                if ($txId) {
                    \App\Models\FinancialTransaction::find($txId)?->delete();
                    $txId = null;
                }
            }

            $dist->update([
                'is_disbursed' => $newStatus,
                'disbursed_at' => $newStatus ? now() : null,
                'financial_transaction_id' => $txId,
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

            // We order by portion_percentage desc so eligible ones are at the top, followed by 0-SHU/excluded ones.
            $distributions = $query->orderByDesc('portion_percentage')->paginate(15);
        } else {
            // Live preview if session not saved yet
            $query = Member::query();
            if ($this->searchMember) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->searchMember . '%')
                      ->orWhere('nomorAnggota', 'like', '%' . $this->searchMember . '%')
                      ->orWhere('unitKerja', 'like', '%' . $this->searchMember . '%');
                });
            }
            $distributions = $query->orderByRaw("CASE WHEN status = 'ACTIVE' THEN 0 ELSE 1 END")
                ->orderByDesc(DB::raw('simpananPokok + simpananWajib'))
                ->paginate(15);
        }

        return view('livewire.admin.rat-session-management', [
            'session' => $session,
            'distributions' => $distributions,
            'summary' => $this->shuSummary,
            'allSessions' => RatSession::orderByDesc('year')->get(),
        ])->layout('layouts.admin');
    }
}
