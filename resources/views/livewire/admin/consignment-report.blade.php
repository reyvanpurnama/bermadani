<div class="space-y-6">

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div
            class="bg-white dark:bg-darkCard p-4 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 flex items-center gap-3">
            <div
                class="w-10 h-10 rounded-lg bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center text-blue-600 text-xl">
                <i class='bx bx-user-voice'></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Total Mitra</p>
                <h4 class="text-lg font-bold text-slate-800 dark:text-white">{{ $stats['totalSuppliers'] }} Aktif</h4>
            </div>
        </div>

        <div class="bg-white dark:bg-darkCard p-4 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Omzet Konsinyasi</p>
            <h3 class="text-xl font-bold text-slate-900 dark:text-white">Rp
                {{ number_format($stats['totalOmzet'], 0, ',', '.') }}</h3>
        </div>

        <div class="bg-indigo-600 p-4 rounded-xl shadow-lg shadow-indigo-500/20 text-white relative overflow-hidden">
            <div class="relative z-10">
                <p class="text-[10px] font-bold text-indigo-200 uppercase tracking-widest mb-1">Pendapatan Fee</p>
                <h3 class="text-xl font-bold">Rp {{ number_format($stats['totalFee'], 0, ',', '.') }}</h3>
                <p class="text-[10px] text-indigo-100 mt-1">Keuntungan Koperasi</p>
            </div>
            <i class='bx bxs-bank absolute -bottom-4 -right-4 text-6xl text-white opacity-10'></i>
        </div>

        <div
            class="bg-white dark:bg-darkCard p-4 rounded-xl shadow-sm border-l-4 border-l-amber-400 border-y border-r border-slate-100 dark:border-slate-700">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Hutang ke Mitra</p>
            <h3 class="text-xl font-bold text-amber-600 dark:text-amber-400">Rp
                {{ number_format($stats['pendingPayable'], 0, ',', '.') }}</h3>
            <p class="text-[10px] text-slate-400 mt-1">Belum settlement</p>
        </div>
    </div>

    {{-- Chart Card --}}
    <div class="bg-white dark:bg-darkCard p-5 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700">
        <div class="flex justify-between items-center mb-4">
            <h3 class="font-bold text-[13px] text-slate-800 dark:text-white">Top 5 Mitra (Berdasarkan Omzet)</h3>
            <select wire:model.live="period"
                class="bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 text-[10px] rounded px-2 py-1 outline-none text-slate-700 dark:text-white">
                <option value="month">Bulan Ini</option>
                <option value="year">Tahun Ini</option>
            </select>
        </div>
        <div id="supplierChart" class="w-full h-[300px]"></div>
    </div>

    {{-- Supplier Performance Table --}}
    <div
        class="bg-white dark:bg-darkCard rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">
        <div class="p-5 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
            <h3 class="font-bold text-[13px] text-slate-800 dark:text-white">Rincian Performa Mitra</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600 dark:text-slate-400">
                <thead
                    class="bg-slate-50 dark:bg-slate-800/50 text-[10px] uppercase font-bold text-slate-500 dark:text-slate-400 tracking-widest">
                    <tr>
                        <th class="px-5 py-3">Nama Mitra</th>
                        <th class="px-5 py-3 text-center">Produk Aktif</th>
                        <th class="px-5 py-3 text-center">Terjual (Qty)</th>
                        <th class="px-5 py-3 text-center">Sell-through Rate</th>
                        <th class="px-5 py-3 text-right">Total Omzet</th>
                        <th class="px-5 py-3 text-right">Fee Koperasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700 text-[12px]">
                    @forelse($supplierPerformance as $supplier)
                        @php
                            $sellThrough = $supplier->totalInitial > 0 ? round(($supplier->totalSold / $supplier->totalInitial) * 100) : 0;
                        @endphp
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors group">
                            <td class="px-5 py-3 font-medium text-slate-800 dark:text-white">
                                <div class="flex items-center gap-2">
                                    <div
                                        class="w-6 h-6 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-[10px] font-bold">
                                        {{ strtoupper(substr($supplier->businessName, 0, 1)) }}
                                    </div>
                                    {{ $supplier->businessName }}
                                </div>
                            </td>
                            <td class="px-5 py-3 text-center">{{ $supplier->productCount }} SKU</td>
                            <td class="px-5 py-3 text-center font-bold text-emerald-600">{{ $supplier->totalSold ?? 0 }}
                            </td>
                            <td class="px-5 py-3 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <span class="text-[11px] font-bold">{{ $sellThrough }}%</span>
                                    <div class="w-16 bg-slate-100 dark:bg-slate-700 h-1.5 rounded-full">
                                        <div class="bg-{{ $sellThrough >= 70 ? 'emerald' : ($sellThrough >= 40 ? 'blue' : 'rose') }}-500 h-1.5 rounded-full"
                                            style="width: {{ $sellThrough }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3 text-right font-bold text-slate-800 dark:text-white">
                                Rp {{ number_format($supplier->totalOmzet ?? 0, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-3 text-right font-bold text-indigo-600 dark:text-indigo-400">
                                Rp {{ number_format($supplier->totalFee ?? 0, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-slate-500">
                                <div class="flex flex-col items-center">
                                    <i class='bx bx-bar-chart-alt-2 text-4xl mb-2 text-slate-300'></i>
                                    <p>Belum ada data konsinyasi.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('livewire:navigated', initChart);
        document.addEventListener('DOMContentLoaded', initChart);

        function initChart() {
            const chartEl = document.querySelector("#supplierChart");
            if (!chartEl) return;

            // Clear existing chart
            chartEl.innerHTML = '';

            const chartData = @json($chartData);
            const isDark = document.documentElement.classList.contains('dark');

            if (chartData.length === 0) {
                chartEl.innerHTML = '<div class="flex items-center justify-center h-full text-slate-400 text-sm">Tidak ada data untuk ditampilkan</div>';
                return;
            }

            var options = {
                series: [{
                    name: 'Omzet',
                    data: chartData.map(item => item.omzet)
                }],
                chart: {
                    height: '100%',
                    type: 'bar',
                    toolbar: { show: false },
                    fontFamily: 'Inter',
                    foreColor: isDark ? '#94a3b8' : '#64748b'
                },
                colors: ['#4F46E5'],
                plotOptions: {
                    bar: { borderRadius: 4, horizontal: true, barHeight: '50%' }
                },
                dataLabels: { enabled: false },
                xaxis: {
                    categories: chartData.map(item => item.name),
                    axisBorder: { show: false },
                    axisTicks: { show: false },
                    labels: {
                        formatter: (val) => {
                            return (val / 1000000).toFixed(1) + ' Jt'
                        }
                    }
                },
                grid: {
                    borderColor: isDark ? '#334155' : '#f1f5f9',
                    strokeDashArray: 4
                },
                tooltip: {
                    theme: isDark ? 'dark' : 'light',
                    y: {
                        formatter: function (val) {
                            return "Rp " + val.toLocaleString('id-ID')
                        }
                    }
                }
            };

            var chart = new ApexCharts(chartEl, options);
            chart.render();
        }
    </script>
@endpush