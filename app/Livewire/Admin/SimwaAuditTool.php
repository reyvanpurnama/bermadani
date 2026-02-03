<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use App\Models\Member;

class SimwaAuditTool extends Component
{
    use WithFileUploads;
    use WithPagination;

    public $csvFiles = [];
    public $processingReport = [];
    public $activeTab = 'upload'; // upload, mapping, preview

    // Mapping Data
    public $unmappedEntries = [];
    public $searchMember = '';

    // Progress Tracking
    public $cleanupProgress = 0;
    public $cleanupStatus = '';
    public $isProcessing = false;

    protected $listeners = ['audit:member-mapped' => 'handleMemberMapped'];

    public function handleMemberMapped($data)
    {
        $this->matchManual($data['rawName'], $data['memberId']);
    }

    public function render()
    {
        $stats = [
            'total_imports' => DB::table('audit_simwa_imports')->count(),
            'unprocessed' => DB::table('audit_simwa_imports')->whereNull('matched_member_id')->distinct('raw_name')->count('raw_name'),
            'processed' => DB::table('audit_simwa_imports')->whereNotNull('matched_member_id')->distinct('matched_member_id')->count('matched_member_id'),
        ];

        // Paginating unmapped distinct names with their earliest appearance
        $unmappedNames = DB::table('audit_simwa_imports')
            ->select('raw_name', DB::raw('MIN(period) as earliest_period'))
            ->whereNull('matched_member_id')
            ->groupBy('raw_name')
            ->orderBy('raw_name')
            ->paginate(10);

        return view('livewire.admin.simwa-audit-tool', [
            'stats' => $stats,
            'unmappedNames' => $unmappedNames
        ])->layout('layouts.admin');
    }

