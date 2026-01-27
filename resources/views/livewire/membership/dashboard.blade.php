@push('styles')
    <style>
        .perspective-1000 {
            perspective: 1000px;
        }

        .transform-style-3d {
            transform-style: preserve-3d;
        }

        .backface-hidden {
            backface-visibility: hidden;
        }

        .rotate-y-180 {
            transform: rotateY(180deg);
        }

        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
    </style>
@endpush

<div x-data="{ 
    showBalance: @entangle('showBalance'),
    flipped: false,
    generateQR() {
        if(typeof QRious !== 'undefined') {
            new QRious({
                element: document.getElementById('qr-code'),
                value: '{{ $member->nomorAnggota ?? '' }}',
                size: 200,
                background: 'white',
                foreground: 'black'
            });
        }
    }
}" x-init="$nextTick(() => generateQR())" @toggle-card.window="flipped = !flipped" class="space-y-6">

    {{-- Flippable Member Card --}}
    <div class="perspective-1000 w-full h-[220px] cursor-pointer group" @click="flipped = !flipped">
        <div class="relative w-full h-full transition-all duration-700 transform-style-3d shadow-2xl rounded-3xl"
            :class="flipped ? 'rotate-y-180' : ''">

            {{-- Front Side --}}
            <div
                class="absolute inset-0 w-full h-full backface-hidden rounded-3xl overflow-hidden bg-gradient-to-br from-[#4F46E5] to-[#7C3AED] dark:from-[#3730a3] dark:to-[#5b21b6] border border-white/10 shadow-lg">

                {{-- Decorative Elements --}}
                <div
                    class="absolute top-0 right-0 w-48 h-48 bg-white/5 rounded-full blur-3xl -mr-16 -mt-16 pointer-events-none">
                </div>
                <div
                    class="absolute bottom-0 left-0 w-32 h-32 bg-purple-500/20 rounded-full blur-2xl -ml-10 -mb-10 pointer-events-none">
                </div>
                <div
                    class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10 mix-blend-overlay">
                </div>

                <div class="relative z-10 p-6 flex flex-col justify-between h-full">
                    {{-- Card Header --}}
                    <div class="flex justify-between items-start">
                        <div class="flex items-center gap-2">
                            <div
                                class="w-8 h-8 rounded-lg bg-white/20 backdrop-blur-sm flex items-center justify-center text-white font-bold">
                                <i class='bx bxs-store-alt'></i>
                            </div>
                            <span class="text-white/80 font-medium tracking-wide text-sm">Bermadani Retail</span>
                        </div>
                        <div class="glass-effect px-3 py-1 rounded-full flex items-center gap-1.5">
                            @php
                                $tierIcon = match ($member->tier ?? 'BRONZE') {
                                    'PLATINUM' => 'bxs-crown',
                                    'GOLD' => 'bxs-medal',
                                    'SILVER' => 'bxs-award',
                                    default => 'bxs-badge',
                                };
                                $tierColor = match ($member->tier ?? 'BRONZE') {
                                    'PLATINUM' => 'text-purple-200',
                                    'GOLD' => 'text-amber-200',
                                    'SILVER' => 'text-slate-200',
                                    default => 'text-orange-200',
                                };
                            @endphp
                            <i class='bx {{ $tierIcon }} {{ $tierColor }} text-sm'></i>
                            <span class="text-[10px] font-bold text-white uppercase tracking-wider">{{ $tier }}</span>
                        </div>
                    </div>

                    {{-- Card Balance --}}
                    <div class="pl-1">
                        <p class="text-white/60 text-xs uppercase tracking-widest mb-1 font-medium">Saldo Bermadani</p>
                        <div class="flex items-center gap-3">
                            <h2 class="text-3xl font-bold text-white tracking-tight font-sans">
                                <span x-show="showBalance">Rp
                                    {{ number_format($member->simpananSukarela ?? 0, 0, ',', '.') }}</span>
                                <span x-show="!showBalance">Rp ••••••••</span>
                            </h2>
                            <button @click.stop="showBalance = !showBalance; $wire.toggleBalance()"
                                class="text-white/50 hover:text-white transition-colors p-1 rounded-full hover:bg-white/10">
                                <i class='bx' :class="showBalance ? 'bx-hide' : 'bx-show'"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Card Footer --}}
                    <div class="flex justify-between items-end">
                        <div class="flex items-center gap-3">
                            <div>
                                <p class="text-white/60 text-[10px] uppercase font-bold mb-0.5">Pemilik Kartu</p>
                                <p class="text-white font-medium text-lg tracking-wide truncate max-w-[180px]">
                                    {{ $member->name ?? 'Member' }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-white/60 text-[10px] uppercase font-bold mb-0.5">Nomor Anggota</p>
                            <p class="font-mono text-white/90 text-sm tracking-wider">
                                {{ $member->nomorAnggota ?? '----' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Hint Animation --}}
                <div
                    class="absolute bottom-3 right-1/2 transform translate-x-1/2 flex flex-col items-center gap-1 opacity-50 animate-pulse pointer-events-none">
                    <span class="text-[9px] text-white uppercase tracking-widest">Tap untuk QR</span>
                </div>
            </div>

            {{-- Back Side (QR Code) --}}
            <div
                class="absolute inset-0 w-full h-full backface-hidden rotate-y-180 rounded-3xl overflow-hidden bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-xl">
                <div class="h-full flex flex-col items-center justify-center p-6 relative">
                    <div class="absolute top-4 right-4 text-slate-400">
                        <i class='bx bx-qr-scan text-2xl'></i>
                    </div>

                    <h3
                        class="text-slate-800 dark:text-white font-bold mb-4 text-center text-sm uppercase tracking-widest">
                        Scan di Kasir</h3>

                    <div class="p-3 bg-white rounded-xl shadow-inner border border-slate-100 dark:border-slate-200">
                        <canvas id="qr-code"></canvas>
                    </div>

                    <p class="mt-4 font-mono text-slate-500 dark:text-slate-400 text-sm tracking-widest font-bold">
                        {{ $member->nomorAnggota ?? '' }}</p>

                    <p class="absolute bottom-4 text-[10px] text-slate-400 uppercase tracking-widest">Tap untuk kembali
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div>
        <h3 class="text-sm font-bold text-slate-700 dark:text-slate-300 mb-4 px-1">Menu Utama</h3>
        <div class="grid grid-cols-4 gap-3">
            <a href="{{ route('membership.simpanan') }}" class="group flex flex-col items-center gap-2">
                <div
                    class="w-14 h-14 rounded-2xl bg-white dark:bg-slate-800 shadow-sm border border-slate-100 dark:border-slate-700 flex items-center justify-center text-2xl text-emerald-500 group-hover:scale-105 group-active:scale-95 transition-all duration-300 relative overflow-hidden">
                    <div class="absolute inset-0 bg-emerald-500/5 group-hover:bg-emerald-500/10 transition-colors">
                    </div>
                    <i class='bx bxs-wallet-alt'></i>
                </div>
                <span class="text-[10px] font-medium text-slate-600 dark:text-slate-400 text-center">Isi Saldo</span>
            </a>

            <a href="{{ route('membership.transfer') }}" class="group flex flex-col items-center gap-2">
                <div
                    class="w-14 h-14 rounded-2xl bg-white dark:bg-slate-800 shadow-sm border border-slate-100 dark:border-slate-700 flex items-center justify-center text-2xl text-[#6366f1] group-hover:scale-105 group-active:scale-95 transition-all duration-300 relative overflow-hidden">
                    <div class="absolute inset-0 bg-[#6366f1]/5 group-hover:bg-[#6366f1]/10 transition-colors"></div>
                    <i class='bx bxs-paper-plane'></i>
                </div>
                <span class="text-[10px] font-medium text-slate-600 dark:text-slate-400 text-center">Kirim</span>
            </a>

            <a href="{{ route('membership.history') }}" class="group flex flex-col items-center gap-2">
                <div
                    class="w-14 h-14 rounded-2xl bg-white dark:bg-slate-800 shadow-sm border border-slate-100 dark:border-slate-700 flex items-center justify-center text-2xl text-amber-500 group-hover:scale-105 group-active:scale-95 transition-all duration-300 relative overflow-hidden">
                    <div class="absolute inset-0 bg-amber-500/5 group-hover:bg-amber-500/10 transition-colors"></div>
                    <i class='bx bxs-time-five'></i>
                </div>
                <span class="text-[10px] font-medium text-slate-600 dark:text-slate-400 text-center">Riwayat</span>
            </a>

            <a href="{{ route('membership.profile') }}" class="group flex flex-col items-center gap-2">
                <div
                    class="w-14 h-14 rounded-2xl bg-white dark:bg-slate-800 shadow-sm border border-slate-100 dark:border-slate-700 flex items-center justify-center text-2xl text-rose-500 group-hover:scale-105 group-active:scale-95 transition-all duration-300 relative overflow-hidden">
                    <div class="absolute inset-0 bg-rose-500/5 group-hover:bg-rose-500/10 transition-colors"></div>
                    <i class='bx bxs-user-account'></i>
                </div>
                <span class="text-[10px] font-medium text-slate-600 dark:text-slate-400 text-center">Akun</span>
            </a>
        </div>
    </div>

    {{-- Loyalty Banner (Restored) --}}
    <div
        class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-slate-900 to-slate-800 dark:from-slate-800 dark:to-slate-900 shadow-lg p-5 flex items-center justify-between group cursor-default">
        <div
            class="absolute top-0 right-0 w-32 h-32 bg-amber-400/10 rounded-full blur-2xl -mr-16 -mt-16 pointer-events-none">
        </div>

        <div class="relative z-10 flex flex-col">
            <span class="text-[10px] text-amber-400 font-bold uppercase tracking-widest mb-1 flex items-center gap-1">
                <i class='bx bxs-star'></i> Bermadani Poin
            </span>
            <h3 class="text-2xl font-bold text-white">{{ number_format($member->points ?? 0) }} <span
                    class="text-sm font-normal text-slate-400">pts</span></h3>
        </div>

        <div class="relative z-10">
            @php
                $tierThresholds = [
                    'BRONZE' => ['current' => 0, 'next' => 1000, 'nextTier' => 'Silver'],
                    'SILVER' => ['current' => 1000, 'next' => 3000, 'nextTier' => 'Gold'],
                    'GOLD' => ['current' => 3000, 'next' => 6000, 'nextTier' => 'Platinum'],
                    'PLATINUM' => ['current' => 6000, 'next' => 6000, 'nextTier' => 'Max'],
                ];
                $tierData = $tierThresholds[$tier] ?? $tierThresholds['BRONZE'];
                $points = $member->points ?? 0;
                $progress = $tierData['next'] > $tierData['current']
                    ? (($points - $tierData['current']) / ($tierData['next'] - $tierData['current'])) * 100
                    : 100;
                $progress = min(100, max(0, $progress));
            @endphp
            <div class="w-24 bg-white/10 rounded-full h-1.5 mt-2">
                <div class="bg-amber-400 h-1.5 rounded-full" style="width: {{ $progress }}%"></div>
            </div>
            <p class="text-[9px] text-white/50 mt-1 text-right">{{ number_format($points) }} /
                {{ number_format($tierData['next']) }}</p>
        </div>
    </div>

    {{-- Recent Transactions List --}}
    <div>
        <div class="flex justify-between items-end mb-4 px-1">
            <h3 class="text-sm font-bold text-slate-700 dark:text-slate-200">Aktivitas Terakhir</h3>
            <a href="{{ route('membership.history') }}"
                class="text-xs text-[#6366f1] hover:text-[#4f46e5] font-medium">Lihat Semua</a>
        </div>

        <div class="space-y-3">
            @forelse($recentTransactions as $trx)
                <div
                    class="bg-white dark:bg-darkCard rounded-2xl p-4 border border-slate-100 dark:border-slate-700 flex items-center gap-4 transition-transform active:scale-[0.99] hover:shadow-sm">
                    <div
                        class="w-12 h-12 rounded-full bg-slate-50 dark:bg-slate-700/50 flex items-center justify-center text-xl text-slate-600 dark:text-slate-300 shrink-0">
                        <i class='bx bx-shopping-bag'></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="text-sm font-bold text-slate-800 dark:text-white truncate">Belanja Bermadani</h4>
                        <p class="text-[10px] text-slate-500 font-medium">{{ $trx->created_at->format('d M Y • H:i') }}</p>
                    </div>
                    <div class="text-right shrink-0">
                        <p class="text-sm font-bold text-slate-800 dark:text-white">-Rp
                            {{ number_format($trx->totalAmount, 0, ',', '.') }}</p>
                        <span class="text-[10px] font-bold text-emerald-500 flex items-center justify-end gap-0.5">
                            {{ ucfirst($trx->status) }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="text-center py-10">
                    <div
                        class="w-16 h-16 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-3 text-slate-300 dark:text-slate-600 text-3xl">
                        <i class='bx bx-receipt'></i>
                    </div>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Belum ada transaksi</p>
                </div>
            @endforelse
        </div>
    </div>
</div>