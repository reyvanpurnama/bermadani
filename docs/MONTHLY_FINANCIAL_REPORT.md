# Monthly Financial Report System

## Overview
Sistem otomatis untuk generate laporan keuangan bulanan (Potongan Gaji & SIMWA) dalam format PDF profesional yang akan diserahkan ke Unit Keuangan UMB.

## Features
✅ **Auto-collect data** dari database:
- Angsuran pinjaman aktif per bulan
- SIMWA (Simpanan Wajib) Rp 50.000/bulan
- Simpanan Sukarela (opsional)

✅ **Professional PDF Template**:
- Header dengan logo & periode
- Summary cards (Total Angsuran, SIMWA, Sukarela, Grand Total)
- Tabel lengkap dengan breakdown per anggota
- Signature section untuk Ketua & Bendahara
- Auto-generated timestamp

✅ **Filter by Period**:
- Pilih bulan (Januari - Desember)
- Pilih tahun (2020 - sekarang)

✅ **Preview & Download**:
- Preview laporan sebelum download
- Download PDF dengan 1 klik
- Format filename: `Laporan_Keuangan_Bulanan_YYYY_MM.pdf`

## Access
**Route:** `/admin/reports/monthly-financial`

**Menu:** Admin Sidebar → Reports → Laporan Bulanan

**Role Required:** SUPER_ADMIN, ADMIN, DEVELOPER

## How It Works

### Data Collection Logic
1. **Members with Active Loans:**
   - Query: Loans dengan status ACTIVE di periode tersebut
   - Data: Nama, Monthly Payment, Sisa Tenor
   - Auto-include: SIMWA 50k

2. **Members with SIMWA Only:**
   - Query: SimpananTransaction tipe WAJIB di periode tersebut
   - Exclude: Yang sudah ada di list angsuran
   - Data: Nama, SIMWA amount

3. **Sukarela Detection:**
   - Check: SimpananTransaction tipe SUKARELA di periode
   - Auto-append: Jika ada, tambahkan ke total

### Calculation
```
Total per Member = Angsuran + SIMWA + Sukarela
Grand Total = Sum of All Members
```

## Usage Example

### Step 1: Pilih Periode
- Bulan: Desember
- Tahun: 2025

### Step 2: Generate Laporan
Klik "Generate Laporan" → Preview muncul dengan:
- Total Member: 81 orang
- Total Angsuran: Rp 27.901.706
- Total SIMWA: Rp 4.100.000
- Total Sukarela: Rp 300.000
- **Grand Total: Rp 32.301.706**

### Step 3: Download PDF
Klik "Download PDF" → File saved: `Laporan_Keuangan_Bulanan_2025_12.pdf`

## PDF Structure

```
┌─────────────────────────────────────────┐
│ KOPERASI KARYAWAN UMB                   │
│ LAPORAN POTONGAN GAJI & SIMWA           │
│ Periode: Desember 2025                  │
├─────────────────────────────────────────┤
│ Info Box:                               │
│ - Tanggal Generate                      │
│ - Total Anggota                         │
│ - Unit Tujuan: Unit Keuangan UMB        │
├─────────────────────────────────────────┤
│ Summary Cards:                          │
│ [Angsuran] [SIMWA] [Sukarela] [Total]  │
├─────────────────────────────────────────┤
│ Table:                                  │
│ No | Nama | Angsuran | SIMWA | ...     │
│  1 | Meti | 1.100.033| 50.000| ...     │
│  2 | Yadi |   741.700| 50.000| ...     │
│ ...                                     │
│ TOTAL: | 27.901.706 | 4.100.000 | ...  │
├─────────────────────────────────────────┤
│ Catatan:                                │
│ 1. Potongan via payroll otomatis        │
│ 2. Transfer ke Rek BCA ...              │
├─────────────────────────────────────────┤
│ Signature Section:                      │
│ Ketua Koperasi | Bendahara Koperasi     │
│ (...............) | (...............)    │
└─────────────────────────────────────────┘
```

## Database Dependencies

### Tables Used:
1. **members** - Data anggota
2. **loans** - Pinjaman aktif
3. **loan_payments** - History pembayaran (untuk hitung sisa tenor)
4. **simpanan_transactions** - SIMWA & Sukarela

### Relationships:
```php
Member → hasMany → Loans
Loan → hasMany → LoanPayments
Member → hasMany → SimpananTransactions
```

## File Structure
```
app/
└── Livewire/
    └── Admin/
        └── MonthlyFinancialReport.php  # Main component

resources/
└── views/
    ├── livewire/
    │   └── admin/
    │       └── monthly-financial-report.blade.php  # UI with preview
    └── admin/
        └── reports/
            └── monthly-financial-pdf.blade.php  # PDF template

routes/
└── web.php  # Route definition
```

## Next Improvements (Optional)
- [ ] Email PDF to Unit Keuangan
- [ ] Schedule auto-generate setiap tanggal 25
- [ ] Export to Excel alternative
- [ ] Historical reports archive
- [ ] Custom bank account per month
- [ ] Multi-signature support

## Troubleshooting

### Issue: PDF blank atau error
**Solution:** 
- Check dompdf installed: `composer show barryvdh/laravel-dompdf`
- Clear cache: `php artisan optimize:clear`

### Issue: Data tidak muncul
**Solution:**
- Verify loan status = 'ACTIVE'
- Check simpanan_transactions periode
- Debug with `dd($reportData)` in component

### Issue: Total tidak akurat
**Solution:**
- Ensure SIMWA default = 50000
- Check sukarela detection logic
- Verify member has `isMemberKoperasi = true`

## Credits
**Developer:** GitHub Copilot + User  
**Library:** barryvdh/laravel-dompdf  
**Framework:** Laravel 12 + Livewire 3  
**Date:** January 2026
