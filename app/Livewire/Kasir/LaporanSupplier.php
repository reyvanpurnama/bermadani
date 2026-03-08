<?php

namespace App\Livewire\Kasir;

use App\Models\Product;
use App\Models\SupplierNotification;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class LaporanSupplier extends Component
{
    public string $date = '';
    public bool $submitted = false;
    public ?string $submittedDate = null;

    public function mount(): void
    {
        $this->date = today()->toDateString();
    }

    /**
     * Ambil semua item transaksi hari ini yang produknya konsinyasi,
     * dikelompokkan per supplier.
     */
    public function getLaporanProperty(): array
    {
        $items = TransactionItem::with(['product.supplier', 'transaction'])
            ->whereHas('transaction', function ($q) {
                $q->whereDate('date', $this->date)
                    ->where('status', 'COMPLETED');
            })
            ->whereHas('product', fn ($q) => $q->where('isConsignment', true))
            ->get();

        if ($items->isEmpty()) return [];

        // Group by supplierId
        $grouped = $items->groupBy(fn ($item) => $item->product->supplierId ?? 0);

        $result = [];
        foreach ($grouped as $supplierId => $supplierItems) {
            $supplier = $supplierItems->first()->product->supplier;
            if (!$supplier) continue;

            $products = $supplierItems->groupBy('productId')->map(function ($productItems) {
                $first = $productItems->first();
                return [
                    'name'          => $first->product->name ?? '-',
                    'qty'           => $productItems->sum('quantity'),
                    'unitPrice'     => $first->unitPrice,
                    'supplierPrice' => $first->cogsPerUnit,
                    'totalOmzet'    => $productItems->sum('totalPrice'),
                    'totalPayable'  => $productItems->sum(fn ($i) => $i->quantity * $i->cogsPerUnit),
                    'margin'        => $productItems->sum('grossProfit'),
                ];
            })->values();

            $result[] = [
                'supplierId'   => $supplierId,
                'supplierName' => $supplier->businessName,
                'products'     => $products,
                'totalQty'     => $products->sum('qty'),
                'totalOmzet'   => $products->sum('totalOmzet'),
                'totalPayable' => $products->sum('totalPayable'),
                'totalMargin'  => $products->sum('margin'),
            ];
        }

        return $result;
    }

    public function getTotalSummaryProperty(): array
    {
        $laporan = $this->laporan;
        return [
            'suppliers'    => count($laporan),
            'totalQty'     => array_sum(array_column($laporan, 'totalQty')),
            'totalOmzet'   => array_sum(array_column($laporan, 'totalOmzet')),
            'totalPayable' => array_sum(array_column($laporan, 'totalPayable')),
            'totalMargin'  => array_sum(array_column($laporan, 'totalMargin')),
        ];
    }

    public function submitLaporan(): void
    {
        $laporan = $this->laporan;

        if (empty($laporan)) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Tidak ada data penjualan konsinyasi untuk dikirim']);
            return;
        }

        foreach ($laporan as $row) {
            SupplierNotification::notifyDailyReport(
                $row['supplierId'],
                \Carbon\Carbon::parse($this->date)->translatedFormat('d F Y'),
                $row['totalQty'],
                $row['totalOmzet'],
                $row['totalPayable'],
            );
        }

        $this->submitted   = true;
        $this->submittedDate = $this->date;

        $this->dispatch('notify', [
            'type'    => 'success',
            'message' => 'Laporan berhasil dikirim ke ' . count($laporan) . ' supplier! 📊',
        ]);
    }

    public function render()
    {
        return view('livewire.kasir.laporan-supplier')->layout('layouts.admin');
    }
}
