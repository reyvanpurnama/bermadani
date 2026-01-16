<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupplierSalesController extends Controller
{
    public function index()
    {
        $supplier = Auth::guard('supplier')->user();
        
        $sales = TransactionItem::whereHas('product', function($q) use ($supplier) {
                $q->where('supplierId', $supplier->id);
            })
            ->with(['product', 'transaction'])
            ->latest()
            ->paginate(15);
            
        // Calculate stats
        $totalOmzet = TransactionItem::whereHas('product', function($q) use ($supplier) {
                $q->where('supplierId', $supplier->id);
            })->sum('totalPrice');
            
        $totalItemsSold = TransactionItem::whereHas('product', function($q) use ($supplier) {
                $q->where('supplierId', $supplier->id);
            })->sum('quantity');
        
        // Calculate supplier revenue (quantity × buyPrice/supplierPrice)
        $supplierRevenue = TransactionItem::whereHas('product', function($q) use ($supplier) {
                $q->where('supplierId', $supplier->id);
            })
            ->join('products', 'transaction_items.productId', '=', 'products.id')
            ->selectRaw('SUM(transaction_items.quantity * products.buyPrice) as total')
            ->value('total') ?? 0;

        return view('supplier.sales', compact('sales', 'totalOmzet', 'totalItemsSold', 'supplierRevenue'));
    }
}
