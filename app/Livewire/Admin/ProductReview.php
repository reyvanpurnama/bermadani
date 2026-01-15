<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ProductReview extends Component
{
    use WithPagination;

    public $status = 'PENDING';

    // Detail Modal
    public $showModal = false;
    public $selectedProduct = null;
    public $sellPrice;
    public $adminNote = '';

    public function setStatus($status)
    {
        $this->status = $status;
        $this->resetPage();
    }

    public function openDetail($productId)
    {
        $this->selectedProduct = Product::with('supplier')->find($productId);
        $this->sellPrice = $this->selectedProduct->sellPrice ?? null;
        $this->adminNote = '';
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedProduct = null;
    }

    public function approve()
    {
        if (!$this->selectedProduct)
            return;

        DB::transaction(function () {
            $this->selectedProduct->update([
                'approvalStatus' => 'APPROVED',
                'status' => 'ACTIVE',
                'sellPrice' => $this->sellPrice,
                'approvedAt' => now(),
                'approvedBy' => auth()->id(),
                'isDraft' => false,
                'isActive' => true,
            ]);

            if ($this->selectedProduct->supplier) {
                $this->selectedProduct->supplier->increment('currentActiveProducts');
            }
        });

        $this->closeModal();
        $this->dispatch('notify', [['message' => 'Produk berhasil disetujui & masuk ke stok', 'type' => 'success']]);
    }

    public function reject()
    {
        if (!$this->selectedProduct)
            return;

        $this->validate([
            'adminNote' => 'required|string|min:5',
        ], [
            'adminNote.required' => 'Alasan penolakan wajib diisi.',
            'adminNote.min' => 'Alasan minimal 5 karakter.',
        ]);

        $this->selectedProduct->update([
            'approvalStatus' => 'REJECTED',
            'rejectionReason' => $this->adminNote,
        ]);

        $this->closeModal();
        $this->dispatch('notify', [['message' => 'Produk ditolak', 'type' => 'warning']]);
    }

    public function render()
    {
        $products = Product::with('supplier')
            ->where('approvalStatus', $this->status)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $counts = [
            'pending' => Product::where('approvalStatus', 'PENDING')->count(),
            'approved' => Product::where('approvalStatus', 'APPROVED')->count(),
            'rejected' => Product::where('approvalStatus', 'REJECTED')->count(),
        ];

        return view('livewire.admin.product-review', [
            'products' => $products,
            'counts' => $counts,
        ])->layout('layouts.admin');
    }
}
