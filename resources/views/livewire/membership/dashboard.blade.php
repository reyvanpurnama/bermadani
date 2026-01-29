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

        [x-cloak] {
            display: none !important;
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
}" x-init="$nextTick(() => generateQR())" @toggle-card.window="flipped = !flipped" class="space-y-8"> {{-- Increased
    vertical space --}}

    @section('page-title', 'Retail Dashboard')

    {{-- Flippable Member Card --}}
    <div class="perspective-1000 w-full h-[220px] cursor-pointer group relative z-10" @click="flipped = !flipped">
        <div class="relative w-full h-full transition-all duration-700 transform-style-3d shadow-2xl rounded-2xl"
            :style="flipped ? 'transform: rotateY(180deg)' : 'transform: rotateY(0deg)'">

            {{-- Front Side (Dark Slate/Chip Style) --}}
            <div
                class="absolute inset-0 w-full h-full backface-hidden rounded-2xl p-6 relative overflow-hidden bg-gradient-to-br from-slate-800 to-slate-900 border border-slate-700/50 text-white flex flex-col justify-between">

                {{-- Decorative Background --}}
                <div
                    class="absolute top-0 -inset-full h-full w-1/2 z-5 block transform -skew-x-12 bg-gradient-to-r from-transparent to-white opacity-10">
                </div>
                <div class="absolute top-0 right-0 w-32 h-32 bg-white/5 rounded-full blur-2xl -mr-10 -mt-10"></div>

                <div class="relative z-10 w-full h-full flex flex-col justify-between">
                    {{-- Header: Chip & Wifi --}}
                    <div>
                        <div class="flex justify-between items-start mb-6">
                            <i class='bx bxs-chip text-4xl opacity-80 text-yellow-500'></i>
                            <div class="flex items-center gap-1 opacity-80">
                                <i class='bx bx-wifi text-2xl rotate-90'></i>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 mb-1 relative z-20" x-data="{ copied: false }">
                            <p class="font-mono text-lg tracking-widest shadow-black drop-shadow-md">
                                {{ $member->nomorAnggota ?? '--------' }}
                            </p>
                            <button
                                @click.stop="navigator.clipboard.writeText('{{ $member->nomorAnggota ?? '' }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                class="text-white/40 hover:text-white transition-colors p-1"
                                title="Salin Nomor Anggota">
                                <i class='bx text-xl' :class="copied ? 'bx-check text-emerald-400' : 'bx-copy'"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Footer: Name & Brand --}}
                    <div class="flex justify-between items-end mt-4">
                        <div>
                            <p class="text-[10px] text-slate-400 uppercase tracking-widest mb-0.5">Member Name</p>
                            <p class="font-bold uppercase tracking-wide text-sm sm:text-base">
                                {{ $member->name ?? 'Member' }}
                            </p>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <div class="w-6 h-6 bg-white/20 rounded flex items-center justify-center"><i
                                    class='bx bxs-cube-alt'></i></div>
                            <span class="font-bold italic text-sm">BERMADANI</span>
                        </div>
                    </div>
                </div>

                {{-- Hint --}}
                <div
                    class="absolute bottom-3 right-1/2 transform translate-x-1/2 flex flex-col items-center gap-1 opacity-50 animate-pulse pointer-events-none">
                    <span class="text-[9px] text-white uppercase tracking-widest">Tap untuk QR</span>
                </div>
            </div>

            {{-- Back Side (QR Code) --}}
            <div style="transform: rotateY(180deg)"
                class="absolute inset-0 w-full h-full backface-hidden rounded-2xl overflow-hidden bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-xl flex items-center justify-center relative">

                <div class="h-full flex flex-col items-center justify-center p-6 w-full">
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
                        {{ $member->nomorAnggota ?? '' }}
                    </p>
                    <p class="absolute bottom-4 text-[10px] text-slate-400 uppercase tracking-widest">Tap untuk kembali
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Balance & Points Summary (AlloBank Style) --}}
    {{-- Balance & Points Summary (AlloBank Style) --}}
    <div
        class="bg-gradient-to-br from-slate-800 to-[#0f172a] border border-slate-700/50 text-white p-6 rounded-2xl shadow-lg relative overflow-hidden group mb-8">
        {{-- Blue Glow Effect --}}
        <div
            class="absolute top-0 right-0 w-64 h-64 bg-blue-600/20 rounded-full blur-3xl -mr-20 -mt-20 pointer-events-none group-hover:bg-blue-600/30 transition-all duration-500">
        </div>
        <div
            class="absolute bottom-0 left-0 w-40 h-40 bg-indigo-500/10 rounded-full blur-2xl -ml-10 -mb-10 pointer-events-none">
        </div>

        <div class="flex items-center justify-between relative z-10">
            {{-- Left: Saldo (Dominant) --}}
            <div class="flex-1" x-data="{ localShow: true }">
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-[11px] font-medium text-gray-400 uppercase tracking-wider">Saldo Bermadani</span>
                    <i class='bx bxs-check-circle text-blue-500 text-xs'></i>
                </div>
                <div class="flex items-center gap-3">
                    <h3 class="text-2xl sm:text-3xl font-bold tracking-tight text-white min-h-[40px] flex items-center">
                        <span x-show="localShow" class="transition-opacity duration-300">
                            Rp {{ number_format($member->simpananSukarela ?? 0, 0, ',', '.') }}
                        </span>
                        <span x-show="!localShow" style="display: none;">
                            Rp •••••••
                        </span>
                    </h3>

                    <button @click="localShow = !localShow"
                        class="text-white/70 hover:text-white transition-colors p-1 rounded-full hover:bg-white/10">
                        <i class='bx text-xl' :class="localShow ? 'bx-hide' : 'bx-show'"></i>
                    </button>
                </div>

                <div class="mt-2">
                    <a href="{{ route('membership.simpanan') }}"
                        class="inline-flex text-[10px] bg-white/10 hover:bg-white/20 text-white px-4 py-1.5 rounded-full font-bold shadow-sm backdrop-blur-md transition-all items-center gap-1 border border-white/10">
                        <i class='bx bx-plus'></i> Isi Saldo
                    </a>
                </div>
            </div>

            {{-- Vertical Divider --}}
            <div class="w-px h-16 bg-white/10 mx-4 sm:mx-8"></div>

            {{-- Right: Points --}}
            <div class="min-w-[80px] sm:min-w-[120px] text-right sm:text-left">
                <div class="flex items-center justify-end sm:justify-start gap-1.5 mb-1">
                    <div
                        class="w-4 h-4 rounded-full bg-amber-500 flex items-center justify-center text-[8px] text-black font-bold">
                        <i class='bx bxs-coin-stack'></i>
                    </div>
                    <span class="text-[11px] font-medium text-gray-400">Points</span>
                </div>
                <h3 class="text-xl sm:text-2xl font-bold text-amber-500">
                    {{ number_format($member->points ?? 0) }}
                </h3>
                <div class="mt-2 text-right sm:text-left">
                    <span
                        class="text-[9px] px-2 py-0.5 rounded bg-white/10 border border-white/5 text-slate-300">{{ $member->tier ?? 'Bronze' }}</span>
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
                <span class="text-[10px] font-medium text-slate-600 dark:text-slate-400 text-center">Isi
                    Saldo</span>
            </a>

            <a href="{{ route('membership.transfer') }}" class="group flex flex-col items-center gap-2">
                <div
                    class="w-14 h-14 rounded-2xl bg-white dark:bg-slate-800 shadow-sm border border-slate-100 dark:border-slate-700 flex items-center justify-center text-2xl text-[#6366f1] group-hover:scale-105 group-active:scale-95 transition-all duration-300 relative overflow-hidden">
                    <div class="absolute inset-0 bg-[#6366f1]/5 group-hover:bg-[#6366f1]/10 transition-colors">
                    </div>
                    <i class='bx bxs-paper-plane'></i>
                </div>
                <span class="text-[10px] font-medium text-slate-600 dark:text-slate-400 text-center">Kirim</span>
            </a>

            <a href="{{ route('membership.history') }}" class="group flex flex-col items-center gap-2">
                <div
                    class="w-14 h-14 rounded-2xl bg-white dark:bg-slate-800 shadow-sm border border-slate-100 dark:border-slate-700 flex items-center justify-center text-2xl text-amber-500 group-hover:scale-105 group-active:scale-95 transition-all duration-300 relative overflow-hidden">
                    <div class="absolute inset-0 bg-amber-500/5 group-hover:bg-amber-500/10 transition-colors">
                    </div>
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
                        <p class="text-[10px] text-slate-500 font-medium">{{ $trx->created_at->format('d M Y • H:i') }}
                        </p>
                    </div>
                    <div class="text-right shrink-0">
                        <p class="text-sm font-bold text-slate-800 dark:text-white">-Rp
                            {{ number_format($trx->totalAmount, 0, ',', '.') }}
                        </p>
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