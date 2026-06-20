<div class="px-4 py-6 sm:px-6 lg:px-8 max-w-7xl mx-auto">
    {{-- Header Section --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900 dark:text-gray-100">Laporan RAT - Neraca Hasil Usaha (Retail)</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Data dikelompokkan per bulan berdasarkan laporan transaksi retail koperasi.</p>
        </div>
        <div class="flex flex-col sm:flex-row sm:items-center gap-4">
            {{-- Filter Tahun --}}
            <div class="flex items-center gap-2">
                <label for="yearFilter" class="text-sm text-gray-600 dark:text-gray-400 font-medium">Filter:</label>
                <select id="yearFilter" wire:model.live="selectedYear" class="bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 text-sm rounded focus:ring-blue-500 focus:border-blue-500 block p-2">
                    <option value="All">Semua Tahun</option>
                    @foreach($availableYears as $yr)
                        <option value="{{ $yr }}">{{ $yr }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Form Upload CSV --}}
            <form wire:submit.prevent="importCsv" class="flex items-center gap-2">
                <input type="file" wire:model="csvFile" accept=".csv" class="block w-full text-xs text-slate-500
                    file:mr-2 file:py-1.5 file:px-3
                    file:rounded-md file:border-0
                    file:text-xs file:font-semibold
                    file:bg-indigo-50 file:text-indigo-700
                    hover:file:bg-indigo-100
                    dark:file:bg-indigo-950/10 dark:file:text-indigo-400
                    border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 p-1" />
                <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded shadow-sm transition-all" wire:loading.attr="disabled">
                    <i class='bx bx-upload'></i>
                    <span wire:loading.remove>Import CSV</span>
                    <span wire:loading>Loading...</span>
                </button>
            </form>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Harga Beli</p>
            <p class="text-lg font-bold text-gray-900 dark:text-gray-100 mt-1">
                Rp {{ number_format($kpi['total_harga_beli'], 0, ',', '.') }}
            </p>
        </div>
        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Harga Jual</p>
            <p class="text-lg font-bold text-gray-900 dark:text-gray-100 mt-1">
                Rp {{ number_format($kpi['total_harga_jual'], 0, ',', '.') }}
            </p>
        </div>
        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Keuntungan</p>
            <p class="text-lg font-bold text-emerald-600 dark:text-emerald-400 mt-1">
                Rp {{ number_format($kpi['total_keuntungan'], 0, ',', '.') }}
            </p>
        </div>
        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Margin Keuntungan</p>
            <p class="text-lg font-bold text-blue-600 dark:text-blue-400 mt-1">
                @if($kpi['total_harga_jual'] > 0)
                    {{ number_format(($kpi['total_keuntungan'] / $kpi['total_harga_jual']) * 100, 2, ',', '.') }}%
                @else
                    0%
                @endif
            </p>
        </div>
    </div>

    {{-- Main Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        {{-- Summaries Table --}}
        <div class="lg:col-span-5 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden h-fit">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                <h3 class="font-semibold text-gray-800 dark:text-gray-200">Ringkasan Keuntungan Per Bulan</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">Pilih bulan untuk menampilkan rincian barang di panel sebelah kanan.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left align-middle">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-300 border-b border-gray-200 dark:border-gray-700">
                        <tr>
                            <th class="px-4 py-3">Bulan</th>
                            <th class="px-4 py-3 text-right">Total Beli</th>
                            <th class="px-4 py-3 text-right">Total Jual</th>
                            <th class="px-4 py-3 text-right">Keuntungan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($summaries as $summary)
                            <tr wire:click="selectMonth('{{ $summary['month_key'] }}')" 
                                class="cursor-pointer transition-all hover:bg-gray-50 dark:hover:bg-gray-700/40 {{ $selectedMonth === $summary['month_key'] ? 'bg-indigo-50/70 dark:bg-indigo-900/20 border-l-4 border-l-indigo-600' : '' }}">
                                <td class="px-4 py-3">
                                    <div class="font-semibold text-gray-900 dark:text-gray-100">{{ $summary['month_name'] }}</div>
                                    <div class="text-[10px] text-gray-500 dark:text-gray-400 mt-0.5">{{ $summary['item_count'] }} Transaksi</div>
                                </td>
                                <td class="px-4 py-3 text-right text-gray-700 dark:text-gray-300">
                                    Rp {{ number_format($summary['total_harga_beli'], 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-right text-gray-700 dark:text-gray-300">
                                    Rp {{ number_format($summary['total_harga_jual'], 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-right font-semibold text-emerald-600 dark:text-emerald-400">
                                    Rp {{ number_format($summary['total_keuntungan'], 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                    Tidak ada data untuk filter tahun ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Details Panel --}}
        <div class="lg:col-span-7">
            @if($selectedMonth)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    {{-- Detail Header --}}
                    <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div>
                            <h3 class="font-semibold text-gray-800 dark:text-gray-200">
                                Detail Transaksi: {{ $this->getMonthName(substr($selectedMonth, 5, 2)) }} {{ substr($selectedMonth, 0, 4) }}
                            </h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Rincian item barang yang terjual beserta margin harga.</p>
                        </div>
                        <button wire:click="clearSelectedMonth" class="text-xs text-gray-500 hover:text-rose-600 font-medium transition-colors">
                            <i class='bx bx-x-circle text-sm align-middle mr-0.5'></i> Tutup Detail
                        </button>
                    </div>

                    {{-- Search Bar --}}
                    <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                        <div class="relative rounded-md shadow-sm">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <i class='bx bx-search text-gray-400 text-sm'></i>
                            </div>
                            <input type="text" 
                                   wire:model.live.debounce.300ms="searchDetail" 
                                   placeholder="Cari nama barang..." 
                                   class="block w-full rounded-md border-gray-300 dark:border-gray-600 pl-10 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>

                    {{-- Details Table --}}
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left align-middle">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-300 border-b border-gray-200 dark:border-gray-700">
                                <tr>
                                    <th class="px-4 py-3">Tanggal</th>
                                    <th class="px-4 py-3">Nama Barang</th>
                                    <th class="px-4 py-3 text-center">Qty</th>
                                    <th class="px-4 py-3 text-right">Harga Beli</th>
                                    <th class="px-4 py-3 text-right">Harga Jual</th>
                                    <th class="px-4 py-3 text-right">Keuntungan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($paginatedDetails as $item)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40">
                                        <td class="px-4 py-3 text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                            {{ $item['tanggal'] }}
                                        </td>
                                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-gray-100">
                                            {{ $item['nama_barang'] }}
                                        </td>
                                        <td class="px-4 py-3 text-center text-gray-700 dark:text-gray-300">
                                            {{ $item['quantity'] }} <span class="text-xs text-gray-400">{{ $item['satuan'] }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-right text-gray-700 dark:text-gray-300 whitespace-nowrap">
                                            <div class="text-xs text-gray-400">@ Rp {{ number_format($item['harga_satuan'], 0, ',', '.') }}</div>
                                            <div class="font-medium">Rp {{ number_format($item['total_harga_beli'], 0, ',', '.') }}</div>
                                        </td>
                                        <td class="px-4 py-3 text-right text-gray-700 dark:text-gray-300 whitespace-nowrap">
                                            Rp {{ number_format($item['harga_jual'], 0, ',', '.') }}
                                        </td>
                                        <td class="px-4 py-3 text-right font-semibold text-emerald-600 dark:text-emerald-400 whitespace-nowrap">
                                            Rp {{ number_format($item['total_keuntungan'], 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                            Tidak ada transaksi barang yang ditemukan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if($paginatedDetails->hasPages())
                        <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                            {{ $paginatedDetails->links() }}
                        </div>
                    @endif
                </div>
            @else
                <div class="bg-gray-50 dark:bg-gray-800/30 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-700 p-12 text-center">
                    <i class='bx bx-pointer text-4xl text-gray-400 dark:text-gray-600 mb-3'></i>
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Rincian Barang Belum Dipilih</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 max-w-sm mx-auto">Silakan klik salah satu baris bulan pada tabel ringkasan di sebelah kiri untuk melihat detail harga beli, harga jual, dan keuntungan per item.</p>
                </div>
            @endif
        </div>
    </div>
</div>
