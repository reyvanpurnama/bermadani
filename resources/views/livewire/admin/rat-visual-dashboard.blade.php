<div class="space-y-4 font-sans text-slate-800 dark:text-slate-100">
    
    {{-- TOP ACTION TOOLBAR --}}
    <div class="bg-white dark:bg-slate-800 p-3 sm:p-4 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 flex flex-wrap items-center justify-between gap-3 print:hidden">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 rounded-lg flex items-center justify-center font-bold">
                <i class='bx bx-calendar text-xl'></i>
            </div>
            <div>
                <label for="select-tahun-buku" class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Tahun Buku RAT</label>
                <select id="select-tahun-buku" wire:model.live="selectedYear" class="bg-slate-50 dark:bg-slate-700 border border-slate-300 dark:border-slate-600 text-slate-900 dark:text-white text-sm font-bold rounded-lg focus:ring-emerald-500 focus:border-emerald-500 py-1 px-3">
                    @foreach($availableYears as $yr)
                        <option value="{{ $yr }}">Tahun Buku {{ $yr }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white text-xs sm:text-sm font-bold rounded-lg shadow-md transition-all">
                <i class='bx bx-printer text-base'></i> Cetak / Export PDF Infografis
            </button>
            <a href="{{ route('admin.rat-report') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 text-xs sm:text-sm font-bold rounded-lg transition-all border border-slate-300 dark:border-slate-600">
                <i class='bx bx-table text-base'></i> Detail Tabel RAT
            </a>
        </div>
    </div>

    {{-- MAIN POSTER INFOGRAPHIC CONTAINER --}}
    <div class="bg-white dark:bg-slate-900 border-2 border-emerald-700 rounded-2xl p-4 sm:p-6 shadow-2xl max-w-[1440px] mx-auto space-y-4 print:p-0 print:border-none print:shadow-none print:bg-white" id="infographic-poster">
        
        {{-- HEADER BANNER --}}
        <div class="bg-gradient-to-r from-emerald-50 via-white to-emerald-50 dark:from-slate-800 dark:via-slate-800/80 dark:to-slate-800 border border-emerald-200 dark:border-emerald-800/60 rounded-xl p-4 flex flex-col md:flex-row items-center justify-between gap-4 shadow-sm relative overflow-hidden">
            <div class="flex items-center gap-4">
                {{-- LOGO --}}
                <div class="w-16 h-16 bg-white dark:bg-slate-700 rounded-2xl flex items-center justify-center shrink-0 shadow-md border border-emerald-100 dark:border-slate-600 p-2">
                    <img src="{{ asset('images/logo.png') }}" alt="KSPPS Berkah Madani" class="w-full h-full object-contain" onerror="this.src='https://cdn-icons-png.flaticon.com/512/3135/3135715.png'">
                </div>
                <div>
                    <span class="text-[11px] font-bold text-emerald-800 dark:text-emerald-400 tracking-widest uppercase block">KSPPS BERKAH MADANI</span>
                    <h1 class="text-xl sm:text-2xl lg:text-3xl font-black tracking-tight text-slate-900 dark:text-white uppercase leading-none">
                        DASHBOARD KEUANGAN KSPPS BERKAH MADANI
                    </h1>
                    <p class="text-xs sm:text-sm font-bold text-slate-600 dark:text-slate-300 mt-1">
                        Ringkasan Kinerja Tahun {{ $dashboard['year'] }} untuk Rapat Anggota Tahunan (RAT)
                    </p>
                    <p class="text-xs font-serif italic text-emerald-700 dark:text-emerald-400 mt-0.5">
                        “Bersama Anggota, Koperasi Kuat, Manfaat Nyata”
                    </p>
                </div>
            </div>

            {{-- HEADER RIGHT BANNER BADGE --}}
            <div class="bg-emerald-700 text-white rounded-xl p-3.5 flex items-center gap-3 shadow-md border border-emerald-600 shrink-0">
                <div class="w-10 h-10 bg-emerald-800 rounded-lg flex items-center justify-center text-emerald-100 text-2xl shrink-0">
                    <i class='bx bxs-group'></i>
                </div>
                <div class="text-left text-[11px] font-black tracking-wider leading-snug uppercase">
                    KOPERASI MILIK KITA<br>
                    KEPUTUSAN KITA<br>
                    MANFAAT UNTUK KITA
                </div>
            </div>
        </div>

        {{-- 5 TOP KPI METRIC CARDS --}}
        <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
            
            {{-- KPI 1: TOTAL ASET --}}
            <div class="bg-slate-50 dark:bg-slate-800 border-2 border-blue-200 dark:border-blue-900 rounded-xl p-3 flex items-center gap-3 shadow-sm hover:shadow-md transition-shadow">
                <div class="w-11 h-11 bg-blue-600 text-white rounded-full flex items-center justify-center text-2xl shrink-0 shadow-md">
                    <i class='bx bx-line-chart'></i>
                </div>
                <div class="min-w-0">
                    <span class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider block">TOTAL ASET</span>
                    <div class="text-lg sm:text-xl font-black text-slate-900 dark:text-white leading-tight">
                        Rp{{ $dashboard['kpi']['totalAset']['val'] }} <span class="text-xs font-bold text-slate-600 dark:text-slate-400">Juta</span>
                    </div>
                    <span class="text-[10px] font-extrabold text-emerald-600 dark:text-emerald-400 flex items-center gap-0.5 mt-0.5">
                        <i class='bx bx-trending-up text-xs'></i> {{ $dashboard['kpi']['totalAset']['growth'] }}
                    </span>
                </div>
            </div>

            {{-- KPI 2: TOTAL PEMBIAYAAN --}}
            <div class="bg-slate-50 dark:bg-slate-800 border-2 border-emerald-200 dark:border-emerald-900 rounded-xl p-3 flex items-center gap-3 shadow-sm hover:shadow-md transition-shadow">
                <div class="w-11 h-11 bg-emerald-600 text-white rounded-full flex items-center justify-center text-2xl shrink-0 shadow-md">
                    <i class='bx bx-handshake'></i>
                </div>
                <div class="min-w-0">
                    <span class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider block">TOTAL PEMBIAYAAN</span>
                    <div class="text-lg sm:text-xl font-black text-slate-900 dark:text-white leading-tight">
                        Rp{{ $dashboard['kpi']['totalPembiayaan']['val'] }} <span class="text-xs font-bold text-slate-600 dark:text-slate-400">Juta</span>
                    </div>
                    <span class="text-[10px] font-extrabold text-emerald-600 dark:text-emerald-400 flex items-center gap-0.5 mt-0.5">
                        <i class='bx bx-trending-up text-xs'></i> {{ $dashboard['kpi']['totalPembiayaan']['growth'] }}
                    </span>
                </div>
            </div>

            {{-- KPI 3: SISA HASIL USAHA --}}
            <div class="bg-slate-50 dark:bg-slate-800 border-2 border-amber-200 dark:border-amber-900 rounded-xl p-3 flex items-center gap-3 shadow-sm hover:shadow-md transition-shadow">
                <div class="w-11 h-11 bg-amber-500 text-white rounded-full flex items-center justify-center text-2xl shrink-0 shadow-md">
                    <i class='bx bx-coin-stack'></i>
                </div>
                <div class="min-w-0">
                    <span class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider block">SISA HASIL USAHA (SHU)</span>
                    <div class="text-lg sm:text-xl font-black text-slate-900 dark:text-white leading-tight">
                        Rp{{ $dashboard['kpi']['shu']['val'] }} <span class="text-xs font-bold text-slate-600 dark:text-slate-400">Juta</span>
                    </div>
                    <span class="text-[10px] font-extrabold text-emerald-600 dark:text-emerald-400 flex items-center gap-0.5 mt-0.5">
                        <i class='bx bx-trending-up text-xs'></i> {{ $dashboard['kpi']['shu']['growth'] }}
                    </span>
                </div>
            </div>

            {{-- KPI 4: JUMLAH ANGGOTA --}}
            <div class="bg-slate-50 dark:bg-slate-800 border-2 border-purple-200 dark:border-purple-900 rounded-xl p-3 flex items-center gap-3 shadow-sm hover:shadow-md transition-shadow">
                <div class="w-11 h-11 bg-purple-600 text-white rounded-full flex items-center justify-center text-2xl shrink-0 shadow-md">
                    <i class='bx bx-group'></i>
                </div>
                <div class="min-w-0">
                    <span class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider block">JUMLAH ANGGOTA</span>
                    <div class="text-lg sm:text-xl font-black text-slate-900 dark:text-white leading-tight">
                        {{ $dashboard['kpi']['jumlahAnggota']['val'] }} <span class="text-xs font-bold text-slate-600 dark:text-slate-400">Orang</span>
                    </div>
                    <span class="text-[10px] font-extrabold text-emerald-600 dark:text-emerald-400 flex items-center gap-0.5 mt-0.5">
                        <i class='bx bx-user-plus text-xs'></i> {{ $dashboard['kpi']['jumlahAnggota']['growth'] }}
                    </span>
                </div>
            </div>

            {{-- KPI 5: KAS & BANK --}}
            <div class="bg-slate-50 dark:bg-slate-800 border-2 border-teal-200 dark:border-teal-900 rounded-xl p-3 flex items-center gap-3 shadow-sm hover:shadow-md transition-shadow col-span-2 md:col-span-1">
                <div class="w-11 h-11 bg-teal-600 text-white rounded-full flex items-center justify-center text-2xl shrink-0 shadow-md">
                    <i class='bx bx-wallet'></i>
                </div>
                <div class="min-w-0">
                    <span class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider block">KAS & BANK</span>
                    <div class="text-lg sm:text-xl font-black text-slate-900 dark:text-white leading-tight">
                        Rp{{ $dashboard['kpi']['kasBank']['val'] }} <span class="text-xs font-bold text-slate-600 dark:text-slate-400">Juta</span>
                    </div>
                    <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400 block mt-0.5">
                        {{ $dashboard['kpi']['kasBank']['note'] }}
                    </span>
                </div>
            </div>

        </div>

        {{-- 8 MODUL INFOGRAFIS (GRID 4 COLS X 2 ROWS) --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3.5">
            
            {{-- CARD 1: KOMPOSISI ASET --}}
            <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-sm flex flex-col justify-between overflow-hidden">
                <div>
                    <div class="bg-[#004B87] text-white px-3 py-2 text-xs font-black tracking-wide uppercase flex items-center gap-2">
                        <span class="bg-white/20 px-1.5 py-0.5 rounded text-[10px]">1</span> KOMPOSISI ASET
                    </div>
                    <div class="p-3">
                        <span class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 text-center block mb-1">Per 31 Desember {{ $dashboard['year'] }}</span>
                        
                        {{-- SIDE BY SIDE: DONUT + HTML LEGEND (NO OVERLAP!) --}}
                        <div class="grid grid-cols-1 sm:grid-cols-12 gap-2 items-center my-2">
                            <div class="sm:col-span-5 h-36 relative flex items-center justify-center">
                                <canvas id="chartKomposisiAset"></canvas>
                                <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                                    <span class="text-[8px] font-black text-slate-400 uppercase">TOTAL ASET</span>
                                    <span class="text-xs font-black text-slate-900 dark:text-white">Rp{{ $dashboard['komposisiAset']['total'] }}</span>
                                    <span class="text-[8px] font-bold text-slate-500">Juta</span>
                                </div>
                            </div>
                            <div class="sm:col-span-7 space-y-1 text-[10px] font-bold">
                                @foreach($dashboard['komposisiAset']['items'] as $item)
                                    <div class="flex items-center gap-1.5 leading-tight">
                                        <span class="w-2.5 h-2.5 rounded shrink-0" style="background-color: {{ $item['color'] }}"></span>
                                        <div class="min-w-0">
                                            <span class="text-slate-700 dark:text-slate-300 block truncate">{{ $item['label'] }}</span>
                                            <span class="text-slate-900 dark:text-white font-extrabold">{{ $item['val'] }} ({{ $item['pct'] }})</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="p-2 pt-0">
                    <div class="bg-[#004B87] text-white text-[11px] font-bold p-2 rounded-lg text-center leading-tight">
                        {{ $dashboard['komposisiAset']['footnote'] }}
                    </div>
                </div>
            </div>

            {{-- CARD 2: KOMPOSISI PENDAPATAN --}}
            <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-sm flex flex-col justify-between overflow-hidden">
                <div>
                    <div class="bg-[#2B7A3E] text-white px-3 py-2 text-xs font-black tracking-wide uppercase flex items-center gap-2">
                        <span class="bg-white/20 px-1.5 py-0.5 rounded text-[10px]">2</span> KOMPOSISI PENDAPATAN
                    </div>
                    <div class="p-3">
                        <span class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 text-center block mb-1">Untuk Tahun Berakhir 31 Des {{ $dashboard['year'] }}</span>
                        
                        {{-- SIDE BY SIDE: DONUT + HTML LEGEND (NO OVERLAP!) --}}
                        <div class="grid grid-cols-1 sm:grid-cols-12 gap-2 items-center my-2">
                            <div class="sm:col-span-5 h-36 relative flex items-center justify-center">
                                <canvas id="chartKomposisiPendapatan"></canvas>
                                <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                                    <span class="text-[8px] font-black text-slate-400 uppercase">TOTAL</span>
                                    <span class="text-xs font-black text-slate-900 dark:text-white">Rp{{ $dashboard['komposisiPendapatan']['total'] }}</span>
                                    <span class="text-[8px] font-bold text-slate-500">Juta</span>
                                </div>
                            </div>
                            <div class="sm:col-span-7 space-y-1.5 text-[10px] font-bold">
                                @foreach($dashboard['komposisiPendapatan']['items'] as $item)
                                    <div class="flex items-center gap-1.5 leading-tight">
                                        <span class="w-2.5 h-2.5 rounded shrink-0" style="background-color: {{ $item['color'] }}"></span>
                                        <div class="min-w-0">
                                            <span class="text-slate-700 dark:text-slate-300 block truncate">{{ $item['label'] }}</span>
                                            <span class="text-slate-900 dark:text-white font-extrabold">{{ $item['val'] }} ({{ $item['pct'] }})</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="p-2 pt-0">
                    <div class="bg-[#2B7A3E] text-white text-[11px] font-bold p-2 rounded-lg text-center leading-tight">
                        {{ $dashboard['komposisiPendapatan']['footnote'] }}
                    </div>
                </div>
            </div>

            {{-- CARD 3: KOMPOSISI BEBAN OPERASIONAL --}}
            <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-sm flex flex-col justify-between overflow-hidden">
                <div>
                    <div class="bg-[#6B3BA7] text-white px-3 py-2 text-xs font-black tracking-wide uppercase flex items-center gap-2">
                        <span class="bg-white/20 px-1.5 py-0.5 rounded text-[10px]">3</span> KOMPOSISI BEBAN OPERASIONAL
                    </div>
                    <div class="p-3">
                        <span class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 text-center block mb-1">Untuk Tahun Berakhir 31 Des {{ $dashboard['year'] }}</span>
                        <div class="h-40">
                            <canvas id="chartKomposisiBeban"></canvas>
                        </div>
                    </div>
                </div>
                <div class="p-2 pt-0">
                    <div class="bg-[#6B3BA7] text-white text-[11px] font-bold p-2 rounded-lg text-center leading-tight">
                        {{ $dashboard['komposisiBeban']['footnote'] }}
                    </div>
                </div>
            </div>

            {{-- CARD 4: TREN SHU 5 TAHUN TERAKHIR --}}
            <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-sm flex flex-col justify-between overflow-hidden">
                <div>
                    <div class="bg-[#1D70B8] text-white px-3 py-2 text-xs font-black tracking-wide uppercase flex items-center gap-2">
                        <span class="bg-white/20 px-1.5 py-0.5 rounded text-[10px]">4</span> TREN SHU 5 TAHUN TERAKHIR
                    </div>
                    <div class="p-3">
                        <span class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 text-center block mb-1">(Dalam Juta Rupiah)</span>
                        <div class="h-40">
                            <canvas id="chartTrenShu"></canvas>
                        </div>
                    </div>
                </div>
                <div class="p-2 pt-0">
                    <div class="bg-[#1D70B8] text-white text-[11px] font-bold p-2 rounded-lg text-center leading-tight">
                        {{ $dashboard['trenShu']['footnote'] }}
                    </div>
                </div>
            </div>

            {{-- CARD 5: RASIO PEMBIAYAAN BERMASALAH (NPF) --}}
            <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-sm flex flex-col justify-between overflow-hidden">
                <div>
                    <div class="bg-[#D97706] text-white px-3 py-2 text-xs font-black tracking-wide uppercase flex items-center gap-2">
                        <span class="bg-white/20 px-1.5 py-0.5 rounded text-[10px]">5</span> RASIO NPF (PEMBIAYAAN BERMASALAH)
                    </div>
                    <div class="p-3">
                        <span class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 text-center block mb-1">Per 31 Desember {{ $dashboard['year'] }}</span>
                        
                        {{-- GAUGE SPEEDOMETER WITH NEEDLE POINTING TO 2,3% --}}
                        <div class="flex flex-col items-center justify-center my-1 relative">
                            <svg viewBox="0 0 200 110" class="w-44 h-24">
                                <!-- Gauge Slices -->
                                <path d="M 20 100 A 80 80 0 0 1 65 35" fill="none" stroke="#22C55E" stroke-width="22" />
                                <path d="M 65 35 A 80 80 0 0 1 100 20" fill="none" stroke="#EAB308" stroke-width="22" />
                                <path d="M 100 20 A 80 80 0 0 1 135 35" fill="none" stroke="#F97316" stroke-width="22" />
                                <path d="M 135 35 A 80 80 0 0 1 180 100" fill="none" stroke="#EF4444" stroke-width="22" />
                                
                                <!-- Needle (-62 deg points to 2,3% in Green zone) -->
                                <g transform="rotate(-62 100 100)">
                                    <line x1="100" y1="100" x2="35" y2="100" stroke="#0F172A" stroke-width="4.5" stroke-linecap="round" />
                                    <circle cx="100" cy="100" r="7" fill="#0F172A" />
                                </g>
                            </svg>

                            <div class="w-full flex justify-between px-1 text-[9px] font-black text-slate-600 dark:text-slate-300 text-center">
                                <span>Lancar<br>(0-2%)</span>
                                <span>Kurang Lancar<br>(2-5%)</span>
                                <span>Diragukan<br>(5-8%)</span>
                                <span>Macet<br>(&gt;8%)</span>
                            </div>

                            <div class="text-center mt-1">
                                <span class="text-2xl font-black text-slate-900 dark:text-white">{{ $dashboard['npf']['val'] }}</span>
                                <span class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 block">({{ $dashboard['npf']['status'] }})</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="p-2 pt-0">
                    <div class="bg-[#D97706] text-white text-[11px] font-bold p-2 rounded-lg text-center leading-tight">
                        {{ $dashboard['npf']['footnote'] }}
                    </div>
                </div>
            </div>

            {{-- CARD 6: PERTUMBUHAN SIMPANAN ANGGOTA --}}
            <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-sm flex flex-col justify-between overflow-hidden">
                <div>
                    <div class="bg-[#059669] text-white px-3 py-2 text-xs font-black tracking-wide uppercase flex items-center gap-2">
                        <span class="bg-white/20 px-1.5 py-0.5 rounded text-[10px]">6</span> PERTUMBUHAN SIMPANAN ANGGOTA
                    </div>
                    <div class="p-3">
                        <span class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 text-center block mb-1">(Dalam Juta Rupiah)</span>
                        <div class="h-40">
                            <canvas id="chartPertumbuhanSimpanan"></canvas>
                        </div>
                    </div>
                </div>
                <div class="p-2 pt-0">
                    <div class="bg-[#059669] text-white text-[11px] font-bold p-2 rounded-lg text-center leading-tight">
                        {{ $dashboard['pertumbuhanSimpanan']['footnote'] }}
                    </div>
                </div>
            </div>

            {{-- CARD 7: ARUS KAS TAHUN 2025 --}}
            <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-sm flex flex-col justify-between overflow-hidden">
                <div>
                    <div class="bg-[#2563EB] text-white px-3 py-2 text-xs font-black tracking-wide uppercase flex items-center gap-2">
                        <span class="bg-white/20 px-1.5 py-0.5 rounded text-[10px]">7</span> ARUS KAS TAHUN {{ $dashboard['year'] }}
                    </div>
                    <div class="p-3">
                        <span class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 text-center block mb-1">(Dalam Juta Rupiah)</span>
                        <div class="h-40">
                            <canvas id="chartArusKas"></canvas>
                        </div>
                    </div>
                </div>
                <div class="p-2 pt-0">
                    <div class="bg-[#2563EB] text-white text-[11px] font-bold p-2 rounded-lg text-center leading-tight">
                        {{ $dashboard['arusKas']['footnote'] }}
                    </div>
                </div>
            </div>

            {{-- CARD 8: RINGKASAN KESEHATAN KOPERASI --}}
            <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-sm flex flex-col justify-between overflow-hidden">
                <div>
                    <div class="bg-[#DC2626] text-white px-3 py-2 text-xs font-black tracking-wide uppercase flex items-center gap-2">
                        <span class="bg-white/20 px-1.5 py-0.5 rounded text-[10px]">8</span> RINGKASAN KESEHATAN KOPERASI
                    </div>
                    <div class="p-3 space-y-1.5">
                        @foreach($dashboard['kesehatan'] as $item)
                            <div class="flex items-center justify-between p-1.5 rounded-lg bg-slate-50 dark:bg-slate-700/60 border border-slate-200 dark:border-slate-600 text-[11px]">
                                <span class="font-bold text-slate-800 dark:text-slate-200 flex items-center gap-1">
                                    <i class='bx bx-check-circle text-emerald-600 text-sm'></i> {{ $item['label'] }}
                                </span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase {{ $item['bg'] }}">
                                    {{ $item['status'] }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="p-2 pt-0">
                    <div class="bg-[#DC2626] text-white text-[11px] font-bold p-2 rounded-lg text-center leading-tight">
                        Koperasi dalam kondisi sehat dan berkelanjutan.
                    </div>
                </div>
            </div>

        </div>

        {{-- FOOTER BANNER (3 SECTIONS) --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            
            {{-- FOOTER LEFT --}}
            <div class="bg-emerald-50 dark:bg-slate-800/80 rounded-xl p-3.5 border border-emerald-200 dark:border-slate-700 flex items-start gap-3">
                <div class="w-8 h-8 bg-emerald-700 text-white rounded-lg flex items-center justify-center shrink-0 mt-0.5 shadow">
                    <i class='bx bx-map-pin text-lg'></i>
                </div>
                <div class="text-xs space-y-1">
                    <span class="font-black text-emerald-900 dark:text-emerald-300 uppercase tracking-wider block">INFORMASI UNTUK PENGAMBILAN KEPUTUSAN</span>
                    <ul class="text-slate-700 dark:text-slate-300 space-y-1 text-[11px] font-medium">
                        <li class="flex items-start gap-1"><i class='bx bx-check text-emerald-600 font-bold text-sm'></i> Pertahankan kualitas pembiayaan dan tingkatkan penagihan.</li>
                        <li class="flex items-start gap-1"><i class='bx bx-check text-emerald-600 font-bold text-sm'></i> Tingkatkan simpanan sukarela & berjangka melalui program menarik.</li>
                        <li class="flex items-start gap-1"><i class='bx bx-check text-emerald-600 font-bold text-sm'></i> Efisiensi beban operasional untuk meningkatkan SHU.</li>
                    </ul>
                </div>
            </div>

            {{-- FOOTER CENTER --}}
            <div class="bg-gradient-to-r from-emerald-700 to-teal-800 text-white rounded-xl p-3.5 flex items-center justify-center gap-3 text-center shadow-md">
                <div class="w-11 h-11 bg-white/20 rounded-xl flex items-center justify-center text-2xl shrink-0">
                    <i class='bx bxs-group'></i>
                </div>
                <div class="text-left">
                    <span class="text-xs font-black tracking-wider block uppercase text-emerald-100">KEPUTUSAN KITA</span>
                    <span class="text-sm font-black tracking-tight block uppercase">MENENTUKAN MASA DEPAN KOPERASI KITA</span>
                </div>
            </div>

            {{-- FOOTER RIGHT --}}
            <div class="bg-amber-50 dark:bg-slate-800/80 rounded-xl p-3.5 border border-amber-200 dark:border-slate-700 flex items-start gap-3">
                <div class="w-8 h-8 bg-amber-500 text-white rounded-lg flex items-center justify-center shrink-0 mt-0.5 shadow">
                    <i class='bx bx-bulb text-lg'></i>
                </div>
                <div class="text-xs">
                    <span class="font-black text-amber-900 dark:text-amber-300 uppercase tracking-wider block">CATATAN:</span>
                    <p class="text-slate-600 dark:text-slate-300 text-[11px] leading-relaxed mt-0.5">
                        Data dalam dashboard ini adalah ringkasan dari laporan keuangan KSPPS Berkah Madani per 31 Desember {{ $dashboard['year'] }} dan dapat berubah sesuai hasil audit dan keputusan RAT.
                    </p>
                </div>
            </div>

        </div>

    </div>

</div>

{{-- CHART.JS SCRIPT INITIALIZATION --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function renderRatDashboardCharts() {
        if (typeof Chart === 'undefined') return;

        // Destroy old chart instances if re-rendering
        ['chartKomposisiAset', 'chartKomposisiPendapatan', 'chartKomposisiBeban', 'chartTrenShu', 'chartPertumbuhanSimpanan', 'chartArusKas'].forEach(id => {
            const chartExist = Chart.getChart(id);
            if (chartExist) chartExist.destroy();
        });

        // 1. Chart Komposisi Aset (Donut without Legend to prevent overlap)
        const ctxAset = document.getElementById('chartKomposisiAset')?.getContext('2d');
        if (ctxAset) {
            new Chart(ctxAset, {
                type: 'doughnut',
                data: {
                    labels: ['Piutang Pembiayaan', 'Kas & Bank', 'Aset Tetap', 'Aset Lainnya'],
                    datasets: [{
                        data: [285.0, 45.2, 18.5, 5.0],
                        backgroundColor: ['#004B87', '#0EA5E9', '#F59E0B', '#94A3B8'],
                        borderWidth: 2,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '72%',
                    plugins: { legend: { display: false } }
                }
            });
        }

        // 2. Chart Komposisi Pendapatan (Donut without Legend)
        const ctxPendapatan = document.getElementById('chartKomposisiPendapatan')?.getContext('2d');
        if (ctxPendapatan) {
            new Chart(ctxPendapatan, {
                type: 'doughnut',
                data: {
                    labels: ['Margin Pembiayaan', 'Pendapatan Administrasi', 'Lain-lain'],
                    datasets: [{
                        data: [48.0, 5.0, 2.0],
                        backgroundColor: ['#2B7A3E', '#0284C7', '#EA580C'],
                        borderWidth: 2,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '72%',
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
                    labels: @json($dashboard['komposisiBeban']['labels']),
                    datasets: [{
                        data: @json($dashboard['komposisiBeban']['data']),
                        backgroundColor: '#6B3BA7',
                        borderRadius: 4,
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { grid: { display: false }, ticks: { font: { size: 9 } } },
                        y: { ticks: { font: { size: 8, weight: 'bold' } } }
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
                    labels: @json($dashboard['trenShu']['years']),
                    datasets: [{
                        label: 'SHU (Juta Rp)',
                        data: @json($dashboard['trenShu']['data']),
                        borderColor: '#1D70B8',
                        backgroundColor: 'rgba(29, 112, 184, 0.15)',
                        fill: true,
                        tension: 0.3,
                        pointRadius: 5,
                        pointBackgroundColor: '#1D70B8',
                        borderWidth: 3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { ticks: { font: { size: 9, weight: 'bold' } } },
                        y: { ticks: { font: { size: 9 } } }
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
                    labels: @json($dashboard['pertumbuhanSimpanan']['categories']),
                    datasets: [
                        { label: '2023', data: [28, 22, 95, 50], backgroundColor: '#94A3B8' },
                        { label: '2024', data: [32, 25, 105, 60], backgroundColor: '#1D4ED8' },
                        { label: '2025', data: [35, 28, 120, 67], backgroundColor: '#059669' },
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'top', labels: { boxWidth: 10, font: { size: 9, weight: 'bold' } } } },
                    scales: {
                        x: { ticks: { font: { size: 9, weight: 'bold' } } },
                        y: { ticks: { font: { size: 9 } } }
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
                    labels: ['Aktivitas Operasi', 'Aktivitas Investasi', 'Aktivitas Pendanaan'],
                    datasets: [{
                        data: [15.0, -8.0, 32.8],
                        backgroundColor: ['#2563EB', '#EF4444', '#059669'],
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { ticks: { font: { size: 8, weight: 'bold' } } },
                        y: { ticks: { font: { size: 9 } } }
                    }
                }
            });
        }
    }

    document.addEventListener('livewire:navigated', renderRatDashboardCharts);
    document.addEventListener('DOMContentLoaded', renderRatDashboardCharts);
</script>
