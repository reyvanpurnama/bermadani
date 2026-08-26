<?php

namespace App\Domains\Koperasi\Services;

use App\Domains\Koperasi\Models\Member;
use App\Domains\Koperasi\Models\MemberShuDistribution;
use App\Domains\Koperasi\Models\RatSession;
use App\Domains\Koperasi\Models\SimpananTransaction;
use App\Models\Transaction;
use App\Models\FinancialTransaction;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ShuCalculationService
{
    /**
     * Determine if a member is eligible for SHU based on session rules.
     */
    public function isMemberEligible(
        int $memberId,
        ?string $joinDate,
        string $status,
        ?string $joinDateCutoff,
        array $excludedMemberIds = [],
        array $includedMemberIds = []
    ): bool {
        // Manual include overrides everything
        if (in_array($memberId, $includedMemberIds)) {
            return true;
        }

        // Manual exclude
        if (in_array($memberId, $excludedMemberIds)) {
            return false;
        }

        // Only ACTIVE members are eligible by default
        if ($status !== 'ACTIVE') {
            return false;
        }

        // Check join date cutoff
        if ($joinDateCutoff && $joinDate) {
            $joinDateTime = Carbon::parse($joinDate);
            $cutoffDateTime = Carbon::parse($joinDateCutoff)->endOfDay();
            if ($joinDateTime->gt($cutoffDateTime)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Calculate a member's simpanan balance at a specific cutoff date.
     * Works by subtracting deposits after cutoff and adding back withdrawals after cutoff.
     */
    public function getMemberSavingsAtCutoff(Member $member, string $type, ?string $cutoffDate = null): float
    {
        $currentBalance = (float) ($type === 'POKOK' ? $member->simpananPokok : $member->simpananWajib);

        if (!$cutoffDate) {
            return $currentBalance;
        }

        $cutoffDateTime = Carbon::parse($cutoffDate)->endOfDay();

        // Sum deposits (SETOR) after the cutoff date
        $depositsAfter = SimpananTransaction::where('memberId', $member->id)
            ->where('status', 'APPROVED')
            ->where('transactionType', 'SETOR')
            ->where('type', $type)
            ->where('created_at', '>', $cutoffDateTime)
            ->sum('amount');

        // Sum withdrawals (TARIK) after the cutoff date
        $withdrawalsAfter = SimpananTransaction::where('memberId', $member->id)
            ->where('status', 'APPROVED')
            ->where('transactionType', 'TARIK')
            ->where('type', $type)
            ->where('created_at', '>', $cutoffDateTime)
            ->sum('amount');

        return max(0, $currentBalance - (float) $depositsAfter + (float) $withdrawalsAfter);
    }

    /**
     * Get total transaksi belanja (POS purchases) for a member in a given year.
     * Used to calculate Jasa Usaha component.
     */
    public function getMemberTransactionTotal(int $memberId, int $year): float
    {
        return (float) Transaction::where('memberId', $memberId)
            ->whereYear('created_at', $year)
            ->sum('totalAmount');
    }

    /**
     * Get all eligible members with their simpanan snapshots.
     */
    public function getEligibleMembers(RatSession $session): Collection
    {
        $allMembers = Member::all();
        $cutoff = $session->join_date_cutoff?->format('Y-m-d');
        $excluded = $session->excluded_member_ids ?? [];
        $included = $session->included_member_ids ?? [];

        return $allMembers->map(function ($member) use ($cutoff, $excluded, $included, $session) {
            $isEligible = $this->isMemberEligible(
                $member->id,
                $member->joinDate?->format('Y-m-d H:i:s'),
                $member->status,
                $cutoff,
                $excluded,
                $included
            );

            $simpok = $this->getMemberSavingsAtCutoff($member, 'POKOK', $cutoff);
            $simwa = $this->getMemberSavingsAtCutoff($member, 'WAJIB', $cutoff);
            $totalTransaksi = $this->getMemberTransactionTotal($member->id, $session->year);

            return (object) [
                'member' => $member,
                'is_eligible' => $isEligible,
                'simpanan_pokok' => $simpok,
                'simpanan_wajib' => $simwa,
                'total_simpanan' => $simpok + $simwa,
                'total_transaksi' => $totalTransaksi,
            ];
        });
    }

    /**
     * Calculate summary statistics for eligible members.
     */
    public function calculateSummary(RatSession $session): array
    {
        $members = $this->getEligibleMembers($session);
        $eligible = $members->where('is_eligible', true);

        $totalSimpok = $eligible->sum('simpanan_pokok');
        $totalSimwa = $eligible->sum('simpanan_wajib');
        $totalSimpanan = $eligible->sum('total_simpanan');
        $totalTransaksi = $eligible->sum('total_transaksi');

        $totalNetProfit = (float) $session->total_net_profit;
        $totalMemberShu = (float) $session->total_member_shu;
        $retainedAmount = max(0, $totalNetProfit - $totalMemberShu);

        // 5-pos breakdown
        $cadPct = (float) ($session->cadangan_percentage ?? 25.00);
        $simpPct = (float) ($session->jasa_simpanan_percentage ?? 30.00);
        $usahaPct = (float) ($session->jasa_usaha_percentage ?? 25.00);
        $pengPct = (float) ($session->pengurus_percentage ?? 10.00);
        $sosPct = (float) ($session->dana_sosial_percentage ?? 10.00);

        $jasaSimpananPool = round($totalMemberShu * ($simpPct / 100), 2);
        $jasaUsahaPool = round($totalMemberShu * ($usahaPct / 100), 2);
        $cadanganPool = round($totalMemberShu * ($cadPct / 100), 2);
        $pengurusPool = round($totalMemberShu * ($pengPct / 100), 2);
        $danaSosialPool = round($totalMemberShu * ($sosPct / 100), 2);

        return [
            'eligibleCount' => $eligible->count(),
            'totalMembers' => $members->count(),
            'totalSimpok' => $totalSimpok,
            'totalSimwa' => $totalSimwa,
            'totalSimpanan' => max(1, $totalSimpanan), // avoid div by 0
            'totalTransaksi' => max(1, $totalTransaksi), // avoid div by 0
            'totalNetProfit' => $totalNetProfit,
            'totalMemberShu' => $totalMemberShu,
            'retainedAmount' => $retainedAmount,
            'jasaSimpananPool' => $jasaSimpananPool,
            'jasaUsahaPool' => $jasaUsahaPool,
            'cadanganPool' => $cadanganPool,
            'pengurusPool' => $pengurusPool,
            'danaSosialPool' => $danaSosialPool,
        ];
    }

    /**
     * Calculate and persist SHU distributions for all members.
     * Returns the number of distributions created/updated.
     */
    public function calculateAndSaveDistributions(RatSession $session): int
    {
        $members = $this->getEligibleMembers($session);
        $eligible = $members->where('is_eligible', true);

        $totalSimpanan = max(1, $eligible->sum('total_simpanan'));
        $totalTransaksi = max(1, $eligible->sum('total_transaksi'));
        $totalMemberShu = (float) $session->total_member_shu;

        $simpPct = (float) ($session->jasa_simpanan_percentage ?? 30.00);
        $usahaPct = (float) ($session->jasa_usaha_percentage ?? 25.00);

        // Pool amounts for member-distributable components
        $jasaSimpananPool = round($totalMemberShu * ($simpPct / 100), 2);
        $jasaUsahaPool = round($totalMemberShu * ($usahaPct / 100), 2);

        $count = 0;

        foreach ($members as $item) {
            $member = $item->member;

            if ($item->is_eligible) {
                // Jasa Simpanan: proportional to total simpanan
                $simpananPortion = $item->total_simpanan / $totalSimpanan;
                $jasaSimpanan = round($simpananPortion * $jasaSimpananPool, 2);

                // Jasa Usaha: proportional to total transactions
                $usahaPortion = $totalTransaksi > 1 ? ($item->total_transaksi / $totalTransaksi) : 0;
                $jasaUsaha = round($usahaPortion * $jasaUsahaPool, 2);

                $totalShu = $jasaSimpanan + $jasaUsaha;
                $overallPortion = $totalMemberShu > 0 ? (($totalShu / $totalMemberShu) * 100) : 0;
            } else {
                $simpananPortion = 0;
                $jasaSimpanan = 0;
                $usahaPortion = 0;
                $jasaUsaha = 0;
                $totalShu = 0;
                $overallPortion = 0;
            }

            MemberShuDistribution::updateOrCreate(
                [
                    'rat_session_id' => $session->id,
                    'member_id' => $member->id,
                ],
                [
                    'total_simpanan_amount' => $item->total_simpanan,
                    'simpanan_pokok_snapshot' => $item->simpanan_pokok,
                    'simpanan_wajib_snapshot' => $item->simpanan_wajib,
                    'portion_percentage' => round($overallPortion, 4),
                    'shu_amount' => $totalShu,
                    'jasa_simpanan_amount' => $jasaSimpanan,
                    'jasa_usaha_amount' => $jasaUsaha,
                    'total_transaksi_amount' => $item->total_transaksi,
                ]
            );

            $count++;
        }

        // Update session snapshot
        $session->update([
            'total_simpanan_wajib_snapshot' => $totalSimpanan,
        ]);

        return $count;
    }

    /**
     * Create a FinancialTransaction for SHU disbursement.
     */
    public function disburseShu(MemberShuDistribution $distribution): ?FinancialTransaction
    {
        $shuAmount = (float) $distribution->shu_amount;
        if ($shuAmount <= 0) {
            return null;
        }

        $distribution->loadMissing(['ratSession', 'member']);

        $tx = FinancialTransaction::create([
            'type' => 'EXPENSE',
            'category' => 'Pembagian SHU',
            'amount' => $shuAmount,
            'transactionDate' => now()->toDateString(),
            'description' => "Pencairan SHU RAT " . ($distribution->ratSession?->year ?? '') .
                " untuk " . ($distribution->member?->name ?? '') .
                " (" . ($distribution->member?->nomorAnggota ?? '') . ")",
            'userId' => auth()->id() ?? 1,
        ]);

        $distribution->update([
            'is_disbursed' => true,
            'disbursed_at' => now(),
            'financial_transaction_id' => $tx->id,
        ]);

        return $tx;
    }

    /**
     * Reverse a SHU disbursement.
     */
    public function reverseDisbursement(MemberShuDistribution $distribution): void
    {
        if ($distribution->financial_transaction_id) {
            FinancialTransaction::find($distribution->financial_transaction_id)?->delete();
        }

        $distribution->update([
            'is_disbursed' => false,
            'disbursed_at' => null,
            'financial_transaction_id' => null,
        ]);
    }

    /**
     * Auto-fetch net profit from financial transactions for a given year.
     */
    public function fetchNetProfitForYear(int $year): float
    {
        try {
            $income = FinancialTransaction::whereYear('transactionDate', $year)
                ->where('type', 'INCOME')
                ->sum('amount');
            $expense = FinancialTransaction::whereYear('transactionDate', $year)
                ->where('type', 'EXPENSE')
                ->sum('amount');
            return max(0, (float) ($income - $expense));
        } catch (\Throwable $e) {
            return 0;
        }
    }
}
