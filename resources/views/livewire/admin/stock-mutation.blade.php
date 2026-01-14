<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Mutasi Stok</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Kelola perpindahan dan penyesuaian stok barang</p>
        </div>
        <button wire:click="openModal"
            class="bg-primary hover:bg-indigo-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors">
            <i class='bx bx-plus'></i>
            <span>Buat Mutasi</span>
        </button>
    </div>

    {{-- Filters --}}
    <div
        class="bg-white dark:bg-darkCard p-4 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 flex flex-col md:flex-row gap-4">
        <div class="flex-1">
            <label class="text-xs font-medium text-slate-500 mb-1 block">Pencarian</label>
            <div class="relative">
                <i class='bx bx-search absolute left-3 top-2.5 text-slate-400'></i>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari barang atau SKU..."
                    class="w-full pl-9 pr-4 py-2 bg-slate-50 dark:bg-slate-800 border-none rounded-lg text-sm focus:ring-2 focus:ring-primary">
            </div>
        </div>
        <div class="w-full md:w-48">
            <label class="text-xs font-medium text-slate-500 mb-1 block">Tipe Mutasi</label>
            <select wire:model.live="filterType"
                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border-none rounded-lg text-sm focus:ring-2 focus:ring-primary">
                <option value="">Semua Tipe</option>
                <option value="ADJUSTMENT">Adjustment</option>
                <option value="RESTOCK">Restock</option>
                <option value="RETURN_IN">Retur Masuk</option>
                <option value="RETURN_OUT">Retur Keluar</option>
                <option value="EXPIRED_OUT">Expired/Rusak</option>
            </select>
        </div>
        <div class="flex gap-2">
            <div>
                <label class="text-xs font-medium text-slate-500 mb-1 block">Dari</label>
                <input wire:model.live="dateStart" type="date"
                    class="px-3 py-2 bg-slate-50 dark:bg-slate-800 border-none rounded-lg text-sm focus:ring-2 focus:ring-primary">
            </div>
            <div>
                <label class="text-xs font-medium text-slate-500 mb-1 block">Sampai</label>
                <input wire:model.live="dateEnd" type="date"
                    class="px-3 py-2 bg-slate-50 dark:bg-slate-800 border-none rounded-lg text-sm focus:ring-2 focus:ring-primary">
            </div>
        </div>
    </div>

    {{-- List Data --}}
    <div
        class="bg-white dark:bg-darkCard rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700">
                    <tr>
                        <th class="px-5 py-3 font-semibold text-slate-600 dark:text-slate-300">Tanggal</th>
                        <th class="px-5 py-3 font-semibold text-slate-600 dark:text-slate-300">Barang</th>
                        <th class="px-5 py-3 font-semibold text-slate-600 dark:text-slate-300">Tipe</th>
                        <th class="px-5 py-3 font-semibold text-slate-600 dark:text-slate-300 text-center">Jumlah</th>
                        <th class="px-5 py-3 font-semibold text-slate-600 dark:text-slate-300">Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                    @forelse($mutations as $log)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <td class="px-5 py-3 text-slate-500">
                                {{ \Carbon\Carbon::parse($log->occurredAt)->translatedFormat('d M Y H:i') }}
                            </td>
                            <td class="px-5 py-3">
                                <div class="font-medium text-slate-800 dark:text-white">{{ $log->product->name }}</div>
                                <div class="text-xs text-slate-500">{{ $log->product->sku }}</div>
                            </td>
                            <td class="px-5 py-3">
                                <span class="px-2 py-1 rounded-full text-xs font-medium border
                                        @if(in_array($log->movementType, ['RESTOCK', 'RETURN_IN', 'PURCHASE_IN'])) bg-emerald-50 text-emerald-600 border-emerald-100
                                        @elseif(in_array($log->movementType, ['SALE_OUT', 'EXPIRED_OUT', 'RETURN_OUT'])) bg-rose-50 text-rose-600 border-rose-100
                                        @else bg-blue-50 text-blue-600 border-blue-100
                                        @endif">
                                    {{ str_replace('_', ' ', $log->movementType) }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-center">
                                <span class="font-bold {{ $log->quantity > 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                    {{ $log->quantity > 0 ? '+' : '' }}{{ $log->quantity }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-slate-500 text-xs max-w-xs truncate">
                                {{ $log->note ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-8 text-center text-slate-500">
                                <div class="flex flex-col items-center">
                                    <i class='bx bx-file-blank text-4xl mb-2 text-slate-300'></i>
                                    <p>Belum ada data mutasi stok pada periode ini.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4 border-t border-slate-200 dark:border-slate-700">
            {{ $mutations->links() }}
        </div>
    </div>

    {{-- Modal Create --}}
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
            <div class="bg-white dark:bg-darkCard rounded-xl shadow-2xl w-full max-w-md overflow-hidden animate-fade-in-up">
                <div class="flex justify-between items-center px-6 py-4 border-b border-slate-200 dark:border-slate-700">
                    <h3 class="font-bold text-lg text-slate-800 dark:text-white">Buat Mutasi Stok Baru</h3>
                    <button wire:click="$set('showModal', false)"
                        class="text-slate-400 hover:text-slate-600 transition-colors">
                        <i class='bx bx-x text-2xl'></i>
                    </button>
                </div>
                <div class="p-6 space-y-4">

                    {{-- Product Select --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Pilih
                            Barang</label>
                        <select wire:model="productId"
                            class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-primary focus:border-primary">
                            <option value="">-- Pilih Barang --</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }} (Stok: {{ $product->stock }})</option>
                            @endforeach
                        </select>
                        @error('productId') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                    </div>

                    {{-- Type --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Tipe Mutasi</label>
                        <select wire:model.live="type"
                            class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-primary focus:border-primary">
                            <option value="ADJUSTMENT">Adjusment (Koreksi)</option>
                            <option value="RESTOCK">Restock (Stok Masuk)</option>
                            <option value="RETURN_IN">Retur Customer (Masuk)</option>
                            <option value="RETURN_OUT">Retur ke Supplier (Keluar)</option>
                            <option value="EXPIRED_OUT">Barang Rusak/Expired (Keluar)</option>
                        </select>
                        @error('type') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                    </div>

                    {{-- Mode Toggle (Otomatis berubah by Type, tapi user bisa override jika Adjustment) --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Jenis
                            Perubahan</label>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" wire:model="mode" value="in" class="text-primary focus:ring-primary" {{ in_array($type, ['EXPIRED_OUT', 'RETURN_OUT']) ? 'disabled' : '' }}>
                                <span
                                    class="text-sm {{ $mode == 'in' ? 'font-bold text-emerald-600' : 'text-slate-500' }}">Penambahan
                                    (+)</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" wire:model="mode" value="out" class="text-primary focus:ring-primary" {{ in_array($type, ['RESTOCK', 'RETURN_IN']) ? 'disabled' : '' }}>
                                <span
                                    class="text-sm {{ $mode == 'out' ? 'font-bold text-rose-600' : 'text-slate-500' }}">Pengurangan
                                    (-)</span>
                            </label>
                        </div>
                    </div>

                    {{-- Quantity --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Jumlah Unit</label>
                        <input wire:model="quantity" type="number" min="1"
                            class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-primary focus:border-primary">
                        @error('quantity') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                    </div>

                    {{-- Note --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Catatan</label>
                        <textarea wire:model="note" rows="2"
                            class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-primary focus:border-primary"
                            placeholder="Contoh: Selisih stok opname, atau barang penyok"></textarea>
                        @error('note') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 flex justify-end gap-3">
                    <button wire:click="$set('showModal', false)"
                        class="px-4 py-2 text-sm text-slate-600 hover:text-slate-800 font-medium transition-colors">Batal</button>
                    <button wire:click="save"
                        class="px-4 py-2 text-sm bg-primary hover:bg-indigo-700 text-white rounded-lg font-medium transition-colors flex items-center gap-1">
                        <i class='bx bx-save'></i> Simpan
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>