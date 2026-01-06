<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
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
        
        // Total Omzet (Total penjualan kotor bulan ini)
        $totalOmzet = TransactionItem::whereIn('productId', $productIds)
            ->whereHas('transaction', function ($query) {
                $query->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->where('status', 'COMPLETED');
            })
            ->sum(DB::raw('quantity * unitPrice'));
        
        // Hitung Total Pendapatan (Omzet - Fee Koperasi)
        // Fee koperasi dihitung dari profitShareRate masing-masing produk
        $totalPendapatan = TransactionItem::whereIn('productId', $productIds)
            ->whereHas('transaction', function ($query) {
                $query->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->where('status', 'COMPLETED');
            })
            ->join('products', 'transaction_items.productId', '=', 'products.id')
            ->sum(DB::raw('transaction_items.quantity * transaction_items.unitPrice * (1 - COALESCE(products.profitShareRate, 0) / 100)'));
        
        // Unit terjual bulan ini
        $unitTerjual = TransactionItem::whereIn('productId', $productIds)
            ->whereHas('transaction', function ($query) {
                $query->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->where('status', 'COMPLETED');
            })
            ->sum('quantity');
        
        // Omzet bulan lalu untuk hitung growth
        $omzetBulanLalu = TransactionItem::whereIn('productId', $productIds)
            ->whereHas('transaction', function ($query) {
                $query->whereMonth('created_at', now()->subMonth()->month)
                    ->whereYear('created_at', now()->subMonth()->year)
                    ->where('status', 'COMPLETED');
            })
            ->sum(DB::raw('quantity * unitPrice'));
        
        // Hitung pertumbuhan omzet
        $omzetGrowth = $omzetBulanLalu > 0 
            ? round((($totalOmzet - $omzetBulanLalu) / $omzetBulanLalu) * 100, 1)
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
        
        // Saldo tertahan (placeholder - bisa dikembangkan sesuai business logic)
        $saldoTertahan = 0;
        
        return view('supplier.dashboard', compact(
            'totalOmzet',
            'totalPendapatan',
            'omzetGrowth',
            'unitTerjual',
            'produkAktif',
            'lowStock',
            'saldoTertahan'
        ));
    }
}
