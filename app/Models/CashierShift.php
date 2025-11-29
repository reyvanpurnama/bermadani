<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashierShift extends Model
{
    protected $fillable = [
        'user_id',
        'opening_cash',
        'check_in_at',
        'check_out_at',
        'closing_cash',
        'expected_cash',
        'difference',
        'total_transactions',
        'total_sales',
        'total_cash_sales',
        'total_non_cash_sales',
        'note',
        'status',
    ];

    protected $casts = [
        'opening_cash' => 'decimal:2',
        'closing_cash' => 'decimal:2',
        'expected_cash' => 'decimal:2',
        'difference' => 'decimal:2',
        'total_sales' => 'decimal:2',
        'total_cash_sales' => 'decimal:2',
        'total_non_cash_sales' => 'decimal:2',
        'check_in_at' => 'datetime',
        'check_out_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return Transaction::where('date', '>=', $this->check_in_at)
            ->when($this->check_out_at, fn($q) => $q->where('date', '<=', $this->check_out_at));
    }

    // Check if shift is currently open
    public function isOpen(): bool
    {
        return $this->status === 'OPEN';
    }

    // Get current open shift for a user
    public static function getOpenShift($userId)
    {
        return self::where('user_id', $userId)
            ->where('status', 'OPEN')
            ->first();
    }

    // Calculate shift summary
    public function calculateSummary()
    {
        $transactions = Transaction::where('date', '>=', $this->check_in_at)
            ->when($this->check_out_at, fn($q) => $q->where('date', '<=', $this->check_out_at))
            ->where('status', 'COMPLETED')
            ->get();

        $this->total_transactions = $transactions->count();
        $this->total_sales = $transactions->sum('totalAmount');
        $this->total_cash_sales = $transactions->where('paymentMethod', 'CASH')->sum('totalAmount');
        $this->total_non_cash_sales = $transactions->where('paymentMethod', '!=', 'CASH')->sum('totalAmount');
        $this->expected_cash = $this->opening_cash + $this->total_cash_sales;
        
        if ($this->closing_cash !== null) {
            $this->difference = $this->closing_cash - $this->expected_cash;
        }

        return $this;
    }

    // Scope for today's shifts
    public function scopeToday($query)
    {
        return $query->whereDate('check_in_at', today());
    }

    // Get shift duration
    public function getDurationAttribute()
    {
        $end = $this->check_out_at ?? now();
        return $this->check_in_at->diffForHumans($end, true);
    }
}
