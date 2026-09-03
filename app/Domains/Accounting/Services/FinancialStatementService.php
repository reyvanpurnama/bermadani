<?php

namespace App\Domains\Accounting\Services;

use App\Models\Member;
use App\Models\Loan;
use App\Models\LoanPayment;
use App\Models\FinancialTransaction;
use App\Domains\Koperasi\Models\SimpananTransaction;
use App\Domains\Accounting\Models\FixedAsset;
use App\Domains\Minimarket\Models\Transaction as PosTransaction;
use App\Models\Product;
use App\Domains\Accounting\Models\BankTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FinancialStatementService
{
    public function getBalanceSheet(int $year): array
    {
        $endDate = Carbon::createFromDate($year, 12, 31)->endOfDay();
        
        // 1. Aset Lancar
        $bankBalance = BankTransaction::where('transaction_date', '<=', $endDate)
            ->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->value('balance') ?? 0;
            
        $kasSetaraKas = $bankBalance; // + manual cash if any
        
        $loans = Loan::whereIn('status', ['ACTIVE', 'OVERDUE'])
            ->where('created_at', '<=', $endDate)
            ->get();
            
        $piutangPembiayaan = $loans->sum('remainingAmount');
        
        $overdueLoans = $loans->where('status', 'OVERDUE')->sum('remainingAmount');
        $cadanganKerugianPiutang = $overdueLoans * 0.05 * -1; // -5% of OVERDUE
        
        $products = Product::all();
        $persediaan = $products->sum(function($product) {
            return $product->stock * $product->buyPrice;
        });
        
        $piutangLain = 0; // Placeholder

        $asetLancarTotal = $kasSetaraKas + $piutangPembiayaan + $cadanganKerugianPiutang + $piutangLain + $persediaan;

        // 2. Aset Tidak Lancar
        $fixedAssets = FixedAsset::where('acquisition_date', '<=', $endDate)
            ->where(function($query) use ($endDate) {
                $query->whereNull('disposed_at')
                      ->orWhere('disposed_at', '>', $endDate);
            })->get();
            
        $asetTetap = $fixedAssets->sum('acquisition_cost');
        $akumulasiPenyusutan = $fixedAssets->sum(function($asset) use ($endDate) {
            // Need custom calculation based on endDate if we want exact past snapshot,
            // but for simplicity here we rely on current model method or recalculate.
            $acquisitionDate = Carbon::parse($asset->acquisition_date);
            $monthsElapsed = $acquisitionDate->diffInMonths($endDate);
            if ($monthsElapsed > $asset->useful_life_months) {
                $monthsElapsed = $asset->useful_life_months;
            }
            $depreciation = $asset->monthly_depreciation * $monthsElapsed;
            $maxDepreciation = $asset->acquisition_cost - $asset->salvage_value;
            return min($depreciation, $maxDepreciation);
        }) * -1;
        
        $asetLainnya = 0; // Placeholder
        
        $asetTidakLancarTotal = $asetTetap + $akumulasiPenyusutan + $asetLainnya;
        
        $totalAset = $asetLancarTotal + $asetTidakLancarTotal;

        // 3. Liabilitas
        $simpananAnggota = Member::sum('simpananSukarela'); // Assuming wadiah is liability
        $utangLain = 0;
        
        $totalLiabilitas = $simpananAnggota + $utangLain;

        // 4. Ekuitas
        $simpananPokok = Member::sum('simpananPokok');
        $simpananWajib = Member::sum('simpananWajib');
        $cadangan = 0; // from rat_manual_entries or calk_entries placeholder
        
        // Income statement logic to get SHU berjalan
        $incomeStatement = $this->getIncomeStatement($year);
        $shuTahunBerjalan = $incomeStatement['shu'];
        
        $totalEkuitas = $simpananPokok + $simpananWajib + $cadangan + $shuTahunBerjalan;

        $totalLiabilitasEkuitas = $totalLiabilitas + $totalEkuitas;

        return [
            'as_of_date' => '31 Desember ' . $year,
            'aset' => [
                'aset_lancar' => [
                    'kas_dan_setara_kas' => $kasSetaraKas,
                    'piutang_pembiayaan' => $piutangPembiayaan,
                    'cadangan_kerugian_piutang' => $cadanganKerugianPiutang,
                    'piutang_lain' => $piutangLain,
                    'persediaan' => $persediaan,
                ],
                'aset_tidak_lancar' => [
                    'aset_tetap' => $asetTetap,
                    'akumulasi_penyusutan' => $akumulasiPenyusutan,
                    'aset_lainnya' => $asetLainnya,
                ],
                'total_aset' => $totalAset,
            ],
            'liabilitas' => [
                'simpanan_anggota' => $simpananAnggota,
                'utang_lain' => $utangLain,
                'total_liabilitas' => $totalLiabilitas,
            ],
            'ekuitas' => [
                'simpanan_pokok' => $simpananPokok,
                'simpanan_wajib' => $simpananWajib,
                'cadangan' => $cadangan,
                'shu_tahun_berjalan' => $shuTahunBerjalan,
                'total_ekuitas' => $totalEkuitas,
            ],
            'total_liabilitas_ekuitas' => $totalLiabilitasEkuitas,
            'is_balanced' => round($totalAset, 2) === round($totalLiabilitasEkuitas, 2),
        ];
    }

    public function getIncomeStatement(int $year): array
    {
        $startDate = Carbon::createFromDate($year, 1, 1)->startOfDay();
        $endDate = Carbon::createFromDate($year, 12, 31)->endOfDay();

        // Pendapatan
        // For margins, we'd ideally check loan payments
        $marginPembiayaan = FinancialTransaction::where('type', 'INCOME')
            ->where('transactionDate', '>=', $startDate)
            ->where('transactionDate', '<=', $endDate)
            ->where('category', 'Margin Pembiayaan')
            ->sum('amount');
            
        $pendapatanAdministrasi = FinancialTransaction::where('type', 'INCOME')
            ->where('transactionDate', '>=', $startDate)
            ->where('transactionDate', '<=', $endDate)
            ->where('category', 'Pendapatan Administrasi')
            ->sum('amount');
            
        $pendapatanLain = FinancialTransaction::where('type', 'INCOME')
            ->where('transactionDate', '>=', $startDate)
            ->where('transactionDate', '<=', $endDate)
            ->whereNotIn('category', ['Margin Pembiayaan', 'Pendapatan Administrasi'])
            ->sum('amount');
            
        $totalPendapatan = $marginPembiayaan + $pendapatanAdministrasi + $pendapatanLain;

        // Beban Operasional
        $bebanGaji = FinancialTransaction::where('type', 'EXPENSE')
            ->where('transactionDate', '>=', $startDate)
            ->where('transactionDate', '<=', $endDate)
            ->where('category', 'Gaji Karyawan')
            ->sum('amount');
            
        $bebanAtk = FinancialTransaction::where('type', 'EXPENSE')
            ->where('transactionDate', '>=', $startDate)
            ->where('transactionDate', '<=', $endDate)
            ->where('category', 'Beli Perlengkapan (ATK/Plastik)')
            ->sum('amount');
            
        $bebanListrikAir = FinancialTransaction::where('type', 'EXPENSE')
            ->where('transactionDate', '>=', $startDate)
            ->where('transactionDate', '<=', $endDate)
            ->where('category', 'Biaya Listrik & Air')
            ->sum('amount');
            
        $bebanPenyusutan = FixedAsset::where('acquisition_date', '<=', $endDate)
            ->where(function($query) use ($endDate) {
                $query->whereNull('disposed_at')
                      ->orWhere('disposed_at', '>', $endDate);
            })->get()->sum(function($asset) {
                return $asset->monthly_depreciation * 12; // Approximation for the year
            });
            
        $bebanOperasionalLain = FinancialTransaction::where('type', 'EXPENSE')
            ->where('transactionDate', '>=', $startDate)
            ->where('transactionDate', '<=', $endDate)
            ->whereNotIn('category', ['Gaji Karyawan', 'Beli Perlengkapan (ATK/Plastik)', 'Biaya Listrik & Air'])
            ->sum('amount');

        $totalBeban = $bebanGaji + $bebanAtk + $bebanListrikAir + $bebanPenyusutan + $bebanOperasionalLain;

        $shu = $totalPendapatan - $totalBeban;

        return [
            'period' => 'Untuk Tahun yang Berakhir 31 Desember ' . $year,
            'pendapatan' => [
                'margin_pembiayaan' => $marginPembiayaan,
                'pendapatan_administrasi' => $pendapatanAdministrasi,
                'pendapatan_lain' => $pendapatanLain,
                'total_pendapatan' => $totalPendapatan,
            ],
            'beban_operasional' => [
                'beban_gaji' => $bebanGaji,
                'beban_atk' => $bebanAtk,
                'beban_listrik_air' => $bebanListrikAir,
                'beban_penyusutan' => $bebanPenyusutan,
                'beban_operasional_lain' => $bebanOperasionalLain,
                'total_beban' => $totalBeban,
            ],
            'shu' => $shu,
        ];
    }

    public function getEquityChanges(int $year): array
    {
        $startDate = Carbon::createFromDate($year, 1, 1)->startOfDay();
        $endDate = Carbon::createFromDate($year, 12, 31)->endOfDay();

        // Calculate member savings transactions
        $simpananPokokIn = SimpananTransaction::where('type', 'POKOK')->where('transactionType', 'SETOR')
            ->whereBetween('created_at', [$startDate, $endDate])->where('status', 'SUCCESS')->sum('amount');
        $simpananPokokOut = SimpananTransaction::where('type', 'POKOK')->where('transactionType', 'TARIK')
            ->whereBetween('created_at', [$startDate, $endDate])->where('status', 'SUCCESS')->sum('amount');
            
        $simpananWajibIn = SimpananTransaction::where('type', 'WAJIB')->where('transactionType', 'SETOR')
            ->whereBetween('created_at', [$startDate, $endDate])->where('status', 'SUCCESS')->sum('amount');
        $simpananWajibOut = SimpananTransaction::where('type', 'WAJIB')->where('transactionType', 'TARIK')
            ->whereBetween('created_at', [$startDate, $endDate])->where('status', 'SUCCESS')->sum('amount');

        $saldoAkhirPokok = Member::sum('simpananPokok');
        $saldoAkhirWajib = Member::sum('simpananWajib');
        
        $saldoAwalPokok = $saldoAkhirPokok - $simpananPokokIn + $simpananPokokOut;
        $saldoAwalWajib = $saldoAkhirWajib - $simpananWajibIn + $simpananWajibOut;

        $incomeStatement = $this->getIncomeStatement($year);
        $shuBerjalan = $incomeStatement['shu'];

        $saldoAwalCadangan = 0; // Placeholder
        $saldoAkhirCadangan = 0; // Placeholder

        return [
            'period' => 'Untuk Tahun yang Berakhir 31 Desember ' . $year,
            'rows' => [
                'saldo_awal' => [
                    'simpanan_pokok' => $saldoAwalPokok,
                    'simpanan_wajib' => $saldoAwalWajib,
                    'cadangan' => $saldoAwalCadangan,
                    'shu_berjalan' => 0, // SHU from previous year becomes retained earnings
                    'total' => $saldoAwalPokok + $saldoAwalWajib + $saldoAwalCadangan
                ],
                'penambahan' => [
                    'simpanan_pokok' => $simpananPokokIn,
                    'simpanan_wajib' => $simpananWajibIn,
                    'cadangan' => 0,
                    'shu_berjalan' => $shuBerjalan,
                    'total' => $simpananPokokIn + $simpananWajibIn + $shuBerjalan
                ],
                'pengurangan' => [
                    'simpanan_pokok' => $simpananPokokOut,
                    'simpanan_wajib' => $simpananWajibOut,
                    'cadangan' => 0,
                    'shu_berjalan' => 0,
                    'total' => $simpananPokokOut + $simpananWajibOut
                ],
                'saldo_akhir' => [
                    'simpanan_pokok' => $saldoAkhirPokok,
                    'simpanan_wajib' => $saldoAkhirWajib,
                    'cadangan' => $saldoAkhirCadangan,
                    'shu_berjalan' => $shuBerjalan,
                    'total' => $saldoAkhirPokok + $saldoAkhirWajib + $saldoAkhirCadangan + $shuBerjalan
                ],
            ]
        ];
    }

    public function getCashFlowStatement(int $year): array
    {
        $startDate = Carbon::createFromDate($year, 1, 1)->startOfDay();
        $endDate = Carbon::createFromDate($year, 12, 31)->endOfDay();

        $incomeStatement = $this->getIncomeStatement($year);

        // Operasi
        $penerimaanAnggota = LoanPayment::whereBetween('paymentDate', [$startDate, $endDate])->sum('amount') 
                           + $incomeStatement['pendapatan']['pendapatan_administrasi'];
                           
        $pembayaranBeban = $incomeStatement['beban_operasional']['total_beban'] - $incomeStatement['beban_operasional']['beban_penyusutan'];
        
        $pembayaranSimpanan = SimpananTransaction::where('transactionType', 'TARIK')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'SUCCESS')
            ->sum('amount');
            
        $kasBersihOperasi = $penerimaanAnggota - $pembayaranBeban - $pembayaranSimpanan;

        // Investasi
        $perolehanAsetTetap = FixedAsset::whereBetween('acquisition_date', [$startDate, $endDate])
            ->sum('acquisition_cost');
            
        $kasBersihInvestasi = -$perolehanAsetTetap;

        // Pendanaan
        $simpananMasuk = SimpananTransaction::where('transactionType', 'SETOR')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'SUCCESS')
            ->sum('amount');
            
        $simpananKeluar = 0; // Already counted in operasi for some, but typically withdrawals are pendanaan. Assuming wadiah is pendanaan. Let's simplify.
        
        $kasBersihPendanaan = $simpananMasuk - $simpananKeluar;
        
        $kenaikanKas = $kasBersihOperasi + $kasBersihInvestasi + $kasBersihPendanaan;
        
        $kasAkhirTahun = BankTransaction::where('transaction_date', '<=', $endDate)
            ->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->value('balance') ?? 0;
            
        $kasAwalTahun = BankTransaction::where('transaction_date', '<', $startDate)
            ->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->value('balance') ?? 0;

        return [
            'period' => 'Untuk Tahun yang Berakhir 31 Desember ' . $year,
            'operasi' => [
                'penerimaan_anggota' => $penerimaanAnggota,
                'pembayaran_beban' => $pembayaranBeban,
                'pembayaran_simpanan' => $pembayaranSimpanan,
                'kas_bersih_operasi' => $kasBersihOperasi,
            ],
            'investasi' => [
                'perolehan_aset_tetap' => $perolehanAsetTetap,
                'kas_bersih_investasi' => $kasBersihInvestasi,
            ],
            'pendanaan' => [
                'simpanan_masuk' => $simpananMasuk,
                'simpanan_keluar' => $simpananKeluar,
                'kas_bersih_pendanaan' => $kasBersihPendanaan,
            ],
            'kenaikan_kas' => $kenaikanKas,
            'kas_awal_tahun' => $kasAwalTahun,
            'kas_akhir_tahun' => $kasAkhirTahun,
        ];
    }

    public function getHealthScorecard(int $year): array
    {
        $balanceSheet = $this->getBalanceSheet($year);
        $incomeStatement = $this->getIncomeStatement($year);
        
        $totalAset = $balanceSheet['aset']['total_aset'];
        $totalEkuitas = $balanceSheet['ekuitas']['total_ekuitas'];
        $totalPiutang = $balanceSheet['aset']['aset_lancar']['piutang_pembiayaan'];
        
        $overdueLoans = Loan::where('status', 'OVERDUE')
            ->where('created_at', '<=', Carbon::createFromDate($year, 12, 31)->endOfDay())
            ->sum('remainingAmount');
            
        $shu = $incomeStatement['shu'];
        $kas = $balanceSheet['aset']['aset_lancar']['kas_dan_setara_kas'];
        $totalBeban = $incomeStatement['beban_operasional']['total_beban'];
        $totalPendapatan = $incomeStatement['pendapatan']['total_pendapatan'];
        
        // Modal / Aset
        $carValue = $totalAset > 0 ? ($totalEkuitas / $totalAset) * 100 : 0;
        $carRating = $carValue >= 20 ? 'BAIK' : ($carValue >= 10 ? 'CUKUP' : 'KURANG');
        
        // NPF
        $npfValue = $totalPiutang > 0 ? ($overdueLoans / $totalPiutang) * 100 : 0;
        $npfRating = $npfValue <= 5 ? 'BAIK' : ($npfValue <= 10 ? 'CUKUP' : 'KURANG');
        
        // ROA
        $roaValue = $totalAset > 0 ? ($shu / $totalAset) * 100 : 0;
        $roaRating = $roaValue >= 5 ? 'BAIK' : ($roaValue >= 2 ? 'CUKUP' : 'KURANG');
        
        // Likuiditas
        $likuiditasValue = $totalAset > 0 ? ($kas / $totalAset) * 100 : 0;
        $likuiditasRating = $likuiditasValue >= 15 ? 'BAIK' : ($likuiditasValue >= 10 ? 'CUKUP' : 'KURANG');
        
        // Efisiensi (BOPO)
        $bopoValue = $totalPendapatan > 0 ? ($totalBeban / $totalPendapatan) * 100 : 0;
        $bopoRating = $bopoValue <= 80 ? 'BAIK' : ($bopoValue <= 90 ? 'CUKUP' : 'KURANG');
        
        return [
            'kecukupan_modal' => ['value' => $carValue, 'rating' => $carRating],
            'kualitas_aset_npf' => ['value' => $npfValue, 'rating' => $npfRating],
            'profitabilitas' => ['value' => $roaValue, 'rating' => $roaRating],
            'likuiditas' => ['value' => $likuiditasValue, 'rating' => $likuiditasRating],
            'efisiensi_operasional' => ['value' => $bopoValue, 'rating' => $bopoRating],
        ];
    }
}
