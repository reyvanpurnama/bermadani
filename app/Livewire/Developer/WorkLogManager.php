<?php

namespace App\Livewire\Developer;

use App\Models\WorkLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class WorkLogManager extends Component
{
    use WithPagination, WithFileUploads;

    // Form inputs
    public $developerName;
    public $date;
    public $startTime;
    public $endTime;
    public $hoursWorked;
    public $description;

    // Filters
    public $filterMonth;
    public $filterYear;
    public $filterPeriod = '';
    public $filterDeveloper = '';

    // Modal
    public $showForm = false;
    public $editingId = null;

    // Import
    public $showImportModal = false;
    public $importFile;
    public $importSummary = null;

    public $latestAvailablePeriodLabel = null;

    protected $queryString = [
        'filterPeriod' => ['except' => ''],
    ];

    protected $rules = [
        'developerName' => 'required|string|min:2|max:100',
        'date' => 'required|date',
        'hoursWorked' => 'required|numeric|min:0.5|max:24',
        'description' => 'required|string|min:5|max:500',
        'startTime' => 'nullable',
        'endTime' => 'nullable',
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

        $this->date = now()->format('Y-m-d');
        $this->developerName = '';
    }

    public function setCurrentPeriod()
    {
        $current = now();
        $this->filterMonth = $current->month;
        $this->filterYear = $current->year;
        $this->filterPeriod = $current->format('Y-m');
        $this->resetPage();
    }

    public function setPreviousMonthPeriod()
    {
        $previous = now()->subMonth();
        $this->filterMonth = $previous->month;
        $this->filterYear = $previous->year;
        $this->filterPeriod = $previous->format('Y-m');
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
        $this->resetPage();
    }

    public function updatedFilterDeveloper()
    {
        $this->resetPage();
    }

    // ========== Import CSV ==========
    public function openImportModal()
    {
        $this->showImportModal = true;
        $this->importFile = null;
        $this->importSummary = null;
    }

    public function closeImportModal()
    {
        $this->showImportModal = false;
        $this->importFile = null;
        $this->importSummary = null;
    }

    public function importCSV()
    {
        $this->validate([
            'importFile' => 'required|mimes:csv,txt|max:2048',
        ]);

        try {
            $filePath = $this->importFile->getRealPath();
            $content = file_get_contents($filePath);
            $lines = preg_split('/\r\n|\r|\n/', $content);

            $developerName = '';
            $dataStarted = false;
            $success = 0;
            $skipped = 0;
            $errors = [];

            foreach ($lines as $lineNumber => $line) {
                $line = trim($line);
                if (empty($line))
                    continue;

                // Parse CSV line
                $columns = str_getcsv($line);

                // Look for developer name in row 2 (NAMA,value,,,)
                if (isset($columns[0]) && strtoupper(trim($columns[0])) === 'NAMA' && isset($columns[1])) {
                    $developerName = trim($columns[1]);
                    continue;
                }

                // Skip header row
                if (isset($columns[0]) && strtolower(trim($columns[0])) === 'tanggal') {
                    $dataStarted = true;
                    continue;
                }

                // Avoid processing footer rows like signatures or totals
                $firstCol = strtoupper(trim($columns[0] ?? ''));
                if (
                    in_array($firstCol, ['JUMLAH LEMBUR', 'TOTAL JAM', '']) ||
                    str_starts_with($firstCol, 'JUMLAH') ||
                    str_starts_with($firstCol, 'TOTAL') ||
                    str_starts_with($firstCol, 'KETUA') ||
                    str_starts_with($firstCol, 'MENGETAHUI')
                ) {
                    continue;
                }

                // Process data rows
                if ($dataStarted && count($columns) >= 5) {
                    try {
                        // Parse date: "Senin, 24 November 2025" or similar
                        $dateStr = trim($columns[0], '"');
                        // Try to extract date part after the comma
                        if (preg_match('/,\s*(.+)/', $dateStr, $matches)) {
                            $dateStr = trim($matches[1]);
                        }

                        // Skip if dateStr doesn't look like a date
                        if (empty($dateStr) || !preg_match('/\d{1,2}\s+\w+\s+\d{4}/', $dateStr)) {
                            $skipped++;
                            continue;
                        }

                        // Translate Indonesian months to English for Carbon
                        $indonesianMonths = [
                            'Januari' => 'January',
                            'Februari' => 'February',
                            'Maret' => 'March',
                            'April' => 'April',
                            'Mei' => 'May',
                            'Juni' => 'June',
                            'Juli' => 'July',
                            'Agustus' => 'August',
                            'September' => 'September',
                            'Oktober' => 'October',
                            'November' => 'November',
                            'Desember' => 'December',
                        ];

                        foreach ($indonesianMonths as $indo => $eng) {
                            $dateStr = str_ireplace($indo, $eng, $dateStr);
                        }

                        $date = Carbon::parse($dateStr);

                        $startTime = trim($columns[1]) ?: null;
                        $endTime = trim($columns[2]) ?: null;

                        // Normalize time format (H:MM -> HH:MM)
                        if ($startTime && preg_match('/^\d:\d{2}$/', $startTime)) {
                            $startTime = '0' . $startTime;
                        }
                        if ($endTime && preg_match('/^\d:\d{2}$/', $endTime)) {
                            $endTime = '0' . $endTime;
                        }
                        // Explicitly handle 0:00 -> 00:00 just in case
                        if ($startTime === '0:00')
                            $startTime = '00:00';
                        if ($endTime === '0:00')
                            $endTime = '00:00';

                        $hoursWorked = floatval(trim($columns[3]));
                        $description = trim($columns[4]);

                        if ($hoursWorked > 0 && !empty($description)) {
                            WorkLog::create([
                                'userId' => auth()->id(),
                                'developerName' => $developerName ?: 'Unknown Developer',
                                'date' => $date->format('Y-m-d'),
                                'startTime' => $startTime,
                                'endTime' => $endTime,
                                'hoursWorked' => $hoursWorked,
                                'description' => $description,
                                'hourlyRate' => 6000.00,
                                'status' => 'PENDING',
                            ]);
                            $success++;
                        } else {
                            $skipped++;
                        }
                    } catch (\Exception $e) {
                        $errors[] = "Baris " . ($lineNumber + 1) . ": " . $e->getMessage();
                    }
                }
            }

            $this->importSummary = [
                'success' => $success,
                'skipped' => $skipped,
                'errors' => count($errors),
                'error_details' => array_slice($errors, 0, 5), // Show first 5 errors
                'developer' => $developerName,
            ];

            if ($success > 0) {
                session()->flash('success', "Import berhasil! {$success} log ditambahkan untuk {$developerName}.");
            } else {
                session()->flash('error', 'Tidak ada data yang berhasil diimport.');
            }

        } catch (\Exception $e) {
            session()->flash('error', 'Gagal import: ' . $e->getMessage());
        }
    }

    // ========== Export PDF ==========
    public function downloadPDF()
    {
        $logs = $this->getFilteredLogsQuery()
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
            'developerNames' => $this->developerNames,
            'generatedAt' => now()->locale('id')->translatedFormat('d F Y H:i')
        ]);

        $fileName = "Laporan_Jam_Kerja_Developer_{$this->filterYear}_{$this->filterMonth}.pdf";

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $fileName);
    }

    // ========== Form CRUD ==========
    public function openForm()
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function closeForm()
    {
        $this->showForm = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->editingId = null;
        $this->developerName = '';
        $this->date = now()->format('Y-m-d');
        $this->startTime = '';
        $this->endTime = '';
        $this->hoursWorked = '';
        $this->description = '';
        $this->resetValidation();
    }

    public function editLog($id)
    {
        $log = WorkLog::where('status', 'PENDING')->find($id);

        if (!$log) {
            session()->flash('error', 'Log tidak ditemukan atau sudah diproses.');
            return;
        }

        $this->editingId = $id;
        $this->developerName = $log->developerName;
        $this->date = $log->date->format('Y-m-d');
        $this->startTime = $log->startTime;
        $this->endTime = $log->endTime;
        $this->hoursWorked = $log->hoursWorked;
        $this->description = $log->description;
        $this->showForm = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'userId' => auth()->id(),
            'developerName' => $this->developerName,
            'date' => $this->date,
            'startTime' => $this->startTime ?: null,
            'endTime' => $this->endTime ?: null,
            'hoursWorked' => $this->hoursWorked,
            'description' => $this->description,
            'hourlyRate' => 6000.00,
            'status' => 'PENDING',
        ];

        if ($this->editingId) {
            $log = WorkLog::where('status', 'PENDING')->find($this->editingId);

            if ($log) {
                $log->update($data);
                session()->flash('success', 'Log kerja berhasil diperbarui.');
            }
        } else {
            WorkLog::create($data);
            session()->flash('success', 'Log kerja berhasil disimpan.');
        }

        $this->closeForm();
    }

    public function deleteLog($id)
    {
        $log = WorkLog::where('status', 'PENDING')->find($id);

        if ($log) {
            $log->delete();
            session()->flash('success', 'Log kerja berhasil dihapus.');
        } else {
            session()->flash('error', 'Hanya log dengan status PENDING yang bisa dihapus.');
        }
    }

    // ========== Computed Properties ==========
    public function getDeveloperNamesProperty()
    {
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

    public function getLogsProperty()
    {
        return $this->getFilteredLogsQuery()
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
    }

    public function getStatsProperty()
    {
        $query = $this->getFilteredLogsQuery();

        $totalHours = (clone $query)->sum('hoursWorked');
        $totalAmount = (clone $query)->sum('totalAmount');
        $pending = (clone $query)->where('status', 'PENDING')->sum('totalAmount');
        $approved = (clone $query)->where('status', 'APPROVED')->sum('totalAmount');
        $paid = (clone $query)->where('status', 'PAID')->sum('totalAmount');

        return [
            'totalHours' => $totalHours,
            'totalAmount' => $totalAmount,
            'pending' => $pending,
            'approved' => $approved,
            'paid' => $paid,
        ];
    }

    private function getFilteredLogsQuery()
    {
        return WorkLog::query()
            ->whereYear('date', $this->filterYear)
            ->whereMonth('date', $this->filterMonth)
            ->when($this->filterDeveloper, fn($q) => $q->where('developerName', $this->filterDeveloper));
    }

    public function render()
    {
        return view('livewire.developer.work-log-manager', [
            'logs' => $this->logs,
            'stats' => $this->stats,
            'developerNames' => $this->developerNames,
            'availablePeriods' => $this->availablePeriods,
            'activePeriodLabel' => Carbon::createFromDate($this->filterYear, $this->filterMonth, 1)->locale('id')->translatedFormat('F Y'),
        ])->layout('layouts.admin');
    }
}
