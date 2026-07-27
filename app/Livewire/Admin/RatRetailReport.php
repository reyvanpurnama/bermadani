<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;

class RatRetailReport extends Component
{
    use WithPagination;
    use WithFileUploads;

    public $selectedYear = 'All';
    public $selectedMonth = null;
    public $searchDetail = '';
    public $availableYears = [];
    public $csvFile;
    public $showDeleteConfirmModal = false;
    public $monthlyStats = [];
    
    // Non-paginated month summaries
    public $monthSummaries = [];

    public function mount()
    {
        $this->loadData();
    }

    public function importCsv()
    {
        $this->validate([
            'csvFile' => 'required|file|mimes:csv,txt|max:10240', // 10MB Max
        ]);

        $filePath = $this->csvFile->getRealPath();
        
        // Ensure directory exists
        $dirPath = base_path('docs/data/databulanan');
        if (!file_exists($dirPath)) {
            mkdir($dirPath, 0775, true);
        }

        $header = null;
        $groupedRows = [];

        if (($handle = fopen($filePath, 'r')) !== false) {
            $rawHeader = fgetcsv($handle, 1000, ',');
            
            $is8ColFormat = false;
            if ($rawHeader && count($rawHeader) >= 5) {
                $h0 = strtolower(trim($rawHeader[0] ?? ''));
                $h3 = strtolower(trim($rawHeader[3] ?? ''));
                $h4 = strtolower(trim($rawHeader[4] ?? ''));
                if ($h0 === 'tanggal' && str_contains($h3, 'harga beli') && str_contains($h4, 'harga jual')) {
                    $is8ColFormat = true;
                }
            }

            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                if (count($data) < 5) continue;

                $tanggal = trim($data[0] ?? '');
                $namaBarangCheck = trim($data[1] ?? '');
                if (empty($tanggal) || empty($namaBarangCheck)) continue;
                if (str_contains(strtolower($tanggal), 'tanggal') || str_contains(strtolower($tanggal), 'total')) continue;
                if (str_contains(strtolower($namaBarangCheck), 'total') || str_contains(strtolower($namaBarangCheck), 'jumlah')) continue;

                $dateParts = preg_split('/[\/\-\.]/', $tanggal);
                if (count($dateParts) !== 3) continue;

                if (strlen(trim($dateParts[0])) === 4) {
                    $year = trim($dateParts[0]);
                    $month = str_pad(trim($dateParts[1]), 2, '0', STR_PAD_LEFT);
                } else {
                    $month = str_pad(trim($dateParts[1]), 2, '0', STR_PAD_LEFT);
                    $year = trim($dateParts[2]);
                }
                $monthKey = "$year-$month";

                if ($is8ColFormat) {
                    $namaBarang = trim($data[1]);
                    $qty = (int) trim($data[2]);
                    $hbSatuan = $this->parseNumber($data[3]);
                    $hjSatuan = $this->parseNumber($data[4]);
                    $totalHb = $qty * $hbSatuan;
                    $totalHj = $qty * $hjSatuan;
                    $totalKeuntungan = $totalHj - $totalHb;
                    $normalizedRow = [$tanggal, $namaBarang, $qty, 'Pcs', $hbSatuan, $totalHb, $hjSatuan, $totalKeuntungan];
                } else {
                    $normalizedRow = $data;
                }

                $groupedRows[$monthKey][] = $normalizedRow;
            }
            fclose($handle);
        }

        if (empty($groupedRows)) {
            $this->dispatch('notify', [
                'message' => 'Format file tidak valid atau data transaksi kosong.',
                'type' => 'error',
            ]);
            return;
        }

        $standardHeader = ['Tanggal', 'Nama Barang', 'Qty', 'Satuan', 'Harga Beli Satuan', 'Total Harga Beli', 'Harga Jual Satuan', 'Total Keuntungan'];
        $importedMonths = [];
        foreach ($groupedRows as $monthKey => $rows) {
            $targetFile = "$dirPath/retail_report_$monthKey.csv";
            
            if (($writeHandle = fopen($targetFile, 'w')) !== false) {
                fputcsv($writeHandle, $standardHeader);
                foreach ($rows as $row) {
                    fputcsv($writeHandle, $row);
                }
                fclose($writeHandle);
                $importedMonths[] = $monthKey;
            }
        }

        $this->csvFile = null;
        $this->loadData();

        if (!empty($importedMonths)) {
            $lastMonthKey = end($importedMonths);
            $this->selectedYear = substr($lastMonthKey, 0, 4);
            $this->selectedMonth = $lastMonthKey;
        }

