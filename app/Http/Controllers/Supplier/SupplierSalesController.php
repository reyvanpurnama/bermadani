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
        $totalRevenue = TransactionItem::whereHas('product', function($q) use ($supplier) {
                $q->where('supplierId', $supplier->id);
            })->sum('totalPrice');
            
        $totalItemsSold = TransactionItem::whereHas('product', function($q) use ($supplier) {
                $q->where('supplierId', $supplier->id);
            })->sum('quantity');

        return view('supplier.sales', compact('sales', 'totalRevenue', 'totalItemsSold'));
    }
}
