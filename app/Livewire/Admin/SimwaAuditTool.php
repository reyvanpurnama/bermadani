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
                $rawAmount = str_replace(['.', 'Rp', ' '], '', $row[3]);
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

        // Members checking - Include both ACTIVE and SUSPENDED (Frozen)
        $members = Member::whereIn('status', ['ACTIVE', 'SUSPENDED'])
            ->where('isMemberKoperasi', true)
            ->get();

        $auditData = [];

        foreach ($members as $member) {
            // Cutoff Date: 1 April 2024
            $cutoffDate = \Carbon\Carbon::create(2024, 4, 1)->startOfMonth();
            $joinDate = \Carbon\Carbon::parse($member->joinDate)->startOfMonth();
            $now = now()->startOfMonth();

            // Part A: Pre-Cutoff (Assumed PAID / MATCH)
            // If joined before cutoff, calculate months until cutoff.
            $preCutoffBalance = 0;
            if ($joinDate->lt($cutoffDate)) {
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

            $expectedAuditTotal = $monthsAudit * 50000;

            // 2. Calculate Actual from CSV (Which are presumably Post-April)
            // STRICT FILTER APPLIED
            $actualAuditTotal = DB::table('audit_simwa_imports')
                ->where('matched_member_id', $member->id)
                ->where(function ($q) {
                    $q->where(function ($sub) {
                        $sub->where('raw_uraian', 'like', '%simwa%')
                            ->where('raw_uraian', 'not like', '%angsuran%')
                            ->where('amount', '<=', 250000);
                    })
                        ->orWhere(function ($sub) {
                            $sub->whereIn('amount', [50000, 100000, 150000, 200000, 250000])
                                ->where('raw_uraian', 'not like', '%angsuran%')
                                ->where('raw_uraian', 'not like', '%panjar%')
                                ->where('raw_uraian', 'not like', '%simpok%');
                        });
                })
                ->sum('amount');

            // 3. Final Reconciliation Logic
            // The "True Balance" should be: Pre-Cutoff (Assumed Paid) + Post-Cutoff (Actual from CSV)
            $proposedBalance = $preCutoffBalance + $actualAuditTotal;

            $currentSystemBalance = $member->simpananWajib;

            // Gap reflects "Did they miss payments in the Audit Period?"
            // OR "Is the System Balance wrong compared to Proposed?"
            // Let's focus on System Integrity -> Gap = Proposed - System.
            $systemDiff = $proposedBalance - $currentSystemBalance;

            // Audit Gap: Did they miss CSV payments? 
            $auditGap = $actualAuditTotal - $expectedAuditTotal;

            $status = 'MATCH';
            if ($auditGap < 0)
                $status = 'ARREARS_DETECTED'; // Detects missing months in CSV
            if ($systemDiff != 0)
                $status = $status == 'MATCH' ? 'BALANCE_MISMATCH' : $status;

            $auditData[] = [
                'member_id' => $member->id,
                'name' => $member->name,
                'join_date' => $member->joinDate,
                'months_audit' => $monthsAudit, // Months we checked CSV for
                'pre_cutoff_balance' => $preCutoffBalance, // Assumed Lunas
                'expected_audit' => $expectedAuditTotal,
                'actual_payroll' => $actualAuditTotal, // From CSV
                'proposed_balance' => $proposedBalance, // The Target Balance
                'current_system' => $currentSystemBalance,
                'gap' => $systemDiff, // For Sync purpose
                'audit_gap' => $auditGap, // For "Nunggak" insight
                'status' => $status
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

            // 1. DELETE ALL OLD SIMWA HISTORY (Nuke)
            \App\Models\SimpananTransaction::where('memberId', $memberId)
                ->where('type', 'WAJIB')
                ->delete();

            $runningBalance = 0;
            $batchInserts = [];

            // =====================================================
            // 2. PRE-APRIL 2024: Generate 50k per month (ASSUMED PAID)
            // =====================================================
            if ($joinDate->lt($cutoffDate)) {
                $currentMonth = $joinDate->copy();

                while ($currentMonth->lt($cutoffDate)) {
                    $runningBalance += 50000;
                    $transactionDate = $currentMonth->copy()->endOfMonth();

                    $batchInserts[] = [
                        'memberId' => $memberId,
                        'type' => 'WAJIB',
                        'transactionType' => 'SETOR',
                        'amount' => 50000,
                        'balanceAfter' => $runningBalance,
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
            $csvRows = DB::table('audit_simwa_imports')
                ->where('matched_member_id', $memberId)
                ->where(function ($q) {
                    $q->where(function ($sub) {
                        $sub->where('raw_uraian', 'like', '%simwa%')
                            ->where('raw_uraian', 'not like', '%angsuran%')
                            ->where('amount', '<=', 250000);
                    })
                        ->orWhere(function ($sub) {
                            $sub->whereIn('amount', [50000, 100000, 150000, 200000, 250000])
                                ->where('raw_uraian', 'not like', '%angsuran%')
                                ->where('raw_uraian', 'not like', '%panjar%')
                                ->where('raw_uraian', 'not like', '%simpok%');
                        });
                })
                ->orderBy('period', 'asc')
                ->get();

            foreach ($csvRows as $row) {
                $runningBalance += $row->amount;
                $date = \Carbon\Carbon::parse($row->period)->endOfMonth()->subDays(2);

                $batchInserts[] = [
                    'memberId' => $memberId,
                    'type' => 'WAJIB',
                    'transactionType' => 'SETOR',
                    'amount' => $row->amount,
                    'balanceAfter' => $runningBalance,
                    'notes' => "Setoran Payroll {$row->period}",
                    'status' => 'APPROVED',
                    'processedBy' => auth()->id(),
                    'created_at' => $date,
                    'updated_at' => $date,
                ];
            }

            // =====================================================
            // 4. BULK INSERT (Much faster than individual inserts)
            // =====================================================
            if (!empty($batchInserts)) {
                // Insert in chunks to avoid memory issues
                foreach (array_chunk($batchInserts, 100) as $chunk) {
                    \App\Models\SimpananTransaction::insert($chunk);
                }
            }

            // 5. Update Final Member Balance
            $member->update(['simpananWajib' => $runningBalance]);
        });

        // Don't call generateReconciliation here to avoid recursion slowdown
    }

    public function syncAll()
    {
        foreach ($this->auditResults as $result) {
            if ($result['proposed_balance'] != $result['current_system']) {
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
