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
        'feeAmount',
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
        'feeAmount' => 'decimal:2',
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
}
