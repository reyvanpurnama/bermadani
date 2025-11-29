<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;

class Products extends Component
{
    use WithPagination;

    public $search = '';
    public $categoryFilter = '';
    public $stockFilter = '';
    public $perPage = 15;

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
                    $q->whereRaw('stock <= threshold');
                } elseif ($this->stockFilter === 'out') {
                    $q->where('stock', 0);
                } elseif ($this->stockFilter === 'available') {
                    $q->whereRaw('stock > threshold');
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
            'total' => Product::count(),
            'low_stock' => Product::whereRaw('stock <= threshold')->count(),
            'out_of_stock' => Product::where('stock', 0)->count(),
            'total_value' => Product::sum(\DB::raw('stock * sellPrice'))
        ];
    }

    public function render()
    {
        // dd('Products component render called', $this->products->count());
        
        return view('livewire.products', [
            'products' => $this->products,
            'categories' => $this->categories,
            'stats' => $this->stats
        ]);
    }
}
