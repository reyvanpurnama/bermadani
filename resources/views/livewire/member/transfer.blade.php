<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Transfer Simpanan</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Transfer saldo simpanan sukarela ke anggota lain</p>
        </div>
    </div>

    {{-- Balance Card with Hide/Unhide --}}
    <div class="bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl p-6 text-white shadow-lg">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2">
                <i class='bx bx-wallet text-2xl'></i>
                <span class="font-medium">Simpanan Sukarela</span>
            </div>
            <button wire:click="toggleBalance" class="p-2 hover:bg-white/20 rounded-lg transition-colors">
                <i class='bx {{ $showBalance ? "bx-hide" : "bx-show" }} text-xl'></i>
            </button>
        </div>
        <div class="text-3xl font-bold mb-2">
            @if($showBalance)
                Rp {{ number_format($member->simpananSukarela ?? 0, 0, ',', '.') }}
            @else
                Rp ••••••••
            @endif
        </div>
        <div class="flex items-center gap-4 text-sm text-white/80">
            <span>Limit Harian: Rp {{ number_format(self::MAX_PER_DAY - $todayTransferred, 0, ',', '.') }}</span>
            <span class="text-white/50">|</span>
            <span>Biaya: GRATIS</span>
        </div>
    </div>

    {{-- Step Indicator --}}
    <div class="flex items-center justify-center gap-4">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold {{ $step >= 1 ? 'bg-primary text-white' : 'bg-slate-200 dark:bg-slate-700 text-slate-500' }}">1</div>
            <span class="text-sm {{ $step >= 1 ? 'text-primary font-medium' : 'text-slate-400' }}">Input</span>
        </div>
        <div class="w-8 h-0.5 {{ $step >= 2 ? 'bg-primary' : 'bg-slate-200 dark:bg-slate-700' }}"></div>
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold {{ $step >= 2 ? 'bg-primary text-white' : 'bg-slate-200 dark:bg-slate-700 text-slate-500' }}">2</div>
            <span class="text-sm {{ $step >= 2 ? 'text-primary font-medium' : 'text-slate-400' }}">Konfirmasi</span>
        </div>
        <div class="w-8 h-0.5 {{ $step >= 3 ? 'bg-primary' : 'bg-slate-200 dark:bg-slate-700' }}"></div>
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold {{ $step >= 3 ? 'bg-emerald-500 text-white' : 'bg-slate-200 dark:bg-slate-700 text-slate-500' }}">
                @if($step >= 3)
                    <i class='bx bx-check'></i>
                @else
                    3
                @endif
            </div>
            <span class="text-sm {{ $step >= 3 ? 'text-emerald-500 font-medium' : 'text-slate-400' }}">Sukses</span>
        </div>
    </div>

    {{-- Step 1: Transfer Form --}}
    @if($step === 1)
    <div class="bg-white dark:bg-darkCard rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 p-6">
        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-6 flex items-center gap-2">
            <i class='bx bx-transfer text-primary'></i> Form Transfer
        </h3>

        {{-- Recipient Search --}}
        <div class="mb-6">
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                Nomor Anggota Tujuan <span class="text-rose-500">*</span>
            </label>
            
            @if($recipientMember)
                {{-- Selected Recipient --}}
                <div class="flex items-center gap-4 p-4 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-xl">
                    <div class="w-12 h-12 rounded-full bg-emerald-500 flex items-center justify-center text-white font-bold text-lg">
                        {{ strtoupper(substr($recipientMember->name, 0, 1)) }}
                    </div>
                    <div class="flex-1">
                        <p class="font-bold text-slate-900 dark:text-white">{{ $recipientMember->name }}</p>
                        <p class="text-sm text-slate-500 dark:text-slate-400">{{ $recipientMember->nomorAnggota }} • {{ $recipientMember->unitKerja }}</p>
                    </div>
                    <button wire:click="clearRecipient" class="p-2 text-slate-400 hover:text-rose-500 transition-colors">
                        <i class='bx bx-x text-xl'></i>
                    </button>
                </div>
            @else
                {{-- Search Input --}}
                <div class="flex gap-2">
                    <input type="text" wire:model="recipientNumber" wire:keydown.enter="searchRecipient"
                        class="flex-1 px-4 py-3 border border-slate-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary bg-white dark:bg-slate-800 text-slate-800 dark:text-white transition-colors"
                        placeholder="Masukkan nomor anggota (contoh: 21000001)">
                    <button wire:click="searchRecipient" wire:loading.attr="disabled"
                        class="px-6 py-3 bg-primary text-white font-bold rounded-xl hover:bg-blue-700 transition-colors disabled:opacity-50">
                        <span wire:loading.remove wire:target="searchRecipient">Cari</span>
                        <span wire:loading wire:target="searchRecipient"><i class='bx bx-loader-alt animate-spin'></i></span>
                    </button>
                </div>
            @endif
            @error('recipientNumber') <p class="text-xs text-rose-500 mt-2">{{ $message }}</p> @enderror
        </div>

        {{-- Amount --}}
        <div class="mb-6">
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                Nominal Transfer <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 font-medium">Rp</span>
                <input type="text" wire:model="amount" inputmode="numeric"
                    class="w-full pl-12 pr-4 py-3 border border-slate-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary bg-white dark:bg-slate-800 text-slate-800 dark:text-white text-lg font-bold transition-colors"
                    placeholder="0"
                    x-data
                    x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.')">
            </div>
            <div class="flex justify-between mt-2 text-xs text-slate-400">
                <span>Min: Rp {{ number_format(self::MIN_TRANSFER, 0, ',', '.') }}</span>
                <span>Max: Rp {{ number_format(self::MAX_PER_TRANSACTION, 0, ',', '.') }}/trx</span>
            </div>
            @error('amount') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror

            {{-- Quick Amount Buttons --}}
            <div class="flex flex-wrap gap-2 mt-4">
                @foreach([50000, 100000, 200000, 500000, 1000000] as $quickAmount)
                    <button type="button" wire:click="$set('amount', '{{ number_format($quickAmount, 0, '', '.') }}')"
                        class="px-4 py-2 text-sm font-medium bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg hover:bg-primary hover:text-white transition-colors">
                        {{ number_format($quickAmount / 1000, 0) }}rb
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Notes --}}
        <div class="mb-6">
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                Catatan (Opsional)
            </label>
            <textarea wire:model="notes" rows="2"
                class="w-full px-4 py-3 border border-slate-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary bg-white dark:bg-slate-800 text-slate-800 dark:text-white resize-none transition-colors"
                placeholder="Contoh: Bayar iuran, Pinjam uang, dll"></textarea>
        </div>

        {{-- Submit Button --}}
        <button wire:click="proceedToConfirm" wire:loading.attr="disabled"
            class="w-full py-4 bg-primary text-white font-bold rounded-xl hover:bg-blue-700 transition-colors disabled:opacity-50 flex items-center justify-center gap-2">
            <span wire:loading.remove wire:target="proceedToConfirm">Lanjutkan</span>
            <span wire:loading wire:target="proceedToConfirm"><i class='bx bx-loader-alt animate-spin'></i> Memproses...</span>
            <i class='bx bx-right-arrow-alt' wire:loading.remove wire:target="proceedToConfirm"></i>
        </button>
    </div>
    @endif

    {{-- Step 2: Confirmation --}}
    @if($step === 2)
    <div class="bg-white dark:bg-darkCard rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 p-6">
        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-6 flex items-center gap-2">
            <i class='bx bx-check-shield text-primary'></i> Konfirmasi Transfer
        </h3>

        {{-- Transfer Summary --}}
        <div class="bg-slate-50 dark:bg-slate-800/50 rounded-xl p-6 mb-6 space-y-4">
            <div class="flex justify-between items-center pb-4 border-b border-slate-200 dark:border-slate-700">
                <span class="text-slate-500 dark:text-slate-400">Penerima</span>
                <div class="text-right">
                    <p class="font-bold text-slate-900 dark:text-white">{{ $recipientMember->name }}</p>
                    <p class="text-sm text-slate-500">{{ $recipientMember->nomorAnggota }}</p>
                </div>
            </div>
            <div class="flex justify-between items-center pb-4 border-b border-slate-200 dark:border-slate-700">
                <span class="text-slate-500 dark:text-slate-400">Nominal</span>
                <span class="text-xl font-bold text-primary">Rp {{ number_format($this->parseAmount($amount), 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between items-center pb-4 border-b border-slate-200 dark:border-slate-700">
                <span class="text-slate-500 dark:text-slate-400">Biaya Admin</span>
                <span class="font-bold text-emerald-500">GRATIS</span>
            </div>
            @if($notes)
            <div class="flex justify-between items-center pb-4 border-b border-slate-200 dark:border-slate-700">
                <span class="text-slate-500 dark:text-slate-400">Catatan</span>
                <span class="text-slate-900 dark:text-white">{{ $notes }}</span>
            </div>
            @endif
            <div class="flex justify-between items-center pt-2">
                <span class="font-bold text-slate-700 dark:text-slate-300">Total Dipotong</span>
                <span class="text-2xl font-bold text-slate-900 dark:text-white">Rp {{ number_format($this->parseAmount($amount), 0, ',', '.') }}</span>
            </div>
        </div>

        {{-- Password Verification --}}
        <div class="mb-6">
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                Masukkan Password Anda <span class="text-rose-500">*</span>
            </label>
            <input type="password" wire:model="password"
                class="w-full px-4 py-3 border border-slate-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary bg-white dark:bg-slate-800 text-slate-800 dark:text-white transition-colors"
                placeholder="Password akun Anda">
            @error('password') <p class="text-xs text-rose-500 mt-2">{{ $message }}</p> @enderror
        </div>

        {{-- Action Buttons --}}
        <div class="flex gap-4">
            <button wire:click="backToForm"
                class="flex-1 py-4 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold rounded-xl hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">
                <i class='bx bx-arrow-back'></i> Kembali
            </button>
            <button wire:click="executeTransfer" wire:loading.attr="disabled"
                class="flex-1 py-4 bg-emerald-500 text-white font-bold rounded-xl hover:bg-emerald-600 transition-colors disabled:opacity-50 flex items-center justify-center gap-2">
                <span wire:loading.remove wire:target="executeTransfer">Transfer Sekarang</span>
                <span wire:loading wire:target="executeTransfer"><i class='bx bx-loader-alt animate-spin'></i> Memproses...</span>
            </button>
        </div>
    </div>
    @endif

    {{-- Step 3: Success --}}
    @if($step === 3 && $transferResult)
    <div class="bg-white dark:bg-darkCard rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 p-6 text-center">
        {{-- Success Animation --}}
        <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center">
            <i class='bx bx-check text-5xl text-emerald-500 animate-bounce'></i>
        </div>

        <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-2">Transfer Berhasil!</h3>
        <p class="text-slate-500 dark:text-slate-400 mb-6">Dana telah dikirim ke penerima</p>

        {{-- Transfer Receipt --}}
        <div class="bg-slate-50 dark:bg-slate-800/50 rounded-xl p-6 text-left mb-6 space-y-3">
            <div class="flex justify-between">
                <span class="text-slate-500 dark:text-slate-400">Referensi</span>
                <span class="font-mono font-bold text-slate-900 dark:text-white">{{ $transferResult['reference'] }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500 dark:text-slate-400">Penerima</span>
                <span class="font-bold text-slate-900 dark:text-white">{{ $transferResult['recipient']['name'] }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500 dark:text-slate-400">No. Anggota</span>
                <span class="text-slate-900 dark:text-white">{{ $transferResult['recipient']['nomorAnggota'] }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500 dark:text-slate-400">Nominal</span>
                <span class="text-xl font-bold text-emerald-500">Rp {{ number_format($transferResult['amount'], 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500 dark:text-slate-400">Waktu</span>
                <span class="text-slate-900 dark:text-white">{{ $transferResult['timestamp'] }}</span>
            </div>
            <div class="border-t border-slate-200 dark:border-slate-700 pt-3 mt-3">
                <div class="flex justify-between">
                    <span class="text-slate-500 dark:text-slate-400">Saldo Tersisa</span>
                    <span class="font-bold text-slate-900 dark:text-white">Rp {{ number_format($transferResult['senderBalanceAfter'], 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="flex gap-4">
            <a href="{{ route('member.dashboard') }}" class="flex-1 py-4 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold rounded-xl hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors text-center">
                <i class='bx bx-home'></i> Dashboard
            </a>
            <button wire:click="newTransfer"
                class="flex-1 py-4 bg-primary text-white font-bold rounded-xl hover:bg-blue-700 transition-colors">
                <i class='bx bx-transfer'></i> Transfer Lagi
            </button>
        </div>
    </div>
    @endif

    {{-- Transfer Info --}}
    <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl p-4">
        <h4 class="font-bold text-amber-800 dark:text-amber-300 mb-2 flex items-center gap-2">
            <i class='bx bx-info-circle'></i> Informasi Transfer
        </h4>
        <ul class="text-sm text-amber-700 dark:text-amber-400 space-y-1">
            <li>• Transfer hanya untuk simpanan sukarela</li>
            <li>• Minimal transfer: Rp {{ number_format(self::MIN_TRANSFER, 0, ',', '.') }}</li>
            <li>• Maksimal per transaksi: Rp {{ number_format(self::MAX_PER_TRANSACTION, 0, ',', '.') }}</li>
            <li>• Maksimal per hari: Rp {{ number_format(self::MAX_PER_DAY, 0, ',', '.') }}</li>
            <li>• Biaya transfer: <strong>GRATIS</strong></li>
        </ul>
    </div>
</div>
