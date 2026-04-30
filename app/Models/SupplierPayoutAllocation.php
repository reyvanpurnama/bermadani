<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierPayoutAllocation extends Model
{
    protected $fillable = [
        'supplierPayoutId',
        'batchId',
        'consignmentItemId',
        'allocatedAmount',
        'allocatedQtyEquivalent',
    ];

    protected $casts = [
        'allocatedAmount' => 'decimal:2',
        'allocatedQtyEquivalent' => 'decimal:4',
    ];

    public function payout()
    {
        return $this->belongsTo(SupplierPayout::class, 'supplierPayoutId');
    }

    public function batch()
    {
        return $this->belongsTo(ConsignmentBatch::class, 'batchId');
    }

    public function item()
    {
        return $this->belongsTo(ConsignmentItem::class, 'consignmentItemId');
    }
}

