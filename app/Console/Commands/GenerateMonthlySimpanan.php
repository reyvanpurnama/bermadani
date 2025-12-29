<?php

namespace App\Console\Commands;

use App\Models\Member;
use App\Models\SimpananTransaction;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GenerateMonthlySimpanan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'simpanan:generate-monthly 
                            {--month= : Bulan dalam format YYYY-MM (default: bulan ini)}
                            {--member= : ID anggota spesifik (opsional)}
                            {--dry-run : Preview tanpa menyimpan ke database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate kewajiban simpanan bulanan (WAJIB & POKOK) untuk anggota aktif';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $month = $this->option('month') 
            ? Carbon::parse($this->option('month') . '-01') 
            : Carbon::now()->startOfMonth();
        
        $billingMonth = $month->format('Y-m');
        $isDryRun = $this->option('dry-run');

        $this->info('');
        $this->info('╔══════════════════════════════════════════════════════════╗');
        $this->info('║     GENERATE KEWAJIBAN SIMPANAN BULANAN                  ║');
        $this->info('╠══════════════════════════════════════════════════════════╣');
        $this->info('║  Periode: ' . $month->translatedFormat('F Y') . str_repeat(' ', 45 - strlen($month->translatedFormat('F Y'))) . '║');
        if ($isDryRun) {
            $this->warn('║  Mode: DRY RUN (tidak menyimpan ke database)             ║');
        }
        $this->info('╚══════════════════════════════════════════════════════════╝');
        $this->info('');

        // Get active members
        $membersQuery = Member::where('status', 'AKTIF');
        
        if ($this->option('member')) {
            $membersQuery->where('id', $this->option('member'));
        }
        
        $members = $membersQuery->get();

        if ($members->isEmpty()) {
            $this->error('Tidak ada anggota aktif yang ditemukan.');
            return 1;
        }

        $this->info("Memproses {$members->count()} anggota aktif...");
        $this->newLine();

        $bar = $this->output->createProgressBar($members->count());
        $bar->start();

        $created = 0;
        $skipped = 0;
        $errors = 0;
        $details = [];

        DB::beginTransaction();

        try {
            foreach ($members as $member) {
                $memberResult = $this->generateForMember($member, $billingMonth, $isDryRun);
                
                $created += $memberResult['created'];
                $skipped += $memberResult['skipped'];
                
                if ($memberResult['created'] > 0) {
                    $details[] = [
                        'Anggota' => $member->name,
                        'No. Anggota' => $member->nomorAnggota,
                        'Simpanan Wajib' => 'Rp ' . number_format($member->besaranSimpananWajib ?? 200000, 0, ',', '.'),
                        'Simpanan Pokok' => 'Rp ' . number_format($member->besaranSimpananPokok ?? 50000, 0, ',', '.'),
                    ];
                }

                $bar->advance();
            }

            if (!$isDryRun) {
                DB::commit();
            } else {
                DB::rollBack();
            }

        } catch (\Exception $e) {
            DB::rollBack();
            $this->newLine(2);
            $this->error('Error: ' . $e->getMessage());
            return 1;
        }

        $bar->finish();
        $this->newLine(2);

        // Summary
        $this->info('╔══════════════════════════════════════════════════════════╗');
        $this->info('║                      RINGKASAN                           ║');
        $this->info('╠══════════════════════════════════════════════════════════╣');
        $this->info('║  Kewajiban Dibuat  : ' . str_pad($created, 5) . '                              ║');
        $this->info('║  Dilewati (duplikat): ' . str_pad($skipped, 5) . '                             ║');
        $this->info('╚══════════════════════════════════════════════════════════╝');
        $this->newLine();

        if (!empty($details) && $this->option('verbose')) {
            $this->table(['Anggota', 'No. Anggota', 'Simpanan Wajib', 'Simpanan Pokok'], $details);
        }

        if ($isDryRun) {
            $this->warn('⚠️  Ini adalah DRY RUN. Jalankan tanpa --dry-run untuk menyimpan ke database.');
        } else {
            $this->info('✅ Selesai! Kewajiban simpanan periode ' . $month->translatedFormat('F Y') . ' telah dibuat.');
        }

        return 0;
    }

    /**
     * Generate simpanan bills for a single member
     */
    private function generateForMember(Member $member, string $billingMonth, bool $isDryRun): array
    {
        $created = 0;
        $skipped = 0;

        // Get member's tier amounts (atau default jika tidak ada)
        $wajibAmount = $member->besaranSimpananWajib ?? 200000;
        $pokokAmount = $member->besaranSimpananPokok ?? 50000;

        $types = [
            'WAJIB' => $wajibAmount,
            'POKOK' => $pokokAmount,
        ];

        foreach ($types as $type => $amount) {
            // Check if already exists for this month
            $exists = SimpananTransaction::where('memberId', $member->id)
                ->where('billingMonth', $billingMonth)
                ->where('type', $type)
                ->where('transactionType', 'SETOR')
                ->exists();

            if ($exists) {
                $skipped++;
                continue;
            }

            if (!$isDryRun) {
                SimpananTransaction::create([
                    'memberId' => $member->id,
                    'type' => $type,
                    'transactionType' => 'SETOR',
                    'amount' => $amount,
                    'billingMonth' => $billingMonth,
                    'billStatus' => 'APPROVED', // Langsung approved, siap bayar
                    'paidAmount' => 0,
                    'description' => "Kewajiban Simpanan {$type} - " . Carbon::parse($billingMonth . '-01')->translatedFormat('F Y'),
                    'transactionDate' => Carbon::parse($billingMonth . '-01'),
                ]);
            }
            $created++;
        }

        return ['created' => $created, 'skipped' => $skipped];
    }
}
