# Panduan Deployment ke cPanel (Production)

Berikut adalah langkah-langkah untuk mengupdate aplikasi Web Koperasi UMB di cPanel dengan fitur **Member Management** dan **Auto-Debit** terbaru.

## Prasyarat
- Akses ke cPanel (File Manager atau Terminal/SSH).
- Backup database dan file project saat ini (Sangat Disarankan).

---

## Metode 1: Menggunakan Git (Disarankan jika tersedia SSH/Terminal)

Jika Anda sudah setup Git di cPanel:

1.  **Login ke Terminal cPanel** (atau via SSH).
2.  **Masuk ke direktori project**:
    ```bash
    cd /path/to/your/project/folder
    # Contoh: cd public_html/web-koperasi
    ```
3.  **Pull perubahan terbaru**:
    ```bash
    git pull origin main
    ```
4.  **Install/Update Dependencies** (jika ada perubahan di composer.json):
    ```bash
    composer install --optimize-autoloader --no-dev
    ```
5.  **Jalankan Migrasi Database**:
    Fitur baru memerlukan tabel dan kolom baru.
    ```bash
    php artisan migrate --force
    ```
6.  **Optimasi Cache**:
    ```bash
    php artisan optimize:clear
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    ```
7.  **Selesai!** Cek fitur baru di browser.

---

## Metode 2: Upload Manual (File Manager / FTP)

Jika tidak menggunakan Git di server:

### 1. Persiapan File di Lokal (Komputer Anda)
1.  Pastikan Anda berada di branch `main` yang sudah ter-update.
2.  Zip file-file yang berubah. Fokus pada folder berikut:
    - `app/` (Logika backend baru)
    - `database/migrations/` (Struktur database baru)
    - `resources/views/` (Tampilan baru)
    - `routes/` (Routing baru)
    - `composer.json` & `composer.lock`
    - **`vendor/`** (Sangat Penting: Library Excel baru ada di sini)

### 2. Upload ke cPanel
1.  Buka **File Manager** di cPanel.
2.  Masuk ke folder root aplikasi Laravel Anda.
3.  Upload file ZIP yang sudah disiapkan.
4.  Ekstrak dan timpa (overwrite) file yang lama.

### 3. Update Database (PENTING)
Karena kita tidak bisa menjalankan `php artisan migrate` dengan mudah tanpa terminal, Anda punya 2 opsi:

**Opsi A: Via Terminal cPanel (Jika ada)**
Jalankan perintah:
```bash
php artisan migrate --force
```

**Opsi B: Via Route (Darurat)**
Jika sama sekali tidak ada akses terminal, Anda bisa membuat route sementara untuk menjalankan migrasi.
1.  Buka `routes/web.php` di File Manager.
2.  Tambahkan kode ini di paling bawah:
    ```php
    Route::get('/run-migration', function () {
        Artisan::call('migrate', ['--force' => true]);
        return 'Migration Completed: ' . Artisan::output();
    });
    ```
3.  Buka browser dan akses: `https://domain-anda.com/run-migration`
4.  Jika sukses, **HAPUS** route tersebut segera demi keamanan.

### 4. Clear Cache
Sama seperti migrasi, jika tidak ada terminal, buat route sementara:
```php
Route::get('/clear-cache', function () {
    Artisan::call('optimize:clear');
    return 'Cache Cleared: ' . Artisan::output();
});
```
Akses `https://domain-anda.com/clear-cache`, lalu hapus route-nya.

---

## Checklist Fitur Baru
Setelah deployment, pastikan mengecek hal berikut:
1.  Menu **Member Management** bisa dibuka.
2.  Menu **Auto-Debit Approval** (Riwayat Auto-Debit) tampil dengan benar.
3.  Coba lakukan filter tahun pada dashboard Auto-Debit.
4.  Pastikan data anggota tampil lengkap di detail anggota.

## Troubleshooting
- **Error 500**: Cek file `.env` pastikan konfigurasi database benar. Cek juga permission folder `storage` dan `bootstrap/cache` harus 775 atau 755.
- **Tampilan Berantakan**: Jalankan clear cache view.
- **Database Error**: Pastikan migrasi sudah dijalankan (tabel `simpanan_transactions` harus ada).

---

# Panduan Import Data Member (Production)

Karena database di production masih kosong, Anda perlu mengimpor data member yang sudah kita siapkan.

## Langkah 1: Upload File Data
1.  Buka **File Manager** di cPanel.
2.  Buat folder `docs/data` di root project Anda (sejajar dengan folder `app`, `public`, dll) jika belum ada.
3.  Upload file berikut dari komputer lokal Anda (folder `docs/data/`):
    - `datamemberkoperasi.xlsx`
    - `anggota.csv`

## Langkah 2: Jalankan Import

### Opsi A: Via Terminal (Disarankan)
Jalankan perintah berikut secara berurutan:

1.  **Import Data Dasar Member**:
    ```bash
    php artisan members:import docs/data/datamemberkoperasi.xlsx
    ```
    *Tunggu hingga proses selesai.*

2.  **Sinkronisasi Tanggal Join & Saldo Awal**:
    ```bash
    php artisan member:sync-from-csv
    ```
    *Perintah ini akan membaca file `docs/data/anggota.csv` secara otomatis.*

### Opsi B: Via Route (Tanpa Terminal)
Jika tidak ada akses terminal, buat route sementara di `routes/web.php`:

```php
Route::get('/run-import', function () {
    // 1. Import Excel
    $excelPath = base_path('docs/data/datamemberkoperasi.xlsx');
    if (!file_exists($excelPath)) return "File Excel tidak ditemukan di $excelPath";
    
    Artisan::call('members:import', ['file' => $excelPath]);
    $output1 = Artisan::output();

    // 2. Sync CSV
    Artisan::call('member:sync-from-csv');
    $output2 = Artisan::output();

    return "<pre>IMPORT RESULT:\n$output1\n\nSYNC RESULT:\n$output2</pre>";
});
```

1.  Akses `https://domain-anda.com/run-import` di browser.
2.  Tunggu prosesnya (bisa memakan waktu 1-2 menit).
3.  Jika sukses, **HAPUS** route ini segera.

