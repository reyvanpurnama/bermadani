<div class="space-y-6">
    <style>
        @media print {
            body * { visibility: hidden; }
            #printableInfographic, #printableInfographic * { visibility: visible; }
            #printableInfographic { position: absolute; left: 0; top: 0; width: 100%; }
            .no-print { display: none !important; }
        }
    </style>

    {{-- Top Header & Navigation Selector --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white dark:bg-darkCard p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 no-print">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="p-2 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">
                    <i class='bx bx-slideshow text-2xl'></i>
                </span>
                <h1 class="text-xl font-bold text-slate-800 dark:text-white">Infografis & Visualisasi RAT 2025</h1>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400">
                Khusus Internal Bermadani • Simpanan: Rp 195,19M | Aset Real Bermadani: Rp 42,75M (Kas: Rp 30,5M + Pinjaman Bermadani DB: Rp 1,23M + Aset: Rp 11M).
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            {{-- Dropdown Switcher --}}
            <div class="relative">
                <select wire:change="setPage($event.target.value)"
                    class="appearance-none bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white text-xs font-bold rounded-xl px-4 py-2.5 pr-10 outline-none cursor-pointer">
                    <option value="1" {{ $page == 1 ? 'selected' : '' }}>Lembar 1: Dashboard Kinerja Keuangan</option>
                    <option value="2" {{ $page == 2 ? 'selected' : '' }}>Lembar 2: Posisi Keuangan (Simpanan & Pinjaman Bermadani)</option>
                    <option value="3" {{ $page == 3 ? 'selected' : '' }}>Lembar 3: Laporan SHU & Bagi Hasil Anggota</option>
                    <option value="4" {{ $page == 4 ? 'selected' : '' }}>Lembar 4: Siklus Akuntansi, Arus Kas & CALK</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-500">
                    <i class='bx bx-chevron-down text-base'></i>
                </div>
            </div>

            <button onclick="window.print()"
                class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-lg shadow-emerald-600/20 transition-all flex items-center gap-2">
                <i class='bx bx-printer text-base'></i> Cetak / Export A4
            </button>
        </div>
    </div>

    {{-- Tabs Bar (Sub Navigation Buttons) --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-2 no-print">
        <button wire:click="setPage(1)"
            class="p-3 rounded-xl border text-left transition-all flex items-center gap-3 {{ $page == 1 ? 'bg-indigo-600 text-white border-indigo-600 shadow-md' : 'bg-white dark:bg-darkCard text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-700 hover:border-indigo-400' }}">
            <span class="w-8 h-8 rounded-lg flex items-center justify-center font-bold text-xs shrink-0 {{ $page == 1 ? 'bg-white/20' : 'bg-slate-100 dark:bg-slate-800' }}">1</span>
            <div class="truncate">
                <p class="text-xs font-bold truncate">Dashboard Kinerja</p>
                <p class="text-[10px] opacity-75 truncate">Visual Utama & KPI</p>
            </div>
        </button>

        <button wire:click="setPage(2)"
            class="p-3 rounded-xl border text-left transition-all flex items-center gap-3 {{ $page == 2 ? 'bg-indigo-600 text-white border-indigo-600 shadow-md' : 'bg-white dark:bg-darkCard text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-700 hover:border-indigo-400' }}">
            <span class="w-8 h-8 rounded-lg flex items-center justify-center font-bold text-xs shrink-0 {{ $page == 2 ? 'bg-white/20' : 'bg-slate-100 dark:bg-slate-800' }}">2</span>
            <div class="truncate">
                <p class="text-xs font-bold truncate">Posisi Keuangan</p>
                <p class="text-[10px] opacity-75 truncate">Simpanan & Pinjaman Bermadani</p>
            </div>
        </button>

        <button wire:click="setPage(3)"
            class="p-3 rounded-xl border text-left transition-all flex items-center gap-3 {{ $page == 3 ? 'bg-indigo-600 text-white border-indigo-600 shadow-md' : 'bg-white dark:bg-darkCard text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-700 hover:border-indigo-400' }}">
            <span class="w-8 h-8 rounded-lg flex items-center justify-center font-bold text-xs shrink-0 {{ $page == 3 ? 'bg-white/20' : 'bg-slate-100 dark:bg-slate-800' }}">3</span>
            <div class="truncate">
                <p class="text-xs font-bold truncate">SHU & Bagi Hasil</p>
                <p class="text-[10px] opacity-75 truncate">Alokasi & Rumus SHU</p>
            </div>
        </button>

        <button wire:click="setPage(4)"
            class="p-3 rounded-xl border text-left transition-all flex items-center gap-3 {{ $page == 4 ? 'bg-indigo-600 text-white border-indigo-600 shadow-md' : 'bg-white dark:bg-darkCard text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-700 hover:border-indigo-400' }}">
            <span class="w-8 h-8 rounded-lg flex items-center justify-center font-bold text-xs shrink-0 {{ $page == 4 ? 'bg-white/20' : 'bg-slate-100 dark:bg-slate-800' }}">4</span>
            <div class="truncate">
                <p class="text-xs font-bold truncate">Siklus & CALK</p>
                <p class="text-[10px] opacity-75 truncate">Arus Kas & Catatan</p>
            </div>
        </button>
    </div>

    {{-- PRINTABLE INFOGRAPHIC CONTAINER --}}
    <div id="printableInfographic" class="bg-white dark:bg-darkCard p-6 md:p-8 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 space-y-6">

        {{-- LEMBAR 1: DASHBOARD KINERJA KEUANGAN --}}
        @if($page == 1)
            {{-- Header Infografis --}}
            <div class="border-b border-slate-200 dark:border-slate-700 pb-5 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-2xl font-bold">
                        <i class='bx bxs-institution'></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-extrabold text-slate-800 dark:text-white uppercase tracking-tight">KOPERASI BERMADANI</h2>
                        <p class="text-xs font-bold text-emerald-600 dark:text-emerald-400">DASHBOARD KINERJA KEUANGAN TAHUN 2025</p>
                        <p class="text-[10px] text-slate-500">Laporan Internal Bermadani • 31 Juli 2026</p>
                    </div>
                </div>
                <div class="text-left md:text-right bg-emerald-50 dark:bg-emerald-900/20 px-4 py-2 rounded-xl border border-emerald-200 dark:border-emerald-800">
                    <p class="text-[10px] font-bold text-emerald-700 dark:text-emerald-400 uppercase tracking-widest">Slogan RAT 2025</p>
                    <p class="text-xs font-semibold text-emerald-800 dark:text-emerald-300">"Bersama Anggota, Koperasi Kuat, Manfaat Nyata"</p>
                </div>
            </div>

            {{-- 4 Cards KPI Utama --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-slate-50 dark:bg-slate-800/60 p-4 rounded-2xl border border-slate-200/60 dark:border-slate-700">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-10 h-10 rounded-xl bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 flex items-center justify-center text-xl">
                            <i class='bx bx-wallet-alt'></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase">Simpanan Pasiva</p>
                            <h4 class="text-base font-bold text-slate-800 dark:text-white">Rp {{ number_format($totalSimpanan, 0, ',', '.') }}</h4>
                        </div>
                    </div>
                    <p class="text-[10px] text-slate-500">Modal {{ $activeCount }} Anggota Aktif</p>
                </div>

                <div class="bg-slate-50 dark:bg-slate-800/60 p-4 rounded-2xl border border-emerald-200/60 dark:border-emerald-900/40">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 flex items-center justify-center text-xl">
                            <i class='bx bx-money'></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-emerald-600 uppercase">Saldo Kas Akhir (CSV)</p>
                            <h4 class="text-base font-bold text-emerald-600 dark:text-emerald-400">Rp {{ number_format($kasBankRiil, 0, ',', '.') }}</h4>
                        </div>
                    </div>
                    <p class="text-[10px] text-slate-500">Saldo Kas Riil (CSV Line 28)</p>
                </div>

                <div class="bg-slate-50 dark:bg-slate-800/60 p-4 rounded-2xl border border-blue-200/60 dark:border-blue-900/40">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-900/30 text-blue-600 flex items-center justify-center text-xl">
                            <i class='bx bx-credit-card-front'></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-blue-600 uppercase">Pinjaman Bermadani DB</p>
                            <h4 class="text-base font-bold text-blue-600 dark:text-blue-400">Rp {{ number_format($outstandingPinjamanBermadani, 0, ',', '.') }}</h4>
                        </div>
                    </div>
                    <p class="text-[10px] text-slate-500">Pinjaman Internal Bermadani</p>
                </div>

                <div class="bg-slate-50 dark:bg-slate-800/60 p-4 rounded-2xl border border-rose-200/60 dark:border-rose-900/40">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-10 h-10 rounded-xl bg-rose-100 dark:bg-rose-900/30 text-rose-600 flex items-center justify-center text-xl">
                            <i class='bx bx-trending-down'></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-rose-600 uppercase">Defisit Modal Real</p>
                            <h4 class="text-base font-bold text-rose-600 dark:text-rose-400">-Rp {{ number_format($defisitModal, 0, ',', '.') }}</h4>
                        </div>
                    </div>
                    <p class="text-[10px] text-rose-500 font-semibold">Defisit Modal Bermadani</p>
                </div>
            </div>

            {{-- Info Card Transparansi --}}
            <div class="p-4 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-300 dark:border-emerald-800 rounded-2xl flex flex-col md:flex-row items-start md:items-center justify-between gap-3 text-xs">
                <div class="flex items-start gap-3">
                    <i class='bx bx-check-shield text-2xl text-emerald-600 shrink-0 mt-0.5'></i>
                    <div>
                        <p class="font-bold text-emerald-800 dark:text-emerald-300 uppercase text-[11px]">Struktur Neraca Khusus Koperasi Bermadani:</p>
                        <p class="text-slate-700 dark:text-slate-300 mt-0.5">
                            Total Simpanan {{ $activeCount }} anggota aktif sebesar <strong>Rp {{ number_format($totalSimpanan, 0, ',', '.') }}</strong> berada di Pasiva. Aset Real internal Bermadani terdiri dari Kas Bank <strong>Rp {{ number_format($kasBankRiil, 0, ',', '.') }}</strong>, Aset Tetap <strong>Rp {{ number_format($asetTetap, 0, ',', '.') }}</strong>, dan Pinjaman Internal Bermadani DB <strong>Rp {{ number_format($outstandingPinjamanBermadani, 0, ',', '.') }}</strong> (Total Aset Real: Rp {{ number_format($totalAsetBermadani, 0, ',', '.') }}). Defisit modal internal tercatat <strong>-Rp {{ number_format($defisitModal, 0, ',', '.') }}</strong>.
                        </p>
                    </div>
                </div>
            </div>

            {{-- 4 Quadrant Grid Charts --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
                {{-- Quadrant 1: Komposisi Simpanan --}}
                <div class="bg-slate-50 dark:bg-slate-800/40 p-5 rounded-2xl border border-slate-200/60 dark:border-slate-700">
                    <h3 class="text-xs font-bold text-slate-800 dark:text-white uppercase mb-3 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span> 1. Komposisi Simpanan Anggota (Pasiva)
                    </h3>
                    <div class="space-y-3">
                        <div>
                            <div class="flex justify-between text-xs mb-1">
                                <span class="font-medium text-slate-600 dark:text-slate-300">Simpanan Wajib (Basis SHU)</span>
                                <span class="font-bold text-emerald-600">Rp {{ number_format($simwa, 0, ',', '.') }} ({{ round(($simwa/$totalSimpanan)*100, 1) }}%)</span>
                            </div>
                            <div class="w-full bg-slate-200 dark:bg-slate-700 h-2.5 rounded-full overflow-hidden">
                                <div class="bg-emerald-500 h-2.5 rounded-full" style="width: {{ round(($simwa/$totalSimpanan)*100, 1) }}%"></div>
                            </div>
                        </div>

                        <div>
                            <div class="flex justify-between text-xs mb-1">
                                <span class="font-medium text-slate-600 dark:text-slate-300">Simpanan Pokok</span>
                                <span class="font-bold text-indigo-600">Rp {{ number_format($simpok, 0, ',', '.') }} ({{ round(($simpok/$totalSimpanan)*100, 1) }}%)</span>
                            </div>
                            <div class="w-full bg-slate-200 dark:bg-slate-700 h-2.5 rounded-full overflow-hidden">
                                <div class="bg-indigo-500 h-2.5 rounded-full" style="width: {{ round(($simpok/$totalSimpanan)*100, 1) }}%"></div>
                            </div>
                        </div>

                        <div>
                            <div class="flex justify-between text-xs mb-1">
                                <span class="font-medium text-slate-600 dark:text-slate-300">Simpanan Sukarela</span>
                                <span class="font-bold text-amber-600">Rp {{ number_format($simsuka, 0, ',', '.') }} ({{ round(($simsuka/$totalSimpanan)*100, 1) }}%)</span>
                            </div>
                            <div class="w-full bg-slate-200 dark:bg-slate-700 h-2.5 rounded-full overflow-hidden">
                                <div class="bg-amber-500 h-2.5 rounded-full" style="width: {{ round(($simsuka/$totalSimpanan)*100, 1) }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Quadrant 2: Aset Real Bermadani --}}
                <div class="bg-slate-50 dark:bg-slate-800/40 p-5 rounded-2xl border border-slate-200/60 dark:border-slate-700">
                    <h3 class="text-xs font-bold text-slate-800 dark:text-white uppercase mb-3 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-blue-500"></span> 2. Rincian Aset Koperasi Bermadani (Aktiva)
                    </h3>
                    <div class="space-y-3">
                        <div class="p-3 bg-white dark:bg-slate-800 rounded-xl border border-slate-200/60 dark:border-slate-700 flex justify-between items-center">
                            <span class="text-xs text-slate-600 dark:text-slate-300">Kas & Bank Akhir (CSV Line 28)</span>
                            <span class="text-xs font-bold text-emerald-600">Rp {{ number_format($kasBankRiil, 0, ',', '.') }}</span>
                        </div>
                        <div class="p-3 bg-white dark:bg-slate-800 rounded-xl border border-slate-200/60 dark:border-slate-700 flex justify-between items-center">
                            <span class="text-xs text-slate-600 dark:text-slate-300">Aset Tetap & Inventaris (CSV Line 13)</span>
                            <span class="text-xs font-bold text-amber-600">Rp {{ number_format($asetTetap, 0, ',', '.') }}</span>
                        </div>
                        <div class="p-3 bg-white dark:bg-slate-800 rounded-xl border border-slate-200/60 dark:border-slate-700 flex justify-between items-center">
                            <span class="text-xs text-slate-600 dark:text-slate-300">Pinjaman Internal Bermadani DB</span>
                            <span class="text-xs font-bold text-indigo-600">Rp {{ number_format($outstandingPinjamanBermadani, 0, ',', '.') }}</span>
                        </div>
                        <div class="p-3 bg-emerald-50 dark:bg-emerald-950/40 rounded-xl border border-emerald-300 dark:border-emerald-800 flex justify-between items-center">
                            <span class="text-xs font-bold text-emerald-800 dark:text-emerald-300">Total Aset Real Bermadani</span>
                            <span class="text-xs font-extrabold text-emerald-600 dark:text-emerald-400">Rp {{ number_format($totalAsetBermadani, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                {{-- Quadrant 3: Alokasi SHU --}}
                <div class="bg-slate-50 dark:bg-slate-800/40 p-5 rounded-2xl border border-slate-200/60 dark:border-slate-700">
                    <h3 class="text-xs font-bold text-slate-800 dark:text-white uppercase mb-3 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span> 3. Pembagian SHU RAT
                    </h3>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="p-4 bg-emerald-50 dark:bg-emerald-950/40 rounded-xl border border-emerald-200 text-center">
                            <p class="text-[10px] font-bold text-emerald-700 uppercase">SHU Dibagikan</p>
                            <h4 class="text-sm font-extrabold text-emerald-600 mt-1">Rp {{ number_format($shuMember, 0, ',', '.') }}</h4>
                            <p class="text-[9px] text-emerald-700/80 mt-1 font-semibold">Hak {{ $activeCount }} Anggota</p>
                        </div>
                        <div class="p-4 bg-blue-50 dark:bg-blue-950/40 rounded-xl border border-blue-200 text-center">
                            <p class="text-[10px] font-bold text-blue-700 uppercase">Dana Cadangan</p>
                            <h4 class="text-sm font-extrabold text-blue-600 mt-1">Rp {{ number_format($retainedModal, 0, ',', '.') }}</h4>
                            <p class="text-[9px] text-blue-700/80 mt-1 font-semibold">Cadangan Operasional</p>
                        </div>
                    </div>
                </div>

                {{-- Quadrant 4: Kesehatan Koperasi --}}
                <div class="bg-slate-50 dark:bg-slate-800/40 p-5 rounded-2xl border border-slate-200/60 dark:border-slate-700">
                    <h3 class="text-xs font-bold text-slate-800 dark:text-white uppercase mb-3 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-purple-500"></span> 4. Status Kesehatan Koperasi
                    </h3>
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div class="p-2.5 bg-white dark:bg-slate-800 rounded-xl flex justify-between items-center border border-slate-100 dark:border-slate-700">
                            <span class="text-slate-600 dark:text-slate-400 font-medium">Kecukupan Modal</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-700">TERTUTUP</span>
                        </div>
                        <div class="p-2.5 bg-white dark:bg-slate-800 rounded-xl flex justify-between items-center border border-slate-100 dark:border-slate-700">
                            <span class="text-slate-600 dark:text-slate-400 font-medium">Likuiditas Kas</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-700">TERBATAS</span>
                        </div>
                        <div class="p-2.5 bg-white dark:bg-slate-800 rounded-xl flex justify-between items-center border border-slate-100 dark:border-slate-700">
                            <span class="text-slate-600 dark:text-slate-400 font-medium">Efisiensi Beban</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-700">EFISIEN</span>
                        </div>
                        <div class="p-2.5 bg-white dark:bg-slate-800 rounded-xl flex justify-between items-center border border-slate-100 dark:border-slate-700">
                            <span class="text-slate-600 dark:text-slate-400 font-medium">Pengelolaan</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-700">BAIK</span>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- LEMBAR 2: POSISI KEUANGAN (NERACA KHUSUS BERMADANI) --}}
        @if($page == 2)
            <div class="border-b border-slate-200 dark:border-slate-700 pb-4 mb-4">
                <h2 class="text-lg font-bold text-slate-800 dark:text-white">LAPORAN POSISI KEUANGAN (NERACA KHUSUS BERMADANI)</h2>
                <p class="text-xs text-slate-500">Per 31 Desember 2025 • Aktiva Real Bermadani vs Pasiva Simpanan Anggota</p>
            </div>

            {{-- Neraca Side-by-Side Table --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- ASET (AKTIVA) --}}
                <div class="border border-slate-200 dark:border-slate-700 rounded-2xl overflow-hidden">
                    <div class="bg-indigo-600 text-white px-4 py-2.5 font-bold text-xs flex justify-between items-center">
                        <span>ASET KOPERASI BERMADANI (AKTIVA)</span>
                        <span>NOMINAL (RP)</span>
                    </div>
                    <div class="p-4 space-y-3 text-xs">
                        <div class="font-bold text-slate-700 dark:text-slate-300 uppercase text-[10px] tracking-wider">Aset Fisik & Pinjaman Bermadani</div>
                        <div class="flex justify-between pl-3 border-b border-slate-100 dark:border-slate-700/50 pb-2">
                            <span class="text-slate-600 dark:text-slate-400">1. Kas & Bank (Saldo Akhir CSV Line 28)</span>
                            <span class="font-mono font-bold text-emerald-600">Rp {{ number_format($kasBankRiil, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between pl-3 border-b border-slate-100 dark:border-slate-700/50 pb-2">
                            <span class="text-slate-600 dark:text-slate-400">2. Aset Tetap & Inventaris Toko (CSV Line 13)</span>
                            <span class="font-mono font-bold text-amber-600">Rp {{ number_format($asetTetap, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between pl-3 border-b border-slate-100 dark:border-slate-700/50 pb-2">
                            <span class="text-slate-600 dark:text-slate-400">3. Piutang Pinjaman Internal Bermadani DB</span>
                            <span class="font-mono font-bold text-indigo-600">Rp {{ number_format($outstandingPinjamanBermadani, 0, ',', '.') }}</span>
                        </div>

                        <div class="flex justify-between bg-indigo-50 dark:bg-indigo-950/30 p-2.5 rounded-xl font-bold text-indigo-700 dark:text-indigo-300 mt-4">
                            <span>TOTAL ASET REAL BERMADANI (AKTIVA)</span>
                            <span class="font-mono">Rp {{ number_format($totalAsetBermadani, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                {{-- LIABILITAS & EKUITAS SIMPANAN (PASIVA) --}}
                <div class="border border-slate-200 dark:border-slate-700 rounded-2xl overflow-hidden">
                    <div class="bg-emerald-600 text-white px-4 py-2.5 font-bold text-xs flex justify-between items-center">
                        <span>SIMPANAN ANGGOTA & LIABILITAS (PASIVA)</span>
                        <span>NOMINAL (RP)</span>
                    </div>
                    <div class="p-4 space-y-3 text-xs">
                        <div class="font-bold text-slate-700 dark:text-slate-300 uppercase text-[10px] tracking-wider">Liabilitas Utang Pihak Ketiga</div>
                        <div class="flex justify-between pl-3 border-b border-slate-100 dark:border-slate-700/50 pb-2">
                            <span class="text-slate-600 dark:text-slate-400">Utang Usaha / Pihak Ketiga</span>
                            <span class="font-mono font-semibold text-slate-400">Rp 0</span>
                        </div>

                        <div class="font-bold text-slate-700 dark:text-slate-300 uppercase text-[10px] tracking-wider pt-2">Simpanan Modal {{ $activeCount }} Anggota Aktif</div>
                        <div class="flex justify-between pl-3 border-b border-slate-100 dark:border-slate-700/50 pb-2">
                            <span class="text-slate-600 dark:text-slate-400">1. Simpanan Pokok Anggota Aktif</span>
                            <span class="font-mono font-semibold">Rp {{ number_format($simpok, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between pl-3 border-b border-slate-100 dark:border-slate-700/50 pb-2">
                            <span class="text-slate-600 dark:text-slate-400">2. Simpanan Wajib Anggota Aktif</span>
                            <span class="font-mono font-semibold text-emerald-600 font-bold">Rp {{ number_format($simwa, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between pl-3 border-b border-slate-100 dark:border-slate-700/50 pb-2">
                            <span class="text-slate-600 dark:text-slate-400">3. Simpanan Sukarela Anggota Aktif</span>
                            <span class="font-mono font-semibold">Rp {{ number_format($simsuka, 0, ',', '.') }}</span>
                        </div>

                        <div class="flex justify-between bg-emerald-50 dark:bg-emerald-950/30 p-2.5 rounded-xl font-bold text-emerald-700 dark:text-emerald-300 mt-4">
                            <span>TOTAL SIMPANAN ANGGOTA (PASIVA)</span>
                            <span class="font-mono">Rp {{ number_format($totalSimpanan, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>

        @endif

        {{-- LEMBAR 3: LAPORAN SHU & DISTRIBUSI BAGI HASIL --}}
        @if($page == 3)
            <div class="border-b border-slate-200 dark:border-slate-700 pb-4 mb-4">
                <h2 class="text-lg font-bold text-slate-800 dark:text-white">LAPORAN SHU & TRANSPARANSI DISTRIBUSI BAGI HASIL ANGGOTA</h2>
                <p class="text-xs text-slate-500">Formula Pembagian Adil Berbasis Simpanan Wajib {{ $activeCount }} Anggota Aktif</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="p-4 bg-emerald-50 dark:bg-emerald-950/40 rounded-2xl border border-emerald-200 dark:border-emerald-800 text-center">
                    <p class="text-[10px] font-bold text-emerald-700 uppercase">Saldo Kas Akhir (CSV)</p>
                    <h3 class="text-xl font-extrabold text-emerald-600 dark:text-emerald-400 mt-1">Rp {{ number_format($kasBankRiil, 0, ',', '.') }}</h3>
                    <p class="text-[10px] text-slate-500 mt-1">Saldo Kas Per 31 Des 2025</p>
                </div>

                <div class="p-4 bg-amber-50 dark:bg-amber-950/40 rounded-2xl border border-amber-200 dark:border-amber-800 text-center">
                    <p class="text-[10px] font-bold text-amber-700 uppercase">SHU Dibagikan</p>
                    <h3 class="text-xl font-extrabold text-amber-600 dark:text-amber-400 mt-1">Rp {{ number_format($shuMember, 0, ',', '.') }}</h3>
                    <p class="text-[10px] text-slate-500 mt-1">Alokasi untuk {{ $activeCount }} Anggota</p>
                </div>

                <div class="p-4 bg-blue-50 dark:bg-blue-950/40 rounded-2xl border border-blue-200 dark:border-blue-800 text-center">
                    <p class="text-[10px] font-bold text-blue-700 uppercase">Dana Cadangan</p>
                    <h3 class="text-xl font-extrabold text-blue-600 dark:text-blue-400 mt-1">Rp {{ number_format($retainedModal, 0, ',', '.') }}</h3>
                    <p class="text-[10px] text-slate-500 mt-1">Alokasi Cadangan Koperasi</p>
                </div>
            </div>

            {{-- Diagram Rumus --}}
            <div class="p-5 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-200 dark:border-slate-700 space-y-3">
                <h3 class="text-xs font-bold text-slate-800 dark:text-white uppercase flex items-center gap-2">
                    <i class='bx bx-calculator text-base text-indigo-600'></i> Formula Perhitungan SHU Per Anggota
                </h3>
                <div class="p-4 bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-mono text-slate-700 dark:text-slate-300">
                    <p class="font-bold text-indigo-600 mb-1">SHU Anggota = (Simpanan Wajib Anggota ÷ Rp {{ number_format($simwa, 0, ',', '.') }}) × Rp {{ number_format($shuMember, 0, ',', '.') }}</p>
                    <p class="text-[11px] text-slate-500 font-sans mt-2">Semakin besar partisipasi simpanan wajib {{ $activeCount }} anggota aktif, semakin besar porsi SHU yang diterima.</p>
                </div>
            </div>
        @endif

        {{-- LEMBAR 4: SIKLUS AKUNTANSI, ARUS KAS & CALK --}}
        @if($page == 4)
            <div class="border-b border-slate-200 dark:border-slate-700 pb-4 mb-4">
                <h2 class="text-lg font-bold text-slate-800 dark:text-white">SIKLUS AKUNTANSI, LAPORAN ARUS KAS & CALK ORGANISASI</h2>
                <p class="text-xs text-slate-500">Transparansi Pembukuan & Tata Kelola Koperasi Bermadani</p>
            </div>

            {{-- Flowchart Siklus Akuntansi Digital --}}
            <div class="p-5 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-200 dark:border-slate-700 space-y-4">
                <div class="flex justify-between items-center">
                    <h3 class="text-xs font-bold text-slate-800 dark:text-white uppercase flex items-center gap-2">
                        <i class='bx bx-git-commit text-base text-emerald-600'></i> Tahapan Siklus Akuntansi Digital Koperasi (100% Selesai)
                    </h3>
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-800 flex items-center gap-1">
                        <i class='bx bx-check-double text-sm'></i> 5 / 5 Tahapan Terintegrasi
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-5 gap-3 text-center text-xs">
                    {{-- Step 1 --}}
                    <div class="p-3.5 bg-white dark:bg-slate-800 rounded-xl border border-emerald-300 dark:border-emerald-700 shadow-sm relative overflow-hidden">
                        <div class="absolute top-0 left-0 right-0 h-1 bg-emerald-500"></div>
                        <span class="w-7 h-7 rounded-full bg-emerald-500 text-white font-extrabold inline-flex items-center justify-center text-xs mb-1.5 shadow-sm">1</span>
                        <p class="font-bold text-slate-800 dark:text-white">Input Transaksi</p>
                        <p class="text-[9px] text-emerald-600 dark:text-emerald-400 font-semibold mt-1 flex items-center justify-center gap-0.5">
                            <i class='bx bx-check'></i> Real-time DB
                        </p>
                    </div>

                    {{-- Step 2 --}}
                    <div class="p-3.5 bg-white dark:bg-slate-800 rounded-xl border border-emerald-300 dark:border-emerald-700 shadow-sm relative overflow-hidden">
                        <div class="absolute top-0 left-0 right-0 h-1 bg-emerald-500"></div>
                        <span class="w-7 h-7 rounded-full bg-emerald-500 text-white font-extrabold inline-flex items-center justify-center text-xs mb-1.5 shadow-sm">2</span>
                        <p class="font-bold text-slate-800 dark:text-white">Jurnal Umum</p>
                        <p class="text-[9px] text-emerald-600 dark:text-emerald-400 font-semibold mt-1 flex items-center justify-center gap-0.5">
                            <i class='bx bx-check'></i> Otomatis
                        </p>
                    </div>

                    {{-- Step 3 --}}
                    <div class="p-3.5 bg-white dark:bg-slate-800 rounded-xl border border-emerald-300 dark:border-emerald-700 shadow-sm relative overflow-hidden">
                        <div class="absolute top-0 left-0 right-0 h-1 bg-emerald-500"></div>
                        <span class="w-7 h-7 rounded-full bg-emerald-500 text-white font-extrabold inline-flex items-center justify-center text-xs mb-1.5 shadow-sm">3</span>
                        <p class="font-bold text-slate-800 dark:text-white">Buku Besar</p>
                        <p class="text-[9px] text-emerald-600 dark:text-emerald-400 font-semibold mt-1 flex items-center justify-center gap-0.5">
                            <i class='bx bx-check'></i> Terposting
                        </p>
                    </div>

                    {{-- Step 4 --}}
                    <div class="p-3.5 bg-white dark:bg-slate-800 rounded-xl border border-emerald-300 dark:border-emerald-700 shadow-sm relative overflow-hidden">
                        <div class="absolute top-0 left-0 right-0 h-1 bg-emerald-500"></div>
                        <span class="w-7 h-7 rounded-full bg-emerald-500 text-white font-extrabold inline-flex items-center justify-center text-xs mb-1.5 shadow-sm">4</span>
                        <p class="font-bold text-slate-800 dark:text-white">Neraca Saldo</p>
                        <p class="text-[9px] text-emerald-600 dark:text-emerald-400 font-semibold mt-1 flex items-center justify-center gap-0.5">
                            <i class='bx bx-check'></i> Terverifikasi
                        </p>
                    </div>

                    {{-- Step 5 --}}
                    <div class="p-3.5 bg-emerald-600 text-white rounded-xl font-bold shadow-md relative overflow-hidden col-span-1 border border-emerald-500">
                        <span class="w-7 h-7 rounded-full bg-white text-emerald-700 font-extrabold inline-flex items-center justify-center text-xs mb-1.5 shadow-sm">5</span>
                        <p class="text-white">Laporan RAT 2025</p>
                        <p class="text-[9px] text-emerald-100 font-semibold mt-1 flex items-center justify-center gap-0.5">
                            <i class='bx bx-check-circle'></i> SIAP RAT
                        </p>
                    </div>
                </div>
            </div>

            {{-- CALK 10 Poin --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs">
                <div class="p-3 bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700">
                    <span class="font-bold text-indigo-600">1. Profil Organisasi:</span> Melayani {{ $activeCount }} Anggota Aktif terdaftar.
                </div>
                <div class="p-3 bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700">
                    <span class="font-bold text-indigo-600">2. Saldo Kas Akhir:</span> Rp {{ number_format($kasBankRiil, 0, ',', '.') }} sesuai CSV Line 28.
                </div>
                <div class="p-3 bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700">
                    <span class="font-bold text-indigo-600">3. Aset Tetap CSV:</span> Rp {{ number_format($asetTetap, 0, ',', '.') }} peralatan & inventaris kantor dari CSV Line 13.
                </div>
                <div class="p-3 bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700">
                    <span class="font-bold text-indigo-600">4. Outstanding Pinjaman Bermadani DB:</span> Rp {{ number_format($outstandingPinjamanBermadani, 0, ',', '.') }} pinjaman internal berjalan.
                </div>
                <div class="p-3 bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700">
                    <span class="font-bold text-indigo-600">5. Total Simpanan Pasiva:</span> Rp {{ number_format($totalSimpanan, 0, ',', '.') }} milik {{ $activeCount }} anggota aktif.
                </div>
                <div class="p-3 bg-white dark:bg-slate-800 rounded-xl border border-rose-100 dark:border-rose-900/40">
                    <span class="font-bold text-rose-600">6. Defisit Modal Real Bermadani:</span> -Rp {{ number_format($defisitModal, 0, ',', '.') }} selisih total Aset Bermadani vs Kewajiban Simpanan.
                </div>
            </div>

            {{-- Form Pengesahan --}}
            <div class="pt-6 border-t border-slate-200 dark:border-slate-700 flex justify-between items-end text-xs">
                <div>
                    <p class="font-bold text-slate-800 dark:text-white">Disetujui Peserta RAT 2025</p>
                    <p class="text-slate-400 mt-8">______________________</p>
                    <p class="text-[10px] text-slate-500">Perwakilan Anggota RAT</p>
                </div>
                <div class="text-right">
                    <p class="font-bold text-slate-800 dark:text-white">Disusun Oleh Pengurus Koperasi</p>
                    <p class="text-slate-800 dark:text-white font-bold mt-8">Ridlo Abdillah</p>
                    <p class="text-[10px] text-slate-500">Ketua Koperasi Bermadani</p>
                </div>
            </div>
        @endif

    </div>
</div>
