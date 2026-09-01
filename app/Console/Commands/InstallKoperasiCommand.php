<?php

namespace App\Console\Commands;

use App\Models\CooperativeSetting;
use App\Shared\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class InstallKoperasiCommand extends Command
{
    protected $signature = 'koperasi:install {--force : Overwrite existing installation}';

    protected $description = 'Interactive CLI installer for new White-Label Koperasi deployments';

    public function handle(): int
    {
        $this->info('');
        $this->info('====================================================');
        $this->info('   🚀 KOPERASI WHITE-LABEL SUITE - CLI INSTALLER   ');
        $this->info('====================================================');
        $this->info('');

        $installedLock = storage_path('installed');

        if (file_exists($installedLock) && !$this->option('force')) {
            $this->warn('⚠️ Aplikasi ini sudah ter-install!');
            $this->line('File lock ditemukan di: ' . $installedLock);
            $this->line('Gunakan flag --force jika ingin menginstall ulang.');
            return self::FAILURE;
        }

        // 1. Cooperative Info
        $coopName = $this->ask('Masukkan Nama Koperasi Client', 'Koperasi Sejahtera Mandiri');
        $coopShort = $this->ask('Masukkan Nama Singkat / Brand', 'KSM');
        $emailDomain = $this->ask('Masukkan Email Domain Koperasi', 'ksm.co.id');

        // 2. Super Admin Credentials
        $adminName = $this->ask('Masukkan Nama Super Admin', 'Administrator Utama');
        $adminEmail = $this->ask('Masukkan Email Super Admin', 'admin@koperasi.id');
        $adminPassword = $this->secret('Masukkan Password Super Admin (min 8 karakter)');

        if (strlen($adminPassword) < 8) {
            $this->error('❌ Password terlalu pendek! Minimal 8 karakter.');
            return self::FAILURE;
        }

        if (!$this->confirm('Apakah kamu yakin ingin melanjutkan instalasi database? (Ini akan mereset DB!)')) {
            $this->line('Instalasi dibatalkan.');
            return self::SUCCESS;
        }

        $this->info('');
        $this->info('⚙️ Step 1: Menjalankan Database Migrations...');
        Artisan::call('migrate:fresh', ['--force' => true]);
        $this->info('   ✅ Database fresh migrated.');

        $this->info('⚙️ Step 2: Seeding Default Settings...');
        Artisan::call('db:seed', ['--class' => 'CooperativeSettingsSeeder', '--force' => true]);

        // Save Custom Inputs
        CooperativeSetting::setValue('coop_name', $coopName, 'general', 'Nama Koperasi');
        CooperativeSetting::setValue('coop_short_name', $coopShort, 'general', 'Nama Singkat');
        CooperativeSetting::setValue('coop_email_domain', $emailDomain, 'general', 'Email Domain');
        $this->info('   ✅ Settings updated.');

        $this->info('⚙️ Step 3: Membuat Akun Super Admin...');
        User::create([
            'name' => $adminName,
            'email' => $adminEmail,
            'password' => Hash::make($adminPassword),
            'role' => 'SUPER_ADMIN',
        ]);
        $this->info("   ✅ Super Admin {$adminEmail} dibuat.");

        $this->info('⚙️ Step 4: Generating App Key & Clearing Cache...');
        if (empty(env('APP_KEY'))) {
            Artisan::call('key:generate', ['--force' => true]);
        }
        Artisan::call('config:clear');
        Artisan::call('cache:clear');

        // Lock Installation
        file_put_contents($installedLock, date('Y-m-d H:i:s'));

        $this->info('');
        $this->info('====================================================');
        $this->info('  🎉 INSTALASI KOPERASI BERHASIL DILAKUKAN! 🎉     ');
        $this->info('====================================================');
        $this->line("Koperasi : {$coopName} ({$coopShort})");
        $this->line("Admin    : {$adminEmail}");
        $this->line("Lock File: {$installedLock}");
        $this->info('');

        return self::SUCCESS;
    }
}
