<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Member extends Model
{
    use HasUuids;

    /**
     * Disable auto-increment (we use CUID/UUID)
     */
    public $incrementing = false;
    protected $keyType = 'string';

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
        $tier = match(true) {
            $this->totalSpent >= 10000000 => 'PLATINUM',
            $this->totalSpent >= 5000000 => 'GOLD',
            $this->totalSpent >= 2000000 => 'SILVER',
            default => 'BRONZE',
        };

        if ($this->tier !== $tier) {
            $this->update(['tier' => $tier]);
        }

        return $this;
    }

    public function recordPurchase($amount)
    {
        $this->increment('totalSpent', $amount);
        $this->update(['lastPurchase' => now()]);
        $this->updateTier();

        return $this;
    }
}
