<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    protected $fillable = [
        'productId',
        'movementType',
        'quantity',
        'referenceType',
        'referenceId',
        'unitCost',
        'note',
        'occurredAt',
        'isProduction',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unitCost' => 'decimal:2',
        'occurredAt' => 'datetime',
        'isProduction' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'productId');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('movementType', $type);
    }

    public function scopeStockIn($query)
    {
        return $query->whereIn('movementType', ['PURCHASE_IN', 'CONSIGNMENT_IN', 'RETURN_IN', 'TRANSFER_IN', 'RESTOCK', 'ADJUSTMENT'])
                     ->where('quantity', '>', 0);
    }

    public function scopeStockOut($query)
    {
        return $query->whereIn('movementType', ['SALE_OUT', 'CONSIGNMENT_RETURN', 'RETURN_OUT', 'EXPIRED_OUT', 'TRANSFER_OUT', 'ADJUSTMENT'])
                     ->where('quantity', '<', 0);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('occurredAt', today());
    }
}
