<div class="space-y-6">
    {{-- Header Banner --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 p-6 md:p-8 text-white shadow-2xl border border-indigo-500/20">
        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute right-1/3 -top-10 w-48 h-48 bg-teal-500/10 rounded-full blur-2xl pointer-events-none"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-500/20 border border-indigo-400/30 text-indigo-300 text-xs font-semibold mb-3 backdrop-blur-md">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Audit System & Rekonsiliasi Payroll
                </div>
                <h1 class="text-2xl md:text-3xl font-black text-white tracking-tight flex items-center gap-3">
                    Audit Simpanan Anggota
                </h1>
                <p class="text-sm text-slate-300 mt-1 max-w-2xl">
                    Manajemen import payroll CSV bulanan, mapping otomatis identitas anggota, dan sinkronisasi saldo Simpanan Wajib & Sukarela secara akurat.
                </p>
            </div>
            
            <div class="flex items-center gap-3 shrink-0">
                <button wire:click="generateReconciliation" 
                    class="px-4 py-2.5 bg-white/10 hover:bg-white/20 text-white text-xs font-bold rounded-xl backdrop-blur-md border border-white/20 transition-all flex items-center gap-2 shadow-lg">
                    <i class='bx bx-refresh text-lg'></i>
                    <span>Refresh Audit Data</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Stats Executive Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Card 1: Total Imports --}}
        <div class="bg-white dark:bg-darkCard p-5 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-800 relative overflow-hidden group hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-widest">Total Data CSV</span>
                    <p class="text-2xl font-black text-slate-900 dark:text-white mt-1">
                        {{ number_format($stats['total_imports']) }}
                        <span class="text-xs font-medium text-slate-400">baris</span>
                    </p>
                    <p class="text-xs text-indigo-600 dark:text-indigo-400 font-bold mt-1">
                        {{ $stats['periods_count'] }} Periode Payroll
                    </p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                    <i class='bx bx-cloud-download'></i>
                </div>
            </div>
        </div>

        {{-- Card 2: Mapping Status --}}
        <div class="bg-white dark:bg-darkCard p-5 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-800 relative overflow-hidden group hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-widest">Status Mapping Nama</span>
                    <p class="text-2xl font-black text-slate-900 dark:text-white mt-1">
                        {{ number_format($stats['processed']) }}
                        <span class="text-xs font-medium text-emerald-500">Mapped</span>
                    </p>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="text-xs font-bold text-rose-500">{{ number_format($stats['unprocessed']) }} Unmapped</span>
                        <span class="text-[10px] px-1.5 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 font-bold">
                            {{ $stats['mapped_percent'] }}%
                        </span>
                    </div>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                    <i class='bx bx-user-check'></i>
                </div>
            </div>
        </div>

        {{-- Card 3: Total Nominal Payroll --}}
        <div class="bg-white dark:bg-darkCard p-5 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-800 relative overflow-hidden group hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-widest">Nominal Payroll Extracted</span>
                    <p class="text-2xl font-black text-amber-600 dark:text-amber-400 mt-1">
                        Rp {{ number_format($stats['total_amount'] / 1000000, 1) }}M
                    </p>
                    <p class="text-xs text-slate-400 font-mono mt-1">
                        Rp {{ number_format($stats['total_amount'], 0, ',', '.') }}
                    </p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                    <i class='bx bx-wallet'></i>
                </div>
            </div>
        </div>

        {{-- Card 4: Audit Status --}}
        <div class="bg-white dark:bg-darkCard p-5 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-800 relative overflow-hidden group hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-widest">Hasil Rekonsiliasi</span>
                    <p class="text-2xl font-black text-slate-900 dark:text-white mt-1">
                        {{ $stats['total_audit'] }}
                        <span class="text-xs font-medium text-slate-400">Member</span>
                    </p>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="text-xs font-bold text-emerald-600">{{ $stats['match_count'] }} Match</span>
                        @if($stats['mismatch_count'] > 0)
                            <span class="text-xs font-bold text-rose-500 animate-pulse">{{ $stats['mismatch_count'] }} Selisih</span>
                        @endif
                    </div>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-sky-50 dark:bg-sky-900/30 text-sky-600 dark:text-sky-400 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                    <i class='bx bx-spreadsheet'></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Container Card --}}
    <div class="bg-white dark:bg-darkCard rounded-3xl shadow-sm border border-slate-100 dark:border-slate-800 overflow-hidden">
        {{-- Navigation Tabs --}}
        <div class="flex flex-wrap border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50 p-2 gap-2">
            <button wire:click="$set('activeTab', 'upload')"
                class="flex-1 min-w-[200px] px-5 py-3.5 rounded-2xl text-xs font-extrabold transition-all flex items-center justify-center gap-2.5 {{ $activeTab === 'upload' ? 'bg-white dark:bg-slate-800 text-indigo-600 dark:text-indigo-400 shadow-sm border border-slate-200/60 dark:border-slate-700' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300' }}">
                <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-black {{ $activeTab === 'upload' ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/50 dark:text-indigo-300' : 'bg-slate-200 text-slate-600 dark:bg-slate-800 dark:text-slate-400' }}">1</span>
                <span>Upload CSV Payroll</span>
                @if(count($this->importedPeriods) > 0)
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-50 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800">
                        {{ count($this->importedPeriods) }} File
                    </span>
                @endif
            </button>

            <button wire:click="$set('activeTab', 'mapping')"
                class="flex-1 min-w-[200px] px-5 py-3.5 rounded-2xl text-xs font-extrabold transition-all flex items-center justify-center gap-2.5 {{ $activeTab === 'mapping' ? 'bg-white dark:bg-slate-800 text-indigo-600 dark:text-indigo-400 shadow-sm border border-slate-200/60 dark:border-slate-700' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300' }}">
                <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-black {{ $activeTab === 'mapping' ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/50 dark:text-indigo-300' : 'bg-slate-200 text-slate-600 dark:bg-slate-800 dark:text-slate-400' }}">2</span>
                <span>Mapping Nama CSV</span>
                @if($stats['unprocessed'] > 0)
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 dark:bg-rose-900/40 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-800 animate-pulse">
                        {{ $stats['unprocessed'] }} Unmapped
                    </span>
                @else
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                        ✓ Clear
                    </span>
                @endif
            </button>

            <button wire:click="$set('activeTab', 'reconciliation')"
                class="flex-1 min-w-[200px] px-5 py-3.5 rounded-2xl text-xs font-extrabold transition-all flex items-center justify-center gap-2.5 {{ $activeTab === 'reconciliation' ? 'bg-white dark:bg-slate-800 text-indigo-600 dark:text-indigo-400 shadow-sm border border-slate-200/60 dark:border-slate-700' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300' }}">
                <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-black {{ $activeTab === 'reconciliation' ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/50 dark:text-indigo-300' : 'bg-slate-200 text-slate-600 dark:bg-slate-800 dark:text-slate-400' }}">3</span>
                <span>Rekonsiliasi & Sync</span>
                @if($stats['total_audit'] > 0)
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-sky-50 dark:bg-sky-900/40 text-sky-600 dark:text-sky-400 border border-sky-200 dark:border-sky-800">
                        {{ $stats['total_audit'] }} Member
                    </span>
                @endif
            </button>
        </div>

        <div class="p-6">
            {{-- TAB 1: UPLOAD CSV --}}
            @if($activeTab === 'upload')
                <div class="space-y-8">
                    {{-- Upload Drag & Drop Area --}}
                    <div class="relative text-center py-12 px-6 border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-3xl bg-slate-50/50 dark:bg-slate-800/20 hover:bg-indigo-50/30 dark:hover:bg-indigo-950/20 transition-all duration-300 group"
                        x-data="{ isDropping: false }" 
                        @dragover.prevent="isDropping = true"
                        @dragleave.prevent="isDropping = false" 
                        @drop.prevent="isDropping = false"
                        :class="{ 'border-indigo-500 bg-indigo-50/40 dark:bg-indigo-950/40': isDropping }">

                        <input type="file" wire:model="csvFiles" multiple class="hidden" id="csvInput">

                        <label for="csvInput" class="cursor-pointer block space-y-4">
                            <div class="w-20 h-20 rounded-3xl bg-gradient-to-tr from-indigo-500 to-violet-600 text-white mx-auto flex items-center justify-center shadow-lg shadow-indigo-500/25 group-hover:scale-110 transition-transform">
                                <i class='bx bx-cloud-upload text-4xl'></i>
                            </div>
                            <div>
                                <h3 class="text-base font-extrabold text-slate-800 dark:text-white">Klik atau Tarik File CSV Payroll Per Bulan</h3>
                                <p class="text-xs text-slate-400 mt-1 max-w-md mx-auto">
                                    Sistem akan secara otomatis mendeteksi Bulan & Tahun dari nama file (contoh: <code class="px-1.5 py-0.5 rounded bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 font-mono font-bold">04-april-2024.csv</code> atau <code class="px-1.5 py-0.5 rounded bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 font-mono font-bold">agustus-2024.csv</code>).
                                </p>
                            </div>
                        </label>

                        @if(count($csvFiles) > 0)
                            <div class="mt-8 p-4 bg-white dark:bg-slate-800 rounded-2xl max-w-lg mx-auto border border-indigo-100 dark:border-indigo-900/50 shadow-xl space-y-3">
                                <div class="flex items-center justify-between text-xs font-bold text-indigo-600 dark:text-indigo-400">
                                    <span><i class='bx bx-file'></i> {{ count($csvFiles) }} file terpilih</span>
                                    <button wire:click="$set('csvFiles', [])" class="text-rose-500 hover:underline">Batal</button>
                                </div>
                                <button wire:click="processUploads"
                                    class="w-full py-3 bg-gradient-to-r from-indigo-600 to-violet-600 text-white text-xs font-extrabold rounded-xl shadow-lg hover:shadow-indigo-500/25 hover:scale-[1.02] transition-all flex items-center justify-center gap-2">
                                    <span wire:loading.remove wire:target="processUploads">
                                        <i class='bx bx-import text-base'></i> Mulai Proses Import / Replace Data
                                    </span>
                                    <span wire:loading wire:target="processUploads" class="flex items-center gap-2">
                                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        Memproses & Menganalisis File...
                                    </span>
                                </button>
                            </div>
                        @endif
                    </div>

                    {{-- Managed Periods List --}}
                    @if(count($this->importedPeriods) > 0)
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-sm font-extrabold text-slate-800 dark:text-white flex items-center gap-2">
                                    <i class='bx bx-calendar text-indigo-500 text-lg'></i> Data Payroll Ter-Import Periode Bulanan
                                </h3>
                                <span class="text-xs text-slate-400">Total {{ count($this->importedPeriods) }} periode tersimpan</span>
                            </div>

                            <div class="overflow-hidden border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm">
                                <table class="w-full text-left text-xs">
                                    <thead class="bg-slate-50 dark:bg-slate-800/80 text-slate-500 dark:text-slate-400 uppercase font-extrabold tracking-wider border-b border-slate-200 dark:border-slate-800">
                                        <tr>
                                            <th class="px-5 py-3.5">Periode</th>
                                            <th class="px-5 py-3.5">Nama File Origin</th>
                                            <th class="px-5 py-3.5 text-right">Total Transaksi</th>
                                            <th class="px-5 py-3.5 text-right">Nominal Ter-Extract</th>
                                            <th class="px-5 py-3.5 text-right">Waktu Import</th>
                                            <th class="px-5 py-3.5 text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800 bg-white dark:bg-darkCard">
                                        @foreach($this->importedPeriods as $period)
                                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/50 transition-colors">
                                                <td class="px-5 py-3.5">
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-mono font-bold bg-indigo-50 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300 border border-indigo-100 dark:border-indigo-800">
                                                        <i class='bx bx-calendar-event mr-1'></i> {{ $period->period }}
                                                    </span>
                                                </td>
                                                <td class="px-5 py-3.5 text-slate-600 dark:text-slate-300 font-medium">
                                                    {{ $period->filename }}
                                                </td>
                                                <td class="px-5 py-3.5 text-right font-mono font-bold text-slate-800 dark:text-slate-200">
                                                    {{ number_format($period->total_rows) }} baris
                                                </td>
                                                <td class="px-5 py-3.5 text-right font-mono font-bold text-emerald-600 dark:text-emerald-400">
                                                    Rp {{ number_format($period->total_amount, 0, ',', '.') }}
                                                </td>
                                                <td class="px-5 py-3.5 text-right text-slate-400">
                                                    {{ \Carbon\Carbon::parse($period->imported_at)->diffForHumans() }}
                                                </td>
                                                <td class="px-5 py-3.5 text-center">
                                                    <button wire:click="deletePeriod('{{ $period->period }}')"
                                                        class="p-2 text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-900/30 rounded-xl transition-colors"
                                                        title="Hapus Data Periode Ini"
                                                        onclick="return confirm('Hapus semua data impor untuk periode {{ $period->period }}?')">
                                                        <i class='bx bx-trash text-lg'></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            {{-- TAB 2: NAME MAPPING --}}
            @if($activeTab === 'mapping')
                <div class="space-y-6">
                    @if($unmappedNames->count() > 0)
                        <div class="p-4 bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-900/50 rounded-2xl flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-900/50 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xl shrink-0">
                                    <i class='bx bx-error-circle'></i>
                                </div>
                                <div>
                                    <h4 class="text-xs font-extrabold text-amber-900 dark:text-amber-200">Perhatian: Terdeteksi Nama CSV Belum Terhubung</h4>
                                    <p class="text-xs text-amber-700 dark:text-amber-400">Pilih identitas anggota resmi di database agar transaksi payroll dapat di-sinkronisasikan.</p>
                                </div>
                            </div>
                            <span class="px-3 py-1 bg-amber-200 dark:bg-amber-800 text-amber-900 dark:text-amber-100 font-extrabold rounded-lg text-xs">
                                {{ $unmappedNames->total() }} Nama
                            </span>
                        </div>

                        <div class="overflow-x-auto border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-slate-50 dark:bg-slate-800/80 text-slate-500 dark:text-slate-400 uppercase font-extrabold tracking-wider border-b border-slate-200 dark:border-slate-800">
                                    <tr>
                                        <th class="px-5 py-3.5 w-12 text-center">#</th>
                                        <th class="px-5 py-3.5">Nama di CSV (Raw)</th>
                                        <th class="px-5 py-3.5">Pencarian / Pilih Member Resmi</th>
                                        <th class="px-5 py-3.5 w-36 text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 bg-white dark:bg-darkCard">
                                    @foreach($unmappedNames as $index => $item)
                                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/50 transition-colors" wire:key="row-{{ md5($item->raw_name) }}">
                                            <td class="px-5 py-3.5 text-center font-bold text-slate-400">
                                                {{ ($unmappedNames->currentPage() - 1) * $unmappedNames->perPage() + $loop->iteration }}
                                            </td>
                                            <td class="px-5 py-3.5">
                                                <div class="font-mono font-extrabold text-slate-800 dark:text-white text-sm">
                                                    {{ $item->raw_name }}
                                                </div>
                                                <div class="text-[10px] text-slate-400 mt-1 uppercase tracking-wider flex items-center gap-1">
                                                    <span>Awal Muncul:</span> 
                                                    <span class="bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 px-1.5 py-0.5 rounded font-mono font-bold">{{ $item->earliest_period }}</span>
                                                </div>
                                            </td>
                                            <td class="px-5 py-3.5">
                                                <livewire:components.member-search-select 
                                                    :key="'search-' . md5($item->raw_name)"
                                                    :wire:key="'search-'.md5($item->raw_name)" 
                                                    :extra-data="$item->raw_name"
                                                    :joined-before="$item->earliest_period" />
                                            </td>
                                            <td class="px-5 py-3.5 text-center">
                                                <span class="px-3 py-1.5 bg-slate-100 dark:bg-slate-800 text-slate-400 text-[10px] font-bold rounded-xl flex items-center justify-center gap-1">
                                                    <i class='bx bx-search'></i> Pilih Member
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="pt-2">
                            {{ $unmappedNames->links() }}
                        </div>
                    @else
                        <div class="text-center py-16 bg-emerald-50/50 dark:bg-emerald-950/20 border border-emerald-200 dark:border-emerald-900/30 rounded-3xl space-y-4">
                            <div class="w-20 h-20 rounded-full bg-emerald-100 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400 mx-auto flex items-center justify-center text-4xl shadow-inner">
                                <i class='bx bx-check-double'></i>
                            </div>
                            <div class="max-w-md mx-auto">
                                <h3 class="text-lg font-extrabold text-slate-800 dark:text-white">Semua Nama CSV Sudah Terpetakan!</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                    Seluruh data import payroll telah memiliki hubungan dengan anggota resmi di database. Anda siap melakukan rekonsiliasi.
                                </p>
                            </div>
                            <button wire:click="$set('activeTab', 'reconciliation')" class="px-6 py-2.5 bg-emerald-600 text-white text-xs font-extrabold rounded-xl shadow-lg hover:bg-emerald-700 transition-colors">
                                Lanjut ke Rekonsiliasi & Sync →
                            </button>
                        </div>
                    @endif
                </div>
            @endif

            {{-- TAB 3: RECONCILIATION & SYNC --}}
            @if($activeTab === 'reconciliation')
                <div class="space-y-6">
                    {{-- Control Panel Banner --}}
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        {{-- Rebuild Settings Controls --}}
                        <div class="bg-slate-50 dark:bg-slate-800/50 rounded-3xl p-6 border border-slate-200 dark:border-slate-800 h-full flex flex-col justify-between">
                            <div>
                                <h3 class="text-xs font-extrabold text-slate-800 dark:text-white uppercase tracking-widest mb-4 flex items-center gap-2">
                                    <i class='bx bx-slider-alt text-indigo-500 text-base'></i> Pengaturan Rebuild History
                                </h3>
                                <div class="space-y-3">
                                    <label class="flex items-center gap-3 p-3 rounded-2xl bg-white dark:bg-darkCard border border-slate-200 dark:border-slate-700 cursor-pointer hover:border-emerald-500 transition-colors shadow-sm">
                                        <input type="checkbox" wire:model="processWajib"
                                            class="w-5 h-5 text-emerald-600 rounded-lg focus:ring-emerald-500 border-slate-300">
                                        <div>
                                            <p class="text-xs font-extrabold text-slate-800 dark:text-slate-200">Rebuild Simpanan Wajib</p>
                                            <p class="text-[10px] text-slate-400">Generate Rp 50rb/bln sesuai masa keanggotaan & payroll</p>
                                        </div>
                                    </label>
                                    <label class="flex items-center gap-3 p-3 rounded-2xl bg-white dark:bg-darkCard border border-slate-200 dark:border-slate-700 cursor-pointer hover:border-indigo-500 transition-colors shadow-sm">
                                        <input type="checkbox" wire:model="processSukarela"
                                            class="w-5 h-5 text-indigo-600 rounded-lg focus:ring-indigo-500 border-slate-300">
                                        <div>
                                            <p class="text-xs font-extrabold text-slate-800 dark:text-slate-200">Rebuild Simpanan Sukarela</p>
                                            <p class="text-[10px] text-slate-400">Generate tabungan sesuai nominal hasil split CSV Payroll</p>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        {{-- Action Execute Card --}}
                        <div class="lg:col-span-2 bg-gradient-to-br from-indigo-600 via-indigo-700 to-violet-800 rounded-3xl p-6 text-white shadow-2xl relative overflow-hidden flex flex-col justify-between">
                            <div class="absolute right-0 top-0 w-64 h-64 bg-white/10 rounded-full blur-3xl pointer-events-none"></div>

                            <div>
                                <div class="flex items-center gap-2 text-indigo-200 text-xs font-bold uppercase tracking-widest mb-1">
                                    <i class='bx bxs-zap'></i> Eksekusi Otomatisasi
                                </div>
                                <h3 class="text-lg font-black mb-2">Final Audit & Rebuild History Saldo</h3>
                                <p class="text-xs text-indigo-100 max-w-xl leading-relaxed">
                                    Proses ini akan menyesuaikan mutasi history simpanan dan membangun ulang saldo <strong class="text-white">Simpanan Wajib</strong> & <strong class="text-white">Simpanan Sukarela</strong> berdasarkan data CSV Payroll yang ter-import. 
                                    <span class="text-amber-200 font-bold block mt-1">⚠️ Catatan: Data Angsuran Pinjaman TIDAK diubah/dihapus.</span>
                                </p>
                            </div>

                            <div class="mt-6 flex flex-wrap items-center justify-between gap-4 pt-4 border-t border-indigo-500/30">
                                <div class="text-xs text-indigo-200 font-mono">
                                    Member Diproses: <strong class="text-white font-bold">{{ count($auditResults) - count($excludedMemberIds) }}</strong> dari {{ count($auditResults) }} Total
                                </div>

                                <button wire:click="cleanupAllSimwa" wire:loading.attr="disabled"
                                    class="px-6 py-3 bg-white text-indigo-700 hover:bg-indigo-50 font-black rounded-2xl shadow-xl hover:scale-105 transition-all flex items-center gap-2 shrink-0 text-xs disabled:opacity-50"
                                    onclick="return confirm('⚠️ KONFIRMASI FINAL REBUILD ⚠️\n\nAnda akan melakukan REBUILD HISTORY untuk {{ count($auditResults) - count($excludedMemberIds) }} anggota.\n\n- Data Angsuran/Pinjaman: AMAN (TIDAK DIHAPUS)\n- Simpanan Wajib: {{ $processWajib ? 'DI-RESET & SYNC' : 'DIBIARKAN' }}\n- Simpanan Sukarela: {{ $processSukarela ? 'DI-RESET & SYNC' : 'DIBIARKAN' }}\n\nLanjutkan?')">
                                    <i class='bx bxs-flask text-lg'></i>
                                    <span>RUN FULL CLEANUP (REBUILD ALL)</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Search & Status Filter Toolbar --}}
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-slate-50/50 dark:bg-slate-900/50 p-4 rounded-2xl border border-slate-200 dark:border-slate-800">
                        <div class="flex flex-wrap items-center gap-3 flex-1">
                            {{-- Search Member Input --}}
                            <div class="relative min-w-[240px] flex-1">
                                <i class='bx bx-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-base'></i>
                                <input type="text" wire:model.live.debounce.300ms="searchAudit"
                                    placeholder="Cari nama atau ID member..."
                                    class="w-full pl-10 pr-4 py-2 bg-white dark:bg-darkCard border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-800 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-indigo-500">
                            </div>

                            {{-- Filter Status Tabs --}}
                            <div class="flex items-center bg-white dark:bg-darkCard p-1 rounded-xl border border-slate-200 dark:border-slate-700">
                                <button wire:click="$set('filterStatus', 'all')"
                                    class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ $filterStatus === 'all' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-200' }}">
                                    Semua
                                </button>
                                <button wire:click="$set('filterStatus', 'match')"
                                    class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ $filterStatus === 'match' ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-200' }}">
                                    Match
                                </button>
                                <button wire:click="$set('filterStatus', 'mismatch')"
                                    class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ $filterStatus === 'mismatch' ? 'bg-rose-600 text-white shadow-sm' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-200' }}">
                                    Selisih / Gap
                                </button>
                            </div>
                        </div>

                        <button wire:click="generateReconciliation"
                            class="px-4 py-2 bg-slate-800 dark:bg-slate-700 hover:bg-slate-900 text-white text-xs font-bold rounded-xl shadow transition-colors flex items-center gap-2">
                            <i class='bx bx-refresh'></i>
                            <span wire:loading.remove wire:target="generateReconciliation">Generate / Refresh Report</span>
                            <span wire:loading wire:target="generateReconciliation">Menganalisis...</span>
                        </button>
                    </div>

                    {{-- High-Density Reconciliation Results Table --}}
                    @if(count($this->filteredAuditResults) > 0)
                        <div class="overflow-x-auto border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-slate-50 dark:bg-slate-800/80 text-slate-500 dark:text-slate-400 uppercase font-extrabold tracking-wider border-b border-slate-200 dark:border-slate-800">
                                    <tr>
                                        <th class="px-4 py-3 text-center w-12 bg-rose-50/50 dark:bg-rose-900/10" title="Lewati / Exclude Dari Cleanup">
                                            <i class='bx bx-block text-rose-500 text-base'></i>
                                        </th>
                                        <th class="px-4 py-3">Member</th>
                                        <th class="px-4 py-3">CSV Mapped Names</th>
                                        <th class="px-4 py-3 text-right">Tgl Gabung</th>
                                        <th class="px-4 py-3 text-center bg-slate-100/50 dark:bg-slate-800/50" colspan="3">Simpanan Wajib (Payroll)</th>
                                        <th class="px-4 py-3 text-center bg-indigo-50/50 dark:bg-indigo-900/20" colspan="2">Simpanan Sukarela</th>
                                        <th class="px-4 py-3 text-center">Aksi</th>
                                    </tr>
                                    <tr class="text-[9px] border-b border-slate-200 dark:border-slate-800 bg-slate-50/30 dark:bg-slate-900/30">
                                        <th class="px-4 py-1.5 bg-rose-50/50 dark:bg-rose-900/10"></th>
                                        <th class="px-4 py-1.5"></th>
                                        <th class="px-4 py-1.5"></th>
                                        <th class="px-4 py-1.5 text-right"></th>
                                        <th class="px-4 py-1.5 text-right bg-slate-100/50 dark:bg-slate-800/50">Proposed</th>
                                        <th class="px-4 py-1.5 text-right bg-slate-100/50 dark:bg-slate-800/50">System</th>
                                        <th class="px-4 py-1.5 text-center bg-slate-100/50 dark:bg-slate-800/50">Gap</th>
                                        <th class="px-4 py-1.5 text-right bg-indigo-50/50 dark:bg-indigo-900/20">CSV Total</th>
                                        <th class="px-4 py-1.5 text-right bg-indigo-50/50 dark:bg-indigo-900/20">System</th>
                                        <th class="px-4 py-1.5"></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 bg-white dark:bg-darkCard">
                                    @foreach($this->filteredAuditResults as $row)
                                        <tr wire:key="audit-row-{{ $row['member_id'] }}"
                                            class="hover:bg-slate-50/80 dark:hover:bg-slate-800/50 transition-colors {{ in_array($row['member_id'], $excludedMemberIds) ? 'opacity-40 grayscale bg-slate-50' : '' }}">
                                            <td class="px-4 py-3 text-center bg-rose-50/20 dark:bg-rose-900/5 border-r border-rose-100 dark:border-rose-900/20">
                                                <input type="checkbox" wire:model.live="excludedMemberIds"
                                                    value="{{ $row['member_id'] }}"
                                                    class="w-4 h-4 text-rose-600 rounded border-slate-300 focus:ring-rose-500 cursor-pointer">
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="flex items-center gap-2">
                                                    <div>
                                                        <div class="flex items-center gap-2">
                                                            <p class="font-extrabold text-slate-800 dark:text-slate-200">
                                                                {{ $row['name'] }}
                                                            </p>
                                                            <button wire:click="openDetailModal({{ $row['member_id'] }})"
                                                                class="text-slate-400 hover:text-indigo-600 transition-colors"
                                                                title="Lihat Detail Rincian CSV">
                                                                <i class='bx bx-search-alt-2 text-base'></i>
                                                            </button>
                                                        </div>
                                                        <div class="flex items-center gap-1.5 mt-0.5">
                                                            <span class="text-[10px] text-slate-400 font-mono">ID: {{ $row['member_id'] }}</span>
                                                            @if($row['is_coop'])
                                                                <span class="text-[9px] bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 px-1.5 py-0.5 rounded font-bold border border-emerald-100 dark:border-emerald-800">COOP</span>
                                                            @else
                                                                <span class="text-[9px] bg-slate-100 dark:bg-slate-800 text-slate-500 px-1.5 py-0.5 rounded font-bold border border-slate-200 dark:border-slate-700">RETAIL</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 max-w-[180px]">
                                                @if(!empty($row['mapped_names']))
                                                    <div class="flex flex-wrap gap-1">
                                                        @foreach($row['mapped_names'] as $csvName)
                                                            <span class="px-1.5 py-0.5 bg-amber-50 dark:bg-amber-950/40 text-amber-800 dark:text-amber-300 rounded text-[9px] font-mono border border-amber-200 dark:border-amber-900 truncate max-w-full block" title="{{ $csvName }}">
                                                                {{ $csvName }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <span class="text-slate-300 italic text-xs">-</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-right font-mono text-slate-400">
                                                {{ \Carbon\Carbon::parse($row['join_date'])->format('d M Y') }}
                                            </td>

                                            {{-- WAJIB --}}
                                            <td class="px-4 py-3 text-right font-mono font-bold text-emerald-600 dark:text-emerald-400 bg-slate-50/50 dark:bg-slate-800/30">
                                                Rp {{ number_format($row['proposed_wajib'], 0, ',', '.') }}
                                                <div class="text-[9px] font-normal text-slate-400">CSV: +{{ number_format($row['actual_payroll']) }}</div>
                                            </td>
                                            <td class="px-4 py-3 text-right font-mono text-slate-600 dark:text-slate-300 bg-slate-50/50 dark:bg-slate-800/30">
                                                Rp {{ number_format($row['current_wajib'], 0, ',', '.') }}
                                            </td>
                                            <td class="px-4 py-3 text-center bg-slate-50/50 dark:bg-slate-800/30">
                                                @if($row['gap'] == 0)
                                                    <span class="px-2 py-0.5 rounded-full bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 font-extrabold text-[10px]">✓ Match</span>
                                                @else
                                                    <span class="px-2 py-0.5 rounded-full bg-rose-50 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 font-extrabold text-[10px]">
                                                        {{ $row['gap'] > 0 ? '+' : '' }}{{ number_format($row['gap']) }}
                                                    </span>
                                                @endif
                                            </td>

                                            {{-- SUKARELA --}}
                                            <td class="px-4 py-3 text-right font-mono font-bold text-indigo-600 dark:text-indigo-400 bg-indigo-50/10 dark:bg-indigo-900/10">
                                                Rp {{ number_format($row['actual_sukarela'], 0, ',', '.') }}
                                            </td>
                                            <td class="px-4 py-3 text-right font-mono text-slate-600 dark:text-slate-300 bg-indigo-50/10 dark:bg-indigo-900/10">
                                                Rp {{ number_format($row['current_sukarela'], 0, ',', '.') }}
                                                <div class="text-[9px] font-normal {{ abs($row['gap_sukarela']) < 100 ? 'text-emerald-500' : 'text-rose-500' }}">
                                                    Gap: {{ number_format($row['gap_sukarela']) }}
                                                </div>
                                            </td>

                                            <td class="px-4 py-3 text-center">
                                                <button wire:click="syncBalance({{ $row['member_id'] }})"
                                                    class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-[10px] font-extrabold rounded-xl transition-all shadow-sm flex items-center gap-1 mx-auto"
                                                    wire:confirm="🔄 REBUILD HISTORY MEMBER?\n\nMember: {{ $row['name'] }}\n\nSistem akan menghapus history Wajib/Sukarela lama dan membuat ulang sesuai data Payroll. Lanjut?">
                                                    <i class='bx bx-refresh'></i> Rebuild
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-12 bg-slate-50 dark:bg-slate-800/30 rounded-2xl border border-dashed border-slate-200 dark:border-slate-800">
                            <p class="text-slate-400 text-xs">Klik "Generate / Refresh Report" untuk memulai analisis rekonsiliasi data.</p>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    {{-- Detail Modal --}}
    @if($showDetailModal && $detailMember)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-slate-950/60 backdrop-blur-sm" wire:click="closeDetailModal"></div>

            <div class="relative w-full max-w-4xl bg-white dark:bg-darkCard rounded-3xl shadow-2xl overflow-hidden max-h-[90vh] flex flex-col border border-slate-100 dark:border-slate-800">
                {{-- Modal Header --}}
                <div class="px-6 py-4 bg-slate-900 text-white flex items-center justify-between">
                    <div>
                        <h3 class="font-extrabold text-base flex items-center gap-2">
                            <i class='bx bx-spreadsheet text-indigo-400'></i> Detail Rincian Import CSV Payroll
                        </h3>
                        <p class="text-xs text-slate-400 mt-0.5">{{ $detailMember->name }} (ID: {{ $detailMember->id }} | No. Anggota: {{ $detailMember->nomorAnggota }})</p>
                    </div>
                    <button wire:click="closeDetailModal" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors">
                        <i class='bx bx-x text-xl'></i>
                    </button>
                </div>

                {{-- Modal Body Table --}}
                <div class="flex-1 overflow-y-auto p-0">
                    <table class="w-full text-xs text-left">
                        <thead class="bg-slate-50 dark:bg-slate-800 text-slate-500 dark:text-slate-400 uppercase font-extrabold sticky top-0 border-b border-slate-200 dark:border-slate-700">
                            <tr>
                                <th class="px-4 py-3 w-10">No</th>
                                <th class="px-4 py-3 w-24">Periode</th>
                                <th class="px-4 py-3">Nama & Uraian Original CSV</th>
                                <th class="px-4 py-3">Split Category</th>
                                <th class="px-4 py-3 text-right w-32">Total Amount</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800 bg-white dark:bg-darkCard">
                            @forelse($detailRows as $r)
                                <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/50 transition-colors {{ $r->is_ignored ? 'bg-rose-50/30 dark:bg-rose-950/20' : '' }}">
                                    <td class="px-4 py-3 font-mono text-slate-400">{{ $loop->iteration }}</td>
                                    <td class="px-4 py-3 font-mono font-bold text-indigo-600 dark:text-indigo-400">{{ $r->period }}</td>
                                    <td class="px-4 py-3">
                                        <div class="font-bold text-slate-800 dark:text-slate-200">{{ $r->raw_name }}</div>
                                        <div class="text-[10px] text-slate-400 font-mono mt-0.5">{{ $r->original_uraian }}</div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex flex-wrap gap-1">
                                            @if($r->simpok > 0)
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300">
                                                    SIMPOK Rp {{ number_format($r->simpok) }}
                                                </span>
                                            @endif
                                            @if($r->simwa > 0)
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">
                                                    SIMWA Rp {{ number_format($r->simwa) }}
                                                </span>
                                            @endif
                                            @if($r->sukarela > 0)
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-100 text-indigo-800 dark:bg-indigo-900/40 dark:text-indigo-300">
                                                    SUKARELA Rp {{ number_format($r->sukarela) }}
                                                </span>
                                            @endif
                                            @if($r->ignored > 0)
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-600 dark:bg-rose-900/40 dark:text-rose-300 line-through opacity-70">
                                                    ANGSURAN Rp {{ number_format($r->ignored) }}
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-right font-mono font-bold text-slate-800 dark:text-white">
                                        Rp {{ number_format($r->total_amount, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                        Tidak ada data import CSV yang terhubung ke member ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Modal Footer --}}
                <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/80 border-t border-slate-200 dark:border-slate-700 flex items-center justify-between">
                    <span class="text-xs text-slate-400">Total {{ count($detailRows) }} baris data CSV</span>
                    <button wire:click="closeDetailModal" class="px-5 py-2 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 text-xs font-bold rounded-xl hover:bg-slate-50 transition-colors">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>