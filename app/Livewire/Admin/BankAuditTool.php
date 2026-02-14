<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use App\Models\BankTransaction;
use App\Models\AuditBankImport;
use App\Models\AuditBankCategoryRule;
use Carbon\Carbon;

class BankAuditTool extends Component
{
    use WithFileUploads;
    use WithPagination;

    public $csvFiles = [];
    public $activeTab = 'upload'; // upload, review, rules, sync
    
    // Review filters
    public $filterType = 'all'; // all, INCOME, EXPENSE
    public $filterCategory = '';
    public $filterPeriod = '';
    public $searchKeterangan = '';

    // Cleanup/Sync
    public $syncProgress = 0;
    public $syncStatus = '';
    public $isProcessing = false;

    protected $listeners = ['refreshComponent' => '$refresh'];

    public function render()
    {
        $stats = [
            'total_imports' => AuditBankImport::count(),
            'unreviewed' => AuditBankImport::where('is_reviewed', false)->count(),
            'unsynced' => AuditBankImport::where('is_synced', false)->count(),
            'total_income' => AuditBankImport::where('detected_type', 'INCOME')->sum('kredit'),
            'total_expense' => AuditBankImport::where('detected_type', 'EXPENSE')->sum('debet'),
        ];

        // Get imported periods for upload tab
        $importedPeriods = DB::table('audit_bank_imports')
            ->select('period', 'filename', DB::raw('count(*) as total_rows'), DB::raw('sum(kredit) as total_kredit'), DB::raw('sum(debet) as total_debet'), DB::raw('max(created_at) as imported_at'))
            ->groupBy('period', 'filename')
            ->orderBy('period', 'desc')
            ->get();

        // Get data for review tab with pagination
        $reviewQuery = AuditBankImport::query();

        if ($this->filterType !== 'all') {
            $reviewQuery->where('detected_type', $this->filterType);
        }

        if ($this->filterCategory) {
            $reviewQuery->where('detected_category', $this->filterCategory);
        }

        if ($this->filterPeriod) {
            $reviewQuery->where('period', $this->filterPeriod);
        }

        if ($this->searchKeterangan) {
            $reviewQuery->where('keterangan', 'like', '%' . $this->searchKeterangan . '%');
        }

        $reviewData = $reviewQuery->orderBy('transaction_date', 'desc')
            ->orderBy('transaction_time', 'desc')
            ->paginate(50);

        // Get available categories for filter
        $categories = AuditBankImport::select('detected_category')
            ->distinct()
            ->whereNotNull('detected_category')
            ->orderBy('detected_category')
            ->pluck('detected_category');

        // Get available periods for filter
        $periods = AuditBankImport::select('period')
            ->distinct()
            ->orderBy('period', 'desc')
            ->pluck('period');

        // Get category rules
        $categoryRules = AuditBankCategoryRule::orderBy('priority', 'desc')->get();

        return view('livewire.admin.bank-audit-tool', [
            'stats' => $stats,
            'importedPeriods' => $importedPeriods,
            'reviewData' => $reviewData,
            'categories' => $categories,
            'periods' => $periods,
            'categoryRules' => $categoryRules,
        ])->layout('layouts.admin');
    }

