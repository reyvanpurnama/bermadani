<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\StockMovement;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function store(StoreProductRequest $request)
    {
        $validated = $request->validated();

        $validated['id'] = Str::uuid();

        // Generate SKU if not provided
        if (empty($validated['sku'])) {
            $prefix = 'PRD'; // You might want dynamic prefix based on category
            $validated['sku'] = $prefix . date('ymd') . strtoupper(Str::random(4));

            // Ensure uniqueness (simple check, for robust loop usually needed but this is low collision chance)
            while (Product::where('sku', $validated['sku'])->exists()) {
                $validated['sku'] = $prefix . date('ymd') . strtoupper(Str::random(4));
            }
        }

        $validated['buyPrice'] = $validated['buyPrice'] ?? 0;

        // Admin-created products are auto-approved (owned by koperasi)
        $validated['approvalStatus'] = 'APPROVED';
        $validated['approvedAt'] = now();
        $validated['approvedBy'] = auth()->id();
        $validated['isActive'] = true;

        $product = Product::create($validated);

        // Record initial stock movement
        if ($product->stock > 0) {
            StockMovement::create([
                'productId' => $product->id,
                'movementType' => 'RESTOCK',
                'quantity' => $product->stock,
                'note' => 'Initial Stock',
                'occurredAt' => now(),
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

        // Track stock changes
        if ($product->stock != $validated['stock']) {
            $diff = $validated['stock'] - $product->stock;

            StockMovement::create([
                'productId' => $product->id,
                'movementType' => $diff > 0 ? 'RESTOCK' : 'ADJUSTMENT',
                'quantity' => abs($diff),
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
