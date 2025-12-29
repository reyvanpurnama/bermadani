# Sistem Tagihan & Pembayaran Simpanan

## Konsep Bisnis

### Status Dual-Track
1. **Bill Status** (Status Tagihan)
   - `DRAFT`: Tagihan baru di-generate, belum final
   - `APPROVED`: Tagihan sudah disetujui, siap terima pembayaran
   - `CANCELLED`: Tagihan dibatalkan

2. **Payment Status** (Status Pembayaran) - *computed*
   - `UNPAID`: Belum ada pembayaran sama sekali
   - `PARTIAL`: Sudah bayar sebagian, masih ada sisa
   - `PAID`: Sudah lunas penuh

### Flow Bisnis

```
1. GENERATE TAGIHAN BULANAN
   Admin → Generate Auto-Debit
   → Buat record di simpanan_transactions
   → billStatus: DRAFT
   → paidAmount: 0
   
2. APPROVE TAGIHAN
   Admin → Review & Approve
   → billStatus: DRAFT → APPROVED
   → Tagihan siap terima pembayaran
   
3. PEMBAYARAN (Bisa Berkali-kali)
   Anggota bayar → Admin input pembayaran
   → Create record di simpanan_payments
   → Update paidAmount di bill
   → Payment Status: UNPAID → PARTIAL → PAID
   
4. LUNAS
   Ketika paidAmount >= amount
   → Update saldo simpanan anggota
   → Tagihan complete
```

## Database Schema

### Tabel: `simpanan_transactions` (BILLS)
Fungsi: Menyimpan **tagihan** bulanan

**Field Baru:**
- `billingMonth` (string): Format Y-m (contoh: "2025-12")
- `billStatus` (enum): DRAFT, APPROVED, CANCELLED
- `paidAmount` (decimal): Total yang sudah dibayar

### Tabel: `simpanan_payments` (PAYMENTS)
Fungsi: Menyimpan **record pembayaran** riil

**Fields:**
- `billId`: Foreign key ke simpanan_transactions
- `memberId`: Foreign key ke members
- `amount`: Jumlah yang dibayar
- `paymentMethod`: CASH, TRANSFER, AUTO_DEBIT
- `paymentDate`: Tanggal bayar
- `referenceNumber`: Nomor transfer/bukti (optional)
- `receiptNumber`: Nomor kuitansi (auto-generated, unique)
- `proofAttachment`: Path file bukti transfer
- `notes`: Catatan tambahan
- `processedBy`: User yang input pembayaran

## Business Rules

### Rule 1: Pembayaran Cicilan
- Anggota bisa bayar sebagian dari tagihan
- Sistem track `paidAmount` vs `amount`
- Bisa bayar berkali-kali sampai lunas

### Rule 2: Pembayaran Multi-Bulan
- Anggota bisa punya beberapa tagihan belum lunas
- Saat bayar, admin pilih mau bayar tagihan bulan mana
- Bisa bayar beberapa tagihan sekaligus

### Rule 3: Update Saldo
- Saldo simpanan anggota **hanya** update ketika tagihan LUNAS penuh
- Tidak update saat pembayaran PARTIAL
- Ini untuk menjaga konsistensi data

### Rule 4: Kuitansi
- Setiap pembayaran dapat kuitansi unik
- Format: `RCP-YYYYMMDD-XXXX`
- Contoh: `RCP-20251229-0001`

## API / Service Layer

### `SimpananPaymentService::recordPayment($data)`
Input:
```php
[
    'billId' => 123,
    'amount' => 50000,
    'paymentMethod' => 'CASH', // CASH, TRANSFER, AUTO_DEBIT
    'paymentDate' => '2025-12-29',
    'referenceNumber' => 'TRX123456', // optional
    'proofAttachment' => $file, // optional (UploadedFile)
    'notes' => 'Pembayaran bulan Desember'
]
```

Output:
```php
[
    'success' => true,
    'payment' => SimpananPayment,
    'bill' => SimpananTransaction (updated),
    'message' => 'Pembayaran berhasil...'
]
```

### `SimpananPaymentService::getUnpaidBills($memberId, $type = null)`
Return: Collection of bills yang belum lunas

### `SimpananPaymentService::getPaymentHistory($memberId, $limit = null)`
Return: Collection of payment records

## Next Steps (To Be Implemented)

### 1. UI - Form Input Pembayaran
- Pilih anggota
- Tampilkan daftar tagihan yang belum lunas
- Form input pembayaran (amount, method, dll)
- Upload bukti transfer (optional)
- Preview kuitansi setelah save

### 2. UI - Receipt/Kuitansi Print
- Blade template untuk kuitansi
- Export PDF
- Print function

### 3. Update Existing Features
- Update MonthlyDebitApproval:
  - Generate tagihan dengan billStatus = DRAFT
  - Approve tagihan → billStatus = APPROVED
  - Tampilkan payment status di tabel
- Update Member Detail:
  - Tab "Tunggakan" untuk lihat unpaid bills
  - Tab "Riwayat Pembayaran"

### 4. Reports
- Laporan Tunggakan per Anggota
- Laporan Pembayaran Harian/Bulanan
- Rekap Piutang Koperasi

## Migration Command

```bash
php artisan migrate
```

Ini akan create:
1. Tabel `simpanan_payments`
2. Add columns ke `simpanan_transactions`: billingMonth, billStatus, paidAmount
