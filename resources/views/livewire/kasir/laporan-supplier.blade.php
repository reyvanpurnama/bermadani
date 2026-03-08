<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <div>
            <a href="{{ route('kasir.dashboard') }}"
                class="inline-flex items-center gap-1 text-xs text-slate-400 hover:text-primary mb-1.5 transition-colors">
                <i class='bx bx-arrow-back text-sm'></i> Dashboard
            </a>
            <h1 class="text-xl font-bold text-slate-800 dark:text-white">Laporan Harian Supplier</h1>
            <p class="text-[11px] text-slate-500 mt-0.5">Rekap penjualan produk konsinyasi per supplier — kirim ke supplier sebelum toko tutup</p>
        </div>
    </div>

    {{-- Submit Success Banner --}}
    @if($submitted)
    <div class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-2xl p-4 flex items-center gap-3">
        <i class='bx bx-check-circle text-2xl text-emerald-500'></i>
        <div>
            <p class="font-semibold text-emerald-700 dark:text-emerald-400 text-sm">Laporan berhasil dikirim!</p>
            <p class="text-xs text-emerald-600 dark:text-emerald-500 mt-0.5">Semua supplier sudah mendapat notifikasi laporan penjualan tanggal {{ \Carbon\Carbon::parse($submittedDate)->translatedFormat('d F Y') }}.</p>
        </div>
    </div>
    @endif

    {{-- Date Filter --}}
    <div class="bg-white dark:bg-darkCard rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 p-5">
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
            <div class="flex items-center gap-3">
                <label class="text-sm font-semibold text-slate-700 dark:text-slate-300 whitespace-nowrap">Tanggal Laporan</label>
                <input type="date" wire:model.live="date"
                    class="bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary/20">
            </div>
            <div class="flex items-center gap-3 ml-auto">
                <span class="text-xs text-slate-400">
                    {{ \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y') }}
                    @if($date === today()->toDateString())
                    <span class="text-emerald-500 font-semibold">· Hari Ini</span>
                    @endif
                </span>
            </div>
        </div>
    </div>

    @php $laporan = $this->laporan; $summary = $this->totalSummary; @endphp

    @if(empty($laporan))
    {{-- Empty --}}
    <div class="bg-white dark:bg-darkCard rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 p-12 text-center">
        <div class="w-16 h-16 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class='bx bx-bar-chart text-3xl text-slate-400'></i>
        </div>
        <h3 class="font-bold text-slate-700 dark:text-white">Tidak ada penjualan konsinyasi</h3>
        <p class="text-sm text-slate-400 mt-1">Belum ada produk konsinyasi yang terjual pada {{ \Carbon\Carbon::parse($date)->translatedFormat('d F Y') }}</p>
    </div>
    @else

    {{-- Summary Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-darkCard rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 p-4">
            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-1">Supplier Aktif</p>
            <h3 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $summary['suppliers'] }}</h3>
        </div>
        <div class="bg-white dark:bg-darkCard rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 p-4">
            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-1">Total Terjual</p>
            <h3 class="text-2xl font-bold text-slate-900 dark:text-white">{{ number_format($summary['totalQty']) }} <span class="text-sm font-medium text-slate-400">pcs</span></h3>
        </div>
        <div class="bg-white dark:bg-darkCard rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 p-4">
            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-1">Total Omzet</p>
            <h3 class="text-xl font-bold text-emerald-600">Rp {{ number_format($summary['totalOmzet'], 0, ',', '.') }}</h3>
        </div>
        <div class="bg-white dark:bg-darkCard rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 p-4">
            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-1">Hak Supplier</p>
            <h3 class="text-xl font-bold text-blue-600">Rp {{ number_format($summary['totalPayable'], 0, ',', '.') }}</h3>
            <p class="text-[10px] text-slate-400 mt-0.5">Margin koperasi: Rp {{ number_format($summary['totalMargin'], 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- Per-Supplier Detail --}}
    <div class="space-y-4">
        @foreach($laporan as $row)
        <div class="bg-white dark:bg-darkCard rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">
            {{-- Supplier Header --}}
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-indigo-100 dark:bg-indigo-900/30 rounded-xl flex items-center justify-center">
                        <i class='bx bx-store text-lg text-primary'></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-900 dark:text-white text-sm">{{ $row['supplierName'] }}</h3>
                        <p class="text-[11px] text-slate-500">{{ count($row['products']) }} jenis produk · {{ $row['totalQty'] }} pcs terjual</p>
                    </div>
                </div>
                <div class="flex items-center gap-4 text-right">
                    <div>
                        <p class="text-[10px] text-slate-400">Omzet</p>
                        <p class="font-bold text-emerald-600 text-sm">Rp {{ number_format($row['totalOmzet'], 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-400">Hak Supplier</p>
                        <p class="font-bold text-blue-600 text-sm">Rp {{ number_format($row['totalPayable'], 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            {{-- Product Detail --}}
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-slate-50 dark:bg-slate-800/50">
                        <tr>
                            <th class="px-5 py-2.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Produk</th>
                            <th class="px-5 py-2.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider text-center">Qty</th>
                            <th class="px-5 py-2.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider text-right">Harga Jual</th>
                            <th class="px-5 py-2.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider text-right">Hak Supplier/pcs</th>
                            <th class="px-5 py-2.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider text-right">Total Omzet</th>
                            <th class="px-5 py-2.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider text-right">Total Hak Supplier</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @foreach($row['products'] as $prod)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30">
                            <td class="px-5 py-3 text-sm font-medium text-slate-800 dark:text-white">{{ $prod['name'] }}</td>
                            <td class="px-5 py-3 text-center">
                                <span class="font-bold text-slate-900 dark:text-white text-sm">{{ $prod['qty'] }}</span>
                                <span class="text-slate-400 text-xs"> pcs</span>
                            </td>
                            <td class="px-5 py-3 text-right text-sm text-slate-600 dark:text-slate-400">Rp {{ number_format($prod['unitPrice'], 0, ',', '.') }}</td>
                            <td class="px-5 py-3 text-right text-sm text-slate-600 dark:text-slate-400">Rp {{ number_format($prod['supplierPrice'], 0, ',', '.') }}</td>
                            <td class="px-5 py-3 text-right font-semibold text-emerald-600 text-sm">Rp {{ number_format($prod['totalOmzet'], 0, ',', '.') }}</td>
                            <td class="px-5 py-3 text-right font-semibold text-blue-600 text-sm">Rp {{ number_format($prod['totalPayable'], 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-slate-50 dark:bg-slate-800/50">
                        <tr>
                            <td colspan="4" class="px-5 py-3 text-xs font-bold text-slate-500 uppercase">Total {{ $row['supplierName'] }}</td>
                            <td class="px-5 py-3 text-right font-bold text-emerald-600 text-sm">Rp {{ number_format($row['totalOmzet'], 0, ',', '.') }}</td>
                            <td class="px-5 py-3 text-right font-bold text-blue-600 text-sm">Rp {{ number_format($row['totalPayable'], 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Submit Button --}}
    <div class="bg-white dark:bg-darkCard rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 p-5 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div>
            <p class="font-semibold text-slate-800 dark:text-white text-sm">Kirim laporan ke supplier?</p>
            <p class="text-xs text-slate-500 mt-0.5">
                Setiap supplier akan mendapat notifikasi otomatis berisi ringkasan penjualan produk mereka hari ini.
            </p>
        </div>
        <button wire:click="submitLaporan()"
            wire:loading.attr="disabled"
            @if($submitted) disabled @endif
            class="bg-primary hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed text-white font-bold py-2.5 px-6 rounded-xl transition-colors text-sm flex items-center gap-2 shadow-sm shadow-indigo-500/20 flex-shrink-0">
            <span wire:loading.remove wire:target="submitLaporan">
                <i class='bx bx-send text-lg'></i>
                {{ $submitted ? '✅ Sudah Dikirim' : 'Kirim ke ' . count($laporan) . ' Supplier' }}
            </span>
            <span wire:loading wire:target="submitLaporan" class="flex items-center gap-2">
                <i class='bx bx-loader-alt animate-spin text-lg'></i> Mengirim...
            </span>
        </button>
    </div>

    @endif

</div>
