<?php

namespace App\Livewire\Admin\Reports;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\CalkEntry;
use App\Domains\Accounting\Services\FinancialStatementService;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.admin')]
class CalkBuilder extends Component
{
    public int $fiscalYear;
    public int $activeStep = 1;
    public bool $isSaved = false;

    // Form inputs for CALK sections
    public array $content = [
        'bab1_profil' => '',
        'bab1_kegiatan' => '',
        'bab2_kebijakan' => '',
        'bab3_kas_tunai' => 0,
        'bab3_bank' => 0,
        'bab4_bermadani' => 0,
        'bab4_bmt' => 0,
        'bab6_opini_dps' => '',
    ];

    public function mount()
    {
        $this->fiscalYear = (int) date('Y') - 1;
        $this->loadCalkData();
    }

    public function updatedFiscalYear()
    {
        $this->loadCalkData();
    }

    public function loadCalkData()
    {
        $service = app(FinancialStatementService::class);
        $neraca = $service->getBalanceSheet($this->fiscalYear);

        // Try loading from database
        $entry = CalkEntry::where('fiscal_year', $this->fiscalYear)
            ->where('section_key', 'full_calk')
            ->first();

        if ($entry && !empty($entry->content)) {
            $this->content = array_merge($this->content, $entry->content);
        } else {
            // Auto-fill defaults from FinancialStatementService
            $kasTotal = $neraca['kas'] ?? 0;
            $piutangTotal = $neraca['piutang_pembiayaan'] ?? 0;

            $this->content['bab1_profil'] = 'KSPPS Berkah Madani didirikan sebagai Koperasi Simpan Pinjam dan Pembiayaan Syariah yang berkedudukan di Bandung. Koperasi ini beroperasi berdasar pada prinsip-prinsip syariah Islam dan peraturan perundang-undangan perkoperasian yang berlaku di Republik Indonesia.';
            $this->content['bab1_kegiatan'] = 'Kegiatan utama KSPPS Berkah Madani meliputi penghimpunan dana simpanan anggota (Wadiah dan Mudharabah), penyaluran pembiayaan syariah (Murabahah, Mudharabah, Musyarakah, dan Qardh), serta pengelolaan unit toko minimarket retail untuk kesejahteraan anggota.';
            $this->content['bab2_kebijakan'] = 'Laporan keuangan disusun berdasarkan Standar Akuntansi Keuangan Entitas Privat (SAK EP) dan PSAK Syariah (PSAK 101 tentang Penyajian Laporan Keuangan Syariah, PSAK 102 Akuntansi Murabahah, PSAK 105 Akuntansi Mudharabah).';
            $this->content['bab3_kas_tunai'] = round($kasTotal * 0.15);
            $this->content['bab3_bank'] = round($kasTotal * 0.85);
            $this->content['bab4_bermadani'] = round($piutangTotal * 0.70);
            $this->content['bab4_bmt'] = round($piutangTotal * 0.30);
            $this->content['bab6_opini_dps'] = 'Berdasarkan pengawasan Dewan Pengawas Syariah (DPS) KSPPS Berkah Madani, seluruh kegiatan operasional pembiayaan, penghimpunan dana, dan investasi yang dilakukan selama tahun buku ' . $this->fiscalYear . ' telah sesuai dengan prinsip-prinsip Syariah Islam dan Fatwa Dewan Syariah Nasional (DSN-MUI).';
        }
    }

    public function setStep(int $step)
    {
        if ($step >= 1 && $step <= 6) {
            $this->activeStep = $step;
        }
    }

    public function save()
    {
        CalkEntry::updateOrCreate(
            [
                'fiscal_year' => $this->fiscalYear,
                'section_key' => 'full_calk',
            ],
            [
                'content' => $this->content,
                'updated_by' => Auth::id(),
            ]
        );

        $this->isSaved = true;
        session()->flash('message', 'Dokumen CALK Tahun ' . $this->fiscalYear . ' berhasil disimpan!');
    }

    public function render()
    {
        $service = app(FinancialStatementService::class);
        $neraca = $service->getBalanceSheet($this->fiscalYear);

        return view('livewire.admin.reports.calk-builder', [
            'neraca' => $neraca,
        ]);
    }
}
