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
        'bankName',
        'bankAccountNumber',
        'bankAccountHolderName',
        'registrationFee',
        'registrationPaymentProof',
        'registrationPaymentStatus',
        'registrationPaymentVerifiedAt',
        'registrationPaymentVerifiedBy',
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
        'registrationFee' => 'decimal:2',
        'isPaymentActive' => 'boolean',
        'isSuspendedForPayment' => 'boolean',
        'isActive' => 'boolean',
        'approvedAt' => 'datetime',
        'registrationPaymentVerifiedAt' => 'datetime',
        'lastPaymentDate' => 'datetime',
        'nextPaymentDue' => 'datetime',
        'suspendedAt' => 'datetime',
        'evaluatedAt' => 'datetime',
        'maxActiveProducts' => 'integer',
        'currentActiveProducts' => 'integer',
        'paymentGraceDays' => 'integer',
        // registrationPaymentStatus tetap string untuk compatibility dengan SQLite
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

    /**
     * Get enum instance from registrationPaymentStatus string
     */
    public function getPaymentStatusEnumAttribute()
    {
        if (!$this->registrationPaymentStatus) {
            return null;
        }
        
        return \App\Enums\RegistrationPaymentStatus::from($this->registrationPaymentStatus);
    }

    /**
     * Get payment status label in Bahasa Indonesia
     */
    public function getPaymentStatusLabelAttribute(): string
    {
        try {
            return $this->paymentStatusEnum?->label() ?? 'Unknown';
        } catch (\Exception $e) {
            return match($this->registrationPaymentStatus) {
                'UNPAID' => 'Belum Dibayar',
                'PENDING_VERIFICATION' => 'Menunggu Verifikasi',
                'VERIFIED' => 'Terverifikasi',
                'REJECTED' => 'Ditolak',
                default => 'Unknown',
            };
        }
    }

    /**
     * Get payment status color for badge
     */
    public function getPaymentStatusColorAttribute(): string
    {
        try {
            return $this->paymentStatusEnum?->color() ?? 'gray';
        } catch (\Exception $e) {
            return match($this->registrationPaymentStatus) {
                'UNPAID' => 'gray',
                'PENDING_VERIFICATION' => 'yellow',
                'VERIFIED' => 'green',
                'REJECTED' => 'red',
                default => 'gray',
            };
        }
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
