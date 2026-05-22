<?php

namespace App\Livewire\Admin;

use App\Models\FinancialTransaction;
use App\Models\Loan;
use App\Models\RatManualEntry;
use App\Models\SimpananTransaction;
use App\Services\RatDetailService;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class RatDetailReport extends Component
{
    public $selectedYear;
    public $availableYears = [];
    public $tables = [];
    public $activeTab = 'neraca';
    public $manualInputs = [];

    public function mount()
    {
        $this->selectedYear = (int) now()->format('Y');
        $this->availableYears = $this->buildAvailableYears();
        $this->loadReport();
    }

    public function updatedSelectedYear()
    {
        $this->loadReport();
    }

    public function notifyExport(): void
    {
        $this->dispatch('notify', [
            'message' => 'Fitur ekspor sedang dalam proses pengembangan.',
            'type' => 'info',
        ]);
    }

    public function saveTable(string $tableKey): void
    {
        $table = $this->tables[$tableKey] ?? null;
        if (!$table) {
            return;
        }

        $errors = [];
        $payload = [];

        foreach ($table['rows'] as $row) {
            if (!$row['editable'] || !$row['key']) {
                continue;
            }

            foreach ($table['columns'] as $column) {
                if ((int) $column['year'] !== (int) $this->selectedYear) {
                    continue;
                }

                $columnKey = $column['key'];
                $fieldKey = $column['field_key'];
                $rowKey = $row['key'];

                $value = $this->manualInputs[$tableKey][$rowKey][$columnKey] ?? null;
                if ($value === '' || $value === null) {
                    $payload[] = [
                        'row_key' => $rowKey,
                        'field_key' => $fieldKey,
                        'year' => (int) $column['year'],
                        'amount' => null,
                    ];
                    continue;
                }

                if (!is_numeric($value)) {
                    $errors[] = $row['label'];
                    continue;
                }

                $normalized = (float) $value;
                if (($row['sign'] ?? 1) < 0) {
                    $normalized = abs($normalized);
                }

                $payload[] = [
                    'row_key' => $rowKey,
                    'field_key' => $fieldKey,
                    'year' => (int) $column['year'],
                    'amount' => $normalized,
                ];
            }
        }

        if (!empty($errors)) {
            $this->dispatch('notify', [
                'message' => 'Input tidak valid untuk: ' . implode(', ', $errors),
                'type' => 'error',
            ]);
            return;
        }

        foreach ($payload as $entry) {
            $entryQuery = RatManualEntry::query()
                ->where('table_key', $tableKey)
                ->where('row_key', $entry['row_key'])
                ->where('field_key', $entry['field_key'])
                ->where('year', $entry['year']);

            if ($entry['amount'] === null) {
                $entryQuery->delete();
                continue;
            }

            $model = $entryQuery->first();
            if (!$model) {
                $model = new RatManualEntry();
                $model->table_key = $tableKey;
                $model->row_key = $entry['row_key'];
                $model->field_key = $entry['field_key'];
                $model->year = $entry['year'];
                $model->created_by = auth()->id();
            }

            $model->amount = $entry['amount'];
            $model->updated_by = auth()->id();
            $model->save();
        }

        $this->loadReport();

        $this->dispatch('notify', [
            'message' => 'Data manual berhasil disimpan.',
            'type' => 'success',
        ]);
    }

    public function formatCurrency($value): string
    {
        if ($value === null) {
            return '-';
        }

        $numeric = (float) $value;
        $formatted = number_format(abs($numeric), 0, ',', '.');

        if ($numeric < 0) {
            return '(Rp ' . $formatted . ')';
        }

        return 'Rp ' . $formatted;
    }

    public function render()
    {
        return view('livewire.admin.rat-detail-report')->layout('layouts.admin');
    }

    private function loadReport(): void
    {
        $this->tables = app(RatDetailService::class)->build((int) $this->selectedYear);
        $this->manualInputs = [];

        foreach ($this->tables as $tableKey => $table) {
            foreach ($table['rows'] as $row) {
                if (!$row['editable'] || !$row['key']) {
                    continue;
                }

                foreach ($table['columns'] as $column) {
                    if ((int) $column['year'] !== (int) $this->selectedYear) {
                        continue;
                    }

                    $columnKey = $column['key'];
                    $this->manualInputs[$tableKey][$row['key']][$columnKey] = $row['raw'][$columnKey] ?? null;
                }
            }
        }
    }

    private function buildAvailableYears(): array
    {
        $currentYear = (int) now()->year;
        $startCandidates = [];

        $simpananStart = SimpananTransaction::min(DB::raw('YEAR(created_at)'));
        if ($simpananStart) {
            $startCandidates[] = (int) $simpananStart;
        }

        $loanStart = Loan::min(DB::raw('YEAR(created_at)'));
        if ($loanStart) {
            $startCandidates[] = (int) $loanStart;
        }

        $financialStart = FinancialTransaction::min(DB::raw('YEAR(transactionDate)'));
        if ($financialStart) {
            $startCandidates[] = (int) $financialStart;
        }

        $manualStart = RatManualEntry::min('year');
        if ($manualStart) {
            $startCandidates[] = (int) $manualStart;
        }

        $startYear = !empty($startCandidates) ? min($startCandidates) : $currentYear;
        $startYear = max(2020, $startYear);

        $years = range($startYear, $currentYear);
        rsort($years);

        return $years;
    }
}
