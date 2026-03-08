<div class="space-y-6">
    {{-- Flash Message --}}
    @if(session('error'))
    <div class="bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-400 px-4 py-3 rounded-xl flex items-center gap-3">
        <i class='bx bx-error-circle text-xl'></i>
        <span class="text-sm font-medium">{{ session('error') }}</span>
    </div>
    @endif

    {{-- Check-In Modal --}}
    @if($showCheckInModal)
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-darkCard rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
            <div class="bg-gradient-to-r from-primary to-indigo-600 p-6 text-white text-center">
                <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class='bx bx-log-in-circle text-4xl'></i>
                </div>
                <h2 class="text-xl font-bold">Selamat Datang! 👋</h2>
                <p class="text-white/80 text-sm mt-1">Silakan check-in untuk memulai shift</p>
            </div>
            <div class="p-6">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Modal Awal (Rp)</label>
                    <input type="number" wire:model="openingCash" 
                        class="w-full px-4 py-3 text-lg font-bold text-center rounded-xl border-2 border-slate-200 dark:border-slate-600 dark:bg-slate-800 focus:ring-primary focus:border-primary"
                        placeholder="0">
                    <p class="text-xs text-slate-400 mt-1 text-center">Masukkan jumlah uang di laci kasir</p>
                </div>
                <button wire:click="checkIn" 
                    class="w-full bg-primary hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded-xl transition-colors flex items-center justify-center gap-2">
                    <i class='bx bx-check-circle text-xl'></i> Check-In Sekarang
                </button>
                <button wire:click="skipCheckIn" 
                    class="w-full mt-2 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-600 dark:text-slate-300 font-medium py-2.5 px-4 rounded-xl transition-colors flex items-center justify-center gap-2 text-sm">
                    <i class='bx bx-show text-lg'></i> Lihat Dashboard Saja
                </button>
                <p class="text-[10px] text-slate-400 text-center mt-2">*Tanpa check-in, kamu tidak bisa melakukan transaksi</p>
            </div>
        </div>
    </div>
    @endif

    {{-- Check-Out Modal --}}
    @if($showCheckOutModal)
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-darkCard rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
            <div class="bg-gradient-to-r from-rose-500 to-pink-600 p-6 text-white text-center">
                <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class='bx bx-log-out-circle text-4xl'></i>
                </div>
                <h2 class="text-xl font-bold">Check-Out Shift</h2>
                <p class="text-white/80 text-sm mt-1">Tutup shift dan lihat ringkasan</p>
            </div>
            <div class="p-6 space-y-4">
                {{-- Shift Summary --}}
                <div class="bg-slate-50 dark:bg-slate-800 rounded-xl p-4 space-y-3">
                    <h3 class="font-bold text-slate-800 dark:text-white text-sm">Ringkasan Shift</h3>
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-slate-500">Modal Awal:</span>
                            <span class="font-bold text-slate-800 dark:text-white">Rp {{ number_format($this->currentShift?->opening_cash ?? 0, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Transaksi:</span>
                            <span class="font-bold text-slate-800 dark:text-white">{{ $this->todayTransactionsCount }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Penjualan Cash:</span>
                            <span class="font-bold text-emerald-600">Rp {{ number_format($this->cashSales, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Total Penjualan:</span>
                            <span class="font-bold text-emerald-600">Rp {{ number_format($this->todaySales, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    <div class="pt-3 border-t border-slate-200 dark:border-slate-700">
                        <div class="flex justify-between text-base">
                            <span class="font-medium text-slate-700 dark:text-slate-300">Seharusnya di Laci:</span>
                            <span class="font-bold text-primary text-lg">Rp {{ number_format($this->expectedCash, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                {{-- Closing Cash Input --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Uang di Laci Sekarang (Rp)</label>
                    <input type="number" wire:model="closingCash" 
                        class="w-full px-4 py-3 text-lg font-bold text-center rounded-xl border-2 border-slate-200 dark:border-slate-600 dark:bg-slate-800 focus:ring-primary focus:border-primary"
                        placeholder="0">
                </div>

                {{-- Difference Warning --}}
                @if($closingCash && $closingCash != $this->expectedCash)
                <div class="p-3 rounded-xl {{ $closingCash > $this->expectedCash ? 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400' : 'bg-rose-50 dark:bg-rose-900/20 text-rose-700 dark:text-rose-400' }}">
                    <div class="flex items-center gap-2">
                        <i class='bx {{ $closingCash > $this->expectedCash ? "bx-trending-up" : "bx-trending-down" }} text-xl'></i>
                        <span class="font-medium">Selisih: Rp {{ number_format(abs($closingCash - $this->expectedCash), 0, ',', '.') }} {{ $closingCash > $this->expectedCash ? '(Lebih)' : '(Kurang)' }}</span>
                    </div>
                </div>
                @endif

                {{-- Note --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Catatan (Opsional)</label>
                    <textarea wire:model="closeNote" rows="2"
                        class="w-full px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-600 dark:bg-slate-800 focus:ring-primary focus:border-primary text-sm"
                        placeholder="Tambahkan catatan jika ada..."></textarea>
                </div>

                <div class="flex gap-3">
                    <button wire:click="$set('showCheckOutModal', false)" 
                        class="flex-1 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 font-medium py-3 px-4 rounded-xl transition-colors">
                        Batal
                    </button>
                    <button wire:click="checkOut" 
                        class="flex-1 bg-rose-500 hover:bg-rose-600 text-white font-bold py-3 px-4 rounded-xl transition-colors flex items-center justify-center gap-2">
                        <i class='bx bx-log-out text-xl'></i> Check-Out
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Main Dashboard Content (only show if checked in OR view-only mode) --}}
    @if($this->currentShift || $viewOnlyMode)
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-800 dark:text-white">Dashboard Kasir</h1>
            @if($this->currentShift)
            <p class="text-sm text-slate-500">Shift dimulai {{ $this->currentShift->check_in_at->format('H:i') }} • {{ $this->currentShift->duration }}</p>
            @else
            <p class="text-sm text-amber-500 flex items-center gap-1"><i class='bx bx-info-circle'></i> Mode lihat saja - Belum check-in</p>
            @endif
        </div>
        <div class="flex items-center gap-3">
            @if($this->currentShift)
            <button wire:click="openCheckOutModal" class="bg-rose-500 hover:bg-rose-600 text-white px-4 py-2 rounded-lg font-medium text-sm flex items-center gap-2 transition-colors">
                <i class='bx bx-log-out text-lg'></i> Check-Out
            </button>
            <a href="{{ route('kasir.pos') }}" class="bg-primary hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium text-sm flex items-center gap-2 transition-colors shadow-lg shadow-indigo-500/30">
                <i class='bx bx-shopping-bag text-lg'></i> Mulai Transaksi
            </a>
            @else
            <button wire:click="openCheckInModal" class="bg-primary hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium text-sm flex items-center gap-2 transition-colors shadow-lg shadow-indigo-500/30">
                <i class='bx bx-log-in text-lg'></i> Check-In Sekarang
            </button>
            @endif
        </div>
    </div>

    @if($viewOnlyMode && !$this->currentShift)
    {{-- View Only Stats - Show overall performance --}}
    @php
        $myTotalSales = \App\Models\Transaction::where('userId', auth()->id())->where('status', 'COMPLETED')->sum('totalAmount');
        $myTotalTransactions = \App\Models\Transaction::where('userId', auth()->id())->where('status', 'COMPLETED')->count();
        $myTodaySales = \App\Models\Transaction::where('userId', auth()->id())->where('status', 'COMPLETED')->whereDate('date', today())->sum('totalAmount');
        $myTodayTransactions = \App\Models\Transaction::where('userId', auth()->id())->where('status', 'COMPLETED')->whereDate('date', today())->count();
    @endphp
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-card dark:bg-darkCard rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-1">Penjualan Hari Ini</p>
                    <h3 class="text-xl font-bold text-emerald-600">Rp {{ number_format($myTodaySales, 0, ',', '.') }}</h3>
                </div>
                <div class="w-10 h-10 bg-emerald-100 dark:bg-emerald-900/30 rounded-xl flex items-center justify-center text-emerald-600">
                    <i class='bx bx-calendar-check text-xl'></i>
                </div>
            </div>
        </div>

        <div class="bg-card dark:bg-darkCard rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-1">Transaksi Hari Ini</p>
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white">{{ $myTodayTransactions }}</h3>
                </div>
                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center text-blue-600">
                    <i class='bx bx-receipt text-xl'></i>
                </div>
            </div>
        </div>

        <div class="bg-card dark:bg-darkCard rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-1">Total Penjualan</p>
                    <h3 class="text-xl font-bold text-primary">Rp {{ number_format($myTotalSales, 0, ',', '.') }}</h3>
                </div>
                <div class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900/30 rounded-xl flex items-center justify-center text-primary">
                    <i class='bx bx-line-chart text-xl'></i>
                </div>
            </div>
        </div>

        <div class="bg-card dark:bg-darkCard rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-1">Total Transaksi</p>
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white">{{ $myTotalTransactions }}</h3>
                </div>
                <div class="w-10 h-10 bg-slate-100 dark:bg-slate-700 rounded-xl flex items-center justify-center text-slate-500">
                    <i class='bx bx-history text-xl'></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Low Stock Alert for View Only Mode --}}
    <div class="bg-card dark:bg-darkCard rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">
        <div class="p-5 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
            <h3 class="font-bold text-base text-slate-800 dark:text-white flex items-center gap-2">
                <i class='bx bx-error-circle text-rose-500'></i> Stok Menipis
            </h3>
        </div>
        <div class="divide-y divide-slate-100 dark:divide-slate-700">
            @forelse($this->lowStockProducts as $product)
                <div class="p-4 hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                    <div class="flex justify-between items-center">
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
                </div>
            @empty
                <div class="p-8 text-center text-slate-400">
                    <i class='bx bx-check-circle text-4xl text-emerald-500'></i>
                    <p class="text-sm mt-2">Semua stok aman!</p>
                </div>
            @endforelse
        </div>
    </div>
    @else
    {{-- Quick Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        {{-- 1. Modal Awal --}}
        <div class="bg-card dark:bg-darkCard rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-1">Modal Awal</p>
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white">Rp {{ number_format($this->currentShift->opening_cash, 0, ',', '.') }}</h3>
                </div>
                <div class="w-10 h-10 bg-slate-100 dark:bg-slate-700 rounded-xl flex items-center justify-center text-slate-500">
                    <i class='bx bx-wallet text-xl'></i>
                </div>
            </div>
        </div>

        {{-- 2. Omzet (ganti dari Penjualan Shift) --}}
        <div class="bg-card dark:bg-darkCard rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-1">Omzet</p>
                    <h3 class="text-xl font-bold text-emerald-600">Rp {{ number_format($this->todaySales, 0, ',', '.') }}</h3>
                </div>
                <div class="w-10 h-10 bg-emerald-100 dark:bg-emerald-900/30 rounded-xl flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                    <i class='bx bx-trending-up text-xl'></i>
                </div>
            </div>
        </div>

        {{-- 3. Pembayaran Tunai --}}
        <div class="bg-card dark:bg-darkCard rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-1">Pembayaran Tunai</p>
                    <h3 class="text-xl font-bold text-blue-600">Rp {{ number_format($this->cashSales, 0, ',', '.') }}</h3>
                </div>
                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center text-blue-600 dark:text-blue-400">
                    <i class='bx bx-money-withdraw text-xl'></i>
                </div>
            </div>
        </div>

        {{-- 4. Pembayaran Digital --}}
        <div class="bg-card dark:bg-darkCard rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-1">Pembayaran Digital</p>
                    <h3 class="text-xl font-bold text-purple-600">Rp {{ number_format($this->digitalSales, 0, ',', '.') }}</h3>
                </div>
                <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center text-purple-600 dark:text-purple-400">
                    <i class='bx bx-credit-card text-xl'></i>
                </div>
            </div>
        </div>

        {{-- 5. Total Kas di Laci --}}
        <div class="bg-card dark:bg-darkCard rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-1">Total Kas di Laci</p>
                    <h3 class="text-xl font-bold text-primary">Rp {{ number_format($this->expectedCash, 0, ',', '.') }}</h3>
                </div>
                <div class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900/30 rounded-xl flex items-center justify-center text-primary">
                    <i class='bx bx-box text-xl'></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Produktivitas Row --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

        {{-- Durasi Shift --}}
        <div class="bg-card dark:bg-darkCard rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 p-4 flex items-center gap-4">
            <div class="w-11 h-11 bg-amber-100 dark:bg-amber-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                <i class='bx bx-timer text-xl text-amber-600 dark:text-amber-400'></i>
            </div>
            <div class="min-w-0">
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Durasi Shift</p>
                @php $elapsed = $this->shiftElapsed; @endphp
                <h3 class="text-lg font-bold text-slate-900 dark:text-white leading-tight">
                    {{ $elapsed['hours'] > 0 ? $elapsed['hours'].'j ' : '' }}{{ $elapsed['minutes'] }}m
                </h3>
                <p class="text-[10px] text-slate-400">Sejak {{ $this->currentShift->check_in_at->format('H:i') }}</p>
            </div>
        </div>

        {{-- Jumlah Transaksi --}}
        <div class="bg-card dark:bg-darkCard rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 p-4 flex items-center gap-4">
            <div class="w-11 h-11 bg-teal-100 dark:bg-teal-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                <i class='bx bx-receipt text-xl text-teal-600 dark:text-teal-400'></i>
            </div>
            <div class="min-w-0">
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Total Transaksi</p>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white leading-tight">{{ $this->todayTransactionsCount }} <span class="text-sm font-medium text-slate-400">trx</span></h3>
                <p class="text-[10px] text-slate-400">Di shift ini</p>
            </div>
        </div>

        {{-- Transaksi / Jam --}}
        <div class="bg-card dark:bg-darkCard rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 p-4 flex items-center gap-4">
            <div class="w-11 h-11 bg-rose-100 dark:bg-rose-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                <i class='bx bx-trending-up text-xl text-rose-600 dark:text-rose-400'></i>
            </div>
            <div class="min-w-0">
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Produktivitas</p>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white leading-tight">{{ $this->transactionsPerHour }} <span class="text-sm font-medium text-slate-400">trx/jam</span></h3>
                <p class="text-[10px] text-slate-400">{{ $this->transactionsPerHour >= 5 ? '🔥 Lagi on fire!' : ($this->transactionsPerHour >= 2 ? '✅ Produktif' : '⏳ Baru mulai') }}</p>
            </div>
        </div>

    </div>

    {{-- Main Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        {{-- Recent Transactions --}}
        <div class="bg-card dark:bg-darkCard rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">
            <div class="p-5 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
                <h3 class="font-bold text-base text-slate-800 dark:text-white">Transaksi Shift Ini</h3>
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
                        <p class="text-sm mt-2">Belum ada transaksi di shift ini</p>
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
            </div>
            <div class="divide-y divide-slate-100 dark:divide-slate-700">
                @forelse($this->lowStockProducts as $product)
                    <div class="p-4 hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                        <div class="flex justify-between items-center">
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
    @endif
    @endif
</div>
