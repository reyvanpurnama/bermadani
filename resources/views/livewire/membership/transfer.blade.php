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
    </style>
@endpush

<div class="max-w-xl mx-auto">
    {{-- Balance Card --}}
    <div
        class="bg-gradient-to-br from-primary to-purple-600 rounded-2xl p-5 text-white shadow-lg shadow-primary/30 mb-6">
        <div class="flex justify-between items-start mb-2">
            <div>
                <p class="text-xs text-white/70 mb-1">Saldo Tersedia</p>
                <h1 class="text-2xl font-bold">
                    @if($showBalance)
                        Rp {{ number_format($member->simpananSukarela ?? 0, 0, ',', '.') }}
                    @else
                        Rp ••••••••
                    @endif
                </h1>
            </div>
            <button wire:click="toggleBalance"
                class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center">
                <i class='bx {{ $showBalance ? "bx-hide" : "bx-show" }}'></i>
            </button>
        </div>
        <p class="text-[11px] text-white/60">Limit harian: Rp
            {{ number_format(10000000 - $todayTransferred, 0, ',', '.') }}</p>
    </div>

    {{-- Step 1: Input Recipient & Amount --}}
    @if($step === 1)
        <div style="animation: fadeIn 0.3s ease-out">
            {{-- Quick Transfer (Recent Recipients) --}}
            @if(count($recentRecipients) > 0)
                <div class="mb-6">
                    <p class="text-sm font-bold text-slate-700 dark:text-slate-300 mb-3">Transfer Cepat</p>
                    <div class="flex gap-3 overflow-x-auto hide-scrollbar pb-2">
                        @foreach($recentRecipients as $recipient)
                            <button wire:click="selectRecipient({{ $recipient->id }})"
                                class="flex flex-col items-center gap-2 min-w-[70px] group">
                                <div
                                    class="w-12 h-12 rounded-full bg-gradient-to-br from-slate-200 to-slate-300 dark:from-slate-600 dark:to-slate-700 flex items-center justify-center text-slate-600 dark:text-slate-300 font-bold group-hover:ring-2 ring-primary transition">
                                    {{ strtoupper(substr($recipient->name, 0, 1)) }}
                                </div>
                                <span
                                    class="text-[10px] text-slate-500 dark:text-slate-400 truncate w-16 text-center">{{ explode(' ', $recipient->name)[0] }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Recipient Input --}}
            <div
                class="bg-white dark:bg-darkCard rounded-2xl p-5 border border-slate-100 dark:border-slate-700 shadow-sm mb-4">
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 block">Nomor Anggota
                    Tujuan</label>
                <div class="flex gap-2">
                    <input type="text" wire:model="recipientNumber" wire:keydown.enter="searchRecipient"
                        placeholder="Contoh: 2024001234"
                        class="flex-1 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-lg px-4 py-3 text-sm text-slate-700 dark:text-white focus:ring-2 focus:ring-primary outline-none font-mono">
                    <button wire:click="searchRecipient"
                        class="px-4 py-3 bg-primary text-white rounded-lg font-bold text-sm hover:bg-indigo-700 transition">
                        <i class='bx bx-search'></i>
                    </button>
                </div>
                @error('recipientNumber') <p class="text-xs text-rose-500 mt-2">{{ $message }}</p> @enderror

                {{-- Found Recipient --}}
                @if($recipientMember)
                    <div
                        class="mt-4 p-4 bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30 rounded-xl flex items-center gap-3">
                        <div
                            class="w-10 h-10 rounded-full bg-emerald-500 text-white flex items-center justify-center font-bold">
                            {{ strtoupper(substr($recipientMember->name, 0, 1)) }}
                        </div>
                        <div class="flex-1">
                            <p class="font-bold text-slate-800 dark:text-white">{{ $recipientMember->name }}</p>
                            <p class="text-[11px] text-slate-500 font-mono">{{ $recipientMember->nomorAnggota }}</p>
                        </div>
                        <button wire:click="clearRecipient" class="text-slate-400 hover:text-slate-600">
                            <i class='bx bx-x text-xl'></i>
                        </button>
                    </div>
                @endif
            </div>

            {{-- Amount Input --}}
            <div
                class="bg-white dark:bg-darkCard rounded-2xl p-5 border border-slate-100 dark:border-slate-700 shadow-sm mb-4">
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 block">Jumlah
                    Transfer</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 font-bold">Rp</span>
                    <input type="text" wire:model="amount" placeholder="0" inputmode="numeric"
                        class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-lg pl-12 pr-4 py-4 text-xl font-bold text-slate-700 dark:text-white focus:ring-2 focus:ring-primary outline-none text-right"
                        x-data x-on:input="$el.value = $el.value.replace(/\D/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.')">
                </div>
                @error('amount') <p class="text-xs text-rose-500 mt-2">{{ $message }}</p> @enderror

                {{-- Quick Amount Buttons --}}
                <div class="grid grid-cols-4 gap-2 mt-4">
                    @foreach([50000, 100000, 200000, 500000] as $quickAmount)
                        <button wire:click="$set('amount', '{{ number_format($quickAmount, 0, ',', '.') }}')"
                            class="py-2 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-lg text-xs font-bold hover:bg-slate-200 dark:hover:bg-slate-600 transition">
                            {{ number_format($quickAmount / 1000) }}rb
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Notes --}}
            <div
                class="bg-white dark:bg-darkCard rounded-2xl p-5 border border-slate-100 dark:border-slate-700 shadow-sm mb-6">
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 block">Catatan
                    (Opsional)</label>
                <input type="text" wire:model="notes" placeholder="Contoh: Bayar makan siang" maxlength="100"
                    class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-lg px-4 py-3 text-sm text-slate-700 dark:text-white focus:ring-2 focus:ring-primary outline-none">
            </div>

            {{-- Continue Button --}}
            <button wire:click="proceedToConfirm"
                class="w-full bg-primary text-white font-bold py-4 rounded-xl hover:bg-indigo-700 transition-colors shadow-lg shadow-primary/30">
                Lanjutkan
            </button>
        </div>
    @endif

    {{-- Step 2: Confirmation --}}
    @if($step === 2)
        <div style="animation: fadeIn 0.3s ease-out">
            <div
                class="bg-white dark:bg-darkCard rounded-2xl p-6 border border-slate-100 dark:border-slate-700 shadow-sm mb-6">
                <h3 class="font-bold text-slate-800 dark:text-white text-center mb-6">Konfirmasi Transfer</h3>

                {{-- Summary --}}
                <div class="space-y-4 mb-6">
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">Penerima</span>
                        <span class="font-bold text-slate-800 dark:text-white">{{ $recipientMember->name }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">No. Anggota</span>
                        <span
                            class="font-mono text-slate-600 dark:text-slate-300">{{ $recipientMember->nomorAnggota }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">Jumlah</span>
                        <span class="font-bold text-2xl text-primary">Rp {{ $amount }}</span>
                    </div>
                    @if($notes)
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-500">Catatan</span>
                            <span class="text-slate-600 dark:text-slate-300">{{ $notes }}</span>
                        </div>
                    @endif
                </div>

                {{-- Password Input --}}
                <div class="mb-6">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 block">Password
                        Akun</label>
                    <input type="password" wire:model="password" placeholder="Masukkan password"
                        class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-lg px-4 py-3 text-sm text-center text-slate-700 dark:text-white focus:ring-2 focus:ring-primary outline-none">
                    @error('password') <p class="text-xs text-rose-500 mt-2 text-center">{{ $message }}</p> @enderror
                </div>

                {{-- Buttons --}}
                <div class="flex gap-3">
                    <button wire:click="backToForm"
                        class="flex-1 py-3 border border-slate-200 dark:border-slate-600 text-slate-600 dark:text-slate-300 font-bold rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700 transition">
                        Kembali
                    </button>
                    <button wire:click="executeTransfer"
                        class="flex-1 py-3 bg-primary text-white font-bold rounded-xl hover:bg-indigo-700 transition shadow-lg shadow-primary/30">
                        <span wire:loading.remove wire:target="executeTransfer">Transfer Sekarang</span>
                        <span wire:loading wire:target="executeTransfer"><i class='bx bx-loader-alt animate-spin'></i>
                            Memproses...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Step 3: Success --}}
    @if($step === 3 && $transferResult)
        <div style="animation: fadeIn 0.3s ease-out">
            <div
                class="bg-white dark:bg-darkCard rounded-2xl p-8 border border-slate-100 dark:border-slate-700 shadow-sm text-center">
                {{-- Success Icon --}}
                <div
                    class="w-20 h-20 mx-auto mb-6 rounded-full bg-emerald-100 dark:bg-emerald-500/20 text-emerald-500 flex items-center justify-center">
                    <i class='bx bx-check text-5xl'></i>
                </div>

                <h3 class="text-xl font-bold text-slate-800 dark:text-white mb-2">Transfer Berhasil!</h3>
                <p class="text-sm text-slate-500 mb-6">{{ $transferResult['timestamp'] }}</p>

                <div class="bg-slate-50 dark:bg-slate-800 rounded-xl p-4 mb-6 text-left space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">No. Referensi</span>
                        <span
                            class="font-mono text-xs text-slate-600 dark:text-slate-300">{{ $transferResult['reference'] }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">Penerima</span>
                        <span
                            class="font-bold text-slate-800 dark:text-white">{{ $transferResult['recipient']['name'] }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">Jumlah</span>
                        <span class="font-bold text-emerald-500">Rp
                            {{ number_format($transferResult['amount'], 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-sm border-t border-slate-200 dark:border-slate-700 pt-3">
                        <span class="text-slate-500">Sisa Saldo</span>
                        <span class="font-bold text-slate-800 dark:text-white">Rp
                            {{ number_format($transferResult['senderBalanceAfter'], 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="flex gap-3">
                    <a href="{{ route('membership.dashboard') }}"
                        class="flex-1 py-3 border border-slate-200 dark:border-slate-600 text-slate-600 dark:text-slate-300 font-bold rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700 transition text-center">
                        Beranda
                    </a>
                    <button wire:click="newTransfer"
                        class="flex-1 py-3 bg-primary text-white font-bold rounded-xl hover:bg-indigo-700 transition">
                        Transfer Lagi
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>