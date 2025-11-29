<div>
    {{-- Flash Message --}}
    @if (session()->has('message'))
        <div class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-xl p-4 mb-6 flex items-center gap-3">
            <i class='bx bx-check-circle text-2xl text-emerald-600 dark:text-emerald-400'></i>
            <span class="text-sm font-medium text-emerald-700 dark:text-emerald-400">{{ session('message') }}</span>
        </div>
    @endif

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-white">Manajemen Inventaris</h1>
            <p class="text-[11px] text-slate-500 mt-0.5">Kelola produk, stok, dan harga.</p>
        </div>
        <div class="flex gap-2">
            <button wire:click="$refresh" class="bg-white dark:bg-darkCard border border-slate-200 dark:border-slate-600 text-slate-700 dark:text-white hover:bg-slate-50 dark:hover:bg-slate-700 px-4 py-2 rounded-lg text-[13px] font-medium shadow-sm transition-colors flex items-center gap-2">
                <i class='bx bx-refresh'></i> Refresh
            </button>
            <a href="{{ route('admin.products.create') }}" class="bg-primary hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-[13px] font-bold shadow-md shadow-indigo-500/20 transition-colors flex items-center gap-2">
                <i class='bx bx-plus text-lg'></i> Tambah Produk
            </a>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-darkCard rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Total Produk</p>
                    <h3 class="text-2xl font-bold text-slate-900 dark:text-white mt-1">{{ $stats['total'] }}</h3>
                </div>
                <div class="w-12 h-12 rounded-xl bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center text-primary">
                    <i class='bx bx-package text-2xl'></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-darkCard rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Stok Menipis</p>
                    <h3 class="text-2xl font-bold text-amber-600 dark:text-amber-400 mt-1">{{ $stats['low_stock'] }}</h3>
                </div>
                <div class="w-12 h-12 rounded-xl bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center text-amber-500">
                    <i class='bx bx-error text-2xl'></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-darkCard rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Stok Habis</p>
                    <h3 class="text-2xl font-bold text-rose-600 dark:text-rose-400 mt-1">{{ $stats['out_of_stock'] }}</h3>
                </div>
                <div class="w-12 h-12 rounded-xl bg-rose-50 dark:bg-rose-900/30 flex items-center justify-center text-rose-500">
                    <i class='bx bx-x-circle text-2xl'></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-darkCard rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Nilai Stok</p>
                    <h3 class="text-2xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">Rp {{ number_format($stats['total_value'], 0, ',', '.') }}</h3>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center text-emerald-500">
                    <i class='bx bx-wallet text-2xl'></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 bg-white dark:bg-darkCard p-4 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 mb-6">
        <div class="relative">
            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 block">Kategori</label>
            <select wire:model.live="categoryFilter" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 text-[13px] rounded-md px-3 py-2 outline-none focus:ring-1 focus:ring-primary text-slate-700 dark:text-white cursor-pointer">
                <option value="">Semua Kategori</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="relative">
            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 block">Status Stok</label>
            <select wire:model.live="stockFilter" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 text-[13px] rounded-md px-3 py-2 outline-none focus:ring-1 focus:ring-primary text-slate-700 dark:text-white cursor-pointer">
                <option value="">Semua Status</option>
                <option value="available">Tersedia</option>
                <option value="low">Stok Menipis</option>
                <option value="out">Stok Habis</option>
            </select>
        </div>

        <div class="relative">
            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 block">Pencarian</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                    <i class='bx bx-search text-lg'></i>
                </span>
                <input wire:model.live.debounce.300ms="search" type="text" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 text-[13px] rounded-md pl-10 pr-3 py-2 outline-none focus:ring-1 focus:ring-primary text-slate-700 dark:text-white" placeholder="Cari nama, SKU...">
            </div>
        </div>

        <div class="flex items-end">
            <button wire:click="resetFilters" class="w-full bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-600 font-medium py-2 rounded-md text-[13px] transition-colors">
                Reset Filter
            </button>
        </div>
    </div>

    {{-- Products Table --}}
    <div class="bg-white dark:bg-darkCard rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-100 dark:border-slate-700">
                    <tr>
                        <th class="px-5 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Nama Produk</th>
                        <th class="px-5 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Kategori</th>
                        <th class="px-5 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Harga</th>
                        <th class="px-5 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest w-[150px]">Level Stok</th>
                        <th class="px-5 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-center">Status</th>
                        <th class="px-5 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700 text-[13px]">
                    @forelse($products as $product)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors group">
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center text-lg">
                                        {{ $product->category?->icon ?? '📦' }}
                                    </div>
                                    <div>
                                        <h6 class="font-semibold text-slate-800 dark:text-white leading-none {{ $product->stock == 0 ? 'line-through opacity-50' : '' }}">
                                            {{ $product->name }}
                                        </h6>
                                        <p class="text-[10px] text-slate-400 mt-0.5">SKU: {{ $product->sku }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 text-slate-600 dark:text-slate-400">{{ $product->category?->name ?? '-' }}</td>
                            <td class="px-5 py-3.5 font-bold text-slate-800 dark:text-white">Rp {{ number_format($product->sellPrice, 0, ',', '.') }}</td>
                            <td class="px-5 py-3.5">
                                <div class="flex justify-between text-[10px] mb-1">
                                    <span class="font-bold {{ $product->stock <= $product->threshold ? 'text-rose-600 dark:text-rose-400' : 'text-slate-700 dark:text-slate-300' }}">
                                        {{ $product->stock }}
                                    </span> 
                                    <span class="text-slate-400">/ {{ $product->threshold }}</span>
                                </div>
                                <div class="w-full bg-slate-100 dark:bg-slate-700 rounded-full h-1.5">
                                    <div class="{{ $product->stock == 0 ? 'bg-rose-500' : ($product->stock <= $product->threshold ? 'bg-amber-500' : 'bg-indigo-500') }} h-1.5 rounded-full" 
                                         style="width: {{ $product->threshold > 0 ? min(100, ($product->stock / $product->threshold) * 100) : 0 }}%"></div>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                @if($product->stock == 0)
                                    <span class="bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-400 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide">Habis</span>
                                @elseif($product->stock <= $product->threshold)
                                    <span class="bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-400 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide">Menipis</span>
                                @else
                                    <span class="bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide">Tersedia</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.products.edit', $product->id) }}" class="text-slate-400 hover:text-primary transition-colors text-lg">
                                        <i class='bx bx-edit-alt'></i>
                                    </a>
                                    <button wire:click="deleteProduct({{ $product->id }})" 
                                            wire:confirm="Yakin ingin menghapus produk ini?"
                                            class="text-slate-400 hover:text-rose-500 transition-colors text-lg">
                                        <i class='bx bx-trash'></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-slate-400">
                                    <i class='bx bx-package text-5xl mb-3 opacity-50'></i>
                                    <p class="text-sm font-medium">Tidak ada produk ditemukan</p>
                                    <p class="text-xs mt-1">Coba ubah filter atau tambah produk baru</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($products->hasPages())
            <div class="px-5 py-4 border-t border-slate-100 dark:border-slate-700">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</div>
