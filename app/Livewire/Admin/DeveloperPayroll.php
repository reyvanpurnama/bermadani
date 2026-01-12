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
    public $filterDeveloper = '';
    public $filterStatus = '';

    // Bulk actions
    public $selectedLogs = [];
    public $selectAll = false;

    public function mount()
    {
        $this->filterMonth = now()->month;
        $this->filterYear = now()->year;
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

    public function getLogsProperty()
    {
        return WorkLog::with('user')
            ->whereYear('date', $this->filterYear)
            ->whereMonth('date', $this->filterMonth)
            ->when($this->filterDeveloper, fn($q) => $q->where('developerName', $this->filterDeveloper))
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
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

    public function getStatsProperty()
    {
        $baseQuery = WorkLog::whereYear('date', $this->filterYear)
            ->whereMonth('date', $this->filterMonth)
            ->when($this->filterDeveloper, fn($q) => $q->where('developerName', $this->filterDeveloper));

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
        return WorkLog::select('developerName', DB::raw('SUM(hoursWorked) as total_hours'), DB::raw('SUM(totalAmount) as total_amount'))
            ->whereYear('date', $this->filterYear)
            ->whereMonth('date', $this->filterMonth)
            ->groupBy('developerName')
            ->orderBy('developerName')
            ->get();
    }

    public function render()
    {
        return view('livewire.admin.developer-payroll', [
            'logs' => $this->logs,
            'developers' => $this->developers,
            'stats' => $this->stats,
            'devSummary' => $this->devSummary,
        ])->layout('layouts.admin');
    }
}
