<div class="space-y-4 sm:space-y-6" x-data="{ showBeritaAcaraModal: false, activeTab: 'acara', showMobileActions: false }">
    {{-- Print Style --}}
    <style>
        @media print {
            body * { visibility: hidden; }
            #printableSection, #printableSection *, #receiptPrintableSection, #receiptPrintableSection * { visibility: visible; }
            #printableSection, #receiptPrintableSection { position: absolute; left: 0; top: 0; width: 100%; }
            .no-print { display: none !important; }
        }
    </style>

    {{-- Wizard Steps --}}
    <div class="no-print">
        <x-rat-wizard-steps :currentStep="4" :sessionId="$ratSession?->id" :sessionStatus="$ratSession?->status ?? 'DRAFT'" />
    </div>

    {{-- Header & Session Switcher --}}
    <div class="bg-white dark:bg-darkCard p-4 sm:p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 no-print space-y-4">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
            <div class="space-y-1 w-full lg:w-auto">
                <div class="flex items-center gap-2.5">
                    <span class="p-2 sm:p-2.5 rounded-xl bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 shrink-0">
                        <i class='bx bx-wallet text-xl sm:text-2xl'></i>
                    </span>
                    <div class="w-full">
                        <div class="flex flex-wrap items-center gap-2">
                            <h1 class="text-lg sm:text-xl font-bold text-slate-800 dark:text-white">Pencairan & Data SHU Anggota</h1>
                            @if($allSessions->count() > 0)
                                <select wire:model.live="sessionId" class="bg-indigo-50 dark:bg-indigo-950/50 border border-indigo-200 dark:border-indigo-800 text-indigo-700 dark:text-indigo-300 text-xs font-bold rounded-xl px-2.5 py-1.5 outline-none cursor-pointer">
                                    @foreach($allSessions as $s)
                                        <option value="{{ $s->id }}">Tahun Buku {{ $s->year }} (Status: {{ $s->status }})</option>
                                    @endforeach
                                </select>
                            @endif
                        </div>
                        <p class="text-[11px] sm:text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Step 4: Pantau data SHU per anggota, pencairan tunai/sukarela, dan cetak slip kwitansi.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Action Buttons (Desktop & Tablet) --}}
            <div class="hidden sm:flex flex-wrap items-center gap-2 w-full lg:w-auto">
                @if($ratSession)
                    <button wire:click="exportCsv"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-3.5 py-2.5 rounded-xl shadow-md shadow-emerald-600/20 transition-all flex items-center gap-1.5 min-h-[40px]">
                        <i class='bx bx-download text-base'></i> Export CSV
                    </button>
                    <button wire:click="openAddManualModal"
                        class="bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold px-3.5 py-2.5 rounded-xl shadow-md shadow-amber-600/20 transition-all flex items-center gap-1.5 min-h-[40px]">
                        <i class='bx bx-user-plus text-base'></i> + Susulan
                    </button>
                    <button @click="showBeritaAcaraModal = true"
                        class="bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white text-xs font-bold px-3.5 py-2.5 rounded-xl shadow-md shadow-indigo-600/20 transition-all flex items-center gap-1.5 min-h-[40px]">
                        <i class='bx bxs-file-doc text-base'></i> Berita Acara
                    </button>
                    <a href="{{ route('admin.rat.pdf-report', $ratSession->id) }}" target="_blank"
                        class="bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold px-3.5 py-2.5 rounded-xl shadow-md shadow-rose-600/20 transition-all flex items-center gap-1.5 min-h-[40px]">
                        <i class='bx bxs-file-pdf text-base'></i> PDF Laporan
                    </a>
                @endif
                <button onclick="window.print()"
                    class="bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 transition-all flex items-center gap-1.5 min-h-[40px]">
                    <i class='bx bx-printer text-base'></i> Cetak Rekap
                </button>
            </div>

            {{-- Mobile Quick Actions Bar (< sm) --}}
            <div class="grid grid-cols-2 gap-2 w-full sm:hidden">
                @if($ratSession)
                    <button wire:click="exportCsv"
                        class="bg-emerald-600 text-white text-xs font-bold px-3 py-2.5 rounded-xl shadow-sm flex items-center justify-center gap-1.5 min-h-[44px]">
                        <i class='bx bx-download text-base'></i> Export Excel
                    </button>
                    <button wire:click="openAddManualModal"
                        class="bg-amber-600 text-white text-xs font-bold px-3 py-2.5 rounded-xl shadow-sm flex items-center justify-center gap-1.5 min-h-[44px]">
                        <i class='bx bx-user-plus text-base'></i> + Susulan SHU
                    </button>
                    <button @click="showBeritaAcaraModal = true"
                        class="bg-indigo-600 text-white text-xs font-bold px-3 py-2.5 rounded-xl shadow-sm flex items-center justify-center gap-1.5 min-h-[44px]">
                        <i class='bx bxs-file-doc text-base'></i> Berita Acara
                    </button>
                    <a href="{{ route('admin.rat.pdf-report', $ratSession->id) }}" target="_blank"
                        class="bg-rose-600 text-white text-xs font-bold px-3 py-2.5 rounded-xl shadow-sm flex items-center justify-center gap-1.5 min-h-[44px]">
                        <i class='bx bxs-file-pdf text-base'></i> PDF Laporan
                    </a>
                @endif
            </div>
        </div>
    </div>

    {{-- Flash Messages --}}
    <div class="no-print">
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
    </div>

    {{-- Disbursement Progress --}}
    <div class="bg-white dark:bg-darkCard p-4 sm:p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 no-print space-y-4">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
            <h3 class="text-xs sm:text-sm font-bold text-slate-800 dark:text-white flex items-center gap-2">
                <i class='bx bx-trending-up text-base text-emerald-500'></i>
                Progress Pencairan SHU RAT {{ $ratSession?->year }}
            </h3>
            @if($stats['pending'] > 0)
                <button wire:click="batchDisburse"
                    onclick="return confirm('Cairkan SHU untuk {{ $stats['pending'] }} anggota sekaligus?')"
                    class="w-full sm:w-auto bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-lg shadow-emerald-600/20 transition-all flex items-center justify-center gap-1.5 min-h-[44px]">
                    <i class='bx bx-check-double text-base'></i> Cairkan Semua ({{ $stats['pending'] }} anggota)
                </button>
            @endif
        </div>

        {{-- Progress Bar --}}
        <div>
            <div class="flex items-center justify-between text-[11px] sm:text-xs font-bold mb-2">
                <span class="text-emerald-600">{{ $stats['disbursed'] }} dicairkan ({{ $stats['percentage'] }}%)</span>
                <span class="text-amber-500">{{ $stats['pending'] }} belum</span>
                <span class="text-slate-700 dark:text-white">{{ $stats['total'] }} total</span>
            </div>
            <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-3 overflow-hidden">
                <div class="bg-gradient-to-r from-emerald-500 to-emerald-400 h-full rounded-full transition-all duration-500"
                    style="width: {{ $stats['percentage'] }}%"></div>
            </div>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="bg-slate-50 dark:bg-slate-800/50 p-3.5 sm:p-4 rounded-xl flex items-center gap-3">
                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-indigo-50 dark:bg-indigo-950/40 flex items-center justify-center text-indigo-600 dark:text-indigo-400 shrink-0">
                    <i class='bx bx-money text-lg sm:text-xl'></i>
                </div>
                <div>
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Total SHU Anggota</p>
                    <h4 class="text-sm sm:text-base font-extrabold text-slate-800 dark:text-white">Rp {{ number_format($stats['totalAmount'], 0, ',', '.') }}</h4>
                </div>
            </div>
            <div class="bg-emerald-50/50 dark:bg-emerald-950/20 p-3.5 sm:p-4 rounded-xl flex items-center gap-3">
                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 flex items-center justify-center text-emerald-600 dark:text-emerald-400 shrink-0">
                    <i class='bx bx-check-circle text-lg sm:text-xl'></i>
                </div>
                <div>
                    <p class="text-[9px] font-bold text-emerald-500 uppercase tracking-widest">Sudah Dicairkan</p>
                    <h4 class="text-sm sm:text-base font-extrabold text-emerald-600 dark:text-emerald-400">Rp {{ number_format($stats['disbursedAmount'], 0, ',', '.') }}</h4>
                </div>
            </div>
            <div class="bg-amber-50/50 dark:bg-amber-950/20 p-3.5 sm:p-4 rounded-xl flex items-center gap-3">
                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-amber-50 dark:bg-amber-950/40 flex items-center justify-center text-amber-500 dark:text-amber-400 shrink-0">
                    <i class='bx bx-time-five text-lg sm:text-xl'></i>
                </div>
                <div>
                    <p class="text-[9px] font-bold text-amber-500 uppercase tracking-widest">Belum Dicairkan</p>
                    <h4 class="text-sm sm:text-base font-extrabold text-amber-600 dark:text-amber-400">Rp {{ number_format($stats['pendingAmount'], 0, ',', '.') }}</h4>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content Section (Search, Filter, Mobile Cards & Desktop Table) --}}
    <div id="printableSection" class="bg-white dark:bg-darkCard p-4 sm:p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
        {{-- Print Header --}}
        <div class="mb-4 pb-3 border-b border-slate-200 dark:border-slate-700">
            <h2 class="text-base font-bold text-slate-800 dark:text-white">DAFTAR HAK SHU & PENCAIRAN ANGGOTA</h2>
            <p class="text-[10px] text-slate-500">{{ $ratSession?->title }} • Tanggal Pelaksanaan: {{ $ratSession?->event_date?->format('d F Y') }}</p>
        </div>

        {{-- Info Cutoff Notice --}}
        <div class="mb-4 p-3 bg-blue-50 dark:bg-blue-950/40 rounded-xl border border-blue-100 dark:border-blue-800/40 text-xs text-blue-700 dark:text-blue-300 flex items-center gap-2.5 no-print">
            <i class='bx bx-info-circle text-lg shrink-0 text-blue-600 dark:text-blue-400'></i>
            <span>
                <strong>Catatan Saldo Simpanan RAT {{ $ratSession?->year }}:</strong> Nominal simpanan yang dijadikan dasar pembagian SHU adalah <strong>Snapshot Per Cutoff 31 Desember {{ $ratSession?->year }}</strong>. Setoran simpanan anggota di tahun berikutnya (misal tahun 2026) tercatat aman di menu Anggota, dan akan dihitung pada pembagian RAT periode berikutnya.
            </span>
        </div>

        {{-- Search & Filter Bar --}}
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 mb-4 no-print">
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                <input type="text" wire:model.live.debounce.300ms="searchMember"
                    placeholder="Cari Nama, No. Anggota, Unit Kerja..."
                    class="bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs rounded-xl px-4 py-2.5 text-slate-800 dark:text-white outline-none w-full sm:w-64 focus:ring-2 focus:ring-primary/20 min-h-[42px]">
                <select wire:model.live="filterDisbursed"
                    class="bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs rounded-xl px-3 py-2.5 text-slate-800 dark:text-white outline-none cursor-pointer min-h-[42px]">
                    <option value="ALL">Status: Semua Anggota</option>
                    <option value="PENDING">Status: Belum Dicairkan</option>
                    <option value="DISBURSED">Status: Sudah Dicairkan</option>
                </select>
            </div>

            {{-- Desktop View Mode Toggle (Ringkas vs Detail Breakdown) --}}
            <div class="hidden md:flex items-center gap-1 bg-slate-100 dark:bg-slate-800 p-1 rounded-xl shrink-0">
                <button wire:click="$set('viewMode', 'SUMMARY')"
                    class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ $viewMode === 'SUMMARY' ? 'bg-white dark:bg-darkCard text-primary dark:text-indigo-400 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">
                    <i class='bx bx-list-ul mr-1'></i> Ringkas
                </button>
                <button wire:click="$set('viewMode', 'DETAILED')"
                    class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ $viewMode === 'DETAILED' ? 'bg-white dark:bg-darkCard text-primary dark:text-indigo-400 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">
                    <i class='bx bx-table mr-1'></i> Rincian SHU
                </button>
            </div>
        </div>

        {{-- 📱 MOBILE CARD VIEW (< md) --}}
        <div class="block md:hidden space-y-3 no-print">
            @forelse($distributions as $index => $dist)
                @php $member = $dist->member; @endphp
                <div wire:key="mob-disb-{{ $dist->id }}"
                    class="bg-slate-50 dark:bg-slate-800/60 p-4 rounded-2xl border border-slate-100 dark:border-slate-700/80 space-y-3">
                    
                    {{-- Card Header --}}
                    <div class="flex items-start justify-between gap-2 border-b border-slate-200/60 dark:border-slate-700/60 pb-2.5">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-mono text-[10px] font-bold px-2 py-0.5 rounded-md bg-indigo-50 text-indigo-600 dark:bg-indigo-950/50 dark:text-indigo-300">
                                    {{ $member?->nomorAnggota ?? '-' }}
                                </span>
                                @if($dist->notes)
                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-semibold bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300">
                                        Susulan
                                    </span>
                                @endif
                            </div>
                            <h4 class="text-sm font-bold text-slate-800 dark:text-white mt-1">
                                {{ $member?->name ?? '-' }}
                            </h4>
                            <p class="text-[10px] text-slate-400">{{ $member?->unitKerja ?? 'Unit Kerja -' }}</p>
                        </div>

                        <div class="text-right shrink-0">
                            @if($dist->is_disbursed)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300">
                                    <i class='bx bx-check mr-1'></i> Dicairkan
                                </span>
                                @if($dist->disbursed_at)
                                    <p class="text-[9px] text-slate-400 mt-0.5">{{ $dist->disbursed_at->format('d/m H:i') }}</p>
                                @endif
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300">
                                    <i class='bx bx-time-five mr-1'></i> Belum
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Nominal SHU & Breakdown --}}
                    <div class="flex items-center justify-between bg-white dark:bg-slate-800 p-3 rounded-xl border border-slate-100 dark:border-slate-700">
                        <div>
                            <span class="text-[9px] font-bold text-slate-400 uppercase block">Total Hak SHU</span>
                            <span class="text-base font-extrabold text-emerald-600 dark:text-emerald-400 font-mono">
                                Rp {{ number_format((float) $dist->shu_amount, 0, ',', '.') }}
                            </span>
                        </div>
                        <div class="text-right text-[10px] text-slate-500 dark:text-slate-400">
                            <span>J. Simpanan: Rp {{ number_format((float) $dist->jasa_simpanan_amount, 0, ',', '.') }}</span><br/>
                            <span>J. Usaha: Rp {{ number_format((float) $dist->jasa_usaha_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    {{-- Total Pencairan (Simpanan + SHU) --}}
                    @if($member)
                        @php $mobTotalSimpanan = (float)$member->simpananPokok + (float)$member->simpananWajib; $mobTotalPencairan = $mobTotalSimpanan + (float)$dist->shu_amount; @endphp
                        <div class="bg-gradient-to-r from-amber-50 to-yellow-50 dark:from-amber-950/30 dark:to-yellow-950/20 p-3 rounded-xl border border-amber-200 dark:border-amber-800/50">
                            <div class="flex items-center gap-1.5 mb-1.5">
                                <i class='bx bx-calculator text-amber-600 dark:text-amber-400 text-sm'></i>
                                <span class="text-[9px] font-bold text-amber-600 dark:text-amber-400 uppercase tracking-wider">Total Pencairan (Jika Keluar)</span>
                            </div>
                            <div class="flex items-baseline justify-between">
                                <span class="text-lg font-extrabold text-amber-700 dark:text-amber-300 font-mono">
                                    Rp {{ number_format($mobTotalPencairan, 0, ',', '.') }}
                                </span>
                                <span class="text-[9px] text-amber-500 dark:text-amber-400">
                                    Simpanan Rp {{ number_format($mobTotalSimpanan, 0, ',', '.') }} + SHU Rp {{ number_format((float) $dist->shu_amount, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    @endif

                    {{-- Mobile Action Buttons Grid --}}
                    <div class="grid grid-cols-2 gap-2 pt-1">
                        <button wire:click="openDetailModal({{ $dist->id }})"
                            class="px-3 py-2 rounded-xl text-xs font-bold bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 flex items-center justify-center gap-1 min-h-[44px]">
                            <i class='bx bx-search text-sm'></i> Detail
                        </button>

                        <button wire:click="openReceiptModal({{ $dist->id }})"
                            class="px-3 py-2 rounded-xl text-xs font-bold bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-300 flex items-center justify-center gap-1 min-h-[44px]">
                            <i class='bx bx-receipt text-sm'></i> Slip SHU
                        </button>

                        @if($dist->is_disbursed)
                            <button wire:click="toggleDisbursed({{ $dist->id }})"
                                class="col-span-2 px-3 py-2.5 rounded-xl text-xs font-bold bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300 hover:bg-rose-100 hover:text-rose-600 transition-all flex items-center justify-center gap-1 min-h-[44px]">
                                Batalkan Status Pencairan
                            </button>
                        @else
                            <button wire:click="disburseSingle({{ $dist->id }})"
                                class="px-3 py-2.5 rounded-xl text-xs font-bold bg-emerald-600 text-white shadow-sm flex items-center justify-center gap-1 min-h-[44px]">
                                <i class='bx bx-check text-base'></i> Tunai
                            </button>

                            <button wire:click="disburseToSukarela({{ $dist->id }})"
                                class="px-3 py-2.5 rounded-xl text-xs font-bold bg-indigo-600 text-white shadow-sm flex items-center justify-center gap-1 min-h-[44px]">
                                <i class='bx bx-wallet text-base'></i> Ke Sukarela
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="py-10 text-center text-slate-400">
                    <i class='bx bx-folder-open text-4xl mb-2 block'></i>
                    Tidak ada data alokasi SHU ditemukan.
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
                        <th class="py-3 px-3">Nama Anggota</th>
                        <th class="py-3 px-3">Unit Kerja</th>
                        @if($viewMode === 'DETAILED')
                            <th class="py-3 px-3 text-right">Simpanan Snapshot</th>
                            <th class="py-3 px-3 text-right">Jasa Simpanan</th>
                            <th class="py-3 px-3 text-right">Jasa Usaha</th>
                            <th class="py-3 px-3 text-center">Porsi</th>
                        @endif
                        <th class="py-3 px-3 text-right">Nominal SHU (Rp)</th>
                        <th class="py-3 px-3 text-center no-print">Status</th>
                        <th class="py-3 px-3 text-center no-print">Aksi & Kwitansi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse($distributions as $index => $dist)
                        @php $member = $dist->member; @endphp
                        <tr wire:key="desk-disb-{{ $dist->id }}" class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-all">
                            <td class="py-2.5 px-3 font-mono text-slate-400">
                                {{ method_exists($distributions, 'firstItem') ? $distributions->firstItem() + $index : $index + 1 }}
                            </td>
                            <td class="py-2.5 px-3 font-mono font-bold text-slate-700 dark:text-slate-300">
                                {{ $member?->nomorAnggota ?? '-' }}
                            </td>
                            <td class="py-2.5 px-3 font-bold text-slate-800 dark:text-white">
                                <div class="flex items-center gap-2">
                                    <span>{{ $member?->name ?? '-' }}</span>
                                    @if($dist->notes)
                                        <span class="px-1.5 py-0.5 rounded text-[9px] font-semibold bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300" title="{{ $dist->notes }}">
                                            Susulan
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-2.5 px-3 text-slate-500">{{ $member?->unitKerja ?? '-' }}</td>

                            @if($viewMode === 'DETAILED')
                                <td class="py-2.5 px-3 text-right font-mono text-slate-600 dark:text-slate-300">
                                    Rp {{ number_format((float) $dist->total_simpanan_amount, 0, ',', '.') }}
                                </td>
                                <td class="py-2.5 px-3 text-right font-mono text-slate-600 dark:text-slate-300">
                                    Rp {{ number_format((float) $dist->jasa_simpanan_amount, 0, ',', '.') }}
                                </td>
                                <td class="py-2.5 px-3 text-right font-mono text-slate-600 dark:text-slate-300">
                                    Rp {{ number_format((float) $dist->jasa_usaha_amount, 0, ',', '.') }}
                                </td>
                                <td class="py-2.5 px-3 text-center font-mono text-indigo-600 dark:text-indigo-400 font-semibold">
                                    {{ number_format((float) $dist->portion_percentage, 2, ',', '.') }}%
                                </td>
                            @endif

                            <td class="py-2.5 px-3 text-right font-mono font-extrabold text-emerald-600 dark:text-emerald-400 text-sm">
                                Rp {{ number_format((float) $dist->shu_amount, 0, ',', '.') }}
                            </td>
                            <td class="py-2.5 px-3 text-center no-print">
                                @if($dist->is_disbursed)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">
                                        <i class='bx bx-check mr-1'></i> Dicairkan
                                    </span>
                                    @if($dist->disbursed_at)
                                        <p class="text-[9px] text-slate-400 mt-0.5">{{ $dist->disbursed_at->format('d/m/Y H:i') }}</p>
                                    @endif
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">
                                        <i class='bx bx-time-five mr-1'></i> Belum
                                    </span>
                                @endif
                            </td>
                            <td class="py-2.5 px-3 text-center no-print">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button wire:click="openDetailModal({{ $dist->id }})"
                                        title="Lihat Rincian Perhitungan SHU Anggota"
                                        class="px-2 py-1.5 rounded-lg text-[10px] font-bold bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-300 transition-all flex items-center gap-1">
                                        <i class='bx bx-search text-xs'></i> Detail
                                    </button>

                                    <button wire:click="openReceiptModal({{ $dist->id }})"
                                        title="Cetak Kwitansi / Slip Tanda Terima SHU"
                                        class="px-2 py-1.5 rounded-lg text-[10px] font-bold bg-indigo-50 text-indigo-600 hover:bg-indigo-100 dark:bg-indigo-950/40 dark:text-indigo-400 transition-all flex items-center gap-1">
                                        <i class='bx bx-receipt text-xs'></i> Slip SHU
                                    </button>

                                    @if($dist->is_disbursed)
                                        <button wire:click="toggleDisbursed({{ $dist->id }})"
                                            class="px-2 py-1.5 rounded-lg text-[10px] font-bold bg-slate-100 text-slate-600 hover:bg-rose-100 hover:text-rose-600 dark:bg-slate-800 dark:hover:bg-rose-900/30 transition-all">
                                            Batalkan
                                        </button>
                                    @else
                                        <button wire:click="disburseSingle({{ $dist->id }})"
                                            title="Cairkan Tunai / Transfer Bank (Catat Pengeluaran)"
                                            class="px-2 py-1.5 rounded-lg text-[10px] font-bold bg-emerald-600 text-white hover:bg-emerald-700 shadow-sm transition-all flex items-center gap-1">
                                            <i class='bx bx-check'></i> Tunai
                                        </button>
                                        <button wire:click="disburseToSukarela({{ $dist->id }})"
                                            title="Masukan Nominal SHU ke Dompet Simpanan Sukarela Anggota"
                                            class="px-2 py-1.5 rounded-lg text-[10px] font-bold bg-indigo-600 text-white hover:bg-indigo-700 shadow-sm transition-all flex items-center gap-1">
                                            <i class='bx bx-wallet'></i> Ke Sukarela
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $viewMode === 'DETAILED' ? 11 : 7 }}" class="py-12 text-center text-slate-400">
                                <i class='bx bx-folder-open text-4xl mb-2 block'></i>
                                Tidak ada data alokasi SHU ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($distributions, 'links'))
            <div class="mt-4 no-print">
                {{ $distributions->links() }}
            </div>
        @endif
    </div>

    {{-- MODAL 1: Detail Breakdown SHU Anggota (Mobile Bottom Sheet / Desktop Centered) --}}
    @if($showDetailModal && $selectedDistribution)
        <div class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-end sm:items-center justify-center p-0 sm:p-4 animate-fade-in no-print"
            wire:keydown.escape="closeDetailModal">
            <div class="bg-white dark:bg-darkCard w-full sm:max-w-xl rounded-t-3xl sm:rounded-2xl p-5 sm:p-6 shadow-2xl border border-slate-100 dark:border-slate-700 space-y-4 max-h-[90vh] overflow-y-auto">
                {{-- Header --}}
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-700 pb-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-950/50 flex items-center justify-center text-indigo-600 dark:text-indigo-400 shrink-0">
                            <i class='bx bx-user-check text-xl'></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-sm sm:text-base text-slate-800 dark:text-white">Detail Hak SHU Anggota</h3>
                            <p class="text-xs text-slate-400">RAT Tahun Buku {{ $selectedDistribution->ratSession?->year }}</p>
                        </div>
                    </div>
                    <button wire:click="closeDetailModal" class="text-slate-400 hover:text-slate-600 dark:hover:text-white p-1">
                        <i class='bx bx-x text-2xl'></i>
                    </button>
                </div>

                {{-- Member Meta Info --}}
                <div class="bg-slate-50 dark:bg-slate-800/60 p-3.5 sm:p-4 rounded-xl space-y-2">
                    <div class="flex justify-between items-center text-xs">
                        <span class="text-slate-400 font-medium">Nama Anggota:</span>
                        <span class="font-bold text-slate-800 dark:text-white text-sm">{{ $selectedDistribution->member?->name }}</span>
                    </div>
                    <div class="flex justify-between items-center text-xs">
                        <span class="text-slate-400 font-medium">Nomor Anggota (NIK):</span>
                        <span class="font-mono font-bold text-indigo-600 dark:text-indigo-400">{{ $selectedDistribution->member?->nomorAnggota }}</span>
                    </div>
                    <div class="flex justify-between items-center text-xs">
                        <span class="text-slate-400 font-medium">Unit Kerja / Institusi:</span>
                        <span class="font-semibold text-slate-700 dark:text-slate-300">{{ $selectedDistribution->member?->unitKerja ?? '-' }}</span>
                    </div>
                </div>

                {{-- Calculation Breakdown Cards --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="p-3 bg-indigo-50/50 dark:bg-indigo-950/30 rounded-xl border border-indigo-100 dark:border-indigo-900/30">
                        <div class="flex justify-between items-center">
                            <span class="text-[10px] font-bold text-indigo-500 uppercase tracking-wider block">Simpanan Cutoff RAT {{ $selectedDistribution->ratSession?->year }}</span>
                            <span class="text-[9px] px-1.5 py-0.5 rounded bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300 font-bold">Per 31 Des {{ $selectedDistribution->ratSession?->year }}</span>
                        </div>
                        <h4 class="text-sm font-extrabold text-indigo-700 dark:text-indigo-300 font-mono mt-1">
                            Rp {{ number_format((float)$selectedDistribution->total_simpanan_amount, 0, ',', '.') }}
                        </h4>
                        <div class="text-[9px] text-indigo-400 mt-1">
                            Pokok: Rp {{ number_format((float)$selectedDistribution->simpanan_pokok_snapshot, 0, ',', '.') }} | 
                            Wajib: Rp {{ number_format((float)$selectedDistribution->simpanan_wajib_snapshot, 0, ',', '.') }}
                        </div>
                        @if($selectedDistribution->member)
                            @php $currentTotal = $selectedDistribution->member->simpananPokok + $selectedDistribution->member->simpananWajib; @endphp
                            @if(abs($currentTotal - $selectedDistribution->total_simpanan_amount) > 1)
                                <div class="mt-2 pt-2 border-t border-indigo-100 dark:border-indigo-900/40 text-[10px] text-slate-600 dark:text-slate-300">
                                    ℹ️ <strong>Saldo Terkini di Menu Member:</strong> Rp {{ number_format($currentTotal, 0, ',', '.') }}
                                    <br/><span class="text-[9px] text-slate-400">(Selisih Rp {{ number_format($currentTotal - $selectedDistribution->total_simpanan_amount, 0, ',', '.') }} dari setoran simpanan wajib tahun berjalan yang akan dihitung pada RAT berikutnya).</span>
                                </div>
                            @endif
                        @endif
                    </div>

                    <div class="p-3 bg-emerald-50/50 dark:bg-emerald-950/30 rounded-xl border border-emerald-100 dark:border-emerald-900/30">
                        <span class="text-[10px] font-bold text-emerald-500 uppercase tracking-wider block">Porsi & Jasa Simpanan</span>
                        <h4 class="text-sm font-extrabold text-emerald-700 dark:text-emerald-300 font-mono mt-1">
                            Rp {{ number_format((float)$selectedDistribution->jasa_simpanan_amount, 0, ',', '.') }}
                        </h4>
                        <div class="text-[9px] text-emerald-500 mt-1">
                            Persentase Porsi: {{ number_format((float)$selectedDistribution->portion_percentage, 4, ',', '.') }}%
                        </div>
                    </div>

                    <div class="p-3 bg-amber-50/50 dark:bg-amber-950/30 rounded-xl border border-amber-100 dark:border-amber-900/30">
                        <span class="text-[10px] font-bold text-amber-500 uppercase tracking-wider block">Jasa Usaha / Transaksi</span>
                        <h4 class="text-sm font-extrabold text-amber-700 dark:text-amber-300 font-mono mt-1">
                            Rp {{ number_format((float)$selectedDistribution->jasa_usaha_amount, 0, ',', '.') }}
                        </h4>
                        <div class="text-[9px] text-amber-500 mt-1">
                            Tx Snapshot: Rp {{ number_format((float)$selectedDistribution->total_transaksi_amount, 0, ',', '.') }}
                        </div>
                    </div>

                    <div class="p-3 bg-emerald-600 text-white rounded-xl shadow-md">
                        <span class="text-[10px] font-bold text-emerald-200 uppercase tracking-wider block">Total SHU Diterima</span>
                        <h4 class="text-base font-extrabold font-mono mt-1">
                            Rp {{ number_format((float)$selectedDistribution->shu_amount, 0, ',', '.') }}
                        </h4>
                        <div class="text-[9px] text-emerald-100 mt-1">
                            Status: {{ $selectedDistribution->is_disbursed ? 'Sudah Dicairkan' : 'Belum Dicairkan' }}
                        </div>
                    </div>
                </div>

                {{-- TOTAL PENCAIRAN (Simpanan Terkini + SHU) --}}
                @if($selectedDistribution->member)
                    @php
                        $detailMember = $selectedDistribution->member;
                        $detailSimpananPokok = (float) $detailMember->simpananPokok;
                        $detailSimpananWajib = (float) $detailMember->simpananWajib;
                        $detailSimpananSukarela = (float) $detailMember->simpananSukarela;
                        $detailTotalSimpanan = $detailSimpananPokok + $detailSimpananWajib;
                        $detailShuAmount = (float) $selectedDistribution->shu_amount;
                        $detailTotalPencairan = $detailTotalSimpanan + $detailShuAmount;
                    @endphp
                    <div class="bg-gradient-to-br from-amber-50 via-yellow-50 to-orange-50 dark:from-amber-950/40 dark:via-yellow-950/30 dark:to-orange-950/20 p-4 rounded-xl border-2 border-amber-300 dark:border-amber-700 shadow-sm space-y-3">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-amber-200 dark:bg-amber-800/60 flex items-center justify-center">
                                <i class='bx bx-calculator text-amber-700 dark:text-amber-300 text-lg'></i>
                            </div>
                            <div>
                                <h4 class="text-xs font-extrabold text-amber-800 dark:text-amber-200 uppercase tracking-wider">Total Pencairan Anggota Keluar</h4>
                                <p class="text-[9px] text-amber-500 dark:text-amber-400">Simpanan Pokok + Wajib + SHU yang harus dibayarkan</p>
                            </div>
                        </div>

                        <div class="bg-white/70 dark:bg-slate-800/60 rounded-lg p-3 space-y-1.5 text-xs">
                            <div class="flex justify-between items-center">
                                <span class="text-slate-500">Simpanan Pokok</span>
                                <span class="font-mono font-bold text-slate-700 dark:text-slate-300">Rp {{ number_format($detailSimpananPokok, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-slate-500">Simpanan Wajib</span>
                                <span class="font-mono font-bold text-slate-700 dark:text-slate-300">Rp {{ number_format($detailSimpananWajib, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between items-center border-t border-slate-200 dark:border-slate-700 pt-1.5">
                                <span class="text-slate-500">Subtotal Simpanan</span>
                                <span class="font-mono font-bold text-slate-800 dark:text-white">Rp {{ number_format($detailTotalSimpanan, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-emerald-600 dark:text-emerald-400 font-medium">+ SHU RAT {{ $selectedDistribution->ratSession?->year }}</span>
                                <span class="font-mono font-bold text-emerald-600 dark:text-emerald-400">Rp {{ number_format($detailShuAmount, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <div class="bg-amber-600 dark:bg-amber-700 text-white rounded-lg p-3 flex items-center justify-between">
                            <div>
                                <span class="text-[9px] font-bold text-amber-200 uppercase tracking-wider block">TOTAL HARUS DIBAYAR</span>
                                <span class="text-[9px] text-amber-200">Simpanan + SHU</span>
                            </div>
                            <h3 class="text-xl font-extrabold font-mono">
                                Rp {{ number_format($detailTotalPencairan, 0, ',', '.') }}
                            </h3>
                        </div>

                        @if($detailSimpananSukarela > 0)
                            <div class="text-[10px] text-amber-600 dark:text-amber-400 flex items-center gap-1">
                                <i class='bx bx-info-circle'></i>
                                <span>Catatan: Anggota juga memiliki Simpanan Sukarela sebesar <strong>Rp {{ number_format($detailSimpananSukarela, 0, ',', '.') }}</strong> yang juga harus dikembalikan.</span>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- Action Footer --}}
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-2 pt-3 border-t border-slate-100 dark:border-slate-700">
                    <button wire:click="openReceiptModal({{ $selectedDistribution->id }})"
                        class="px-4 py-2.5 text-xs font-bold text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-950/40 rounded-xl hover:bg-indigo-100 transition-all flex items-center justify-center gap-1.5 min-h-[44px]">
                        <i class='bx bx-receipt text-base'></i> Cetak Slip SHU
                    </button>
                    <div class="flex items-center gap-2">
                        <button wire:click="closeDetailModal" class="w-full sm:w-auto px-4 py-2.5 text-xs font-bold text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl min-h-[44px]">Tutup</button>
                        @if(!$selectedDistribution->is_disbursed)
                            <button wire:click="disburseSingle({{ $selectedDistribution->id }}); closeDetailModal();"
                                class="w-full sm:w-auto px-4 py-2.5 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-sm min-h-[44px]">
                                Cairkan Sekarang
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- MODAL 2: Slip Kwitansi SHU Printable (Mobile Bottom Sheet / Desktop Centered) --}}
    @if($showReceiptModal && $selectedReceipt)
        <div class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-end sm:items-center justify-center p-0 sm:p-4 animate-fade-in no-print"
            wire:keydown.escape="closeReceiptModal">
            <div class="bg-white dark:bg-darkCard w-full sm:max-w-lg rounded-t-3xl sm:rounded-2xl p-5 sm:p-6 shadow-2xl border border-slate-100 dark:border-slate-700 space-y-4 max-h-[92vh] overflow-y-auto">
                {{-- Header --}}
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-700 pb-3 no-print">
                    <h3 class="font-bold text-sm text-slate-800 dark:text-white flex items-center gap-2">
                        <i class='bx bx-receipt text-indigo-600 text-lg'></i> Preview Slip Kwitansi SHU
                    </h3>
                    <div class="flex items-center gap-2">
                        <button onclick="window.print()" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold px-3 py-2 rounded-xl shadow-sm flex items-center gap-1 min-h-[38px]">
                            <i class='bx bx-printer text-base'></i> Cetak Slip
                        </button>
                        <button wire:click="closeReceiptModal" class="text-slate-400 hover:text-slate-600 dark:hover:text-white p-1">
                            <i class='bx bx-x text-2xl'></i>
                        </button>
                    </div>
                </div>

                {{-- Printable Receipt Content --}}
                <div id="receiptPrintableSection" class="bg-white text-slate-900 p-5 sm:p-6 rounded-xl border border-slate-200 font-sans space-y-4 text-xs">
                    {{-- Kop --}}
                    <div class="text-center border-b pb-3 border-slate-300">
                        <h2 class="text-sm font-extrabold uppercase tracking-wider text-slate-900">{{ config('cooperative.legal_name') }}</h2>
                        <p class="text-[10px] text-slate-600">{{ config('cooperative.parent_org') }}</p>
                        <p class="text-[9px] text-slate-500 font-mono mt-0.5">SLIP PEMBERITAHUAN & KWITANSI PENCAIRAN SHU RAT {{ $selectedReceipt->ratSession?->year }}</p>
                    </div>

                    {{-- Member Metadata --}}
                    <div class="grid grid-cols-2 gap-2 text-[11px] bg-slate-50 p-3 rounded-lg border border-slate-200">
                        <div>
                            <span class="text-slate-500 block text-[9px]">Nomor Anggota</span>
                            <span class="font-mono font-bold text-slate-900">{{ $selectedReceipt->member?->nomorAnggota }}</span>
                        </div>
                        <div>
                            <span class="text-slate-500 block text-[9px]">Nama Anggota</span>
                            <span class="font-bold text-slate-900">{{ $selectedReceipt->member?->name }}</span>
                        </div>
                        <div class="col-span-2">
                            <span class="text-slate-500 block text-[9px]">Unit Kerja / Institusi</span>
                            <span class="font-medium text-slate-800">{{ $selectedReceipt->member?->unitKerja ?? '-' }}</span>
                        </div>
                    </div>

                    {{-- Breakdown Table --}}
                    <table class="w-full border-collapse border border-slate-300 text-[10px]">
                        <thead>
                            <tr class="bg-slate-100 font-bold uppercase text-slate-700">
                                <th class="border border-slate-300 p-2 text-left">Rincian</th>
                                <th class="border border-slate-300 p-2 text-right">Jumlah (Rp)</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- SHU Section --}}
                            <tr class="bg-slate-50">
                                <td class="border border-slate-300 p-2 font-bold text-slate-700" colspan="2">A. RINCIAN SHU</td>
                            </tr>
                            <tr>
                                <td class="border border-slate-300 p-2 pl-4">Jasa Simpanan (Alokasi Modal)</td>
                                <td class="border border-slate-300 p-2 text-right font-mono font-semibold">
                                    Rp {{ number_format((float)$selectedReceipt->jasa_simpanan_amount, 0, ',', '.') }}
                                </td>
                            </tr>
                            <tr>
                                <td class="border border-slate-300 p-2 pl-4">Jasa Usaha / Transaksi (Alokasi Partisipasi)</td>
                                <td class="border border-slate-300 p-2 text-right font-mono font-semibold">
                                    Rp {{ number_format((float)$selectedReceipt->jasa_usaha_amount, 0, ',', '.') }}
                                </td>
                            </tr>
                            <tr class="bg-emerald-50 font-bold">
                                <td class="border border-slate-300 p-2 pl-4 uppercase text-emerald-800">Total Hak SHU</td>
                                <td class="border border-slate-300 p-2 text-right font-mono text-sm text-emerald-700">
                                    Rp {{ number_format((float)$selectedReceipt->shu_amount, 0, ',', '.') }}
                                </td>
                            </tr>

                            {{-- Simpanan Section --}}
                            @if($selectedReceipt->member)
                                @php
                                    $rcptPokok = (float) $selectedReceipt->member->simpananPokok;
                                    $rcptWajib = (float) $selectedReceipt->member->simpananWajib;
                                    $rcptSukarela = (float) $selectedReceipt->member->simpananSukarela;
                                    $rcptTotalSimpanan = $rcptPokok + $rcptWajib;
                                    $rcptTotalPencairan = $rcptTotalSimpanan + (float)$selectedReceipt->shu_amount;
                                @endphp
                                <tr class="bg-slate-50">
                                    <td class="border border-slate-300 p-2 font-bold text-slate-700" colspan="2">B. SIMPANAN ANGGOTA (Saldo Terkini)</td>
                                </tr>
                                <tr>
                                    <td class="border border-slate-300 p-2 pl-4">Simpanan Pokok</td>
                                    <td class="border border-slate-300 p-2 text-right font-mono font-semibold">
                                        Rp {{ number_format($rcptPokok, 0, ',', '.') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="border border-slate-300 p-2 pl-4">Simpanan Wajib</td>
                                    <td class="border border-slate-300 p-2 text-right font-mono font-semibold">
                                        Rp {{ number_format($rcptWajib, 0, ',', '.') }}
                                    </td>
                                </tr>
                                <tr class="bg-indigo-50 font-bold">
                                    <td class="border border-slate-300 p-2 pl-4 uppercase text-indigo-800">Total Simpanan (Pokok + Wajib)</td>
                                    <td class="border border-slate-300 p-2 text-right font-mono text-sm text-indigo-700">
                                        Rp {{ number_format($rcptTotalSimpanan, 0, ',', '.') }}
                                    </td>
                                </tr>

                                {{-- Grand Total --}}
                                <tr class="bg-amber-100 font-bold border-t-2 border-amber-400">
                                    <td class="border border-amber-300 p-2.5 uppercase text-amber-900 text-[11px]">TOTAL PENCAIRAN (SHU + SIMPANAN)</td>
                                    <td class="border border-amber-300 p-2.5 text-right font-mono text-base text-amber-900">
                                        Rp {{ number_format($rcptTotalPencairan, 0, ',', '.') }}
                                    </td>
                                </tr>
                                @if($rcptSukarela > 0)
                                    <tr class="bg-blue-50">
                                        <td class="border border-slate-300 p-2 pl-4 text-blue-700 italic" colspan="2">
                                            * Simpanan Sukarela: Rp {{ number_format($rcptSukarela, 0, ',', '.') }} (dikembalikan terpisah)
                                        </td>
                                    </tr>
                                @endif
                            @endif
                        </tbody>
                    </table>

                    {{-- Signatures --}}
                    <div class="grid grid-cols-2 gap-4 pt-4 text-[10px] text-center">
                        <div>
                            <p class="text-slate-500">Penerima (Anggota),</p>
                            <div class="h-12"></div>
                            <p class="font-bold border-t border-slate-300 pt-1 text-slate-800">({{ $selectedReceipt->member?->name }})</p>
                        </div>
                        <div>
                            <p class="text-slate-500">Kasir / Bendahara Koperasi,</p>
                            <div class="h-12"></div>
                            <p class="font-bold border-t border-slate-300 pt-1 text-slate-800">({{ coop_setting('bendahara_name') }})</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Actions Footer --}}
    <div class="bg-white dark:bg-darkCard p-4 sm:p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 no-print">
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
            <button wire:click="goBack"
                class="w-full sm:w-auto bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 transition-all flex items-center justify-center gap-2 min-h-[44px]">
                <i class='bx bx-left-arrow-alt text-base'></i> Kembali ke Alokasi SHU
            </button>

            @if($stats['pending'] === 0 && $stats['total'] > 0)
                <button wire:click="completeSession"
                    onclick="return confirm('Semua SHU telah dicairkan. Tandai proses RAT sebagai selesai?')"
                    class="w-full sm:w-auto bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-5 py-2.5 rounded-xl shadow-lg shadow-emerald-600/20 transition-all flex items-center justify-center gap-2 min-h-[44px]">
                    <i class='bx bx-check-double text-base'></i> Selesaikan Proses RAT
                </button>
            @endif
        </div>
    </div>

    {{-- MODAL 3: Form Berita Acara RAT (Mobile Bottom Sheet / Desktop Centered) --}}
    @if($ratSession)
        <div x-show="showBeritaAcaraModal" x-cloak
            class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-md flex items-end sm:items-center justify-center p-0 sm:p-4 no-print"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-full sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-full sm:translate-y-0 sm:scale-95">

            <div class="bg-white dark:bg-darkCard w-full sm:max-w-3xl rounded-t-3xl sm:rounded-2xl shadow-2xl border border-slate-100 dark:border-slate-700 overflow-hidden flex flex-col max-h-[92vh]"
                @click.away="showBeritaAcaraModal = false">
                
                {{-- Modal Header --}}
                <div class="px-5 py-3.5 sm:px-6 sm:py-4 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white flex items-center justify-between shadow-sm shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-white/10 backdrop-blur flex items-center justify-center text-white shrink-0">
                            <i class='bx bxs-file-doc text-lg sm:text-xl'></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-xs sm:text-sm">Cetak Berita Acara RAT {{ $ratSession->year }}</h3>
                            <p class="text-[10px] sm:text-[11px] text-indigo-200">Format Resmi {{ config('cooperative.short_name') }}</p>
                        </div>
                    </div>
                    <button @click="showBeritaAcaraModal = false" class="text-white/80 hover:text-white transition-all p-1">
                        <i class='bx bx-x text-2xl'></i>
                    </button>
                </div>

                {{-- Tab Navigation --}}
                <div class="flex overflow-x-auto no-scrollbar border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/30 px-4 sm:px-6 pt-3 gap-2 shrink-0">
                    <button @click="activeTab = 'acara'"
                        :class="activeTab === 'acara' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400 bg-white dark:bg-darkCard' : 'border-transparent text-slate-500 hover:text-slate-700'"
                        class="px-3 sm:px-4 py-2 text-xs font-bold border-b-2 rounded-t-xl transition-all flex items-center gap-1.5 shrink-0">
                        <i class='bx bx-calendar-event text-sm'></i> 1. Acara & Kuorum
                    </button>
                    <button @click="activeTab = 'pengurus'"
                        :class="activeTab === 'pengurus' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400 bg-white dark:bg-darkCard' : 'border-transparent text-slate-500 hover:text-slate-700'"
                        class="px-3 sm:px-4 py-2 text-xs font-bold border-b-2 rounded-t-xl transition-all flex items-center gap-1.5 shrink-0">
                        <i class='bx bx-pen text-sm'></i> 2. Pimpinan
                    </button>
                    <button @click="activeTab = 'rekomendasi'"
                        :class="activeTab === 'rekomendasi' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400 bg-white dark:bg-darkCard' : 'border-transparent text-slate-500 hover:text-slate-700'"
                        class="px-3 sm:px-4 py-2 text-xs font-bold border-b-2 rounded-t-xl transition-all flex items-center gap-1.5 shrink-0">
                        <i class='bx bx-notepad text-sm'></i> 3. Rekomendasi
                    </button>
                    <button @click="activeTab = 'summary'"
                        :class="activeTab === 'summary' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400 bg-white dark:bg-darkCard' : 'border-transparent text-slate-500 hover:text-slate-700'"
                        class="px-3 sm:px-4 py-2 text-xs font-bold border-b-2 rounded-t-xl transition-all flex items-center gap-1.5 shrink-0">
                        <i class='bx bx-pie-chart-alt-2 text-sm'></i> 4. Preview SHU
                    </button>
                </div>

                {{-- Form Body --}}
                <form action="{{ route('admin.rat.pdf-berita-acara', $ratSession->id) }}" method="POST" target="_blank" class="flex-1 overflow-y-auto p-4 sm:p-6 space-y-4">
                    @csrf

                    {{-- TAB 1: Acara & Kuorum --}}
                    <div x-show="activeTab === 'acara'" class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1 flex items-center gap-1">
                                    <i class='bx bx-hash text-indigo-500'></i> Nomor Berita Acara
                                </label>
                                <input type="text" name="nomor_surat" value="001/BA-RAT/BERMADANI/{{ $ratSession->year }}" required
                                    class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-800 dark:text-white outline-none focus:border-indigo-500 min-h-[40px]">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1 flex items-center gap-1">
                                    <i class='bx bx-calendar text-indigo-500'></i> Hari & Tanggal Pelaksanaan
                                </label>
                                <input type="text" name="hari_tanggal" value="{{ $ratSession->event_date ? $ratSession->event_date->translatedFormat('l, d F Y') : date('d F Y') }}" required
                                    class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-800 dark:text-white outline-none focus:border-indigo-500 min-h-[40px]">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1 flex items-center gap-1">
                                    <i class='bx bx-time text-indigo-500'></i> Waktu Pelaksanaan
                                </label>
                                <input type="text" name="jam" value="09:00 - 12:00 WIB" required
                                    class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-800 dark:text-white outline-none focus:border-indigo-500 min-h-[40px]">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1 flex items-center gap-1">
                                    <i class='bx bx-map-pin text-indigo-500'></i> Tempat Pelaksanaan
                                </label>
                                <input type="text" name="tempat" value="{{ coop_setting('rat_default_venue') }}" required
                                    class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-800 dark:text-white outline-none focus:border-indigo-500 min-h-[40px]">
                            </div>
                        </div>

                        <div class="pt-2">
                            <h4 class="text-xs font-bold text-slate-800 dark:text-white flex items-center gap-1 mb-2">
                                <i class='bx bx-group text-indigo-500'></i> Data Kehadiran / Kuorum Acara
                            </h4>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 bg-slate-50 dark:bg-slate-800/40 p-4 rounded-xl border border-slate-100 dark:border-slate-700/60">
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-500 mb-1">Total Anggota</label>
                                    <input type="number" name="total_anggota" value="{{ $stats['total'] }}" required
                                        class="w-full text-xs bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg px-3 py-1.5 text-slate-800 dark:text-white font-bold">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-500 mb-1">Anggota Hadir</label>
                                    <input type="number" name="anggota_hadir" value="{{ $stats['total'] }}" required
                                        class="w-full text-xs bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg px-3 py-1.5 text-slate-800 dark:text-white font-bold text-emerald-600">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-500 mb-1">Pengurus Hadir</label>
                                    <input type="number" name="pengurus_hadir" value="3" required
                                        class="w-full text-xs bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg px-3 py-1.5 text-slate-800 dark:text-white font-bold">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-500 mb-1">Pengawas Hadir</label>
                                    <input type="number" name="pengawas_hadir" value="1" required
                                        class="w-full text-xs bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg px-3 py-1.5 text-slate-800 dark:text-white font-bold">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- TAB 2: Pimpinan & Pengurus --}}
                    <div x-show="activeTab === 'pengurus'" class="space-y-4">
                        <div class="flex items-center justify-between bg-indigo-50 dark:bg-indigo-950/40 p-3 rounded-xl border border-indigo-100 dark:border-indigo-900/40">
                            <span class="text-xs text-indigo-700 dark:text-indigo-300 font-medium flex items-center gap-1">
                                <i class='bx bx-info-circle text-base'></i> Nama-nama ini akan tercetak di lembar tanda tangan pengesahan PDF.
                            </span>
                        </div>

                        <div class="space-y-3">
                            <h4 class="text-xs font-bold text-slate-800 dark:text-white flex items-center gap-1">
                                <i class='bx bx-user-voice text-indigo-500'></i> Pimpinan Sidang RAT
                            </h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-500 mb-1">Ketua Sidang RAT</label>
                                    <input type="text" name="ketua_sidang" placeholder="Contoh: Dr. H. Ahmad Fathoni, M.Ag"
                                        class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-800 dark:text-white outline-none">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-500 mb-1">Sekretaris Sidang RAT</label>
                                    <input type="text" name="sekretaris_sidang" placeholder="Contoh: Siti Rahmawati, S.E"
                                        class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-800 dark:text-white outline-none">
                                </div>
                            </div>
                        </div>

                        <div class="space-y-3 pt-2">
                            <h4 class="text-xs font-bold text-slate-800 dark:text-white flex items-center gap-1">
                                <i class='bx bx-award text-indigo-500'></i> Pengurus & Pengawas Koperasi
                            </h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-500 mb-1">Ketua Koperasi</label>
                                    <input type="text" name="ketua_koperasi" placeholder="Nama Ketua Koperasi..."
                                        class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-800 dark:text-white outline-none">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-500 mb-1">Sekretaris Koperasi</label>
                                    <input type="text" name="sekretaris_koperasi" placeholder="Nama Sekretaris..."
                                        class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-800 dark:text-white outline-none">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-500 mb-1">Bendahara Koperasi</label>
                                    <input type="text" name="bendahara_koperasi" placeholder="Nama Bendahara..."
                                        class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-800 dark:text-white outline-none">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-500 mb-1">Ketua Pengawas Koperasi</label>
                                    <input type="text" name="ketua_pengawas" placeholder="Nama Ketua Pengawas..."
                                        class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-800 dark:text-white outline-none">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- TAB 3: Substansi & Rekomendasi --}}
                    <div x-show="activeTab === 'rekomendasi'" class="space-y-4">
                        <div class="flex items-center justify-between bg-indigo-50 dark:bg-indigo-950/40 p-3 rounded-xl border border-indigo-100 dark:border-indigo-900/40">
                            <span class="text-xs text-indigo-700 dark:text-indigo-300 font-medium flex items-center gap-1">
                                <i class='bx bx-info-circle text-base'></i> Poin rekomendasi & catatan masukan hasil RAT ini akan otomatis tercetak di Berita Acara PDF.
                            </span>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1 flex items-center gap-1">
                                <i class='bx bx-edit text-indigo-500'></i> Catatan Substansi & Rekomendasi Anggota (Bisa Diedit):
                            </label>
                            <textarea name="catatan_rekomendasi" rows="7" 
                                class="w-full text-xs font-sans bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-3 text-slate-800 dark:text-white outline-none focus:border-indigo-500">1. Persiapan & Sosialisasi Laporan RAT: Rapat Anggota Tahunan Tahun Buku {{ $ratSession->year }} merekomendasikan agar pelaksanaan RAT pada periode selanjutnya dipersiapkan secara lebih matang. Salah satu poin utamanya adalah seluruh data dan dokumen Laporan Pertanggungjawaban (LPJ) RAT disosialisasikan terlebih dahulu kepada anggota sekurang-kurangnya 1 (satu) minggu sebelum pelaksanaan RAT.
2. Detail & Transparansi Laporan Keuangan: Laporan Keuangan Koperasi disajikan secara lebih rinci, detail, dan transparan, khususnya data mengenai perincian saldo dan mutasi simpanan seluruh anggota.</textarea>
                            <p class="text-[10px] text-slate-400 mt-1">Kamu bisa menambah, mengedit, atau menghapus catatan poin di atas sebelum mengklik tombol Download PDF.</p>
                        </div>
                    </div>

                    {{-- TAB 4: Summary Financial --}}
                    <div x-show="activeTab === 'summary'" class="space-y-4">
                        <div class="bg-gradient-to-br from-slate-900 to-indigo-950 p-5 rounded-2xl text-white shadow-lg space-y-4">
                            <div class="flex items-center justify-between border-b border-white/10 pb-3">
                                <div>
                                    <p class="text-[10px] text-indigo-300 font-bold uppercase tracking-wider">Ringkasan Data Keuangan RAT</p>
                                    <h4 class="text-sm font-bold text-white">{{ $ratSession->title }}</h4>
                                </div>
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                    {{ $ratSession->status }}
                                </span>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-[10px] text-slate-400 font-medium">Total Laba Bersih (Net Profit)</p>
                                    <h3 class="text-base font-extrabold text-white">Rp {{ number_format((float)$ratSession->total_net_profit, 0, ',', '.') }}</h3>
                                </div>
                                <div>
                                    <p class="text-[10px] text-emerald-400 font-medium">Total SHU Dibagikan</p>
                                    <h3 class="text-base font-extrabold text-emerald-400">Rp {{ number_format((float)$ratSession->total_member_shu, 0, ',', '.') }}</h3>
                                </div>
                            </div>

                            <div class="border-t border-white/10 pt-3">
                                <p class="text-[10px] text-slate-400 font-medium mb-2">Rincian Pembagian 5 Pos SHU:</p>
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 text-xs">
                                    <div class="bg-white/5 p-2 rounded-lg">
                                        <span class="text-[9px] text-slate-400 block">Cadangan ({{ $ratSession->cadangan_percentage ?? 25 }}%)</span>
                                        <span class="font-bold text-white">Rp {{ number_format((float)$ratSession->total_member_shu * (($ratSession->cadangan_percentage ?? 25)/100), 0, ',', '.') }}</span>
                                    </div>
                                    <div class="bg-white/5 p-2 rounded-lg">
                                        <span class="text-[9px] text-slate-400 block">Jasa Simpanan ({{ $ratSession->jasa_simpanan_percentage ?? 30 }}%)</span>
                                        <span class="font-bold text-white">Rp {{ number_format((float)$ratSession->total_member_shu * (($ratSession->jasa_simpanan_percentage ?? 30)/100), 0, ',', '.') }}</span>
                                    </div>
                                    <div class="bg-white/5 p-2 rounded-lg">
                                        <span class="text-[9px] text-slate-400 block">Jasa Usaha ({{ $ratSession->jasa_usaha_percentage ?? 25 }}%)</span>
                                        <span class="font-bold text-white">Rp {{ number_format((float)$ratSession->total_member_shu * (($ratSession->jasa_usaha_percentage ?? 25)/100), 0, ',', '.') }}</span>
                                    </div>
                                    <div class="bg-white/5 p-2 rounded-lg">
                                        <span class="text-[9px] text-slate-400 block">Pengurus ({{ $ratSession->pengurus_percentage ?? 10 }}%)</span>
                                        <span class="font-bold text-white">Rp {{ number_format((float)$ratSession->total_member_shu * (($ratSession->pengurus_percentage ?? 10)/100), 0, ',', '.') }}</span>
                                    </div>
                                    <div class="bg-white/5 p-2 rounded-lg">
                                        <span class="text-[9px] text-slate-400 block">Dana Sosial ({{ $ratSession->dana_sosial_percentage ?? 10 }}%)</span>
                                        <span class="font-bold text-white">Rp {{ number_format((float)$ratSession->total_member_shu * (($ratSession->dana_sosial_percentage ?? 10)/100), 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Footer Action Buttons --}}
                    <div class="pt-3 sm:pt-4 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 border-t border-slate-100 dark:border-slate-700">
                        <div class="flex items-center justify-center sm:justify-start gap-1">
                            <button type="button" @click="activeTab = 'acara'" :class="activeTab === 'acara' ? 'text-indigo-600 font-bold' : 'text-slate-400'" class="text-xs p-1">1</button>
                            <span class="text-slate-300">•</span>
                            <button type="button" @click="activeTab = 'pengurus'" :class="activeTab === 'pengurus' ? 'text-indigo-600 font-bold' : 'text-slate-400'" class="text-xs p-1">2</button>
                            <span class="text-slate-300">•</span>
                            <button type="button" @click="activeTab = 'rekomendasi'" :class="activeTab === 'rekomendasi' ? 'text-indigo-600 font-bold' : 'text-slate-400'" class="text-xs p-1">3</button>
                        </div>

                        <div class="flex items-center gap-2">
                            <button type="button" @click="showBeritaAcaraModal = false"
                                class="w-full sm:w-auto px-4 py-2.5 rounded-xl text-xs font-bold bg-slate-100 text-slate-600 hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-300 transition-all min-h-[44px]">
                                Batal
                            </button>
                            <button type="submit" @click="showBeritaAcaraModal = false"
                                class="w-full sm:w-auto px-5 py-2.5 rounded-xl text-xs font-bold bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white shadow-lg shadow-indigo-600/25 flex items-center justify-center gap-2 transition-all min-h-[44px]">
                                <i class='bx bxs-file-pdf text-base'></i> Download PDF Berita Acara
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- MODAL 4: Form Tambah Susulan SHU (Mobile Bottom Sheet / Desktop Centered) --}}
    @if($showAddManualModal)
        <div class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-end sm:items-center justify-center p-0 sm:p-4 animate-fade-in no-print">
            <div class="bg-white dark:bg-darkCard w-full sm:max-w-lg rounded-t-3xl sm:rounded-2xl p-5 sm:p-6 shadow-2xl border border-slate-200 dark:border-slate-700 max-h-[92vh] overflow-y-auto space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-700 pb-3">
                    <h3 class="font-bold text-sm sm:text-base text-slate-900 dark:text-white flex items-center gap-2">
                        <i class='bx bx-user-plus text-amber-600 text-xl'></i>
                        Tambah Susulan SHU (Anggota Non-Aktif / Ex-Anggota)
                    </h3>
                    <button wire:click="closeAddManualModal" class="text-slate-400 hover:text-slate-600 dark:hover:text-white p-1">
                        <i class='bx bx-x text-2xl'></i>
                    </button>
                </div>

                <div class="space-y-4">
                    <div class="p-3 bg-amber-50 dark:bg-amber-950/40 rounded-xl border border-amber-100 dark:border-amber-800/40 text-xs text-slate-600 dark:text-slate-300">
                        ℹ️ <strong>Pengaman Data:</strong> Menambahkan susulan SHU ini <strong>TIDAK AKAN merubah atau menghitung ulang nominal SHU anggota lain</strong> yang sudah disahkan.
                    </div>

                    <div>
                        <label class="text-xs font-bold text-slate-700 dark:text-slate-300 block mb-1">Pilih Anggota / Ex-Anggota (Anggota Keluar)</label>
                        <select wire:model.live="selectedMemberId" class="w-full border rounded-xl p-2.5 text-xs font-bold dark:bg-slate-800 dark:border-slate-600 dark:text-white focus:ring-2 focus:ring-primary/20 outline-none min-h-[44px]">
                            <option value="">-- Pilih Anggota (Cari NIK / Nama) --</option>
                            @foreach($allMembers as $m)
                                <option value="{{ $m->id }}">
                                    {{ optional($m->user)->name ?? $m->name }} (NIK: {{ $m->nomorAnggota }} • Status: {{ $m->status }})
                                </option>
                            @endforeach
                        </select>
                        @error('selectedMemberId') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs font-bold text-slate-700 dark:text-slate-300 block mb-1">Jasa Simpanan (Rp)</label>
                            <input type="number" wire:model="manualJasaSimpanan" step="100"
                                class="w-full border rounded-xl p-2.5 text-xs font-bold dark:bg-slate-800 dark:border-slate-600 dark:text-white focus:ring-2 focus:ring-primary/20 outline-none min-h-[44px]"
                                placeholder="0">
                            @error('manualJasaSimpanan') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="text-xs font-bold text-slate-700 dark:text-slate-300 block mb-1">Jasa Usaha / Transaksi (Rp)</label>
                            <input type="number" wire:model="manualJasaUsaha" step="100"
                                class="w-full border rounded-xl p-2.5 text-xs font-bold dark:bg-slate-800 dark:border-slate-600 dark:text-white focus:ring-2 focus:ring-primary/20 outline-none min-h-[44px]"
                                placeholder="0">
                            @error('manualJasaUsaha') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="p-3 bg-slate-50 dark:bg-slate-800 rounded-xl flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-600 dark:text-slate-300">Total SHU Susulan:</span>
                        <span class="text-sm font-extrabold text-emerald-600 dark:text-emerald-400">
                            Rp {{ number_format((float)$manualJasaSimpanan + (float)$manualJasaUsaha, 0, ',', '.') }}
                        </span>
                    </div>

                    <div>
                        <label class="text-xs font-bold text-slate-700 dark:text-slate-300 block mb-1">Keterangan / Catatan Susulan</label>
                        <input type="text" wire:model="manualNotes"
                            class="w-full border rounded-xl p-2.5 text-xs dark:bg-slate-800 dark:border-slate-600 dark:text-white focus:ring-2 focus:ring-primary/20 outline-none min-h-[44px]"
                            placeholder="Misal: Susulan SHU 2025 untuk Deden Abdul Wahid (Resigned 2026)">
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-2 justify-end mt-5 pt-3 border-t border-slate-100 dark:border-slate-700">
                    <button wire:click="closeAddManualModal"
                        class="w-full sm:w-auto px-4 py-2.5 text-xs font-bold text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl min-h-[44px]">Batal</button>
                    <button wire:click="saveManualDistribution" wire:loading.attr="disabled"
                        class="w-full sm:w-auto px-5 py-2.5 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-sm flex items-center justify-center gap-1.5 min-h-[44px]">
                        <i class='bx bx-check-circle text-base' wire:loading.remove wire:target="saveManualDistribution"></i>
                        <i class='bx bx-loader-alt animate-spin text-base' wire:loading wire:target="saveManualDistribution"></i>
                        <span>Simpan Susulan SHU</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
