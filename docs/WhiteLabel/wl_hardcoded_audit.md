# Audit: Hardcoded Values (White-Label Blockers)

Daftar lengkap semua nilai yang di-hardcode di codebase dan harus dijadikan **config-driven** agar software bisa dipakai koperasi lain.

---

## 🔴 1. Branding & Identitas Koperasi

### 1.1 Nama Koperasi (40+ lokasi)

Terdapat **4 varian nama** yang tersebar di seluruh view:

| Varian Nama | Contoh Lokasi | Jumlah |
|---|---|---|
| `Bermadani` | sidebar, login, landing, layouts | ~15 file |
| `Koperasi UMB` | layout titles, member dashboard, POS | ~10 file |
| `Koperasi Konsumen Syariah Berkah Solusi Madani` | PDF reports, RAT documents | ~6 file |
| `Universitas Muhammadiyah Bandung / Bengkulu / Mercu Buana` | Receipts, reports | ~5 file |

**File-file kunci yang perlu diubah:**

```text
# Layouts (semua <title> tag)
resources/views/layouts/admin.blade.php          → "Koperasi UMB"
resources/views/layouts/member.blade.php         → "Koperasi UMB"
resources/views/layouts/membership.blade.php     → "Bermadani"
resources/views/layouts/supplier.blade.php       → "Koperasi UMB"

# Auth & Public
resources/views/auth/login.blade.php             → "BERMADANI", title, slogan
resources/views/landing.blade.php                → "Bermadani" (4 lokasi)
resources/views/supplier/register.blade.php      → "Bermadani" (3 lokasi)

# Sidebars
resources/views/partials/sidebar.blade.php       → "Bermadani"
resources/views/partials/member-sidebar.blade.php → "Bermadani"

# PDF Reports (nama legal formal)
resources/views/admin/reports/member-account-pdf.blade.php
resources/views/admin/reports/monthly-financial-pdf.blade.php
resources/views/admin/reports/payroll-simple-pdf.blade.php
resources/views/pdf/berita-acara-rat.blade.php
resources/views/pdf/rat-shu-report.blade.php
```

### 1.2 Nama Pejabat Hardcoded

| Nama | Jabatan | File |
|---|---|---|
| `RIDLO ABDILLAH, S.Pd., M.Si.` | Ketua Koperasi | `developer-payroll-pdf`, `member-account-pdf` |
| `Muhammad Alwi Almaliki` | Manager Operasional | `monthly-financial-pdf`, `payroll-simple-pdf` |

### 1.3 Kota Penandatanganan

`Bandung` hardcoded di 4 file PDF report sebagai kota penandatanganan dokumen.

---

## 🔴 2. Contact Info & Rekening Bank

### 2.1 Alamat & Telepon

| Info | Value | File |
|---|---|---|
| Alamat Bandung | `Jl. Soekarno-Hatta No.752, Cipadung Kidul...` | `berita-acara-rat.blade.php` |
| Alamat Bengkulu | `Jl. Bali, Bengkulu` | `transactions/receipt.blade.php` |
| Telp Bengkulu | `(0736) 22765` | `transactions/receipt.blade.php` |
| Telp Jakarta | `(021) 5840-816` | `payment-receipt.blade.php` |
| Website | `www.koperasiumb.com` | `transactions/receipt.blade.php` |

### 2.2 Rekening Bank

| Bank | No. Rekening | Atas Nama | File |
|---|---|---|---|
| KB Bukopin Syariah | `7704020507` | Kop. Berkah Solusi Madani | `payroll-simple-pdf` |
| Bank Mandiri | `123-00-9876543-2` | Koperasi UMB | `membership/simpanan` |

### 2.3 Email Domain

`@bermadani.id` hardcoded di **5 file PHP**:
- `MemberService.php` (auto-generate member email)
- `RetailMemberManagement.php`
- `PosCustom.php`
- `RegenerateMemberNumbers.php`
- `UpdateMemberEmails.php`

---

## 🟡 3. Konstanta Keuangan

### 3.1 Fee & Rate Defaults

| Parameter | Default Value | File |
|---|---|---|
| Admin Fee Pinjaman | Rp 25.000 | `LoanCreate.php` |
| Simwa BMT Deduction | Rp 30.000 | `LoanCreate.php` |
| Simpanan Wajib Default | Rp 50.000/bulan | `MonthlyFinancialReport.php`, migration |
| Supplier Registration Fee | Rp 25.000 | migration |
| Supplier Monthly Fee | Rp 25.000 | migration |
| Consignment Profit Share | 90% supplier / 10% koperasi | migration |

### 3.2 SHU Allocation Percentages

| Pos SHU | Default % | Source |
|---|---|---|
| Cadangan | 25% | migration `overhaul_rat_sessions` |
| Jasa Simpanan | 30% | migration |
| Jasa Usaha | 25% | migration |
| Pengurus | 10% | migration |
| Dana Sosial | 10% | migration |

### 3.3 Loan Source Enum (Hardcoded Partner)

`BERMADANI` dan `BMT_ITQAN` di-hardcode sebagai enum di migration, model, dan 10+ Livewire/Blade files. Idealnya jadi tabel `channeling_partners` yang dinamis.

---

## 🟡 4. Tema & Warna

### 4.1 Layout Colors (CSS Hex)

| Layout | Primary Color | Hex |
|---|---|---|
| Admin | Indigo-600 | `#4F46E5` |
| Member | Emerald-500 | `#10b981` |
| Membership | Indigo-500 | `#6366f1` |
| Supplier | Indigo-600 | `#4F46E5` |
| Landing/Login | Sapphire Blue | `#0F52BA` |

### 4.2 PDF Template Colors

5 PDF templates menggunakan inline CSS dengan `#0F52BA` hardcoded di 30+ baris styling.

---

## 🟡 5. Logo & Asset Paths

| Path | File | Issue |
|---|---|---|
| `/home/tanesheva/Documents/Bermadani/logo/Kop.png` | `RatReportPdfController.php` | ⚠️ Path dev lokal! |
| `public_path('images/Kop.png')` | `RatReportPdfController.php`, `berita-acara-rat` | Hardcoded filename |
| External Unsplash image | `login.blade.php` | Background image |

---

## 🟠 6. Fallback Data Spesifik Bermadani

`RatInfographic.php` dan `RatVisualDashboard.php` mengandung fallback angka spesifik Bermadani:
- Active members: `113`, `131`
- Simpanan Wajib: `Rp 156.100.000`
- Simpanan Pokok: `Rp 22.100.000`
- Cash: `Rp 30.499.118`

---

## 🔗 Related Notes
- [[wl_roadmap|White-Label Roadmap]]
- [[wl_config_architecture|Config Architecture]]
- [[mod_koperasi|Domain Koperasi]]
- [[schema_summary|Database Schema]]
