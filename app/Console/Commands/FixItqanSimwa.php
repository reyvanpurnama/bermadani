<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FixItqanSimwa extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:itqan-simwa';
    protected $description = 'Fix Simwa Amount for BMT Itqan Loans from CSV';

    public function handle()
    {
        $csvPath = base_path('docs/data/angsuran-itqan.csv');

        if (!file_exists($csvPath)) {
            $this->error("File not found: $csvPath");
            return;
        }

        $this->info("Reading CSV: $csvPath");
        $rows = array_map('str_getcsv', file($csvPath));
        if (count($rows) === 0)
            return;

        $header = $rows[0];
        unset($rows[0]);

        $count = 0;
        foreach ($rows as $row) {
            if (count($row) !== count($header))
                continue;

            $data = array_combine($header, $row);
            $nama = $data['NAMA_DEBITUR'] ?? '';
            $amount = (float) ($data['PLAFOND_RP'] ?? 0);
            $simwa = (float) ($data['SIMPANAN_WAJIB'] ?? 30000);
            $total = (float) ($data['TOTAL'] ?? 0);

            // Find Member matches
            $member = \App\Models\Member::where('name', 'like', "%$nama%")->first();

            if (!$member) {
                $this->warn("Member not found: $nama");
                continue;
            }

            // Find Loan matches
            $loan = \App\Models\Loan::where('member_id', $member->id)
                ->where('loanSource', 'BMT_ITQAN')
                ->where('amount', $amount)
                ->first();

            if ($loan) {
                $loan->update([
                    'simwa_amount' => $simwa,
                    'monthlyPayment' => $total,
                ]);
                $this->info("Updated {$member->name}: Simwa {$simwa}, Total {$total}");
                $count++;
            } else {
                $this->warn("Loan not found for {$member->name} Amount {$amount}");
            }
        }

        $this->info("Done! Updated $count loans.");
    }
}
