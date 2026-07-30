<div class="min-h-screen bg-slate-100 dark:bg-slate-900 p-3 sm:p-6 print:p-0 print:bg-white text-slate-800 dark:text-slate-100 font-sans" id="rat-dashboard-container">
    
    {{-- Top Action Toolbar (Hidden when printing) --}}
    <div class="mb-4 flex flex-wrap items-center justify-between gap-3 bg-white dark:bg-slate-800 p-3 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 print:hidden">
        <div class="flex items-center gap-3">
            <span class="font-bold text-sm text-slate-700 dark:text-slate-200 flex items-center gap-1.5">
                <i class='bx bx-calendar text-emerald-600 text-lg'></i> Pilih Tahun Buku:
            </span>
            <select wire:model.live="selectedYear" class="bg-slate-50 dark:bg-slate-700 border border-slate-300 dark:border-slate-600 text-slate-900 dark:text-slate-100 text-sm font-semibold rounded-lg focus:ring-emerald-500 focus:border-emerald-500 p-2">
                @foreach($availableYears as $yr)
                    <option value="{{ $yr }}">Tahun {{ $yr }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs sm:text-sm font-semibold rounded-lg shadow-sm transition-all">
                <i class='bx bx-printer text-base'></i> Cetak / Export PDF Infografis
            </button>
            <a href="{{ route('admin.rat-report') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-200 hover:bg-slate-300 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-800 dark:text-slate-200 text-xs sm:text-sm font-semibold rounded-lg transition-all">
                <i class='bx bx-table text-base'></i> Detail Tabel Laporan
            </a>
        </div>
    </div>

    {{-- DASHBOARD CONTAINER WRAPPER --}}
    <div class="bg-slate-50 dark:bg-slate-900 p-4 sm:p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xl max-w-[1400px] mx-auto space-y-4 print:border-none print:shadow-none print:p-0">
        
        {{-- HEADER BANNER --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl p-4 border border-slate-200 dark:border-slate-700 flex flex-col md:flex-row items-center justify-between gap-4 shadow-sm relative overflow-hidden">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-emerald-500/10 rounded-xl flex items-center justify-center shrink-0 border border-emerald-500/20">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo KSPPS" class="w-10 h-10 object-contain" onerror="this.src='https://cdn-icons-png.flaticon.com/512/3135/3135715.png'">
                </div>
                <div>
                    <h1 class="text-xl sm:text-2xl font-black tracking-tight text-emerald-900 dark:text-emerald-400 uppercase">
                        DASHBOARD KEUANGAN KSPPS BERKAH MADANI
                    </h1>
                    <p class="text-xs sm:text-sm font-medium text-slate-600 dark:text-slate-300">
                        Ringkasan Kinerja Tahun {{ $dashboard['year'] }} untuk Rapat Anggota Tahunan (RAT)
                    </p>
                    <p class="text-xs italic text-slate-500 font-serif mt-0.5">
                        "Bersama Anggota, Koperasi Kuat, Manfaat Nyata"
                    </p>
                </div>
            </div>
            <div class="bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/60 rounded-xl p-3 flex items-center gap-3 text-right max-w-xs shrink-0">
                <div class="w-10 h-10 bg-emerald-600 rounded-lg flex items-center justify-center text-white shrink-0 text-xl shadow-sm">
                    <i class='bx bxs-group'></i>
                </div>
                <div class="text-left text-[11px] font-bold text-emerald-900 dark:text-emerald-300 leading-tight uppercase">
                    KOPERASI MILIK KITA<br>
                    KEPUTUSAN KITA<br>
                    MANFAAT UNTUK KITA
                </div>
            </div>
        </div>

        {{-- 5 TOP METRIC KPI CARDS --}}
        <div class="grid grid-cols-2 md:grid-cols-5 gap-3 sm:gap-4">
            
            {{-- KPI 1: TOTAL ASET --}}
            <div class="bg-white dark:bg-slate-800 rounded-xl p-3 sm:p-4 border border-slate-200 dark:border-slate-700 shadow-sm flex items-center gap-3 relative overflow-hidden">
                <div class="w-10 sm:w-12 h-10 sm:h-12 bg-blue-600 rounded-full flex items-center justify-center text-white text-xl sm:text-2xl shrink-0 shadow-md">
                    <i class='bx bx-line-chart'></i>
                </div>
                <div class="min-w-0">
                    <span class="text-[10px] sm:text-xs font-bold text-slate-500 uppercase tracking-wider block">TOTAL ASET</span>
                    <div class="text-base sm:text-xl font-black text-slate-900 dark:text-white leading-tight">
                        Rp{{ $dashboard['kpi']['totalAset']['val'] }} <span class="text-xs font-normal">Juta</span>
                    </div>
                    <span class="text-[9px] sm:text-[10px] text-emerald-600 font-semibold flex items-center gap-0.5 mt-0.5">
                        <i class='bx bx-trending-up'></i> {{ $dashboard['kpi']['totalAset']['growth'] }}
                    </span>
                </div>
            </div>

            {{-- KPI 2: TOTAL PEMBIAYAAN --}}
            <div class="bg-white dark:bg-slate-800 rounded-xl p-3 sm:p-4 border border-slate-200 dark:border-slate-700 shadow-sm flex items-center gap-3 relative overflow-hidden">
                <div class="w-10 sm:w-12 h-10 sm:h-12 bg-emerald-600 rounded-full flex items-center justify-center text-white text-xl sm:text-2xl shrink-0 shadow-md">
                    <i class='bx bx-handshake'></i>
                </div>
                <div class="min-w-0">
                    <span class="text-[10px] sm:text-xs font-bold text-slate-500 uppercase tracking-wider block">TOTAL PEMBIAYAAN</span>
                    <div class="text-base sm:text-xl font-black text-slate-900 dark:text-white leading-tight">
                        Rp{{ $dashboard['kpi']['totalPembiayaan']['val'] }} <span class="text-xs font-normal">Juta</span>
                    </div>
                    <span class="text-[9px] sm:text-[10px] text-emerald-600 font-semibold flex items-center gap-0.5 mt-0.5">
                        <i class='bx bx-trending-up'></i> {{ $dashboard['kpi']['totalPembiayaan']['growth'] }}
                    </span>
                </div>
            </div>

            {{-- KPI 3: SHU --}}
            <div class="bg-white dark:bg-slate-800 rounded-xl p-3 sm:p-4 border border-slate-200 dark:border-slate-700 shadow-sm flex items-center gap-3 relative overflow-hidden">
                <div class="w-10 sm:w-12 h-10 sm:h-12 bg-amber-500 rounded-full flex items-center justify-center text-white text-xl sm:text-2xl shrink-0 shadow-md">
                    <i class='bx bx-coin-stack'></i>
                </div>
                <div class="min-w-0">
                    <span class="text-[10px] sm:text-xs font-bold text-slate-500 uppercase tracking-wider block">SISA HASIL USAHA (SHU)</span>
                    <div class="text-base sm:text-xl font-black text-slate-900 dark:text-white leading-tight">
                        Rp{{ $dashboard['kpi']['shu']['val'] }} <span class="text-xs font-normal">Juta</span>
                    </div>
                    <span class="text-[9px] sm:text-[10px] text-emerald-600 font-semibold flex items-center gap-0.5 mt-0.5">
                        <i class='bx bx-trending-up'></i> {{ $dashboard['kpi']['shu']['growth'] }}
                    </span>
                </div>
            </div>

            {{-- KPI 4: JUMLAH ANGGOTA --}}
            <div class="bg-white dark:bg-slate-800 rounded-xl p-3 sm:p-4 border border-slate-200 dark:border-slate-700 shadow-sm flex items-center gap-3 relative overflow-hidden">
                <div class="w-10 sm:w-12 h-10 sm:h-12 bg-purple-600 rounded-full flex items-center justify-center text-white text-xl sm:text-2xl shrink-0 shadow-md">
                    <i class='bx bx-group'></i>
                </div>
                <div class="min-w-0">
                    <span class="text-[10px] sm:text-xs font-bold text-slate-500 uppercase tracking-wider block">JUMLAH ANGGOTA</span>
                    <div class="text-base sm:text-xl font-black text-slate-900 dark:text-white leading-tight">
                        {{ $dashboard['kpi']['jumlahAnggota']['val'] }} <span class="text-xs font-normal">Orang</span>
                    </div>
                    <span class="text-[9px] sm:text-[10px] text-emerald-600 font-semibold flex items-center gap-0.5 mt-0.5">
                        <i class='bx bx-user-plus'></i> {{ $dashboard['kpi']['jumlahAnggota']['growth'] }}
                    </span>
                </div>
            </div>

            {{-- KPI 5: KAS & BANK --}}
            <div class="bg-white dark:bg-slate-800 rounded-xl p-3 sm:p-4 border border-slate-200 dark:border-slate-700 shadow-sm flex items-center gap-3 col-span-2 md:col-span-1 relative overflow-hidden">
                <div class="w-10 sm:w-12 h-10 sm:h-12 bg-teal-600 rounded-full flex items-center justify-center text-white text-xl sm:text-2xl shrink-0 shadow-md">
                    <i class='bx bx-wallet'></i>
                </div>
                <div class="min-w-0">
                    <span class="text-[10px] sm:text-xs font-bold text-slate-500 uppercase tracking-wider block">KAS & BANK</span>
                    <div class="text-base sm:text-xl font-black text-slate-900 dark:text-white leading-tight">
                        Rp{{ $dashboard['kpi']['kasBank']['val'] }} <span class="text-xs font-normal">Juta</span>
                    </div>
                    <span class="text-[9px] sm:text-[10px] text-slate-500 font-medium block mt-0.5">
                        {{ $dashboard['kpi']['kasBank']['note'] }}
                    </span>
                </div>
            </div>

        </div>

        {{-- GRID 8 MODUL VISUALISASI --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            
            {{-- MODUL 1: KOMPOSISI ASET --}}
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col overflow-hidden">
                <div class="bg-blue-900 text-white px-3 py-2 text-xs font-bold uppercase flex items-center justify-between">
                    <span>1. KOMPOSISI ASET</span>
                </div>
                <div class="p-3 flex-1 flex flex-col justify-between space-y-3">
                    <span class="text-[11px] text-slate-500 font-semibold text-center block">Per 31 Desember {{ $dashboard['year'] }}</span>
                    <div class="h-44 relative flex items-center justify-center">
                        <canvas id="chartKomposisiAset"></canvas>
                        <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                            <span class="text-[10px] font-bold text-slate-400">TOTAL ASET</span>
                            <span class="text-sm font-black text-slate-800 dark:text-white">Rp{{ $dashboard['kpi']['totalAset']['val'] }}</span>
                            <span class="text-[10px] font-bold text-slate-500">Juta</span>
                        </div>
                    </div>
                    <div class="bg-blue-700 text-white text-[11px] p-2 rounded-lg text-center font-medium">
                        {{ $dashboard['komposisiAset']['footnote'] }}
                    </div>
                </div>
            </div>

            {{-- MODUL 2: KOMPOSISI PENDAPATAN --}}
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col overflow-hidden">
                <div class="bg-emerald-800 text-white px-3 py-2 text-xs font-bold uppercase flex items-center justify-between">
                    <span>2. KOMPOSISI PENDAPATAN</span>
                </div>
                <div class="p-3 flex-1 flex flex-col justify-between space-y-3">
                    <span class="text-[11px] text-slate-500 font-semibold text-center block">Untuk Tahun yang Berakhir 31 Des {{ $dashboard['year'] }}</span>
                    <div class="h-44 relative flex items-center justify-center">
                        <canvas id="chartKomposisiPendapatan"></canvas>
                        <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                            <span class="text-[10px] font-bold text-slate-400">TOTAL PENDAPATAN</span>
                            <span class="text-sm font-black text-slate-800 dark:text-white">Rp{{ $dashboard['komposisiPendapatan']['total'] }}</span>
                            <span class="text-[10px] font-bold text-slate-500">Juta</span>
                        </div>
                    </div>
                    <div class="bg-emerald-700 text-white text-[11px] p-2 rounded-lg text-center font-medium">
                        {{ $dashboard['komposisiPendapatan']['footnote'] }}
                    </div>
                </div>
            </div>

            {{-- MODUL 3: KOMPOSISI BEBAN OPERASIONAL --}}
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col overflow-hidden">
                <div class="bg-purple-900 text-white px-3 py-2 text-xs font-bold uppercase flex items-center justify-between">
                    <span>3. KOMPOSISI BEBAN OPERASIONAL</span>
                </div>
                <div class="p-3 flex-1 flex flex-col justify-between space-y-3">
                    <span class="text-[11px] text-slate-500 font-semibold text-center block">Untuk Tahun yang Berakhir 31 Des {{ $dashboard['year'] }}</span>
                    <div class="h-44">
                        <canvas id="chartKomposisiBeban"></canvas>
                    </div>
                    <div class="bg-purple-800 text-white text-[11px] p-2 rounded-lg text-center font-medium">
                        {{ $dashboard['komposisiBeban']['footnote'] }}
                    </div>
                </div>
            </div>

            {{-- MODUL 4: TREN SHU 5 TAHUN TERAKHIR --}}
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col overflow-hidden">
                <div class="bg-sky-800 text-white px-3 py-2 text-xs font-bold uppercase flex items-center justify-between">
                    <span>4. TREN SHU 5 TAHUN TERAKHIR</span>
                </div>
                <div class="p-3 flex-1 flex flex-col justify-between space-y-3">
                    <span class="text-[11px] text-slate-500 font-semibold text-center block">(Dalam Juta Rupiah)</span>
                    <div class="h-44">
                        <canvas id="chartTrenShu"></canvas>
                    </div>
                    <div class="bg-sky-800 text-white text-[11px] p-2 rounded-lg text-center font-medium">
                        {{ $dashboard['trenShu']['footnote'] }}
                    </div>
                </div>
            </div>

            {{-- MODUL 5: RASIO PEMBIAYAAN BERMASALAH (NPF) GAUGE --}}
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col overflow-hidden">
                <div class="bg-amber-800 text-white px-3 py-2 text-xs font-bold uppercase flex items-center justify-between">
                    <span>5. RASIO PEMBIAYAAN BERMASALAH (NPF)</span>
                </div>
                <div class="p-3 flex-1 flex flex-col justify-between space-y-2">
                    <span class="text-[11px] text-slate-500 font-semibold text-center block">Per 31 Desember {{ $dashboard['year'] }}</span>
                    
                    {{-- GAUGE SPEEDOMETER SVG --}}
                    <div class="flex flex-col items-center justify-center my-1 relative">
                        <svg viewBox="0 0 200 110" class="w-44 h-24">
                            <!-- Gauge background slices -->
                            <!-- Lancar (0-25%) Green -->
                            <path d="M 20 100 A 80 80 0 0 1 65 35" fill="none" stroke="#22C55E" stroke-width="22" />
                            <!-- Kurang Lancar (25-50%) Yellow -->
                            <path d="M 65 35 A 80 80 0 0 1 100 20" fill="none" stroke="#EAB308" stroke-width="22" />
                            <!-- Diragukan (50-75%) Orange -->
                            <path d="M 100 20 A 80 80 0 0 1 135 35" fill="none" stroke="#F97316" stroke-width="22" />
                            <!-- Macet (75-100%) Red -->
                            <path d="M 135 35 A 80 80 0 0 1 180 100" fill="none" stroke="#EF4444" stroke-width="22" />
                            
                            <!-- Needle (rotated to ~2.3%) -->
                            <g transform="rotate(-65 100 100)">
                                <line x1="100" y1="100" x2="40" y2="100" stroke="#1E293B" stroke-width="5" stroke-linecap="round" />
                                <circle cx="100" cy="100" r="8" fill="#1E293B" />
                            </g>
                        </svg>

                        {{-- Labels --}}
                        <div class="w-full flex justify-between px-4 text-[9px] font-bold text-slate-600 dark:text-slate-400">
                            <span>Lancar<br>(0-2%)</span>
                            <span>Kurang Lancar<br>(2-5%)</span>
                            <span>Diragukan<br>(5-8%)</span>
                            <span>Macet<br>(&gt;8%)</span>
                        </div>

                        <div class="text-center mt-1">
                            <span class="text-xl font-black text-slate-900 dark:text-white">{{ $dashboard['npf']['val'] }}%</span>
                            <span class="text-[10px] text-slate-500 font-semibold block">({{ $dashboard['npf']['status'] }})</span>
                        </div>
                    </div>

                    <div class="bg-amber-600 text-white text-[11px] p-2 rounded-lg text-center font-medium">
                        {{ $dashboard['npf']['footnote'] }}
                    </div>
                </div>
            </div>

            {{-- MODUL 6: PERTUMBUHAN SIMPANAN ANGGOTA --}}
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col overflow-hidden">
                <div class="bg-emerald-900 text-white px-3 py-2 text-xs font-bold uppercase flex items-center justify-between">
                    <span>6. PERTUMBUHAN SIMPANAN ANGGOTA</span>
                </div>
                <div class="p-3 flex-1 flex flex-col justify-between space-y-3">
                    <span class="text-[11px] text-slate-500 font-semibold text-center block">(Dalam Juta Rupiah)</span>
                    <div class="h-44">
                        <canvas id="chartPertumbuhanSimpanan"></canvas>
                    </div>
                    <div class="bg-emerald-800 text-white text-[11px] p-2 rounded-lg text-center font-medium">
                        {{ $dashboard['pertumbuhanSimpanan']['footnote'] }}
                    </div>
                </div>
            </div>

            {{-- MODUL 7: ARUS KAS TAHUN 2025 --}}
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col overflow-hidden">
                <div class="bg-blue-900 text-white px-3 py-2 text-xs font-bold uppercase flex items-center justify-between">
                    <span>7. ARUS KAS TAHUN {{ $dashboard['year'] }}</span>
                </div>
                <div class="p-3 flex-1 flex flex-col justify-between space-y-3">
                    <span class="text-[11px] text-slate-500 font-semibold text-center block">(Dalam Juta Rupiah)</span>
                    <div class="h-44">
                        <canvas id="chartArusKas"></canvas>
                    </div>
                    <div class="bg-blue-800 text-white text-[11px] p-2 rounded-lg text-center font-medium">
                        {{ $dashboard['arusKas']['footnote'] }}
                    </div>
                </div>
            </div>

            {{-- MODUL 8: RINGKASAN KESEHATAN KOPERASI --}}
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col overflow-hidden">
                <div class="bg-rose-800 text-white px-3 py-2 text-xs font-bold uppercase flex items-center justify-between">
                    <span>8. RINGKASAN KESEHATAN KOPERASI</span>
                </div>
                <div class="p-3 flex-1 flex flex-col justify-between space-y-2">
                    <div class="space-y-1.5 my-auto">
                        @foreach($dashboard['kesehatan'] as $item)
                            <div class="flex items-center justify-between p-1.5 rounded-lg bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 text-[11px]">
                                <span class="font-medium text-slate-700 dark:text-slate-200 flex items-center gap-1.5">
                                    <i class='bx bx-check-shield text-emerald-600 text-sm'></i> {{ $item['label'] }}
                                </span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-black border {{ $item['badge'] }}">
                                    {{ $item['status'] }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                    <div class="bg-rose-700 text-white text-[11px] p-2 rounded-lg text-center font-medium">
                        Koperasi dalam kondisi sehat dan berkelanjutan.
                    </div>
                </div>
            </div>

        </div>

        {{-- FOOTER ADVICE & NOTES BANNER --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            
            {{-- ADVICE 1 --}}
            <div class="bg-white dark:bg-slate-800 rounded-xl p-3 sm:p-4 border border-slate-200 dark:border-slate-700 shadow-sm flex items-start gap-3">
                <div class="w-8 h-8 bg-emerald-600 rounded-full flex items-center justify-center text-white shrink-0 mt-0.5">
                    <i class='bx bx-map-pin text-lg'></i>
                </div>
                <div class="text-xs space-y-1">
                    <span class="font-bold text-slate-900 dark:text-white uppercase tracking-wider block">INFORMASI UNTUK PENGAMBILAN KEPUTUSAN</span>
                    <ul class="text-slate-600 dark:text-slate-300 space-y-1 text-[11px]">
                        <li class="flex items-start gap-1"><i class='bx bx-check text-emerald-600 mt-0.5'></i> Pertahankan kualitas pembiayaan dan tingkatkan penagihan.</li>
                        <li class="flex items-start gap-1"><i class='bx bx-check text-emerald-600 mt-0.5'></i> Tingkatkan simpanan sukarela & berjangka melalui program menarik.</li>
                        <li class="flex items-start gap-1"><i class='bx bx-check text-emerald-600 mt-0.5'></i> Efisiensi beban operasional untuk meningkatkan SHU.</li>
                    </ul>
                </div>
            </div>

            {{-- ADVICE 2 --}}
            <div class="bg-white dark:bg-slate-800 rounded-xl p-3 sm:p-4 border border-slate-200 dark:border-slate-700 shadow-sm flex items-center gap-3 justify-center text-center">
                <div class="w-10 h-10 bg-emerald-500/10 rounded-full flex items-center justify-center text-emerald-600 text-2xl shrink-0">
                    <i class='bx bxs-group'></i>
                </div>
                <div class="text-left">
                    <span class="text-xs font-extrabold text-emerald-900 dark:text-emerald-300 block uppercase">KEPUTUSAN KITA</span>
                    <span class="text-xs font-bold text-slate-700 dark:text-slate-200 block uppercase">MENENTUKAN MASA DEPAN KOPERASI KITA</span>
                </div>
            </div>

            {{-- ADVICE 3 --}}
            <div class="bg-white dark:bg-slate-800 rounded-xl p-3 sm:p-4 border border-slate-200 dark:border-slate-700 shadow-sm flex items-start gap-3">
                <div class="w-8 h-8 bg-amber-500 rounded-full flex items-center justify-center text-white shrink-0 mt-0.5">
                    <i class='bx bx-bulb text-lg'></i>
                </div>
                <div class="text-xs">
                    <span class="font-bold text-slate-900 dark:text-white uppercase tracking-wider block">CATATAN:</span>
                    <p class="text-slate-500 dark:text-slate-400 text-[11px] leading-tight mt-1">
                        Data dalam dashboard ini adalah ringkasan dari laporan keuangan KSPPS Berkah Madani per 31 Desember {{ $dashboard['year'] }} dan dapat berubah sesuai hasil audit dan keputusan RAT.
                    </p>
                </div>
            </div>

        </div>

    </div>

</div>

{{-- CHART.JS INTEGRATION SCRIPT --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('livewire:navigated', () => { initCharts(); });
    document.addEventListener('DOMContentLoaded', () => { initCharts(); });

    function initCharts() {
        if (typeof Chart === 'undefined') return;

        // 1. Chart Komposisi Aset (Donut)
        const ctxAset = document.getElementById('chartKomposisiAset')?.getContext('2d');
        if (ctxAset) {
            new Chart(ctxAset, {
                type: 'doughnut',
                data: {
                    labels: @json($dashboard['komposisiAset']['labels']),
                    datasets: [{
                        data: @json($dashboard['komposisiAset']['data']),
                        backgroundColor: @json($dashboard['komposisiAset']['colors']),
                        borderWidth: 2,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: {
                        legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 9 } } }
                    }
                }
            });
        }

        // 2. Chart Komposisi Pendapatan (Donut)
        const ctxPendapatan = document.getElementById('chartKomposisiPendapatan')?.getContext('2d');
        if (ctxPendapatan) {
            new Chart(ctxPendapatan, {
                type: 'doughnut',
                data: {
                    labels: @json($dashboard['komposisiPendapatan']['labels']),
                    datasets: [{
                        data: @json($dashboard['komposisiPendapatan']['data']),
                        backgroundColor: @json($dashboard['komposisiPendapatan']['colors']),
                        borderWidth: 2,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: {
                        legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 9 } } }
                    }
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
                        backgroundColor: '#7C3AED',
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
                        y: { ticks: { font: { size: 9 } } }
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
                        x: { ticks: { font: { size: 9 } } },
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
                        { label: '2025', data: [35, 28, 120, 67], backgroundColor: '#16A34A' },
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'top', labels: { boxWidth: 10, font: { size: 9 } } } },
                    scales: {
                        x: { ticks: { font: { size: 9 } } },
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
                    labels: ['Kas Aktivitas Operasi', 'Kas Aktivitas Investasi', 'Kas Aktivitas Pendanaan'],
                    datasets: [{
                        data: [
                            @json($dashboard['arusKas']['operasi']),
                            @json($dashboard['arusKas']['investasi']),
                            @json($dashboard['arusKas']['pendanaan'])
                        ],
                        backgroundColor: ['#1D4ED8', '#EF4444', '#16A34A'],
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { ticks: { font: { size: 8 } } },
                        y: { ticks: { font: { size: 9 } } }
                    }
                }
            });
        }
    }
</script>
