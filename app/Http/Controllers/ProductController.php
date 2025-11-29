<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:products,sku|max:50',
            'categoryId' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'sellPrice' => 'required|numeric|min:0',
            'buyPrice' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'threshold' => 'required|integer|min:1',
        ]);

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

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:50|unique:products,sku,' . $id,
            'categoryId' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'sellPrice' => 'required|numeric|min:0',
            'buyPrice' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'threshold' => 'required|integer|min:1',
        ]);

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
