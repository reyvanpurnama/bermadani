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

    {{-- Main Grid Content --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        {{-- Left Column: Summaries List --}}
        <div class="lg:col-span-5 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-150 dark:border-gray-700 overflow-hidden h-fit">
            <div class="p-4 border-b border-gray-150 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                <h3 class="font-bold text-gray-900 dark:text-gray-100 text-sm">Ringkasan Omzet & Keuntungan Bulanan</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Pilih baris bulan untuk menganalisis rincian produk di sebelah kanan.</p>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left align-middle">
                    <thead class="text-[10px] text-gray-400 font-bold uppercase tracking-wider bg-gray-50 dark:bg-gray-700/50 border-b border-gray-150 dark:border-gray-700">
                        <tr>
                            <th class="px-4 py-3">Bulan / Transaksi</th>
                            <th class="px-4 py-3 text-right">Total HPP (Beli)</th>
                            <th class="px-4 py-3 text-right">Total Omzet (Jual)</th>
                            <th class="px-4 py-3 text-right">Total Keuntungan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($summaries as $summary)
                            <tr wire:click="selectMonth('{{ $summary['month_key'] }}')" 
                                class="cursor-pointer transition-all hover:bg-gray-50 dark:hover:bg-gray-700/40 {{ $selectedMonth === $summary['month_key'] ? 'bg-indigo-50/60 dark:bg-indigo-950/20 border-l-4 border-l-indigo-600 font-semibold' : '' }}">
                                <td class="px-4 py-3">
                                    <div class="text-gray-900 dark:text-gray-100 font-bold text-sm">{{ $summary['month_name'] }}</div>
                                    <span class="inline-flex items-center px-1.5 py-0.5 mt-1 rounded text-[9px] font-bold bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300">
                                        {{ number_format($summary['item_count']) }} Baris Data
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300 font-medium">
                                    Rp {{ number_format($summary['total_harga_beli'], 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-right text-gray-700 dark:text-gray-300 font-semibold">
                                    Rp {{ number_format($summary['total_harga_jual'], 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <span class="font-bold text-sm {{ $summary['total_keuntungan'] >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                                        Rp {{ number_format($summary['total_keuntungan'], 0, ',', '.') }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-12 text-center text-gray-400 dark:text-gray-500">
                                    <i class='bx bx-info-circle text-3xl mb-2'></i>
                                    <p class="text-xs font-semibold">Tidak ada data transaksi ditemukan.</p>
                                    <p class="text-[10px] mt-0.5">Silakan import file CSV Anda terlebih dahulu.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Right Column: Details Panel --}}
        <div class="lg:col-span-7">
            @if($selectedMonth)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-150 dark:border-gray-700 overflow-hidden">
                    {{-- Detail Header --}}
                    <div class="p-4 border-b border-gray-150 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div>
                            <h3 class="font-bold text-gray-900 dark:text-gray-100 text-sm">
                                Detail Transaksi: {{ $this->getMonthName(substr($selectedMonth, 5, 2)) }} {{ substr($selectedMonth, 0, 4) }}
                            </h3>
                            <p class="text-[10px] text-gray-400 mt-0.5">Rincian kuantitas, harga pokok satuan, harga jual satuan, dan hasil laba bersih per item.</p>
                        </div>
                        <button wire:click="clearSelectedMonth" class="inline-flex items-center gap-1 text-xs text-gray-500 hover:text-rose-600 font-bold transition-colors">
                            <i class='bx bx-x-circle text-sm'></i> Tutup Detail
                        </button>
                    </div>

                    {{-- Search Filter --}}
                    <div class="p-3 border-b border-gray-150 dark:border-gray-700 bg-white dark:bg-gray-800">
                        <div class="relative rounded-md shadow-sm">
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
                                    <th class="px-3 py-3">Tanggal</th>
                                    <th class="px-3 py-3">Nama Produk</th>
                                    <th class="px-3 py-3 text-center">Qty Terjual</th>
                                    <th class="px-3 py-3 text-right">Harga Beli (Satuan)</th>
                                    <th class="px-3 py-3 text-right">Total HPP (Beli)</th>
                                    <th class="px-3 py-3 text-right">Harga Jual (Satuan)</th>
                                    <th class="px-3 py-3 text-right">Total Omzet (Jual)</th>
                                    <th class="px-3 py-3 text-right text-indigo-600 dark:text-indigo-400">Total Keuntungan</th>
                                    <th class="px-3 py-3 text-right">Margin (%)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @forelse($paginatedDetails as $item)
                                    <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/20">
                                        <td class="px-3 py-3 text-[10px] text-gray-400 dark:text-gray-500 whitespace-nowrap">
                                            {{ $item['tanggal'] }}
                                        </td>
                                        <td class="px-3 py-3 font-bold text-gray-900 dark:text-gray-100 max-w-[140px] truncate" title="{{ $item['nama_barang'] }}">
                                            {{ $item['nama_barang'] }}
                                        </td>
                                        <td class="px-3 py-3 text-center text-gray-700 dark:text-gray-300 whitespace-nowrap">
                                            <span class="font-semibold text-gray-900 dark:text-white">{{ number_format($item['quantity']) }}</span>
                                            <span class="text-[10px] text-gray-400">{{ $item['satuan'] }}</span>
                                        </td>
                                        <td class="px-3 py-3 text-right text-gray-600 dark:text-gray-300 whitespace-nowrap">
                                            Rp {{ number_format($item['harga_beli_satuan'], 0, ',', '.') }}
                                        </td>
                                        <td class="px-3 py-3 text-right font-medium text-gray-900 dark:text-gray-100 whitespace-nowrap">
                                            Rp {{ number_format($item['total_harga_beli'], 0, ',', '.') }}
                                        </td>
                                        <td class="px-3 py-3 text-right text-gray-600 dark:text-gray-300 whitespace-nowrap">
                                            Rp {{ number_format($item['harga_jual_satuan'], 0, ',', '.') }}
                                        </td>
                                        <td class="px-3 py-3 text-right font-medium text-gray-900 dark:text-gray-100 whitespace-nowrap">
                                            Rp {{ number_format($item['total_harga_jual'], 0, ',', '.') }}
                                        </td>
                                        <td class="px-3 py-3 text-right whitespace-nowrap">
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
                                        <td class="px-3 py-3 text-right font-semibold whitespace-nowrap {{ $item['persentase_keuntungan'] > 0 ? 'text-emerald-600 dark:text-emerald-400' : ($item['persentase_keuntungan'] < 0 ? 'text-rose-600 dark:text-rose-400' : 'text-gray-500') }}">
                                            {{ number_format($item['persentase_keuntungan'], 2, ',', '.') }}%
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="px-3 py-10 text-center text-gray-400 dark:text-gray-500">
                                            Tidak ada transaksi barang yang sesuai dengan pencarian Anda.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination Section --}}
                    @if($paginatedDetails->hasPages())
                        <div class="px-4 py-3 border-t border-gray-150 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
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
                        Silakan klik salah satu baris bulan pada tabel ringkasan di sebelah kiri untuk menganalisis rincian produk, harga beli, harga jual, dan laba kotor secara detail.
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
