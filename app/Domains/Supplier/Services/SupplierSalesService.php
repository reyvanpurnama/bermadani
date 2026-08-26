<?php

namespace App\Domains\Supplier\Services;

use App\Models\Product;
use App\Models\TransactionItem;
use App\Models\AuditRetailProductMapping;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class SupplierSalesService
{
    /**
     * Get combined sales records (POS + Mapped CSV Audit) for a supplier.
     */
    public static function getSalesForSupplier(int $supplierId): Collection
    {
        $products = Product::where('supplierId', $supplierId)->get()->keyBy('id');
        if ($products->isEmpty()) {
            return collect();
        }

        $salesList = collect();

        // 1. Fetch POS transactions from TransactionItem
        $posItems = TransactionItem::whereIn('productId', $products->keys())
            ->with(['transaction'])
            ->get();

        foreach ($posItems as $item) {
            $product = $products[$item->productId] ?? null;
            if (!$product) continue;

            $buyPrice = $product->buyPrice ?? $item->cogsPerUnit ?? 0;
            $dt = $item->transaction?->date ?? $item->created_at;

            $salesList->push((object)[
                'id' => 'pos_' . $item->id,
                'created_at' => $dt instanceof Carbon ? $dt : Carbon::parse($dt),
                'product' => $product,
                'quantity' => $item->quantity,
                'unit_price' => $item->unitPrice,
                'buy_price' => $buyPrice,
                'total_price' => $item->totalPrice,
                'supplier_revenue' => $item->quantity * $buyPrice,
                'source' => 'POS Kasir',
            ]);
        }

        // 2. Fetch Mapped Audit CSV transactions
        $mappings = AuditRetailProductMapping::whereIn('product_id', $products->keys())
            ->pluck('product_id', 'raw_product_name');

        if ($mappings->isNotEmpty()) {
            $files = glob(base_path('docs/data/databulanan/retail_report_*.csv'));
            foreach ($files as $file) {
                if (($h = fopen($file, 'r')) !== false) {
                    fgetcsv($h, 1000, ','); // skip header
                    while (($d = fgetcsv($h, 1000, ',')) !== false) {
                        if (count($d) < 7) continue;
                        $tanggal = trim($d[0] ?? '');
                        $rawName = trim($d[1] ?? '');
                        if (empty($tanggal) || empty($rawName)) continue;
                        if (str_contains(strtolower($tanggal), 'tanggal') || str_contains(strtolower($tanggal), 'total')) continue;
                        if (str_contains(strtolower($rawName), 'total') || str_contains(strtolower($rawName), 'jumlah')) continue;

                        if (isset($mappings[$rawName])) {
                            $productId = $mappings[$rawName];
                            $product = $products[$productId] ?? null;
                            if (!$product) continue;

                            $qty = (int) trim($d[2]);
                            $hbSatuan = self::parseNumber($d[4]);
                            $hjSatuan = self::parseNumber($d[6]);

                            $buyPrice = $hbSatuan > 0 ? $hbSatuan : ($product->buyPrice ?? 0);
                            $dateObj = self::parseDate($tanggal);

                            $salesList->push((object)[
                                'id' => 'csv_' . md5($file . $tanggal . $rawName . $qty),
                                'created_at' => $dateObj,
                                'product' => $product,
                                'quantity' => $qty,
                                'unit_price' => $hjSatuan,
                                'buy_price' => $buyPrice,
                                'total_price' => $qty * $hjSatuan,
                                'supplier_revenue' => $qty * $buyPrice,
                                'source' => 'Laporan Retail CSV',
                            ]);
                        }
                    }
                    fclose($h);
                }
            }
        }

        // Sort by date descending
        return $salesList->sortByDesc(function($item) {
            return $item->created_at ? $item->created_at->timestamp : 0;
        })->values();
    }

    private static function parseDate($val): Carbon
    {
        $val = trim((string)$val);
        try {
            $dateParts = preg_split('/[\/\-\.]/', $val);
            if (count($dateParts) === 3) {
                if (strlen(trim($dateParts[0])) === 4) {
                    $year = (int) trim($dateParts[0]);
                    $month = (int) trim($dateParts[1]);
                    $day = (int) trim($dateParts[2]);
                } else {
                    $day = (int) trim($dateParts[0]);
                    $month = (int) trim($dateParts[1]);
                    $year = (int) trim($dateParts[2]);
                }
                return Carbon::createFromDate($year, $month, $day)->startOfDay();
            }
        } catch (\Exception $e) {}

        return now();
    }

    private static function parseNumber($val): float
    {
        $val = trim((string)$val);
        if ($val === '' || $val === '#N/A' || $val === '-') return 0.0;
        if (str_contains($val, '.') && str_contains($val, ',')) {
            $val = str_replace('.', '', $val);
            $val = str_replace(',', '.', $val);
            return (float) preg_replace('/[^\d\.\-]/', '', $val);
        }
        if (str_contains($val, ',')) {
            $val = str_replace(',', '.', $val);
            return (float) preg_replace('/[^\d\.\-]/', '', $val);
        }
        if (str_contains($val, '.')) {
            $parts = explode('.', $val);
            if (count($parts) > 2) {
                $val = str_replace('.', '', $val);
            } elseif (strlen($parts[1]) === 3 && is_numeric($parts[0]) && (int)$parts[0] > 0 && (int)$parts[0] < 10000) {
                $val = str_replace('.', '', $val);
            }
        }
        return (float) preg_replace('/[^\d\.\-]/', '', $val);
    }
}
