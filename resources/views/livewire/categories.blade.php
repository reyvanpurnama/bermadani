<div>
    {{-- Flash Messages --}}
    @if (session()->has('message'))
        <div class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-xl p-4 mb-6 flex items-center gap-3">
            <i class='bx bx-check-circle text-2xl text-emerald-600 dark:text-emerald-400'></i>
            <span class="text-sm font-medium text-emerald-700 dark:text-emerald-400">{{ session('message') }}</span>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800 rounded-xl p-4 mb-6 flex items-center gap-3">
            <i class='bx bx-error-circle text-2xl text-rose-600 dark:text-rose-400'></i>
            <span class="text-sm font-medium text-rose-700 dark:text-rose-400">{{ session('error') }}</span>
        </div>
    @endif

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Kategori Produk</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Kelola pengelompokan barang untuk POS & Laporan.</p>
        </div>
        <button wire:click="openCreateModal" class="bg-primary hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-bold shadow-md shadow-indigo-500/20 transition-colors flex items-center gap-2">
            <i class='bx bx-plus text-lg'></i> Tambah Kategori
        </button>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white dark:bg-darkCard p-5 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center text-blue-600 text-2xl">
                <i class='bx bx-layer'></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Total Kategori</p>
                <h4 class="text-xl font-bold text-slate-800 dark:text-white">{{ $stats['total'] }} Kategori</h4>
            </div>
        </div>
        
        <div class="bg-white dark:bg-darkCard p-5 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center text-emerald-600 text-2xl">
                <i class='bx bx-check-circle'></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Status Aktif</p>
                <h4 class="text-xl font-bold text-slate-800 dark:text-white">{{ $stats['active'] }} Aktif</h4>
            </div>
        </div>
        
        <div class="bg-white dark:bg-darkCard p-5 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-50 dark:bg-amber-500/10 flex items-center justify-center text-amber-500 text-2xl">
                <i class='bx bx-star'></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Top Kategori</p>
                <h4 class="text-xl font-bold text-slate-800 dark:text-white">{{ $stats['top_category'] }} ({{ $stats['top_percentage'] }}%)</h4>
            </div>
        </div>
    </div>

    {{-- Search --}}
    <div class="bg-white dark:bg-darkCard rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 p-4 mb-6">
        <div class="relative">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class='bx bx-search text-slate-400 text-xl'></i>
            </span>
            <input wire:model.live.debounce.300ms="search" type="text" 
                   class="w-full bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 text-slate-900 dark:text-white text-sm rounded-lg focus:ring-primary focus:border-primary block pl-10 p-2.5 outline-none transition-all placeholder-slate-400" 
                   placeholder="Cari kategori...">
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white dark:bg-darkCard rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600 dark:text-slate-400">
                <thead class="bg-slate-50 dark:bg-slate-700/50 text-xs uppercase font-semibold text-slate-500 dark:text-slate-400">
                    <tr>
                        <th class="px-6 py-4">Nama Kategori</th>
                        <th class="px-6 py-4">Slug / Kode</th>
                        <th class="px-6 py-4">Total Produk</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse($categories as $category)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center text-xl">
                                        {{ $category->icon ?: '📦' }}
                                    </div>
                                    <div>
                                        <h6 class="font-semibold text-slate-800 dark:text-white leading-none">{{ $category->name }}</h6>
                                        @if($category->description)
                                            <p class="text-xs text-slate-400 mt-1">{{ $category->description }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 font-mono text-slate-500">/{{ $category->slug }}</td>
                            <td class="px-6 py-4">
                                <span class="font-bold text-slate-800 dark:text-white">{{ $category->products_count }}</span> Item
                            </td>
                            <td class="px-6 py-4">
                                <button wire:click="toggleStatus({{ $category->id }})" 
                                        class="{{ $category->isActive ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400' : 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300' }} px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide hover:opacity-80 transition-opacity">
                                    {{ $category->isActive ? 'Aktif' : 'Hidden' }}
                                </button>
                            </td>
                            <td class="px-6 py-4 text-right flex justify-end gap-2">
                                <button wire:click="openEditModal({{ $category->id }})" class="text-slate-400 hover:text-primary transition-colors">
                                    <i class='bx bx-edit-alt text-xl'></i>
                                </button>
                                <button wire:click="delete({{ $category->id }})" 
                                        wire:confirm="Yakin ingin menghapus kategori ini?"
                                        class="text-slate-400 hover:text-rose-500 transition-colors">
                                    <i class='bx bx-trash text-xl'></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-slate-400">
                                    <i class='bx bx-category text-5xl mb-3 opacity-50'></i>
                                    <p class="text-sm font-medium">Belum ada kategori</p>
                                    <p class="text-xs mt-1">Tambahkan kategori untuk mengelompokkan produk</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($categories->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-700">
                {{ $categories->links() }}
            </div>
        @endif
    </div>

    {{-- Modal --}}
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ show: @entangle('showModal') }">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                {{-- Overlay --}}
                <div x-show="show" 
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 transition-opacity bg-slate-500 bg-opacity-75" 
                     @click="$wire.closeModal()"></div>

                {{-- Modal --}}
                <div x-show="show"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="inline-block align-bottom bg-white dark:bg-darkCard rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    
                    <div class="bg-white dark:bg-darkCard px-6 pt-6 pb-4">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white">
                                {{ $isEditing ? 'Edit Kategori' : 'Tambah Kategori Baru' }}
                            </h3>
                            <button wire:click="closeModal" class="text-slate-400 hover:text-slate-600 dark:hover:text-white">
                                <i class='bx bx-x text-2xl'></i>
                            </button>
                        </div>

                        <form wire:submit.prevent="save" class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Nama Kategori <span class="text-rose-500">*</span></label>
                                <input wire:model="name" type="text" required
                                       class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 text-slate-900 dark:text-white text-sm rounded-lg px-4 py-2.5 outline-none focus:ring-2 focus:ring-primary @error('name') border-rose-500 @enderror"
                                       placeholder="Contoh: Makanan Ringan">
                                @error('name') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Slug / Kode</label>
                                <input wire:model="slug" type="text"
                                       class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 text-slate-900 dark:text-white text-sm rounded-lg px-4 py-2.5 outline-none focus:ring-2 focus:ring-primary"
                                       placeholder="Otomatis dari nama (opsional)">
                                <p class="text-xs text-slate-400 mt-1">Kosongkan untuk generate otomatis</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Icon Emoji</label>
                                <input wire:model="icon" type="text" maxlength="10"
                                       class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 text-slate-900 dark:text-white text-sm rounded-lg px-4 py-2.5 outline-none focus:ring-2 focus:ring-primary"
                                       placeholder="🍜 (emoji)">
                                <p class="text-xs text-slate-400 mt-1">Salin emoji dari keyboard: 🍜 🥤 ✏️ 👕 📱</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Deskripsi</label>
                                <textarea wire:model="description" rows="2"
                                          class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 text-slate-900 dark:text-white text-sm rounded-lg px-4 py-2.5 outline-none focus:ring-2 focus:ring-primary resize-none"
                                          placeholder="Deskripsi singkat (opsional)"></textarea>
                            </div>

                            <div class="flex items-center gap-2">
                                <input wire:model="isActive" type="checkbox" id="isActive" class="w-4 h-4 text-primary border-slate-300 rounded focus:ring-primary">
                                <label for="isActive" class="text-sm text-slate-700 dark:text-slate-300">Aktif (tampil di POS)</label>
                            </div>

                            <div class="flex justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-700">
                                <button type="button" wire:click="closeModal" class="px-4 py-2 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-600 font-medium rounded-lg text-sm transition-colors">
                                    Batal
                                </button>
                                <button type="submit" class="px-4 py-2 bg-primary hover:bg-indigo-700 text-white font-bold rounded-lg text-sm shadow-lg shadow-indigo-500/20 transition-all flex items-center gap-2">
                                    <i class='bx bx-save'></i> {{ $isEditing ? 'Update' : 'Simpan' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
