@section('title', 'Pembiayaan Syariah Saya')

<div class="space-y-6">
    {{-- Header Banner --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white/80 dark:bg-zinc-900/80 backdrop-blur-xl p-6 rounded-3xl shadow-sm border border-zinc-200/80 dark:border-zinc-800">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="p-2 rounded-xl bg-[#155A6B]/10 dark:bg-emerald-400/20 text-[#155A6B] dark:text-emerald-400 font-bold">
                    <i class='bx bxs-bank text-xl'></i>
                </span>
                <h1 class="font-heading font-extrabold text-xl text-zinc-900 dark:text-white">Portofolio Pembiayaan Syariah</h1>
            </div>
            <p class="text-xs text-zinc-500 dark:text-zinc-400">
                Informasi akad aktif, sisa kewajiban (outstanding), dan kartu angsuran taqsith bulanan.
            </p>
        </div>
        <div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-extrabold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                <i class='bx bx-check-shield text-base mr-1.5'></i> Transaksi Sesuai Syariah
            </span>
        </div>
    </div>

    {{-- Top Metrics Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        {{-- Total Outstanding Card --}}
        <div class="bg-gradient-to-br from-[#155A6B] to-emerald-600 text-white p-6 rounded-3xl shadow-xl border border-emerald-400/30 relative overflow-hidden">
            <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
            <div class="flex justify-between items-start mb-3">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-100 bg-white/15 backdrop-blur-md px-3 py-0.5 rounded-full">
                    Sisa Kewajiban (Outstanding)
                </span>
                <div class="w-8 h-8 rounded-xl bg-white/20 flex items-center justify-center text-white text-lg">
                    <i class='bx bx-wallet'></i>
                </div>
            </div>
            <h3 class="font-heading text-3xl font-extrabold tracking-tight mb-1 text-white">
                Rp {{ number_format($totalOutstanding, 0, ',', '.') }}
            </h3>
            <p class="text-xs text-emerald-100/90 font-medium">
                Total sisa pokok dari {{ count($activeLoans) }} akad pembiayaan aktif
            </p>
        </div>

        {{-- Monthly Payment Card --}}
        <div class="bg-white/80 dark:bg-zinc-900/80 backdrop-blur-xl p-6 rounded-3xl shadow-sm border border-zinc-200/80 dark:border-zinc-800 flex flex-col justify-between">
            <div class="flex justify-between items-start mb-3">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-zinc-400">
                    Angsuran Bulanan (Taqsith)
                </span>
                <div class="w-8 h-8 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center text-lg">
                    <i class='bx bx-calendar-check'></i>
                </div>
            </div>
            <div>
                <h3 class="font-heading text-2xl sm:text-3xl font-extrabold text-zinc-900 dark:text-white tracking-tight mb-1">
                    Rp {{ number_format($totalMonthlyPayment, 0, ',', '.') }}
                </h3>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 font-medium">
                    Total kewajiban pemotongan per bulan
                </p>
            </div>
        </div>

        {{-- Active Contracts Card --}}
        <div class="bg-white/80 dark:bg-zinc-900/80 backdrop-blur-xl p-6 rounded-3xl shadow-sm border border-zinc-200/80 dark:border-zinc-800 flex flex-col justify-between">
            <div class="flex justify-between items-start mb-3">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-zinc-400">
                    Akad Pembiayaan Aktif
                </span>
                <div class="w-8 h-8 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center text-lg">
                    <i class='bx bx-file-blank'></i>
                </div>
            </div>
            <div>
                <h3 class="font-heading text-2xl sm:text-3xl font-extrabold text-zinc-900 dark:text-white tracking-tight mb-1">
                    {{ count($activeLoans) }} <span class="text-sm font-bold text-zinc-400">Akad</span>
                </h3>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 font-medium">
                    {{ count($completedLoans) }} akad telah selesai (lunas)
                </p>
            </div>
        </div>
    </div>

    {{-- Active Loans List Section --}}
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="font-heading font-extrabold text-base text-zinc-900 dark:text-white flex items-center gap-2">
                <i class='bx bx-list-ul text-emerald-500 text-lg'></i> Daftar Pembiayaan Aktif
            </h2>
            <span class="text-xs text-zinc-400 font-bold">{{ count($activeLoans) }} Pembiayaan Berjalan</span>
        </div>

        @forelse($activeLoans as $loan)
            @php
                $simwaBMT = $loan->simwa_amount ?? 0;
                $monthlyTotal = $loan->monthlyPayment;
                $installmentPure = $monthlyTotal - $simwaBMT;
                $progress = $loan->tenor > 0 ? min(100, ($loan->paid_installments / $loan->tenor) * 100) : 0;
                $isBMT = $loan->loanSource === 'BMT_ITQAN';
            @endphp

            <div class="bg-white/80 dark:bg-zinc-900/80 backdrop-blur-xl rounded-3xl p-6 border border-zinc-200/80 dark:border-zinc-800 shadow-sm relative overflow-hidden">
                {{-- Header: Source & Outstanding Amount --}}
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-5 pb-4 border-b border-zinc-200/60 dark:border-zinc-800">
                    <div class="flex items-center gap-3.5">
                        <div class="w-12 h-12 rounded-2xl {{ $isBMT ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' : 'bg-blue-500/10 text-blue-600 dark:text-blue-400' }} flex items-center justify-center text-2xl shrink-0">
                            <i class='bx {{ $isBMT ? 'bxs-bank' : 'bxs-building-house' }}'></i>
                        </div>
                        <div>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider {{ $isBMT ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' : 'bg-blue-500/10 text-blue-600 dark:text-blue-400' }}">
                                {{ $isBMT ? 'BMT ITQAN' : 'KOPERASI BERMADANI' }}
                            </span>
                            <h4 class="font-heading font-extrabold text-sm text-zinc-900 dark:text-white mt-1">
                                {{ $loan->purpose ? $loan->purpose : ($isBMT ? 'Pembiayaan Syariah BMT' : 'Pembiayaan Koperasi') }}
                            </h4>
                        </div>
                    </div>

                    <div class="text-left sm:text-right bg-zinc-50 dark:bg-zinc-800/50 sm:bg-transparent p-3 sm:p-0 rounded-2xl">
                        <span class="text-[10px] text-zinc-400 font-extrabold uppercase tracking-wider block mb-0.5">
                            Sisa Kewajiban (Outstanding)
                        </span>
                        <h3 class="font-heading text-xl font-extrabold text-emerald-600 dark:text-emerald-400 tracking-tight">
                            Rp {{ number_format($loan->remainingAmount, 0, ',', '.') }}
                        </h3>
                        <p class="text-[10px] text-zinc-400">
                            dari Plafond Rp {{ number_format($loan->amount, 0, ',', '.') }}
                        </p>
                    </div>
                </div>

                {{-- Progress Bar --}}
                <div class="mb-5">
                    <div class="flex justify-between items-end text-xs mb-2">
                        <div>
                            <span class="font-bold text-zinc-800 dark:text-white">
                                Status Angsuran: Ke-{{ $loan->paid_installments }} dari {{ $loan->tenor }} Bulan
                            </span>
                            <span class="text-[10px] text-zinc-400 block mt-0.5">
                                Sisa {{ max(0, $loan->tenor - $loan->paid_installments) }} bulan angsuran lagi
                            </span>
                        </div>
                        <span class="font-extrabold px-2.5 py-0.5 rounded-full text-[10px] uppercase tracking-wider {{ $isBMT ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' : 'bg-blue-500/10 text-blue-600 dark:text-blue-400' }}">
                            {{ round($progress) }}% Terbayar
                        </span>
                    </div>
                    <div class="w-full h-2.5 bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden">
                        <div class="h-full {{ $isBMT ? 'bg-emerald-500' : 'bg-[#155A6B]' }} rounded-full transition-all duration-700 shadow-sm"
                            style="width: {{ $progress }}%"></div>
                    </div>
                </div>

                {{-- Monthly Payment Breakdown --}}
                <div class="bg-zinc-50 dark:bg-zinc-800/40 rounded-2xl p-4 border border-zinc-200/50 dark:border-zinc-700/50 mb-4 text-xs">
                    <div class="flex justify-between items-center mb-2">
                        <span class="font-bold text-zinc-700 dark:text-zinc-300">Potongan Angsuran Per Bulan</span>
                        <span class="font-mono font-extrabold text-sm text-zinc-900 dark:text-white">
                            Rp {{ number_format($monthlyTotal, 0, ',', '.') }}
                        </span>
                    </div>

                    <div class="space-y-1.5 pt-2 border-t border-zinc-200/60 dark:border-zinc-700/60 text-xs">
                        <div class="flex justify-between">
                            <span class="text-zinc-500">Angsuran Pokok + Margin / Ujrah:</span>
                            <span class="font-mono font-bold text-zinc-800 dark:text-zinc-200">
                                Rp {{ number_format($installmentPure, 0, ',', '.') }}
                            </span>
                        </div>
                        @if($simwaBMT > 0)
                            <div class="flex justify-between">
                                <span class="text-emerald-600 dark:text-emerald-400 font-semibold flex items-center gap-1">
                                    <i class='bx bx-plus-circle'></i> Simpanan Wajib Pembiayaan:
                                </span>
                                <span class="font-mono font-extrabold text-emerald-600 dark:text-emerald-400">
                                    Rp {{ number_format($simwaBMT, 0, ',', '.') }}
                                </span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Action Link --}}
                <div class="flex justify-end">
                    <a href="{{ route('member.loans.detail', $loan->id) }}"
                        class="bg-[#155A6B] hover:bg-[#0E3E4B] text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-sm transition-all flex items-center gap-2">
                        <i class='bx bx-history text-base text-emerald-300'></i> Lihat Riwayat Angsuran
                    </a>
                </div>
            </div>
        @empty
            <div class="text-center py-12 px-4 border-2 border-dashed border-zinc-200 dark:border-zinc-800 rounded-3xl bg-zinc-50/50 dark:bg-zinc-900/30">
                <div class="w-14 h-14 bg-emerald-500/10 dark:bg-emerald-400/20 rounded-full flex items-center justify-center text-3xl text-emerald-500 mx-auto mb-3">
                    <i class='bx bx-check-circle'></i>
                </div>
                <h3 class="font-heading font-extrabold text-zinc-900 dark:text-white text-base mb-1">Tidak Ada Pembiayaan Aktif</h3>
                <p class="text-zinc-500 text-xs max-w-md mx-auto">
                    Alhamdulillah, Anda tidak memiliki kewajiban pembiayaan atau angsuran berjalan saat ini.
                </p>
            </div>
        @endforelse
    </div>

    {{-- Completed Loans History Section --}}
    @if(count($completedLoans) > 0)
        <div class="mt-8">
            <h3 class="font-heading font-extrabold text-sm text-zinc-900 dark:text-white mb-3 flex items-center gap-2">
                <i class='bx bx-check-double text-emerald-500 text-lg'></i> Riwayat Pembiayaan Selesai (Lunas)
            </h3>
            <div class="space-y-3">
                @foreach($completedLoans as $loan)
                    <div class="bg-white/80 dark:bg-zinc-900/80 backdrop-blur-xl rounded-2xl p-4 border border-zinc-200/80 dark:border-zinc-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-lg shrink-0">
                                <i class='bx bx-check'></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-xs text-zinc-900 dark:text-white">
                                    {{ $loan->purpose ? $loan->purpose : ($loan->loanSource === 'BMT_ITQAN' ? 'Pembiayaan BMT Itqan' : 'Pembiayaan Koperasi') }}
                                </h4>
                                <p class="text-[10px] text-zinc-400 font-mono">
                                    Plafond: Rp {{ number_format($loan->amount, 0, ',', '.') }} • Tenor: {{ $loan->tenor }} Bulan
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center justify-between sm:justify-end gap-3">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 uppercase">
                                LUNAS
                            </span>
                            <a href="{{ route('member.loans.detail', $loan->id) }}"
                                class="text-xs font-bold text-[#155A6B] dark:text-emerald-400 hover:underline flex items-center gap-1">
                                <i class='bx bx-history'></i> Detail
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>