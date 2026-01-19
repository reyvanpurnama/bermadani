<?php

namespace App\Console\Commands;

use App\Models\ConsignmentBatch;
use Illuminate\Console\Command;

class UpdateBatchStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'batch:update-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update status batch konsinyasi yang sudah habis stoknya menjadi PENDING_SETTLEMENT';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking active batches...');
        
        // Get all ACTIVE batches
        $activeBatches = ConsignmentBatch::where('status', 'ACTIVE')
            ->with('items')
            ->get();
        
        $updatedCount = 0;
        
        foreach ($activeBatches as $batch) {
            // Check if all items have 0 remaining quantity
            $hasRemaining = $batch->items->sum('remainingQty') > 0;
            
            if (!$hasRemaining) {
                // Update status to PENDING_SETTLEMENT
                $batch->update(['status' => 'PENDING_SETTLEMENT']);
                $this->line("✓ Batch #{$batch->batchCode} updated to PENDING_SETTLEMENT");
                $updatedCount++;
            }
        }
        
        if ($updatedCount > 0) {
            $this->info("\n✓ Successfully updated {$updatedCount} batch(es) to PENDING_SETTLEMENT");
        } else {
            $this->info("\n✓ No batches need status update");
        }
        
        return Command::SUCCESS;
    }
}
