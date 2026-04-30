<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsignmentItemCount extends Model
{
    protected $fillable = [
        'consignmentItemId',
        'batchId',
        'supplierId',
        'productId',
        'userId',
        'beforeQty',
        'physicalQty',
        'soldDeltaQty',
        'soldDeltaAmount',
        'payableDeltaAmount',
        'marginDeltaAmount',
        'countedAt',
        'note',
    ];

    protected $casts = [
        'beforeQty' => 'integer',
        'physicalQty' => 'integer',
        'soldDeltaQty' => 'integer',
        'soldDeltaAmount' => 'decimal:2',
        'payableDeltaAmount' => 'decimal:2',
        'marginDeltaAmount' => 'decimal:2',
        'countedAt' => 'datetime',
    ];

    public function item()
    {
        return $this->belongsTo(ConsignmentItem::class, 'consignmentItemId');
    }

    public function batch()
    {
        return $this->belongsTo(ConsignmentBatch::class, 'batchId');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplierId');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'productId');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }
}

