<div class="space-y-4 sm:space-y-6">
    {{-- Wizard Steps --}}
    <x-rat-wizard-steps :currentStep="3" :sessionId="$ratSession?->id" :sessionStatus="$ratSession?->status ?? 'DRAFT'" />

    {{-- Header & Session Switcher --}}
    <div class="bg-white dark:bg-darkCard p-4 sm:p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 space-y-4">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
            <div class="space-y-1 w-full lg:w-auto">
                <div class="flex items-center gap-2.5">
                    <span class="p-2 sm:p-2.5 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 shrink-0">
                        <i class='bx bx-pie-chart-alt-2 text-xl sm:text-2xl'></i>
                    </span>
                    <div class="w-full">
                        <div class="flex flex-wrap items-center gap-2">
                            <h1 class="text-lg sm:text-xl font-bold text-slate-800 dark:text-white">Alokasi & Kalkulasi SHU</h1>
                            @if($allSessions->count() > 0)
                                <select wire:model.live="sessionId" class="bg-indigo-50 dark:bg-indigo-950/50 border border-indigo-200 dark:border-indigo-800 text-indigo-700 dark:text-indigo-300 text-xs font-bold rounded-xl px-2.5 py-1.5 outline-none cursor-pointer">
                                    @foreach($allSessions as $s)
                                        <option value="{{ $s->id }}">Tahun Buku {{ $s->year }} (Status: {{ $s->status }})</option>
                                    @endforeach
                                </select>
                            @endif
                        </div>
                        <p class="text-[11px] sm:text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Step 3: Hitung alokasi SHU per anggota berdasarkan Jasa Simpanan & Jasa Usaha, lalu sahkan.
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2 w-full lg:w-auto">
                @if($ratSession)
                    <button wire:click="exportCsv"
                        class="w-full sm:w-auto bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-3.5 py-2.5 rounded-xl shadow-md shadow-emerald-600/20 transition-all flex items-center justify-center gap-1.5 min-h-[44px]">
                        <i class='bx bx-download text-base'></i> Export CSV / Excel
                    </button>
                    <a href="{{ route('admin.rat.pdf-report', $ratSession->id) }}" target="_blank"
                        class="w-full sm:w-auto bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold px-3.5 py-2.5 rounded-xl shadow-md shadow-rose-600/20 transition-all flex items-center justify-center gap-1.5 min-h-[44px]">
                        <i class='bx bxs-file-pdf text-base'></i> PDF Laporan SHU
                    </a>
                @endif
                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold {{ $ratSession?->isFinalized() ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' }}">
                    {{ $ratSession?->status_label ?? 'N/A' }}
                </span>
            </div>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if (session()->has('success'))
        <div class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-400 px-4 py-3 rounded-xl flex items-center gap-2">
            <i class='bx bx-check-circle text-xl shrink-0'></i>
            <span class="text-xs font-medium">{{ session('success') }}</span>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-400 px-4 py-3 rounded-xl flex items-center gap-2">
            <i class='bx bx-error-circle text-xl shrink-0'></i>
            <span class="text-xs font-medium">{{ session('error') }}</span>
        </div>
    @endif
    @if (session()->has('info'))
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 text-blue-700 dark:text-blue-400 px-4 py-3 rounded-xl flex items-center gap-2">
            <i class='bx bx-info-circle text-xl shrink-0'></i>
            <span class="text-xs font-medium">{{ session('info') }}</span>
        </div>
    @endif

    {{-- 5 Pos SHU Allocation Cards --}}
    @if(!empty($summary))
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 sm:gap-4">
            @php
                $allocCards = [
                    ['label' => 'Dana Cadangan', 'amount' => $summary['cadanganPool'] ?? 0, 'pct' => $ratSession?->cadangan_percentage ?? 25, 'color' => 'blue', 'icon' => 'bx-shield-quarter'],
                    ['label' => 'Jasa Simpanan', 'amount' => $summary['jasaSimpananPool'] ?? 0, 'pct' => $ratSession?->jasa_simpanan_percentage ?? 30, 'color' => 'emerald', 'icon' => 'bx-coin-stack'],
                    ['label' => 'Jasa Usaha', 'amount' => $summary['jasaUsahaPool'] ?? 0, 'pct' => $ratSession?->jasa_usaha_percentage ?? 25, 'color' => 'amber', 'icon' => 'bx-shopping-bag'],
                    ['label' => 'Pengurus', 'amount' => $summary['pengurusPool'] ?? 0, 'pct' => $ratSession?->pengurus_percentage ?? 10, 'color' => 'purple', 'icon' => 'bx-user-voice'],
                    ['label' => 'Dana Sosial', 'amount' => $summary['danaSosialPool'] ?? 0, 'pct' => $ratSession?->dana_sosial_percentage ?? 10, 'color' => 'rose', 'icon' => 'bx-heart'],
                ];
            @endphp

            @foreach($allocCards as $card)
                <div class="bg-white dark:bg-darkCard p-3.5 sm:p-4 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
                    <div class="flex items-center gap-2 mb-1.5">
                        <div class="w-6 h-6 sm:w-7 sm:h-7 rounded-lg bg-indigo-50 dark:bg-indigo-950/40 flex items-center justify-center shrink-0">
                            <i class='bx {{ $card["icon"] }} text-indigo-600 dark:text-indigo-400 text-xs sm:text-sm'></i>
                        </div>
                        <span class="text-[9px] sm:text-[10px] font-bold text-slate-500 uppercase tracking-wider truncate">{{ $card['label'] }}</span>
                    </div>
                    <h4 class="text-xs sm:text-sm font-extrabold text-slate-800 dark:text-white font-mono">
                        Rp {{ number_format((float) $card['amount'], 0, ',', '.') }}
                    </h4>
                    <p class="text-[9px] text-indigo-500 font-medium mt-0.5">{{ number_format((float) $card['pct'], 1) }}% dari SHU</p>
                </div>
            @endforeach
        </div>

        {{-- Summary Row --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
            <div class="bg-slate-50 dark:bg-slate-800/50 p-3.5 sm:p-4 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 flex items-center gap-3">
                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-indigo-50 dark:bg-indigo-950/40 flex items-center justify-center text-indigo-600 dark:text-indigo-400 shrink-0">
                    <i class='bx bx-user-check text-lg sm:text-xl'></i>
                </div>
                <div>
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Anggota Berhak</p>
                    <h4 class="text-sm sm:text-base font-extrabold text-slate-800 dark:text-white">{{ $summary['eligibleCount'] ?? 0 }} Anggota</h4>
                </div>
            </div>
            <div class="bg-emerald-50/50 dark:bg-emerald-950/20 p-3.5 sm:p-4 rounded-2xl shadow-sm border border-emerald-200 dark:border-emerald-800/60 flex items-center gap-3">
                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 flex items-center justify-center text-emerald-600 dark:text-emerald-400 shrink-0">
                    <i class='bx bx-wallet text-lg sm:text-xl'></i>
                </div>
                <div>
                    <p class="text-[9px] font-bold text-emerald-500 uppercase tracking-widest">Total SHU Dibagikan</p>
                    <h4 class="text-sm sm:text-base font-extrabold text-emerald-600 dark:text-emerald-400 font-mono">Rp {{ number_format($summary['totalMemberShu'] ?? 0, 0, ',', '.') }}</h4>
                </div>
            </div>
            <div class="bg-blue-50/50 dark:bg-blue-950/20 p-3.5 sm:p-4 rounded-2xl shadow-sm border border-blue-200 dark:border-blue-800/60 flex items-center gap-3">
                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-blue-50 dark:bg-blue-950/40 flex items-center justify-center text-blue-600 dark:text-blue-400 shrink-0">
                    <i class='bx bx-building text-lg sm:text-xl'></i>
                </div>
                <div>
                    <p class="text-[9px] font-bold text-blue-500 uppercase tracking-widest">Laba Ditahan</p>
                    <h4 class="text-sm sm:text-base font-extrabold text-blue-600 dark:text-blue-400 font-mono">Rp {{ number_format($summary['retainedAmount'] ?? 0, 0, ',', '.') }}</h4>
                </div>
            </div>
        </div>
    @endif

    {{-- Calculate Button CTA --}}
    @if(!$ratSession?->isFinalized())
        <div class="bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-indigo-950/20 dark:to-purple-950/20 p-4 sm:p-6 rounded-2xl border border-indigo-200 dark:border-indigo-800">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                    <h3 class="text-xs sm:text-sm font-bold text-slate-800 dark:text-white flex items-center gap-2">
                        <i class='bx bx-calculator text-indigo-500'></i>
                        Perhitungan SHU Per Anggota
                    </h3>
                    <p class="text-[10px] sm:text-xs text-slate-500 mt-1">
                        Klik tombol di bawah untuk menghitung ulang distribusi SHU seluruh anggota berdasarkan konfigurasi saat ini.
                    </p>
                </div>
                <button wire:click="recalculate"
                    class="w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold px-6 py-3 rounded-xl shadow-lg shadow-indigo-600/20 transition-all flex items-center justify-center gap-2 whitespace-nowrap min-h-[44px]">
                    <i class='bx bx-refresh text-lg'></i> Hitung Ulang SHU
                </button>
            </div>
        </div>
    @endif

    {{-- Formula Info Card (Panduan Rumus Transparan Admin) --}}
    <div x-data="{ openFormula: false }" class="bg-white dark:bg-darkCard rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">
        <button @click="openFormula = !openFormula" 
            class="w-full p-4 flex items-center justify-between bg-slate-50/50 dark:bg-slate-800/30 hover:bg-slate-100/50 transition-all text-left">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                    <i class='bx bx-help-circle text-lg'></i>
                </div>
                <div>
                    <h3 class="text-xs font-bold text-slate-800 dark:text-white">💡 Panduan Rumus Perhitungan SHU Transparan</h3>
                    <p class="text-[10px] text-slate-500">Klik untuk melihat rumus pembagian SHU Jasa Simpanan & Jasa Usaha</p>
                </div>
            </div>
            <i class='bx text-slate-400 text-lg transition-transform duration-200' :class="openFormula ? 'bx-chevron-up' : 'bx-chevron-down'"></i>
        </button>

        <div x-show="openFormula" x-transition class="p-4 sm:p-6 border-t border-slate-100 dark:border-slate-700 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Jasa Simpanan Formula --}}
                <div class="bg-emerald-50/60 dark:bg-emerald-950/20 p-4 rounded-xl border border-emerald-200 dark:border-emerald-800/40">
                    <div class="flex items-center gap-2 mb-2">
                        <i class='bx bx-coin-stack text-emerald-600 text-base'></i>
                        <h4 class="text-xs font-bold text-emerald-800 dark:text-emerald-300">1. Rumus Jasa Simpanan</h4>
                    </div>
                    <div class="p-2.5 bg-white dark:bg-slate-900 rounded-lg border border-emerald-100 dark:border-emerald-900 font-mono text-[11px] text-slate-700 dark:text-slate-300 mb-2 overflow-x-auto">
                        Jasa Simpanan = (Total Simpanan Anggota ÷ Total Simpanan Seluruh Anggota) × Pool Jasa Simpanan
                    </div>
                    <ul class="text-[10px] text-emerald-700 dark:text-emerald-400 space-y-1">
                        <li>• <strong>Simpanan Anggota</strong> = Simpanan Pokok + Simpanan Wajib s/d Cutoff (31 Des {{ $ratSession?->year }}).</li>
                        <li>• Setoran periode berikutnya <strong>TIDAK dihitung</strong> dalam pembagian RAT {{ $ratSession?->year }}.</li>
                    </ul>
                </div>

                {{-- Jasa Usaha Formula --}}
                <div class="bg-amber-50/60 dark:bg-amber-950/20 p-4 rounded-xl border border-amber-200 dark:border-amber-800/40">
                    <div class="flex items-center gap-2 mb-2">
                        <i class='bx bx-shopping-bag text-amber-600 text-base'></i>
                        <h4 class="text-xs font-bold text-amber-800 dark:text-amber-300">2. Rumus Jasa Usaha (Belanja POS)</h4>
                    </div>
                    <div class="p-2.5 bg-white dark:bg-slate-900 rounded-lg border border-amber-100 dark:border-amber-900 font-mono text-[11px] text-slate-700 dark:text-slate-300 mb-2 overflow-x-auto">
                        Jasa Usaha = (Total Belanja Anggota ÷ Total Belanja Seluruh Anggota) × Pool Jasa Usaha
                    </div>
                    <ul class="text-[10px] text-amber-700 dark:text-amber-400 space-y-1">
                        <li>• <strong>Belanja Anggota</strong> = Akumulasi transaksi belanja di Minimarket Koperasi selama {{ $ratSession?->year }}.</li>
                        <li>• Anggota yang lebih sering belanja mendapat bagian Jasa Usaha lebih besar.</li>
                    </ul>
                </div>
            </div>

            <div class="bg-indigo-50/60 dark:bg-indigo-950/20 p-4 rounded-xl border border-indigo-200 dark:border-indigo-800/40">
                <div class="flex items-center gap-2 mb-2">
                    <i class='bx bx-pie-chart-alt text-indigo-600 text-base'></i>
                    <h4 class="text-xs font-bold text-indigo-800 dark:text-indigo-300">3. Maksud Kolom Porsi (%)</h4>
                </div>
                <div class="p-2.5 bg-white dark:bg-slate-900 rounded-lg border border-indigo-100 dark:border-indigo-900 font-mono text-[11px] text-slate-700 dark:text-slate-300 mb-2 overflow-x-auto">
                    Porsi (%) = (Total SHU Diterima Anggota ÷ Total SHU Anggota) × 100%
                </div>
                <p class="text-[10px] text-indigo-700 dark:text-indigo-400">
                    Menunjukkan berapa persen alokasi SHU yang didapatkan oleh 1 orang anggota dari total pool SHU Anggota. Total seluruh porsi anggota jika dijumlahkan tepat <strong>100.00%</strong>.
                </p>
            </div>
        </div>
    </div>

    {{-- Main Distribution Content (Search Bar, Mobile Cards & Desktop Table) --}}
    <div class="bg-white dark:bg-darkCard p-4 sm:p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 space-y-4">
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
            <h3 class="text-xs sm:text-sm font-bold text-slate-800 dark:text-white flex items-center gap-2">
                <i class='bx bx-table text-base text-slate-400'></i>
                Tabel Distribusi SHU Per Anggota
            </h3>
            <input type="text" wire:model.live.debounce.300ms="searchMember"
                placeholder="Cari Nama, No. Anggota, Unit Kerja..."
                class="bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs rounded-xl px-4 py-2.5 text-slate-800 dark:text-white outline-none w-full sm:w-64 focus:ring-2 focus:ring-primary/20 min-h-[42px]">
        </div>

        {{-- 📱 MOBILE CARD VIEW (< md) --}}
        <div class="block md:hidden space-y-3">
            @forelse($distributions as $index => $dist)
                @php $member = $dist->member; @endphp
                <div wire:key="mob-alloc-{{ $dist->id }}"
                    class="bg-slate-50 dark:bg-slate-800/60 p-4 rounded-2xl border border-slate-100 dark:border-slate-700/80 space-y-3">
                    
                    {{-- Card Header --}}
                    <div class="flex items-start justify-between gap-2 border-b border-slate-200/60 dark:border-slate-700/60 pb-2">
                        <div>
                            <span class="font-mono text-[10px] font-bold px-2 py-0.5 rounded-md bg-indigo-50 text-indigo-600 dark:bg-indigo-950/50 dark:text-indigo-300">
                                {{ $member?->nomorAnggota ?? '-' }}
                            </span>
                            <h4 class="text-sm font-bold text-slate-800 dark:text-white mt-1">
                                {{ $member?->name ?? '-' }}
                            </h4>
                            <p class="text-[10px] text-slate-400">{{ $member?->unitKerja ?? 'Unit Kerja -' }}</p>
                        </div>
                        <div class="text-right">
                            <span class="text-[9px] font-bold text-slate-400 block uppercase">Porsi SHU</span>
                            <span class="font-mono font-bold text-indigo-600 dark:text-indigo-400 text-xs">
                                {{ number_format((float) $dist->portion_percentage, 3, ',', '.') }}%
                            </span>
                        </div>
                    </div>

                    {{-- Main Content --}}
                    <div class="flex items-center justify-between bg-white dark:bg-slate-800 p-3 rounded-xl border border-slate-100 dark:border-slate-700">
                        <div>
                            <span class="text-[9px] font-bold text-slate-400 uppercase block">Total SHU Diterima</span>
                            <span class="text-base font-extrabold text-emerald-600 dark:text-emerald-400 font-mono">
                                Rp {{ number_format((float) $dist->shu_amount, 0, ',', '.') }}
                            </span>
                        </div>
                        <div class="text-right text-[10px] text-slate-500 dark:text-slate-400 space-y-0.5">
                            <div>J. Simpanan: <strong class="text-emerald-600">Rp {{ number_format((float) $dist->jasa_simpanan_amount, 0, ',', '.') }}</strong></div>
                            <div>J. Usaha: <strong class="text-amber-600">Rp {{ number_format((float) $dist->jasa_usaha_amount, 0, ',', '.') }}</strong></div>
                        </div>
                    </div>

                    {{-- Card Footer Details & Trigger Modal --}}
                    <div class="flex items-center justify-between pt-1">
                        <div class="text-[10px] text-slate-400">
                            Simpanan: Rp {{ number_format((float) $dist->total_simpanan_amount, 0, ',', '.') }}
                        </div>
                        <button wire:click="openDetailModal({{ $dist->id }})"
                            class="px-3 py-1.5 rounded-lg text-xs font-bold bg-indigo-50 text-indigo-600 dark:bg-indigo-950/50 dark:text-indigo-300 flex items-center gap-1 min-h-[38px]">
                            <i class='bx bx-search text-sm'></i> Rincian Rumus
                        </button>
                    </div>
                </div>
            @empty
                <div class="py-10 text-center text-slate-400">
                    <i class='bx bx-calculator text-4xl text-slate-300 mb-2 block'></i>
                    Belum ada data alokasi. Klik <strong>"Hitung Ulang SHU"</strong> untuk memulai.
                </div>
            @endforelse
        </div>

        {{-- 🖥️ DESKTOP DATA TABLE (≥ md) --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800 text-slate-500 dark:text-slate-400 uppercase text-[10px] tracking-wider border-b border-slate-200 dark:border-slate-700">
                        <th class="py-3 px-3">No</th>
                        <th class="py-3 px-3">No. Anggota</th>
                        <th class="py-3 px-3">Nama</th>
                        <th class="py-3 px-3 text-right">Simp. Pokok</th>
                        <th class="py-3 px-3 text-right">Simp. Wajib</th>
                        <th class="py-3 px-3 text-right">Total Belanja</th>
                        <th class="py-3 px-3 text-right">Jasa Simpanan</th>
                        <th class="py-3 px-3 text-right">Jasa Usaha</th>
                        <th class="py-3 px-3 text-center">Porsi</th>
                        <th class="py-3 px-3 text-right font-bold">Total SHU</th>
                        <th class="py-3 px-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse($distributions as $index => $dist)
                        @php $member = $dist->member; @endphp
                        <tr wire:key="desk-alloc-{{ $dist->id }}" 
                            class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-all {{ (float) $dist->shu_amount <= 0 ? 'opacity-40' : '' }}">
                            <td class="py-2.5 px-3 font-mono text-slate-400">
                                {{ method_exists($distributions, 'firstItem') ? $distributions->firstItem() + $index : $index + 1 }}
                            </td>
                            <td class="py-2.5 px-3 font-mono font-bold text-slate-700 dark:text-slate-300">
                                {{ $member?->nomorAnggota ?? '-' }}
                            </td>
                            <td class="py-2.5 px-3 font-bold text-slate-800 dark:text-white">
                                <div>{{ $member?->name ?? '-' }}</div>
                                <div class="text-[9px] text-slate-400">{{ $member?->unitKerja ?? '' }}</div>
                            </td>
                            <td class="py-2.5 px-3 text-right font-mono text-slate-600 dark:text-slate-400">
                                Rp {{ number_format((float) ($dist->simpanan_pokok_snapshot > 0 ? $dist->simpanan_pokok_snapshot : ($member?->simpananPokok ?? 0)), 0, ',', '.') }}
                            </td>
                            <td class="py-2.5 px-3 text-right font-mono text-slate-600 dark:text-slate-400">
                                Rp {{ number_format((float) ($dist->simpanan_wajib_snapshot > 0 ? $dist->simpanan_wajib_snapshot : ($dist->total_simpanan_amount ?? $member?->simpananWajib ?? 0)), 0, ',', '.') }}
                            </td>
                            <td class="py-2.5 px-3 text-right font-mono text-slate-600 dark:text-slate-400">
                                Rp {{ number_format((float) $dist->total_transaksi_amount, 0, ',', '.') }}
                            </td>
                            <td class="py-2.5 px-3 text-right font-mono text-emerald-600 dark:text-emerald-400">
                                Rp {{ number_format((float) $dist->jasa_simpanan_amount, 0, ',', '.') }}
                            </td>
                            <td class="py-2.5 px-3 text-right font-mono text-amber-600 dark:text-amber-400">
                                Rp {{ number_format((float) $dist->jasa_usaha_amount, 0, ',', '.') }}
                            </td>
                            <td class="py-2.5 px-3 text-center font-mono font-bold text-indigo-600 dark:text-indigo-400">
                                {{ number_format((float) $dist->portion_percentage, 3, ',', '.') }}%
                            </td>
                            <td class="py-2.5 px-3 text-right font-mono font-bold text-emerald-600 dark:text-emerald-400 text-sm">
                                Rp {{ number_format((float) $dist->shu_amount, 0, ',', '.') }}
                            </td>
                            <td class="py-2.5 px-3 text-center">
                                <button wire:click="openDetailModal({{ $dist->id }})"
                                    title="Lihat Rincian Perhitungan SHU"
                                    class="px-2 py-1.5 rounded-lg text-[10px] font-bold bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-300 transition-all">
                                    <i class='bx bx-search text-xs'></i> Detail
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="py-8 text-center text-slate-400">
                                <div class="flex flex-col items-center gap-2">
                                    <i class='bx bx-calculator text-4xl text-slate-300'></i>
                                    <p>Belum ada data distribusi. Klik <strong>"Hitung Ulang SHU"</strong> untuk memulai perhitungan.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($distributions, 'links'))
            <div class="mt-4">
                {{ $distributions->links() }}
            </div>
        @endif
    </div>

    {{-- MODAL: Detail Breakdown Kalkulasi SHU Anggota (Mobile Bottom Sheet / Desktop Centered) --}}
    @if($showDetailModal && $selectedDistribution)
        <div class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-end sm:items-center justify-center p-0 sm:p-4 animate-fade-in"
            wire:keydown.escape="closeDetailModal">
            <div class="bg-white dark:bg-darkCard w-full sm:max-w-xl rounded-t-3xl sm:rounded-2xl p-5 sm:p-6 shadow-2xl border border-slate-100 dark:border-slate-700 space-y-4 max-h-[90vh] overflow-y-auto">
                {{-- Header --}}
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-700 pb-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-950/50 flex items-center justify-center text-indigo-600 dark:text-indigo-400 shrink-0">
                            <i class='bx bx-calculator text-xl'></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-sm sm:text-base text-slate-800 dark:text-white">Detail Kalkulasi SHU Anggota</h3>
                            <p class="text-xs text-slate-400">RAT Tahun Buku {{ $selectedDistribution->ratSession?->year }}</p>
                        </div>
                    </div>
                    <button wire:click="closeDetailModal" class="text-slate-400 hover:text-slate-600 dark:hover:text-white p-1">
                        <i class='bx bx-x text-2xl'></i>
                    </button>
                </div>

                {{-- Member Meta Info --}}
                <div class="bg-slate-50 dark:bg-slate-800/60 p-3.5 rounded-xl space-y-2 text-xs">
                    <div class="flex justify-between items-center">
                        <span class="text-slate-400 font-medium">Nama Anggota:</span>
                        <span class="font-bold text-slate-800 dark:text-white">{{ $selectedDistribution->member?->name }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-slate-400 font-medium">Nomor Anggota (NIK):</span>
                        <span class="font-mono font-bold text-indigo-600 dark:text-indigo-400">{{ $selectedDistribution->member?->nomorAnggota }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-slate-400 font-medium">Unit Kerja:</span>
                        <span class="font-semibold text-slate-700 dark:text-slate-300">{{ $selectedDistribution->member?->unitKerja ?? '-' }}</span>
                    </div>
                </div>

                {{-- Detailed Formula Steps --}}
                <div class="space-y-3">
                    {{-- 1. Jasa Simpanan Step --}}
                    <div class="p-3 bg-emerald-50/50 dark:bg-emerald-950/30 rounded-xl border border-emerald-100 dark:border-emerald-900/30 text-xs space-y-1">
                        <div class="flex justify-between font-bold text-emerald-700 dark:text-emerald-300">
                            <span>1. Jasa Simpanan (Alokasi Modal)</span>
                            <span>Rp {{ number_format((float)$selectedDistribution->jasa_simpanan_amount, 0, ',', '.') }}</span>
                        </div>
                        <p class="text-[10px] text-emerald-600 dark:text-emerald-400">
                            Simpanan Pokok: Rp {{ number_format((float)$selectedDistribution->simpanan_pokok_snapshot, 0, ',', '.') }} + Wajib: Rp {{ number_format((float)$selectedDistribution->simpanan_wajib_snapshot, 0, ',', '.') }} = Total Rp {{ number_format((float)$selectedDistribution->total_simpanan_amount, 0, ',', '.') }}
                        </p>
                    </div>

                    {{-- 2. Jasa Usaha Step --}}
                    <div class="p-3 bg-amber-50/50 dark:bg-amber-950/30 rounded-xl border border-amber-100 dark:border-amber-900/30 text-xs space-y-1">
                        <div class="flex justify-between font-bold text-amber-700 dark:text-amber-300">
                            <span>2. Jasa Usaha (Alokasi Partisipasi)</span>
                            <span>Rp {{ number_format((float)$selectedDistribution->jasa_usaha_amount, 0, ',', '.') }}</span>
                        </div>
                        <p class="text-[10px] text-amber-600 dark:text-amber-400">
                            Total Belanja Minimarket 2025: Rp {{ number_format((float)$selectedDistribution->total_transaksi_amount, 0, ',', '.') }}
                        </p>
                    </div>

                    {{-- Total SHU Box --}}
                    <div class="p-4 bg-gradient-to-r from-emerald-600 to-indigo-600 text-white rounded-xl shadow-md flex justify-between items-center">
                        <div>
                            <span class="text-[10px] font-bold uppercase text-emerald-100 block">Total Hak SHU Anggota</span>
                            <span class="text-xs text-emerald-200">Porsi: {{ number_format((float)$selectedDistribution->portion_percentage, 4, ',', '.') }}%</span>
                        </div>
                        <span class="text-lg font-extrabold font-mono">
                            Rp {{ number_format((float)$selectedDistribution->shu_amount, 0, ',', '.') }}
                        </span>
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="flex justify-end pt-2 border-t border-slate-100 dark:border-slate-700">
                    <button wire:click="closeDetailModal" class="px-5 py-2.5 text-xs font-bold text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl min-h-[44px]">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Actions Footer --}}
    <div class="bg-white dark:bg-darkCard p-4 sm:p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                <button wire:click="goBack"
                    class="w-full sm:w-auto bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 transition-all flex items-center justify-center gap-2 min-h-[44px]">
                    <i class='bx bx-left-arrow-alt text-base'></i> Kembali
                </button>

                @if($ratSession?->isFinalized())
                    <button wire:click="reopenSession"
                        class="w-full sm:w-auto bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold px-4 py-2.5 rounded-xl transition-all flex items-center justify-center gap-2 min-h-[44px]">
                        <i class='bx bx-edit text-base'></i> Buka Kembali (Draft)
                    </button>
                @endif
            </div>

            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                @if(!$ratSession?->isFinalized())
                    <button wire:click="finalizeSession"
                        onclick="return confirm('Apakah Anda yakin? SHU akan dipublikasikan ke portal anggota.')"
                        class="w-full sm:w-auto bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-5 py-2.5 rounded-xl shadow-lg shadow-emerald-600/20 transition-all flex items-center justify-center gap-2 min-h-[44px]">
                        <i class='bx bx-check-double text-base'></i> Sahkan & Publikasikan SHU
                    </button>
                @else
                    <button wire:click="advanceToDisbursement"
                        class="w-full sm:w-auto bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-5 py-2.5 rounded-xl shadow-lg shadow-emerald-600/20 transition-all flex items-center justify-center gap-2 min-h-[44px]">
                        Lanjut ke Pencairan SHU
                        <i class='bx bx-right-arrow-alt text-base'></i>
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>
