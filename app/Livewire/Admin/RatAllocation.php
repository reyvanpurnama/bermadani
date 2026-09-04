<?php

namespace App\Livewire\Admin;

use App\Models\MemberShuDistribution;
use App\Models\RatSession;
use App\Services\ShuCalculationService;
use Livewire\Component;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RatAllocation extends Component
{
    use WithPagination;

    public $sessionId;
    public $searchMember = '';
    public $showDetailModal = false;
    public $selectedDistributionId = null;

    protected ShuCalculationService $shuService;

    public function boot(ShuCalculationService $shuService)
    {
        $this->shuService = $shuService;
    }

    public function mount($session = null)
    {
        $ratSession = $session ? RatSession::find($session) : null;
        if (!$ratSession) {
            $ratSession = RatSession::latest('year')->first() ?? RatSession::latest('id')->first();
        }

        if (!$ratSession) {
            return redirect()->route('admin.rat.setup');
        }

        $this->sessionId = $ratSession->id;

        // Auto-run calculation if distributions exist without snapshot columns or if empty
        $needsCalculation = MemberShuDistribution::where('rat_session_id', $ratSession->id)
            ->where(function ($q) {
                $q->where('simpanan_pokok_snapshot', 0)
                  ->where('simpanan_wajib_snapshot', 0);
            })->exists();

        if ($needsCalculation || MemberShuDistribution::where('rat_session_id', $ratSession->id)->count() === 0) {
            if (!$ratSession->isFinalized()) {
                $this->shuService->calculateAndSaveDistributions($ratSession);
            }
        }
    }

    public function updatingSearchMember()
    {
        $this->resetPage();
    }

    public function getSessionProperty()
    {
        return RatSession::find($this->sessionId);
    }

    public function getAllSessionsProperty()
    {
        return RatSession::orderByDesc('year')->get();
    }

    public function selectSession($id)
    {
        $session = RatSession::find($id);
        if ($session) {
            $this->sessionId = $session->id;
            $this->resetPage();
        }
    }

    public function updatedSessionId($id)
    {
        $this->selectSession($id);
    }

    public function getSummaryProperty()
    {
        $session = $this->session;
        if (!$session) return [];
        return $this->shuService->calculateSummary($session);
    }

    // === Detail Modal Methods ===
    public function openDetailModal($distributionId)
    {
        $this->selectedDistributionId = $distributionId;
        $this->showDetailModal = true;
    }

    public function closeDetailModal()
    {
        $this->showDetailModal = false;
        $this->selectedDistributionId = null;
    }

    public function getSelectedDistributionProperty()
    {
        if (!$this->selectedDistributionId) return null;
        return MemberShuDistribution::with(['member', 'ratSession'])->find($this->selectedDistributionId);
    }

    /**
     * Recalculate all SHU distributions based on current session config.
     */
    public function recalculate()
    {
        $session = $this->session;
        if (!$session || $session->isFinalized()) {
            session()->flash('error', 'Tidak dapat menghitung ulang: sesi sudah disahkan.');
            return;
        }

        $count = $this->shuService->calculateAndSaveDistributions($session);
        session()->flash('success', "Perhitungan SHU selesai! {$count} distribusi anggota telah dihitung.");
    }

    /**
     * Finalize session: lock all data and publish SHU to member portal.
     */
    public function finalizeSession()
    {
        $session = $this->session;
        if (!$session) return;

        // Ensure distributions exist
        $distCount = MemberShuDistribution::where('rat_session_id', $session->id)->count();
        if ($distCount === 0) {
            $this->recalculate();
        }

        if ($session->transitionTo(RatSession::STATUS_FINALIZED)) {
            session()->flash('success', 'Sesi RAT berhasil disahkan & SHU dipublikasikan ke portal anggota!');
        } else {
            session()->flash('error', 'Tidak dapat mengesahkan sesi. Status saat ini: ' . $session->status_label);
        }
    }

    /**
     * Reopen session back to editable state.
     */
    public function reopenSession()
    {
        $session = $this->session;
        if (!$session) return;

        if ($session->transitionTo(RatSession::STATUS_MEMBERS_LOCKED)) {
            session()->flash('info', 'Status sesi RAT dikembalikan. Data dapat diedit kembali.');
        }
    }

    /**
     * Export allocation table to CSV/Excel.
     */
    public function exportCsv(): StreamedResponse
    {
        $session = $this->session;
        $year = $session ? $session->year : date('Y');
        $fileName = "Kalkulasi_Alokasi_SHU_RAT_{$year}_" . date('Ymd_His') . ".csv";

        $headers = [
            "Content-type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename={$fileName}",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function () use ($session) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF"); // UTF-8 BOM

            fputcsv($file, [
                'No',
                'No. Anggota',
                'Nama Anggota',
                'Unit Kerja / Institusi',
                'Simpanan Pokok Snapshot (Rp)',
                'Simpanan Wajib Snapshot (Rp)',
                'Total Simpanan Snapshot (Rp)',
                'Total Transaksi Snapshot (Rp)',
                'Jasa Simpanan (Rp)',
                'Jasa Usaha (Rp)',
                'Porsi (%)',
                'Total SHU (Rp)',
                'Status Sesi'
            ]);

            if ($session) {
                $query = MemberShuDistribution::with('member')
                    ->where('rat_session_id', $session->id)
                    ->orderBy('id', 'asc');

                $no = 1;
                foreach ($query->cursor() as $dist) {
                    $m = $dist->member;
                    fputcsv($file, [
                        $no++,
                        $m?->nomorAnggota ?? '-',
                        $m?->name ?? '-',
                        $m?->unitKerja ?? '-',
                        round((float) $dist->simpanan_pokok_snapshot, 0),
                        round((float) $dist->simpanan_wajib_snapshot, 0),
                        round((float) $dist->total_simpanan_amount, 0),
                        round((float) $dist->total_transaksi_amount, 0),
                        round((float) $dist->jasa_simpanan_amount, 0),
                        round((float) $dist->jasa_usaha_amount, 0),
                        number_format((float) $dist->portion_percentage, 4, ',', ''),
                        round((float) $dist->shu_amount, 0),
                        $session->status_label
                    ]);
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function advanceToDisbursement()
    {
        $session = $this->session;
        if (!$session || !$session->isFinalized()) {
            session()->flash('error', 'Sahkan sesi terlebih dahulu sebelum melakukan pencairan.');
            return;
        }

        $session->transitionTo(RatSession::STATUS_DISBURSING);
        return redirect()->route('admin.rat.disbursement', ['session' => $session->id]);
    }

    public function goBack()
    {
        $session = $this->session;
        return redirect()->route('admin.rat.eligibility', ['session' => $session->id ?? 0]);
    }

    public function render()
    {
        $session = $this->session;
        $distributions = collect();

        if ($session) {
            $query = MemberShuDistribution::with('member')
                ->where('rat_session_id', $session->id);

            if ($this->searchMember) {
                $query->whereHas('member', function ($q) {
                    $q->where('name', 'like', '%' . $this->searchMember . '%')
                      ->orWhere('nomorAnggota', 'like', '%' . $this->searchMember . '%')
                      ->orWhere('unitKerja', 'like', '%' . $this->searchMember . '%');
                });
            }

            $distributions = $query->orderByDesc('shu_amount')->paginate(25);
        }

        return view('livewire.admin.rat-allocation', [
            'ratSession' => $session,
            'allSessions' => $this->allSessions,
            'distributions' => $distributions,
            'summary' => $this->summary,
            'selectedDistribution' => $this->selectedDistribution,
            'showDetailModal' => $this->showDetailModal,
        ])->layout('layouts.admin');
    }
}
