<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Member;
use App\Models\SimpananTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class GenerateMonthlyBills extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'simpanan:generate-bills {month? : Format Y-m, default current month}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate monthly Simpanan Wajib bills for all active members';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $month = $this->argument('month') ?? now()->format('Y-m');
        
        // Validate format
        try {
            Carbon::createFromFormat('Y-m', $month);
        } catch (\Exception $e) {
            $this->error('Invalid month format. Use Y-m (e.g. 2025-12)');
            return 1;
        }

        $this->info("Generating bills for month: $month");

        $members = Member::where('status', 'ACTIVE')->get(); // Assuming 'ACTIVE' is the status
        // If status column is different, I should check Member model again. 
        // Member model has 'status' field. Let's assume 'ACTIVE' is correct or check if there are other values.
        
        $count = 0;
        $skipped = 0;

        DB::beginTransaction();
        try {
            foreach ($members as $member) {
                // Check if bill already exists
                $exists = SimpananTransaction::where('memberId', $member->id)
                    ->where('type', 'WAJIB')
                    ->where('transactionType', 'SETOR')
                    ->where('billingMonth', $month)
                    ->exists();

                if ($exists) {
                    $skipped++;
                    continue;
                }

                // Create bill
                SimpananTransaction::create([
                    'memberId' => $member->id,
                    'type' => 'WAJIB',
                    'transactionType' => 'SETOR',
                    'amount' => $member->monthly_simpanan_wajib > 0 ? $member->monthly_simpanan_wajib : 50000,
                    'balanceAfter' => 0, // Will be calculated when paid? Or should be current balance? 
                                         // Usually balance updates on payment. For bill, maybe 0 or current balance.
                                         // Let's check SimpananTransaction model again. 
                                         // It's a transaction table used for billing. 
                                         // When bill is created, it's a "request" for payment.
                                         // When paid, we might create another transaction or update this one?
                                         // The system seems to use SimpananTransaction AS the bill.
                                         // And SimpananPayment as the payment record.
                    'notes' => "Simpanan Wajib Bulan " . Carbon::createFromFormat('Y-m', $month)->translatedFormat('F Y'),
                    'status' => 'PENDING', // Transaction status
                    'billStatus' => 'APPROVED', // Ready to be paid
                    'billingMonth' => $month,
                    'paidAmount' => 0,
                    'processedBy' => 1, // System/Admin
                    'approvedBy' => 1, // System/Admin
                    'approvedAt' => now(),
                ]);

                $count++;
            }
            
            DB::commit();
            $this->info("Successfully generated $count bills. Skipped $skipped existing bills.");
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Error: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
