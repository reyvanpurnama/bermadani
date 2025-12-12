<?php

namespace App\Console\Commands;

use App\Services\MemberService;
use Illuminate\Console\Command;

class ImportMembers extends Command
{
    protected $signature = 'members:import {file}';
    protected $description = 'Import members from Excel file';

    public function handle(MemberService $memberService)
    {
        $filePath = $this->argument('file');

        if (!file_exists($filePath)) {
            $this->error("File not found: {$filePath}");
            return 1;
        }

        $this->info("Starting import from: {$filePath}");
        $this->info('');

        try {
            $result = $memberService->importFromExcel($filePath);

            $this->info('Import Summary:');
            $this->info('✓ Success: ' . $result['success']);
            $this->warn('⊘ Skipped: ' . $result['skipped']);
            $this->error('✗ Errors: ' . $result['errors']);

            if (count($result['error_details']) > 0) {
                $this->info('');
                $this->error('Error Details:');
                foreach (array_slice($result['error_details'], 0, 10) as $error) {
                    $this->line('  - ' . $error);
                }
                
                if (count($result['error_details']) > 10) {
                    $this->line('  ... and ' . (count($result['error_details']) - 10) . ' more errors');
                }
            }

            $this->info('');
            $this->info('Import completed!');
            return 0;

        } catch (\Exception $e) {
            $this->error('Import failed: ' . $e->getMessage());
            $this->error($e->getTraceAsString());
            return 1;
        }
    }
}
