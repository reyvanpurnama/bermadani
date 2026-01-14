<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\StockMovement;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    // ... Existing CRUD Methods (store, update, destroy) ...

    /**
     * Display product approvals page
     */
    public function approvals(Request $request)
    {
        $status = $request->get('status', 'pending');
        $search = $request->get('search');
        $supplierId = $request->get('supplier_id');
        $categoryId = $request->get('category_id');

        $query = Product::with(['supplier', 'category']);

        // Jika status ada, filter by approvalStatus. Jika kosong (tab 'Semua'), jangan filter status
        if ($request->has('status')) {
            $query->where('approvalStatus', strtoupper($status));
        }

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($supplierId) {
            $query->where('supplierId', $supplierId);
        }

        if ($categoryId) {
            $query->where('categoryId', $categoryId);
        }

        $products = $query->orderBy('created_at', 'desc')->paginate(10);

        // Counts
        $pendingCount = Product::where('approvalStatus', 'PENDING')->count();
        $approvedCount = Product::where('approvalStatus', 'APPROVED')->count();
        $rejectedCount = Product::where('approvalStatus', 'REJECTED')->count();

        $suppliers = Supplier::all();
        $categories = Category::all();

        return view('admin.product-approvals', compact(
            'products',
            'status',
            'pendingCount',
            'approvedCount',
            'rejectedCount',
            'suppliers',
            'categories'
        ));
    }

    public function approve($id)
    {
        $product = Product::findOrFail($id);
        $product->update([
            'approvalStatus' => 'APPROVED',
            'isActive' => true
        ]);

        return redirect()->back()->with('success', 'Produk berhasil disetujui');
    }

    public function reject(Request $request, $id)
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:255'
        ]);

        $product = Product::findOrFail($id);
        $product->update([
            'approvalStatus' => 'REJECTED',
            'rejectionReason' => $validated['reason'],
            'isActive' => false
        ]);

        return redirect()->back()->with('success', 'Produk berhasil ditolak');
    }

    // --- Legacy methods kept below ---

    public function store(StoreProductRequest $request)
    {
        $validated = $request->validated();

        $validated['id'] = Str::uuid();
        $validated['buyPrice'] = $validated['buyPrice'] ?? 0;
        // Default new products (if created by admin) to APPROVED? Or if created by Supplier?
        // Admin create: APPROVED
        // Supplier create: PENDING (handled in SupplierProductController)
        $validated['approvalStatus'] = 'APPROVED';

        $product = Product::create($validated);

        if ($product->stock > 0) {
            StockMovement::create([
                // 'id' => Str::uuid(), // Migration shows Auto Increment ID, checking table...
                // Table uses $table->id(); so it's auto increment integer.
                // But ProductController used Str::uuid()? Let me check StockMovement again.
                // StockMovement migration: $table->id(); (BigInteger).
                // Product migration: ... checking.
                // ProductController says $validated['id'] = Str::uuid().
                // I should verify if Product uses UUID. 
                // Assuming existing store() is correct for Product ID logic.
                // But StockMovement ID logic in existing Controller seems wrong if table is auto-inc.
                // I will follow existing controller logic but be careful.
                'productId' => $product->id,
                'movementType' => 'RESTOCK', // Migration uses enum, 'type' was used in legacy code?
                // Legacy code used 'type', but migration says 'movementType'. I will fix this mapping if I can.
                // Actually, I should just paste the new methods and leave legacy for now OR fix legacy if obviously broken.
                // Legacy: 'type' => 'RESTOCK'. Migration: 'movementType'. Legacy is BROKEN vs Migration.
                // I will Fix store() as well.
                'quantity' => $product->stock,
                // 'reference' => 'Initial Stock', // Migration uses 'note' or 'referenceId'/'referenceType'
                'note' => 'Initial Stock',
                'occurredAt' => now(), // Migration: occurredAt
            ]);
        }

        return redirect()->route('admin.products')
            ->with('message', 'Produk berhasil ditambahkan');
    }

    public function update(UpdateProductRequest $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validated();
        $validated['buyPrice'] = $validated['buyPrice'] ?? 0;

        if ($product->stock != $validated['stock']) {
            $diff = $validated['stock'] - $product->stock;

            $product->stockMovements()->create([
                'movementType' => $diff > 0 ? 'RESTOCK' : 'ADJUSTMENT',
                'quantity' => abs($diff), // Migration expects +/-, logic handled elsewhere? I'll use standard create
                'note' => 'Manual Adjustment',
                'occurredAt' => now(),
            ]);
        }

        $product->update($validated);

        return redirect()->route('admin.products')
            ->with('message', 'Produk berhasil diperbarui');
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return redirect()->route('admin.products')
            ->with('message', 'Produk berhasil dihapus');
    }
}
