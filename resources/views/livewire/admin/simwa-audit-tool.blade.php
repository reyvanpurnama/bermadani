<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Audit Simpanan Wajib & Sukarela</h1>
            <p class="text-xs text-slate-500">Import CSV payroll, mapping nama, dan rekonsiliasi data simpanan (Wajib &
                Sukarela).</p>
        </div>
    </div>

    {{-- Flash Notifications --}}


    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-darkCard p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Total Records</h3>
            <p class="text-3xl font-bold text-slate-900 dark:text-white">{{ number_format($stats['total_imports']) }}
            </p>
        </div>
        <div class="bg-white dark:bg-darkCard p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
            <h3 class="text-xs font-bold text-rose-500 uppercase tracking-widest mb-2">Unmapped Names</h3>
            <p class="text-3xl font-bold text-rose-600">{{ number_format($stats['unprocessed']) }} <span
                    class="text-xs font-normal text-rose-400">Orang</span></p>
        </div>
        <div class="bg-white dark:bg-darkCard p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
            <h3 class="text-xs font-bold text-emerald-500 uppercase tracking-widest mb-2">Mapped Members</h3>
            <p class="text-3xl font-bold text-emerald-600">{{ number_format($stats['processed']) }} <span
                    class="text-xs font-normal text-emerald-400">Orang</span></p>
        </div>
    </div>

    {{-- Main Content --}}
    <div
        class="bg-white dark:bg-darkCard rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">
        {{-- Tabs --}}
        <div class="flex border-b border-slate-100 dark:border-slate-700">
            <button wire:click="$set('activeTab', 'upload')"
                class="px-6 py-4 text-sm font-bold border-b-2 transition-colors {{ $activeTab === 'upload' ? 'border-primary text-primary' : 'border-transparent text-slate-500 hover:text-slate-700' }}">
                1. Upload CSV
            </button>
            <button wire:click="$set('activeTab', 'mapping')"
                class="px-6 py-4 text-sm font-bold border-b-2 transition-colors {{ $activeTab === 'mapping' ? 'border-primary text-primary' : 'border-transparent text-slate-500 hover:text-slate-700' }}">
                2. Name Mapping
            </button>
            <button wire:click="$set('activeTab', 'reconciliation')"
                class="px-6 py-4 text-sm font-bold border-b-2 transition-colors {{ $activeTab === 'reconciliation' ? 'border-primary text-primary' : 'border-transparent text-slate-500 hover:text-slate-700' }}">
                3. Rekonsiliasi & Sync
            </button>
        </div>

        <div class="p-6">
            {{-- Tab 1: Upload --}}
            @if($activeTab === 'upload')
                <div class="space-y-8">
                    {{-- Upload Area --}}
                    <div class="text-center py-12 border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-2xl bg-slate-50 dark:bg-slate-800/50"
                        x-data="{ isDropping: false }" @dragover.prevent="isDropping = true"
                        @dragleave.prevent="isDropping = false" @drop.prevent="isDropping = false"
                        :class="{ 'border-primary bg-primary/5': isDropping }">

                        <input type="file" wire:model="csvFiles" multiple class="hidden" id="csvInput">

                        <label for="csvInput" class="cursor-pointer block space-y-4">
                            <div
                                class="w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-400 mx-auto flex items-center justify-center">
                                <i class='bx bx-cloud-upload text-3xl'></i>
                            </div>
                            <div>
                                <p class="font-bold text-slate-700 dark:text-slate-200">Klik untuk upload CSV Payroll per
                                    Bulan</p>
                                <p class="text-xs text-slate-400 mt-1">Sistem otomatis mendeteksi Bulan & Tahun dari nama
                                    file (contoh: 04-april-2024.csv)</p>
                            </div>
                        </label>

                        @if(count($csvFiles) > 0)
                            <div class="mt-8 space-y-2 max-w-md mx-auto">
                                <p class="text-sm font-bold text-emerald-600">{{ count($csvFiles) }} file dipilih</p>
                                <button wire:click="processUploads"
                                    class="px-6 py-2 bg-primary text-white text-sm font-bold rounded-xl shadow-lg hover:bg-indigo-700 transition-colors">
                                    <span wire:loading.remove wire:target="processUploads">Mulai Proses Import / Update</span>
                                    <span wire:loading wire:target="processUploads">Memproses...</span>
                                </button>
                            </div>
                        @endif
                    </div>

                    {{-- Managed Periods List --}}
                    @if(count($this->importedPeriods) > 0)
                        <div>
                            <h3 class="text-sm font-bold text-slate-700 dark:text-slate-200 mb-4 flex items-center gap-2">
                                <i class='bx bx-calendar'></i> Data Payroll Ter-Import
                            </h3>
                            <div class="overflow-hidden border border-slate-200 dark:border-slate-700 rounded-xl">
                                <table class="w-full text-left text-sm">
                                    <thead
                                        class="bg-slate-50 dark:bg-slate-800 text-slate-500 uppercase text-[10px] font-bold tracking-wider">
                                        <tr>
                                            <th class="px-4 py-3">Periode</th>
                                            <th class="px-4 py-3">Filename</th>
                                            <th class="px-4 py-3 text-right">Total Rows</th>
                                            <th class="px-4 py-3 text-right">Total Amount</th>
                                            <th class="px-4 py-3 text-right">Imported At</th>
                                            <th class="px-4 py-3 text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700 bg-white dark:bg-darkCard">
                                        @foreach($this->importedPeriods as $period)
                                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50">
                                                <td class="px-4 py-3 font-mono font-bold text-slate-700 dark:text-slate-300">
                                                    {{ $period->period }}
                                                </td>
                                                <td class="px-4 py-3 text-xs text-slate-500">
                                                    {{ $period->filename }}
                                                </td>
                                                <td class="px-4 py-3 text-right font-mono">
                                                    {{ number_format($period->total_rows) }}
                                                </td>
                                                <td class="px-4 py-3 text-right font-mono text-emerald-600">
                                                    {{ number_format($period->total_amount) }}
                                                </td>
                                                <td class="px-4 py-3 text-right text-xs text-slate-400">
                                                    {{ \Carbon\Carbon::parse($period->imported_at)->diffForHumans() }}
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    <button wire:click="deletePeriod('{{ $period->period }}')"
                                                        class="text-rose-500 hover:text-rose-700 transition-colors"
                                                        onclick="return confirm('Hapus semua data impor untuk periode {{ $period->period }}?')">
                                                        <i class='bx bx-trash'></i>
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

            {{-- Tab 2: Mapping --}}
            @if($activeTab === 'mapping')
                <div class="space-y-4">
                    @if(session()->has('message'))
                        <div class="p-3 bg-emerald-50 text-emerald-600 text-sm font-bold rounded-xl flex items-center gap-2">
                            <i class='bx bx-check-circle'></i> {{ session('message') }}
                        </div>
                    @endif

                    @if($unmappedNames->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm">
                                <thead
                                    class="bg-slate-50 dark:bg-slate-800 text-slate-500 uppercase text-[10px] font-bold tracking-wider">
                                    <tr>
                                        <th class="px-4 py-3 w-10 text-center">#</th>
                                        <th class="px-4 py-3">Nama di CSV (Raw)</th>
                                        <th class="px-4 py-3">Cari Member Asli</th>
                                        <th class="px-4 py-3 w-32 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                                    @foreach($unmappedNames as $index => $item)
                                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50"
                                            wire:key="row-{{ md5($item->raw_name) }}">
                                            <td class="px-4 py-3 text-center text-xs font-bold text-slate-400">
                                                {{ ($unmappedNames->currentPage() - 1) * $unmappedNames->perPage() + $loop->iteration }}
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="font-mono font-bold text-slate-700 dark:text-slate-200">
                                                    {{ $item->raw_name }}</div>
                                                <div class="text-[10px] text-slate-400 mt-1 uppercase tracking-tighter">
                                                    Pertama Muncul: <span
                                                        class="bg-slate-100 dark:bg-slate-800 px-1 rounded font-bold">{{ $item->earliest_period }}</span>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3">
                                                {{-- Filter search results based on the earliest period --}}
                                                <livewire:components.member-search-select :key="'search-' . md5($item->raw_name)"
                                                    :wire:key="'search-'.md5($item->raw_name)" :extra-data="$item->raw_name"
                                                    :joined-before="$item->earliest_period" />
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                {{-- Button triggered by event --}}
                                                <button
                                                    class="px-3 py-1.5 bg-slate-100 text-slate-400 text-xs font-bold rounded-lg cursor-not-allowed">
                                                    Pilih Member
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="pt-4">
                            {{ $unmappedNames->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div
                                class="w-16 h-16 rounded-full bg-emerald-50 text-emerald-500 mx-auto flex items-center justify-center mb-4">
                                <i class='bx bx-check-double text-3xl'></i>
                            </div>
                            <h3 class="font-bold text-slate-800 dark:text-white">Semua Nama Sudah Terpetakan!</h3>
                            <p class="text-slate-400 text-sm">Semua data import sudah memiliki jodoh di database member.</p>
                        </div>
                    @endif
            @endif

                {{-- Tab 3: Reconciliation --}}
                @if($activeTab === 'reconciliation')
                    <div class="space-y-6">
                        {{-- NUCLEAR OPTION: One-Click Cleanup --}}
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            {{-- Controls --}}
                            <div
                                class="bg-white dark:bg-darkCard rounded-2xl p-6 border border-slate-200 dark:border-slate-700 h-full">
                                <h3 class="text-sm font-bold text-slate-800 dark:text-white mb-4 flex items-center gap-2">
                                    <i class='bx bx-slider-alt'></i> Pengaturan Cleanup
                                </h3>
                                <div class="space-y-3">
                                    <label
                                        class="flex items-center gap-3 p-3 rounded-xl border border-slate-100 dark:border-slate-700 cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                                        <input type="checkbox" wire:model="processWajib"
                                            class="w-5 h-5 text-emerald-600 rounded focus:ring-emerald-500 border-gray-300">
                                        <div>
                                            <p class="text-sm font-bold text-slate-700 dark:text-slate-200">Rebuild Simpanan
                                                Wajib</p>
                                            <p class="text-[10px] text-slate-400">Hapus & buat ulang history simpanan wajib
                                                (Rp 50rb/bln)</p>
                                        </div>
                                    </label>
                                    <label
                                        class="flex items-center gap-3 p-3 rounded-xl border border-slate-100 dark:border-slate-700 cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                                        <input type="checkbox" wire:model="processSukarela"
                                            class="w-5 h-5 text-indigo-600 rounded focus:ring-indigo-500 border-gray-300">
                                        <div>
                                            <p class="text-sm font-bold text-slate-700 dark:text-slate-200">Rebuild Simpanan
                                                Sukarela</p>
                                            <p class="text-[10px] text-slate-400">Hapus & buat ulang history sukarela sesuai
                                                nominal CSV Payroll</p>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            {{-- Action Button --}}
                            <div
                                class="lg:col-span-2 bg-gradient-to-r from-indigo-600 to-violet-600 rounded-2xl p-6 text-white shadow-2xl flex flex-col justify-center">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1 pr-6">
                                        <h3 class="text-lg font-bold mb-1">🧹 Final Audit & History Rebuild</h3>
                                        <p class="text-sm text-indigo-100 mb-2">
                                            Proses ini akan <strong class="text-white">MENGHAPUS (Reset)</strong> history
                                            simpanan lama dan membangun ulang berdasarkan data CSV Import yang sudah Anda
                                            upload.
                                            Saldo member akan otomatis disesuaikan.
                                        </p>

                                        {{-- Loading Indicator --}}
                                        <div wire:loading wire:target="cleanupAllSimwa" class="mt-4 space-y-3">
                                            <div class="flex items-center gap-3">
                                                <div class="flex gap-1">
                                                    <div class="w-2 h-2 bg-white rounded-full animate-bounce"
                                                        style="animation-delay: 0ms"></div>
                                                    <div class="w-2 h-2 bg-white rounded-full animate-bounce"
                                                        style="animation-delay: 150ms"></div>
                                                    <div class="w-2 h-2 bg-white rounded-full animate-bounce"
                                                        style="animation-delay: 300ms"></div>
                                                </div>
                                                <span class="text-sm font-mono tracking-tighter italic">Processing
                                                    cleanup... (Excluded: {{ count($excludedMemberIds) }} members)</span>
                                            </div>
                                        </div>
                                    </div>
                                    <button wire:click="cleanupAllSimwa" wire:loading.attr="disabled"
                                        class="px-8 py-4 bg-white text-indigo-600 font-bold rounded-xl shadow-lg hover:shadow-xl hover:scale-105 transition-all flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed shrink-0"
                                        onclick="return confirm('⚠️ KONFIRMASI FINAL ⚠️\n\nAnda akan melakukan REBUILD HISTORY untuk {{ count($auditResults) - count($excludedMemberIds) }} anggota.\n\n- Data Angsuran/Pinjaman: AMAN (TIDAK DIHAPUS)\n- Simpanan Wajib: {{ $processWajib ? 'DI-RESET' : 'DIBIARKAN' }}\n- Simpanan Sukarela: {{ $processSukarela ? 'DI-RESET' : 'DIBIARKAN' }}\n\nLanjutkan?')">
                                        <i class='bx bxs-flask-vial text-2xl'></i>
                                        <div class="text-left leading-tight">
                                            <div class="text-sm font-bold">RUN FULL CLEANUP</div>
                                            <div class="text-[10px] opacity-75">Sikat & Rapihkan Semua</div>
                                        </div>
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Manual Controls --}}
                        <div
                            class="flex justify-between items-center bg-slate-50 dark:bg-slate-800/50 p-4 rounded-xl border border-dashed border-slate-200 dark:border-slate-700">
                            <div class="flex items-center gap-6">
                                <div>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Step 1:
                                        Preview Data</p>
                                    <button wire:click="generateReconciliation"
                                        class="px-5 py-2.5 bg-slate-800 text-white text-xs font-bold rounded-lg shadow hover:bg-slate-900 transition-colors flex items-center gap-2">
                                        <i class='bx bx-spreadsheet'></i>
                                        <span wire:loading.remove wire:target="generateReconciliation">Generate/Refresh
                                            Report</span>
                                        <span wire:loading wire:target="generateReconciliation">Updating...</span>
                                    </button>
                                </div>

                                @if(count($auditResults) > 0)
                                    <div class="h-10 w-[1px] bg-slate-200 dark:bg-slate-700"></div>
                                    <div>
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Anggota
                                            Terdeteksi</p>
                                        <p class="text-xl font-black text-slate-900 dark:text-white">{{ count($auditResults) }}
                                            <span class="text-[10px] font-normal text-slate-500">Orang</span></p>
                                    </div>
                                @endif
                            </div>

                            <div class="text-right">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Step 2:
                                    Execution</p>
                                <p class="text-xs text-slate-500 italic">Gunakan tombol "Run Full Cleanup" di atas ↑</p>
                            </div>
                        </div>

                        @if(count($auditResults) > 0)
                            <div class="overflow-x-auto border border-slate-200 dark:border-slate-700 rounded-xl">
                                <table class="w-full text-left text-sm">
                                    <thead
                                        class="bg-slate-50 dark:bg-slate-800 text-slate-500 uppercase text-[10px] font-bold tracking-wider">
                                        <tr>
                                            <th
                                                class="px-4 py-3 text-center w-12 bg-rose-50/50 dark:bg-rose-900/10 rounded-tl-xl">
                                                <i class='bx bx-block text-rose-500 text-lg'
                                                    title="Exclude / Lewati Anggota Ini"></i></th>
                                            <th class="px-4 py-3">Member</th>
                                            <th class="px-4 py-3 w-48">CSV Source Names</th>
                                            <th class="px-4 py-3 text-right">Join Date</th>
                                            <th class="px-4 py-3 text-center bg-slate-100/50 dark:bg-slate-800/50" colspan="3">
                                                SIMPANAN WAJIB (MANDATORY)</th>
                                            <th class="px-4 py-3 text-center bg-indigo-50/50 dark:bg-indigo-900/20" colspan="2">
                                                SIMPANAN SUKARELA (VOLUNTARY)</th>
                                            <th class="px-4 py-3 text-center">Action</th>
                                        </tr>
                                        <tr class="text-[9px] border-b border-slate-100 dark:border-slate-700">
                                            <th class="px-4 bg-rose-50/50 dark:bg-rose-900/10"></th>
                                            <th class="px-4"></th>
                                            <th class="px-4"></th>
                                            <th class="px-4 text-right"></th>
                                            <th class="px-4 py-2 text-right bg-slate-100/50 dark:bg-slate-800/50">Proposed</th>
                                            <th class="px-4 py-2 text-right bg-slate-100/50 dark:bg-slate-800/50">System</th>
                                            <th class="px-4 py-2 text-center bg-slate-100/50 dark:bg-slate-800/50">Gap</th>
                                            <th class="px-4 py-2 text-right bg-indigo-50/50 dark:bg-indigo-900/20">CSV Total
                                            </th>
                                            <th class="px-4 py-2 text-right bg-indigo-50/50 dark:bg-indigo-900/20">System</th>
                                            <th class="px-4 py-2"></th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700 bg-white dark:bg-darkCard">
                                        @foreach($auditResults as $row)
                                                                            <tr wire:key="audit-row-{{ $row['member_id'] }}"
                                                                                class="hover:bg-slate-50 dark:hover:bg-slate-800/50 {{ in_array($row['member_id'], $excludedMemberIds) ? 'opacity-50 grayscale bg-slate-50 dark:bg-slate-800' : '' }}">
                                                                                <td
                                                                                    class="px-4 py-3 text-center bg-rose-50/20 dark:bg-rose-900/5 border-r border-rose-100 dark:border-rose-900/20">
                                                                                    <input type="checkbox" wire:model.live="excludedMemberIds"
                                                                                        value="{{ $row['member_id'] }}"
                                                                                        class="w-4 h-4 text-rose-600 rounded border-gray-300 focus:ring-rose-500 cursor-pointer shadow-sm">
                                                                                </td>
                                                                                <td class="px-4 py-3">
                                                                                    <div class="flex items-center gap-2">
                                                                                        <div>
                                                                                            <div class="flex items-center gap-2">
                                                                                                <p class="font-bold text-slate-700 dark:text-slate-200">
                                                                                                    {{ $row['name'] }}</p>
                                                                                                <button wire:click="openDetailModal({{ $row['member_id'] }})"
                                                                                                    class="text-slate-400 hover:text-indigo-500 transition-colors"
                                                                                                    title="Lihat Detail CSV">
                                                                                                    <i class='bx bx-search-alt-2'></i>
                                                                                                </button>
                                                                                            </div>
                                                                                            <div class="flex items-center gap-2">
                                                                                                <span class="text-[10px] text-slate-400 font-mono">ID:
                                                                                                    {{ $row['member_id'] }}</span>
                                                                                                @if($row['is_coop'])
                                                                                                    <span
                                                                                                        class="text-[9px] bg-emerald-50 text-emerald-600 px-1 rounded border border-emerald-100">COOP</span>
                                                                                                @else
                                                                                                    <span
                                                                                                        class="text-[9px] bg-slate-50 text-slate-500 px-1 rounded border border-slate-100">RETAIL</span>
                                                                                                @endif
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </td>
                                                                                <td class="px-4 py-3 max-w-[200px]">
                                                                                    @if(!empty($row['mapped_names']))
                                                                                        <div class="flex flex-wrap gap-1">
                                                                                            @foreach($row['mapped_names'] as $csvName)
                                                                                                <span
                                                                                                    class="px-1.5 py-0.5 bg-yellow-50 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400 rounded text-[10px] font-mono border border-yellow-100 dark:border-yellow-700 truncate max-w-full block"
                                                                                                    title="{{ $csvName }}">
                                                                                                    {{ $csvName }}
                                                                                                </span>
                                                                                            @endforeach
                                                                                        </div>
                                                                                    @else
                                                                                        <span class="text-slate-300 italic text-xs">-</span>
                                                                                    @endif
                                                                                </td>
                                                                                <td class="px-4 py-3 text-right text-xs font-mono text-slate-400">
                                                                                    {{ \Carbon\Carbon::parse($row['join_date'])->format('d M Y') }}
                                                                                </td>
                                                                                {{-- WAJIB --}}
                                                                                <td
                                                                                    class="px-4 py-3 text-right font-mono font-bold text-emerald-600 bg-slate-100/30 dark:bg-slate-800/30">
                                                                                    {{ number_format($row['proposed_wajib']) }}
                                                                                    <div class="text-[9px] font-normal text-slate-400">CSV:
                                                                                        +{{ number_format($row['actual_payroll']) }}</div>
                                                                                </td>
                                                                                <td
                                                                                    class="px-4 py-3 text-right font-mono text-slate-600 dark:text-slate-300 bg-slate-100/30 dark:bg-slate-800/30">
                                                                                    {{ number_format($row['current_wajib']) }}
                                                                                </td>
                                                                                <td class="px-4 py-3 text-center bg-slate-100/30 dark:bg-slate-800/30">
                                                                                    @if($row['gap'] == 0)
                                                                                        <i class='bx bxs-check-circle text-emerald-500 text-lg'></i>
                                                                                    @else
                                                                                        <span
                                                                                            class="px-2 py-1 bg-rose-100 text-rose-700 text-[10px] font-bold rounded-full border border-rose-200">
                                                                                            {{ $row['gap'] > 0 ? '+' : '' }}{{ number_format($row['gap']) }}
                                                                                        </span>
                                                                                    @endif
                                                                                </td>
                                                                                {{-- SUKARELA --}}
                                                                                <td
                                                                                    class="px-4 py-3 text-right font-mono font-bold text-indigo-600 dark:text-indigo-400 bg-indigo-50/20 dark:bg-indigo-900/10">
                                                                                    {{ number_format($row['actual_sukarela']) }}
                                                                                </td>
                                                                                <td
                                                                                    class="px-4 py-3 text-right font-mono text-slate-600 dark:text-slate-300 bg-indigo-50/20 dark:bg-indigo-900/10">
                                                                                    {{ number_format($row['current_sukarela']) }}
                                                                                    <div
                                                                                        class="text-[9px] font-normal {{ abs($row['gap_sukarela']) < 100 ? 'text-emerald-500' : 'text-rose-500' }}">
                                                                                        Gap: {{ number_format($row['gap_sukarela']) }}
                                                                                    </div>
                                                                                </td>
                                                                                <td class="px-4 py-3 text-center">
                                                                                    <button wire:click="syncBalance({{ $row['member_id'] }})"
                                                                                        class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-[10px] font-bold rounded-lg transition-all shadow-sm hover:shadow-md flex items-center gap-1 mx-auto"
                                                                                        wire:confirm="🔄 REBUILD HISTORY (WAJIB & SUKARELA)?

                                            Member: {{ $row['name'] }}

                                            Proses ini akan:
                                            1. Hapus history Wajib & Sukarela lama
                                            2. Buat ulang history detail dari Payroll

                                            Lanjut?">
                                                                                        <i class='bx bx-refresh'></i>
                                                                                        Rebuild History
                                                                                    </button>
                                                                                </td>
                                                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-12 bg-slate-50 dark:bg-slate-800/50 rounded-xl">
                                <p class="text-slate-500">Klik "Generate Report" untuk memulai analisis selisih data.</p>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        {{-- Detail Modal --}}
        @if($showDetailModal && $detailMember)
            <div class="fixed inset-0 z-[60] flex items-center justify-center p-4">
                {{-- Backdrop --}}
                <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" wire:click="closeDetailModal"></div>

                {{-- Modal Content --}}
                <div
                    class="relative w-full max-w-4xl bg-white dark:bg-slate-800 rounded-2xl shadow-xl overflow-hidden max-h-[90vh] flex flex-col">
                    <div
                        class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between">
                        <div>
                            <h3 class="font-bold text-lg">Detail Import CSV</h3>
                            <p class="text-sm text-slate-500">{{ $detailMember->name }} ({{ $detailMember->nomorAnggota }})
                            </p>
                        </div>
                        <button wire:click="closeDetailModal"
                            class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                            <i class='bx bx-x text-2xl'></i>
                        </button>
                    </div>

                    <div class="flex-1 overflow-y-auto p-0">
                        <table class="w-full text-sm">
                            <thead
                                class="bg-slate-100 dark:bg-slate-700/50 text-xs text-slate-500 uppercase font-medium sticky top-0">
                                <tr>
                                    <th class="px-4 py-3 text-left w-10">No</th>
                                    <th class="px-4 py-3 text-left">Period</th>
                                    <th class="px-4 py-3 text-left">Nama di CSV</th>
                                    <th class="px-4 py-3 text-left">Uraian Asli</th>
                                    <th class="px-4 py-3 text-center">Diproses Sebagai</th>
                                    <th class="px-4 py-3 text-right">Nominal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                                @forelse($detailRows as $r)
                                    @php
                                        // Determine what this row was processed as
                                        $processedAs = 'OTHER';
                                        $badgeClass = 'bg-slate-100 text-slate-500 dark:bg-slate-700 dark:text-slate-400';
                                        $lowerUraian = strtolower($r->raw_uraian);

                                        // Priority 1: AUTO-SPLIT records (created by import logic)
                                        if (str_contains($r->raw_uraian, 'AUTO-SPLIT SIMPOK')) {
                                            $processedAs = 'SIMPOK';
                                            $badgeClass = 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400';
                                        } elseif (str_contains($r->raw_uraian, 'AUTO-SPLIT SIMWA')) {
                                            $processedAs = 'SIMWA';
                                            $badgeClass = 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400';
                                        } elseif (str_contains($r->raw_uraian, 'AUTO-SPLIT SUKARELA') || str_contains($r->raw_uraian, 'AUTO-DETECT EXTRA')) {
                                            $processedAs = 'SUKARELA';
                                            $badgeClass = 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400';
                                        }
                                        // Priority 2: Angsuran (IGNORED) - check BEFORE tabungan/sukarela!
                                        elseif (str_contains($lowerUraian, 'angsuran') || str_contains($lowerUraian, 'angs ')) {
                                            $processedAs = 'IGNORED';
                                            $badgeClass = 'bg-rose-100 text-rose-600 dark:bg-rose-900/30 dark:text-rose-400';
                                        }
                                        // Priority 3: Pure SIMWA (no angsuran)
                                        elseif (preg_match('/\bsimwa\b/i', $r->raw_uraian)) {
                                            $processedAs = 'SIMWA';
                                            $badgeClass = 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400';
                                        }
                                        // Priority 4: Pure Tabungan/Sukarela (no angsuran)
                                        elseif (str_contains($lowerUraian, 'tabungan') || str_contains($lowerUraian, 'sukarela')) {
                                            $processedAs = 'SUKARELA';
                                            $badgeClass = 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400';
                                        }

                                        // Extract original uraian (remove AUTO- prefix if present)
                                        $originalUraian = $r->raw_uraian;
                                        if (preg_match('/^AUTO-[A-Z\s]+:\s*(.+)$/i', $r->raw_uraian, $m)) {
                                            $originalUraian = $m[1];
                                        }
                                    @endphp
                                    <tr
                                        class="hover:bg-slate-50 dark:hover:bg-slate-700/30 {{ $processedAs === 'IGNORED' ? 'opacity-50' : '' }}">
                                        <td class="px-4 py-3 font-mono text-slate-400 text-xs">{{ $loop->iteration }}</td>
                                        <td class="px-4 py-3 font-mono text-slate-600 dark:text-slate-400 text-xs">
                                            {{ $r->period }}</td>
                                        <td class="px-4 py-3 font-medium text-slate-700 dark:text-slate-200">{{ $r->raw_name }}
                                        </td>
                                        <td class="px-4 py-3 text-xs">
                                            <span
                                                class="px-2 py-1 rounded bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 font-mono">
                                                {{ $originalUraian }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="px-2 py-1 rounded text-[10px] font-bold uppercase {{ $badgeClass }}">
                                                {{ $processedAs }}
                                            </span>
                                        </td>
                                        <td
                                            class="px-4 py-3 text-right font-mono font-bold {{ $processedAs === 'IGNORED' ? 'text-slate-400 line-through' : 'text-emerald-600' }}">
                                            {{ number_format($r->amount) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-12 text-center text-slate-500">
                                            Tidak ada data import CSV yang terhubung ke member ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Summary Section --}}
                    @if($detailRows && count($detailRows) > 0)
                        @php
                            $totalSimwa = 0;
                            $countSimwa = 0;
                            $totalSukarela = 0;
                            $countSukarela = 0;
                            $totalSimpok = 0;
                            $countSimpok = 0;
                            $totalIgnored = 0;
                            $countIgnored = 0;

                            foreach ($detailRows as $row) {
                                $lowerUraian = strtolower($row->raw_uraian);

                                if (str_contains($row->raw_uraian, 'AUTO-SPLIT SIMPOK')) {
                                    $totalSimpok += $row->amount;
                                    $countSimpok++;
                                } elseif (str_contains($row->raw_uraian, 'AUTO-SPLIT SIMWA')) {
                                    $totalSimwa += $row->amount;
                                    $countSimwa++;
                                } elseif (str_contains($row->raw_uraian, 'AUTO-SPLIT SUKARELA') || str_contains($row->raw_uraian, 'AUTO-DETECT EXTRA')) {
                                    $totalSukarela += $row->amount;
                                    $countSukarela++;
                                } elseif (str_contains($lowerUraian, 'angsuran') || str_contains($lowerUraian, 'angs ')) {
                                    $totalIgnored += $row->amount;
                                    $countIgnored++;
                                } elseif (preg_match('/\bsimwa\b/i', $row->raw_uraian)) {
                                    $totalSimwa += $row->amount;
                                    $countSimwa++;
                                } elseif (str_contains($lowerUraian, 'tabungan') || str_contains($lowerUraian, 'sukarela')) {
                                    $totalSukarela += $row->amount;
                                    $countSukarela++;
                                }
                            }
                        @endphp
                        <div
                            class="px-6 py-4 bg-gradient-to-r from-slate-50 to-slate-100 dark:from-slate-800 dark:to-slate-800/50 border-t border-slate-200 dark:border-slate-700">
                            <div class="text-xs font-bold uppercase text-slate-500 mb-3">Ringkasan Transaksi</div>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                {{-- SIMWA --}}
                                <div
                                    class="bg-white dark:bg-slate-700/50 rounded-lg p-3 border border-emerald-200 dark:border-emerald-800">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span
                                            class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">SIMWA</span>
                                    </div>
                                    <div class="text-lg font-bold text-emerald-600 dark:text-emerald-400">Rp
                                        {{ number_format($totalSimwa) }}</div>
                                    <div class="text-xs text-slate-500">{{ $countSimwa }} bulan × Rp 50.000</div>
                                </div>

                                {{-- SUKARELA --}}
                                <div
                                    class="bg-white dark:bg-slate-700/50 rounded-lg p-3 border border-indigo-200 dark:border-indigo-800">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span
                                            class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400">SUKARELA</span>
                                    </div>
                                    <div class="text-lg font-bold text-indigo-600 dark:text-indigo-400">Rp
                                        {{ number_format($totalSukarela) }}</div>
                                    <div class="text-xs text-slate-500">{{ $countSukarela }} transaksi</div>
                                </div>

                                {{-- SIMPOK --}}
                                <div
                                    class="bg-white dark:bg-slate-700/50 rounded-lg p-3 border border-amber-200 dark:border-amber-800">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span
                                            class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">SIMPOK</span>
                                    </div>
                                    <div class="text-lg font-bold text-amber-600 dark:text-amber-400">Rp
                                        {{ number_format($totalSimpok) }}</div>
                                    <div class="text-xs text-slate-500">{{ $countSimpok }} transaksi</div>
                                </div>

                                {{-- IGNORED --}}
                                <div
                                    class="bg-white dark:bg-slate-700/50 rounded-lg p-3 border border-rose-200 dark:border-rose-800 opacity-60">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span
                                            class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-rose-100 text-rose-600 dark:bg-rose-900/30 dark:text-rose-400">IGNORED</span>
                                    </div>
                                    <div class="text-lg font-bold text-rose-500 dark:text-rose-400 line-through">Rp
                                        {{ number_format($totalIgnored) }}</div>
                                    <div class="text-xs text-slate-500">{{ $countIgnored }} angsuran</div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div
                        class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 border-t border-slate-200 dark:border-slate-700 text-right">
                        <button wire:click="closeDetailModal"
                            class="px-4 py-2 bg-white border border-slate-300 rounded-lg text-sm text-slate-600 hover:bg-slate-50 transition-colors">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>