        $monthNames = array_map(function($key) {
            return $this->getMonthName(substr($key, 5, 2)) . ' ' . substr($key, 0, 4);
        }, $importedMonths);

        $this->dispatch('notify', [
            'message' => 'Laporan retail berhasil di-import untuk: ' . implode(', ', $monthNames),
            'type' => 'success',
        ]);
    }

    public function updatedSelectedYear()
    {
        $this->selectedMonth = null;
        $this->resetPage('detailPage');
    }

    public function selectMonth($monthKey)
    {
        $this->selectedMonth = $monthKey;
        $this->resetPage('detailPage');
    }

    public function clearSelectedMonth()
    {
        $this->selectedMonth = null;
        $this->resetPage('detailPage');
    }

    private function parseNumber($val)
    {
        $val = trim((string)$val);
        if ($val === '' || $val === '#N/A' || $val === '-') {
            return 0.0;
        }

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

    private function getMonthName($month)
    {
        $months = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
            '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
            '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember',
        ];
        return $months[$month] ?? $month;
    }

    private function loadData()
    {
        $dirPath = base_path('docs/data/databulanan');
        if (!file_exists($dirPath)) {
            mkdir($dirPath, 0775, true);
        }

        // Check for retail_report_*.csv files
        $files = glob("$dirPath/retail_report_*.csv");

        $years = [];
        $summaries = [];

        foreach ($files as $file) {
            if (($handle = fopen($file, 'r')) !== false) {
                // Skip header
                fgetcsv($handle, 1000, ',');

                while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                    if (count($data) < 7) continue;

                    $tanggal = trim($data[0] ?? '');
                    $namaBarangCheck = trim($data[1] ?? '');
                    if (empty($tanggal) || empty($namaBarangCheck)) continue;
                    if (str_contains(strtolower($tanggal), 'tanggal') || str_contains(strtolower($tanggal), 'total')) continue;
                    if (str_contains(strtolower($namaBarangCheck), 'total') || str_contains(strtolower($namaBarangCheck), 'jumlah')) continue;

                    $dateParts = preg_split('/[\/\-\.]/', $tanggal);
                    if (count($dateParts) !== 3) continue;

                    if (strlen(trim($dateParts[0])) === 4) {
                        $year = trim($dateParts[0]);
                        $month = str_pad(trim($dateParts[1]), 2, '0', STR_PAD_LEFT);
                    } else {
                        $month = str_pad(trim($dateParts[1]), 2, '0', STR_PAD_LEFT);
                        $year = trim($dateParts[2]);
                    }
                    $monthKey = "$year-$month";

                    $quantity = (int) trim($data[2]);
                    $totalHargaBeli = $this->parseNumber($data[5]);
                    $hargaJualSatuan = $this->parseNumber($data[6]);

                    $totalHargaJual = $quantity * $hargaJualSatuan;
                    $totalKeuntungan = $totalHargaJual - $totalHargaBeli;

                    if (!isset($summaries[$monthKey])) {
                        $summaries[$monthKey] = [
                            'month_key' => $monthKey,
                            'year' => $year,
                            'month' => $month,
                            'month_name' => $this->getMonthName($month) . ' ' . $year,
                            'total_harga_beli' => 0.0,
                            'total_harga_jual' => 0.0,
                            'total_keuntungan' => 0.0,
                            'item_count' => 0,
                        ];
                    }

                    $summaries[$monthKey]['total_harga_beli'] += $totalHargaBeli;
                    $summaries[$monthKey]['total_harga_jual'] += $totalHargaJual;
                    $summaries[$monthKey]['total_keuntungan'] += $totalKeuntungan;
                    $summaries[$monthKey]['item_count']++;

                    if (!in_array($year, $years)) {
                        $years[] = $year;
                    }
                }
                fclose($handle);
            }
        }

        // Sort summaries chronologically
        ksort($summaries);
        $this->monthSummaries = array_values($summaries);

        // Sort years descending
        rsort($years);
        $this->availableYears = $years;
    }

    private function splitMasterFile($masterPath, $dirPath)
    {
        $header = null;
        $grouped = [];
        if (($handle = fopen($masterPath, 'r')) !== false) {
            $header = fgetcsv($handle, 1000, ',');
            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                if (count($data) < 8) continue;
                $tanggal = trim($data[0]);
                if (empty($tanggal) || strtolower($tanggal) === 'tanggal') continue;
                $dateParts = explode('/', $tanggal);
                if (count($dateParts) !== 3) continue;

                $month = str_pad(trim($dateParts[1]), 2, '0', STR_PAD_LEFT);
                $year = trim($dateParts[2]);
                $monthKey = "$year-$month";

                $grouped[$monthKey][] = $data;
            }
            fclose($handle);
        }

        foreach ($grouped as $monthKey => $rows) {
            $target = "$dirPath/retail_report_$monthKey.csv";
            if (($write = fopen($target, 'w')) !== false) {
                fputcsv($write, $header);
                foreach ($rows as $row) {
                    fputcsv($write, $row);
                }
                fclose($write);
            }
        }

        if (file_exists($masterPath)) {
            rename($masterPath, $masterPath . '.imported');
        }
    }

    public function getDetailsProperty()
    {
        if (!$this->selectedMonth) {
            return [];
        }

        $filePath = base_path("docs/data/databulanan/retail_report_{$this->selectedMonth}.csv");
        if (!file_exists($filePath)) {
            return [];
        }

        // Fetch mappings
        $mappings = \App\Models\AuditRetailProductMapping::with(['product.supplier'])->get()->keyBy('raw_product_name');

        $details = [];
        $totalQty = 0;
        $totalHpp = 0.0;
        $totalOmzet = 0.0;
        $productAgg = [];
        $supplierAgg = [];

        if (($handle = fopen($filePath, 'r')) !== false) {
            // Skip header
            fgetcsv($handle, 1000, ',');

            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                if (count($data) < 7) continue;

                $tanggal = trim($data[0] ?? '');
                $namaBarang = trim($data[1] ?? '');
                if (empty($tanggal) || empty($namaBarang)) continue;
                if (str_contains(strtolower($tanggal), 'tanggal') || str_contains(strtolower($tanggal), 'total')) continue;
                if (str_contains(strtolower($namaBarang), 'total') || str_contains(strtolower($namaBarang), 'jumlah')) continue;
                $quantity = (int) trim($data[2]);
                $satuan = trim($data[3]);
                $hargaBeliSatuan = $this->parseNumber($data[4]);
                $totalHargaBeli = $this->parseNumber($data[5]);
                $hargaJualSatuan = $this->parseNumber($data[6]);

                $totalHargaJual = $quantity * $hargaJualSatuan;
                $totalKeuntungan = $totalHargaJual - $totalHargaBeli;
                $persentaseKeuntungan = $totalHargaJual > 0 ? ($totalKeuntungan / $totalHargaJual) * 100 : 0;

                $mapped = $mappings[$namaBarang] ?? null;
                $product = $mapped?->product;

                // Aggregations
                $totalQty += $quantity;
                $totalHpp += $totalHargaBeli;
                $totalOmzet += $totalHargaJual;

                // Product Aggregation
                $pName = $product ? $product->name : $namaBarang;
                if (!isset($productAgg[$pName])) {
                    $productAgg[$pName] = [
                        'name' => $pName,
                        'qty' => 0,
                        'profit' => 0.0,
                        'mapped' => $product !== null
                    ];
                }
                $productAgg[$pName]['qty'] += $quantity;
                $productAgg[$pName]['profit'] += $totalKeuntungan;

                // Supplier Aggregation
                $sName = ($product && $product->supplier) ? $product->supplier->businessName : "Belum Terpetakan / Non-Supplier";
                if (!isset($supplierAgg[$sName])) {
                    $supplierAgg[$sName] = [
                        'name' => $sName,
                        'qty' => 0,
                        'profit' => 0.0,
                        'mapped' => ($product && $product->supplier) !== null
                    ];
                }
                $supplierAgg[$sName]['qty'] += $quantity;
                $supplierAgg[$sName]['profit'] += $totalKeuntungan;

                $rowDetail = [
                    'tanggal' => $tanggal,
                    'nama_barang' => $namaBarang,
                    'quantity' => $quantity,
                    'satuan' => $satuan,
                    'harga_beli_satuan' => $hargaBeliSatuan,
                    'total_harga_beli' => $totalHargaBeli,
                    'harga_jual_satuan' => $hargaJualSatuan,
                    'total_harga_jual' => $totalHargaJual,
                    'total_keuntungan' => $totalKeuntungan,
                    'persentase_keuntungan' => $persentaseKeuntungan,
                    'product' => $product,
                ];

                // Apply search filter if present
                if (!empty($this->searchDetail)) {
                    if (strpos(strtolower($namaBarang), strtolower($this->searchDetail)) === false && 
                        ($product === null || strpos(strtolower($product->name), strtolower($this->searchDetail)) === false)) {
                        continue;
                    }
                }

                $details[] = $rowDetail;
            }
            fclose($handle);
        }

        // Sort aggregations by profit desc
        uasort($productAgg, function($a, $b) {
            return $b['profit'] <=> $a['profit'];
        });
        uasort($supplierAgg, function($a, $b) {
            return $b['profit'] <=> $a['profit'];
        });

        // Find top supplier
        $topSupplierName = '-';
        $topSupplierProfit = 0.0;
        foreach ($supplierAgg as $sName => $data) {
            if ($sName !== "Belum Terpetakan / Non-Supplier") {
                $topSupplierName = $sName;
                $topSupplierProfit = $data['profit'];
                break; // Because sorted by profit desc
            }
        }

        $hppRatio = $totalOmzet > 0 ? ($totalHpp / $totalOmzet) * 100 : 0;

        $this->monthlyStats = [
            'total_qty' => $totalQty,
            'top_supplier_name' => $topSupplierName,
            'top_supplier_profit' => $topSupplierProfit,
            'top_products' => array_slice($productAgg, 0, 5),
            'supplier_contributions' => $supplierAgg,
            'hpp_ratio' => $hppRatio,
            'total_omzet' => $totalOmzet,
            'total_keuntungan' => $totalOmzet - $totalHpp,
        ];

        return $details;
    }

    public function render()
    {
        $this->loadData();

        $filteredSummaries = $this->monthSummaries;
        if ($this->selectedYear !== 'All') {
            $filteredSummaries = array_filter($this->monthSummaries, function ($item) {
                return $item['year'] === $this->selectedYear;
            });
            $filteredSummaries = array_values($filteredSummaries);
        }

        if (!empty($filteredSummaries)) {
            $filteredKeys = array_column($filteredSummaries, 'month_key');
            if (!$this->selectedMonth || !in_array($this->selectedMonth, $filteredKeys)) {
                $this->selectedMonth = $filteredKeys[0];
            }
        } else {
            $this->selectedMonth = null;
        }

        $kpi = [
            'total_harga_beli' => 0.0,
            'total_harga_jual' => 0.0,
            'total_keuntungan' => 0.0,
        ];

        foreach ($filteredSummaries as $item) {
            $kpi['total_harga_beli'] += $item['total_harga_beli'];
            $kpi['total_harga_jual'] += $item['total_harga_jual'];
            $kpi['total_keuntungan'] += $item['total_keuntungan'];
        }

        $detailsCollection = $this->getDetailsProperty();
        $totalDetailsCount = count($detailsCollection);
        $perPage = 15;
        $currentPage = $this->getPage('detailPage') ?: 1;
        
        $offset = ($currentPage - 1) * $perPage;
        $paginatedDetails = array_slice($detailsCollection, $offset, $perPage);
        
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedDetails,
            $totalDetailsCount,
            $perPage,
            $currentPage,
            ['path' => url()->current(), 'pageName' => 'detailPage']
        );

        $chartData = [
            'categories' => array_column($filteredSummaries, 'month_name'),
            'hpp' => array_column($filteredSummaries, 'total_harga_beli'),
            'omzet' => array_column($filteredSummaries, 'total_harga_jual'),
            'keuntungan' => array_column($filteredSummaries, 'total_keuntungan'),
        ];

        return view('livewire.admin.rat-retail-report', [
            'summaries' => $filteredSummaries,
            'kpi' => $kpi,
            'paginatedDetails' => $paginator,
            'chartData' => $chartData,
        ])->layout('layouts.admin');
    }

    public function confirmDeleteMonth()
    {
        $this->showDeleteConfirmModal = true;
    }

    public function deleteMonth()
    {
        if (!$this->selectedMonth) {
            return;
        }

        $filePath = base_path("docs/data/databulanan/retail_report_{$this->selectedMonth}.csv");
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $deletedMonthName = $this->getMonthName(substr($this->selectedMonth, 5, 2)) . ' ' . substr($this->selectedMonth, 0, 4);

        $this->selectedMonth = null;
        $this->showDeleteConfirmModal = false;
        $this->loadData();

        $this->dispatch('notify', [
            'message' => "Seluruh data transaksi untuk bulan $deletedMonthName berhasil dihapus.",
            'type' => 'success',
        ]);
    }
}
