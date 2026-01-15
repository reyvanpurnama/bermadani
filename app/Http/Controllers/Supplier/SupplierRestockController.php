<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Models\ConsignmentBatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupplierRestockController extends Controller
{
    public function index()
    {
        $supplier = Auth::guard('supplier')->user();
        
        // Get consignment batches for this supplier
        $batches = ConsignmentBatch::where('supplierId', $supplier->id)
            ->with(['items.product'])
            ->latest()
            ->paginate(15);
        
        // Count by status
        $requestedCount = ConsignmentBatch::where('supplierId', $supplier->id)
            ->where('status', 'REQUESTED')
            ->count();
        
        $activeCount = ConsignmentBatch::where('supplierId', $supplier->id)
            ->where('status', 'ACTIVE')
            ->count();
        
        $pendingSettlementCount = ConsignmentBatch::where('supplierId', $supplier->id)
            ->where('status', 'PENDING_SETTLEMENT')
            ->count();
        
        return view('supplier.restock', compact('batches', 'requestedCount', 'activeCount', 'pendingSettlementCount'));
    }
}
