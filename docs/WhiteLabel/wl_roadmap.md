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

### Fase 1: Config-Driven Foundation ⬜
> **Goal**: Semua identitas & parameter koperasi bisa diubah tanpa edit source code.

- [ ] Buat `config/cooperative.php` — file konfigurasi utama identitas koperasi
- [ ] Buat tabel `cooperative_settings` — untuk setting yang bisa diubah lewat Admin panel
- [ ] Replace semua hardcoded nama/alamat/telepon di 66 Blade views → `{{ config('cooperative.name') }}`
- [ ] Replace email domain `@bermadani.id` → `config('cooperative.email_domain')`
- [ ] Replace hardcoded pejabat penandatangan → database `cooperative_settings`
- [ ] Replace hardcoded fee/rate/persentase → `config('cooperative.finance.*')`

Detail arsitektur: [[wl_config_architecture|Config Architecture]]

### Fase 2: Dynamic Theming ⬜
> **Goal**: Warna, logo, dan branding bisa dikustomisasi per instalasi.

- [ ] Buat sistem theme config (primary color, accent color, logo URL)
- [ ] Replace hardcoded hex colors di layout files → CSS variables
- [ ] Replace hardcoded hex colors di PDF templates → config-driven
- [ ] Buat halaman "Pengaturan Branding" di Admin panel (upload logo, pilih warna)

### Fase 3: Installer & Packaging ⬜
> **Goal**: Koperasi baru bisa setup dari nol dalam 5 menit.

- [ ] Buat `php artisan koperasi:install` — setup wizard CLI
- [ ] Buat Setup Wizard di browser (first-run flow)
- [ ] Dokumentasi deployment guide untuk white-label customer
- [ ] Hapus/pindahkan semua data fixture spesifik Bermadani ke seeder opsional

---

## 🔗 Related Notes
- [[00_OVERVIEW|System Overview]]
- [[01_ARCHITECTURE_GUIDE|Architecture Guide]]
- [[wl_hardcoded_audit|Audit Hardcoded Values]]
- [[wl_config_architecture|Config Architecture]]
- [[Refactoring_Log|Refactoring Log]]
