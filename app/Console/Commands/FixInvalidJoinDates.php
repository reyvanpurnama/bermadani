<?php

namespace App\Console\Commands;

use App\Models\Member;
use Carbon\Carbon;
use Illuminate\Console\Command;

class FixInvalidJoinDates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'member:fix-join-dates {--year=2021 : The year to set the join date to}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix members with join date in Dec 2025 by setting them to Jan 1st of specified year';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $year = $this->option('year');
        $targetDate = Carbon::createFromDate($year, 1, 1);
        
        // Find members joined in Dec 2025 (likely import errors)
        $members = Member::whereYear('joinDate', 2025)
                        ->whereMonth('joinDate', 12)
                        ->get();

        $count = $members->count();

        if ($count === 0) {
            $this->info('No members found with join date in Dec 2025.');
            return;
        }

        if ($this->confirm("Found {$count} members with join date in Dec 2025. Update them to {$targetDate->format('d M Y')}?", true)) {
            $bar = $this->output->createProgressBar($count);

            foreach ($members as $member) {
                $member->update([
                    'joinDate' => $targetDate
                ]);
                $bar->advance();
            }

            $bar->finish();
            $this->newLine();
            $this->info("Successfully updated {$count} members.");
            
            // Suggest running recalculation
            $this->info('Now run: php artisan member:recalculate-simpanan-wajib');
        }
    }
}
