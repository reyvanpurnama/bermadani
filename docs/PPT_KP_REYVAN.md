# 📊 PPT KERJA PRAKTIK — M REYVAN PURNAMA
**Implementasi Sistem Informasi Koperasi Bermadani Berbasis Web**
Durasi: 5-10 menit presentasi | Sisa: Q&A

---

## SLIDE 1 — COVER

**Judul:**
> Implementasi Sistem Informasi Koperasi Bermadani Berbasis Web

| Field | Isi |
|---|---|
| Nama | M Reyvan Purnama |
| NIM | [NIM REYVAN] |
| Program Studi | Sistem Informasi |
| Instansi | Koperasi Bermadani - Universitas Muhammadiyah Bandung |
| Periode | Oktober — Desember 2025 |

---

## SLIDE 2 — MASALAH

**Headline:** *Sebelum ada sistem ini...*

- 📋 Transaksi dicatat manual / spreadsheet terpisah
- 📦 Stok barang sering tidak akurat
- 📑 Laporan keuangan makan waktu lama
- 🔍 Tidak ada audit trail dan transparansi

> **"Koperasi dengan ratusan anggota, tapi masih pakai Excel."**

---

## SLIDE 3 — SOLUSI & TECH STACK

**Headline:** *Satu platform, semua terintegrasi*

```
Laravel 12      →  Backend & Business Logic
Livewire 3      →  Reaktif tanpa JavaScript berat
Tailwind CSS 4  →  UI modern & konsisten
Alpine.js       →  Interaktivitas ringan
MySQL 8         →  Database relasional
```

**Kenapa Laravel?**
- Convention over configuration → development lebih cepat
- Ekosistem besar, security built-in
- Cocok untuk tim berbasis PHP

---

## SLIDE 4 — FITUR UTAMA ⭐

*Tunjukin screenshot langsung, jelasin singkat*

### 4A. POS (Point of Sale)
- Kasir transaksi + member search real-time
- Bayar pakai saldo simpanan sukarela
- Quick register member langsung di kasir

### 4B. Portal Anggota
- Cek saldo simpanan (Pokok, Wajib, Sukarela)
- Transfer simpanan antar anggota
- Riwayat transaksi & pinjaman

### 4C. Laporan Keuangan
- Generate PDF otomatis bulanan
- Neraca (balance sheet)
- Rekap harian & rekap unit

### 4D. Audit SIMWA
- Import CSV dari BMT
- Smart split logic (SIMPOK/SIMWA/SUKARELA)
- Rekonsiliasi otomatis vs data sistem

---

## SLIDE 5 — SCOPE & ARSITEKTUR

**26 Modul** dikembangkan dalam 3 bulan

| Role | Akses |
|---|---|
| SuperAdmin / Admin | Full access semua modul |
| Kasir | POS & shift management |
| Supplier | Portal konsinyasi |
| Anggota Koperasi | Portal simpanan & pinjaman |
| Member Retail | Portal belanja & saldo |

**Highlights:**
- ✅ Dark mode support penuh
- ✅ Mobile-first responsive
- ✅ Multi-guard authentication

---

## SLIDE 6 — PENCAPAIAN

> *Angka yang ngomong sendiri*

| Metric | Angka |
|---|---|
| Total Commits | **457 commits** |
| Durasi | **3 bulan** |
| Modul Selesai | **26 modul** |
| Halaman/Komponen | **50+** |
| Status | **Production-ready** |

**Rata-rata:** 5 commits per hari selama 3 bulan

---

## SLIDE 7 — KESIMPULAN

- Sistem berhasil **digitalisasi operasional** Koperasi Bermadani secara end-to-end
- Pengalaman **full-stack real-world**: dari setup project hingga production deployment
- Sistem **scalable** dan siap dikembangkan lebih lanjut

---

## 🎤 CHEAT SHEET Q&A

**Q: Kenapa pakai Laravel, bukan framework lain?**
> Convention over configuration, ekosistem mature, security built-in, cocok untuk project skala menengah-besar

**Q: Kesulitan terbesar?**
> Logika rekonsiliasi SIMWA — format CSV dari BMT tidak konsisten, harus bikin smart split logic untuk deteksi SIMPOK/SIMWA/SUKARELA secara otomatis

**Q: Bedanya Livewire sama React/Vue?**
> Livewire server-side rendering, tidak perlu API endpoint terpisah, lebih simpel untuk tim PHP, trade-off: tidak secepat SPA untuk interaksi ultra-realtime

**Q: Bagaimana keamanan sistemnya?**
> Multi-guard auth, CSRF protection, role-based middleware, activity logging, input validation & sanitization

**Q: Sistem ini sudah dipakai?**
> Sudah production-ready dan siap digunakan untuk operasional koperasi

---

*Notes untuk presenter: Slide 4 adalah inti — tunjukin screenshot/demo langsung biar berkesan. Jangan baca slide, ceritain aja dengan santai.*
