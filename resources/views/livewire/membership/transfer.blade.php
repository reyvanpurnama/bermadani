@push('styles')
    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }

        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
@endpush

<div class="max-w-xl mx-auto pb-24 lg:pb-0" x-data="{ showBalance: {{ $showBalance ? 'true' : 'false' }} }">
    {{-- Header Title (Mobile App style) --}}
    <div class="mb-6 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('membership.dashboard') }}"
                class="p-2 -ml-2 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-full transition-colors">
                <i class='bx bx-arrow-back text-2xl'></i>
            </a>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Kirim Uang</h1>
        </div>
        <div class="text-sm font-medium text-slate-500 dark:text-slate-400">
            @if($step === 1) Langkah 1/3
            @elseif($step === 2) Langkah 2/3
            @else Selesai
            @endif
        </div>
    </div>

    {{-- Progress --}}
    <div class="h-1 bg-slate-200 dark:bg-slate-700 rounded-full mb-8 overflow-hidden">
        <div class="h-full bg-primary transition-all duration-500 ease-out" style="width: {{ $step * 33.33 }}%"></div>
    </div>

    {{-- Step 1: Input --}}
    @if($step === 1)
        {{-- Balance Card --}}
        <div
            class="bg-gradient-to-br from-slate-900 to-slate-800 rounded-2xl p-6 text-white shadow-lg shadow-slate-900/20 mb-8 relative overflow-hidden transition-all hover:scale-[1.02] group">
            {{-- Decorative --}}
            <div
                class="absolute top-0 right-0 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl -mr-16 -mt-16 pointer-events-none group-hover:bg-emerald-500/20 transition-all duration-500">
            </div>

            <div class="relative z-10">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-6 h-6 rounded bg-emerald-500/20 flex items-center justify-center">
                        <i class='bx bxs-bank text-emerald-400 text-xs'></i>
                    </div>
                    <span
                        class="text-slate-300 text-sm font-medium">{{ $member->isMemberKoperasi ? 'Saldo Sukarela' : 'Saldo Bermadani' }}</span>
                </div>
                <div class="text-3xl font-bold tracking-tight mb-4">
                    Rp {{ number_format($member->simpananSukarela ?? 0, 0, ',', '.') }}
                </div>
                <div
                    class="flex items-center gap-2 text-xs text-slate-300 bg-white/5 w-fit px-3 py-1.5 rounded-full border border-white/5">
                    <i class='bx bx-info-circle'></i>
                    <span>Limit Harian: Rp {{ number_format(self::MAX_PER_DAY - $todayTransferred, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <div
            class="bg-white dark:bg-darkCard rounded-2xl shadow-sm border border-slate-100 dark:border-slate-800 overflow-hidden">

            {{-- Quick Transfer (Recent) --}}
            @if(count($recentRecipients) > 0 && !$recipientMember)
                <div class="p-6 pb-0 border-b border-slate-50 dark:border-slate-800/50">
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-4">
                        Terakhir Transfer
                    </label>
                    <div class="flex gap-4 overflow-x-auto pb-2 scrollbar-hide">
                        @foreach($recentRecipients as $recent)
                            <button wire:click="selectRecipient({{ $recent->id }})"
                                class="flex flex-col items-center gap-2 min-w-[72px] group relative">
                                <div
                                    class="w-14 h-14 rounded-full bg-slate-100 dark:bg-slate-700 border-2 border-transparent group-hover:border-primary flex items-center justify-center text-xl font-bold text-slate-600 dark:text-slate-300 group-hover:text-primary transition-all shadow-sm">
                                    {{ strtoupper(substr($recent->name, 0, 1)) }}
                                </div>
                                <span
                                    class="text-xs text-center text-slate-600 dark:text-slate-400 font-medium truncate w-full group-hover:text-primary transition-colors">
                                    {{ Str::limit(explode(' ', $recent->name)[0], 8) }}
                                </span>
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Search Recipient --}}
            <div class="p-6 border-b border-slate-100 dark:border-slate-800">
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-3">
                    Kirim ke
                </label>

                @if($recipientMember)
                    <div
                        class="flex items-center gap-4 bg-slate-50 dark:bg-slate-800 p-4 rounded-xl border border-slate-100 dark:border-slate-700 animate-[fadeIn_0.3s_ease-out]">
                        <div
                            class="w-12 h-12 rounded-full bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center text-primary font-bold text-lg">
                            {{ strtoupper(substr($recipientMember->name, 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="font-bold text-slate-900 dark:text-white truncate">{{ $recipientMember->name }}</h4>
                            <p class="text-xs text-slate-500 truncate">{{ $recipientMember->nomorAnggota }} •
                                {{ $recipientMember->unitKerja }}
                            </p>
                        </div>
                        <button wire:click="clearRecipient" class="p-2 text-slate-400 hover:text-rose-500 transition-colors">
                            <i class='bx bx-x text-xl'></i>
                        </button>
                    </div>
                @else
                    <div class="relative">
                        <i class='bx bx-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xl'></i>
                        <input type="text" wire:model="recipientNumber" wire:keydown.enter="searchRecipient"
                            class="w-full pl-12 pr-12 py-4 bg-slate-50 dark:bg-slate-800 border-none rounded-xl focus:ring-2 focus:ring-primary text-slate-900 dark:text-white font-medium placeholder:text-slate-400 transition-all"
                            placeholder="Cari nomor anggota">
                        <button wire:click="searchRecipient"
                            class="absolute right-3 top-1/2 -translate-y-1/2 p-2 bg-white dark:bg-slate-700 rounded-lg shadow-sm text-primary hover:text-blue-700 hover:shadow-md transition-all">
                            <i class='bx bx-right-arrow-alt'></i>
                        </button>
                    </div>
                @endif
                @error('recipientNumber') <p class="text-xs text-rose-500 mt-2 font-medium flex items-center gap-1"><i
                class='bx bx-error-circle'></i> {{ $message }}</p> @enderror
            </div>

            {{-- Amount --}}
            <div class="p-6">
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-3">
                    Nominal Transfer
                </label>

                <div class="relative mb-6">
                    <span class="absolute left-0 top-1/2 -translate-y-1/2 text-slate-400 text-2xl font-bold">Rp</span>
                    <input type="text" wire:model="amount" inputmode="numeric"
                        class="w-full pl-10 pr-4 py-2 border-none bg-transparent text-4xl font-bold text-slate-900 dark:text-white placeholder:text-slate-300 focus:ring-0 p-0"
                        placeholder="0" x-data
                        x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.')">
                </div>

                {{-- Quick Amount --}}
                <div class="flex gap-2 overflow-x-auto pb-2 scrollbar-hide mb-6">
                    @foreach([50000, 100000, 200000, 500000, 1000000] as $quickAmount)
                        <button type="button" wire:click="$set('amount', '{{ number_format($quickAmount, 0, '', '.') }}')"
                            class="flex-shrink-0 px-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-full text-sm font-medium text-slate-600 dark:text-slate-300 hover:border-primary hover:text-primary hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all">
                            {{ number_format($quickAmount / 1000, 0) }}rb
                        </button>
                    @endforeach
                </div>

                {{-- Notes --}}
                <div class="relative group">
                    <i
                        class='bx bx-edit absolute left-4 top-3.5 text-slate-400 group-focus-within:text-primary transition-colors'></i>
                    <textarea wire:model="notes" rows="1"
                        class="w-full pl-10 pr-4 py-3 bg-slate-50 dark:bg-slate-800 border-none rounded-xl focus:ring-2 focus:ring-primary text-slate-900 dark:text-white placeholder:text-slate-400 resize-none transition-all"
                        placeholder="Tulis catatan (opsional)"></textarea>
                </div>

                @error('amount') <p class="text-xs text-rose-500 mt-2 font-medium flex items-center gap-1"><i
                class='bx bx-error-circle'></i> {{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Action Button --}}
        <div class="mt-6">
            <button wire:click="proceedToConfirm" wire:loading.attr="disabled"
                class="w-full py-4 bg-primary text-white font-bold text-lg rounded-xl hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/30 flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                <span wire:loading.remove wire:target="proceedToConfirm">Lanjutkan</span>
                <span wire:loading wire:target="proceedToConfirm"><i class='bx bx-loader-alt animate-spin'></i>
                    Memproses...</span>
                <i class='bx bx-right-arrow-alt' wire:loading.remove wire:target="proceedToConfirm"></i>
            </button>
        </div>
    @endif

    {{-- Step 2: Confirmation --}}
    @if($step === 2)
        <div
            class="bg-white dark:bg-darkCard rounded-2xl shadow-sm border border-slate-100 dark:border-slate-800 overflow-hidden animate-[fadeIn_0.3s_ease-out]">
            <div class="p-6 bg-slate-50 dark:bg-slate-800/50 border-b border-slate-100 dark:border-slate-800 text-center">
                <p class="text-slate-500 dark:text-slate-400 text-sm mb-1">Total Nominal</p>
                <h2 class="text-3xl font-bold text-slate-900 dark:text-white">Rp
                    {{ number_format($this->parseAmount($amount), 0, ',', '.') }}
                </h2>
            </div>

            <div class="p-6 space-y-6">
                {{-- Recipient Detail --}}
                <div>
                    <label
                        class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-3">Penerima</label>
                    <div class="flex items-center gap-4">
                        <div
                            class="w-12 h-12 rounded-full bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center text-primary font-bold text-lg">
                            {{ strtoupper(substr($recipientMember->name, 0, 1)) }}
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-900 dark:text-white">{{ $recipientMember->name }}</h4>
                            <p class="text-sm text-slate-500">{{ $recipientMember->nomorAnggota }}</p>
                        </div>
                    </div>
                </div>

                {{-- Details --}}
                <div class="space-y-4 pt-4 border-t border-slate-100 dark:border-slate-800">
                    <div class="flex justify-between items-center">
                        <span class="text-slate-500 dark:text-slate-400">Biaya Admin</span>
                        <span class="font-bold text-emerald-500">GRATIS</span>
                    </div>
                    @if($notes)
                        <div class="flex justify-between items-start">
                            <span class="text-slate-500 dark:text-slate-400">Catatan</span>
                            <span class="text-slate-900 dark:text-white text-right max-w-[60%]">{{ $notes }}</span>
                        </div>
                    @endif
                </div>

                {{-- Password Input --}}
                <div class="pt-4">
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">
                        PIN / Password Anda
                    </label>
                    <div class="relative">
                        <input type="password" wire:model="password"
                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border-none rounded-xl focus:ring-2 focus:ring-primary text-slate-900 dark:text-white transition-all text-center tracking-widest text-lg"
                            placeholder="••••••">
                    </div>
                    @error('password') <p class="text-xs text-rose-500 mt-2 font-medium flex items-center gap-1"><i
                    class='bx bx-error-circle'></i> {{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="mt-6 flex gap-4">
            <button wire:click="backToForm"
                class="flex-1 py-4 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold rounded-xl hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">
                Batal
            </button>
            <button wire:click="executeTransfer" wire:loading.attr="disabled"
                class="flex-[2] py-4 bg-primary text-white font-bold rounded-xl hover:bg-blue-700 transition-colors disabled:opacity-50 flex items-center justify-center gap-2 shadow-lg shadow-blue-500/30">
                <span wire:loading.remove wire:target="executeTransfer">Konfirmasi & Kirim</span>
                <span wire:loading wire:target="executeTransfer"><i class='bx bx-loader-alt animate-spin'></i>
                    Memproses...</span>
            </button>
        </div>
    @endif

    {{-- Step 3: Success --}}
    @if($step === 3 && $transferResult)
        <div class="text-center pt-8 animate-[fadeIn_0.5s_ease-out]">
            <div
                class="w-24 h-24 mx-auto mb-6 rounded-full bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center relative">
                <div class="absolute inset-0 rounded-full bg-emerald-500/20 animate-ping"></div>
                <i class='bx bx-check text-6xl text-emerald-500'></i>
            </div>

            <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-2">Transfer Berhasil!</h3>
            <p class="text-slate-500 dark:text-slate-400 mb-8">Dana telah berhasil dikirim</p>

            {{-- Receipt Card --}}
            <div
                class="bg-white dark:bg-darkCard rounded-2xl shadow-lg border border-slate-100 dark:border-slate-800 overflow-hidden relative mb-8">
                {{-- Decorative jagged edge (CSS trick or just simple line) --}}
                <div class="p-6 space-y-4">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-slate-500">Ref ID</span>
                        <span
                            class="font-mono font-bold text-slate-900 dark:text-white">{{ $transferResult['reference'] }}</span>
                    </div>
                    <div class="border-b border-dashed border-slate-200 dark:border-slate-700 my-4"></div>

                    <div class="flex justify-between items-start">
                        <span class="text-slate-500">Penerima</span>
                        <div class="text-right">
                            <div class="font-bold text-slate-900 dark:text-white">{{ $transferResult['recipient']['name'] }}
                            </div>
                            <div class="text-xs text-slate-500">{{ $transferResult['recipient']['nomorAnggota'] }}</div>
                        </div>
                    </div>

                    <div class="flex justify-between items-center">
                        <span class="text-slate-500">Waktu</span>
                        <span class="text-slate-900 dark:text-white">{{ $transferResult['timestamp'] }}</span>
                    </div>

                    <div class="bg-emerald-50 dark:bg-emerald-900/20 rounded-xl p-4 mt-4 flex justify-between items-center">
                        <span class="text-emerald-700 dark:text-emerald-300 font-medium">Total Terkirim</span>
                        <span class="text-xl font-bold text-emerald-600 dark:text-emerald-400">Rp
                            {{ number_format($transferResult['amount'], 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <div class="flex gap-4">
                <a href="{{ route('membership.dashboard') }}"
                    class="flex-1 py-4 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold rounded-xl hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                    Ke Beranda
                </a>
                <button wire:click="newTransfer"
                    class="flex-1 py-4 bg-primary text-white font-bold rounded-xl hover:bg-blue-700 transition-colors shadow-lg shadow-blue-500/30">
                    Transfer Lagi
                </button>
            </div>
        </div>
    @endif
</div>