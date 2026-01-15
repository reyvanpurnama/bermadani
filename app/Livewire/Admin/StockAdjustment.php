<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class StockAdjustment extends Component
{
    public $search = '';
    public $selectedProduct = null;
    public $adjustmentType = 'out'; // 'in' or 'out'
    public $quantity = 1;
    public $reason = 'DAMAGED';
    public $occurredAt;
    public $note = '';

    protected $rules = [
        'selectedProduct' => 'required',
        'adjustmentType' => 'required|in:in,out',
        'quantity' => 'required|integer|min:1',
        'reason' => 'required|string',
        'note' => 'nullable|string|max:500',
    ];

    public function mount()
    {
        $this->occurredAt = now()->format('Y-m-d');
    }

    public function updatedSearch()
    {
        $this->selectedProduct = null;
    }

    public function selectProduct($productId)
    {
        $this->selectedProduct = Product::find($productId);
        $this->search = '';
    }

    public function incrementQty()
    {
        $this->quantity++;
    }

    public function decrementQty()
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }

    public function getNewStockProperty()
    {
        if (!$this->selectedProduct)
            return 0;

        $change = $this->adjustmentType === 'out' ? -$this->quantity : $this->quantity;
        return max(0, $this->selectedProduct->stock + $change);
    }

    public function save()
    {
        $this->validate();

        $product = $this->selectedProduct;
        $qty = $this->adjustmentType === 'out' ? -abs($this->quantity) : abs($this->quantity);

        // Check stock if reducing
        if ($qty < 0 && $product->stock < abs($qty)) {
            $this->addError('quantity', 'Stok tidak mencukupi. Stok saat ini: ' . $product->stock);
            return;
        }

        DB::transaction(function () use ($product, $qty) {
            $product->stock += $qty;
            $product->save();

            $product->stockMovements()->create([
                'movementType' => 'ADJUSTMENT',
                'quantity' => $qty,
                'note' => $this->reason . ($this->note ? ' - ' . $this->note : ''),
                'occurredAt' => $this->occurredAt,
            ]);
        });

        // Reset form
        $this->reset(['selectedProduct', 'quantity', 'note']);
        $this->quantity = 1;

        $this->dispatch('notify', ['message' => 'Penyesuaian stok berhasil disimpan', 'type' => 'success']);
    }

    public function render()
    {
        $searchResults = [];
        if (strlen($this->search) >= 2) {
            $searchResults = Product::where('isActive', true)
                ->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('sku', 'like', '%' . $this->search . '%');
                })
                ->limit(5)
                ->get();
        }

        // Recent adjustments
        $recentAdjustments = StockMovement::with('product')
            ->where('movementType', 'ADJUSTMENT')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Monthly stats
        $monthlyStats = StockMovement::where('movementType', 'ADJUSTMENT')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->selectRaw('SUM(CASE WHEN quantity < 0 THEN quantity ELSE 0 END) as total_out')
            ->selectRaw('SUM(CASE WHEN quantity > 0 THEN quantity ELSE 0 END) as total_in')
            ->first();

        return view('livewire.admin.stock-adjustment', [
            'searchResults' => $searchResults,
            'recentAdjustments' => $recentAdjustments,
            'monthlyStats' => $monthlyStats,
        ])->layout('layouts.admin');
    }
}
