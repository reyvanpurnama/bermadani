<?php

namespace App\Livewire;

use App\Models\FinancialTransaction;
use Livewire\Component;
use Livewire\WithPagination;

class ManualTransactionHistory extends Component
{
    use WithPagination;

    public $search = '';
    public $typeFilter = '';
    public $dateFilter = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = FinancialTransaction::with('user')
            ->latest('transactionDate')
            ->latest('id');

        if ($this->search) {
            $query->where(function($q) {
                $q->where('description', 'like', '%' . $this->search . '%')
                  ->orWhere('category', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->typeFilter) {
            $query->where('type', $this->typeFilter);
        }

        if ($this->dateFilter) {
            $query->whereDate('transactionDate', $this->dateFilter);
        }

        return view('livewire.manual-transaction-history', [
            'transactions' => $query->paginate(10)
        ]);
    }
}
