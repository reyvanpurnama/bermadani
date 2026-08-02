<?php

namespace App\Livewire\Admin;

use App\Models\MemberShuDistribution;
use App\Models\RatSession;
use App\Services\ShuCalculationService;
use Livewire\Component;
use Livewire\WithPagination;

class RatDisbursement extends Component
{
    use WithPagination;

    public $sessionId;
    public $searchMember = '';
    public $filterDisbursed = 'ALL'; // ALL, PENDING, DISBURSED

    protected ShuCalculationService $shuService;

    public function boot(ShuCalculationService $shuService)
    {
        $this->shuService = $shuService;
    }

    public function mount($session)
    {
        $ratSession = RatSession::findOrFail($session);
        $this->sessionId = $ratSession->id;
    }

    public function updatingSearchMember()
    {
        $this->resetPage();
    }

    public function getSessionProperty()
    {
        return RatSession::find($this->sessionId);
    }

    public function getDisbursementStatsProperty()
    {
        $session = $this->session;
        if (!$session) return ['total' => 0, 'disbursed' => 0, 'pending' => 0, 'totalAmount' => 0, 'disbursedAmount' => 0, 'pendingAmount' => 0];

        $eligible = MemberShuDistribution::where('rat_session_id', $session->id)
            ->where('shu_amount', '>', 0);

        $total = (clone $eligible)->count();
        $disbursed = (clone $eligible)->where('is_disbursed', true)->count();
        $pending = $total - $disbursed;

        $totalAmount = (clone $eligible)->sum('shu_amount');
        $disbursedAmount = (clone $eligible)->where('is_disbursed', true)->sum('shu_amount');
        $pendingAmount = (float) $totalAmount - (float) $disbursedAmount;

        return [
            'total' => $total,
            'disbursed' => $disbursed,
            'pending' => $pending,
            'totalAmount' => (float) $totalAmount,
            'disbursedAmount' => (float) $disbursedAmount,
            'pendingAmount' => $pendingAmount,
            'percentage' => $total > 0 ? round(($disbursed / $total) * 100, 1) : 0,
        ];
    }

    public function toggleDisbursed($distributionId)
    {
        $session = $this->session;
        if (!$session || !$session->isFinalized()) {
            session()->flash('error', 'Sesi belum disahkan. Pencairan tidak dapat dilakukan.');
            return;
        }

        $dist = MemberShuDistribution::find($distributionId);
        if (!$dist || $dist->rat_session_id !== $session->id) return;

        if ($dist->is_disbursed) {
            $this->shuService->reverseDisbursement($dist);
            session()->flash('info', 'Pencairan dibatalkan.');
        } else {
            $this->shuService->disburseShu($dist);
            session()->flash('success', 'SHU berhasil dicairkan & tercatat di Keuangan.');
        }
    }

    /**
     * Batch disburse all pending members.
     */
    public function batchDisburse()
    {
        $session = $this->session;
        if (!$session || !$session->isFinalized()) return;

        $pendingDists = MemberShuDistribution::where('rat_session_id', $session->id)
            ->where('shu_amount', '>', 0)
            ->where('is_disbursed', false)
            ->get();

        $count = 0;
        foreach ($pendingDists as $dist) {
            $this->shuService->disburseShu($dist);
            $count++;
        }

        session()->flash('success', "{$count} pencairan SHU berhasil dilakukan secara batch.");
    }

    public function completeSession()
    {
        $session = $this->session;
        if (!$session) return;

        $stats = $this->disbursementStats;
        if ($stats['pending'] > 0) {
            session()->flash('error', "Masih ada {$stats['pending']} anggota belum dicairkan. Selesaikan semua pencairan terlebih dahulu.");
            return;
        }

        if ($session->transitionTo(RatSession::STATUS_COMPLETED)) {
            session()->flash('success', 'Proses RAT selesai! Semua SHU telah dicairkan.');
        }
    }

    public function goBack()
    {
        $session = $this->session;
        return redirect()->route('admin.rat.allocation', ['session' => $session->id ?? 0]);
    }

    public function render()
    {
        $session = $this->session;
        $distributions = collect();

        if ($session) {
            $query = MemberShuDistribution::with('member')
                ->where('rat_session_id', $session->id)
                ->where('shu_amount', '>', 0); // Only show eligible members

            if ($this->searchMember) {
                $query->whereHas('member', function ($q) {
                    $q->where('name', 'like', '%' . $this->searchMember . '%')
                      ->orWhere('nomorAnggota', 'like', '%' . $this->searchMember . '%')
                      ->orWhere('unitKerja', 'like', '%' . $this->searchMember . '%');
                });
            }

            if ($this->filterDisbursed === 'PENDING') {
                $query->where('is_disbursed', false);
            } elseif ($this->filterDisbursed === 'DISBURSED') {
                $query->where('is_disbursed', true);
            }

            $distributions = $query->orderByDesc('shu_amount')->paginate(20);
        }

        return view('livewire.admin.rat-disbursement', [
            'ratSession' => $session,
            'distributions' => $distributions,
            'stats' => $this->disbursementStats,
        ])->layout('layouts.admin');
    }
}
