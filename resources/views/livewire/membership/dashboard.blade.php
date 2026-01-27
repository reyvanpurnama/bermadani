<div>
    {{-- Hero Balance Card --}}
    <div class="relative mb-6">
        <div
            class="bg-gradient-to-br from-primary to-purple-600 rounded-3xl p-6 text-white shadow-xl shadow-primary/30 border border-white/10 overflow-hidden">
            <!-- Decorative Circles -->
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full blur-3xl -mr-10 -mt-10"></div>
            <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/10 rounded-full blur-2xl -ml-6 -mb-6"></div>

            <div class="relative z-10">
                <!-- Greeting -->
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <p class="text-sm text-white/70 mb-1">Halo,</p>
                        <h2 class="text-xl font-bold">{{ $member->name ?? 'Member' }}</h2>
                    </div>
                    <button wire:click="toggleBalance"
                        class="w-9 h-9 rounded-full bg-white/20 flex items-center justify-center hover:bg-white/30 transition">
                        <i class='bx {{ $showBalance ? "bx-hide" : "bx-show" }} text-lg'></i>
                    </button>
                </div>

                <!-- Balance -->
                <div class="mb-4">
                    <p class="text-xs text-white/70 mb-1 uppercase tracking-wider">Saldo Bermadani</p>
                    <h1 class="text-3xl font-bold tracking-tight">
                        @if($showBalance)
                            Rp {{ number_format($member->simpananSukarela ?? 0, 0, ',', '.') }}
                        @else
                            Rp ••••••••
                        @endif
                    </h1>
                </div>

                <!-- Member Info -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-white/70 font-mono">{{ $member->nomorAnggota ?? '-' }}</span>
                        <span class="px-2 py-0.5 text-[10px] rounded-full font-bold uppercase 
                            @if(($member->tier ?? 'BRONZE') === 'PLATINUM') bg-purple-400/30 text-purple-100
                            @elseif(($member->tier ?? 'BRONZE') === 'GOLD') bg-amber-400/30 text-amber-100
                            @elseif(($member->tier ?? 'BRONZE') === 'SILVER') bg-gray-300/30 text-gray-100
                            @else bg-orange-400/30 text-orange-100
                            @endif">
                            {{ $member->tier ?? 'BRONZE' }}
                        </span>
                    </div>
                    <span class="text-sm font-bold text-amber-300">{{ number_format($member->points ?? 0) }} Poin</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Actions (Mobile Grid) --}}
    <div class="mb-8">
        <h3 class="text-sm font-bold text-slate-700 dark:text-slate-300 mb-4">Menu Utama</h3>
        <div class="grid grid-cols-4 gap-3">
            <a href="{{ route('membership.simpanan') }}"
                class="flex flex-col items-center gap-2 p-3 bg-white dark:bg-darkCard rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm hover:shadow-md transition-shadow group">
                <div
                    class="w-12 h-12 rounded-xl bg-emerald-100 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                    <i class='bx bx-wallet'></i>
                </div>
                <span class="text-[10px] font-medium text-slate-600 dark:text-slate-300">Simpanan</span>
            </a>

            <a href="{{ route('membership.transfer') }}"
                class="flex flex-col items-center gap-2 p-3 bg-white dark:bg-darkCard rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm hover:shadow-md transition-shadow group">
                <div
                    class="w-12 h-12 rounded-xl bg-blue-100 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                    <i class='bx bx-transfer-alt'></i>
                </div>
                <span class="text-[10px] font-medium text-slate-600 dark:text-slate-300">Transfer</span>
            </a>

            <a href="{{ route('membership.history') }}"
                class="flex flex-col items-center gap-2 p-3 bg-white dark:bg-darkCard rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm hover:shadow-md transition-shadow group">
                <div
                    class="w-12 h-12 rounded-xl bg-amber-100 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                    <i class='bx bx-receipt'></i>
                </div>
                <span class="text-[10px] font-medium text-slate-600 dark:text-slate-300">Riwayat</span>
            </a>

            <a href="{{ route('membership.profile') }}"
                class="flex flex-col items-center gap-2 p-3 bg-white dark:bg-darkCard rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm hover:shadow-md transition-shadow group">
                <div
                    class="w-12 h-12 rounded-xl bg-purple-100 dark:bg-purple-500/10 text-purple-600 dark:text-purple-400 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                    <i class='bx bx-user'></i>
                </div>
                <span class="text-[10px] font-medium text-slate-600 dark:text-slate-300">Profil</span>
            </a>
        </div>
    </div>

    {{-- Points & Tier Progress --}}
    <div class="bg-white dark:bg-darkCard rounded-2xl p-5 border border-slate-100 dark:border-slate-700 shadow-sm mb-8">
        <div class="flex items-center gap-3 mb-4">
            <div
                class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center text-white shadow-lg shadow-amber-500/30">
                <i class='bx bxs-medal text-xl'></i>
            </div>
            <div>
                <h4 class="font-bold text-slate-800 dark:text-white">Loyalty Program</h4>
                <p class="text-[11px] text-slate-500">Kumpulkan poin setiap belanja</p>
            </div>
        </div>

        @php
            $tierThresholds = [
                'BRONZE' => ['current' => 0, 'next' => 1000, 'nextTier' => 'Silver'],
                'SILVER' => ['current' => 1000, 'next' => 3000, 'nextTier' => 'Gold'],
                'GOLD' => ['current' => 3000, 'next' => 6000, 'nextTier' => 'Platinum'],
                'PLATINUM' => ['current' => 6000, 'next' => 6000, 'nextTier' => 'Max'],
            ];
            $tier = $member->tier ?? 'BRONZE';
            $tierData = $tierThresholds[$tier] ?? $tierThresholds['BRONZE'];
            $points = $member->points ?? 0;
            $progress =
                $tierData['next'] > $tierData['current']
                ? (($points - $tierData['current']) / ($tierData['next'] - $tierData['current'])) * 100
                : 100;
            $progress = min(100, max(0, $progress));
            $remaining = max(0, $tierData['next'] - $points);
        @endphp

        <div class="flex justify-between text-[11px] mb-2 font-medium">
            <span class="text-slate-600 dark:text-slate-300">{{ $tier }} ({{ number_format($points) }}
                pts)</span>
            <span class="text-slate-400">{{ $tierData['nextTier'] }} ({{ number_format($tierData['next']) }}
                pts)</span>
        </div>
        <div class="w-full bg-slate-100 dark:bg-slate-700 rounded-full h-2.5 overflow-hidden">
            <div class="bg-gradient-to-r from-amber-400 to-orange-500 h-2.5 rounded-full relative transition-all duration-500"
                style="width: {{ $progress }}%"></div>
        </div>
        @if ($tier !== 'PLATINUM')
            <p class="text-[10px] text-slate-400 mt-2 text-right">
                Butuh <b class="text-amber-500">{{ number_format($remaining) }}</b> poin untuk naik tier!
            </p>
        @else
            <p class="text-[10px] text-emerald-500 mt-2 text-right font-bold">
                🎉 Kamu di tier tertinggi!
            </p>
        @endif
    </div>

    {{-- Recent Transactions --}}
    <div>
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-sm font-bold text-slate-700 dark:text-slate-300">Transaksi Terakhir</h3>
            <a href="{{ route('membership.history') }}" class="text-xs text-primary hover:underline font-medium">Lihat
                Semua</a>
        </div>

        <div class="space-y-3">
            @forelse($recentTransactions as $trx)
                <div
                    class="bg-white dark:bg-darkCard rounded-xl p-4 border border-slate-100 dark:border-slate-700 flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-full bg-blue-50 dark:bg-blue-500/10 text-blue-500 flex items-center justify-center">
                        <i class='bx bx-shopping-bag text-xl'></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-slate-800 dark:text-white truncate">Belanja
                            #{{ $trx->id }}</p>
                        <p class="text-[11px] text-slate-400">{{ $trx->created_at->format('d M Y • H:i') }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-bold text-slate-800 dark:text-white">-Rp
                            {{ number_format($trx->totalAmount, 0, ',', '.') }}
                        </p>
                        <span
                            class="text-[10px] font-bold text-emerald-500 bg-emerald-50 dark:bg-emerald-500/10 px-1.5 py-0.5 rounded">{{ $trx->status }}</span>
                    </div>
                </div>
            @empty
                <div class="text-center py-8 text-slate-400">
                    <i class='bx bx-shopping-bag text-4xl mb-2 opacity-50'></i>
                    <p class="text-sm">Belum ada transaksi</p>
                </div>
            @endforelse
        </div>
    </div>
</div>