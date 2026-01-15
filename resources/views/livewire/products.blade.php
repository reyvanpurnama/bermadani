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
            <button wire:click="openCategoryModal" class="bg-white dark:bg-darkCard border border-slate-200 dark:border-slate-600 text-slate-700 dark:text-white hover:bg-slate-50 dark:hover:bg-slate-700 px-4 py-2 rounded-lg text-[13px] font-medium shadow-sm transition-colors flex items-center gap-2">
                <i class='bx bx-category'></i> Kategori
            </button>
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
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Titip Jual Baru</p>
                    <h3 class="text-2xl font-bold text-purple-600 dark:text-purple-400 mt-1">{{ $stats['consignment_waiting'] ?? 0 }}</h3>
                </div>
                <div class="w-12 h-12 rounded-xl bg-purple-50 dark:bg-purple-900/30 flex items-center justify-center text-purple-500">
                    <i class='bx bx-store text-2xl'></i>
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
            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 block">Status</label>
            <select wire:model.live="stockFilter" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 text-[13px] rounded-md px-3 py-2 outline-none focus:ring-1 focus:ring-primary text-slate-700 dark:text-white cursor-pointer">
                <option value="">Semua Status</option>
                <option value="available">Tersedia</option>
                <option value="low">Stok Menipis</option>
                <option value="out">Stok Habis</option>
                <option value="consignment_waiting">Titip Jual (Belum Masuk)</option>
                <option value="pending">Menunggu Review</option>
                <option value="rejected">Ditolak</option>
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
                                        <div class="flex items-center gap-2">
                                            <h6 class="font-semibold text-slate-800 dark:text-white leading-none {{ $product->stock == 0 && !$product->supplierId ? 'line-through opacity-50' : '' }}">
                                                {{ $product->name }}
                                            </h6>
                                            @if($product->supplierId)
                                                <span class="bg-purple-100 text-purple-700 dark:bg-purple-500/20 dark:text-purple-400 px-1.5 py-0.5 rounded text-[8px] font-bold uppercase">Konsinyasi</span>
                                            @endif
                                        </div>
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
                                @if($product->approvalStatus === 'PENDING')
                                    <span class="bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-400 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide">Menunggu Review</span>
                                @elseif($product->approvalStatus === 'REJECTED')
                                    <span class="bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-400 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide cursor-help" title="{{ $product->rejectionReason ?? 'Tidak ada alasan' }}">Ditolak</span>
                                @elseif($product->stock == 0 && $product->supplierId)
                                    <span class="bg-purple-100 text-purple-700 dark:bg-purple-500/20 dark:text-purple-400 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide">Belum Masuk</span>
                                @elseif($product->stock == 0)
                                    <span class="bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-400 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide">Habis</span>
                                @elseif($product->stock <= $product->threshold)
                                    <span class="bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-400 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide">Menipis</span>
                                @else
                                    <span class="bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide">Tersedia</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if($product->supplierId && $product->approvalStatus === 'APPROVED')
                                        @if($product->stock == 0)
                                            {{-- Urgent: Stok habis --}}
                                            <button wire:click="openQuickBatchModal({{ $product->id }})" 
                                                    class="bg-rose-500 hover:bg-rose-600 text-white px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wide shadow-sm shadow-rose-500/30 hover:shadow-rose-500/50 transition-all flex items-center gap-1 animate-pulse">
                                                <i class='bx bx-plus-circle'></i> Minta Stok
                                            </button>
                                        @elseif($product->stock <= $product->threshold)
                                            {{-- Warning: Stok menipis --}}
                                            <button wire:click="openQuickBatchModal({{ $product->id }})" 
                                                    class="bg-amber-500 hover:bg-amber-600 text-white px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wide shadow-sm shadow-amber-500/30 hover:shadow-amber-500/50 transition-all flex items-center gap-1">
                                                <i class='bx bx-plus-circle'></i> Restock
                                            </button>
                                        @else
                                            {{-- Normal: Stok cukup --}}
                                            <button wire:click="openQuickBatchModal({{ $product->id }})" 
                                                    class="text-slate-400 hover:text-primary transition-colors text-lg" 
                                                    title="Tambah Stok">
                                                <i class='bx bx-plus-circle'></i>
                                            </button>
                                        @endif
                                    @endif
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

    {{-- Category Modal --}}
    @if($showCategoryModal)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                {{-- Overlay --}}
                <div wire:click="closeCategoryModal" class="fixed inset-0 transition-opacity bg-slate-900/70 backdrop-blur-sm"></div>

                {{-- Modal Panel --}}
                <div class="inline-block w-full max-w-4xl p-6 my-8 text-left align-middle transition-all transform bg-white dark:bg-darkCard shadow-xl rounded-2xl relative">
                    {{-- Modal Header --}}
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-700">
                        <div>
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white">
                                <i class='bx bx-category text-primary'></i> Kelola Kategori
                            </h3>
                            <p class="text-xs text-slate-500 mt-1">Tambah, edit, atau hapus kategori produk.</p>
                        </div>
                        <button wire:click="closeCategoryModal" class="text-slate-400 hover:text-slate-600 dark:hover:text-white transition-colors">
                            <i class='bx bx-x text-2xl'></i>
                        </button>
                    </div>

                    {{-- Flash Messages --}}
                    @if (session()->has('categoryMessage'))
                        <div class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-lg p-3 my-4 flex items-center gap-2">
                            <i class='bx bx-check-circle text-xl text-emerald-600 dark:text-emerald-400'></i>
                            <span class="text-sm font-medium text-emerald-700 dark:text-emerald-400">{{ session('categoryMessage') }}</span>
                        </div>
                    @endif
                    @if (session()->has('categoryError'))
                        <div class="bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800 rounded-lg p-3 my-4 flex items-center gap-2">
                            <i class='bx bx-error-circle text-xl text-rose-600 dark:text-rose-400'></i>
                            <span class="text-sm font-medium text-rose-700 dark:text-rose-400">{{ session('categoryError') }}</span>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-4">
                        {{-- Left: Category List --}}
                        <div class="lg:col-span-2">
                            {{-- Search --}}
                            <div class="relative mb-4">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                                    <i class='bx bx-search'></i>
                                </span>
                                <input wire:model.live.debounce.300ms="categorySearch" type="text" 
                                       class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 text-[13px] rounded-lg pl-10 pr-3 py-2.5 outline-none focus:ring-1 focus:ring-primary text-slate-700 dark:text-white" 
                                       placeholder="Cari kategori...">
                            </div>

                            {{-- Category Table --}}
                            <div class="bg-slate-50 dark:bg-slate-800/50 rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 max-h-[400px] overflow-y-auto">
                                <table class="w-full text-left text-[13px]">
                                    <thead class="bg-slate-100 dark:bg-slate-700 sticky top-0">
                                        <tr>
                                            <th class="px-4 py-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider">Kategori</th>
                                            <th class="px-4 py-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider text-center">Produk</th>
                                            <th class="px-4 py-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider text-center">Status</th>
                                            <th class="px-4 py-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider text-right">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                                        @forelse($categoryList as $category)
                                            <tr class="hover:bg-white dark:hover:bg-slate-800 transition-colors {{ $categoryId == $category->id ? 'bg-indigo-50 dark:bg-indigo-900/20' : '' }}">
                                                <td class="px-4 py-3">
                                                    <div class="flex items-center gap-3">
                                                        <span class="text-xl">{{ $category->icon ?: '📦' }}</span>
                                                        <div>
                                                            <h6 class="font-semibold text-slate-800 dark:text-white">{{ $category->name }}</h6>
                                                            <p class="text-[10px] text-slate-400 font-mono">/{{ $category->slug }}</p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    <span class="font-bold text-slate-700 dark:text-white">{{ $category->products_count }}</span>
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    <button wire:click="toggleCategoryStatus('{{ $category->id }}')" 
                                                            class="{{ $category->isActive ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400' : 'bg-slate-200 text-slate-500 dark:bg-slate-600 dark:text-slate-400' }} px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide hover:opacity-80">
                                                        {{ $category->isActive ? 'Aktif' : 'Hidden' }}
                                                    </button>
                                                </td>
                                                <td class="px-4 py-3 text-right">
                                                    <div class="flex items-center justify-end gap-1">
                                                        <button wire:click="editCategory('{{ $category->id }}')" 
                                                                class="text-slate-400 hover:text-primary transition-colors p-1">
                                                            <i class='bx bx-edit-alt text-lg'></i>
                                                        </button>
                                                        <button wire:click="deleteCategory('{{ $category->id }}')" 
                                                                wire:confirm="Yakin ingin menghapus kategori ini?"
                                                                class="text-slate-400 hover:text-rose-500 transition-colors p-1">
                                                            <i class='bx bx-trash text-lg'></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="px-4 py-8 text-center">
                                                    <div class="flex flex-col items-center justify-center text-slate-400">
                                                        <i class='bx bx-category text-3xl mb-2 opacity-50'></i>
                                                        <p class="text-sm">Belum ada kategori</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Right: Add/Edit Form --}}
                        <div class="lg:col-span-1">
                            <div class="bg-slate-50 dark:bg-slate-800/50 rounded-xl p-4 border border-slate-200 dark:border-slate-700">
                                <div class="flex items-center justify-between mb-4">
                                    <h4 class="text-sm font-bold text-slate-700 dark:text-white">
                                        {{ $isEditingCategory ? '✏️ Edit Kategori' : '➕ Tambah Baru' }}
                                    </h4>
                                    @if($isEditingCategory)
                                        <button wire:click="resetCategoryForm" class="text-xs text-primary hover:underline">
                                            Batal Edit
                                        </button>
                                    @endif
                                </div>

                                <form wire:submit="saveCategory" class="space-y-4">
                                    {{-- Name --}}
                                    <div>
                                        <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block mb-1">Nama Kategori *</label>
                                        <input wire:model="categoryName" type="text" 
                                               class="w-full bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 text-[13px] rounded-lg px-3 py-2.5 outline-none focus:ring-1 focus:ring-primary text-slate-700 dark:text-white" 
                                               placeholder="Contoh: Minuman">
                                        @error('categoryName') <span class="text-rose-500 text-xs mt-1">{{ $message }}</span> @enderror
                                    </div>

                                    {{-- Icon --}}
                                    <div>
                                        <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block mb-1">Icon (Emoji)</label>
                                        <input wire:model="categoryIcon" type="text" 
                                               class="w-full bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 text-[13px] rounded-lg px-3 py-2.5 outline-none focus:ring-1 focus:ring-primary text-slate-700 dark:text-white" 
                                               placeholder="🥤">
                                    </div>

                                    {{-- Slug --}}
                                    <div>
                                        <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block mb-1">Slug (Opsional)</label>
                                        <input wire:model="categorySlug" type="text" 
                                               class="w-full bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 text-[13px] rounded-lg px-3 py-2.5 outline-none focus:ring-1 focus:ring-primary text-slate-700 dark:text-white font-mono" 
                                               placeholder="minuman">
                                    </div>

                                    {{-- Description --}}
                                    <div>
                                        <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block mb-1">Deskripsi</label>
                                        <textarea wire:model="categoryDescription" rows="2"
                                                  class="w-full bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 text-[13px] rounded-lg px-3 py-2.5 outline-none focus:ring-1 focus:ring-primary text-slate-700 dark:text-white resize-none" 
                                                  placeholder="Deskripsi singkat..."></textarea>
                                    </div>

                                    {{-- Status --}}
                                    <div class="flex items-center gap-2">
                                        <input wire:model="categoryIsActive" type="checkbox" id="categoryActive"
                                               class="w-4 h-4 text-primary bg-slate-100 border-slate-300 rounded focus:ring-primary">
                                        <label for="categoryActive" class="text-sm text-slate-600 dark:text-slate-300">Aktif</label>
                                    </div>

                                    {{-- Submit Button --}}
                                    <button type="submit" 
                                            class="w-full bg-primary hover:bg-indigo-700 text-white font-bold py-2.5 rounded-lg text-sm transition-colors flex items-center justify-center gap-2">
                                        <i class='bx {{ $isEditingCategory ? "bx-save" : "bx-plus" }}'></i>
                                        {{ $isEditingCategory ? 'Simpan Perubahan' : 'Tambah Kategori' }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Quick Batch Modal --}}
    @if($showQuickBatchModal && $quickBatchProduct)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
            <div class="bg-white dark:bg-darkCard rounded-xl shadow-2xl w-full max-w-md overflow-hidden animate-fade-in-up">
                <!-- Header -->
                <div class="flex justify-between items-center px-6 py-4 border-b border-slate-100 dark:border-slate-800 bg-purple-50 dark:bg-purple-500/10">
                    <div>
                        <h3 class="font-bold text-lg text-slate-800 dark:text-white flex items-center gap-2">
                            <i class='bx bx-plus-circle text-purple-600'></i> Buat Permintaan Stok
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">Minta stok baru dari supplier</p>
                    </div>
                    <button wire:click="closeQuickBatchModal"
                        class="w-8 h-8 rounded-full flex items-center justify-center text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 transition-all">
                        <i class='bx bx-x text-2xl'></i>
                    </button>
                </div>

                <div class="p-6 space-y-4">
                    <!-- Product Info -->
                    <div class="bg-slate-50 dark:bg-slate-800 p-4 rounded-xl">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-lg bg-purple-100 dark:bg-purple-500/20 flex items-center justify-center text-2xl">
                                {{ $quickBatchProduct->category?->icon ?? '📦' }}
                            </div>
                            <div class="flex-grow">
                                <h4 class="font-semibold text-slate-800 dark:text-white">{{ $quickBatchProduct->name }}</h4>
                                <p class="text-[11px] text-slate-500">
                                    {{ $quickBatchProduct->supplier?->businessName ?? 'Supplier' }} • 
                                    Rp {{ number_format($quickBatchProduct->sellPrice, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4 mt-3 pt-3 border-t border-slate-200 dark:border-slate-700">
                            <div class="text-center flex-1">
                                <p class="text-[10px] text-slate-400 uppercase">Stok Saat Ini</p>
                                <p class="font-bold text-lg {{ $quickBatchProduct->stock == 0 ? 'text-rose-500' : 'text-slate-700 dark:text-white' }}">{{ $quickBatchProduct->stock }}</p>
                            </div>
                            <div class="text-center flex-1">
                                <p class="text-[10px] text-slate-400 uppercase">Threshold</p>
                                <p class="font-bold text-lg text-slate-700 dark:text-white">{{ $quickBatchProduct->threshold }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Qty Input -->
                    <div>
                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 uppercase mb-2">Jumlah Permintaan</label>
                        <input wire:model="quickBatchQty" type="number" min="1"
                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-center text-xl font-bold focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500">
                    </div>

                    <!-- Fee Input -->
                    <div>
                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 uppercase mb-2">Fee Koperasi (%)</label>
                        <input wire:model="quickBatchFee" type="number" min="0" max="100"
                            class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-center font-medium focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500">
                    </div>

                    <!-- Info -->
                    <div class="bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/30 rounded-lg p-3">
                        <p class="text-[11px] text-amber-700 dark:text-amber-400">
                            <i class='bx bx-info-circle mr-1'></i>
                            Supplier akan menerima notifikasi untuk menyiapkan barang.
                        </p>
                    </div>
                </div>

                <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 flex justify-end gap-3 border-t border-slate-100 dark:border-slate-800">
                    <button wire:click="closeQuickBatchModal"
                        class="px-5 py-2.5 text-xs font-bold text-slate-600 hover:text-slate-800 hover:bg-slate-100 dark:text-slate-400 dark:hover:text-white dark:hover:bg-slate-700 rounded-lg transition-all uppercase tracking-wide">
                        Batal
                    </button>
                    <button wire:click="saveQuickBatch"
                        class="px-5 py-2.5 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-xs font-bold uppercase tracking-wide shadow-lg shadow-purple-500/30 hover:shadow-purple-500/50 transition-all flex items-center gap-2">
                        <i class='bx bx-send text-lg'></i> Kirim Permintaan
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
