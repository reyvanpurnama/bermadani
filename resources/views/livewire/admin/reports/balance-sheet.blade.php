<div class="p-6">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Laporan Posisi Keuangan (Neraca)</h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm">Konsolidasi Unit Koperasi & Retail per
                {{ now()->format('d F Y') }}
            </p>
        </div>
        <button onclick="window.print()"
            class="bg-primary hover:bg-primary/90 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors">
            <i class='bx bx-printer'></i> Cetak
        </button>
    </div>

    <!-- Summary Cards (Optional, maybe specific ratios later) -->

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- ASET (ASSETS) -->
        <div
            class="bg-white dark:bg-darkCard rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden flex flex-col h-full">
            <div class="p-4 border-b border-slate-100 dark:border-slate-700 bg-emerald-50/50 dark:bg-emerald-500/10">
                <h3 class="font-bold text-lg text-emerald-700 dark:text-emerald-400 flex items-center gap-2">
                    <i class='bx bx-trending-up'></i> ASET
                </h3>
            </div>

            <div class="p-4 flex-1 space-y-4">
                {{-- Aset Lancar --}}
                <div>
                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Aset Lancar</h4>

                    <div class="flex justify-between items-center py-2 border-b border-slate-50 dark:border-slate-800">
                        <span class="text-slate-600 dark:text-slate-300">Kas dan Setara Kas</span>
                        <span class="font-mono font-medium text-slate-800 dark:text-white">Rp
                            {{ number_format($assets['cash'], 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-slate-50 dark:border-slate-800">
                        <span class="text-slate-600 dark:text-slate-300">Piutang Pinjaman Anggota</span>
                        <span class="font-mono font-medium text-slate-800 dark:text-white">Rp
                            {{ number_format($assets['receivables'], 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-slate-50 dark:border-slate-800">
                        <span class="text-slate-600 dark:text-slate-300">Persediaan Barang Dagang</span>
                        <span class="font-mono font-medium text-slate-800 dark:text-white">Rp
                            {{ number_format($assets['inventory'], 0, ',', '.') }}</span>
                    </div>
                </div>

                {{-- Aset Tetap --}}
                <div class="pt-2">
                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Aset Tetap</h4>
                    <div class="flex justify-between items-center py-2 border-b border-slate-50 dark:border-slate-800">
                        <span class="text-slate-600 dark:text-slate-300">Aset Tetap (Inventaris)</span>
                        <span class="font-mono font-medium text-slate-800 dark:text-white">Rp
                            {{ number_format($assets['fixed_assets'], 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <div class="p-4 bg-slate-50 dark:bg-slate-800 border-t border-slate-200 dark:border-slate-700 mt-auto">
                <div class="flex justify-between items-center">
                    <span class="font-bold text-slate-700 dark:text-slate-300">TOTAL ASET</span>
                    <span class="font-bold text-xl text-emerald-600 dark:text-emerald-400 font-mono">Rp
                        {{ number_format($assets['total'], 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- LIABILITAS & EKUITAS (LIABILITIES + EQUITY) -->
        <div
            class="bg-white dark:bg-darkCard rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden flex flex-col h-full">
            <div class="p-4 border-b border-slate-100 dark:border-slate-700 bg-rose-50/50 dark:bg-rose-500/10">
                <h3 class="font-bold text-lg text-rose-700 dark:text-rose-400 flex items-center gap-2">
                    <i class='bx bx-pie-chart-alt-2'></i> LIABILITAS & EKUITAS
                </h3>
            </div>

            <div class="p-4 flex-1 space-y-6">
                {{-- Liabilities --}}
                <div>
                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Liabilitas (Kewajiban)
                    </h4>

                    <div class="flex justify-between items-center py-2 border-b border-slate-50 dark:border-slate-800">
                        <span class="text-slate-600 dark:text-slate-300">Simpanan Sukarela</span>
                        <span class="font-mono font-medium text-slate-800 dark:text-white">Rp
                            {{ number_format($liabilities['simpanan_sukarela'], 0, ',', '.') }}</span>
                    </div>

                    <div
                        class="flex justify-between items-center py-2 mt-2 bg-slate-50 dark:bg-slate-700/30 px-2 rounded">
                        <span class="font-semibold text-slate-700 dark:text-slate-300 text-sm">Total Liabilitas</span>
                        <span class="font-mono font-bold text-slate-800 dark:text-white">Rp
                            {{ number_format($liabilities['total'], 0, ',', '.') }}</span>
                    </div>
                </div>

                {{-- Equity --}}
                <div>
                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Ekuitas (Modal)</h4>

                    <div class="flex justify-between items-center py-2 border-b border-slate-50 dark:border-slate-800">
                        <span class="text-slate-600 dark:text-slate-300">Simpanan Pokok</span>
                        <span class="font-mono font-medium text-slate-800 dark:text-white">Rp
                            {{ number_format($equity['simpanan_pokok'], 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-slate-50 dark:border-slate-800">
                        <span class="text-slate-600 dark:text-slate-300">Simpanan Wajib</span>
                        <span class="font-mono font-medium text-slate-800 dark:text-white">Rp
                            {{ number_format($equity['simpanan_wajib'], 0, ',', '.') }}</span>
                    </div>

                    <div class="flex justify-between items-center py-2 border-b border-slate-50 dark:border-slate-800">
                        <span class="text-slate-600 dark:text-slate-300">Modal Disetor / Hibah</span>
                        <span class="font-mono font-medium text-slate-800 dark:text-white">Rp
                            {{ number_format($equity['capital'], 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-slate-50 dark:border-slate-800">
                        <div class="flex flex-col">
                            <span class="text-slate-600 dark:text-slate-300">SHU Tahun Berjalan</span>
                            <span class="text-[10px] text-slate-400 italic">Est. Aset - Liabilitas - Modal</span>
                        </div>
                        <span
                            class="font-mono font-medium {{ $equity['shu'] >= 0 ? 'text-emerald-600' : 'text-rose-500' }}">
                            Rp {{ number_format($equity['shu'], 0, ',', '.') }}
                        </span>
                    </div>

                    <div
                        class="flex justify-between items-center py-2 mt-2 bg-slate-50 dark:bg-slate-700/30 px-2 rounded">
                        <span class="font-semibold text-slate-700 dark:text-slate-300 text-sm">Total Ekuitas</span>
                        <span class="font-mono font-bold text-slate-800 dark:text-white">Rp
                            {{ number_format($equity['total'], 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <div class="p-4 bg-slate-50 dark:bg-slate-800 border-t border-slate-200 dark:border-slate-700 mt-auto">
                <div class="flex justify-between items-center">
                    <span class="font-bold text-slate-700 dark:text-slate-300">TOTAL LIABILITAS & EKUITAS</span>
                    <span class="font-bold text-xl text-rose-600 dark:text-rose-400 font-mono">Rp
                        {{ number_format($liabilities['total'] + $equity['total'], 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-6 text-center">
        <p class="text-xs text-slate-400 italic">
            * Laporan ini dihasilkan secara otomatis oleh sistem ("Unaudited"). Pastikan seluruh transaksi manual, stok,
            dan pinjaman telah tercatat dengan benar.
        </p>
    </div>
    <style>
        @media print {

            #main-sidebar,
            #sidebar-header {
                display: none;
            }

            .no-print {
                display: none;
            }

            body {
                background: white;
                color: black;
            }

            .bg-white,
            .dark\:bg-darkCard {
                box-shadow: none;
                border: 1px solid #ddd;
            }

            .text-white {
                color: black !important;
            }
        }
    </style>
</div>