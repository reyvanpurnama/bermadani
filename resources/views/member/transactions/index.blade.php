@extends('layouts.member')

@section('title', 'Riwayat Belanja')

@section('content')
    <div class="px-4 pb-8">
        <!-- Header Stats -->
        <div class="grid grid-cols-2 gap-4 mb-6">
            <div class="bg-white dark:bg-darkCard p-4 rounded-2xl shadow-sm border border-slate-100 dark:border-white/5">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-xl bg-indigo-100 dark:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                        <i class='bx bx-receipt text-xl'></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-slate-800 dark:text-white">{{ $totalTransactions }}</p>
                        <p class="text-[10px] text-slate-500 uppercase tracking-wider font-bold">Transaksi</p>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-darkCard p-4 rounded-2xl shadow-sm border border-slate-100 dark:border-white/5">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                        <i class='bx bx-wallet text-xl'></i>
                    </div>
                    <div>
                        <p class="text-lg font-bold text-slate-800 dark:text-white">Rp
                            {{ number_format($totalSpent, 0, ',', '.') }}</p>
                        <p class="text-[10px] text-slate-500 uppercase tracking-wider font-bold">Total Belanja</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transaction List -->
        <div class="space-y-3">
            @forelse($transactions as $trx)
                <a href="{{ route('member.transactions.show', $trx->id) }}"
                    class="block bg-white dark:bg-darkCard p-4 rounded-2xl shadow-sm border border-slate-100 dark:border-white/5 hover:border-indigo-200 dark:hover:border-indigo-500/30 transition-all group">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-white/5 flex items-center justify-center text-slate-500 dark:text-slate-400 group-hover:bg-indigo-100 dark:group-hover:bg-indigo-500/20 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                <i class='bx bx-shopping-bag text-xl'></i>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-800 dark:text-white">{{ $trx->invoiceNumber }}</p>
                                <p class="text-[11px] text-slate-500">{{ $trx->date->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-bold text-slate-800 dark:text-white">Rp
                                {{ number_format($trx->totalAmount, 0, ',', '.') }}</p>
                            <span
                                class="inline-block px-2 py-0.5 text-[10px] font-bold rounded-full 
                                        {{ $trx->status === 'COMPLETED' ? 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400' : 'bg-slate-100 text-slate-600' }}">
                                {{ $trx->status }}
                            </span>
                        </div>
                    </div>
                </a>
            @empty
                <div class="bg-white dark:bg-darkCard p-12 rounded-2xl border border-slate-100 dark:border-white/5 text-center">
                    <div
                        class="w-16 h-16 rounded-full bg-slate-100 dark:bg-white/5 mx-auto flex items-center justify-center text-slate-400 text-3xl mb-4">
                        <i class='bx bx-shopping-bag'></i>
                    </div>
                    <h3 class="text-sm font-bold text-slate-800 dark:text-white mb-1">Belum Ada Transaksi</h3>
                    <p class="text-xs text-slate-500">Riwayat belanja Anda akan muncul di sini.</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($transactions->hasPages())
            <div class="mt-6">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>
@endsection