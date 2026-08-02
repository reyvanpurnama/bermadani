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

    public function toggleBalance()
    {
        $this->showBalance = !$this->showBalance;
    }

    public function getShuInfoProperty()
    {
        if (!$this->member) return null;

        // Try to fetch finalized RAT session distribution
        $latestSession = \App\Models\RatSession::where('status', 'FINALIZED')->orderByDesc('year')->first();

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
                    'portionPercentage' => (float) $dist->portion_percentage,
                    'simpananWajib' => (float) $dist->simpanan_wajib_amount,
                    'isDisbursed' => $dist->is_disbursed,
                ];
            }
        }

        // Live calculation estimate from latest draft/configured session
        $latestDraft = \App\Models\RatSession::whereIn('status', ['DRAFT', 'CONFIGURED', 'MEMBERS_LOCKED'])
            ->orderByDesc('year')->first();

        $totalSimwa = (float) Member::where('status', 'ACTIVE')->sum('simpananWajib');
        $totalSimpok = (float) Member::where('status', 'ACTIVE')->sum('simpananPokok');
        $totalSimpanan = max(1, $totalSimwa + $totalSimpok);
        $memberSimpanan = (float) ($this->member->simpananWajib ?? 0) + (float) ($this->member->simpananPokok ?? 0);
        $portion = ($memberSimpanan / $totalSimpanan);

        if ($latestDraft && (float) $latestDraft->total_member_shu > 0) {
            $estimatedTotalShu = (float) $latestDraft->total_member_shu;
            $estimatedYear = $latestDraft->year;
            $estimatedTitle = $latestDraft->title ?? 'RAT Tahun Buku ' . $latestDraft->year;
        } else {
            // Compute from financial transactions
            $currentYear = (int) date('Y');
            $income = (float) \App\Models\FinancialTransaction::whereYear('transactionDate', $currentYear)->where('type', 'INCOME')->sum('amount');
            $expense = (float) \App\Models\FinancialTransaction::whereYear('transactionDate', $currentYear)->where('type', 'EXPENSE')->sum('amount');
            $estimatedTotalShu = max(0, $income - $expense);
            $estimatedYear = $currentYear;
            $estimatedTitle = 'RAT Tahun Buku ' . $currentYear;
        }

        $estimatedShu = round($portion * $estimatedTotalShu, 2);

        return [
            'year' => $estimatedYear,
            'title' => $estimatedTitle,
            'isFinalized' => false,
            'shuAmount' => $estimatedShu,
            'portionPercentage' => round($portion * 100, 4),
            'simpananWajib' => $memberSimpanan,
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
