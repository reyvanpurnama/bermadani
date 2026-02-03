-- ============================================================================
-- PERBAIKAN DATA SIMPANAN SUKARELA - SOPHA HAFITRIANI (Member ID: 468)
-- ============================================================================
-- No Anggota: 24000043
-- 
-- Data yang benar:
-- 2024: Sep(300k), Okt(500k), Nov(500k), Des(500k)
-- 2025: Jan(500k), Feb(500k), Mar(750k), Apr(750k), Mei(750k), Jun(750k)
-- 
-- Total Setor: 5.800.000
-- Total Tarik: 3.630.000 (Mei: 1.630k, Jun: 1.000k, Des: 1.000k)
-- Saldo Akhir: 2.170.000
-- ============================================================================

-- Step 1: Backup data lama (optional - untuk jaga-jaga)
-- CREATE TABLE simpanan_transactions_backup_sopha AS 
-- SELECT * FROM simpanan_transactions WHERE memberId = 468 AND type = 'SUKARELA';

-- Step 2: Hapus semua transaksi SETOR yang salah
DELETE FROM simpanan_transactions 
WHERE memberId = 468 
  AND type = 'SUKARELA' 
  AND transactionType = 'SETOR';

-- Step 3: Insert 10 transaksi SETOR yang benar (1 transaksi per bulan)
-- Catatan: balanceAfter dihitung dengan mempertimbangkan transaksi TARIK yang ada

-- 2024 (4 bulan)
INSERT INTO simpanan_transactions (memberId, type, transactionType, amount, balanceAfter, notes, processedBy, status, created_at, updated_at) VALUES
(468, 'SUKARELA', 'SETOR', 300000.00, 300000.00, 'Setoran Simpanan Sukarela - September 2024', 1, 'APPROVED', '2024-09-25 12:00:00', NOW()),
(468, 'SUKARELA', 'SETOR', 500000.00, 800000.00, 'Setoran Simpanan Sukarela - Oktober 2024', 1, 'APPROVED', '2024-10-25 12:00:00', NOW()),
(468, 'SUKARELA', 'SETOR', 500000.00, 1300000.00, 'Setoran Simpanan Sukarela - November 2024', 1, 'APPROVED', '2024-11-25 12:00:00', NOW()),
(468, 'SUKARELA', 'SETOR', 500000.00, 1800000.00, 'Setoran Simpanan Sukarela - Desember 2024', 1, 'APPROVED', '2024-12-25 12:00:00', NOW()),

-- 2025 (6 bulan)
(468, 'SUKARELA', 'SETOR', 500000.00, 2300000.00, 'Setoran Simpanan Sukarela - Januari 2025', 1, 'APPROVED', '2025-01-25 12:00:00', NOW()),
(468, 'SUKARELA', 'SETOR', 500000.00, 2800000.00, 'Setoran Simpanan Sukarela - Februari 2025', 1, 'APPROVED', '2025-02-25 12:00:00', NOW()),
(468, 'SUKARELA', 'SETOR', 750000.00, 3550000.00, 'Setoran Simpanan Sukarela - Maret 2025', 1, 'APPROVED', '2025-03-25 12:00:00', NOW()),
(468, 'SUKARELA', 'SETOR', 750000.00, 4300000.00, 'Setoran Simpanan Sukarela - April 2025', 1, 'APPROVED', '2025-04-25 12:00:00', NOW()),

-- Mei 2025 - Setor 750k (tgl 25), kemudian ada TARIK 1.630k (tgl 20 - tapi sebelum setor)
-- Urutan: Setor 750k -> Balance jadi 5.050k, lalu TARIK 1.630k -> Balance jadi 3.420k
(468, 'SUKARELA', 'SETOR', 750000.00, 5050000.00, 'Setoran Simpanan Sukarela - Mei 2025', 1, 'APPROVED', '2025-05-25 12:00:00', NOW()),

-- Juni 2025 - Setor 750k (tgl 25), sebelumnya ada TARIK 1.000k (tgl 17)
-- Balance sebelum Juni: 3.420k (setelah TARIK Mei), setor 750k -> 4.170k, TARIK 1.000k -> 3.170k
(468, 'SUKARELA', 'SETOR', 750000.00, 4170000.00, 'Setoran Simpanan Sukarela - Juni 2025', 1, 'APPROVED', '2025-06-25 12:00:00', NOW());

-- Step 4: Update balanceAfter untuk transaksi TARIK yang sudah ada
-- Urutan kronologis: Mei setor -> Mei tarik -> Jun tarik -> Jun setor -> Des tarik
UPDATE simpanan_transactions SET balanceAfter = 3420000.00 WHERE id = 16124; -- TARIK Mei (20/5): 5.050k - 1.630k = 3.420k
UPDATE simpanan_transactions SET balanceAfter = 3170000.00 WHERE id = 16126; -- TARIK Jun (17/6): 4.170k - 1.000k = 3.170k  
UPDATE simpanan_transactions SET balanceAfter = 2170000.00 WHERE id = 16129; -- TARIK Des (13/12): 3.170k - 1.000k = 2.170k

-- Step 5: Update saldo simpanan sukarela di tabel members
UPDATE members 
SET simpananSukarela = 2170000.00,
    updated_at = NOW()
WHERE id = 468;

-- ============================================================================
-- VERIFIKASI (jalankan query ini setelah update untuk cek)
-- ============================================================================
-- SELECT 
--     created_at,
--     transactionType,
--     amount,
--     balanceAfter,
--     notes
-- FROM simpanan_transactions
-- WHERE memberId = 468 AND type = 'SUKARELA'
-- ORDER BY created_at ASC;
-- 
-- SELECT id, name, nomorAnggota, simpananSukarela 
-- FROM members 
-- WHERE id = 468;
-- ============================================================================
