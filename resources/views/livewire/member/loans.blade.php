@section('title', 'Pinjaman Saya')

<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-end justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-800 dark:text-white">Pinjaman Aktif</h2>
            <p class="text-xs text-slate-500">Daftar kewajiban pinjaman yang sedang berjalan.</p>
        </div>
        {{-- Optional: "Ajukan Pinjaman" button if we implement it logic later --}}
        {{-- <button
            class="px-3 py-1.5 bg-indigo-600 text-white text-xs font-bold rounded-lg shadow-lg shadow-indigo-500/30 flex items-center gap-1">
            <i class='bx bx-plus'></i> Ajukan
        </button> --}}
    </div>

    {{-- Active Loans List --}}
    <div class="space-y-4">
        @forelse($activeLoans as $loan)
            @php
                // Calculation Logic
                $simwaBMT = $loan->simwa_amount ?? 0;
                $monthlyTotal = $loan->monthlyPayment;
                $installmentPure = $monthlyTotal - $simwaBMT;

                $progress = $loan->tenor > 0 ? ($loan->paid_installments / $loan->tenor) * 100 : 0;
                $isBMT = $loan->loanSource === 'BMT_ITQAN';
                $themeColor = $isBMT ? 'emerald' : 'blue';
            @endphp

            <div
                class="bg-white dark:bg-darkCard rounded-2xl p-5 border border-slate-100 dark:border-slate-700 shadow-sm relative overflow-hidden group">
                {{-- Background Decoration --}}
                <div
                    class="absolute -right-10 -top-10 w-40 h-40 bg-{{ $themeColor }}-500/5 rounded-full group-hover:scale-150 transition-transform duration-700">
                </div>

                {{-- Header: Source & Amount --}}
                <div class="flex justify-between items-start mb-6 relative z-10">
                    <div class="flex items-start gap-3">
                        <div
                            class="w-12 h-12 rounded-xl bg-{{ $themeColor }}-50 dark:bg-{{ $themeColor }}-900/20 text-{{ $themeColor }}-500 flex items-center justify-center text-2xl shrink-0">
                            <i class='bx {{ $isBMT ? 'bxs-bank' : 'bxs-building-house' }}'></i>
                        </div>
                        <div>
                            <span
                                class="text-[10px] font-bold uppercase tracking-wider text-{{ $themeColor }}-500 mb-1 block">
                                {{ $isBMT ? 'BMT ITQAN' : 'KOPERASI BERMADANI' }}
                            </span>
                            <h4 class="text-xl font-black text-slate-800 dark:text-white tracking-tight">
                                Rp {{ number_format($loan->remainingAmount, 0, ',', '.') }}
                            </h4>
                            <p class="text-[10px] text-slate-400">Sisa Pokok Pinjaman</p>
                        </div>
                    </div>
                    <div class="text-right">
                        {{-- Status Badge --}}
                        <span
                            class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-100 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20 uppercase">
                            Active
                        </span>
                    </div>
                </div>

                {{-- Progress Bar --}}
                <div class="mb-5 relative z-10">
                    <div class="flex justify-between items-end text-xs mb-2">
                        <div>
                            <span class="font-bold text-slate-700 dark:text-slate-200 block">Angsuran
                                ke-{{ $loan->paid_installments + 1 }}</span>
                            <span class="text-[10px] text-slate-400">dari {{ $loan->tenor }} Bulan</span>
                        </div>
                        <span class="font-bold text-{{ $themeColor }}-500">{{ round($progress) }}%</span>
                    </div>
                    <div class="w-full h-3 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                        <div class="h-full bg-{{ $themeColor }}-500 rounded-full transition-all duration-1000"
                            style="width: {{ $progress }}%"></div>
                    </div>
                </div>

                {{-- Monthly Payment Detail --}}
                <div
                    class="bg-slate-50 dark:bg-slate-800/50 rounded-xl p-4 border border-slate-100 dark:border-slate-700/50 relative z-10">
                    <div class="flex justify-between items-center mb-3">
                        <span class="text-xs font-medium text-slate-600 dark:text-slate-400">Total Potongan Bulanan</span>
                        <span class="text-lg font-bold text-slate-800 dark:text-white">Rp
                            {{ number_format($monthlyTotal, 0, ',', '.') }}</span>
                    </div>

                    {{-- Breakdown --}}
                    <div class="space-y-2 pt-3 border-t border-slate-200 dark:border-slate-700">
                        <div class="flex justify-between text-xs">
                            <span class="text-slate-500">Angsuran Pokok + Margin</span>
                            <span class="font-mono font-medium text-slate-700 dark:text-slate-300">Rp
                                {{ number_format($installmentPure, 0, ',', '.') }}</span>
                        </div>
                        @if($simwaBMT > 0)
                            <div class="flex justify-between text-xs">
                                <span class="text-emerald-600 dark:text-emerald-400 font-medium flex items-center gap-1">
                                    <i class='bx bxs-check-circle'></i> Simpanan Wajib BMT
                                </span>
                                <span class="font-mono font-bold text-emerald-600 dark:text-emerald-400">Rp
                                    {{ number_format($simwaBMT, 0, ',', '.') }}</span>
                            </div>
                            <p class="text-[9px] text-slate-400 mt-1 italic">
                                *Simpanan Wajib khusus anggota pembiayaan BMT Itqan.
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div
                class="text-center py-12 px-4 border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-2xl bg-slate-50 dark:bg-slate-800/30">
                <div
                    class="w-16 h-16 bg-slate-100 dark:bg-slate-700 rounded-full flex items-center justify-center text-3xl text-slate-400 mx-auto mb-4">
                    <i class='bx bx-happy-beaming'></i>
                </div>
                <h3 class="text-slate-800 dark:text-white font-bold text-lg mb-1">Tidak Ada Pinjaman Aktif</h3>
                <p class="text-slate-500 text-sm">Anda tidak memiliki kewajiban pinjaman yang sedang berjalan saat ini.</p>
            </div>
        @endforelse
    </div>

    {{-- Completed Loans History --}}
    @if(count($completedLoans) > 0)
        <div class="mt-8">
            <h3 class="text-sm font-bold text-slate-700 dark:text-slate-300 mb-4 px-1">Riwayat Selesai (Lunas)</h3>
            <div class="space-y-3">
                @foreach($completedLoans as $loan)
                    <div
                        class="bg-white dark:bg-darkCard rounded-xl p-4 border border-slate-100 dark:border-slate-700 flex items-center gap-4 opacity-75 hover:opacity-100 transition-opacity">
                        <div
                            class="w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-500 flex items-center justify-center text-xl shrink-0">
                            <i class='bx bx-check-double'></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-sm font-bold text-slate-700 dark:text-slate-300">
                                {{ $loan->loanSource === 'BMT_ITQAN' ? 'BMT Itqan' : 'Kop. Bermadani' }}
                            </h4>
                            <p class="text-[10px] text-slate-500">Lunas pada: {{ $loan->updated_at->format('d M Y') }}</p>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-bold text-slate-400 line-through">Rp
                                {{ number_format($loan->amount, 0, ',', '.') }}</span>
                            <span class="block text-[9px] font-bold text-emerald-500 uppercase tracking-wider">Lunas</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>