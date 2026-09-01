<?php

namespace App\Livewire\Admin;

use App\Models\CooperativeSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class CooperativeSettings extends Component
{
    use WithFileUploads;

    public string $activeTab = 'general';

    // === General Settings ===
    public string $coop_name = '';
    public string $coop_legal_name = '';
    public string $coop_short_name = '';
    public string $coop_parent_org = '';
    public string $coop_tagline = '';
    public string $coop_landing_tagline = '';
    public string $coop_website = '';
    public string $coop_email_domain = '';
    public string $coop_address = '';
    public string $coop_city = '';
    public string $coop_phone = '';

    // === Branding Files ===
    public $logo_file;
    public $kop_surat_file;
    public $favicon_file;
    public string $current_logo = '';
    public string $current_kop = '';
    public string $current_favicon = '';

    // === Officers & RAT ===
    public string $ketua_name = '';
    public string $ketua_title = '';
    public string $bendahara_name = '';
    public string $bendahara_title = '';
    public string $pengawas_name = '';
    public string $pengawas_title = '';
    public string $rat_default_venue = '';
    public string $rat_letter_prefix = '';
    public string $rat_slogan = '';

    // === Bank Accounts ===
    public string $bank_primary_name = '';
    public string $bank_primary_number = '';
    public string $bank_primary_holder = '';
    public string $bank_transfer_name = '';
    public string $bank_transfer_number = '';
    public string $bank_transfer_holder = '';

    // === Theme Colors ===
    public string $theme_primary = '#0F52BA';
    public string $theme_admin = '#4F46E5';
    public string $theme_member = '#10b981';
    public string $theme_membership = '#6366f1';
    public string $theme_supplier = '#4F46E5';

    // === Financial Parameters ===
    public $fin_simwa_default = 50000;
    public $fin_loan_admin_fee = 25000;
    public $fin_bmt_simwa_deduction = 30000;
    public $fin_supplier_reg_fee = 25000;
    public $fin_supplier_monthly_fee = 25000;
    public $fin_consignment_share = 90.00;

    // === Receipt Text ===
    public string $receipt_footer_text = '';
    public string $receipt_policy_text = '';

    public function mount(): void
    {
        // Load General
        $this->coop_name            = coop_config('name');
        $this->coop_legal_name      = coop_config('legal_name');
        $this->coop_short_name      = coop_config('short_name');
        $this->coop_parent_org      = coop_config('parent_org');
        $this->coop_tagline         = coop_config('tagline');
        $this->coop_landing_tagline = coop_config('landing_tagline');
        $this->coop_website         = coop_config('website');
        $this->coop_email_domain    = coop_config('email_domain');
        $this->coop_address         = coop_config('address');
        $this->coop_city            = coop_config('city');
        $this->coop_phone           = coop_config('phone');

        // Load Branding
        $this->current_logo    = coop_config('logo_path');
        $this->current_kop     = coop_config('kop_surat_path');
        $this->current_favicon = coop_config('favicon_path');

        // Load Officers & RAT
        $this->ketua_name        = coop_setting('ketua_name', 'Ridlo Abdillah, S.Pd., M.Si.');
        $this->ketua_title       = coop_setting('ketua_title', 'Ketua Koperasi');
        $this->bendahara_name    = coop_setting('bendahara_name', 'Muhammad Alwi Almaliki');
        $this->bendahara_title   = coop_setting('bendahara_title', 'Manager Operasional');
        $this->pengawas_name     = coop_setting('pengawas_name', '');
        $this->pengawas_title    = coop_setting('pengawas_title', 'Pengawas');
        $this->rat_default_venue = coop_setting('rat_default_venue', 'Ruang Rapat Utama Koperasi');
        $this->rat_letter_prefix = coop_setting('rat_letter_prefix', '/BA-RAT/');
        $this->rat_slogan        = coop_setting('rat_slogan', 'Bersama Anggota, Koperasi Kuat, Manfaat Nyata');

        // Load Bank Accounts
        $this->bank_primary_name    = coop_setting('bank_primary_name', 'KB Bukopin Syariah');
        $this->bank_primary_number  = coop_setting('bank_primary_number', '7704020507');
        $this->bank_primary_holder  = coop_setting('bank_primary_holder', 'Koperasi Konsumen Syariah Berkah Solusi Madani');
        $this->bank_transfer_name   = coop_setting('bank_transfer_name', 'Bank Mandiri');
        $this->bank_transfer_number = coop_setting('bank_transfer_number', '123-00-9876543-2');
        $this->bank_transfer_holder = coop_setting('bank_transfer_holder', 'Koperasi UMB');

        // Load Theme
        $this->theme_primary    = coop_config('theme.primary');
        $this->theme_admin      = coop_config('theme.admin');
        $this->theme_member     = coop_config('theme.member');
        $this->theme_membership = coop_config('theme.membership');
        $this->theme_supplier   = coop_config('theme.supplier');

        // Load Finance
        $this->fin_simwa_default       = coop_config('finance.simpanan_wajib_default');
        $this->fin_loan_admin_fee       = coop_config('finance.loan_admin_fee');
        $this->fin_bmt_simwa_deduction  = coop_config('finance.bmt_simwa_deduction');
        $this->fin_supplier_reg_fee     = coop_config('finance.supplier_registration_fee');
        $this->fin_supplier_monthly_fee = coop_config('finance.supplier_monthly_fee');
        $this->fin_consignment_share    = coop_config('finance.consignment_profit_share');

        // Load Receipt
        $this->receipt_footer_text = coop_config('receipt.footer_text');
        $this->receipt_policy_text = coop_config('receipt.policy_text');
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function saveGeneral(): void
    {
        $this->validate([
            'coop_name'       => 'required|string|max:255',
            'coop_legal_name' => 'required|string|max:255',
            'coop_short_name' => 'required|string|max:50',
            'coop_email_domain' => 'required|string|max:100',
        ]);

        $settings = [
            'coop_name'            => [$this->coop_name, 'general', 'Nama Koperasi'],
            'coop_legal_name'      => [$this->coop_legal_name, 'general', 'Nama Legal'],
            'coop_short_name'      => [$this->coop_short_name, 'general', 'Nama Singkat'],
            'coop_parent_org'      => [$this->coop_parent_org, 'general', 'Organisasi Induk'],
            'coop_tagline'         => [$this->coop_tagline, 'general', 'Tagline Aplikasi'],
            'coop_landing_tagline' => [$this->coop_landing_tagline, 'general', 'Tagline Landing Page'],
            'coop_website'         => [$this->coop_website, 'general', 'Website'],
            'coop_email_domain'    => [$this->coop_email_domain, 'general', 'Email Domain'],
            'coop_address'         => [$this->coop_address, 'general', 'Alamat Lengkap'],
            'coop_city'            => [$this->coop_city, 'general', 'Kota'],
            'coop_phone'           => [$this->coop_phone, 'general', 'No. Telepon'],
        ];

        foreach ($settings as $key => [$val, $group, $label]) {
            CooperativeSetting::setValue($key, $val, $group, $label);
        }

        session()->flash('message', 'Identitas Koperasi berhasil diperbarui!');
    }

    public function saveBranding(): void
    {
        $this->validate([
            'logo_file'      => 'nullable|image|max:2048',
            'kop_surat_file' => 'nullable|image|max:3072',
            'favicon_file'   => 'nullable|image|max:1024',
        ]);

        if ($this->logo_file) {
            $path = $this->logo_file->store('branding', 'public');
            CooperativeSetting::setValue('logo_path', 'storage/' . $path, 'branding', 'Path Logo');
            $this->current_logo = 'storage/' . $path;
        }

        if ($this->kop_surat_file) {
            $path = $this->kop_surat_file->store('branding', 'public');
            CooperativeSetting::setValue('kop_surat_path', 'storage/' . $path, 'branding', 'Path Kop Surat');
            $this->current_kop = 'storage/' . $path;
        }

        if ($this->favicon_file) {
            $path = $this->favicon_file->store('branding', 'public');
            CooperativeSetting::setValue('favicon_path', 'storage/' . $path, 'branding', 'Path Favicon');
            $this->current_favicon = 'storage/' . $path;
        }

        session()->flash('message', 'Aset Branding berhasil diperbarui!');
    }

    public function saveOfficers(): void
    {
        $settings = [
            'ketua_name'        => [$this->ketua_name, 'officers', 'Nama Ketua'],
            'ketua_title'       => [$this->ketua_title, 'officers', 'Jabatan Ketua'],
            'bendahara_name'    => [$this->bendahara_name, 'officers', 'Nama Bendahara/Manager'],
            'bendahara_title'   => [$this->bendahara_title, 'officers', 'Jabatan Bendahara/Manager'],
            'pengawas_name'     => [$this->pengawas_name, 'officers', 'Nama Pengawas'],
            'pengawas_title'    => [$this->pengawas_title, 'officers', 'Jabatan Pengawas'],
            'rat_default_venue' => [$this->rat_default_venue, 'rat', 'Tempat RAT Default'],
            'rat_letter_prefix' => [$this->rat_letter_prefix, 'rat', 'Prefix Surat RAT'],
            'rat_slogan'        => [$this->rat_slogan, 'rat', 'Slogan RAT'],
        ];

        foreach ($settings as $key => [$val, $group, $label]) {
            CooperativeSetting::setValue($key, $val, $group, $label);
        }

        session()->flash('message', 'Data Pejabat & RAT berhasil disimpan!');
    }

    public function saveBank(): void
    {
        $settings = [
            'bank_primary_name'    => [$this->bank_primary_name, 'bank', 'Nama Bank Utama'],
            'bank_primary_number'  => [$this->bank_primary_number, 'bank', 'No. Rekening Utama'],
            'bank_primary_holder'  => [$this->bank_primary_holder, 'bank', 'Atas Nama Utama'],
            'bank_transfer_name'   => [$this->bank_transfer_name, 'bank', 'Nama Bank Transfer'],
            'bank_transfer_number' => [$this->bank_transfer_number, 'bank', 'No. Rekening Transfer'],
            'bank_transfer_holder' => [$this->bank_transfer_holder, 'bank', 'Atas Nama Transfer'],
        ];

        foreach ($settings as $key => [$val, $group, $label]) {
            CooperativeSetting::setValue($key, $val, $group, $label);
        }

        session()->flash('message', 'Rekening Bank berhasil disimpan!');
    }

    public function saveTheme(): void
    {
        $settings = [
            'theme_primary'    => [$this->theme_primary, 'theme', 'Warna Primary'],
            'theme_admin'      => [$this->theme_admin, 'theme', 'Warna Admin'],
            'theme_member'     => [$this->theme_member, 'theme', 'Warna Portal Member'],
            'theme_membership' => [$this->theme_membership, 'theme', 'Warna Simpanan'],
            'theme_supplier'   => [$this->theme_supplier, 'theme', 'Warna Supplier'],
        ];

        foreach ($settings as $key => [$val, $group, $label]) {
            CooperativeSetting::setValue($key, $val, $group, $label);
        }

        session()->flash('message', 'Tema & Warna berhasil diperbarui!');
    }

    public function saveFinance(): void
    {
        $settings = [
            'fin_simwa_default'       => [(string)$this->fin_simwa_default, 'finance', 'Simpanan Wajib Default'],
            'fin_loan_admin_fee'      => [(string)$this->fin_loan_admin_fee, 'finance', 'Admin Fee Pinjaman'],
            'fin_bmt_simwa_deduction' => [(string)$this->fin_bmt_simwa_deduction, 'finance', 'Simwa BMT'],
            'fin_supplier_reg_fee'    => [(string)$this->fin_supplier_reg_fee, 'finance', 'Biaya Pendaftaran Supplier'],
            'fin_supplier_monthly_fee'=> [(string)$this->fin_supplier_monthly_fee, 'finance', 'Biaya Bulanan Supplier'],
            'fin_consignment_share'   => [(string)$this->fin_consignment_share, 'finance', 'Bagi Hasil Supplier (%)'],
            'receipt_footer_text'     => [$this->receipt_footer_text, 'receipt', 'Teks Footer Struk'],
            'receipt_policy_text'     => [$this->receipt_policy_text, 'receipt', 'Teks Kebijakan Struk'],
        ];

        foreach ($settings as $key => [$val, $group, $label]) {
            CooperativeSetting::setValue($key, $val, $group, $label);
        }

        session()->flash('message', 'Parameter Keuangan berhasil diperbarui!');
    }

    public function createBackup(): void
    {
        try {
            $backupService = new \App\Services\DatabaseBackupService();
            $filePath = $backupService->generateDump();
            $filename = basename($filePath);

            session()->flash('message', "Backup database `{$filename}` berhasil dibuat!");
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal membuat backup database: ' . $e->getMessage());
        }
    }

    public function downloadBackup(string $filename)
    {
        $safeName = basename($filename);
        $filePath = storage_path("app/backups/{$safeName}");

        if (file_exists($filePath)) {
            return response()->download($filePath);
        }

        session()->flash('error', 'File backup tidak ditemukan!');
    }

    public function deleteBackup(string $filename): void
    {
        $backupService = new \App\Services\DatabaseBackupService();
        if ($backupService->deleteBackup($filename)) {
            session()->flash('message', "File backup `{$filename}` berhasil dihapus!");
        } else {
            session()->flash('error', 'Gagal menghapus file backup!');
        }
    }

    public function getBackupListProperty(): array
    {
        $backupService = new \App\Services\DatabaseBackupService();
        return $backupService->getBackupList();
    }

    public function render()
    {
        return view('livewire.admin.cooperative-settings', [
            'backups' => $this->backupList,
        ])
            ->extends('layouts.admin')
            ->section('content');
    }
}
