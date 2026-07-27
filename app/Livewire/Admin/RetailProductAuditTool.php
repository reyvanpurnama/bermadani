<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use App\Models\Product;
use App\Models\AuditRetailProductMapping;
use Illuminate\Support\Facades\DB;

class RetailProductAuditTool extends Component
{
    use WithFileUploads;
    use WithPagination;

    public $csvFile;
    public $activeTab = 'upload'; // upload, mapping, preview
    public $searchMapping = '';
    public $filterStatus = 'all'; // all, mapped, unmapped
    public $filterCategory = 'all'; // all, unmapped, or categoryId
    public $sortBy = 'name_asc'; // name_asc, name_desc, category_asc, supplier_asc

    protected $listeners = ['audit:product-mapped' => 'handleProductMapped'];

    public function handleProductMapped($data)
    {
        $rawName = $data['rawName'];
        $productId = $data['productId'];

        AuditRetailProductMapping::updateOrCreate(
            ['raw_product_name' => $rawName],
            ['product_id' => $productId]
        );

        session()->flash('message', "Produk '$rawName' berhasil dipetakan.");
    }

    public function unmapProduct($rawName)
    {
        AuditRetailProductMapping::where('raw_product_name', $rawName)->delete();
        session()->flash('message', "Pemetaan untuk '$rawName' telah dihapus.");
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

                $tanggal = trim($data[0]);
                if (empty($tanggal) || strtolower($tanggal) === 'tanggal') continue;

                $dateParts = explode('/', $tanggal);
                if (count($dateParts) !== 3) continue;

                $month = str_pad(trim($dateParts[1]), 2, '0', STR_PAD_LEFT);
                $year = trim($dateParts[2]);
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
            session()->flash('error', 'Format file tidak valid atau data transaksi kosong.');
            return;
        }

        $standardHeader = ['Tanggal', 'Nama Barang', 'Qty', 'Satuan', 'Harga Beli Satuan', 'Total Harga Beli', 'Harga Jual Satuan', 'Total Keuntungan'];
        foreach ($groupedRows as $monthKey => $rows) {
            $targetFile = "$dirPath/retail_report_$monthKey.csv";
            
            if (($writeHandle = fopen($targetFile, 'w')) !== false) {
                fputcsv($writeHandle, $standardHeader);
                foreach ($rows as $row) {
                    fputcsv($writeHandle, $row);
                }
                fclose($writeHandle);
            }
        }

        $this->csvFile = null;

        session()->flash('message', 'Laporan CSV retail berhasil di-import dan dipisahkan per bulan.');
        $this->activeTab = 'mapping';

