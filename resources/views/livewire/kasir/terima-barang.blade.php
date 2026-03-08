<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <div>
            <a href="{{ route('kasir.dashboard') }}"
                class="inline-flex items-center gap-1 text-xs text-slate-400 hover:text-primary mb-1.5 transition-colors">
                <i class='bx bx-arrow-back text-sm'></i> Dashboard
            </a>
            <h1 class="text-xl font-bold text-slate-800 dark:text-white">Terima Barang</h1>
            <p class="text-[11px] text-slate-500 mt-0.5">Konfirmasi penerimaan barang dari supplier — stok otomatis bertambah setelah konfirmasi</p>
        </div>
    </div>

    {{-- Empty State --}}
    @if($this->batches->isEmpty())
    <div class="bg-white dark:bg-darkCard rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 p-12 text-center">
        <div class="w-16 h-16 bg-emerald-50 dark:bg-emerald-900/20 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class='bx bx-check-shield text-3xl text-emerald-500'></i>
        </div>
        <h3 class="font-bold text-slate-700 dark:text-white">Tidak ada barang yang menunggu</h3>
        <p class="text-sm text-slate-400 mt-1">Semua pengiriman supplier sudah dikonfirmasi ✅</p>
    </div>
    @else

    {{-- Pending Batches List --}}
    <div class="space-y-3">
        @foreach($this->batches as $batch)
        <div class="bg-white dark:bg-darkCard rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 p-5">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-start gap-4">
                    {{-- Animated pulse icon --}}
                    <div class="w-11 h-11 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                        <i class='bx bx-package text-xl text-blue-600 dark:text-blue-400 animate-pulse'></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <h3 class="font-bold text-slate-900 dark:text-white">{{ $batch->batchCode }}</h3>
                            <span class="text-[10px] bg-blue-50 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400 px-2 py-0.5 rounded-full font-bold uppercase">Menunggu Konfirmasi</span>
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5">
                            <span class="font-medium text-slate-700 dark:text-slate-300">{{ $batch->supplier->businessName ?? '-' }}</span>
                            · Dikirim {{ $batch->created_at->diffForHumans() }}
                        </p>
                        <div class="flex flex-wrap gap-2 mt-2">
                            @foreach($batch->items->take(4) as $item)
                            <span class="text-[11px] bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-400 px-2 py-0.5 rounded-lg">
                                {{ $item->product->name ?? '-' }} <span class="font-bold">×{{ $item->initialQty }}</span>
                            </span>
                            @endforeach
                            @if($batch->items->count() > 4)
                            <span class="text-[11px] text-slate-400">+{{ $batch->items->count() - 4 }} lainnya</span>
                            @endif
                        </div>
                        @if($batch->note)
                        <p class="text-[11px] text-amber-600 dark:text-amber-400 mt-1.5 flex items-center gap-1">
                            <i class='bx bx-note text-sm'></i> {{ $batch->note }}
                        </p>
                        @endif
                    </div>
                </div>
                <div class="flex items-center gap-3 sm:flex-shrink-0">
                    <div class="text-right">
                        <p class="text-[10px] text-slate-400">Total Produk</p>
                        <p class="font-bold text-slate-900 dark:text-white">{{ $batch->items->sum('initialQty') }} pcs</p>
                    </div>
                    <button wire:click="openDetail({{ $batch->id }})"
                        class="bg-primary hover:bg-indigo-700 text-white font-semibold px-4 py-2.5 rounded-xl text-sm transition-colors flex items-center gap-2 shadow-sm shadow-indigo-500/20">
                        <i class='bx bx-check-double text-lg'></i> Konfirmasi
                    </button>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{ $this->batches->links() }}
    @endif

    {{-- Konfirmasi Modal --}}
    @if($showDetailModal && $this->selectedBatch)
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-darkCard rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden max-h-[90vh] flex flex-col">

            {{-- Modal Header --}}
            <div class="bg-gradient-to-r from-primary to-indigo-600 p-5 text-white flex-shrink-0">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-bold">Konfirmasi Terima Barang</h2>
                        <p class="text-white/80 text-xs mt-0.5">
                            {{ $this->selectedBatch->batchCode }} · {{ $this->selectedBatch->supplier->businessName ?? '-' }}
                        </p>
                    </div>
                    <button wire:click="closeDetail()" class="text-white/70 hover:text-white transition-colors">
                        <i class='bx bx-x text-2xl'></i>
                    </button>
                </div>
            </div>

            {{-- Modal Body --}}
            <div class="p-5 overflow-y-auto flex-1 space-y-4">

                {{-- Info --}}
                <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl px-4 py-3 flex items-center gap-2 text-sm text-amber-700 dark:text-amber-400">
                    <i class='bx bx-info-circle'></i>
                    Periksa jumlah fisik barang. Ubah qty jika ada selisih (rusak/kurang). Stok otomatis bertambah sesuai yang dikonfirmasi.
                </div>

                {{-- Items Table --}}
                <div class="divide-y divide-slate-100 dark:divide-slate-700 border border-slate-100 dark:border-slate-700 rounded-xl overflow-hidden">
                    <div class="grid grid-cols-12 gap-2 px-4 py-2.5 bg-slate-50 dark:bg-slate-800/50 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <div class="col-span-5">Produk</div>
                        <div class="col-span-3 text-center">Diminta</div>
                        <div class="col-span-4 text-center">Diterima (Fisik)</div>
                    </div>
                    @foreach($receiveItems as $index => $ri)
                    <div class="grid grid-cols-12 gap-2 px-4 py-3 items-center">
                        <div class="col-span-5">
                            <p class="text-sm font-medium text-slate-800 dark:text-white">{{ $ri['productName'] }}</p>
                        </div>
                        <div class="col-span-3 text-center">
                            <span class="font-bold text-slate-600 dark:text-slate-300">{{ $ri['requestedQty'] }}</span>
                            <span class="text-[10px] text-slate-400 ml-0.5">pcs</span>
                        </div>
                        <div class="col-span-4">
                            <input type="number"
                                wire:model="receiveItems.{{ $index }}.receivedQty"
                                min="0" max="{{ $ri['requestedQty'] * 2 }}"
                                class="w-full bg-slate-50 dark:bg-slate-800 border {{ (int)($ri['receivedQty'] ?? $ri['requestedQty']) < $ri['requestedQty'] ? 'border-rose-300 dark:border-rose-700' : 'border-slate-200 dark:border-slate-600' }} rounded-xl px-3 py-1.5 text-sm font-bold text-center focus:ring-2 focus:ring-primary/20 focus:border-primary">
                            @if(isset($ri['receivedQty']) && (int)$ri['receivedQty'] < $ri['requestedQty'])
                            <p class="text-[10px] text-rose-500 text-center mt-0.5">
                                Selisih: {{ $ri['requestedQty'] - (int)$ri['receivedQty'] }} pcs
                            </p>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Note --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Catatan (Opsional)</label>
                    <textarea wire:model="receiveNote" rows="2" placeholder="Misal: ada 2 donat kemasan rusak..."
                        class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-xl px-4 py-2.5 text-sm resize-none focus:ring-2 focus:ring-primary/20 focus:border-primary"></textarea>
                </div>
            </div>

            {{-- Modal Footer --}}
            <div class="p-5 border-t border-slate-100 dark:border-slate-700 flex gap-3 flex-shrink-0">
                <button wire:click="closeDetail()"
                    class="flex-1 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 font-medium py-2.5 px-4 rounded-xl transition-colors text-sm">
                    Batal
                </button>
                <button wire:click="confirmReceive()" wire:loading.attr="disabled"
                    class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 px-4 rounded-xl transition-colors text-sm flex items-center justify-center gap-2">
                    <span wire:loading.remove wire:target="confirmReceive">
                        <i class='bx bx-check-circle text-lg'></i> Konfirmasi Terima
                    </span>
                    <span wire:loading wire:target="confirmReceive" class="flex items-center gap-2">
                        <i class='bx bx-loader-alt animate-spin text-lg'></i> Memproses...
                    </span>
                </button>
            </div>
        </div>
    </div>
    @endif

</div>
