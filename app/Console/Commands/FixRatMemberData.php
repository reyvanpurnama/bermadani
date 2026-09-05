<?php

namespace App\Console\Commands;

use App\Models\Member;
use App\Models\MemberShuDistribution;
use App\Models\RatSession;
use App\Services\ShuCalculationService;
use App\Domains\Koperasi\Models\SimpananTransaction;
use Illuminate\Console\Command;

class FixRatMemberData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rat:fix-member-data {--session= : Optional RAT session ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sinkronisasi status anggota aktif, saldo simpanan, dan kalkulasi alokasi SHU RAT di database produksi.';

    /**
     * Execute the console command.
     */
    public function handle(ShuCalculationService $shuService): int
    {
        $this->info('🚀 Memulai Audit & Sinkronisasi Data Anggota & RAT...');

        // 1. Fix member status: SUSPENDED without notes -> ACTIVE
        $this->info('1. Memeriksa status anggota (SUSPENDED -> ACTIVE)...');
        $fixedStatusCount = Member::where('status', 'SUSPENDED')
            ->whereNull('status_note')
            ->update(['status' => 'ACTIVE']);
        $this->info("   ✅ Berhasil mengupdate {$fixedStatusCount} anggota dari SUSPENDED ke ACTIVE.");

        // 2. Sync members simpananPokok & simpananWajib balance columns with simpanan_transactions
        $this->info('2. Memeriksa & menyinkronkan saldo simpanan anggota dengan histori transaksi...');
        $syncedBalanceCount = 0;
        $members = Member::all();

        foreach ($members as $m) {
            $sumPokok = (float) SimpananTransaction::where('memberId', $m->id)
                ->where('type', 'POKOK')
                ->where('status', 'APPROVED')
                ->where('transactionType', 'SETOR')
                ->sum('amount') 
                - (float) SimpananTransaction::where('memberId', $m->id)
                ->where('type', 'POKOK')
                ->where('status', 'APPROVED')
                ->where('transactionType', 'TARIK')
                ->sum('amount');

            $sumWajib = (float) SimpananTransaction::where('memberId', $m->id)
                ->where('type', 'WAJIB')
                ->where('status', 'APPROVED')
                ->where('transactionType', 'SETOR')
                ->sum('amount')
                - (float) SimpananTransaction::where('memberId', $m->id)
                ->where('type', 'WAJIB')
                ->where('status', 'APPROVED')
                ->where('transactionType', 'TARIK')
                ->sum('amount');

            if (abs((float)$m->simpananPokok - $sumPokok) > 0.01 || abs((float)$m->simpananWajib - $sumWajib) > 0.01) {
                $m->update([
                    'simpananPokok' => max(0, $sumPokok),
                    'simpananWajib' => max(0, $sumWajib),
                ]);
                $syncedBalanceCount++;
            }
        }
        $this->info("   ✅ Berhasil menyinkronkan saldo {$syncedBalanceCount} anggota.");

        // 3. Recalculate RAT Distributions
        $this->info('3. Menghitung ulang kalkulasi & alokasi SHU RAT...');
        $sessionId = $this->option('session');

        $sessions = $sessionId 
            ? RatSession::where('id', $sessionId)->get()
            : RatSession::all();

        foreach ($sessions as $session) {
            $count = $shuService->calculateAndSaveDistributions($session);
            $this->info("   ✅ RAT Tahun Buku {$session->year} (Session ID {$session->id}): {$count} distribusi anggota berhasil dihitung ulang.");
        }

        $this->info('🎉 PROSES AUDIT & SINKRONISASI SELESAI DENGAN SUKSES!');
        return Command::SUCCESS;
    }
}
