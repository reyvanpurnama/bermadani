@extends('layouts.member')

@section('title', 'Detail Transaksi')

@section('content')
    <div class="px-4 pb-8">
        <!-- Back Button -->
        <a href="{{ route('member.transactions') }}"
            class="inline-flex items-center gap-2 text-sm text-slate-500 hover:text-primary mb-4 font-medium">
            <i class='bx bx-arrow-back'></i> Kembali
        </a>

        <!-- Receipt Card -->
        <div
            class="bg-white dark:bg-darkCard rounded-2xl shadow-sm border border-slate-100 dark:border-white/5 overflow-hidden">

            <!-- Header -->
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-6 text-white text-center relative overflow-hidden">
                <div class="absolute inset-0 opacity-10"
                    style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 16px 16px;">
                </div>
                <div class="relative z-10">
                    <div class="w-12 h-12 rounded-full bg-white/20 mx-auto flex items-center justify-center text-2xl mb-3">
                        <i class='bx bx-check-circle'></i>
                    </div>
                    <h2 class="text-lg font-bold">Transaksi Berhasil</h2>
                    <p class="text-sm opacity-80 mt-1">{{ $transaction->date->format('d M Y, H:i') }}</p>
                </div>
            </div>

            <!-- Invoice Info -->
            <div class="p-4 border-b border-slate-100 dark:border-white/5">
                <div class="flex justify-between items-center">
                    <span class="text-xs text-slate-500 uppercase tracking-wider font-bold">Invoice</span>
                    <span
                        class="text-sm font-bold text-slate-800 dark:text-white font-mono">{{ $transaction->invoiceNumber }}</span>
                </div>
            </div>

            <!-- Items List -->
            <div class="p-4">
                <h5 class="text-xs text-slate-500 uppercase tracking-wider font-bold mb-3">Detail Pembelian</h5>
                <div class="space-y-3">
                    @foreach($transaction->items as $item)
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-slate-800 dark:text-white">
                                    {{ $item->productName ?? 'Produk' }}</p>
                                <p class="text-xs text-slate-500">{{ $item->quantity }} x Rp
                                    {{ number_format($item->price ?? 0, 0, ',', '.') }}</p>
                            </div>
                            <p class="text-sm font-bold text-slate-800 dark:text-white">Rp
                                {{ number_format($item->totalPrice ?? 0, 0, ',', '.') }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Totals -->
            <div class="p-4 border-t border-dashed border-slate-200 dark:border-white/10">
                <div class="flex justify-between items-center">
                    <span class="text-sm font-bold text-slate-800 dark:text-white">Total Bayar</span>
                    <span class="text-xl font-bold text-indigo-600 dark:text-indigo-400">Rp
                        {{ number_format($transaction->totalAmount, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center mt-2 text-xs text-slate-500">
                    <span>Metode Pembayaran</span>
                    <span class="font-medium">{{ $transaction->paymentMethod }}</span>
                </div>
            </div>

            <!-- Points Earned -->
            <div class="p-4 bg-yellow-50 dark:bg-yellow-500/10 border-t border-yellow-100 dark:border-yellow-500/20">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2 text-yellow-700 dark:text-yellow-400">
                        <i class='bx bxs-star text-xl'></i>
                        <span class="text-sm font-bold">Poin Didapat</span>
                    </div>
                    <span
                        class="text-lg font-bold text-yellow-700 dark:text-yellow-400">+{{ floor($transaction->totalAmount / 10000) }}</span>
                </div>
            </div>
        </div>
    </div>
@endsection