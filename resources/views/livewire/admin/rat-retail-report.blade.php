<div class="px-4 py-6 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-6">
    {{-- Header Section --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-150 dark:border-gray-700 p-6 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <div class="flex items-center gap-2 text-indigo-600 dark:text-indigo-400 font-semibold text-sm uppercase tracking-wider">
                <i class='bx bx-spreadsheet text-lg'></i>
                <span>RAT Akuntansi</span>
            </div>
            <h1 class="text-2xl font-extrabold text-gray-900 dark:text-gray-100 mt-1">Neraca Hasil Usaha Retail</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Data penjualan, harga pokok pembelian (HPP), omzet, dan margin keuntungan bersih per bulan.</p>
        </div>
        
        {{-- Filter & Actions --}}
        <div class="flex flex-wrap items-center gap-3">
            <div class="flex items-center bg-gray-50 dark:bg-gray-700/50 rounded-lg p-1 border border-gray-200 dark:border-gray-600">
                <label for="yearFilter" class="text-xs text-gray-500 dark:text-gray-400 font-bold px-2 uppercase">Tahun</label>
                <select id="yearFilter" wire:model.live="selectedYear" class="bg-white dark:bg-gray-700 border-0 text-gray-900 dark:text-gray-100 text-xs rounded-md focus:ring-2 focus:ring-indigo-500 p-1.5 font-semibold shadow-sm">
                    <option value="All">Semua</option>
                    @foreach($availableYears as $yr)
                        <option value="{{ $yr }}">{{ $yr }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- CSV Uploader & KPI Cards Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        {{-- CSV Uploader Card --}}
        <div class="lg:col-span-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-150 dark:border-gray-700 p-5 flex flex-col justify-between">
            <div>
                <h3 class="font-bold text-gray-900 dark:text-gray-100 text-sm flex items-center gap-1.5">
                    <i class='bx bx-cloud-upload text-indigo-600 text-base'></i>
                    Import Laporan CSV Baru
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1.5 leading-relaxed">
                    Unggah lembar laporan transaksi retail (.csv) untuk memperbarui data neraca hasil usaha. Data lama akan diperbarui secara otomatis.
                </p>
            </div>
            
            <form wire:submit.prevent="importCsv" class="mt-4 space-y-3">
                <div class="relative border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-lg p-4 text-center hover:border-indigo-500 dark:hover:border-indigo-400 transition-colors">
                    <input type="file" wire:model="csvFile" accept=".csv" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" />
                    <div class="space-y-1.5 text-center">
                        <i class='bx bxs-file-blank text-3xl text-gray-400 dark:text-gray-500'></i>
                        <div class="text-xs font-semibold text-indigo-600 dark:text-indigo-400">
                            @if($csvFile)
                                {{ $csvFile->getClientOriginalName() }}
                            @else
                                Pilih berkas CSV
                            @endif
                        </div>
                        <p class="text-[10px] text-gray-400">Maks. 10 MB (.csv)</p>
                    </div>
                </div>
                
                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-lg shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" wire:loading.attr="disabled">
                    <i class='bx bx-check-double text-sm'></i>
                    <span wire:loading.remove>Proses & Perbarui Data</span>
                    <span wire:loading>Memproses...</span>
                </button>
            </form>
        </div>

        {{-- KPI Cards Column --}}
        <div class="lg:col-span-8 grid grid-cols-1 sm:grid-cols-2 gap-4">
            {{-- Card 1: Harga Beli (HPP) --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-150 dark:border-gray-700 p-5 border-l-4 border-l-slate-400 dark:border-l-slate-500 flex items-center justify-between">
                <div class="space-y-1">
                    <p class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Harga Pokok (HPP)</p>
                    <p class="text-xl font-black text-gray-900 dark:text-gray-100">
                        Rp {{ number_format($kpi['total_harga_beli'], 0, ',', '.') }}
                    </p>
                    <p class="text-[10px] text-gray-400">Total belanja modal barang</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-slate-50 dark:bg-slate-800 flex items-center justify-center text-slate-500">
                    <i class='bx bx-shopping-bag text-xl'></i>
                </div>
            </div>

            {{-- Card 2: Harga Jual (Omzet) --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-150 dark:border-gray-700 p-5 border-l-4 border-l-indigo-500 dark:border-l-indigo-400 flex items-center justify-between">
                <div class="space-y-1">
                    <p class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Total Penjualan (Omzet)</p>
                    <p class="text-xl font-black text-gray-900 dark:text-gray-100">
                        Rp {{ number_format($kpi['total_harga_jual'], 0, ',', '.') }}
                    </p>
                    <p class="text-[10px] text-gray-400">Pendapatan kotor retail</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-indigo-50 dark:bg-indigo-950 flex items-center justify-center text-indigo-500 dark:text-indigo-400">
                    <i class='bx bx-trending-up text-xl'></i>
                </div>
            </div>

            {{-- Card 3: Keuntungan Bersih --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-150 dark:border-gray-700 p-5 border-l-4 border-l-emerald-500 dark:border-l-emerald-400 flex items-center justify-between">
                <div class="space-y-1">
                    <p class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Hasil Usaha (Keuntungan)</p>
                    <p class="text-xl font-black text-emerald-600 dark:text-emerald-400">
                        Rp {{ number_format($kpi['total_keuntungan'], 0, ',', '.') }}
                    </p>
                    <p class="text-[10px] text-gray-400">Total laba kotor retail</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-emerald-50 dark:bg-emerald-950 flex items-center justify-center text-emerald-500 dark:text-emerald-400">
                    <i class='bx bx-dollar-circle text-xl'></i>
                </div>
            </div>

            {{-- Card 4: Margin Margin --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-150 dark:border-gray-700 p-5 border-l-4 border-l-blue-500 dark:border-l-blue-400 flex items-center justify-between">
                <div class="space-y-1">
                    <p class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Margin Keuntungan</p>
                    <p class="text-xl font-black text-blue-600 dark:text-blue-400">
                        @if($kpi['total_harga_jual'] > 0)
                            {{ number_format(($kpi['total_keuntungan'] / $kpi['total_harga_jual']) * 100, 2, ',', '.') }}%
                        @else
                            0%
                        @endif
                    </p>
                    <p class="text-[10px] text-gray-400">Rasio margin terhadap omzet</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-blue-50 dark:bg-blue-950 flex items-center justify-center text-blue-500 dark:text-blue-400">
                    <i class='bx bx-pie-chart-alt text-xl'></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Financial Trend Chart Card --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-150 dark:border-gray-700 p-5"
         x-data="{
             chart: null,
             chartData: @js($chartData),
             getColors() {
                 const isDark = document.documentElement.classList.contains('dark');
                 return {
                     text: isDark ? '#94a3b8' : '#64748b',
                     grid: isDark ? '#1e293b' : '#e2e8f0',
                     tooltipTheme: isDark ? 'dark' : 'light'
                 };
             },
             init() {
                 const c = this.getColors();
                 const options = {
                     series: [
                         {
                             name: 'Total Omzet (Jual)',
                             type: 'column',
                             data: this.chartData.omzet
                         },
                         {
                             name: 'Total HPP (Beli)',
                             type: 'column',
                             data: this.chartData.hpp
                         },
                         {
                             name: 'Total Keuntungan',
                             type: 'line',
                             data: this.chartData.keuntungan
                         }
                     ],
                     chart: {
                         height: 320,
                         type: 'line',
                         toolbar: { show: false },
                         fontFamily: 'Inter',
                         foreColor: c.text
                     },
                     stroke: {
                         width: [0, 0, 3],
                         curve: 'smooth'
                     },
                     colors: ['#6366f1', '#94a3b8', '#10b981'],
                     fill: {
                         opacity: [0.85, 0.85, 1],
                     },
                     xaxis: {
                         categories: this.chartData.categories,
                         axisBorder: { show: false },
                         axisTicks: { show: false }
                     },
                     grid: {
                         borderColor: c.grid,
                         strokeDashArray: 4
                     },
                     legend: {
                         position: 'top',
                         horizontalAlign: 'right'
                     },
                     tooltip: {
                         theme: c.tooltipTheme,
                         y: {
                             formatter: function (value) {
                                 return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                             }
                         }
                     }
                 };

                 this.chart = new ApexCharts(this.$refs.fluctuationChart, options);
                 this.chart.render();

                 // Watch for Livewire changes
                 this.$watch('chartData', (value) => {
                     if (this.chart) {
                         this.chart.updateOptions({
                             xaxis: { categories: value.categories }
                         });
                         this.chart.updateSeries([
                             { name: 'Total Omzet (Jual)', data: value.omzet },
                             { name: 'Total HPP (Beli)', data: value.hpp },
                             { name: 'Total Keuntungan', data: value.keuntungan }
                         ]);
                     }
                 });

                 // Support Dark Mode toggle observer
                 const observer = new MutationObserver(() => {
                     const nc = this.getColors();
                     if (this.chart) {
                         this.chart.updateOptions({
                             chart: { foreColor: nc.text },
                             grid: { borderColor: nc.grid },
                             tooltip: { theme: nc.tooltipTheme }
                         });
                     }
                 });
                 observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
             }
         }"
         x-effect="chartData = @js($chartData)"
         wire:ignore>
        <div class="flex items-center justify-between mb-4 border-b border-gray-150 dark:border-gray-700 pb-3">
            <div>
                <h3 class="font-bold text-gray-900 dark:text-gray-100 text-sm">Visualisasi Tren Keuangan</h3>
                <p class="text-xs text-gray-400 mt-0.5">Perbandingan grafik Omzet (Penjualan), HPP (Pembelian), dan Laba Bersih secara bulanan.</p>
            </div>
        </div>
        <div x-ref="fluctuationChart" class="w-full"></div>
    </div>

    <style>
        .scrollbar-none::-webkit-scrollbar {
            display: none;
        }
        .scrollbar-none {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>

    {{-- Month Selector Horizontal Scroll Section --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-150 dark:border-gray-700 p-5">
        <div class="flex items-center justify-between mb-4 border-b border-gray-150 dark:border-gray-700 pb-3">
            <div>
                <h3 class="font-bold text-gray-900 dark:text-gray-100 text-sm">Pilih Bulan Transaksi</h3>
                <p class="text-xs text-gray-400 mt-0.5">Pilih salah satu bulan di bawah untuk memuat rincian transaksi harian secara detail.</p>
            </div>
            <div class="text-xs bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 font-bold px-3 py-1 rounded-full">
                {{ count($summaries) }} Bulan Tersedia
            </div>
        </div>

        {{-- Horizontal Scrollable Cards List --}}
        <div class="flex gap-4 overflow-x-auto pb-2 scrollbar-none snap-x snap-mandatory">
            @forelse($summaries as $summary)
                <button wire:click="selectMonth('{{ $summary['month_key'] }}')"
                        class="flex-none w-60 snap-start p-4 rounded-xl border text-left transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 hover:scale-[1.01]
                               {{ $selectedMonth === $summary['month_key'] 
                                  ? 'bg-indigo-600 border-indigo-600 text-white shadow-lg shadow-indigo-100 dark:shadow-none' 
                                  : 'bg-gray-50 dark:bg-gray-700/30 border-gray-200 dark:border-gray-600 hover:border-indigo-400 text-gray-800 dark:text-gray-100' }}">
                    <div class="font-bold text-sm truncate {{ $selectedMonth === $summary['month_key'] ? 'text-white' : 'text-gray-900 dark:text-gray-100' }}">
                        {{ $summary['month_name'] }}
                    </div>
                    <div class="text-[10px] mt-0.5 {{ $selectedMonth === $summary['month_key'] ? 'text-indigo-200' : 'text-gray-400' }}">
                        {{ number_format($summary['item_count']) }} Item Terjual
                    </div>

                    <div class="mt-4 space-y-1.5 text-xs">
                        <div class="flex justify-between">
                            <span class="{{ $selectedMonth === $summary['month_key'] ? 'text-indigo-200' : 'text-gray-400' }}">Total HPP:</span>
                            <span class="font-semibold">Rp {{ number_format($summary['total_harga_beli'], 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="{{ $selectedMonth === $summary['month_key'] ? 'text-indigo-200' : 'text-gray-400' }}">Total Omzet:</span>
                            <span class="font-bold">Rp {{ number_format($summary['total_harga_jual'], 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between pt-1.5 border-t {{ $selectedMonth === $summary['month_key'] ? 'border-indigo-500/50' : 'border-gray-200 dark:border-gray-600' }}">
                            <span class="{{ $selectedMonth === $summary['month_key'] ? 'text-indigo-200' : 'text-gray-400' }}">Laba Bersih:</span>
                            <span class="font-black {{ $selectedMonth === $summary['month_key'] ? 'text-emerald-300' : 'text-emerald-600 dark:text-emerald-400' }}">
                                Rp {{ number_format($summary['total_keuntungan'], 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </button>
            @empty
                <div class="w-full py-8 text-center text-gray-400 dark:text-gray-500 text-xs font-semibold">
                    Tidak ada ringkasan bulanan tersedia. Silakan unggah file CSV terlebih dahulu.
                </div>
            @endforelse
        </div>
    </div>

    {{-- Details Panel Section (Full Width) --}}
    @if($selectedMonth)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-150 dark:border-gray-700 overflow-hidden">
            {{-- Detail Header --}}
            <div class="p-5 border-b border-gray-150 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h3 class="font-bold text-gray-900 dark:text-gray-100 text-base">
                        Detail Transaksi: {{ $this->getMonthName(substr($selectedMonth, 5, 2)) }} {{ substr($selectedMonth, 0, 4) }}
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Rincian kuantitas, HPP, harga jual, total omzet, dan margin bersih per item produk.</p>
                </div>
                <button wire:click="clearSelectedMonth" class="inline-flex items-center gap-1.5 text-xs text-gray-500 hover:text-rose-600 font-bold transition-colors">
                    <i class='bx bx-x-circle text-base'></i> Tutup Detail
                </button>
            </div>

            {{-- Search Filter --}}
            <div class="p-4 border-b border-gray-150 dark:border-gray-700 bg-white dark:bg-gray-800">
                <div class="relative rounded-md shadow-sm max-w-md">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <i class='bx bx-search text-gray-400 text-sm'></i>
                    </div>
                    <input type="text" 
                           wire:model.live.debounce.300ms="searchDetail" 
                           placeholder="Cari nama produk / barang di bulan ini..." 
                           class="block w-full rounded-lg border-gray-200 dark:border-gray-600 pl-10 text-xs bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </div>

            {{-- Details Table --}}
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left align-middle">
                    <thead class="text-[10px] text-gray-400 font-bold uppercase tracking-wider bg-gray-50 dark:bg-gray-700/50 border-b border-gray-150 dark:border-gray-700">
                        <tr>
                            <th class="px-4 py-4">Tanggal</th>
                            <th class="px-4 py-4">Nama Produk</th>
                            <th class="px-4 py-4 text-right">Harga Beli (Satuan)</th>
                            <th class="px-4 py-4 text-right">Harga Jual (Satuan)</th>
                            <th class="px-4 py-4 text-center">Qty Terjual</th>
                            <th class="px-4 py-4 text-right">Total HPP (Beli)</th>
                            <th class="px-4 py-4 text-right">Total Omzet (Jual)</th>
                            <th class="px-4 py-4 text-right text-indigo-600 dark:text-indigo-400">Total Keuntungan</th>
                            <th class="px-4 py-4 text-right">Margin (%)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($paginatedDetails as $item)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/20">
                                <td class="px-4 py-3.5 text-[10px] text-gray-400 dark:text-gray-500 whitespace-nowrap">
                                    {{ $item['tanggal'] }}
                                </td>
                                <td class="px-4 py-3.5 font-bold text-gray-900 dark:text-gray-100 max-w-[200px] truncate" title="{{ $item['nama_barang'] }}">
                                    {{ $item['nama_barang'] }}
                                </td>
                                <td class="px-4 py-3.5 text-right text-gray-600 dark:text-gray-300 whitespace-nowrap">
                                    Rp {{ number_format($item['harga_beli_satuan'], 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3.5 text-right text-gray-600 dark:text-gray-300 whitespace-nowrap">
                                    Rp {{ number_format($item['harga_jual_satuan'], 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3.5 text-center text-gray-700 dark:text-gray-300 whitespace-nowrap">
                                    <span class="font-semibold text-gray-900 dark:text-white">{{ number_format($item['quantity']) }}</span>
                                    <span class="text-[10px] text-gray-400">{{ $item['satuan'] }}</span>
                                </td>
                                <td class="px-4 py-3.5 text-right font-medium text-gray-900 dark:text-gray-100 whitespace-nowrap">
                                    Rp {{ number_format($item['total_harga_beli'], 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3.5 text-right font-medium text-gray-900 dark:text-gray-100 whitespace-nowrap">
                                    Rp {{ number_format($item['total_harga_jual'], 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3.5 text-right whitespace-nowrap">
                                    @if($item['total_keuntungan'] > 0)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 dark:bg-emerald-950/30 text-emerald-600 dark:text-emerald-400">
                                            +Rp {{ number_format($item['total_keuntungan'], 0, ',', '.') }}
                                        </span>
                                    @elseif($item['total_keuntungan'] < 0)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 dark:bg-rose-950/30 text-rose-600 dark:text-rose-400">
                                            Rp {{ number_format($item['total_keuntungan'], 0, ',', '.') }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-slate-50 dark:bg-slate-950/30 text-slate-500 dark:text-slate-400">
                                            Rp 0
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 text-right font-semibold whitespace-nowrap {{ $item['persentase_keuntungan'] > 0 ? 'text-emerald-600 dark:text-emerald-400' : ($item['persentase_keuntungan'] < 0 ? 'text-rose-600 dark:text-rose-400' : 'text-gray-500') }}">
                                    {{ number_format($item['persentase_keuntungan'], 2, ',', '.') }}%
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-4 py-12 text-center text-gray-400 dark:text-gray-500">
                                    Tidak ada transaksi barang yang sesuai dengan pencarian Anda.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination Section --}}
            @if($paginatedDetails->hasPages())
                <div class="px-5 py-4 border-t border-gray-150 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                    {{ $paginatedDetails->links() }}
                </div>
            @endif
        </div>
    @else
        <div class="bg-slate-50 dark:bg-gray-800/20 rounded-xl border-2 border-dashed border-gray-200 dark:border-gray-700 p-16 text-center">
            <div class="w-12 h-12 rounded-full bg-indigo-50 dark:bg-indigo-950 flex items-center justify-center text-indigo-500 dark:text-indigo-400 mx-auto mb-4">
                <i class='bx bx-mouse-pointer text-2xl animate-bounce'></i>
            </div>
            <h3 class="text-sm font-bold text-gray-900 dark:text-gray-100">Bulan Belum Terpilih</h3>
            <p class="text-xs text-gray-400 mt-1.5 max-w-sm mx-auto leading-relaxed">
                Silakan pilih salah satu bulan di atas untuk menganalisis rincian produk, HPP, omzet, dan margin keuntungan secara detail.
            </p>
        </div>
    @endif
</div>