    public function processUploads()
    {
        $this->validate([
            'csvFiles.*' => 'required|file|mimes:csv,txt|max:10240', // 10MB max
        ]);

        foreach ($this->csvFiles as $file) {
            $filename = $file->getClientOriginalName();

            // 1. Strict Period Parsing (YYYY-MM)
            $period = $this->parsePeriodFromFilename($filename);
            if (!$period) {
                // Fallback / Warning? For now use filename
                $period = 'unknown-' . time();
            }

            // 2. Clear existing data for this period (Prevent Duplicates)
            // If user uploads "April 2024" again, we assume they want to REPLACE the old version.
            DB::table('audit_simwa_imports')->where('period', $period)->delete();

            $path = $file->getRealPath();
            $data = array_map('str_getcsv', file($path));

            // Remove header
            if (isset($data[0]) && (str_contains(strtolower($data[0][0]), 'no') || str_contains(strtolower($data[0][1]), 'nama'))) {
                array_shift($data);
            }

            $batchData = [];
            foreach ($data as $row) {
                if (count($row) < 4)
                    continue;
                $rawName = trim($row[1]);
                if (empty($rawName))
                    continue;

                $rawUraian = $row[2] ?? '';
                $rawAmount = str_replace([',', '.', 'Rp', ' '], '', $row[3]);
                $rawAmount = is_numeric($rawAmount) ? $rawAmount : 0;

                // Auto-match
                $mapping = DB::table('audit_simwa_name_mappings')->where('raw_name', $rawName)->first();
                $matchedMemberId = $mapping ? $mapping->member_id : null;

                if (!$matchedMemberId) {
                    $member = Member::where('name', 'LIKE', $rawName)->first();
                    if ($member) {
                        $matchedMemberId = $member->id;
                        $this->saveMapping($rawName, $member->id);
                    }
                }

                $batchData[] = [
                    'filename' => $filename,
                    'period' => $period,
                    'raw_name' => $rawName,
                    'raw_uraian' => $rawUraian,
                    'amount' => $rawAmount,
                    'matched_member_id' => $matchedMemberId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                // Chunk insert to avoid memory issues
                if (count($batchData) >= 100) {
                    DB::table('audit_simwa_imports')->insert($batchData);
                    $batchData = [];
                }
            }

            if (!empty($batchData)) {
                DB::table('audit_simwa_imports')->insert($batchData);
            }
        }

        $this->reset('csvFiles');
        session()->flash('message', 'File berhasil diimport dan data lama (jika ada) untuk bulan tersebut telah diganti.');
        $this->activeTab = 'upload'; // Stay on upload to see list
    }

    private function parsePeriodFromFilename($filename)
    {
        // Expected: "04-april-2024.csv" or "agustus-2024.csv"
        $lower = strtolower($filename);

        // Extract Year (4 digits)
        preg_match('/20\d{2}/', $lower, $matches);
        $year = $matches[0] ?? null;
        if (!$year)
            return null;

        // Extract Month
        $months = [
            'januari' => '01',
            'februari' => '02',
            'maret' => '03',
            'april' => '04',
            'mei' => '05',
            'juni' => '06',
            'juli' => '07',
            'agustus' => '08',
            'september' => '09',
            'oktober' => '10',
            'november' => '11',
            'desember' => '12',
            'jan' => '01',
            'feb' => '02',
            'mar' => '03',
            'apr' => '04',
            'jun' => '06',
            'jul' => '07',
            'agu' => '08',
            'sep' => '09',
            'okt' => '10',
            'nov' => '11',
            'des' => '12'
        ];

        foreach ($months as $name => $code) {
            if (str_contains($lower, $name)) {
                return "$year-$code";
            }
        }

        return null;
    }

    public function deletePeriod($period)
    {
        DB::table('audit_simwa_imports')->where('period', $period)->delete();
        session()->flash('message', "Data periode $period berhasil dihapus.");
    }

    public function getImportedPeriodsProperty()
    {
        return DB::table('audit_simwa_imports')
            ->select('period', 'filename', DB::raw('count(*) as total_rows'), DB::raw('sum(amount) as total_amount'), DB::raw('max(created_at) as imported_at'))
            ->groupBy('period', 'filename')
            ->orderBy('period', 'desc')
            ->get();
    }

    public function matchManual($rawName, $memberId)
    {
        if (!$memberId)
            return;

        // 1. Save to Knowledge Base (Name Mappings)
        $this->saveMapping($rawName, $memberId);

        // 2. Update All existing audit rows with this raw_name
        DB::table('audit_simwa_imports')
            ->where('raw_name', $rawName)
            ->update(['matched_member_id' => $memberId]);

        session()->flash('message', "Berhasil mapping: $rawName");
    }

    // Reconciliation Data
    public $auditResults = [];
    public $filterStatus = 'all'; // all, match, mismatch

    public function generateReconciliation()
    {
        $this->activeTab = 'reconciliation';

        // Members checking:
        // 1. Regular Cooperative Members (ACTIVE/SUSPENDED)
        // 2. Retail Members (isMemberKoperasi=0) but ONLY if they have data in our audit imports
        $members = Member::where(function ($query) {
            $query->where('isMemberKoperasi', true)
                ->whereIn('status', ['ACTIVE', 'SUSPENDED']);
        })
            ->orWhere(function ($query) {
                $query->where('isMemberKoperasi', false)
                    ->whereIn('id', DB::table('audit_simwa_imports')->pluck('matched_member_id'));
            })
            ->get();

        $auditData = [];

        foreach ($members as $member) {
            // Cutoff Date: 1 April 2024
            $cutoffDate = \Carbon\Carbon::create(2024, 4, 1)->startOfMonth();
            $joinDate = \Carbon\Carbon::parse($member->joinDate)->startOfMonth();
            $now = now()->startOfMonth();

            // Part A: Pre-Cutoff (Assumed PAID / MATCH)
            // Only applies to Cooperative Members
            $preCutoffBalance = 0;
            if ($member->isMemberKoperasi && $joinDate->lt($cutoffDate)) {
                // diffInMonths is basically (Year2 - Year1) * 12 + (Month2 - Month1)
                $monthsPre = $joinDate->diffInMonths($cutoffDate);
                // e.g. Join Jan, Cutoff April. Diff = 3 (Jan, Feb, Mar). Correct.
                $preCutoffBalance = $monthsPre * 50000;
            }

            // Part B: Post-Cutoff (The Audit Period)
            // Start counting expected from MAX(JoinDate, Cutoff)
            $auditStartDate = $joinDate->gt($cutoffDate) ? $joinDate : $cutoffDate;

            $monthsAudit = 0;
            if ($auditStartDate->lte($now)) {
                $monthsAudit = $auditStartDate->diffInMonths($now) + 1; // Inclusive current month
            }

            $expectedAuditTotal = ($member->isMemberKoperasi) ? ($monthsAudit * 50000) : 0;

            // 2. Calculate Actual from CSV (Post-April)
            $memberPeriods = DB::table('audit_simwa_imports')
                ->where('matched_member_id', $member->id)
                ->select('period')
                ->distinct()
                ->get();

            $actualWajibTotal = 0;
            $actualSukarelaTotal = 0;

            foreach ($memberPeriods as $mp) {
                // 1. Mandatory (Wajib) Logic
                $specificSimwa = DB::table('audit_simwa_imports')
                    ->where('matched_member_id', $member->id)
                    ->where('period', $mp->period)
                    ->where('raw_uraian', 'like', '%simwa%')
                    ->sum('amount');

                // 2. Voluntary (Sukarela/Tabungan) Logic
                $specificSukarela = DB::table('audit_simwa_imports')
                    ->where('matched_member_id', $member->id)
                    ->where('period', $mp->period)
                    ->where(function ($q) {
                        $q->where('raw_uraian', 'like', '%Tabungan%')
                            ->orWhere('raw_uraian', 'like', '%Sukarela%');
                    })
                    ->sum('amount');

                if ($specificSimwa > 0) {
                    $actualWajibTotal += 50000; // Force 50k standard (User Request: ignore CSV nominal for Simwa)
                } elseif ($member->isMemberKoperasi) {
                    $actualWajibTotal += 50000;
                }

                if ($specificSukarela > 0) {
                    $actualSukarelaTotal += $specificSukarela;
                }
            }

            // Get mapped CSV names
            $mappedNames = DB::table('audit_simwa_imports')
                ->where('matched_member_id', $member->id)
                ->pluck('raw_name')
                ->unique()
                ->values()
                ->toArray();

            // 3. Final Reconciliation Logic
            $proposedWajib = $preCutoffBalance + $actualWajibTotal;
            $currentWajib = $member->simpananWajib;
            $wajibGap = $proposedWajib - $currentWajib;

            $currentSukarela = $member->simpananSukarela ?? 0;
            $sukarelaGap = $actualSukarelaTotal - $currentSukarela;

            $auditGapWajib = $actualWajibTotal - $expectedAuditTotal;

            $status = 'MATCH';
            if ($auditGapWajib < 0)
                $status = 'ARREARS_DETECTED';
            if ($wajibGap != 0 || abs($sukarelaGap) > 100)
                $status = ($status == 'MATCH' ? 'BALANCE_MISMATCH' : $status);

            $auditData[] = [
                'member_id' => $member->id,
                'name' => $member->name,
                'is_coop' => $member->isMemberKoperasi,
                'join_date' => $member->joinDate,
                'months_audit' => $monthsAudit,
                'pre_cutoff_balance' => $preCutoffBalance,
                'expected_audit' => $expectedAuditTotal,
                'actual_payroll' => $actualWajibTotal,
                'actual_sukarela' => $actualSukarelaTotal,
                'proposed_wajib' => $proposedWajib,
                'current_wajib' => $currentWajib,
                'current_sukarela' => $currentSukarela,
                'gap' => $wajibGap,
                'gap_sukarela' => $sukarelaGap,
                'audit_gap' => $auditGapWajib,
                'status' => $status,
                'mapped_names' => $mappedNames,
            ];
        }

        $this->auditResults = $auditData;
    }

    public function syncBalance($memberId)
    {
        $result = collect($this->auditResults)->firstWhere('member_id', $memberId);
        if (!$result)
            return;

        DB::transaction(function () use ($result, $memberId) {
            $member = Member::find($memberId);

            // CUTOFF: April 1, 2024
            $cutoffDate = \Carbon\Carbon::create(2024, 4, 1)->startOfMonth();
            $joinDate = \Carbon\Carbon::parse($member->joinDate)->startOfMonth();

            // 1. DELETE ALL OLD SIMWA & SUKARELA HISTORY (Nuke for full rebuild)
            \App\Models\SimpananTransaction::where('memberId', $memberId)
                ->whereIn('type', ['WAJIB', 'SUKARELA'])
                ->delete();

            $runningWajib = 0;
            $runningSukarela = 0;
            $batchInserts = [];

            // =====================================================
            // 2. PRE-APRIL 2024: Generate 50k per month (ASSUMED PAID)
            // Only for Coop Members
            // =====================================================
            if ($member->isMemberKoperasi && $joinDate->lt($cutoffDate)) {
                $currentMonth = $joinDate->copy();

                while ($currentMonth->lt($cutoffDate)) {
                    $runningWajib += 50000;
                    $transactionDate = $currentMonth->copy()->endOfMonth();

                    $batchInserts[] = [
                        'memberId' => $memberId,
                        'type' => 'WAJIB',
                        'transactionType' => 'SETOR',
                        'amount' => 50000,
                        'balanceAfter' => $runningWajib,
                        'notes' => 'Simpanan Wajib ' . $currentMonth->translatedFormat('F Y'),
                        'status' => 'APPROVED',
                        'processedBy' => auth()->id(),
                        'created_at' => $transactionDate,
                        'updated_at' => $transactionDate,
                    ];

                    $currentMonth->addMonth();
                }
            }

            // =====================================================
            // 3. POST-APRIL 2024: Use CSV Data (Detailed per period)
            // =====================================================
            $memberPeriods = DB::table('audit_simwa_imports')
                ->where('matched_member_id', $memberId)
                ->select('period')
                ->distinct()
                ->orderBy('period', 'asc')
                ->get();

            foreach ($memberPeriods as $mp) {
                $date = \Carbon\Carbon::parse($mp->period)->endOfMonth()->subDays(2);

                // --- A. Handle WAJIB ---
                $wRows = DB::table('audit_simwa_imports')
                    ->where('matched_member_id', $memberId)
                    ->where('period', $mp->period)
                    ->where('raw_uraian', 'like', '%simwa%')
                    ->get();

                $wAmount = 0;
                $isAutoCredit = false;

                if ($wRows->count() > 0) {
                    // NOMINAL CSV DIABAIKAN SESUAI REQUEST USER
                    // Isu Elma Mutiara: Payroll "Simpok+Simwa" 250.000, tapi sistem harus catat Simwa 50.000
                    // Jadi kita force 50.000 jika ada entry simwa.
                    $wAmount = 50000;
                } elseif ($member->isMemberKoperasi) {
                    $wAmount = 50000;
                    $isAutoCredit = true;
                }

                if ($wAmount > 0) {
                    $runningWajib += $wAmount;
                    $batchInserts[] = [
                        'memberId' => $memberId,
                        'type' => 'WAJIB',
                        'transactionType' => 'SETOR',
                        'amount' => $wAmount,
                        'balanceAfter' => $runningWajib,
                        'notes' => ($isAutoCredit ? "Setoran Payroll (via Angsuran/Other) " : "Setoran Payroll ") . $mp->period,
                        'status' => 'APPROVED',
                        'processedBy' => auth()->id(),
                        'created_at' => $date,
                        'updated_at' => $date,
                    ];
                }

                // --- B. Handle SUKARELA ---
                $sRows = DB::table('audit_simwa_imports')
                    ->where('matched_member_id', $memberId)
                    ->where('period', $mp->period)
                    ->where(function ($q) {
                        $q->where('raw_uraian', 'like', '%Tabungan%')
                            ->orWhere('raw_uraian', 'like', '%Sukarela%');
                    })
                    ->get();

                foreach ($sRows as $sRow) {
                    $runningSukarela += $sRow->amount;
                    $batchInserts[] = [
                        'memberId' => $memberId,
                        'type' => 'SUKARELA',
                        'transactionType' => 'SETOR',
                        'amount' => $sRow->amount,
                        'balanceAfter' => $runningSukarela,
                        'notes' => "Setoran Payroll (Sukarela/Tabungan) " . $mp->period,
                        'status' => 'APPROVED',
                        'processedBy' => auth()->id(),
                        'created_at' => $date,
                        'updated_at' => $date,
                    ];
                }
            }

            // =====================================================
            // 4. BULK INSERT
            // =====================================================
            if (!empty($batchInserts)) {
                foreach (array_chunk($batchInserts, 100) as $chunk) {
                    \App\Models\SimpananTransaction::insert($chunk);
                }
            }

            // 5. Update Final Member Balances
            $member->update([
                'simpananWajib' => $runningWajib,
                'simpananSukarela' => $runningSukarela
            ]);
        });
    }

    public function syncAll()
    {
        foreach ($this->auditResults as $result) {
            if ($result['proposed_wajib'] != $result['current_wajib'] || abs($result['gap_sukarela']) > 0) {
                $this->syncBalance($result['member_id']);
            }
        }
        session()->flash('message', "Semua member berhasil disinkronisasi dengan data Payroll!");
    }

    public function cleanupAllSimwa()
    {
        try {
            // Step 1: Generate reconciliation
            $this->generateReconciliation();

            $total = count($this->auditResults);
            if ($total === 0) {
                session()->flash('error', 'Tidak ada member untuk diproses.');
                return;
            }

            // Step 2: Rebuild each member
            $count = 0;
            $errors = 0;
            foreach ($this->auditResults as $result) {
                try {
                    $this->syncBalance($result['member_id']);
                    $count++;
                } catch (\Exception $e) {
                    \Log::error("Failed to sync member {$result['member_id']}: " . $e->getMessage());
                    $errors++;
                }
            }

            // Step 3: Refresh reconciliation to show updated data
            $this->generateReconciliation();

            // Show result
            if ($errors > 0) {
                session()->flash('message', "⚠️ Cleanup selesai: {$count} berhasil, {$errors} gagal.");
            } else {
                session()->flash('message', "✅ CLEANUP SELESAI! {$count} member history berhasil di-rebuild dengan detail bulanan.");
            }

        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
            \Log::error("Cleanup failed: " . $e->getMessage());
        }
    }

    private function saveMapping($rawName, $memberId)
    {
        DB::table('audit_simwa_name_mappings')->updateOrInsert(
            ['raw_name' => $rawName],
            ['member_id' => $memberId, 'updated_at' => now()]
        );
    }
}
