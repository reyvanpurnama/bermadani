<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SimpananPayment extends Model
{
    protected $fillable = [
        'billId',
        'memberId',
        'amount',
        'paymentMethod',
        'paymentDate',
        'referenceNumber',
        'receiptNumber',
        'notes',
        'proofAttachment',
        'processedBy',
    ];

    protected $casts = [
        'paymentDate' => 'date',
        'amount' => 'decimal:2',
    ];

    /**
     * Generate unique receipt number
     */
    public static function generateReceiptNumber(): string
    {
        $prefix = 'RCP';
        $date = now()->format('Ymd');
        $lastReceipt = self::whereDate('created_at', today())
            ->orderBy('id', 'desc')
            ->first();
        
        $sequence = $lastReceipt ? (int)substr($lastReceipt->receiptNumber, -4) + 1 : 1;
        
        return sprintf('%s-%s-%04d', $prefix, $date, $sequence);
    }

    // Relationships
    public function bill(): BelongsTo
    {
        return $this->belongsTo(SimpananTransaction::class, 'billId');
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'memberId');
    }

    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processedBy');
    }
}
