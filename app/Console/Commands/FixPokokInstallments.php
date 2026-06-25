<?php

namespace App\Console\Commands;

use App\Models\Member;
use App\Models\SimpananTransaction;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixPokokInstallments extends Command
{
    protected $signature = 'fix:pokok-installments';

    protected $description = 'Fix members who were registered with CICIL_4X but first installment was auto-paid instead of going through payroll';

    public function handle()
    {
        $this->info('🔍 Scanning for members with auto-paid first POKOK installment...');

        // Find members who have future CICIL_4X bills (type=POKOK, paidAmount=0)
        // but also have a "Simpanan pokok awal" transaction (auto-paid first installment)
        $memberIds = SimpananTransaction::where('type', 'POKOK')
            ->where('transactionType', 'SETOR')
            ->where('paidAmount', 0)
            ->whereNotNull('billingMonth')
            ->where('billStatus', 'APPROVED')
            ->where('notes', 'like', 'Cicilan Simpanan Pokok Ke-%')
            ->distinct()
            ->pluck('memberId');

        if ($memberIds->isEmpty()) {
            $this->info('✅ No members found that need fixing.');
            return 0;
        }

        $this->info("Found {$memberIds->count()} member(s) with CICIL_4X bills.");

        $fixed = 0;

        foreach ($memberIds as $memberId) {
            $member = Member::find($memberId);
            if (!$member) continue;

            // Check if this member has an auto-paid "Simpanan pokok awal" transaction
            $autoPaidTx = SimpananTransaction::where('memberId', $memberId)
                ->where('type', 'POKOK')
                ->where('transactionType', 'SETOR')
                ->where('notes', 'Simpanan pokok awal')
                ->where('status', 'APPROVED')
                ->whereNull('billingMonth')
                ->first();

            if (!$autoPaidTx) {
                $this->line("  ⏭ {$member->name} - No auto-paid transaction found, skipping.");
                continue;
            }

            // Check if a bill for the current month already exists
            $currentBillingMonth = Carbon::parse($autoPaidTx->created_at)->format('Y-m');
            $existingBill = SimpananTransaction::where('memberId', $memberId)
                ->where('type', 'POKOK')
                ->where('transactionType', 'SETOR')
                ->where('billingMonth', $currentBillingMonth)
                ->where('billStatus', 'APPROVED')
                ->first();

            if ($existingBill) {
                $this->line("  ⏭ {$member->name} - Bill for {$currentBillingMonth} already exists, skipping.");
                continue;
            }

            DB::beginTransaction();
            try {
                $amount = (float) $autoPaidTx->amount;

                // 1. Create proper payroll-billable transaction for the first month
                SimpananTransaction::create([
                    'memberId' => $memberId,
                    'type' => 'POKOK',
                    'transactionType' => 'SETOR',
                    'amount' => $amount,
                    'paidAmount' => 0,
                    'billingMonth' => $currentBillingMonth,
                    'billStatus' => 'APPROVED',
                    'status' => 'APPROVED',
                    'balanceAfter' => 0,
                    'notes' => 'Cicilan Simpanan Pokok Ke-1 dari 4',
                    'processedBy' => $autoPaidTx->processedBy,
                ]);

                // 2. Reverse the auto-paid amount from member's simpananPokok balance
                $member->decrement('simpananPokok', $amount);

                // 3. Delete the auto-paid transaction (it will be replaced by payroll processing)
                $autoPaidTx->delete();

                DB::commit();

                $this->info("  ✅ {$member->name} - Fixed! Created bill for {$currentBillingMonth}, reversed Rp " . number_format($amount, 0, ',', '.'));
                $fixed++;

            } catch (\Exception $e) {
                DB::rollBack();
                $this->error("  ❌ {$member->name} - Error: {$e->getMessage()}");
            }
        }

        $this->newLine();
        $this->info("🏁 Done. Fixed {$fixed} member(s).");

        return 0;
    }
}
