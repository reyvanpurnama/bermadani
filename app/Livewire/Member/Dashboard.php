<?php

namespace App\Livewire\Member;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Member;
use App\Models\SimpananTransaction;
use App\Models\Membership\TransferHistory; // unused
use App\Models\Loan;
use App\Models\Transaction;

#[Layout('layouts.member')]
class Dashboard extends Component
{
    public $member;
    public $recentTransactions = [];
    public $recentSimpanan = [];
    public $showBalance = true;
    public $unreadTransfers = [];
    public $unreadCount = 0;
    public $activeLoans = [];

    public function mount()
    {
        $user = auth()->user();
        $this->member = Member::where('userId', $user->id)->first();

        if ($this->member) {
            // Get Active Loans
            $this->activeLoans = Loan::where('member_id', $this->member->id)
                ->where('status', 'ACTIVE')
                ->latest('startDate')
                ->get();
            // Recent shopping transactions
            $this->recentTransactions = Transaction::where('memberId', $this->member->id)
                ->latest()
                ->take(5)
                ->get();

            // Recent simpanan activities
            $this->recentSimpanan = SimpananTransaction::where('memberId', $this->member->id)
                ->where('status', 'APPROVED')
                ->latest()
                ->take(5)
                ->get();

            // Check unread transfers (today only)
            if (\Illuminate\Support\Facades\Schema::hasColumn('simpanan_transactions', 'isRead')) {
                $this->unreadTransfers = SimpananTransaction::where('memberId', $this->member->id)
                    ->where('transactionType', 'TRANSFER_IN')
                    ->where('isRead', false)
                    ->whereDate('created_at', today())
                    ->with('relatedMember')
                    ->latest()
                    ->get();

                $this->unreadCount = $this->unreadTransfers->count();
            }
        }
    }

    public function toggleBalance()
    {
        $this->showBalance = !$this->showBalance;
    }

    public function getShuInfoProperty()
    {
        if (!$this->member) return null;

        // 1. Try to fetch finalized/disbursing/completed RAT session distribution
        $latestSession = \App\Models\RatSession::whereIn('status', ['FINALIZED', 'DISBURSING', 'COMPLETED'])
            ->orderByDesc('year')->first();

        if ($latestSession) {
            $dist = \App\Models\MemberShuDistribution::where('rat_session_id', $latestSession->id)
                ->where('member_id', $this->member->id)
                ->first();

            if ($dist) {
                return [
                    'year' => $latestSession->year,
                    'title' => $latestSession->title,
                    'isFinalized' => true,
                    'shuAmount' => (float) $dist->shu_amount,
                    'jasaSimpanan' => (float) $dist->jasa_simpanan_amount,
                    'jasaUsaha' => (float) $dist->jasa_usaha_amount,
                    'portionPercentage' => (float) $dist->portion_percentage,
                    'totalSimpanan' => (float) ($dist->total_simpanan_amount > 0 ? $dist->total_simpanan_amount : ((float)$dist->simpanan_pokok_snapshot + (float)$dist->simpanan_wajib_snapshot)),
                    'totalTransaksi' => (float) $dist->total_transaksi_amount,
                    'isDisbursed' => (bool) $dist->is_disbursed,
                ];
            }
        }

        // 2. Try to fetch latest draft/configured session distribution for live preview
        $latestDraft = \App\Models\RatSession::orderByDesc('year')->first();

        if ($latestDraft) {
            $dist = \App\Models\MemberShuDistribution::where('rat_session_id', $latestDraft->id)
                ->where('member_id', $this->member->id)
                ->first();

            if ($dist) {
                return [
                    'year' => $latestDraft->year,
                    'title' => $latestDraft->title,
                    'isFinalized' => false,
                    'shuAmount' => (float) $dist->shu_amount,
                    'jasaSimpanan' => (float) $dist->jasa_simpanan_amount,
                    'jasaUsaha' => (float) $dist->jasa_usaha_amount,
                    'portionPercentage' => (float) $dist->portion_percentage,
                    'totalSimpanan' => (float) ($dist->total_simpanan_amount > 0 ? $dist->total_simpanan_amount : ((float)$dist->simpanan_pokok_snapshot + (float)$dist->simpanan_wajib_snapshot)),
                    'totalTransaksi' => (float) $dist->total_transaksi_amount,
                    'isDisbursed' => (bool) $dist->is_disbursed,
                ];
            }
        }

        // 3. Fallback estimate if no session distribution exists
        $currentYear = (int) date('Y');
        $totalSimwa = (float) Member::where('status', 'ACTIVE')->sum('simpananWajib');
        $totalSimpok = (float) Member::where('status', 'ACTIVE')->sum('simpananPokok');
        $totalSimpanan = max(1, $totalSimwa + $totalSimpok);
        $memberSimpanan = (float) ($this->member->simpananWajib ?? 0) + (float) ($this->member->simpananPokok ?? 0);
        $portion = ($memberSimpanan / $totalSimpanan);

        return [
            'year' => $currentYear,
            'title' => 'RAT Tahun Buku ' . $currentYear,
            'isFinalized' => false,
            'shuAmount' => 0,
            'jasaSimpanan' => 0,
            'jasaUsaha' => 0,
            'portionPercentage' => round($portion * 100, 4),
            'totalSimpanan' => $memberSimpanan,
            'totalTransaksi' => 0,
            'isDisbursed' => false,
        ];
    }

    public function render()
    {
        return view('livewire.member.dashboard', [
            'shuInfo' => $this->shuInfo,
        ]);
    }
}
