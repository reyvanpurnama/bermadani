<div class="space-y-6">
    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white dark:bg-darkCard p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="p-2 rounded-lg bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400">
                    <i class='bx bx-bar-chart-alt-2 text-2xl'></i>
                </span>
                <h1 class="text-xl font-bold text-slate-800 dark:text-white">Visualisasi & Analisis Arus Kas</h1>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400">
                Dashboard analitik arus kas (Pemasukan, Pengeluaran & Net Surplus) berbasis data transaksi historis & rekap.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            {{-- Year Selector --}}
            <div class="relative">
                <select wire:model.live="selectedYear"
                    class="appearance-none bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white text-xs font-bold rounded-xl px-4 py-2.5 pr-9 outline-none focus:ring-2 focus:ring-indigo-500 cursor-pointer">
                    @foreach($availableYears as $year)
                        <option value="{{ $year }}">Tahun {{ $year }}</option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-500">
                    <i class='bx bx-chevron-down text-base'></i>
                </div>
            </div>

            <a href="{{ route('admin.manual-transaction') }}"
                class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-sm shadow-indigo-600/20 transition-all flex items-center gap-2">
                <i class='bx bx-plus text-base'></i> Transaksi Manual
            </a>
            <a href="{{ route('admin.manual-transaction.history') }}"
                class="bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 transition-all flex items-center gap-2">
                <i class='bx bx-history text-base'></i> Riwayat Transaksi
            </a>
        </div>
    </div>

    {{-- Summary KPI Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Total Kas Masuk --}}
        <div class="bg-white dark:bg-darkCard p-5 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 flex items-center gap-4 relative overflow-hidden group">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center text-emerald-600 dark:text-emerald-400 text-2xl shrink-0 group-hover:scale-110 transition-transform">
                <i class='bx bx-trending-up'></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Total Kas Masuk</p>
                <h3 class="text-xl font-bold text-slate-800 dark:text-white">
                    Rp {{ number_format($summary['totalIncome'], 0, ',', '.') }}
                </h3>
                <span class="text-[10px] text-emerald-600 dark:text-emerald-400 font-medium">
                    ~ Rp {{ number_format($summary['avgIncome'], 0, ',', '.') }} / bln
                </span>
            </div>
            <div class="absolute -right-3 -bottom-3 text-emerald-500/5 text-7xl pointer-events-none">
                <i class='bx bx-wallet'></i>
            </div>
        </div>

        {{-- Total Kas Keluar --}}
        <div class="bg-white dark:bg-darkCard p-5 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 flex items-center gap-4 relative overflow-hidden group">
            <div class="w-12 h-12 rounded-xl bg-rose-50 dark:bg-rose-500/10 flex items-center justify-center text-rose-600 dark:text-rose-400 text-2xl shrink-0 group-hover:scale-110 transition-transform">
                <i class='bx bx-trending-down'></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Total Kas Keluar</p>
                <h3 class="text-xl font-bold text-slate-800 dark:text-white">
                    Rp {{ number_format($summary['totalExpense'], 0, ',', '.') }}
                </h3>
                <span class="text-[10px] text-rose-600 dark:text-rose-400 font-medium">
                    ~ Rp {{ number_format($summary['avgExpense'], 0, ',', '.') }} / bln
                </span>
            </div>
            <div class="absolute -right-3 -bottom-3 text-rose-500/5 text-7xl pointer-events-none">
                <i class='bx bx-credit-card'></i>
            </div>
        </div>

        {{-- Net Cash Flow --}}
        <div class="bg-white dark:bg-darkCard p-5 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 flex items-center gap-4 relative overflow-hidden group">
            <div class="w-12 h-12 rounded-xl {{ $summary['netCashFlow'] >= 0 ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400' : 'bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400' }} flex items-center justify-center text-2xl shrink-0 group-hover:scale-110 transition-transform">
                <i class='bx {{ $summary['netCashFlow'] >= 0 ? 'bx-line-chart' : 'bx-error-circle' }}'></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Arus Kas Bersih (Net)</p>
                <h3 class="text-xl font-bold {{ $summary['netCashFlow'] >= 0 ? 'text-indigo-600 dark:text-indigo-400' : 'text-amber-600 dark:text-amber-400' }}">
                    Rp {{ number_format($summary['netCashFlow'], 0, ',', '.') }}
                </h3>
                <span class="text-[10px] text-slate-400 font-medium">
                    {{ $summary['netCashFlow'] >= 0 ? 'Surplus Operasional' : 'Defisit Operasional' }}
                </span>
            </div>
            <div class="absolute -right-3 -bottom-3 text-indigo-500/5 text-7xl pointer-events-none">
                <i class='bx bx-pie-chart-alt-2'></i>
            </div>
        </div>

        {{-- Bulan Aktif Terhitung --}}
        <div class="bg-white dark:bg-darkCard p-5 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 flex items-center gap-4 relative overflow-hidden group">
            <div class="w-12 h-12 rounded-xl bg-cyan-50 dark:bg-cyan-500/10 flex items-center justify-center text-cyan-600 dark:text-cyan-400 text-2xl shrink-0 group-hover:scale-110 transition-transform">
                <i class='bx bx-calendar'></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Bulan Tercatat</p>
                <h3 class="text-xl font-bold text-slate-800 dark:text-white">
                    {{ $summary['activeMonths'] }} Bulan
                </h3>
                <span class="text-[10px] text-cyan-600 dark:text-cyan-400 font-medium">
                    Tahun {{ $selectedYear }}
                </span>
            </div>
            <div class="absolute -right-3 -bottom-3 text-cyan-500/5 text-7xl pointer-events-none">
                <i class='bx bx-time-five'></i>
            </div>
        </div>
    </div>

    {{-- Main Charts Grid Row 1 --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        {{-- Chart 1: Monthly Cash Flow Trend (Area Chart) --}}
        <div class="lg:col-span-8 bg-white dark:bg-darkCard p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 flex flex-col justify-between"
            x-data="{
                chart: null,
                data: @js($chartData['trend']),
                init() {
                    this.renderChart();
                    $wire.watch('chartData', (newData) => {
                        this.data = newData.trend;
                        this.updateChart();
                    });
                },
                renderChart() {
                    const isDark = document.documentElement.classList.contains('dark');
                    const options = {
                        series: [
                            { name: 'Kas Masuk', data: this.data.income },
                            { name: 'Kas Keluar', data: this.data.expense },
                        ],
                        chart: {
                            type: 'area',
                            height: 330,
                            toolbar: { show: false },
                            fontFamily: 'Inter, sans-serif',
                            foreColor: isDark ? '#94a3b8' : '#64748b'
                        },
                        colors: ['#10B981', '#EF4444'],
                        dataLabels: { enabled: false },
                        stroke: { curve: 'smooth', width: 3 },
                        fill: {
                            type: 'gradient',
                            gradient: { shadeIntensity: 1, opacityFrom: 0.3, opacityTo: 0.05, stops: [0, 90, 100] }
                        },
                        xaxis: {
                            categories: this.data.categories,
                            axisBorder: { show: false },
                            axisTicks: { show: false }
                        },
                        yaxis: {
                            labels: {
                                formatter: (val) => 'Rp ' + (val / 1000000).toFixed(1) + ' Jt'
                            }
                        },
                        grid: { borderColor: isDark ? '#1e293b' : '#f1f5f9', strokeDashArray: 4 },
                        tooltip: {
                            theme: isDark ? 'dark' : 'light',
                            y: { formatter: (val) => 'Rp ' + new Intl.NumberFormat('id-ID').format(val) }
                        },
                        legend: { position: 'top', horizontalAlign: 'right' }
                    };
                    this.chart = new ApexCharts(this.$refs.trendChart, options);
                    this.chart.render();
                },
                updateChart() {
                    if (this.chart) {
                        this.chart.updateOptions({
                            xaxis: { categories: this.data.categories },
                            series: [
                                { name: 'Kas Masuk', data: this.data.income },
                                { name: 'Kas Keluar', data: this.data.expense }
                            ]
                        });
                    }
                }
            }" wire:ignore>
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="font-bold text-base text-slate-800 dark:text-white">Tren Arus Kas Bulanan</h3>
                    <p class="text-xs text-slate-400">Perbandingan pergerakan Kas Masuk vs Kas Keluar (Tahun {{ $selectedYear }})</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="flex items-center gap-1 text-xs font-semibold text-emerald-600"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> Masuk</span>
                    <span class="flex items-center gap-1 text-xs font-semibold text-rose-600 ml-2"><span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span> Keluar</span>
                </div>
            </div>
            <div x-ref="trendChart" class="w-full"></div>
        </div>

        {{-- Chart 2: Net Cash Flow Bar Chart (Positive/Negative) --}}
        <div class="lg:col-span-4 bg-white dark:bg-darkCard p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 flex flex-col justify-between"
            x-data="{
                chart: null,
                data: @js($chartData['trend']),
                init() {
                    this.renderChart();
                    $wire.watch('chartData', (newData) => {
                        this.data = newData.trend;
                        this.updateChart();
                    });
                },
                renderChart() {
                    const isDark = document.documentElement.classList.contains('dark');
                    const options = {
                        series: [{ name: 'Net Arus Kas', data: this.data.net }],
                        chart: {
                            type: 'bar',
                            height: 330,
                            toolbar: { show: false },
                            fontFamily: 'Inter, sans-serif',
                            foreColor: isDark ? '#94a3b8' : '#64748b'
                        },
                        plotOptions: {
                            bar: {
                                colors: {
                                    ranges: [
                                        { from: -9999999999, to: -1, color: '#EF4444' },
                                        { from: 0, to: 9999999999, color: '#6366F1' }
                                    ]
                                },
                                borderRadius: 6,
                                columnWidth: '55%'
                            }
                        },
                        dataLabels: { enabled: false },
                        xaxis: {
                            categories: this.data.categories,
                            axisBorder: { show: false },
                            axisTicks: { show: false }
                        },
                        yaxis: {
                            labels: {
                                formatter: (val) => (val / 1000000).toFixed(1) + ' Jt'
                            }
                        },
                        grid: { borderColor: isDark ? '#1e293b' : '#f1f5f9', strokeDashArray: 4 },
                        tooltip: {
                            theme: isDark ? 'dark' : 'light',
                            y: { formatter: (val) => 'Rp ' + new Intl.NumberFormat('id-ID').format(val) }
                        }
                    };
                    this.chart = new ApexCharts(this.$refs.netChart, options);
                    this.chart.render();
                },
                updateChart() {
                    if (this.chart) {
                        this.chart.updateOptions({
                            xaxis: { categories: this.data.categories },
                            series: [{ name: 'Net Arus Kas', data: this.data.net }]
                        });
                    }
                }
            }" wire:ignore>
            <div class="mb-4">
                <h3 class="font-bold text-base text-slate-800 dark:text-white">Arus Kas Bersih (Net)</h3>
                <p class="text-xs text-slate-400">Surplus / Defisit Kas tiap Bulan</p>
            </div>
            <div x-ref="netChart" class="w-full"></div>
        </div>
    </div>

    {{-- Main Charts Grid Row 2: Breakdown Kategori --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Chart 3: Kas Masuk Breakdown --}}
        <div class="bg-white dark:bg-darkCard p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 flex flex-col justify-between"
            x-data="{
                chart: null,
                data: @js($chartData['incomeCategories']),
                init() {
                    this.renderChart();
                    $wire.watch('chartData', (newData) => {
                        this.data = newData.incomeCategories;
                        this.updateChart();
                    });
                },
                renderChart() {
                    const isDark = document.documentElement.classList.contains('dark');
                    const options = {
                        series: this.data.series,
                        labels: this.data.labels,
                        chart: {
                            type: 'donut',
                            height: 320,
                            fontFamily: 'Inter, sans-serif',
                            foreColor: isDark ? '#94a3b8' : '#64748b'
                        },
                        colors: ['#10B981', '#3B82F6', '#6366F1', '#8B5CF6', '#F59E0B', '#EC4899'],
                        legend: { position: 'bottom' },
                        dataLabels: { enabled: false },
                        tooltip: {
                            theme: isDark ? 'dark' : 'light',
                            y: { formatter: (val) => 'Rp ' + new Intl.NumberFormat('id-ID').format(val) }
                        }
                    };
                    this.chart = new ApexCharts(this.$refs.incomeDonut, options);
                    this.chart.render();
                },
                updateChart() {
                    if (this.chart) {
                        this.chart.updateOptions({
                            labels: this.data.labels,
                            series: this.data.series
                        });
                    }
                }
            }" wire:ignore>
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="font-bold text-base text-slate-800 dark:text-white">Komposisi Kas Masuk</h3>
                    <p class="text-xs text-slate-400">Kontribusi sumber dana masuk per kategori</p>
                </div>
                <span class="p-2 rounded-lg bg-emerald-50 text-emerald-600 text-lg">
                    <i class='bx bx-pie-chart'></i>
                </span>
            </div>
            <div x-ref="incomeDonut" class="w-full"></div>
        </div>

        {{-- Chart 4: Kas Keluar Breakdown --}}
        <div class="bg-white dark:bg-darkCard p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 flex flex-col justify-between"
            x-data="{
                chart: null,
                data: @js($chartData['expenseCategories']),
                init() {
                    this.renderChart();
                    $wire.watch('chartData', (newData) => {
                        this.data = newData.expenseCategories;
                        this.updateChart();
                    });
                },
                renderChart() {
                    const isDark = document.documentElement.classList.contains('dark');
                    const options = {
                        series: [{ name: 'Total Pengeluaran', data: this.data.series }],
                        chart: {
                            type: 'bar',
                            height: 320,
                            toolbar: { show: false },
                            fontFamily: 'Inter, sans-serif',
                            foreColor: isDark ? '#94a3b8' : '#64748b'
                        },
                        plotOptions: {
                            bar: {
                                horizontal: true,
                                borderRadius: 6,
                                barHeight: '60%'
                            }
                        },
                        colors: ['#F43F5E'],
                        dataLabels: { enabled: false },
                        xaxis: {
                            categories: this.data.labels,
                            labels: {
                                formatter: (val) => (val / 1000000).toFixed(1) + ' Jt'
                            }
                        },
                        grid: { borderColor: isDark ? '#1e293b' : '#f1f5f9', strokeDashArray: 4 },
                        tooltip: {
                            theme: isDark ? 'dark' : 'light',
                            y: { formatter: (val) => 'Rp ' + new Intl.NumberFormat('id-ID').format(val) }
                        }
                    };
                    this.chart = new ApexCharts(this.$refs.expenseBar, options);
                    this.chart.render();
                },
                updateChart() {
                    if (this.chart) {
                        this.chart.updateOptions({
                            xaxis: { categories: this.data.labels },
                            series: [{ name: 'Total Pengeluaran', data: this.data.series }]
                        });
                    }
                }
            }" wire:ignore>
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="font-bold text-base text-slate-800 dark:text-white">Breakdown Kas Keluar</h3>
                    <p class="text-xs text-slate-400">Peringkat pengeluaran terbanyak per kategori</p>
                </div>
                <span class="p-2 rounded-lg bg-rose-50 text-rose-600 text-lg">
                    <i class='bx bx-bar-chart-alt'></i>
                </span>
            </div>
            <div x-ref="expenseBar" class="w-full"></div>
        </div>
    </div>
</div>
