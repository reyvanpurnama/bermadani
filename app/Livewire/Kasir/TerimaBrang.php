<?php

namespace App\Livewire\Kasir;

use App\Models\ConsignmentBatch;
use App\Models\ConsignmentItem;
use App\Models\StockMovement;
use App\Models\SupplierNotification;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class TerimaBrang extends Component
{
    use WithPagination;

    public $showDetailModal = false;
    public $selectedBatchId = null;
    public $receiveItems = [];
    public $receiveNote = '';

    public function getBatchesProperty()
    {
        return ConsignmentBatch::with(['supplier', 'items.product'])
            ->where('status', 'REQUESTED')
            ->latest()
            ->paginate(10);
    }

    public function getSelectedBatchProperty()
    {
        if (!$this->selectedBatchId) return null;
        return ConsignmentBatch::with(['supplier', 'items.product'])->find($this->selectedBatchId);
    }

    public function openDetail($batchId)
    {
        $this->selectedBatchId = $batchId;
        $batch = $this->selectedBatch;

        if (!$batch || $batch->status !== 'REQUESTED') {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Batch tidak valid atau sudah diproses']);
            return;
        }

        $this->receiveItems = [];
        foreach ($batch->items as $item) {
            $this->receiveItems[] = [
                'itemId'       => $item->id,
                'productName'  => $item->product->name ?? '-',
                'requestedQty' => $item->initialQty,
                'receivedQty'  => $item->initialQty, // default: sesuai request
                'note'         => '',
            ];
        }

        $this->receiveNote = '';
        $this->showDetailModal = true;
    }

    public function closeDetail()
    {
        $this->showDetailModal = false;
        $this->selectedBatchId = null;
        $this->receiveItems = [];
        $this->receiveNote = '';
    }

    public function confirmReceive()
    {
        $batch = $this->selectedBatch;
        if (!$batch || $batch->status !== 'REQUESTED') {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Batch tidak valid']);
            return;
        }

        $this->validate([
            'receiveItems.*.receivedQty' => 'required|integer|min:0',
        ]);

        DB::transaction(function () use ($batch) {
            $adjustedItems = [];
            $notes         = [];
            $totalValue    = 0; // dihitung di dalam loop dari data aktual yg diterima

            foreach ($this->receiveItems as $ri) {
                $item         = ConsignmentItem::with('product')->find($ri['itemId']);
                $requestedQty = (int) $ri['requestedQty'];
                $receivedQty  = (int) $ri['receivedQty'];
                $damagedQty   = max(0, $requestedQty - $receivedQty);

                if (!$item) continue; // item hilang dari DB, skip

                // Update item
                $item->update([
                    'receivedQty'  => $receivedQty,
                    'initialQty'   => $receivedQty, // effective qty adalah yg diterima
                    'remainingQty' => $receivedQty,
                    'damagedQty'   => $damagedQty,
                ]);

                // Akumulasi totalValue dari data aktual (bukan dari collection lama)
                $totalValue += $item->sellPrice * $receivedQty;

                if ($receivedQty > 0 && $item->product) {
                    // Tambah stok produk
                    $item->product->increment('stock', $receivedQty);

                    // Catat stock movement
                    StockMovement::create([
                        'productId'     => $item->productId,
                        'movementType'  => 'CONSIGNMENT_IN',
                        'quantity'      => $receivedQty,
                        'referenceType' => 'CONSIGNMENT_BATCH',
                        'referenceId'   => $batch->id,
                        'note'          => "Terima konsinyasi batch {$batch->batchCode}"
                            . ($damagedQty > 0 ? " (diminta: {$requestedQty}, diterima: {$receivedQty}, selisih: {$damagedQty})" : ''),
                        'occurredAt'    => now(),
                    ]);
                }

                $productName = $item->product->name ?? "Item #{$item->id}";
                if ($receivedQty !== $requestedQty) {
                    $notes[] = "{$productName}: diminta {$requestedQty}, diterima {$receivedQty}";
                }

                $adjustedItems[] = [
                    'requestedQty' => $requestedQty,
                    'receivedQty'  => $receivedQty,
                ];
            }

            // Build batch note
            $batchNote = $this->receiveNote;
            if (!empty($notes)) {
                $batchNote = (!empty($batchNote) ? $batchNote . "\n\n" : '') . "Penyesuaian Qty:\n" . implode("\n", $notes);
            }

            $batch->update([
                'status'     => 'ACTIVE',
                'receivedAt' => now(),
                'note'       => $batchNote,
                'totalValue' => $totalValue,
            ]);

            // Notifikasi ke supplier
            SupplierNotification::notifyBatchReceived(
                $batch->supplierId,
                $batch->batchCode,
                $adjustedItems
            );
        });

        $this->closeDetail();
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Barang berhasil diterima! Stok sudah ditambahkan. ✅']);
    }

    public function render()
    {
        return view('livewire.kasir.terima-barang')->layout('layouts.admin');
    }
}
