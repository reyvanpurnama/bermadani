<div>
    @section('page-title', 'Riwayat Belanja')

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white dark:bg-darkCard p-5 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 text-blue-600 rounded-xl flex items-center justify-center">
                    <i class='bx bx-shopping-bag text-2xl'></i>
                </div>
                <div>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Transaksi</p>
                    <h3 class="text-2xl font-bold text-slate-800 dark:text-white">{{ $stats['totalTransactions'] }}</h3>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-darkCard p-5 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 rounded-xl flex items-center justify-center">
                    <i class='bx bx-money text-2xl'></i>
                </div>
                <div>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Belanja</p>
                    <h3 class="text-xl font-bold text-slate-800 dark:text-white">Rp {{ number_format($stats['totalSpent'], 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-amber-500 to-orange-500 p-5 rounded-2xl shadow-lg text-white">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                    <i class='bx bxs-star text-2xl'></i>
                </div>
                <div>
                    <p class="text-[11px] text-amber-100 uppercase tracking-wider">Poin Terkumpul</p>
                    <h3 class="text-2xl font-bold">{{ number_format($member->points ?? 0) }} Pts</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter --}}
    <div class="bg-white dark:bg-darkCard rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 p-4 mb-6">
        <div class="flex flex-wrap items-center gap-4">
            <div class="flex items-center gap-2">
                <label class="text-sm text-slate-500 dark:text-slate-400">Filter Bulan:</label>
                <input type="month" wire:model.live="filterMonth" class="px-3 py-2 border border-slate-200 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-800 text-slate-800 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary transition-colors">
            </div>
            @if($filterMonth)
                <button wire:click="$set('filterMonth', '')" class="text-sm text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-300 flex items-center gap-1">
                    <i class='bx bx-x'></i> Reset Filter
                </button>
            @endif
        </div>
    </div>

    {{-- Transactions List --}}
    <div class="bg-white dark:bg-darkCard rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">
        @if($transactions->count() > 0)
            <div class="divide-y divide-slate-100 dark:divide-slate-700">
                @foreach($transactions as $trx)
                    <div class="p-4 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors cursor-pointer" x-data="{ expanded: false }" @click="expanded = !expanded">
                        <div class="flex justify-between items-center">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-blue-50 dark:bg-blue-900/20 text-blue-600 rounded-xl flex items-center justify-center">
                                    <i class='bx bx-shopping-bag text-xl'></i>
                                </div>
                                <div>
                                    <p class="font-bold text-slate-800 dark:text-white">Belanja #{{ $trx->id }}</p>
                                    <p class="text-sm text-slate-500">{{ $trx->created_at->format('d M Y • H:i') }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-bold text-slate-800 dark:text-white">Rp {{ number_format($trx->totalAmount, 0, ',', '.') }}</p>
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 text-xs font-bold rounded-full {{ $trx->status === 'COMPLETED' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                    {{ $trx->status }}
                                </span>
                            </div>
                        </div>
                        
                        {{-- Expanded Details --}}
                        <div x-show="expanded" x-collapse class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-700">
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
                                <div>
                                    <p class="text-slate-500">Total Belanja</p>
                                    <p class="font-bold text-slate-800 dark:text-white">Rp {{ number_format($trx->totalAmount, 0, ',', '.') }}</p>
                                </div>
                                <div>
                                    <p class="text-slate-500">Metode Bayar</p>
                                    <p class="font-bold text-slate-800 dark:text-white">{{ $trx->paymentMethod ?? 'CASH' }}</p>
                                </div>
                                <div>
                                    <p class="text-slate-500">Poin Didapat</p>
                                    <p class="font-bold text-amber-500">+{{ floor($trx->totalAmount / 1000) }} Pts</p>
                                </div>
                            </div>
                            @if($trx->items && $trx->items->count() > 0)
                                <div class="mt-4">
                                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Item Pembelian</p>
                                    <div class="space-y-2">
                                        @foreach($trx->items->take(5) as $item)
                                            <div class="flex justify-between text-sm bg-slate-50 dark:bg-slate-800 px-3 py-2 rounded-lg">
                                                <span class="text-slate-700 dark:text-slate-300">{{ $item->product->name ?? 'Produk' }} x{{ $item->quantity }}</span>
                                                <span class="font-bold text-slate-800 dark:text-white">Rp {{ number_format($item->totalPrice ?? 0, 0, ',', '.') }}</span>
                                            </div>
                                        @endforeach
                                        @if($trx->items->count() > 5)
                                            <p class="text-xs text-slate-400 text-center">+{{ $trx->items->count() - 5 }} item lainnya</p>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-700">
                {{ $transactions->links() }}
            </div>
        @else
            <div class="p-12 text-center">
                <div class="w-16 h-16 bg-slate-100 dark:bg-slate-700 rounded-full mx-auto mb-4 flex items-center justify-center">
                    <i class='bx bx-shopping-bag text-3xl text-slate-400'></i>
                </div>
                <h4 class="text-lg font-bold text-slate-800 dark:text-white mb-2">Belum Ada Transaksi</h4>
                <p class="text-sm text-slate-500">Riwayat belanja Anda akan muncul di sini</p>
            </div>
        @endif
    </div>
</div>