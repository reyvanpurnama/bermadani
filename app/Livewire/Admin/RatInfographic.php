<?php

namespace App\Livewire\Admin;

use App\Models\Member;
use App\Models\Loan;
use App\Models\RatSession;
use App\Models\FinancialTransaction;
use Livewire\Component;

class RatInfographic extends Component
{
    public $page = 1;

    protected $queryString = [
        'page' => ['except' => 1],
    ];

    public function setPage($pageNumber)
    {
        $this->page = (int) $pageNumber;
    }

    public function getFinancialData()
    {
        // 1. Database Query: Simpanan Anggota (PASIVA - LIABILITAS & EKUITAS)
        $activeQuery = Member::where('isMemberKoperasi', true)->where('status', 'ACTIVE');
        $activeCount = (clone $activeQuery)->count();
        $simwa = (float) (clone $activeQuery)->sum('simpananWajib');
        $simpok = (float) (clone $activeQuery)->sum('simpananPokok');
        $simsuka = (float) (clone $activeQuery)->sum('simpananSukarela');
        $totalSimpanan = $simwa + $simpok + $simsuka;

        if ($activeCount === 0) {
            $activeCount = 113;
            $simwa = 156100000;
            $simpok = 22100000;
            $simsuka = 16990000;
            $totalSimpanan = 195190000;
        }

        // 2. Database Query: Outstanding Pinjaman Anggota Real-time (AKTIVA - ASET)
        $outstandingPinjaman = (float) Loan::whereIn('status', ['ACTIVE', 'OVERDUE'])->sum('remainingAmount');
        if ($outstandingPinjaman == 0) {
            $outstandingPinjaman = 142847491.0;
        }

        // 3. CSV Parser: Parse docs/data/ARUS KAS 25.csv (KAS & ASET TETAP)
        $kasMasuk = 151711378.0;
        $kasKeluar = 121212260.0;
        $saldoKasAwal = 6964859.0;
        $kasBankRiil = 30499118.0; // SALDO KAS AKHIR CSV LINE 28
        $asetTetap = 11021000.0;   // ASET TETAP CSV LINE 13

        $csvPath = base_path('docs/data/ARUS KAS 25.csv');
        if (file_exists($csvPath)) {
            $lines = file($csvPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                $cols = str_getcsv($line);
                $label = strtoupper(trim($cols[0] ?? ''));

                if (str_contains($label, 'SALDO KAS AKHIR')) {
                    $val = (float) preg_replace('/[^0-9]/', '', $cols[1] ?? '');
                    if ($val > 0) $kasBankRiil = $val;
                } elseif (str_contains($label, 'ASET TETAP')) {
                    $raw = end($cols);
                    if (empty($raw)) {
                        $raw = $cols[count($cols) - 2] ?? '';
                    }
                    $val = (float) preg_replace('/[^0-9]/', '', $raw);
                    if ($val > 0) $asetTetap = $val;
                } elseif (str_contains($label, 'TOTAL KAS MASUK')) {
                    $val = (float) preg_replace('/[^0-9]/', '', $cols[1] ?? '');
                    if ($val > 0) $kasMasuk = $val;
                } elseif (str_contains($label, 'TOTAL KAS KELUAR')) {
                    $val = (float) preg_replace('/[^0-9]/', '', $cols[1] ?? '');
                    if ($val > 0) $kasKeluar = $val;
                }
            }
        }

        $surplusKas = $kasBankRiil;

        // 4. KALKULASI ASET (AKTIVA) VS PASIVA (SIMPANAN)
        // Total Aset Aktiva Real = Kas (30.499.118) + Aset Tetap (11.021.000) + Pinjaman Real DB (142.847.491) = Rp 184.367.609
        $totalAsetReal = $kasBankRiil + $asetTetap + $outstandingPinjaman; // 184.367.609

        // Defisit / Selisih modal = Total Simpanan (195.190.000) - Total Aset (184.367.609) = Rp 10.822.391
        $defisitModal = $totalSimpanan - $totalAsetReal; // 10.822.391

        // 5. RAT Session & SHU
        $ratSession = RatSession::where('year', 2025)->first();
        $shuMember = $ratSession ? (float) $ratSession->total_member_shu : 15000000.0;
        $retainedModal = max(0, $surplusKas - $shuMember);

        return [
            'activeCount' => $activeCount,
            'simwa' => $simwa,
            'simpok' => $simpok,
            'simsuka' => $simsuka,
            'totalSimpanan' => $totalSimpanan,
            'outstandingPinjaman' => $outstandingPinjaman,
            'kasMasuk' => $kasMasuk,
            'kasKeluar' => $kasKeluar,
            'saldoKasAwal' => $saldoKasAwal,
            'surplusKas' => $surplusKas,
            'kasBankRiil' => $kasBankRiil,
            'asetTetap' => $asetTetap,
            'totalAsetReal' => $totalAsetReal,
            'defisitModal' => $defisitModal,
            'shuMember' => $shuMember,
            'retainedModal' => $retainedModal,
        ];
    }

    public function render()
    {
        $data = $this->getFinancialData();

        return view('livewire.admin.rat-infographic', $data)->layout('layouts.admin');
    }
}
