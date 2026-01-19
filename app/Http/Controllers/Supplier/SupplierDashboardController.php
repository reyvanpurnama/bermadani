<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Models\ConsignmentBatch;
use App\Models\Product;
use App\Models\TransactionItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SupplierDashboardController extends Controller
{
    public function index()
    {
        /** @var \App\Models\Supplier $supplier */
        $supplier = Auth::guard('supplier')->user();

        // Get supplier's product IDs
        $productIds = Product::where('supplierId', $supplier->id)->pluck('id');

        // Total Pendapatan Supplier (quantity × buyPrice) bulan ini
        $totalPendapatan = TransactionItem::whereIn('productId', $productIds)
            ->whereHas('transaction', function ($query) {
                $query->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->where('status', 'COMPLETED');
            })
            ->join('products', 'transaction_items.productId', '=', 'products.id')
            ->sum(DB::raw('transaction_items.quantity * products.buyPrice'));

        // Unit terjual bulan ini
        $unitTerjual = TransactionItem::whereIn('productId', $productIds)
            ->whereHas('transaction', function ($query) {
                $query->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->where('status', 'COMPLETED');
            })
            ->sum('quantity');

        // Pendapatan bulan lalu untuk hitung growth
        $pendapatanBulanLalu = TransactionItem::whereIn('productId', $productIds)
            ->whereHas('transaction', function ($query) {
                $query->whereMonth('created_at', now()->subMonth()->month)
                    ->whereYear('created_at', now()->subMonth()->year)
                    ->where('status', 'COMPLETED');
            })
            ->join('products', 'transaction_items.productId', '=', 'products.id')
            ->sum(DB::raw('transaction_items.quantity * products.buyPrice'));

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
