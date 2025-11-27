<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionItem extends Model
{
    protected $fillable = [
        'transactionId',
        'productId',
        'quantity',
        'unitPrice',
        'totalPrice',
        'cogsPerUnit',
        'totalCogs',
        'grossProfit',
        'isProduction',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unitPrice' => 'decimal:2',
        'totalPrice' => 'decimal:2',
        'cogsPerUnit' => 'decimal:2',
        'totalCogs' => 'decimal:2',
        'grossProfit' => 'decimal:2',
        'isProduction' => 'boolean',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transactionId');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'productId');
    }

    protected static function booted()
    {
        static::creating(function ($item) {
            // Auto calculate total price
            if (!$item->totalPrice) {
                $item->totalPrice = $item->quantity * $item->unitPrice;
            }

            // Auto calculate COGS and profit
            if ($item->product) {
                $item->cogsPerUnit = $item->product->avgCost ?? $item->product->buyPrice ?? 0;
                $item->totalCogs = $item->quantity * $item->cogsPerUnit;
                $item->grossProfit = $item->totalPrice - $item->totalCogs;
            }
        });
    }
}
