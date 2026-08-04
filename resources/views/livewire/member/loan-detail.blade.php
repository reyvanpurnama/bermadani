@section('title', 'Detail & Riwayat Angsuran Pembiayaan')

<div class="space-y-6">
    {{-- Navigation & Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white dark:bg-darkCard p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('member.loans') }}"
                    class="p-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 transition-all">
                    <i class='bx bx-left-arrow-alt text-xl'></i>
                </a>
                <span class="p-2 rounded-lg bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">
                    <i class='bx bx-history text-2xl'></i>
                </span>
                <h1 class="text-xl font-bold text-slate-800 dark:text-white">Detail & Riwayat Angsuran</h1>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400">
                Rincian kontrak pembiayaan syariah dan mutasi angsuran terbayar.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('member.loans') }}"
                class="bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 transition-all flex items-center gap-2">
                <i class='bx bx-arrow-back text-base'></i> Kembali ke Daftar
            </a>
        </div>
    </div>

    @php
        $isBMT = $loan->loanSource === 'BMT_ITQAN';
        $themeColor = $isBMT ? 'emerald' : 'indigo';
        $simwaBMT = $loan->simwa_amount ?? 0;
        $monthlyTotal = $loan->monthlyPayment;
        $installmentPure = $monthlyTotal - $simwaBMT;
        $progress = $loan->tenor > 0 ? min(100, ($loan->paid_installments / $loan->tenor) * 100) : 0;
    @endphp

    {{-- Contract Header Info Card --}}
    <div class="bg-white dark:bg-darkCard p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 relative overflow-hidden">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 pb-6 border-b border-slate-100 dark:border-slate-700">
            <div class="flex items-start gap-4">
                <div class="w-14 h-14 rounded-2xl bg-{{ $themeColor }}-50 dark:bg-{{ $themeColor }}-900/30 text-{{ $themeColor }}-600 dark:text-{{ $themeColor }}-400 flex items-center justify-center text-3xl shrink-0">
                    <i class='bx {{ $isBMT ? 'bxs-bank' : 'bxs-building-house' }}'></i>
                </div>
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-{{ $themeColor }}-600 dark:text-{{ $themeColor }}-400 bg-{{ $themeColor }}-50 dark:bg-{{ $themeColor }}-900/30 px-2.5 py-0.5 rounded-full">
                            {{ $isBMT ? 'BMT ITQAN' : 'KOPERASI BERMADANI' }}
                        </span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $loan->status === 'ACTIVE' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400' }}">
                            {{ $loan->status === 'ACTIVE' ? 'AKAD AKTIF' : 'LUNAS / SELESAI' }}
                        </span>
                    </div>
                    <h2 class="text-lg font-bold text-slate-800 dark:text-white">
                        {{ $loan->purpose ? $loan->purpose : ($isBMT ? 'Pembiayaan Syariah BMT' : 'Pembiayaan Koperasi') }}
                    </h2>
                </div>
            </div>

            <div class="bg-gradient-to-br from-emerald-600 to-teal-700 text-white p-4 rounded-xl shadow-sm text-right">
                <span class="text-[10px] text-emerald-100 font-bold uppercase tracking-wider block mb-0.5">
                    Sisa Kewajiban (Outstanding)
                </span>
                <h3 class="text-2xl font-black tracking-tight">
                    Rp {{ number_format($loan->remainingAmount, 0, ',', '.') }}
                </h3>
                <p class="text-[10px] text-emerald-100/90 font-medium">
                    dari Plafond Awal Rp {{ number_format($loan->amount, 0, ',', '.') }}
                </p>
            </div>
        </div>

        {{-- Metrics Row --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-6">
            <div class="bg-slate-50 dark:bg-slate-800/40 p-3.5 rounded-xl border border-slate-100 dark:border-slate-700/50">
                <span class="text-[10px] font-bold uppercase text-slate-400 block mb-0.5">Plafond Pembiayaan</span>
                <h4 class="text-sm font-bold text-slate-800 dark:text-white font-mono">
                    Rp {{ number_format($loan->amount, 0, ',', '.') }}
                </h4>
            </div>

            <div class="bg-slate-50 dark:bg-slate-800/40 p-3.5 rounded-xl border border-slate-100 dark:border-slate-700/50">
                <span class="text-[10px] font-bold uppercase text-slate-400 block mb-0.5">Potongan Per Bulan</span>
                <h4 class="text-sm font-bold text-slate-800 dark:text-white font-mono">
                    Rp {{ number_format($monthlyTotal, 0, ',', '.') }}
                </h4>
            </div>

            <div class="bg-slate-50 dark:bg-slate-800/40 p-3.5 rounded-xl border border-slate-100 dark:border-slate-700/50">
                <span class="text-[10px] font-bold uppercase text-slate-400 block mb-0.5">Tenor & Progress</span>
                <h4 class="text-sm font-bold text-slate-800 dark:text-white font-mono">
                    {{ $loan->paid_installments }} / {{ $loan->tenor }} Bulan ({{ round($progress) }}%)
                </h4>
            </div>

            <div class="bg-slate-50 dark:bg-slate-800/40 p-3.5 rounded-xl border border-slate-100 dark:border-slate-700/50">
                <span class="text-[10px] font-bold uppercase text-slate-400 block mb-0.5">Periode Akad</span>
                <h4 class="text-xs font-bold text-slate-800 dark:text-white font-mono">
                    {{ $loan->startDate ? $loan->startDate->format('M Y') : '-' }} s/d {{ $loan->endDate ? $loan->endDate->format('M Y') : '-' }}
                </h4>
            </div>
        </div>

        {{-- Progress Bar --}}
        <div class="mt-6">
            <div class="flex justify-between items-center text-xs mb-1.5">
                <span class="font-bold text-slate-700 dark:text-slate-300">Kemajuan Pelunasan Angsuran</span>
                <span class="font-bold text-emerald-600 dark:text-emerald-400 font-mono">{{ round($progress) }}% Terbayar</span>
            </div>
            <div class="w-full h-3 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden p-0.5">
                <div class="h-full bg-gradient-to-r from-emerald-500 to-teal-500 rounded-full transition-all duration-1000 shadow-sm"
                    style="width: {{ $progress }}%"></div>
            </div>
        </div>
    </div>

    {{-- Breakdown Detail Card --}}
    <div class="bg-white dark:bg-darkCard p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
        <h3 class="text-sm font-bold text-slate-800 dark:text-white mb-4 flex items-center gap-2">
            <i class='bx bx-pie-chart-alt-2 text-emerald-500 text-lg'></i> Rincian Skema Potongan Angsuran Syariah
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
            <div class="bg-slate-50 dark:bg-slate-800/40 p-4 rounded-xl border border-slate-100 dark:border-slate-700/50 flex justify-between items-center">
                <div>
                    <span class="font-bold text-slate-700 dark:text-slate-300 block">Angsuran Pokok + Margin / Ujrah</span>
                    <span class="text-[10px] text-slate-400">Porsi angsuran pokok dan margin pembiayaan</span>
                </div>
                <span class="font-mono font-bold text-sm text-slate-800 dark:text-white">
                    Rp {{ number_format($installmentPure, 0, ',', '.') }}
                </span>
            </div>

            @if($simwaBMT > 0)
                <div class="bg-emerald-50/50 dark:bg-emerald-950/20 p-4 rounded-xl border border-emerald-100 dark:border-emerald-900/30 flex justify-between items-center">
                    <div>
                        <span class="font-bold text-emerald-700 dark:text-emerald-400 block flex items-center gap-1">
                            <i class='bx bx-check-circle'></i> Simpanan Wajib Pembiayaan BMT
                        </span>
                        <span class="text-[10px] text-emerald-600/80 dark:text-emerald-400/80">Simpanan wajib khusus anggota pembiayaan</span>
                    </div>
                    <span class="font-mono font-bold text-sm text-emerald-600 dark:text-emerald-400">
                        Rp {{ number_format($simwaBMT, 0, ',', '.') }}
                    </span>
                </div>
            @else
                <div class="bg-slate-50 dark:bg-slate-800/40 p-4 rounded-xl border border-slate-100 dark:border-slate-700/50 flex justify-between items-center">
                    <div>
                        <span class="font-bold text-slate-700 dark:text-slate-300 block">Simpanan Wajib Pembiayaan</span>
                        <span class="text-[10px] text-slate-400">Termasuk dalam skema simpanan utama koperasi</span>
                    </div>
                    <span class="font-mono font-bold text-sm text-slate-400">
                        Rp 0
                    </span>
                </div>
            @endif
        </div>
    </div>

    {{-- Repayment History Table Section --}}
    <div class="bg-white dark:bg-darkCard p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-bold text-slate-800 dark:text-white flex items-center gap-2">
                <i class='bx bx-table text-emerald-500 text-lg'></i> Tabel Mutasi & History Pembayaran Angsuran
            </h3>
            <span class="text-xs text-slate-400 font-mono font-medium">
                {{ count($payments) }} Catatan Transaksi
            </span>
        </div>

        @if(count($payments) > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-800 text-slate-400 uppercase text-[10px] tracking-wider border-b border-slate-200 dark:border-slate-700">
                            <th class="py-3 px-3">No</th>
                            <th class="py-3 px-3">Tanggal Bayar</th>
                            <th class="py-3 px-3 text-right">Nominal Angsuran</th>
                            <th class="py-3 px-3">Keterangan Transaksi</th>
                            <th class="py-3 px-3 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @foreach($payments as $index => $payment)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30">
                                <td class="py-3 px-3 font-mono text-slate-400">{{ $index + 1 }}</td>
                                <td class="py-3 px-3 font-mono text-slate-700 dark:text-slate-300">
                                    {{ $payment->paymentDate ? $payment->paymentDate->format('d F Y') : '-' }}
                                </td>
                                <td class="py-3 px-3 text-right font-mono font-bold text-emerald-600 dark:text-emerald-400">
                                    Rp {{ number_format((float) $payment->amount, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-3 text-slate-600 dark:text-slate-300">
                                    {{ $payment->description ?? 'Potongan Gaji / Setoran Angsuran Bulanan' }}
                                </td>
                                <td class="py-3 px-3 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 uppercase">
                                        DIBAYAR
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            {{-- Fallback: Interactive Monthly Installment Schedule Timeline --}}
            <div class="space-y-2">
                <p class="text-xs text-slate-400 italic mb-4">
                    Jadwal dan status pembayaran angsuran bulanan berdasarkan mutasi pembukuan pembiayaan:
                </p>
                <div class="grid grid-cols-1 gap-2.5">
                    @for($i = 1; $i <= $loan->tenor; $i++)
                        @php $isPaid = $i <= $loan->paid_installments; @endphp
                        <div class="flex items-center justify-between p-3.5 rounded-xl border transition-all {{ $isPaid ? 'bg-emerald-50/40 border-emerald-200/70 dark:bg-emerald-950/20 dark:border-emerald-900/50' : 'bg-slate-50/40 border-slate-100 dark:bg-slate-800/20 dark:border-slate-700/50 opacity-60' }}">
                            <div class="flex items-center gap-3.5">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold font-mono {{ $isPaid ? 'bg-emerald-600 text-white shadow-sm' : 'bg-slate-200 text-slate-500 dark:bg-slate-700 dark:text-slate-400' }}">
                                    {{ $i }}
                                </div>
                                <div>
                                    <h5 class="text-xs font-bold {{ $isPaid ? 'text-slate-800 dark:text-white' : 'text-slate-400' }}">
                                        Angsuran Bulan Ke-{{ $i }}
                                    </h5>
                                    <p class="text-[10px] text-slate-400 font-mono">
                                        {{ $isPaid ? 'Status: Dipotong rutin dari payroll / disetor' : 'Status: Belum jatuh tempo' }}
                                    </p>
                                </div>
                            </div>

                            <div class="text-right">
                                <span class="text-xs font-mono font-bold block {{ $isPaid ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400' }}">
                                    Rp {{ number_format($loan->monthlyPayment, 0, ',', '.') }}
                                </span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider {{ $isPaid ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300' : 'bg-slate-100 text-slate-400 dark:bg-slate-800 dark:text-slate-500' }}">
                                    {{ $isPaid ? 'LUNAS / TERBAYAR' : 'BELUM BAYAR' }}
                                </span>
                            </div>
                        </div>
                    @endfor
                </div>
            </div>
        @endif
    </div>
</div>
