<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Audit Rekening Koran Bank</h1>
            <p class="text-xs text-slate-500">Import CSV rekening koran, auto-kategorisasi, dan sinkronisasi ke Bank Transactions.</p>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div class="bg-white dark:bg-darkCard p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Total Records</h3>
            <p class="text-3xl font-bold text-slate-900 dark:text-white">{{ number_format($stats['total_imports']) }}</p>
        </div>
        <div class="bg-white dark:bg-darkCard p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
            <h3 class="text-xs font-bold text-amber-500 uppercase tracking-widest mb-2">Unreviewed</h3>
            <p class="text-3xl font-bold text-amber-600">{{ number_format($stats['unreviewed']) }}</p>
        </div>
        <div class="bg-white dark:bg-darkCard p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
            <h3 class="text-xs font-bold text-rose-500 uppercase tracking-widest mb-2">Unsynced</h3>
            <p class="text-3xl font-bold text-rose-600">{{ number_format($stats['unsynced']) }}</p>
        </div>
        <div class="bg-white dark:bg-darkCard p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
            <h3 class="text-xs font-bold text-emerald-500 uppercase tracking-widest mb-2">Total Income</h3>
            <p class="text-2xl font-bold text-emerald-600">Rp {{ number_format($stats['total_income'], 0) }}</p>
        </div>
        <div class="bg-white dark:bg-darkCard p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
            <h3 class="text-xs font-bold text-red-500 uppercase tracking-widest mb-2">Total Expense</h3>
            <p class="text-2xl font-bold text-red-600">Rp {{ number_format($stats['total_expense'], 0) }}</p>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="bg-white dark:bg-darkCard rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">
        {{-- Tabs --}}
        <div class="flex border-b border-slate-100 dark:border-slate-700">
            <button wire:click="$set('activeTab', 'upload')"
                class="px-6 py-4 text-sm font-bold border-b-2 transition-colors {{ $activeTab === 'upload' ? 'border-primary text-primary' : 'border-transparent text-slate-500 hover:text-slate-700' }}">
                1. Upload CSV
            </button>
            <button wire:click="$set('activeTab', 'review')"
                class="px-6 py-4 text-sm font-bold border-b-2 transition-colors {{ $activeTab === 'review' ? 'border-primary text-primary' : 'border-transparent text-slate-500 hover:text-slate-700' }}">
                2. Review Data
            </button>
            <button wire:click="$set('activeTab', 'rules')"
                class="px-6 py-4 text-sm font-bold border-b-2 transition-colors {{ $activeTab === 'rules' ? 'border-primary text-primary' : 'border-transparent text-slate-500 hover:text-slate-700' }}">
                3. Category Rules
            </button>
            <button wire:click="$set('activeTab', 'sync')"
                class="px-6 py-4 text-sm font-bold border-b-2 transition-colors {{ $activeTab === 'sync' ? 'border-primary text-primary' : 'border-transparent text-slate-500 hover:text-slate-700' }}">
                4. Sync to Bank
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
                                <p class="font-bold text-slate-700 dark:text-slate-200">Klik untuk upload CSV Rekening Koran</p>
                                <p class="text-xs text-slate-400 mt-1">Format: rk_kspps_berkah_madani_[bulan]_[tahun]_DB_READY.csv</p>
                            </div>
                        </label>

                        @if(count($csvFiles) > 0)
                            <div class="mt-8 space-y-2 max-w-md mx-auto">
                                <p class="text-sm font-bold text-emerald-600">{{ count($csvFiles) }} file dipilih</p>
                                <button wire:click="processUploads"
                                    class="px-6 py-2 bg-primary text-white text-sm font-bold rounded-xl shadow-lg hover:bg-indigo-700 transition-colors">
                                    <span wire:loading.remove wire:target="processUploads">Mulai Proses Import</span>
                                    <span wire:loading wire:target="processUploads">Memproses...</span>
                                </button>
                            </div>
                        @endif
                    </div>

                    {{-- Imported Periods List --}}
                    @if(count($importedPeriods) > 0)
                        <div>
                            <h3 class="text-sm font-bold text-slate-700 dark:text-slate-200 mb-4">Data Rekening Koran Ter-Import</h3>
                            <div class="overflow-hidden border border-slate-200 dark:border-slate-700 rounded-xl">
                                <table class="w-full text-left text-sm">
                                    <thead class="bg-slate-50 dark:bg-slate-800 text-slate-500 uppercase text-[10px] font-bold">
                                        <tr>
                                            <th class="px-4 py-3">Periode</th>
                                            <th class="px-4 py-3">Filename</th>
                                            <th class="px-4 py-3 text-right">Rows</th>
                                            <th class="px-4 py-3 text-right">Total Kredit</th>
                                            <th class="px-4 py-3 text-right">Total Debet</th>
                                            <th class="px-4 py-3 text-right">Imported</th>
                                            <th class="px-4 py-3 text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                                        @foreach($importedPeriods as $period)
                                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50">
                                                <td class="px-4 py-3 font-mono font-bold">{{ $period->period }}</td>
                                                <td class="px-4 py-3 text-xs">{{ $period->filename }}</td>
                                                <td class="px-4 py-3 text-right font-mono">{{ number_format($period->total_rows) }}</td>
                                                <td class="px-4 py-3 text-right font-mono text-emerald-600">{{ number_format($period->total_kredit) }}</td>
                                                <td class="px-4 py-3 text-right font-mono text-red-600">{{ number_format($period->total_debet) }}</td>
                                                <td class="px-4 py-3 text-right text-xs">{{ \Carbon\Carbon::parse($period->imported_at)->diffForHumans() }}</td>
                                                <td class="px-4 py-3 text-center">
                                                    <button wire:click="deletePeriod('{{ $period->period }}')"
                                                        class="text-rose-500 hover:text-rose-700"
                                                        onclick="return confirm('Hapus data {{ $period->period }}?')">
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

            {{-- Tab 2: Review --}}
            @if($activeTab === 'review')
                <div class="space-y-4">
                    {{-- Filters --}}
                    <div class="grid grid-cols-4 gap-4">
                        <select wire:model.live="filterType" class="rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800">
                            <option value="all">Semua Tipe</option>
                            <option value="INCOME">Income</option>
                            <option value="EXPENSE">Expense</option>
                        </select>
                        <select wire:model.live="filterCategory" class="rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}">{{ $cat }}</option>
                            @endforeach
                        </select>
                        <select wire:model.live="filterPeriod" class="rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800">
                            <option value="">Semua Periode</option>
                            @foreach($periods as $per)
                                <option value="{{ $per }}">{{ $per }}</option>
                            @endforeach
                        </select>
                        <input type="text" wire:model.live="searchKeterangan" placeholder="Cari keterangan..." class="rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800">
                    </div>

                    <button wire:click="markAllAsReviewed" class="px-4 py-2 bg-emerald-500 text-white rounded-xl text-sm">
                        Mark All as Reviewed
                    </button>

                    {{-- Data Table --}}
                    <div class="overflow-x-auto border border-slate-200 dark:border-slate-700 rounded-xl">
                        <table class="w-full text-sm">
                            <thead class="bg-slate-50 dark:bg-slate-800 text-[10px] uppercase">
                                <tr>
                                    <th class="px-3 py-2">Date</th>
                                    <th class="px-3 py-2">Time</th>
                                    <th class="px-3 py-2">Keterangan</th>
                                    <th class="px-3 py-2 text-right">Debet</th>
                                    <th class="px-3 py-2 text-right">Kredit</th>
                                    <th class="px-3 py-2">Type</th>
                                    <th class="px-3 py-2">Category</th>
                                    <th class="px-3 py-2">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                                @foreach($reviewData as $item)
                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50">
                                        <td class="px-3 py-2 text-xs">{{ $item->transaction_date->format('d/m/Y') }}</td>
                                        <td class="px-3 py-2 text-xs font-mono">{{ substr($item->transaction_time, 0, 5) }}</td>
                                        <td class="px-3 py-2 text-xs">{{ \Str::limit($item->keterangan, 50) }}</td>
                                        <td class="px-3 py-2 text-right font-mono text-red-600">{{ $item->debet > 0 ? number_format($item->debet, 0) : '-' }}</td>
                                        <td class="px-3 py-2 text-right font-mono text-emerald-600">{{ $item->kredit > 0 ? number_format($item->kredit, 0) : '-' }}</td>
                                        <td class="px-3 py-2">
                                            <span class="px-2 py-1 text-[10px] rounded-full {{ $item->detected_type === 'INCOME' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                                                {{ $item->detected_type }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-2 text-xs">{{ $item->detected_category }}</td>
                                        <td class="px-3 py-2 text-center">
                                            @if($item->is_synced)
                                                <i class='bx bx-check-circle text-emerald-500'></i>
                                            @elseif($item->is_reviewed)
                                                <i class='bx bx-time text-amber-500'></i>
                                            @else
                                                <i class='bx bx-circle text-slate-300'></i>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{ $reviewData->links() }}
                </div>
            @endif

            {{-- Tab 3: Rules --}}
            @if($activeTab === 'rules')
                <div class="space-y-4">
                    <h3 class="font-bold">Auto-Categorization Rules</h3>
                    <div class="overflow-x-auto border border-slate-200 dark:border-slate-700 rounded-xl">
                        <table class="w-full text-sm">
                            <thead class="bg-slate-50 dark:bg-slate-800">
                                <tr>
                                    <th class="px-4 py-3 text-left">Priority</th>
                                    <th class="px-4 py-3 text-left">Pattern</th>
                                    <th class="px-4 py-3 text-left">Type</th>
                                    <th class="px-4 py-3 text-left">Category</th>
                                    <th class="px-4 py-3 text-center">Active</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                                @foreach($categoryRules as $rule)
                                    <tr>
                                        <td class="px-4 py-3 font-mono">{{ $rule->priority }}</td>
                                        <td class="px-4 py-3 font-mono text-xs">{{ $rule->pattern }}</td>
                                        <td class="px-4 py-3">
                                            <span class="px-2 py-1 text-xs rounded-full {{ $rule->type === 'INCOME' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                                                {{ $rule->type }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3">{{ $rule->category }}</td>
                                        <td class="px-4 py-3 text-center">
                                            @if($rule->is_active)
                                                <i class='bx bx-check-circle text-emerald-500'></i>
                                            @else
                                                <i class='bx bx-x-circle text-slate-300'></i>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            {{-- Tab 4: Sync --}}
            @if($activeTab === 'sync')
                <div class="space-y-4">
                    <div class="text-center py-12">
                        <h3 class="text-lg font-bold mb-4">Sinkronisasi ke Bank Transactions</h3>
                        <p class="text-sm text-slate-500 mb-6">{{ $stats['unsynced'] }} transaksi belum disinkronkan</p>
                        
                        @if($isProcessing)
                            <div class="mb-4">
                                <div class="w-full bg-slate-200 rounded-full h-4">
                                    <div class="bg-primary h-4 rounded-full transition-all" style="width: {{ $syncProgress }}%"></div>
                                </div>
                                <p class="text-xs mt-2">{{ $syncProgress }}%</p>
                            </div>
                        @endif

                        <button wire:click="syncToBank" 
                            @if($stats['unsynced'] === 0 || $isProcessing) disabled @endif
                            class="px-6 py-3 bg-primary text-white rounded-xl font-bold disabled:opacity-50">
                            Sync {{ $stats['unsynced'] }} Transaksi
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
