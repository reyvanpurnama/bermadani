@section('title', 'Pembiayaan Syariah Saya')

<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white dark:bg-darkCard p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="p-2 rounded-lg bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">
                    <i class='bx bxs-bank text-2xl'></i>
                </span>
                <h1 class="text-xl font-bold text-slate-800 dark:text-white">Pembiayaan Syariah Saya</h1>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400">
                Portofolio pembiayaan, sisa kewajiban (outstanding), dan riwayat pembayaran angsuran.
            </p>
        </div>
        <div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                <i class='bx bx-check-shield text-sm mr-1'></i> Transaksi Sesuai Syariah
            </span>
        </div>
    </div>

    {{-- Executive Summary Cards (Top Metrics) --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        {{-- Total Outstanding Card --}}
        <div class="bg-gradient-to-br from-emerald-600 to-teal-700 text-white p-5 rounded-2xl shadow-md shadow-emerald-600/10 relative overflow-hidden">
            <div class="absolute -right-4 -bottom-4 w-28 h-28 bg-white/10 rounded-full blur-xl pointer-events-none"></div>
            <div class="flex justify-between items-start mb-3">
                <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-100 bg-white/10 px-2.5 py-1 rounded-full backdrop-blur-sm">
                    Sisa Kewajiban (Outstanding)
                </span>
                <div class="w-8 h-8 rounded-lg bg-white/15 flex items-center justify-center text-white">
                    <i class='bx bx-wallet text-lg'></i>
                </div>
            </div>
            <h3 class="text-2xl font-black tracking-tight mb-1">
                Rp {{ number_format($totalOutstanding, 0, ',', '.') }}
            </h3>
            <p class="text-[11px] text-emerald-100/90 font-medium">
                Total sisa pokok kewajiban dari {{ count($activeLoans) }} akad aktif
            </p>
        </div>

        {{-- Monthly Payment Card --}}
        <div class="bg-white dark:bg-darkCard p-5 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 flex flex-col justify-between">
            <div class="flex justify-between items-start mb-3">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                    Angsuran Bulanan (Taqsith)
                </span>
                <div class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                    <i class='bx bx-calendar-check text-lg'></i>
                </div>
            </div>
            <div>
                <h3 class="text-2xl font-black text-slate-800 dark:text-white tracking-tight mb-1">
                    Rp {{ number_format($totalMonthlyPayment, 0, ',', '.') }}
                </h3>
                <p class="text-[11px] text-slate-400 font-medium">
                    Total kewajiban pemotongan per bulan
                </p>
            </div>
        </div>

        {{-- Active Contracts Card --}}
        <div class="bg-white dark:bg-darkCard p-5 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 flex flex-col justify-between">
            <div class="flex justify-between items-start mb-3">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                    Akad Pembiayaan Aktif
                </span>
                <div class="w-8 h-8 rounded-lg bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 flex items-center justify-center">
                    <i class='bx bx-file-blank text-lg'></i>
                </div>
            </div>
            <div>
                <h3 class="text-2xl font-black text-slate-800 dark:text-white tracking-tight mb-1">
                    {{ count($activeLoans) }} <span class="text-sm font-normal text-slate-400">Akad</span>
                </h3>
                <p class="text-[11px] text-slate-400 font-medium">
                    {{ count($completedLoans) }} akad telah selesai (lunas)
                </p>
            </div>
        </div>
    </div>

    {{-- Active Loans List Section --}}
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-base font-bold text-slate-800 dark:text-white flex items-center gap-2">
                <i class='bx bx-list-ul text-emerald-500'></i> Daftar Pembiayaan Aktif
            </h2>
            <span class="text-xs text-slate-400 font-medium">{{ count($activeLoans) }} Pembiayaan Berjalan</span>
        </div>

        @forelse($activeLoans as $loan)
            @php
                $simwaBMT = $loan->simwa_amount ?? 0;
                $monthlyTotal = $loan->monthlyPayment;
                $installmentPure = $monthlyTotal - $simwaBMT;
                $progress = $loan->tenor > 0 ? min(100, ($loan->paid_installments / $loan->tenor) * 100) : 0;
                $isBMT = $loan->loanSource === 'BMT_ITQAN';
                $themeColor = $isBMT ? 'emerald' : 'indigo';
            @endphp

            <div class="bg-white dark:bg-darkCard rounded-2xl p-6 border border-slate-100 dark:border-slate-700 shadow-sm relative overflow-hidden">
                {{-- Header: Source & Outstanding Amount --}}
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 relative z-10 pb-4 border-b border-slate-100 dark:border-slate-700/60">
                    <div class="flex items-center gap-3.5">
                        <div class="w-12 h-12 rounded-2xl bg-{{ $themeColor }}-50 dark:bg-{{ $themeColor }}-900/30 text-{{ $themeColor }}-600 dark:text-{{ $themeColor }}-400 flex items-center justify-center text-2xl shrink-0">
                            <i class='bx {{ $isBMT ? 'bxs-bank' : 'bxs-building-house' }}'></i>
                        </div>
                        <div>
                            <span class="inline-flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-wider text-{{ $themeColor }}-600 dark:text-{{ $themeColor }}-400 bg-{{ $themeColor }}-50 dark:bg-{{ $themeColor }}-900/30 px-2.5 py-0.5 rounded-full mb-1">
                                {{ $isBMT ? 'BMT ITQAN' : 'KOPERASI BERMADANI' }}
                            </span>
                            <h4 class="text-sm font-bold text-slate-700 dark:text-slate-200">
                                {{ $loan->purpose ? $loan->purpose : ($isBMT ? 'Pembiayaan Syariah BMT' : 'Pembiayaan Koperasi') }}
                            </h4>
                        </div>
                    </div>

                    <div class="text-left sm:text-right bg-slate-50 dark:bg-slate-800/50 sm:bg-transparent p-3 sm:p-0 rounded-xl">
                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block mb-0.5">
                            Sisa Kewajiban (Outstanding)
                        </span>
                        <h3 class="text-xl font-black text-emerald-600 dark:text-emerald-400 tracking-tight">
                            Rp {{ number_format($loan->remainingAmount, 0, ',', '.') }}
                        </h3>
                        <p class="text-[10px] text-slate-400">
                            dari Plafond Awal Rp {{ number_format($loan->amount, 0, ',', '.') }}
                        </p>
                    </div>
                </div>

                {{-- Progress Bar --}}
                <div class="mb-5 relative z-10">
                    <div class="flex justify-between items-end text-xs mb-2">
                        <div>
                            <span class="font-bold text-slate-700 dark:text-slate-200">
                                Status Angsuran: Ke-{{ $loan->paid_installments }} dari {{ $loan->tenor }} Bulan
                            </span>
                            <span class="text-[10px] text-slate-400 block mt-0.5">
                                Sisa {{ max(0, $loan->tenor - $loan->paid_installments) }} bulan angsuran lagi
                            </span>
                        </div>
                        <span class="font-black text-{{ $themeColor }}-600 dark:text-{{ $themeColor }}-400 bg-{{ $themeColor }}-50 dark:bg-{{ $themeColor }}-900/30 px-2 py-0.5 rounded-lg">
                            {{ round($progress) }}% Terbayar
                        </span>
                    </div>
                    <div class="w-full h-3 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden p-0.5">
                        <div class="h-full bg-gradient-to-r from-{{ $themeColor }}-500 to-teal-500 rounded-full transition-all duration-700 shadow-sm"
                            style="width: {{ $progress }}%"></div>
                    </div>
                </div>

                {{-- Monthly Payment Breakdown --}}
                <div class="bg-slate-50 dark:bg-slate-800/40 rounded-xl p-4 border border-slate-100 dark:border-slate-700/50 mb-4">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-xs font-bold text-slate-700 dark:text-slate-300">Potongan Angsuran Per Bulan</span>
                        <span class="text-base font-black text-slate-800 dark:text-white font-mono">
                            Rp {{ number_format($monthlyTotal, 0, ',', '.') }}
                        </span>
                    </div>

                    <div class="space-y-1.5 pt-2 border-t border-slate-200/60 dark:border-slate-700/60 text-xs">
                        <div class="flex justify-between">
                            <span class="text-slate-500">Angsuran Pokok + Margin / Ujrah:</span>
                            <span class="font-mono font-medium text-slate-700 dark:text-slate-300">
                                Rp {{ number_format($installmentPure, 0, ',', '.') }}
                            </span>
                        </div>
                        @if($simwaBMT > 0)
                            <div class="flex justify-between">
                                <span class="text-emerald-600 dark:text-emerald-400 font-medium flex items-center gap-1">
                                    <i class='bx bx-plus-circle'></i> Simpanan Wajib Pembiayaan:
                                </span>
                                <span class="font-mono font-bold text-emerald-600 dark:text-emerald-400">
                                    Rp {{ number_format($simwaBMT, 0, ',', '.') }}
                                </span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Action Link --}}
                <div class="flex justify-end relative z-10">
                    <a href="{{ route('member.loans.detail', $loan->id) }}"
                        class="bg-slate-800 dark:bg-slate-700 hover:bg-slate-900 dark:hover:bg-slate-600 text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-sm transition-all flex items-center gap-2">
                        <i class='bx bx-history text-base text-emerald-400'></i> Lihat Riwayat Angsuran
                    </a>
                </div>
            </div>
        @empty
            <div class="text-center py-12 px-4 border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-2xl bg-slate-50/50 dark:bg-slate-800/20">
                <div class="w-16 h-16 bg-emerald-50 dark:bg-emerald-950/40 rounded-full flex items-center justify-center text-3xl text-emerald-500 mx-auto mb-3">
                    <i class='bx bx-check-circle'></i>
                </div>
                <h3 class="text-slate-800 dark:text-white font-bold text-base mb-1">Tidak Ada Pembiayaan Aktif</h3>
                <p class="text-slate-500 text-xs max-w-md mx-auto">
                    Alhamdulillah, Anda tidak memiliki kewajiban pembiayaan atau angsuran berjalan saat ini.
                </p>
            </div>
        @endforelse
    </div>

    {{-- Completed Loans History Section --}}
    @if(count($completedLoans) > 0)
        <div class="mt-8">
            <h3 class="text-sm font-bold text-slate-700 dark:text-slate-300 mb-3 flex items-center gap-2">
                <i class='bx bx-check-double text-emerald-500 text-lg'></i> Riwayat Pembiayaan Selesai (Lunas / Tabarru')
            </h3>
            <div class="space-y-3">
                @foreach($completedLoans as $loan)
                    <div class="bg-white dark:bg-darkCard rounded-xl p-4 border border-slate-100 dark:border-slate-700 flex flex-col sm:flex-row sm:items-center justify-between gap-3 opacity-80 hover:opacity-100 transition-opacity">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-lg shrink-0">
                                <i class='bx bx-check'></i>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-slate-800 dark:text-white">
                                    {{ $loan->purpose ? $loan->purpose : ($loan->loanSource === 'BMT_ITQAN' ? 'Pembiayaan BMT Itqan' : 'Pembiayaan Kop. Bermadani') }}
                                </h4>
                                <p class="text-[10px] text-slate-400 font-mono">
                                    Plafond Awal: Rp {{ number_format($loan->amount, 0, ',', '.') }} • Tenor: {{ $loan->tenor }} Bulan
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center justify-between sm:justify-end gap-3">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-400">
                                LUNAS
                            </span>
                            <a href="{{ route('member.loans.detail', $loan->id) }}"
                                class="text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:underline flex items-center gap-1">
                                <i class='bx bx-history'></i> Detail
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>