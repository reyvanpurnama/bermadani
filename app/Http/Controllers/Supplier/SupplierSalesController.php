<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Services\SupplierSalesService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;

class SupplierSalesController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\Supplier $supplier */
        $supplier = Auth::guard('supplier')->user();
        
        $allSales = SupplierSalesService::getSalesForSupplier($supplier->id);

        $totalOmzet = $allSales->sum('total_price');
        $totalItemsSold = $allSales->sum('quantity');
        $supplierRevenue = $allSales->sum('supplier_revenue');

        $perPage = 15;
        $page = (int) $request->get('page', 1);
        $paginatedSales = new LengthAwarePaginator(
            $allSales->forPage($page, $perPage)->values(),
            $allSales->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('supplier.sales', compact('sales', 'totalOmzet', 'totalItemsSold', 'supplierRevenue'), [
            'sales' => $paginatedSales,
            'totalOmzet' => $totalOmzet,
            'totalItemsSold' => $totalItemsSold,
            'supplierRevenue' => $supplierRevenue,
        ]);
    }
}
