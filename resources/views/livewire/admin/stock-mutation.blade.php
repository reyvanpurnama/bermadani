<div class="space-y-6">
    
    {{-- Header & Filters --}}
    <div class="bg-white dark:bg-darkCard p-4 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 flex flex-col md:flex-row gap-4 justify-between items-end md:items-center">
            
        <div class="flex flex-col sm:flex-row flex-1 gap-3 w-full">
            <div class="relative flex-1">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400"><i class='bx bx-search'></i></span>
                <input wire:model.live.debounce.300ms="search" type="text" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-lg pl-9 py-2 text-[12px] outline-none focus:border-primary placeholder-slate-400 dark:text-white" placeholder="Cari nama barang / SKU...">
            </div>
            
            <select wire:model.live="filterType" class="bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-lg px-3 py-2 text-[12px] outline-none text-slate-700 dark:text-white cursor-pointer w-full sm:w-40">
                <option value="">Semua Tipe</option>
                <option value="RESTOCK">Barang Masuk (In)</option>
                <option value="SOLD">Terjual (Out)</option>
                <option value="ADJUSTMENT">Penyesuaian (Adj)</option>
                <option value="EXPIRED_OUT">Expired (Out)</option>
                <option value="RETURN_IN">Retur Masuk</option>
                <option value="RETURN_OUT">Retur Keluar</option>
            </select>

            <div class="relative w-full sm:w-40">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400"><i class='bx bx-calendar'></i></span>
                <input wire:model.live="dateStart" type="date" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-lg pl-9 py-2 text-[12px] outline-none text-slate-700 dark:text-white">
            </div>
        </div>

        <button wire:click="openModal" class="bg-white dark:bg-darkCard border border-primary text-primary hover:bg-indigo-50 dark:hover:bg-indigo-900/20 px-4 py-2 rounded-lg text-[12px] font-bold flex items-center gap-2 transition-colors whitespace-nowrap">
            <i class='bx bx-plus-circle'></i> Buat Mutasi
        </button>
    </div>

    {{-- Main Table --}}
    <div class="bg-white dark:bg-darkCard rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600 dark:text-slate-400">
                <thead class="bg-slate-50 dark:bg-slate-800/50 text-[10px] uppercase font-bold text-slate-500 dark:text-slate-400 tracking-widest">
                    <tr>
                        <th class="px-5 py-3">Waktu</th>
                        <th class="px-5 py-3">Referensi</th>
                        <th class="px-5 py-3">Produk</th>
                        <th class="px-5 py-3 text-center">Tipe</th>
                        <th class="px-5 py-3 text-center">Qty</th>
                        <th class="px-5 py-3 text-right">User/Aktor</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700 text-[12px]">
                    @forelse($movements as $movement)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                            <td class="px-5 py-3.5 whitespace-nowrap">
                                <span class="block font-semibold text-slate-700 dark:text-slate-300">{{ $movement->created_at->format('d M Y') }}</span>
                                <span class="text-[10px] text-slate-400">{{ $movement->created_at->format('H:i') }} WIB</span>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="font-mono text-indigo-500 bg-indigo-50 dark:bg-indigo-500/10 px-1.5 py-0.5 rounded text-[11px] hover:underline cursor-pointer" title="{{ $movement->note }}">
                                    #ref-{{ substr($movement->id, 0, 8) }}
                                </span>
                                <span class="block text-[10px] text-slate-400 mt-0.5 truncate max-w-[150px]">{{ $movement->note ?? '-' }}</span>
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded bg-slate-100 dark:bg-slate-700 flex items-center justify-center text-[10px]">
                                        📦
                                    </div>
                                    <div>
                                        <span class="font-medium text-slate-800 dark:text-white block">{{ $movement->product->name }}</span>
                                        <span class="text-[10px] text-slate-400">{{ $movement->product->sku }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                @php
                                    $typeClass = match($movement->movementType) {
                                        'RESTOCK', 'RETURN_IN' => 'text-emerald-500 bg-emerald-50 dark:bg-emerald-500/10 border-emerald-100 dark:border-emerald-500/20',
                                        'SOLD', 'EXPIRED_OUT', 'RETURN_OUT' => 'text-rose-500 bg-rose-50 dark:bg-rose-500/10 border-rose-100 dark:border-rose-500/20',
                                        'ADJUSTMENT' => 'text-amber-500 bg-amber-50 dark:bg-amber-500/10 border-amber-100 dark:border-amber-500/20',
                                        default => 'text-slate-500 bg-slate-50'
                                    };
                                    $label = match($movement->movementType) {
                                        'RESTOCK' => 'IN',
                                        'RETURN_IN' => 'RET IN',
                                        'SOLD' => 'OUT',
                                        'EXPIRED_OUT' => 'EXP',
                                        'RETURN_OUT' => 'RET OUT',
                                        'ADJUSTMENT' => 'ADJ',
                                        default => $movement->movementType
                                    };
                                @endphp
                                <span class="{{ $typeClass }} px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide border">
                                    {{ $label }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-center font-bold {{ $movement->quantity > 0 ? 'text-emerald-500' : 'text-rose-500' }}">
                                {{ $movement->quantity > 0 ? '+' : '' }}{{ $movement->quantity }}
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <span class="text-[11px] text-slate-600 dark:text-slate-400">Admin</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-slate-500">
                                <div class="flex flex-col items-center">
                                    <i class='bx bx-clipboard text-4xl mb-2 text-slate-300'></i>
                                    <p>Belum ada data mutasi.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($movements->hasPages())
        <div class="px-5 py-3 border-t border-slate-100 dark:border-slate-700">
            {{ $movements->links() }}
        </div>
        @endif
    </div>

    {{-- Modal Create Mutation --}}
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
        <div class="bg-white dark:bg-darkCard rounded-xl shadow-2xl w-full max-w-md overflow-hidden animate-fade-in-up">
            <div class="flex justify-between items-center px-6 py-4 border-b border-slate-200 dark:border-slate-700">
                <h3 class="font-bold text-lg text-slate-800 dark:text-white">Buat Mutasi Stok</h3>
                <button wire:click="openModal" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <i class='bx bx-x text-2xl'></i>
                </button>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Pilih Produk</label>
                    <select wire:model="productId" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-primary focus:border-primary text-sm">
                        <option value="">-- Cari Produk --</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }} (Stok: {{ $product->stock }})</option>
                        @endforeach
                    </select>
                    @error('productId') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Tipe Mutasi</label>
                    <select wire:model.live="type" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-primary focus:border-primary text-sm">
                        <option value="ADJUSTMENT">Koreksi Stok (Adjustment)</option>
                        <option value="EXPIRED_OUT">Barang Kadaluarsa</option>
                        <option value="RETURN_IN">Retur Pembeli (Masuk)</option>
                        <option value="RETURN_OUT">Retur ke Supplier (Keluar)</option>
                    </select>
                </div>

                <div>
                    <span class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Jenis Perubahan</span>
                    <div class="flex gap-4">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" wire:model="mode" value="in" class="text-primary focus:ring-primary" {{ $type != 'ADJUSTMENT' ? 'disabled' : '' }}>
                            <span class="text-sm {{ $mode == 'in' ? 'text-emerald-600 font-bold' : 'text-slate-600' }}">Penambahan (+)</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" wire:model="mode" value="out" class="text-primary focus:ring-primary" {{ $type != 'ADJUSTMENT' ? 'disabled' : '' }}>
                            <span class="text-sm {{ $mode == 'out' ? 'text-rose-600 font-bold' : 'text-slate-600' }}">Pengurangan (-)</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Jumlah</label>
                    <input wire:model="quantity" type="number" min="1" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-primary focus:border-primary text-sm">
                    @error('quantity') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Catatan</label>
                    <textarea wire:model="note" rows="2" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-primary focus:border-primary text-sm"></textarea>
                </div>
            </div>
            <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 flex justify-end gap-3">
                <button wire:click="openModal" class="px-4 py-2 text-sm text-slate-600 hover:text-slate-800 font-medium transition-colors">Batal</button>
                <button wire:click="save" class="px-4 py-2 text-sm bg-primary hover:bg-indigo-700 text-white rounded-lg font-bold transition-colors">
                    Simpan Mutasi
                </button>
            </div>
        </div>
    </div>
    @endif

</div>