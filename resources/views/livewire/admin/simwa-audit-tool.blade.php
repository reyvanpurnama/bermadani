<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Audit Simpanan Wajib & Sukarela</h1>
            <p class="text-xs text-slate-500">Import CSV payroll, mapping nama, dan rekonsiliasi data simpanan (Wajib & Sukarela).</p>
        </div>
    </div>

    {{-- Flash Notifications --}}
    @if (session()->has('message'))
        <div class="bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-700 text-emerald-800 dark:text-emerald-200 px-6 py-4 rounded-2xl flex items-center gap-3 animate-pulse">
            <i class='bx bx-check-circle text-2xl text-emerald-500'></i>
            <div>
                <p class="font-bold">Sukses!</p>
                <p class="text-sm">{{ session('message') }}</p>
            </div>
            <button wire:click="$refresh" class="ml-auto text-emerald-500 hover:text-emerald-700">
                <i class='bx bx-x text-xl'></i>
            </button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-rose-50 dark:bg-rose-900/30 border border-rose-200 dark:border-rose-700 text-rose-800 dark:text-rose-200 px-6 py-4 rounded-2xl flex items-center gap-3">
            <i class='bx bx-error-circle text-2xl text-rose-500'></i>
            <div>
                <p class="font-bold">Error!</p>
                <p class="text-sm">{{ session('error') }}</p>
            </div>
            <button wire:click="$refresh" class="ml-auto text-rose-500 hover:text-rose-700">
                <i class='bx bx-x text-xl'></i>
            </button>
        </div>
    @endif

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-darkCard p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Total Records</h3>
            <p class="text-3xl font-bold text-slate-900 dark:text-white">{{ number_format($stats['total_imports']) }}
            </p>
        </div>
        <div class="bg-white dark:bg-darkCard p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
            <h3 class="text-xs font-bold text-rose-500 uppercase tracking-widest mb-2">Unmapped Names</h3>
            <p class="text-3xl font-bold text-rose-600">{{ number_format($stats['unprocessed']) }} <span class="text-xs font-normal text-rose-400">Orang</span></p>
        </div>
        <div class="bg-white dark:bg-darkCard p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
            <h3 class="text-xs font-bold text-emerald-500 uppercase tracking-widest mb-2">Mapped Members</h3>
            <p class="text-3xl font-bold text-emerald-600">{{ number_format($stats['processed']) }} <span class="text-xs font-normal text-emerald-400">Orang</span></p>
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
                            <div class="w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-400 mx-auto flex items-center justify-center">
                                <i class='bx bx-cloud-upload text-3xl'></i>
                            </div>
                            <div>
                                <p class="font-bold text-slate-700 dark:text-slate-200">Klik untuk upload CSV Payroll per Bulan</p>
                                <p class="text-xs text-slate-400 mt-1">Sistem otomatis mendeteksi Bulan & Tahun dari nama file (contoh: 04-april-2024.csv)</p>
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
                                    <thead class="bg-slate-50 dark:bg-slate-800 text-slate-500 uppercase text-[10px] font-bold tracking-wider">
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
                                                <div class="font-mono font-bold text-slate-700 dark:text-slate-200">{{ $item->raw_name }}</div>
                                                <div class="text-[10px] text-slate-400 mt-1 uppercase tracking-tighter">
                                                    Pertama Muncul: <span class="bg-slate-100 dark:bg-slate-800 px-1 rounded font-bold">{{ $item->earliest_period }}</span>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3">
                                                {{-- Filter search results based on the earliest period --}}
                                                <livewire:components.member-search-select 
                                                    :key="'search-' . md5($item->raw_name)"
                                                    :wire:key="'search-'.md5($item->raw_name)" 
                                                    :extra-data="$item->raw_name"
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
                    <div class="bg-gradient-to-r from-indigo-600 to-violet-600 rounded-2xl p-6 text-white shadow-2xl">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <h3 class="text-lg font-bold mb-1">🧹 Final Audit & History Rebuild (Wajib & Sukarela)</h3>
                                <p class="text-sm text-indigo-100 mb-2">Pilih ini untuk membangun ulang mutasi Wajib (50rb/bln) dan Sukarela (Payroll) secara detail.</p>
                                
                                {{-- Loading Indicator --}}
                                <div wire:loading wire:target="cleanupAllSimwa" class="mt-4 space-y-3">
                                    <div class="flex items-center gap-3">
                                        <div class="flex gap-1">
                                            <div class="w-2 h-2 bg-white rounded-full animate-bounce" style="animation-delay: 0ms"></div>
                                            <div class="w-2 h-2 bg-white rounded-full animate-bounce" style="animation-delay: 150ms"></div>
                                            <div class="w-2 h-2 bg-white rounded-full animate-bounce" style="animation-delay: 300ms"></div>
                                        </div>
                                        <span class="text-sm font-mono tracking-tighter italic">Nuking old data & Rebuilding (Wajib + Sukarela) history...</span>
                                    </div>
                                </div>
                            </div>
                            <button wire:click="cleanupAllSimwa"
                                wire:loading.attr="disabled"
                                class="px-8 py-4 bg-white text-indigo-600 font-bold rounded-xl shadow-lg hover:shadow-xl hover:scale-105 transition-all flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed ml-4"
                                onclick="return confirm('⚠️ NUCLEAR OPTION ⚠️\n\n- SEMUA histori Wajib & Sukarela lama untuk member yang terdeteksi di atas akan DIHAPUS.\n- Histori akan dibangun ulang BERDASARKAN data Payroll CSV ini.\n- Saldo Member akan diupdate otomatis.\n\nLANJUTKAN?')">
                                <i class='bx bxs-flask-vial text-2xl'></i>
                                <div class="text-left leading-tight">
                                    <div class="text-sm font-bold">RUN FULL CLEANUP</div>
                                    <div class="text-[10px] opacity-75">Sikat & Rapihkan Semua</div>
                                </div>
                            </button>
                        </div>
                    </div>

                    {{-- Manual Controls --}}
                    <div class="flex justify-between items-center bg-slate-50 dark:bg-slate-800/50 p-4 rounded-xl border border-dashed border-slate-200 dark:border-slate-700">
                        <div class="flex items-center gap-6">
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Step 1: Preview Data</p>
                                <button wire:click="generateReconciliation" 
                                    class="px-5 py-2.5 bg-slate-800 text-white text-xs font-bold rounded-lg shadow hover:bg-slate-900 transition-colors flex items-center gap-2">
                                    <i class='bx bx-spreadsheet'></i>
                                    <span wire:loading.remove wire:target="generateReconciliation">Generate/Refresh Report</span>
                                    <span wire:loading wire:target="generateReconciliation">Updating...</span>
                                </button>
                            </div>
                            
                            @if(count($auditResults) > 0)
                                <div class="h-10 w-[1px] bg-slate-200 dark:bg-slate-700"></div>
                                <div>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Anggota Terdeteksi</p>
                                    <p class="text-xl font-black text-slate-900 dark:text-white">{{ count($auditResults) }} <span class="text-[10px] font-normal text-slate-500">Orang</span></p>
                                </div>
                            @endif
                        </div>

                        <div class="text-right">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Step 2: Execution</p>
                            <p class="text-xs text-slate-500 italic">Gunakan tombol "Run Full Cleanup" di atas ↑</p>
                        </div>
                    </div>

                    @if(count($auditResults) > 0)
                        <div class="overflow-x-auto border border-slate-200 dark:border-slate-700 rounded-xl">
                            <table class="w-full text-left text-sm">
                                <thead class="bg-slate-50 dark:bg-slate-800 text-slate-500 uppercase text-[10px] font-bold tracking-wider">
                                    <tr>
                                        <th class="px-4 py-3">Member</th>
                                        <th class="px-4 py-3 text-right">Join Date</th>
                                        <th class="px-4 py-3 text-center bg-slate-100/50 dark:bg-slate-800/50" colspan="3">SIMPANAN WAJIB (MANDATORY)</th>
                                        <th class="px-4 py-3 text-center bg-indigo-50/50 dark:bg-indigo-900/20" colspan="2">SIMPANAN SUKARELA (VOLUNTARY)</th>
                                        <th class="px-4 py-3 text-center">Action</th>
                                    </tr>
                                    <tr class="text-[9px] border-b border-slate-100 dark:border-slate-700">
                                        <th class="px-4"></th>
                                        <th class="px-4 text-right"></th>
                                        <th class="px-4 py-2 text-right bg-slate-100/50 dark:bg-slate-800/50">Proposed</th>
                                        <th class="px-4 py-2 text-right bg-slate-100/50 dark:bg-slate-800/50">System</th>
                                        <th class="px-4 py-2 text-center bg-slate-100/50 dark:bg-slate-800/50">Gap</th>
                                        <th class="px-4 py-2 text-right bg-indigo-50/50 dark:bg-indigo-900/20">CSV Total</th>
                                        <th class="px-4 py-2 text-right bg-indigo-50/50 dark:bg-indigo-900/20">System</th>
                                        <th class="px-4 py-2"></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-700 bg-white dark:bg-darkCard">
                                    @foreach($auditResults as $row)
                                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50">
                                            <td class="px-4 py-3">
                                                <div class="flex items-center gap-2">
                                                    <div>
                                                        <p class="font-bold text-slate-700 dark:text-slate-200">{{ $row['name'] }}</p>
                                                        <div class="flex items-center gap-2">
                                                            <span class="text-[10px] text-slate-400 font-mono">ID: {{ $row['member_id'] }}</span>
                                                            @if($row['is_coop'])
                                                                <span class="text-[9px] bg-emerald-50 text-emerald-600 px-1 rounded border border-emerald-100">COOP</span>
                                                            @else
                                                                <span class="text-[9px] bg-slate-50 text-slate-500 px-1 rounded border border-slate-100">RETAIL</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 text-right text-xs font-mono text-slate-400">
                                                {{ \Carbon\Carbon::parse($row['join_date'])->format('d M Y') }}
                                            </td>
                                            {{-- WAJIB --}}
                                            <td class="px-4 py-3 text-right font-mono font-bold text-emerald-600 bg-slate-100/30 dark:bg-slate-800/30">
                                                {{ number_format($row['proposed_wajib']) }}
                                                <div class="text-[9px] font-normal text-slate-400">CSV: +{{ number_format($row['actual_payroll']) }}</div>
                                            </td>
                                            <td class="px-4 py-3 text-right font-mono text-slate-600 dark:text-slate-300 bg-slate-100/30 dark:bg-slate-800/30">
                                                {{ number_format($row['current_wajib']) }}
                                            </td>
                                            <td class="px-4 py-3 text-center bg-slate-100/30 dark:bg-slate-800/30">
                                                @if($row['gap'] == 0)
                                                    <i class='bx bxs-check-circle text-emerald-500 text-lg'></i>
                                                @else
                                                    <span class="px-2 py-1 bg-rose-100 text-rose-700 text-[10px] font-bold rounded-full border border-rose-200">
                                                        {{ $row['gap'] > 0 ? '+' : '' }}{{ number_format($row['gap']) }}
                                                    </span>
                                                @endif
                                            </td>
                                            {{-- SUKARELA --}}
                                            <td class="px-4 py-3 text-right font-mono font-bold text-indigo-600 dark:text-indigo-400 bg-indigo-50/20 dark:bg-indigo-900/10">
                                                {{ number_format($row['actual_sukarela']) }}
                                            </td>
                                            <td class="px-4 py-3 text-right font-mono text-slate-600 dark:text-slate-300 bg-indigo-50/20 dark:bg-indigo-900/10">
                                                {{ number_format($row['current_sukarela']) }}
                                                <div class="text-[9px] font-normal {{ abs($row['gap_sukarela']) < 100 ? 'text-emerald-500' : 'text-rose-500' }}">
                                                    Gap: {{ number_format($row['gap_sukarela']) }}
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <button wire:click="syncBalance({{ $row['member_id'] }})" 
                                                    class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-[10px] font-bold rounded-lg transition-all shadow-sm hover:shadow-md flex items-center gap-1 mx-auto"
                                                    onclick="return confirm('🔄 REBUILD HISTORY (WAJIB & SUKARELA)?\n\nMember: {{ $row['name'] }}\n\nProses ini akan:\n1. Hapus history Wajib & Sukarela lama\n2. Buat ulang history detail dari Payroll\n\nLanjut?')">
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
</div>