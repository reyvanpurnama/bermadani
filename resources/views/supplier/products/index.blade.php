@extends('layouts.supplier')

@section('title', 'Produk Saya')

@section('content')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Produk Saya</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Kelola daftar produk konsinyasi Anda.</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('supplier.products.create') }}" class="bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2">
            <i class='bx bx-plus'></i>
            Tambah Produk
        </a>
    </div>
</div>

<!-- Stats Overview -->
@php
    $supplier = Auth::guard('supplier')->user();
    $totalProducts = $products->total();
    $activeCount = \App\Models\Product::where('supplierId', $supplier->id)
        ->where('approvalStatus', 'APPROVED')
        ->where('isActive', true)
        ->count();
    $pendingCount = \App\Models\Product::where('supplierId', $supplier->id)
        ->where('approvalStatus', 'PENDING')
        ->count();
    $rejectedCount = \App\Models\Product::where('supplierId', $supplier->id)
        ->where('approvalStatus', 'REJECTED')
        ->count();
@endphp
<div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-6">
    <div class="bg-white dark:bg-darkCard p-4 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400">
                <i class='bx bx-box text-lg'></i>
            </div>
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Produk</span>
        </div>
        <h3 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $totalProducts }}</h3>
    </div>
    
    <div class="bg-white dark:bg-darkCard p-4 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                <i class='bx bx-check-circle text-lg'></i>
            </div>
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Aktif</span>
        </div>
        <h3 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $activeCount }}</h3>
        <p class="text-xs text-slate-500 mt-1">Dari maks {{ $supplier->maxActiveProducts }} produk</p>
    </div>

    <div class="bg-white dark:bg-darkCard p-4 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-8 h-8 rounded-lg bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center text-amber-600 dark:text-amber-400">
                <i class='bx bx-time text-lg'></i>
            </div>
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Menunggu Review</span>
        </div>
        <h3 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $pendingCount }}</h3>
    </div>

    <div class="bg-white dark:bg-darkCard p-4 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-8 h-8 rounded-lg bg-rose-50 dark:bg-rose-900/30 flex items-center justify-center text-rose-600 dark:text-rose-400">
                <i class='bx bx-x-circle text-lg'></i>
            </div>
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Ditolak</span>
        </div>
        <h3 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $rejectedCount }}</h3>
    </div>
</div>

<!-- Products Table -->
<div class="bg-white dark:bg-darkCard rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">
    <!-- Filters -->
    <div class="p-4 border-b border-slate-100 dark:border-slate-700 flex flex-col sm:flex-row gap-4 justify-between">
        <div class="relative w-full sm:w-64">
            <i class='bx bx-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400'></i>
            <input type="text" placeholder="Cari produk..." class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 text-sm rounded-lg pl-10 pr-4 py-2 outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
        </div>
        <div class="flex gap-2">
            <select class="bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 text-sm rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary cursor-pointer">
                <option value="">Semua Kategori</option>
                <!-- Categories would be populated here -->
            </select>
            <select class="bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 text-sm rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary cursor-pointer">
                <option value="">Semua Status</option>
                <option value="active">Aktif</option>
                <option value="inactive">Non-aktif</option>
            </select>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-100 dark:border-slate-700">
                <tr>
                    <th class="px-6 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Produk</th>
                    <th class="px-6 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Kategori</th>
                    <th class="px-6 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Harga</th>
                    <th class="px-6 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Fee Koperasi</th>
                    <th class="px-6 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Stok</th>
                    <th class="px-6 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Status</th>
                    <th class="px-6 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                @forelse($products as $product)
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-slate-100 dark:bg-slate-700 flex items-center justify-center text-slate-400">
                                <i class='bx bx-image text-xl'></i>
                            </div>
                            <div>
                                <h6 class="font-medium text-slate-900 dark:text-white">{{ $product->name }}</h6>
                                <p class="text-xs text-slate-500">{{ $product->sku }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">
                        {{ $product->category->name ?? '-' }}
                    </td>
                    <td class="px-6 py-4 text-sm">
                        @if($product->approvalStatus === 'APPROVED')
                            <div>
                                <span class="font-medium text-slate-900 dark:text-white">Rp {{ number_format($product->sellPrice, 0, ',', '.') }}</span>
                                <span class="text-xs text-slate-400 block">Jual</span>
                            </div>
                        @else
                            <div>
                                <span class="font-medium text-slate-900 dark:text-white">Rp {{ number_format($product->buyPrice, 0, ',', '.') }}</span>
                                <span class="text-xs text-slate-400 block">Harga Ajuan</span>
                            </div>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                            {{ number_format($product->profitShareRate ?? 0, 0) }}%
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $product->stock > 10 ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-400' }}">
                            {{ $product->stock }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($product->approvalStatus === 'PENDING')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400">
                                Menunggu Review
                            </span>
                        @elseif($product->approvalStatus === 'REJECTED')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-400 cursor-help" title="Alasan: {{ $product->rejectionReason ?? 'Tidak ada alasan' }}">
                                Ditolak
                            </span>
                        @elseif($product->status === 'ACTIVE')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400">
                                Aktif
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-400">
                                Non-aktif
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('supplier.products.edit', $product->id) }}" class="p-1 text-slate-400 hover:text-primary transition-colors">
                                <i class='bx bx-edit text-lg'></i>
                            </a>
                            <form action="{{ route('supplier.products.destroy', $product->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1 text-slate-400 hover:text-rose-500 transition-colors">
                                    <i class='bx bx-trash text-lg'></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-8 text-center text-slate-500 dark:text-slate-400">
                        <div class="flex flex-col items-center justify-center">
                            <i class='bx bx-box text-4xl mb-2 text-slate-300'></i>
                            <p>Belum ada produk.</p>
                            <a href="{{ route('supplier.products.create') }}" class="text-primary hover:underline mt-1 text-sm">Tambah produk pertama Anda</a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-700">
        {{ $products->links() }}
    </div>
</div>
@endsection
