# Config Architecture for White-Label

Arsitektur konfigurasi yang dibutuhkan agar software bisa berjalan di koperasi manapun **tanpa mengubah source code**.

---

## 🏗️ Strategi: 2 Layer Config

### Layer 1: `config/cooperative.php` (Static Config)
Untuk nilai yang **jarang berubah** dan biasanya di-set saat instalasi pertama kali.

```php
<?php
// config/cooperative.php

return [
    // === IDENTITAS ===
    'name'            => env('COOP_NAME', 'Koperasi Bermadani'),
    'legal_name'      => env('COOP_LEGAL_NAME', 'Koperasi Konsumen Syariah Berkah Solusi Madani'),
    'short_name'      => env('COOP_SHORT_NAME', 'Bermadani'),
    'parent_org'      => env('COOP_PARENT_ORG', 'Universitas Muhammadiyah Bandung'),
    'tagline'         => env('COOP_TAGLINE', 'Satu Kartu, Ribuan Kemudahan'),
    'website'         => env('COOP_WEBSITE', 'www.koperasiumb.com'),
    'email_domain'    => env('COOP_EMAIL_DOMAIN', 'bermadani.id'),

    // === KONTAK ===
    'address'         => env('COOP_ADDRESS', 'Jl. Soekarno-Hatta No.752, Bandung'),
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
        'supplier'    => env('COOP_COLOR_SUPPLIER', '#4F46E5'),
    ],

    // === KEUANGAN (defaults) ===
    'finance' => [
        'simpanan_wajib_default'    => env('COOP_SIMWA_DEFAULT', 50000),
        'loan_admin_fee'            => env('COOP_LOAN_ADMIN_FEE', 25000),
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
];
```

### Layer 2: `cooperative_settings` (Database Table)
Untuk nilai yang **sering berubah** atau perlu diubah lewat Admin panel tanpa akses server.

```text
Table: cooperative_settings
┌─────────────────────────┬───────────────────────────────────────────┐
│ key (VARCHAR, PK)       │ value (TEXT)                             │
├─────────────────────────┼───────────────────────────────────────────┤
│ ketua_name              │ Ridlo Abdillah, S.Pd., M.Si.             │
│ ketua_title             │ Ketua Koperasi                           │
│ bendahara_name          │ Muhammad Alwi Almaliki                   │
│ bendahara_title         │ Manager Operasional                      │
│ bank_name               │ KB Bukopin Syariah                       │
│ bank_account_number     │ 7704020507                               │
│ bank_account_holder     │ Kop. Konsumen Syariah Berkah Solusi Madani│
│ bank_transfer_name      │ Bank Mandiri                             │
│ bank_transfer_number    │ 123-00-9876543-2                         │
│ bank_transfer_holder    │ Koperasi UMB                             │
│ rat_default_venue       │ Ruang Rapat Utama Koperasi Bermadani UMB │
│ rat_letter_prefix       │ /BA-RAT/BERMADANI/                       │
│ receipt_footer_text     │ Terima kasih atas kunjungan Anda!         │
│ receipt_policy_text     │ Barang yang sudah dibeli tidak dapat...    │
└─────────────────────────┴───────────────────────────────────────────┘
```

---

## 📐 Pola Penggunaan di Blade Views

### Sebelum (Hardcoded)
```blade
<title>Admin - Koperasi UMB</title>
<h1>BERMADANI</h1>
<p>Jl. Soekarno-Hatta No.752, Bandung</p>
```

### Sesudah (Config-Driven)
```blade
<title>Admin - {{ config('cooperative.name') }}</title>
<h1>{{ config('cooperative.short_name') }}</h1>
<p>{{ config('cooperative.address') }}</p>
```

### Untuk Database Settings (Pejabat, Bank, dll)
```php
// Helper function (buat di app/Helpers atau Service)
function coop_setting(string $key, $default = null): string
{
    return cache()->remember("coop_setting_{$key}", 3600, function () use ($key, $default) {
        return DB::table('cooperative_settings')
            ->where('key', $key)->value('value') ?? $default;
    });
}
```

```blade
{{-- Di PDF template --}}
<div class="signer-name">{{ coop_setting('ketua_name') }}</div>
<div class="signer-title">{{ coop_setting('ketua_title') }}</div>
```

---

## 🎨 Pola Tema Dinamis (CSS Variables)

### Di Layout (`layouts/admin.blade.php`)
```blade
<style>
    :root {
        --color-primary: {{ config('cooperative.theme.admin') }};
        --color-primary-light: {{ config('cooperative.theme.admin') }}22;
    }
</style>
```

### Di CSS
```css
/* Ganti semua #4F46E5 → var(--color-primary) */
.sidebar-active { background-color: var(--color-primary); }
.btn-primary { background-color: var(--color-primary); }
```

---

## 📁 File `.env` untuk Instalasi Baru

```env
# === COOPERATIVE IDENTITY ===
COOP_NAME="Koperasi Sejahtera Mandiri"
COOP_LEGAL_NAME="Koperasi Konsumen Sejahtera Mandiri"
COOP_SHORT_NAME="KSM"
COOP_PARENT_ORG="PT Maju Bersama"
COOP_EMAIL_DOMAIN="ksm.co.id"
COOP_ADDRESS="Jl. Sudirman No.123, Jakarta"
COOP_CITY="Jakarta"
COOP_PHONE="(021) 5551234"
COOP_WEBSITE="www.ksm.co.id"

# === THEME ===
COOP_COLOR_PRIMARY="#1E40AF"
COOP_COLOR_ADMIN="#1E40AF"
COOP_COLOR_MEMBER="#059669"

# === FINANCE ===
COOP_SIMWA_DEFAULT=100000
COOP_LOAN_ADMIN_FEE=50000
```

---

## 🔗 Related Notes
- [[wl_roadmap|White-Label Roadmap]]
- [[wl_hardcoded_audit|Audit Hardcoded Values]]
- [[01_ARCHITECTURE_GUIDE|Architecture Guide]]
- [[Coding_Standards|Coding Standards]]
