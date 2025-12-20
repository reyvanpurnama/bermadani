<?php

namespace App\Console\Commands;

use App\Models\Member;
use App\Models\SimpananTransaction;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncMembersFromCsv extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'member:sync-from-csv';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync member data (join date, simpanan) from docs/data/anggota.csv';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $csvPath = base_path('docs/data/anggota.csv');

        if (!file_exists($csvPath)) {
            $this->error("File not found: $csvPath");
            return;
        }

        $this->info('Reading CSV file...');
        
        $file = fopen($csvPath, 'r');
        $header = fgetcsv($file); // Skip header
        
        // Map Indonesian month names to English for parsing
        $monthMap = [
            'Jan' => 'Jan', 'Feb' => 'Feb', 'Mar' => 'Mar', 'Apr' => 'Apr', 'Mei' => 'May', 'Jun' => 'Jun',
            'Jul' => 'Jul', 'Agt' => 'Aug', 'Agu' => 'Aug', 'Sep' => 'Sep', 'Okt' => 'Oct', 'Nov' => 'Nov', 'Des' => 'Dec'
        ];

        $targetDate = Carbon::create(2025, 12, 1)->startOfMonth();
        $monthlyAmount = 50000;
        
        // Get an admin ID for 'processedBy' field (required)
        $adminId = \App\Models\User::where('role', 'ADMIN')->first()->id ?? 1;
        
        $updatedCount = 0;
        $notFoundCount = 0;

        DB::beginTransaction();
        SimpananTransaction::unguard();

        try {
            while (($row = fgetcsv($file)) !== false) {
                // CSV Structure: NO, NAMA ANGGOTA, PENDAFTARAN ANGGOTA, SIMPANAN POKOK, TOTAL SIMPANAN WAJIB
                if (empty($row[1])) continue; // Skip empty rows

                $name = trim($row[1]);
                $dateStr = trim($row[2]);

                // Translate month name
                foreach ($monthMap as $indo => $eng) {
                    $dateStr = str_replace($indo, $eng, $dateStr);
                }

                try {
                    $joinDate = Carbon::createFromFormat('j M Y', $dateStr)->startOfDay();
                } catch (\Exception $e) {
                    $this->warn("Invalid date format for $name: {$row[2]}");
                    continue;
                }

                // Find member
                $member = Member::where('name', 'LIKE', $name)->first();

                if (!$member) {
                    $this->warn("Member not found: $name");
                    $notFoundCount++;
                    continue;
                }

                // Calculate Simpanan Wajib
                // Logic: From join date until Dec 2025
                $calculationDate = $joinDate->copy()->startOfMonth();
                
                if ($calculationDate->gt($targetDate)) {
                    $months = 0;
                } else {
                    // Inclusive count
                    $months = $calculationDate->diffInMonths($targetDate) + 1;
                }
                
                $simpananWajib = $months * $monthlyAmount;

                // Update Member
                $member->update([
                    'joinDate' => $joinDate,
                    'simpananPokok' => 200000,
                    'simpananSukarela' => 0,
                    'simpananWajib' => $simpananWajib
                ]);

                // --- Generate History ---
                
                // 1. Clear existing history for clean slate (Wajib, Pokok, & Sukarela)
                SimpananTransaction::where('memberId', $member->id)
                    ->whereIn('type', ['POKOK', 'WAJIB', 'SUKARELA'])
                    ->delete();

                // 2. Create Simpanan Pokok Transaction
                SimpananTransaction::create([
                    'memberId' => $member->id,
                    'type' => 'POKOK',
                    'transactionType' => 'SETOR',
                    'amount' => 200000,
                    'balanceAfter' => 200000,
                    'notes' => 'Simpanan Pokok Awal',
                    'status' => 'APPROVED',
                    'processedBy' => $adminId,
                    'created_at' => $joinDate,
                    'updated_at' => $joinDate,
                ]);

                // 3. Create Simpanan Wajib Transactions
                $currentDate = $joinDate->copy()->startOfMonth();
                $wajibBalance = 0;

                while ($currentDate->lte($targetDate)) {
                    $wajibBalance += $monthlyAmount;

                    SimpananTransaction::create([
                        'memberId' => $member->id,
                        'type' => 'WAJIB',
                        'transactionType' => 'SETOR',
                        'amount' => $monthlyAmount,
                        'balanceAfter' => $wajibBalance,
                        'notes' => 'Simpanan Wajib ' . $currentDate->format('F Y'),
                        'status' => 'APPROVED',
                        'processedBy' => $adminId,
                        'created_at' => $currentDate->copy()->setDay(1)->setHour(9), // Set to 1st of month
                        'updated_at' => $currentDate->copy()->setDay(1)->setHour(9),
                    ]);

                    $currentDate->addMonth();
                }

                $updatedCount++;
                $this->output->write('.');
            }

            DB::commit();
            fclose($file);

            $this->newLine();
            $this->info("Sync completed.");
            $this->info("Updated: $updatedCount members");
            $this->info("Not Found: $notFoundCount members");

        } catch (\Exception $e) {
            DB::rollBack();
            fclose($file);
            $this->error("Error: " . $e->getMessage());
        }
    }
}
