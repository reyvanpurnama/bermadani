<div class="space-y-6">
    <div class="flex items-center gap-3 mb-2">
        <a href="{{ route('admin.stock-mutation') }}"
            class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-500 transition-colors">
            <i class='bx bx-arrow-back text-xl'></i>
        </a>
        <div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-white">Stock Opname</h1>
            <p class="text-[11px] text-slate-500 mt-0.5">Penyesuaian stok fisik dan sistem.</p>
        </div>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Form Input --}}
        <div class="lg:col-span-2">
            <div
                class="bg-white dark:bg-darkCard rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 p-6">

                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-[13px] font-bold text-slate-800 dark:text-white flex items-center gap-2">
                        <span
                            class="w-6 h-6 rounded bg-indigo-50 text-primary flex items-center justify-center text-xs">1</span>
                        Input Koreksi
                    </h3>
                    <span
                        class="text-[10px] text-slate-400 bg-slate-50 dark:bg-slate-800 px-2 py-1 rounded">#ADJ-NEW</span>
                </div>

                <div class="space-y-5">

                    {{-- Product Search --}}
                    <div class="relative">
                        <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-1.5">Pilih
                            Produk <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <span
                                class="absolute inset-y-0 left-3 flex items-center text-slate-400 pointer-events-none"><i
                                    class='bx bx-search'></i></span>
                            <input wire:model.live.debounce.300ms="search" type="text"
                                class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-lg pl-9 pr-3 py-2.5 text-[13px] outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all dark:text-white placeholder-slate-400"
                                placeholder="Cari nama barang atau scan barcode...">
                        </div>

                        {{-- Search Results Dropdown --}}
                        @if(count($searchResults) > 0)
                            <div
                                class="absolute z-10 w-full mt-1 bg-white dark:bg-darkCard border border-slate-200 dark:border-slate-700 rounded-lg shadow-lg max-h-48 overflow-y-auto">
                                @foreach($searchResults as $product)
                                    <button wire:click="selectProduct({{ $product->id }})" type="button"
                                        class="w-full px-4 py-3 flex justify-between items-center hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors text-left">
                                        <div>
                                            <p class="text-[12px] font-medium text-slate-800 dark:text-white">
                                                {{ $product->name }}
                                            </p>
                                            <p class="text-[10px] text-slate-500">{{ $product->sku }}</p>
                                        </div>
                                        <span class="text-[11px] font-bold text-primary">{{ $product->stock }} Pcs</span>
                                    </button>
                                @endforeach
                            </div>
                        @endif

                        {{-- Selected Product Card --}}
                        @if($selectedProduct)
                            <div
                                class="mt-2 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800/30 rounded-lg flex justify-between items-center">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-8 h-8 bg-white dark:bg-slate-800 rounded flex items-center justify-center text-lg">
                                        📦</div>
                                    <div>
                                        <p class="text-[12px] font-bold text-slate-800 dark:text-white">
                                            {{ $selectedProduct->name }}
                                        </p>
                                        <p class="text-[10px] text-slate-500">SKU: {{ $selectedProduct->sku }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-[10px] text-slate-500 uppercase tracking-wider">Stok Sistem</p>
                                    <p class="text-[14px] font-bold text-primary">{{ $selectedProduct->stock }} Pcs</p>
                                </div>
                            </div>
                        @endif
                        @error('selectedProduct') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                    </div>

                    {{-- Adjustment Type & Quantity --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-2">Jenis
                                Penyesuaian <span class="text-rose-500">*</span></label>
                            <div class="flex gap-3">
                                <label class="flex-1 cursor-pointer">
                                    <input type="radio" wire:model="adjustmentType" value="out" class="peer sr-only">
                                    <div
                                        class="border border-slate-200 dark:border-slate-600 rounded-lg p-3 text-center hover:bg-slate-50 dark:hover:bg-slate-700 peer-checked:border-rose-500 peer-checked:bg-rose-50 peer-checked:text-rose-600 dark:peer-checked:bg-rose-900/20 dark:peer-checked:text-rose-400 transition-all">
                                        <div class="text-lg mb-1"><i class='bx bx-minus-circle'></i></div>
                                        <span class="text-[11px] font-bold">Kurangi Stok</span>
                                    </div>
                                </label>
                                <label class="flex-1 cursor-pointer">
                                    <input type="radio" wire:model="adjustmentType" value="in" class="peer sr-only">
                                    <div
                                        class="border border-slate-200 dark:border-slate-600 rounded-lg p-3 text-center hover:bg-slate-50 dark:hover:bg-slate-700 peer-checked:border-emerald-500 peer-checked:bg-emerald-50 peer-checked:text-emerald-600 dark:peer-checked:bg-emerald-900/20 dark:peer-checked:text-emerald-400 transition-all">
                                        <div class="text-lg mb-1"><i class='bx bx-plus-circle'></i></div>
                                        <span class="text-[11px] font-bold">Tambah Stok</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-1.5">Jumlah
                                (Qty) <span class="text-rose-500">*</span></label>
                            <div class="flex items-center">
                                <button wire:click="decrementQty" type="button"
                                    class="w-10 h-10 bg-slate-100 dark:bg-slate-700 rounded-l-lg border-y border-l border-slate-200 dark:border-slate-600 flex items-center justify-center hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors text-slate-500"><i
                                        class='bx bx-minus'></i></button>
                                <input wire:model="quantity" type="number"
                                    class="w-full h-10 bg-white dark:bg-slate-800 border-y border-slate-200 dark:border-slate-600 text-center text-[14px] font-bold text-slate-800 dark:text-white outline-none [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                                <button wire:click="incrementQty" type="button"
                                    class="w-10 h-10 bg-slate-100 dark:bg-slate-700 rounded-r-lg border-y border-r border-slate-200 dark:border-slate-600 flex items-center justify-center hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors text-slate-500"><i
                                        class='bx bx-plus'></i></button>
                            </div>
                            @if($selectedProduct)
                                <p class="text-[10px] text-slate-400 mt-1 text-right">Stok Baru: <span
                                        class="font-bold text-slate-600 dark:text-slate-300">{{ $this->newStock }}
                                        Pcs</span></p>
                            @endif
                            @error('quantity') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- Reason & Date --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-1.5">Alasan
                                <span class="text-rose-500">*</span></label>
                            <select wire:model="reason"
                                class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-lg px-3 py-2.5 text-[13px] outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all dark:text-white cursor-pointer">
                                <option value="DAMAGED">Barang Rusak / Cacat</option>
                                <option value="EXPIRED">Expired / Kadaluarsa</option>
                                <option value="LOST">Hilang (Stok Opname)</option>
                                <option value="BONUS">Bonus Supplier</option>
                                <option value="CORRECTION">Koreksi Kesalahan Input</option>
                                <option value="OTHER">Lainnya</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-1.5">Tanggal
                                Kejadian</label>
                            <input wire:model="occurredAt" type="date"
                                class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-lg px-3 py-2.5 text-[13px] outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all dark:text-white">
                        </div>
                    </div>

                    {{-- Note --}}
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-1.5">Catatan
                            Tambahan</label>
                        <textarea wire:model="note" rows="2"
                            class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-lg px-3 py-2.5 text-[13px] outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all dark:text-white placeholder-slate-400"
                            placeholder="Contoh: Kemasan sobek saat pemindahan barang..."></textarea>
                    </div>

                    {{-- Submit --}}
                    <div class="pt-2">
                        <button wire:click="save" type="button"
                            class="w-full bg-primary hover:bg-indigo-700 text-white font-bold py-3 rounded-lg shadow-lg shadow-indigo-500/20 transition-all text-sm flex items-center justify-center gap-2">
                            <i class='bx bx-save text-lg'></i> Simpan Penyesuaian
                        </button>
                    </div>

                </div>
            </div>
        </div>

        {{-- Right Column: Stats & History --}}
        <div class="lg:col-span-1 space-y-6">

            {{-- Monthly Stats --}}
            <div
                class="bg-white dark:bg-darkCard p-5 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700">
                <h3 class="text-[13px] font-bold text-slate-800 dark:text-white mb-4">Adjustment Bulan Ini</h3>
                <div class="flex justify-between items-center mb-3">
                    <span class="text-[11px] text-slate-500">Total Item Hilang/Rusak</span>
                    <span class="font-bold text-rose-500">{{ $monthlyStats->total_out ?? 0 }} Pcs</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-[11px] text-slate-500">Total Item Ditambahkan</span>
                    <span class="font-bold text-emerald-500">+{{ $monthlyStats->total_in ?? 0 }} Pcs</span>
                </div>
            </div>

            {{-- Recent History --}}
            <div
                class="bg-white dark:bg-darkCard rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden flex-1">
                <div class="p-4 border-b border-slate-100 dark:border-slate-700">
                    <h3 class="text-[13px] font-bold text-slate-800 dark:text-white">Riwayat Terakhir</h3>
                </div>
                <div class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse($recentAdjustments as $adj)
                        <div class="p-4 hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                            <div class="flex justify-between items-start mb-1">
                                <span
                                    class="text-[12px] font-bold text-slate-800 dark:text-white">{{ $adj->product->name ?? '-' }}</span>
                                <span class="text-[10px] text-slate-400">{{ $adj->created_at->diffForHumans() }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <div class="flex items-center gap-2">
                                    <span
                                        class="text-[10px] {{ $adj->quantity < 0 ? 'bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-400' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400' }} px-1.5 py-0.5 rounded font-bold">
                                        {{ $adj->quantity < 0 ? 'OUT' : 'IN' }}
                                    </span>
                                    <span
                                        class="text-[11px] text-slate-500 truncate w-24">{{ Str::limit($adj->note, 15) }}</span>
                                </div>
                                <span
                                    class="text-[12px] font-bold {{ $adj->quantity < 0 ? 'text-rose-500' : 'text-emerald-500' }}">
                                    {{ $adj->quantity > 0 ? '+' : '' }}{{ $adj->quantity }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="p-4 text-center text-slate-400 text-[12px]">
                            Belum ada riwayat adjustment
                        </div>
                    @endforelse
                </div>
                <div class="p-3 border-t border-slate-100 dark:border-slate-700 text-center">
                    <a href="{{ route('admin.stock-mutation') }}"
                        class="text-[11px] font-bold text-primary hover:underline">Lihat Semua Log</a>
                </div>
            </div>

        </div>

    </div>
</div>