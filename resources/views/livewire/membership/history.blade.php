<div>
    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-xl font-bold text-slate-800 dark:text-white">Riwayat Belanja</h1>
        <p class="text-sm text-slate-500">Lihat semua transaksi belanja kamu</p>
    </div>

    {{-- Filter & Stats --}}
    <div class="bg-white dark:bg-darkCard rounded-2xl p-4 border border-slate-100 dark:border-slate-700 shadow-sm mb-6">
        <div class="flex flex-col sm:flex-row gap-4 sm:items-center sm:justify-between">
            <div>
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 block">Periode</label>
                <input type="month" wire:model.live="filterMonth"
                    class="bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-lg px-3 py-2 text-sm text-slate-700 dark:text-white focus:ring-2 focus:ring-primary outline-none">
            </div>
            <div class="flex gap-4">
                <div class="text-center">
                    <p class="text-[10px] text-slate-400 uppercase tracking-wider">Total Belanja</p>
                    <p class="text-lg font-bold text-slate-800 dark:text-white">Rp
                        {{ number_format($stats['totalSpent'] ?? 0, 0, ',', '.') }}</p>
                </div>
                <div class="text-center">
                    <p class="text-[10px] text-slate-400 uppercase tracking-wider">Transaksi</p>
                    <p class="text-lg font-bold text-primary">{{ $stats['totalTransactions'] ?? 0 }}x</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Transaction List --}}
    <div class="space-y-3">
        @forelse($transactions as $trx)
            <div
                class="bg-white dark:bg-darkCard rounded-xl p-4 border border-slate-100 dark:border-slate-700 flex items-center gap-3 hover:shadow-md transition-shadow">
                <div
                    class="w-11 h-11 rounded-full bg-blue-50 dark:bg-blue-500/10 text-blue-500 flex items-center justify-center flex-shrink-0">
                    <i class='bx bx-shopping-bag text-xl'></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-slate-800 dark:text-white truncate">Transaksi #{{ $trx->id }}</p>
                    <p class="text-[11px] text-slate-400">{{ $trx->created_at->format('d M Y • H:i') }}</p>
                </div>
                <div class="text-right">
                    <p class="text-sm font-bold text-slate-800 dark:text-white">Rp
                        {{ number_format($trx->totalAmount, 0, ',', '.') }}</p>
                    <span class="text-[10px] font-bold px-1.5 py-0.5 rounded
                            @if($trx->status === 'COMPLETED' || $trx->status === 'completed') bg-emerald-50 dark:bg-emerald-500/10 text-emerald-500
                            @elseif($trx->status === 'PENDING') bg-amber-50 dark:bg-amber-500/10 text-amber-500
                            @else bg-slate-100 dark:bg-slate-700 text-slate-500
                            @endif">
                        {{ strtoupper($trx->status) }}
                    </span>
                </div>
            </div>
        @empty
            <div
                class="bg-white dark:bg-darkCard rounded-2xl p-8 border border-slate-100 dark:border-slate-700 text-center">
                <i class='bx bx-receipt text-5xl text-slate-300 dark:text-slate-600 mb-3'></i>
                <p class="text-slate-500 dark:text-slate-400">Belum ada riwayat transaksi</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($transactions->hasPages())
        <div class="mt-6">
            {{ $transactions->links() }}
        </div>
    @endif
</div>