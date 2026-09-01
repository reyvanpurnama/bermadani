<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DatabaseBackupService
{
    /**
     * Generate a full MySQL SQL Dump file (phpMyAdmin format) in storage/app/backups/
     * Pure PHP implementation - works on any host (cPanel/VPS) without requiring CLI mysqldump binary.
     */
    public function generateDump(): string
    {
        $backupDir = storage_path('app/backups');
        if (!file_exists($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $coopShort = preg_replace('/[^a-zA-Z0-9_-]/', '', coop_config('short_name', 'koperasi'));
        $filename = "backup_{$coopShort}_" . date('Y-m-d_H-i-s') . ".sql";
        $filePath = "{$backupDir}/{$filename}";

        $handle = fopen($filePath, 'w+');

        // Write SQL Header
        $dbName = config('database.connections.mysql.database');
        $coopName = coop_config('name');

        fwrite($handle, "-- ====================================================\n");
        fwrite($handle, "-- DATABASE BACKUP: {$coopName}\n");
        fwrite($handle, "-- Database: {$dbName}\n");
        fwrite($handle, "-- Generated: " . date('Y-m-d H:i:s') . "\n");
        fwrite($handle, "-- Format: MySQL Dump (phpMyAdmin Compatible)\n");
        fwrite($handle, "-- ====================================================\n\n");
        fwrite($handle, "SET FOREIGN_KEY_CHECKS=0;\n");
        fwrite($handle, "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n");
        fwrite($handle, "SET time_zone = \"+00:00\";\n\n");
        fwrite($handle, "START TRANSACTION;\n\n");

        // Get all tables
        $tables = DB::select('SHOW TABLES');
        $tableKey = "Tables_in_{$dbName}";

        foreach ($tables as $tableObj) {
            $table = $tableObj->$tableKey ?? current((array)$tableObj);

            fwrite($handle, "-- --------------------------------------------------------\n");
            fwrite($handle, "-- Table structure for table `{$table}`\n");
            fwrite($handle, "-- --------------------------------------------------------\n\n");
            fwrite($handle, "DROP TABLE IF EXISTS `{$table}`;\n");

            // Get Create Table statement
            $createTableObj = DB::select("SHOW CREATE TABLE `{$table}`");
            $createSql = $createTableObj[0]->{'Create Table'} ?? '';
            fwrite($handle, "{$createSql};\n\n");

            // Get Table Data (Insert statements in chunks)
            $count = DB::table($table)->count();
            if ($count > 0) {
                fwrite($handle, "-- Dumping data for table `{$table}` ({$count} rows)\n\n");

                DB::table($table)->orderByRaw('1')->chunk(500, function ($rows) use ($handle, $table) {
                    $insertHeader = "";
                    $valuesSql = [];

                    foreach ($rows as $row) {
                        $arrayRow = (array)$row;

                        if (empty($insertHeader)) {
                            $columns = array_keys($arrayRow);
                            $escapedColumns = array_map(fn($col) => "`{$col}`", $columns);
                            $insertHeader = "INSERT INTO `{$table}` (" . implode(', ', $escapedColumns) . ") VALUES\n";
                        }

                        $escapedValues = array_map(function ($value) {
                            if (is_null($value)) {
                                return 'NULL';
                            }
                            if (is_bool($value)) {
                                return $value ? '1' : '0';
                            }
                            if (is_numeric($value)) {
                                return $value;
                            }
                            // Escape special characters for SQL
                            $escaped = addcslashes($value, "\000\n\r\\'\032");
                            return "'{$escaped}'";
                        }, array_values($arrayRow));

                        $valuesSql[] = "(" . implode(', ', $escapedValues) . ")";
                    }

                    if (!empty($valuesSql)) {
                        fwrite($handle, $insertHeader . implode(",\n", $valuesSql) . ";\n\n");
                    }
                });
            }
        }

        fwrite($handle, "SET FOREIGN_KEY_CHECKS=1;\n");
        fwrite($handle, "COMMIT;\n");

        fclose($handle);

        return $filePath;
    }

    /**
     * Get list of all generated backups in storage/app/backups/
     */
    public function getBackupList(): array
    {
        $backupDir = storage_path('app/backups');
        if (!file_exists($backupDir)) {
            return [];
        }

        $files = glob("{$backupDir}/*.sql");
        $backups = [];

        foreach ($files as $file) {
            $size = filesize($file);
            $backups[] = [
                'filename' => basename($file),
                'filepath' => $file,
                'size_formatted' => $this->formatBytes($size),
                'size_bytes' => $size,
                'created_at' => date('Y-m-d H:i:s', filemtime($file)),
                'timestamp' => filemtime($file),
            ];
        }

        // Sort descending by timestamp (newest first)
        usort($backups, fn($a, $b) => $b['timestamp'] <=> $a['timestamp']);

        return $backups;
    }

    /**
     * Delete a backup file by filename.
     */
    public function deleteBackup(string $filename): bool
    {
        // Sanitize filename to prevent directory traversal
        $safeName = basename($filename);
        $filePath = storage_path("app/backups/{$safeName}");

        if (file_exists($filePath)) {
            return unlink($filePath);
        }

        return false;
    }

    /**
     * Import / Restore a MySQL SQL Dump file into the database.
     * Pure PHP implementation using DB::unprepared.
     */
    public function importDump(string $filePath): void
    {
        if (!file_exists($filePath)) {
            throw new \Exception("File SQL dump tidak ditemukan di: {$filePath}");
        }

        // Increase memory & execution time for large imports
        ini_set('memory_limit', '512M');
        set_time_limit(300);

        $sqlContent = file_get_contents($filePath);

        // Execute SQL within disabled FK checks block
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        try {
            DB::unprepared($sqlContent);
        } finally {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        // Clear application caches after database restore
        cache()->flush();
    }

    /**
     * Format bytes to human readable string (KB, MB).
     */
    private function formatBytes(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' bytes';
    }
}
