# White-Label Roadmap

Roadmap transformasi **Koperasi Bermadani** dari single-tenant product menjadi **White-Label Software** yang bisa dijual/diinstall ke koperasi manapun.

---

## 🎯 Tujuan

Membuat software ini bisa dipakai oleh koperasi lain dengan **branding mereka sendiri** — tanpa perlu mengubah source code. Cukup ganti konfigurasi, logo, dan warna.

---

## 📊 Current Gap Analysis

> **Hasil audit menemukan 150+ hardcoded values** yang tersebar di 66 Blade views, 7 Services, dan 16 Console Commands.

| Kategori | Jumlah Temuan | Severity |
|---|---|---|
| Nama Koperasi ("Bermadani", "UMB", nama legal) | 40+ lokasi | 🔴 Critical |
| Nama Pejabat (Ketua, Bendahara) hardcoded | 8 lokasi | 🔴 Critical |
| Alamat, Telepon, Bank Account | 12 lokasi | 🔴 Critical |
| Email domain `@bermadani.id` | 5 lokasi | 🔴 Critical |
| Warna tema (hex colors) | 8 layout files + 5 PDF templates | 🟡 Medium |
| Konstanta keuangan (fee, rate, persentase SHU) | 15+ lokasi | 🟡 Medium |
| Loan source enum `BERMADANI/BMT_ITQAN` | 10+ lokasi | 🟡 Medium |
| Logo path hardcoded (termasuk path dev lokal!) | 3 lokasi | 🟡 Medium |
| Fallback data RAT (angka spesifik Bermadani) | 10+ lokasi | 🟠 Low |

Detail lengkap: [[wl_hardcoded_audit|Audit Hardcoded Values]]

---

## 🗺️ Roadmap (3 Fase)

### Fase 1: Config-Driven Foundation ✅
> **Goal**: Semua identitas & parameter koperasi bisa diubah tanpa edit source code.

- [x] Buat `config/cooperative.php` — file konfigurasi utama identitas koperasi
- [x] Buat tabel `cooperative_settings` — untuk setting yang bisa diubah lewat Admin panel
- [x] Replace semua hardcoded nama/alamat/telepon di 66 Blade views → `{{ coop_config('name') }}`
- [x] Replace email domain `@bermadani.id` → `coop_config('email_domain')`
- [x] Replace hardcoded pejabat penandatangan → database `cooperative_settings`
- [x] Replace hardcoded fee/rate/persentase → `coop_config('finance.*')`

Detail arsitektur: [[wl_config_architecture|Config Architecture]]

### Fase 2: Dynamic Theming & Admin Settings ✅
> **Goal**: Warna, logo, dan branding bisa dikustomisasi per instalasi langsung dari Admin Panel.

- [x] Buat sistem theme config (primary color, admin, member, supplier colors)
- [x] Replace hardcoded colors di layout files → `coop_config()`
- [x] Replace hardcoded colors di PDF templates → config-driven
- [x] Buat halaman "Pengaturan Koperasi" di Admin panel (`/admin/settings`) dengan 6 tab:
  1. Identitas & Kontak
  2. Branding & Logo (Upload logo, kop surat, favicon)
  3. Pejabat & RAT (Ketua, Bendahara, Pengawas, Slogan RAT)
  4. Rekening Bank (Bank Utama & Bank Transfer)
  5. Tema & Warna (Color Picker per layout)
  6. Parameter Keuangan & Struk Kasir

### Fase 3: Installer & Packaging ✅
> **Goal**: Koperasi baru bisa setup dari nol dalam 5 menit.

- [x] Buat `php artisan koperasi:install` — setup wizard CLI interaktif
- [x] Buat Web Setup Wizard di browser (`/install`) 4-step UI:
  1. System Requirements Check (PHP 8.2+, Extensions, Storage Permissions)
  2. Database Configuration & Connection Tester
  3. Identitas Koperasi & Akun Super Admin Setup
  4. Automated Setup Execution & Installation Lock (`storage/installed`)
- [x] Middlewares `EnsureAppIsInstalled` & `RedirectIfInstalled`
- [x] Automatic deployment lock & protection

---

## 🔗 Related Notes
- [[00_OVERVIEW|System Overview]]
- [[01_ARCHITECTURE_GUIDE|Architecture Guide]]
- [[wl_hardcoded_audit|Audit Hardcoded Values]]
- [[wl_config_architecture|Config Architecture]]
- [[Refactoring_Log|Refactoring Log]]
