-- FIX DATA PINJAMAN INTERNAL & HYBRID (BERMADANI)
-- Berdasarkan Analisa Payroll Des 2025 vs Angsuran Itqan

START TRANSACTION;

-- ==========================================
-- 1. MEMBER HYBRID (Punya Itqan + Internal)
-- ==========================================

-- Meti Mediyastuti
-- Payroll: 1.100.033 | Itqan: 358.333 | Simwa: 50.000
-- Internal = 1.100.033 - 358.333 - 50.000 = 691.700
INSERT INTO `loans` (`member_id`, `loanSource`, `amount`, `monthlyPayment`, `tenor`, `paid_installments`, `status`, `created_at`, `updated_at`)
SELECT `id`, 'BERMADANI', 691700 * 12, 691700, 12, 0, 'ACTIVE', NOW(), NOW()
FROM `members` WHERE `name` LIKE '%Meti Mediyastuti%';

-- Yusup Sopyan
-- Payroll: 1.536.133 | Itqan: 1.152.800 | Simwa: 50.000
-- Internal = 1.536.133 - 1.152.800 - 50.000 = 333.333
INSERT INTO `loans` (`member_id`, `loanSource`, `amount`, `monthlyPayment`, `tenor`, `paid_installments`, `status`, `created_at`, `updated_at`)
SELECT `id`, 'BERMADANI', 333333 * 12, 333333, 12, 0, 'ACTIVE', NOW(), NOW()
FROM `members` WHERE `name` LIKE '%Yusup Sopyan%';

-- Rizan Febrian
-- Payroll: 1.383.906 | Itqan Total: 1.147.239 | Simwa: 50.000
-- Internal = 1.383.906 - 1.147.239 - 50.000 = 186.667
INSERT INTO `loans` (`member_id`, `loanSource`, `amount`, `monthlyPayment`, `tenor`, `paid_installments`, `status`, `created_at`, `updated_at`)
SELECT `id`, 'BERMADANI', 186667 * 12, 186667, 12, 0, 'ACTIVE', NOW(), NOW()
FROM `members` WHERE `name` LIKE '%Rizan Febrian%';


-- ==========================================
-- 2. MEMBER MURNI INTERNAL (Gak ada di Itqan)
-- Rumus: Angsuran = Payroll - 50.000 (Simwa)
-- ==========================================

-- Asep Indra Sugiri (741.700 - 50.000 = 691.700)
INSERT INTO `loans` (`member_id`, `loanSource`, `amount`, `monthlyPayment`, `tenor`, `paid_installments`, `status`, `created_at`, `updated_at`)
SELECT `id`, 'BERMADANI', 691700 * 16, 691700, 16, 0, 'ACTIVE', NOW(), NOW()
FROM `members` WHERE `name` LIKE '%Asep Indra Sugiri%';

-- Siti Solihat (1.483.350 - 50.000 - 200.000 Sukarela = 1.233.350)
-- Note: Sukarela diupdate terpisah di bawah
INSERT INTO `loans` (`member_id`, `loanSource`, `amount`, `monthlyPayment`, `tenor`, `paid_installments`, `status`, `created_at`, `updated_at`)
SELECT `id`, 'BERMADANI', 1233350 * 12, 1233350, 12, 0, 'ACTIVE', NOW(), NOW()
FROM `members` WHERE `name` LIKE '%Siti Solihat%';

-- Aldi Waluya P (320.000 - 50.000 = 270.000)
INSERT INTO `loans` (`member_id`, `loanSource`, `amount`, `monthlyPayment`, `tenor`, `paid_installments`, `status`, `created_at`, `updated_at`)
SELECT `id`, 'BERMADANI', 270000 * 12, 270000, 12, 0, 'ACTIVE', NOW(), NOW()
FROM `members` WHERE `name` LIKE '%Aldi Waluya P%';

-- Deni Saepul (320.000 - 50.000 = 270.000)
INSERT INTO `loans` (`member_id`, `loanSource`, `amount`, `monthlyPayment`, `tenor`, `paid_installments`, `status`, `created_at`, `updated_at`)
SELECT `id`, 'BERMADANI', 270000 * 12, 270000, 12, 0, 'ACTIVE', NOW(), NOW()
FROM `members` WHERE `name` LIKE '%Deni Saepul%';

-- Deden (291.667 - 50.000 = 241.667)
INSERT INTO `loans` (`member_id`, `loanSource`, `amount`, `monthlyPayment`, `tenor`, `paid_installments`, `status`, `created_at`, `updated_at`)
SELECT `id`, 'BERMADANI', 241667 * 16, 241667, 16, 0, 'ACTIVE', NOW(), NOW()
FROM `members` WHERE `name` LIKE 'Deden%';

-- Dea Siti Nuraeni (320.000 - 50.000 = 270.000)
INSERT INTO `loans` (`member_id`, `loanSource`, `amount`, `monthlyPayment`, `tenor`, `paid_installments`, `status`, `created_at`, `updated_at`)
SELECT `id`, 'BERMADANI', 270000 * 12, 270000, 12, 0, 'ACTIVE', NOW(), NOW()
FROM `members` WHERE `name` LIKE '%Dea Siti Nuraeni%';

-- Syarif Syahidin (570.000 - 50.000 = 520.000)
INSERT INTO `loans` (`member_id`, `loanSource`, `amount`, `monthlyPayment`, `tenor`, `paid_installments`, `status`, `created_at`, `updated_at`)
SELECT `id`, 'BERMADANI', 520000 * 12, 520000, 12, 0, 'ACTIVE', NOW(), NOW()
FROM `members` WHERE `name` LIKE '%Syarif Syahidin%';


-- ==========================================
-- 3. UPDATE SUKARELA (Tita & Siti Solihat)
-- ==========================================

-- Siti Solihat: Sukarela 200.000
UPDATE `members` SET `monthly_sukarela_amount` = 200000 
WHERE `name` LIKE '%Siti Solihat%';

-- Tita: Sukarela 100.000
UPDATE `members` SET `monthly_sukarela_amount` = 100000 
WHERE `name` LIKE 'Tita%';

COMMIT;
