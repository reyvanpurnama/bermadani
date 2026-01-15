<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-white">Batch Konsinyasi</h1>
            <p class="text-[11px] text-slate-500 mt-0.5">Kelola barang titipan supplier & mahasiswa.</p>
        </div>
        <button wire:click="openCreateModal"
            class="bg-primary hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-[13px] font-bold shadow-md shadow-indigo-500/20 transition-colors flex items-center gap-2">
            <i class='bx bx-archive-in text-lg'></i> Terima Barang Baru
        </button>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div
            class="bg-white dark:bg-darkCard p-4 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 flex items-center gap-3">
            <div
                class="w-10 h-10 rounded-lg bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center text-blue-600 text-xl">
                <i class='bx bx-box'></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Batch Aktif</p>
                <h4 class="text-lg font-bold text-slate-800 dark:text-white">{{ $stats['activeBatches'] }} Batch</h4>
            </div>
        </div>
        <div
            class="bg-white dark:bg-darkCard p-4 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 flex items-center gap-3">
            <div
                class="w-10 h-10 rounded-lg bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center text-emerald-600 text-xl">
                <i class='bx bx-money'></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Nilai Aset</p>
                <h4 class="text-lg font-bold text-slate-800 dark:text-white">Rp
                    {{ number_format($stats['totalAssetValue'], 0, ',', '.') }}</h4>
            </div>
        </div>
        <div
            class="bg-white dark:bg-darkCard p-4 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 flex items-center gap-3">
            <div
                class="w-10 h-10 rounded-lg bg-indigo-50 dark:bg-indigo-500/10 flex items-center justify-center text-primary text-xl">
                <i class='bx bx-trending-up'></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Terjual</p>
                <h4 class="text-lg font-bold text-slate-800 dark:text-white">- <span
                        class="text-[10px] font-normal text-slate-400">Avg</span></h4>
            </div>
        </div>
        <div
            class="bg-white dark:bg-darkCard p-4 rounded-xl shadow-sm border-l-4 border-l-amber-400 border border-slate-100 dark:border-slate-700 flex items-center gap-3">
            <div
                class="w-10 h-10 rounded-lg bg-amber-50 dark:bg-amber-500/10 flex items-center justify-center text-amber-500 text-xl">
                <i class='bx bx-time-five'></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Perlu Dibayar</p>
                <h4 class="text-lg font-bold text-slate-800 dark:text-white">Rp
                    {{ number_format($stats['pendingPayment'], 0, ',', '.') }}</h4>
            </div>
        </div>
    </div>

    {{-- Tab Filter --}}
    <div class="flex gap-2 border-b border-slate-200 dark:border-slate-700 pb-1 overflow-x-auto">
        <button wire:click="setStatus('')"
            class="px-4 py-2 text-[13px] {{ $status === '' ? 'font-semibold text-primary border-b-2 border-primary' : 'font-medium text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-white transition-colors' }}">Semua
            Batch</button>
        <button wire:click="setStatus('ACTIVE')"
            class="px-4 py-2 text-[13px] {{ $status === 'ACTIVE' ? 'font-semibold text-primary border-b-2 border-primary' : 'font-medium text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-white transition-colors' }}">Aktif
            (Jualan)</button>
        <button wire:click="setStatus('PENDING_SETTLEMENT')"
            class="px-4 py-2 text-[13px] {{ $status === 'PENDING_SETTLEMENT' ? 'font-semibold text-primary border-b-2 border-primary' : 'font-medium text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-white transition-colors' }}">Siap
            Settlement</button>
        <button wire:click="setStatus('SETTLED')"
            class="px-4 py-2 text-[13px] {{ $status === 'SETTLED' ? 'font-semibold text-primary border-b-2 border-primary' : 'font-medium text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-white transition-colors' }}">Selesai/Lunas</button>
    </div>

    {{-- Table --}}
    <div
        class="bg-white dark:bg-darkCard rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600 dark:text-slate-400">
                <thead
                    class="bg-slate-50 dark:bg-slate-800/50 text-[10px] uppercase font-bold text-slate-500 dark:text-slate-400 tracking-widest">
                    <tr>
                        <th class="px-5 py-3">Batch ID / Tgl</th>
                        <th class="px-5 py-3">Supplier</th>
                        <th class="px-5 py-3">Item</th>
                        <th class="px-5 py-3 w-[200px]">Progress Terjual</th>
                        <th class="px-5 py-3 text-right">Estimasi Omzet</th>
                        <th class="px-5 py-3 text-center">Status</th>
                        <th class="px-5 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700 text-[13px]">
                    @forelse($batches as $batch)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors group">
                            <td class="px-5 py-3.5">
                                <span
                                    class="font-mono font-bold text-slate-700 dark:text-slate-200 block">#{{ $batch->batchCode }}</span>
                                <span class="text-[10px] text-slate-400">{{ $batch->receivedAt?->format('d M Y') }}</span>
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-2">
                                    <div
                                        class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-[10px] font-bold">
                                        {{ strtoupper(substr($batch->supplier->businessName ?? 'S', 0, 1)) }}
                                    </div>
                                    <span
                                        class="font-medium text-slate-700 dark:text-slate-200">{{ $batch->supplier->businessName ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="block font-medium">{{ $batch->items->count() }} SKU</span>
                                <span class="text-[10px] text-slate-400">Total {{ $batch->totalInitialQty }} Pcs</span>
                            </td>
                            <td class="px-5 py-3.5">
                                @php
                                    $percent = $batch->soldPercent;
                                    $colorClass = $percent >= 70 ? 'emerald' : ($percent >= 30 ? 'amber' : 'rose');
                                @endphp
                                <div class="flex justify-between text-[10px] mb-1">
                                    <span class="font-bold text-{{ $colorClass }}-600">{{ $percent }}% Terjual</span>
                                    <span
                                        class="text-slate-400">{{ $batch->totalSoldQty }}/{{ $batch->totalInitialQty }}</span>
                                </div>
                                <div class="w-full bg-slate-100 dark:bg-slate-700 rounded-full h-1.5">
                                    <div class="bg-{{ $colorClass }}-500 h-1.5 rounded-full" style="width: {{ $percent }}%">
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 text-right font-bold text-slate-800 dark:text-white">
                                Rp {{ number_format($batch->totalValue, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                @if($batch->status === 'ACTIVE')
                                    <span
                                        class="bg-indigo-50 text-indigo-600 dark:bg-indigo-500/20 dark:text-indigo-400 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide">Active</span>
                                @elseif($batch->status === 'PENDING_SETTLEMENT')
                                    <span
                                        class="bg-amber-50 text-amber-600 dark:bg-amber-500/20 dark:text-amber-400 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide">Pending
                                        Pay</span>
                                @elseif($batch->status === 'SETTLED')
                                    <span
                                        class="bg-emerald-50 text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide">Settled</span>
                                @else
                                    <span
                                        class="bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide">{{ $batch->status }}</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                <button wire:click="openDetail({{ $batch->id }})"
                                    class="text-slate-400 hover:text-primary transition-colors">
                                    <i class='bx bx-show text-lg'></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-8 text-center text-slate-500">
                                <div class="flex flex-col items-center">
                                    <i class='bx bx-box text-4xl mb-2 text-slate-300'></i>
                                    <p>Belum ada batch konsinyasi.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($batches->hasPages())
            <div class="px-5 py-3 border-t border-slate-100 dark:border-slate-700">
                {{ $batches->links() }}
            </div>
        @endif
    </div>

    {{-- Create Modal --}}
    @if($showCreateModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4 overflow-y-auto">
            <div
                class="bg-white dark:bg-darkCard rounded-xl shadow-2xl w-full max-w-2xl overflow-hidden animate-fade-in-up my-8">
                <div class="flex justify-between items-center px-6 py-4 border-b border-slate-200 dark:border-slate-700">
                    <h3 class="font-bold text-lg text-slate-800 dark:text-white">Terima Barang Konsinyasi</h3>
                    <button wire:click="$set('showCreateModal', false)"
                        class="text-slate-400 hover:text-slate-600 transition-colors">
                        <i class='bx bx-x text-2xl'></i>
                    </button>
                </div>
                <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Supplier</label>
                        <select wire:model="supplierId"
                            class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-primary focus:border-primary text-sm">
                            <option value="">-- Pilih Supplier --</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->businessName }}</option>
                            @endforeach
                        </select>
                        @error('supplierId') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Item Barang</label>
                            <button wire:click="addItem" type="button"
                                class="text-primary text-xs font-bold hover:underline">+ Tambah Item</button>
                        </div>

                        <div class="space-y-3">
                            @foreach($items as $index => $item)
                                <div
                                    class="bg-slate-50 dark:bg-slate-800 p-3 rounded-lg border border-slate-200 dark:border-slate-700">
                                    <div class="grid grid-cols-12 gap-2">
                                        <div class="col-span-5">
                                            <label class="text-[10px] text-slate-500">Produk</label>
                                            <select wire:model="items.{{ $index }}.productId"
                                                class="w-full px-2 py-1.5 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded text-xs">
                                                <option value="">Pilih...</option>
                                                @foreach($products as $product)
                                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-span-2">
                                            <label class="text-[10px] text-slate-500">Qty</label>
                                            <input wire:model="items.{{ $index }}.initialQty" type="number" min="1"
                                                class="w-full px-2 py-1.5 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded text-xs">
                                        </div>
                                        <div class="col-span-2">
                                            <label class="text-[10px] text-slate-500">Harga Jual</label>
                                            <input wire:model="items.{{ $index }}.sellPrice" type="number"
                                                class="w-full px-2 py-1.5 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded text-xs">
                                        </div>
                                        <div class="col-span-2">
                                            <label class="text-[10px] text-slate-500">Fee %</label>
                                            <input wire:model="items.{{ $index }}.feePercent" type="number"
                                                class="w-full px-2 py-1.5 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded text-xs">
                                        </div>
                                        <div class="col-span-1 flex items-end justify-center">
                                            @if(count($items) > 1)
                                                <button wire:click="removeItem({{ $index }})" type="button"
                                                    class="text-rose-500 hover:text-rose-700 p-1">
                                                    <i class='bx bx-trash'></i>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @error('items') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Catatan
                            (Opsional)</label>
                        <textarea wire:model="note" rows="2"
                            class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-primary focus:border-primary text-sm"></textarea>
                    </div>
                </div>
                <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 flex justify-end gap-3">
                    <button wire:click="$set('showCreateModal', false)"
                        class="px-4 py-2 text-sm text-slate-600 hover:text-slate-800 font-medium transition-colors">Batal</button>
                    <button wire:click="saveBatch"
                        class="px-4 py-2 text-sm bg-primary hover:bg-indigo-700 text-white rounded-lg font-bold transition-colors">
                        Simpan Batch
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Detail Modal --}}
    @if($showDetailModal && $selectedBatch)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4 overflow-y-auto">
            <div
                class="bg-white dark:bg-darkCard rounded-xl shadow-2xl w-full max-w-4xl overflow-hidden animate-fade-in-up my-8">
                <div class="flex justify-between items-center px-6 py-4 border-b border-slate-200 dark:border-slate-700">
                    <div class="flex items-center gap-3">
                        <h3 class="font-bold text-lg text-slate-800 dark:text-white">Batch #{{ $selectedBatch->batchCode }}
                        </h3>
                        @if($selectedBatch->status === 'ACTIVE')
                            <span
                                class="bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400 text-[10px] px-2 py-0.5 rounded-full uppercase tracking-wide">Active</span>
                        @endif
                    </div>
                    <button wire:click="closeDetail" class="text-slate-400 hover:text-slate-600 transition-colors">
                        <i class='bx bx-x text-2xl'></i>
                    </button>
                </div>

                <div class="p-6 grid grid-cols-1 lg:grid-cols-3 gap-6 max-h-[70vh] overflow-y-auto">

                    {{-- Left: Progress & Items --}}
                    <div class="lg:col-span-2 space-y-4">
                        {{-- Progress --}}
                        <div class="bg-slate-50 dark:bg-slate-800 p-4 rounded-lg">
                            <div class="flex justify-between items-end mb-2">
                                <div>
                                    <h3 class="text-[13px] font-bold text-slate-800 dark:text-white">Progres Penjualan</h3>
                                    <p class="text-[10px] text-slate-500">{{ $selectedBatch->totalSoldQty }} dari
                                        {{ $selectedBatch->totalInitialQty }} item terjual</p>
                                </div>
                                <span
                                    class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ $selectedBatch->soldPercent }}%</span>
                            </div>
                            <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-2.5">
                                <div class="bg-emerald-500 h-2.5 rounded-full transition-all duration-1000"
                                    style="width: {{ $selectedBatch->soldPercent }}%"></div>
                            </div>
                        </div>

                        {{-- Items Table --}}
                        <div
                            class="bg-white dark:bg-slate-800/50 rounded-lg border border-slate-200 dark:border-slate-700 overflow-hidden">
                            <div
                                class="p-4 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
                                <h3 class="text-[13px] font-bold text-slate-800 dark:text-white">Rincian Barang</h3>
                                <div class="text-[10px] text-slate-500 bg-slate-100 dark:bg-slate-700 px-2 py-1 rounded">
                                    {{ $selectedBatch->items->count() }} SKU</div>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-sm">
                                    <thead
                                        class="bg-slate-50 dark:bg-slate-800/50 text-[10px] uppercase font-bold text-slate-500 tracking-widest">
                                        <tr>
                                            <th class="px-4 py-2">Produk</th>
                                            <th class="px-4 py-2 text-right">Hrg Jual</th>
                                            <th class="px-4 py-2 text-center">Stok Awal</th>
                                            <th class="px-4 py-2 text-center">Terjual</th>
                                            <th class="px-4 py-2 text-center">Sisa</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700 text-[12px]">
                                        @foreach($selectedBatch->items as $item)
                                            <tr>
                                                <td class="px-4 py-2">
                                                    <div class="font-semibold text-slate-800 dark:text-white">
                                                        {{ $item->product->name ?? '-' }}</div>
                                                    <div class="text-[10px] text-slate-400">Fee: {{ $item->feePercent }}%</div>
                                                </td>
                                                <td class="px-4 py-2 text-right font-medium">Rp
                                                    {{ number_format($item->sellPrice, 0, ',', '.') }}</td>
                                                <td class="px-4 py-2 text-center">{{ $item->initialQty }}</td>
                                                <td class="px-4 py-2 text-center text-emerald-600 font-bold">
                                                    {{ $item->soldQty }}</td>
                                                <td class="px-4 py-2 text-center text-rose-500 font-bold">
                                                    {{ $item->remainingQty }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Right: Supplier & Summary --}}
                    <div class="lg:col-span-1 space-y-4">
                        {{-- Supplier Info --}}
                        <div
                            class="bg-white dark:bg-slate-800/50 p-4 rounded-lg border border-slate-200 dark:border-slate-700 flex flex-col items-center text-center">
                            <div
                                class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center text-white text-xl font-bold shadow-lg mb-2">
                                {{ strtoupper(substr($selectedBatch->supplier->businessName ?? 'S', 0, 1)) }}
                            </div>
                            <h4 class="text-md font-bold text-slate-900 dark:text-white">
                                {{ $selectedBatch->supplier->businessName ?? '-' }}</h4>
                            <p class="text-xs text-slate-500">{{ $selectedBatch->supplier->supplierType ?? 'Supplier' }}</p>
                        </div>

                        {{-- Financial Summary --}}
                        <div
                            class="bg-white dark:bg-slate-800/50 p-4 rounded-lg border border-slate-200 dark:border-slate-700">
                            <h3
                                class="text-[13px] font-bold text-slate-800 dark:text-white mb-3 border-b border-slate-100 dark:border-slate-700 pb-2">
                                Ringkasan Keuangan</h3>

                            <div class="space-y-2 text-[12px]">
                                <div class="flex justify-between items-center">
                                    <span class="text-slate-500">Total Omzet (Gross)</span>
                                    <span class="font-medium text-slate-800 dark:text-white">Rp
                                        {{ number_format($selectedBatch->totalSold, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-slate-500">Fee Koperasi</span>
                                    <span class="font-medium text-rose-500">- Rp
                                        {{ number_format($selectedBatch->feeAmount, 0, ',', '.') }}</span>
                                </div>
                                <div class="border-t border-slate-100 dark:border-slate-700 my-1"></div>
                                <div class="flex justify-between items-center">
                                    <span class="font-bold text-slate-700 dark:text-slate-300">Hak Supplier</span>
                                    <span class="text-[14px] font-bold text-emerald-600 dark:text-emerald-400">Rp
                                        {{ number_format($selectedBatch->payableAmount, 0, ',', '.') }}</span>
                                </div>
                            </div>

                            @if($selectedBatch->status === 'PENDING_SETTLEMENT')
                                <button wire:click="processSettlement"
                                    class="w-full mt-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-bold text-[12px] shadow-md shadow-indigo-500/20 transition-colors flex items-center justify-center gap-2">
                                    <i class='bx bx-money-withdraw'></i> Proses Settlement
                                </button>
                            @endif

                            @if($selectedBatch->status === 'ACTIVE' && $selectedBatch->items->sum('remainingQty') > 0)
                                <button wire:click="openReturModal"
                                    class="w-full mt-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-lg font-bold text-[12px] shadow-md shadow-amber-500/20 transition-colors flex items-center justify-center gap-2">
                                    <i class='bx bx-undo'></i> Retur Barang Sisa
                                </button>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </div>
    @endif

    {{-- Retur Modal --}}
    @if($showReturModal && $selectedBatch)
        <div class="fixed inset-0 z-[60] flex items-center justify-center bg-black/50 backdrop-blur-sm p-4 overflow-y-auto">
            <div
                class="bg-white dark:bg-darkCard rounded-xl shadow-2xl w-full max-w-lg overflow-hidden animate-fade-in-up my-8">
                <div class="flex justify-between items-center px-6 py-4 border-b border-slate-200 dark:border-slate-700 bg-amber-50 dark:bg-amber-500/10">
                    <div>
                        <h3 class="font-bold text-lg text-slate-800 dark:text-white flex items-center gap-2">
                            <i class='bx bx-undo text-amber-500'></i> Retur Barang Konsinyasi
                        </h3>
                        <p class="text-[11px] text-slate-500 mt-0.5">Batch #{{ $selectedBatch->batchCode }} - {{ $selectedBatch->supplier->businessName ?? '-' }}</p>
                    </div>
                    <button wire:click="closeReturModal" class="text-slate-400 hover:text-slate-600 transition-colors">
                        <i class='bx bx-x text-2xl'></i>
                    </button>
                </div>
                
                <div class="p-6 space-y-4 max-h-[60vh] overflow-y-auto">
                    <div class="bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/30 rounded-lg p-3">
                        <p class="text-[12px] text-amber-700 dark:text-amber-400">
                            <i class='bx bx-info-circle mr-1'></i>
                            Barang yang diretur akan dikembalikan ke supplier dan stok akan dikurangi.
                        </p>
                    </div>

                    <div class="space-y-3">
                        @foreach($returItems as $index => $item)
                            <div class="bg-slate-50 dark:bg-slate-800 p-4 rounded-lg border border-slate-200 dark:border-slate-700">
                                <div class="flex justify-between items-start mb-3">
                                    <div>
                                        <h4 class="font-semibold text-slate-800 dark:text-white text-[13px]">{{ $item['productName'] }}</h4>
                                        <p class="text-[10px] text-slate-500">Sisa: {{ $item['remainingQty'] }} pcs</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <label class="text-[11px] text-slate-600 dark:text-slate-400 whitespace-nowrap">Jumlah Retur:</label>
                                    <input wire:model="returItems.{{ $index }}.returQty" type="number" min="0" max="{{ $item['remainingQty'] }}"
                                        class="flex-1 px-3 py-2 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg text-sm text-center font-medium">
                                    <span class="text-[11px] text-slate-500">pcs</span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if(count($returItems) === 0)
                        <div class="text-center py-8 text-slate-500">
                            <i class='bx bx-check-circle text-4xl text-emerald-500 mb-2'></i>
                            <p class="text-[13px]">Semua barang sudah terjual!</p>
                        </div>
                    @endif
                </div>
                
                <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 flex justify-end gap-3 border-t border-slate-200 dark:border-slate-700">
                    <button wire:click="closeReturModal"
                        class="px-4 py-2 text-sm text-slate-600 hover:text-slate-800 font-medium transition-colors">Batal</button>
                    @if(count($returItems) > 0)
                        <button wire:click="processRetur"
                            class="px-4 py-2 text-sm bg-amber-500 hover:bg-amber-600 text-white rounded-lg font-bold transition-colors flex items-center gap-2">
                            <i class='bx bx-check'></i> Proses Retur
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif

</div>