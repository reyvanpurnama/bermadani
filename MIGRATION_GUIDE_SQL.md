# Panduan Migrasi Database Manual (via phpMyAdmin)

Jika Anda ingin memindahkan data dari lokal ke production secara manual tanpa menggunakan perintah import, ikuti langkah-langkah berikut.

## Langkah 1: Export Data dari Lokal (SUDAH DILAKUKAN - MODE AMAN)
Saya telah membuat file export khusus yang **AMAN** untuk production yang sudah berisi data.
File ini bernama: **`database_data_safe.sql`** (ada di root folder project).

**Keamanan file ini:**
*   **TIDAK MENGHAPUS** data lama (No DROP TABLE).
*   **TIDAK MENIMPA** user yang sudah ada (Menggunakan INSERT IGNORE).
*   Hanya data baru yang akan masuk.
*   Tabel `activity_log` dan data lainnya **AMAN**.

File ini berisi data tambahan untuk tabel:
*   `users`
*   `members`
*   `simpanan_transactions`

## Langkah 2: Import ke Production (cPanel)
1.  Login ke cPanel -> Buka **phpMyAdmin**.
2.  Pilih database production Anda.
3.  (Opsional tapi Disarankan) Backup database production saat ini dengan menu **Export** -> **Quick**.
4.  Buka tab **Import**.
5.  Upload file **`database_data_safe.sql`** yang ada di root folder project.
6.  Klik **Go** / **Kirim**.

## Langkah 3: Verifikasi
1.  Buka aplikasi web Anda.
2.  Cek menu **Member Management**, pastikan data anggota muncul.
3.  Cek menu **Riwayat Auto-Debit**, pastikan data transaksi muncul.

---

## Catatan Penting
*   Pastikan struktur tabel di lokal dan production sudah sama. Jika Anda belum menjalankan `php artisan migrate` di server, cara ini justru lebih aman karena file SQL dari lokal biasanya sudah menyertakan struktur tabel (`CREATE TABLE`).
*   Jika ada error "Foreign Key Constraint Fails", coba disable foreign key check sementara saat import. Biasanya ada opsi `SET FOREIGN_KEY_CHECKS=0;` di awal file SQL export.
