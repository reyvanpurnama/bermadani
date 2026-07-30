<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemberShuDistribution extends Model
{
    use HasFactory;

    protected $fillable = [
        'rat_session_id',
        'member_id',
        'simpanan_wajib_amount',
        'portion_percentage',
        'shu_amount',
        'is_disbursed',
        'disbursed_at',
    ];

    protected $casts = [
        'simpanan_wajib_amount' => 'decimal:2',
        'portion_percentage' => 'decimal:4',
        'shu_amount' => 'decimal:2',
        'is_disbursed' => 'boolean',
        'disbursed_at' => 'datetime',
    ];

    public function ratSession()
    {
        return $this->belongsTo(RatSession::class, 'rat_session_id');
    }

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id');
    }
}
