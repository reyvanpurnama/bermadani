<?php

namespace App\Console\Commands;

use App\Services\DatabaseBackupService;
use Illuminate\Console\Command;

class DatabaseBackupCommand extends Command
{
    protected $signature = 'db:backup';

    protected $description = 'Generate a full MySQL SQL database backup (.sql) compatible with phpMyAdmin';

    public function handle(DatabaseBackupService $backupService): int
    {
        $this->info('⚙️ Generating MySQL Database Backup...');

        try {
            $filePath = $backupService->generateDump();
            $filename = basename($filePath);
            $size = number_format(filesize($filePath) / 1024, 2) . ' KB';

            $this->info('==================================================');
            $this->info('  ✅ BACKUP DATABASE BERHASIL DIBUAT! 🎉        ');
            $this->info('==================================================');
            $this->line("File Dump : {$filename}");
            $this->line("Lokasi    : {$filePath}");
            $this->line("Ukuran    : {$size}");
            $this->info('');

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('❌ Gagal membuat backup: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}
