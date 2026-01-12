<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Models\RestockRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupplierRestockController extends Controller
{
    public function index()
    {
        $supplier = Auth::guard('supplier')->user();
        
        $restockRequests = RestockRequest::where('supplierId', $supplier->id)
            ->with(['product.category', 'requestedByUser'])
            ->latest()
            ->paginate(15);
        
        $pendingCount = RestockRequest::where('supplierId', $supplier->id)
            ->where('status', 'PENDING')
            ->count();
        
        $acceptedCount = RestockRequest::where('supplierId', $supplier->id)
            ->where('status', 'ACCEPTED')
            ->count();
        
        return view('supplier.restock', compact('restockRequests', 'pendingCount', 'acceptedCount'));
    }

    public function respond(Request $request, $id)
    {
        $validated = $request->validate([
            'action' => 'required|in:accept,reject',
            'confirmed_qty' => 'required_if:action,accept|integer|min:1',
            'supplier_note' => 'nullable|string|max:500',
        ]);

        try {
            $restockRequest = RestockRequest::findOrFail($id);
            
            // Verify ownership
            if ($restockRequest->supplierId !== Auth::guard('supplier')->id()) {
                abort(403, 'Unauthorized');
            }

            if ($validated['action'] === 'accept') {
                $restockRequest->update([
                    'status' => 'ACCEPTED',
                    'confirmedQty' => $validated['confirmed_qty'],
                    'supplierNote' => $validated['supplier_note'],
                    'respondedAt' => now(),
                ]);

                return redirect()
                    ->back()
                    ->with('success', 'Request restock berhasil diterima. Segera kirim produk ke koperasi.');
            } else {
                $restockRequest->update([
                    'status' => 'REJECTED',
                    'supplierNote' => $validated['supplier_note'] ?? 'Ditolak oleh supplier',
                    'respondedAt' => now(),
                ]);

                return redirect()
                    ->back()
                    ->with('success', 'Request restock ditolak.');
            }
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
