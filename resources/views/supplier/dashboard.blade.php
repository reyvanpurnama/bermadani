@extends('layouts.supplier')

@section('title', 'Dashboard')
@section('header-title', 'Dasbor Mitra')
@section('header-subtitle', 'Ringkasan performa penjualan produk Anda')

@section('content')
    <div class="max-w-md mx-auto lg:max-w-none">

        <!-- Actionables / Penting Hari Ini -->
        <div class="mb-6">
            <h3 class="text-sm font-bold text-slate-800 dark:text-white mb-3 flex items-center gap-2">
                <i class='bx bx-task text-primary'></i> Penting Hari Ini
            </h3>
            <div class="grid grid-cols-3 gap-3">
                <!-- Requested Batches -->
                <a href="{{ route('supplier.restock') }}"
                    class="bg-white dark:bg-darkCard p-3 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm flex flex-col items-center text-center relative group overflow-hidden hover:border-blue-300 transition-all">
                    @if($requestedBatchesCount > 0)
                        <span
                            class="absolute top-2 right-2 w-2 h-2 bg-rose-500 rounded-full animate-pulse shadow-rose-500/50 shadow-md"></span>
                    @endif
                    <div
                        class="w-10 h-10 rounded-full bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                        <i class='bx bx-archive-in text-xl'></i>
                    </div>
                    <span
                        class="text-lg font-bold text-slate-800 dark:text-white leading-none mb-1">{{ $requestedBatchesCount }}</span>
                    <span class="text-[10px] text-slate-500 font-medium">Perlu Dikirim</span>
                </a>

                <!-- Pending Settlement -->
                <div
                    class="bg-white dark:bg-darkCard p-3 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm flex flex-col items-center text-center group hover:border-amber-300 transition-all">
                    @if($pendingSettlementCount > 0)
                        <span
                            class="absolute top-2 right-2 w-2 h-2 bg-rose-500 rounded-full animate-pulse shadow-rose-500/50 shadow-md"></span>
                    @endif
                    <div
                        class="w-10 h-10 rounded-full bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                        <i class='bx bx-money-withdraw text-xl'></i>
                    </div>
                    <span
                        class="text-lg font-bold text-slate-800 dark:text-white leading-none mb-1">{{ $pendingSettlementCount }}</span>
                    <span class="text-[10px] text-slate-500 font-medium">Siap Cair</span>
                </div>

                <!-- Low Stock -->
                <a href="{{ route('supplier.products.index') }}"
                    class="bg-white dark:bg-darkCard p-3 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm flex flex-col items-center text-center group hover:border-rose-300 transition-all">
                    @if($lowStock > 0)
                        <span
                            class="absolute top-2 right-2 w-2 h-2 bg-rose-500 rounded-full animate-pulse shadow-rose-500/50 shadow-md"></span>
                    @endif
                    <div
                        class="w-10 h-10 rounded-full bg-rose-50 dark:bg-rose-900/20 text-rose-600 dark:text-rose-400 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                        <i class='bx bx-error-circle text-xl'></i>
                    </div>
                    <span class="text-lg font-bold text-slate-800 dark:text-white leading-none mb-1">{{ $lowStock }}</span>
                    <span class="text-[10px] text-slate-500 font-medium">Stok Menipis</span>
                </a>
            </div>
        </div>

        <!-- Business Stats Grid -->
        <div class="mb-6">
            <h3 class="text-sm font-bold text-slate-800 dark:text-white mb-3 flex items-center gap-2">
                <i class='bx bx-stats text-emerald-500'></i> Bisnis Saya
            </h3>
            <div class="grid grid-cols-2 gap-3">
                <!-- Pendapatan (Highlighted) -->
                <a href="{{ route('supplier.sales') }}"
                    class="col-span-2 bg-gradient-to-r from-emerald-500 to-teal-600 p-5 rounded-2xl shadow-lg shadow-emerald-500/20 text-white relative overflow-hidden transform transition-all hover:scale-[1.01] cursor-pointer group">
                    <i class='bx bx-wallet absolute -bottom-4 -right-4 text-[80px] text-white/10 transform rotate-12 group-hover:rotate-6 transition-transform'></i>
                    <div class="relative z-10">
                        <p class="text-[11px] font-medium text-emerald-100 uppercase tracking-wider mb-1 flex items-center justify-between">
                            Pendapatan Bulan Ini
                            <i class='bx bx-chevron-right text-lg opacity-70'></i>
                        </p>
                        <h3 class="text-2xl font-bold mb-2 tracking-tight">Rp
                            {{ number_format($totalPendapatan ?? 0, 0, ',', '.') }}</h3>
                        <span
                            class="inline-flex items-center gap-1 text-[11px] bg-white/20 px-2 py-1 rounded-lg backdrop-blur-sm border border-white/10">
                            <i class='bx bx-trending-up'></i> {{ $pendapatanGrowth ?? 0 }}% vs bulan lalu
                        </span>
                    </div>
                </a>

                <!-- Saldo Tertahan -->
                <a href="{{ route('supplier.restock') }}"
                    class="bg-white dark:bg-darkCard p-4 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm block hover:border-amber-300 transition-colors">
                    <div class="flex items-center gap-2 mb-2">
                        <div
                            class="w-7 h-7 rounded-lg bg-amber-50 dark:bg-amber-900/20 text-amber-600 flex items-center justify-center">
                            <i class='bx bx-lock-open text-sm'></i>
                        </div>
                        <span class="text-[11px] font-medium text-slate-500">Saldo Tertahan</span>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white truncate">Rp
                        {{ number_format($saldoTertahan ?? 0, 0, ',', '.') }}</h3>
                </a>

                <!-- Unit Terjual -->
                <a href="{{ route('supplier.sales') }}"
                    class="bg-white dark:bg-darkCard p-4 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm block hover:border-blue-300 transition-colors">
                    <div class="flex items-center gap-2 mb-2">
                        <div
                            class="w-7 h-7 rounded-lg bg-blue-50 dark:bg-blue-900/20 text-blue-600 flex items-center justify-center">
                            <i class='bx bx-package text-sm'></i>
                        </div>
                        <span class="text-[11px] font-medium text-slate-500">Unit Terjual</span>
                    </div>
                    <div class="flex items-baseline gap-1">
                        <h3 class="text-lg font-bold text-slate-800 dark:text-white">{{ $unitTerjual ?? 0 }}</h3>
                        <span class="text-xs text-slate-400">Pcs</span>
                    </div>
                </a>

                <!-- Produk Aktif -->
                <a href="{{ route('supplier.products.index') }}"
                    class="bg-white dark:bg-darkCard p-4 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm block hover:border-indigo-300 transition-colors">
                    <div class="flex items-center gap-2 mb-2">
                        <div
                            class="w-7 h-7 rounded-lg bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 flex items-center justify-center">
                            <i class='bx bx-store text-sm'></i>
                        </div>
                        <span class="text-[11px] font-medium text-slate-500">Produk</span>
                    </div>
                    <div class="flex items-baseline gap-1">
                        <h3 class="text-lg font-bold text-slate-800 dark:text-white">{{ $produkAktif ?? 0 }}</h3>
                        <span class="text-xs text-slate-400">SKU</span>
                    </div>
                </a>
            </div>
        </div>

        <!-- Riwayat Pembayaran Section -->
        @if($recentSettled->count() > 0)
            <div class="mb-6">
                <h3 class="text-sm font-bold text-slate-800 dark:text-white mb-3 flex items-center gap-2">
                    <i class='bx bx-check-circle text-emerald-500'></i> Pembayaran Terakhir
                </h3>
                <div class="space-y-2">
                    @foreach($recentSettled as $batch)
                        <a href="{{ route('supplier.restock') }}" 
                            class="block bg-white dark:bg-darkCard p-4 rounded-xl border border-emerald-100 dark:border-emerald-900/30 shadow-sm hover:shadow-md hover:border-emerald-300 dark:hover:border-emerald-700 transition-all cursor-pointer group">
                            <div class="flex items-start justify-between gap-3 mb-2">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-xs font-bold text-slate-700 dark:text-slate-300 group-hover:text-emerald-600 transition-colors">#{{ $batch->batchCode }}</span>
                                        <span class="bg-emerald-50 text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 px-2 py-0.5 rounded text-[9px] font-bold">
                                            ✓ LUNAS
                                        </span>
                                    </div>
                                    <p class="text-[10px] text-slate-500 flex items-center gap-1.5">
                                        <i class='bx bx-calendar'></i>
                                        Dibayar: <span class="font-semibold text-emerald-600">{{ $batch->settledAt?->format('d M Y H:i') ?? '-' }}</span>
                                    </p>
                                    @if($batch->items->count() > 0)
                                        <div class="flex flex-wrap gap-1.5 mt-2">
                                            @foreach($batch->items->take(2) as $item)
                                                <span class="text-[9px] bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 px-1.5 py-0.5 rounded">
                                                    {{ $item->product->name ?? '-' }}
                                                </span>
                                            @endforeach
                                            @if($batch->items->count() > 2)
                                                <span class="text-[9px] text-slate-400">+{{ $batch->items->count() - 2 }} lainnya</span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                                <div class="text-right flex flex-col items-end gap-1">
                                    <p class="text-[10px] text-slate-500">Diterima</p>
                                    <p class="text-sm font-bold text-emerald-600 dark:text-emerald-400 group-hover:scale-105 transition-transform">
                                        Rp {{ number_format($batch->payableAmount ?? 0, 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
                <a href="{{ route('supplier.restock') }}" 
                    class="mt-3 w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-slate-50 dark:bg-slate-800 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 border border-slate-200 dark:border-slate-700 hover:border-emerald-300 dark:hover:border-emerald-700 rounded-lg text-xs font-semibold text-slate-600 dark:text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 transition-all group">
                    <i class='bx bx-history text-base'></i>
                    Lihat Riwayat Lengkap
                    <i class='bx bx-chevron-right text-sm group-hover:translate-x-1 transition-transform'></i>
                </a>
            </div>
        @endif

        <!-- Chart Section (Simplified) -->
        <div
            class="bg-white dark:bg-darkCard p-5 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm mb-24 lg:mb-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-bold text-sm text-slate-800 dark:text-white flex items-center gap-2">
                    <i class='bx bx-line-chart text-slate-400'></i> Trend Penjualan
                </h3>
                <a href="{{ route('supplier.sales') }}" class="text-[10px] text-primary hover:underline flex items-center gap-1">
                    Lihat Detail <i class='bx bx-chevron-right'></i>
                </a>
            </div>
            <div class="h-[180px] w-full" id="salesChart"></div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const getChartColors = (theme) => ({
            text: theme === 'dark' ? '#94a3b8' : '#64748b',
            grid: theme === 'dark' ? '#334155' : '#f1f5f9',
            tooltipTheme: theme === 'dark' ? 'dark' : 'light'
        });

        // Check current theme
        const isDark = document.documentElement.classList.contains('dark');

        var optionsSales = {
            series: [{ name: 'Penjualan (Rp)', data: [0, 0, 0, 0, 0, 0, 0] }],
            chart: {
                height: '100%',
                type: 'area',
                toolbar: { show: false },
                fontFamily: 'Inter',
                foreColor: isDark ? '#94a3b8' : '#64748b'
            },
            colors: ['#10b981'], // Emerald theme for Money
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 2 },
            fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.05, stops: [0, 90, 100] } },
            xaxis: {
                categories: ["Sen", "Sel", "Rab", "Kam", "Jum", "Sab", "Min"],
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: {
                labels: { formatter: (val) => { return val >= 1000 ? (val / 1000) + 'k' : val } }
            },
            grid: { borderColor: isDark ? '#334155' : '#f1f5f9', strokeDashArray: 4 },
            tooltip: { theme: isDark ? 'dark' : 'light' }
        };

        var chartSales = new ApexCharts(document.querySelector("#salesChart"), optionsSales);
        chartSales.render();

        function updateCharts(theme) {
            const c = getChartColors(theme);
            chartSales.updateOptions({
                chart: { foreColor: c.text },
                grid: { borderColor: c.grid },
                tooltip: { theme: c.tooltipTheme }
            });
        }
    </script>
@endpush