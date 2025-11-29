<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'invoiceNumber',
        'member_id',
        'type',
        'totalAmount',
        'paymentMethod',
        'status',
        'note',
        'date',
        'isProduction',
    ];

    protected $casts = [
        'totalAmount' => 'decimal:2',
        'date' => 'datetime',
        'isProduction' => 'boolean',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id');
    }

    public function items()
    {
        return $this->hasMany(TransactionItem::class, 'transaction_id');
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'COMPLETED');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeSales($query)
    {
        return $query->where('type', 'SALE');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('date', today());
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('date', now()->month)
                     ->whereYear('date', now()->year);
    }

    public function calculateTotal()
    {
        $total = $this->items()->sum('totalPrice');
        $this->update(['totalAmount' => $total]);
        return $total;
    }

    public function getTotalGrossProfitAttribute()
    {
        return $this->items->sum('grossProfit');
    }
}
