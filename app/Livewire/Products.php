<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\Category;
use App\Models\ConsignmentBatch;
use App\Models\ConsignmentItem;
use App\Models\SupplierNotification;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class Products extends Component
{
    use WithPagination;

    public $search = '';
    public $categoryFilter = '';
    public $stockFilter = '';
    public $perPage = 10;
    
    // Category Modal Properties
    public $showCategoryModal = false;
    public $categoryId;
    public $categoryName = '';
    public $categorySlug = '';
    public $categoryIcon = '';
    public $categoryDescription = '';
    public $categoryIsActive = true;
    public $isEditingCategory = false;
    public $categorySearch = '';

    // Quick Batch Modal Properties
    public $showQuickBatchModal = false;
    public $quickBatchProduct = null;
    public $quickBatchQty = 1;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingCategoryFilter()
    {
        $this->resetPage();
    }

    public function updatingStockFilter()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->categoryFilter = '';
        $this->stockFilter = '';
        $this->resetPage();
    }

    public function deleteProduct($productId)
    {
        $product = Product::find($productId);
        
        if ($product) {
            $product->delete();
            session()->flash('message', 'Produk berhasil dihapus');
        }
    }

    public function getProductsProperty()
    {
        $query = Product::with('category')
            ->when($this->search, function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('sku', 'like', '%' . $this->search . '%');
            })
            ->when($this->categoryFilter, function ($q) {
                $q->where('categoryId', $this->categoryFilter);
            })
            ->when($this->stockFilter, function ($q) {
                if ($this->stockFilter === 'low') {
                    $q->whereRaw('stock <= threshold')->where('stock', '>', 0)->where('approvalStatus', 'APPROVED');
                } elseif ($this->stockFilter === 'out') {
                    $q->where('stock', 0)->whereNull('supplierId')->where('approvalStatus', 'APPROVED');
                } elseif ($this->stockFilter === 'available') {
                    $q->whereRaw('stock > threshold')->where('approvalStatus', 'APPROVED');
                } elseif ($this->stockFilter === 'pending') {
                    $q->where('approvalStatus', 'PENDING');
                } elseif ($this->stockFilter === 'rejected') {
                    $q->where('approvalStatus', 'REJECTED');
                } elseif ($this->stockFilter === 'consignment_waiting') {
                    // Only show consignment products that have REQUESTED batches
                    $q->where('approvalStatus', 'APPROVED')
                      ->whereNotNull('supplierId')
                      ->whereHas('consignmentItems', function($query) {
                          $query->whereHas('batch', function($batchQuery) {
                              $batchQuery->where('status', 'REQUESTED');
                          });
                      });
                }
            });

        return $query->latest()->paginate($this->perPage);
    }

    public function getCategoriesProperty()
    {
        return Category::orderBy('name')->get();
    }

    public function getStatsProperty()
    {
        return [
            'total' => Product::where('approvalStatus', 'APPROVED')->count(),
            'low_stock' => Product::where('approvalStatus', 'APPROVED')->whereRaw('stock <= threshold')->where('stock', '>', 0)->count(),
            'out_of_stock' => Product::where('approvalStatus', 'APPROVED')->where('stock', 0)->whereNull('supplierId')->count(),
            'consignment_waiting' => Product::where('approvalStatus', 'APPROVED')
                ->whereNotNull('supplierId')
                ->whereHas('consignmentItems', function($query) {
                    $query->whereHas('batch', function($batchQuery) {
                        $batchQuery->where('status', 'REQUESTED');
                    });
                })->count(),
            'total_value' => Product::where('approvalStatus', 'APPROVED')->sum(\DB::raw('stock * sellPrice'))
        ];
    }

    // ===== Category Modal Methods =====
    
    public function openCategoryModal()
    {
        $this->showCategoryModal = true;
        $this->resetCategoryForm();
    }

    public function closeCategoryModal()
    {
        $this->showCategoryModal = false;
        $this->resetCategoryForm();
    }

    public function resetCategoryForm()
    {
        $this->categoryId = null;
        $this->categoryName = '';
        $this->categorySlug = '';
        $this->categoryIcon = '';
        $this->categoryDescription = '';
        $this->categoryIsActive = true;
        $this->isEditingCategory = false;
        $this->resetErrorBag();
    }

    public function createCategory()
    {
        $this->resetCategoryForm();
        $this->isEditingCategory = false;
    }

    public function editCategory($id)
    {
        $category = Category::findOrFail($id);
        
        $this->categoryId = $category->id;
        $this->categoryName = $category->name;
        $this->categorySlug = $category->slug;
        $this->categoryIcon = $category->icon;
        $this->categoryDescription = $category->description;
        $this->categoryIsActive = $category->isActive;
        $this->isEditingCategory = true;
    }

    public function saveCategory()
    {
        $this->validate([
            'categoryName' => 'required|string|max:100',
            'categorySlug' => 'nullable|string|max:100',
            'categoryIcon' => 'nullable|string|max:10',
            'categoryDescription' => 'nullable|string|max:255',
            'categoryIsActive' => 'boolean',
        ]);

        if ($this->isEditingCategory) {
            $category = Category::findOrFail($this->categoryId);
            $category->update([
                'name' => $this->categoryName,
                'slug' => $this->categorySlug ?: Str::slug($this->categoryName),
                'icon' => $this->categoryIcon,
                'description' => $this->categoryDescription,
                'isActive' => $this->categoryIsActive,
            ]);
            
            session()->flash('categoryMessage', 'Kategori berhasil diperbarui');
        } else {
            Category::create([
                'id' => Str::uuid(),
                'name' => $this->categoryName,
                'slug' => $this->categorySlug ?: Str::slug($this->categoryName),
                'icon' => $this->categoryIcon,
                'description' => $this->categoryDescription,
                'isActive' => $this->categoryIsActive,
            ]);
            
            session()->flash('categoryMessage', 'Kategori berhasil ditambahkan');
        }

        $this->resetCategoryForm();
    }

    public function deleteCategory($id)
    {
        $category = Category::findOrFail($id);
        
        // Check if has products
        if ($category->products()->count() > 0) {
            session()->flash('categoryError', 'Kategori tidak bisa dihapus karena masih memiliki produk');
            return;
        }
        
        $category->delete();
        session()->flash('categoryMessage', 'Kategori berhasil dihapus');
    }

    public function toggleCategoryStatus($id)
    {
        $category = Category::findOrFail($id);
        $category->update(['isActive' => !$category->isActive]);
        
        session()->flash('categoryMessage', 'Status kategori berhasil diubah');
    }

    public function getCategoryListProperty()
    {
        return Category::withCount('products')
            ->when($this->categorySearch, function ($q) {
                $q->where('name', 'like', '%' . $this->categorySearch . '%');
            })
            ->orderBy('name')
            ->get();
    }

    // ===== Quick Batch Modal Methods =====
    
    public function openQuickBatchModal($productId)
    {
        $this->quickBatchProduct = Product::with('supplier')->find($productId);
        $this->quickBatchQty = max(1, $this->quickBatchProduct->threshold ?? 10);
        $this->showQuickBatchModal = true;
    }

    public function closeQuickBatchModal()
    {
        $this->showQuickBatchModal = false;
        $this->quickBatchProduct = null;
        $this->quickBatchQty = 1;
    }

    public function saveQuickBatch()
    {
        $this->validate([
            'quickBatchQty' => 'required|integer|min:1',
        ]);

        if (!$this->quickBatchProduct || !$this->quickBatchProduct->supplierId) {
            $this->dispatch('notify', ['message' => 'Produk tidak valid', 'type' => 'error']);
            return;
        }

        DB::transaction(function () {
            $product = $this->quickBatchProduct;
            $sellPrice = $product->sellPrice;
            $feePercent = $product->profitShareRate ?? 10; // Use product's profit share or default 10%
            $priceAfterFee = $sellPrice * (1 - ($feePercent / 100));

            // Create batch with REQUESTED status (not ACTIVE yet)
            $batch = ConsignmentBatch::create([
                'batchCode' => ConsignmentBatch::generateBatchCode(),
                'supplierId' => $product->supplierId,
                'status' => 'REQUESTED', // Waiting for supplier to deliver
                'receivedAt' => null, // Not received yet
                'note' => 'Permintaan stok dari inventaris',
                'totalValue' => $sellPrice * $this->quickBatchQty,
            ]);

            ConsignmentItem::create([
                'batchId' => $batch->id,
                'productId' => $product->id,
                'initialQty' => $this->quickBatchQty,
                'remainingQty' => $this->quickBatchQty,
                'sellPrice' => $sellPrice,
                'supplierPrice' => $product->buyPrice, // Harga dari supplier
            ]);

            // DON'T add stock yet - stock will be added when batch is received/confirmed

            // Send notification to supplier
            SupplierNotification::notifyBatchRequest(
                $product->supplierId,
                $batch->batchCode,
                [['productId' => $product->id, 'initialQty' => $this->quickBatchQty]]
            );
        });

        $this->closeQuickBatchModal();
        $this->dispatch('notify', ['message' => 'Permintaan stok berhasil dikirim ke supplier', 'type' => 'success']);
    }

    public function hasPendingBatch($productId)
    {
        return ConsignmentBatch::whereHas('items', function($q) use ($productId) {
            $q->where('productId', $productId);
        })->where('status', 'REQUESTED')->exists();
    }

    public function hasActiveBatch($productId)
    {
        return ConsignmentBatch::whereHas('items', function($q) use ($productId) {
            $q->where('productId', $productId);
        })->whereIn('status', ['ACTIVE', 'PENDING_SETTLEMENT', 'SETTLED'])->exists();
    }

    public function hasPendingSettlement($productId)
    {
        return ConsignmentBatch::whereHas('items', function($q) use ($productId) {
            $q->where('productId', $productId);
        })->where('status', 'PENDING_SETTLEMENT')->exists();
    }

    public function render()
    {
        // dd('Products component render called', $this->products->count());
        
        return view('livewire.products', [
            'products' => $this->products,
            'categories' => $this->categories,
            'stats' => $this->stats,
            'categoryList' => $this->categoryList
        ]);
    }
}
