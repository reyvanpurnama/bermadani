<?php

namespace App\Console\Commands;

use App\Models\Member;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RecalculateSimpananWajib extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'member:recalculate-simpanan-wajib';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculate Simpanan Wajib for all members based on join date until Dec 2025';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting recalculation of Simpanan Wajib...');

        $targetDate = Carbon::create(2025, 12, 1)->startOfMonth();
        $monthlyAmount = 50000;

        $members = Member::all();
        $bar = $this->output->createProgressBar(count($members));

        DB::beginTransaction();

        try {
            foreach ($members as $member) {
                if (!$member->joinDate) {
                    $this->warn("\nMember {$member->name} (ID: {$member->id}) has no join date. Skipping.");
                    continue;
                }

                $joinDate = $member->joinDate->copy()->startOfMonth();
                
                // Calculate months
                if ($joinDate->gt($targetDate)) {
                    $months = 0;
                } else {
                    // diffInMonths returns absolute difference as integer
                    // e.g. Jan to Dec is 11 months difference, but we want inclusive count (12 months)
                    $months = $joinDate->diffInMonths($targetDate) + 1;
                }

                $newSimpananWajib = $months * $monthlyAmount;

                $member->update([
                    'simpananWajib' => $newSimpananWajib
                ]);

                // Update total simpanan logic if it's not a stored column (it seems calculated in blade, but let's check if there's a total column)
                // Member model has 'simpananPokok', 'simpananWajib', 'simpananSukarela'. 
                // 'totalSimpanan' is likely an accessor.

                $bar->advance();
            }

            DB::commit();
            $bar->finish();
            $this->newLine();
            $this->info('Successfully recalculated Simpanan Wajib for all members.');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('An error occurred: ' . $e->getMessage());
        }
    }
}
