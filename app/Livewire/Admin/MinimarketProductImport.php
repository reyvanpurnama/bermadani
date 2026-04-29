<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;

class MinimarketProductImport extends Component
{
    use WithFileUploads;

    public $file;
    public $headers = [];
    public $previewData = [];

    public $importStats = [
        'total_rows' => 0,
        'preview_rows' => 0,
        'success' => 0,
        'skipped_duplicates' => 0,
        'failed' => 0,
    ];

    public $importErrors = [];

    public function updatedFile()
    {
        $this->resetImportState();

        $this->validate([
            'file' => 'required|mimes:csv,txt|max:5120',
        ]);

        $this->parseCsvPreview();
    }

    private function resetImportState(): void
    {
        $this->headers = [];
        $this->previewData = [];
        $this->importErrors = [];
        $this->importStats = [
            'total_rows' => 0,
            'preview_rows' => 0,
            'success' => 0,
            'skipped_duplicates' => 0,
            'failed' => 0,
        ];
    }

    private function normalizeHeader(string $header): string
    {
        $header = trim($header);
        $header = preg_replace('/\s+/', '', $header);
        return strtoupper($header);
    }

    private function normalizeName(string $name): string
    {
        $name = trim($name);
        $name = preg_replace('/\s+/', ' ', $name);
        return $name;
    }

    private function parsePercent(?string $raw): ?float
    {
        if ($raw === null) {
            return null;
        }

        $raw = trim($raw);
        if ($raw === '') {
            return null;
        }

        $cleaned = str_replace(['%', ' '], '', $raw);

        // If it contains both '.' and ',', assume '.' is thousand or decimal? For percent, both are decimals in our data.
        // Prefer treating ',' as decimal comma.
        if (str_contains($cleaned, ',') && str_contains($cleaned, '.')) {
            // Example might be 1.234,56 but percent is unlikely; normalize as ID style
            $candidate = str_replace('.', '', $cleaned);
            $candidate = str_replace(',', '.', $candidate);
            $value = (float) $candidate;
        } elseif (str_contains($cleaned, ',')) {
            $value = (float) str_replace(',', '.', $cleaned);
        } else {
            $value = (float) $cleaned;
        }

        if ($value < 0 || $value > 10000) {
            return null;
        }

        return $value;
    }

    /**
     * Returns candidate parses for money-like values seen in the CSV.
     * Handles: 4.250 (thousand), 1.983,30 (ID), 833,3 (comma decimal), 19.98 (dot decimal), etc.
     */
    private function moneyCandidates(?string $raw): array
    {
        if ($raw === null) {
            return [];
        }

        $raw = trim($raw);
        if ($raw === '') {
            return [];
        }

        // Remove Rp and spaces
        $cleaned = str_ireplace(['rp', ' '], '', $raw);

        $candidates = [];

        $hasComma = str_contains($cleaned, ',');
        $hasDot = str_contains($cleaned, '.');

        if ($hasComma && $hasDot) {
            // Treat as Indonesian style: dots thousand, comma decimal
            $candidate = str_replace('.', '', $cleaned);
            $candidate = str_replace(',', '.', $candidate);
            $candidates[] = (float) $candidate;
        } elseif ($hasComma) {
            // Treat comma as decimal
            $candidates[] = (float) str_replace(',', '.', $cleaned);
        } elseif ($hasDot) {
            // Ambiguous: could be thousand separator or decimal
            $candidates[] = (float) str_replace('.', '', $cleaned); // thousand style
            $candidates[] = (float) $cleaned; // decimal style
        } else {
            $candidates[] = (float) $cleaned;
        }

        // Filter, de-dup, sanity
        $candidates = array_values(array_unique(array_map(function ($v) {
            $v = (float) $v;
            if (!is_finite($v) || $v < 0 || $v > 1000000000) {
                return null;
            }
            return $v;
        }, $candidates)));

        return array_values(array_filter($candidates, fn ($v) => $v !== null));
    }

