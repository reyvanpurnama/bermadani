<?php

namespace Database\Seeders;

use App\Models\CooperativeSetting;
use Illuminate\Database\Seeder;

class CooperativeSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // === Officers / Pejabat ===
            ['key' => 'ketua_name',          'value' => 'Ridlo Abdillah, S.Pd., M.Si.',                  'group' => 'officers', 'label' => 'Nama Ketua Koperasi'],
            ['key' => 'ketua_title',         'value' => 'Ketua Koperasi',                                'group' => 'officers', 'label' => 'Jabatan Ketua'],
            ['key' => 'bendahara_name',      'value' => 'Muhammad Alwi Almaliki',                        'group' => 'officers', 'label' => 'Nama Bendahara/Manager'],
            ['key' => 'bendahara_title',     'value' => 'Manager Operasional',                           'group' => 'officers', 'label' => 'Jabatan Bendahara'],
            ['key' => 'pengawas_name',       'value' => '',                                              'group' => 'officers', 'label' => 'Nama Pengawas'],
            ['key' => 'pengawas_title',      'value' => 'Pengawas',                                     'group' => 'officers', 'label' => 'Jabatan Pengawas'],

            // === Bank Accounts ===
            ['key' => 'bank_primary_name',    'value' => 'KB Bukopin Syariah',                           'group' => 'bank', 'label' => 'Nama Bank Utama'],
            ['key' => 'bank_primary_number',  'value' => '7704020507',                                   'group' => 'bank', 'label' => 'No. Rekening Utama'],
            ['key' => 'bank_primary_holder',  'value' => 'Koperasi Konsumen Syariah Berkah Solusi Madani','group' => 'bank', 'label' => 'Atas Nama Rekening Utama'],
            ['key' => 'bank_transfer_name',   'value' => 'Bank Mandiri',                                 'group' => 'bank', 'label' => 'Nama Bank Transfer'],
            ['key' => 'bank_transfer_number', 'value' => '123-00-9876543-2',                             'group' => 'bank', 'label' => 'No. Rekening Transfer'],
            ['key' => 'bank_transfer_holder', 'value' => 'Koperasi UMB',                                 'group' => 'bank', 'label' => 'Atas Nama Transfer'],

            // === RAT Defaults ===
            ['key' => 'rat_default_venue',    'value' => 'Ruang Rapat Utama Koperasi Bermadani UMB',     'group' => 'rat', 'label' => 'Tempat RAT Default'],
            ['key' => 'rat_letter_prefix',    'value' => '/BA-RAT/BERMADANI/',                           'group' => 'rat', 'label' => 'Prefix Nomor Surat RAT'],
            ['key' => 'rat_slogan',           'value' => 'Bersama Anggota, Koperasi Kuat, Manfaat Nyata','group' => 'rat', 'label' => 'Slogan RAT'],
        ];

        foreach ($settings as $setting) {
            CooperativeSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
