<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ConsignmentBatch;
use App\Models\ConsignmentItem;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\FinancialTransaction;
use App\Models\SupplierNotification;
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

    // Receive Confirmation
    public $showReceiveForm = false;
    public $receiveItems = [];
    public $receiveNote = '';

    // Retur Modal
    public $showReturModal = false;
    public $returItems = [];

    protected $rules = [
        'supplierId' => 'required|exists:suppliers,id',
        'items' => 'required|array|min:1',
        'items.*.productId' => 'required|exists:products,id',
        'items.*.initialQty' => 'required|integer|min:1',
    ];

    // Auto-filter products when supplier changes
    public function updatedSupplierId($value)
    {
        // Reset items when supplier changes
        $this->items = [
            ['productId' => '', 'initialQty' => 1]
        ];
    }

    public function setStatus($status)
    {
        $this->status = $status;
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->reset(['supplierId', 'note', 'items']);
        $this->items = [
            ['productId' => '', 'initialQty' => 1]
        ];
        $this->showCreateModal = true;
    }

    public function addItem()
    {
        $this->items[] = ['productId' => '', 'initialQty' => 1];
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
                // Get prices from the product
                $product = Product::find($item['productId']);
                if (!$product)
                    continue;

                $sellPrice = $product->sellPrice;
                $supplierPrice = $product->buyPrice; // Harga dari supplier

                ConsignmentItem::create([
                    'batchId' => $batch->id,
                    'productId' => $item['productId'],
                    'initialQty' => $item['initialQty'],
                    'remainingQty' => $item['initialQty'],
                    'sellPrice' => $sellPrice,
                    'supplierPrice' => $supplierPrice,
                ]);

                $totalValue += $sellPrice * $item['initialQty'];

                // Add stock to product
                $product->increment('stock', $item['initialQty']);
            }

            $batch->update(['totalValue' => $totalValue]);

            // Send notification to supplier
            SupplierNotification::notifyBatchRequest(
                $this->supplierId,
                $batch->batchCode,
                $this->items
            );
        });

        $this->showCreateModal = false;
        $this->dispatch('notify', ['message' => 'Batch konsinyasi berhasil ditambahkan', 'type' => 'success']);
    }

    public function openDetail($batchId)
    {
        $this->selectedBatch = ConsignmentBatch::with(['supplier', 'items.product'])->find($batchId);

        // Auto-prepare receive items if in REQUESTED status (Unified Modal UX)
        if ($this->selectedBatch && $this->selectedBatch->status === 'REQUESTED') {
            $this->receiveItems = [];
            foreach ($this->selectedBatch->items as $item) {
                $this->receiveItems[] = [
                    'itemId' => $item->id,
                    'productName' => $item->product->name,
                    'requestedQty' => $item->initialQty,
                    'receivedQty' => $item->initialQty,
                    'note' => '',
                ];
            }
            $this->receiveNote = '';
        }

        $this->showDetailModal = true;
    }

    public function closeDetail()
    {
        $this->showDetailModal = false;
        $this->selectedBatch = null;
        $this->receiveItems = []; // clean up
    }

    public function openReceiveForm()
    {
        if (!$this->selectedBatch || $this->selectedBatch->status !== 'REQUESTED') {
            return;
        }

        $this->showDetailModal = false;

        // Prepare receive items with default values
        $this->receiveItems = [];
        foreach ($this->selectedBatch->items as $item) {
            $this->receiveItems[] = [
                'itemId' => $item->id,
                'productName' => $item->product->name,
                'requestedQty' => $item->initialQty,
                'receivedQty' => $item->initialQty, // Default to requested qty
                'note' => '',
            ];
        }
        $this->receiveNote = '';
        $this->showReceiveForm = true;
    }

    public function closeReceiveForm()
    {
        $this->showReceiveForm = false;
        $this->receiveItems = [];
        $this->receiveNote = '';
    }

    public function confirmReceive()
    {
        if (!$this->selectedBatch || $this->selectedBatch->status !== 'REQUESTED') {
            return;
        }

        // Validate receive items
        $this->validate([
            'receiveItems.*.receivedQty' => 'required|integer|min:0',
        ]);

        DB::transaction(function () {
            $notes = [];

            // Process each item
            foreach ($this->receiveItems as $receiveItem) {
                $item = ConsignmentItem::find($receiveItem['itemId']);
                $requestedQty = $receiveItem['requestedQty'];
                $receivedQty = $receiveItem['receivedQty'];
                $damagedQty = $requestedQty - $receivedQty; // Calculate damaged/lost qty

                // Update item with actual received qty
                $item->update([
                    'receivedQty' => $receivedQty,  // Qty yang benar-benar diterima
                    'remainingQty' => $receivedQty, // Stock awal = qty diterima
                    'damagedQty' => $damagedQty > 0 ? $damagedQty : 0,
                ]);

                // Add stock
                if ($receivedQty > 0) {
                    $item->product->increment('stock', $receivedQty);

                    // Build note for stock movement
                    $noteText = "Terima konsinyasi batch {$this->selectedBatch->batchCode}";
                    if ($receivedQty != $requestedQty) {
                        $diff = $receivedQty - $requestedQty;
                        $noteText .= " (Diminta: {$requestedQty}, Diterima: {$receivedQty}, Selisih: {$diff})";
                    }
                    if (!empty($receiveItem['note'])) {
                        $noteText .= " - {$receiveItem['note']}";
                    }

                    // Record stock movement
                    \App\Models\StockMovement::create([
                        'productId' => $item->productId,
                        'movementType' => 'CONSIGNMENT_IN',
                        'quantity' => $receivedQty,
                        'referenceType' => 'CONSIGNMENT_BATCH',
                        'referenceId' => $this->selectedBatch->id,
                        'note' => $noteText,
                        'occurredAt' => now(),
                    ]);
                }

                // Collect notes for batch
                if ($receivedQty != $requestedQty) {
                    $productName = $item->product->name;
                    $notes[] = "{$productName}: Diminta {$requestedQty}, Diterima {$receivedQty}";
                    if (!empty($receiveItem['note'])) {
                        $notes[] = "  → {$receiveItem['note']}";
                    }
                }
            }

            // Build batch note
            $batchNote = $this->receiveNote;
            if (!empty($notes)) {
                $batchNote = (!empty($batchNote) ? $batchNote . "\n\n" : '') . "Penyesuaian Qty:\n" . implode("\n", $notes);
            }

            // Recalculate total value based on actual received qty
            $totalValue = 0;
            foreach ($this->selectedBatch->items as $item) {
                $totalValue += $item->sellPrice * $item->initialQty;
            }

            // Update batch status to ACTIVE
            $this->selectedBatch->update([
                'status' => 'ACTIVE',
                'receivedAt' => now(),
                'note' => $batchNote,
                'totalValue' => $totalValue,
            ]);
        });



        $this->closeDetail();
        $this->dispatch('notify', ['message' => 'Barang berhasil diterima dan stok telah ditambahkan', 'type' => 'success']);
    }

    public function processSettlement()
    {
        if (!$this->selectedBatch)
            return;

        DB::transaction(function () {
            // Recalculate totals before settlement
            $this->selectedBatch->recalculateTotals();

            $payableAmount = $this->selectedBatch->payableAmount;
            $supplier = $this->selectedBatch->supplier;

            // 1. Update batch status to SETTLED
            $this->selectedBatch->update([
                'status' => 'SETTLED',
                'settledAt' => now(),
            ]);

            // 2. Record financial transaction (expense for paying supplier)
            FinancialTransaction::create([
                'type' => 'EXPENSE',
                'category' => 'Pembayaran Supplier Konsinyasi',
                'amount' => $payableAmount,
                'transactionDate' => today(),
                'description' => "Pembayaran ke supplier {$supplier->businessName} untuk batch #{$this->selectedBatch->batchCode}. Total terjual: " . number_format($this->selectedBatch->totalSold, 0, ',', '.'),
                'userId' => auth()->id(),
            ]);
        });

        $this->closeDetail();
        $this->dispatch('notify', ['message' => 'Pembayaran ke supplier berhasil diproses', 'type' => 'success']);
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
                if ($returItem['returQty'] <= 0)
                    continue;

                $item = ConsignmentItem::with('product')->find($returItem['itemId']);
                if (!$item)
                    continue;

                // Validate retur qty doesn't exceed remaining
                $returQty = min($returItem['returQty'], $item->remainingQty);
                if ($returQty <= 0)
                    continue;

                // Update consignment item - use recordReturn method for proper tracking
                $item->recordReturn($returQty);

                // Reduce product stock
                $item->product->decrement('stock', $returQty);

                // Create stock movement record
                \App\Models\StockMovement::create([
                    'productId' => $item->productId,
                    'movementType' => 'CONSIGNMENT_RETURN',
                    'quantity' => -$returQty,
                    'referenceType' => 'CONSIGNMENT_BATCH',
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
        $this->dispatch('notify', ['message' => 'Retur berhasil diproses', 'type' => 'success']);
    }

    public function render()
    {
        $query = ConsignmentBatch::with(['supplier', 'items']);

        if ($this->status) {
            $query->where('status', $this->status);
        }

        $batches = $query->orderBy('created_at', 'desc')->paginate(10);

        // Stats
        $activeBatches = ConsignmentBatch::where('status', 'ACTIVE')->get();
        $pendingSettlementBatches = ConsignmentBatch::where('status', 'PENDING_SETTLEMENT')->get();

        $stats = [
            'activeBatches' => $activeBatches->count(),
            'totalAssetValue' => $activeBatches->sum('totalValue'),
            'totalSold' => $activeBatches->sum('totalSold') + $pendingSettlementBatches->sum('totalSold'),
            'pendingPayment' => $activeBatches->sum('payableAmount') + $pendingSettlementBatches->sum('payableAmount'),
        ];

        $suppliers = Supplier::where('isActive', true)->orderBy('businessName')->get();

        // Filter products by selected supplier (only APPROVED products)
        $products = collect();
        if ($this->supplierId) {
            $products = Product::where('isActive', true)
                ->where('supplierId', $this->supplierId)
                ->where('approvalStatus', 'APPROVED')
                ->orderBy('name')
                ->get();
        }

        return view('livewire.admin.consignment-batches', [
            'batches' => $batches,
            'stats' => $stats,
            'suppliers' => $suppliers,
            'products' => $products,
        ])->layout('layouts.admin');
    }
}