    private function pickBestPrices(array $buyCandidates, array $sellCandidates, ?float $marginPercent): array
    {
        $best = [
            'buy' => 0.0,
            'sell' => 0.0,
            'note' => null,
        ];

        // If sell is missing but margin exists, derive sell from buy.
        if (empty($sellCandidates) && !empty($buyCandidates) && $marginPercent !== null) {
            $scores = [];
            foreach ($buyCandidates as $buy) {
                if ($buy <= 0) {
                    continue;
                }
                $sell = $buy * (1 + ($marginPercent / 100));
                $scores[] = [
                    'buy' => $buy,
                    'sell' => $sell,
                    'score' => abs($sell - $buy) / max(1, $buy),
                ];
            }
            if (!empty($scores)) {
                usort($scores, fn ($a, $b) => $a['score'] <=> $b['score']);
                $best['buy'] = round($scores[0]['buy'], 2);
                $best['sell'] = round($scores[0]['sell'], 2);
                $best['note'] = 'Sell price derived from margin%';
                return $best;
            }
        }

        // Evaluate pairs
        $pairs = [];
        foreach ($buyCandidates as $buy) {
            foreach ($sellCandidates as $sell) {
                if ($buy <= 0 || $sell <= 0) {
                    continue;
                }

                $score = 0.0;

                if ($sell < $buy) {
                    $score += 100000; // hard penalty
                }

                // Prefer "reasonable" ranges for minimarket
                if ($buy > 1000000) {
                    $score += 5000;
                }
                if ($sell > 2000000) {
                    $score += 5000;
                }

                if ($marginPercent !== null && $buy > 0) {
                    $markup = (($sell - $buy) / $buy) * 100;
                    $score += abs($markup - $marginPercent) * 100;
                }

                // Prefer values that are close to integers for IDR
                $score += (abs($buy - round($buy)) > 0 ? 1 : 0);
                $score += (abs($sell - round($sell)) > 0 ? 1 : 0);

                $pairs[] = compact('buy', 'sell', 'score');
            }
        }

        if (!empty($pairs)) {
            usort($pairs, fn ($a, $b) => $a['score'] <=> $b['score']);
            $best['buy'] = round($pairs[0]['buy'], 2);
            $best['sell'] = round($pairs[0]['sell'], 2);
            return $best;
        }

        // Fallbacks
        $best['buy'] = round($buyCandidates[0] ?? 0, 2);
        $best['sell'] = round($sellCandidates[0] ?? 0, 2);
        return $best;
    }

    private function parseCsvPreview(): void
    {
        $result = $this->parseCsvRows(50);
        $this->headers = $result['headers'];
        $this->previewData = $result['items'];
        $this->importStats['total_rows'] = $result['total_rows'];
        $this->importStats['preview_rows'] = count($this->previewData);
    }

    private function safeNameExists(string $name): bool
    {
        try {
            return Product::whereRaw('LOWER(TRIM(name)) = ?', [mb_strtolower(trim($name))])->exists();
        } catch (\Exception $e) {
            // If DB is not available (local dev), don't block preview.
            return false;
        }
    }

    /**
     * Parse CSV into normalized items.
     * Returns: headers, total_rows (data lines after header), items (limited if $limit provided).
     */
    private function parseCsvRows(?int $limit = null): array
    {
        $path = $this->file->getRealPath();
        $rows = array_map('str_getcsv', file($path));

        if (empty($rows)) {
            $this->importErrors[] = 'File CSV kosong.';
            return ['headers' => [], 'total_rows' => 0, 'items' => []];
        }

        $headerIndex = null;
        foreach ($rows as $index => $row) {
            $first = isset($row[0]) ? trim((string) $row[0]) : '';
            if (strtoupper($first) === 'BARANG') {
                $headerIndex = $index;
                break;
            }
        }

        if ($headerIndex === null) {
            $this->importErrors[] = 'Header tidak ditemukan. Pastikan ada kolom pertama bernama BARANG.';
            return ['headers' => [], 'total_rows' => 0, 'items' => []];
        }

        $rawHeaders = $rows[$headerIndex];
        $headers = array_map(fn ($h) => $this->normalizeHeader((string) $h), $rawHeaders);

        $dataRows = array_slice($rows, $headerIndex + 1);
        $totalRows = count($dataRows);

        $items = [];

        foreach ($dataRows as $row) {
            if ($limit !== null && count($items) >= $limit) {
                break;
            }

            $nonEmpty = array_filter($row, fn ($v) => trim((string) $v) !== '');
            if (empty($nonEmpty)) {
                continue;
            }

            $row = array_pad($row, count($headers), null);
            $row = array_slice($row, 0, count($headers));
            $rowAssoc = array_combine($headers, $row);

            $name = $this->normalizeName((string) ($rowAssoc['BARANG'] ?? ''));
            if ($name === '') {
                continue;
            }

            $rawBuy = (string) ($rowAssoc['MODAL/PCS'] ?? '');
            $rawMargin = (string) ($rowAssoc['MARGIN%'] ?? '');
            $rawSell = (string) ($rowAssoc['HARGAJUAL'] ?? '');
            $rawSeduh = (string) ($rowAssoc['SEDUH'] ?? '');
            $rawInventory = (string) ($rowAssoc['INVENTORY'] ?? '');

            $marginPercent = $this->parsePercent($rawMargin);

            $buyCandidates = $this->moneyCandidates($rawBuy);
            $sellCandidates = $this->moneyCandidates($rawSell);
            $seduhCandidates = $this->moneyCandidates($rawSeduh);

            $picked = $this->pickBestPrices($buyCandidates, $sellCandidates, $marginPercent);
            $sellPrice = $picked['sell'];
            $buyPrice = $picked['buy'];
            $seduhPrice = $seduhCandidates[0] ?? null;

            $stock = (int) preg_replace('/\D+/', '', $rawInventory);

            $description = null;
            if ($seduhPrice !== null && $seduhPrice > 0) {
                $description = 'Harga seduh: Rp ' . number_format($seduhPrice, 0, ',', '.');
            }

            $exists = $this->safeNameExists($name);

            $status = 'OK';
            $warning = null;
            if ($exists) {
                $status = 'DUPLICATE';
            }
            if ($sellPrice <= 0) {
                $status = 'INVALID';
                $warning = 'Harga jual tidak valid.';
            }

            $items[] = [
                'name' => $name,
                'buyPrice' => $buyPrice,
                'sellPrice' => $sellPrice,
                'stock' => $stock,
                'marginPercent' => $marginPercent,
                'description' => $description,
                'status' => $status,
                'warning' => $warning ?? $picked['note'],
                'raw' => [
                    'buy' => $rawBuy,
                    'sell' => $rawSell,
                    'margin' => $rawMargin,
                    'seduh' => $rawSeduh,
                    'inventory' => $rawInventory,
                ],
            ];
        }

        return ['headers' => $headers, 'total_rows' => $totalRows, 'items' => $items];
    }

