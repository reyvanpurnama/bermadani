<div class="p-6 space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-slate-200">Executive Dashboard</h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm">Analisis Kinerja dan Kesehatan Finansial Koperasi</p>
        </div>
        <div>
            <select wire:model.live="selectedYear" class="bg-white dark:bg-zinc-800 border border-slate-300 dark:border-zinc-700 text-slate-700 dark:text-slate-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                    <option value="{{ $y }}">{{ $y }}</option>
                @endfor
            </select>
        </div>
    </div>

    <!-- Row 1: KPI Cards -->
    <div class="flex overflow-x-auto pb-4 sm:pb-0 sm:grid sm:grid-cols-2 lg:grid-cols-5 gap-4">
        @php
            $kpis = [
                ['label' => 'Total Aset', 'key' => 'total_aset', 'icon' => 'bx-building-house', 'color' => 'indigo', 'is_rupiah' => true],
                ['label' => 'Total Pembiayaan', 'key' => 'total_pembiayaan', 'icon' => 'bx-wallet', 'color' => 'emerald', 'is_rupiah' => true],
                ['label' => 'SHU Berjalan', 'key' => 'shu', 'icon' => 'bx-line-chart', 'color' => 'amber', 'is_rupiah' => true],
                ['label' => 'Jumlah Anggota', 'key' => 'jumlah_anggota', 'icon' => 'bx-group', 'color' => 'teal', 'is_rupiah' => false],
                ['label' => 'Kas & Bank', 'key' => 'kas_bank', 'icon' => 'bx-money', 'color' => 'rose', 'is_rupiah' => true],
            ];
        @endphp

        @foreach($kpis as $kpi)
            @php
                $data = $kpiData[$kpi['key']] ?? ['value' => 0, 'yoy' => 0];
                $val = $data['value'];
                $yoy = $data['yoy'];
                
                $colorMap = [
                    'indigo' => 'bg-indigo-500 text-indigo-500',
                    'emerald' => 'bg-emerald-500 text-emerald-500',
                    'amber' => 'bg-amber-500 text-amber-500',
                    'teal' => 'bg-teal-500 text-teal-500',
                    'rose' => 'bg-rose-500 text-rose-500',
                ];
                $borderClasses = match($kpi['color']) {
                    'indigo' => 'border-l-indigo-500',
                    'emerald' => 'border-l-emerald-500',
                    'amber' => 'border-l-amber-500',
                    'teal' => 'border-l-teal-500',
                    'rose' => 'border-l-rose-500',
                    default => 'border-l-indigo-500'
                };
            @endphp
            <div class="min-w-[240px] sm:min-w-0 bg-white dark:bg-zinc-800 rounded-2xl shadow-sm border border-slate-200 dark:border-zinc-700 p-4 border-l-4 {{ $borderClasses }}">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-slate-500 dark:text-slate-400 text-sm font-medium">{{ $kpi['label'] }}</p>
                        <h3 class="text-xl font-bold text-slate-800 dark:text-slate-200 mt-1">
                            {{ $kpi['is_rupiah'] ? 'Rp ' . number_format($val, 0, ',', '.') : number_format($val, 0, ',', '.') }}
                        </h3>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-slate-50 dark:bg-zinc-700 flex items-center justify-center">
                        <i class="bx {{ $kpi['icon'] }} text-xl {{ explode(' ', $colorMap[$kpi['color']])[1] }}"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm">
                    @if($yoy > 0)
                        <i class="bx bx-trending-up text-emerald-500 mr-1"></i>
                        <span class="text-emerald-500 font-medium">{{ number_format($yoy, 1, ',', '.') }}%</span>
                    @elseif($yoy < 0)
                        <i class="bx bx-trending-down text-rose-500 mr-1"></i>
                        <span class="text-rose-500 font-medium">{{ number_format(abs($yoy), 1, ',', '.') }}%</span>
                    @else
                        <i class="bx bx-minus text-slate-400 mr-1"></i>
                        <span class="text-slate-400 font-medium">0%</span>
                    @endif
                    <span class="text-slate-400 dark:text-slate-500 ml-2">vs tahun lalu</span>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Row 2: Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Komposisi Aset -->
        <div class="bg-white dark:bg-zinc-800 rounded-2xl shadow-sm border border-slate-200 dark:border-zinc-700 p-5">
            <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-300 mb-4">Komposisi Aset</h3>
            <div id="chart-aset" class="w-full h-[250px]" wire:ignore></div>
        </div>

        <!-- Komposisi Pendapatan -->
        <div class="bg-white dark:bg-zinc-800 rounded-2xl shadow-sm border border-slate-200 dark:border-zinc-700 p-5">
            <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-300 mb-4">Komposisi Pendapatan</h3>
            <div id="chart-pendapatan" class="w-full h-[250px]" wire:ignore></div>
        </div>

        <!-- Komposisi Beban -->
        <div class="bg-white dark:bg-zinc-800 rounded-2xl shadow-sm border border-slate-200 dark:border-zinc-700 p-5 lg:col-span-2">
            <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-300 mb-4">Komposisi Beban Utama</h3>
            <div id="chart-beban" class="w-full h-[250px]" wire:ignore></div>
        </div>
    </div>

    <!-- Row 3: NPF & Flow -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- NPF Gauge -->
        <div class="bg-white dark:bg-zinc-800 rounded-2xl shadow-sm border border-slate-200 dark:border-zinc-700 p-5">
            <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-300 mb-4">Rasio NPF (Non-Performing Financing)</h3>
            <div id="chart-npf" class="w-full h-[200px]" wire:ignore></div>
            <div class="text-center mt-2 text-sm text-slate-500 dark:text-slate-400">
                Ambang Batas Sehat: <span class="font-medium text-emerald-500">&lt; 5%</span>
            </div>
        </div>

        <!-- Pertumbuhan Simpanan -->
        <div class="bg-white dark:bg-zinc-800 rounded-2xl shadow-sm border border-slate-200 dark:border-zinc-700 p-5 lg:col-span-2">
            <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-300 mb-4">Pertumbuhan Simpanan Anggota</h3>
            <div id="chart-simpanan" class="w-full h-[200px]" wire:ignore></div>
        </div>
    </div>

    <!-- Row 4: Trend & Cashflow -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Tren SHU -->
        <div class="bg-white dark:bg-zinc-800 rounded-2xl shadow-sm border border-slate-200 dark:border-zinc-700 p-5">
            <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-300 mb-4">Tren SHU 5 Tahun Terakhir</h3>
            <div id="chart-tren-shu" class="w-full h-[250px]" wire:ignore></div>
        </div>

        <!-- Arus Kas -->
        <div class="bg-white dark:bg-zinc-800 rounded-2xl shadow-sm border border-slate-200 dark:border-zinc-700 p-5">
            <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-300 mb-4">Arus Kas (Operasi, Investasi, Pendanaan)</h3>
            <div id="chart-arus-kas" class="w-full h-[250px]" wire:ignore></div>
        </div>
    </div>

    <!-- Row 5: Health Scorecard -->
    <div class="bg-white dark:bg-zinc-800 rounded-2xl shadow-sm border border-slate-200 dark:border-zinc-700 p-5">
        <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-300 mb-4">Scorecard Kesehatan Koperasi (PEARLS/CAMEL)</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-slate-500 bg-slate-50 dark:bg-zinc-900/50 dark:text-slate-400 uppercase">
                    <tr>
                        <th class="px-4 py-3 rounded-l-lg">Indikator</th>
                        <th class="px-4 py-3 text-right">Nilai Aktual</th>
                        <th class="px-4 py-3 text-center">Standar</th>
                        <th class="px-4 py-3 rounded-r-lg">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-zinc-700">
                    @foreach($healthScorecard as $score)
                    <tr class="hover:bg-slate-50 dark:hover:bg-zinc-700/50">
                        <td class="px-4 py-3 font-medium text-slate-700 dark:text-slate-200">{{ $score['name'] ?? 'Indikator' }}</td>
                        <td class="px-4 py-3 text-right">{{ $score['value'] ?? '0' }}%</td>
                        <td class="px-4 py-3 text-center text-slate-500">{{ $score['target'] ?? '> 0' }}%</td>
                        <td class="px-4 py-3 text-left">
                            @php
                                $status = $score['status'] ?? 'CUKUP';
                                $badgeClass = match($status) {
                                    'BAIK' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400',
                                    'CUKUP' => 'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-400',
                                    'KURANG' => 'bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-400',
                                    default => 'bg-slate-100 text-slate-700 dark:bg-zinc-700 dark:text-slate-300'
                                };
                            @endphp
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $badgeClass }}">
                                {{ $status }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                    @if(empty($healthScorecard))
                    <tr>
                        <td colspan="4" class="px-4 py-6 text-center text-slate-500">Data scorecard belum tersedia untuk tahun ini.</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    @script
    <script>
        const isDark = document.documentElement.classList.contains('dark');
        const textColor = isDark ? '#cbd5e1' : '#475569';
        const gridColor = isDark ? '#3f3f46' : '#e2e8f0';
        
        let charts = {};

        function formatRupiah(val) {
            return "Rp " + new Intl.NumberFormat('id-ID').format(val);
        }

        function initCharts(data) {
            // Aset Donut
            if(charts.aset) charts.aset.destroy();
            charts.aset = new ApexCharts(document.querySelector("#chart-aset"), {
                series: data.komposisi_aset.series,
                chart: { type: 'donut', height: 250, background: 'transparent' },
                labels: data.komposisi_aset.labels,
                colors: ['#14B8A6', '#6366F1', '#F59E0B', '#94A3B8'],
                stroke: { show: false },
                dataLabels: { enabled: false },
                legend: { position: 'bottom', labels: { colors: textColor } },
                tooltip: { y: { formatter: formatRupiah } },
                theme: { mode: isDark ? 'dark' : 'light' }
            });
            charts.aset.render();

            // Pendapatan Donut
            if(charts.pendapatan) charts.pendapatan.destroy();
            charts.pendapatan = new ApexCharts(document.querySelector("#chart-pendapatan"), {
                series: data.komposisi_pendapatan.series,
                chart: { type: 'donut', height: 250, background: 'transparent' },
                labels: data.komposisi_pendapatan.labels,
                colors: ['#10B981', '#6366F1', '#F59E0B'],
                stroke: { show: false },
                dataLabels: { enabled: false },
                legend: { position: 'bottom', labels: { colors: textColor } },
                tooltip: { y: { formatter: formatRupiah } },
                theme: { mode: isDark ? 'dark' : 'light' }
            });
            charts.pendapatan.render();

            // Beban Bar
            if(charts.beban) charts.beban.destroy();
            charts.beban = new ApexCharts(document.querySelector("#chart-beban"), {
                series: [{ name: 'Beban', data: data.komposisi_beban.series }],
                chart: { type: 'bar', height: 250, background: 'transparent', toolbar: { show: false } },
                plotOptions: { bar: { horizontal: true, borderRadius: 4 } },
                colors: ['#F43F5E'],
                dataLabels: { enabled: false },
                xaxis: { 
                    categories: data.komposisi_beban.labels,
                    labels: { formatter: (val) => "Rp " + new Intl.NumberFormat('id-ID', {notation: "compact"}).format(val), style: { colors: textColor } },
                    axisBorder: { show: false },
                    axisTicks: { show: false }
                },
                yaxis: { labels: { style: { colors: textColor } } },
                grid: { borderColor: gridColor, strokeDashArray: 4 },
                tooltip: { y: { formatter: formatRupiah } },
                theme: { mode: isDark ? 'dark' : 'light' }
            });
            charts.beban.render();

            // NPF Gauge
            if(charts.npf) charts.npf.destroy();
            charts.npf = new ApexCharts(document.querySelector("#chart-npf"), {
                series: [data.npf],
                chart: { type: 'radialBar', height: 220, background: 'transparent' },
                plotOptions: {
                    radialBar: {
                        startAngle: -90,
                        endAngle: 90,
                        track: { background: isDark ? '#3f3f46' : '#e2e8f0', strokeWidth: '100%', margin: 5 },
                        dataLabels: {
                            name: { show: false },
                            value: { fontSize: '24px', fontWeight: 700, color: textColor, formatter: (val) => val + "%" }
                        }
                    }
                },
                colors: [data.npf > 5 ? '#F43F5E' : '#10B981'],
                theme: { mode: isDark ? 'dark' : 'light' }
            });
            charts.npf.render();

            // Simpanan Grouped Bar
            if(charts.simpanan) charts.simpanan.destroy();
            charts.simpanan = new ApexCharts(document.querySelector("#chart-simpanan"), {
                series: [
                    { name: 'Pokok', data: data.simpanan.pokok },
                    { name: 'Wajib', data: data.simpanan.wajib },
                    { name: 'Sukarela', data: data.simpanan.sukarela }
                ],
                chart: { type: 'bar', height: 200, background: 'transparent', toolbar: { show: false } },
                plotOptions: { bar: { horizontal: false, columnWidth: '50%', borderRadius: 4 } },
                colors: ['#6366F1', '#14B8A6', '#F59E0B'],
                dataLabels: { enabled: false },
                xaxis: { 
                    categories: data.simpanan.labels,
                    labels: { style: { colors: textColor } }
                },
                yaxis: { labels: { formatter: (val) => "Rp " + new Intl.NumberFormat('id-ID', {notation: "compact"}).format(val), style: { colors: textColor } } },
                grid: { borderColor: gridColor, strokeDashArray: 4 },
                tooltip: { y: { formatter: formatRupiah } },
                theme: { mode: isDark ? 'dark' : 'light' }
            });
            charts.simpanan.render();

            // Tren SHU
            if(charts.trenShu) charts.trenShu.destroy();
            charts.trenShu = new ApexCharts(document.querySelector("#chart-tren-shu"), {
                series: [{ name: 'SHU', data: data.tren_shu.series }],
                chart: { type: 'area', height: 250, background: 'transparent', toolbar: { show: false } },
                colors: ['#6366F1'],
                fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.05, stops: [0, 100] } },
                dataLabels: { enabled: false },
                stroke: { curve: 'smooth', width: 3 },
                xaxis: { 
                    categories: data.tren_shu.labels,
                    labels: { style: { colors: textColor } }
                },
                yaxis: { labels: { formatter: (val) => "Rp " + new Intl.NumberFormat('id-ID', {notation: "compact"}).format(val), style: { colors: textColor } } },
                grid: { borderColor: gridColor, strokeDashArray: 4 },
                tooltip: { y: { formatter: formatRupiah } },
                theme: { mode: isDark ? 'dark' : 'light' }
            });
            charts.trenShu.render();

            // Arus Kas
            if(charts.arusKas) charts.arusKas.destroy();
            charts.arusKas = new ApexCharts(document.querySelector("#chart-arus-kas"), {
                series: [{ name: 'Arus Kas', data: data.arus_kas.series }],
                chart: { type: 'bar', height: 250, background: 'transparent', toolbar: { show: false } },
                colors: [function({ value }) {
                    return value < 0 ? '#F43F5E' : '#10B981';
                }],
                plotOptions: { bar: { borderRadius: 4, columnWidth: '50%' } },
                dataLabels: { enabled: false },
                xaxis: { 
                    categories: data.arus_kas.labels,
                    labels: { style: { colors: textColor } }
                },
                yaxis: { labels: { formatter: (val) => "Rp " + new Intl.NumberFormat('id-ID', {notation: "compact"}).format(val), style: { colors: textColor } } },
                grid: { borderColor: gridColor, strokeDashArray: 4 },
                tooltip: { y: { formatter: formatRupiah } },
                theme: { mode: isDark ? 'dark' : 'light' }
            });
            charts.arusKas.render();
        }

        // Handle initial load
        if (@json($chartData)) {
            initCharts(@json($chartData));
        }

        // Handle updates
        Livewire.on('dashboardDataLoaded', (data) => {
            initCharts(data[0]);
        });
    </script>
    @endscript
</div>
