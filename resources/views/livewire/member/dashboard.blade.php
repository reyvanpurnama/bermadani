@push('styles')
    <style>
        .perspective-1000 { perspective: 1000px; }
        .transform-style-3d { transform-style: preserve-3d; }
        .backface-hidden { backface-visibility: hidden; }
        .rotate-y-180 { transform: rotateY(180deg); }
        
        @keyframes subtleWiggle {
            0%, 100% { transform: rotateY(0deg); }
            20% { transform: rotateY(10deg); }
            40% { transform: rotateY(-8deg); }
            60% { transform: rotateY(4deg); }
        }
        .animate-subtle-wiggle { animation: subtleWiggle 1.4s ease-out; }
        [x-cloak] { display: none !important; }
    </style>
@endpush

<div x-data="{ showBalance: {{ $showBalance ? 'true' : 'false' }} }" class="space-y-6">
    @section('title', 'Beranda Anggota')

    {{-- Toast Notification for Unread Transfers --}}
    @if($unreadCount > 0)
        <div x-data="{ show: true }" x-show="show" 
             x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-4" 
             class="mb-2">
            <div class="bg-gradient-to-r from-emerald-600 via-teal-600 to-[#155A6B] rounded-2xl p-4 sm:p-5 shadow-xl shadow-emerald-600/20 border border-emerald-400/40 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full blur-2xl -mr-10 -mt-10 pointer-events-none"></div>
                <div class="flex items-start gap-4 relative z-10">
                    <div class="w-11 h-11 bg-white/20 backdrop-blur-md rounded-xl flex items-center justify-center text-white shrink-0 shadow-sm">
                        <i class='bx bx-transfer text-2xl'></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <h4 class="text-white font-extrabold text-base">💰 Transfer Masuk Diterima!</h4>
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-white text-emerald-700 uppercase tracking-wider">Hari Ini</span>
                        </div>
                        @if($unreadCount === 1)
                            @php $transfer = $unreadTransfers->first(); @endphp
                            <p class="text-emerald-50 text-xs sm:text-sm mt-0.5 leading-relaxed">
                                Anda menerima transfer <span class="font-extrabold text-white">Rp {{ number_format($transfer->amount, 0, ',', '.') }}</span> dari <span class="font-extrabold text-white">{{ $transfer->relatedMember->name ?? 'Anggota' }}</span>
                            </p>
                        @else
                            <p class="text-emerald-50 text-xs sm:text-sm mt-0.5 leading-relaxed">
                                Anda menerima <span class="font-extrabold text-white">{{ $unreadCount }} transfer</span> hari ini dengan total <span class="font-extrabold text-white">Rp {{ number_format($unreadTransfers->sum('amount'), 0, ',', '.') }}</span>
                            </p>
                        @endif
                        <a href="{{ route('member.simpanan') }}" class="inline-flex items-center gap-1.5 mt-3 px-3.5 py-1.5 bg-white text-[#155A6B] rounded-xl text-xs font-bold hover:bg-emerald-50 transition-colors shadow-sm">
                            <span>Lihat Rincian Simpanan</span>
                            <i class='bx bx-right-arrow-alt text-base'></i>
                        </a>
                    </div>
                    <button @click="show = false" class="text-white/70 hover:text-white transition-colors p-1 rounded-lg">
                        <i class='bx bx-x text-2xl'></i>
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Welcome Hero Greeting Bar --}}
    <div class="bg-white/80 dark:bg-zinc-900/80 backdrop-blur-xl border border-zinc-200/80 dark:border-zinc-800 rounded-3xl p-5 sm:p-6 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-[#155A6B] to-emerald-500 text-white font-extrabold flex items-center justify-center text-xl shadow-md shadow-[#155A6B]/20 shrink-0">
                {{ strtoupper(substr(optional($member->user)->name ?? $member->name ?? 'A', 0, 1)) }}
            </div>
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h2 class="font-heading font-extrabold text-lg sm:text-xl text-zinc-900 dark:text-white tracking-tight">
                        Selamat Datang, {{ optional($member->user)->name ?? $member->name ?? 'Anggota Civitas' }}
                    </h2>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-[#155A6B]/10 dark:bg-emerald-400/20 text-[#155A6B] dark:text-emerald-400 border border-[#155A6B]/20 dark:border-emerald-400/30 uppercase tracking-wider">
                        {{ $member->tier ?? 'Bronze' }} Member
                    </span>
                </div>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1 flex items-center gap-2">
                    <span>ID Anggota: <strong class="font-mono text-zinc-700 dark:text-zinc-300">{{ $member->nomorAnggota ?? '-' }}</strong></span>
                    <span>•</span>
                    <span class="text-emerald-600 dark:text-emerald-400 font-semibold flex items-center gap-1">
                        <i class='bx bxs-check-shield'></i> Status: {{ $member->status ?? 'Aktif' }}
                    </span>
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2 self-start sm:self-auto">
            <a href="{{ route('member.profile') }}" class="px-4 py-2 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-200 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5">
                <i class='bx bx-user-circle text-base text-[#155A6B] dark:text-emerald-400'></i>
                <span>Profil Saya</span>
            </a>
        </div>
    </div>

    {{-- Top Bento Row: Digital Member Pass (2 cols on lg) + Quick Actions (1 col on lg) --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Digital Member Pass Card (Spans 2 cols on lg) --}}
        <div class="lg:col-span-2">
            <div class="perspective-1000 w-full h-[220px] sm:h-[230px] relative z-10 group cursor-pointer" x-data="{ flipped: false }" @click="flipped = !flipped">
                <div class="relative w-full h-full transition-all duration-700 transform-style-3d shadow-2xl rounded-3xl"
                     :class="flipped ? 'rotate-y-180' : ''" x-init="setTimeout(() => $el.classList.add('animate-subtle-wiggle'), 800)">

                    {{-- Front Side (Metallic Deep Teal Apple Card) --}}
                    <div class="absolute inset-0 w-full h-full backface-hidden rounded-3xl overflow-hidden bg-gradient-to-br from-[#155A6B] via-[#0E3E4B] to-[#071F26] border border-white/15 text-white flex flex-col justify-between p-6 transition-all duration-300 shadow-xl"
                         :class="flipped ? 'z-0 opacity-0' : 'z-20 opacity-100 delay-150'">
                        
                        {{-- Background Mesh Decor --}}
                        <div class="absolute top-0 -inset-full h-full w-1/2 z-5 block transform -skew-x-12 bg-gradient-to-r from-transparent via-white/10 to-transparent"></div>
                        <div class="absolute -top-12 -right-12 w-48 h-48 bg-emerald-400/15 rounded-full blur-3xl pointer-events-none"></div>
                        <div class="absolute -bottom-10 -left-10 w-40 h-40 bg-[#155A6B]/40 rounded-full blur-2xl pointer-events-none"></div>

                        <div class="relative z-10 w-full h-full flex flex-col justify-between">
                            {{-- Header --}}
                            <div>
                                <div class="flex justify-between items-start mb-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-7 rounded-md bg-gradient-to-tr from-amber-300 via-amber-400 to-amber-200 border border-amber-100/50 shadow-inner flex items-center justify-center">
                                            <div class="w-6 h-4 border border-amber-700/30 rounded-sm"></div>
                                        </div>
                                        <div class="flex items-center gap-1 text-white/70">
                                            <i class='bx bx-wifi text-xl rotate-90'></i>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="px-2.5 py-1 rounded-full bg-white/10 backdrop-blur-md border border-white/15 text-[10px] font-extrabold uppercase tracking-widest text-emerald-300">
                                            {{ $member->tier ?? 'Bronze' }} CARD
                                        </span>
                                        <div class="w-7 h-7 rounded-full bg-white/10 backdrop-blur-md flex items-center justify-center text-white/80" title="Klik untuk balik kartu (QR Code)">
                                            <i class='bx bx-refresh text-lg'></i>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center gap-3 mb-1 relative z-20" x-data="{ copied: false }">
                                    <p class="font-mono text-xl sm:text-2xl font-bold tracking-widest drop-shadow-md text-white">
                                        {{ $member->nomorAnggota ?? '--------' }}
                                    </p>
                                    <button
                                        @click.stop="navigator.clipboard.writeText('{{ $member->nomorAnggota ?? '' }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                        class="text-white/60 hover:text-white transition-colors p-1.5 rounded-lg bg-white/5 hover:bg-white/15 backdrop-blur-sm"
                                        title="Salin Nomor Anggota">
                                        <i class='bx text-lg' :class="copied ? 'bx-check text-emerald-400 font-bold' : 'bx-copy'"></i>
                                    </button>
                                </div>
                            </div>

                            {{-- Footer --}}
                            <div class="flex justify-between items-end">
                                <div>
                                    <p class="text-[9px] text-emerald-200/80 uppercase font-extrabold tracking-widest mb-0.5">Pemegang Kartu Anggota</p>
                                    <p class="font-heading font-extrabold uppercase tracking-wider text-sm sm:text-base text-white">
                                        {{ optional($member->user)->name ?? $member->name ?? 'Anggota' }}
                                    </p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-lg bg-white/15 backdrop-blur-md flex items-center justify-center text-white text-base">
                                        <i class='bx bxs-cube-alt'></i>
                                    </div>
                                    <span class="font-heading font-black italic tracking-wide text-xs sm:text-sm text-white">BERMADANI</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Back Side (QR Code View for POS Checkout) --}}
                    <div class="absolute inset-0 w-full h-full backface-hidden rounded-3xl overflow-hidden bg-zinc-900 border border-zinc-700 shadow-2xl rotate-y-180 flex items-center justify-center relative transition-all duration-300"
                         :class="flipped ? 'z-20 opacity-100 delay-150' : 'z-0 opacity-0'">
                        <div class="text-center relative z-10">
                            <div class="inline-flex items-center justify-center p-3 bg-white rounded-2xl shadow-xl mb-2">
                                <i class='bx bx-qr text-6xl text-zinc-900'></i>
                            </div>
                            <p class="text-[10px] text-zinc-400 font-mono tracking-widest uppercase font-bold">QR Member POS Kasir Koperasi</p>
                            <p class="text-[11px] font-mono text-emerald-400 font-bold mt-1">{{ $member->nomorAnggota }}</p>
                        </div>
                        <p class="absolute bottom-3 text-[10px] text-zinc-500 uppercase tracking-widest font-bold flex items-center gap-1">
                            <i class='bx bx-tap'></i> Ketuk untuk membalik kembali
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Actions Tile (1 col on lg) --}}
        <div class="bg-white/80 dark:bg-zinc-900/80 backdrop-blur-xl border border-zinc-200/80 dark:border-zinc-800 rounded-3xl p-5 shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between mb-3">
                <h3 class="font-heading font-extrabold text-xs text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Aksi Cepat</h3>
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            </div>

            <div class="grid grid-cols-2 gap-3 flex-1">
                <a href="{{ route('member.simpanan') }}" class="group p-3.5 rounded-2xl bg-zinc-50 dark:bg-zinc-800/60 hover:bg-[#155A6B]/10 dark:hover:bg-emerald-500/10 border border-zinc-200/50 dark:border-zinc-700/50 transition-all duration-200 flex flex-col justify-between">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500/10 dark:bg-emerald-400/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                        <i class='bx bxs-wallet-alt'></i>
                    </div>
                    <div class="mt-3">
                        <span class="font-heading font-extrabold text-xs text-zinc-800 dark:text-white block group-hover:text-[#155A6B] dark:group-hover:text-emerald-400 transition-colors">Simpanan</span>
                        <span class="text-[10px] text-zinc-400 block mt-0.5">Atur Saldo</span>
                    </div>
                </a>

                <a href="{{ route('member.transfer') }}" class="group p-3.5 rounded-2xl bg-zinc-50 dark:bg-zinc-800/60 hover:bg-[#155A6B]/10 dark:hover:bg-emerald-500/10 border border-zinc-200/50 dark:border-zinc-700/50 transition-all duration-200 flex flex-col justify-between">
                    <div class="w-10 h-10 rounded-xl bg-blue-500/10 dark:bg-blue-400/20 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                        <i class='bx bxs-paper-plane'></i>
                    </div>
                    <div class="mt-3">
                        <span class="font-heading font-extrabold text-xs text-zinc-800 dark:text-white block group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">Transfer</span>
                        <span class="text-[10px] text-zinc-400 block mt-0.5">Kirim Sesama</span>
                    </div>
                </a>

                <a href="{{ route('member.loans') }}" class="group p-3.5 rounded-2xl bg-zinc-50 dark:bg-zinc-800/60 hover:bg-[#155A6B]/10 dark:hover:bg-emerald-500/10 border border-zinc-200/50 dark:border-zinc-700/50 transition-all duration-200 flex flex-col justify-between">
                    <div class="w-10 h-10 rounded-xl bg-amber-500/10 dark:bg-amber-400/20 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                        <i class='bx bxs-bank'></i>
                    </div>
                    <div class="mt-3">
                        <span class="font-heading font-extrabold text-xs text-zinc-800 dark:text-white block group-hover:text-amber-600 dark:group-hover:text-amber-400 transition-colors">Pembiayaan</span>
                        <span class="text-[10px] text-zinc-400 block mt-0.5">Portofolio Akad</span>
                    </div>
                </a>

                <a href="{{ route('member.transactions') }}" class="group p-3.5 rounded-2xl bg-zinc-50 dark:bg-zinc-800/60 hover:bg-[#155A6B]/10 dark:hover:bg-emerald-500/10 border border-zinc-200/50 dark:border-zinc-700/50 transition-all duration-200 flex flex-col justify-between">
                    <div class="w-10 h-10 rounded-xl bg-purple-500/10 dark:bg-purple-400/20 text-purple-600 dark:text-purple-400 flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                        <i class='bx bxs-receipt'></i>
                    </div>
                    <div class="mt-3">
                        <span class="font-heading font-extrabold text-xs text-zinc-800 dark:text-white block group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors">Riwayat</span>
                        <span class="text-[10px] text-zinc-400 block mt-0.5">Daftar Transaksi</span>
                    </div>
                </a>
            </div>
        </div>

    </div>

    {{-- Main Balance & Financial Overview Bento Grid --}}
    @php
        $totalSimpanan = ($member->simpananPokok ?? 0) + ($member->simpananWajib ?? 0) + ($member->simpananSukarela ?? 0);
    @endphp
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        {{-- Total Simpanan Card (2 cols on md/lg) --}}
        <div class="md:col-span-2 bg-gradient-to-br from-[#155A6B] via-[#0E3E4B] to-zinc-900 border border-[#155A6B]/30 text-white p-6 rounded-3xl shadow-xl relative overflow-hidden group">
            <div class="absolute -right-16 -top-16 w-64 h-64 bg-emerald-400/15 rounded-full blur-3xl pointer-events-none group-hover:bg-emerald-400/25 transition-all duration-500"></div>
            
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 relative z-10">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-1.5">
                        <span class="text-[11px] font-extrabold text-emerald-200 uppercase tracking-wider bg-white/10 backdrop-blur-md px-2.5 py-0.5 rounded-full">
                            Akumulasi Total Simpanan
                        </span>
                        <i class='bx bxs-check-circle text-emerald-400 text-sm'></i>
                    </div>
                    <div class="flex items-center gap-3 mt-1">
                        <h3 class="font-heading text-3xl sm:text-4xl font-extrabold tracking-tight text-white min-h-[44px] flex items-center">
                            <span x-show="showBalance" class="transition-opacity duration-300">
                                Rp {{ number_format($totalSimpanan, 0, ',', '.') }}
                            </span>
                            <span x-show="!showBalance" class="tracking-widest" style="display: none;">Rp •••••••••</span>
                        </h3>
                        <button @click="showBalance = !showBalance; $wire.toggleBalance()" class="text-white/70 hover:text-white transition-colors p-1.5 rounded-full hover:bg-white/10">
                            <i class='bx text-xl' :class="showBalance ? 'bx-hide' : 'bx-show'"></i>
                        </button>
                    </div>

                    {{-- Breakdown Pills --}}
                    <div class="flex items-center gap-2 mt-4 flex-wrap">
                        <div class="px-3 py-1.5 rounded-xl bg-white/10 backdrop-blur-md border border-white/10 text-xs">
                            <span class="text-white/70 text-[10px] uppercase font-bold block">Pokok</span>
                            <span class="font-mono font-bold text-white">
                                <span x-show="showBalance">Rp {{ number_format($member->simpananPokok ?? 0, 0, ',', '.') }}</span>
                                <span x-show="!showBalance" style="display: none;">Rp •••</span>
                            </span>
                        </div>
                        <div class="px-3 py-1.5 rounded-xl bg-white/10 backdrop-blur-md border border-white/10 text-xs">
                            <span class="text-white/70 text-[10px] uppercase font-bold block">Wajib</span>
                            <span class="font-mono font-bold text-white">
                                <span x-show="showBalance">Rp {{ number_format($member->simpananWajib ?? 0, 0, ',', '.') }}</span>
                                <span x-show="!showBalance" style="display: none;">Rp •••</span>
                            </span>
                        </div>
                        <div class="px-3 py-1.5 rounded-xl bg-emerald-500/20 backdrop-blur-md border border-emerald-400/30 text-xs">
                            <span class="text-emerald-200 text-[10px] uppercase font-bold block">Sukarela</span>
                            <span class="font-mono font-bold text-emerald-300">
                                <span x-show="showBalance">Rp {{ number_format($member->simpananSukarela ?? 0, 0, ',', '.') }}</span>
                                <span x-show="!showBalance" style="display: none;">Rp •••</span>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="sm:self-stretch sm:flex sm:flex-col sm:justify-between sm:items-end w-full sm:w-auto">
                    <a href="{{ route('member.simpanan') }}" class="w-full sm:w-auto px-4 py-2.5 bg-white text-[#155A6B] hover:bg-emerald-50 rounded-2xl font-extrabold text-xs shadow-md transition-all flex items-center justify-center gap-1.5">
                        <i class='bx bx-list-ul text-base'></i> Rincian Simpanan
                    </a>
                </div>
            </div>
        </div>

        {{-- Loyalty Points & Tier Card (1 col on md/lg) --}}
        <div class="bg-white/80 dark:bg-zinc-900/80 backdrop-blur-xl border border-zinc-200/80 dark:border-zinc-800 p-6 rounded-3xl shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-zinc-400">Poin Loyalty & Status</span>
                <div class="w-8 h-8 rounded-full bg-amber-500/10 dark:bg-amber-400/20 text-amber-500 flex items-center justify-center text-lg">
                    <i class='bx bxs-star'></i>
                </div>
            </div>

            <div class="my-4">
                <div class="flex items-baseline gap-2">
                    <h3 class="font-heading text-3xl font-extrabold text-amber-500 tracking-tight">
                        {{ number_format($member->points ?? 0) }}
                    </h3>
                    <span class="text-xs font-bold text-zinc-400">Poin</span>
                </div>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                    Dapatkan poin dari setiap transaksi belanja di Toko Koperasi.
                </p>
            </div>

            <div class="pt-3 border-t border-zinc-100 dark:border-zinc-800 flex items-center justify-between">
                <span class="text-xs text-zinc-500 font-medium">Tingkat Diskon:</span>
                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20 uppercase tracking-wider">
                    {{ $member->tier ?? 'Bronze' }} Member
                </span>
            </div>
        </div>

    </div>

    {{-- SHU Card Widget --}}
    @if($shuInfo)
        <div x-data="{ showMemberFormula: false }" class="bg-gradient-to-r from-[#155A6B] via-teal-700 to-emerald-700 text-white p-6 rounded-3xl shadow-xl border border-emerald-400/30 relative overflow-hidden group">
            <div class="absolute top-0 right-0 w-40 h-40 bg-white/10 rounded-full blur-2xl -mr-10 -mt-10 pointer-events-none"></div>

            <div class="flex flex-col sm:flex-row items-start justify-between gap-4 relative z-10">
                <div>
                    <div class="flex items-center gap-2 mb-1.5 flex-wrap">
                        <span class="px-3 py-0.5 rounded-full text-[10px] font-extrabold bg-white/20 uppercase tracking-wider backdrop-blur-sm">
                            🎁 Sisa Hasil Usaha (SHU) {{ $shuInfo['title'] }}
                        </span>
                        @if($shuInfo['isFinalized'])
                            <span class="text-[10px] font-extrabold bg-emerald-400 text-emerald-950 px-2.5 py-0.5 rounded-full">Resmi Disahkan RAT</span>
                        @else
                            <span class="text-[10px] font-extrabold bg-amber-400 text-amber-950 px-2.5 py-0.5 rounded-full">Estimasi Perhitungan</span>
                        @endif
                    </div>
                    <p class="text-xs text-emerald-100 mb-1">Porsi Bagian SHU Anggota yang Siap Diterima:</p>
                    <h3 class="font-heading text-2xl sm:text-3xl font-black tracking-tight text-white">
                        Rp {{ number_format($shuInfo['shuAmount'], 0, ',', '.') }}
                    </h3>
                    <p class="text-xs text-emerald-100/90 mt-1 font-medium">
                        Porsi Keanggotaan: <span class="font-extrabold text-white">{{ number_format($shuInfo['portionPercentage'], 3, ',', '.') }}%</span> | Basis Simpanan: Rp {{ number_format($shuInfo['totalSimpanan'] ?? 0, 0, ',', '.') }}
                    </p>
                    <button @click="showMemberFormula = !showMemberFormula" 
                        class="mt-3 text-xs bg-white/15 hover:bg-white/25 text-white px-3.5 py-1.5 rounded-xl font-bold backdrop-blur-sm transition-all flex items-center gap-1.5">
                        <i class='bx bx-info-circle text-sm'></i> <span x-text="showMemberFormula ? 'Sembunyikan Cara Hitung' : 'Lihat Transparansi Perhitungan SHU'"></span>
                    </button>
                </div>

                <div class="sm:text-right flex flex-row sm:flex-col items-center sm:items-end justify-between w-full sm:w-auto gap-2">
                    <div class="w-12 h-12 rounded-2xl bg-white/15 backdrop-blur-md flex items-center justify-center text-2xl text-white shadow-sm">
                        <i class='bx bx-gift'></i>
                    </div>
                    @if($shuInfo['isDisbursed'])
                        <span class="text-xs bg-white text-emerald-800 font-extrabold px-3 py-1 rounded-full shadow-sm">
                            ✓ Telah Dicairkan
                        </span>
                    @else
                        <span class="text-xs bg-white/20 text-white font-bold px-3 py-1 rounded-full backdrop-blur-md border border-white/20">
                            Siap Dicairkan
                        </span>
                    @endif
                </div>
            </div>

            {{-- Transparent Formula Accordion inside Card --}}
            <div x-show="showMemberFormula" x-transition class="mt-5 pt-4 border-t border-white/20 text-xs space-y-2 relative z-10">
                <p class="font-extrabold text-white text-xs">📐 Rumus Transparansi Pembagian SHU:</p>
                <div class="bg-black/30 backdrop-blur-md p-4 rounded-2xl space-y-2 text-xs font-mono text-emerald-50 border border-white/10">
                    <p>1. <strong>Jasa Simpanan</strong> = (Simpanan Anda ÷ Total Simpanan Koperasi) × Pool Jasa Simpanan</p>
                    <p>2. <strong>Jasa Usaha / Belanja</strong> = (Total Belanja Anda ÷ Total Belanja Koperasi) × Pool Jasa Usaha</p>
                    <p class="text-white font-extrabold font-sans pt-1 border-t border-white/10">Total SHU Hak Anggota = Jasa Simpanan + Jasa Usaha</p>
                </div>
            </div>
        </div>
    @endif

    {{-- Active Loans Section --}}
    @if(count($activeLoans) > 0)
        <div class="bg-white/80 dark:bg-zinc-900/80 backdrop-blur-xl border border-zinc-200/80 dark:border-zinc-800 rounded-3xl p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-heading font-extrabold text-base text-zinc-900 dark:text-white flex items-center gap-2">
                    <i class='bx bx-bank text-xl text-[#155A6B] dark:text-emerald-400'></i> Pembiayaan & Pinjaman Aktif
                </h3>
                <a href="{{ route('member.loans') }}" class="text-xs font-bold text-[#155A6B] dark:text-emerald-400 hover:underline">Lihat Portofolio</a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($activeLoans as $loan)
                    @php
                        $simwaBMT = $loan->simwa_amount ?? 0;
                        $monthlyTotal = $loan->monthlyPayment;
                        $installmentPure = $monthlyTotal - $simwaBMT;
                        $progress = $loan->tenor > 0 ? min(100, ($loan->paid_installments / $loan->tenor) * 100) : 0;
                        $isBMT = $loan->loanSource === 'BMT_ITQAN';
                    @endphp

                    <div class="bg-zinc-50 dark:bg-zinc-800/50 rounded-2xl p-5 border border-zinc-200/60 dark:border-zinc-700/60 relative overflow-hidden group">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <span class="px-2.5 py-0.5 rounded-full text-[9px] font-extrabold uppercase tracking-wider {{ $isBMT ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20' : 'bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20' }}">
                                    {{ $isBMT ? 'BMT ITQAN' : 'KOPERASI BERMADANI' }}
                                </span>
                                <h4 class="font-heading font-extrabold text-lg text-zinc-900 dark:text-white mt-1">
                                    Rp {{ number_format($loan->remainingAmount, 0, ',', '.') }}
                                </h4>
                                <p class="text-[10px] text-zinc-400 font-medium">Sisa Kewajiban Pokok</p>
                            </div>
                            <div class="w-10 h-10 rounded-xl {{ $isBMT ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' : 'bg-blue-500/10 text-blue-600 dark:text-blue-400' }} flex items-center justify-center text-xl">
                                <i class='bx {{ $isBMT ? 'bxs-bank' : 'bxs-building-house' }}'></i>
                            </div>
                        </div>

                        {{-- Progress Bar --}}
                        <div class="mb-3">
                            <div class="flex justify-between text-[10px] font-extrabold text-zinc-500 mb-1">
                                <span>Angsuran Ke-{{ $loan->paid_installments + 1 }} dari {{ $loan->tenor }} Bulan</span>
                                <span class="{{ $isBMT ? 'text-emerald-600 dark:text-emerald-400' : 'text-blue-600 dark:text-blue-400' }}">{{ round($progress) }}%</span>
                            </div>
                            <div class="w-full h-2 bg-zinc-200 dark:bg-zinc-700 rounded-full overflow-hidden">
                                <div class="h-full {{ $isBMT ? 'bg-emerald-500' : 'bg-[#155A6B]' }} rounded-full transition-all duration-500"
                                    style="width: {{ $progress }}%"></div>
                            </div>
                        </div>

                        <div class="bg-white dark:bg-zinc-900 rounded-xl p-3 border border-zinc-200/50 dark:border-zinc-700/50 text-xs">
                            <div class="flex justify-between items-center">
                                <span class="text-zinc-500 text-[11px]">Potongan Bulanan:</span>
                                <span class="font-mono font-bold text-zinc-900 dark:text-white">Rp {{ number_format($monthlyTotal, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Bottom Bento Split Row: Simpanan Activities + Recent Store Purchases --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        {{-- Activity Feed: Simpanan --}}
        <div class="bg-white/80 dark:bg-zinc-900/80 backdrop-blur-xl border border-zinc-200/80 dark:border-zinc-800 rounded-3xl p-6 shadow-sm">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-heading font-extrabold text-sm text-zinc-900 dark:text-white flex items-center gap-2">
                    <i class='bx bx-history text-lg text-[#155A6B] dark:text-emerald-400'></i> Aktivitas Simpanan
                </h3>
                <a href="{{ route('member.simpanan') }}" class="text-xs font-bold text-[#155A6B] dark:text-emerald-400 hover:underline">Lihat Semua</a>
            </div>

            <div class="space-y-3">
                @forelse($recentSimpanan as $simp)
                    <div class="p-3.5 rounded-2xl bg-zinc-50 dark:bg-zinc-800/40 border border-zinc-200/50 dark:border-zinc-700/40 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl {{ in_array($simp->transactionType, ['SETOR', 'TRANSFER_IN']) ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' : 'bg-rose-500/10 text-rose-500' }} flex items-center justify-center text-lg shrink-0">
                                <i class='bx {{ in_array($simp->transactionType, ['SETOR', 'TRANSFER_IN']) ? 'bx-down-arrow-alt' : 'bx-up-arrow-alt' }}'></i>
                            </div>
                            <div class="min-w-0">
                                <h4 class="font-extrabold text-xs text-zinc-900 dark:text-white truncate">
                                    @if($simp->transactionType === 'SETOR') Setoran Simpanan {{ ucfirst(strtolower($simp->type)) }}
                                    @elseif($simp->transactionType === 'TRANSFER_IN') Transfer Masuk
                                    @elseif($simp->transactionType === 'TRANSFER_OUT') Transfer Keluar
                                    @else Penarikan {{ ucfirst(strtolower($simp->type)) }} @endif
                                </h4>
                                <p class="text-[10px] text-zinc-400">
                                    {{ $simp->created_at->format('d M Y • H:i') }}
                                </p>
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="font-mono font-extrabold text-xs {{ in_array($simp->transactionType, ['SETOR', 'TRANSFER_IN']) ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-500' }}">
                                {{ in_array($simp->transactionType, ['SETOR', 'TRANSFER_IN']) ? '+' : '-' }}Rp {{ number_format($simp->amount, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 border-2 border-dashed border-zinc-200 dark:border-zinc-800 rounded-2xl">
                        <i class='bx bx-wallet text-3xl text-zinc-300 dark:text-zinc-600 mb-1'></i>
                        <p class="text-xs text-zinc-400">Belum ada mutasi simpanan terbaru.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Activity Feed: Shopping / Transactions --}}
        <div class="bg-white/80 dark:bg-zinc-900/80 backdrop-blur-xl border border-zinc-200/80 dark:border-zinc-800 rounded-3xl p-6 shadow-sm">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-heading font-extrabold text-sm text-zinc-900 dark:text-white flex items-center gap-2">
                    <i class='bx bx-shopping-bag text-lg text-blue-500'></i> Riwayat Belanja Toko POS
                </h3>
                <a href="{{ route('member.transactions') }}" class="text-xs font-bold text-[#155A6B] dark:text-emerald-400 hover:underline">Lihat Semua</a>
            </div>

            <div class="space-y-3">
                @forelse($recentTransactions as $trx)
                    <div class="p-3.5 rounded-2xl bg-zinc-50 dark:bg-zinc-800/40 border border-zinc-200/50 dark:border-zinc-700/40 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center text-lg shrink-0">
                                <i class='bx bx-cart-alt'></i>
                            </div>
                            <div class="min-w-0">
                                <h4 class="font-extrabold text-xs text-zinc-900 dark:text-white truncate">Transaksi Belanja Toko</h4>
                                <p class="text-[10px] text-zinc-400">{{ $trx->created_at->format('d M Y • H:i') }}</p>
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="font-mono font-extrabold text-xs text-zinc-900 dark:text-white">
                                -Rp {{ number_format($trx->totalAmount, 0, ',', '.') }}
                            </span>
                            <span class="block text-[9px] font-bold text-emerald-500 uppercase">{{ ucfirst($trx->status) }}</span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 border-2 border-dashed border-zinc-200 dark:border-zinc-800 rounded-2xl">
                        <i class='bx bx-shopping-bag text-3xl text-zinc-300 dark:text-zinc-600 mb-1'></i>
                        <p class="text-xs text-zinc-400">Belum ada riwayat transaksi belanja.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>
</div>

