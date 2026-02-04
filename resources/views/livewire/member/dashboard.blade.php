@push('styles')
    <style>
        .perspective-1000 { perspective: 1000px; }
        .transform-style-3d { transform-style: preserve-3d; }
        .backface-hidden { backface-visibility: hidden; }
        .rotate-y-180 { transform: rotateY(180deg); }
        
        @keyframes wiggle {
            0%, 100% { transform: rotateY(0deg); }
            20% { transform: rotateY(15deg); }
            40% { transform: rotateY(-10deg); }
            60% { transform: rotateY(5deg); }
        }
        .animate-wiggle { animation: wiggle 1.5s ease-out; }
        [x-cloak] { display: none !important; }
    </style>
@endpush

<div x-data="{ showBalance: {{ $showBalance ? 'true' : 'false' }} }" class="space-y-8">
    @section('page-title', 'Dashboard Anggota')

    {{-- Toast Notification for Unread Transfers --}}
    @if($unreadCount > 0)
        <div x-data="{ show: true }" x-show="show" 
             x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-4" 
             class="mb-6">
            <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-2xl p-5 shadow-xl shadow-emerald-500/30 border border-emerald-400 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full blur-2xl -mr-10 -mt-10"></div>
                <div class="flex items-start gap-4 relative z-10">
                    <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center text-white flex-shrink-0">
                        <i class='bx bx-transfer text-2xl'></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="text-white font-bold text-lg mb-1">💰 Transfer Masuk!</h4>
                        @if($unreadCount === 1)
                            @php $transfer = $unreadTransfers->first(); @endphp
                            <p class="text-emerald-50 text-sm leading-relaxed">Anda menerima transfer <span class="font-bold text-white">Rp {{ number_format($transfer->amount, 0, ',', '.') }}</span> dari <span class="font-bold text-white">{{ $transfer->relatedMember->name ?? 'Member' }}</span></p>
                        @else
                            <p class="text-emerald-50 text-sm leading-relaxed">Anda menerima <span class="font-bold text-white">{{ $unreadCount }} transfer</span> hari ini. Total <span class="font-bold text-white">Rp {{ number_format($unreadTransfers->sum('amount'), 0, ',', '.') }}</span></p>
                        @endif
                        <a href="{{ route('member.simpanan') }}" class="inline-flex items-center gap-1 mt-3 px-4 py-2 bg-white text-emerald-600 rounded-lg text-sm font-bold hover:bg-emerald-50 transition-colors">
                            Lihat Detail <i class='bx bx-right-arrow-alt'></i>
                        </a>
                    </div>
                    <button @click="show = false" class="text-white/80 hover:text-white transition-colors p-1">
                        <i class='bx bx-x text-2xl'></i>
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Member Card (Flippable) --}}
    <div class="perspective-1000 w-full h-[220px] relative z-10 group cursor-pointer mb-8" x-data="{ flipped: false }" @click="flipped = !flipped">
        <div class="relative w-full h-full transition-all duration-700 transform-style-3d shadow-2xl rounded-2xl"
             :class="flipped ? 'rotate-y-180' : ''" x-init="setTimeout(() => $el.classList.add('animate-wiggle'), 1000)">

            {{-- Front Side --}}
            <div class="absolute inset-0 w-full h-full backface-hidden rounded-2xl overflow-hidden bg-gradient-to-br from-slate-800 to-slate-900 border border-slate-700/50 text-white flex flex-col justify-between p-6 transition-all duration-300"
                 :class="flipped ? 'z-0 opacity-0' : 'z-20 opacity-100 delay-200'">
                
                <div class="absolute top-0 -inset-full h-full w-1/2 z-5 block transform -skew-x-12 bg-gradient-to-r from-transparent to-white opacity-10"></div>
                <div class="absolute top-0 right-0 w-32 h-32 bg-white/5 rounded-full blur-2xl -mr-10 -mt-10"></div>

                <div class="relative z-10 w-full h-full flex flex-col justify-between">
                    {{-- Header --}}
                    <div>
                        <div class="flex justify-between items-start mb-6">
                            <i class='bx bxs-chip text-4xl opacity-80 text-yellow-500'></i>
                            <div class="flex items-center gap-3 opacity-80">
                                <div class="w-6 h-6 rounded-full border border-white/50 flex items-center justify-center animate-pulse">
                                    <i class='bx bx-refresh text-lg text-white'></i>
                                </div>
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

                    {{-- Footer --}}
                    <div class="flex justify-between items-end mt-4">
                        <div>
                            <p class="text-[10px] text-slate-400 uppercase tracking-widest mb-0.5">Nama Anggota</p>
                            <p class="font-bold uppercase tracking-wide text-sm sm:text-base">{{ $member->name ?? 'Member' }}</p>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <div class="w-6 h-6 bg-white/20 rounded flex items-center justify-center"><i class='bx bxs-cube-alt'></i></div>
                            <span class="font-bold italic text-sm">BERMADANI</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Back Side --}}
            <div class="absolute inset-0 w-full h-full backface-hidden rounded-2xl overflow-hidden bg-slate-900 border border-slate-700 shadow-xl rotate-y-180 flex items-center justify-center relative transition-all duration-300"
                 :class="flipped ? 'z-20 opacity-100 delay-200' : 'z-0 opacity-0'">
                <div class="text-center relative z-10">
                    <div class="inline-flex items-center justify-center p-3 bg-white rounded-xl shadow-lg mb-2">
                        <i class='bx bx-qr text-6xl text-slate-800'></i>
                    </div>
                    <p class="text-[10px] text-slate-400 font-mono tracking-widest uppercase">Member ID QR Code</p>
                </div>
                <p class="absolute bottom-4 text-[10px] text-slate-500 uppercase tracking-widest">Tap to Flip Back</p>
            </div>
        </div>
    </div>

    {{-- Main Balance Card (Retail Style) --}}
    @php
        $totalSimpanan = ($member->simpananPokok ?? 0) + ($member->simpananWajib ?? 0) + ($member->simpananSukarela ?? 0);
    @endphp
    <div class="mt-4 bg-gradient-to-br from-slate-800 to-[#0f172a] border border-slate-700/50 text-white p-6 rounded-2xl shadow-lg relative overflow-hidden group mb-8">
        {{-- Blue Glow Effect --}}
        <div class="absolute top-0 right-0 w-64 h-64 bg-blue-600/20 rounded-full blur-3xl -mr-20 -mt-20 pointer-events-none group-hover:bg-blue-600/30 transition-all duration-500"></div>
        <div class="absolute bottom-0 left-0 w-40 h-40 bg-indigo-500/10 rounded-full blur-2xl -ml-10 -mb-10 pointer-events-none"></div>
        
        <div class="flex items-center justify-between relative z-10">
            <div class="flex-1">
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-[11px] font-medium text-gray-400 uppercase tracking-wider">Total Simpanan</span>
                    <i class='bx bxs-check-circle text-blue-500 text-xs'></i>
                </div>
                <div class="flex items-center gap-3">
                    <h3 class="text-2xl sm:text-3xl font-bold tracking-tight text-white min-h-[40px] flex items-center">
                        <span x-show="showBalance" class="transition-opacity duration-300">
                            Rp {{ number_format($totalSimpanan, 0, ',', '.') }}
                        </span>
                        <span x-show="!showBalance" class="tracking-widest" style="display: none;">Rp •••••••</span>
                    </h3>
                    <button @click="showBalance = !showBalance; $wire.toggleBalance()" class="text-white hover:text-white transition-colors p-1 rounded-full hover:bg-white/10">
                        <i class='bx text-xl' :class="showBalance ? 'bx-hide' : 'bx-show'"></i>
                    </button>
                </div>
                <div class="mt-2">
                    <a href="{{ route('member.simpanan') }}" class="inline-flex text-[10px] bg-white/10 hover:bg-white/20 text-white px-4 py-1.5 rounded-full font-bold shadow-sm backdrop-blur-md transition-all items-center gap-1 border border-white/10">
                        <i class='bx bx-list-ul'></i> Rincian Simpanan
                    </a>
                </div>
            </div>

            <div class="w-px h-16 bg-white/10 mx-4 sm:mx-8"></div>

            <div class="min-w-[80px] sm:min-w-[120px] text-right sm:text-left">
                <div class="flex items-center justify-end sm:justify-start gap-1.5 mb-1">
                    <div class="w-4 h-4 rounded-full bg-amber-500 flex items-center justify-center text-[8px] text-black font-bold"><i class='bx bxs-coin-stack'></i></div>
                    <span class="text-[11px] font-medium text-gray-400">Points</span>
                </div>
                <h3 class="text-xl sm:text-2xl font-bold text-amber-500">{{ number_format($member->points ?? 0) }}</h3>
                <div class="mt-2 text-right sm:text-left">
                    <span class="text-[9px] px-2 py-0.5 rounded bg-white/10 border border-white/5 text-slate-300">{{ $member->tier ?? 'Bronze' }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Active Loans --}}
    @if(count($activeLoans) > 0)
        <div class="mb-8">
            <h3 class="text-sm font-bold text-slate-800 dark:text-white mb-4 px-1 flex items-center gap-2">
                <i class='bx bx-wallet-alt text-lg text-indigo-500'></i> Pinjaman Aktif
            </h3>
            
            <div class="flex overflow-x-auto gap-4 pb-4 snap-x hide-scrollbar px-1">
                @foreach($activeLoans as $loan)
                    @php
                        // Calculation Logic for Display
                        $simwaBMT = $loan->simwa_amount ?? 0;
                        $monthlyTotal = $loan->monthlyPayment;
                        $installmentPure = $monthlyTotal - $simwaBMT;
                        
                        $progress = $loan->tenor > 0 ? ($loan->paid_installments / $loan->tenor) * 100 : 0;
                        $isBMT = $loan->loanSource === 'BMT_ITQAN';
                    @endphp

                    <div class="snap-center shrink-0 w-80 bg-white dark:bg-darkCard rounded-2xl p-5 border border-slate-100 dark:border-slate-700 shadow-sm relative overflow-hidden group">
                        {{-- Background Decoration --}}
                        <div class="absolute -right-10 -top-10 w-32 h-32 {{ $isBMT ? 'bg-emerald-500/5' : 'bg-blue-500/5' }} rounded-full group-hover:scale-150 transition-transform duration-700"></div>

                        {{-- Header: Source & Amount --}}
                        <div class="flex justify-between items-start mb-4 relative z-10">
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider {{ $isBMT ? 'text-emerald-500' : 'text-blue-500' }} mb-1 block">
                                    {{ $loan->loanSource === 'BMT_ITQAN' ? 'BMT ITQAN' : 'KOP. BERMADANI' }}
                                </span>
                                <h4 class="text-lg font-black text-slate-800 dark:text-white">
                                    Rp {{ number_format($loan->remainingAmount, 0, ',', '.') }}
                                </h4>
                                <p class="text-[10px] text-slate-400">Sisa Pinjaman</p>
                            </div>
                            <div class="w-10 h-10 rounded-full {{ $isBMT ? 'bg-emerald-50 text-emerald-500' : 'bg-blue-50 text-blue-500' }} flex items-center justify-center text-xl">
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
                                <div class="h-full {{ $isBMT ? 'bg-emerald-500' : 'bg-blue-500' }} rounded-full" style="width: {{ $progress }}%"></div>
                            </div>
                        </div>

                        {{-- Monthly Payment Detail --}}
                        <div class="bg-slate-50 dark:bg-slate-800/50 rounded-xl p-3 border border-slate-100 dark:border-slate-700/50 relative z-10">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-xs text-slate-500">Potongan Bulanan</span>
                                <span class="text-sm font-bold text-slate-800 dark:text-white">Rp {{ number_format($monthlyTotal, 0, ',', '.') }}</span>
                            </div>
                            
                            {{-- Breakdown (Accordion Style) --}}
                            <div class="space-y-1 pt-2 border-t border-slate-200 dark:border-slate-700/50">
                                <div class="flex justify-between text-[10px]">
                                    <span class="text-slate-400">Angsuran Pokok + Margin</span>
                                    <span class="font-mono text-slate-600 dark:text-slate-300">Rp {{ number_format($installmentPure, 0, ',', '.') }}</span>
                                </div>
                                @if($simwaBMT > 0)
                                    <div class="flex justify-between text-[10px]">
                                        <span class="text-emerald-500 font-medium">Simpanan Wajib BMT</span>
                                        <span class="font-mono text-emerald-600 dark:text-emerald-400 font-bold">Rp {{ number_format($simwaBMT, 0, ',', '.') }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <!-- Debug/Empty State to confirm visibility -->
        <div class="mb-8 px-1">
             <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-dashed border-slate-300 dark:border-slate-700 text-center">
                <p class="text-xs text-slate-500 dark:text-slate-400">Tidak ada pinjaman aktif saat ini.</p>
            </div>
        </div>
    @endif

    <div>
        <h3 class="text-sm font-bold text-slate-700 dark:text-slate-300 mb-4 px-1">Menu Utama</h3>
        <div class="grid grid-cols-4 gap-3">
            <a href="{{ route('member.simpanan') }}" class="group flex flex-col items-center gap-2">
                <div class="w-14 h-14 rounded-2xl bg-white dark:bg-slate-800 shadow-sm border border-slate-100 dark:border-slate-700 flex items-center justify-center text-2xl text-emerald-500 group-hover:scale-105 group-active:scale-95 transition-all duration-300 relative overflow-hidden">
                    <div class="absolute inset-0 bg-emerald-500/5 group-hover:bg-emerald-500/10 transition-colors"></div>
                    <i class='bx bxs-wallet-alt'></i>
                </div>
                <span class="text-[10px] font-medium text-slate-600 dark:text-slate-400 text-center">Simpanan</span>
            </a>

            <a href="{{ route('member.transfer') }}" class="group flex flex-col items-center gap-2">
                <div class="w-14 h-14 rounded-2xl bg-white dark:bg-slate-800 shadow-sm border border-slate-100 dark:border-slate-700 flex items-center justify-center text-2xl text-blue-500 group-hover:scale-105 group-active:scale-95 transition-all duration-300 relative overflow-hidden">
                    <div class="absolute inset-0 bg-blue-500/5 group-hover:bg-blue-500/10 transition-colors"></div>
                    <i class='bx bxs-paper-plane'></i>
                </div>
                <span class="text-[10px] font-medium text-slate-600 dark:text-slate-400 text-center">Transfer</span>
            </a>

            <a href="{{ route('member.transactions') }}" class="group flex flex-col items-center gap-2">
                <div class="w-14 h-14 rounded-2xl bg-white dark:bg-slate-800 shadow-sm border border-slate-100 dark:border-slate-700 flex items-center justify-center text-2xl text-amber-500 group-hover:scale-105 group-active:scale-95 transition-all duration-300 relative overflow-hidden">
                    <div class="absolute inset-0 bg-amber-500/5 group-hover:bg-amber-500/10 transition-colors"></div>
                    <i class='bx bxs-time-five'></i>
                </div>
                <span class="text-[10px] font-medium text-slate-600 dark:text-slate-400 text-center">Riwayat</span>
            </a>

            <a href="{{ route('member.profile') }}" class="group flex flex-col items-center gap-2">
                <div class="w-14 h-14 rounded-2xl bg-white dark:bg-slate-800 shadow-sm border border-slate-100 dark:border-slate-700 flex items-center justify-center text-2xl text-purple-500 group-hover:scale-105 group-active:scale-95 transition-all duration-300 relative overflow-hidden">
                    <div class="absolute inset-0 bg-purple-500/5 group-hover:bg-purple-500/10 transition-colors"></div>
                    <i class='bx bxs-user-circle'></i>
                </div>
                <span class="text-[10px] font-medium text-slate-600 dark:text-slate-400 text-center">Profil</span>
            </a>
        </div>
    </div>

    {{-- Activity Feed --}}
    <div class="space-y-6">
        
        {{-- Simpanan Activities --}}
        <div>
            <div class="flex justify-between items-end mb-4 px-1">
                <h3 class="text-sm font-bold text-slate-700 dark:text-slate-200">Aktivitas Simpanan</h3>
                <a href="{{ route('member.simpanan') }}" class="text-xs text-emerald-600 hover:text-emerald-500 font-medium">Lihat Semua</a>
            </div>
            <div class="space-y-3">
                @forelse($recentSimpanan as $simp)
                    <div class="bg-white dark:bg-darkCard rounded-2xl p-4 border border-slate-100 dark:border-slate-700 flex items-center gap-4 hover:shadow-sm transition-all">
                        <div class="w-10 h-10 rounded-full {{ in_array($simp->transactionType, ['SETOR', 'TRANSFER_IN']) ? 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600' : 'bg-rose-50 dark:bg-rose-900/20 text-rose-500' }} flex items-center justify-center text-lg shrink-0">
                            <i class='bx {{ in_array($simp->transactionType, ['SETOR', 'TRANSFER_IN']) ? 'bx-down-arrow-alt' : 'bx-up-arrow-alt' }}'></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-bold text-slate-800 dark:text-white truncate">
                                @if($simp->transactionType === 'SETOR') Simpanan {{ ucfirst(strtolower($simp->type)) }}
                                @elseif($simp->transactionType === 'TRANSFER_IN') Transfer Masuk
                                @elseif($simp->transactionType === 'TRANSFER_OUT') Transfer Keluar
                                @else Penarikan {{ ucfirst(strtolower($simp->type)) }} @endif
                            </h4>
                            <p class="text-[10px] text-slate-500 font-medium">
                                @if($simp->transactionType === 'SETOR') Setoran
                                @elseif($simp->transactionType === 'TRANSFER_IN') Dari: {{ $simp->relatedMember->name ?? '-' }}
                                @elseif($simp->transactionType === 'TRANSFER_OUT') Ke: {{ $simp->relatedMember->name ?? '-' }}
                                @else Penarikan @endif
                                • {{ $simp->created_at->format('d M') }}
                            </p>
                        </div>
                        <div class="text-right shrink-0">
                            <p class="text-sm font-bold {{ in_array($simp->transactionType, ['SETOR', 'TRANSFER_IN']) ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-500' }}">
                                {{ in_array($simp->transactionType, ['SETOR', 'TRANSFER_IN']) ? '+' : '-' }}Rp {{ number_format($simp->amount, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-6 border-2 border-dashed border-slate-100 dark:border-slate-700 rounded-2xl">
                        <p class="text-xs text-slate-400">Belum ada aktivitas simpanan</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Shopping Activities --}}
        <div>
            <div class="flex justify-between items-end mb-4 px-1">
                <h3 class="text-sm font-bold text-slate-700 dark:text-slate-200">Riwayat Belanja</h3>
                <a href="{{ route('member.transactions') }}" class="text-xs text-emerald-600 hover:text-emerald-500 font-medium">Lihat Semua</a>
            </div>
            <div class="space-y-3">
                @forelse($recentTransactions as $trx)
                    <div class="bg-white dark:bg-darkCard rounded-2xl p-4 border border-slate-100 dark:border-slate-700 flex items-center gap-4 hover:shadow-sm transition-all">
                        <div class="w-10 h-10 rounded-full bg-blue-50 dark:bg-blue-900/20 text-blue-600 flex items-center justify-center text-lg shrink-0">
                            <i class='bx bx-shopping-bag'></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-bold text-slate-800 dark:text-white truncate">Belanja Toko</h4>
                            <p class="text-[10px] text-slate-500 font-medium">{{ $trx->created_at->format('d M Y • H:i') }}</p>
                        </div>
                        <div class="text-right shrink-0">
                            <p class="text-sm font-bold text-slate-800 dark:text-white">-Rp {{ number_format($trx->totalAmount, 0, ',', '.') }}</p>
                            <span class="text-[9px] font-bold text-emerald-500">{{ ucfirst($trx->status) }}</span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-6 border-2 border-dashed border-slate-100 dark:border-slate-700 rounded-2xl">
                        <p class="text-xs text-slate-400">Belum ada transaksi belanja</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>
</div>
