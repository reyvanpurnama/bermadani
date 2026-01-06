@extends('layouts.supplier')

@section('title', 'Dashboard')
@section('header-title', 'Dasbor Mitra')
@section('header-subtitle', 'Ringkasan performa penjualan produk Anda')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">

    <div class="bg-white dark:bg-darkCard p-5 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 flex items-center justify-between group hover:border-emerald-200 transition-colors">
        <div>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Total Omzet</p>
            <div class="flex items-end gap-2">
                <h3 class="text-xl font-bold text-slate-900 dark:text-white leading-none">Rp {{ number_format($totalOmzet ?? 0, 0, ',', '.') }}</h3>
                <span class="text-emerald-500 text-[10px] font-bold flex items-center mb-0.5"><i class='bx bx-up-arrow-alt'></i> {{ $omzetGrowth ?? 0 }}%</span>
            </div>
        </div>
        <div class="w-10 h-10 rounded-lg bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center text-emerald-600 dark:text-emerald-400 group-hover:scale-110 transition-transform">
            <i class='bx bx-wallet text-xl'></i>
        </div>
    </div>

    <!-- Total Pendapatan (Bersih setelah dipotong fee koperasi) -->
    <div class="bg-white dark:bg-darkCard p-5 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 flex items-center justify-between group hover:border-teal-200 transition-colors">
        <div>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Total Pendapatan</p>
            <div class="flex items-end gap-2">
                <h3 class="text-xl font-bold text-teal-600 dark:text-teal-400 leading-none">Rp {{ number_format($totalPendapatan ?? 0, 0, ',', '.') }}</h3>
            </div>
            <p class="text-[9px] text-slate-500 mt-1">Bersih setelah fee koperasi</p>
        </div>
        <div class="w-10 h-10 rounded-lg bg-teal-50 dark:bg-teal-500/10 flex items-center justify-center text-teal-600 dark:text-teal-400 group-hover:scale-110 transition-transform">
            <i class='bx bx-money text-xl'></i>
        </div>
    </div>

    <div class="bg-white dark:bg-darkCard p-5 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 flex items-center justify-between group hover:border-blue-200 transition-colors">
        <div>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Unit Terjual</p>
            <div class="flex items-end gap-2">
                <h3 class="text-xl font-bold text-slate-900 dark:text-white leading-none">{{ $unitTerjual ?? 0 }} Pcs</h3>
                <span class="text-slate-400 text-[10px] mb-0.5">Bulan ini</span>
            </div>
        </div>
        <div class="w-10 h-10 rounded-lg bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center text-blue-600 group-hover:scale-110 transition-transform">
            <i class='bx bx-package text-xl'></i>
        </div>
    </div>

    <div class="bg-white dark:bg-darkCard p-5 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 flex items-center justify-between group hover:border-indigo-200 transition-colors">
        <div>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Produk Aktif</p>
            <div class="flex items-end gap-2">
                <h3 class="text-xl font-bold text-slate-900 dark:text-white leading-none">{{ $produkAktif ?? 0 }} SKU</h3>
                <span class="text-indigo-500 text-[10px] font-bold flex items-center mb-0.5">{{ $lowStock ?? 0 }} Low Stock</span>
            </div>
        </div>
        <div class="w-10 h-10 rounded-lg bg-indigo-50 dark:bg-indigo-500/10 flex items-center justify-center text-primary group-hover:scale-110 transition-transform">
            <i class='bx bx-store text-xl'></i>
        </div>
    </div>

    <div class="bg-white dark:bg-darkCard p-5 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 flex items-center justify-between border-l-4 border-l-amber-400 cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
        <div>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Saldo Tertahan</p>
            <div class="flex items-end gap-2">
                <h3 class="text-xl font-bold text-slate-900 dark:text-white leading-none">Rp {{ number_format($saldoTertahan ?? 0, 0, ',', '.') }}</h3>
            </div>
            <p class="text-[9px] text-amber-600 mt-1 font-semibold">Klik untuk Request Cair</p>
        </div>
        <div class="w-10 h-10 rounded-lg bg-amber-50 dark:bg-amber-500/10 flex items-center justify-center text-amber-500">
            <i class='bx bx-time-five text-xl'></i>
        </div>
    </div>

</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 h-full">

    <div class="lg:col-span-2 bg-white dark:bg-darkCard p-5 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 flex flex-col min-h-[300px]">
        <div class="flex justify-between items-center mb-4">
            <div>
                <h3 class="font-bold text-[14px] text-slate-800 dark:text-white">Trend Penjualan</h3>
                <p class="text-[10px] text-slate-500">Performa produk Anda 7 hari terakhir</p>
            </div>
            <select class="bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 text-[10px] rounded px-2 py-1 outline-none text-slate-700 dark:text-white">
                <option>7 Hari Terakhir</option>
                <option>Bulan Ini</option>
            </select>
        </div>
        <div class="flex-1 w-full relative">
            <div id="salesChart" class="absolute inset-0 w-full h-full"></div>
        </div>
    </div>

    <div class="lg:col-span-1 bg-white dark:bg-darkCard p-5 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 flex flex-col min-h-[300px]">
        <div class="mb-4">
            <h3 class="font-bold text-[14px] text-slate-800 dark:text-white">Produk Terlaris</h3>
            <p class="text-[10px] text-slate-500">Berdasarkan kuantitas terjual</p>
        </div>

        <div class="flex-1 overflow-y-auto space-y-4 pr-1 custom-scrollbar">
            <!-- Placeholder Data -->
            <div class="text-center text-slate-400 text-xs py-10">
                Belum ada data penjualan
            </div>
        </div>
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
            labels: { formatter: (val) => { return val >= 1000 ? (val/1000) + 'k' : val } }
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
