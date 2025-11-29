<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;

class Categories extends Component
{
    use WithPagination;

    public $search = '';
    public $categoryId;
    public $name = '';
    public $slug = '';
    public $icon = '';
    public $description = '';
    public $isActive = true;
    public $showModal = false;
    public $isEditing = false;

    protected $rules = [
        'name' => 'required|string|max:100',
        'slug' => 'nullable|string|max:100',
        'icon' => 'nullable|string|max:10',
        'description' => 'nullable|string|max:255',
        'isActive' => 'boolean',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showModal = true;
        $this->isEditing = false;
    }

    public function openEditModal($id)
    {
        $category = Category::findOrFail($id);
        
        $this->categoryId = $category->id;
        $this->name = $category->name;
        $this->slug = $category->slug;
        $this->icon = $category->icon;
        $this->description = $category->description;
        $this->isActive = $category->isActive;
        
        $this->showModal = true;
        $this->isEditing = true;
    }

    public function save()
    {
        $this->validate();

        if ($this->isEditing) {
            $category = Category::findOrFail($this->categoryId);
            $category->update([
                'name' => $this->name,
                'slug' => $this->slug ?: \Str::slug($this->name),
                'icon' => $this->icon,
                'description' => $this->description,
                'isActive' => $this->isActive,
            ]);
            
            session()->flash('message', 'Kategori berhasil diperbarui');
        } else {
            Category::create([
                'id' => \Str::uuid(),
                'name' => $this->name,
                'slug' => $this->slug ?: \Str::slug($this->name),
                'icon' => $this->icon,
                'description' => $this->description,
                'isActive' => $this->isActive,
            ]);
            
            session()->flash('message', 'Kategori berhasil ditambahkan');
        }

        $this->closeModal();
    }

    public function delete($id)
    {
        $category = Category::findOrFail($id);
        
        // Check if has products
        if ($category->products()->count() > 0) {
            session()->flash('error', 'Kategori tidak bisa dihapus karena masih memiliki produk');
            return;
        }
        
        $category->delete();
        session()->flash('message', 'Kategori berhasil dihapus');
    }

    public function toggleStatus($id)
    {
        $category = Category::findOrFail($id);
        $category->update(['isActive' => !$category->isActive]);
        
        session()->flash('message', 'Status kategori berhasil diubah');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->categoryId = null;
        $this->name = '';
        $this->slug = '';
        $this->icon = '';
        $this->description = '';
        $this->isActive = true;
        $this->resetErrorBag();
    }

    public function getCategoriesProperty()
    {
        return Category::withCount('products')
            ->when($this->search, function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('slug', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(10);
    }

    public function getStatsProperty()
    {
        $categories = Category::withCount('products')->get();
        $topCategory = $categories->sortByDesc('products_count')->first();
        
        return [
            'total' => $categories->count(),
            'active' => $categories->where('isActive', true)->count(),
            'top_category' => $topCategory ? $topCategory->name : '-',
            'top_percentage' => $topCategory && $topCategory->products_count > 0 
                ? round(($topCategory->products_count / Product::count()) * 100) 
                : 0
        ];
    }

    public function render()
    {
        return view('livewire.categories', [
            'categories' => $this->categories,
            'stats' => $this->stats
        ]);
    }
}
