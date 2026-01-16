@extends('layouts.supplier')

@section('title', 'Laporan Penjualan')

@section('content')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Riwayat Penjualan</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Daftar transaksi produk konsinyasi Anda.</p>
    </div>
    <div class="flex gap-2">
        <button class="bg-white dark:bg-darkCard text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-600 px-4 py-2 rounded-lg text-sm font-medium hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors flex items-center gap-2">
            <i class='bx bx-download'></i>
            Export Excel
        </button>
    </div>
</div>

<!-- Sales List -->
<div class="bg-white dark:bg-darkCard rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">
    
    {{-- Mobile Card View --}}
    <div class="sm:hidden divide-y divide-slate-100 dark:divide-slate-700">
        @forelse($sales as $sale)
        <div class="p-4">
            <div class="flex items-start gap-3">
                @if($sale->product->image)
                    <img src="{{ asset('storage/' . $sale->product->image) }}" alt="{{ $sale->product->name }}" class="w-12 h-12 rounded-lg object-cover flex-shrink-0">
                @else
                    <div class="w-12 h-12 rounded-lg bg-slate-100 dark:bg-slate-700 flex items-center justify-center text-slate-400 flex-shrink-0">
                        <i class='bx bx-image text-xl'></i>
                    </div>
                @endif
                <div class="min-w-0 flex-1">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <h6 class="font-semibold text-slate-900 dark:text-white truncate">{{ $sale->product->name }}</h6>
                            <p class="text-[10px] text-slate-500">{{ $sale->product->sku }}</p>
                        </div>
                        <span class="font-bold text-emerald-600 dark:text-emerald-400 text-[13px] flex-shrink-0">
                            +Rp {{ number_format($sale->quantity * $sale->product->buyPrice, 0, ',', '.') }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between mt-1.5 text-[10px] text-slate-500">
                        <span>{{ $sale->created_at->format('d M Y, H:i') }}</span>
                        <span>{{ $sale->quantity }} × Rp {{ number_format($sale->product->buyPrice, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="p-8 text-center text-slate-500 dark:text-slate-400">
            <i class='bx bx-cart-alt text-4xl mb-2 text-slate-300'></i>
            <p>Belum ada penjualan.</p>
        </div>
        @endforelse
    </div>

    {{-- Desktop Table View --}}
    <div class="hidden sm:block overflow-x-auto">
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
