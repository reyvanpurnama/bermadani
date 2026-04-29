<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Pinjaman Anggota</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Monitoring pinjaman aktif, sisa hutang, dan progres angsuran anggota.</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.loans.import') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 text-sm font-semibold hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                <i class='bx bx-import text-base'></i>
                Import
            </a>
            <a href="{{ route('admin.loans.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-primary hover:bg-primary/90 text-white text-sm font-bold shadow-sm shadow-primary/20 transition-colors">
                <i class='bx bx-plus text-base'></i>
                Tambah Pinjaman
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-darkCard border border-slate-100 dark:border-slate-700 rounded-xl shadow-sm p-4">
            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Pinjaman Berjalan</p>
            <p class="text-2xl font-bold text-slate-900 dark:text-white mt-1">{{ number_format($stats['activeLoans']) }}</p>
            <p class="text-xs text-slate-500 mt-1">Status ACTIVE + OVERDUE</p>
        </div>

        <div class="bg-white dark:bg-darkCard border border-slate-100 dark:border-slate-700 rounded-xl shadow-sm p-4">
            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Outstanding</p>
            <p class="text-2xl font-bold text-slate-900 dark:text-white mt-1">Rp {{ number_format($stats['outstandingTotal'], 0, ',', '.') }}</p>
            <p class="text-xs text-slate-500 mt-1">Total sisa hutang berjalan</p>
        </div>

        <div class="bg-white dark:bg-darkCard border border-slate-100 dark:border-slate-700 rounded-xl shadow-sm p-4">
            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Angsuran/Bulan</p>
            <p class="text-2xl font-bold text-slate-900 dark:text-white mt-1">Rp {{ number_format($stats['monthlyInstallmentTotal'], 0, ',', '.') }}</p>
            <p class="text-xs text-slate-500 mt-1">Akumulasi angsuran aktif</p>
        </div>

        <div class="bg-white dark:bg-darkCard border border-slate-100 dark:border-slate-700 rounded-xl shadow-sm p-4">
            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Debitur Aktif</p>
            <p class="text-2xl font-bold text-slate-900 dark:text-white mt-1">{{ number_format($stats['activeDebtors']) }}</p>
            <p class="text-xs text-slate-500 mt-1">Anggota dengan pinjaman berjalan</p>
        </div>
    </div>

    <div class="bg-white dark:bg-darkCard rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 p-1.5">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div class="relative sm:col-span-2">
                <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-slate-400">
                    <i class='bx bx-search text-lg'></i>
                </div>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama atau nomor anggota..."
                    class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl pl-10 pr-4 py-2.5 text-sm focus:ring-2 focus:ring-primary/20 text-slate-700 dark:text-white placeholder-slate-400">
            </div>

            <div class="relative">
                <select wire:model.live="filterStatus"
                    class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary/20 text-slate-700 dark:text-white cursor-pointer appearance-none">
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

            <div class="flex items-center gap-2">
                <div class="relative flex-1">
                    <select wire:model.live="filterSource"
                        class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary/20 text-slate-700 dark:text-white cursor-pointer appearance-none">
                        <option value="">Semua Sumber</option>
                        <option value="BERMADANI">Bermadani</option>
                        <option value="BMT_ITQAN">BMT Itqan</option>
                    </select>
                    <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none">
                        <i class='bx bx-chevron-down text-slate-400'></i>
                    </div>
                </div>

                <button wire:click="clearFilters"
                    class="px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-500 hover:text-slate-700 dark:text-slate-300 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800 text-xs font-semibold transition-colors">
                    Reset
                </button>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-darkCard rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600 dark:text-slate-300">
                <thead class="bg-slate-50 dark:bg-slate-800/60 border-b border-slate-100 dark:border-slate-700">
                    <tr>
                        <th class="px-6 py-3 text-[10px] uppercase font-bold tracking-wider text-slate-400">Anggota</th>
                        <th class="px-6 py-3 text-[10px] uppercase font-bold tracking-wider text-slate-400">Sumber</th>
                        <th class="px-6 py-3 text-[10px] uppercase font-bold tracking-wider text-slate-400 text-right">Pokok</th>
                        <th class="px-6 py-3 text-[10px] uppercase font-bold tracking-wider text-slate-400 text-right">Angsuran/Bulan</th>
                        <th class="px-6 py-3 text-[10px] uppercase font-bold tracking-wider text-slate-400 text-right">Sisa Hutang</th>
                        <th class="px-6 py-3 text-[10px] uppercase font-bold tracking-wider text-slate-400">Progress Tenor</th>
                        <th class="px-6 py-3 text-[10px] uppercase font-bold tracking-wider text-slate-400">Status</th>
                        <th class="px-6 py-3 text-[10px] uppercase font-bold tracking-wider text-slate-400">Mulai/Akhir</th>
                        <th class="px-6 py-3 text-[10px] uppercase font-bold tracking-wider text-slate-400 text-center">Aksi</th>
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
                            <td class="px-6 py-4 align-top">
                                <div class="min-w-[200px]">
                                    <p class="font-semibold text-slate-800 dark:text-white">{{ $loan->member?->name ?? '-' }}</p>
                                    <p class="text-[11px] text-slate-500 mt-0.5">{{ $loan->member?->nomorAnggota ?? '-' }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4 align-top">
                                <span class="inline-flex px-2.5 py-1 text-[10px] font-bold rounded-full border {{ $this->sourceBadgeClass($loan->loanSource) }}">
                                    {{ $this->formatLoanSource($loan->loanSource) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 align-top text-right font-semibold text-slate-700 dark:text-slate-200">
                                Rp {{ number_format($loan->amount, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 align-top text-right font-semibold text-primary dark:text-indigo-400">
                                Rp {{ number_format($loan->monthlyPayment, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 align-top text-right">
                                <span class="font-bold {{ $loan->remainingAmount > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-emerald-600 dark:text-emerald-400' }}">
                                    Rp {{ number_format($loan->remainingAmount, 0, ',', '.') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 align-top">
                                <div class="min-w-[170px]">
                                    <div class="flex items-center justify-between text-[11px] mb-1">
                                        <span class="text-slate-500">{{ $paidInstallments }}/{{ max(0, $tenor) }} cicilan</span>
                                        <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $progress }}%</span>
                                    </div>
                                    <div class="h-1.5 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                                        <div class="h-full rounded-full {{ $progress >= 100 ? 'bg-emerald-500' : 'bg-primary' }}" style="width: {{ $progress }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 align-top">
                                <div class="space-y-1">
                                    <span class="inline-flex px-2.5 py-1 text-[10px] font-bold rounded-full border {{ $this->statusBadgeClass($loan->status) }}">
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
                            <td class="px-6 py-4 align-top">
                                <div class="text-[11px] text-slate-500">
                                    <div>Mulai: {{ $loan->startDate ? $loan->startDate->format('d M Y') : '-' }}</div>
                                    <div>Akhir: {{ $loan->endDate ? $loan->endDate->format('d M Y') : '-' }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 align-top text-center">
                                @if($loan->member)
                                    <a href="{{ route('admin.members.show', $loan->member->id) }}"
                                        class="inline-flex items-center gap-1 text-xs font-semibold text-slate-500 hover:text-slate-800 dark:text-slate-300 dark:hover:text-white px-3 py-1.5 rounded-lg border border-transparent hover:border-slate-200 dark:hover:border-slate-700 hover:bg-white dark:hover:bg-slate-800 transition-colors">
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
                            <td colspan="9" class="px-6 py-14 text-center">
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

        <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/30">
            {{ $loans->links() }}
        </div>
    </div>
</div>
