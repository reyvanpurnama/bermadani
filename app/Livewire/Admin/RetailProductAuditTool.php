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

        $destinationPath = base_path('docs/Laporan Keuangan Koperasi UMB - Sheet6.csv');

        // Ensure directory exists
        if (!file_exists(dirname($destinationPath))) {
            mkdir(dirname($destinationPath), 0775, true);
        }

        // Copy/overwrite the uploaded file
        copy($this->csvFile->getRealPath(), $destinationPath);

        $this->csvFile = null;

        session()->flash('message', 'Laporan CSV retail berhasil di-import.');
        $this->activeTab = 'mapping';

        // Auto-run auto-map after upload
        $this->autoMap();
    }

    public function autoMap()
    {
        $filePath = base_path('docs/Laporan Keuangan Koperasi UMB - Sheet6.csv');
        if (!file_exists($filePath)) {
            return;
        }

        $rawNames = [];
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

    public function getCsvProducts()
    {
        $filePath = base_path('docs/Laporan Keuangan Koperasi UMB - Sheet6.csv');
        if (!file_exists($filePath)) {
            return [];
        }

        $rawNames = [];
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
        return array_unique($rawNames);
    }

    public function render()
    {
        $filePath = base_path('docs/Laporan Keuangan Koperasi UMB - Sheet6.csv');
        $csvExists = file_exists($filePath);
        $fileStats = [];

        if ($csvExists) {
            $fileStats = [
                'size' => filesize($filePath),
                'updated_at' => filemtime($filePath),
            ];
        }

        $csvProducts = $this->getCsvProducts();
        $totalCsvProducts = count($csvProducts);

        // Fetch mappings
        $mappings = AuditRetailProductMapping::with(['product.supplier'])->get()->keyBy('raw_product_name');
        
        $mappedCount = 0;
        $unmappedCount = 0;
        $mappingList = [];

        foreach ($csvProducts as $rawName) {
            $mapped = isset($mappings[$rawName]) ? $mappings[$rawName] : null;
            if ($mapped && $mapped->product_id) {
                $mappedCount++;
            } else {
                $unmappedCount++;
            }

            if ($this->searchMapping === '' || strpos(strtolower($rawName), strtolower($this->searchMapping)) !== false) {
                $mappingList[] = [
                    'raw_name' => $rawName,
                    'mapping' => $mapped,
                ];
            }
        }

        // Preview Table Data
        $previewRows = [];
        if ($this->activeTab === 'preview' && $csvExists) {
            if (($handle = fopen($filePath, 'r')) !== false) {
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

        return view('livewire.admin.retail-product-audit-tool', [
            'csvExists' => $csvExists,
            'fileStats' => $fileStats,
            'totalCsvProducts' => $totalCsvProducts,
            'mappedCount' => $mappedCount,
            'unmappedCount' => $unmappedCount,
            'mappingList' => $mappingList,
            'previewRows' => $previewRows,
        ])->layout('layouts.admin');
    }
}
