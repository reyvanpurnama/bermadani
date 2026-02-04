@extends('layouts.member')

@section('title', 'Beranda')

@section('content')

    <!-- Profile Incomplete Alert -->
    @if($isProfileIncomplete)
        <div
            class="mb-6 p-4 rounded-2xl bg-gradient-to-r from-amber-500 to-orange-500 text-white shadow-lg shadow-orange-500/20 relative overflow-hidden">
            <div class="relative z-10 flex items-center gap-4">
                <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center text-2xl animate-pulse">
                    <i class='bx bx-error-circle'></i>
                </div>
                <div class="flex-1">
                    <h4 class="font-bold text-sm">Lengkapi Profil Anda</h4>
                    <p class="text-[11px] opacity-90">Data Anda masih belum lengkap. Update sekarang untuk keamanan.</p>
                </div>
                <button class="px-3 py-1.5 rounded-lg bg-white/20 text-xs font-bold hover:bg-white/30 transition">
                    Edit
                </button>
            </div>
            <!-- Decor -->
            <div class="absolute -right-5 -bottom-10 w-32 h-32 bg-white/10 rounded-full"></div>
        </div>
    @endif

    <!-- Digital Member Card (Flip Effect) -->
    <div class="perspective-1000 w-full h-56 relative group cursor-pointer mb-8" onclick="this.classList.toggle('flip')">
        <!-- Front -->
        <div
            class="absolute inset-0 w-full h-full rounded-2xl bg-gradient-to-br from-slate-900 to-slate-800 text-white shadow-2xl p-6 flex flex-col justify-between overflow-hidden border border-white/10 backface-hidden transition-transform duration-700">
            <!-- Background Pattern -->
            <div class="absolute inset-0 opacity-10"
                style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 24px 24px;">
            </div>
            <div class="absolute top-0 right-0 w-40 h-40 bg-primary/30 rounded-full blur-[50px] -mr-10 -mt-10"></div>

            <div class="relative z-10 flex justify-between items-start">
                <div>
                    <h5 class="text-xs font-bold opacity-60 tracking-widest uppercase">Member Card</h5>
                    <h3 class="text-lg font-bold mt-1 tracking-wider">KOPERASI UMB</h3>
                </div>
                <div
                    class="px-2 py-1 rounded bg-white/10 border border-white/10 backdrop-blur-md text-[10px] font-bold uppercase tracking-wider">
                    {{ $member->tier ?? 'Bronze' }}
                </div>
            </div>

            <div class="relative z-10 flex items-end justify-between">
                <div>
                    <h4 class="text-lg font-bold tracking-wide">{{ $member->name }}</h4>
                    <p class="text-xs font-mono opacity-70 mt-1">{{ $member->nomorAnggota }}</p>
                </div>
                <div class="bg-white p-1 rounded-lg">
                    <canvas id="qr-code" class="w-16 h-16"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Loans -->
    @if(count($activeLoans) > 0)
        <div class="mb-8">
            <h3 class="text-sm font-bold text-slate-800 dark:text-white mb-4 flex items-center gap-2">
                <i class='bx bx-wallet-alt text-lg text-indigo-500'></i> Pinjaman Aktif
            </h3>

            <div class="flex overflow-x-auto gap-4 pb-4 snap-x hide-scrollbar">
                @foreach($activeLoans as $loan)
                    @php
                        // Calculation Logic for Display
                        $simwaBMT = $loan->simwa_amount ?? 0;
                        $monthlyTotal = $loan->monthlyPayment;
                        $installmentPure = $monthlyTotal - $simwaBMT;

                        $progress = $loan->tenor > 0 ? ($loan->paid_installments / $loan->tenor) * 100 : 0;
                        $isBMT = $loan->loanSource === 'BMT_ITQAN';
                    @endphp

                    <div
                        class="snap-center shrink-0 w-80 bg-white dark:bg-darkCard rounded-2xl p-5 border border-slate-100 dark:border-white/5 shadow-sm relative overflow-hidden group">
                        {{-- Background Decoration --}}
                        <div
                            class="absolute -right-10 -top-10 w-32 h-32 {{ $isBMT ? 'bg-emerald-500/5' : 'bg-blue-500/5' }} rounded-full group-hover:scale-150 transition-transform duration-700">
                        </div>

                        {{-- Header: Source & Amount --}}
                        <div class="flex justify-between items-start mb-4 relative z-10">
                            <div>
                                <span
                                    class="text-[10px] font-bold uppercase tracking-wider {{ $isBMT ? 'text-emerald-500' : 'text-blue-500' }} mb-1 block">
                                    {{ $loan->loanSource === 'BMT_ITQAN' ? 'BMT ITQAN' : 'KOP. BERMADANI' }}
                                </span>
                                <h4 class="text-lg font-black text-slate-800 dark:text-white">
                                    Rp {{ number_format($loan->remainingAmount, 0, ',', '.') }}
                                </h4>
                                <p class="text-[10px] text-slate-400">Sisa Pinjaman</p>
                            </div>
                            <div
                                class="w-10 h-10 rounded-full {{ $isBMT ? 'bg-emerald-50 text-emerald-500' : 'bg-blue-50 text-blue-500' }} flex items-center justify-center text-xl">
                                <i class='bx {{ $isBMT ? 'bxs-bank' : 'bxs-building-house' }}'></i>
                            </div>
                        </div>

                        {{-- Progress Bar --}}
                        <div class="mb-4 relative z-10">
                            <div class="flex justify-between text-[10px] font-bold text-slate-500 mb-1">
                                <span>Angsuran ke-{{ $loan->paid_installments + 1 }}</span>
                                <span>{{ $loan->tenor }} Bulan</span>
                            </div>
                            <div class="w-full h-2 bg-slate-100 dark:bg-slate-700/50 rounded-full overflow-hidden">
                                <div class="h-full {{ $isBMT ? 'bg-emerald-500' : 'bg-blue-500' }} rounded-full"
                                    style="width: {{ $progress }}%"></div>
                            </div>
                        </div>

                        {{-- Monthly Payment Detail --}}
                        <div
                            class="bg-slate-50 dark:bg-slate-800/50 rounded-xl p-3 border border-slate-100 dark:border-white/5 relative z-10">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-xs text-slate-500">Potongan Bulanan</span>
                                <span class="text-sm font-bold text-slate-800 dark:text-white">Rp
                                    {{ number_format($monthlyTotal, 0, ',', '.') }}</span>
                            </div>

                            {{-- Breakdown (Accordion Style) --}}
                            <div class="space-y-1 pt-2 border-t border-slate-200 dark:border-slate-700/50">
                                <div class="flex justify-between text-[10px]">
                                    <span class="text-slate-400">Angsuran Pokok + Margin</span>
                                    <span class="font-mono text-slate-600 dark:text-slate-300">Rp
                                        {{ number_format($installmentPure, 0, ',', '.') }}</span>
                                </div>
                                @if($simwaBMT > 0)
                                    <div class="flex justify-between text-[10px]">
                                        <span class="text-emerald-500 font-medium">Simpanan Wajib BMT</span>
                                        <span class="font-mono text-emerald-600 dark:text-emerald-400 font-bold">Rp
                                            {{ number_format($simwaBMT, 0, ',', '.') }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <!-- Debug/Empty State -->
        <div class="mb-8 p-4 bg-slate-50 dark:bg-darkCard rounded-2xl border border-dashed border-slate-300 dark:border-slate-700 text-center">
            <p class="text-xs text-slate-500">Tidak ada pinjaman aktif yang tercatat di sistem.</p>
        </div>
    @endif

    <!-- Stats Grid -->
    <div class="grid grid-cols-2 gap-4 mb-8">
        <!-- Points -->
        <div
            class="bg-white dark:bg-darkCard p-5 rounded-2xl shadow-sm border border-slate-100 dark:border-white/5 flex flex-col items-center justify-center text-center">
            <div
                class="w-10 h-10 rounded-full bg-yellow-100 dark:bg-yellow-500/20 text-yellow-600 dark:text-yellow-400 flex items-center justify-center text-xl mb-2">
                <i class='bx bxs-star'></i>
            </div>
            <h3 class="text-2xl font-bold text-slate-800 dark:text-white">{{ number_format($member->points ?? 0) }}</h3>
            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Poin Loyalty</p>
        </div>

        <!-- Tier Info -->
        <div
            class="bg-white dark:bg-darkCard p-5 rounded-2xl shadow-sm border border-slate-100 dark:border-white/5 flex flex-col items-center justify-center text-center">
            <div
                class="w-10 h-10 rounded-full bg-emerald-100 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xl mb-2">
                <i class='bx bxs-badge-check'></i>
            </div>
            <h3 class="text-xl font-bold text-slate-800 dark:text-white">{{ $member->tier ?? 'Bronze' }}</h3>
            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Status Member</p>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="mb-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-bold text-slate-800 dark:text-white">Riwayat Belanja</h3>
            <a href="#" class="text-xs font-bold text-primary hover:text-indigo-400">Lihat Semua</a>
        </div>

        <div class="space-y-3">
            <!-- Empty State for now -->
            <div class="bg-white dark:bg-darkCard p-8 rounded-2xl border border-slate-100 dark:border-white/5 text-center">
                <div
                    class="w-12 h-12 rounded-full bg-slate-100 dark:bg-white/5 mx-auto flex items-center justify-center text-slate-400 text-2xl mb-3">
                    <i class='bx bx-shopping-bag'></i>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400">Belum ada transaksi terbaru.</p>
            </div>
        </div>
    </div>

    <!-- QR Code Script -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrious/4.0.2/qrious.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            new QRious({
                element: document.getElementById('qr-code'),
                value: '{{ $member->nomorAnggota }}',
                size: 100,
                backgroundAlpha: 0,
                foreground: 'black'
            });
        });
    </script>
@endsection