<div class="space-y-6" x-data="{ showBeritaAcaraModal: false, activeTab: 'acara' }">
    {{-- Print Style --}}
    <style>
        @media print {
            body * { visibility: hidden; }
            #printableSection, #printableSection * { visibility: visible; }
            #printableSection { position: absolute; left: 0; top: 0; width: 100%; }
            .no-print { display: none !important; }
        }
    </style>

    {{-- Wizard Steps --}}
    <div class="no-print">
        <x-rat-wizard-steps :currentStep="4" :sessionId="$ratSession?->id" :sessionStatus="$ratSession?->status ?? 'DRAFT'" />
    </div>

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white dark:bg-darkCard p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 no-print">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="p-2 rounded-lg bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400">
                    <i class='bx bx-wallet text-2xl'></i>
                </span>
                <h1 class="text-xl font-bold text-slate-800 dark:text-white">Pencairan SHU</h1>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400">
                Step 4: Catat pencairan SHU ke masing-masing anggota dan cetak tanda terima.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            @if($ratSession)
                <button @click="showBeritaAcaraModal = true"
                    class="bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-lg shadow-indigo-600/20 transition-all flex items-center gap-2">
                    <i class='bx bxs-file-doc text-lg'></i> Cetak Berita Acara RAT
                </button>
                <a href="{{ route('admin.rat.pdf-report', $ratSession->id) }}" target="_blank"
                    class="bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-md shadow-rose-600/20 transition-all flex items-center gap-2">
                    <i class='bx bxs-file-pdf text-lg'></i> Download PDF Laporan SHU
                </a>
            @endif
            <button onclick="window.print()"
                class="bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 transition-all flex items-center gap-2">
                <i class='bx bx-printer text-base'></i> Cetak Rekap
            </button>
        </div>
    </div>

    {{-- Flash Messages --}}
    <div class="no-print">
        @if (session()->has('success'))
            <div class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-400 px-4 py-3 rounded-xl flex items-center gap-2">
                <i class='bx bx-check-circle text-xl'></i>
                <span class="text-xs font-medium">{{ session('success') }}</span>
            </div>
        @endif
        @if (session()->has('error'))
            <div class="bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-400 px-4 py-3 rounded-xl flex items-center gap-2">
                <i class='bx bx-error-circle text-xl'></i>
                <span class="text-xs font-medium">{{ session('error') }}</span>
            </div>
        @endif
        @if (session()->has('info'))
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 text-blue-700 dark:text-blue-400 px-4 py-3 rounded-xl flex items-center gap-2">
                <i class='bx bx-info-circle text-xl'></i>
                <span class="text-xs font-medium">{{ session('info') }}</span>
            </div>
        @endif
    </div>

    {{-- Disbursement Progress --}}
    <div class="bg-white dark:bg-darkCard p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 no-print">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-4">
            <h3 class="text-sm font-bold text-slate-800 dark:text-white flex items-center gap-2">
                <i class='bx bx-trending-up text-base text-emerald-500'></i>
                Progress Pencairan
            </h3>
            @if($stats['pending'] > 0)
                <button wire:click="batchDisburse"
                    onclick="return confirm('Cairkan SHU untuk {{ $stats['pending'] }} anggota sekaligus?')"
                    class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-5 py-2.5 rounded-xl shadow-lg shadow-emerald-600/20 transition-all flex items-center gap-2">
                    <i class='bx bx-check-double text-base'></i> Cairkan Semua ({{ $stats['pending'] }} anggota)
                </button>
            @endif
        </div>

        {{-- Progress Bar --}}
        <div class="mb-4">
            <div class="flex items-center justify-between text-xs font-bold mb-2">
                <span class="text-emerald-600">{{ $stats['disbursed'] }} dicairkan</span>
                <span class="text-slate-400">{{ $stats['pending'] }} belum</span>
                <span class="text-slate-700 dark:text-white">{{ $stats['total'] }} total</span>
            </div>
            <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-3 overflow-hidden">
                <div class="bg-gradient-to-r from-emerald-500 to-emerald-400 h-full rounded-full transition-all duration-500"
                    style="width: {{ $stats['percentage'] }}%"></div>
            </div>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-slate-50 dark:bg-slate-800/50 p-4 rounded-xl flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-indigo-50 dark:bg-indigo-950/40 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                    <i class='bx bx-money text-lg'></i>
                </div>
                <div>
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Total SHU</p>
                    <h4 class="text-sm font-bold text-slate-800 dark:text-white">Rp {{ number_format($stats['totalAmount'], 0, ',', '.') }}</h4>
                </div>
            </div>
            <div class="bg-emerald-50/50 dark:bg-emerald-950/20 p-4 rounded-xl flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                    <i class='bx bx-check-circle text-lg'></i>
                </div>
                <div>
                    <p class="text-[9px] font-bold text-emerald-500 uppercase tracking-widest">Sudah Dicairkan</p>
                    <h4 class="text-sm font-bold text-emerald-600 dark:text-emerald-400">Rp {{ number_format($stats['disbursedAmount'], 0, ',', '.') }}</h4>
                </div>
            </div>
            <div class="bg-amber-50/50 dark:bg-amber-950/20 p-4 rounded-xl flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-amber-50 dark:bg-amber-950/40 flex items-center justify-center text-amber-500 dark:text-amber-400">
                    <i class='bx bx-time-five text-lg'></i>
                </div>
                <div>
                    <p class="text-[9px] font-bold text-amber-500 uppercase tracking-widest">Belum Dicairkan</p>
                    <h4 class="text-sm font-bold text-amber-600 dark:text-amber-400">Rp {{ number_format($stats['pendingAmount'], 0, ',', '.') }}</h4>
                </div>
            </div>
        </div>
    </div>

    {{-- Disbursement Table --}}
    <div id="printableSection" class="bg-white dark:bg-darkCard p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
        {{-- Print Header --}}
        <div class="mb-4 pb-3 border-b border-slate-200 dark:border-slate-700">
            <h2 class="text-base font-bold text-slate-800 dark:text-white">DAFTAR PENCAIRAN SHU ANGGOTA</h2>
            <p class="text-[10px] text-slate-500">{{ $ratSession?->title }} • {{ $ratSession?->event_date?->format('d F Y') }}</p>
        </div>

        {{-- Search & Filter --}}
        <div class="flex items-center gap-3 mb-4 no-print">
            <input type="text" wire:model.live.debounce.300ms="searchMember"
                placeholder="Cari Anggota..."
                class="bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs rounded-xl px-4 py-2 text-slate-800 dark:text-white outline-none w-52">
            <select wire:model.live="filterDisbursed"
                class="bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs rounded-xl px-3 py-2 text-slate-800 dark:text-white outline-none">
                <option value="ALL">Semua</option>
                <option value="PENDING">Belum Dicairkan</option>
                <option value="DISBURSED">Sudah Dicairkan</option>
            </select>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800 text-slate-500 dark:text-slate-400 uppercase text-[10px] tracking-wider border-b border-slate-200 dark:border-slate-700">
                        <th class="py-3 px-3">No</th>
                        <th class="py-3 px-3">No. Anggota</th>
                        <th class="py-3 px-3">Nama Anggota</th>
                        <th class="py-3 px-3">Unit Kerja</th>
                        <th class="py-3 px-3 text-right">Nominal SHU (Rp)</th>
                        <th class="py-3 px-3 text-center no-print">Status</th>
                        <th class="py-3 px-3 text-center no-print">Aksi</th>
                        <th class="py-3 px-3 text-center only-print hidden">Tanda Terima</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse($distributions as $index => $dist)
                        @php $member = $dist->member; @endphp
                        <tr wire:key="disb-{{ $dist->id }}" class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-all">
                            <td class="py-2.5 px-3 font-mono text-slate-400">{{ $distributions->firstItem() + $index }}</td>
                            <td class="py-2.5 px-3 font-mono font-bold text-slate-700 dark:text-slate-300">
                                {{ $member?->nomorAnggota ?? '-' }}
                            </td>
                            <td class="py-2.5 px-3 font-bold text-slate-800 dark:text-white">
                                {{ $member?->name ?? '-' }}
                            </td>
                            <td class="py-2.5 px-3 text-slate-500">{{ $member?->unitKerja ?? '-' }}</td>
                            <td class="py-2.5 px-3 text-right font-mono font-bold text-emerald-600 dark:text-emerald-400 text-sm">
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
                                @if($dist->is_disbursed)
                                    <button wire:click="toggleDisbursed({{ $dist->id }})"
                                        class="px-3 py-1.5 rounded-lg text-[10px] font-bold bg-slate-100 text-slate-600 hover:bg-rose-100 hover:text-rose-600 dark:bg-slate-800 dark:hover:bg-rose-900/30 transition-all">
                                        Batalkan
                                    </button>
                                @else
                                    <div class="flex items-center justify-center gap-1">
                                        <button wire:click="disburseSingle({{ $dist->id }})"
                                            title="Cairkan Tunai / Transfer Bank (Catat Pengeluaran)"
                                            class="px-2.5 py-1.5 rounded-lg text-[10px] font-bold bg-emerald-600 text-white hover:bg-emerald-700 shadow-sm transition-all flex items-center gap-1">
                                            <i class='bx bx-check'></i> Tunai/Transfer
                                        </button>
                                        <button wire:click="disburseToSukarela({{ $dist->id }})"
                                            title="Masukan Nominal SHU ke Dompet Simpanan Sukarela Anggota"
                                            class="px-2.5 py-1.5 rounded-lg text-[10px] font-bold bg-indigo-600 text-white hover:bg-indigo-700 shadow-sm transition-all flex items-center gap-1">
                                            <i class='bx bx-wallet'></i> Ke Sukarela
                                        </button>
                                    </div>
                                @endif
                            </td>
                            <td class="py-2.5 px-3 text-center only-print hidden border-b">
                                _______________________
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">Tidak ada data distribusi.</td>
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

    {{-- Actions --}}
    <div class="bg-white dark:bg-darkCard p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 no-print">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <button wire:click="goBack"
                class="bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 transition-all flex items-center gap-2">
                <i class='bx bx-left-arrow-alt text-base'></i> Kembali ke Alokasi
            </button>

            @if($stats['pending'] === 0 && $stats['total'] > 0)
                <button wire:click="completeSession"
                    onclick="return confirm('Semua SHU telah dicairkan. Tandai proses RAT sebagai selesai?')"
                    class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-5 py-2.5 rounded-xl shadow-lg shadow-emerald-600/20 transition-all flex items-center gap-2">
                    <i class='bx bx-check-double text-base'></i> Selesaikan Proses RAT
                </button>
            @endif
        </div>
    </div>

    {{-- Modal Form Berita Acara RAT (Interactive & Tabbed) --}}
    @if($ratSession)
        <div x-show="showBeritaAcaraModal" x-cloak
            class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-md flex items-center justify-center p-4 no-print"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95">

            <div class="bg-white dark:bg-darkCard w-full max-w-3xl rounded-2xl shadow-2xl border border-slate-100 dark:border-slate-700 overflow-hidden flex flex-col max-h-[90vh]"
                @click.away="showBeritaAcaraModal = false">
                
                {{-- Modal Header --}}
                <div class="px-6 py-4 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white flex items-center justify-between shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-white/10 backdrop-blur flex items-center justify-center text-white">
                            <i class='bx bxs-file-doc text-xl'></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-sm">Cetak Berita Acara RAT {{ $ratSession->year }}</h3>
                            <p class="text-[11px] text-indigo-200">Format Resmi Koperasi Konsumen Syariah Bermadani UMB</p>
                        </div>
                    </div>
                    <button @click="showBeritaAcaraModal = false" class="text-white/80 hover:text-white transition-all">
                        <i class='bx bx-x text-2xl'></i>
                    </button>
                </div>

                {{-- Tab Navigation --}}
                <div class="flex overflow-x-auto no-scrollbar border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/30 px-6 pt-3 gap-2">
                    <button @click="activeTab = 'acara'"
                        :class="activeTab === 'acara' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400 bg-white dark:bg-darkCard' : 'border-transparent text-slate-500 hover:text-slate-700'"
                        class="px-4 py-2 text-xs font-bold border-b-2 rounded-t-xl transition-all flex items-center gap-1.5 shrink-0">
                        <i class='bx bx-calendar-event text-sm'></i> 1. Acara & Kuorum
                    </button>
                    <button @click="activeTab = 'pengurus'"
                        :class="activeTab === 'pengurus' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400 bg-white dark:bg-darkCard' : 'border-transparent text-slate-500 hover:text-slate-700'"
                        class="px-4 py-2 text-xs font-bold border-b-2 rounded-t-xl transition-all flex items-center gap-1.5 shrink-0">
                        <i class='bx bx-pen text-sm'></i> 2. Pimpinan & Pengurus
                    </button>
                    <button @click="activeTab = 'rekomendasi'"
                        :class="activeTab === 'rekomendasi' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400 bg-white dark:bg-darkCard' : 'border-transparent text-slate-500 hover:text-slate-700'"
                        class="px-4 py-2 text-xs font-bold border-b-2 rounded-t-xl transition-all flex items-center gap-1.5 shrink-0">
                        <i class='bx bx-notepad text-sm'></i> 3. Substansi & Rekomendasi
                    </button>
                    <button @click="activeTab = 'summary'"
                        :class="activeTab === 'summary' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400 bg-white dark:bg-darkCard' : 'border-transparent text-slate-500 hover:text-slate-700'"
                        class="px-4 py-2 text-xs font-bold border-b-2 rounded-t-xl transition-all flex items-center gap-1.5 shrink-0">
                        <i class='bx bx-pie-chart-alt-2 text-sm'></i> 4. Preview SHU
                    </button>
                </div>

                {{-- Form Body --}}
                <form action="{{ route('admin.rat.pdf-berita-acara', $ratSession->id) }}" method="POST" target="_blank" class="flex-1 overflow-y-auto p-6 space-y-4">
                    @csrf

                    {{-- TAB 1: Acara & Kuorum --}}
                    <div x-show="activeTab === 'acara'" class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1 flex items-center gap-1">
                                    <i class='bx bx-hash text-indigo-500'></i> Nomor Berita Acara
                                </label>
                                <input type="text" name="nomor_surat" value="001/BA-RAT/BERMADANI/{{ $ratSession->year }}" required
                                    class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-800 dark:text-white outline-none focus:border-indigo-500">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1 flex items-center gap-1">
                                    <i class='bx bx-calendar text-indigo-500'></i> Hari & Tanggal Pelaksanaan
                                </label>
                                <input type="text" name="hari_tanggal" value="{{ $ratSession->event_date ? $ratSession->event_date->translatedFormat('l, d F Y') : date('d F Y') }}" required
                                    class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-800 dark:text-white outline-none focus:border-indigo-500">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1 flex items-center gap-1">
                                    <i class='bx bx-time text-indigo-500'></i> Waktu Pelaksanaan
                                </label>
                                <input type="text" name="jam" value="09:00 - 12:00 WIB" required
                                    class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-800 dark:text-white outline-none focus:border-indigo-500">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1 flex items-center gap-1">
                                    <i class='bx bx-map-pin text-indigo-500'></i> Tempat Pelaksanaan
                                </label>
                                <input type="text" name="tempat" value="{{ coop_setting('rat_default_venue') }}" required
                                    class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-800 dark:text-white outline-none focus:border-indigo-500">
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

                    {{-- TAB 3: Substansi & Rekomendasi Hasil RAT --}}
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
                    <div class="pt-4 flex items-center justify-between border-t border-slate-100 dark:border-slate-700">
                        <div class="flex items-center gap-1">
                            <button type="button" @click="activeTab = 'acara'" :class="activeTab === 'acara' ? 'text-indigo-600 font-bold' : 'text-slate-400'" class="text-xs">1</button>
                            <span class="text-slate-300">•</span>
                            <button type="button" @click="activeTab = 'pengurus'" :class="activeTab === 'pengurus' ? 'text-indigo-600 font-bold' : 'text-slate-400'" class="text-xs">2</button>
                            <span class="text-slate-300">•</span>
                            <button type="button" @click="activeTab = 'summary'" :class="activeTab === 'summary' ? 'text-indigo-600 font-bold' : 'text-slate-400'" class="text-xs">3</button>
                        </div>

                        <div class="flex items-center gap-2">
                            <button type="button" @click="showBeritaAcaraModal = false"
                                class="px-4 py-2 rounded-xl text-xs font-bold bg-slate-100 text-slate-600 hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-300 transition-all">
                                Batal
                            </button>
                            <button type="submit" @click="showBeritaAcaraModal = false"
                                class="px-5 py-2.5 rounded-xl text-xs font-bold bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white shadow-lg shadow-indigo-600/25 flex items-center gap-2 transition-all">
                                <i class='bx bxs-file-pdf text-base'></i> Download PDF Berita Acara
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
