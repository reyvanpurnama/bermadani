<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Models\ConsignmentBatch;
use App\Models\Product;
use App\Services\SupplierSalesService;
use Illuminate\Support\Facades\Auth;

class SupplierDashboardController extends Controller
{
    public function index()
    {
        /** @var \App\Models\Supplier $supplier */
        $supplier = Auth::guard('supplier')->user();

        $allSales = SupplierSalesService::getSalesForSupplier($supplier->id);

        $nowMonth = now()->month;
        $nowYear = now()->year;
        $subMonth = now()->subMonth()->month;
        $subYear = now()->subMonth()->year;

        $thisMonthSales = $allSales->filter(function($item) use ($nowMonth, $nowYear) {
            return $item->created_at->month == $nowMonth && $item->created_at->year == $nowYear;
        });

        $lastMonthSales = $allSales->filter(function($item) use ($subMonth, $subYear) {
            return $item->created_at->month == $subMonth && $item->created_at->year == $subYear;
        });

        // Total Pendapatan Supplier (bulan ini or total all time fallback)
        $totalPendapatan = $thisMonthSales->sum('supplier_revenue');
        if ($totalPendapatan == 0 && $allSales->isNotEmpty()) {
            $totalPendapatan = $allSales->sum('supplier_revenue');
        }

        // Unit terjual (bulan ini or total all time fallback)
        $unitTerjual = $thisMonthSales->sum('quantity');
        if ($unitTerjual == 0 && $allSales->isNotEmpty()) {
            $unitTerjual = $allSales->sum('quantity');
        }

        // Pendapatan bulan lalu untuk hitung growth
        $pendapatanBulanLalu = $lastMonthSales->sum('supplier_revenue');

        // Hitung pertumbuhan pendapatan
        $pendapatanGrowth = $pendapatanBulanLalu > 0
            ? round((($totalPendapatan - $pendapatanBulanLalu) / $pendapatanBulanLalu) * 100, 1)
            : 0;

        // Produk aktif
        $produkAktif = Product::where('supplierId', $supplier->id)
            ->where('isActive', true)
            ->count();

        // Low stock products
        $lowStock = Product::where('supplierId', $supplier->id)
            ->where('isActive', true)
            ->whereColumn('stock', '<=', 'threshold')
            ->count();

        // Saldo tertahan (total payableAmount dari batch yang belum dibayar)
        $saldoTertahan = ConsignmentBatch::where('supplierId', $supplier->id)
            ->whereIn('status', ['ACTIVE', 'PENDING_SETTLEMENT'])
            ->sum('payableAmount');

        // Actionable Items Stats
        $requestedBatchesCount = ConsignmentBatch::where('supplierId', $supplier->id)
            ->where('status', 'REQUESTED')
            ->count();

        $pendingSettlementCount = ConsignmentBatch::where('supplierId', $supplier->id)
            ->where('status', 'PENDING_SETTLEMENT')
            ->count();

        // Recent settled batches (3 terakhir yang sudah dibayar)
        $recentSettled = ConsignmentBatch::where('supplierId', $supplier->id)
            ->where('status', 'SETTLED')
            ->with(['items.product'])
            ->latest('settledAt')
            ->take(3)
            ->get();

        return view('supplier.dashboard', compact(
            'totalPendapatan',
            'pendapatanGrowth',
            'unitTerjual',
            'produkAktif',
            'lowStock',
            'saldoTertahan',
            'requestedBatchesCount',
            'pendingSettlementCount',
            'recentSettled'
        ));
    }
}
