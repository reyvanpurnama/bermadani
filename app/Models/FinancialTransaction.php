<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinancialTransaction extends Model
{
    protected $fillable = [
        'type',
        'category',
        'amount',
        'transactionDate',
        'description',
        'proofFile',
        'userId',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'transactionDate' => 'date',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'userId');
    }

    // Scopes
    public function scopeIncome($query)
    {
        return $query->where('type', 'INCOME');
    }

    public function scopeExpense($query)
    {
        return $query->where('type', 'EXPENSE');
    }

    public function scopeByDateRange($query, $start, $end)
    {
        return $query->whereBetween('transactionDate', [$start, $end]);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('transactionDate', today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('transactionDate', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('transactionDate', now()->month)
                     ->whereYear('transactionDate', now()->year);
    }

    public function scopeThisYear($query)
    {
        return $query->whereYear('transactionDate', now()->year);
    }
}
