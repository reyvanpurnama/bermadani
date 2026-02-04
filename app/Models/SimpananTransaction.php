<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SimpananTransaction extends Model
{
    protected $fillable = [
        'memberId',
        'relatedMemberId',
        'type',
        'transactionType',
        'amount',
        'balanceAfter',
        'notes',
        'buktiPath',
        'processedBy',
        'status',
        'approvedBy',
        'approvedAt',
        'rejectionReason',
        'billingMonth',
        'billStatus',
        'paidAmount',
        'transferReference',
        'isRead',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balanceAfter' => 'decimal:2',
        'paidAmount' => 'decimal:2',
        'approvedAt' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function member()
    {
        return $this->belongsTo(Member::class, 'memberId');
    }

    public function relatedMember()
    {
        return $this->belongsTo(Member::class, 'relatedMemberId');
    }

    public function processor()
    {
        return $this->belongsTo(User::class, 'processedBy');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approvedBy');
    }

    public function payments()
    {
        return $this->hasMany(SimpananPayment::class, 'billId');
    }

    /**
     * Computed Properties
     */
    public function getPaymentStatusAttribute()
    {
        if ($this->paidAmount == 0) {
            return 'UNPAID';
        } elseif ($this->paidAmount >= $this->amount) {
            return 'PAID';
        } else {
            return 'PARTIAL';
        }
    }

    public function getRemainingAmountAttribute()
    {
        return max(0, $this->amount - $this->paidAmount);
    }

    /**
     * Scopes
     */
    public function scopeByMember($query, $memberId)
    {
        return $query->where('memberId', $memberId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeDeposits($query)
    {
        return $query->where('transactionType', 'SETOR');
    }

    public function scopeWithdrawals($query)
    {
        return $query->where('transactionType', 'TARIK');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'PENDING');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'APPROVED');
    }

    /**
     * Accessors
     */
    public function getFormattedAmountAttribute()
    {
        return 'Rp ' . number_format((float) $this->amount, 0, ',', '.');
    }

    public function getFormattedBalanceAttribute()
    {
        return 'Rp ' . number_format((float) $this->balanceAfter, 0, ',', '.');
    }

    public function getTypeLabelAttribute()
    {
        return match ($this->type) {
            'POKOK' => 'Simpanan Pokok',
            'WAJIB' => 'Simpanan Wajib',
            'SUKARELA' => 'Simpanan Sukarela',
            default => $this->type,
        };
    }

    public function getTransactionTypeLabelAttribute()
    {
        return match ($this->transactionType) {
            'SETOR' => 'Setoran',
            'TARIK' => 'Penarikan',
            default => $this->transactionType,
        };
    }

    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            'APPROVED' => 'success',
            'PENDING' => 'warning',
            'REJECTED' => 'danger',
            default => 'secondary',
        };
    }
}
