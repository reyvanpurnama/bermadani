<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkLog extends Model
{
    protected $fillable = [
        'userId',
        'developerName',
        'date',
        'startTime',
        'endTime',
        'hoursWorked',
        'description',
        'hourlyRate',
        'totalAmount',
        'status',
        'approvedBy',
        'approvedAt',
        'paidAt',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'hoursWorked' => 'decimal:2',
        'hourlyRate' => 'decimal:2',
        'totalAmount' => 'decimal:2',
        'approvedAt' => 'datetime',
        'paidAt' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approvedBy');
    }

    /**
     * Scopes
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('userId', $userId);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'PENDING');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'APPROVED');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'PAID');
    }

    public function scopeInMonth($query, $year, $month)
    {
        return $query->whereYear('date', $year)->whereMonth('date', $month);
    }

    /**
     * Accessors
     */
    public function getFormattedAmountAttribute()
    {
        return 'Rp ' . number_format((float) $this->totalAmount, 0, ',', '.');
    }

    public function getFormattedHoursAttribute()
    {
        return number_format((float) $this->hoursWorked, 1) . ' jam';
    }

    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'PENDING' => 'Menunggu',
            'APPROVED' => 'Disetujui',
            'REJECTED' => 'Ditolak',
            'PAID' => 'Dibayar',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            'PENDING' => 'amber',
            'APPROVED' => 'emerald',
            'REJECTED' => 'rose',
            'PAID' => 'blue',
            default => 'slate',
        };
    }

    /**
     * Boot method to auto-calculate totalAmount
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $model->totalAmount = $model->hoursWorked * $model->hourlyRate;
        });
    }
}
