<?php

namespace App\Domains\Koperasi\Models;

use App\Shared\Models\User;
use Illuminate\Database\Eloquent\Model;

class MemberSettlement extends Model
{
    protected $table = 'member_settlements';

    protected $fillable = [
        'member_id',
        'simpanan_pokok',
        'simpanan_wajib',
        'simpanan_sukarela',
        'total_gross_simpanan',
        'loan_deduction',
        'net_refund_amount',
        'status',
        'payment_method',
        'bank_name',
        'bank_account_number',
        'bank_account_holder',
        'settled_at',
        'settled_by',
        'notes',
    ];

    protected $casts = [
        'simpanan_pokok' => 'decimal:2',
        'simpanan_wajib' => 'decimal:2',
        'simpanan_sukarela' => 'decimal:2',
        'total_gross_simpanan' => 'decimal:2',
        'loan_deduction' => 'decimal:2',
        'net_refund_amount' => 'decimal:2',
        'settled_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id');
    }

    public function settledBy()
    {
        return $this->belongsTo(User::class, 'settled_by');
    }
}
