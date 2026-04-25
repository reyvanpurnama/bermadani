<div>
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-white">Payroll Developer</h1>
            <p class="text-[11px] text-slate-500 mt-0.5">Kelola dan proses pembayaran jam kerja developer.</p>
        </div>
        <div>
            <button wire:click="downloadPDF"
                class="bg-rose-500 hover:bg-rose-600 text-white px-4 py-2 rounded-lg text-[12px] font-bold shadow-md shadow-rose-500/20 transition-colors flex items-center gap-2">
                <i class='bx bxs-file-pdf text-lg'></i> Export Report
            </button>
        </div>
    </div>

    <div class="flex flex-wrap items-center gap-2 mb-4">
        <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Periode Aktif</span>
        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400 text-[11px] font-bold border border-indigo-100 dark:border-indigo-500/20">
            <i class='bx bx-calendar'></i> {{ $activePeriodLabel }}
        </span>
        @if($latestAvailablePeriodLabel && $latestAvailablePeriodLabel !== $activePeriodLabel)
            <span class="text-[10px] text-slate-400">Periode data terbaru: {{ $latestAvailablePeriodLabel }}</span>
        @endif
    </div>

    <div class="flex flex-wrap gap-2 mb-5">
        <button wire:click="setCurrentPeriod"
            class="px-3 py-1.5 rounded-lg text-[11px] font-bold border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
            Bulan Ini
        </button>
        <button wire:click="setLatestAvailablePeriod"
            class="px-3 py-1.5 rounded-lg text-[11px] font-bold border border-indigo-200 dark:border-indigo-500/30 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-500/10 transition-colors">
            Data Terbaru
        </button>
        <div class="ml-auto inline-flex items-center rounded-lg border border-slate-200 dark:border-slate-700 p-1 bg-white dark:bg-darkCard">
            <button wire:click="setViewMode('cards')"
                class="px-3 py-1.5 rounded-md text-[11px] font-bold transition-colors {{ $viewMode === 'cards' ? 'bg-indigo-600 text-white' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                Card
            </button>
            <button wire:click="setViewMode('table')"
                class="px-3 py-1.5 rounded-md text-[11px] font-bold transition-colors {{ $viewMode === 'table' ? 'bg-indigo-600 text-white' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                Table
            </button>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if (session()->has('success'))
        <div
            class="bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30 text-emerald-700 dark:text-emerald-400 px-4 py-3 rounded-lg mb-6 flex items-center gap-2">
            <i class='bx bxs-check-circle text-xl'></i>
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div
            class="bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/30 text-rose-700 dark:text-rose-400 px-4 py-3 rounded-lg mb-6 flex items-center gap-2">
            <i class='bx bxs-error-circle text-xl'></i>
            {{ session('error') }}
        </div>
    @endif

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        <div class="bg-white dark:bg-darkCard p-4 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700">
            <div class="flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-lg bg-indigo-50 dark:bg-indigo-500/10 flex items-center justify-center text-primary text-xl">
                    <i class='bx bx-time-five'></i>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Total Jam</p>
                    <h4 class="text-lg font-bold text-slate-800 dark:text-white">
                        {{ number_format($stats['totalHours'], 1) }}</h4>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-darkCard p-4 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700">
            <div class="flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-lg bg-slate-50 dark:bg-slate-700/50 flex items-center justify-center text-slate-500 text-xl">
                    <i class='bx bx-wallet'></i>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Total</p>
                    <h4 class="text-lg font-bold text-slate-800 dark:text-white">Rp
                        {{ number_format($stats['totalAmount'], 0, ',', '.') }}</h4>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-darkCard p-4 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700">
            <div class="flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-lg bg-amber-50 dark:bg-amber-500/10 flex items-center justify-center text-amber-500 text-xl">
                    <i class='bx bx-hourglass'></i>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Pending</p>
                    <h4 class="text-lg font-bold text-amber-600 dark:text-amber-400">Rp
                        {{ number_format($stats['pending'], 0, ',', '.') }}</h4>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-darkCard p-4 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700">
            <div class="flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-lg bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center text-emerald-600 text-xl">
                    <i class='bx bx-check-circle'></i>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Approved</p>
                    <h4 class="text-lg font-bold text-emerald-600 dark:text-emerald-400">Rp
                        {{ number_format($stats['approved'], 0, ',', '.') }}</h4>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-darkCard p-4 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700">
            <div class="flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-lg bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center text-blue-600 text-xl">
                    <i class='bx bx-money'></i>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Dibayar</p>
                    <h4 class="text-lg font-bold text-blue-600 dark:text-blue-400">Rp
                        {{ number_format($stats['paid'], 0, ',', '.') }}</h4>
                </div>
            </div>
        </div>
    </div>

    {{-- Developer Summary --}}
    @if($viewMode === 'table' && $devSummary->count() > 0)
        <div class="bg-white dark:bg-darkCard rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 p-4 mb-6">
            <h3 class="text-sm font-bold text-slate-800 dark:text-white mb-3 flex items-center gap-2">
                <i class='bx bx-group'></i> Ringkasan per Developer
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($devSummary as $summary)
                    <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-800 rounded-lg group hover:ring-1 ring-indigo-500/30 transition-all">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-9 h-9 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center text-white font-bold text-xs shadow-md shadow-indigo-500/20">
                                {{ strtoupper(substr($summary->developerName ?? 'X', 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-800 dark:text-white">
                                    {{ $summary->developerName ?? 'Unknown' }}</p>
                                <p class="text-[10px] text-slate-400 flex items-center gap-1">
                                    <i class='bx bx-time'></i> {{ number_format($summary->total_hours, 1) }} jam
                                </p>
                            </div>
                        </div>
                        <span class="text-sm font-bold text-primary dark:text-indigo-400">Rp
                            {{ number_format($summary->total_amount, 0, ',', '.') }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    @if($viewMode === 'cards')
        <div class="space-y-4 mb-6">
            @forelse($developerCards as $card)
                <details class="bg-white dark:bg-darkCard rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden group">
                    <summary class="list-none cursor-pointer p-4 sm:p-5">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center text-white font-bold text-sm shadow-md shadow-indigo-500/20">
                                    {{ strtoupper(substr($card['developerName'], 0, 1)) }}
                                </div>
                                <div>
                                    <h3 class="text-sm font-bold text-slate-800 dark:text-white">{{ $card['developerName'] }}</h3>
                                    <p class="text-[11px] text-slate-400">{{ number_format($card['totalHours'], 1) }} jam • Rp {{ number_format($card['totalAmount'], 0, ',', '.') }}</p>
                                </div>
                            </div>
                            <div class="flex flex-wrap items-center gap-2">
                                @if($card['pendingCount'] > 0)
                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400 border border-amber-100 dark:border-amber-500/20">PENDING {{ $card['pendingCount'] }}</span>
                                @endif
                                @if($card['approvedCount'] > 0)
                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-500/20">APPROVED {{ $card['approvedCount'] }}</span>
                                @endif
                                @if($card['paidCount'] > 0)
                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-[10px] font-bold bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400 border border-blue-100 dark:border-blue-500/20">PAID {{ $card['paidCount'] }}</span>
                                @endif
                                @if($card['rejectedCount'] > 0)
                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-[10px] font-bold bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-400 border border-rose-100 dark:border-rose-500/20">REJECTED {{ $card['rejectedCount'] }}</span>
                                @endif
                                <span class="text-[11px] font-bold text-indigo-600 dark:text-indigo-400">Detail</span>
                                <i class='bx bx-chevron-down text-indigo-500 text-lg transition-transform group-open:rotate-180'></i>
                            </div>
                        </div>
                    </summary>
                    <div class="border-t border-slate-100 dark:border-slate-700">
                        <div class="divide-y divide-slate-100 dark:divide-slate-700">
                            @foreach($card['logs'] as $log)
                                <div class="p-4 sm:p-5 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
                                    <div class="min-w-0">
                                        <div class="flex flex-wrap items-center gap-2 mb-1">
                                            <span class="text-[12px] font-bold text-slate-700 dark:text-slate-200">{{ $log->date->translatedFormat('d M Y') }}</span>
                                            <span class="text-[11px] text-slate-400">{{ number_format($log->hoursWorked, 1) }} jam</span>
                                            <span class="text-[11px] font-bold text-primary dark:text-indigo-400">Rp {{ number_format($log->totalAmount, 0, ',', '.') }}</span>
                                        </div>
                                        <p class="text-[12px] text-slate-500 dark:text-slate-400 truncate" title="{{ $log->description }}">{{ $log->description }}</p>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-[10px] font-bold uppercase
                                            @if($log->status === 'PENDING') bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400 border border-amber-100 dark:border-amber-500/20
                                            @elseif($log->status === 'APPROVED') bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-500/20
                                            @elseif($log->status === 'PAID') bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400 border border-blue-100 dark:border-blue-500/20
                                            @else bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-400 border border-rose-100 dark:border-rose-500/20
                                            @endif">
                                            {{ $log->statusLabel }}
                                        </span>
                                        @if($log->status === 'PENDING')
                                            <button wire:click="approveSingle({{ $log->id }})"
                                                class="w-7 h-7 flex items-center justify-center rounded bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400 hover:bg-emerald-100 hover:text-emerald-700 transition-colors" title="Approve">
                                                <i class='bx bx-check text-lg'></i>
                                            </button>
                                            <button wire:click="rejectLog({{ $log->id }})" wire:confirm="Yakin tolak log ini?"
                                                class="w-7 h-7 flex items-center justify-center rounded bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-400 hover:bg-rose-100 hover:text-rose-700 transition-colors" title="Reject">
                                                <i class='bx bx-x text-lg'></i>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </details>
            @empty
                <div class="bg-white dark:bg-darkCard rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 px-4 py-8 text-center text-slate-500">
                    <i class='bx bx-calendar-x text-4xl mb-2'></i>
                    <p class="font-semibold text-slate-700 dark:text-slate-200">Belum ada log kerja untuk {{ $activePeriodLabel }}.</p>
                    <p class="text-[11px] mt-1 text-slate-400">Coba pindah ke periode data terbaru atau bulan lain yang sudah ada isinya.</p>
                </div>
            @endforelse
        </div>
    @endif

    {{-- Filters --}}
    <div
        class="flex flex-wrap gap-4 mb-6 bg-white dark:bg-darkCard p-4 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700">
        <div>
            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 block">Periode</label>
            <select wire:model.live="filterPeriod"
                class="bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 text-[13px] rounded-md px-3 py-2 outline-none focus:ring-1 focus:ring-primary text-slate-700 dark:text-white cursor-pointer">
                @if($filterPeriod && !$availablePeriods->contains(fn($period) => $period['value'] === $filterPeriod))
                    <option value="{{ $filterPeriod }}">{{ $activePeriodLabel }} (Belum ada data)</option>
                @endif
                @foreach($availablePeriods as $period)
                    <option value="{{ $period['value'] }}">{{ $period['label'] }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 block">Developer</label>
            <select wire:model.live="filterDeveloper"
                class="bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 text-[13px] rounded-md px-3 py-2 outline-none focus:ring-1 focus:ring-primary text-slate-700 dark:text-white cursor-pointer">
                <option value="">Semua Developer</option>
                @foreach($developers as $name)
                    <option value="{{ $name }}">{{ $name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 block">Status</label>
            <select wire:model.live="filterStatus"
                class="bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 text-[13px] rounded-md px-3 py-2 outline-none focus:ring-1 focus:ring-primary text-slate-700 dark:text-white cursor-pointer">
                <option value="">Semua Status</option>
                <option value="PENDING">Pending</option>
                <option value="APPROVED">Approved</option>
                <option value="PAID">Paid</option>
                <option value="REJECTED">Rejected</option>
            </select>
        </div>
        
        <div class="ml-auto flex items-end">
             <div class="text-xs text-slate-500 bg-slate-50 dark:bg-slate-800 px-3 py-2 rounded-md">
                <i class='bx bx-info-circle'></i> Menampilkan: <span class="font-bold text-slate-800 dark:text-white">{{ $logs->total() }}</span> log work
            </div>
        </div>
    </div>

    {{-- Bulk Actions --}}
    @if($viewMode === 'table' && count($selectedLogs) > 0)
        <div
            class="bg-indigo-50 dark:bg-indigo-500/10 border border-indigo-200 dark:border-indigo-500/30 rounded-xl p-4 mb-4 flex flex-wrap items-center justify-between gap-4 sticky top-4 z-20 shadow-lg shadow-indigo-100 dark:shadow-none backdrop-blur-sm">
            <span class="text-sm font-bold text-indigo-600 dark:text-indigo-400 flex items-center gap-2">
                <i class='bx bx-check-square text-lg'></i> {{ count($selectedLogs) }} log terpilih
            </span>
            <div class="flex gap-2">
                <button wire:click="approveSelected"
                    class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-xs font-bold hover:bg-emerald-700 transition-colors flex items-center gap-1 shadow-md shadow-emerald-500/20">
                    <i class='bx bx-check'></i> Approve Selected
                </button>
                <button wire:click="markAsPaid"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg text-xs font-bold hover:bg-blue-700 transition-colors flex items-center gap-1 shadow-md shadow-blue-500/20">
                    <i class='bx bx-money'></i> Mark as Paid
                </button>
            </div>
        </div>
    @endif

    {{-- Table --}}
    @if($viewMode === 'table')
    <div
        class="bg-white dark:bg-darkCard rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600 dark:text-slate-400">
                <thead
                    class="bg-slate-50 dark:bg-slate-800/50 text-[10px] uppercase font-bold text-slate-500 dark:text-slate-400 tracking-widest">
                    <tr>
                        <th class="px-4 py-3 w-12 text-center">
                            <input type="checkbox" wire:model.live="selectAll"
                                class="w-4 h-4 text-indigo-600 rounded focus:ring-indigo-500 border-slate-300 dark:border-slate-600 dark:bg-slate-700 cursor-pointer">
                        </th>
                        <th class="px-4 py-3">Developer</th>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3">Durasi</th>
                        <th class="px-4 py-3">Deskripsi</th>
                        <th class="px-4 py-3">Bayaran</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700 text-[13px]">
                    @forelse ($logs as $log)
                        <tr
                            class="group hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors {{ in_array($log->id, $selectedLogs) ? 'bg-indigo-50/50 dark:bg-indigo-900/10' : '' }}">
                            <td class="px-4 py-3 text-center">
                                <input type="checkbox" wire:model.live="selectedLogs" value="{{ $log->id }}"
                                    class="w-4 h-4 text-indigo-600 rounded focus:ring-indigo-500 border-slate-300 dark:border-slate-600 dark:bg-slate-700 cursor-pointer opacity-40 group-hover:opacity-100 {{ in_array($log->id, $selectedLogs) ? 'opacity-100' : '' }} transition-opacity">
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <div
                                        class="w-7 h-7 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center text-white font-bold text-[10px]">
                                        {{ strtoupper(substr($log->developerName ?? 'X', 0, 1)) }}
                                    </div>
                                    <span
                                        class="font-medium text-slate-800 dark:text-white">{{ $log->developerName ?? 'Unknown' }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 font-medium">
                                {{ $log->date->translatedFormat('d M Y') }}
                                <span
                                    class="block text-[10px] text-slate-400">{{ $log->date->translatedFormat('l') }}</span>
                            </td>
                            <td class="px-4 py-3 font-bold text-slate-800 dark:text-white">
                                {{ number_format($log->hoursWorked, 1) }} jam
                            </td>
                            <td class="px-4 py-3 max-w-[180px] truncate" title="{{ $log->description }}">
                                {{ $log->description }}
                            </td>
                            <td class="px-4 py-3 font-bold text-primary dark:text-indigo-400">
                                Rp {{ number_format($log->totalAmount, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase
                                        @if($log->status === 'PENDING') bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400 border border-amber-100 dark:border-amber-500/20
                                        @elseif($log->status === 'APPROVED') bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-500/20
                                        @elseif($log->status === 'PAID') bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400 border border-blue-100 dark:border-blue-500/20
                                        @else bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-400 border border-rose-100 dark:border-rose-500/20
                                        @endif">
                                    {{ $log->statusLabel }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($log->status === 'PENDING')
                                    <div class="flex items-center justify-center gap-1">
                                        <button wire:click="approveSingle({{ $log->id }})"
                                            class="w-6 h-6 flex items-center justify-center rounded bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400 hover:bg-emerald-100 hover:text-emerald-700 transition-colors" title="Approve">
                                            <i class='bx bx-check text-lg'></i>
                                        </button>
                                        <button wire:click="rejectLog({{ $log->id }})" wire:confirm="Yakin tolak log ini?"
                                            class="w-6 h-6 flex items-center justify-center rounded bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-400 hover:bg-rose-100 hover:text-rose-700 transition-colors" title="Reject">
                                            <i class='bx bx-x text-lg'></i>
                                        </button>
                                    </div>
                                @else
                                    <span class="text-slate-300 dark:text-slate-600 text-[10px]">
                                        @if($log->approver)
                                            by {{ Str::limit($log->approver->name, 10) }}
                                        @else
                                            -
                                        @endif
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-slate-500">
                                <i class='bx bx-calendar-x text-4xl mb-2'></i>
                                <p class="font-semibold text-slate-700 dark:text-slate-200">Belum ada log kerja untuk {{ $activePeriodLabel }}.</p>
                                <p class="text-[11px] mt-1 text-slate-400">Coba pindah ke periode data terbaru atau bulan lain yang sudah ada isinya.</p>
                                <div class="mt-3 flex items-center justify-center gap-2">
                                    <button wire:click="setLatestAvailablePeriod"
                                        class="px-3 py-1.5 rounded-lg text-[11px] font-bold bg-indigo-600 text-white hover:bg-indigo-700 transition-colors">
                                        Data Terbaru
                                    </button>
                                    <button wire:click="setCurrentPeriod"
                                        class="px-3 py-1.5 rounded-lg text-[11px] font-bold border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                                        Bulan Ini
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="px-4 py-4 border-t border-slate-100 dark:border-slate-700">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
    @endif
</div>