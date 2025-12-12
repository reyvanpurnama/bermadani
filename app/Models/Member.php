<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'userId',
        'nomorAnggota',
        'name',
        'email',
        'phone',
        'address',
        'gender',
        'unitKerja',
        'joinDate',
        'status',
        'isMemberKoperasi',
        'simpananPokok',
        'simpananWajib',
        'simpananSukarela',
        'points',
        'tier',
        'totalSpent',
        'lastPurchase',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected $casts = [
        'joinDate' => 'datetime',
        'lastPurchase' => 'datetime',
        'isMemberKoperasi' => 'boolean',
        'simpananPokok' => 'decimal:2',
        'simpananWajib' => 'decimal:2',
        'simpananSukarela' => 'decimal:2',
        'totalSpent' => 'decimal:2',
        'points' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }

    public function savings()
    {
        return $this->hasMany(Saving::class, 'memberId');
    }

    public function simpananTransactions()
    {
        return $this->hasMany(SimpananTransaction::class, 'memberId');
    }

    public function loans()
    {
        return $this->hasMany(Loan::class, 'memberId');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'memberId');
    }

    public function pointsHistory()
    {
        return $this->hasMany(MemberPointsHistory::class, 'memberId');
    }

    /**
     * Accessors & Mutators
     */
    public function getTotalSimpananAttribute()
    {
        return $this->simpananPokok + $this->simpananWajib + $this->simpananSukarela;
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'ACTIVE' => 'success',
            'INACTIVE' => 'warning',
            'SUSPENDED' => 'danger',
            default => 'secondary',
        };
    }

    public function getTierBadgeAttribute()
    {
        return match($this->tier) {
            'PLATINUM' => 'primary',
            'GOLD' => 'warning',
            'SILVER' => 'info',
            'BRONZE' => 'secondary',
            default => 'secondary',
        };
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'ACTIVE');
    }

    public function scopeByUnit($query, $unit)
    {
        return $query->where('unitKerja', $unit);
    }

    public function scopeByTier($query, $tier)
    {
        return $query->where('tier', $tier);
    }

    public function scopeKoperasiMembers($query)
    {
        return $query->where('isMemberKoperasi', true);
    }

    /**
     * Business Logic Methods
     */
    public function addSimpanan($type, $amount, $description = null)
    {
        // Create saving record
        $saving = $this->savings()->create([
            'type' => $type,
            'amount' => $amount,
            'description' => $description,
            'date' => now(),
        ]);

        // Update member's simpanan balance
        switch ($type) {
            case 'POKOK':
                $this->increment('simpananPokok', $amount);
                break;
            case 'WAJIB':
                $this->increment('simpananWajib', $amount);
                break;
            case 'SUKARELA':
                $this->increment('simpananSukarela', $amount);
                break;
            case 'WITHDRAWAL':
                $this->decrement('simpananSukarela', $amount);
                break;
        }

        return $saving;
    }

    public function addPoints($points, $description, $transactionId = null, $expiresAt = null)
    {
        $newBalance = $this->points + $points;

        $this->pointsHistory()->create([
            'transactionId' => $transactionId,
            'type' => 'EARNED',
            'points' => $points,
            'balance' => $newBalance,
            'description' => $description,
            'expiresAt' => $expiresAt,
        ]);

        $this->increment('points', $points);
        $this->updateTier();

        return $this;
    }

    public function redeemPoints($points, $description)
    {
        if ($this->points < $points) {
            throw new \Exception('Insufficient points');
        }

        $newBalance = $this->points - $points;

        $this->pointsHistory()->create([
            'type' => 'REDEEMED',
            'points' => -$points,
            'balance' => $newBalance,
            'description' => $description,
        ]);

        $this->decrement('points', $points);

        return $this;
    }

    public function updateTier()
    {
        // Tier based on POINTS, not totalSpent
        // BRONZE: 0-999, SILVER: 1000-2999, GOLD: 3000-5999, PLATINUM: 6000+
        $tier = match(true) {
            $this->points >= 6000 => 'PLATINUM',
            $this->points >= 3000 => 'GOLD',
            $this->points >= 1000 => 'SILVER',
            default => 'BRONZE',
        };

        if ($this->tier !== $tier) {
            $this->update(['tier' => $tier]);
        }

        return $this;
    }

    /**
     * Generate unique member number
     * Format: MBR-YYYYMMDD-XXX
     */
    public static function generateNomorAnggota()
    {
        $date = now()->format('Ymd');
        $prefix = "MBR-{$date}-";

        $lastMember = self::where('nomorAnggota', 'LIKE', "{$prefix}%")
            ->latest('id')
            ->first();

        if ($lastMember) {
            $lastNumber = (int) substr($lastMember->nomorAnggota, -3);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Get progress to next tier (0-100)
     */
    public function getNextTierProgressAttribute()
    {
        $thresholds = [
            'BRONZE' => ['current' => 0, 'next' => 1000],
            'SILVER' => ['current' => 1000, 'next' => 3000],
            'GOLD' => ['current' => 3000, 'next' => 6000],
            'PLATINUM' => ['current' => 6000, 'next' => 6000], // Max tier
        ];

        $tierData = $thresholds[$this->tier] ?? $thresholds['BRONZE'];
        
        if ($this->tier === 'PLATINUM') {
            return 100; // Already at max tier
        }

        $currentPoints = $this->points - $tierData['current'];
        $requiredPoints = $tierData['next'] - $tierData['current'];
        
        return min(100, ($currentPoints / $requiredPoints) * 100);
    }

    /**
     * Get points needed for next tier
     */
    public function getPointsToNextTierAttribute()
    {
        $thresholds = [
            'BRONZE' => 1000,
            'SILVER' => 3000,
            'GOLD' => 6000,
            'PLATINUM' => 0, // Already at max
        ];

        $nextThreshold = $thresholds[$this->tier] ?? 1000;
        
        if ($this->tier === 'PLATINUM') {
            return 0; // Already at max tier
        }

        return max(0, $nextThreshold - $this->points);
    }

    public function recordPurchase($amount)
    {
        $this->increment('totalSpent', $amount);
        $this->update(['lastPurchase' => now()]);
        $this->updateTier();

        return $this;
    }
}
