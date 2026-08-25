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
    public $disbursementFilter = 'ALL'; // Alias for backward compatibility

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

    public function getSessionProperty()
    {
        return RatSession::find($this->sessionId);
    }

    public function disburseSingle($distributionId)
    {
        $dist = MemberShuDistribution::where('rat_session_id', $this->sessionId)
            ->where('id', $distributionId)
            ->first();

        if (!$dist || $dist->is_disbursed) return;

        $dist->markAsDisbursed();
        session()->flash('success', "SHU untuk {$dist->member?->name} berhasil dicairkan (Tunai/Transfer).");
    }

    public function disburseToSukarela($distributionId)
    {
        $dist = MemberShuDistribution::where('rat_session_id', $this->sessionId)
            ->where('id', $distributionId)
            ->first();

        if (!$dist || $dist->is_disbursed) return;

        $shuAmount = (float) $dist->shu_amount;
        if ($shuAmount > 0 && $dist->member) {
            // Add to Simpanan Sukarela balance
            $dist->member->addSimpanan(
                'SUKARELA',
                $shuAmount,
                "Pencairan SHU RAT {$dist->ratSession?->year} ke Simpanan Sukarela"
            );
        }

        $dist->markAsDisbursed();
        session()->flash('success', "SHU Rp " . number_format($shuAmount, 0, ',', '.') . " untuk {$dist->member?->name} berhasil dimasukkan ke Simpanan Sukarela!");
    }

    public function toggleDisbursed($distributionId)
    {
        $dist = MemberShuDistribution::where('rat_session_id', $this->sessionId)
            ->where('id', $distributionId)
            ->first();

        if (!$dist) return;

        if ($dist->is_disbursed) {
            $dist->markAsPending();
            session()->flash('info', "Pencairan SHU untuk {$dist->member?->name} dibatalkan.");
        } else {
            $dist->markAsDisbursed();
            session()->flash('success', "SHU untuk {$dist->member?->name} berhasil dicairkan.");
        }
    }

    public function batchDisburse()
    {
        $distributions = MemberShuDistribution::where('rat_session_id', $this->sessionId)
            ->where('is_disbursed', false)
            ->get();

        $count = 0;
        foreach ($distributions as $dist) {
            $dist->markAsDisbursed();
            $count++;
        }

        session()->flash('success', "Berhasil mencairkan SHU untuk {$count} anggota.");
    }

    public function disburseAll()
    {
        $this->batchDisburse();
    }

    public function completeSession()
    {
        $session = $this->session;
        if (!$session) return;

        if ($session->transitionTo(RatSession::STATUS_COMPLETED)) {
            session()->flash('success', 'Seluruh pencairan SHU telah diselesaikan dan sesi RAT resmi ditutup!');
        } else {
            session()->flash('error', 'Gagal merubah status ke Selesai.');
        }
    }

    public function goBack()
    {
        return redirect()->route('admin.rat.allocation', ['session' => $this->sessionId]);
    }

    public function getStatsProperty()
    {
        $session = $this->session;
        if (!$session) {
            return [
                'total' => 0,
                'disbursed' => 0,
                'pending' => 0,
                'totalAmount' => 0,
                'disbursedAmount' => 0,
                'pendingAmount' => 0,
                'percentage' => 0,
            ];
        }

        $query = MemberShuDistribution::where('rat_session_id', $session->id);
        $total = (clone $query)->count();
        $disbursed = (clone $query)->where('is_disbursed', true)->count();
        $pending = $total - $disbursed;
        $totalAmount = (float) (clone $query)->sum('shu_amount');
        $disbursedAmount = (float) (clone $query)->where('is_disbursed', true)->sum('shu_amount');
        $pendingAmount = $totalAmount - $disbursedAmount;
        $percentage = $total > 0 ? round(($disbursed / $total) * 100, 1) : 0;

        return [
            'total' => $total,
            'disbursed' => $disbursed,
            'pending' => $pending,
            'totalAmount' => $totalAmount,
            'disbursedAmount' => $disbursedAmount,
            'pendingAmount' => $pendingAmount,
            'percentage' => $percentage,
        ];
    }

    public function render()
    {
        $session = $this->session;
        $distributions = collect();

        if ($session) {
            $query = MemberShuDistribution::with('member')
                ->where('rat_session_id', $session->id);

            $filter = $this->filterDisbursed ?: $this->disbursementFilter;

            if ($filter === 'PENDING') {
                $query->where('is_disbursed', false);
            } elseif ($filter === 'DISBURSED') {
                $query->where('is_disbursed', true);
            }

            if ($this->searchMember) {
                $query->whereHas('member', function ($q) {
                    $q->where('name', 'like', '%' . $this->searchMember . '%')
                      ->orWhere('nomorAnggota', 'like', '%' . $this->searchMember . '%');
                });
            }

            $distributions = $query->orderBy('is_disbursed', 'asc')->orderByDesc('shu_amount')->paginate(20);
        }

        return view('livewire.admin.rat-disbursement', [
            'ratSession' => $session,
            'distributions' => $distributions,
            'stats' => $this->stats,
        ])->layout('layouts.admin');
    }
}
