<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierPayout extends Model
{
    protected $fillable = [
        'payoutCode',
        'supplierId',
        'userId',
        'payoutDate',
        'grossDueAmount',
        'paidAmount',
        'outstandingAfter',
        'note',
    ];

    protected $casts = [
        'payoutDate' => 'date',
        'grossDueAmount' => 'decimal:2',
        'paidAmount' => 'decimal:2',
        'outstandingAfter' => 'decimal:2',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplierId');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }

    public function allocations()
    {
        return $this->hasMany(SupplierPayoutAllocation::class, 'supplierPayoutId');
    }

    public static function generateCode(): string
    {
        $latest = self::latest()->first();
        $number = $latest ? (intval(substr($latest->payoutCode, 4)) + 1) : 1;

        return 'PAY-' . str_pad((string) $number, 6, '0', STR_PAD_LEFT);
    }
}

