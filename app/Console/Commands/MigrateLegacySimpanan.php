<?php

namespace App\Console\Commands;

use App\Models\SimpananTransaction;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateLegacySimpanan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'simpanan:migrate-legacy 
                            {--dry-run : Preview tanpa menyimpan ke database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrasi data simpanan lama: set billStatus=PAID untuk transaksi SETOR yang sudah ada';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');

        $this->info('');
        $this->info('╔══════════════════════════════════════════════════════════╗');
        $this->info('║     MIGRASI DATA SIMPANAN LAMA                           ║');
        $this->info('╠══════════════════════════════════════════════════════════╣');
        $this->info('║  Fungsi: Tandai transaksi lama sebagai LUNAS (PAID)      ║');
        if ($isDryRun) {
            $this->warn('║  Mode: DRY RUN (tidak menyimpan ke database)             ║');
        }
        $this->info('╚══════════════════════════════════════════════════════════╝');
        $this->info('');

        // Count transactions to update
        $toUpdate = SimpananTransaction::where('transactionType', 'SETOR')
            ->where(function($q) {
                $q->where('billStatus', 'DRAFT')
                  ->orWhereNull('billStatus');
            })
            ->count();

        if ($toUpdate === 0) {
            $this->info('✅ Tidak ada data yang perlu dimigrasi.');
            return 0;
        }

        $this->info("Ditemukan {$toUpdate} transaksi SETOR dengan status DRAFT/NULL");
        $this->newLine();

        // Show sample data
        $samples = SimpananTransaction::where('transactionType', 'SETOR')
            ->where(function($q) {
                $q->where('billStatus', 'DRAFT')
                  ->orWhereNull('billStatus');
            })
            ->with('member:id,name,nomorAnggota')
            ->take(5)
            ->get(['id', 'memberId', 'type', 'amount', 'transactionDate', 'billStatus']);

        $this->info('Contoh data yang akan diupdate:');
        $this->table(
            ['ID', 'Anggota', 'Jenis', 'Jumlah', 'Tanggal', 'Status Lama'],
            $samples->map(fn($s) => [
                $s->id,
                $s->member?->name ?? 'N/A',
                $s->type,
                'Rp ' . number_format($s->amount, 0, ',', '.'),
                $s->transactionDate?->format('d/m/Y') ?? '-',
                $s->billStatus ?? 'NULL'
            ])
        );

        if (!$isDryRun && !$this->confirm('Lanjutkan update data?', true)) {
            $this->warn('Dibatalkan.');
            return 0;
        }

        $bar = $this->output->createProgressBar($toUpdate);
        $bar->start();

        DB::beginTransaction();

        try {
            // Update in chunks to avoid memory issues
            SimpananTransaction::where('transactionType', 'SETOR')
                ->where(function($q) {
                    $q->where('billStatus', 'DRAFT')
                      ->orWhereNull('billStatus');
                })
                ->chunkById(100, function($transactions) use ($bar, $isDryRun) {
                    foreach ($transactions as $transaction) {
                        if (!$isDryRun) {
                            $transaction->update([
                                'billStatus' => 'PAID',
                                'paidAmount' => $transaction->amount, // Sudah lunas
                                'billingMonth' => $transaction->billingMonth 
                                    ?? ($transaction->transactionDate 
                                        ? Carbon::parse($transaction->transactionDate)->format('Y-m')
                                        : Carbon::now()->format('Y-m')),
                            ]);
                        }
                        $bar->advance();
                    }
                });

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
        $this->info('║  Total Diupdate: ' . str_pad($toUpdate, 6) . '                              ║');
        $this->info('║  Status Baru: PAID (Lunas)                               ║');
        $this->info('╚══════════════════════════════════════════════════════════╝');

        if ($isDryRun) {
            $this->newLine();
            $this->warn('⚠️  Ini adalah DRY RUN. Jalankan tanpa --dry-run untuk menyimpan ke database.');
        } else {
            $this->newLine();
            $this->info('✅ Selesai! Data simpanan lama telah ditandai sebagai LUNAS.');
            $this->info('');
            $this->info('Langkah selanjutnya:');
            $this->info('  1. Jalankan: php artisan simpanan:generate-monthly');
            $this->info('     untuk generate kewajiban simpanan bulan ini.');
        }

        return 0;
    }
}
