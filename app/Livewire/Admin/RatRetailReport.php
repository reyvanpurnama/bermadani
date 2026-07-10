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

        $destinationPath = base_path('docs/Laporan Keuangan Koperasi UMB - Sheet6.csv');
        
        // Ensure directory exists
        if (!file_exists(dirname($destinationPath))) {
            mkdir(dirname($destinationPath), 0775, true);
        }

        // Copy/overwrite the uploaded file
        copy($this->csvFile->getRealPath(), $destinationPath);

        $this->csvFile = null;
        $this->loadData();

        $this->dispatch('notify', [
            'message' => 'Laporan CSV retail berhasil di-import dan diperbarui.',
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
        $filePath = base_path('docs/Laporan Keuangan Koperasi UMB - Sheet6.csv');
        if (!file_exists($filePath)) {
            $this->monthSummaries = [];
            $this->availableYears = [];
            return;
        }

        $years = [];
        $summaries = [];

        if (($handle = fopen($filePath, 'r')) !== false) {
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
                $hargaBeliSatuan = $this->parseNumber($data[4]);
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

        // Sort summaries chronologically
        ksort($summaries);
        $this->monthSummaries = array_values($summaries);

        // Sort years descending
        rsort($years);
        $this->availableYears = $years;
    }

    public function getDetailsProperty()
    {
        if (!$this->selectedMonth) {
            return [];
        }

        $filePath = base_path('docs/Laporan Keuangan Koperasi UMB - Sheet6.csv');
        if (!file_exists($filePath)) {
            return [];
        }

        $details = [];

        if (($handle = fopen($filePath, 'r')) !== false) {
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

                if ($monthKey !== $this->selectedMonth) {
                    continue;
                }

                $namaBarang = trim($data[1]);
                $quantity = (int) trim($data[2]);
                $satuan = trim($data[3]);
                $hargaBeliSatuan = $this->parseNumber($data[4]);
                $totalHargaBeli = $this->parseNumber($data[5]);
                $hargaJualSatuan = $this->parseNumber($data[6]);

                $totalHargaJual = $quantity * $hargaJualSatuan;
                $totalKeuntungan = $totalHargaJual - $totalHargaBeli;
                $persentaseKeuntungan = $totalHargaJual > 0 ? ($totalKeuntungan / $totalHargaJual) * 100 : 0;

                // Apply search filter if present
                if (!empty($this->searchDetail)) {
                    if (strpos(strtolower($namaBarang), strtolower($this->searchDetail)) === false) {
                        continue;
                    }
                }

                $details[] = [
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
                ];
            }
            fclose($handle);
        }

        return $details;
    }

    public function render()
    {
        // Reload fresh summaries list (just in case)
        $this->loadData();

        $filteredSummaries = $this->monthSummaries;
        if ($this->selectedYear !== 'All') {
            $filteredSummaries = array_filter($this->monthSummaries, function ($item) {
                return $item['year'] === $this->selectedYear;
            });
            $filteredSummaries = array_values($filteredSummaries);
        }

        // Auto-select first month of the filtered list if current selectedMonth is not in the filtered list
        if (!empty($filteredSummaries)) {
            $filteredKeys = array_column($filteredSummaries, 'month_key');
            if (!$this->selectedMonth || !in_array($this->selectedMonth, $filteredKeys)) {
                $this->selectedMonth = $filteredKeys[0];
            }
        } else {
            $this->selectedMonth = null;
        }

        // Calculate KPI summaries
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

        // Paginate the details table manually from the collection
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

        return view('livewire.admin.rat-retail-report', [
            'summaries' => $filteredSummaries,
            'kpi' => $kpi,
            'paginatedDetails' => $paginator,
        ])->layout('layouts.admin');
    }
}
