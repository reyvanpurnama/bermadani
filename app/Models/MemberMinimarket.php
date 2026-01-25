<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * MemberMinimarket Model
 * 
 * Table: member_minimarket
 * Untuk loyalty program minimarket (customer biasa)
 */
class MemberMinimarket extends Model
{
    protected $table = 'member_minimarket';
    
    protected $fillable = [
        'userId',
        'memberNumber',
        'cardNumber',
        'points',
        'totalSpent',
        'lastVisit',
        'status',
        'registeredBy',
    ];
    
    protected $casts = [
        'points' => 'integer',
        'totalSpent' => 'decimal:2',
        'lastVisit' => 'datetime',
    ];
    
    /**
     * Relasi ke User
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }
    
    /**
     * Relasi ke Member Koperasi (jika ada)
     */
    public function memberKoperasi()
    {
        return $this->hasOne(Member::class, 'userId', 'userId');
    }
    
    /**
     * Check apakah member ini juga Member Koperasi
     */
    public function isMemberKoperasi()
    {
        return $this->memberKoperasi()->exists();
    }
    
    /**
     * Relasi ke user yang mendaftarkan
     */
    public function registrar()
    {
        return $this->belongsTo(User::class, 'registeredBy');
    }
    
    /**
     * Get tier based on total spent (calculated property, not stored)
     */
    public function getTierAttribute()
    {
        if ($this->totalSpent >= 10000000) {
            return 'PLATINUM';
        } elseif ($this->totalSpent >= 5000000) {
            return 'GOLD';
        } elseif ($this->totalSpent >= 1000000) {
            return 'SILVER';
        }
        
        return 'BRONZE';
    }
    
    /**
     * Add points from purchase
     */
    public function addPoints($amount)
    {
        $pointsEarned = floor($amount / 10000); // 1 point per 10.000
        
        $this->increment('points', $pointsEarned);
        $this->increment('totalSpent', $amount);
        $this->update(['lastVisit' => now()]);
        
        return $pointsEarned;
    }
    
    /**
     * Redeem points
     */
    public function redeemPoints($points)
    {
        if ($this->points < $points) {
            throw new \Exception('Insufficient points');
        }
        
        $this->decrement('points', $points);
        
        return true;
    }
}
