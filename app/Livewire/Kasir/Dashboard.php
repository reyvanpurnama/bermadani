<?php

namespace App\Livewire\Kasir;

use App\Models\Product;
use App\Models\Transaction;
use Carbon\Carbon;
use Livewire\Component;

class Dashboard extends Component
{
    public function getTodaySalesProperty()
    {
        // TODO: Filter by user_id once column is added to transactions table
        return Transaction::whereDate('date', today())
            ->where('status', 'COMPLETED')
            ->sum('totalAmount');
    }

    public function getTodayTransactionsCountProperty()
    {
        // TODO: Filter by user_id once column is added to transactions table
        return Transaction::whereDate('date', today())
            ->count();
    }

    public function getLowStockProductsProperty()
    {
        return Product::active()
            ->whereColumn('stock', '<=', 'threshold')
            ->orderBy('stock', 'asc')
            ->limit(5)
            ->get();
    }

    public function getRecentTransactionsProperty()
    {
        // TODO: Filter by user_id once column is added to transactions table
        return Transaction::with('member')
            ->latest('date')
            ->latest('id')
            ->limit(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.kasir.dashboard')
            ->layout('layouts.admin', [
                'pageTitle' => 'Dashboard Kasir'
            ]);
    }
}
