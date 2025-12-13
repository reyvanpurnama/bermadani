<?php

namespace App\Console\Commands;

use App\Models\Member;
use App\Models\SimpananTransaction;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DebitSimpananWajib extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'simpanan:debit-wajib {--month= : Month to process (YYYY-MM format, default: current month)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process monthly simpanan wajib auto-debit for all active members';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $monthStr = $this->option('month') ?? now()->format('Y-m');
        
        try {
            $month = Carbon::createFromFormat('Y-m', $monthStr)->startOfMonth();
        } catch (\Exception $e) {
            $this->error("Invalid month format. Use YYYY-MM (e.g., 2026-01)");
            return 1;
        }

        $this->info("Processing simpanan wajib debit for: {$month->format('F Y')}");
        $this->newLine();

        // Get all active members who joined before or during this month
        $members = Member::where('status', 'ACTIVE')
            ->whereDate('joinDate', '<=', $month->endOfMonth())
            ->get();

        if ($members->isEmpty()) {
            $this->warn('No active members found.');
            return 0;
        }

        $this->info("Found {$members->count()} active members");
        $this->newLine();

        $bar = $this->output->createProgressBar($members->count());
        $bar->start();

        $processed = 0;
        $skipped = 0;
        $errors = [];

        DB::beginTransaction();

        try {
            foreach ($members as $member) {
                // Skip if already debited this month
                $existingDebit = SimpananTransaction::where('memberId', $member->id)
                    ->where('type', 'WAJIB')
                    ->where('transactionType', 'SETOR')
                    ->whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->where('notes', 'LIKE', 'Auto-debit simpanan wajib%')
                    ->exists();

                if ($existingDebit) {
                    $skipped++;
                    $bar->advance();
                    continue;
                }

                // Calculate new balance
                $currentBalance = $member->simpananWajib ?? 0;
                $newBalance = $currentBalance + $member->monthly_wajib_amount;

                // Create transaction
                SimpananTransaction::create([
                    'memberId' => $member->id,
                    'type' => 'WAJIB',
                    'transactionType' => 'SETOR',
                    'amount' => $member->monthly_wajib_amount,
                    'balanceAfter' => $newBalance,
                    'notes' => "Auto-debit simpanan wajib - {$month->format('F Y')}",
                    'processedBy' => 1, // System user
                    'status' => 'PENDING', // Will be approved by admin
                    'created_at' => $month,
                    'updated_at' => $month,
                ]);

                // Update last debit date (but don't update simpananWajib yet, wait for approval)
                $member->update([
                    'last_wajib_debit_date' => $month->format('Y-m-d')
                ]);

                $processed++;
                $bar->advance();
            }

            DB::commit();

            $bar->finish();
            $this->newLine(2);

            $this->info("✅ Debit completed!");
            $this->table(
                ['Metric', 'Count'],
                [
                    ['Processed', $processed],
                    ['Skipped (already debited)', $skipped],
                    ['Errors', count($errors)],
                ]
            );

            if (!empty($errors)) {
                $this->newLine();
                $this->error('Errors:');
                foreach (array_slice($errors, 0, 10) as $error) {
                    $this->line("  • {$error}");
                }
                if (count($errors) > 10) {
                    $this->line("  ... and " . (count($errors) - 10) . " more");
                }
            }

            $this->newLine();
            $this->warn('⚠️  Note: All transactions are in PENDING status. Admin needs to approve them.');

            return 0;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Transaction failed: {$e->getMessage()}");
            return 1;
        }
    }
}
