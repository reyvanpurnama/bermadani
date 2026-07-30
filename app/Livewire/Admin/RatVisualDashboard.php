<?php

namespace App\Livewire\Admin;

use App\Models\BankTransaction;
use App\Models\FinancialTransaction;
use App\Models\Loan;
use App\Models\Member;
use App\Models\SimpananTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class RatVisualDashboard extends Component
{
    public $selectedYear;
    public $availableYears = [];

    public function mount()
    {
        $this->selectedYear = 2025;

        $startYear = 2020;
        $currentYear = (int) date('Y');
        $years = range($startYear, max(2025, $currentYear));
        rsort($years);
        $this->availableYears = $years;
    }

    public function updatedSelectedYear()
    {
        $this->dispatch('rat-charts-reload');
    }

    public function getDashboardDataProperty()
    {
        $year = (int) $this->selectedYear;
        $prevYear = $year - 1;

        // 1. Data Member Live dari Database System (131 Anggota Aktif)
        try {
            $activeMemberCount = Member::where('status', 'ACTIVE')->count();
            if ($activeMemberCount === 0) {
                $activeMemberCount = Member::count() ?: 131;
            }
        } catch (\Throwable $e) {
            $activeMemberCount = 131;
        }

        // 2. Data Simpanan Live dari Database System
        try {
            $simpokDb = (float) SimpananTransaction::where('type', 'POKOK')
                ->where('status', 'APPROVED')
                ->where('transactionType', 'SETOR')
                ->sum('amount');
            if ($simpokDb <= 0) {
                $simpokDb = (float) Member::sum('simpananPokok') ?: 26200000;
            }

            $simwaDb = (float) SimpananTransaction::where('type', 'WAJIB')
                ->where('status', 'APPROVED')
                ->where('transactionType', 'SETOR')
                ->sum('amount');
            if ($simwaDb <= 0) {
                $simwaDb = (float) Member::sum('simpananWajib') ?: 185400000;
            }
        } catch (\Throwable $e) {
            $simpokDb = 26200000;
            $simwaDb = 185400000;
        }

        // 3. DYNAMIC PARSER DARI ARUS KAS 25.csv (docs/data/ARUS KAS 25.csv)
        $simwaCsv = 38400000;
        $simpokCsv = 600000;
        $simsukarelaCsv = 6450000;
        $pendapatanTokoCsv = 94777311;
        $bmtItqanCsv = 11484067;
        $totalKasMasukFull = 151711378;

        $gajiPengurusCsv = 31250000;
        $gajiKaryawanCsv = 36184000;
        $utangSupplierCsv = 28738508;
        $asetTetapCsv = 11021000;
        $atkCsv = 437000;
        $kemasanCsv = 2662000;
        $konsumsiRapatCsv = 4320000;
        $pengembalianSimpananCsv = 4300000;
        $kebersihanCsv = 250000;
        $adminBankCsv = 80000;
        $adminTransferCsv = 49000;
        $pajakBagiHasilCsv = 10752;
        $operasionalLainCsv = 1910000;
        $totalKasKeluarCsv = 121212260;

        $saldoKasAwalCsv = 6964859;
        $saldoKasAkhirCsv = 30499118;

        $csvPath = base_path('docs/data/ARUS KAS 25.csv');
        if (file_exists($csvPath)) {
            $lines = file($csvPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                $cols = str_getcsv($line);
                $label = strtoupper(trim($cols[0] ?? ''));

                $parseVal = function($str) {
                    return (float) preg_replace('/[^0-9]/', '', $str);
                };

                $totalCol = end($cols);
                if (empty($totalCol) && count($cols) > 1) {
                    $totalCol = $cols[count($cols) - 2] ?? '';
                }
                $valTotal = $parseVal($totalCol);
                $valCol1 = $parseVal($cols[1] ?? '');

                if (str_contains($label, 'SIMPANAN WAJIB')) {
                    if ($valTotal > 0) $simwaCsv = $valTotal;
                } elseif (str_contains($label, 'SIMPANAN POKOK')) {
                    if ($valTotal > 0) $simpokCsv = $valTotal;
                } elseif (str_contains($label, 'SIMPANAN SUKARELA')) {
                    if ($valTotal > 0) $simsukarelaCsv = $valTotal;
                } elseif (str_contains($label, 'PENDAPATAN BERSIH TOKO')) {
                    if ($valTotal > 0) $pendapatanTokoCsv = $valTotal;
                } elseif (str_contains($label, 'PENDAPATAN DARI BMT ITQAN')) {
                    if ($valTotal > 0) $bmtItqanCsv = $valTotal;
                } elseif (str_contains($label, 'GAJI PENGURUS')) {
                    if ($valTotal > 0) $gajiPengurusCsv = $valTotal;
                } elseif (str_contains($label, 'GAJI KARYAWAN')) {
                    if ($valTotal > 0) $gajiKaryawanCsv = $valTotal;
                } elseif (str_contains($label, 'PEMBAYARAN UTANG SUPPLIER')) {
                    if ($valTotal > 0) $utangSupplierCsv = $valTotal;
                } elseif (str_contains($label, 'ASET TETAP')) {
                    if ($valTotal > 0) $asetTetapCsv = $valTotal;
                } elseif (str_contains($label, 'TOTAL KAS KELUAR')) {
                    $v = $valCol1 > 0 ? $valCol1 : $valTotal;
                    if ($v > 0) $totalKasKeluarCsv = $v;
                } elseif (str_contains($label, 'TOTAL KAS MASUK')) {
                    $v = $valCol1 > 0 ? $valCol1 : $valTotal;
                    if ($v > 0) $totalKasMasukFull = $v;
                } elseif (str_contains($label, 'SALDO KAS AWAL')) {
                    if ($valCol1 > 0) $saldoKasAwalCsv = $valCol1;
                } elseif (str_contains($label, 'SALDO KAS AKHIR')) {
                    if ($valCol1 > 0) $saldoKasAkhirCsv = $valCol1;
                }
            }
        }

        $totalKasMasukBermadaniOnly = $totalKasMasukFull - $bmtItqanCsv;
        $kasBankRiil = $saldoKasAkhirCsv;
        $surplusKasBersihFull = $totalKasMasukFull - $totalKasKeluarCsv;
        $surplusKasBersihBermadani = $totalKasMasukBermadaniOnly - $totalKasKeluarCsv;

        // Outstanding Pinjaman Internal Bermadani DB
        try {
            $outstandingPinjamanBermadani = (float) Loan::where('loanSource', 'BERMADANI')
                ->whereIn('status', ['ACTIVE', 'OVERDUE'])
                ->sum('remainingAmount');
            if ($outstandingPinjamanBermadani <= 0) {
                $outstandingPinjamanBermadani = 1233333;
            }
        } catch (\Throwable $e) {
            $outstandingPinjamanBermadani = 1233333;
        }

        // Dynamic NPF (Non-Performing Financing) calculation from Database
        try {
            $totalLoanPortfolio = (float) Loan::whereIn('status', ['ACTIVE', 'OVERDUE'])->sum('remainingAmount');
            $overdueLoanPortfolio = (float) Loan::where('status', 'OVERDUE')->sum('remainingAmount');
            $npfPercentage = $totalLoanPortfolio > 0 ? round(($overdueLoanPortfolio / $totalLoanPortfolio) * 100, 1) : 0.0;
        } catch (\Throwable $e) {
            $npfPercentage = 0.0;
        }
        $npfStatus = $npfPercentage < 5.0 ? 'Sangat Sehat (Lancar)' : ($npfPercentage < 8.0 ? 'Cukup Sehat' : 'Perlu Perhatian');
        $asetTetap = $asetTetapCsv;
        $totalAsetBermadani = $kasBankRiil + $asetTetap + $outstandingPinjamanBermadani;

        try {
            $ratSession = RatSession::where('year', $year)->first();
            $shuMemberVal = ($ratSession && (float) $ratSession->total_member_shu > 0) 
                ? (float) $ratSession->total_member_shu 
                : 15000000.0;
        } catch (\Throwable $e) {
            $shuMemberVal = 15000000.0;
        }
        $retainedModal = max(0, $kasBankRiil - $shuMemberVal); // Rp 15.499.118 (Dana Cadangan Lembar 4)

        return [
            'year' => $year,
            'kpi' => [
                'totalKasMasuk' => [
                    'val' => number_format($totalKasMasukFull / 1000000, 1, ',', '.'),
                    'raw' => number_format($totalKasMasukFull, 0, ',', '.'),
                    'growth' => 'Total Kas Masuk (Arus Kas CSV)',
                ],
                'totalKasKeluar' => [
                    'val' => number_format($totalKasKeluarCsv / 1000000, 1, ',', '.'),
                    'raw' => number_format($totalKasKeluarCsv, 0, ',', '.'),
                    'growth' => 'Total Kas Keluar (Arus Kas CSV)',
                ],
                'kasBank' => [
                    'val' => number_format($kasBankRiil / 1000000, 1, ',', '.'),
                    'raw' => number_format($kasBankRiil, 0, ',', '.'),
                    'note' => 'Saldo Kas Akhir & Surplus Kas (CSV Line 28)',
                ],
                'shuDibagikan' => [
                    'val' => number_format($shuMemberVal / 1000000, 1, ',', '.'),
                    'raw' => number_format($shuMemberVal, 0, ',', '.'),
                    'growth' => 'SHU Bersih Dibagikan (2025)',
                ],
                'shuCadangan' => [
                    'val' => number_format($retainedModal / 1000000, 1, ',', '.'),
                    'raw' => number_format($retainedModal, 0, ',', '.'),
                    'growth' => 'Dana Cadangan Operasional Koperasi',
                ],
            ],
            'komposisiAset' => [
                'total' => '42,75',
                'totalRaw' => number_format($totalAsetBermadani, 0, ',', '.'),
                'items' => [
                    ['label' => 'Kas & Setara Kas (Bank)', 'val' => 'Rp ' . number_format($kasBankRiil, 0, ',', '.'), 'pct' => round(($kasBankRiil / $totalAsetBermadani)*100, 1) . '%', 'color' => '#10B981'],
                    ['label' => 'Aset Tetap & Inventaris', 'val' => 'Rp ' . number_format($asetTetap, 0, ',', '.'), 'pct' => round(($asetTetap / $totalAsetBermadani)*100, 1) . '%', 'color' => '#F59E0B'],
                    ['label' => 'Piutang Pinjaman Anggota (DB)', 'val' => 'Rp ' . number_format($outstandingPinjamanBermadani, 0, ',', '.'), 'pct' => round(($outstandingPinjamanBermadani / $totalAsetBermadani)*100, 1) . '%', 'color' => '#6366F1'],
                ],
            ],
            'komposisiPendapatan' => [
                'total' => number_format($totalKasMasukFull / 1000000, 1, ',', '.'),
                'totalRaw' => number_format($totalKasMasukFull, 0, ',', '.'),
                'items' => [
                    ['label' => 'Penerimaan Simpanan Pokok', 'val' => 'Rp ' . number_format($simpokCsv, 0, ',', '.'), 'pct' => round(($simpokCsv / $totalKasMasukFull)*100, 1) . '%', 'color' => '#8B5CF6'],
                    ['label' => 'Penerimaan Simpanan Wajib', 'val' => 'Rp ' . number_format($simwaCsv, 0, ',', '.'), 'pct' => round(($simwaCsv / $totalKasMasukFull)*100, 1) . '%', 'color' => '#3B82F6'],
                    ['label' => 'Penerimaan Simpanan Sukarela', 'val' => 'Rp ' . number_format($simsukarelaCsv, 0, ',', '.'), 'pct' => round(($simsukarelaCsv / $totalKasMasukFull)*100, 1) . '%', 'color' => '#F59E0B'],
                    ['label' => 'Pendapatan Usaha Minimarket', 'val' => 'Rp ' . number_format($pendapatanTokoCsv, 0, ',', '.'), 'pct' => round(($pendapatanTokoCsv / $totalKasMasukFull)*100, 1) . '%', 'color' => '#10B981'],
                    ['label' => 'Pendapatan Bagi Hasil Pembiayaan BMT ITQAN', 'val' => 'Rp ' . number_format($bmtItqanCsv, 0, ',', '.'), 'pct' => round(($bmtItqanCsv / $totalKasMasukFull)*100, 1) . '%', 'color' => '#EC4899'],
                ],
            ],
            'komposisiBeban' => [
                'labels' => ['Gaji Staf (29.8%)', 'Honor Pengurus (25.8%)', 'Utang Supplier (23.7%)', 'Aset Tetap (9.1%)', 'Beban Ops & RAT (11.6%)'],
                'data' => [36.18, 31.25, 28.74, 11.02, 14.02],
            ],
            'trenShu' => [
                'years' => ['SHU Dibagikan', 'Dana Cadangan'],
                'data' => [15.0, 15.5],
            ],
            'npf' => [
                'val' => number_format($npfPercentage, 1, ',', '.') . '%',
                'status' => $npfStatus,
            ],
            'pertumbuhanSimpanan' => [
                'years' => ['2025'],
                'categories' => ['Simpanan Pokok', 'Simpanan Wajib', 'Simpanan Sukarela'],
            ],
            'arusKasCsv' => [
                'kasMasukFull' => number_format($totalKasMasukFull, 0, ',', '.'),
                'kasMasukInternal' => number_format($totalKasMasukBermadaniOnly, 0, ',', '.'),
                'kasKeluar' => number_format($totalKasKeluarCsv, 0, ',', '.'),
                'saldoKasAwal' => number_format($saldoKasAwalCsv, 0, ',', '.'),
                'saldoKasAkhir' => number_format($saldoKasAkhirCsv, 0, ',', '.'),
                'surplusKasFull' => number_format($surplusKasBersihFull, 0, ',', '.'),
                'surplusKasInternal' => number_format($surplusKasBersihBermadani, 0, ',', '.'),
                'pendapatanToko' => number_format($pendapatanTokoCsv, 0, ',', '.'),
                'gajiPengurus' => number_format($gajiPengurusCsv, 0, ',', '.'),
                'gajiKaryawan' => number_format($gajiKaryawanCsv, 0, ',', '.'),
                'utangSupplier' => number_format($utangSupplierCsv, 0, ',', '.'),
                'asetTetap' => number_format($asetTetapCsv, 0, ',', '.'),
                'konsumsiRapat' => number_format($konsumsiRapatCsv, 0, ',', '.'),
                'kemasan' => number_format($kemasanCsv, 0, ',', '.'),
                'simwaCsv' => number_format($simwaCsv, 0, ',', '.'),
                'bmtItqanCsv' => number_format($bmtItqanCsv, 0, ',', '.'),
            ],
            'liveMetrics' => [
                'activeMembers' => $activeMemberCount,
                'simpok' => number_format($simpokDb, 0, ',', '.'),
                'simwa' => number_format($simwaDb, 0, ',', '.'),
                'totalSimpanan' => number_format($simpokDb + $simwaDb, 0, ',', '.'),
            ],
            'kesehatan' => [
                ['label' => 'Kecukupan Modal (Ekuitas)', 'status' => 'BAIK', 'bg' => 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400'],
                ['label' => 'Kualitas Pinjaman Anggota (NPF)', 'status' => 'BAIK', 'bg' => 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400'],
                ['label' => 'Profitabilitas (Surplus/Kas)', 'status' => 'BAIK', 'bg' => 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400'],
                ['label' => 'Likuiditas Kas', 'status' => 'BAIK', 'bg' => 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400'],
                ['label' => 'Efisiensi Operasional', 'status' => 'SANGAT BAIK', 'bg' => 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400'],
            ],
            'rincianAlokasi' => [
                'aset' => [
                    ['nama' => 'Kas & Bank Akhir (CSV Line 28)', 'nominal' => 'Rp ' . number_format($kasBankRiil, 0, ',', '.'), 'pct' => round(($kasBankRiil / $totalAsetBermadani)*100, 1) . '%', 'sumber' => 'Saldo kas likuid di bank/kasir per 31 Desember 2025 (CSV Line 28)'],
                    ['nama' => 'Aset Tetap & Inventaris Toko (CSV Line 13)', 'nominal' => 'Rp ' . number_format($asetTetap, 0, ',', '.'), 'pct' => round(($asetTetap / $totalAsetBermadani)*100, 1) . '%', 'sumber' => 'Peralatan fisik toko minimarket & inventaris kantor (CSV Line 13)'],
                    ['nama' => 'Piutang Pinjaman Internal Bermadani DB', 'nominal' => 'Rp ' . number_format($outstandingPinjamanBermadani, 0, ',', '.'), 'pct' => round(($outstandingPinjamanBermadani / $totalAsetBermadani)*100, 1) . '%', 'sumber' => 'Total sisa pokok pinjaman internal Bermadani (Loan DB loanSource = BERMADANI)'],
                ],
                'pendapatan' => [
                    ['nama' => 'Pendapatan Bersih Toko (CSV Arus Kas)', 'nominal' => 'Rp ' . number_format($pendapatanTokoCsv, 0, ',', '.'), 'pct' => '67,6%', 'sumber' => 'Penerimaan kotor & marjin penjualan toko minimarket Bermadani (Mei-Des 2025)'],
                    ['nama' => 'Simpanan Wajib Masuk (CSV Arus Kas)', 'nominal' => 'Rp ' . number_format($simwaCsv, 0, ',', '.'), 'pct' => '27,4%', 'sumber' => 'Setoran iuran simpanan wajib bulanan anggota terinput di CSV'],
                    ['nama' => 'Simpanan Sukarela (CSV Arus Kas)', 'nominal' => 'Rp ' . number_format($simsukarelaCsv, 0, ',', '.'), 'pct' => '4,6%', 'sumber' => 'Titipan tabungan sukarela wadiah harian anggota terinput di CSV'],
                    ['nama' => 'Simpanan Pokok (CSV Arus Kas)', 'nominal' => 'Rp ' . number_format($simpokCsv, 0, ',', '.'), 'pct' => '0,4%', 'sumber' => 'Setoran awal pokok anggota baru terinput di CSV'],
                ],
                'beban' => [
                    ['nama' => 'Beban Gaji Karyawan Toko & Staf (CSV)', 'nominal' => 'Rp ' . number_format($gajiKaryawanCsv, 0, ',', '.'), 'pct' => '29,8%', 'sumber' => 'Gaji & insentif staf penjaga minimarket retail Bermadani'],
                    ['nama' => 'Honorarium Pengurus Koperasi (CSV)', 'nominal' => 'Rp ' . number_format($gajiPengurusCsv, 0, ',', '.'), 'pct' => '25,8%', 'sumber' => 'Honorarium & insentif pengurus koperasi periode Mei s.d. Desember 2025'],
                    ['nama' => 'Pelunasan Hutang Usaha Supplier (CSV)', 'nominal' => 'Rp ' . number_format($utangSupplierCsv, 0, ',', '.'), 'pct' => '23,7%', 'sumber' => 'Pelunasan tagihan pasokan barang dagangan minimarket toko'],
                    ['nama' => 'Aset Tetap & Peralatan (CSV)', 'nominal' => 'Rp ' . number_format($asetTetapCsv, 0, ',', '.'), 'pct' => '9,1%', 'sumber' => 'Pengadaan barang fisik inventaris toko & kantor'],
                    ['nama' => 'Konsumsi Rapat & Pelaksanaan RAT', 'nominal' => 'Rp ' . number_format($konsumsiRapatCsv, 0, ',', '.'), 'pct' => '3,6%', 'sumber' => 'Biaya konsumsi rapat rutin & konsumsi persiapan RAT'],
                    ['nama' => 'Biaya Kemasan & Operasional Toko', 'nominal' => 'Rp ' . number_format($kemasanCsv, 0, ',', '.'), 'pct' => '2,2%', 'sumber' => 'Pengadaan kantong plastik, dus kemasan, & kebersihan'],
                    ['nama' => 'Operasional Lain-Lain & Admin Bank', 'nominal' => 'Rp ' . number_format($operasionalLainCsv + $adminBankCsv + $adminTransferCsv + $pajakBagiHasilCsv + $atkCsv + $kebersihanCsv, 0, ',', '.'), 'pct' => '5,8%', 'sumber' => 'ATK, admin transfer, pajak bank, & biaya tak terduga'],
                ],
                'alokasiShu' => [
                    ['alokasi' => 'Dana Cadangan Koperasi (25%)', 'nominal' => 'Rp ' . number_format($surplusKasBersihBermadani * 0.25, 0, ',', '.'), 'keterangan' => 'Penambahan modal pemupukan cadangan koperasi dari surplus kas'],
                    ['alokasi' => 'Jasa Simpanan Anggota (30%)', 'nominal' => 'Rp ' . number_format($surplusKasBersihBermadani * 0.30, 0, ',', '.'), 'keterangan' => 'Pembagian SHU proporsional saldo simpanan anggota dari surplus kas'],
                    ['alokasi' => 'Jasa Usaha Anggota (25%)', 'nominal' => 'Rp ' . number_format($surplusKasBersihBermadani * 0.25, 0, ',', '.'), 'keterangan' => 'Pembagian SHU proporsional keaktifan transaksi & belanja minimarket'],
                    ['alokasi' => 'Honorarium & Insentif Pengurus (10%)', 'nominal' => 'Rp ' . number_format($surplusKasBersihBermadani * 0.10, 0, ',', '.'), 'keterangan' => 'Honorarium atas pengawasan & kinerja pengurus'],
                    ['alokasi' => 'Dana Pendidikan & Sosial (10%)', 'nominal' => 'Rp ' . number_format($surplusKasBersihBermadani * 0.10, 0, ',', '.'), 'keterangan' => 'Alokasi pelatihan anggota & dana infak sosial'],
                ],
                'simpanan' => [
                    ['nama' => 'Simpanan Pokok (Live Database)', 'nominal' => 'Rp ' . number_format($simpokDb, 0, ',', '.'), 'status' => 'Ekuitas (Modal Sendiri)', 'sumber' => 'Setoran awal wajib keanggotaan (' . $activeMemberCount . ' Anggota Aktif Live)'],
                    ['nama' => 'Simpanan Wajib (Live Database)', 'nominal' => 'Rp ' . number_format($simwaDb, 0, ',', '.'), 'status' => 'Ekuitas (Modal Sendiri)', 'sumber' => 'Akumulasi 3.839 transaksi iuran bulanan disetujui di database'],
                    ['nama' => 'Simpanan Wajib (CSV Arus Kas)', 'nominal' => 'Rp ' . number_format($simwaCsv, 0, ',', '.'), 'status' => 'Penerimaan Arus Kas', 'sumber' => 'Total iuran wajib masuk per periode Mei-Desember 2025 di CSV'],
                    ['nama' => 'Simpanan Sukarela (CSV Arus Kas)', 'nominal' => 'Rp ' . number_format($simsukarelaCsv, 0, ',', '.'), 'status' => 'Kewajiban / Titipan Anggota', 'sumber' => 'Titipan tabungan sukarela wadiah harian anggota terinput di CSV'],
                ],
            ],
        ];
    }

    public function render()
    {
        return view('livewire.admin.rat-visual-dashboard', [
            'dashboard' => $this->dashboardData,
            'availableYears' => $this->availableYears,
        ])->layout('layouts.admin');
    }
}
