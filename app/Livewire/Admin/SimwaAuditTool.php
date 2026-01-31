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

    protected $listeners = ['audit:member-mapped' => 'handleMemberMapped'];

    public function handleMemberMapped($data)
    {
        $this->matchManual($data['rawName'], $data['memberId']);
    }

    public function render()
    {
        $stats = [
            'total_imports' => DB::table('audit_simwa_imports')->count(),
            'unprocessed' => DB::table('audit_simwa_imports')->whereNull('matched_member_id')->count(),
            'processed' => DB::table('audit_simwa_imports')->whereNotNull('matched_member_id')->count(),
        ];

        // Paginating unmapped distinct names
        $unmappedNames = DB::table('audit_simwa_imports')
            ->select('raw_name')
            ->whereNull('matched_member_id')
            ->distinct()
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

        // Members checking
        $members = Member::where('status', 'ACTIVE')
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

            // 1. Create adjustment transaction
            // We overwrite the balance to match PROPOSED BALANCE (Pre-Cutoff Assumed + Post-Cutoff CSV)
            $newBalance = $result['proposed_balance'];
            $difference = $newBalance - $member->simpananWajib;

            if ($difference != 0) {
                \App\Models\SimpananTransaction::create([
                    'memberId' => $memberId,
                    'type' => 'WAJIB', // Adjusted to match Model convention if applicable, or generic string
                    'amount' => abs($difference),
                    'transactionType' => $difference > 0 ? 'SETOR' : 'TARIK', // CREDIT idx -> SETOR
                    'balanceAfter' => $newBalance, // REQUIRED FIELD
                    'notes' => 'Audit Correction: Pre-April Assumed + Post-April CSV',
                    'status' => 'APPROVED', // Assuming direct approval for system sync
                    'processedBy' => auth()->id()
                ]);

                // 2. Update Member Balance
                $member->update(['simpananWajib' => $newBalance]);
            }
        });

        session()->flash('message', "Saldo member {$result['name']} berhasil disinkronisasi!");
        $this->generateReconciliation(); // Refresh data
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

    private function saveMapping($rawName, $memberId)
    {
        DB::table('audit_simwa_name_mappings')->updateOrInsert(
            ['raw_name' => $rawName],
            ['member_id' => $memberId, 'updated_at' => now()]
        );
    }
}
