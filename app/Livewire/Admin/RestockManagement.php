<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\RestockRequest;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class RestockManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $status = '';

    // Modal Create
    public $showModal = false;
    public $productId;
    public $requestedQty = 10;
    public $note = '';

    // Modal Receive
    public $showReceiveModal = false;
    public $selectedRequestId;
    public $confirmedQty;

    protected $rules = [
        'productId' => 'required|exists:products,id',
        'requestedQty' => 'required|integer|min:1',
        'note' => 'nullable|string|max:255',
    ];

    public function render()
    {
        $query = RestockRequest::with(['product', 'supplier', 'requestedByUser'])
            ->orderBy('created_at', 'desc');

        if ($this->search) {
            $query->whereHas('product', function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('sku', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        $requests = $query->paginate(10);

        // Products list for select (Only active products with supplier)
        $products = Product::where('isActive', true)
            ->whereNotNull('supplierId')
            ->orderBy('name')
            ->get();

        return view('livewire.admin.restock-management', [
            'requests' => $requests,
            'products' => $products
        ])->layout('layouts.admin');
    }

    public function openModal()
    {
        $this->reset(['productId', 'requestedQty', 'note']);
        $this->showModal = true;
    }

    public function saveRequest()
    {
        $this->validate();

        $product = Product::find($this->productId);

        if (!$product->supplierId) {
            $this->addError('productId', 'Produk ini tidak memiliki Supplier terhubung.');
            return;
        }

        RestockRequest::create([
            'productId' => $this->productId,
            'supplierId' => $product->supplierId,
            'requestedBy' => auth()->id(),
            'requestedQty' => $this->requestedQty,
            'note' => $this->note,
            'status' => 'PENDING',
        ]);

        $this->showModal = false;
        $this->dispatch('notify', [['message' => 'Request restock berhasil dikirim', 'type' => 'success']]);
    }

    public function openReceiveModal($id)
    {
        $req = RestockRequest::find($id);
        if (!$req || $req->status == 'COMPLETED')
            return;

        $this->selectedRequestId = $id;
        $this->confirmedQty = $req->confirmedQty ?? $req->requestedQty;
        $this->showReceiveModal = true;
    }

    public function confirmReceive()
    {
        $this->validate([
            'confirmedQty' => 'required|integer|min:1',
        ]);

        $req = RestockRequest::find($this->selectedRequestId);

        DB::transaction(function () use ($req) {
            $req->update([
                'status' => 'COMPLETED',
                'confirmedQty' => $this->confirmedQty,
                'completedAt' => now(),
            ]);

            // Add Stock
            $req->product->addStock(
                $this->confirmedQty,
                'RESTOCK',
                'Restock Request #' . $req->id
            );
        });

        $this->showReceiveModal = false;
        $this->dispatch('notify', [['message' => 'Stok berhasil diterima & ditambahkan', 'type' => 'success']]);
    }

    public function cancelRequest($id)
    {
        $req = RestockRequest::find($id);
        if ($req && $req->status == 'PENDING') {
            $req->delete(); // Or set to CANCELLED/REJECTED if soft deletes
            $this->dispatch('notify', [['message' => 'Request dibatalkan', 'type' => 'success']]);
        }
    }
}
