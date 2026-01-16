@extends('layouts.supplier')

@section('title', 'Laporan Penjualan')

@section('content')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Laporan Penjualan</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Pantau penjualan produk konsinyasi Anda.</p>
    </div>
    <div class="flex gap-2">
        <button class="bg-white dark:bg-darkCard text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-600 px-4 py-2 rounded-lg text-sm font-medium hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors flex items-center gap-2">
            <i class='bx bx-download'></i>
            Export Excel
        </button>
    </div>
</div>

<!-- Stats Overview -->
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
    <div class="bg-white dark:bg-darkCard p-4 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                <i class='bx bx-shopping-bag text-lg'></i>
            </div>
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Terjual</span>
        </div>
        <h3 class="text-2xl font-bold text-slate-900 dark:text-white">{{ number_format($totalItemsSold, 0, ',', '.') }} <span class="text-sm font-normal text-slate-500">Unit</span></h3>
    </div>

    <div class="bg-white dark:bg-darkCard p-4 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                <i class='bx bx-wallet text-lg'></i>
            </div>
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Pendapatan Anda</span>
        </div>
        <h3 class="text-2xl font-bold text-slate-900 dark:text-white">Rp {{ number_format($supplierRevenue, 0, ',', '.') }}</h3>
        <p class="text-xs text-slate-500 mt-1">Yang akan Anda terima</p>
    </div>
</div>

<!-- Sales Table -->
<div class="bg-white dark:bg-darkCard rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-100 dark:border-slate-700">
                <tr>
                    <th class="px-6 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Tanggal</th>
                    <th class="px-6 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Produk</th>
                    <th class="px-6 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Jumlah</th>
                    <th class="px-6 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Harga per Unit</th>
                    <th class="px-6 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Pendapatan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                @forelse($sales as $sale)
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                    <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">
                        {{ $sale->created_at->format('d M Y H:i') }}
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div>
                                <h6 class="font-medium text-slate-900 dark:text-white">{{ $sale->product->name }}</h6>
                                <p class="text-xs text-slate-500">{{ $sale->product->sku }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center text-sm text-slate-900 dark:text-white font-medium">
                        {{ $sale->quantity }}
                    </td>
                    <td class="px-6 py-4 text-right text-sm text-slate-600 dark:text-slate-400">
                        Rp {{ number_format($sale->product->buyPrice, 0, ',', '.') }}
                    </td>
                    <td class="px-6 py-4 text-right text-sm font-bold text-emerald-600 dark:text-emerald-400">
                        Rp {{ number_format($sale->quantity * $sale->product->buyPrice, 0, ',', '.') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-slate-500 dark:text-slate-400">
                        <div class="flex flex-col items-center justify-center">
                            <i class='bx bx-cart-alt text-4xl mb-2 text-slate-300'></i>
                            <p>Belum ada penjualan.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-700">
        {{ $sales->links() }}
    </div>
</div>
@endsection
