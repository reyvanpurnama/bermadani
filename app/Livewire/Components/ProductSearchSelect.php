<?php

namespace App\Livewire\Components;

use Livewire\Component;
use App\Models\Product;

class ProductSearchSelect extends Component
{
    public $query = '';
    public $results = [];
    public $selectedName = '';
    public $extraData = null; // To pass rawName or context

    public function updatedQuery()
    {
        $q = trim($this->query);
        if (strlen($q) < 2) {
            $this->results = [];
            return;
        }

        $this->results = Product::query()
            ->with('supplier')
            ->where(function($query) use ($q) {
                $query->where('name', 'like', '%' . $q . '%')
                      ->orWhere('sku', 'like', '%' . $q . '%');
            })
            ->limit(10)
            ->get();
    }

    public function selectResult($id, $name)
    {
        $this->selectedName = $name;
        $this->query = '';
        $this->results = [];

        $this->dispatch('audit:product-mapped', [
            'productId' => $id,
            'rawName' => $this->extraData
        ]);
    }

    public function render()
    {
        return view('livewire.components.product-search-select');
    }
}
