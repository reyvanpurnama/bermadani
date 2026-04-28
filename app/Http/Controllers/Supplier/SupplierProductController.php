<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SupplierProductController extends Controller
{
    private function canSupplierModify(Product $product): bool
    {
        return in_array($product->approvalStatus, ['PENDING', 'REJECTED'], true);
    }

    public function index()
    {
        $supplier = Auth::guard('supplier')->user();
        $products = Product::where('supplierId', $supplier->id)
            ->latest()
            ->paginate(10);
            
        return view('supplier.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('supplier.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $supplier = Auth::guard('supplier')->user();
        
        // Check limit
        if ($supplier->currentActiveProducts >= $supplier->maxActiveProducts) {
            return back()->with('error', 'Anda telah mencapai batas maksimal produk aktif.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:2048', // 2MB Max
        ]);

        // Handle Image Upload
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        // Generate SKU automatically or use input? 
        // For now, let's generate a simple SKU: SUP-{ID}-{RAND}
        $sku = 'SUP-' . $supplier->id . '-' . strtoupper(Str::random(6));

        $product = Product::create([
            'supplierId' => $supplier->id,
            'categoryId' => $validated['category_id'],
            'name' => $validated['name'],
            'sku' => $sku,
            'description' => $validated['description'],
            'buyPrice' => $validated['price'],
            'sellPrice' => 0,
            'stock' => 0, // Stock managed via consignment batch
            'image' => $imagePath,
            'status' => 'INACTIVE',
            'isConsignment' => true,
            'ownershipType' => 'TITIPAN',
            'isActive' => false,
            'approvalStatus' => 'PENDING',
        ]);

        // Don't increment active products count until approved
        // $supplier->increment('currentActiveProducts');

        return redirect()->route('supplier.products.index')->with('success', 'Produk berhasil diajukan dan menunggu persetujuan admin.');
    }

    public function edit(Product $product)
    {
        // Ensure ownership
        if ($product->supplierId !== Auth::guard('supplier')->id()) {
            abort(403);
        }
        if (! $this->canSupplierModify($product)) {
            return redirect()->route('supplier.products.index')
                ->with('error', 'Produk yang sudah disetujui tidak dapat diedit dari portal supplier.');
        }

        $categories = Category::all();
        return view('supplier.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        // Ensure ownership
        if ($product->supplierId !== Auth::guard('supplier')->id()) {
            abort(403);
        }
        if (! $this->canSupplierModify($product)) {
            return redirect()->route('supplier.products.index')
                ->with('error', 'Produk yang sudah disetujui tidak dapat diperbarui dari portal supplier.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:2048',
        ]);

        $imagePath = $product->image;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        // Update product - supplier can only update buyPrice, not sellPrice or stock
        $product->update([
            'categoryId' => $validated['category_id'],
            'name' => $validated['name'],
            'description' => $validated['description'],
            'buyPrice' => $validated['price'],
            'image' => $imagePath,
        ]);

        return redirect()->route('supplier.products.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        // Ensure ownership
        if ($product->supplierId !== Auth::guard('supplier')->id()) {
            abort(403);
        }
        if (! $this->canSupplierModify($product)) {
            return redirect()->route('supplier.products.index')
                ->with('error', 'Produk yang sudah disetujui tidak dapat dihapus dari portal supplier.');
        }

        $supplier = Auth::guard('supplier')->user();
        
        if ($product->status === 'ACTIVE') {
            $supplier->decrement('currentActiveProducts');
        }

        $product->delete();
        return redirect()->route('supplier.products.index')->with('success', 'Produk berhasil dihapus.');
    }
}
