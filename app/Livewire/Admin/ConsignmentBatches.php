<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ConsignmentBatch;
use App\Models\ConsignmentItem;
use App\Models\Supplier;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ConsignmentBatches extends Component
{
    use WithPagination;

    public $status = '';

    // Create Modal
    public $showCreateModal = false;
    public $supplierId;
    public $note = '';
    public $items = [];

    // Detail Modal
    public $showDetailModal = false;
    public $selectedBatch = null;

    // Retur Modal
    public $showReturModal = false;
    public $returItems = [];

    protected $rules = [
        'supplierId' => 'required|exists:suppliers,id',
        'items' => 'required|array|min:1',
        'items.*.productId' => 'required|exists:products,id',
        'items.*.initialQty' => 'required|integer|min:1',
        'items.*.sellPrice' => 'required|numeric|min:0',
        'items.*.feePercent' => 'required|numeric|min:0|max:100',
    ];

    public function setStatus($status)
    {
        $this->status = $status;
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->reset(['supplierId', 'note', 'items']);
        $this->items = [
            ['productId' => '', 'initialQty' => 1, 'sellPrice' => 0, 'feePercent' => 10]
        ];
        $this->showCreateModal = true;
    }

    public function addItem()
    {
        $this->items[] = ['productId' => '', 'initialQty' => 1, 'sellPrice' => 0, 'feePercent' => 10];
    }

    public function removeItem($index)
    {
        if (count($this->items) > 1) {
            unset($this->items[$index]);
            $this->items = array_values($this->items);
        }
    }

    public function saveBatch()
    {
        $this->validate();

        DB::transaction(function () {
            $batch = ConsignmentBatch::create([
                'batchCode' => ConsignmentBatch::generateBatchCode(),
                'supplierId' => $this->supplierId,
                'status' => 'ACTIVE',
                'receivedAt' => now(),
                'note' => $this->note,
            ]);

            $totalValue = 0;
            foreach ($this->items as $item) {
                $priceAfterFee = $item['sellPrice'] * (1 - ($item['feePercent'] / 100));

                ConsignmentItem::create([
                    'batchId' => $batch->id,
                    'productId' => $item['productId'],
                    'initialQty' => $item['initialQty'],
                    'remainingQty' => $item['initialQty'],
                    'sellPrice' => $item['sellPrice'],
                    'feePercent' => $item['feePercent'],
                    'priceAfterFee' => $priceAfterFee,
                ]);

                $totalValue += $item['sellPrice'] * $item['initialQty'];

                // Add stock to product
                $product = Product::find($item['productId']);
                if ($product) {
                    $product->increment('stock', $item['initialQty']);
                }
            }

            $batch->update(['totalValue' => $totalValue]);
        });

        $this->showCreateModal = false;
        $this->dispatch('notify', [['message' => 'Batch konsinyasi berhasil ditambahkan', 'type' => 'success']]);
    }

    public function openDetail($batchId)
    {
        $this->selectedBatch = ConsignmentBatch::with(['supplier', 'items.product'])->find($batchId);
        $this->showDetailModal = true;
    }

    public function closeDetail()
    {
        $this->showDetailModal = false;
        $this->selectedBatch = null;
    }

    public function processSettlement()
    {
        if (!$this->selectedBatch)
            return;

        // Recalculate totals before settlement
        $this->selectedBatch->recalculateTotals();

        $this->selectedBatch->update([
            'status' => 'SETTLED',
            'settledAt' => now(),
        ]);

        $this->closeDetail();
        $this->dispatch('notify', [['message' => 'Settlement berhasil diproses', 'type' => 'success']]);
    }

    public function openReturModal()
    {
        if (!$this->selectedBatch)
            return;

        // Prepare retur items from batch items that have remaining qty
        $this->returItems = [];
        foreach ($this->selectedBatch->items as $item) {
            if ($item->remainingQty > 0) {
                $this->returItems[] = [
                    'itemId' => $item->id,
                    'productName' => $item->product->name,
                    'remainingQty' => $item->remainingQty,
                    'returQty' => $item->remainingQty, // Default: return all remaining
                ];
            }
        }

        $this->showReturModal = true;
    }

    public function closeReturModal()
    {
        $this->showReturModal = false;
        $this->returItems = [];
    }

    public function processRetur()
    {
        DB::transaction(function () {
            foreach ($this->returItems as $returItem) {
                if ($returItem['returQty'] <= 0) continue;

                $item = ConsignmentItem::with('product')->find($returItem['itemId']);
                if (!$item) continue;

                // Validate retur qty doesn't exceed remaining
                $returQty = min($returItem['returQty'], $item->remainingQty);
                if ($returQty <= 0) continue;

                // Update consignment item
                $item->decrement('remainingQty', $returQty);

                // Reduce product stock
                $item->product->decrement('stock', $returQty);

                // Create stock movement record
                \App\Models\StockMovement::create([
                    'productId' => $item->productId,
                    'movementType' => 'CONSIGNMENT_RETURN',
                    'quantity' => -$returQty,
                    'referenceType' => 'ConsignmentBatch',
                    'referenceId' => $this->selectedBatch->id,
                    'note' => "Retur konsinyasi batch {$this->selectedBatch->batchCode}",
                    'occurredAt' => now(),
                ]);
            }

            // Recalculate batch totals
            $this->selectedBatch->recalculateTotals();

            // Check if all items have been sold/returned - change to pending settlement
            $this->selectedBatch->load('items');
            $hasRemaining = $this->selectedBatch->items->sum('remainingQty') > 0;
            if (!$hasRemaining) {
                $this->selectedBatch->update(['status' => 'PENDING_SETTLEMENT']);
            }
        });

        $this->closeReturModal();
        $this->closeDetail();
        $this->dispatch('notify', [['message' => 'Retur berhasil diproses', 'type' => 'success']]);
    }

    public function render()
    {
        $query = ConsignmentBatch::with(['supplier', 'items']);

        if ($this->status) {
            $query->where('status', $this->status);
        }

        $batches = $query->orderBy('created_at', 'desc')->paginate(10);

        // Stats
        $stats = [
            'activeBatches' => ConsignmentBatch::where('status', 'ACTIVE')->count(),
            'totalAssetValue' => ConsignmentBatch::where('status', 'ACTIVE')->sum('totalValue'),
            'pendingPayment' => ConsignmentBatch::where('status', 'PENDING_SETTLEMENT')->sum('payableAmount'),
        ];

        $suppliers = Supplier::where('isActive', true)->orderBy('businessName')->get();
        $products = Product::where('isActive', true)->orderBy('name')->get();

        return view('livewire.admin.consignment-batches', [
            'batches' => $batches,
            'stats' => $stats,
            'suppliers' => $suppliers,
            'products' => $products,
        ])->layout('layouts.admin');
    }
}
