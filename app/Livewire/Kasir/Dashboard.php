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
        return Transaction::whereDate('date', today())
            ->where('status', 'COMPLETED')
            ->where('userId', auth()->id())
            ->sum('totalAmount');
    }

    public function getTodayTransactionsCountProperty()
    {
        return Transaction::whereDate('date', today())
            ->where('userId', auth()->id())
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
        return Transaction::with('member')
            ->where('userId', auth()->id())
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
