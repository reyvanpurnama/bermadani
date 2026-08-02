<div class="space-y-6">
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
        <div class="flex items-center gap-2">
            @if($ratSession)
                <a href="{{ route('admin.rat.pdf-report', $ratSession->id) }}" target="_blank"
                    class="bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-md shadow-rose-600/20 transition-all flex items-center gap-2">
                    <i class='bx bxs-file-pdf text-lg'></i> Download PDF Laporan Lengkap
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
                                <button wire:click="toggleDisbursed({{ $dist->id }})"
                                    class="px-3 py-1.5 rounded-lg text-[10px] font-bold transition-all {{ $dist->is_disbursed ? 'bg-slate-100 text-slate-600 hover:bg-rose-100 hover:text-rose-600 dark:bg-slate-800 dark:hover:bg-rose-900/30' : 'bg-emerald-600 text-white hover:bg-emerald-700 shadow-sm' }}">
                                    {{ $dist->is_disbursed ? 'Batalkan' : 'Cairkan' }}
                                </button>
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
</div>
