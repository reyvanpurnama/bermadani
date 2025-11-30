<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;

class Supplier extends Authenticatable
{
    protected $fillable = [
        'code',
        'ownerName',
        'businessName',
        'phone',
        'email',
        'address',
        'description',
        'productCategory',
        'password',
        'monthlyFee',
        'preferredPaymentMethod',
        'paymentTerms',
        'isPaymentActive',
        'paymentStatus',
        'lastPaymentDate',
        'nextPaymentDue',
        'paymentGraceDays',
        'isSuspendedForPayment',
        'suspendedAt',
        'suspensionReason',
        'maxActiveProducts',
        'currentActiveProducts',
        'status',
        'approvedAt',
        'approvedById',
        'rejectedReason',
        'productQualityScore',
        'productPriceScore',
        'productPackagingScore',
        'productAverageScore',
        'evaluationNotes',
        'evaluatedBy',
        'evaluatedAt',
        'isActive',
        'note',
    ];

    protected $casts = [
        'monthlyFee' => 'decimal:2',
        'productAverageScore' => 'decimal:2',
        'isPaymentActive' => 'boolean',
        'isSuspendedForPayment' => 'boolean',
        'isActive' => 'boolean',
        'approvedAt' => 'datetime',
        'lastPaymentDate' => 'datetime',
        'nextPaymentDue' => 'datetime',
        'suspendedAt' => 'datetime',
        'evaluatedAt' => 'datetime',
        'maxActiveProducts' => 'integer',
        'currentActiveProducts' => 'integer',
        'paymentGraceDays' => 'integer',
    ];

    protected $hidden = [
        'password',
    ];

    public function products()
    {
        return $this->hasMany(Product::class, 'supplierId');
    }

    public function scopeActive($query)
    {
        return $query->where('isActive', true)->where('status', 'ACTIVE');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'PENDING');
    }

    public function approve($approvedById)
    {
        $this->update([
            'status' => 'APPROVED_PENDING_PAYMENT',
            'approvedAt' => now(),
            'approvedById' => $approvedById,
        ]);

        return $this;
    }

    public function activate()
    {
        $this->update([
            'status' => 'ACTIVE',
            'isActive' => true,
        ]);

        return $this;
    }

    public function suspend($reason = null)
    {
        $this->update([
            'status' => 'SUSPENDED',
            'isSuspendedForPayment' => true,
            'suspendedAt' => now(),
            'suspensionReason' => $reason,
        ]);

        return $this;
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    public function calculateAverageScore()
    {
        $scores = array_filter([
            $this->productQualityScore,
            $this->productPriceScore,
            $this->productPackagingScore,
        ]);

        if (empty($scores)) {
            return null;
        }

        $average = array_sum($scores) / count($scores);
        $this->update(['productAverageScore' => round($average, 2)]);

        return $average;
    }
}
