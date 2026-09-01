<?php

return [
    // === IDENTITAS ===
    'name'            => env('COOP_NAME', 'Koperasi Bermadani'),
    'legal_name'      => env('COOP_LEGAL_NAME', 'Koperasi Konsumen Syariah Berkah Solusi Madani'),
    'short_name'      => env('COOP_SHORT_NAME', 'Bermadani'),
    'parent_org'      => env('COOP_PARENT_ORG', 'Universitas Muhammadiyah Bandung'),
    'tagline'         => env('COOP_TAGLINE', 'Satu Kartu, Ribuan Kemudahan'),
    'landing_tagline' => env('COOP_LANDING_TAGLINE', 'Satu Akses Untuk Semua Anggota'),
    'website'         => env('COOP_WEBSITE', 'www.koperasiumb.com'),
    'email_domain'    => env('COOP_EMAIL_DOMAIN', 'bermadani.id'),
    'version'         => env('COOP_VERSION', 'v1.0.0'),

    // === KONTAK ===
    'address'         => env('COOP_ADDRESS', 'Jl. Soekarno-Hatta No.752, Cipadung Kidul, Kec. Panyileukan, Kota Bandung, Jawa Barat 40614'),
    'city'            => env('COOP_CITY', 'Bandung'),
    'phone'           => env('COOP_PHONE', '(022) 1234567'),

    // === BRANDING ===
    'logo_path'       => env('COOP_LOGO', 'images/logo.png'),
    'kop_surat_path'  => env('COOP_KOP', 'images/Kop.png'),
    'favicon_path'    => env('COOP_FAVICON', 'images/favicon.ico'),

    // === TEMA ===
    'theme' => [
        'primary'     => env('COOP_COLOR_PRIMARY', '#0F52BA'),
        'admin'       => env('COOP_COLOR_ADMIN', '#4F46E5'),
        'member'      => env('COOP_COLOR_MEMBER', '#10b981'),
        'membership'  => env('COOP_COLOR_MEMBERSHIP', '#6366f1'),
        'supplier'    => env('COOP_COLOR_SUPPLIER', '#4F46E5'),
    ],

    // === KEUANGAN (defaults) ===
    'finance' => [
        'simpanan_wajib_default'    => env('COOP_SIMWA_DEFAULT', 50000),
        'loan_admin_fee'            => env('COOP_LOAN_ADMIN_FEE', 25000),
        'bmt_simwa_deduction'       => env('COOP_BMT_SIMWA', 30000),
        'supplier_registration_fee' => env('COOP_SUPPLIER_REG_FEE', 25000),
        'supplier_monthly_fee'      => env('COOP_SUPPLIER_MONTHLY_FEE', 25000),
        'consignment_profit_share'  => env('COOP_CONSIGNMENT_SHARE', 90.00),
    ],

    // === SHU ALLOCATION (%) ===
    'shu' => [
        'cadangan'        => env('COOP_SHU_CADANGAN', 25.00),
        'jasa_simpanan'   => env('COOP_SHU_SIMPANAN', 30.00),
        'jasa_usaha'      => env('COOP_SHU_USAHA', 25.00),
        'pengurus'        => env('COOP_SHU_PENGURUS', 10.00),
        'dana_sosial'     => env('COOP_SHU_SOSIAL', 10.00),
    ],

    // === DEFAULT PASSWORD ===
    'default_password' => env('COOP_DEFAULT_PASSWORD', 'password'),

    // === RECEIPT ===
    'receipt' => [
        'footer_text'  => env('COOP_RECEIPT_FOOTER', 'Terima kasih atas kunjungan Anda!'),
        'policy_text'  => env('COOP_RECEIPT_POLICY', 'Barang yang sudah dibeli tidak dapat dikembalikan'),
    ],
];
