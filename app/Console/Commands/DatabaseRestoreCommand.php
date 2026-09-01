<?php

namespace App\Console\Commands;

use App\Services\DatabaseBackupService;
use Illuminate\Console\Command;

class DatabaseRestoreCommand extends Command
{
    protected $signature = 'db:restore {file : Path to the .sql dump file}';

    protected $description = 'Restore a MySQL SQL dump file (.sql) into the active database';

    public function handle(DatabaseBackupService $backupService): int
    {
        $filePath = $this->argument('file');

        if (!file_exists($filePath)) {
            // Try storage/app/backups/ if relative filename given
            $altPath = storage_path("app/backups/" . basename($filePath));
            if (file_exists($altPath)) {
                $filePath = $altPath;
            } else {
                $this->error("❌ File .sql tidak ditemukan di: {$filePath}");
                return self::FAILURE;
            }
        }

        $filename = basename($filePath);

        if (!$this->confirm("⚠️ PERHATIAN! Merestore {$filename} akan MENIMPA seluruh data di database. Lanjutkan?")) {
            $this->line('Restore dibatalkan.');
            return self::SUCCESS;
        }

        $this->info("⚙️ Restoring database from {$filename}...");

        try {
            $backupService->importDump($filePath);

            $this->info('==================================================');
            $this->info('  🎉 RESTORE DATABASE BERHASIL DILAKUKAN! 🎉    ');
            $this->info('==================================================');
            $this->line("File SQL: {$filename}");
            $this->info('');

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('❌ Gagal merestore database: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}
