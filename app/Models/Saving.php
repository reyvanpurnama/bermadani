<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Saving extends Model
{
    protected $fillable = [
        'memberId',
        'type',
        'amount',
        'description',
        'date',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'date' => 'datetime',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class, 'memberId');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeWithdrawals($query)
    {
        return $query->where('type', 'WITHDRAWAL');
    }

    public function scopeDeposits($query)
    {
        return $query->whereIn('type', ['POKOK', 'WAJIB', 'SUKARELA']);
    }
}