        // Auto-run auto-map after upload
        $this->autoMap();
    }

    public function autoMap()
    {
        $files = $this->getCsvFiles();
        if (empty($files)) {
            return;
        }

        $rawNames = [];
        foreach ($files as $filePath) {
            if (($handle = fopen($filePath, 'r')) !== false) {
                // Skip header
                fgetcsv($handle, 1000, ',');

                while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                    if (count($data) < 2) continue;
                    $name = trim($data[1]);
                    if (!empty($name) && strtolower($name) !== 'nama barang') {
                        $rawNames[] = $name;
                    }
                }
                fclose($handle);
            }
        }
        $rawNames = array_unique($rawNames);

        $mappedCount = 0;
        foreach ($rawNames as $rawName) {
            // Skip if already mapped
            if (AuditRetailProductMapping::where('raw_product_name', $rawName)->exists()) {
                continue;
            }

            // Try direct case-insensitive match
            $product = Product::whereRaw('LOWER(TRIM(name)) = ?', [strtolower(trim($rawName))])->first();
            if ($product) {
                AuditRetailProductMapping::create([
                    'raw_product_name' => $rawName,
                    'product_id' => $product->id
                ]);
                $mappedCount++;
            }
        }

        if ($mappedCount > 0) {
            session()->flash('message', "Sistem berhasil mencocokkan otomatis $mappedCount produk dengan nama yang sama.");
        }
    }

    private function parseNumber($val)
    {
        $val = trim($val);
        if ($val === '' || $val === '#N/A' || $val === '-') {
            return 0.0;
        }
        $val = str_replace(',', '.', $val);
        $val = preg_replace('/[^\d\.\-]/', '', $val);
        return (float) $val;
    }

    private function getCsvFiles()
    {
        $dirPath = base_path('docs/data/databulanan');
        if (!file_exists($dirPath)) {
            mkdir($dirPath, 0775, true);
        }

        $files = glob("$dirPath/retail_report_*.csv");

        return $files;
    }

    public function getCsvProducts()
    {
        $files = $this->getCsvFiles();
        $rawNames = [];
        foreach ($files as $filePath) {
            if (($handle = fopen($filePath, 'r')) !== false) {
                fgetcsv($handle, 1000, ',');
                while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                    if (count($data) < 2) continue;
                    $name = trim($data[1]);
                    if (!empty($name) && strtolower($name) !== 'nama barang') {
                        $rawNames[] = $name;
                    }
                }
                fclose($handle);
            }
        }
        return array_unique($rawNames);
    }

    public function render()
    {
        $files = $this->getCsvFiles();
        $csvExists = !empty($files);
        $fileStats = [];

        if ($csvExists) {
            // Stats of the most recently updated file
            $latestMtime = 0;
            $totalSize = 0;
            foreach ($files as $file) {
                $mtime = filemtime($file);
                if ($mtime > $latestMtime) {
                    $latestMtime = $mtime;
                }
                $totalSize += filesize($file);
            }
            $fileStats = [
                'size' => $totalSize,
                'updated_at' => $latestMtime,
            ];
        }

        $csvProducts = [];
        $csvPrices = []; // raw_name => ['beli' => [], 'jual' => []]

        if ($csvExists) {
            foreach ($files as $file) {
                if (($handle = fopen($file, 'r')) !== false) {
                    // Skip header
                    fgetcsv($handle, 1000, ',');
                    while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                        if (count($data) < 7) continue;
                        $rawName = trim($data[1]);
                        if (empty($rawName) || strtolower($rawName) === 'nama barang') continue;

                        $csvProducts[] = $rawName;

                        $beli = $this->parseNumber($data[4]);
                        $jual = $this->parseNumber($data[6]);

                        if (!isset($csvPrices[$rawName])) {
                            $csvPrices[$rawName] = [
                                'beli' => [],
                                'jual' => []
                            ];
                        }
                        if ($beli > 0) {
                            $csvPrices[$rawName]['beli'][] = $beli;
                        }
                        if ($jual > 0) {
                            $csvPrices[$rawName]['jual'][] = $jual;
                        }
                    }
                    fclose($handle);
                }
            }
        }
        $csvProducts = array_unique($csvProducts);

        foreach ($csvPrices as $rName => $prices) {
            $csvPrices[$rName]['beli'] = array_values(array_unique($prices['beli']));
            sort($csvPrices[$rName]['beli']);
            $csvPrices[$rName]['jual'] = array_values(array_unique($prices['jual']));
            sort($csvPrices[$rName]['jual']);
        }

        $totalCsvProducts = count($csvProducts);

        // Fetch mappings
        $mappings = AuditRetailProductMapping::with(['product.supplier', 'product.category'])->get()->keyBy('raw_product_name');
        
        $mappedCount = 0;
        $unmappedCount = 0;
        $mappingList = [];

        foreach ($csvProducts as $rawName) {
            $mapped = isset($mappings[$rawName]) ? $mappings[$rawName] : null;
            $product = $mapped?->product;
            $categoryName = $product?->category?->name ?? 'Belum Terhubung / Non-Kategori';
            $supplierName = $product?->supplier?->businessName ?? 'TOKO (Koperasi)';

            if ($mapped && $mapped->product_id) {
                $mappedCount++;
            } else {
                $unmappedCount++;
            }

            // 1. Search Filter
            if ($this->searchMapping !== '') {
                $search = strtolower($this->searchMapping);
                $matchesRaw = strpos(strtolower($rawName), $search) !== false;
                $matchesProduct = $product && strpos(strtolower($product->name), $search) !== false;
                $matchesSupplier = $product && strpos(strtolower($supplierName), $search) !== false;

                if (!$matchesRaw && !$matchesProduct && !$matchesSupplier) {
                    continue;
                }
            }

            // 2. Status Filter
            if ($this->filterStatus === 'mapped' && (!$mapped || !$mapped->product_id)) {
                continue;
            }
            if ($this->filterStatus === 'unmapped' && ($mapped && $mapped->product_id)) {
                continue;
            }

            // 3. Category Filter
            if ($this->filterCategory !== 'all') {
                if ($this->filterCategory === 'unmapped') {
                    if ($product && $product->categoryId) {
                        continue;
                    }
                } else {
                    if (!$product || $product->categoryId != $this->filterCategory) {
                        continue;
                    }
                }
            }

            $mappingList[] = [
                'raw_name' => $rawName,
                'mapping' => $mapped,
                'product' => $product,
                'category_name' => $categoryName,
                'supplier_name' => $supplierName,
                'prices' => $csvPrices[$rawName] ?? ['beli' => [], 'jual' => []],
            ];
        }

        // 4. Sorting
        usort($mappingList, function($a, $b) {
            switch ($this->sortBy) {
                case 'name_asc':
                    return strcasecmp($a['raw_name'], $b['raw_name']);
                case 'name_desc':
                    return strcasecmp($b['raw_name'], $a['raw_name']);
                case 'category_asc':
                    $cmp = strcasecmp($a['category_name'], $b['category_name']);
                    return $cmp !== 0 ? $cmp : strcasecmp($a['raw_name'], $b['raw_name']);
                case 'supplier_asc':
                    $cmp = strcasecmp($a['supplier_name'], $b['supplier_name']);
                    return $cmp !== 0 ? $cmp : strcasecmp($a['raw_name'], $b['raw_name']);
                default:
                    return 0;
            }
        });

        $categories = \App\Models\Category::orderBy('name')->get();

        // Preview Table Data
        $previewRows = [];
        if ($this->activeTab === 'preview' && $csvExists) {
            foreach ($files as $file) {
                if (($handle = fopen($file, 'r')) !== false) {
                    fgetcsv($handle, 1000, ',');
                    while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                        if (count($data) < 8) continue;
                        $tanggal = trim($data[0]);
                        if (empty($tanggal) || strtolower($tanggal) === 'tanggal') continue;

                        $rawName = trim($data[1]);
                        $quantity = (int) trim($data[2]);
                        $satuan = trim($data[3]);
                        $hargaBeliSatuan = $this->parseNumber($data[4]);
                        $totalHargaBeli = $this->parseNumber($data[5]);
                        $hargaJualSatuan = $this->parseNumber($data[6]);

                        $totalHargaJual = $quantity * $hargaJualSatuan;
                        $totalKeuntungan = $totalHargaJual - $totalHargaBeli;
                        $persentaseKeuntungan = $totalHargaJual > 0 ? ($totalKeuntungan / $totalHargaJual) * 100 : 0;

                        $mapped = $mappings[$rawName] ?? null;

                        $previewRows[] = [
                            'tanggal' => $tanggal,
                            'raw_name' => $rawName,
                            'quantity' => $quantity,
                            'satuan' => $satuan,
                            'harga_beli_satuan' => $hargaBeliSatuan,
                            'total_harga_beli' => $totalHargaBeli,
                            'harga_jual_satuan' => $hargaJualSatuan,
                            'total_harga_jual' => $totalHargaJual,
                            'total_keuntungan' => $totalKeuntungan,
                            'persentase_keuntungan' => $persentaseKeuntungan,
                            'product' => $mapped?->product,
                        ];
                    }
                    fclose($handle);
                }
            }
        }

        return view('livewire.admin.retail-product-audit-tool', [
            'csvExists' => $csvExists,
            'fileStats' => $fileStats,
            'totalCsvProducts' => $totalCsvProducts,
            'mappedCount' => $mappedCount,
            'unmappedCount' => $unmappedCount,
            'mappingList' => $mappingList,
            'previewRows' => $previewRows,
            'categories' => $categories,
        ])->layout('layouts.admin');
    }
}
