-- Query untuk Menambahkan Member AHMAD SOBAR & History Simpanan (FINAL VERSION - Structure Adjusted)
-- Run di phpMyAdmin

START TRANSACTION;

-- 0. BERSIH-BERSIH (Hapus data lama "Ahmad Sobar" jika ada, agar bersih)
-- Hapus transaksi simpanan member tersebut dulu (karena foreign key)
DELETE FROM `simpanan_transactions` WHERE `memberId` IN (SELECT `id` FROM `members` WHERE `email` = '25000014@bermadani.id');
DELETE FROM `members` WHERE `email` = '25000014@bermadani.id';
DELETE FROM `users` WHERE `email` = '25000014@bermadani.id';

-- 1. Insert User
INSERT INTO `users` 
(`name`, `email`, `password`, `role`, `isActive`, `mustChangePassword`, `created_at`, `updated_at`) 
VALUES 
('AHMAD SOBAR', '25000014@bermadani.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'MEMBER', 1, 1, '2025-01-16 10:00:00', '2025-01-16 10:00:00');

SET @UserId = LAST_INSERT_ID();

-- 2. Insert Member (Lengkap sesuai struktur)
INSERT INTO `members` 
(`userId`, `nomorAnggota`, `name`, `email`, `gender`, `unitKerja`, `joinDate`, `status`, `isMemberKoperasi`, 
 `simwa_payment_method`, `sukarela_payment_method`, `monthly_sukarela_amount`, `monthly_simpanan_wajib`, `monthly_wajib_amount`, 
 `simpananPokok`, `simpananWajib`, `simpananSukarela`, `points`, `totalSpent`, `tier`, `created_at`, `updated_at`) 
VALUES 
(@UserId, '25000014', 'AHMAD SOBAR', '25000014@bermadani.id', 'MALE', 'UMBandung', '2025-01-16 10:00:00', 'ACTIVE', 1, 
 'SALARY_DEDUCTION', 'MANUAL', 0, 50000, 50000, 
 200000, 650000, 0, 0, 0, 'BRONZE', '2025-01-16 10:00:00', '2025-01-16 10:00:00');

SET @MemberId = LAST_INSERT_ID();

-- 3. Insert Simpanan POKOK
INSERT INTO `simpanan_transactions` 
(`memberId`, `type`, `transactionType`, `amount`, `paidAmount`, `balanceAfter`, `status`, `notes`, `processedBy`, `billingMonth`, `created_at`, `updated_at`) 
VALUES 
(@MemberId, 'POKOK', 'SETOR', 200000, 200000, 200000, 'APPROVED', 'Simpanan Pokok', 1, NULL, '2025-01-16 10:00:00', '2025-01-16 10:00:00');

-- 4. Simpanan WAJIB (Jan 2025 - Jan 2026)
INSERT INTO `simpanan_transactions` 
(`memberId`, `type`, `transactionType`, `amount`, `paidAmount`, `balanceAfter`, `status`, `notes`, `processedBy`, `billingMonth`, `created_at`, `updated_at`) 
VALUES 
(@MemberId, 'WAJIB', 'SETOR', 50000, 50000, 250000, 'APPROVED', 'Simpanan Wajib Jan 2025', 1, '2025-01', '2025-01-25 10:00:00', '2025-01-25 10:00:00'),
(@MemberId, 'WAJIB', 'SETOR', 50000, 50000, 300000, 'APPROVED', 'Simpanan Wajib Feb 2025', 1, '2025-02', '2025-02-25 10:00:00', '2025-02-25 10:00:00'),
(@MemberId, 'WAJIB', 'SETOR', 50000, 50000, 350000, 'APPROVED', 'Simpanan Wajib Mar 2025', 1, '2025-03', '2025-03-25 10:00:00', '2025-03-25 10:00:00'),
(@MemberId, 'WAJIB', 'SETOR', 50000, 50000, 400000, 'APPROVED', 'Simpanan Wajib Apr 2025', 1, '2025-04', '2025-04-25 10:00:00', '2025-04-25 10:00:00'),
(@MemberId, 'WAJIB', 'SETOR', 50000, 50000, 450000, 'APPROVED', 'Simpanan Wajib May 2025', 1, '2025-05', '2025-05-25 10:00:00', '2025-05-25 10:00:00'),
(@MemberId, 'WAJIB', 'SETOR', 50000, 50000, 500000, 'APPROVED', 'Simpanan Wajib Jun 2025', 1, '2025-06', '2025-06-25 10:00:00', '2025-06-25 10:00:00'),
(@MemberId, 'WAJIB', 'SETOR', 50000, 50000, 550000, 'APPROVED', 'Simpanan Wajib Jul 2025', 1, '2025-07', '2025-07-25 10:00:00', '2025-07-25 10:00:00'),
(@MemberId, 'WAJIB', 'SETOR', 50000, 50000, 600000, 'APPROVED', 'Simpanan Wajib Aug 2025', 1, '2025-08', '2025-08-25 10:00:00', '2025-08-25 10:00:00'),
(@MemberId, 'WAJIB', 'SETOR', 50000, 50000, 650000, 'APPROVED', 'Simpanan Wajib Sep 2025', 1, '2025-09', '2025-09-25 10:00:00', '2025-09-25 10:00:00'),
(@MemberId, 'WAJIB', 'SETOR', 50000, 50000, 700000, 'APPROVED', 'Simpanan Wajib Oct 2025', 1, '2025-10', '2025-10-25 10:00:00', '2025-10-25 10:00:00'),
(@MemberId, 'WAJIB', 'SETOR', 50000, 50000, 750000, 'APPROVED', 'Simpanan Wajib Nov 2025', 1, '2025-11', '2025-11-25 10:00:00', '2025-11-25 10:00:00'),
(@MemberId, 'WAJIB', 'SETOR', 50000, 50000, 800000, 'APPROVED', 'Simpanan Wajib Dec 2025', 1, '2025-12', '2025-12-25 10:00:00', '2025-12-25 10:00:00'),
(@MemberId, 'WAJIB', 'SETOR', 50000, 50000, 850000, 'APPROVED', 'Simpanan Wajib Jan 2026', 1, '2026-01', '2026-01-25 10:00:00', '2026-01-25 10:00:00');

COMMIT;
