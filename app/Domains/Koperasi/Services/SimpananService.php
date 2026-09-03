<?php

namespace App\Domains\Koperasi\Services;

use App\Domains\Koperasi\Models\Member;
use App\Domains\Koperasi\Models\SimpananTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SimpananService
{
    /**
     * Helper to dynamically detect memberId vs member_id column name.
     */
    public function getMemberCol(string $table): string
    {
        if (Schema::hasColumn($table, 'memberId')) {
            return 'memberId';
        }
        if (Schema::hasColumn($table, 'member_id')) {
            return 'member_id';
        }
        return 'memberId';
    }

    /**
     * Recalculate and synchronize net savings balances for a member in DB.
     * Takes both SETOR and TARIK transaction types into account for all savings categories.
     */
    public function recalculateMemberBalances(int $memberId): array
    {
        $trxCol = $this->getMemberCol('simpanan_transactions');

        // Simpanan Pokok: SETOR - TARIK
        $pokokMasuk = (float) DB::table('simpanan_transactions')
            ->where($trxCol, $memberId)
            ->where('type', 'POKOK')
            ->where(function ($q) {
                $q->whereNull('transactionType')->orWhere('transactionType', 'SETOR');
            })
            ->where('status', 'APPROVED')
            ->sum('amount');

        $pokokKeluar = (float) DB::table('simpanan_transactions')
            ->where($trxCol, $memberId)
            ->where('type', 'POKOK')
            ->where('transactionType', 'TARIK')
            ->where('status', 'APPROVED')
            ->sum('amount');

        $sumPokok = max(0, $pokokMasuk - $pokokKeluar);

        // Simpanan Wajib: SETOR - TARIK
        $wajibMasuk = (float) DB::table('simpanan_transactions')
            ->where($trxCol, $memberId)
            ->where('type', 'WAJIB')
            ->where(function ($q) {
                $q->whereNull('transactionType')->orWhere('transactionType', 'SETOR');
            })
            ->where('status', 'APPROVED')
            ->sum('amount');

        $wajibKeluar = (float) DB::table('simpanan_transactions')
            ->where($trxCol, $memberId)
            ->where('type', 'WAJIB')
            ->where('transactionType', 'TARIK')
            ->where('status', 'APPROVED')
            ->sum('amount');

        $sumWajib = max(0, $wajibMasuk - $wajibKeluar);

        // Simpanan Sukarela: SETOR - TARIK
        $sukarelaMasuk = (float) DB::table('simpanan_transactions')
            ->where($trxCol, $memberId)
            ->where('type', 'SUKARELA')
            ->where(function ($q) {
                $q->whereNull('transactionType')->orWhere('transactionType', 'SETOR');
            })
            ->where('status', 'APPROVED')
            ->sum('amount');

        $sukarelaKeluar = (float) DB::table('simpanan_transactions')
            ->where($trxCol, $memberId)
            ->where(function ($q) {
                $q->where('type', 'TARIK_SUKARELA')
                  ->orWhere(function ($sub) {
                      $sub->where('type', 'SUKARELA')->where('transactionType', 'TARIK');
                  });
            })
            ->where('status', 'APPROVED')
            ->sum('amount');

        $sumSukarela = max(0, $sukarelaMasuk - $sukarelaKeluar);

        DB::table('members')
            ->where('id', $memberId)
            ->update([
                'simpananPokok' => $sumPokok,
                'simpananWajib' => $sumWajib,
                'simpananSukarela' => $sumSukarela,
            ]);

        return [
            'simpananPokok' => $sumPokok,
            'simpananWajib' => $sumWajib,
            'simpananSukarela' => $sumSukarela,
        ];
    }

    /**
     * Build the 12-month Simpanan Wajib grid status for a given member and year.
     */
    public function buildSimwaGrid(Member $member, int $selectedYear): array
    {
        $grid = [];
        $today = Carbon::now();
        $joinDate = $member->joinDate ? Carbon::parse($member->joinDate)->startOfMonth() : $today->startOfMonth();
        $trxCol = $this->getMemberCol('simpanan_transactions');

        $txs = SimpananTransaction::where($trxCol, $member->id)
            ->where('status', 'APPROVED')
            ->get();

        $imports = Schema::hasTable('audit_simwa_imports')
            ? DB::table('audit_simwa_imports')->where('matched_member_id', $member->id)->get()
            : collect();

        $monthsName = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        for ($i = 1; $i <= 12; $i++) {
            $currentMonthDate = Carbon::createFromDate($selectedYear, $i, 1)->endOfMonth();
            $periodKey = sprintf('%s-%02d', $selectedYear, $i);
            $monthName = $monthsName[$i];

            $hasTx = $txs->filter(function ($t) use ($selectedYear, $i, $periodKey, $monthName) {
                // Primary: Strict billingMonth matching
                if (!empty($t->billingMonth) && $t->billingMonth !== '-') {
                    return $t->billingMonth === $periodKey;
                }

                // Fallback: Check created_at date and notes
                $txYear = (int) $t->created_at->format('Y');
                $txMonth = (int) $t->created_at->format('n');

                if (!empty($t->notes) && preg_match('/20\d{2}/', $t->notes, $matches)) {
                    $txYear = (int) $matches[0];
                }

                $dateMatch = ($txYear === $selectedYear && $txMonth === $i);
                $notesMatch = !empty($t->notes) && str_contains(strtolower($t->notes), strtolower($monthName)) && str_contains($t->notes, (string) $selectedYear);

                if ($t->type === 'WAJIB') {
                    return $dateMatch || $notesMatch;
                }

                if ($t->type === 'SUKARELA' && (!empty($t->notes) && (str_contains(strtolower($t->notes), 'payroll') || str_contains(strtolower($t->notes), 'tabungan')))) {
                    return $dateMatch || $notesMatch;
                }

                return false;
            })->first();

            $hasImport = $imports->filter(function ($imp) use ($periodKey) {
                return $imp->period === $periodKey && (
                    str_contains(strtoupper($imp->raw_uraian), 'SIMWA') ||
                    str_contains(strtoupper($imp->raw_uraian), 'TABUNGAN') ||
                    str_contains(strtoupper($imp->raw_uraian), 'SUKARELA')
                );
            })->first();

            $status = 'UNPAID';
            $paidDate = null;
            $paidAmount = 0;

            if ($hasTx) {
                $status = 'PAID';
                $paidDate = $hasTx->created_at->format('d M Y');
                $paidAmount = (float) $hasTx->amount;
            } elseif ($hasImport) {
                $status = 'PAID';
                $paidDate = 'Payroll / Import';
                $paidAmount = (float) $hasImport->amount;
            } elseif ($currentMonthDate->isFuture() && $currentMonthDate->format('Y-m') > $today->format('Y-m')) {
                $status = 'FUTURE';
            } elseif ($currentMonthDate->lt($joinDate)) {
                $status = 'NOT_MEMBER';
            }

            $grid[$i] = [
                'monthNum' => $i,
                'monthName' => Carbon::create()->month($i)->translatedFormat('M'),
                'fullName' => $monthName,
                'periodKey' => $periodKey,
                'status' => $status,
                'paidDate' => $paidDate,
                'paidAmount' => $paidAmount,
            ];
        }

        return $grid;
    }

    /**
     * Mark a period as PAID for a member via 1-Click Audit.
     */
    public function quickSetPaidPeriod(int $memberId, string $periodKey, string $monthName, ?float $amount = null, ?int $processedBy = null): bool
    {
        $member = Member::findOrFail($memberId);
        $amount = $amount ?? ($member->monthly_simpanan_wajib > 0 ? (float) $member->monthly_simpanan_wajib : 50000);
        $memberService = app(MemberService::class);

        $trx = $memberService->addSimpanan(
            $memberId,
            'WAJIB',
            $amount,
            "Koreksi Audit Admin: Setor Wajib - {$monthName}",
            null,
            $processedBy ?? auth()->id()
        );

        if (Schema::hasColumn('simpanan_transactions', 'billingMonth')) {
            DB::table('simpanan_transactions')
                ->where('id', $trx->id)
                ->update(['billingMonth' => $periodKey]);
        }

        if (Schema::hasTable('simpanan_bills')) {
            $billCol = $this->getMemberCol('simpanan_bills');
            DB::table('simpanan_bills')
                ->where($billCol, $memberId)
                ->where('billingMonth', $periodKey)
                ->where('type', 'WAJIB')
                ->update([
                    'paymentStatus' => 'PAID',
                    'paidAmount' => $amount,
                    'remainingAmount' => 0,
                    'paidAt' => now(),
                ]);
        }

        $this->recalculateMemberBalances($memberId);
        return true;
    }

    /**
     * Mark a period as UNPAID for a member via 1-Click Audit.
     */
    public function quickSetUnpaidPeriod(int $memberId, string $periodKey, string $monthName): bool
    {
        $parts = explode('-', $periodKey);
        $year = (int) ($parts[0] ?? date('Y'));
        $monthNum = (int) ($parts[1] ?? 1);
        $trxCol = $this->getMemberCol('simpanan_transactions');

        // Delete transactions strictly scoped to this period
        DB::table('simpanan_transactions')
            ->where($trxCol, $memberId)
            ->where(function ($q) use ($periodKey, $monthName, $year, $monthNum) {
                $q->where(function ($sub) use ($periodKey) {
                      if (Schema::hasColumn('simpanan_transactions', 'billingMonth')) {
                          $sub->where('billingMonth', $periodKey);
                      }
                  })
                  ->orWhere(function ($sub) use ($year, $monthNum) {
                      $sub->where('type', 'WAJIB')
                          ->whereYear('created_at', $year)
                          ->whereMonth('created_at', $monthNum);
                  })
                  ->orWhere(function ($sub) use ($monthName, $year) {
                      $sub->where('type', 'WAJIB')
                          ->where('notes', 'like', "%{$monthName}%")
                          ->where('notes', 'like', "%{$year}%");
                  });
            })
            ->delete();

        // Clear matched audit import for this period
        if (Schema::hasTable('audit_simwa_imports')) {
            DB::table('audit_simwa_imports')
                ->where('matched_member_id', $memberId)
                ->where('period', $periodKey)
                ->delete();
        }

        // Reset bill status if bill exists
        if (Schema::hasTable('simpanan_bills')) {
            $billCol = $this->getMemberCol('simpanan_bills');
            DB::table('simpanan_bills')
                ->where($billCol, $memberId)
                ->where('billingMonth', $periodKey)
                ->where('type', 'WAJIB')
                ->update([
                    'paymentStatus' => 'UNPAID',
                    'paidAmount' => 0,
                    'remainingAmount' => DB::raw('amount'),
                    'paidAt' => null,
                ]);
        }

        $this->recalculateMemberBalances($memberId);
        return true;
    }

    /**
     * Edit period setoran nominal and notes.
     */
    public function saveEditPeriodAmount(
        int $memberId,
        string $periodKey,
        string $monthName,
        string $type,
        float $newAmount,
        ?string $notes = null,
        ?int $processedBy = null
    ): bool {
        $parts = explode('-', $periodKey);
        $year = (int) ($parts[0] ?? date('Y'));
        $monthNum = (int) ($parts[1] ?? 1);
        $trxCol = $this->getMemberCol('simpanan_transactions');

        // Delete existing transactions for this period
        DB::table('simpanan_transactions')
            ->where($trxCol, $memberId)
            ->where(function ($q) use ($periodKey, $monthName, $type, $year, $monthNum) {
                $q->where(function ($sub) use ($periodKey) {
                      if (Schema::hasColumn('simpanan_transactions', 'billingMonth')) {
                          $sub->where('billingMonth', $periodKey);
                      }
                  })
                  ->orWhere(function ($sub) use ($type, $year, $monthNum) {
                      $sub->where('type', $type)
                          ->whereYear('created_at', $year)
                          ->whereMonth('created_at', $monthNum);
                  })
                  ->orWhere(function ($sub) use ($monthName, $type, $year) {
                      $sub->where('type', $type)
                          ->where('notes', 'like', "%{$monthName}%")
                          ->where('notes', 'like', "%{$year}%");
                  });
            })
            ->delete();

        // Clear import if amount set to 0
        if ($newAmount == 0 && Schema::hasTable('audit_simwa_imports')) {
            DB::table('audit_simwa_imports')
                ->where('matched_member_id', $memberId)
                ->where('period', $periodKey)
                ->delete();
        }

        if ($newAmount > 0) {
            $memberService = app(MemberService::class);
            $trx = $memberService->addSimpanan(
                $memberId,
                $type,
                $newAmount,
                $notes ?: "Koreksi Audit Admin: Setor {$type} - {$monthName}",
                null,
                $processedBy ?? auth()->id()
            );

            if (Schema::hasColumn('simpanan_transactions', 'billingMonth')) {
                DB::table('simpanan_transactions')
                    ->where('id', $trx->id)
                    ->update(['billingMonth' => $periodKey]);
            }

            if (Schema::hasTable('simpanan_bills')) {
                $billCol = $this->getMemberCol('simpanan_bills');
                DB::table('simpanan_bills')
                    ->where($billCol, $memberId)
                    ->where('billingMonth', $periodKey)
                    ->where('type', $type)
                    ->update([
                        'paymentStatus' => 'PAID',
                        'paidAmount' => $newAmount,
                        'remainingAmount' => 0,
                        'paidAt' => now(),
                    ]);
            }
        } else {
            if (Schema::hasTable('simpanan_bills')) {
                $billCol = $this->getMemberCol('simpanan_bills');
                DB::table('simpanan_bills')
                    ->where($billCol, $memberId)
                    ->where('billingMonth', $periodKey)
                    ->where('type', $type)
                    ->update([
                        'paymentStatus' => 'UNPAID',
                        'paidAmount' => 0,
                        'remainingAmount' => DB::raw('amount'),
                        'paidAt' => null,
                    ]);
            }
        }

        $this->recalculateMemberBalances($memberId);
        return true;
    }

    /**
     * Update member join date safely.
     */
    public function updateJoinDate(int $memberId, string $newDate): bool
    {
        DB::table('members')
            ->where('id', $memberId)
            ->update(['joinDate' => $newDate]);

        return true;
    }

    /**
     * Quick set member join date to month start of a period key (Y-m).
     */
    public function quickSetJoinMonth(int $memberId, string $periodKey): string
    {
        $newDate = Carbon::createFromFormat('Y-m', $periodKey)->startOfMonth()->format('Y-m-d');
        $this->updateJoinDate($memberId, $newDate);
        return $newDate;
    }
}
