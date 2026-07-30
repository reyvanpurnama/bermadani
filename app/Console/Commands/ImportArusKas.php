<?php

namespace App\Console\Commands;

use App\Models\FinancialTransaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportArusKas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:arus-kas {file=docs/data/ARUS KAS 25.csv} {year=2025}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $signature_description = 'Import rekap arus kas bulanan dari CSV ke financial_transactions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filePath = $this->argument('file');
        $year = (int) $this->argument('year');

        if (!file_exists($filePath)) {
            $this->error("File tidak ditemukan: {$filePath}");
            return Command::FAILURE;
        }

        $this->info("Memulai import arus kas dari {$filePath} untuk tahun {$year}...");

        $adminUser = User::where('role', 'ADMIN')->first() ?? User::first();
        if (!$adminUser) {
            $this->error("Tidak ada user terdaftar di database untuk memproses transaksi.");
            return Command::FAILURE;
        }

        $handle = fopen($filePath, 'r');
        if (!$handle) {
            $this->error("Gagal membuka file: {$filePath}");
            return Command::FAILURE;
        }

        // Mapping index kolom ke bulan
        $monthsMap = [
            1 => ['name' => 'Mei', 'num' => 5],
            2 => ['name' => 'Juni', 'num' => 6],
            3 => ['name' => 'Juli', 'num' => 7],
            4 => ['name' => 'Agustus', 'num' => 8],
            5 => ['name' => 'September', 'num' => 9],
            6 => ['name' => 'Oktober', 'num' => 10],
            7 => ['name' => 'November', 'num' => 11],
            8 => ['name' => 'Desember', 'num' => 12],
        ];

        $currentSection = null; // 'INCOME' atau 'EXPENSE'
        $rowCount = 0;
        $insertedCount = 0;

        DB::beginTransaction();

        try {
            while (($row = fgetcsv($handle, 2048, ',')) !== false) {
                $rowCount++;

                // Skip header atau baris kosong
                if (empty($row) || !isset($row[0])) {
                    continue;
                }

                $firstCol = trim($row[0]);

                if (empty($firstCol)) {
                    // Cari indikator section di kolom-kolom berikutnya jika kolom pertama kosong
                    $rowStr = implode(' ', $row);
                    if (str_contains(strtoupper($rowStr), 'KAS MASUK')) {
                        $currentSection = 'INCOME';
                        $this->info("Menemukan kategori: KAS MASUK (Pemasukan)");
                        continue;
                    } elseif (str_contains(strtoupper($rowStr), 'KAS KELUAR')) {
                        $currentSection = 'EXPENSE';
                        $this->info("Menemukan kategori: KAS KELUAR (Pengeluaran)");
                        continue;
                    }
                    continue;
                }

                // Cek penanda section utama di kolom pertama
                if (str_contains(strtoupper($firstCol), 'LAPORAN ARUS KAS')) {
                    continue;
                }
                if (str_contains(strtoupper($firstCol), 'KAS MASUK')) {
                    $currentSection = 'INCOME';
                    $this->info("Menemukan kategori: KAS MASUK (Pemasukan)");
                    continue;
                }
                if (str_contains(strtoupper($firstCol), 'KAS KELUAR')) {
                    $currentSection = 'EXPENSE';
                    $this->info("Menemukan kategori: KAS KELUAR (Pengeluaran)");
                    continue;
                }

                // Skip summary/total lines
                if (in_array(strtoupper($firstCol), [
                    'TOTAL',
                    'SALDO KAS AWAL',
                    'TOTAL KAS MASUK',
                    'TOTAL KAS KELUAR',
                    'SALDO KAS AKHIR'
                ])) {
                    continue;
                }

                if (!$currentSection) {
                    continue;
                }

                $categoryName = $firstCol;

                // Memproses 8 bulan (index 1 sampai 8)
                for ($i = 1; $i <= 8; $i++) {
                    if (!isset($row[$i]) || trim($row[$i]) === '') {
                        continue;
                    }

                    // Bersihkan nominal dari format Rp, titik ribuan, dll
                    $rawAmount = trim($row[$i]);
                    // Hapus "Rp", "." (pemisah ribuan), ganti "," (pemisah desimal) dengan "."
                    $cleanAmount = str_replace(['Rp', '.', ' '], '', $rawAmount);
                    $cleanAmount = str_replace(',', '.', $cleanAmount);
                    $amount = (float) $cleanAmount;

                    if ($amount <= 0) {
                        continue;
                    }

                    $monthInfo = $monthsMap[$i];
                    $monthNum = $monthInfo['num'];
                    $monthName = $monthInfo['name'];

                    // Buat tanggal akhir bulan
                    $transactionDate = Carbon::create($year, $monthNum, 1)->endOfMonth()->format('Y-m-d');

                    FinancialTransaction::create([
                        'type' => $currentSection,
                        'category' => $categoryName,
                        'amount' => $amount,
                        'transactionDate' => $transactionDate,
                        'description' => "Import Arus Kas {$year} - {$categoryName} ({$monthName})",
                        'userId' => $adminUser->id,
                    ]);

                    $insertedCount++;
                }
            }

            DB::commit();
            fclose($handle);

            $this->info("Sukses! Berhasil mengimpor {$insertedCount} entri transaksi arus kas ke database.");
            return Command::SUCCESS;

        } catch (\Exception $e) {
            DB::rollBack();
            fclose($handle);
            $this->error("Terjadi error saat import: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
