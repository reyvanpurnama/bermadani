<div>
    @section('page-title', 'Dashboard Anggota')

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        {{-- Member Card --}}
        <div class="bg-gradient-to-br from-slate-800 to-slate-900 rounded-2xl p-6 shadow-2xl text-white relative overflow-hidden group h-full flex flex-col justify-between border border-slate-700/50 min-h-[220px]">
            <div class="absolute top-0 -inset-full h-full w-1/2 z-5 block transform -skew-x-12 bg-gradient-to-r from-transparent to-white opacity-10 group-hover:animate-shine"></div>
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/5 rounded-full blur-2xl -mr-10 -mt-10"></div>

            <div>
                <div class="flex justify-between items-start mb-6">
                    <i class='bx bxs-chip text-4xl opacity-80'></i>
                    <div class="flex items-center gap-1 opacity-80">
                        <i class='bx bx-wifi text-2xl rotate-90'></i>
                    </div>
                </div>
                <p class="font-mono text-lg tracking-widest mb-1 shadow-black drop-shadow-md">
                    {{ $member->nomorAnggota ?? '--------' }}
                </p>
            </div>

            <div class="flex justify-between items-end mt-4">
                <div>
                    <p class="text-[10px] text-slate-400 uppercase tracking-widest mb-0.5">Member Name</p>
                    <p class="font-bold uppercase tracking-wide text-sm sm:text-base">{{ $member->name ?? 'Member' }}</p>
                </div>
                <div class="flex items-center gap-1.5">
                    <div class="w-6 h-6 bg-white/20 rounded flex items-center justify-center"><i class='bx bxs-cube-alt'></i></div>
                    <span class="font-bold italic text-sm">BERMADANI</span>
                </div>
            </div>
        </div>

        {{-- Points & Tier Card --}}
        <div class="bg-white dark:bg-darkCard p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 flex flex-col justify-between h-full relative overflow-hidden min-h-[220px]">
            <div class="absolute top-0 right-0 w-24 h-24 bg-slate-100 dark:bg-slate-700 rounded-bl-full -mr-4 -mt-4 z-0"></div>

            <div class="relative z-10">
                <div class="flex justify-between items-start mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-slate-200 dark:bg-slate-600 text-slate-600 dark:text-slate-300 rounded-xl flex items-center justify-center text-2xl shadow-inner">
                            <i class='bx bxs-medal'></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-slate-800 dark:text-white">{{ $member->tier ?? 'Bronze' }} Member</h4>
                            <p class="text-[11px] text-slate-500">Tier Keanggotaan</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] text-slate-400 uppercase tracking-wider mb-0.5">Poin Reward</p>
                        <h3 class="text-2xl font-bold text-amber-500">{{ number_format($member->points ?? 0) }} <span class="text-xs text-slate-400">Pts</span></h3>
                    </div>
                </div>

                <div>
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
                        $progress = $tierData['next'] > $tierData['current'] 
                            ? (($points - $tierData['current']) / ($tierData['next'] - $tierData['current'])) * 100 
                            : 100;
                        $progress = min(100, max(0, $progress));
                        $remaining = max(0, $tierData['next'] - $points);
                    @endphp
                    <div class="flex justify-between text-[11px] mb-2 font-medium">
                        <span class="text-slate-600 dark:text-slate-300">{{ $tier }} ({{ number_format($tierData['current']) }})</span>
                        <span class="text-slate-400">Target: {{ $tierData['nextTier'] }} ({{ number_format($tierData['next']) }})</span>
                    </div>
                    <div class="w-full bg-slate-100 dark:bg-slate-700 rounded-full h-2.5 overflow-hidden">
                        <div class="bg-gradient-to-r from-slate-400 to-amber-400 h-2.5 rounded-full relative" style="width: {{ $progress }}%">
                            <div class="absolute top-0 left-0 w-full h-full bg-white opacity-20 animate-pulse"></div>
                        </div>
                    </div>
                    @if($tier !== 'PLATINUM')
                        <p class="text-[10px] text-slate-400 mt-2 text-right">
                            Kumpulkan <b>{{ number_format($remaining) }}</b> poin lagi untuk naik level!
                        </p>
                    @else
                        <p class="text-[10px] text-emerald-500 mt-2 text-right font-bold">
                            🎉 Selamat! Anda sudah di tier tertinggi!
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Simpanan Portfolio --}}
    <div class="bg-white dark:bg-darkCard rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 p-6 mb-8">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <i class='bx bxs-wallet text-emerald-600'></i> Portofolio Simpanan
            </h3>
            <button wire:click="toggleBalance" class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg transition-colors" title="{{ $showBalance ? 'Sembunyikan Saldo' : 'Tampilkan Saldo' }}">
                <i class='bx {{ $showBalance ? "bx-hide" : "bx-show" }} text-xl'></i>
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            {{-- Simpanan Pokok --}}
            <div class="p-4 bg-slate-50 dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 flex flex-col justify-between h-full">
                <div class="flex justify-between items-start mb-2">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">S. Pokok</span>
                    <i class='bx bxs-lock-alt text-slate-300 text-lg'></i>
                </div>
                <div>
                    <h4 class="text-lg font-bold text-slate-800 dark:text-white">
                        @if($showBalance)
                            Rp {{ number_format($member->simpananPokok ?? 0, 0, ',', '.') }}
                        @else
                            Rp ••••••
                        @endif
                    </h4>
                    <p class="text-[10px] text-slate-400 mt-1">Sekali (Saat Daftar)</p>
                </div>
            </div>

            {{-- Simpanan Wajib --}}
            <div class="p-4 bg-slate-50 dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 flex flex-col justify-between h-full">
                <div class="flex justify-between items-start mb-2">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">S. Wajib</span>
                    <i class='bx bxs-calendar text-slate-300 text-lg'></i>
                </div>
                <div>
                    <h4 class="text-lg font-bold text-slate-800 dark:text-white">
                        @if($showBalance)
                            Rp {{ number_format($member->simpananWajib ?? 0, 0, ',', '.') }}
                        @else
                            Rp ••••••
                        @endif
                    </h4>
                    <p class="text-[10px] text-slate-400 mt-1">Akumulasi Bulanan</p>
                </div>
            </div>

            {{-- Simpanan Sukarela --}}
            <div class="p-4 bg-emerald-50 dark:bg-emerald-900/10 rounded-xl border border-emerald-100 dark:border-emerald-800/30 flex flex-col justify-between h-full">
                <div class="flex justify-between items-start mb-2">
                    <span class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-widest">S. Sukarela</span>
                    <i class='bx bxs-bank text-emerald-300 text-lg'></i>
                </div>
                <div>
                    <h4 class="text-lg font-bold text-slate-800 dark:text-white">
                        @if($showBalance)
                            Rp {{ number_format($member->simpananSukarela ?? 0, 0, ',', '.') }}
                        @else
                            Rp ••••••
                        @endif
                    </h4>
                    <p class="text-[10px] text-slate-400 mt-1">Dapat Ditarik</p>
                </div>
            </div>

            {{-- Total --}}
            @php
                $totalSimpanan = ($member->simpananPokok ?? 0) + ($member->simpananWajib ?? 0) + ($member->simpananSukarela ?? 0);
            @endphp
            <div class="p-4 bg-primary text-white rounded-xl shadow-lg shadow-blue-500/20 flex flex-col justify-between h-full relative overflow-hidden">
                <div class="absolute right-0 top-0 w-16 h-16 bg-white opacity-10 rounded-bl-full -mr-2 -mt-2"></div>
                <div class="relative z-10">
                    <span class="text-[10px] font-bold text-blue-200 uppercase tracking-widest">Total Aset</span>
                    <h3 class="text-2xl font-bold mt-1">
                        @if($showBalance)
                            Rp {{ number_format($totalSimpanan, 0, ',', '.') }}
                        @else
                            Rp ••••••••
                        @endif
                    </h3>
                    <p class="text-[10px] text-blue-100 mt-2 opacity-80">Saldo aktif di Koperasi</p>
                </div>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="flex gap-4 mt-4">
            <a href="{{ route('member.transfer') }}" class="flex-1 py-3 px-6 bg-emerald-500 hover:bg-emerald-600 text-white font-bold rounded-xl transition-colors flex items-center justify-center gap-2">
                <i class='bx bx-transfer text-xl'></i>
                <span>Transfer Sukarela</span>
            </a>
            <a href="{{ route('member.simpanan') }}" class="flex-1 py-3 px-6 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 font-bold rounded-xl transition-colors flex items-center justify-center gap-2">
                <i class='bx bx-history text-xl'></i>
                <span>Riwayat</span>
            </a>
        </div>
    </div>

    {{-- Recent Activities --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {{-- Recent Transactions --}}
        <div class="space-y-4">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Riwayat Belanja</h3>
                <a href="{{ route('member.transactions') }}" class="text-sm text-primary hover:underline">Lihat Semua</a>
            </div>

            <div class="bg-white dark:bg-darkCard rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">
                @if(count($recentTransactions) > 0)
                    <div class="divide-y divide-slate-100 dark:divide-slate-700">
                        @foreach($recentTransactions as $trx)
                            <div class="p-4 flex justify-between items-center hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-blue-50 dark:bg-blue-900/20 text-blue-600 flex items-center justify-center">
                                        <i class='bx bx-shopping-bag'></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-slate-800 dark:text-white">Belanja #{{ $trx->id }}</p>
                                        <p class="text-[11px] text-slate-500">{{ $trx->created_at->format('d M • H:i') }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-bold text-slate-800 dark:text-white">-Rp {{ number_format($trx->totalAmount, 0, ',', '.') }}</p>
                                    <p class="text-[10px] text-emerald-500 font-bold">{{ $trx->status }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-8 text-center text-slate-400">
                        <i class='bx bx-shopping-bag text-4xl mb-2 opacity-50'></i>
                        <p class="text-sm">Belum ada transaksi belanja</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Recent Simpanan --}}
        <div class="space-y-4">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Aktivitas Simpanan</h3>
                <a href="{{ route('member.simpanan') }}" class="text-sm text-primary hover:underline">Lihat Semua</a>
            </div>

            <div class="bg-white dark:bg-darkCard rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">
                @if(count($recentSimpanan) > 0)
                    <div class="divide-y divide-slate-100 dark:divide-slate-700">
                        @foreach($recentSimpanan as $simp)
                            <div class="p-4 flex justify-between items-center hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full {{ $simp->transactionType === 'SETOR' ? 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600' : 'bg-rose-50 dark:bg-rose-900/20 text-rose-500' }} flex items-center justify-center">
                                        <i class='bx {{ $simp->transactionType === 'SETOR' ? 'bx-down-arrow-alt' : 'bx-up-arrow-alt' }} text-xl'></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-slate-800 dark:text-white">Simpanan {{ ucfirst(strtolower($simp->type)) }}</p>
                                        <p class="text-[11px] text-slate-500">{{ $simp->transactionType === 'SETOR' ? 'Setoran' : 'Penarikan' }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-bold {{ $simp->transactionType === 'SETOR' ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-500' }}">
                                        {{ $simp->transactionType === 'SETOR' ? '+' : '-' }}Rp {{ number_format($simp->amount, 0, ',', '.') }}
                                    </p>
                                    <p class="text-[10px] text-slate-400">{{ $simp->created_at->format('d M') }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-8 text-center text-slate-400">
                        <i class='bx bx-wallet text-4xl mb-2 opacity-50'></i>
                        <p class="text-sm">Belum ada aktivitas simpanan</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
