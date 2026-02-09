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

                // Check for Extra Sukarela in Column 4 (Notes)
                // Example: "+ Sukarela 200" or "Tabungan 100"
                $extraSukarelaAmount = 0;
                $extraNote = trim($row[4] ?? '');

                // Create full uraian for display (uraian + notes)
                $fullUraian = $rawUraian;
                if (!empty($extraNote)) {
                    $fullUraian .= ' | ' . $extraNote;
                }

                if (!empty($extraNote) && preg_match('/(sukarela|tabungan)\s*(\+)?\s*(\d+)/i', $extraNote, $matches)) {
                    // Extract number, e.g. 200 -> 200000 (usually abbreviated in thousands if small, or full?)
                    // User said "Sukarela 200" -> 200rb.
                    // Let's assume if < 1000, it's in thousands.
                    $val = (int) $matches[3];
                    if ($val < 1000)
                        $val *= 1000;
                    if ($val < 10000)
                        $val *= 1000; // Double check, usually 200 means 200000.  Minimum deposit usually 10k.

                    $extraSukarelaAmount = $val;
                    // DON'T append to rawUraian - this causes double counting!
                }

                // Auto-match logic ...
                $mapping = DB::table('audit_simwa_name_mappings')->where('raw_name', $rawName)->first();
                $matchedMemberId = $mapping ? $mapping->member_id : null;

                if (!$matchedMemberId) {
                    $member = Member::where('name', 'LIKE', $rawName)->first();
                    if ($member) {
                        $matchedMemberId = $member->id;
                        $this->saveMapping($rawName, $member->id);
                    }
                }

                // ============================================
                // SMART SPLIT LOGIC FOR SIMPOK, SIMWA, TABUNGAN
                // ============================================

                $lowerUraian = strtolower($rawUraian);
                $lowerNote = strtolower($extraNote);
                $combinedText = $lowerUraian . ' ' . $lowerNote;

                // Skip Angsuran rows (loan repayments) - they are handled separately
                $isAngsuran = str_contains($combinedText, 'angsuran') || str_contains($combinedText, 'angs');

                // Detect patterns
                $hasSimpok = str_contains($combinedText, 'simpok');
                $hasSimwa = preg_match('/\bsimwa\b/i', $combinedText);
                $hasTabungan = str_contains($combinedText, 'tabungan') || str_contains($combinedText, 'tab+');

                // Initialize split amounts
                $splitSimpok = 0;
                $splitSimwa = 0;
                $splitSukarela = 0;
                $mainRecordAmount = $rawAmount;
                $mainRecordUraian = $rawUraian;

                if (!$isAngsuran) {

                    // PATTERN 1: "simpok+simwa" or "simpok simwa" = 250k (200k Simpok + 50k Simwa)
                    if ($hasSimpok && $hasSimwa && !$hasTabungan && $rawAmount >= 250000) {
                        $splitSimpok = 200000;
                        $splitSimwa = 50000;
                        $splitSukarela = $rawAmount - 250000; // Any excess
                        $mainRecordAmount = 0; // All split out
                    }

                    // PATTERN 2: "tab+simpok+simwa" = 450k (200k Tabungan + 200k Simpok + 50k Simwa)
                    elseif ($hasTabungan && $hasSimpok && $hasSimwa && $rawAmount >= 450000) {
                        $splitSimpok = 200000;
                        $splitSimwa = 50000;
                        $splitSukarela = $rawAmount - 250000; // Remaining is Tabungan/Sukarela (200k for 450k input)
                        $mainRecordAmount = 0; // All split out
                    }

                    // PATTERN 3: Pure "simpokX" cicilan (e.g. simpok1, simpok 2, Simpok3)
                    // These are 50k installments of Simpanan Pokok
                    elseif ($hasSimpok && !$hasSimwa && !$hasTabungan && preg_match('/simpok\s*\d/i', $combinedText)) {
                        // Check if it's a double payment like "simpok 1,2" = 100k
                        if (preg_match('/simpok\s*\d\s*,\s*\d/i', $combinedText)) {
                            $splitSimpok = $rawAmount; // Full amount is Simpok (usually 100k for 2 installments)
                        } else {
                            $splitSimpok = $rawAmount; // Full amount is Simpok (usually 50k for 1 installment)
                        }
                        $mainRecordAmount = 0;
                    }

                    // PATTERN 4: Pure "simwa" only (no simpok)
                    // If amount > 50k, split: 50k Simwa + excess to Sukarela
                    elseif ($hasSimwa && !$hasSimpok && !$hasTabungan) {
                        if ($rawAmount > 50000) {
                            $splitSimwa = 50000;
                            $splitSukarela = $rawAmount - 50000;
                            $mainRecordAmount = 0;
                        } else {
                            $splitSimwa = $rawAmount;
                            $mainRecordAmount = 0;
                        }
                    }

                    // PATTERN 5: Pure "Tabungan" only
                    // All goes to Sukarela
                    elseif ($hasTabungan && !$hasSimpok && !$hasSimwa) {
                        $splitSukarela = $rawAmount;
                        $mainRecordAmount = 0;
                    }
                } else {
                    // =============================================
                    // PATTERN 6: ANGSURAN - Always includes Simwa 50k!
                    // Total angsuran amount INCLUDES: Simwa 50k + optional Sukarela
                    // E.g. "Angsuran 12, 1.383.350, + Sukarela 100"
                    //      = Simwa 50k + Sukarela 100k + Angsuran sisa (IGNORED)
                    // =============================================

                    // ALWAYS extract Simwa 50k from Angsuran rows
                    $splitSimwa = 50000;

                    // Check for Sukarela - PRIORITIZE notes column, then uraian
                    // Don't combine both to avoid double-counting same info
                    if ($extraSukarelaAmount > 0) {
                        // Use notes column value (already calculated above)
                        $splitSukarela = $extraSukarelaAmount;
                        $extraSukarelaAmount = 0; // Don't add again in extra record
                    } elseif (preg_match('/(sukarela|tabungan)\s*(\+)?\s*(\d+)/i', $rawUraian, $uraianMatch)) {
                        // Fallback to uraian column if notes is empty
                        $val = (int) $uraianMatch[3];
                        // Convert abbreviated amounts: 100 -> 100,000
                        if ($val < 1000)
                            $val *= 1000;
                        if ($val < 10000)
                            $val *= 1000;
                        $splitSukarela = $val;
                    }

                    // Check for EXTRA Simwa in uraian (rare case: "Angsuran 5+Simwa 2" = 2x 50k)
                    // Pattern: "simwa" with number > 1 means multiple months
                    if (preg_match('/simwa\s*(\d+)/i', $rawUraian, $simwaMatch)) {
                        $simwaCount = (int) $simwaMatch[1];
                        if ($simwaCount > 1) {
                            $splitSimwa = $simwaCount * 50000; // Override with explicit count
                        }
                    }

                    // Main Angsuran amount stays full (will be marked as IGNORED in display)
                }

                // ============================================
                // INSERT RECORDS
                // ============================================

                // Main record (for unhandled/Angsuran cases, or remaining amount)
                if ($mainRecordAmount > 0 || (!$hasSimpok && !$hasSimwa && !$hasTabungan)) {
                    $batchData[] = [
                        'filename' => $filename,
                        'period' => $period,
                        'raw_name' => $rawName,
                        'raw_uraian' => $fullUraian,
                        'amount' => $mainRecordAmount > 0 ? $mainRecordAmount : $rawAmount,
                        'matched_member_id' => $matchedMemberId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                // SIMPOK Record
                if ($splitSimpok > 0) {
                    $batchData[] = [
                        'filename' => $filename,
                        'period' => $period,
                        'raw_name' => $rawName,
                        'raw_uraian' => 'AUTO-SPLIT SIMPOK: ' . $fullUraian,
                        'amount' => $splitSimpok,
                        'matched_member_id' => $matchedMemberId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                // SIMWA Record
                if ($splitSimwa > 0) {
                    $batchData[] = [
                        'filename' => $filename,
                        'period' => $period,
                        'raw_name' => $rawName,
                        'raw_uraian' => 'AUTO-SPLIT SIMWA: ' . $fullUraian,
                        'amount' => $splitSimwa,
                        'matched_member_id' => $matchedMemberId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                // SUKARELA/TABUNGAN Record
                if ($splitSukarela > 0) {
                    $batchData[] = [
                        'filename' => $filename,
                        'period' => $period,
                        'raw_name' => $rawName,
                        'raw_uraian' => 'AUTO-SPLIT SUKARELA: ' . $fullUraian,
                        'amount' => $splitSukarela,
                        'matched_member_id' => $matchedMemberId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                // Extra Sukarela from Notes Column (e.g. "+ Sukarela 200")
                if ($extraSukarelaAmount > 0) {
                    $batchData[] = [
                        'filename' => $filename,
                        'period' => $period,
                        'raw_name' => $rawName,
                        'raw_uraian' => 'AUTO-DETECT EXTRA: ' . $extraNote,
                        'amount' => $extraSukarelaAmount,
                        'matched_member_id' => $matchedMemberId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

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
        $this->dispatch('notify', ['type' => 'success', 'message' => 'File berhasil diimport dan data lama (jika ada) untuk bulan tersebut telah diganti.']);
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
        $this->dispatch('notify', ['type' => 'success', 'message' => "Data periode $period berhasil dihapus."]);
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

        $this->dispatch('notify', ['type' => 'success', 'message' => "Berhasil mapping: $rawName"]);
    }

    // Reconciliation Data
    public $auditResults = [];
    public $filterStatus = 'all'; // all, match, mismatch
    public $excludedMemberIds = []; // IDs to skip during cleanup
    public $processWajib = true;
    public $processSukarela = true;

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
                            ->orWhere('raw_uraian', 'like', '%Sukarela%')
                            ->orWhere('raw_uraian', 'like', '%AUTO-SPLIT SIMWA%')
                            ->orWhere('raw_uraian', 'like', '%AUTO-SPLIT SUKARELA%'); // Catch tabungan from combo splits
                    })
                    ->where('raw_uraian', 'not like', '%Angsuran%')
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

    public function syncBalance($memberId, $processWajib = null, $processSukarela = null, $silent = false)
    {
        // Use class properties as default if arguments are not provided (e.g. from single button click)
        $processWajib = $processWajib ?? $this->processWajib;
        $processSukarela = $processSukarela ?? $this->processSukarela;

        $result = collect($this->auditResults)->firstWhere('member_id', $memberId);
        if (!$result)
            return;

        DB::transaction(function () use ($result, $memberId, $processWajib, $processSukarela) {
            $member = Member::find($memberId);

            // CUTOFF: April 1, 2024
            $cutoffDate = \Carbon\Carbon::create(2024, 4, 1)->startOfMonth();
            $joinDate = \Carbon\Carbon::parse($member->joinDate)->startOfMonth();

            // 1. DELETE logic based on flags
            $typesToDelete = [];
            if ($processWajib)
                $typesToDelete[] = 'WAJIB';
            if ($processSukarela)
                $typesToDelete[] = 'SUKARELA';

            if (!empty($typesToDelete)) {
                // EXCEPTION: Jangan hapus transaksi yang berhubungan dengan 'Angsuran' atau 'Pinjaman'
                \App\Models\SimpananTransaction::where('memberId', $memberId)
                    ->whereIn('type', $typesToDelete)
                    ->where('notes', 'not like', '%Angsuran%')
                    ->where('notes', 'not like', '%Pinjaman%')
                    ->delete();
            }

            $batchInserts = [];
            $runningWajib = 0;
            $runningSukarela = 0;

            // 2. PRE-APRIL 2024: Generate 50k per month (ASSUMED PAID)
            if ($processWajib && $member->isMemberKoperasi && $joinDate->lt($cutoffDate)) {
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

            // 3. POST-APRIL 2024: Use CSV Data (Detailed per period)
            $memberPeriods = DB::table('audit_simwa_imports')
                ->where('matched_member_id', $memberId)
                ->select('period')
                ->distinct()
                ->orderBy('period', 'asc')
                ->get();

            foreach ($memberPeriods as $mp) {
                // Ensure date is 29th or End of Month (for Feb)
                $periodDate = \Carbon\Carbon::parse($mp->period);
                $day = min(29, $periodDate->daysInMonth);
                $date = $periodDate->copy()->setDay($day)->endOfDay();

                if ($processWajib) {
                    $wRows = DB::table('audit_simwa_imports')
                        ->where('matched_member_id', $memberId)
                        ->where('period', $mp->period)
                        ->where('raw_uraian', 'like', '%simwa%')
                        ->get();

                    $wAmount = 0;
                    $isAutoCredit = false;

                    if ($wRows->count() > 0) {
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
                }

                if ($processSukarela) {
                    $sRows = DB::table('audit_simwa_imports')
                        ->where('matched_member_id', $memberId)
                        ->where('period', $mp->period)
                        ->where(function ($q) {
                            $q->where('raw_uraian', 'like', '%Tabungan%')
                                ->orWhere('raw_uraian', 'like', '%Sukarela%')
                                ->orWhere('raw_uraian', 'like', '%AUTO-SPLIT SIMWA%')
                                ->orWhere('raw_uraian', 'like', '%AUTO-SPLIT SUKARELA%'); // Catch tabungan from combo splits
                        })
                        ->where('raw_uraian', 'not like', '%Angsuran%') // SAFETY: Prevent counting Angsuran rows that mention Sukarela in notes
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
            }

            if (!empty($batchInserts)) {
                foreach (array_chunk($batchInserts, 100) as $chunk) {
                    \App\Models\SimpananTransaction::insert($chunk);
                }
            }

            $updates = [];
            if ($processWajib)
                $updates['simpananWajib'] = $runningWajib;
            if ($processSukarela)
                $updates['simpananSukarela'] = $runningSukarela;

            if (!empty($updates)) {
                $member->update($updates);
            }
        });

        if (!$silent) {
            $this->dispatch('notify', ['type' => 'success', 'message' => "History member {$memberId} berhasil di-rebuild!"]);
        }
    }

    public function syncAll()
    {
        foreach ($this->auditResults as $result) {
            if ($result['proposed_wajib'] != $result['current_wajib'] || abs($result['gap_sukarela']) > 0) {
                $this->syncBalance($result['member_id']);
            }
        }
        $this->dispatch('notify', ['type' => 'success', 'message' => "Semua member berhasil disinkronisasi dengan data Payroll!"]);
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
                // Check if member is excluded
                if (in_array($result['member_id'], $this->excludedMemberIds)) {
                    continue;
                }

                try {
                    // Pass silent=true specifically for bulk operation
                    $this->syncBalance($result['member_id'], $this->processWajib, $this->processSukarela, true);
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
                $this->dispatch('notify', ['type' => 'warning', 'message' => "⚠️ Cleanup selesai: {$count} berhasil, {$errors} gagal."]);
            } else {
                $this->dispatch('notify', ['type' => 'success', 'message' => "✅ CLEANUP SELESAI! {$count} member history berhasil di-rebuild dengan detail bulanan."]);
            }

        } catch (\Exception $e) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
            \Log::error("Cleanup failed: " . $e->getMessage());
        }
    }

    // Detail Modal Logic
    public $showDetailModal = false;
    public $detailMember = null;
    public $detailRows = [];

    public function openDetailModal($memberId)
    {
        $this->detailMember = Member::find($memberId);
        if (!$this->detailMember)
            return;

        // Fetch raw rows
        $rawRows = DB::table('audit_simwa_imports')
            ->where('matched_member_id', $memberId)
            ->orderBy('period', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        // Consolidate rows by period + original uraian (group splits into single row)
        $consolidated = [];
        foreach ($rawRows as $row) {
            // Extract original uraian (remove AUTO-SPLIT prefix)
            $originalUraian = $row->raw_uraian;
            if (preg_match('/^AUTO-[A-Z\s]+:\s*(.+)$/i', $row->raw_uraian, $m)) {
                $originalUraian = $m[1];
            }

            // Create unique key for grouping
            $key = $row->period . '|' . $row->raw_name . '|' . $originalUraian;

            if (!isset($consolidated[$key])) {
                $consolidated[$key] = (object) [
                    'period' => $row->period,
                    'raw_name' => $row->raw_name,
                    'original_uraian' => $originalUraian,
                    'total_amount' => 0,
                    'simpok' => 0,
                    'simwa' => 0,
                    'sukarela' => 0,
                    'ignored' => 0,
                    'is_ignored' => false,
                ];
            }

            $entry = $consolidated[$key];

            // Categorize and sum amounts
            if (str_contains($row->raw_uraian, 'AUTO-SPLIT SIMPOK')) {
                $entry->simpok += $row->amount;
            } elseif (str_contains($row->raw_uraian, 'AUTO-SPLIT SIMWA')) {
                $entry->simwa += $row->amount;
            } elseif (str_contains($row->raw_uraian, 'AUTO-SPLIT SUKARELA') || str_contains($row->raw_uraian, 'AUTO-DETECT EXTRA')) {
                $entry->sukarela += $row->amount;
            } elseif (str_contains(strtolower($row->raw_uraian), 'angsuran') || str_contains(strtolower($row->raw_uraian), 'angs ')) {
                $entry->ignored += $row->amount;
                $entry->is_ignored = true;
            } elseif (preg_match('/\bsimwa\b/i', $row->raw_uraian)) {
                $entry->simwa += $row->amount;
            } elseif (str_contains(strtolower($row->raw_uraian), 'tabungan') || str_contains(strtolower($row->raw_uraian), 'sukarela')) {
                $entry->sukarela += $row->amount;
            }

            $entry->total_amount += $row->amount;
            $consolidated[$key] = $entry;
        }

        $this->detailRows = collect(array_values($consolidated));
        $this->showDetailModal = true;
    }

    public function closeDetailModal()
    {
        $this->showDetailModal = false;
        $this->detailMember = null;
        $this->detailRows = [];
    }

    private function saveMapping($rawName, $memberId)
    {
        DB::table('audit_simwa_name_mappings')->updateOrInsert(
            ['raw_name' => $rawName],
            ['member_id' => $memberId, 'updated_at' => now()]
        );
    }
}
