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