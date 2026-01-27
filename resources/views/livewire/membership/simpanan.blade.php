<div>
    {{-- Header with Balance --}}
    <div class="relative mb-6">
        <div
            class="bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl p-5 text-white shadow-lg shadow-emerald-500/30 border border-white/10">
            <div class="flex justify-between items-start mb-3">
                <div>
                    <p class="text-xs text-white/70 mb-1">Saldo Bermadani</p>
                    <h1 class="text-2xl font-bold">
                        @if($showBalance)
                            Rp {{ number_format($member->simpananSukarela ?? 0, 0, ',', '.') }}
                        @else
                            Rp ••••••••
                        @endif
                    </h1>
                </div>
                <button wire:click="toggleBalance"
                    class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center hover:bg-white/30 transition">
                    <i class='bx {{ $showBalance ? "bx-hide" : "bx-show" }}'></i>
                </button>
            </div>
            <p class="text-[11px] text-white/70">{{ $member->name ?? 'Member' }} • {{ $member->nomorAnggota ?? '-' }}
            </p>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="flex gap-2 mb-6 overflow-x-auto hide-scrollbar">
        <button wire:click="setTab('all')"
            class="px-4 py-2 rounded-full text-sm font-medium whitespace-nowrap transition-colors
                {{ $activeTab === 'all' ? 'bg-primary text-white' : 'bg-white dark:bg-darkCard text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700' }}">
            Semua
        </button>
        <button wire:click="setTab('in')"
            class="px-4 py-2 rounded-full text-sm font-medium whitespace-nowrap transition-colors
                {{ $activeTab === 'in' ? 'bg-emerald-500 text-white' : 'bg-white dark:bg-darkCard text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700' }}">
            <i class='bx bx-down-arrow-alt'></i> Masuk
        </button>
        <button wire:click="setTab('out')"
            class="px-4 py-2 rounded-full text-sm font-medium whitespace-nowrap transition-colors
                {{ $activeTab === 'out' ? 'bg-rose-500 text-white' : 'bg-white dark:bg-darkCard text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700' }}">
            <i class='bx bx-up-arrow-alt'></i> Keluar
        </button>
    </div>

    {{-- Transaction List --}}
    <div class="space-y-3">
        @forelse($simpanan as $item)
            <div
                class="bg-white dark:bg-darkCard rounded-xl p-4 border border-slate-100 dark:border-slate-700 flex items-center gap-3">
                <div class="w-10 h-10 rounded-full flex-shrink-0 flex items-center justify-center
                        @if(in_array($item->transactionType, ['SETOR', 'TRANSFER_IN', 'CASHBACK'])) bg-emerald-50 dark:bg-emerald-500/10 text-emerald-500
                        @else bg-rose-50 dark:bg-rose-500/10 text-rose-500
                        @endif">
                    <i
                        class='bx {{ in_array($item->transactionType, ['SETOR', 'TRANSFER_IN', 'CASHBACK']) ? 'bx-down-arrow-alt' : 'bx-up-arrow-alt' }} text-xl'></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-slate-800 dark:text-white truncate">
                        @if($item->transactionType === 'TRANSFER_IN')
                            Transfer dari {{ $item->relatedMember->name ?? 'Member' }}
                        @elseif($item->transactionType === 'TRANSFER_OUT')
                            Transfer ke {{ $item->relatedMember->name ?? 'Member' }}
                        @elseif($item->transactionType === 'SETOR')
                            Top Up Saldo
                        @elseif($item->transactionType === 'TARIK')
                            Penarikan
                        @elseif($item->transactionType === 'CASHBACK')
                            Cashback Belanja
                        @else
                            {{ $item->transactionType }}
                        @endif
                    </p>
                    <p class="text-[11px] text-slate-400">{{ $item->created_at->format('d M Y • H:i') }}</p>
                </div>
                <div class="text-right">
                    <p
                        class="text-sm font-bold {{ in_array($item->transactionType, ['SETOR', 'TRANSFER_IN', 'CASHBACK']) ? 'text-emerald-500' : 'text-rose-500' }}">
                        {{ in_array($item->transactionType, ['SETOR', 'TRANSFER_IN', 'CASHBACK']) ? '+' : '-' }}Rp
                        {{ number_format($item->amount, 0, ',', '.') }}
                    </p>
                    <p class="text-[10px] text-slate-400">Saldo: Rp
                        {{ number_format($item->balanceAfter ?? 0, 0, ',', '.') }}</p>
                </div>
            </div>
        @empty
            <div
                class="bg-white dark:bg-darkCard rounded-2xl p-8 border border-slate-100 dark:border-slate-700 text-center">
                <i class='bx bx-wallet text-5xl text-slate-300 dark:text-slate-600 mb-3'></i>
                <p class="text-slate-500 dark:text-slate-400">Belum ada mutasi saldo</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($simpanan->hasPages())
        <div class="mt-6">
            {{ $simpanan->links() }}
        </div>
    @endif
</div>