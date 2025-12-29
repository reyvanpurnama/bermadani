<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SimpananTransaction;
use Illuminate\Support\Facades\DB;

class InitLegacySimpanan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'simpanan:init-legacy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialize legacy simpanan transactions as PAID/APPROVED';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Initializing legacy transactions...');

        // Update all existing transactions that have billStatus = DRAFT (default)
        // We assume all existing data before this feature is "PAID" or "SETTLED"
        // So they don't show up as unpaid bills.
        
        // Option 1: Mark them as PAID bills
        // Option 2: Just ignore them by keeping them DRAFT? 
        // The query for unpaid bills is: billStatus='APPROVED' AND paidAmount < amount.
        // So if they are DRAFT, they won't show up.
        // BUT, the user said "Semua kewajiban simpanan anggota ini sudah lunas".
        // If they are DRAFT, they are not "Lunas" (Paid), they are just not "Approved Bills".
        
        // If we want to show history correctly, maybe we should mark them as APPROVED and PAID.
        
        $count = SimpananTransaction::where('billStatus', 'DRAFT')
            ->update([
                'billStatus' => 'APPROVED',
                'paidAmount' => DB::raw('amount'), // Assume fully paid
                'billingMonth' => DB::raw("DATE_FORMAT(created_at, '%Y-%m')") // Set billing month based on creation date
            ]);

        $this->info("Updated $count transactions to APPROVED and PAID status.");
        
        return 0;
    }
}