    public function processUploads()
    {
        $this->validate([
            'csvFiles.*' => 'required|file|mimes:csv,txt|max:20480', // 20MB max
        ]);

        foreach ($this->csvFiles as $file) {
            $filename = $file->getClientOriginalName();

            // Parse period from filename
            $period = $this->parsePeriodFromFilename($filename);
            if (!$period) {
                $period = 'unknown-' . time();
            }

            // Clear existing data for this period
            AuditBankImport::where('period', $period)->delete();

            $path = $file->getRealPath();
            $data = array_map('str_getcsv', file($path));

            // Remove header
            if (isset($data[0]) && (str_contains(strtolower($data[0][0]), 'tgl'))) {
                array_shift($data);
            }

            $batchData = [];
            foreach ($data as $row) {
                if (count($row) < 6) continue;

                $tgl = trim($row[0]);
                $waktu = trim($row[1]);
                $keterangan = trim($row[2]);
                $debet = floatval(str_replace([',', ' '], '', $row[3]));
                $kredit = floatval(str_replace([',', ' '], '', $row[4]));
                $saldo = floatval(str_replace([',', ' '], '', $row[5]));

                if (empty($keterangan)) continue;

                $transactionDate = $this->parseTransactionDate($tgl, $period);
                $transactionTime = $this->parseTransactionTime($waktu);

                // Auto-categorize
                $match = AuditBankCategoryRule::matchKeterangan($keterangan);
                
                $detectedType = null;
                $detectedCategory = null;

                if ($match) {
                    $detectedType = $match['type'];
                    $detectedCategory = $match['category'];
                } else {
                    if ($kredit > 0) {
                        $detectedType = 'INCOME';
                        $detectedCategory = 'Lainnya';
                    } elseif ($debet > 0) {
                        $detectedType = 'EXPENSE';
                        $detectedCategory = 'Lainnya';
                    }
                }

                $batchData[] = [
                    'filename' => $filename,
                    'period' => $period,
                    'transaction_date' => $transactionDate,
                    'transaction_time' => $transactionTime,
                    'keterangan' => $keterangan,
                    'debet' => $debet,
                    'kredit' => $kredit,
                    'saldo' => $saldo,
                    'detected_type' => $detectedType,
                    'detected_category' => $detectedCategory,
                    'is_reviewed' => false,
                    'is_synced' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                if (count($batchData) >= 500) {
                    DB::table('audit_bank_imports')->insert($batchData);
                    $batchData = [];
                }
            }

            if (!empty($batchData)) {
                DB::table('audit_bank_imports')->insert($batchData);
            }
        }

        $this->reset('csvFiles');
        $this->dispatch('notify', ['type' => 'success', 'message' => 'File berhasil diimport!']);
    }

    private function parsePeriodFromFilename($filename)
    {
        $lower = strtolower($filename);
        preg_match('/20\d{2}/', $lower, $matches);
        $year = $matches[0] ?? null;
        if (!$year) return null;

        $months = [
            'januari' => '01', 'februari' => '02', 'maret' => '03', 'april' => '04',
            'mei' => '05', 'juni' => '06', 'juli' => '07', 'agustus' => '08',
            'september' => '09', 'oktober' => '10', 'november' => '11', 'desember' => '12',
        ];

        foreach ($months as $name => $code) {
            if (str_contains($lower, $name)) {
                return "$year-$code";
            }
        }
        return null;
    }

    private function parseTransactionDate($tgl, $period)
    {
        if (preg_match('/(\d{1,2})\/(\d{1,2})/', $tgl, $matches)) {
            $day = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
            $month = str_pad($matches[2], 2, '0', STR_PAD_LEFT);
            $year = substr($period, 0, 4);
            return "$year-$month-$day";
        }
        return null;
    }

    private function parseTransactionTime($waktu)
    {
        if (preg_match('/(\d{1,2}):(\d{2})/', $waktu, $matches)) {
            $hour = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
            $minute = $matches[2];
            return "$hour:$minute:00";
        }
        return '00:00:00';
    }

    public function deletePeriod($period)
    {
        AuditBankImport::where('period', $period)->delete();
        $this->dispatch('notify', ['type' => 'success', 'message' => "Data periode $period berhasil dihapus."]);
    }

    public function markAllAsReviewed()
    {
        AuditBankImport::where('is_reviewed', false)->update(['is_reviewed' => true]);
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Semua data ditandai sebagai reviewed.']);
    }

    public function syncToBank()
    {
        $this->isProcessing = true;
        $imports = AuditBankImport::where('is_synced', false)->get();
        $total = $imports->count();
        $processed = 0;

        foreach ($imports as $import) {
            $bankTransaction = BankTransaction::create([
                'transaction_date' => $import->transaction_date,
                'transaction_time' => $import->transaction_time,
                'description' => $import->final_description,
                'debit' => $import->debet,
                'credit' => $import->kredit,
                'balance' => $import->saldo,
                'type' => $import->final_type,
                'category' => $import->final_category,
                'period' => $import->period,
                'source_file' => $import->filename,
            ]);

            $import->update([
                'is_synced' => true,
                'synced_bank_transaction_id' => $bankTransaction->id,
            ]);

            $processed++;
            $this->syncProgress = round(($processed / $total) * 100);
        }

        $this->isProcessing = false;
        $this->dispatch('notify', ['type' => 'success', 'message' => "$total transaksi berhasil disinkronkan."]);
    }
}
