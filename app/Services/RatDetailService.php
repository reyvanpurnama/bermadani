<?php

namespace App\Services;

use App\Models\RatManualEntry;
use App\Models\SimpananTransaction;
use Illuminate\Support\Facades\DB;

class RatDetailService
{
    public function build(int $year): array
    {
        $tables = config('rat_tables', []);
        $report = [];

        foreach ($tables as $tableKey => $tableDef) {
            $columns = $this->resolveColumns($tableDef['columns'] ?? [], $year);
            $columnYears = array_values(array_unique(array_filter(array_map(fn ($col) => $col['year'], $columns))));
            $fieldKeys = array_values(array_unique(array_map(fn ($col) => $col['field_key'], $columns)));

            $manualEntries = RatManualEntry::query()
                ->where('table_key', $tableKey)
                ->whereIn('year', $columnYears)
                ->whereIn('field_key', $fieldKeys)
                ->get();

            $manualMap = [];
            foreach ($manualEntries as $entry) {
                $manualMap[$entry->year][$entry->field_key][$entry->row_key] = (float) $entry->amount;
            }

            $rows = [];
            $effectiveValues = [];
            $rawValues = [];

            foreach ($tableDef['rows'] as $rowDef) {
                $type = $rowDef['type'] ?? 'row';
                $rowKey = $rowDef['key'] ?? null;
                $sign = (int) ($rowDef['sign'] ?? 1);
                $hasSumOf = isset($rowDef['sum_of']) && is_array($rowDef['sum_of']);

                $row = [
                    'key' => $rowKey,
                    'label' => $rowDef['label'],
                    'type' => $type,
                    'level' => (int) ($rowDef['level'] ?? 0),
                    'sign' => $sign,
                    'editable' => $this->isRowEditable($type, $hasSumOf, $rowDef),
                    'values' => [],
                    'raw' => [],
                ];

                if ($type === 'section' || $type === 'group') {
                    $rows[] = $row;
                    continue;
                }

                foreach ($columns as $column) {
                    $columnKey = $column['key'];
                    $columnYear = $column['year'];
                    $fieldKey = $column['field_key'];

                    if ($type === 'total' && $hasSumOf) {
                        $row['values'][$columnKey] = null;
                        $row['raw'][$columnKey] = null;
                        continue;
                    }

                    $rawValue = $manualMap[$columnYear][$fieldKey][$rowKey] ?? null;
                    if ($rawValue === null && isset($rowDef['auto_key']) && $fieldKey === 'nilai') {
                        $rawValue = $this->computeAutoValue($rowDef['auto_key'], $columnYear);
                    }

                    $rawValues[$rowKey][$columnKey] = $rawValue;
                    $effectiveValues[$rowKey][$columnKey] = $this->applySign($rawValue, $sign);

                    $row['raw'][$columnKey] = $rawValue;
                    $row['values'][$columnKey] = $effectiveValues[$rowKey][$columnKey];
                }

                $rows[] = $row;
            }

            // Compute totals with sum_of rules
            foreach ($rows as $index => $row) {
                if ($row['type'] !== 'total') {
                    continue;
                }

                $rowDef = $this->findRowDef($tableDef['rows'], $row['key']);
                if (!isset($rowDef['sum_of']) || !is_array($rowDef['sum_of'])) {
                    continue;
                }

                foreach ($columns as $column) {
                    $columnKey = $column['key'];
                    $total = 0.0;

                    foreach ($rowDef['sum_of'] as $sumItem) {
                        $itemKey = is_array($sumItem) ? $sumItem['key'] : $sumItem;
                        $itemSign = is_array($sumItem) && isset($sumItem['sign']) ? (int) $sumItem['sign'] : 1;
                        $total += ($effectiveValues[$itemKey][$columnKey] ?? 0) * $itemSign;
                    }

                    $rows[$index]['raw'][$columnKey] = $total;
                    $rows[$index]['values'][$columnKey] = $total;
                }
            }

            $report[$tableKey] = [
                'title' => $tableDef['title'],
                'columns' => $columns,
                'rows' => $rows,
            ];
        }

        return $report;
    }

    private function resolveColumns(array $columns, int $year): array
    {
        $resolved = [];
        foreach ($columns as $column) {
            $offset = (int) ($column['year_offset'] ?? 0);
            $columnYear = $year + $offset;
            $label = $column['label'] ?? '';
            $label = str_replace(['{year}', '{prev}'], [$year, $year - 1], $label);

            $resolved[] = [
                'key' => $column['key'],
                'label' => $label,
                'year' => $columnYear,
                'field_key' => $column['field_key'] ?? 'nilai',
            ];
        }

        return $resolved;
    }

    private function isRowEditable(string $type, bool $hasSumOf, array $rowDef): bool
    {
        if (isset($rowDef['editable'])) {
            return (bool) $rowDef['editable'];
        }

        if ($type === 'section' || $type === 'group') {
            return false;
        }

        if ($type === 'total' && $hasSumOf) {
            return false;
        }

        return true;
    }

    private function applySign(?float $value, int $sign): ?float
    {
        if ($value === null) {
            return null;
        }

        return $value * $sign;
    }

    private function computeAutoValue(string $autoKey, int $year): ?float
    {
        return match ($autoKey) {
            'simpanan_pokok_balance' => $this->simpananBalanceByYear('POKOK', $year),
            'simpanan_wajib_balance' => $this->simpananBalanceByYear('WAJIB', $year),
            'simpanan_sukarela_balance' => $this->simpananBalanceByYear('SUKARELA', $year),
            'simpanan_pokok_inflow' => $this->simpananInflowByYear('POKOK', $year),
            'simpanan_wajib_inflow' => $this->simpananInflowByYear('WAJIB', $year),
            'simpanan_sukarela_inflow' => $this->simpananInflowByYear('SUKARELA', $year),
            'simpanan_sukarela_outflow' => $this->simpananOutflowByYear('SUKARELA', $year),
            default => null,
        };
    }

    private function simpananBalanceByYear(string $type, int $year): float
    {
        return (float) SimpananTransaction::query()
            ->where('type', $type)
            ->where('status', 'APPROVED')
            ->whereYear('created_at', '<=', $year)
            ->sum(DB::raw('CASE WHEN transactionType IN ("SETOR", "TRANSFER_IN") THEN amount ELSE -amount END'));
    }

    private function simpananInflowByYear(string $type, int $year): float
    {
        return (float) SimpananTransaction::query()
            ->where('type', $type)
            ->where('status', 'APPROVED')
            ->whereYear('created_at', $year)
            ->whereIn('transactionType', ['SETOR', 'TRANSFER_IN'])
            ->sum('amount');
    }

    private function simpananOutflowByYear(string $type, int $year): float
    {
        return (float) SimpananTransaction::query()
            ->where('type', $type)
            ->where('status', 'APPROVED')
            ->whereYear('created_at', $year)
            ->whereIn('transactionType', ['TARIK', 'TRANSFER_OUT'])
            ->sum('amount');
    }

    private function findRowDef(array $rowDefs, ?string $key): ?array
    {
        foreach ($rowDefs as $rowDef) {
            if (($rowDef['key'] ?? null) === $key) {
                return $rowDef;
            }
        }

        return null;
    }
}
