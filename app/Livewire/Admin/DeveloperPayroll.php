<?php

namespace App\Livewire\Admin;

use App\Models\WorkLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class DeveloperPayroll extends Component
{
    use WithPagination;

    // Filters
    public $filterMonth;
    public $filterYear;
    public $filterPeriod = '';
    public $filterDeveloper = '';
    public $filterStatus = '';
    public $viewMode = 'cards';

    // Bulk actions
    public $selectedLogs = [];
    public $selectAll = false;

    public $latestAvailablePeriodLabel = null;

    protected $queryString = [
        'viewMode' => ['except' => 'cards'],
    ];

    public function mount()
    {
        $latestWorkLog = WorkLog::select('date')
            ->whereNotNull('date')
            ->orderByDesc('date')
            ->first();

        if ($latestWorkLog) {
            $latestDate = Carbon::parse($latestWorkLog->date);
            $this->filterMonth = $latestDate->month;
            $this->filterYear = $latestDate->year;
            $this->filterPeriod = $latestDate->format('Y-m');
            $this->latestAvailablePeriodLabel = $latestDate->locale('id')->translatedFormat('F Y');
        } else {
            $this->filterMonth = now()->month;
            $this->filterYear = now()->year;
            $this->filterPeriod = now()->format('Y-m');
            $this->latestAvailablePeriodLabel = now()->locale('id')->translatedFormat('F Y');
        }
    }

    public function setCurrentPeriod()
    {
        $this->filterMonth = now()->month;
        $this->filterYear = now()->year;
        $this->filterPeriod = now()->format('Y-m');
        $this->resetPage();
    }

    public function setPreviousMonthPeriod()
    {
        $previousMonth = now()->subMonth();
        $this->filterMonth = $previousMonth->month;
        $this->filterYear = $previousMonth->year;
        $this->filterPeriod = $previousMonth->format('Y-m');
        $this->resetPage();
    }

    public function setLastThreeMonthsPeriod()
    {
        $latestWorkLog = WorkLog::select('date')
            ->whereNotNull('date')
            ->orderByDesc('date')
            ->first();

        $baseDate = $latestWorkLog ? Carbon::parse($latestWorkLog->date) : now();
        $threeMonthsBack = $baseDate->copy()->subMonthsNoOverflow(2);

        $this->filterMonth = $threeMonthsBack->month;
        $this->filterYear = $threeMonthsBack->year;
        $this->filterPeriod = $threeMonthsBack->format('Y-m');
        $this->resetPage();
    }

    public function setLatestAvailablePeriod()
    {
        $latestWorkLog = WorkLog::select('date')
            ->whereNotNull('date')
            ->orderByDesc('date')
            ->first();

        if (! $latestWorkLog) {
            return;
        }

        $latestDate = Carbon::parse($latestWorkLog->date);
        $this->filterMonth = $latestDate->month;
        $this->filterYear = $latestDate->year;
        $this->filterPeriod = $latestDate->format('Y-m');
        $this->latestAvailablePeriodLabel = $latestDate->locale('id')->translatedFormat('F Y');
        $this->resetPage();
    }

    public function updatedFilterPeriod($value)
    {
        if (! preg_match('/^\d{4}-\d{2}$/', (string) $value)) {
            return;
        }

        [$year, $month] = explode('-', $value);
        $this->filterYear = (int) $year;
        $this->filterMonth = (int) $month;

        $this->selectedLogs = [];
        $this->selectAll = false;
        $this->resetPage();
    }

    public function updatedFilterDeveloper()
    {
        $this->selectedLogs = [];
        $this->selectAll = false;
        $this->resetPage();
    }

    public function updatedFilterStatus()
    {
        $this->selectedLogs = [];
        $this->selectAll = false;
        $this->resetPage();
    }

    public function setViewMode($mode)
    {
        if (! in_array($mode, ['cards', 'table'], true)) {
            return;
        }

        $this->viewMode = $mode;
        $this->selectedLogs = [];
        $this->selectAll = false;
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedLogs = $this->logs->pluck('id')->toArray();
        } else {
            $this->selectedLogs = [];
        }
    }

    public function downloadPDF()
    {
        $logs = WorkLog::whereYear('date', $this->filterYear)
            ->whereMonth('date', $this->filterMonth)
            ->when($this->filterDeveloper, fn($q) => $q->where('developerName', $this->filterDeveloper))
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->orderBy('developerName')
            ->orderBy('date')
            ->get();

        $stats = $this->stats;
        $monthName = Carbon::createFromFormat('m', $this->filterMonth)->locale('id')->translatedFormat('F');

        $pdf = Pdf::loadView('admin.reports.developer-payroll-pdf', [
            'logs' => $logs,
            'stats' => $stats,
            'month' => $this->filterMonth,
            'year' => $this->filterYear,
            'monthName' => $monthName,
            'filterDeveloper' => $this->filterDeveloper,
            // 'developerNames' => $this->developerNames, // Unused in PDF view actually? Check view.
            'generatedAt' => now()->locale('id')->translatedFormat('d F Y H:i')
        ]);

        $fileName = "Laporan_Payroll_Developer_{$this->filterYear}_{$this->filterMonth}.pdf";

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $fileName);
    }

    public function approveSelected()
    {
        if (empty($this->selectedLogs)) {
            session()->flash('error', 'Pilih minimal 1 log untuk diapprove.');
            return;
        }

        DB::beginTransaction();
        try {
            WorkLog::whereIn('id', $this->selectedLogs)
                ->where('status', 'PENDING')
                ->update([
                    'status' => 'APPROVED',
                    'approvedBy' => auth()->id(),
                    'approvedAt' => now(),
                ]);

            DB::commit();
            $count = count($this->selectedLogs);
            session()->flash('success', "Berhasil approve {$count} log kerja.");
            $this->selectedLogs = [];
            $this->selectAll = false;
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal approve: ' . $e->getMessage());
        }
    }

    public function markAsPaid()
    {
        if (empty($this->selectedLogs)) {
            session()->flash('error', 'Pilih minimal 1 log untuk ditandai lunas.');
            return;
        }

        DB::beginTransaction();
        try {
            WorkLog::whereIn('id', $this->selectedLogs)
                ->where('status', 'APPROVED')
                ->update([
                    'status' => 'PAID',
                    'paidAt' => now(),
                ]);

            DB::commit();
            $count = count($this->selectedLogs);
            session()->flash('success', "Berhasil menandai {$count} log sebagai PAID.");
            $this->selectedLogs = [];
            $this->selectAll = false;
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal memproses: ' . $e->getMessage());
        }
    }

    public function rejectLog($id)
    {
        $log = WorkLog::find($id);
        if ($log && $log->status === 'PENDING') {
            $log->update([
                'status' => 'REJECTED',
                'approvedBy' => auth()->id(),
                'approvedAt' => now(),
            ]);
            session()->flash('success', 'Log kerja ditolak.');
        }
    }

    public function approveSingle($id)
    {
        $log = WorkLog::find($id);
        if ($log && $log->status === 'PENDING') {
            $log->update([
                'status' => 'APPROVED',
                'approvedBy' => auth()->id(),
                'approvedAt' => now(),
            ]);
            session()->flash('success', 'Log kerja disetujui.');
        }
    }

    public function approveDeveloperPending($developerName)
    {
        $pendingLogs = $this->getFilteredLogsQuery()
            ->where('developerName', $developerName)
            ->where('status', 'PENDING')
            ->get();

        if ($pendingLogs->isEmpty()) {
            session()->flash('error', 'Tidak ada log pending untuk developer ini.');
            return;
        }

        DB::beginTransaction();
        try {
            WorkLog::whereIn('id', $pendingLogs->pluck('id'))
                ->update([
                    'status' => 'APPROVED',
                    'approvedBy' => auth()->id(),
                    'approvedAt' => now(),
                ]);

            DB::commit();
            $count = $pendingLogs->count();
            session()->flash('success', "Berhasil approve {$count} log pending untuk {$developerName}.");
            $this->selectedLogs = [];
            $this->selectAll = false;
            $this->resetPage();
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal approve developer: ' . $e->getMessage());
        }
    }

    public function getLogsProperty()
    {
        return $this->getFilteredLogsQuery()
            ->with(['user', 'approver'])
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
    }

    public function getDevelopersProperty()
    {
        // Fetch distinct developer names from WorkLog table
        return WorkLog::select('developerName')
            ->distinct()
            ->orderBy('developerName')
            ->pluck('developerName');
    }

    public function getAvailablePeriodsProperty()
    {
        return WorkLog::selectRaw('YEAR(date) as year, MONTH(date) as month')
            ->whereNotNull('date')
            ->distinct()
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->get()
            ->map(function ($item) {
                $periodDate = Carbon::createFromDate((int) $item->year, (int) $item->month, 1);

                return [
                    'value' => $periodDate->format('Y-m'),
                    'label' => $periodDate->locale('id')->translatedFormat('F Y'),
                ];
            });
    }

    public function getStatsProperty()
    {
        $baseQuery = $this->getFilteredLogsQuery();

        return [
            'totalHours' => (clone $baseQuery)->sum('hoursWorked'),
            'totalAmount' => (clone $baseQuery)->sum('totalAmount'),
            'pending' => (clone $baseQuery)->where('status', 'PENDING')->sum('totalAmount'),
            'approved' => (clone $baseQuery)->where('status', 'APPROVED')->sum('totalAmount'),
            'paid' => (clone $baseQuery)->where('status', 'PAID')->sum('totalAmount'),
            'pendingCount' => (clone $baseQuery)->where('status', 'PENDING')->count(),
        ];
    }

    public function getDevSummaryProperty()
    {
        return $this->getFilteredLogsQuery()
            ->select('developerName', DB::raw('SUM(hoursWorked) as total_hours'), DB::raw('SUM(totalAmount) as total_amount'))
            ->groupBy('developerName')
            ->orderBy('developerName')
            ->get();
    }

    public function getDeveloperCardsProperty()
    {
        $logs = $this->getFilteredLogsQuery()
            ->with('approver')
            ->orderBy('developerName')
            ->orderByDesc('date')
            ->orderByDesc('created_at')
            ->get();

        return $logs
            ->groupBy(fn($log) => $log->developerName ?: 'Unknown')
            ->map(function ($items, $developerName) {
                return [
                    'developerName' => $developerName,
                    'totalHours' => (float) $items->sum('hoursWorked'),
                    'totalAmount' => (float) $items->sum('totalAmount'),
                    'pendingCount' => $items->where('status', 'PENDING')->count(),
                    'approvedCount' => $items->where('status', 'APPROVED')->count(),
                    'paidCount' => $items->where('status', 'PAID')->count(),
                    'rejectedCount' => $items->where('status', 'REJECTED')->count(),
                    'logs' => $items,
                ];
            })
            ->values();
    }

    private function getFilteredLogsQuery()
    {
        return WorkLog::query()
            ->whereYear('date', $this->filterYear)
            ->whereMonth('date', $this->filterMonth)
            ->when($this->filterDeveloper, fn($q) => $q->where('developerName', $this->filterDeveloper))
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus));
    }

    public function render()
    {
        return view('livewire.admin.developer-payroll', [
            'logs' => $this->logs,
            'developers' => $this->developers,
            'availablePeriods' => $this->availablePeriods,
            'stats' => $this->stats,
            'devSummary' => $this->devSummary,
            'developerCards' => $this->developerCards,
            'activePeriodLabel' => Carbon::createFromDate($this->filterYear, $this->filterMonth, 1)->locale('id')->translatedFormat('F Y'),
        ])->layout('layouts.admin');
    }
}
