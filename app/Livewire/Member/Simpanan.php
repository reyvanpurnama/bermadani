<?php

namespace App\Livewire\Member;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\Member;
use App\Models\SimpananTransaction;
use Carbon\Carbon;

#[Layout('layouts.member')]
class Simpanan extends Component
{
    use WithPagination;

    public $member;
    public $activeTab = 'all';
    public $filterType = '';
    public $showBalance = true;
    public $unreadCount = 0;
    public $selectedTransfer = null;
    public $showReceiptModal = false;
    public $selectedYear;

    public function mount()
    {
        $user = auth()->user();
        $this->member = Member::where('userId', $user->id)->first();
        $this->selectedYear = date('Y');
        $this->markAsRead();
    }

    public function toggleBalance()
    {
        $this->showBalance = !$this->showBalance;
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
        $this->filterType = $tab === 'all' ? '' : strtoupper($tab);
        $this->resetPage();
    }

    public function markAsRead()
    {
        if ($this->member) {
            SimpananTransaction::where('memberId', $this->member->id)
                ->where('isRead', false)
                ->update(['isRead' => true]);
        }
    }

    public function viewReceipt($transferId)
    {
        $this->selectedTransfer = SimpananTransaction::with(['member', 'relatedMember'])
            ->findOrFail($transferId);
        $this->showReceiptModal = true;
    }

    public function closeReceipt()
    {
        $this->showReceiptModal = false;
        $this->selectedTransfer = null;
    }

    public function getSimwaGridProperty()
    {
        if (!$this->member)
            return [];

        $grid = [];
        $today = Carbon::now();
        $joinDate = $this->member->joinDate ? Carbon::parse($this->member->joinDate)->startOfMonth() : $today->startOfMonth();
        $selectedYear = (int) ($this->selectedYear ?? date('Y'));

        $txs = SimpananTransaction::where('memberId', $this->member->id)
            ->where('status', 'APPROVED')
            ->get();

        $imports = \DB::table('audit_simwa_imports')
            ->where('matched_member_id', $this->member->id)
            ->get();

        $monthsName = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        for ($i = 1; $i <= 12; $i++) {
            $currentMonthDate = Carbon::createFromDate($selectedYear, $i, 1)->endOfMonth();
            $periodKey = sprintf('%s-%02d', $selectedYear, $i);
            $monthName = $monthsName[$i];

            $hasTx = $txs->filter(function ($t) use ($selectedYear, $i, $periodKey, $monthName) {
                if ($t->billingMonth === $periodKey) return true;

                $dateMatch = ($t->created_at->format('Y') == $selectedYear && $t->created_at->format('n') == $i);
                $notesMatch = !empty($t->notes) && str_contains(strtolower($t->notes), strtolower($monthName)) && (str_contains($t->notes, (string)$selectedYear) || $t->created_at->format('Y') == $selectedYear);

                if ($t->type === 'WAJIB') {
                    return $dateMatch || $notesMatch;
                }

                if ($t->type === 'SUKARELA' && (!empty($t->notes) && (str_contains(strtolower($t->notes), 'payroll') || str_contains(strtolower($t->notes), 'tabungan')))) {
                    return $dateMatch || $notesMatch;
                }

                return false;
            })->first();

            $hasImport = $imports->filter(function ($imp) use ($periodKey) {
                return $imp->period === $periodKey && (
                    str_contains(strtoupper($imp->raw_uraian), 'SIMWA') || 
                    str_contains(strtoupper($imp->raw_uraian), 'TABUNGAN') || 
                    str_contains(strtoupper($imp->raw_uraian), 'SUKARELA')
                );
            })->first();

            $status = 'UNPAID';

            if ($hasTx || $hasImport) {
                $status = 'PAID';
            } elseif ($currentMonthDate->isFuture() && $currentMonthDate->format('Y-m') > $today->format('Y-m')) {
                $status = 'FUTURE';
            } elseif ($currentMonthDate->lt($joinDate)) {
                $status = 'NOT_MEMBER';
            }

            $grid[$i] = [
                'monthName' => Carbon::create()->month($i)->translatedFormat('M'),
                'fullName' => $monthName,
                'status' => $status,
            ];
        }

        return $grid;
    }

    public function render()
    {
        $simpanan = collect();

        if ($this->member) {
            $query = SimpananTransaction::where('memberId', $this->member->id)
                ->where('status', 'APPROVED')
                ->orderBy('created_at', 'desc');

            if ($this->filterType && $this->filterType !== 'all') {
                $query->where('type', $this->filterType);
            }

            $simpanan = $query->paginate(10);
        }

        return view('livewire.member.simpanan', [
            'simpanan' => $simpanan,
        ]);
    }
}
