<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\StockMovement;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class StockMutation extends Component
{
    use WithPagination;

    public $search = '';
    public $filterType = '';
    public $dateStart;
    public $dateEnd;

    // Modal Form Data
    public $showModal = false;
    public $productId;
    public $type = 'ADJUSTMENT'; // Default
    public $mode = 'in'; // in (+) or out (-)
    public $quantity = 1;
    public $note = '';

    protected $rules = [
        'productId' => 'required|exists:products,id',
        'type' => 'required|in:ADJUSTMENT,EXPIRED_OUT,RETURN_IN,RETURN_OUT,RESTOCK',
        'mode' => 'required|in:in,out',
        'quantity' => 'required|integer|min:1',
        'note' => 'nullable|string|max:255',
    ];

    public function mount()
    {
        $this->dateStart = now()->startOfMonth()->format('Y-m-d');
        $this->dateEnd = now()->endOfMonth()->format('Y-m-d');
    }

    public function render()
    {
        $query = StockMovement::with('product')
            ->whereBetween(DB::raw('DATE(occurredAt)'), [$this->dateStart, $this->dateEnd]);

        if ($this->search) {
            $query->whereHas('product', function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('sku', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filterType) {
            $query->where('movementType', $this->filterType);
        }

        $mutations = $query->orderBy('occurredAt', 'desc')->paginate(10);
        $products = Product::where('isActive', true)->orderBy('name')->get();

        return view('livewire.admin.stock-mutation', [
            'mutations' => $mutations,
            'products' => $products
        ])->layout('layouts.admin');
    }

    public function openModal()
    {
        $this->reset(['productId', 'type', 'mode', 'quantity', 'note']);
        $this->showModal = true;
    }

    public function updatedType($value)
    {
        // Auto-set mode based on type
        if (in_array($value, ['EXPIRED_OUT', 'RETURN_OUT'])) {
            $this->mode = 'out';
        } elseif (in_array($value, ['RETURN_IN', 'RESTOCK'])) {
            $this->mode = 'in';
        } else {
            $this->mode = 'in'; // Default for Adjustment
        }
    }

    public function save()
    {
        $this->validate();

        $product = Product::find($this->productId);
        $qty = abs($this->quantity);

        if ($this->mode === 'out') {
            $qty = -$qty;
        }

        if ($qty < 0 && $product->stock < abs($qty)) {
            $this->addError('quantity', 'Stok saat ini (' . $product->stock . ') tidak mencukupi untuk pengurangan ini.');
            return;
        }

        DB::transaction(function () use ($product, $qty) {
            $product->stock += $qty;
            $product->save();

            // Update lastRestockAt specific logic
            if ($this->type == 'RESTOCK') {
                $product->update(['lastRestockAt' => now()]);
            }

            $product->stockMovements()->create([
                'movementType' => $this->type,
                'quantity' => $qty,
                'note' => $this->note,
                'occurredAt' => now(),
            ]);
        });

        $this->showModal = false;
        $this->dispatch('notify', [['message' => 'Mutasi berhasil disimpan', 'type' => 'success']]);
    }
}
