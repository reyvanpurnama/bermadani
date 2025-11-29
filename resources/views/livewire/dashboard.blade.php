<div class="space-y-6">
    {{-- Welcome Banner --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 mb-6">
        <div class="lg:col-span-8 h-full">
            <div class="bg-indigo-600 rounded-xl p-5 text-white shadow-sm relative overflow-hidden flex flex-col justify-center h-full">
                <div class="relative z-10 flex justify-between items-center">
                    <div>
                        <h2 class="text-lg font-bold mb-1">Selamat datang kembali, {{ auth()->user()->name }}! 👋</h2>
                        <p class="text-indigo-100 text-xs mb-3 opacity-90">
                            @if($this->profitGrowth > 0)
                                Performa penjualan naik <span class="font-bold text-white">{{ $this->profitGrowth }}%</span> {{ strtolower($this->previousPeriodLabel) }}.
                            @elseif($this->profitGrowth < 0)
                                Performa penjualan turun <span class="font-bold text-white">{{ abs($this->profitGrowth) }}%</span> {{ strtolower($this->previousPeriodLabel) }}.
                            @else
                                Performa penjualan stabil {{ strtolower($this->previousPeriodLabel) }}.
                            @endif
                        </p>
                        <a href="{{ route('admin.pos') }}" class="bg-card/20 hover:bg-card/30 text-white text-[10px] uppercase tracking-wider px-3 py-1.5 rounded-md font-semibold transition-colors border border-white/10 inline-block">
                            Buka POS
                        </a>
                    </div>
                    <i class='bx bx-trophy text-6xl text-indigo-400 opacity-40 mr-4'></i>
                </div>
                <div class="absolute right-0 top-0 h-full w-1/3 opacity-10 bg-gradient-to-l from-white to-transparent transform skew-x-12 pointer-events-none"></div>
            </div>
        </div>

        <div class="lg:col-span-4 h-full flex flex-col gap-4">
            <div class="flex-1 bg-card dark:bg-darkCard px-5 py-3 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Total Keuntungan</p>
                    <div class="flex items-end gap-2">
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white leading-none">Rp {{ number_format($this->netProfit, 0, ',', '.') }}</h3>
                        @if($this->profitGrowth > 0)
                            <span class="text-emerald-500 text-[10px] font-bold flex items-center mb-0.5"><i class='bx bx-up-arrow-alt'></i> {{ $this->profitGrowth }}%</span>
                        @elseif($this->profitGrowth < 0)
                            <span class="text-rose-500 text-[10px] font-bold flex items-center mb-0.5"><i class='bx bx-down-arrow-alt'></i> {{ abs($this->profitGrowth) }}%</span>
                        @endif
                    </div>
                </div>
                <div class="bg-emerald-50 dark:bg-emerald-500/10 p-2 rounded-lg text-emerald-500"><i class='bx bx-line-chart text-lg'></i></div>
            </div>

            <div class="flex-1 bg-card dark:bg-darkCard px-5 py-3 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Total Penjualan</p>
                    <div class="flex items-end gap-2">
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white leading-none">Rp {{ number_format($this->totalSales, 0, ',', '.') }}</h3>
                    </div>
                </div>
                <div class="bg-blue-50 dark:bg-blue-500/10 p-2 rounded-lg text-blue-500"><i class='bx bx-wallet text-lg'></i></div>
            </div>
        </div>
    </div>

    {{-- Main Content Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
        <div class="lg:col-span-8 flex flex-col gap-4 h-fit">
            {{-- Revenue Chart Section with Profitability Sidebar --}}
            <div class="bg-card dark:bg-darkCard rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 flex flex-col md:flex-row overflow-hidden">
                {{-- Chart Area --}}
                <div class="flex-1 p-5 border-b md:border-b-0 md:border-r border-slate-100 dark:border-slate-700 flex flex-col justify-between">
                    <div>
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-2">
                            <div>
                                <h3 class="font-bold text-base text-slate-800 dark:text-white">Ringkasan Pendapatan</h3>
                                <p class="text-[11px] text-slate-500">Analisis Pemasukan vs Pengeluaran</p>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="flex gap-3 hidden sm:flex">
                                    <div class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-indigo-500"></span><span class="text-[10px] text-slate-500 font-medium">Pemasukan</span></div>
                                    <div class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-slate-300"></span><span class="text-[10px] text-slate-500 font-medium">Pengeluaran</span></div>
                                </div>
                                <div class="h-4 w-px bg-slate-200 dark:bg-slate-600 hidden sm:block"></div>
                                <div class="relative">
                                    <select wire:model.live="filter" class="appearance-none bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 text-slate-700 dark:text-slate-200 text-[11px] font-semibold rounded-md pl-3 pr-8 py-1.5 outline-none focus:ring-1 focus:ring-primary focus:border-primary cursor-pointer transition-all hover:bg-slate-100 dark:hover:bg-slate-700">
                                        <option value="today">Hari Ini</option>
                                        <option value="week">Minggu Ini</option>
                                        <option value="month">Bulan Ini</option>
                                        <option value="year">Tahun Ini</option>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-500"><i class='bx bx-chevron-down text-sm'></i></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex-1 w-full min-h-[260px]">
                        <div class="text-center py-12 text-slate-400">
                            <i class='bx bx-line-chart text-5xl opacity-30'></i>
                            <p class="text-xs mt-2">Chart akan ditampilkan di sini</p>
                            <div class="mt-3 text-xs">
                                <span class="font-bold text-slate-700 dark:text-white">Pemasukan:</span> 
                                <span class="text-emerald-600 dark:text-emerald-400 font-bold">Rp {{ number_format($this->totalSales, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Profitability Sidebar --}}
                <div class="w-full md:w-[260px] p-5 flex flex-col justify-between bg-slate-50/50 dark:bg-slate-800/30 border-l border-slate-100 dark:border-slate-700">
                    <div class="mb-2">
                        <h3 class="font-bold text-[14px] text-slate-800 dark:text-white">Profitabilitas</h3>
                        <p class="text-[10px] text-slate-500 uppercase tracking-widest">Analisis Laba & Pengeluaran</p>
                    </div>

                    <div class="bg-emerald-50/80 dark:bg-emerald-500/10 rounded-xl p-4 border border-emerald-100 dark:border-emerald-500/20 relative overflow-hidden group">
                        <div class="absolute -right-3 -bottom-3 text-emerald-500/10 dark:text-emerald-400/10 text-6xl group-hover:scale-110 transition-transform duration-500 pointer-events-none">
                            <i class='bx bx-wallet'></i>
                        </div>
                        <p class="text-[9px] font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-widest mb-1">Laba Bersih Saat Ini</p>
                        
                        <h2 class="text-2xl font-bold text-slate-800 dark:text-white tracking-tight relative z-10">
                            Rp {{ number_format($this->netProfit, 0, ',', '.') }}
                        </h2>
                        
                        <div class="flex items-center gap-2 mt-2 relative z-10">
                            @if($this->profitGrowth > 0)
                                <div class="flex items-center justify-center bg-emerald-500 text-white rounded-full w-4 h-4 shadow-sm shadow-emerald-500/30">
                                    <i class='bx bx-trending-up text-[10px]'></i>
                                </div>
                                <span class="text-[11px] font-bold text-emerald-700 dark:text-emerald-400">+{{ $this->profitGrowth }}%</span>
                            @elseif($this->profitGrowth < 0)
                                <div class="flex items-center justify-center bg-rose-500 text-white rounded-full w-4 h-4 shadow-sm shadow-rose-500/30">
                                    <i class='bx bx-trending-down text-[10px]'></i>
                                </div>
                                <span class="text-[11px] font-bold text-rose-700 dark:text-rose-400">{{ $this->profitGrowth }}%</span>
                            @else
                                <div class="flex items-center justify-center bg-slate-400 text-white rounded-full w-4 h-4 shadow-sm">
                                    <i class='bx bx-minus text-[10px]'></i>
                                </div>
                                <span class="text-[11px] font-bold text-slate-500">0%</span>
                            @endif
                            <span class="text-[10px] text-slate-400">{{ $this->previousPeriodLabel }}</span>
                        </div>
                    </div>

                    <div class="space-y-3 mt-4">
                        <div>
                            <div class="flex justify-between items-end mb-1">
                                <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Margin Kotor</span>
                                <span class="text-[11px] font-bold text-slate-700 dark:text-white">{{ $this->grossMarginPercent }}%</span>
                            </div>
                            <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-1">
                                <div class="bg-indigo-500 h-1 rounded-full transition-all duration-500" style="width: {{ $this->grossMarginPercent }}%"></div>
                            </div>
                            <p class="text-[9px] text-slate-400 mt-0.5">Rp {{ number_format($this->grossProfit, 0, ',', '.') }}</p>
                        </div>

                        <div>
                            <div class="flex justify-between items-end mb-1">
                                <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Total Pengeluaran</span>
                                <span class="text-[11px] font-bold text-slate-700 dark:text-white">{{ $this->operatingMarginPercent }}%</span>
                            </div>
                            <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-1">
                                <div class="bg-rose-500 h-1 rounded-full transition-all duration-500" style="width: {{ $this->operatingMarginPercent }}%"></div>
                            </div>
                            <p class="text-[9px] text-slate-400 mt-0.5">Rp {{ number_format($this->operatingExpenses, 0, ',', '.') }}</p>
                        </div>

                        <p class="text-[9px] text-slate-400 mt-2 italic">*Laba Bersih = Penjualan - Pengeluaran.</p>
                    </div>
                </div>
            </div>

            {{-- Quick Stats Grid (4 small cards) --}}
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-card dark:bg-darkCard p-3 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 flex flex-col justify-between group hover:border-indigo-200 transition-colors cursor-pointer relative overflow-hidden h-[100px]">
                    <span class="absolute top-2.5 right-2.5 text-[9px] bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-300 px-1.5 py-px rounded font-bold uppercase tracking-wide">Shift 1</span>
                    <div class="mb-1">
                        <div class="w-8 h-8 rounded-md bg-indigo-50 dark:bg-indigo-500/10 flex items-center justify-center text-primary transition-all duration-300 group-hover:bg-indigo-500 group-hover:text-white group-hover:shadow-lg group-hover:shadow-indigo-500/40">
                            <i class='bx bx-store-alt text-base'></i>
                        </div>
                    </div>
                    <div>
                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-0.5 leading-none">Saldo Kasir</p>
                        <h6 class="text-[13px] font-bold text-slate-800 dark:text-white leading-tight truncate">Rp {{ number_format($this->totalSales, 0, ',', '.') }}</h6>
                    </div>
                </div>
                <div class="bg-card dark:bg-darkCard p-3 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 flex flex-col justify-between group hover:border-amber-200 transition-colors cursor-pointer relative overflow-hidden h-[100px]">
                    <span class="absolute top-2.5 right-2.5 text-[9px] bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400 px-1.5 py-px rounded font-bold uppercase tracking-wide">Urgent</span>
                    <div class="mb-1">
                        <div class="w-8 h-8 rounded-md bg-amber-50 dark:bg-amber-500/10 flex items-center justify-center text-amber-500 transition-all duration-300 group-hover:bg-amber-500 group-hover:text-white group-hover:shadow-lg group-hover:shadow-amber-500/40">
                            <i class='bx bx-file-blank text-base'></i>
                        </div>
                    </div>
                    <div>
                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-0.5 leading-none">Pengajuan</p>
                        <h6 class="text-[13px] font-bold text-slate-800 dark:text-white leading-tight truncate">0 Berkas</h6>
                    </div>
                </div>
                <div class="bg-card dark:bg-darkCard p-3 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 flex flex-col justify-between group hover:border-rose-200 transition-colors cursor-pointer relative overflow-hidden h-[100px]">
                    <span class="absolute top-2.5 right-2.5 text-[9px] bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-400 px-1.5 py-px rounded font-bold uppercase tracking-wide">Hari Ini</span>
                    <div class="mb-1">
                        <div class="w-8 h-8 rounded-md bg-rose-50 dark:bg-rose-500/10 flex items-center justify-center text-rose-500 transition-all duration-300 group-hover:bg-rose-500 group-hover:text-white group-hover:shadow-lg group-hover:shadow-rose-500/40">
                            <i class='bx bx-time-five text-base'></i>
                        </div>
                    </div>
                    <div>
                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-0.5 leading-none">Penagihan</p>
                        <h6 class="text-[13px] font-bold text-slate-800 dark:text-white leading-tight truncate">Rp 0</h6>
                    </div>
                </div>
                <div class="bg-card dark:bg-darkCard p-3 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 flex flex-col justify-between group hover:border-cyan-200 transition-colors cursor-pointer relative overflow-hidden h-[100px]">
                    <span class="absolute top-2.5 right-2.5 text-[9px] bg-cyan-50 text-cyan-600 dark:bg-cyan-500/10 dark:text-cyan-400 px-1.5 py-px rounded font-bold uppercase tracking-wide">Active</span>
                    <div class="mb-1">
                        <div class="w-8 h-8 rounded-md bg-cyan-50 dark:bg-cyan-500/10 flex items-center justify-center text-cyan-500 transition-all duration-300 group-hover:bg-cyan-500 group-hover:text-white group-hover:shadow-lg group-hover:shadow-cyan-500/40">
                            <i class='bx bx-receipt text-base'></i>
                        </div>
                    </div>
                    <div>
                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-0.5 leading-none">Gantung</p>
                        <h6 class="text-[13px] font-bold text-slate-800 dark:text-white leading-tight truncate">0 Nota</h6>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-4 flex flex-col gap-4 h-fit">
            {{-- Stats Cards (Product, Member, etc) --}}
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-card dark:bg-darkCard p-3 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700">
                    <div class="w-8 h-8 rounded-md bg-indigo-50 dark:bg-indigo-500/10 flex items-center justify-center text-primary mb-2">
                        <i class='bx bx-package text-base'></i>
                    </div>
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Produk Aktif</p>
                    <h6 class="text-xl font-bold text-slate-800 dark:text-white">{{ $this->totalProducts }}</h6>
                </div>
                <div class="bg-card dark:bg-darkCard p-3 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700">
                    <div class="w-8 h-8 rounded-md bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center text-emerald-500 mb-2">
                        <i class='bx bx-user text-base'></i>
                    </div>
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Member Aktif</p>
                    <h6 class="text-xl font-bold text-slate-800 dark:text-white">{{ $this->totalMembers }}</h6>
                </div>
                <div class="bg-card dark:bg-darkCard p-3 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700">
                    <div class="w-8 h-8 rounded-md bg-amber-50 dark:bg-amber-500/10 flex items-center justify-center text-amber-500 mb-2">
                        <i class='bx bx-error-circle text-base'></i>
                    </div>
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Stok Kritis</p>
                    <h6 class="text-xl font-bold text-slate-800 dark:text-white">{{ $this->lowStockProducts->count() }}</h6>
                </div>
                <div class="bg-card dark:bg-darkCard p-3 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700">
                    <div class="w-8 h-8 rounded-md bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center text-blue-500 mb-2">
                        <i class='bx bx-category text-base'></i>
                    </div>
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Kategori</p>
                    <h6 class="text-xl font-bold text-slate-800 dark:text-white">{{ \App\Models\Category::where('isActive', true)->count() }}</h6>
                </div>
            </div>

            {{-- Tabs: Stok Kritis / Produk Terlaris --}}
            <div class="bg-card dark:bg-darkCard rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 p-5 flex flex-col h-full min-h-[280px]" x-data="{ activeTab: 'stok' }">
                {{-- Tabs Header --}}
                <div class="flex items-center gap-2 md:gap-4 border-b border-slate-100 dark:border-slate-700 pb-3 mb-3 overflow-x-auto">
                    <button @click="activeTab = 'stok'" :class="activeTab === 'stok' ? 'text-slate-800 dark:text-white border-rose-500' : 'text-slate-400 dark:text-slate-500 border-transparent hover:text-slate-600 dark:hover:text-slate-300'" class="text-[11px] md:text-[13px] font-bold border-b-2 pb-3 -mb-3.5 transition-colors whitespace-nowrap shrink-0">
                        <i class='bx bx-error-circle text-rose-500 mr-1'></i> Stok Kritis
                    </button>
                    <button @click="activeTab = 'laris'" :class="activeTab === 'laris' ? 'text-slate-800 dark:text-white border-amber-500' : 'text-slate-400 dark:text-slate-500 border-transparent hover:text-slate-600 dark:hover:text-slate-300'" class="text-[11px] md:text-[13px] font-bold border-b-2 pb-3 -mb-3.5 transition-colors whitespace-nowrap shrink-0">
                        <i class='bx bx-trophy text-amber-500 mr-1'></i> Produk Terlaris
                    </button>
                    <div class="flex-1 text-right">
                        <a href="{{ route('admin.products') }}" class="text-[9px] md:text-[10px] text-primary font-bold hover:underline whitespace-nowrap">Lihat Semua</a>
                    </div>
                </div>

                {{-- Content: Stok Menipis --}}
                <div x-show="activeTab === 'stok'" class="flex-1 space-y-4 overflow-y-auto pr-1">
                    @forelse($this->lowStockProducts as $product)
                        <div>
                            <div class="flex justify-between items-end mb-1">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded bg-slate-100 dark:bg-slate-700 flex items-center justify-center text-[10px]">
                                        {{ $product->category?->icon ?? '📦' }}
                                    </div>
                                    <div>
                                        <h6 class="text-[12px] font-semibold text-slate-800 dark:text-white leading-none">{{ $product->name }}</h6>
                                        <p class="text-[9px] text-slate-400">{{ $product->category?->name ?? '-' }}</p>
                                    </div>
                                </div>
                                <span class="text-[11px] font-bold {{ $product->stock <= 5 ? 'text-rose-500' : 'text-amber-500' }}">
                                    Sisa {{ $product->stock }}
                                </span>
                            </div>
                            <div class="w-full bg-slate-100 dark:bg-slate-700 rounded-full h-1">
                                <div class="{{ $product->stock <= 5 ? 'bg-rose-500' : 'bg-amber-500' }} h-1 rounded-full" style="width: {{ min(100, ($product->stock / max($product->threshold, 1)) * 50) }}%"></div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-slate-400">
                            <i class='bx bx-check-circle text-4xl text-emerald-500'></i>
                            <p class="text-sm mt-2">Semua stok aman!</p>
                        </div>
                    @endforelse
                </div>

                {{-- Content: Produk Terlaris --}}
                <div x-show="activeTab === 'laris'" class="flex-1 overflow-y-auto pr-1 space-y-3" x-cloak>
                    @forelse($this->topProducts as $index => $product)
                        <div class="flex items-center gap-3 group cursor-pointer">
                            <div class="w-8 h-8 rounded-lg {{ $index === 0 ? 'bg-amber-50 dark:bg-amber-500/10 text-amber-500 ring-1 ring-amber-100 dark:ring-amber-500/20' : 'bg-slate-50 dark:bg-slate-700 text-slate-500 ring-1 ring-slate-200 dark:ring-slate-600' }} flex items-center justify-center font-bold text-[12px]">
                                #{{ $index + 1 }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <h6 class="text-[12px] font-semibold text-slate-800 dark:text-white leading-none truncate">{{ $product->name }}</h6>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-[9px] text-slate-400">{{ $product->category?->name ?? '-' }}</span>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="block text-[13px] font-bold text-slate-800 dark:text-white">{{ $product->total_sold }}</span>
                                <span class="text-[9px] text-slate-400">Terjual</span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-slate-400">
                            <i class='bx bx-chart text-4xl'></i>
                            <p class="text-sm mt-2">Belum ada data penjualan</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Transactions --}}
    <div class="bg-card dark:bg-darkCard rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">
        <div class="p-5 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
            <h3 class="font-bold text-base text-slate-800 dark:text-white">Transaksi Terkini</h3>
            <a href="{{ route('admin.transactions') }}" class="text-sm text-primary font-medium hover:underline">Lihat Semua</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600 dark:text-slate-400">
                <thead class="bg-slate-50 dark:bg-slate-700/50 text-xs uppercase font-semibold text-slate-500 dark:text-slate-400">
                    <tr>
                        <th class="px-6 py-3">Invoice</th>
                        <th class="px-6 py-3">Member</th>
                        <th class="px-6 py-3">Tanggal</th>
                        <th class="px-6 py-3">Jumlah</th>
                        <th class="px-6 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse($this->recentTransactions as $trx)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                            <td class="px-6 py-3 font-mono text-xs text-slate-500">{{ $trx->invoiceNumber }}</td>
                            <td class="px-6 py-3">
                                @if($trx->member)
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center text-primary dark:text-indigo-400 font-bold text-[10px]">
                                            {{ strtoupper(substr($trx->member->name, 0, 2)) }}
                                        </div>
                                        <span class="font-medium text-slate-900 dark:text-white text-xs">{{ $trx->member->name }}</span>
                                    </div>
                                @else
                                    <span class="text-slate-400 text-xs">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-3 text-xs">{{ $trx->date?->format('d M Y, H:i') }}</td>
                            <td class="px-6 py-3 font-bold text-slate-900 dark:text-white text-xs">Rp {{ number_format($trx->totalAmount, 0, ',', '.') }}</td>
                            <td class="px-6 py-3">
                                <span class="{{ $trx->status === 'COMPLETED' ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400' : 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400' }} px-2 py-1 rounded-full text-[10px] font-semibold">
                                    {{ $trx->status === 'COMPLETED' ? 'Lunas' : $trx->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-slate-400">
                                <i class='bx bx-receipt text-4xl'></i>
                                <p class="mt-2">Belum ada transaksi</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
