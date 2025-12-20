<?php

namespace App\Services;

use App\Models\Member;
use App\Models\SimpananTransaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MemberService
{
    /**
     * Create new member
     *
     * @param array $data
     * @return Member
     * @throws \Exception
     */
    public function createMember(array $data): Member
    {
        return DB::transaction(function () use ($data) {
            // Generate unique nomor anggota
            $nomorAnggota = Member::generateNomorAnggota();

            // Create or link to user account
            if (isset($data['createNewUser']) && $data['createNewUser']) {
                $user = User::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'username' => $data['username'],
                    'password' => Hash::make($data['password']),
                    'role' => 'member',
                    'isActive' => true,
                ]);
                $userId = $user->id;
            } else {
                $userId = $data['userId'];
            }

            // Create member
            $member = Member::create([
                'userId' => $userId,
                'nomorAnggota' => $nomorAnggota,
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'address' => $data['address'] ?? null,
                'gender' => $data['gender'],
                'unitKerja' => $data['unitKerja'],
                'joinDate' => now(),
                'status' => 'ACTIVE',
                'isMemberKoperasi' => $data['isMemberKoperasi'] ?? true,
                'simpananPokok' => 0,
                'simpananWajib' => 0,
                'simpananSukarela' => 0,
                'points' => 0,
                'tier' => 'BRONZE',
                'totalSpent' => 0,
            ]);

            // Add initial simpanan if provided
            if (isset($data['simpananPokok']) && $data['simpananPokok'] > 0) {
                $this->addSimpanan(
                    $member->id,
                    'POKOK',
                    $data['simpananPokok'],
                    'Simpanan pokok awal',
                    $data['buktiPokokPath'] ?? null,
                    auth()->id()
                );
            }

            if (isset($data['simpananWajib']) && $data['simpananWajib'] > 0) {
                $this->addSimpanan(
                    $member->id,
                    'WAJIB',
                    $data['simpananWajib'],
                    'Simpanan wajib awal',
                    $data['buktiWajibPath'] ?? null,
                    auth()->id()
                );
            }

            if (isset($data['simpananSukarela']) && $data['simpananSukarela'] > 0) {
                $this->addSimpanan(
                    $member->id,
                    'SUKARELA',
                    $data['simpananSukarela'],
                    'Simpanan sukarela awal',
                    $data['buktiSukarelaPath'] ?? null,
                    auth()->id()
                );
            }

            return $member->fresh();
        });
    }

    /**
     * Update member information
     *
     * @param int $memberId
     * @param array $data
     * @return Member
     */
    public function updateMember(int $memberId, array $data): Member
    {
        $member = Member::findOrFail($memberId);

        $member->update([
            'name' => $data['name'] ?? $member->name,
            'email' => $data['email'] ?? $member->email,
            'phone' => $data['phone'] ?? $member->phone,
            'address' => $data['address'] ?? $member->address,
            'gender' => $data['gender'] ?? $member->gender,
            'unitKerja' => $data['unitKerja'] ?? $member->unitKerja,
            'status' => $data['status'] ?? $member->status,
            'isMemberKoperasi' => $data['isMemberKoperasi'] ?? $member->isMemberKoperasi,
        ]);

        return $member->fresh();
    }

    /**
     * Add simpanan (deposit)
     *
     * @param int $memberId
     * @param string $type POKOK|WAJIB|SUKARELA
     * @param float $amount
     * @param string|null $notes
     * @param string|null $buktiPath
     * @param int $processedBy
     * @return SimpananTransaction
     */
    public function addSimpanan(
        int $memberId,
        string $type,
        float $amount,
        ?string $notes = null,
        ?string $buktiPath = null,
        int $processedBy = null
    ): SimpananTransaction {
        return DB::transaction(function () use ($memberId, $type, $amount, $notes, $buktiPath, $processedBy) {
            $member = Member::findOrFail($memberId);

            // Update member's balance
            $field = match($type) {
                'POKOK' => 'simpananPokok',
                'WAJIB' => 'simpananWajib',
                'SUKARELA' => 'simpananSukarela',
            };

            $member->increment($field, $amount);
            $member = $member->fresh();

            // Get new balance for this type
            $balanceAfter = $member->{$field};

            // Create transaction record
            $transaction = SimpananTransaction::create([
                'memberId' => $memberId,
                'type' => $type,
                'transactionType' => 'SETOR',
                'amount' => $amount,
                'balanceAfter' => $balanceAfter,
                'notes' => $notes,
                'buktiPath' => $buktiPath,
                'processedBy' => $processedBy ?? auth()->id(),
                'status' => 'APPROVED',
            ]);

            return $transaction;
        });
    }

    /**
     * Withdraw simpanan sukarela
     *
     * @param int $memberId
     * @param float $amount
     * @param string $reason
     * @param bool $requireApproval
     * @param int $processedBy
     * @return SimpananTransaction
     * @throws \Exception
     */
    public function withdrawSimpanan(
        int $memberId,
        float $amount,
        string $reason,
        bool $requireApproval = false,
        int $processedBy = null
    ): SimpananTransaction {
        return DB::transaction(function () use ($memberId, $amount, $reason, $requireApproval, $processedBy) {
            $member = Member::findOrFail($memberId);

            // Check if sufficient balance
            if ($member->simpananSukarela < $amount) {
                throw new \Exception('Saldo simpanan sukarela tidak mencukupi.');
            }

            // Determine status based on approval requirement
            $status = $requireApproval ? 'PENDING' : 'APPROVED';

            // If auto-approved, update member balance immediately
            if (!$requireApproval) {
                $member->decrement('simpananSukarela', $amount);
                $member = $member->fresh();
            }

            $balanceAfter = $requireApproval 
                ? $member->simpananSukarela // Show current balance if pending
                : $member->simpananSukarela; // Show new balance if approved

            // Create transaction record
            $transaction = SimpananTransaction::create([
                'memberId' => $memberId,
                'type' => 'SUKARELA',
                'transactionType' => 'TARIK',
                'amount' => $amount,
                'balanceAfter' => $balanceAfter,
                'notes' => $reason,
                'processedBy' => $processedBy ?? auth()->id(),
                'status' => $status,
            ]);

            return $transaction;
        });
    }

    /**
     * Approve withdrawal
     *
     * @param int $transactionId
     * @param int $approvedBy
     * @return SimpananTransaction
     */
    public function approveWithdrawal(int $transactionId, int $approvedBy): SimpananTransaction
    {
        return DB::transaction(function () use ($transactionId, $approvedBy) {
            $transaction = SimpananTransaction::findOrFail($transactionId);

            if ($transaction->status !== 'PENDING') {
                throw new \Exception('Transaction is not pending approval.');
            }

            // Update member balance
            $member = $transaction->member;
            $member->decrement('simpananSukarela', $transaction->amount);
            $member = $member->fresh();

            // Update transaction
            $transaction->update([
                'status' => 'APPROVED',
                'approvedBy' => $approvedBy,
                'approvedAt' => now(),
                'balanceAfter' => $member->simpananSukarela,
            ]);

            return $transaction->fresh();
        });
    }

    /**
     * Reject withdrawal
     *
     * @param int $transactionId
     * @param string $reason
     * @param int $approvedBy
     * @return SimpananTransaction
     */
    public function rejectWithdrawal(int $transactionId, string $reason, int $approvedBy): SimpananTransaction
    {
        $transaction = SimpananTransaction::findOrFail($transactionId);

        if ($transaction->status !== 'PENDING') {
            throw new \Exception('Transaction is not pending approval.');
        }

        $transaction->update([
            'status' => 'REJECTED',
            'approvedBy' => $approvedBy,
            'approvedAt' => now(),
            'rejectionReason' => $reason,
        ]);

        return $transaction->fresh();
    }

    /**
     * Calculate points from transaction amount
     * Default: Rp 1,000 = 1 point
     *
     * @param float $amount
     * @param float $multiplier
     * @return int
     */
    public function calculatePoints(float $amount, float $multiplier = 1.0): int
    {
        $basePoints = floor($amount / 1000); // Rp 1,000 = 1 point
        return (int) ($basePoints * $multiplier);
    }

    /**
     * Add transaction and update member's points & tier
     *
     * @param int $memberId
     * @param float $transactionAmount
     * @return Member
     */
    public function recordTransaction(int $memberId, float $transactionAmount): Member
    {
        $member = Member::findOrFail($memberId);

        // Calculate points with tier multiplier
        $multiplier = match($member->tier) {
            'PLATINUM' => 2.0,
            'GOLD' => 1.5,
            'SILVER' => 1.2,
            default => 1.0,
        };

        $points = $this->calculatePoints($transactionAmount, $multiplier);

        // Update member
        $member->increment('points', $points);
        $member->increment('totalSpent', $transactionAmount);
        $member->update(['lastPurchase' => now()]);

        // Update tier based on new points
        $member->updateTier();

        return $member->fresh();
    }

    /**
     * Get member statistics
     *
     * @return array
     */
    public function getStats(): array
    {
        return [
            'total' => Member::count(),
            'active' => Member::where('status', 'ACTIVE')->count(),
            'totalSimpanan' => Member::sum(DB::raw('simpananPokok + simpananWajib + simpananSukarela')),
            'simpananPokok' => Member::sum('simpananPokok'),
            'simpananWajib' => Member::sum('simpananWajib'),
            'simpananSukarela' => Member::sum('simpananSukarela'),
            'avgPoints' => Member::avg('points'),
        ];
    }

    /**
     * Suspend member
     *
     * @param int $memberId
     * @param string $reason
     * @return Member
     */
    public function suspendMember(int $memberId, string $reason): Member
    {
        $member = Member::findOrFail($memberId);
        $member->update(['status' => 'SUSPENDED']);
        
        // Log activity
        // ActivityLog::create([...]);

        return $member->fresh();
    }

    /**
     * Activate member
     *
     * @param int $memberId
     * @return Member
     */
    public function activateMember(int $memberId): Member
    {
        $member = Member::findOrFail($memberId);
        $member->update(['status' => 'ACTIVE']);
        
        // Log activity
        // ActivityLog::create([...]);

        return $member->fresh();
    }

    /**
     * Import members from Excel file
     *
     * @param string $filePath
     * @return array
     */
    public function importFromExcel(string $filePath): array
    {
        $import = new \App\Imports\MemberImport($this);
        
        \Maatwebsite\Excel\Facades\Excel::import($import, $filePath);
        
        return $import->getSummary();
    }
}