    public function import(): void
    {
        if (!$this->file) {
            return;
        }

        $this->importStats['success'] = 0;
        $this->importStats['skipped_duplicates'] = 0;
        $this->importStats['failed'] = 0;
        $this->importErrors = [];

        $category = Category::firstOrCreate(
            ['name' => 'Minimarket'],
            [
                'description' => 'Produk minimarket koperasi',
                'icon' => '🛒',
                'order' => 0,
                'isActive' => true,
            ]
        );

        // Re-parse full CSV for import (preview is limited)
        $parsed = $this->parseCsvRows(null);
        $items = $parsed['items'];
        $this->importStats['total_rows'] = $parsed['total_rows'];
        $this->importStats['preview_rows'] = count($this->previewData);

        DB::beginTransaction();
        try {
            foreach ($items as $item) {
                if (($item['status'] ?? '') === 'INVALID') {
                    $this->importStats['failed']++;
                    continue;
                }

                $name = $this->normalizeName((string) ($item['name'] ?? ''));
                if ($name === '') {
                    $this->importStats['failed']++;
                    continue;
                }

                $exists = $this->safeNameExists($name);
                if ($exists) {
                    $this->importStats['skipped_duplicates']++;
                    continue;
                }

                $stock = (int) ($item['stock'] ?? 0);

                $product = Product::create([
                    'name' => $name,
                    'description' => $item['description'] ?? null,
                    'categoryId' => $category->id,
                    'buyPrice' => $item['buyPrice'] ?? 0,
                    'sellPrice' => $item['sellPrice'] ?? 0,
                    'stock' => max(0, $stock),
                    'threshold' => 5,
                    'unit' => 'pcs',
                    'ownershipType' => 'TOKO',
                    'status' => 'ACTIVE',
                    'isConsignment' => false,
                    'isActive' => true,
                    'supplierId' => null,
                    'approvalStatus' => 'APPROVED',
                    'approvedAt' => now(),
                    'approvedBy' => auth()->id(),
                    'lastRestockAt' => $stock > 0 ? now() : null,
                ]);

                if ($stock > 0) {
                    StockMovement::create([
                        'productId' => $product->id,
                        'movementType' => 'RESTOCK',
                        'quantity' => $stock,
                        'note' => 'Initial Stock (CSV Import: Minimarket)',
                        'occurredAt' => now(),
                    ]);
                }

                $this->importStats['success']++;
            }

            DB::commit();

            session()->flash('message', 'Import Minimarket selesai. Sukses: ' . $this->importStats['success'] . ', Skip duplikat: ' . $this->importStats['skipped_duplicates'] . ', Gagal: ' . $this->importStats['failed']);

            $this->file = null;
            $this->previewData = [];

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.minimarket-product-import')->layout('layouts.admin');
    }
}
