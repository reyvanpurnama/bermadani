<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Member;
use Illuminate\Support\Facades\DB;

class RegenerateMemberNumbers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'members:regenerate-numbers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Regenerate member numbers to YYNNNNNN format (8 digits)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Regenerating member numbers to YYNNNNNN format...');

        // Group members by join year
        $members = Member::orderBy('joinDate', 'asc')->orderBy('id', 'asc')->get();
        
        $countByYear = [];
        $updated = 0;

        DB::beginTransaction();
        try {
            foreach ($members as $member) {
                // Get join year (2 digits)
                $joinYear = $member->joinDate ? $member->joinDate->format('y') : now()->format('y');
                
                // Initialize counter for this year
                if (!isset($countByYear[$joinYear])) {
                    $countByYear[$joinYear] = 0;
                }
                
                // Increment counter
                $countByYear[$joinYear]++;
                
                // Generate new number: YY + 6 digit sequence
                $newNumber = $joinYear . str_pad($countByYear[$joinYear], 6, '0', STR_PAD_LEFT);
                
                // Update member
                $member->update([
                    'nomorAnggota' => $newNumber,
                    'email' => $newNumber . '@bermadani.id',
                ]);
                
                $updated++;
            }

            DB::commit();
            
            $this->info("Successfully updated $updated member numbers.");
            $this->info("Format: YYNNNNNN (e.g., 24000001)");
            $this->newLine();
            
            // Show summary per year
            $this->info("Summary per year:");
            foreach ($countByYear as $year => $count) {
                $this->line("  20$year: $count members (20{$year}000001 - 20{$year}" . str_pad($count, 6, '0', STR_PAD_LEFT) . ")");
            }

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Error: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
