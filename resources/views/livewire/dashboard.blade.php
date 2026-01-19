<div class="space-y-6">
    {{-- Welcome Banner --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 mb-6">
        <div class="lg:col-span-8 h-full">
            <div
                class="bg-indigo-600 rounded-xl p-5 text-white shadow-sm relative overflow-hidden flex flex-col justify-center h-full">
                <div class="relative z-10 flex justify-between items-center">
                    <div>
                        <h2 class="text-lg font-bold mb-1">Selamat datang kembali, {{ auth()->user()->name }}! 👋</h2>
                        <p class="text-indigo-100 text-xs mb-3 opacity-90">
                            @if($this->profitGrowth > 0)
                                Performa penjualan naik <span class="font-bold text-white">{{ $this->profitGrowth }}%</span>
                                {{ strtolower($this->previousPeriodLabel) }}.
                            @elseif($this->profitGrowth < 0)
                                Performa penjualan turun <span
                                    class="font-bold text-white">{{ abs($this->profitGrowth) }}%</span>
                                {{ strtolower($this->previousPeriodLabel) }}.
                            @else
                                Performa penjualan stabil {{ strtolower($this->previousPeriodLabel) }}.
                            @endif
                        </p>
                        <a href="{{ route('admin.reports.balance-sheet') }}"
                            class="bg-white/20 hover:bg-white/30 text-white text-[10px] uppercase tracking-wider px-3 py-1.5 rounded-md font-semibold transition-colors border border-white/10 inline-block">
                            Neraca Saldo
                        </a>
                    </div>
                    <i class='bx bx-trophy text-6xl text-indigo-400 opacity-40 mr-4'></i>
                </div>
                <div
                    class="absolute right-0 top-0 h-full w-1/3 opacity-10 bg-gradient-to-l from-white to-transparent transform skew-x-12 pointer-events-none">
                </div>
            </div>
        </div>

        <div class="lg:col-span-4 h-full grid grid-cols-2 lg:flex lg:flex-col gap-4">
            <div
                class="flex-1 bg-card dark:bg-darkCard px-5 py-3 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 flex items-center justify-between group relative">
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Total Laba Bersih
                    </p>
                    <div class="flex items-end gap-2">
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white leading-none">Rp
                            {{ number_format($this->allTimeProfit, 0, ',', '.') }}
                        </h3>
                    </div>
                    <p class="text-[9px] text-slate-400 mt-0.5">Sejak {{ $this->firstTransactionDate }}</p>
                </div>
                <div class="bg-emerald-50 dark:bg-emerald-500/10 p-2 rounded-lg text-emerald-500"><i
                        class='bx bx-line-chart text-lg'></i></div>
            </div>

            <div
                class="flex-1 bg-card dark:bg-darkCard px-5 py-3 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 flex items-center justify-between group relative">
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Total Omzet</p>
                    <div class="flex items-end gap-2">
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white leading-none">Rp
                            {{ number_format($this->allTimeSales, 0, ',', '.') }}
                        </h3>
                    </div>
                    <p class="text-[9px] text-slate-400 mt-0.5">Sejak {{ $this->firstTransactionDate }}</p>
                </div>
                <div class="bg-blue-50 dark:bg-blue-500/10 p-2 rounded-lg text-blue-500"><i
                        class='bx bx-wallet text-lg'></i></div>
            </div>
        </div>
    </div>

    {{-- Main Content Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
        <div class="lg:col-span-8 flex flex-col gap-4 h-fit">
            {{-- Revenue Chart Section with Profitability Sidebar --}}
            <div
                class="bg-card dark:bg-darkCard rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 flex flex-col md:flex-row overflow-hidden">
                {{-- Chart Area --}}
                <div
                    class="flex-1 p-5 border-b md:border-b-0 md:border-r border-slate-100 dark:border-slate-700 flex flex-col justify-between">
                    <div>
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-2">
                            <div>
                                <h3 class="font-bold text-base text-slate-800 dark:text-white">Ringkasan Omzet</h3>
                                <p class="text-[11px] text-slate-500">Analisis Omzet vs Beban</p>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="flex gap-3 hidden sm:flex">
                                    <div class="flex items-center gap-1.5"><span
                                            class="w-2 h-2 rounded-full bg-indigo-500"></span><span
                                            class="text-[10px] text-slate-500 font-medium">Omzet</span></div>
                                    <div class="flex items-center gap-1.5"><span
                                            class="w-2 h-2 rounded-full bg-slate-300"></span><span
                                            class="text-[10px] text-slate-500 font-medium">Beban</span></div>
                                </div>
                                <div class="h-4 w-px bg-slate-200 dark:bg-slate-600 hidden sm:block"></div>
                                {{-- Date Range Picker --}}
                                <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                                    <button @click="open = !open"
                                        class="flex items-center gap-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-lg px-3 py-1.5 text-xs font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors shadow-sm">
                                        <i class='bx bx-calendar text-slate-500 text-sm'></i>
                                        <span class="truncate max-w-[120px] sm:max-w-none">
                                            @if($dateFilter === 'custom')
                                                {{ \Carbon\Carbon::parse($startDate)->format('d M y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M y') }}
                                            @else
                                                {{ match($dateFilter) {
                                                    'today' => 'Hari Ini',
                                                    'yesterday' => 'Kemarin',
                                                    'this_week' => 'Minggu Ini',
                                                    'this_month' => 'Bulan Ini',
                                                    'last_month' => 'Bulan Lalu',
                                                    'this_year' => 'Tahun Ini',
                                                    default => 'Bulan Ini'
                                                } }}
                                            @endif
                                        </span>
                                        <i class='bx bx-chevron-down text-slate-400'></i>
                                    </button>

                                    {{-- Mobile Backdrop --}}
                                    <div x-show="open" x-transition.opacity 
                                        @click="open = false"
                                        class="fixed inset-0 z-40 bg-black/40 backdrop-blur-sm md:hidden"
                                        x-cloak></div>

                                    <div x-show="open" 
                                        x-transition:enter="transition ease-out duration-300"
                                        x-transition:enter-start="opacity-0 translate-y-full md:translate-y-0 md:opacity-0 md:scale-95"
                                        x-transition:enter-end="opacity-100 translate-y-0 md:opacity-100 md:scale-100"
                                        x-transition:leave="transition ease-in duration-200"
                                        x-transition:leave-start="opacity-100 translate-y-0 md:opacity-100 md:scale-100"
                                        x-transition:leave-end="opacity-0 translate-y-full md:translate-y-0 md:opacity-0 md:scale-95"
                                        x-cloak 
                                        class="fixed bottom-0 left-0 right-0 z-50 w-full bg-white dark:bg-slate-800 rounded-t-2xl shadow-2xl border-t border-slate-200 dark:border-slate-700 
                                               md:absolute md:bottom-auto md:left-auto md:right-0 md:top-full md:mt-2 md:w-auto md:min-w-[450px] md:max-w-[450px] md:rounded-xl md:border md:shadow-xl md:flex md:flex-row overflow-hidden
                                               flex flex-col max-h-[85vh] md:max-h-none">
                                        
                                        {{-- Mobile Handle --}}
                                        <div class="flex justify-center p-3 md:hidden">
                                            <div class="w-12 h-1.5 bg-slate-300 dark:bg-slate-600 rounded-full"></div>
                                        </div>

                                        {{-- Presets Sidebar --}}
                                        <div class="w-full md:w-36 bg-slate-50 dark:bg-slate-900/50 border-b md:border-b-0 md:border-r border-slate-100 dark:border-slate-700 p-2 flex flex-row md:flex-col gap-1 overflow-x-auto md:overflow-visible">
                                            @foreach([
                                                'today' => 'Hari Ini',
                                                'yesterday' => 'Kemarin',
                                                'this_week' => 'Minggu Ini',
                                                'this_month' => 'Bulan Ini',
                                                'last_month' => 'Bulan Lalu',
                                                'this_year' => 'Tahun Ini'
                                            ] as $key => $label)
                                                <button wire:click="setDateFilter('{{ $key }}')" @click="open = false"
                                                    class="whitespace-nowrap md:whitespace-normal text-left px-3 py-2 rounded-md text-[11px] font-medium transition-colors flex-shrink-0 md:w-full {{ $dateFilter === $key ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700' }}">
                                                    {{ $label }}
                                                </button>
                                            @endforeach
                                            <div class="w-px md:w-full h-8 md:h-px bg-slate-200 dark:bg-slate-700 mx-1 md:mx-0 md:my-1 flex-shrink-0"></div>
                                            <button @click="document.getElementById('startDate').focus()"
                                                class="whitespace-nowrap md:whitespace-normal text-left px-3 py-2 rounded-md text-[11px] font-medium transition-colors flex-shrink-0 md:w-full {{ $dateFilter === 'custom' ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700' }}">
                                                Custom Range
                                            </button>
                                        </div>

                                        {{-- Custom Range Area --}}
                                        <div class="flex-1 p-4 bg-white dark:bg-slate-800 flex flex-col justify-center pb-10 md:pb-4">
                                            <h4 class="text-[10px] font-bold text-slate-900 dark:text-white uppercase tracking-wider mb-3 hidden md:block">Pilih Rentang Tanggal</h4>
                                            
                                            {{-- Mobile Title --}}
                                            <div class="mb-4 md:hidden">
                                                <h3 class="text-sm font-bold text-slate-900 dark:text-white">Pilih Tanggal</h3>
                                                <p class="text-[11px] text-slate-500">Sesuaikan rentang waktu laporan</p>
                                            </div>

                                            <div class="grid grid-cols-2 gap-3 mb-4">
                                                <div>
                                                    <label class="block text-[10px] font-medium text-slate-500 mb-1">Dari</label>
                                                    <input type="date" wire:model.live.debounce.500ms="startDate" id="startDate"
                                                        class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-md px-2 py-2 md:py-1.5 text-xs text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                                </div>
                                                <div>
                                                    <label class="block text-[10px] font-medium text-slate-500 mb-1">Sampai</label>
                                                    <input type="date" wire:model.live.debounce.500ms="endDate"
                                                        class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-md px-2 py-2 md:py-1.5 text-xs text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                                </div>
                                            </div>
                                            <div class="flex justify-between items-center pt-2 border-t border-slate-100 dark:border-slate-700">
                                                <span class="text-[10px] text-slate-400">
                                                    {{ \Carbon\Carbon::parse($startDate)->diffInDays(\Carbon\Carbon::parse($endDate)) + 1 }} Hari
                                                </span>
                                                <button type="button" @click="open = false"
                                                    class="bg-indigo-600 hover:bg-indigo-700 text-white text-[10px] uppercase font-bold px-4 py-2.5 md:py-2 rounded-md transition-colors shadow-sm shadow-indigo-200 dark:shadow-none w-full md:w-auto ml-3 md:ml-0">
                                                    Selesai
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex-1 w-full min-h-[260px] relative group/chart" x-data="{
                            chart: null,
                            hasData: false,
                            get isDark() { return document.documentElement.classList.contains('dark') },
                            init() {
                                let colors = {
                                    text: this.isDark ? '#94a3b8' : '#64748b',
                                    grid: this.isDark ? '#1e293b' : '#e2e8f0',
                                    tooltip: this.isDark ? 'dark' : 'light',
                                    income: this.isDark ? '#10b981' : '#059669',
                                    expense: this.isDark ? '#ef4444' : '#dc2626'
                                };

                                var options = {
                                    series: [],
                                    chart: {
                                        height: '100%',
                                        type: 'area',
                                        toolbar: { show: false },
                                        fontFamily: 'Inter, system-ui, sans-serif',
                                        foreColor: colors.text,
                                        background: 'transparent',
                                        animations: { 
                                            enabled: true,
                                            easing: 'easeinout',
                                            speed: 800,
                                            animateGradually: {
                                                enabled: true,
                                                delay: 150
                                            },
                                            dynamicAnimation: {
                                                enabled: true,
                                                speed: 350
                                            }
                                        },
                                        zoom: { enabled: false },
                                        selection: { enabled: false }
                                    },
                                    colors: [colors.income, colors.expense],
                                    dataLabels: { enabled: false },
                                    stroke: { 
                                        curve: 'smooth', 
                                        width: [3, 3],
                                        lineCap: 'round'
                                    },
                                    fill: { 
                                        type: 'gradient',
                                        gradient: { 
                                            type: 'vertical',
                                            shadeIntensity: 1,
                                            opacityFrom: [0.5, 0.3],
                                            opacityTo: [0.05, 0.02],
                                            stops: [0, 85, 100]
                                        }
                                    },
                                    markers: {
                                        size: 0,
                                        hover: {
                                            size: 5,
                                            sizeOffset: 3
                                        }
                                    },
                                    xaxis: { 
                                        type: 'datetime', 
                                        categories: [], 
                                        axisBorder: { show: false }, 
                                        axisTicks: { show: false },
                                        labels: {
                                            style: { 
                                                fontSize: '11px',
                                                fontWeight: 500,
                                                colors: colors.text
                                            },
                                            rotate: 0,
                                            hideOverlappingLabels: true,
                                            datetimeUTC: false
                                        },
                                        tooltip: { enabled: false }
                                    },
                                    yaxis: {
                                        labels: {
                                            style: { 
                                                fontSize: '11px',
                                                fontWeight: 500,
                                                colors: colors.text
                                            },
                                            formatter: function(value) {
                                                if (value >= 1000000) {
                                                    return 'Rp ' + (value / 1000000).toFixed(1) + 'jt';
                                                } else if (value >= 1000) {
                                                    return 'Rp ' + (value / 1000).toFixed(0) + 'rb';
                                                }
                                                return 'Rp ' + value.toFixed(0);
                                            }
                                        },
                                        min: 0
                                    },
                                    grid: { 
                                        borderColor: colors.grid,
                                        strokeDashArray: 3,
                                        xaxis: { lines: { show: false } },
                                        yaxis: { lines: { show: true } },
                                        padding: {
                                            top: 0,
                                            right: 10,
                                            bottom: 0,
                                            left: 10
                                        }
                                    },
                                    legend: { 
                                        show: true,
                                        position: 'top',
                                        horizontalAlign: 'right',
                                        fontSize: '12px',
                                        fontWeight: 600,
                                        markers: {
                                            width: 10,
                                            height: 10,
                                            radius: 3
                                        },
                                        itemMargin: {
                                            horizontal: 12,
                                            vertical: 0
                                        }
                                    },
                                    tooltip: { 
                                        theme: colors.tooltip,
                                        shared: true,
                                        intersect: false,
                                        x: {
                                            format: 'dd MMM yyyy HH:mm'
                                        },
                                        y: {
                                            formatter: function(value) {
                                                if (!value) return 'Rp 0';
                                                return 'Rp ' + value.toLocaleString('id-ID');
                                            }
                                        },
                                        marker: {
                                            show: true
                                        },
                                        style: {
                                            fontSize: '12px',
                                            fontFamily: 'Inter, system-ui, sans-serif'
                                        }
                                    },
                                    responsive: [
                                        {
                                            breakpoint: 768,
                                            options: {
                                                legend: {
                                                    position: 'bottom',
                                                    horizontalAlign: 'center'
                                                },
                                                xaxis: {
                                                    labels: {
                                                        style: { fontSize: '10px' }
                                                    }
                                                }
                                            }
                                        }
                                    ]
                                };

                                this.chart = new ApexCharts(this.$refs.revenueChart, options);
                                this.chart.render();

                                this.update($wire.chartData);

                                $wire.watch('chartData', (value) => {
                                    this.update(value);
                                });
                                
                                const observer = new MutationObserver((mutations) => {
                                    mutations.forEach((mutation) => {
                                        if (mutation.attributeName === 'class') {
                                            const darkNow = document.documentElement.classList.contains('dark');
                                            const incomeColor = darkNow ? '#10b981' : '#059669';
                                            const expenseColor = darkNow ? '#ef4444' : '#dc2626';
                                            
                                            this.chart.updateOptions({
                                                chart: { foreColor: darkNow ? '#94a3b8' : '#64748b' },
                                                colors: [incomeColor, expenseColor],
                                                grid: { borderColor: darkNow ? '#1e293b' : '#e2e8f0' },
                                                tooltip: { theme: darkNow ? 'dark' : 'light' },
                                                yaxis: {
                                                    labels: {
                                                        style: { 
                                                            colors: darkNow ? '#94a3b8' : '#64748b'
                                                        }
                                                    }
                                                },
                                                xaxis: {
                                                    labels: {
                                                        style: { 
                                                            colors: darkNow ? '#94a3b8' : '#64748b'
                                                        }
                                                    }
                                                }
                                            });
                                        }
                                    });
                                });
                                observer.observe(document.documentElement, { attributes: true });
                            },
                            update(data) {
                                if(!data || !data.categories || data.categories.length === 0) {
                                    this.hasData = false;
                                    return;
                                }

                                this.hasData = true;

                                // Clean Update - Width is always 100%
                                if (this.$refs.chartContainer) {
                                    this.$refs.chartContainer.style.width = '100%';
                                }
                                
                                const granularity = data.granularity || 'daily';

                                this.chart.updateOptions({
                                    xaxis: { 
                                        categories: data.categories,
                                        labels: {
                                            formatter: function(val, timestamp) {
                                                const d = new Date(timestamp || val);
                                                if(isNaN(d.getTime())) return val;
                                                
                                                if (granularity === 'hourly') {
                                                    const hour = d.getHours().toString().padStart(2, '0');
                                                    return hour + ':00';
                                                } else if (granularity === 'monthly') {
                                                     const month = d.toLocaleString('id-ID', { month: 'short' });
                                                     const year = d.getFullYear();
                                                     return `${month} ${year}`;
                                                } else {
                                                     const day = d.getDate();
                                                     const month = d.toLocaleString('id-ID', { month: 'short' });
                                                     return `${day} ${month}`;
                                                }
                                            },
                                            style: { 
                                                fontSize: '11px',
                                                fontWeight: 500,
                                                colors: this.isDark ? '#94a3b8' : '#64748b'
                                            }
                                        }
                                    },
                                    tooltip: {
                                        x: {
                                            formatter: function(value) {
                                                const d = new Date(value);
                                                if (granularity === 'hourly') {
                                                    return d.toLocaleString('id-ID', { 
                                                        day: 'numeric', 
                                                        month: 'short', 
                                                        hour: '2-digit', 
                                                        minute: '2-digit' 
                                                    });
                                                } else if (granularity === 'monthly') {
                                                    return d.toLocaleString('id-ID', { month: 'long', year: 'numeric' });
                                                } else {
                                                    return d.toLocaleString('id-ID', { 
                                                        day: 'numeric', 
                                                        month: 'long', 
                                                        year: 'numeric' 
                                                    });
                                                }
                                            }
                                        }
                                    },
                                    series: [
                                        { name: 'Pemasukan', data: data.income || [] },
                                        { name: 'Pengeluaran', data: data.expense || [] }
                                    ]
                                });
                            }
                        }" wire:ignore>
                        <!-- Empty State -->
                        <div x-show="!hasData" 
                             x-transition
                             class="absolute inset-0 flex flex-col items-center justify-center bg-slate-50/50 dark:bg-slate-900/20 backdrop-blur-sm rounded-xl z-10">
                            <div class="text-center px-4">
                                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
                                    <i class='bx bx-line-chart text-3xl text-slate-400'></i>
                                </div>
                                <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Belum Ada Data</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 max-w-xs">
                                    Grafik akan muncul setelah ada transaksi pada periode ini
                                </p>
                            </div>
                        </div>

                        <div class="absolute inset-0 overflow-x-auto custom-scroll pb-2">
                            <div x-ref="chartContainer" class="h-full min-w-full transition-all duration-300">
                                <div x-ref="revenueChart" class="w-full h-full"></div>
                            </div>
                        </div>
                        {{-- Hint Overlay for Scroll --}}
                        <div x-show="$refs.chartContainer && $refs.chartContainer.style.width !== '100%'" 
                             class="absolute bottom-2 right-2 pointer-events-none opacity-0 group-hover/chart:opacity-100 transition-opacity bg-black/50 text-white text-[9px] px-2 py-1 rounded-full backdrop-blur-sm z-10">
                            Scroll for more <i class='bx bx-right-arrow-alt align-middle'></i>
                        </div>
                    </div>
                </div>

                {{-- Profitability Sidebar --}}
                <div
                    class="w-full md:w-[260px] p-5 flex flex-col justify-between bg-slate-50/50 dark:bg-slate-800/30 border-l border-slate-100 dark:border-slate-700">
                    <div class="mb-2">
                        <h3 class="font-bold text-[14px] text-slate-800 dark:text-white">Profitabilitas</h3>
                        <p class="text-[10px] text-slate-500 uppercase tracking-widest">Analisis Laba & Beban</p>
                    </div>

                    <div
                        class="bg-emerald-50/80 dark:bg-emerald-500/10 rounded-xl p-4 border border-emerald-100 dark:border-emerald-500/20 relative overflow-hidden group">
                        <div
                            class="absolute -right-3 -bottom-3 text-emerald-500/10 dark:text-emerald-400/10 text-6xl group-hover:scale-110 transition-transform duration-500 pointer-events-none">
                            <i class='bx bx-wallet'></i>
                        </div>
                        <p
                            class="text-[9px] font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-widest mb-1">
                            Laba Bersih</p>

                        <h2 class="text-2xl font-bold text-slate-800 dark:text-white tracking-tight relative z-10">
                            Rp {{ number_format($this->netProfit, 0, ',', '.') }}
                        </h2>

                        <div class="flex items-center gap-2 mt-2 relative z-10">
                            @if($this->profitGrowth > 0)
                                <div
                                    class="flex items-center justify-center bg-emerald-500 text-white rounded-full w-4 h-4 shadow-sm shadow-emerald-500/30">
                                    <i class='bx bx-trending-up text-[10px]'></i>
                                </div>
                                <span
                                    class="text-[11px] font-bold text-emerald-700 dark:text-emerald-400">+{{ $this->profitGrowth }}%</span>
                            @elseif($this->profitGrowth < 0)
                                <div
                                    class="flex items-center justify-center bg-rose-500 text-white rounded-full w-4 h-4 shadow-sm shadow-rose-500/30">
                                    <i class='bx bx-trending-down text-[10px]'></i>
                                </div>
                                <span
                                    class="text-[11px] font-bold text-rose-700 dark:text-rose-400">{{ $this->profitGrowth }}%</span>
                            @else
                                <div
                                    class="flex items-center justify-center bg-slate-400 text-white rounded-full w-4 h-4 shadow-sm">
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
                                <span
                                    class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Omzet</span>
                                <span
                                    class="text-[11px] font-bold text-slate-700 dark:text-white">{{ $this->grossMarginPercent }}%</span>
                            </div>
                            <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-1">
                                <div class="bg-indigo-500 h-1 rounded-full transition-all duration-500"
                                    style="width: {{ min(100, $this->grossMarginPercent) }}%"></div>
                            </div>
                            <p class="text-[9px] text-slate-400 mt-0.5">Rp
                                {{ number_format($this->grossProfit, 0, ',', '.') }}
                            </p>
                        </div>

                        @if($this->otherIncome > 0)
                            <div>
                                @php
                                    $totalIncome = $this->grossProfit + $this->otherIncome;
                                    $otherIncomePercent = $totalIncome > 0 ? min(100, ($this->otherIncome / $totalIncome) * 100) : 0;
                                @endphp
                                <div class="flex justify-between items-end mb-1">
                                    <span
                                        class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Pendapatan
                                        Lain</span>
                                    <span
                                        class="text-[11px] font-bold text-slate-700 dark:text-white">{{ number_format($otherIncomePercent, 1) }}%</span>
                                </div>
                                <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-1">
                                    <div class="bg-emerald-500 h-1 rounded-full transition-all duration-500"
                                        style="width: {{ $otherIncomePercent }}%"></div>
                                </div>
                                <p class="text-[9px] text-slate-400 mt-0.5">Rp
                                    {{ number_format($this->otherIncome, 0, ',', '.') }}
                                </p>
                            </div>
                        @endif

                        <div>
                            @php
                                // Hitung persentase pengeluaran terhadap total pemasukan (gross + other)
                                $totalIncome = $this->grossProfit + $this->otherIncome;
                                $expenseBarPercent = 0;
                                if ($totalIncome > 0) {
                                    $expenseBarPercent = min(100, ($this->operatingExpenses / $totalIncome) * 100);
                                } elseif ($this->operatingExpenses > 0) {
                                    $expenseBarPercent = 100; // Ada pengeluaran tanpa pemasukan
                                }
                            @endphp
                            <div class="flex justify-between items-end mb-1">
                                <span
                                    class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Total
                                    Beban Operasional</span>
                                <span
                                    class="text-[11px] font-bold text-slate-700 dark:text-white">{{ number_format($expenseBarPercent, 1) }}%</span>
                            </div>
                            <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-1">
                                <div class="bg-rose-500 h-1 rounded-full transition-all duration-500"
                                    style="width: {{ $expenseBarPercent }}%"></div>
                            </div>
                            <p class="text-[9px] text-slate-400 mt-0.5">Rp
                                {{ number_format($this->operatingExpenses, 0, ',', '.') }}
                            </p>
                        </div>

                        <p class="text-[9px] text-slate-400 mt-2 italic">*Laba Bersih = Omzet + Pendapatan Lain - Beban
                            Operasional</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-4 flex flex-col gap-4 h-fit">
            {{-- Quick Stats Grid (4 small cards) - Saldo Kasir, Pengajuan, Penagihan, Gantung --}}
            <div class="grid grid-cols-2 gap-4">
                <div
                    class="bg-card dark:bg-darkCard p-3 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 flex flex-col justify-between group hover:border-indigo-200 transition-colors cursor-pointer relative overflow-hidden h-[100px]">
                    <span
                        class="absolute top-2.5 right-2.5 text-[9px] bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-300 px-1.5 py-px rounded font-bold uppercase tracking-wide">Shift
                        1</span>
                    <div class="mb-1">
                        <div
                            class="w-8 h-8 rounded-md bg-indigo-50 dark:bg-indigo-500/10 flex items-center justify-center text-primary transition-all duration-300 group-hover:bg-indigo-500 group-hover:text-white group-hover:shadow-lg group-hover:shadow-indigo-500/40">
                            <i class='bx bx-store-alt text-base'></i>
                        </div>
                    </div>
                    <div>
                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-0.5 leading-none">
                            Saldo Kasir</p>
                        <h6 class="text-[13px] font-bold text-slate-800 dark:text-white leading-tight truncate">Rp
                            {{ number_format($this->cashOnHand, 0, ',', '.') }}
                        </h6>
                    </div>
                </div>
                <div
                    class="bg-card dark:bg-darkCard p-3 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 flex flex-col justify-between group hover:border-amber-200 transition-colors cursor-pointer relative overflow-hidden h-[100px]">
                    <span
                        class="absolute top-2.5 right-2.5 text-[9px] bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400 px-1.5 py-px rounded font-bold uppercase tracking-wide">Urgent</span>
                    <div class="mb-1">
                        <div
                            class="w-8 h-8 rounded-md bg-amber-50 dark:bg-amber-500/10 flex items-center justify-center text-amber-500 transition-all duration-300 group-hover:bg-amber-500 group-hover:text-white group-hover:shadow-lg group-hover:shadow-amber-500/40">
                            <i class='bx bx-file-blank text-base'></i>
                        </div>
                    </div>
                    <div>
                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-0.5 leading-none">
                            Pengajuan</p>
                        <h6 class="text-[13px] font-bold text-slate-800 dark:text-white leading-tight truncate">0 Berkas
                        </h6>
                    </div>
                </div>
                <div
                    class="bg-card dark:bg-darkCard p-3 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 flex flex-col justify-between group hover:border-rose-200 transition-colors cursor-pointer relative overflow-hidden h-[100px]">
                    <span
                        class="absolute top-2.5 right-2.5 text-[9px] bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-400 px-1.5 py-px rounded font-bold uppercase tracking-wide">Hari
                        Ini</span>
                    <div class="mb-1">
                        <div
                            class="w-8 h-8 rounded-md bg-rose-50 dark:bg-rose-500/10 flex items-center justify-center text-rose-500 transition-all duration-300 group-hover:bg-rose-500 group-hover:text-white group-hover:shadow-lg group-hover:shadow-rose-500/40">
                            <i class='bx bx-time-five text-base'></i>
                        </div>
                    </div>
                    <div>
                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-0.5 leading-none">
                            Penagihan</p>
                        <h6 class="text-[13px] font-bold text-slate-800 dark:text-white leading-tight truncate">Rp 0
                        </h6>
                    </div>
                </div>
                <div
                    class="bg-card dark:bg-darkCard p-3 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 flex flex-col justify-between group hover:border-cyan-200 transition-colors cursor-pointer relative overflow-hidden h-[100px]">
                    <span
                        class="absolute top-2.5 right-2.5 text-[9px] bg-cyan-50 text-cyan-600 dark:bg-cyan-500/10 dark:text-cyan-400 px-1.5 py-px rounded font-bold uppercase tracking-wide">Active</span>
                    <div class="mb-1">
                        <div
                            class="w-8 h-8 rounded-md bg-cyan-50 dark:bg-cyan-500/10 flex items-center justify-center text-cyan-500 transition-all duration-300 group-hover:bg-cyan-500 group-hover:text-white group-hover:shadow-lg group-hover:shadow-cyan-500/40">
                            <i class='bx bx-receipt text-base'></i>
                        </div>
                    </div>
                    <div>
                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-0.5 leading-none">
                            Gantung</p>
                        <h6 class="text-[13px] font-bold text-slate-800 dark:text-white leading-tight truncate">0 Nota
                        </h6>
                    </div>
                </div>
            </div>

            {{-- Stats Cards (Product, Member, etc) - COMMENTED OUT FOR SAFETY --}}
            {{--
            <div class="grid grid-cols-2 gap-4">
                <div
                    class="bg-card dark:bg-darkCard p-3 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700">
                    <div
                        class="w-8 h-8 rounded-md bg-indigo-50 dark:bg-indigo-500/10 flex items-center justify-center text-primary mb-2">
                        <i class='bx bx-package text-base'></i>
                    </div>
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Produk Aktif</p>
                    <h6 class="text-xl font-bold text-slate-800 dark:text-white">{{ $this->totalProducts }}</h6>
                </div>
                <div
                    class="bg-card dark:bg-darkCard p-3 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700">
                    <div
                        class="w-8 h-8 rounded-md bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center text-emerald-500 mb-2">
                        <i class='bx bx-user text-base'></i>
                    </div>
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Member Aktif</p>
                    <h6 class="text-xl font-bold text-slate-800 dark:text-white">{{ $this->totalMembers }}</h6>
                </div>
                <div
                    class="bg-card dark:bg-darkCard p-3 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700">
                    <div
                        class="w-8 h-8 rounded-md bg-amber-50 dark:bg-amber-500/10 flex items-center justify-center text-amber-500 mb-2">
                        <i class='bx bx-error-circle text-base'></i>
                    </div>
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Stok Kritis</p>
                    <h6 class="text-xl font-bold text-slate-800 dark:text-white">{{ $this->lowStockProducts->count() }}
                    </h6>
                </div>
                <div
                    class="bg-card dark:bg-darkCard p-3 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700">
                    <div
                        class="w-8 h-8 rounded-md bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center text-blue-500 mb-2">
                        <i class='bx bx-category text-base'></i>
                    </div>
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Kategori</p>
                    <h6 class="text-xl font-bold text-slate-800 dark:text-white">{{
                        \App\Models\Category::where('isActive', true)->count() }}</h6>
                </div>
            </div>
            --}

            {{-- Tabs: Stok Kritis / Produk Terlaris --}}
            <div class="bg-card dark:bg-darkCard rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 p-5 flex flex-col h-full min-h-[280px]"
                x-data="{ activeTab: 'stok' }">
                {{-- Tabs Header --}}
                <div
                    class="flex items-center gap-2 md:gap-4 border-b border-slate-100 dark:border-slate-700 pb-3 mb-3 overflow-x-auto">
                    <button @click="activeTab = 'stok'"
                        :class="activeTab === 'stok' ? 'text-slate-800 dark:text-white border-rose-500' : 'text-slate-400 dark:text-slate-500 border-transparent hover:text-slate-600 dark:hover:text-slate-300'"
                        class="text-[11px] md:text-[13px] font-bold border-b-2 pb-3 -mb-3.5 transition-colors whitespace-nowrap shrink-0">
                        <i class='bx bx-error-circle text-rose-500 mr-1'></i> Stok Kritis
                    </button>
                    <button @click="activeTab = 'laris'"
                        :class="activeTab === 'laris' ? 'text-slate-800 dark:text-white border-amber-500' : 'text-slate-400 dark:text-slate-500 border-transparent hover:text-slate-600 dark:hover:text-slate-300'"
                        class="text-[11px] md:text-[13px] font-bold border-b-2 pb-3 -mb-3.5 transition-colors whitespace-nowrap shrink-0">
                        <i class='bx bx-trophy text-amber-500 mr-1'></i> Produk Terlaris
                    </button>
                    <div class="flex-1 text-right">
                        <a href="{{ route('admin.products') }}"
                            class="text-[9px] md:text-[10px] text-primary font-bold hover:underline whitespace-nowrap">Lihat
                            Semua</a>
                    </div>
                </div>

                {{-- Content: Stok Menipis --}}
                <div x-show="activeTab === 'stok'" class="flex-1 space-y-4 overflow-y-auto pr-1">
                    @forelse($this->lowStockProducts as $product)
                        <div>
                            <div class="flex justify-between items-end mb-1">
                                <div class="flex items-center gap-2">
                                    <div
                                        class="w-6 h-6 rounded bg-slate-100 dark:bg-slate-700 flex items-center justify-center text-[10px]">
                                        {{ $product->category?->icon ?? '📦' }}
                                    </div>
                                    <div>
                                        <h6 class="text-[12px] font-semibold text-slate-800 dark:text-white leading-none">
                                            {{ $product->name }}
                                        </h6>
                                        <p class="text-[9px] text-slate-400">{{ $product->category?->name ?? '-' }}</p>
                                    </div>
                                </div>
                                <span
                                    class="text-[11px] font-bold {{ $product->stock <= 5 ? 'text-rose-500' : 'text-amber-500' }}">
                                    Sisa {{ $product->stock }}
                                </span>
                            </div>
                            <div class="w-full bg-slate-100 dark:bg-slate-700 rounded-full h-1">
                                <div class="{{ $product->stock <= 5 ? 'bg-rose-500' : 'bg-amber-500' }} h-1 rounded-full"
                                    style="width: {{ min(100, ($product->stock / max($product->threshold, 1)) * 50) }}%">
                                </div>
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
                            <div
                                class="w-8 h-8 rounded-lg {{ $index === 0 ? 'bg-amber-50 dark:bg-amber-500/10 text-amber-500 ring-1 ring-amber-100 dark:ring-amber-500/20' : 'bg-slate-50 dark:bg-slate-700 text-slate-500 ring-1 ring-slate-200 dark:ring-slate-600' }} flex items-center justify-center font-bold text-[12px]">
                                #{{ $index + 1 }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <h6 class="text-[12px] font-semibold text-slate-800 dark:text-white leading-none truncate">
                                    {{ $product->name }}
                                </h6>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-[9px] text-slate-400">{{ $product->category?->name ?? '-' }}</span>
                                </div>
                            </div>
                            <div class="text-right">
                                <span
                                    class="block text-[13px] font-bold text-slate-800 dark:text-white">{{ $product->total_sold }}</span>
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
    <div
        class="bg-card dark:bg-darkCard rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">
        <div class="p-5 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
            <h3 class="font-bold text-base text-slate-800 dark:text-white">Transaksi Terkini</h3>
            <a href="{{ route('admin.transactions') }}" class="text-sm text-primary font-medium hover:underline">Lihat
                Semua</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600 dark:text-slate-400">
                <thead
                    class="bg-slate-50 dark:bg-slate-700/50 text-xs uppercase font-semibold text-slate-500 dark:text-slate-400">
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
                                        <div
                                            class="w-6 h-6 rounded-full bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center text-primary dark:text-indigo-400 font-bold text-[10px]">
                                            {{ strtoupper(substr($trx->member->name, 0, 2)) }}
                                        </div>
                                        <span
                                            class="font-medium text-slate-900 dark:text-white text-xs">{{ $trx->member->name }}</span>
                                    </div>
                                @else
                                    <span class="text-slate-400 text-xs">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-3 text-xs">{{ $trx->date?->format('d M Y, H:i') }}</td>
                            <td class="px-6 py-3 font-bold text-slate-900 dark:text-white text-xs">Rp
                                {{ number_format($trx->totalAmount, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-3">
                                <span
                                    class="{{ $trx->status === 'COMPLETED' ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400' : 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400' }} px-2 py-1 rounded-full text-[10px] font-semibold">
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