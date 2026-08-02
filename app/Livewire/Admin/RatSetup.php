<?php

namespace App\Livewire\Admin;

use App\Models\RatSession;
use App\Models\FinancialTransaction;
use App\Services\ShuCalculationService;
use Livewire\Component;

class RatSetup extends Component
{
    public $selectedSessionId;
    public $year;
    public $eventDate;
    public $title;
    public $totalNetProfit = 0;
    public $totalMemberShu = 0;
    public $memberAllocationPercentage = 100;
    public $notes;

    // 5-pos allocation
    public $cadanganPercentage = 25.00;
    public $jasaSimpananPercentage = 30.00;
    public $jasaUsahaPercentage = 25.00;
    public $pengurusPercentage = 10.00;
    public $danaSosialPercentage = 10.00;

    protected ShuCalculationService $shuService;

    public function boot(ShuCalculationService $shuService)
    {
        $this->shuService = $shuService;
    }

    public function mount()
    {
        $this->year = (int) date('Y');
        $this->eventDate = date('Y-m-d');
        $this->title = 'RAT Koperasi Bermadani Tahun Buku ' . $this->year;

        // Load latest or current year session
        $existing = RatSession::where('year', $this->year)->first();
        if (!$existing) {
            $existing = RatSession::orderByDesc('year')->first();
        }

        if ($existing) {
            $this->loadSession($existing);
        } else {
            $this->fetchFinancialData();
        }
    }

    public function loadSession(RatSession $session)
    {
        $this->selectedSessionId = $session->id;
        $this->year = $session->year;
        $this->eventDate = $session->event_date?->format('Y-m-d') ?? date('Y-m-d');
        $this->title = $session->title;
        $this->totalNetProfit = (float) $session->total_net_profit;
        $this->totalMemberShu = (float) $session->total_member_shu;
        $this->memberAllocationPercentage = (float) $session->member_allocation_percentage;
        $this->notes = $session->notes;

        $this->cadanganPercentage = (float) $session->cadangan_percentage;
        $this->jasaSimpananPercentage = (float) $session->jasa_simpanan_percentage;
        $this->jasaUsahaPercentage = (float) $session->jasa_usaha_percentage;
        $this->pengurusPercentage = (float) $session->pengurus_percentage;
        $this->danaSosialPercentage = (float) $session->dana_sosial_percentage;
    }

    public function loadSessionById($sessionId)
    {
        $session = RatSession::find($sessionId);
        if ($session) {
            $this->loadSession($session);
        }
    }

    public function fetchFinancialData()
    {
        $net = $this->shuService->fetchNetProfitForYear($this->year);
        if ($net > 0) {
            $this->totalNetProfit = $net;
        }
    }

    public function updatedTotalMemberShu($value)
    {
        $val = (float) ($value ?: 0);
        $net = (float) ($this->totalNetProfit ?: 0);
        if ($net > 0) {
            $this->memberAllocationPercentage = round(($val / $net) * 100, 2);
        }
    }

    public function updatedMemberAllocationPercentage($value)
    {
        $val = (float) ($value ?: 0);
        $net = (float) ($this->totalNetProfit ?: 0);
        $this->totalMemberShu = round($net * ($val / 100), 0);
    }

    public function getActiveSessionProperty()
    {
        return $this->selectedSessionId ? RatSession::find($this->selectedSessionId) : null;
    }

    public function getAllocationTotalProperty(): float
    {
        return (float) $this->cadanganPercentage
            + (float) $this->jasaSimpananPercentage
            + (float) $this->jasaUsahaPercentage
            + (float) $this->pengurusPercentage
            + (float) $this->danaSosialPercentage;
    }

    public function createNewSession()
    {
        $this->selectedSessionId = null;
        $this->year = (int) date('Y');
        $this->eventDate = date('Y-m-d');
        $this->title = 'RAT Koperasi Bermadani Tahun Buku ' . $this->year;
        $this->totalNetProfit = 0;
        $this->totalMemberShu = 0;
        $this->memberAllocationPercentage = 100;
        $this->notes = null;
        $this->cadanganPercentage = 25.00;
        $this->jasaSimpananPercentage = 30.00;
        $this->jasaUsahaPercentage = 25.00;
        $this->pengurusPercentage = 10.00;
        $this->danaSosialPercentage = 10.00;
        $this->fetchFinancialData();
    }

    public function saveSession()
    {
        $this->validate([
            'year' => 'required|integer|min:2000|max:2100',
            'eventDate' => 'required|date',
            'title' => 'required|string|max:255',
            'totalNetProfit' => 'required|numeric|min:0',
            'totalMemberShu' => 'required|numeric|min:0',
            'memberAllocationPercentage' => 'required|numeric|min:0|max:100',
            'cadanganPercentage' => 'required|numeric|min:0|max:100',
            'jasaSimpananPercentage' => 'required|numeric|min:0|max:100',
            'jasaUsahaPercentage' => 'required|numeric|min:0|max:100',
            'pengurusPercentage' => 'required|numeric|min:0|max:100',
            'danaSosialPercentage' => 'required|numeric|min:0|max:100',
        ]);

        // Validate allocation total
        if (abs($this->allocationTotal - 100) > 0.1) {
            session()->flash('error', 'Total alokasi 5 pos SHU harus = 100%. Saat ini: ' . number_format($this->allocationTotal, 2) . '%');
            return;
        }

        $session = RatSession::updateOrCreate(
            ['year' => $this->year],
            [
                'event_date' => $this->eventDate,
                'title' => $this->title,
                'total_net_profit' => (float) $this->totalNetProfit,
                'member_allocation_percentage' => (float) $this->memberAllocationPercentage,
                'total_member_shu' => (float) $this->totalMemberShu,
                'cadangan_percentage' => (float) $this->cadanganPercentage,
                'jasa_simpanan_percentage' => (float) $this->jasaSimpananPercentage,
                'jasa_usaha_percentage' => (float) $this->jasaUsahaPercentage,
                'pengurus_percentage' => (float) $this->pengurusPercentage,
                'dana_sosial_percentage' => (float) $this->danaSosialPercentage,
                'status' => RatSession::STATUS_DRAFT,
                'notes' => $this->notes,
                'created_by' => auth()->id(),
            ]
        );

        $this->selectedSessionId = $session->id;
        session()->flash('success', 'Konfigurasi Sesi RAT berhasil disimpan!');
    }

    public function advanceToEligibility()
    {
        $session = $this->activeSession;
        if (!$session) {
            session()->flash('error', 'Simpan konfigurasi terlebih dahulu.');
            return;
        }

        $session->transitionTo(RatSession::STATUS_CONFIGURED);
        return redirect()->route('admin.rat.eligibility', ['session' => $session->id]);
    }

    public function render()
    {
        return view('livewire.admin.rat-setup', [
            'session' => $this->activeSession,
            'allSessions' => RatSession::orderByDesc('year')->get(),
            'allocationTotal' => $this->allocationTotal,
        ])->layout('layouts.admin');
    }
}
