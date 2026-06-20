<div class="p-6 space-y-6">
    {{-- Header --}}
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div class="flex items-start gap-3">
            <a href="{{ route('admin.transactions') }}"
                class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-500 transition-colors">
                <i class='bx bx-arrow-back text-xl'></i>
            </a>
            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Audit Rekening Koran Bank</h1>
                    <span
                        class="px-2 py-1 rounded bg-slate-100 dark:bg-slate-800 text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Hidden Tool</span>
                </div>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                    Import CSV rekening koran, validasi saldo, review kategori, lalu sinkronkan ke transaksi bank.
                </p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.reports.balance-sheet') }}"
                class="px-4 py-2 rounded-lg border border-slate-200 dark:border-slate-700 text-sm font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors flex items-center gap-2">
                <i class='bx bx-spreadsheet'></i>
                Bandingkan Neraca
            </a>
            <button wire:click="$set('activeTab', 'upload')"
                class="px-4 py-2 rounded-lg bg-primary hover:bg-primary/90 text-white text-sm font-semibold transition-colors flex items-center gap-2">
                <i class='bx bx-upload'></i>
                Import CSV
            </button>
        </div>
    </div>

    {{-- Operational Snapshot --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <div class="bg-white dark:bg-darkCard rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 p-4">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Records</p>
                    <p class="text-2xl font-bold text-slate-800 dark:text-white font-mono mt-1">{{ number_format($stats['total_imports'], 0, ',', '.') }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-500 flex items-center justify-center">
                    <i class='bx bx-data text-xl'></i>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-darkCard rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 p-4">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Belum Direview</p>
                    <p class="text-2xl font-bold text-amber-600 dark:text-amber-400 font-mono mt-1">{{ number_format($stats['unreviewed'], 0, ',', '.') }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-amber-50 dark:bg-amber-500/10 text-amber-500 flex items-center justify-center">
                    <i class='bx bx-time-five text-xl'></i>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-darkCard rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 p-4">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Belum Sync</p>
                    <p class="text-2xl font-bold text-blue-600 dark:text-blue-400 font-mono mt-1">{{ number_format($stats['unsynced'], 0, ',', '.') }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-blue-50 dark:bg-blue-500/10 text-blue-500 flex items-center justify-center">
                    <i class='bx bx-transfer-alt text-xl'></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Balance Reconciliation --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-darkCard rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden flex flex-col h-full">
            <div class="p-4 border-b border-slate-100 dark:border-slate-700 bg-emerald-50/70 dark:bg-emerald-500/10">
                <h3 class="font-bold text-lg text-emerald-700 dark:text-emerald-400 flex items-center gap-2">
                    <i class='bx bx-trending-up'></i> ALUR REKENING KORAN
                </h3>
            </div>

            <div class="p-4 flex-1 space-y-3">
                <div class="flex justify-between items-center py-2 border-b border-slate-50 dark:border-slate-800 gap-4">
                    <div class="flex items-center gap-2">
                        <span class="text-slate-600 dark:text-slate-300">Saldo Awal Import</span>
                        <button wire:click="$set('saldoAwal', 0)" class="text-[10px] font-bold text-slate-400 hover:text-rose-500 transition-colors flex items-center gap-0.5 font-sans" title="Reset ke 0">
                            <i class='bx bx-refresh text-xs'></i> Reset
                        </button>
                    </div>
                    <div class="relative max-w-[200px]">
                        <span class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-xs font-bold text-slate-400">Rp</span>
                        <input type="number" wire:model.live="saldoAwal" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-lg pl-8 pr-2.5 py-1 text-sm font-bold text-slate-800 dark:text-white text-right outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all font-mono" step="0.01" placeholder="0">
                    </div>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-slate-50 dark:border-slate-800 gap-4">
                    <span class="text-slate-600 dark:text-slate-300">Total Kredit Masuk</span>
                    <span class="font-mono font-medium text-emerald-600 dark:text-emerald-400 text-right">+ Rp {{ number_format($stats['total_kredit'], 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-slate-50 dark:border-slate-800 gap-4">
                    <span class="text-slate-600 dark:text-slate-300">Total Debet Keluar</span>
                    <span class="font-mono font-medium text-rose-600 dark:text-rose-400 text-right">- Rp {{ number_format($stats['total_debet'], 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="p-4 bg-slate-50 dark:bg-slate-800 border-t border-slate-200 dark:border-slate-700 mt-auto">
                <div class="flex justify-between items-center gap-4">
                    <span class="font-bold text-slate-700 dark:text-slate-300">SALDO AKHIR KALKULASI</span>
                    <span class="font-bold text-xl text-emerald-600 dark:text-emerald-400 font-mono text-right">Rp {{ number_format($stats['saldo_akhir_calculated'], 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-darkCard rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden flex flex-col h-full">
            <div class="p-4 border-b border-slate-100 dark:border-slate-700 bg-blue-50/70 dark:bg-blue-500/10">
                <h3 class="font-bold text-lg text-blue-700 dark:text-blue-400 flex items-center gap-2">
                    <i class='bx bx-check-shield'></i> VALIDASI SALDO CSV
                </h3>
            </div>

            <div class="p-4 flex-1 space-y-3">
                <div class="flex justify-between items-center py-2 border-b border-slate-50 dark:border-slate-800 gap-4">
                    <span class="text-slate-600 dark:text-slate-300">Saldo Akhir dari CSV</span>
                    <span class="font-mono font-medium text-slate-800 dark:text-white text-right">Rp {{ number_format($stats['saldo_akhir_actual'], 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-slate-50 dark:border-slate-800 gap-4">
                    <span class="text-slate-600 dark:text-slate-300">Selisih CSV vs Kalkulasi</span>
                    <span class="font-mono font-bold text-right {{ abs($stats['selisih']) < 1 ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600 dark:text-amber-400' }}">
                        {{ $stats['selisih'] >= 0 ? '+' : '-' }} Rp {{ number_format(abs($stats['selisih']), 2, ',', '.') }}
                    </span>
                </div>
                <div class="flex justify-between items-center py-2 gap-4">
                    <span class="text-slate-600 dark:text-slate-300">Status Rekonsiliasi</span>
                    @if(abs($stats['selisih']) < 1)
                        <span class="px-2 py-1 rounded bg-emerald-50 dark:bg-emerald-500/10 text-[11px] font-bold text-emerald-700 dark:text-emerald-400 uppercase tracking-wider">Match</span>
                    @else
                        <span class="px-2 py-1 rounded bg-amber-50 dark:bg-amber-500/10 text-[11px] font-bold text-amber-700 dark:text-amber-400 uppercase tracking-wider">Perlu Cek</span>
                    @endif
                </div>
            </div>

            <div class="p-4 bg-slate-50 dark:bg-slate-800 border-t border-slate-200 dark:border-slate-700 mt-auto">
                <div class="flex justify-between items-center gap-4">
                    <span class="font-bold text-slate-700 dark:text-slate-300">NET MOVEMENT</span>
                    <span class="font-bold text-xl text-blue-600 dark:text-blue-400 font-mono text-right">Rp {{ number_format($stats['total_kredit'] - $stats['total_debet'], 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Workspace --}}
    <div class="bg-white dark:bg-darkCard rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="border-b border-slate-100 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/50 px-3 py-3">
            <div class="flex flex-wrap gap-2">
                <button wire:click="$set('activeTab', 'upload')"
                    class="px-4 py-2 rounded-lg text-sm font-bold transition-colors flex items-center gap-2 {{ $activeTab === 'upload' ? 'bg-primary text-white shadow-sm' : 'text-slate-600 dark:text-slate-300 hover:bg-white dark:hover:bg-slate-700' }}">
                    <i class='bx bx-upload'></i>
                    Upload CSV
                </button>
                <button wire:click="$set('activeTab', 'review')"
                    class="px-4 py-2 rounded-lg text-sm font-bold transition-colors flex items-center gap-2 {{ $activeTab === 'review' ? 'bg-primary text-white shadow-sm' : 'text-slate-600 dark:text-slate-300 hover:bg-white dark:hover:bg-slate-700' }}">
                    <i class='bx bx-search-alt'></i>
                    Review Data
                </button>
                <button wire:click="$set('activeTab', 'rules')"
                    class="px-4 py-2 rounded-lg text-sm font-bold transition-colors flex items-center gap-2 {{ $activeTab === 'rules' ? 'bg-primary text-white shadow-sm' : 'text-slate-600 dark:text-slate-300 hover:bg-white dark:hover:bg-slate-700' }}">
                    <i class='bx bx-slider-alt'></i>
                    Rules
                </button>
                <button wire:click="$set('activeTab', 'sync')"
                    class="px-4 py-2 rounded-lg text-sm font-bold transition-colors flex items-center gap-2 {{ $activeTab === 'sync' ? 'bg-primary text-white shadow-sm' : 'text-slate-600 dark:text-slate-300 hover:bg-white dark:hover:bg-slate-700' }}">
                    <i class='bx bx-transfer'></i>
                    Sync Bank
                </button>
            </div>
        </div>

        <div class="p-5 lg:p-6">
            {{-- Tab 1: Upload --}}
            @if($activeTab === 'upload')
                <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                    <div class="xl:col-span-1">
                        <div class="border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-800/50 p-6 text-center transition-colors"
                            x-data="{ isDropping: false }" @dragover.prevent="isDropping = true"
                            @dragleave.prevent="isDropping = false" @drop.prevent="isDropping = false"
                            :class="{ 'border-primary bg-primary/5': isDropping }">

                            <input type="file" wire:model="csvFiles" multiple class="hidden" id="csvInput">

                            <label for="csvInput" class="cursor-pointer block space-y-4">
                                <div class="w-14 h-14 rounded-lg bg-white dark:bg-slate-700 text-primary mx-auto flex items-center justify-center shadow-sm">
                                    <i class='bx bx-cloud-upload text-3xl'></i>
                                </div>
                                <div>
                                    <p class="font-bold text-slate-700 dark:text-slate-200">Upload CSV rekening koran</p>
                                    <p class="text-xs text-slate-400 mt-1">Bisa pilih banyak file sekaligus.</p>
                                </div>
                            </label>

                            <div class="mt-5 p-3 rounded-lg bg-white dark:bg-slate-900/40 border border-slate-100 dark:border-slate-700 text-left">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Format File</p>
                                <p class="text-xs text-slate-600 dark:text-slate-300 font-mono break-all">rk_kspps_berkah_madani_[bulan]_[tahun]_DB_READY.csv</p>
                            </div>

                            @if(count($csvFiles) > 0)
                                <div class="mt-5 space-y-3">
                                    <p class="text-sm font-bold text-emerald-600 dark:text-emerald-400">{{ count($csvFiles) }} file dipilih</p>
                                    <button wire:click="processUploads"
                                        class="w-full px-4 py-2 bg-primary text-white text-sm font-bold rounded-lg shadow-sm hover:bg-primary/90 transition-colors flex items-center justify-center gap-2">
                                        <i class='bx bx-play-circle' wire:loading.remove wire:target="processUploads"></i>
                                        <span wire:loading.remove wire:target="processUploads">Mulai Import</span>
                                        <span wire:loading wire:target="processUploads">Memproses...</span>
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="xl:col-span-2">
                        <div class="flex items-center justify-between gap-3 mb-3">
                            <div>
                                <h3 class="font-bold text-slate-800 dark:text-white">Periode Ter-Import</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Ringkasan file yang sudah masuk ke staging audit.</p>
                            </div>
                            <span class="text-xs font-mono text-slate-500 dark:text-slate-400">{{ count($importedPeriods) }} periode</span>
                        </div>

                        @if(count($importedPeriods) > 0)
                            <div class="overflow-x-auto border border-slate-200 dark:border-slate-700 rounded-lg">
                                <table class="w-full text-left text-sm">
                                    <thead class="bg-slate-50 dark:bg-slate-800 text-slate-500 dark:text-slate-400 uppercase text-[10px] font-bold tracking-wider">
                                        <tr>
                                            <th class="px-4 py-3">Periode</th>
                                            <th class="px-4 py-3">File</th>
                                            <th class="px-4 py-3 text-right">Rows</th>
                                            <th class="px-4 py-3 text-right">Kredit</th>
                                            <th class="px-4 py-3 text-right">Debet</th>
                                            <th class="px-4 py-3 text-right">Imported</th>
                                            <th class="px-4 py-3 text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                                        @foreach($importedPeriods as $period)
                                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                                <td class="px-4 py-3 font-mono font-bold text-slate-800 dark:text-white whitespace-nowrap">{{ $period->period }}</td>
                                                <td class="px-4 py-3 text-xs text-slate-600 dark:text-slate-300 min-w-56">{{ $period->filename }}</td>
                                                <td class="px-4 py-3 text-right font-mono text-slate-700 dark:text-slate-200">{{ number_format($period->total_rows, 0, ',', '.') }}</td>
                                                <td class="px-4 py-3 text-right font-mono text-emerald-600 dark:text-emerald-400">{{ number_format($period->total_kredit, 0, ',', '.') }}</td>
                                                <td class="px-4 py-3 text-right font-mono text-rose-600 dark:text-rose-400">{{ number_format($period->total_debet, 0, ',', '.') }}</td>
                                                <td class="px-4 py-3 text-right text-xs text-slate-500 whitespace-nowrap">{{ \Carbon\Carbon::parse($period->imported_at)->diffForHumans() }}</td>
                                                <td class="px-4 py-3 text-center">
                                                    <button wire:click="deletePeriod('{{ $period->period }}')"
                                                        class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-500/10 transition-colors"
                                                        onclick="return confirm('Hapus data {{ $period->period }}?')"
                                                        title="Hapus periode">
                                                        <i class='bx bx-trash'></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="border border-slate-200 dark:border-slate-700 rounded-lg p-8 text-center text-slate-500 dark:text-slate-400">
                                <i class='bx bx-folder-open text-3xl mb-2'></i>
                                <p class="text-sm">Belum ada periode yang diimport.</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Tab 2: Review --}}
            @if($activeTab === 'review')
                <div class="space-y-4">
                    <div class="flex flex-col gap-3 xl:flex-row xl:items-end xl:justify-between">
                        <div>
                            <h3 class="font-bold text-slate-800 dark:text-white">Review Transaksi</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Filter transaksi sebelum ditandai reviewed atau disinkronkan.</p>
                        </div>
                        <button wire:click="markAllAsReviewed"
                            class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-bold transition-colors flex items-center justify-center gap-2">
                            <i class='bx bx-check-double'></i>
                            Mark All Reviewed
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-3">
                        <select wire:model.live="filterType" class="rounded-lg border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-sm focus:border-primary focus:ring-primary">
                            <option value="all">Semua Tipe</option>
                            <option value="INCOME">Income</option>
                            <option value="EXPENSE">Expense</option>
                        </select>
                        <select wire:model.live="filterCategory" class="rounded-lg border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-sm focus:border-primary focus:ring-primary">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}">{{ $cat }}</option>
                            @endforeach
                        </select>
                        <select wire:model.live="filterPeriod" class="rounded-lg border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-sm focus:border-primary focus:ring-primary">
                            <option value="">Semua Periode</option>
                            @foreach($periods as $per)
                                <option value="{{ $per }}">{{ $per }}</option>
                            @endforeach
                        </select>
                        <div class="relative">
                            <i class='bx bx-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400'></i>
                            <input type="text" wire:model.live="searchKeterangan" placeholder="Cari keterangan..." class="w-full rounded-lg border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-sm pl-9 focus:border-primary focus:ring-primary">
                        </div>
                    </div>

                    <div class="overflow-x-auto border border-slate-200 dark:border-slate-700 rounded-lg">
                        <table class="w-full text-sm">
                            <thead class="bg-slate-50 dark:bg-slate-800 text-[10px] uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                <tr>
                                    <th class="px-3 py-3 text-left">Tanggal</th>
                                    <th class="px-3 py-3 text-left">Jam</th>
                                    <th class="px-3 py-3 text-left min-w-64">Keterangan</th>
                                    <th class="px-3 py-3 text-right">Debet</th>
                                    <th class="px-3 py-3 text-right">Kredit</th>
                                    <th class="px-3 py-3 text-left">Tipe</th>
                                    <th class="px-3 py-3 text-left">Kategori</th>
                                    <th class="px-3 py-3 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                                @foreach($reviewData as $item)
                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                        <td class="px-3 py-3 text-xs text-slate-600 dark:text-slate-300 whitespace-nowrap">{{ $item->transaction_date->format('d/m/Y') }}</td>
                                        <td class="px-3 py-3 text-xs font-mono text-slate-500 whitespace-nowrap">{{ substr($item->transaction_time, 0, 5) }}</td>
                                        <td class="px-3 py-3 text-xs text-slate-700 dark:text-slate-200">{{ \Str::limit($item->keterangan, 72) }}</td>
                                        <td class="px-3 py-3 text-right font-mono text-rose-600 dark:text-rose-400 whitespace-nowrap">{{ $item->debet > 0 ? number_format($item->debet, 0, ',', '.') : '-' }}</td>
                                        <td class="px-3 py-3 text-right font-mono text-emerald-600 dark:text-emerald-400 whitespace-nowrap">{{ $item->kredit > 0 ? number_format($item->kredit, 0, ',', '.') : '-' }}</td>
                                        <td class="px-3 py-3">
                                            <span class="px-2 py-1 text-[10px] font-bold rounded {{ $item->detected_type === 'INCOME' ? 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400' : 'bg-rose-50 dark:bg-rose-500/10 text-rose-700 dark:text-rose-400' }}">
                                                {{ $item->detected_type }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-3 text-xs text-slate-600 dark:text-slate-300 whitespace-nowrap">{{ $item->detected_category }}</td>
                                        <td class="px-3 py-3 text-center">
                                            @if($item->is_synced)
                                                <span title="Synced"><i class='bx bx-check-circle text-emerald-500 text-lg'></i></span>
                                            @elseif($item->is_reviewed)
                                                <span title="Reviewed"><i class='bx bx-time text-amber-500 text-lg'></i></span>
                                            @else
                                                <span title="Pending"><i class='bx bx-circle text-slate-300 text-lg'></i></span>
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
                    <div>
                        <h3 class="font-bold text-slate-800 dark:text-white">Auto-Categorization Rules</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Urutan prioritas rule yang dipakai saat CSV diimport.</p>
                    </div>

                    <div class="overflow-x-auto border border-slate-200 dark:border-slate-700 rounded-lg">
                        <table class="w-full text-sm">
                            <thead class="bg-slate-50 dark:bg-slate-800 text-[10px] uppercase tracking-wider text-slate-500 dark:text-slate-400">
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
                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                        <td class="px-4 py-3 font-mono font-bold text-slate-700 dark:text-slate-200">{{ $rule->priority }}</td>
                                        <td class="px-4 py-3 font-mono text-xs text-slate-600 dark:text-slate-300 min-w-64">{{ $rule->pattern }}</td>
                                        <td class="px-4 py-3">
                                            <span class="px-2 py-1 text-[10px] font-bold rounded {{ $rule->type === 'INCOME' ? 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400' : 'bg-rose-50 dark:bg-rose-500/10 text-rose-700 dark:text-rose-400' }}">
                                                {{ $rule->type }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-slate-700 dark:text-slate-200">{{ $rule->category }}</td>
                                        <td class="px-4 py-3 text-center">
                                            @if($rule->is_active)
                                                <i class='bx bx-check-circle text-emerald-500 text-lg'></i>
                                            @else
                                                <i class='bx bx-x-circle text-slate-300 text-lg'></i>
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
                <div class="max-w-2xl mx-auto text-center py-8">
                    <div class="w-16 h-16 rounded-lg bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 mx-auto flex items-center justify-center mb-4">
                        <i class='bx bx-transfer-alt text-3xl'></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-2">Sinkronisasi ke Bank Transactions</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">
                        {{ number_format($stats['unsynced'], 0, ',', '.') }} transaksi belum masuk ke tabel transaksi bank.
                    </p>

                    @if($isProcessing)
                        <div class="mb-5">
                            <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-3 overflow-hidden">
                                <div class="bg-primary h-3 rounded-full transition-all" style="width: {{ $syncProgress }}%"></div>
                            </div>
                            <p class="text-xs mt-2 text-slate-500 font-mono">{{ $syncProgress }}%</p>
                        </div>
                    @endif

                    <button wire:click="syncToBank"
                        @if($stats['unsynced'] === 0 || $isProcessing) disabled @endif
                        class="px-6 py-3 bg-primary hover:bg-primary/90 text-white rounded-lg font-bold disabled:opacity-50 disabled:cursor-not-allowed transition-colors inline-flex items-center gap-2">
                        <i class='bx bx-sync'></i>
                        Sync {{ number_format($stats['unsynced'], 0, ',', '.') }} Transaksi
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>
