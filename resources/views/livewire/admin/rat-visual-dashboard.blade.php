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
        
        {{-- KPI 1: TOTAL ASET --}}
        <div class="bg-white dark:bg-darkCard p-5 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 flex items-center gap-4 relative overflow-hidden group">
            <div class="w-12 h-12 rounded-xl bg-indigo-50 dark:bg-indigo-500/10 flex items-center justify-center text-indigo-600 dark:text-indigo-400 text-2xl shrink-0 group-hover:scale-110 transition-transform">
                <i class='bx bx-line-chart'></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Total Aset</p>
                <h3 class="text-lg font-bold text-slate-800 dark:text-white" title="Rp {{ $dashboard['kpi']['totalAset']['raw'] }}">
                    Rp {{ $dashboard['kpi']['totalAset']['val'] }} Jt
                </h3>
                <span class="text-[10px] text-slate-400 block font-medium">Rp {{ $dashboard['kpi']['totalAset']['raw'] }}</span>
            </div>
        </div>

        {{-- KPI 2: TOTAL PEMBIAYAAN --}}
        <div class="bg-white dark:bg-darkCard p-5 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 flex items-center gap-4 relative overflow-hidden group">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center text-emerald-600 dark:text-emerald-400 text-2xl shrink-0 group-hover:scale-110 transition-transform">
                <i class='bx bx-handshake'></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Total Pembiayaan</p>
                <h3 class="text-lg font-bold text-slate-800 dark:text-white" title="Rp {{ $dashboard['kpi']['totalPembiayaan']['raw'] }}">
                    Rp {{ $dashboard['kpi']['totalPembiayaan']['val'] }} Jt
                </h3>
                <span class="text-[10px] text-slate-400 block font-medium">Rp {{ $dashboard['kpi']['totalPembiayaan']['raw'] }}</span>
            </div>
        </div>

        {{-- KPI 3: SISA HASIL USAHA --}}
        <div class="bg-white dark:bg-darkCard p-5 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 flex items-center gap-4 relative overflow-hidden group">
            <div class="w-12 h-12 rounded-xl bg-amber-50 dark:bg-amber-500/10 flex items-center justify-center text-amber-600 dark:text-amber-400 text-2xl shrink-0 group-hover:scale-110 transition-transform">
                <i class='bx bx-coin-stack'></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">SHU Tahun Ini</p>
                <h3 class="text-lg font-bold text-slate-800 dark:text-white" title="Rp {{ $dashboard['kpi']['shu']['raw'] }}">
                    Rp {{ $dashboard['kpi']['shu']['val'] }} Jt
                </h3>
                <span class="text-[10px] text-slate-400 block font-medium">Rp {{ $dashboard['kpi']['shu']['raw'] }}</span>
            </div>
        </div>

        {{-- KPI 4: JUMLAH ANGGOTA --}}
        <div class="bg-white dark:bg-darkCard p-5 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 flex items-center gap-4 relative overflow-hidden group">
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
        </div>

        {{-- KPI 5: KAS & BANK --}}
        <div class="bg-white dark:bg-darkCard p-5 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 flex items-center gap-4 relative overflow-hidden group">
            <div class="w-12 h-12 rounded-xl bg-teal-50 dark:bg-teal-500/10 flex items-center justify-center text-teal-600 dark:text-teal-400 text-2xl shrink-0 group-hover:scale-110 transition-transform">
                <i class='bx bx-wallet'></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Kas & Bank</p>
                <h3 class="text-lg font-bold text-slate-800 dark:text-white" title="Rp {{ $dashboard['kpi']['kasBank']['raw'] }}">
                    Rp {{ $dashboard['kpi']['kasBank']['val'] }} Jt
                </h3>
                <span class="text-[10px] text-slate-400 font-medium block">
                    Rp {{ $dashboard['kpi']['kasBank']['raw'] }}
                </span>
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
                        <span class="text-[9px] font-bold text-slate-400 uppercase">ASET</span>
                        <span class="text-sm font-bold text-slate-800 dark:text-white">Rp 354 Jt</span>
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
                    Mayoritas aset disalurkan untuk pembiayaan anggota (80.5%).
                </span>
            </div>
        </div>

        {{-- CARD 2: KOMPOSISI PENDAPATAN --}}
        <div class="bg-white dark:bg-darkCard p-5 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 flex flex-col justify-between">
            <div>
                <div class="flex items-center gap-2 mb-3">
                    <span class="w-7 h-7 rounded-lg bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold text-xs">2</span>
                    <h3 class="text-sm font-bold text-slate-800 dark:text-white">Komposisi Pendapatan</h3>
                </div>

                <div class="h-36 relative flex items-center justify-center my-2">
                    <canvas id="chartKomposisiPendapatan"></canvas>
                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                        <span class="text-[9px] font-bold text-slate-400 uppercase">TOTAL</span>
                        <span class="text-sm font-bold text-slate-800 dark:text-white">Rp 55 Jt</span>
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
                    Pendapatan utama didominasi oleh margin pembiayaan (87.3%).
                </span>
            </div>
        </div>

        {{-- CARD 3: BEBAN OPERASIONAL --}}
        <div class="bg-white dark:bg-darkCard p-5 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 flex flex-col justify-between">
            <div>
                <div class="flex items-center gap-2 mb-3">
                    <span class="w-7 h-7 rounded-lg bg-purple-50 dark:bg-purple-500/10 text-purple-600 dark:text-purple-400 flex items-center justify-center font-bold text-xs">3</span>
                    <h3 class="text-sm font-bold text-slate-800 dark:text-white">Beban Operasional</h3>
                </div>

                <div class="h-32 my-1">
                    <canvas id="chartKomposisiBeban"></canvas>
                </div>

                <div class="space-y-1 mt-2 text-[10px] font-medium text-slate-600 dark:text-slate-300 border-t border-slate-100 dark:border-slate-800 pt-2">
                    <div class="flex justify-between"><span>Gaji Pengurus/Staf:</span><span class="font-bold text-purple-600 dark:text-purple-400">Rp 20.000.000</span></div>
                    <div class="flex justify-between"><span>Operasional & RAT:</span><span class="font-bold text-slate-800 dark:text-white">Rp 17.500.000</span></div>
                    <div class="flex justify-between"><span>Penyusutan Inventaris:</span><span class="font-bold text-slate-800 dark:text-white">Rp 3.000.000</span></div>
                    <div class="flex justify-between"><span>ATK & Cetak:</span><span class="font-bold text-slate-800 dark:text-white">Rp 2.500.000</span></div>
                    <div class="flex justify-between"><span>Listrik & Air:</span><span class="font-bold text-slate-800 dark:text-white">Rp 2.000.000</span></div>
                </div>
            </div>
            <div class="mt-3 pt-2 border-t border-slate-100 dark:border-slate-700/60">
                <span class="text-[10px] text-slate-400 font-medium leading-tight block">
                    Total Beban: <strong class="text-slate-800 dark:text-white">Rp 45.000.000</strong>
                </span>
            </div>
        </div>

        {{-- CARD 4: TREN SHU & DISTRIBUSI --}}
        <div class="bg-white dark:bg-darkCard p-5 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 flex flex-col justify-between">
            <div>
                <div class="flex items-center gap-2 mb-3">
                    <span class="w-7 h-7 rounded-lg bg-sky-50 dark:bg-sky-500/10 text-sky-600 dark:text-sky-400 flex items-center justify-center font-bold text-xs">4</span>
                    <h3 class="text-sm font-bold text-slate-800 dark:text-white">Tren SHU & Alokasi</h3>
                </div>

                <div class="h-32 my-1">
                    <canvas id="chartTrenShu"></canvas>
                </div>

                <div class="space-y-1 mt-2 text-[10px] font-medium text-slate-600 dark:text-slate-300 border-t border-slate-100 dark:border-slate-800 pt-2">
                    <div class="flex justify-between"><span>Cadangan (25%):</span><span class="font-bold text-amber-600 dark:text-amber-400">Rp 2.500.000</span></div>
                    <div class="flex justify-between"><span>Jasa Simpanan (30%):</span><span class="font-bold text-emerald-600 dark:text-emerald-400">Rp 3.000.000</span></div>
                    <div class="flex justify-between"><span>Jasa Usaha (25%):</span><span class="font-bold text-sky-600 dark:text-sky-400">Rp 2.500.000</span></div>
                    <div class="flex justify-between"><span>Pengurus & Pengawas (10%):</span><span class="font-bold text-slate-800 dark:text-white">Rp 1.000.000</span></div>
                    <div class="flex justify-between"><span>Pendidikan & Sosial (10%):</span><span class="font-bold text-slate-800 dark:text-white">Rp 1.000.000</span></div>
                </div>
            </div>
            <div class="mt-3 pt-2 border-t border-slate-100 dark:border-slate-700/60">
                <span class="text-[10px] text-slate-400 font-medium leading-tight block">
                    Total SHU Bersih: <strong class="text-emerald-600 dark:text-emerald-400">Rp 10.000.000</strong>
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
                    <div class="flex justify-between"><span>Simpanan Pokok:</span><span class="font-bold text-slate-800 dark:text-white">Rp 35.000.000</span></div>
                    <div class="flex justify-between"><span>Simpanan Wajib:</span><span class="font-bold text-blue-600 dark:text-blue-400">Rp 28.000.000</span></div>
                    <div class="flex justify-between"><span>Simpanan Sukarela:</span><span class="font-bold text-emerald-600 dark:text-emerald-400">Rp 120.000.000</span></div>
                    <div class="flex justify-between"><span>Simpanan Berjangka:</span><span class="font-bold text-indigo-600 dark:text-indigo-400">Rp 67.000.000</span></div>
                </div>
            </div>
            <div class="mt-3 pt-2 border-t border-slate-100 dark:border-slate-700/60">
                <span class="text-[10px] text-slate-400 font-medium leading-tight block">
                    Total Simpanan: <strong class="text-emerald-600 dark:text-emerald-400">Rp 250.000.000</strong>
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

                <div class="h-44 my-2">
                    <canvas id="chartArusKas"></canvas>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-700/60">
                <span class="text-[11px] text-slate-500 dark:text-slate-400 font-medium leading-tight block">
                    Kas bersih mengalami kenaikan +Rp 39.8 juta tahun ini.
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
                    labels: ['Piutang Pembiayaan', 'Kas & Bank', 'Aset Tetap', 'Aset Lainnya'],
                    datasets: [{
                        data: [285.0, 45.2, 18.5, 5.0],
                        backgroundColor: ['#6366F1', '#06B6D4', '#F59E0B', '#94A3B8'],
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

        // 2. Chart Komposisi Pendapatan (Donut)
        const ctxPendapatan = document.getElementById('chartKomposisiPendapatan')?.getContext('2d');
        if (ctxPendapatan) {
            new Chart(ctxPendapatan, {
                type: 'doughnut',
                data: {
                    labels: ['Margin Pembiayaan', 'Pendapatan Administrasi', 'Lain-lain'],
                    datasets: [{
                        data: [48.0, 5.0, 2.0],
                        backgroundColor: ['#10B981', '#3B82F6', '#F97316'],
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
                    labels: ['Gaji', 'Operasional', 'Penyusutan', 'ATK', 'Air & Listrik'],
                    datasets: [{
                        data: [20.0, 17.5, 3.0, 2.5, 2.0],
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

        // 4. Chart Tren SHU 5 Tahun (Line)
        const ctxTrenShu = document.getElementById('chartTrenShu')?.getContext('2d');
        if (ctxTrenShu) {
            new Chart(ctxTrenShu, {
                type: 'line',
                data: {
                    labels: ['2021', '2022', '2023', '2024', '2025'],
                    datasets: [{
                        label: 'SHU (Jt)',
                        data: [5.2, 6.1, 7.3, 8.0, 10.0],
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
                    labels: ['Pokok', 'Wajib', 'Sukarela', 'Berjangka'],
                    datasets: [
                        { label: '2023', data: [28, 22, 95, 50], backgroundColor: '#CBD5E1', borderRadius: 4 },
                        { label: '2024', data: [32, 25, 105, 60], backgroundColor: '#6366F1', borderRadius: 4 },
                        { label: '2025', data: [35, 28, 120, 67], backgroundColor: '#10B981', borderRadius: 4 },
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
                    labels: ['Operasi', 'Investasi', 'Pendanaan'],
                    datasets: [{
                        data: [15.0, -8.0, 32.8],
                        backgroundColor: ['#3B82F6', '#EF4444', '#10B981'],
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
