<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsignmentBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'batchCode',
        'supplierId',
        'status',
        'totalValue',
        'totalSold',
        'payableAmount',
        'receivedAt',
        'settledAt',
        'note',
    ];

    protected $casts = [
        'receivedAt' => 'datetime',
        'settledAt' => 'datetime',
        'totalValue' => 'decimal:2',
        'totalSold' => 'decimal:2',
        'payableAmount' => 'decimal:2',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplierId');
    }

    public function items()
    {
        return $this->hasMany(ConsignmentItem::class, 'batchId');
    }

    public function payouts()
    {
        return $this->hasMany(SupplierPayoutAllocation::class, 'batchId');
    }

    public function getTotalInitialQtyAttribute()
    {
        return $this->items->sum('initialQty');
    }

    public function getTotalSoldQtyAttribute()
    {
        return $this->items->sum('soldQty');
    }

    public function getSoldPercentAttribute()
    {
        $total = $this->totalInitialQty;
        if ($total == 0)
            return 0;
        return round(($this->totalSoldQty / $total) * 100);
    }

    public static function generateBatchCode()
    {
        $latest = self::latest()->first();
        $number = $latest ? (intval(substr($latest->batchCode, 5)) + 1) : 1;
        return 'BCH-' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Recalculate batch totals from items
     */
    public function recalculateTotals(): void
    {
        $this->load('items');
        
        $totalSold = 0;
        $payableAmount = 0;
        
        foreach ($this->items as $item) {
            $itemSoldValue = $item->soldQty * $item->sellPrice;
            $itemPayable = $item->soldQty * $item->supplierPrice;
            
            $totalSold += $itemSoldValue;
            $payableAmount += $itemPayable;
        }
        
        $this->update([
            'totalSold' => $totalSold,
            'payableAmount' => $payableAmount,
        ]);

        // Auto-update status to PENDING_SETTLEMENT if all items are sold/returned
        if ($this->status === 'ACTIVE') {
            $hasRemaining = $this->items->sum('remainingQty') > 0;
            if (!$hasRemaining) {
                $this->update(['status' => 'PENDING_SETTLEMENT']);
            }
        }
    }

    /**
     * Get total margin (profit) koperasi
     */
    public function getMarginAttribute()
    {
        return $this->totalSold - $this->payableAmount;
    }

    public function getOutstandingPayableAttribute()
    {
        $paid = (float) ($this->payouts()->sum('allocatedAmount') ?? 0);

        return max(0, (float) $this->payableAmount - $paid);
    }

    public function syncLifecycleStatus(): void
    {
        $hasRemaining = $this->items()->sum('remainingQty') > 0;
        $outstanding = $this->outstandingPayable;

        if ($hasRemaining) {
            $nextStatus = 'ACTIVE';
        } elseif ($outstanding > 0) {
            $nextStatus = 'PENDING_SETTLEMENT';
        } else {
            $nextStatus = 'SETTLED';
        }

        if ($this->status !== $nextStatus) {
            $payload = ['status' => $nextStatus];

            if ($nextStatus === 'SETTLED' && ! $this->settledAt) {
                $payload['settledAt'] = now();
            }

            $this->update($payload);
        }
    }

    /**
     * Find active consignment item for a product
     * Returns the oldest active batch item (FIFO)
     */
    public static function findActiveItemForProduct(int $productId): ?ConsignmentItem
    {
        return ConsignmentItem::whereHas('batch', function ($q) {
                $q->where('status', 'ACTIVE');
            })
            ->where('productId', $productId)
            ->where('remainingQty', '>', 0)
            ->orderBy('created_at', 'asc') // FIFO
            ->first();
    }
}
