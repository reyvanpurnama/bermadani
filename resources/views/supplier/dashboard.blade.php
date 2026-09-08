@extends('layouts.supplier')

@section('title', 'Dashboard')
@section('header-title', 'Dasbor Mitra')
@section('header-subtitle', 'Ringkasan performa penjualan produk Anda')

@section('content')
    <div class="max-w-6xl mx-auto space-y-8">

        <!-- Actionables / Penting Hari Ini -->
        <div>
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-extrabold text-zinc-900 dark:text-white flex items-center gap-2 tracking-tight">
                    <div class="w-2 h-2 rounded-full bg-[#155A6B] animate-ping"></div>
                    Penting Hari Ini
                </h3>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <!-- Requested Batches -->
                <a href="{{ route('supplier.restock') }}"
                    class="group relative overflow-hidden rounded-2xl bg-white/80 dark:bg-zinc-900/80 backdrop-blur-xl p-5 border border-zinc-200/80 dark:border-zinc-800/80 shadow-md hover:shadow-2xl hover:border-[#155A6B]/50 transition-all duration-300 flex items-center gap-4">
                    @if($requestedBatchesCount > 0)
                        <span
                            class="absolute top-3 right-3 w-2.5 h-2.5 bg-rose-500 rounded-full animate-pulse ring-4 ring-rose-500/20"></span>
                    @endif
                    <div
                        class="w-12 h-12 rounded-xl bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 flex items-center justify-center group-hover:scale-110 transition-transform duration-300 shrink-0">
                        <i class='bx bx-archive-in text-2xl'></i>
                    </div>
                    <div>
                        <span class="text-2xl font-black text-zinc-900 dark:text-white leading-none block mb-1">{{ $requestedBatchesCount }}</span>
                        <span class="text-xs font-semibold text-zinc-500 dark:text-zinc-400">Batch Perlu Dikirim</span>
                    </div>
                </a>

                <!-- Pending Settlement -->
                <a href="{{ route('supplier.restock') }}"
                    class="group relative overflow-hidden rounded-2xl bg-white/80 dark:bg-zinc-900/80 backdrop-blur-xl p-5 border border-zinc-200/80 dark:border-zinc-800/80 shadow-md hover:shadow-2xl hover:border-amber-500/50 transition-all duration-300 flex items-center gap-4">
                    @if($pendingSettlementCount > 0)
                        <span
                            class="absolute top-3 right-3 w-2.5 h-2.5 bg-amber-500 rounded-full animate-pulse ring-4 ring-amber-500/20"></span>
                    @endif
                    <div
                        class="w-12 h-12 rounded-xl bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 flex items-center justify-center group-hover:scale-110 transition-transform duration-300 shrink-0">
                        <i class='bx bx-money-withdraw text-2xl'></i>
                    </div>
                    <div>
                        <span class="text-2xl font-black text-zinc-900 dark:text-white leading-none block mb-1">{{ $pendingSettlementCount }}</span>
                        <span class="text-xs font-semibold text-zinc-500 dark:text-zinc-400">Pembayaran Siap Cair</span>
                    </div>
                </a>

                <!-- Low Stock -->
                <a href="{{ route('supplier.products.index') }}"
                    class="group relative overflow-hidden rounded-2xl bg-white/80 dark:bg-zinc-900/80 backdrop-blur-xl p-5 border border-zinc-200/80 dark:border-zinc-800/80 shadow-md hover:shadow-2xl hover:border-rose-500/50 transition-all duration-300 flex items-center gap-4">
                    @if($lowStock > 0)
                        <span
                            class="absolute top-3 right-3 w-2.5 h-2.5 bg-rose-500 rounded-full animate-pulse ring-4 ring-rose-500/20"></span>
                    @endif
                    <div
                        class="w-12 h-12 rounded-xl bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 flex items-center justify-center group-hover:scale-110 transition-transform duration-300 shrink-0">
                        <i class='bx bx-error-circle text-2xl'></i>
                    </div>
                    <div>
                        <span class="text-2xl font-black text-zinc-900 dark:text-white leading-none block mb-1">{{ $lowStock }}</span>
                        <span class="text-xs font-semibold text-zinc-500 dark:text-zinc-400">SKU Stok Menipis</span>
                    </div>
                </a>
            </div>
        </div>

        <!-- Business Stats Bento Grid -->
        <div>
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-extrabold text-zinc-900 dark:text-white flex items-center gap-2 tracking-tight">
                    <i class='bx bx-pie-chart-alt-2 text-[#155A6B] dark:text-teal-400 text-lg'></i>
                    Bisnis Saya
                </h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Pendapatan Highlight Widget (2 Cols on Desktop) -->
                <a href="{{ route('supplier.sales') }}"
                    class="md:col-span-2 relative overflow-hidden rounded-3xl bg-gradient-to-br from-[#155A6B] via-[#166072] to-[#1a6b80] p-6 sm:p-8 text-white shadow-xl shadow-[#155A6B]/20 hover:shadow-2xl hover:scale-[1.005] transition-all duration-300 group flex flex-col justify-between min-h-[12rem]">
                    <i class='bx bx-wallet absolute -bottom-6 -right-6 text-[140px] text-white/10 transform rotate-12 group-hover:rotate-6 transition-transform duration-500 pointer-events-none'></i>
                    
                    <div class="relative z-10 flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-teal-100 flex items-center gap-1.5">
                            <i class='bx bx-dollar-circle text-lg'></i> Pendapatan Bulan Ini
                        </span>
                        <span class="w-8 h-8 rounded-full bg-white/10 backdrop-blur-md flex items-center justify-center text-white group-hover:translate-x-1 transition-transform">
                            <i class='bx bx-right-arrow-alt text-xl'></i>
                        </span>
                    </div>

                    <div class="relative z-10 my-4">
                        <h3 class="text-3xl sm:text-4xl font-extrabold tracking-tight">Rp {{ number_format($totalPendapatan ?? 0, 0, ',', '.') }}</h3>
                    </div>

                    <div class="relative z-10 flex items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 text-xs font-semibold bg-white/20 px-3 py-1.5 rounded-full backdrop-blur-md border border-white/20">
                            <i class='bx bx-trending-up text-teal-200'></i> {{ $pendapatanGrowth ?? 0 }}% vs bulan lalu
                        </span>
                    </div>
                </a>

                <!-- Saldo Tertahan Tile -->
                <a href="{{ route('supplier.restock') }}"
                    class="rounded-3xl bg-white/80 dark:bg-zinc-900/80 backdrop-blur-xl p-6 border border-zinc-200/80 dark:border-zinc-800/80 shadow-md hover:shadow-xl hover:border-amber-500/40 transition-all duration-300 flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 flex items-center justify-center">
                            <i class='bx bx-time-five text-xl'></i>
                        </div>
                        <span class="text-[11px] font-bold text-zinc-400 uppercase tracking-wider">Tertahan</span>
                    </div>
                    <div class="mt-6">
                        <span class="text-xs text-zinc-500 font-medium">Saldo Menunggu Konsinyasi</span>
                        <h3 class="text-xl font-extrabold text-zinc-900 dark:text-white tracking-tight mt-1">
                            Rp {{ number_format($saldoTertahan ?? 0, 0, ',', '.') }}
                        </h3>
                    </div>
                </a>
            </div>

            <!-- Sub Stats Row -->
            <div class="grid grid-cols-2 gap-4 mt-4">
                <!-- Unit Terjual -->
                <a href="{{ route('supplier.sales') }}"
                    class="rounded-2xl bg-white/80 dark:bg-zinc-900/80 backdrop-blur-xl p-5 border border-zinc-200/80 dark:border-zinc-800/80 shadow-md hover:shadow-xl hover:border-blue-500/40 transition-all duration-300 flex items-center gap-4">
                    <div class="w-11 h-11 rounded-xl bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                        <i class='bx bx-package text-xl'></i>
                    </div>
                    <div>
                        <span class="text-xs font-semibold text-zinc-500 dark:text-zinc-400">Total Terjual</span>
                        <div class="flex items-baseline gap-1 mt-0.5">
                            <h4 class="text-xl font-extrabold text-zinc-900 dark:text-white tracking-tight">{{ $unitTerjual ?? 0 }}</h4>
                            <span class="text-xs text-zinc-400 font-medium">Unit</span>
                        </div>
                    </div>
                </a>

                <!-- Produk Aktif -->
                <a href="{{ route('supplier.products.index') }}"
                    class="rounded-2xl bg-white/80 dark:bg-zinc-900/80 backdrop-blur-xl p-5 border border-zinc-200/80 dark:border-zinc-800/80 shadow-md hover:shadow-xl hover:border-indigo-500/40 transition-all duration-300 flex items-center gap-4">
                    <div class="w-11 h-11 rounded-xl bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
                        <i class='bx bx-store-alt text-xl'></i>
                    </div>
                    <div>
                        <span class="text-xs font-semibold text-zinc-500 dark:text-zinc-400">Katalog Aktif</span>
                        <div class="flex items-baseline gap-1 mt-0.5">
                            <h4 class="text-xl font-extrabold text-zinc-900 dark:text-white tracking-tight">{{ $produkAktif ?? 0 }}</h4>
                            <span class="text-xs text-zinc-400 font-medium">SKU</span>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Riwayat Pembayaran Section -->
        @if($recentSettled->count() > 0)
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-extrabold text-zinc-900 dark:text-white flex items-center gap-2 tracking-tight">
                        <i class='bx bx-check-shield text-teal-500 text-lg'></i> Pembayaran Terakhir
                    </h3>
                </div>

                <div class="space-y-3">
                    @foreach($recentSettled as $batch)
                        <a href="{{ route('supplier.restock') }}" 
                            class="block rounded-2xl bg-white/80 dark:bg-zinc-900/80 backdrop-blur-xl p-5 border border-zinc-200/80 dark:border-zinc-800/80 shadow-md hover:shadow-xl hover:border-teal-500/40 transition-all duration-300 group">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div class="space-y-1.5">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-extrabold text-zinc-900 dark:text-white group-hover:text-[#155A6B] dark:group-hover:text-teal-400 transition-colors">#{{ $batch->batchCode }}</span>
                                        <span class="bg-teal-50 text-teal-700 dark:bg-teal-950/50 dark:text-teal-300 border border-teal-200 dark:border-teal-800/50 px-2.5 py-0.5 rounded-full text-[10px] font-bold">
                                            ✓ LUNAS
                                        </span>
                                    </div>
                                    <p class="text-xs text-zinc-500 flex items-center gap-1.5">
                                        <i class='bx bx-calendar text-sm'></i>
                                        Dibayar: <span class="font-semibold text-zinc-700 dark:text-zinc-300">{{ $batch->settledAt?->format('d M Y H:i') ?? '-' }}</span>
                                    </p>
                                    @if($batch->items->count() > 0)
                                        @php
                                            $totalSold = $batch->items->sum('soldQty');
                                            $totalReturned = $batch->items->sum('returnedQty');
                                        @endphp
                                        <div class="flex items-center gap-3 text-xs pt-1">
                                            <span class="text-teal-600 dark:text-teal-400 font-semibold flex items-center gap-1">
                                                <i class='bx bx-check-circle'></i> {{ $totalSold }} terjual
                                            </span>
                                            @if($totalReturned > 0)
                                                <span class="text-zinc-300 dark:text-zinc-700">•</span>
                                                <span class="text-rose-600 dark:text-rose-400 font-semibold flex items-center gap-1">
                                                    <i class='bx bx-undo'></i> {{ $totalReturned }} diretur
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                </div>

                                <div class="text-left sm:text-right flex flex-col sm:items-end justify-center pt-2 sm:pt-0 border-t sm:border-0 border-zinc-100 dark:border-zinc-800">
                                    <span class="text-[11px] font-medium text-zinc-400 uppercase tracking-wider">Total Cair</span>
                                    <span class="text-lg font-extrabold text-[#155A6B] dark:text-teal-400 group-hover:scale-105 transition-transform">
                                        Rp {{ number_format($batch->payableAmount ?? 0, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                <a href="{{ route('supplier.restock') }}" 
                    class="mt-4 w-full flex items-center justify-center gap-2 px-5 py-3 rounded-2xl bg-white/80 dark:bg-zinc-900/80 backdrop-blur-xl border border-zinc-200/80 dark:border-zinc-800/80 hover:border-[#155A6B] dark:hover:border-teal-400 shadow-sm text-xs font-bold text-zinc-700 dark:text-zinc-300 hover:text-[#155A6B] dark:hover:text-teal-400 transition-all group">
                    <i class='bx bx-history text-base'></i>
                    Lihat Riwayat Konsinyasi Lengkap
                    <i class='bx bx-chevron-right text-sm group-hover:translate-x-1 transition-transform'></i>
                </a>
            </div>
        @endif

        <!-- Chart Section -->
        <div
            class="rounded-3xl bg-white/80 dark:bg-zinc-900/80 backdrop-blur-xl p-6 border border-zinc-200/80 dark:border-zinc-800/80 shadow-xl">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h3 class="font-extrabold text-sm text-zinc-900 dark:text-white flex items-center gap-2 tracking-tight">
                        <i class='bx bx-line-chart text-[#155A6B] dark:text-teal-400 text-lg'></i> Tren Penjualan 7 Hari Terakhir
                    </h3>
                </div>
                <a href="{{ route('supplier.sales') }}" class="text-xs font-bold text-[#155A6B] dark:text-teal-400 hover:underline flex items-center gap-1">
                    Detail <i class='bx bx-chevron-right'></i>
                </a>
            </div>
            <div class="h-[220px] w-full" id="salesChart"></div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const getChartColors = (theme) => ({
            text: theme === 'dark' ? '#a1a1aa' : '#71717a',
            grid: theme === 'dark' ? '#27272a' : '#f4f4f5',
            tooltipTheme: theme === 'dark' ? 'dark' : 'light'
        });

        const isDark = document.documentElement.classList.contains('dark');

        var optionsSales = {
            series: [{ name: 'Penjualan (Rp)', data: [0, 0, 0, 0, 0, 0, 0] }],
            chart: {
                height: '100%',
                type: 'area',
                toolbar: { show: false },
                fontFamily: '-apple-system, BlinkMacSystemFont, "SF Pro Display", "Plus Jakarta Sans", sans-serif',
                foreColor: isDark ? '#a1a1aa' : '#71717a'
            },
            colors: ['#155A6B'],
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 2.5 },
            fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.35, opacityTo: 0.02, stops: [0, 90, 100] } },
            xaxis: {
                categories: ["Sen", "Sel", "Rab", "Kam", "Jum", "Sab", "Min"],
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: {
                labels: { formatter: (val) => { return val >= 1000 ? (val / 1000) + 'k' : val } }
            },
            grid: { borderColor: isDark ? '#27272a' : '#f4f4f5', strokeDashArray: 4 },
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