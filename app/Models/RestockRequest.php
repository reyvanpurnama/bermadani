<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RestockRequest extends Model
{
    protected $fillable = [
        'productId',
        'supplierId',
        'requestedBy',
        'requestedQty',
        'note',
        'status',
        'confirmedQty',
        'supplierNote',
        'respondedAt',
        'completedAt',
    ];

    protected $casts = [
        'requestedQty' => 'integer',
        'confirmedQty' => 'integer',
        'respondedAt' => 'datetime',
        'completedAt' => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'productId');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplierId');
    }

    public function requestedByUser()
    {
        return $this->belongsTo(\App\Models\User::class, 'requestedBy');
    }
}
