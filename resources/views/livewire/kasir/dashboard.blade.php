<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-800 dark:text-white">Dashboard Kasir</h1>
            <p class="text-sm text-slate-500">Selamat datang, {{ auth()->user()->name }}!</p>
        </div>
        <a href="{{ route('kasir.pos') }}" class="bg-primary hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium text-sm flex items-center gap-2 transition-colors shadow-lg shadow-indigo-500/30">
            <i class='bx bx-shopping-bag text-lg'></i> Mulai Transaksi Baru
        </a>
    </div>

    {{-- Quick Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-card dark:bg-darkCard rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-1">Penjualan Hari Ini</p>
                    <h3 class="text-2xl font-bold text-slate-900 dark:text-white">Rp {{ number_format($this->todaySales, 0, ',', '.') }}</h3>
                </div>
                <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-900/30 rounded-xl flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                    <i class='bx bx-wallet text-2xl'></i>
                </div>
            </div>
        </div>

        <div class="bg-card dark:bg-darkCard rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-1">Jumlah Transaksi</p>
                    <h3 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $this->todayTransactionsCount }}</h3>
                    <p class="text-[10px] text-slate-400 mt-1">Transaksi hari ini</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center text-blue-600 dark:text-blue-400">
                    <i class='bx bx-receipt text-2xl'></i>
                </div>
            </div>
        </div>

        <div class="bg-card dark:bg-darkCard rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-1">Stok Kritis</p>
                    <h3 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $this->lowStockProducts->count() }}</h3>
                    <p class="text-[10px] text-rose-500 mt-1">Produk butuh restock</p>
                </div>
                <div class="w-12 h-12 bg-rose-100 dark:bg-rose-900/30 rounded-xl flex items-center justify-center text-rose-600 dark:text-rose-400">
                    <i class='bx bx-error-circle text-2xl'></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        {{-- Recent Transactions --}}
        <div class="bg-card dark:bg-darkCard rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">
            <div class="p-5 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
                <h3 class="font-bold text-base text-slate-800 dark:text-white">Transaksi Terakhir</h3>
                <a href="{{ route('kasir.transactions') }}" class="text-sm text-primary font-medium hover:underline">Lihat Semua</a>
            </div>
            <div class="divide-y divide-slate-100 dark:divide-slate-700">
                @forelse($this->recentTransactions as $trx)
                    <div class="p-4 hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                        <div class="flex justify-between items-start mb-2">
                            <span class="font-mono text-xs text-slate-500">{{ $trx->invoiceNumber }}</span>
                            <span class="text-[10px] text-slate-400">{{ $trx->date?->format('H:i') }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <div>
                                @if($trx->member)
                                    <span class="text-sm font-medium text-slate-900 dark:text-white">{{ $trx->member->name }}</span>
                                @else
                                    <span class="text-sm text-slate-400">Guest</span>
                                @endif
                                <span class="text-[10px] bg-{{ $trx->paymentMethod === 'CASH' ? 'emerald' : 'blue' }}-100 dark:bg-{{ $trx->paymentMethod === 'CASH' ? 'emerald' : 'blue' }}-900/30 text-{{ $trx->paymentMethod === 'CASH' ? 'emerald' : 'blue' }}-600 dark:text-{{ $trx->paymentMethod === 'CASH' ? 'emerald' : 'blue' }}-400 px-1.5 py-0.5 rounded ml-2">{{ $trx->paymentMethod }}</span>
                            </div>
                            <span class="text-sm font-bold text-slate-900 dark:text-white">Rp {{ number_format($trx->totalAmount, 0, ',', '.') }}</span>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-400">
                        <i class='bx bx-receipt text-4xl'></i>
                        <p class="text-sm mt-2">Belum ada transaksi hari ini</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Low Stock Alert --}}
        <div class="bg-card dark:bg-darkCard rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">
            <div class="p-5 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
                <h3 class="font-bold text-base text-slate-800 dark:text-white flex items-center gap-2">
                    <i class='bx bx-error-circle text-rose-500'></i> Stok Menipis
                </h3>
                <a href="{{ route('admin.products') }}" class="text-sm text-primary font-medium hover:underline">Lihat Semua</a>
            </div>
            <div class="divide-y divide-slate-100 dark:divide-slate-700">
                @forelse($this->lowStockProducts as $product)
                    <div class="p-4 hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                        <div class="flex justify-between items-center mb-2">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded bg-slate-100 dark:bg-slate-700 flex items-center justify-center text-sm">
                                    {{ $product->category?->icon ?? '📦' }}
                                </div>
                                <div>
                                    <h6 class="text-sm font-semibold text-slate-800 dark:text-white">{{ $product->name }}</h6>
                                    <p class="text-[10px] text-slate-400">{{ $product->category?->name ?? '-' }}</p>
                                </div>
                            </div>
                            <span class="text-sm font-bold {{ $product->stock <= 5 ? 'text-rose-500' : 'text-amber-500' }}">
                                Sisa {{ $product->stock }}
                            </span>
                        </div>
                        <div class="w-full bg-slate-100 dark:bg-slate-700 rounded-full h-1">
                            <div class="{{ $product->stock <= 5 ? 'bg-rose-500' : 'bg-amber-500' }} h-1 rounded-full" style="width: {{ min(100, ($product->stock / max($product->threshold, 1)) * 50) }}%"></div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-400">
                        <i class='bx bx-check-circle text-4xl text-emerald-500'></i>
                        <p class="text-sm mt-2">Semua stok aman!</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>
</div>
