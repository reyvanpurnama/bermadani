<div>
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-white">Payroll Developer</h1>
            <p class="text-[11px] text-slate-500 mt-0.5">Kelola dan proses pembayaran jam kerja developer.</p>
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
    @if($devSummary->count() > 0)
        <div class="bg-white dark:bg-darkCard rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 p-4 mb-6">
            <h3 class="text-sm font-bold text-slate-800 dark:text-white mb-3">Ringkasan per Developer</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($devSummary as $summary)
                    <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-800 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center text-white font-bold text-xs">
                                {{ strtoupper(substr($summary->user->name ?? 'X', 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-800 dark:text-white">
                                    {{ $summary->user->name ?? 'Unknown' }}</p>
                                <p class="text-[10px] text-slate-400">{{ number_format($summary->total_hours, 1) }} jam</p>
                            </div>
                        </div>
                        <span class="text-sm font-bold text-primary">Rp
                            {{ number_format($summary->total_amount, 0, ',', '.') }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Filters --}}
    <div
        class="flex flex-wrap gap-4 mb-6 bg-white dark:bg-darkCard p-4 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700">
        <div>
            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 block">Bulan</label>
            <select wire:model.live="filterMonth"
                class="bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 text-[13px] rounded-md px-3 py-2 outline-none focus:ring-1 focus:ring-primary text-slate-700 dark:text-white cursor-pointer">
                @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}">{{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}</option>
                @endfor
            </select>
        </div>
        <div>
            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 block">Tahun</label>
            <select wire:model.live="filterYear"
                class="bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 text-[13px] rounded-md px-3 py-2 outline-none focus:ring-1 focus:ring-primary text-slate-700 dark:text-white cursor-pointer">
                @for($y = date('Y'); $y >= 2024; $y--)
                    <option value="{{ $y }}">{{ $y }}</option>
                @endfor
            </select>
        </div>
        <div>
            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 block">Developer</label>
            <select wire:model.live="filterDeveloper"
                class="bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 text-[13px] rounded-md px-3 py-2 outline-none focus:ring-1 focus:ring-primary text-slate-700 dark:text-white cursor-pointer">
                <option value="">Semua Developer</option>
                @foreach($developers as $dev)
                    <option value="{{ $dev->id }}">{{ $dev->name }}</option>
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
    </div>

    {{-- Bulk Actions --}}
    @if(count($selectedLogs) > 0)
        <div
            class="bg-indigo-50 dark:bg-indigo-500/10 border border-indigo-200 dark:border-indigo-500/30 rounded-xl p-4 mb-4 flex flex-wrap items-center justify-between gap-4">
            <span class="text-sm font-bold text-indigo-600 dark:text-indigo-400">
                <i class='bx bx-check-square'></i> {{ count($selectedLogs) }} log terpilih
            </span>
            <div class="flex gap-2">
                <button wire:click="approveSelected"
                    class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-xs font-bold hover:bg-emerald-700 transition-colors flex items-center gap-1">
                    <i class='bx bx-check'></i> Approve Selected
                </button>
                <button wire:click="markAsPaid"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg text-xs font-bold hover:bg-blue-700 transition-colors flex items-center gap-1">
                    <i class='bx bx-money'></i> Mark as Paid
                </button>
            </div>
        </div>
    @endif

    {{-- Table --}}
    <div
        class="bg-white dark:bg-darkCard rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600 dark:text-slate-400">
                <thead
                    class="bg-slate-50 dark:bg-slate-800/50 text-[10px] uppercase font-bold text-slate-500 dark:text-slate-400 tracking-widest">
                    <tr>
                        <th class="px-4 py-3 w-12">
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
                            class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors {{ in_array($log->id, $selectedLogs) ? 'bg-indigo-50/50 dark:bg-indigo-900/10' : '' }}">
                            <td class="px-4 py-3">
                                <input type="checkbox" wire:model.live="selectedLogs" value="{{ $log->id }}"
                                    class="w-4 h-4 text-indigo-600 rounded focus:ring-indigo-500 border-slate-300 dark:border-slate-600 dark:bg-slate-700 cursor-pointer">
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <div
                                        class="w-7 h-7 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center text-white font-bold text-[10px]">
                                        {{ strtoupper(substr($log->user->name ?? 'X', 0, 1)) }}
                                    </div>
                                    <span
                                        class="font-medium text-slate-800 dark:text-white">{{ $log->user->name ?? '-' }}</span>
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
                                            class="text-slate-400 hover:text-emerald-600 transition-colors p-1" title="Approve">
                                            <i class='bx bx-check text-lg'></i>
                                        </button>
                                        <button wire:click="rejectLog({{ $log->id }})" wire:confirm="Yakin tolak log ini?"
                                            class="text-slate-400 hover:text-rose-500 transition-colors p-1" title="Reject">
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
                                <p>Belum ada log kerja untuk periode ini.</p>
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
</div>