<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RatSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'year',
        'event_date',
        'title',
        'total_net_profit',
        'member_allocation_percentage',
        'total_member_shu',
        'total_simpanan_wajib_snapshot',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'event_date' => 'date',
        'total_net_profit' => 'decimal:2',
        'member_allocation_percentage' => 'decimal:2',
        'total_member_shu' => 'decimal:2',
        'total_simpanan_wajib_snapshot' => 'decimal:2',
    ];

    public function distributions()
    {
        return $this->hasMany(MemberShuDistribution::class, 'rat_session_id');
    }
}
