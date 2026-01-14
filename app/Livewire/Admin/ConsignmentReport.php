<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\ConsignmentBatch;
use App\Models\ConsignmentItem;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;

class ConsignmentReport extends Component
{
    public $period = 'month'; // 'month' or 'year'

    public function setPeriod($period)
    {
        $this->period = $period;
    }

    public function render()
    {
        // Date filter
        $dateStart = $this->period === 'month'
            ? now()->startOfMonth()
            : now()->startOfYear();
        $dateEnd = now();

        // Stats
        $stats = [
            'totalSuppliers' => ConsignmentBatch::distinct('supplierId')->count('supplierId'),
            'totalOmzet' => ConsignmentBatch::whereBetween('created_at', [$dateStart, $dateEnd])
                ->sum('totalSold'),
            'totalFee' => ConsignmentBatch::whereBetween('created_at', [$dateStart, $dateEnd])
                ->sum('feeAmount'),
            'pendingPayable' => ConsignmentBatch::where('status', 'PENDING_SETTLEMENT')
                ->sum('payableAmount'),
        ];

        // Supplier Performance Data
        $supplierPerformance = DB::table('consignment_batches')
            ->join('suppliers', 'consignment_batches.supplierId', '=', 'suppliers.id')
            ->join('consignment_items', 'consignment_batches.id', '=', 'consignment_items.batchId')
            ->whereBetween('consignment_batches.created_at', [$dateStart, $dateEnd])
            ->select(
                'suppliers.id',
                'suppliers.businessName',
                'suppliers.productCategory',
                DB::raw('COUNT(DISTINCT consignment_items.productId) as productCount'),
                DB::raw('SUM(consignment_items.soldQty) as totalSold'),
                DB::raw('SUM(consignment_items.initialQty) as totalInitial'),
                DB::raw('SUM(consignment_items.soldQty * consignment_items.sellPrice) as totalOmzet'),
                DB::raw('SUM(consignment_items.soldQty * consignment_items.sellPrice * consignment_items.feePercent / 100) as totalFee')
            )
            ->groupBy('suppliers.id', 'suppliers.businessName', 'suppliers.productCategory')
            ->orderByDesc('totalOmzet')
            ->get();

        // Chart data (Top 5)
        $chartData = $supplierPerformance->take(5)->map(function ($item) {
            return [
                'name' => $item->businessName,
                'omzet' => (float) $item->totalOmzet,
            ];
        });

        return view('livewire.admin.consignment-report', [
            'stats' => $stats,
            'supplierPerformance' => $supplierPerformance,
            'chartData' => $chartData,
        ])->layout('layouts.admin');
    }
}
