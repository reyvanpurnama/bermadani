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

                $groupedRows[$monthKey][] = $data;
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

        $importedMonths = [];
        foreach ($groupedRows as $monthKey => $rows) {
            $targetFile = "$dirPath/retail_report_$monthKey.csv";
            
            if (($writeHandle = fopen($targetFile, 'w')) !== false) {
                fputcsv($writeHandle, $header);
                foreach ($rows as $row) {
                    fputcsv($writeHandle, $row);
                }
                fclose($writeHandle);
                $importedMonths[] = $monthKey;
            }
        }

        $this->csvFile = null;
        $this->loadData();

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
        $val = trim($val);
        if ($val === '' || $val === '#N/A' || $val === '-') {
            return 0.0;
        }
        // Replace comma decimal with dot
        $val = str_replace(',', '.', $val);
        // Remove everything except numbers, dots, and minus
        $val = preg_replace('/[^\d\.\-]/', '', $val);
        return (float) $val;
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

        // Fallback: split the old master file if it exists and no monthly files exist yet
        $masterFile = base_path('docs/Laporan Keuangan Koperasi UMB - Sheet6.csv');
        if (empty($files) && file_exists($masterFile)) {
            $this->splitMasterFile($masterFile, $dirPath);
            $files = glob("$dirPath/retail_report_*.csv");
        }

        $years = [];
        $summaries = [];

        foreach ($files as $file) {
            if (($handle = fopen($file, 'r')) !== false) {
                // Skip header
                fgetcsv($handle, 1000, ',');

                while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                    if (count($data) < 8) continue;

                    $tanggal = trim($data[0]);
                    if (empty($tanggal) || strtolower($tanggal) === 'tanggal') continue;

                    $dateParts = explode('/', $tanggal);
                    if (count($dateParts) !== 3) continue;

                    $month = str_pad(trim($dateParts[1]), 2, '0', STR_PAD_LEFT);
                    $year = trim($dateParts[2]);
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
                if (count($data) < 8) continue;

                $tanggal = trim($data[0]);
                if (empty($tanggal) || strtolower($tanggal) === 'tanggal') continue;

                $namaBarang = trim($data[1]);
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
