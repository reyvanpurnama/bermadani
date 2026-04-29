<div class="space-y-6">
    <div class="bg-white dark:bg-darkCard border border-slate-100 dark:border-slate-700 rounded-2xl p-4 sm:p-6 shadow-sm space-y-5">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white">Pinjaman Anggota</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                    Monitoring pinjaman aktif, sisa hutang, dan progres angsuran anggota.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 w-full lg:w-auto">
                <a href="{{ route('admin.loans.import') }}"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 text-sm font-semibold hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors w-full">
                    <i class='bx bx-import text-base'></i>
                    Import
                </a>
                <a href="{{ route('admin.loans.create') }}"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-primary hover:bg-primary/90 text-white text-sm font-bold shadow-sm shadow-primary/20 transition-colors w-full">
                    <i class='bx bx-plus text-base'></i>
                    Tambah Pinjaman
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-3 sm:gap-4">
            <div class="rounded-xl border border-slate-100 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 p-4">
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Pinjaman Berjalan</p>
                <p class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white mt-1">{{ number_format($stats['activeLoans']) }}</p>
                <p class="text-xs text-slate-500 mt-1">Status ACTIVE + OVERDUE</p>
            </div>

            <div class="rounded-xl border border-slate-100 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 p-4">
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Outstanding</p>
                <p class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white mt-1">Rp {{ number_format($stats['outstandingTotal'], 0, ',', '.') }}</p>
                <p class="text-xs text-slate-500 mt-1">Total sisa hutang berjalan</p>
            </div>

            <div class="rounded-xl border border-slate-100 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 p-4">
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Angsuran/Bulan</p>
                <p class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white mt-1">Rp {{ number_format($stats['monthlyInstallmentTotal'], 0, ',', '.') }}</p>
                <p class="text-xs text-slate-500 mt-1">Akumulasi angsuran aktif</p>
            </div>

            <div class="rounded-xl border border-slate-100 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 p-4">
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Debitur Aktif</p>
                <p class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white mt-1">{{ number_format($stats['activeDebtors']) }}</p>
                <p class="text-xs text-slate-500 mt-1">Anggota dengan pinjaman berjalan</p>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-darkCard rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 p-3 sm:p-4">
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-12 gap-3">
            <div class="relative md:col-span-2 xl:col-span-4">
                <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-slate-400">
                    <i class='bx bx-search text-lg'></i>
                </div>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama atau nomor anggota..."
                    class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl pl-10 pr-4 py-2.5 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none text-slate-700 dark:text-white placeholder-slate-400">
            </div>

            <div class="relative xl:col-span-2">
                <select wire:model.live="filterStatus"
                    class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none text-slate-700 dark:text-white cursor-pointer appearance-none">
                    <option value="ACTIVE">Status: Active</option>
                    <option value="OVERDUE">Status: Overdue</option>
                    <option value="COMPLETED">Status: Completed</option>
                    <option value="PENDING">Status: Pending</option>
                    <option value="REJECTED">Status: Rejected</option>
                    <option value="ALL">Status: Semua</option>
                </select>
                <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none">
                    <i class='bx bx-chevron-down text-slate-400'></i>
                </div>
            </div>

            <div class="relative xl:col-span-2">
                <select wire:model.live="filterSource"
                    class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none text-slate-700 dark:text-white cursor-pointer appearance-none">
                    <option value="">Semua Sumber</option>
                    <option value="BERMADANI">Bermadani</option>
                    <option value="BMT_ITQAN">BMT Itqan</option>
                </select>
                <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none">
                    <i class='bx bx-chevron-down text-slate-400'></i>
                </div>
            </div>

            <div class="relative xl:col-span-3">
                <select wire:model.live="sortBy"
                    class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none text-slate-700 dark:text-white cursor-pointer appearance-none">
                    <option value="priority">Sort: Prioritas Sistem</option>
                    <option value="start_date">Sort: Tanggal Mulai</option>
                    <option value="end_date">Sort: Tanggal Akhir</option>
                    <option value="member_name">Sort: Nama Anggota</option>
                    <option value="member_number">Sort: No. Anggota</option>
                    <option value="amount">Sort: Pokok Pinjaman</option>
                    <option value="monthly_payment">Sort: Angsuran/Bulan</option>
                    <option value="remaining_amount">Sort: Sisa Hutang</option>
                    <option value="tenor">Sort: Tenor</option>
                    <option value="progress">Sort: Progress Cicilan</option>
                    <option value="status">Sort: Status</option>
                    <option value="source">Sort: Sumber</option>
                </select>
                <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none">
                    <i class='bx bx-chevron-down text-slate-400'></i>
                </div>
            </div>

            <div class="xl:col-span-1 grid grid-cols-2 gap-2">
                <button wire:click="toggleSortDirection"
                    class="w-full px-2 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-500 hover:text-slate-700 dark:text-slate-300 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800 text-[11px] font-bold transition-colors">
                    {{ $sortDirection === 'asc' ? 'ASC' : 'DESC' }}
                </button>
                <button wire:click="clearFilters"
                    class="w-full px-2 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-500 hover:text-slate-700 dark:text-slate-300 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800 text-[11px] font-bold transition-colors">
                    Reset
                </button>
            </div>
        </div>
    </div>

    <div class="xl:hidden grid grid-cols-1 lg:grid-cols-2 gap-3">
        @forelse($loans as $loan)
            @php
                $progress = $this->getProgressPercentage($loan);
                $paidInstallments = (int) ($loan->paid_installments ?? 0);
                $tenor = (int) ($loan->tenor ?? 0);
                $isOverdueWarning = $this->isOverdueWarning($loan);
            @endphp
            <div class="bg-white dark:bg-darkCard rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm p-4 space-y-4 h-full">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-sm font-bold text-slate-900 dark:text-white">{{ $loan->member?->name ?? '-' }}</p>
                        <p class="text-xs text-slate-500 mt-0.5">{{ $loan->member?->nomorAnggota ?? '-' }}</p>
                    </div>
                    <div class="text-right space-y-1.5">
                        <span class="inline-flex px-2 py-0.5 text-[10px] font-bold rounded-full border {{ $this->sourceBadgeClass($loan->loanSource) }}">
                            {{ $this->formatLoanSource($loan->loanSource) }}
                        </span>
                        <span class="inline-flex px-2 py-0.5 text-[10px] font-bold rounded-full border {{ $this->statusBadgeClass($loan->status) }}">
                            {{ $loan->status }}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2 text-xs">
                    <div class="rounded-lg bg-slate-50 dark:bg-slate-800/80 p-2.5">
                        <p class="text-slate-500">Pokok</p>
                        <p class="font-bold text-slate-800 dark:text-slate-200 mt-0.5" title="Rp {{ number_format($loan->amount, 0, ',', '.') }}">{{ $this->formatCompactCurrency($loan->amount) }}</p>
                    </div>
                    <div class="rounded-lg bg-slate-50 dark:bg-slate-800/80 p-2.5">
                        <p class="text-slate-500">Angsuran/Bulan</p>
                        <p class="font-bold text-primary dark:text-indigo-400 mt-0.5" title="Rp {{ number_format($loan->monthlyPayment, 0, ',', '.') }}">{{ $this->formatCompactCurrency($loan->monthlyPayment) }}</p>
                    </div>
                    <div class="col-span-2 rounded-lg bg-slate-50 dark:bg-slate-800/80 p-2.5">
                        <p class="text-slate-500">Sisa Hutang</p>
                        <p class="font-bold mt-0.5 {{ $loan->remainingAmount > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-emerald-600 dark:text-emerald-400' }}"
                            title="Rp {{ number_format($loan->remainingAmount, 0, ',', '.') }}">
                            {{ $this->formatCompactCurrency($loan->remainingAmount) }}
                        </p>
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between text-[11px] mb-1">
                        <span class="text-slate-500">{{ $paidInstallments }}/{{ max(0, $tenor) }} cicilan</span>
                        <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $progress }}%</span>
                    </div>
                    <div class="h-1.5 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                        <div class="h-full rounded-full {{ $progress >= 100 ? 'bg-emerald-500' : 'bg-primary' }}" style="width: {{ $progress }}%"></div>
                    </div>
                </div>

                <div class="text-[11px] text-slate-500 space-y-1">
                    <div>{{ $this->formatDateRangeShort($loan->startDate, $loan->endDate) }}</div>
                    @if($isOverdueWarning)
                        <div class="inline-flex items-center gap-1 text-amber-600 dark:text-amber-400 font-semibold">
                            <i class='bx bx-error-circle'></i>
                            Lewat jatuh tempo
                        </div>
                    @endif
                </div>

                <div>
                    @if($loan->member)
                        <a href="{{ route('admin.members.show', $loan->member->id) }}"
                            class="inline-flex items-center justify-center gap-1 w-full text-xs font-semibold text-slate-600 dark:text-slate-300 hover:text-slate-800 dark:hover:text-white px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                            Detail Member
                            <i class='bx bx-chevron-right'></i>
                        </a>
                    @else
                        <span class="text-[11px] text-slate-400">-</span>
                    @endif
                </div>
            </div>
        @empty
            <div class="lg:col-span-2 bg-white dark:bg-darkCard rounded-2xl border border-slate-100 dark:border-slate-700 p-10 text-center">
                <div class="flex flex-col items-center justify-center text-slate-400">
                    <div class="w-14 h-14 rounded-full bg-slate-50 dark:bg-slate-800 flex items-center justify-center mb-3">
                        <i class='bx bx-search-alt text-2xl opacity-60'></i>
                    </div>
                    <p class="font-semibold text-slate-600 dark:text-slate-300">Tidak ada data pinjaman ditemukan</p>
                    <p class="text-xs mt-1">Coba ubah filter atau kata kunci pencarian.</p>
                </div>
            </div>
        @endforelse
    </div>

    <div class="hidden xl:block bg-white dark:bg-darkCard rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300">
                <thead class="bg-slate-50 dark:bg-slate-800/60 border-b border-slate-100 dark:border-slate-700">
                    <tr>
                        <th class="px-4 py-2.5 uppercase font-bold tracking-wider text-slate-400">Anggota</th>
                        <th class="px-3 py-2.5 uppercase font-bold tracking-wider text-slate-400 text-right">Pokok</th>
                        <th class="px-3 py-2.5 uppercase font-bold tracking-wider text-slate-400 text-right">Angsuran/Bln</th>
                        <th class="px-3 py-2.5 uppercase font-bold tracking-wider text-slate-400 text-right">Sisa</th>
                        <th class="px-3 py-2.5 uppercase font-bold tracking-wider text-slate-400">Progress</th>
                        <th class="px-3 py-2.5 uppercase font-bold tracking-wider text-slate-400">Status</th>
                        <th class="px-3 py-2.5 uppercase font-bold tracking-wider text-slate-400">Periode</th>
                        <th class="px-4 py-2.5 uppercase font-bold tracking-wider text-slate-400 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse($loans as $loan)
                        @php
                            $progress = $this->getProgressPercentage($loan);
                            $paidInstallments = (int) ($loan->paid_installments ?? 0);
                            $tenor = (int) ($loan->tenor ?? 0);
                            $isOverdueWarning = $this->isOverdueWarning($loan);
                        @endphp
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
                            <td class="px-4 py-3 align-top">
                                <div>
                                    <p class="font-semibold text-sm text-slate-800 dark:text-white">{{ $loan->member?->name ?? '-' }}</p>
                                    <div class="mt-0.5 flex items-center gap-2">
                                        <p class="text-[11px] text-slate-500">{{ $loan->member?->nomorAnggota ?? '-' }}</p>
                                        <span class="inline-flex px-2 py-0.5 text-[10px] font-bold rounded-full border {{ $this->sourceBadgeClass($loan->loanSource) }}">
                                            {{ $this->formatLoanSource($loan->loanSource) }}
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-3 py-3 align-top text-right font-semibold text-slate-700 dark:text-slate-200" title="Rp {{ number_format($loan->amount, 0, ',', '.') }}">
                                {{ $this->formatCompactCurrency($loan->amount) }}
                            </td>
                            <td class="px-3 py-3 align-top text-right font-semibold text-primary dark:text-indigo-400" title="Rp {{ number_format($loan->monthlyPayment, 0, ',', '.') }}">
                                {{ $this->formatCompactCurrency($loan->monthlyPayment) }}
                            </td>
                            <td class="px-3 py-3 align-top text-right" title="Rp {{ number_format($loan->remainingAmount, 0, ',', '.') }}">
                                <span class="font-bold {{ $loan->remainingAmount > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-emerald-600 dark:text-emerald-400' }}">
                                    {{ $this->formatCompactCurrency($loan->remainingAmount) }}
                                </span>
                            </td>
                            <td class="px-3 py-3 align-top">
                                <div>
                                    <div class="flex items-center justify-between text-[11px] mb-1">
                                        <span class="text-slate-500">{{ $paidInstallments }}/{{ max(0, $tenor) }}</span>
                                        <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $progress }}%</span>
                                    </div>
                                    <div class="h-1.5 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                                        <div class="h-full rounded-full {{ $progress >= 100 ? 'bg-emerald-500' : 'bg-primary' }}" style="width: {{ $progress }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-3 py-3 align-top">
                                <div class="space-y-1">
                                    <span class="inline-flex px-2 py-0.5 text-[10px] font-bold rounded-full border {{ $this->statusBadgeClass($loan->status) }}">
                                        {{ $loan->status }}
                                    </span>
                                    @if($isOverdueWarning)
                                        <div class="text-[10px] font-semibold text-amber-600 dark:text-amber-400 flex items-center gap-1">
                                            <i class='bx bx-error-circle'></i>
                                            Lewat jatuh tempo
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-3 py-3 align-top text-[11px] text-slate-500 whitespace-nowrap">
                                {{ $this->formatDateRangeShort($loan->startDate, $loan->endDate) }}
                            </td>
                            <td class="px-4 py-3 align-top text-center">
                                @if($loan->member)
                                    <a href="{{ route('admin.members.show', $loan->member->id) }}"
                                        class="inline-flex items-center gap-1 text-xs font-semibold text-slate-500 hover:text-slate-800 dark:text-slate-300 dark:hover:text-white px-2.5 py-1 rounded-lg border border-transparent hover:border-slate-200 dark:hover:border-slate-700 hover:bg-white dark:hover:bg-slate-800 transition-colors">
                                        Detail
                                        <i class='bx bx-chevron-right'></i>
                                    </a>
                                @else
                                    <span class="text-[11px] text-slate-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-14 text-center">
                                <div class="flex flex-col items-center justify-center text-slate-400">
                                    <div class="w-16 h-16 rounded-full bg-slate-50 dark:bg-slate-800 flex items-center justify-center mb-3">
                                        <i class='bx bx-search-alt text-3xl opacity-60'></i>
                                    </div>
                                    <p class="font-semibold text-slate-600 dark:text-slate-300">Tidak ada data pinjaman ditemukan</p>
                                    <p class="text-xs mt-1">Coba ubah filter atau kata kunci pencarian.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="px-1.5 py-2.5 bg-slate-50/60 dark:bg-slate-800/30 border border-slate-100 dark:border-slate-700 rounded-xl">
        {{ $loans->links() }}
    </div>
</div>
