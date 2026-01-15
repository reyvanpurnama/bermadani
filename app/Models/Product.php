<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'categoryId',
        'sku',
        'buyPrice',
        'sellPrice',
        'stock',
        'threshold',
        'unit',
        'ownershipType',
        'status',
        'isConsignment',
        'isActive',
        'supplierId',
        'supplierContact',
        'profitShareRate',
        'stockCycle',
        'avgCost',
        'expiryPolicy',
        'lastRestockAt',
        'image',
        'approvalStatus',
        'rejectionReason',
        'approvedAt',
        'approvedBy',
        'isDraft',
    ];

    protected $appends = ['margin', 'marginPercentage'];

    protected $casts = [
        'buyPrice' => 'decimal:2',
        'sellPrice' => 'decimal:2',
        'avgCost' => 'decimal:2',
        'profitShareRate' => 'decimal:2',
        'stock' => 'integer',
        'threshold' => 'integer',
        'isConsignment' => 'boolean',
        'isActive' => 'boolean',
        'lastRestockAt' => 'datetime',
        'approvedAt' => 'datetime',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'categoryId');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplierId');
    }

    public function transactionItems()
    {
        return $this->hasMany(TransactionItem::class, 'productId');
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class, 'productId');
    }

    public function consignmentItems()
    {
        return $this->hasMany(ConsignmentItem::class, 'productId');
    }



    public function scopeActive($query)
    {
        return $query->where('isActive', true);
    }

    public function scopeApproved($query)
    {
        return $query->where('approvalStatus', 'APPROVED');
    }

    public function scopePending($query)
    {
        return $query->where('approvalStatus', 'PENDING');
    }

    public function scopeAvailableForSale($query)
    {
        return $query->where('isActive', true)
            ->where('approvalStatus', 'APPROVED')
            ->where('status', 'ACTIVE')
            ->where('stock', '>', 0);
    }

    public function getMarginAttribute()
    {
        if (!$this->sellPrice || !$this->buyPrice) {
            return 0;
        }
        return $this->sellPrice - $this->buyPrice;
    }

    public function getMarginPercentageAttribute()
    {
        if (!$this->buyPrice || $this->buyPrice == 0) {
            return 0;
        }
        return (($this->sellPrice - $this->buyPrice) / $this->buyPrice) * 100;
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('stock', '<=', 'threshold');
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('categoryId', $categoryId);
    }

    public function scopeConsignment($query)
    {
        return $query->where('isConsignment', true);
    }

    public function addStock($quantity, $movementType = 'PURCHASE_IN', $note = null, $unitCost = null)
    {
        $this->increment('stock', $quantity);

        $this->stockMovements()->create([
            'movementType' => $movementType,
            'quantity' => $quantity,
            'unitCost' => $unitCost,
            'note' => $note,
            'occurredAt' => now(),
        ]);

        if ($movementType === 'RESTOCK') {
            $this->update(['lastRestockAt' => now()]);
        }

        return $this;
    }

    public function reduceStock($quantity, $movementType = 'SALE_OUT', $note = null)
    {
        if ($this->stock < $quantity) {
            throw new \Exception('Insufficient stock');
        }

        $this->decrement('stock', $quantity);

        $this->stockMovements()->create([
            'movementType' => $movementType,
            'quantity' => -$quantity,
            'note' => $note,
            'occurredAt' => now(),
        ]);

        return $this;
    }

    public function isLowStock()
    {
        return $this->stock <= $this->threshold;
    }

    public function getGrossProfitAttribute()
    {
        return $this->sellPrice - ($this->avgCost ?? $this->buyPrice ?? 0);
    }

    public function getGrossProfitMarginAttribute()
    {
        if ($this->sellPrice == 0)
            return 0;
        return ($this->grossProfit / $this->sellPrice) * 100;
    }
}
