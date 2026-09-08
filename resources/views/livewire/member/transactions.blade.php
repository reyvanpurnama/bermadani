<div>
    @section('title', 'Riwayat Belanja Toko')

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white/80 dark:bg-zinc-900/80 backdrop-blur-xl p-5 rounded-3xl border border-zinc-200/80 dark:border-zinc-800 shadow-sm">
            <div class="flex items-center gap-3.5">
                <div class="w-11 h-11 bg-blue-500/10 text-blue-600 dark:text-blue-400 rounded-2xl flex items-center justify-center text-xl shrink-0">
                    <i class='bx bx-shopping-bag'></i>
                </div>
                <div>
                    <p class="text-[10px] text-zinc-400 uppercase tracking-wider font-extrabold">Total Frekuensi</p>
                    <h3 class="font-heading text-2xl font-extrabold text-zinc-900 dark:text-white">{{ $stats['totalTransactions'] }} Transaksi</h3>
                </div>
            </div>
        </div>
        <div class="bg-white/80 dark:bg-zinc-900/80 backdrop-blur-xl p-5 rounded-3xl border border-zinc-200/80 dark:border-zinc-800 shadow-sm">
            <div class="flex items-center gap-3.5">
                <div class="w-11 h-11 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-2xl flex items-center justify-center text-xl shrink-0">
                    <i class='bx bx-money'></i>
                </div>
                <div>
                    <p class="text-[10px] text-zinc-400 uppercase tracking-wider font-extrabold">Akumulasi Belanja</p>
                    <h3 class="font-heading text-xl font-extrabold text-zinc-900 dark:text-white">Rp {{ number_format($stats['totalSpent'], 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-amber-500 to-orange-500 p-5 rounded-3xl shadow-lg text-white">
            <div class="flex items-center gap-3.5">
                <div class="w-11 h-11 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center text-xl shrink-0 text-white">
                    <i class='bx bxs-star'></i>
                </div>
                <div>
                    <p class="text-[10px] text-amber-100 uppercase tracking-wider font-extrabold">Poin Loyalty</p>
                    <h3 class="font-heading text-2xl font-extrabold text-white">{{ number_format($member->points ?? 0) }} Pts</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter --}}
    <div class="bg-white/80 dark:bg-zinc-900/80 backdrop-blur-xl rounded-3xl border border-zinc-200/80 dark:border-zinc-800 p-4 mb-6 shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <label class="text-xs font-bold text-zinc-500 dark:text-zinc-400">Filter Periode Bulan:</label>
                <input type="month" wire:model.live="filterMonth" class="px-3.5 py-2 border border-zinc-200 dark:border-zinc-700 rounded-xl text-xs bg-zinc-50 dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-[#155A6B] focus:border-[#155A6B] transition-all">
            </div>
            @if($filterMonth)
                <button wire:click="$set('filterMonth', '')" class="text-xs font-bold text-rose-500 hover:text-rose-600 flex items-center gap-1">
                    <i class='bx bx-x text-base'></i> Reset Filter
                </button>
            @endif
        </div>
    </div>

    {{-- Transactions List --}}
    <div class="bg-white/80 dark:bg-zinc-900/80 backdrop-blur-xl rounded-3xl border border-zinc-200/80 dark:border-zinc-800 overflow-hidden shadow-sm">
        @if($transactions->count() > 0)
            <div class="divide-y divide-zinc-200/60 dark:divide-zinc-800">
                @foreach($transactions as $trx)
                    <div class="p-5 hover:bg-zinc-50 dark:hover:bg-zinc-800/40 transition-colors cursor-pointer" x-data="{ expanded: false }" @click="expanded = !expanded">
                        <div class="flex justify-between items-center gap-4">
                            <div class="flex items-center gap-4">
                                <div class="w-11 h-11 bg-blue-500/10 text-blue-600 dark:text-blue-400 rounded-2xl flex items-center justify-center text-xl shrink-0">
                                    <i class='bx bx-shopping-bag'></i>
                                </div>
                                <div>
                                    <p class="font-heading font-extrabold text-sm text-zinc-900 dark:text-white">Struk Transaksi #{{ $trx->id }}</p>
                                    <p class="text-xs text-zinc-400 mt-0.5">{{ $trx->created_at->format('d M Y • H:i') }} WIB</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-mono font-extrabold text-base text-zinc-900 dark:text-white">Rp {{ number_format($trx->totalAmount, 0, ',', '.') }}</p>
                                <span class="px-2 py-0.5 text-[9px] font-extrabold rounded-full uppercase tracking-wider {{ $trx->status === 'COMPLETED' ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' : 'bg-amber-500/10 text-amber-600' }}">
                                    {{ $trx->status }}
                                </span>
                            </div>
                        </div>
                        
                        {{-- Expanded Details --}}
                        <div x-show="expanded" x-collapse class="mt-4 pt-4 border-t border-zinc-200/60 dark:border-zinc-800">
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 text-xs">
                                <div>
                                    <p class="text-zinc-400">Total Pembayaran</p>
                                    <p class="font-mono font-extrabold text-zinc-900 dark:text-white mt-0.5">Rp {{ number_format($trx->totalAmount, 0, ',', '.') }}</p>
                                </div>
                                <div>
                                    <p class="text-zinc-400">Metode Bayar</p>
                                    <p class="font-bold text-zinc-900 dark:text-white mt-0.5">{{ $trx->paymentMethod ?? 'CASH' }}</p>
                                </div>
                                <div>
                                    <p class="text-zinc-400">Bonus Poin</p>
                                    <p class="font-bold text-amber-500 mt-0.5">+{{ floor($trx->totalAmount / 1000) }} Pts</p>
                                </div>
                            </div>
                            @if($trx->items && $trx->items->count() > 0)
                                <div class="mt-4">
                                    <p class="text-[10px] font-extrabold text-zinc-400 uppercase tracking-wider mb-2">Item Pembelian POS</p>
                                    <div class="space-y-2">
                                        @foreach($trx->items->take(5) as $item)
                                            <div class="flex justify-between text-xs bg-zinc-50 dark:bg-zinc-800/60 px-3.5 py-2 rounded-xl">
                                                <span class="text-zinc-700 dark:text-zinc-300 font-medium">{{ $item->product->name ?? 'Produk' }} x{{ $item->quantity }}</span>
                                                <span class="font-mono font-bold text-zinc-900 dark:text-white">Rp {{ number_format($item->totalPrice ?? 0, 0, ',', '.') }}</span>
                                            </div>
                                        @endforeach
                                        @if($trx->items->count() > 5)
                                            <p class="text-[10px] text-zinc-400 text-center">+{{ $trx->items->count() - 5 }} item lainnya</p>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="px-6 py-4 border-t border-zinc-200/80 dark:border-zinc-800">
                {{ $transactions->links() }}
            </div>
        @else
            <div class="p-12 text-center">
                <div class="w-14 h-14 bg-zinc-100 dark:bg-zinc-800 rounded-full mx-auto mb-3 flex items-center justify-center text-zinc-400 text-2xl">
                    <i class='bx bx-shopping-bag'></i>
                </div>
                <h4 class="font-heading font-extrabold text-base text-zinc-900 dark:text-white mb-1">Belum Ada Transaksi Belanja</h4>
                <p class="text-xs text-zinc-500">Riwayat transaksi belanja Anda di toko POS akan tercatat di sini.</p>
            </div>
        @endif
    </div>
</div>