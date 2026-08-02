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
        'total_simpanan_amount',
        'simpanan_pokok_snapshot',
        'simpanan_wajib_snapshot',
        'portion_percentage',
        'shu_amount',
        'jasa_simpanan_amount',
        'jasa_usaha_amount',
        'total_transaksi_amount',
        'is_disbursed',
        'disbursed_at',
        'financial_transaction_id',
    ];

    protected $casts = [
        'total_simpanan_amount' => 'decimal:2',
        'simpanan_pokok_snapshot' => 'decimal:2',
        'simpanan_wajib_snapshot' => 'decimal:2',
        'portion_percentage' => 'decimal:4',
        'shu_amount' => 'decimal:2',
        'jasa_simpanan_amount' => 'decimal:2',
        'jasa_usaha_amount' => 'decimal:2',
        'total_transaksi_amount' => 'decimal:2',
        'is_disbursed' => 'boolean',
        'disbursed_at' => 'datetime',
        'financial_transaction_id' => 'integer',
    ];

    public function ratSession()
    {
        return $this->belongsTo(RatSession::class, 'rat_session_id');
    }

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id');
    }

    public function financialTransaction()
    {
        return $this->belongsTo(FinancialTransaction::class, 'financial_transaction_id');
    }
}
