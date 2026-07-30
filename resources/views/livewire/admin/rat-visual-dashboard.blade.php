<div class="space-y-6">
    <style>
        @media print {
            @page {
                size: A4 landscape;
                margin: 5mm;
            }

            body {
                background: #ffffff !important;
                color: #0f172a !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            #main-sidebar, #sidebar-toggle, #sidebar-header, header, nav, aside, .print\:hidden {
                display: none !important;
            }

            main, #main-content {
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
                overflow: visible !important;
            }

            .space-y-6 {
                gap: 1rem !important;
                width: 100% !important;
            }

            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .rounded-2xl, .rounded-xl {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }
        }
    </style>
    
    {{-- HEADER & TOOLBAR SECTION --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white dark:bg-darkCard p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 print:hidden">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center text-emerald-600 dark:text-emerald-400 text-2xl shrink-0">
                <i class='bx bx-pie-chart-alt-2'></i>
            </div>
            <div>
                <h1 class="text-xl font-bold text-slate-800 dark:text-white">Dashboard Keuangan RAT {{ $dashboard['year'] }}</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400">
                    Koperasi Konsumen Syariah Berkah Solusi Madani — Ringkasan Kinerja & Infografis Rapat Anggota Tahunan
                </p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            {{-- Year Selector --}}
            <div class="relative">
                <select wire:model.live="selectedYear"
                    class="appearance-none bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white text-xs font-bold rounded-xl px-4 py-2.5 pr-9 outline-none focus:ring-2 focus:ring-emerald-500 cursor-pointer">
                    @foreach($availableYears as $year)
                        <option value="{{ $year }}">Tahun Buku {{ $year }}</option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-500">
                    <i class='bx bx-chevron-down text-base'></i>
                </div>
            </div>

            <button onclick="window.print()"
                class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-sm shadow-emerald-600/20 transition-all flex items-center gap-2">
                <i class='bx bx-printer text-base'></i> Cetak Laporan RAT
            </button>
            <a href="{{ route('admin.rat-report') }}"
                class="bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 transition-all flex items-center gap-2">
                <i class='bx bx-table text-base'></i> Tabel RAT
            </a>
        </div>
    </div>

    {{-- TOP 5 KPI SUMMARY CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        
        {{-- KPI 1: TOTAL KAS MASUK INTERNAL --}}
        <div class="bg-white dark:bg-darkCard p-5 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 flex items-center gap-4 relative group cursor-pointer">
            <div class="w-12 h-12 rounded-xl bg-indigo-50 dark:bg-indigo-500/10 flex items-center justify-center text-indigo-600 dark:text-indigo-400 text-2xl shrink-0 group-hover:scale-110 transition-transform">
                <i class='bx bx-line-chart'></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Kas Masuk Internal</p>
                <h3 class="text-lg font-bold text-slate-800 dark:text-white">
                    Rp {{ $dashboard['kpi']['totalKasMasuk']['val'] }} Jt
                </h3>
                <span class="text-[10px] text-slate-400 block font-medium">Rp {{ $dashboard['kpi']['totalKasMasuk']['raw'] }}</span>
            </div>

            {{-- Floating Hover Tooltip --}}
            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-64 p-3 bg-slate-900 text-white text-[11px] rounded-xl shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 pointer-events-none z-50">
                <div class="font-bold text-indigo-300 border-b border-slate-700 pb-1 mb-1.5 flex items-center gap-1">
                    <i class='bx bx-info-circle text-sm'></i> Asal Sumber Kas Masuk (CSV):
                </div>
                <ul class="space-y-1 text-[10px] text-slate-300">
                    <li>• Pendapatan Toko Minimarket: <strong>Rp 94.777.311</strong></li>
                    <li>• Simpanan Wajib Anggota: <strong>Rp 38.400.000</strong></li>
                    <li>• Simpanan Sukarela: <strong>Rp 6.450.000</strong></li>
                    <li>• Simpanan Pokok: <strong>Rp 600.000</strong></li>
                </ul>
                <div class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-slate-900"></div>
            </div>
        </div>

        {{-- KPI 2: TOTAL KAS KELUAR --}}
        <div class="bg-white dark:bg-darkCard p-5 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 flex items-center gap-4 relative group cursor-pointer">
            <div class="w-12 h-12 rounded-xl bg-rose-50 dark:bg-rose-500/10 flex items-center justify-center text-rose-600 dark:text-rose-400 text-2xl shrink-0 group-hover:scale-110 transition-transform">
                <i class='bx bx-money-withdraw'></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Total Kas Keluar</p>
                <h3 class="text-lg font-bold text-slate-800 dark:text-white">
                    Rp {{ $dashboard['kpi']['totalKasKeluar']['val'] }} Jt
                </h3>
                <span class="text-[10px] text-slate-400 block font-medium">Rp {{ $dashboard['kpi']['totalKasKeluar']['raw'] }}</span>
            </div>

            {{-- Floating Hover Tooltip --}}
            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-64 p-3 bg-slate-900 text-white text-[11px] rounded-xl shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 pointer-events-none z-50">
                <div class="font-bold text-rose-300 border-b border-slate-700 pb-1 mb-1.5 flex items-center gap-1">
                    <i class='bx bx-info-circle text-sm'></i> Asal Pengeluaran Kas (CSV):
                </div>
                <p class="text-[10px] text-slate-300">
                    Gaji Staf (Rp 36,1M) + Gaji Pengurus (Rp 31,2M) + Utang Supplier (Rp 28,7M) + Aset Tetap (Rp 11M) + RAT & Ops (Rp 14M).
                </p>
                <div class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-slate-900"></div>
            </div>
        </div>

        {{-- KPI 3: SURPLUS SHU DIBAGIKAN --}}
        <div class="bg-white dark:bg-darkCard p-5 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 flex items-center gap-4 relative group cursor-pointer">
            <div class="w-12 h-12 rounded-xl bg-amber-50 dark:bg-amber-500/10 flex items-center justify-center text-amber-600 dark:text-amber-400 text-2xl shrink-0 group-hover:scale-110 transition-transform">
                <i class='bx bx-coin-stack'></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Surplus SHU Dibagikan</p>
                <h3 class="text-lg font-bold text-slate-800 dark:text-white">
                    Rp {{ $dashboard['kpi']['surplusKas']['val'] }} Jt
                </h3>
                <span class="text-[10px] text-slate-400 block font-medium">Rp {{ $dashboard['kpi']['surplusKas']['raw'] }}</span>
            </div>

            {{-- Floating Hover Tooltip --}}
            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-64 p-3 bg-slate-900 text-white text-[11px] rounded-xl shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 pointer-events-none z-50">
                <div class="font-bold text-amber-300 border-b border-slate-700 pb-1 mb-1.5 flex items-center gap-1">
                    <i class='bx bx-info-circle text-sm'></i> Rincian SHU & Alokasi Persediaan:
                </div>
                <ul class="space-y-1 text-[10px] text-slate-300">
                    <li>• Surplus Kas Gross Operasional: <strong>Rp 19.015.051</strong></li>
                    <li>• Dialokasikan ke Persediaan Toko: <strong>- Rp 4.015.051</strong></li>
                    <li class="border-t border-slate-700 pt-1 text-amber-300 font-bold">• SHU Bersih Dibagikan: <strong>Rp 15.000.000</strong></li>
                </ul>
                <div class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-slate-900"></div>
            </div>
        </div>

        {{-- KPI 4: JUMLAH ANGGOTA --}}
        <div class="bg-white dark:bg-darkCard p-5 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 flex items-center gap-4 relative group cursor-pointer">
            <div class="w-12 h-12 rounded-xl bg-purple-50 dark:bg-purple-500/10 flex items-center justify-center text-purple-600 dark:text-purple-400 text-2xl shrink-0 group-hover:scale-110 transition-transform">
                <i class='bx bx-group'></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Jumlah Anggota</p>
                <h3 class="text-lg font-bold text-slate-800 dark:text-white">
                    {{ $dashboard['kpi']['jumlahAnggota']['val'] }} Orang
                </h3>
                <span class="text-[10px] text-emerald-600 dark:text-emerald-400 font-semibold">{{ $dashboard['kpi']['jumlahAnggota']['growth'] }}</span>
            </div>

            {{-- Floating Hover Tooltip --}}
            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-64 p-3 bg-slate-900 text-white text-[11px] rounded-xl shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 pointer-events-none z-50">
                <div class="font-bold text-purple-300 border-b border-slate-700 pb-1 mb-1.5 flex items-center gap-1">
                    <i class='bx bx-info-circle text-sm'></i> Asal Sumber Anggota:
                </div>
                <p class="text-[10px] text-slate-300">
                    Data 131 anggota aktif terdaftar Koperasi Konsumen Syariah Berkah Solusi Madani (Live System Database).
                </p>
                <div class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-slate-900"></div>
            </div>
        </div>

        {{-- KPI 5: SALDO KAS AKHIR --}}
        <div class="bg-white dark:bg-darkCard p-5 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 flex items-center gap-4 relative group cursor-pointer">
            <div class="w-12 h-12 rounded-xl bg-teal-50 dark:bg-teal-500/10 flex items-center justify-center text-teal-600 dark:text-teal-400 text-2xl shrink-0 group-hover:scale-110 transition-transform">
                <i class='bx bx-wallet'></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Saldo Kas Akhir</p>
                <h3 class="text-lg font-bold text-slate-800 dark:text-white">
                    Rp {{ $dashboard['kpi']['kasBank']['val'] }} Jt
                </h3>
                <span class="text-[10px] text-slate-400 font-medium block">
                    Rp {{ $dashboard['kpi']['kasBank']['raw'] }}
                </span>
            </div>

            {{-- Floating Hover Tooltip --}}
            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-64 p-3 bg-slate-900 text-white text-[11px] rounded-xl shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 pointer-events-none z-50">
                <div class="font-bold text-teal-300 border-b border-slate-700 pb-1 mb-1.5 flex items-center gap-1">
                    <i class='bx bx-info-circle text-sm'></i> Asal Saldo Kas Akhir (CSV):
                </div>
                <ul class="space-y-1 text-[10px] text-slate-300">
                    <li>• Saldo Kas Awal (Mei): <strong>Rp 6.964.859</strong></li>
                    <li>• Total Surplus Kas: <strong>Rp 30.499.118</strong></li>
                    <li>• Total Kas Akhir (Desember): <strong>Rp 37.463.977</strong></li>
                </ul>
                <div class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-slate-900"></div>
            </div>
        </div>

    </div>

    {{-- 8 MODUL VISUALISASI GRID (2 ROWS X 4 COLS) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
        
        {{-- CARD 1: KOMPOSISI ASET --}}
        <div class="bg-white dark:bg-darkCard p-5 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 flex flex-col justify-between">
            <div>
                <div class="flex items-center gap-2 mb-3">
                    <span class="w-7 h-7 rounded-lg bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-xs">1</span>
                    <h3 class="text-sm font-bold text-slate-800 dark:text-white">Komposisi Aset</h3>
                </div>

                <div class="h-36 relative flex items-center justify-center my-2">
                    <canvas id="chartKomposisiAset"></canvas>
                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                        <span class="text-[9px] font-bold text-slate-400 uppercase">ASET REAL</span>
                        <span class="text-sm font-bold text-slate-800 dark:text-white">Rp {{ $dashboard['komposisiAset']['total'] }} Jt</span>
                    </div>
                </div>

                <div class="space-y-1.5 mt-3 text-xs">
                    @foreach($dashboard['komposisiAset']['items'] as $item)
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500 dark:text-slate-400 flex items-center gap-1.5 truncate">
                                <span class="w-2 h-2 rounded-full shrink-0" style="background-color: {{ $item['color'] }}"></span>
                                {{ $item['label'] }}
                            </span>
                            <div class="text-right shrink-0 ml-1">
                                <span class="font-bold text-slate-800 dark:text-white">{{ $item['val'] }}</span>
                                <span class="text-[10px] text-slate-400 font-medium ml-0.5">({{ $item['pct'] }})</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-700/60">
                <span class="text-[11px] text-slate-500 dark:text-slate-400 font-medium leading-tight block">
                    Total Aset Real Bermadani = <strong class="text-emerald-600 dark:text-emerald-400">Rp {{ $dashboard['komposisiAset']['totalRaw'] }}</strong>.
                </span>
            </div>
        </div>

        {{-- CARD 2: KOMPOSISI KAS MASUK --}}
        <div class="bg-white dark:bg-darkCard p-5 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 flex flex-col justify-between">
            <div>
                <div class="flex items-center gap-2 mb-3">
                    <span class="w-7 h-7 rounded-lg bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold text-xs">2</span>
                    <h3 class="text-sm font-bold text-slate-800 dark:text-white">Komposisi Kas Masuk</h3>
                </div>

                <div class="h-36 relative flex items-center justify-center my-2">
                    <canvas id="chartKomposisiPendapatan"></canvas>
                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                        <span class="text-[9px] font-bold text-slate-400 uppercase">TOTAL</span>
                        <span class="text-sm font-bold text-slate-800 dark:text-white">Rp 140,2 Jt</span>
                    </div>
                </div>

                <div class="space-y-1.5 mt-3 text-xs">
                    @foreach($dashboard['komposisiPendapatan']['items'] as $item)
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500 dark:text-slate-400 flex items-center gap-1.5 truncate">
                                <span class="w-2 h-2 rounded-full shrink-0" style="background-color: {{ $item['color'] }}"></span>
                                {{ $item['label'] }}
                            </span>
                            <div class="text-right shrink-0 ml-1">
                                <span class="font-bold text-slate-800 dark:text-white">{{ $item['val'] }}</span>
                                <span class="text-[10px] text-slate-400 font-medium ml-0.5">({{ $item['pct'] }})</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-700/60">
                <span class="text-[11px] text-slate-500 dark:text-slate-400 font-medium leading-tight block">
                    Penerimaan terbesar berasal dari omzet toko minimarket (67.6%).
                </span>
            </div>
        </div>

        {{-- CARD 3: BEBAN & PENGELUARAN KAS --}}
        <div class="bg-white dark:bg-darkCard p-5 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 flex flex-col justify-between">
            <div>
                <div class="flex items-center gap-2 mb-3">
                    <span class="w-7 h-7 rounded-lg bg-purple-50 dark:bg-purple-500/10 text-purple-600 dark:text-purple-400 flex items-center justify-center font-bold text-xs">3</span>
                    <h3 class="text-sm font-bold text-slate-800 dark:text-white">Beban & Pengeluaran</h3>
                </div>

                <div class="h-32 my-1">
                    <canvas id="chartKomposisiBeban"></canvas>
                </div>

                <div class="space-y-1 mt-2 text-[10px] font-medium text-slate-600 dark:text-slate-300 border-t border-slate-100 dark:border-slate-800 pt-2">
                    <div class="flex justify-between"><span>Gaji Karyawan Toko:</span><span class="font-bold text-purple-600 dark:text-purple-400">Rp 36.184.000</span></div>
                    <div class="flex justify-between"><span>Gaji Pengurus Koperasi:</span><span class="font-bold text-slate-800 dark:text-white">Rp 31.250.000</span></div>
                    <div class="flex justify-between"><span>Utang Supplier Barang:</span><span class="font-bold text-slate-800 dark:text-white">Rp 28.738.508</span></div>
                    <div class="flex justify-between"><span>Pengadaan Aset Tetap:</span><span class="font-bold text-slate-800 dark:text-white">Rp 11.021.000</span></div>
                    <div class="flex justify-between"><span>Konsumsi RAT & Ops:</span><span class="font-bold text-slate-800 dark:text-white">Rp 14.018.752</span></div>
                </div>
            </div>
            <div class="mt-3 pt-2 border-t border-slate-100 dark:border-slate-700/60">
                <span class="text-[10px] text-slate-400 font-medium leading-tight block">
                    Total Kas Keluar (CSV): <strong class="text-rose-600 dark:text-rose-400">Rp 121.212.260</strong>
                </span>
            </div>
        </div>

        {{-- CARD 4: SURPLUS KAS & ALOKASI SHU --}}
        <div class="bg-white dark:bg-darkCard p-5 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 flex flex-col justify-between">
            <div>
                <div class="flex items-center gap-2 mb-3">
                    <span class="w-7 h-7 rounded-lg bg-sky-50 dark:bg-sky-500/10 text-sky-600 dark:text-sky-400 flex items-center justify-center font-bold text-xs">4</span>
                    <h3 class="text-sm font-bold text-slate-800 dark:text-white">Surplus Kas & Alokasi</h3>
                </div>

                <div class="h-32 my-1">
                    <canvas id="chartTrenShu"></canvas>
                </div>

                <div class="space-y-1 mt-2 text-[10px] font-medium text-slate-600 dark:text-slate-300 border-t border-slate-100 dark:border-slate-800 pt-2">
                    <div class="flex justify-between"><span>Cadangan (25%):</span><span class="font-bold text-amber-600 dark:text-amber-400">Rp 4.753.763</span></div>
                    <div class="flex justify-between"><span>Jasa Simpanan (30%):</span><span class="font-bold text-emerald-600 dark:text-emerald-400">Rp 5.704.515</span></div>
                    <div class="flex justify-between"><span>Jasa Usaha (25%):</span><span class="font-bold text-sky-600 dark:text-sky-400">Rp 4.753.763</span></div>
                    <div class="flex justify-between"><span>Pengurus & Pengawas (10%):</span><span class="font-bold text-slate-800 dark:text-white">Rp 1.901.505</span></div>
                    <div class="flex justify-between"><span>Pendidikan & Sosial (10%):</span><span class="font-bold text-slate-800 dark:text-white">Rp 1.901.505</span></div>
                </div>
            </div>
            <div class="mt-3 pt-2 border-t border-slate-100 dark:border-slate-700/60">
                <span class="text-[10px] text-slate-400 font-medium leading-tight block">
                    Surplus Kas Internal: <strong class="text-emerald-600 dark:text-emerald-400">Rp 19.015.051</strong>
                </span>
            </div>
        </div>

        {{-- CARD 5: RASIO PEMBIAYAAN BERMASALAH (NPF) - SLEEK MODULAR DESIGN --}}
        <div class="bg-white dark:bg-darkCard p-5 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2">
                        <span class="w-7 h-7 rounded-lg bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold text-xs">5</span>
                        <h3 class="text-sm font-bold text-slate-800 dark:text-white">Rasio NPF</h3>
                    </div>
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/40">
                        {{ $dashboard['npf']['status'] }}
                    </span>
                </div>

                {{-- NPF Sleek Metric & Progress Bar --}}
                <div class="my-4 text-center">
                    <div class="text-3xl font-extrabold text-slate-800 dark:text-white mb-1">
                        {{ $dashboard['npf']['val'] }}
                    </div>
                    <p class="text-xs text-slate-400 font-medium">Pembiayaan Bermasalah (Target &lt; 5%)</p>

                    {{-- Segmented Risk Indicator Bar --}}
                    <div class="mt-4 space-y-2">
                        <div class="h-3 w-full bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden flex p-0.5 gap-1">
                            <div class="h-full w-[25%] bg-emerald-500 rounded-l-full relative" title="Lancar (0-2%)">
                                <div class="absolute -right-1 -top-1 w-2.5 h-2.5 bg-slate-900 ring-2 ring-white rounded-full"></div>
                            </div>
                            <div class="h-full w-[25%] bg-amber-400" title="Kurang Lancar (2-5%)"></div>
                            <div class="h-full w-[25%] bg-orange-500" title="Diragukan (5-8%)"></div>
                            <div class="h-full w-[25%] bg-rose-500 rounded-r-full" title="Macet (>8%)"></div>
                        </div>

                        <div class="grid grid-cols-4 text-[9px] font-bold text-slate-400 text-center pt-1">
                            <span class="text-emerald-600 dark:text-emerald-400">Lancar</span>
                            <span>K.Lancar</span>
                            <span>Diragukan</span>
                            <span class="text-rose-500">Macet</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-700/60">
                <span class="text-[11px] text-slate-500 dark:text-slate-400 font-medium leading-tight block">
                    Kualitas pembiayaan terjaga sehat jauh di bawah ambang batas risiko.
                </span>
            </div>
        </div>

        {{-- CARD 6: PERTUMBUHAN SIMPANAN ANGGOTA --}}
        <div class="bg-white dark:bg-darkCard p-5 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 flex flex-col justify-between">
            <div>
                <div class="flex items-center gap-2 mb-3">
                    <span class="w-7 h-7 rounded-lg bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold text-xs">6</span>
                    <h3 class="text-sm font-bold text-slate-800 dark:text-white">Pertumbuhan Simpanan</h3>
                </div>

                <div class="h-32 my-1">
                    <canvas id="chartPertumbuhanSimpanan"></canvas>
                </div>

                <div class="space-y-1 mt-2 text-[10px] font-medium text-slate-600 dark:text-slate-300 border-t border-slate-100 dark:border-slate-800 pt-2">
                    <div class="flex justify-between"><span>Simpanan Pokok (Live DB):</span><span class="font-bold text-slate-800 dark:text-white">Rp {{ $dashboard['liveMetrics']['simpok'] }}</span></div>
                    <div class="flex justify-between"><span>Simpanan Wajib (Live DB):</span><span class="font-bold text-blue-600 dark:text-blue-400">Rp {{ $dashboard['liveMetrics']['simwa'] }}</span></div>
                    <div class="flex justify-between"><span>Simpanan Sukarela (Estimasi):</span><span class="font-bold text-emerald-600 dark:text-emerald-400">Rp 120.000.000</span></div>
                    <div class="flex justify-between"><span>Simpanan Berjangka:</span><span class="font-bold text-indigo-600 dark:text-indigo-400">Rp 67.000.000</span></div>
                </div>
            </div>
            <div class="mt-3 pt-2 border-t border-slate-100 dark:border-slate-700/60">
                <span class="text-[10px] text-slate-400 font-medium leading-tight block">
                    Simpanan Pokok + Wajib (DB): <strong class="text-emerald-600 dark:text-emerald-400">Rp {{ $dashboard['liveMetrics']['totalSimpanan'] }}</strong>
                </span>
            </div>
        </div>

        {{-- CARD 7: ARUS KAS TAHUN 2025 --}}
        <div class="bg-white dark:bg-darkCard p-5 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 flex flex-col justify-between">
            <div>
                <div class="flex items-center gap-2 mb-3">
                    <span class="w-7 h-7 rounded-lg bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center font-bold text-xs">7</span>
                    <h3 class="text-sm font-bold text-slate-800 dark:text-white">Arus Kas {{ $dashboard['year'] }}</h3>
                </div>

                <div class="h-32 my-1">
                    <canvas id="chartArusKas"></canvas>
                </div>

                <div class="space-y-1 mt-2 text-[10px] font-medium text-slate-600 dark:text-slate-300 border-t border-slate-100 dark:border-slate-800 pt-2">
                    <div class="flex justify-between"><span>Kas Masuk Internal (CSV):</span><span class="font-bold text-emerald-600 dark:text-emerald-400">Rp {{ $dashboard['arusKasCsv']['kasMasukInternal'] }}</span></div>
                    <div class="flex justify-between"><span>Kas Keluar (CSV):</span><span class="font-bold text-rose-500">Rp {{ $dashboard['arusKasCsv']['kasKeluar'] }}</span></div>
                    <div class="flex justify-between"><span>Surplus Kas Bersih:</span><span class="font-bold text-blue-600 dark:text-blue-400">Rp {{ $dashboard['arusKasCsv']['surplusKasInternal'] }}</span></div>
                    <div class="flex justify-between"><span>Saldo Kas Akhir:</span><span class="font-bold text-slate-800 dark:text-white">Rp {{ $dashboard['arusKasCsv']['saldoKasAkhir'] }}</span></div>
                </div>
            </div>
            <div class="mt-3 pt-2 border-t border-slate-100 dark:border-slate-700/60">
                <span class="text-[10px] text-slate-400 font-medium leading-tight block">
                    Sumber data: <strong class="text-slate-800 dark:text-white">ARUS KAS 25.csv</strong>
                </span>
            </div>
        </div>

        {{-- CARD 8: RINGKASAN KESEHATAN KOPERASI --}}
        <div class="bg-white dark:bg-darkCard p-5 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 flex flex-col justify-between">
            <div>
                <div class="flex items-center gap-2 mb-3">
                    <span class="w-7 h-7 rounded-lg bg-teal-50 dark:bg-teal-500/10 text-teal-600 dark:text-teal-400 flex items-center justify-center font-bold text-xs">8</span>
                    <h3 class="text-sm font-bold text-slate-800 dark:text-white">Kesehatan Koperasi</h3>
                </div>

                <div class="space-y-2 my-2 text-xs">
                    @foreach($dashboard['kesehatan'] as $item)
                        <div class="flex items-center justify-between p-2 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-700/50">
                            <span class="font-medium text-slate-600 dark:text-slate-300 flex items-center gap-1.5">
                                <i class='bx bx-check-shield text-emerald-500 text-base'></i>
                                {{ $item['label'] }}
                            </span>
                            <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold uppercase {{ $item['bg'] }}">
                                {{ $item['status'] }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="mt-3 pt-3 border-t border-slate-100 dark:border-slate-700/60">
                <span class="text-[11px] text-emerald-600 dark:text-emerald-400 font-bold block">
                    Status: Koperasi Sehat & Berkelanjutan.
                </span>
            </div>
        </div>

    </div>

    {{-- INTERACTIVE DETAIL RINCIAN ALOKASI NOMINAL PANEL --}}
    <div class="bg-white dark:bg-darkCard p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 space-y-4" x-data="{ tab: 'aset' }">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 pb-3 border-b border-slate-100 dark:border-slate-700">
            <div>
                <h3 class="text-base font-bold text-slate-800 dark:text-white flex items-center gap-2">
                    <i class='bx bx-list-check text-emerald-600 text-xl'></i> Matriks Rincian & Sumber Alokasi Keuangan RAT
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400">
                    Transparansi rincian alokasi dana internal Koperasi Konsumen Syariah Berkah Solusi Madani (Tahun Buku {{ $dashboard['year'] }})
                </p>
            </div>
            
            {{-- Tab Switcher --}}
            <div class="flex p-1 bg-slate-100 dark:bg-slate-800 rounded-xl text-xs font-bold gap-1 flex-wrap">
                <button @click="tab = 'aset'" :class="tab === 'aset' ? 'bg-white dark:bg-slate-700 text-indigo-600 dark:text-indigo-400 shadow-sm' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'" class="px-3 py-1.5 rounded-lg transition-all">
                    Alokasi Aset
                </button>
                <button @click="tab = 'simpanan'" :class="tab === 'simpanan' ? 'bg-white dark:bg-slate-700 text-blue-600 dark:text-blue-400 shadow-sm' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'" class="px-3 py-1.5 rounded-lg transition-all">
                    Simpanan & Modal Wajib
                </button>
                <button @click="tab = 'pendapatan'" :class="tab === 'pendapatan' ? 'bg-white dark:bg-slate-700 text-emerald-600 dark:text-emerald-400 shadow-sm' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'" class="px-3 py-1.5 rounded-lg transition-all">
                    Pendapatan
                </button>
                <button @click="tab = 'beban'" :class="tab === 'beban' ? 'bg-white dark:bg-slate-700 text-purple-600 dark:text-purple-400 shadow-sm' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'" class="px-3 py-1.5 rounded-lg transition-all">
                    Beban Operasional
                </button>
                <button @click="tab = 'shu'" :class="tab === 'shu' ? 'bg-white dark:bg-slate-700 text-amber-600 dark:text-amber-400 shadow-sm' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'" class="px-3 py-1.5 rounded-lg transition-all">
                    Distribusi SHU
                </button>
                <button @click="tab = 'aruskas'" :class="tab === 'aruskas' ? 'bg-white dark:bg-slate-700 text-emerald-600 dark:text-emerald-400 shadow-sm' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'" class="px-3 py-1.5 rounded-lg transition-all flex items-center gap-1">
                    <i class='bx bx-table text-sm'></i> Arus Kas Bulanan (CSV)
                </button>
            </div>
        </div>

        {{-- TAB 1: RINCIAN ALOKASI ASET --}}
        <div x-show="tab === 'aset'" class="overflow-x-auto">
            <table class="w-full text-xs text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800 text-slate-400 font-bold uppercase tracking-wider border-b border-slate-100 dark:border-slate-700">
                        <th class="p-3">Elemen Aset</th>
                        <th class="p-3 text-right">Nominal Rp</th>
                        <th class="p-3 text-center">Porsi (%)</th>
                        <th class="p-3">Sumber & Deskripsi Alokasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium">
                    @foreach($dashboard['rincianAlokasi']['aset'] as $row)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40">
                            <td class="p-3 font-bold text-slate-800 dark:text-white">{{ $row['nama'] }}</td>
                            <td class="p-3 text-right font-bold text-indigo-600 dark:text-indigo-400">{{ $row['nominal'] }}</td>
                            <td class="p-3 text-center font-bold text-slate-700 dark:text-slate-300">{{ $row['pct'] }}</td>
                            <td class="p-3 text-slate-500 dark:text-slate-400">{{ $row['sumber'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- TAB RINCIAN SIMPANAN ANGGOTA --}}
        <div x-show="tab === 'simpanan'" class="overflow-x-auto" style="display: none;">
            <table class="w-full text-xs text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800 text-slate-400 font-bold uppercase tracking-wider border-b border-slate-100 dark:border-slate-700">
                        <th class="p-3">Kategori Simpanan</th>
                        <th class="p-3 text-right">Nominal Rp</th>
                        <th class="p-3 text-center">Status Pos Akuntansi</th>
                        <th class="p-3">Sumber & Penjelasan Keuangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium">
                    @foreach($dashboard['rincianAlokasi']['simpanan'] as $row)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40">
                            <td class="p-3 font-bold text-slate-800 dark:text-white">{{ $row['nama'] }}</td>
                            <td class="p-3 text-right font-bold text-blue-600 dark:text-blue-400">{{ $row['nominal'] }}</td>
                            <td class="p-3 text-center font-bold text-slate-700 dark:text-slate-300">
                                <span class="px-2 py-0.5 rounded-full text-[10px] bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400">
                                    {{ $row['status'] }}
                                </span>
                            </td>
                            <td class="p-3 text-slate-500 dark:text-slate-400">{{ $row['sumber'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- TAB 2: RINCIAN PENDAPATAN --}}
        <div x-show="tab === 'pendapatan'" class="overflow-x-auto" style="display: none;">
            <table class="w-full text-xs text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800 text-slate-400 font-bold uppercase tracking-wider border-b border-slate-100 dark:border-slate-700">
                        <th class="p-3">Sumber Pendapatan</th>
                        <th class="p-3 text-right">Nominal Rp</th>
                        <th class="p-3 text-center">Kontribusi (%)</th>
                        <th class="p-3">Deskripsi Alokasi Sumber</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium">
                    @foreach($dashboard['rincianAlokasi']['pendapatan'] as $row)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40">
                            <td class="p-3 font-bold text-slate-800 dark:text-white">{{ $row['nama'] }}</td>
                            <td class="p-3 text-right font-bold text-emerald-600 dark:text-emerald-400">{{ $row['nominal'] }}</td>
                            <td class="p-3 text-center font-bold text-slate-700 dark:text-slate-300">{{ $row['pct'] }}</td>
                            <td class="p-3 text-slate-500 dark:text-slate-400">{{ $row['sumber'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- TAB 3: RINCIAN BEBAN --}}
        <div x-show="tab === 'beban'" class="overflow-x-auto" style="display: none;">
            <table class="w-full text-xs text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800 text-slate-400 font-bold uppercase tracking-wider border-b border-slate-100 dark:border-slate-700">
                        <th class="p-3">Kategori Beban</th>
                        <th class="p-3 text-right">Nominal Rp</th>
                        <th class="p-3 text-center">Porsi (%)</th>
                        <th class="p-3">Rincian Pengeluaran</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium">
                    @foreach($dashboard['rincianAlokasi']['beban'] as $row)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40">
                            <td class="p-3 font-bold text-slate-800 dark:text-white">{{ $row['nama'] }}</td>
                            <td class="p-3 text-right font-bold text-purple-600 dark:text-purple-400">{{ $row['nominal'] }}</td>
                            <td class="p-3 text-center font-bold text-slate-700 dark:text-slate-300">{{ $row['pct'] }}</td>
                            <td class="p-3 text-slate-500 dark:text-slate-400">{{ $row['sumber'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- TAB 4: RINCIAN DISTRIBUSI SHU --}}
        <div x-show="tab === 'shu'" class="overflow-x-auto" style="display: none;">
            <table class="w-full text-xs text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800 text-slate-400 font-bold uppercase tracking-wider border-b border-slate-100 dark:border-slate-700">
                        <th class="p-3">Pos Alokasi SHU</th>
                        <th class="p-3 text-right">Nominal Rp</th>
                        <th class="p-3">Ketentuan AD/ART & Tujuan Alokasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium">
                    @foreach($dashboard['rincianAlokasi']['alokasiShu'] as $row)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40">
                            <td class="p-3 font-bold text-slate-800 dark:text-white">{{ $row['alokasi'] }}</td>
                            <td class="p-3 text-right font-bold text-amber-600 dark:text-amber-400">{{ $row['nominal'] }}</td>
                            <td class="p-3 text-slate-500 dark:text-slate-400">{{ $row['keterangan'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- TAB 6: LAPORAN ARUS KAS BULANAN (FULL CSV TABLE MEI - DES 2025) --}}
        <div x-show="tab === 'aruskas'" class="overflow-x-auto" style="display: none;">
            <table class="w-full text-[11px] text-left border-collapse min-w-[800px]">
                <thead>
                    <tr class="bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 font-bold border-b border-slate-200 dark:border-slate-700">
                        <th class="p-2.5">Pos Transaksi Arus Kas</th>
                        <th class="p-2.5 text-right">Mei</th>
                        <th class="p-2.5 text-right">Juni</th>
                        <th class="p-2.5 text-right">Juli</th>
                        <th class="p-2.5 text-right">Agustus</th>
                        <th class="p-2.5 text-right">September</th>
                        <th class="p-2.5 text-right">Oktober</th>
                        <th class="p-2.5 text-right">November</th>
                        <th class="p-2.5 text-right">Desember</th>
                        <th class="p-2.5 text-right bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-400">Total 2025</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium">
                    {{-- HEADER KAS MASUK --}}
                    <tr class="bg-emerald-50/50 dark:bg-emerald-950/20 text-emerald-800 dark:text-emerald-300 font-bold">
                        <td colspan="10" class="p-2 uppercase tracking-wider text-[10px]">I. KAS MASUK</td>
                    </tr>
                    <tr>
                        <td class="p-2 pl-4 text-slate-700 dark:text-slate-300">Simpanan Wajib</td>
                        <td class="p-2 text-right text-slate-500">0</td>
                        <td class="p-2 text-right">4.400.000</td>
                        <td class="p-2 text-right">5.650.000</td>
                        <td class="p-2 text-right">5.500.000</td>
                        <td class="p-2 text-right">6.000.000</td>
                        <td class="p-2 text-right">5.650.000</td>
                        <td class="p-2 text-right">5.650.000</td>
                        <td class="p-2 text-right">5.550.000</td>
                        <td class="p-2 text-right font-bold text-emerald-600 dark:text-emerald-400">38.400.000</td>
                    </tr>
                    <tr>
                        <td class="p-2 pl-4 text-slate-700 dark:text-slate-300">Simpanan Pokok</td>
                        <td class="p-2 text-right text-slate-500">0</td>
                        <td class="p-2 text-right">200.000</td>
                        <td class="p-2 text-right">200.000</td>
                        <td class="p-2 text-right text-slate-500">0</td>
                        <td class="p-2 text-right text-slate-500">0</td>
                        <td class="p-2 text-right">200.000</td>
                        <td class="p-2 text-right text-slate-500">0</td>
                        <td class="p-2 text-right text-slate-500">0</td>
                        <td class="p-2 text-right font-bold text-emerald-600 dark:text-emerald-400">600.000</td>
                    </tr>
                    <tr>
                        <td class="p-2 pl-4 text-slate-700 dark:text-slate-300">Simpanan Sukarela</td>
                        <td class="p-2 text-right">1.050.000</td>
                        <td class="p-2 text-right">1.050.000</td>
                        <td class="p-2 text-right">1.050.000</td>
                        <td class="p-2 text-right">400.000</td>
                        <td class="p-2 text-right">400.000</td>
                        <td class="p-2 text-right">1.050.000</td>
                        <td class="p-2 text-right">1.050.000</td>
                        <td class="p-2 text-right">400.000</td>
                        <td class="p-2 text-right font-bold text-emerald-600 dark:text-emerald-400">6.450.000</td>
                    </tr>
                    <tr>
                        <td class="p-2 pl-4 text-slate-700 dark:text-slate-300">Pendapatan Bersih Toko Minimarket</td>
                        <td class="p-2 text-right text-slate-500">0</td>
                        <td class="p-2 text-right">115.000</td>
                        <td class="p-2 text-right">785.000</td>
                        <td class="p-2 text-right">1.902.500</td>
                        <td class="p-2 text-right">7.838.414</td>
                        <td class="p-2 text-right">34.815.401</td>
                        <td class="p-2 text-right">28.289.851</td>
                        <td class="p-2 text-right">21.031.145</td>
                        <td class="p-2 text-right font-bold text-emerald-600 dark:text-emerald-400">94.777.311</td>
                    </tr>
                    <tr class="bg-slate-50 dark:bg-slate-800/80 font-bold border-t border-b border-emerald-200 dark:border-emerald-800/50">
                        <td class="p-2 text-emerald-700 dark:text-emerald-300">TOTAL KAS MASUK INTERNAL BERMADANI</td>
                        <td class="p-2 text-right">1.050.000</td>
                        <td class="p-2 text-right">5.765.000</td>
                        <td class="p-2 text-right">7.685.000</td>
                        <td class="p-2 text-right">7.802.500</td>
                        <td class="p-2 text-right">14.238.414</td>
                        <td class="p-2 text-right">41.715.401</td>
                        <td class="p-2 text-right">34.989.851</td>
                        <td class="p-2 text-right">26.981.145</td>
                        <td class="p-2 text-right text-emerald-600 dark:text-emerald-400">140.227.311</td>
                    </tr>

                    {{-- HEADER KAS KELUAR --}}
                    <tr class="bg-rose-50/50 dark:bg-rose-950/20 text-rose-800 dark:text-rose-300 font-bold">
                        <td colspan="10" class="p-2 uppercase tracking-wider text-[10px]">II. KAS KELUAR (PENGELUARAN)</td>
                    </tr>
                    <tr>
                        <td class="p-2 pl-4 text-slate-700 dark:text-slate-300">Gaji Pengurus Koperasi</td>
                        <td class="p-2 text-right">4.000.000</td>
                        <td class="p-2 text-right">4.000.000</td>
                        <td class="p-2 text-right">4.000.000</td>
                        <td class="p-2 text-right">4.000.000</td>
                        <td class="p-2 text-right">4.000.000</td>
                        <td class="p-2 text-right">4.000.000</td>
                        <td class="p-2 text-right">4.000.000</td>
                        <td class="p-2 text-right">3.250.000</td>
                        <td class="p-2 text-right font-bold text-rose-600 dark:text-rose-400">31.250.000</td>
                    </tr>
                    <tr>
                        <td class="p-2 pl-4 text-slate-700 dark:text-slate-300">Gaji Karyawan Minimarket</td>
                        <td class="p-2 text-right text-slate-500">0</td>
                        <td class="p-2 text-right">2.000.000</td>
                        <td class="p-2 text-right">2.000.000</td>
                        <td class="p-2 text-right">3.000.000</td>
                        <td class="p-2 text-right">3.000.000</td>
                        <td class="p-2 text-right">8.728.000</td>
                        <td class="p-2 text-right">8.728.000</td>
                        <td class="p-2 text-right">8.728.000</td>
                        <td class="p-2 text-right font-bold text-rose-600 dark:text-rose-400">36.184.000</td>
                    </tr>
                    <tr>
                        <td class="p-2 pl-4 text-slate-700 dark:text-slate-300">Pembayaran Utang Supplier</td>
                        <td class="p-2 text-right text-slate-500">0</td>
                        <td class="p-2 text-right">482.000</td>
                        <td class="p-2 text-right">16.394.008</td>
                        <td class="p-2 text-right">2.762.000</td>
                        <td class="p-2 text-right">2.936.500</td>
                        <td class="p-2 text-right">3.682.000</td>
                        <td class="p-2 text-right text-slate-500">0</td>
                        <td class="p-2 text-right">2.482.000</td>
                        <td class="p-2 text-right font-bold text-rose-600 dark:text-rose-400">28.738.508</td>
                    </tr>
                    <tr>
                        <td class="p-2 pl-4 text-slate-700 dark:text-slate-300">Pengadaan Aset Tetap</td>
                        <td class="p-2 text-right">9.021.000</td>
                        <td class="p-2 text-right text-slate-500">0</td>
                        <td class="p-2 text-right text-slate-500">0</td>
                        <td class="p-2 text-right text-slate-500">0</td>
                        <td class="p-2 text-right text-slate-500">0</td>
                        <td class="p-2 text-right text-slate-500">0</td>
                        <td class="p-2 text-right text-slate-500">0</td>
                        <td class="p-2 text-right">2.000.000</td>
                        <td class="p-2 text-right font-bold text-rose-600 dark:text-rose-400">11.021.000</td>
                    </tr>
                    <tr>
                        <td class="p-2 pl-4 text-slate-700 dark:text-slate-300">Biaya Kemasan & Kantong</td>
                        <td class="p-2 text-right text-slate-500">0</td>
                        <td class="p-2 text-right text-slate-500">0</td>
                        <td class="p-2 text-right text-slate-500">0</td>
                        <td class="p-2 text-right text-slate-500">0</td>
                        <td class="p-2 text-right">148.000</td>
                        <td class="p-2 text-right">1.028.000</td>
                        <td class="p-2 text-right">753.000</td>
                        <td class="p-2 text-right">733.000</td>
                        <td class="p-2 text-right font-bold text-rose-600 dark:text-rose-400">2.662.000</td>
                    </tr>
                    <tr>
                        <td class="p-2 pl-4 text-slate-700 dark:text-slate-300">Konsumsi Rapat & Pelaksanaan RAT</td>
                        <td class="p-2 text-right text-slate-500">0</td>
                        <td class="p-2 text-right text-slate-500">0</td>
                        <td class="p-2 text-right text-slate-500">0</td>
                        <td class="p-2 text-right text-slate-500">0</td>
                        <td class="p-2 text-right text-slate-500">0</td>
                        <td class="p-2 text-right">1.440.000</td>
                        <td class="p-2 text-right">1.440.000</td>
                        <td class="p-2 text-right">1.440.000</td>
                        <td class="p-2 text-right font-bold text-rose-600 dark:text-rose-400">4.320.000</td>
                    </tr>
                    <tr>
                        <td class="p-2 pl-4 text-slate-700 dark:text-slate-300">Pengembalian Simpanan Anggota</td>
                        <td class="p-2 text-right text-slate-500">0</td>
                        <td class="p-2 text-right text-slate-500">0</td>
                        <td class="p-2 text-right text-slate-500">0</td>
                        <td class="p-2 text-right">4.300.000</td>
                        <td class="p-2 text-right text-slate-500">0</td>
                        <td class="p-2 text-right text-slate-500">0</td>
                        <td class="p-2 text-right text-slate-500">0</td>
                        <td class="p-2 text-right text-slate-500">0</td>
                        <td class="p-2 text-right font-bold text-rose-600 dark:text-rose-400">4.300.000</td>
                    </tr>
                    <tr>
                        <td class="p-2 pl-4 text-slate-700 dark:text-slate-300">ATK, Admin Bank, Transfer & Lainnya</td>
                        <td class="p-2 text-right">447.000</td>
                        <td class="p-2 text-right">182.652</td>
                        <td class="p-2 text-right">211.892</td>
                        <td class="p-2 text-right">296.258</td>
                        <td class="p-2 text-right">306.604</td>
                        <td class="p-2 text-right">600.546</td>
                        <td class="p-2 text-right">376.900</td>
                        <td class="p-2 text-right">304.900</td>
                        <td class="p-2 text-right font-bold text-rose-600 dark:text-rose-400">2.736.752</td>
                    </tr>
                    <tr class="bg-rose-50 dark:bg-rose-950/40 font-bold border-t border-b border-rose-200 dark:border-rose-800">
                        <td class="p-2 text-rose-700 dark:text-rose-300">TOTAL KAS KELUAR (PENGELUARAN)</td>
                        <td class="p-2 text-right">13.468.000</td>
                        <td class="p-2 text-right">6.664.652</td>
                        <td class="p-2 text-right">22.605.900</td>
                        <td class="p-2 text-right">14.358.258</td>
                        <td class="p-2 text-right">10.391.104</td>
                        <td class="p-2 text-right">19.538.546</td>
                        <td class="p-2 text-right">15.297.900</td>
                        <td class="p-2 text-right">18.947.900</td>
                        <td class="p-2 text-right text-rose-600 dark:text-rose-400">121.212.260</td>
                    </tr>

                    {{-- RINGKASAN REKAPITULASI --}}
                    <tr class="bg-emerald-600 text-white font-extrabold text-xs">
                        <td class="p-2.5">SURPLUS KAS BERSIH BERMADANI 2025</td>
                        <td colspan="8" class="p-2.5 text-emerald-100 font-normal">Total Kas Masuk Internal (140,2M) - Total Kas Keluar (121,2M)</td>
                        <td class="p-2.5 text-right text-amber-300 text-sm">Rp 19.015.051</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- CHART.JS INTEGRATION SCRIPT --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function renderRatDashboardCharts() {
        if (typeof Chart === 'undefined') return;

        // Destroy old chart instances if re-rendering
        ['chartKomposisiAset', 'chartKomposisiPendapatan', 'chartKomposisiBeban', 'chartTrenShu', 'chartPertumbuhanSimpanan', 'chartArusKas'].forEach(id => {
            const chartExist = Chart.getChart(id);
            if (chartExist) chartExist.destroy();
        });

        const isDark = document.documentElement.classList.contains('dark');
        const textColor = isDark ? '#94A3B8' : '#64748B';

        // 1. Chart Komposisi Aset (Donut)
        const ctxAset = document.getElementById('chartKomposisiAset')?.getContext('2d');
        if (ctxAset) {
            new Chart(ctxAset, {
                type: 'doughnut',
                data: {
                    labels: ['Simpanan Live DB', 'Surplus Kas (SHU)', 'Aset Tetap Toko', 'Kas Awal Periode', 'Persediaan Dagangan'],
                    datasets: [{
                        data: [211.60, 15.00, 11.02, 6.96, 4.02],
                        backgroundColor: ['#6366F1', '#06B6D4', '#F59E0B', '#10B981', '#EC4899'],
                        borderWidth: 0,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '76%',
                    plugins: { legend: { display: false } }
                }
            });
        }

        // 2. Chart Komposisi Kas Masuk (Donut)
        const ctxPendapatan = document.getElementById('chartKomposisiPendapatan')?.getContext('2d');
        if (ctxPendapatan) {
            new Chart(ctxPendapatan, {
                type: 'doughnut',
                data: {
                    labels: ['Pendapatan Toko', 'Simpanan Wajib', 'Simpanan Sukarela', 'Simpanan Pokok'],
                    datasets: [{
                        data: [94.78, 38.40, 6.45, 0.60],
                        backgroundColor: ['#10B981', '#3B82F6', '#F59E0B', '#8B5CF6'],
                        borderWidth: 0,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '76%',
                    plugins: { legend: { display: false } }
                }
            });
        }

        // 3. Chart Komposisi Beban (Horizontal Bar)
        const ctxBeban = document.getElementById('chartKomposisiBeban')?.getContext('2d');
        if (ctxBeban) {
            new Chart(ctxBeban, {
                type: 'bar',
                data: {
                    labels: ['Gaji Staf', 'Gaji Pengurus', 'Supplier', 'Aset Tetap', 'RAT & Ops'],
                    datasets: [{
                        data: [36.18, 31.25, 28.74, 11.02, 14.02],
                        backgroundColor: '#8B5CF6',
                        borderRadius: 6,
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { grid: { display: false }, ticks: { color: textColor, font: { size: 9 } } },
                        y: { border: { display: false }, ticks: { color: textColor, font: { size: 9, weight: 'bold' } } }
                    }
                }
            });
        }

        // 4. Chart Tren SHU / Surplus (Line)
        const ctxTrenShu = document.getElementById('chartTrenShu')?.getContext('2d');
        if (ctxTrenShu) {
            new Chart(ctxTrenShu, {
                type: 'line',
                data: {
                    labels: ['2021', '2022', '2023', '2024', '2025'],
                    datasets: [{
                        label: 'Surplus (Jt)',
                        data: [5.2, 6.1, 7.3, 12.0, 15.0],
                        borderColor: '#0284C7',
                        backgroundColor: 'rgba(2, 132, 199, 0.1)',
                        fill: true,
                        tension: 0.3,
                        pointRadius: 4,
                        pointBackgroundColor: '#0284C7'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { grid: { display: false }, ticks: { color: textColor, font: { size: 9, weight: 'bold' } } },
                        y: { border: { display: false }, ticks: { color: textColor, font: { size: 9 } } }
                    }
                }
            });
        }

        // 6. Chart Pertumbuhan Simpanan (Grouped Bar)
        const ctxSimpanan = document.getElementById('chartPertumbuhanSimpanan')?.getContext('2d');
        if (ctxSimpanan) {
            new Chart(ctxSimpanan, {
                type: 'bar',
                data: {
                    labels: ['Pokok', 'Wajib', 'Sukarela'],
                    datasets: [
                        { label: 'Live Database', data: [26.2, 185.4, 0], backgroundColor: '#6366F1', borderRadius: 4 },
                        { label: 'CSV Arus Kas', data: [0.6, 38.4, 6.45], backgroundColor: '#10B981', borderRadius: 4 },
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'top', labels: { boxWidth: 8, color: textColor, font: { size: 9, weight: 'bold' } } } },
                    scales: {
                        x: { grid: { display: false }, ticks: { color: textColor, font: { size: 9, weight: 'bold' } } },
                        y: { border: { display: false }, ticks: { color: textColor, font: { size: 9 } } }
                    }
                }
            });
        }

        // 7. Chart Arus Kas (Bar)
        const ctxArusKas = document.getElementById('chartArusKas')?.getContext('2d');
        if (ctxArusKas) {
            new Chart(ctxArusKas, {
                type: 'bar',
                data: {
                    labels: ['Kas Masuk', 'Kas Keluar', 'Surplus SHU'],
                    datasets: [{
                        data: [140.23, 121.21, 15.00],
                        backgroundColor: ['#10B981', '#EF4444', '#3B82F6'],
                        borderRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { grid: { display: false }, ticks: { color: textColor, font: { size: 9, weight: 'bold' } } },
                        y: { border: { display: false }, ticks: { color: textColor, font: { size: 9 } } }
                    }
                }
            });
        }
    }

    document.addEventListener('livewire:navigated', renderRatDashboardCharts);
    document.addEventListener('DOMContentLoaded', renderRatDashboardCharts);

    document.addEventListener('livewire:initialized', () => {
        Livewire.on('rat-charts-reload', () => {
            setTimeout(renderRatDashboardCharts, 100);
        });
    });

    if (window.Livewire) {
        Livewire.hook('commit', ({ respond, succeed }) => {
            succeed(() => {
                setTimeout(renderRatDashboardCharts, 100);
            });
        });
    }
</script>
