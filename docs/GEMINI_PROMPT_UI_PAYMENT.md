# PROMPT UNTUK GEMINI - UI Form Pembayaran Simpanan

## KONTEKS SISTEM

Saya sedang mengembangkan **Sistem Informasi Koperasi** berbasis web dengan fitur manajemen simpanan anggota. Sistem ini menggunakan:
- **Framework**: Laravel 12 + Livewire 3
- **UI Framework**: Tailwind CSS
- **Design**: Modern dashboard dengan dark mode support
- **Icons**: BoxIcons (`bx` class)

## EXISTING DESIGN PATTERN

Sistem ini sudah memiliki komponen dengan style konsisten. Contoh pattern yang sudah ada:

### 1. Card Container
```html
<div class="bg-white dark:bg-darkCard rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
    <!-- content -->
</div>
```

### 2. Input Field
```html
<div class="mb-4">
    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">
        Nama Field
    </label>
    <input type="text" 
           class="w-full px-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors"
           placeholder="Masukkan...">
</div>
```

### 3. Button Primary
```html
<button class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold shadow-lg shadow-indigo-500/20 transition-all transform hover:-translate-y-1 flex items-center gap-2">
    <i class='bx bx-save'></i>
    Simpan
</button>
```

### 4. Select Dropdown
```html
<select class="w-full px-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500 transition-colors">
    <option value="">Pilih...</option>
</select>
```

### 5. Alert Success
```html
<div class="mb-6 p-4 bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30 text-emerald-700 dark:text-emerald-400 rounded-xl flex items-center gap-3 shadow-sm">
    <i class='bx bxs-check-circle text-2xl'></i>
    <span class="font-medium">Success message</span>
</div>
```

## FITUR YANG HARUS DIBUAT

### Halaman: Form Input Pembayaran Simpanan

**URL**: `/admin/payments/create`

**Tujuan**: 
Admin dapat mencatat pembayaran simpanan dari anggota (cash, transfer, atau auto-debit). Sistem harus support pembayaran cicilan dan multi-tagihan.

**Data Flow Livewire**:
```php
// Properties yang tersedia dari backend
public $members; // Collection of all members
public $selectedMemberId;
public $unpaidBills = []; // Bills yang belum lunas untuk member terpilih
public $selectedBills = []; // Array of bill IDs yang dipilih untuk dibayar
public $paymentMethod = 'CASH'; // CASH, TRANSFER, AUTO_DEBIT
public $paymentDate;
public $totalAmount = 0; // Auto-calculated
public $referenceNumber; // Nomor transfer (optional)
public $proofAttachment; // File upload (optional)
public $notes;
```

**Alur Interaksi**:
1. Admin pilih anggota dari dropdown → `wire:model.live="selectedMemberId"`
2. Sistem otomatis load daftar tagihan yang belum lunas → tampil di tabel
3. Admin centang tagihan mana yang mau dibayar → checkbox dengan `wire:model="selectedBills"`
4. Sistem auto-calculate total amount
5. Admin pilih metode pembayaran
6. Jika TRANSFER → wajib isi referenceNumber & upload bukti
7. Admin isi tanggal pembayaran
8. Submit → `wire:click="processPayment"`

## REQUIREMENTS DETAIL

### Section 1: Header
- Title: "Input Pembayaran Simpanan"
- Subtitle: "Catat pembayaran simpanan dari anggota"
- Button "Kembali" di kiri atas (icon: `bx-arrow-back`)

### Section 2: Form Pilih Anggota
- Dropdown select member
- Tampilkan: `Nama Anggota - No. Anggota - Unit Kerja`
- Searchable (gunakan Livewire wire:model.live)
- Loading state saat data fetching

### Section 3: Tabel Tagihan Belum Lunas
Hanya muncul SETELAH anggota dipilih.

**Kolom Tabel**:
1. Checkbox untuk pilih tagihan
2. Bulan Tagihan (dari field `billingMonth`, format: "Desember 2025")
3. Jenis Simpanan (POKOK/WAJIB/SUKARELA)
4. Jumlah Tagihan (amount)
5. Sudah Dibayar (paidAmount)
6. Sisa Tagihan (remainingAmount) - **bold & highlighted**

**Features**:
- Checkbox "Pilih Semua" di header
- Highlight row yang dipilih (bg-indigo-50)
- Badge status: UNPAID (merah), PARTIAL (kuning)
- Show empty state jika tidak ada tagihan

### Section 4: Summary Card (Sticky di Samping)
Card yang show:
- Total Tagihan Dipilih: Rp xxx
- Jumlah Item: x tagihan
- Auto-update saat checkbox berubah

### Section 5: Detail Pembayaran Form

**Fields**:
1. **Metode Pembayaran** (radio buttons):
   - CASH (icon: bx-money)
   - TRANSFER (icon: bx-transfer)
   - AUTO_DEBIT (icon: bx-credit-card)

2. **Tanggal Pembayaran** (date input)

3. **Nomor Referensi** (text input, WAJIB jika TRANSFER)

4. **Upload Bukti Transfer** (file input, WAJIB jika TRANSFER)
   - Accept: image/*, .pdf
   - Preview gambar setelah upload
   - Max size info: "Max 2MB"

5. **Catatan** (textarea, optional)

### Section 6: Action Buttons
- Button "Batal" (secondary, outline)
- Button "Proses Pembayaran" (primary, disabled jika form invalid)
  - Loading state saat submit
  - Icon: bx-check-circle

## VALIDASI & UX

### Show Error State:
- Border merah pada field yang error
- Text error di bawah field (text-rose-500)

### Loading States:
- Skeleton loader saat fetch unpaid bills
- Button disabled + spinner saat submit
- Overlay blocking saat processing

### Success Flow:
- Setelah success, show modal konfirmasi dengan:
  - Nomor Kuitansi
  - Total Dibayar
  - Button "Cetak Kuitansi"
  - Button "Input Pembayaran Lain"

## RESPONSIVE DESIGN

- Desktop: Form 2 kolom (form kiri, summary kanan)
- Tablet: Stack jadi 1 kolom
- Mobile: Simplified view, sticky summary card di bottom

## ACCESSIBILITY

- Label jelas untuk semua input
- Placeholder yang deskriptif
- Focus states yang jelas
- Error messages yang helpful

## OUTPUT YANG DIMINTA

Tolong generate **Livewire Blade Component** lengkap dengan:

1. **HTML Structure** mengikuti pattern design yang sudah ada
2. **Livewire Directives** yang tepat (wire:model, wire:click, dll)
3. **Conditional Rendering** (@if, @foreach)
4. **Dark Mode Support** penuh
5. **Responsive Classes** Tailwind
6. **Loading & Empty States**
7. **Icon Integration** dengan BoxIcons

Format file: `resources/views/livewire/admin/payment-form.blade.php`

**PENTING**:
- Jangan gunakan Alpine.js (sistem pakai Livewire only)
- Konsisten dengan color scheme: indigo (primary), emerald (success), amber (warning), rose (danger)
- Gunakan rounded-xl atau rounded-2xl untuk semua komponen
- Dark mode class: `dark:bg-darkCard` untuk card, `dark:bg-slate-800` untuk input

Terima kasih!
