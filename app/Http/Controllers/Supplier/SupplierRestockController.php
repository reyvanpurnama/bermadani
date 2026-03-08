<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Models\ConsignmentBatch;
use App\Models\ConsignmentItem;
use App\Models\Product;
use App\Models\SupplierNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

    /**
     * Supplier membuat request pengiriman harian (barang dititipkan)
     */
    public function create()
    {
        $supplier = Auth::guard('supplier')->user();

        // Hanya produk milik supplier ini yang sudah di-approve admin
        $products = Product::where('supplierId', $supplier->id)
            ->where('approvalStatus', 'APPROVED')
            ->where('isActive', true)
            ->orderBy('name')
            ->get();

        if ($products->isEmpty()) {
            return redirect()->route('supplier.restock')
                ->with('error', 'Anda belum memiliki produk yang disetujui admin. Daftarkan produk terlebih dahulu.');
        }

        return view('supplier.restock-create', compact('supplier', 'products'));
    }

    /**
     * Simpan request pengiriman harian → status REQUESTED, kasir yang akan konfirmasi
     */
    public function store(Request $request)
    {
        $supplier = Auth::guard('supplier')->user();

        $request->validate([
            'items'              => 'required|array|min:1',
            'items.*.productId'  => 'required|exists:products,id',
            'items.*.qty'        => 'required|integer|min:1|max:9999',
            'note'               => 'nullable|string|max:500',
        ]);

        // Pastikan semua produk milik supplier ini dan sudah approved
        $productIds = collect($request->items)->pluck('productId');
        $validCount = Product::whereIn('id', $productIds)
            ->where('supplierId', $supplier->id)
            ->where('approvalStatus', 'APPROVED')
            ->count();

        if ($validCount !== $productIds->count()) {
            return back()->withErrors(['items' => 'Terdapat produk yang tidak valid.'])->withInput();
        }

        DB::transaction(function () use ($supplier, $request) {
            $batch = ConsignmentBatch::create([
                'batchCode'  => ConsignmentBatch::generateBatchCode(),
                'supplierId' => $supplier->id,
                'status'     => 'REQUESTED',
                'note'       => $request->note,
            ]);

            $totalValue = 0;
            foreach ($request->items as $item) {
                $product = Product::find($item['productId']);
                if (!$product) continue;

                ConsignmentItem::create([
                    'batchId'       => $batch->id,
                    'productId'     => $product->id,
                    'initialQty'    => $item['qty'],
                    'receivedQty'   => 0,
                    'remainingQty'  => 0,
                    'soldQty'       => 0,
                    'sellPrice'     => $product->sellPrice,
                    'supplierPrice' => $product->buyPrice ?? 0,
                ]);

                $totalValue += $product->sellPrice * $item['qty'];
            }

            $batch->update(['totalValue' => $totalValue]);
        });

        return redirect()->route('supplier.restock')
            ->with('success', 'Permintaan pengiriman berhasil dikirim! Kasir akan mengkonfirmasi penerimaan barang. 📦');
    }
}

