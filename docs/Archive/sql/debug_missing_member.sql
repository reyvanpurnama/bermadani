-- Mencari Member ACTIVE yang TIDAK Masuk Laporan Potongan Gaji
-- Kriteria Masuk Laporan:
-- 1. Punya Pinjaman ACTIVE
-- 2. ATAU Simpanan Wajib metode SALARY_DEDUCTION
-- 3. ATAU Simpanan Sukarela metode SALARY_DEDUCTION (> 0)

SELECT 
    id, 
    no_anggota, 
    name, 
    unit_kerja,
    status,
    simwa_payment_method, 
    sukarela_payment_method
FROM members
WHERE status = 'ACTIVE'
-- Filter yang TIDAK punya hutang aktif
AND id NOT IN (
    SELECT DISTINCT member_id 
    FROM loans 
    WHERE status = 'ACTIVE' 
    AND remaining_amount > 0 -- Asumsi logic backend
)
-- Filter yang simwa-nya BUKAN potong gaji
AND (simwa_payment_method != 'SALARY_DEDUCTION' OR simwa_payment_method IS NULL)
-- Filter yang sukarela-nya BUKAN potong gaji
AND (
    sukarela_payment_method != 'SALARY_DEDUCTION' 
    OR sukarela_payment_method IS NULL 
    OR monthly_sukarela_amount <= 0
);
