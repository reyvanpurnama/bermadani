<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsignmentItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'batchId',
        'productId',
        'initialQty',
        'damagedQty',
        'soldQty',
        'remainingQty',
        'sellPrice',
        'supplierPrice',
    ];

    protected $casts = [
        'sellPrice' => 'decimal:2',
        'supplierPrice' => 'decimal:2',
    ];

    public function batch()
    {
        return $this->belongsTo(ConsignmentBatch::class, 'batchId');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'productId');
    }

    public function getPayableAmountAttribute()
    {
        return $this->soldQty * $this->supplierPrice;
    }

    public function getMarginAttribute()
    {
        return ($this->sellPrice - $this->supplierPrice) * $this->soldQty;
    }

    /**
     * Record a sale for this consignment item
     * Updates soldQty, remainingQty, and recalculates batch totals
     */
    public function recordSale(int $quantity): void
    {
        $this->increment('soldQty', $quantity);
        $this->decrement('remainingQty', $quantity);
        
        // Recalculate batch totals
        $this->batch->recalculateTotals();
    }

    /**
     * Record a return (retur) for this consignment item
     * Decreases remainingQty and recalculates batch totals
     */
    public function recordReturn(int $quantity): void
    {
        $this->decrement('remainingQty', $quantity);
        
        // Recalculate batch totals
        $this->batch->recalculateTotals();
    }
}
