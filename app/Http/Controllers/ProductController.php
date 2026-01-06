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
        $validated['buyPrice'] = $validated['buyPrice'] ?? 0;

        $product = Product::create($validated);

        // Record initial stock movement
        if ($product->stock > 0) {
            StockMovement::create([
                'id' => Str::uuid(),
                'productId' => $product->id,
                'type' => 'RESTOCK',
                'quantity' => $product->stock,
                'reference' => 'Initial Stock',
                'date' => now(),
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
                'id' => Str::uuid(),
                'productId' => $product->id,
                'type' => $diff > 0 ? 'RESTOCK' : 'ADJUSTMENT',
                'quantity' => abs($diff),
                'reference' => 'Manual Adjustment',
                'date' => now(),
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
