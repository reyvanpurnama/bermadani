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

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white dark:bg-darkCard p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="p-2 rounded-lg bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400">
                    <i class='bx bx-file text-2xl'></i>
                </span>
                <h1 class="text-xl font-bold text-slate-800 dark:text-white">Penyelenggaraan RAT & Pembagian SHU</h1>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400">
                Manajemen sesi Rapat Anggota Tahunan (RAT), alokasi pembagian SHU vs modal usaha, dan cetak tanda terima.
            </p>
        </div>

        <div class="flex items-center gap-3">
            @if($allSessions->isNotEmpty())
                <div class="relative">
                    <select wire:change="loadSession($event.target.value)"
                        class="appearance-none bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white text-xs font-bold rounded-xl px-4 py-2.5 pr-9 outline-none cursor-pointer">
                        @foreach($allSessions as $s)
                            <option value="{{ $s->id }}" {{ $session?->id === $s->id ? 'selected' : '' }}>
                                RAT {{ $s->year }} ({{ $s->status }})
                            </option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-500">
                        <i class='bx bx-chevron-down text-base'></i>
                    </div>
                </div>
            @endif

            <button onclick="window.print()"
                class="bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 transition-all flex items-center gap-2">
                <i class='bx bx-printer text-base'></i> Cetak Rekap RAT
            </button>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-400 px-4 py-3 rounded-xl flex items-center gap-2">
            <i class='bx bx-check-circle text-xl'></i>
            <span class="text-xs font-medium">{{ session('success') }}</span>
        </div>
    @endif

    @if (session()->has('info'))
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 text-blue-700 dark:text-blue-400 px-4 py-3 rounded-xl flex items-center gap-2">
            <i class='bx bx-info-circle text-xl'></i>
            <span class="text-xs font-medium">{{ session('info') }}</span>
        </div>
    @endif

    {{-- Top Cards / KPI --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Total Laba Bersih --}}
        <div class="bg-white dark:bg-darkCard p-5 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Total Laba Bersih</p>
            <h3 class="text-xl font-bold text-slate-800 dark:text-white">
                Rp {{ number_format($totalNetProfit, 0, ',', '.') }}
            </h3>
            <p class="text-[10px] text-slate-400 mt-1">Tahun Buku {{ $year }}</p>
        </div>

        {{-- SHU Dibagikan ke Anggota --}}
        <div class="bg-white dark:bg-darkCard p-5 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
            <p class="text-[10px] font-bold text-emerald-500 uppercase tracking-widest mb-1">Total SHU Dibagikan</p>
            <h3 class="text-xl font-bold text-emerald-600 dark:text-emerald-400">
                Rp {{ number_format($totalMemberShu, 0, ',', '.') }}
            </h3>
            <p class="text-[10px] text-emerald-600/80 font-medium mt-1">{{ number_format($memberAllocationPercentage, 2) }}% dari Laba Bersih</p>
        </div>

        {{-- Modal Usaha / Cadangan Koperasi --}}
        <div class="bg-white dark:bg-darkCard p-5 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
            <p class="text-[10px] font-bold text-blue-500 uppercase tracking-widest mb-1">Modal Usaha / Cadangan</p>
            <h3 class="text-xl font-bold text-blue-600 dark:text-blue-400">
                Rp {{ number_format($summary['retainedAmount'], 0, ',', '.') }}
            </h3>
            <p class="text-[10px] text-slate-400 mt-1">Ditahan untuk Operasional / Modal</p>
        </div>

        {{-- Status RAT --}}
        <div class="bg-white dark:bg-darkCard p-5 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 flex flex-col justify-between">
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Status Sesi RAT</p>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold {{ $session?->status === 'FINALIZED' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' }}">
                        {{ $session?->status === 'FINALIZED' ? 'DISAHKAN & DIPUBLISH' : 'DRAFT (PERSIAPAN)' }}
                    </span>
                </div>
            </div>
            <p class="text-[10px] text-slate-400 mt-2">
                {{ $session?->status === 'FINALIZED' ? 'SHU tampil di portal anggota' : 'Belum dipublikasikan' }}
            </p>
        </div>
    </div>

    {{-- Form Config & Actions --}}
    <div class="bg-white dark:bg-darkCard p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
        <h3 class="text-sm font-bold text-slate-800 dark:text-white mb-4">Pengaturan Alokasi Laba Bersih & SHU RAT {{ $year }}</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-4">
            <div>
                <label class="block text-[11px] font-bold text-slate-500 mb-1">Tahun Buku</label>
                <input type="number" wire:model.blur="year"
                    class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-xs font-bold text-slate-800 dark:text-white outline-none">
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-500 mb-1">Tanggal RAT</label>
                <input type="date" wire:model.blur="eventDate"
                    class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-800 dark:text-white outline-none">
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-500 mb-1">Total Laba Bersih (Rp)</label>
                <input type="number" wire:model.live.debounce.300ms="totalNetProfit"
                    class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-xs font-bold text-slate-800 dark:text-white outline-none">
            </div>

            <div>
                <label class="block text-[11px] font-bold text-emerald-600 mb-1">Total SHU Dibagikan (Rp)</label>
                <input type="number" wire:model.live.debounce.300ms="totalMemberShu"
                    class="w-full bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-300 dark:border-emerald-700 rounded-xl px-3 py-2 text-xs font-bold text-emerald-600 outline-none"
                    placeholder="Misal: 15000000">
            </div>

            <div>
                <label class="block text-[11px] font-bold text-indigo-600 mb-1">Alokasi Anggota (%)</label>
                <input type="number" wire:model.live.debounce.300ms="memberAllocationPercentage" step="0.01" min="0" max="100"
                    class="w-full bg-indigo-50 dark:bg-indigo-950/40 border border-indigo-300 dark:border-indigo-700 rounded-xl px-3 py-2 text-xs font-bold text-indigo-600 outline-none">
            </div>
        </div>

        <div class="mb-4">
            <label class="block text-[11px] font-bold text-slate-500 mb-1">Judul / Tema RAT</label>
            <input type="text" wire:model.blur="title"
                class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-800 dark:text-white outline-none"
                placeholder="Contoh: RAT Koperasi Bermadani Tahun Buku 2025">
        </div>

        <div class="flex flex-wrap items-center justify-between gap-3 pt-4 border-t border-slate-100 dark:border-slate-700">
            <div class="flex items-center gap-2">
                <button wire:click="saveSession"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-sm transition-all flex items-center gap-2">
                    <i class='bx bx-save text-base'></i> Simpan & Hitung Ulang SHU
                </button>

                @if($session?->status === 'FINALIZED')
                    <button wire:click="reopenSession"
                        class="bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold px-4 py-2.5 rounded-xl transition-all flex items-center gap-2">
                        <i class='bx bx-edit text-base'></i> Kembalikan ke Draft
                    </button>
                @endif
            </div>

            @if($session?->status !== 'FINALIZED')
                <button wire:click="finalizeSession" onclick="return confirm('Apakah Anda yakin ingin memfinalisasi & mempublikasikan SHU ke seluruh portal anggota?')"
                    class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-5 py-2.5 rounded-xl shadow-lg shadow-emerald-600/20 transition-all flex items-center gap-2">
                    <i class='bx bx-check-double text-base'></i> Sahkan & Publikasikan SHU ke Anggota
                </button>
            @else
                <span class="text-xs text-emerald-600 dark:text-emerald-400 font-bold flex items-center gap-1">
                    <i class='bx bx-check-circle text-lg'></i> SHU Telah Dipublikasikan ke Anggota
                </span>
            @endif
        </div>
    </div>

    {{-- Printable Section for Physical Event --}}
    <div id="printableSection" class="bg-white dark:bg-darkCard p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
        {{-- Print Header --}}
        <div class="mb-6 pb-4 border-b border-slate-200 dark:border-slate-700 flex justify-between items-center">
            <div>
                <h2 class="text-lg font-bold text-slate-800 dark:text-white">DAFTAR PEMBAGIAN SHU ANGGOTA KOPERASI BERMADANI</h2>
                <p class="text-xs text-slate-500">{{ $title }} • Tanggal Pelaksanaan: {{ \Carbon\Carbon::parse($eventDate)->format('d F Y') }}</p>
                <p class="text-xs text-slate-500 font-semibold mt-0.5">
                    Total Laba Bersih: Rp {{ number_format($totalNetProfit, 0, ',', '.') }} | 
                    SHU Dibagikan: <span class="text-emerald-600 font-bold">Rp {{ number_format($totalMemberShu, 0, ',', '.') }}</span> |
                    Modal Usaha Ditahan: <span class="text-blue-600 font-bold">Rp {{ number_format($summary['retainedAmount'], 0, ',', '.') }}</span>
                </p>
            </div>
            <div class="no-print">
                <input type="text" wire:model.live.debounce.300ms="searchMember"
                    placeholder="Cari Anggota / No / Unit..."
                    class="bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs rounded-xl px-4 py-2 text-slate-800 dark:text-white outline-none w-64">
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800 text-slate-500 dark:text-slate-400 uppercase text-[10px] tracking-wider border-b border-slate-200 dark:border-slate-700">
                        <th class="py-3 px-3">No</th>
                        <th class="py-3 px-3">No. Anggota</th>
                        <th class="py-3 px-3">Nama Anggota</th>
                        <th class="py-3 px-3">Unit Kerja</th>
                        <th class="py-3 px-3 text-right">Simpanan Wajib (Rp)</th>
                        <th class="py-3 px-3 text-center">Porsi (%)</th>
                        <th class="py-3 px-3 text-right">Nominal SHU (Rp)</th>
                        <th class="py-3 px-3 text-center no-print">Status Pencairan</th>
                        <th class="py-3 px-3 text-center only-print hidden">Tanda Terima</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse($distributions as $index => $item)
                        @php
                            $isModel = $item instanceof \App\Models\MemberShuDistribution;
                            $member = $isModel ? $item->member : $item;
                            $simwa = $isModel ? $item->simpanan_wajib_amount : ($member->simpananWajib ?? 0);
                            $portion = $isModel ? $item->portion_percentage : (($simwa / $summary['totalSimwa']) * 100);
                            $shu = $isModel ? $item->shu_amount : ($portion / 100) * $summary['totalMemberShu'];
                            $isDisbursed = $isModel ? $item->is_disbursed : false;
                        @endphp
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30">
                            <td class="py-2.5 px-3 font-mono text-slate-400">{{ $index + 1 }}</td>
                            <td class="py-2.5 px-3 font-mono font-bold text-slate-700 dark:text-slate-300">
                                {{ $member->nomorAnggota ?? '-' }}
                            </td>
                            <td class="py-2.5 px-3 font-bold text-slate-800 dark:text-white">
                                {{ $member->name }}
                            </td>
                            <td class="py-2.5 px-3 text-slate-500">
                                {{ $member->unitKerja ?? '-' }}
                            </td>
                            <td class="py-2.5 px-3 text-right font-mono text-slate-700 dark:text-slate-300">
                                Rp {{ number_format($simwa, 0, ',', '.') }}
                            </td>
                            <td class="py-2.5 px-3 text-center font-mono font-bold text-indigo-600 dark:text-indigo-400">
                                {{ number_format($portion, 3, ',', '.') }}%
                            </td>
                            <td class="py-2.5 px-3 text-right font-mono font-bold text-emerald-600 dark:text-emerald-400">
                                Rp {{ number_format($shu, 0, ',', '.') }}
                            </td>
                            <td class="py-2.5 px-3 text-center no-print">
                                @if($isModel)
                                    <button wire:click="toggleDisbursed({{ $item->id }})"
                                        class="px-2.5 py-1 rounded-full text-[10px] font-bold transition-all {{ $isDisbursed ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400' }}">
                                        {{ $isDisbursed ? '✓ Dicairkan' : 'Belum' }}
                                    </button>
                                @else
                                    <span class="text-[10px] text-slate-400">Draft</span>
                                @endif
                            </td>
                            <td class="py-2.5 px-3 text-center only-print hidden border-b">
                                _______________________
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-8 text-center text-slate-400">Tidak ada data anggota aktif.</td>
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
</div>
