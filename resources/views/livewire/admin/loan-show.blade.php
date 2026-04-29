<div class="space-y-6">
    <div class="flex flex-col gap-3">
        <a href="{{ route('admin.loans') }}"
            class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 transition-colors w-fit">
            <i class='bx bx-arrow-back text-base'></i>
            Kembali ke Daftar Pinjaman
        </a>
        <div class="text-xs text-slate-400 dark:text-slate-500">
            Admin / Pinjaman / #{{ $loan->id }}
        </div>
    </div>

    <div class="bg-white dark:bg-darkCard border border-slate-100 dark:border-slate-700 rounded-2xl p-4 sm:p-6 shadow-sm">
        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
            <div class="space-y-2">
                <div class="flex items-center gap-2 flex-wrap">
                    <h1 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white">Detail Pinjaman #{{ $loan->id }}</h1>
                    <span class="inline-flex px-2.5 py-1 text-[10px] font-bold rounded-full border {{ $this->sourceBadgeClass($loan->loanSource) }}">
                        {{ $this->formatLoanSource($loan->loanSource) }}
                    </span>
                    <span class="inline-flex px-2.5 py-1 text-[10px] font-bold rounded-full border {{ $this->statusBadgeClass($loan->status) }}">
                        {{ $loan->status }}
                    </span>
                </div>
                <p class="text-sm text-slate-500 dark:text-slate-400">{{ $loan->member?->name ?? '-' }} • {{ $loan->member?->nomorAnggota ?? '-' }}</p>
                @if($isOverdueWarning)
                    <p class="inline-flex items-center gap-1 text-xs font-semibold text-amber-600 dark:text-amber-400">
                        <i class='bx bx-error-circle'></i>
                        Pinjaman melewati tanggal akhir.
                    </p>
                @endif
            </div>

            <div class="text-left lg:text-right">
                <p class="text-[11px] uppercase tracking-wider font-bold text-slate-400">Sisa Hutang</p>
                <p class="text-2xl font-black {{ $loan->remainingAmount > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-emerald-600 dark:text-emerald-400' }}">
                    {{ $this->formatCurrency($loan->remainingAmount) }}
                </p>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Progress {{ $loan->paid_installments ?? 0 }}/{{ $loan->tenor ?? 0 }} cicilan</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-3 sm:gap-4">
        <div class="rounded-xl border border-slate-100 dark:border-slate-700 bg-white dark:bg-darkCard p-4">
            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Pokok</p>
            <p class="text-lg font-bold text-slate-900 dark:text-white mt-1">{{ $this->formatCurrency($loan->amount) }}</p>
        </div>
        <div class="rounded-xl border border-slate-100 dark:border-slate-700 bg-white dark:bg-darkCard p-4">
            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Angsuran/Bulan</p>
            <p class="text-lg font-bold text-primary dark:text-indigo-400 mt-1">{{ $this->formatCurrency($loan->monthlyPayment) }}</p>
        </div>
        <div class="rounded-xl border border-slate-100 dark:border-slate-700 bg-white dark:bg-darkCard p-4">
            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Sisa Hutang</p>
            <p class="text-lg font-bold {{ $loan->remainingAmount > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-emerald-600 dark:text-emerald-400' }} mt-1">{{ $this->formatCurrency($loan->remainingAmount) }}</p>
        </div>
        <div class="rounded-xl border border-slate-100 dark:border-slate-700 bg-white dark:bg-darkCard p-4">
            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Progress</p>
            <p class="text-lg font-bold text-slate-900 dark:text-white mt-1">{{ $progress }}%</p>
            <div class="h-1.5 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden mt-2">
                <div class="h-full rounded-full {{ $progress >= 100 ? 'bg-emerald-500' : 'bg-primary' }}" style="width: {{ $progress }}%"></div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
        <div class="xl:col-span-8 space-y-6">
            <div class="bg-white dark:bg-darkCard border border-slate-100 dark:border-slate-700 rounded-2xl p-4 sm:p-5">
                <h2 class="text-sm font-bold text-slate-900 dark:text-white mb-4">Profil Pinjaman</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-[11px] uppercase font-bold tracking-wider text-slate-400">Tanggal Mulai</p>
                        <p class="mt-1 text-slate-700 dark:text-slate-200">{{ $loan->startDate ? $loan->startDate->format('d M Y') : '-' }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] uppercase font-bold tracking-wider text-slate-400">Tanggal Akhir</p>
                        <p class="mt-1 text-slate-700 dark:text-slate-200">{{ $loan->endDate ? $loan->endDate->format('d M Y') : '-' }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] uppercase font-bold tracking-wider text-slate-400">Tenor</p>
                        <p class="mt-1 text-slate-700 dark:text-slate-200">{{ $loan->tenor ? $loan->tenor . ' bulan' : '-' }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] uppercase font-bold tracking-wider text-slate-400">Angsuran Terbayar</p>
                        <p class="mt-1 text-slate-700 dark:text-slate-200">{{ $loan->paid_installments ?? 0 }} kali</p>
                    </div>
                    <div>
                        <p class="text-[11px] uppercase font-bold tracking-wider text-slate-400">Margin</p>
                        <p class="mt-1 text-slate-700 dark:text-slate-200">{{ number_format((float) $loan->interestRate, 1, ',', '.') }}%</p>
                    </div>
                    <div>
                        <p class="text-[11px] uppercase font-bold tracking-wider text-slate-400">No. Rekening</p>
                        <p class="mt-1 text-slate-700 dark:text-slate-200">{{ $loan->account_number ?: '-' }}</p>
                    </div>
                    @if($loan->loanSource === 'BMT_ITQAN')
                        <div>
                            <p class="text-[11px] uppercase font-bold tracking-wider text-slate-400">Simwa/Bulan</p>
                            <p class="mt-1 text-slate-700 dark:text-slate-200">{{ $this->formatCurrency($loan->simwa_amount) }}</p>
                        </div>
                    @endif
                    <div class="sm:col-span-2">
                        <p class="text-[11px] uppercase font-bold tracking-wider text-slate-400">Tujuan Pinjaman</p>
                        <p class="mt-1 text-slate-700 dark:text-slate-200">{{ $loan->purpose ?: '-' }}</p>
                    </div>
                    <div class="sm:col-span-2">
                        <p class="text-[11px] uppercase font-bold tracking-wider text-slate-400">Deskripsi</p>
                        <p class="mt-1 text-slate-700 dark:text-slate-200">{{ $loan->description ?: '-' }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-darkCard border border-slate-100 dark:border-slate-700 rounded-2xl p-4 sm:p-5">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-sm font-bold text-slate-900 dark:text-white">Riwayat Pembayaran Pinjaman</h2>
                    <span class="text-xs text-slate-500 dark:text-slate-400">Total tercatat: {{ $this->formatCurrency($totalRecordedPayments) }}</span>
                </div>

                @if($loan->payments->count() > 0)
                    <div class="overflow-x-auto rounded-lg border border-slate-200 dark:border-slate-700">
                        <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300">
                            <thead class="bg-slate-50 dark:bg-slate-800/60 border-b border-slate-100 dark:border-slate-700">
                                <tr>
                                    <th class="px-4 py-2.5 uppercase tracking-wider font-bold text-slate-400">Tanggal</th>
                                    <th class="px-4 py-2.5 uppercase tracking-wider font-bold text-slate-400">Keterangan</th>
                                    <th class="px-4 py-2.5 uppercase tracking-wider font-bold text-slate-400 text-right">Nominal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                                @foreach($loan->payments as $payment)
                                    <tr>
                                        <td class="px-4 py-2.5">{{ $payment->paymentDate ? $payment->paymentDate->format('d M Y') : '-' }}</td>
                                        <td class="px-4 py-2.5">{{ $payment->description ?: '-' }}</td>
                                        <td class="px-4 py-2.5 text-right font-semibold text-slate-800 dark:text-slate-200">{{ $this->formatCurrency($payment->amount) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="rounded-lg border border-dashed border-slate-300 dark:border-slate-600 p-6 text-center text-slate-400 dark:text-slate-500 text-sm">
                        Belum ada riwayat pembayaran pinjaman.
                    </div>
                @endif
            </div>
        </div>

        <div class="xl:col-span-4 space-y-4">
            <div class="bg-white dark:bg-darkCard border border-slate-100 dark:border-slate-700 rounded-2xl p-4 sm:p-5">
                <h2 class="text-sm font-bold text-slate-900 dark:text-white mb-4">Aksi Operasional</h2>

                @if($loan->member)
                    <div class="space-y-2.5">
                        <a href="{{ route('admin.members.show', $loan->member->id) }}"
                            class="w-full inline-flex items-center justify-between rounded-xl border border-slate-200 dark:border-slate-700 px-3 py-2.5 text-sm font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800">
                            Profil Anggota
                            <i class='bx bx-chevron-right text-base'></i>
                        </a>
                        <a href="{{ route('admin.members.simpanan', $loan->member->id) }}"
                            class="w-full inline-flex items-center justify-between rounded-xl border border-slate-200 dark:border-slate-700 px-3 py-2.5 text-sm font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800">
                            Kelola Simpanan
                            <i class='bx bx-chevron-right text-base'></i>
                        </a>
                        <a href="{{ route('admin.members.edit', $loan->member->id) }}"
                            class="w-full inline-flex items-center justify-between rounded-xl border border-slate-200 dark:border-slate-700 px-3 py-2.5 text-sm font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800">
                            Edit Data Anggota/Pinjaman
                            <i class='bx bx-chevron-right text-base'></i>
                        </a>
                        @if($loan->loanSource === 'BMT_ITQAN')
                            <a href="{{ route('admin.loans.import') }}"
                                class="w-full inline-flex items-center justify-between rounded-xl border border-blue-200 dark:border-blue-800 px-3 py-2.5 text-sm font-semibold text-blue-700 dark:text-blue-300 bg-blue-50/70 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/30">
                                Import Angsuran
                                <i class='bx bx-import text-base'></i>
                            </a>
                        @endif
                    </div>
                @else
                    <div class="rounded-lg border border-dashed border-slate-300 dark:border-slate-600 p-5 text-sm text-slate-400 dark:text-slate-500 text-center">
                        Data anggota tidak tersedia untuk pinjaman ini.
                    </div>
                @endif
            </div>

            <div class="bg-white dark:bg-darkCard border border-slate-100 dark:border-slate-700 rounded-2xl p-4 sm:p-5">
                <h2 class="text-sm font-bold text-slate-900 dark:text-white mb-3">Ringkasan Aktivitas</h2>
                <div class="space-y-2 text-xs text-slate-500 dark:text-slate-400">
                    <div class="flex items-center justify-between">
                        <span>Dibuat</span>
                        <span class="font-semibold text-slate-700 dark:text-slate-300">{{ $loan->created_at?->format('d M Y H:i') ?? '-' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Disetujui</span>
                        <span class="font-semibold text-slate-700 dark:text-slate-300">{{ $loan->approvedAt?->format('d M Y H:i') ?? '-' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Pembayaran Terakhir</span>
                        <span class="font-semibold text-slate-700 dark:text-slate-300">{{ $lastPayment?->paymentDate?->format('d M Y') ?? '-' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Update Terakhir</span>
                        <span class="font-semibold text-slate-700 dark:text-slate-300">{{ $loan->updated_at?->format('d M Y H:i') ?? '-' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
