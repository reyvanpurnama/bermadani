<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\BankTransaction;
use App\Models\AuditBankImport;
use App\Models\AuditBankCategoryRule;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ImportBankStatements extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bank:import-statements';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import all Rekening Koran CSV files from docs/data/rekening_koran_bermadani';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $baseDir = base_path('docs/data/rekening_koran_bermadani');
        if (!is_dir($baseDir)) {
            $this->error("Directory not found: $baseDir");
            return 1;
        }

        $files = [];
        // Scan 2025 and 2026 directories
        foreach (['2025', '2026'] as $yearDir) {
            $dir = $baseDir . DIRECTORY_SEPARATOR . $yearDir;
            if (is_dir($dir)) {
                $dirFiles = glob($dir . DIRECTORY_SEPARATOR . '*.csv');
                if (is_array($dirFiles)) {
                    $files = array_merge($files, $dirFiles);
                }
            }
        }

        if (empty($files)) {
            $this->warn("No CSV files found.");
            return 0;
        }

        $this->info("Found " . count($files) . " CSV files to import.");

        foreach ($files as $filePath) {
            $filename = basename($filePath);
            $period = $this->parsePeriodFromFilename($filename);
            
            if (!$period) {
                $this->error("Could not parse period from filename: $filename");
                continue;
            }

            $this->info("Importing file: $filename (Period: $period)...");

            // Clean up existing data for this period to prevent duplication
            AuditBankImport::where('period', $period)->delete();
            BankTransaction::where('period', $period)->delete();

            $data = array_map('str_getcsv', file($filePath));

            // Remove header row
            if (isset($data[0]) && (str_contains(strtolower($data[0][0]), 'tgl'))) {
                array_shift($data);
            }

            $batchImports = [];
            $batchTransactions = [];
            $rowCount = 0;

            foreach ($data as $row) {
                if (count($row) < 6) continue;

                $tgl = trim($row[0]);
                $waktu = trim($row[1]);
                $keterangan = trim($row[2]);
                $debet = floatval(str_replace([',', ' '], '', $row[3]));
                $kredit = floatval(str_replace([',', ' '], '', $row[4]));
                $saldo = floatval(str_replace([',', ' '], '', $row[5]));

                if (empty($keterangan)) continue;

                $transactionDate = $this->parseTransactionDate($tgl, $period);
                $transactionTime = $this->parseTransactionTime($waktu);

                // Auto-categorize
                $match = AuditBankCategoryRule::matchKeterangan($keterangan);
                
                $detectedType = null;
                $detectedCategory = null;

                if ($match) {
                    $detectedType = $match['type'];
                    $detectedCategory = $match['category'];
                } else {
                    if ($kredit > 0) {
                        $detectedType = 'INCOME';
                        $detectedCategory = 'Lainnya';
                    } elseif ($debet > 0) {
                        $detectedType = 'EXPENSE';
                        $detectedCategory = 'Lainnya';
                    }
                }

                $batchImports[] = [
                    'filename' => $filename,
                    'period' => $period,
                    'transaction_date' => $transactionDate,
                    'transaction_time' => $transactionTime,
                    'keterangan' => $keterangan,
                    'debet' => $debet,
                    'kredit' => $kredit,
                    'saldo' => $saldo,
                    'detected_type' => $detectedType,
                    'detected_category' => $detectedCategory,
                    'is_reviewed' => true,
                    'is_synced' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $batchTransactions[] = [
                    'transaction_date' => $transactionDate,
                    'transaction_time' => $transactionTime,
                    'description' => $keterangan,
                    'debit' => $debet,
                    'credit' => $kredit,
                    'balance' => $saldo,
                    'type' => $detectedType,
                    'category' => $detectedCategory,
                    'period' => $period,
                    'source_file' => $filename,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $rowCount++;

                if (count($batchImports) >= 500) {
                    DB::table('audit_bank_imports')->insert($batchImports);
                    DB::table('bank_transactions')->insert($batchTransactions);
                    $batchImports = [];
                    $batchTransactions = [];
                }
            }

            if (!empty($batchImports)) {
                DB::table('audit_bank_imports')->insert($batchImports);
                DB::table('bank_transactions')->insert($batchTransactions);
            }

            $this->info("Successfully imported $rowCount rows from $filename.");
        }

        $this->info("All bank statements have been successfully imported!");
        return 0;
    }

    private function parsePeriodFromFilename($filename)
    {
        $lower = strtolower($filename);
        preg_match('/20\d{2}/', $lower, $matches);
        $year = $matches[0] ?? null;
        if (!$year) return null;

        $months = [
            'januari' => '01', 'februari' => '02', 'maret' => '03', 'april' => '04',
            'mei' => '05', 'juni' => '06', 'juli' => '07', 'agustus' => '08',
            'september' => '09', 'oktober' => '10', 'november' => '11', 'desember' => '12',
        ];

        foreach ($months as $name => $code) {
            if (str_contains($lower, $name)) {
                return "$year-$code";
            }
        }
        return null;
    }

    private function parseTransactionDate($tgl, $period)
    {
        if (preg_match('/(\d{1,2})\/(\d{1,2})/', $tgl, $matches)) {
            $day = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
            $month = str_pad($matches[2], 2, '0', STR_PAD_LEFT);
            $year = substr($period, 0, 4);
            return "$year-$month-$day";
        }
        return null;
    }

    private function parseTransactionTime($waktu)
    {
        if (preg_match('/(\d{1,2}):(\d{2})/', $waktu, $matches)) {
            $hour = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
            $minute = $matches[2];
            return "$hour:$minute:00";
        }
        return '00:00:00';
    }
}
