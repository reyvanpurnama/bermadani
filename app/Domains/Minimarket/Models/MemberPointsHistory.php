<?php

namespace App\Domains\Minimarket\Models;
use App\Domains\Koperasi\Models\Member;

use Illuminate\Database\Eloquent\Model;

class MemberPointsHistory extends Model
{
    protected $fillable = [
        'memberId',
        'transactionId',
        'type',
        'points',
        'balance',
        'description',
        'expiresAt',
    ];

    protected $casts = [
        'points' => 'integer',
        'balance' => 'integer',
        'expiresAt' => 'datetime',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class, 'memberId');
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transactionId');
    }

    public function scopeEarned($query)
    {
        return $query->where('type', 'EARNED');
    }

    public function scopeRedeemed($query)
    {
        return $query->where('type', 'REDEEMED');
    }

    public function scopeExpired($query)
    {
        return $query->where('type', 'EXPIRED');
    }

    public function scopeActive($query)
    {
        return $query->where('type', 'EARNED')
                     ->where(function($q) {
                         $q->whereNull('expiresAt')
                           ->orWhere('expiresAt', '>', now());
                     });
    }
}
