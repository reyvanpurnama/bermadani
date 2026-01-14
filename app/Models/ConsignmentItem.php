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
        'soldQty',
        'remainingQty',
        'sellPrice',
        'feePercent',
        'priceAfterFee',
    ];

    protected $casts = [
        'sellPrice' => 'decimal:2',
        'feePercent' => 'decimal:2',
        'priceAfterFee' => 'decimal:2',
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
        return $this->soldQty * $this->priceAfterFee;
    }
}
