@extends('layouts.supplier')

@section('title', 'Penjualan')
@section('header-title', 'Laporan Penjualan')
@section('header-subtitle', 'Daftar transaksi produk konsinyasi Anda (POS Kasir & Audit Retail CSV)')

@section('content')
    <div class="max-w-6xl mx-auto space-y-6">
        <!-- Top Navigation Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <a href="{{ route('supplier.dashboard') }}" 
                    class="inline-flex items-center gap-1.5 text-xs font-bold text-zinc-500 hover:text-[#155A6B] dark:text-zinc-400 dark:hover:text-teal-400 mb-1 transition-colors group">
                    <i class='bx bx-arrow-back text-base group-hover:-translate-x-1 transition-transform'></i>
                    Kembali ke Dasbor
                </a>
                <h1 class="text-2xl font-black text-zinc-900 dark:text-white tracking-tight">Rincian Penjualan Produk</h1>
            </div>
            
            <div class="flex gap-2">
                <button
                    class="bg-white/80 dark:bg-zinc-900/80 backdrop-blur-xl text-zinc-700 dark:text-zinc-300 border border-zinc-200/80 dark:border-zinc-800/80 px-4 py-2.5 rounded-xl text-xs font-bold hover:border-[#155A6B] transition-all flex items-center gap-2 shadow-sm">
                    <i class='bx bx-download text-base text-[#155A6B] dark:text-teal-400'></i>
                    Export Excel
                </button>
            </div>
        </div>

        <!-- Summary Cards Row -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <!-- Pendapatan Supplier Card -->
            <div class="rounded-2xl bg-gradient-to-br from-[#155A6B] to-[#1a6b80] p-5 text-white shadow-lg shadow-[#155A6B]/20">
                <span class="text-[11px] font-bold uppercase tracking-wider text-teal-100 block mb-1">Total Pendapatan Anda</span>
                <h3 class="text-2xl font-extrabold tracking-tight">Rp {{ number_format($supplierRevenue ?? 0, 0, ',', '.') }}</h3>
            </div>

            <!-- Total Omzet Retail -->
            <div class="rounded-2xl bg-white/80 dark:bg-zinc-900/80 backdrop-blur-xl p-5 border border-zinc-200/80 dark:border-zinc-800/80 shadow-md">
                <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-400 block mb-1">Total Omzet Penjualan</span>
                <h3 class="text-2xl font-extrabold text-zinc-900 dark:text-white tracking-tight">Rp {{ number_format($totalOmzet ?? 0, 0, ',', '.') }}</h3>
            </div>

            <!-- Total Unit Terjual -->
            <div class="rounded-2xl bg-white/80 dark:bg-zinc-900/80 backdrop-blur-xl p-5 border border-zinc-200/80 dark:border-zinc-800/80 shadow-md">
                <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-400 block mb-1">Total Unit Terjual</span>
                <div class="flex items-baseline gap-1">
                    <h3 class="text-2xl font-extrabold text-zinc-900 dark:text-white tracking-tight">{{ number_format($totalItemsSold ?? 0, 0, ',', '.') }}</h3>
                    <span class="text-xs text-zinc-400 font-medium">Pcs</span>
                </div>
            </div>
        </div>

        <!-- Sales Data List & Table -->
        <div
            class="rounded-3xl bg-white/80 dark:bg-zinc-900/80 backdrop-blur-xl border border-zinc-200/80 dark:border-zinc-800/80 shadow-xl overflow-hidden">

            {{-- Mobile Card View --}}
            <div class="sm:hidden divide-y divide-zinc-200/60 dark:divide-zinc-800/60">
                @forelse($sales as $sale)
                    <div class="p-4">
                        <div class="flex items-start gap-3">
                            @if(!empty($sale->product->image))
                                <img src="{{ asset('storage/' . $sale->product->image) }}" alt="{{ $sale->product->name }}"
                                    class="w-12 h-12 rounded-xl object-cover flex-shrink-0 shadow-sm">
                            @else
                                <div
                                    class="w-12 h-12 rounded-xl bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-zinc-400 flex-shrink-0">
                                    <i class='bx bx-image text-2xl'></i>
                                </div>
                            @endif
                            <div class="min-w-0 flex-1">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="min-w-0">
                                        <h6 class="font-bold text-sm text-zinc-900 dark:text-white truncate">{{ $sale->product->name ?? 'Produk' }}</h6>
                                        <span class="inline-block px-2 py-0.5 rounded-full text-[9px] font-bold bg-teal-50 dark:bg-teal-950/50 text-[#155A6B] dark:text-teal-300 border border-teal-200/60 dark:border-teal-800/40">
                                            {{ $sale->source ?? 'POS Kasir' }}
                                        </span>
                                    </div>
                                    <span class="font-extrabold text-[#155A6B] dark:text-teal-400 text-xs shrink-0">
                                        +Rp {{ number_format($sale->supplier_revenue ?? ($sale->quantity * ($sale->product->buyPrice ?? 0)), 0, ',', '.') }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between mt-2 text-[11px] text-zinc-500 font-medium">
                                    <span>{{ $sale->created_at ? $sale->created_at->format('d M Y') : '-' }}</span>
                                    <span>{{ $sale->quantity }} × Rp {{ number_format($sale->buy_price ?? $sale->product->buyPrice ?? 0, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-10 text-center text-zinc-400">
                        <i class='bx bx-cart-alt text-5xl mb-2 text-zinc-300 dark:text-zinc-700'></i>
                        <p class="text-xs font-semibold">Belum ada riwayat penjualan.</p>
                    </div>
                @endforelse
            </div>

            {{-- Desktop Table View --}}
            <div class="hidden sm:block overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-zinc-50/80 dark:bg-zinc-800/40 border-b border-zinc-200/80 dark:border-zinc-800/80">
                        <tr>
                            <th class="px-6 py-3.5 text-[10px] font-extrabold text-zinc-400 uppercase tracking-wider">Tanggal & Sumber</th>
                            <th class="px-6 py-3.5 text-[10px] font-extrabold text-zinc-400 uppercase tracking-wider">Produk</th>
                            <th class="px-6 py-3.5 text-[10px] font-extrabold text-zinc-400 uppercase tracking-wider text-center">Jumlah</th>
                            <th class="px-6 py-3.5 text-[10px] font-extrabold text-zinc-400 uppercase tracking-wider text-right">Harga per Unit</th>
                            <th class="px-6 py-3.5 text-[10px] font-extrabold text-zinc-400 uppercase tracking-wider text-right">Pendapatan Supplier</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200/60 dark:divide-zinc-800/60 text-xs">
                        @forelse($sales as $sale)
                            <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30 transition-colors">
                                <td class="px-6 py-4 text-zinc-600 dark:text-zinc-400 font-medium">
                                    {{ $sale->created_at ? $sale->created_at->format('d M Y') : '-' }}
                                    <span class="block text-[10px] font-extrabold text-[#155A6B] dark:text-teal-400 mt-0.5">
                                        {{ $sale->source ?? 'POS Kasir' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div>
                                            <h6 class="font-bold text-zinc-900 dark:text-white">{{ $sale->product->name ?? 'Produk' }}</h6>
                                            <p class="text-[11px] text-zinc-400 font-medium">{{ $sale->product->sku ?? '-' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center text-zinc-900 dark:text-white font-extrabold">
                                    {{ $sale->quantity }}
                                </td>
                                <td class="px-6 py-4 text-right text-zinc-600 dark:text-zinc-400 font-medium">
                                    Rp {{ number_format($sale->buy_price ?? $sale->product->buyPrice ?? 0, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-right font-extrabold text-[#155A6B] dark:text-teal-400">
                                    Rp {{ number_format($sale->supplier_revenue ?? ($sale->quantity * ($sale->product->buyPrice ?? 0)), 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-zinc-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <i class='bx bx-cart-alt text-5xl mb-2 text-zinc-300 dark:text-zinc-700'></i>
                                        <p class="text-xs font-semibold">Belum ada riwayat penjualan.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-zinc-200/80 dark:border-zinc-800/80">
                {{ $sales->links() }}
            </div>
        </div>
    </div>
@endsection