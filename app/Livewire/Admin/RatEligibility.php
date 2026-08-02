<?php

namespace App\Livewire\Admin;

use App\Models\Member;
use App\Models\RatSession;
use App\Services\ShuCalculationService;
use Livewire\Component;
use Livewire\WithPagination;

class RatEligibility extends Component
{
    use WithPagination;

    public $sessionId;
    public $joinDateCutoff;
    public $excludedMemberIds = [];
    public $includedMemberIds = [];
    public $searchMember = '';
    public $filterStatus = 'ALL';

    protected ShuCalculationService $shuService;

    public function boot(ShuCalculationService $shuService)
    {
        $this->shuService = $shuService;
    }

    public function mount($session)
    {
        $ratSession = RatSession::findOrFail($session);
        $this->sessionId = $ratSession->id;
        $this->joinDateCutoff = $ratSession->join_date_cutoff?->format('Y-m-d');
        $this->excludedMemberIds = $ratSession->excluded_member_ids ?? [];
        $this->includedMemberIds = $ratSession->included_member_ids ?? [];
    }

    public function updatingSearchMember()
    {
        $this->resetPage();
    }

    public function getSessionProperty()
    {
        return RatSession::find($this->sessionId);
    }

    public function getSummaryProperty()
    {
        $session = $this->session;
        if (!$session) return [];

        // Build a temporary session-like object with current form values for live preview
        $previewSession = clone $session;
        $previewSession->join_date_cutoff = $this->joinDateCutoff ? \Carbon\Carbon::parse($this->joinDateCutoff) : null;
        $previewSession->excluded_member_ids = $this->excludedMemberIds;
        $previewSession->included_member_ids = $this->includedMemberIds;

        return $this->shuService->calculateSummary($previewSession);
    }

    public function toggleMemberExclusion($memberId)
    {
        $session = $this->session;
        if (!$session || $session->isFinalized()) {
            return;
        }

        $member = Member::find($memberId);
        if (!$member) return;

        $wasEligible = $this->shuService->isMemberEligible(
            $member->id,
            $member->joinDate?->format('Y-m-d H:i:s'),
            $member->status,
            $this->joinDateCutoff,
            $this->excludedMemberIds,
            $this->includedMemberIds
        );

        if ($wasEligible) {
            // Remove from included (if was there)
            $this->includedMemberIds = array_values(array_diff($this->includedMemberIds, [$memberId]));
            // Add to excluded
            if (!in_array($memberId, $this->excludedMemberIds)) {
                $this->excludedMemberIds[] = $memberId;
            }
        } else {
            // Remove from excluded (if was there)
            $this->excludedMemberIds = array_values(array_diff($this->excludedMemberIds, [$memberId]));
            // Add to included
            if (!in_array($memberId, $this->includedMemberIds)) {
                $this->includedMemberIds[] = $memberId;
            }
        }

        // Auto-save to session
        $session->update([
            'join_date_cutoff' => $this->joinDateCutoff ?: null,
            'excluded_member_ids' => $this->excludedMemberIds,
            'included_member_ids' => $this->includedMemberIds,
        ]);
    }

    public function saveEligibility()
    {
        $session = $this->session;
        if (!$session) return;

        $session->update([
            'join_date_cutoff' => $this->joinDateCutoff ?: null,
            'excluded_member_ids' => $this->excludedMemberIds,
            'included_member_ids' => $this->includedMemberIds,
        ]);

        session()->flash('success', 'Konfigurasi eligibilitas anggota berhasil disimpan!');
    }

    public function advanceToAllocation()
    {
        $this->saveEligibility();
        $session = $this->session;
        if (!$session) return;

        $session->transitionTo(RatSession::STATUS_MEMBERS_LOCKED);
        return redirect()->route('admin.rat.allocation', ['session' => $session->id]);
    }

    public function goBack()
    {
        return redirect()->route('admin.rat.setup');
    }

    public function render()
    {
        $session = $this->session;
        $query = Member::query();

        if ($this->searchMember) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->searchMember . '%')
                  ->orWhere('nomorAnggota', 'like', '%' . $this->searchMember . '%')
                  ->orWhere('unitKerja', 'like', '%' . $this->searchMember . '%');
            });
        }

        if ($this->filterStatus === 'ACTIVE') {
            $query->where('status', 'ACTIVE');
        } elseif ($this->filterStatus === 'INACTIVE') {
            $query->where('status', '!=', 'ACTIVE');
        }

        $members = $query->orderByRaw("CASE WHEN status = 'ACTIVE' THEN 0 ELSE 1 END")
            ->orderBy('name')
            ->paginate(20);

        return view('livewire.admin.rat-eligibility', [
            'ratSession' => $session,
            'members' => $members,
            'summary' => $this->summary,
        ])->layout('layouts.admin');
    }
}
