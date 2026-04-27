@extends('layouts.admin')

@section('title', 'Detail Transaksi')

@section('content')
@php
    $transaction = \App\Models\Transaction::with(['member', 'items.product'])->findOrFail($transactionId);
@endphp

<div>
    <!-- Header with Back Button and Status -->
    <div class="mb-6 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.transactions') }}" 
                class="text-slate-400 hover:text-slate-600 dark:hover:text-white transition-colors">
                <i class='bx bx-arrow-back text-2xl'></i>
            </a>
            <div class="h-6 w-px bg-slate-200 dark:bg-slate-700"></div>
            <h1 class="text-lg font-bold text-slate-800 dark:text-white flex items-center gap-2">
                <span class="font-mono text-slate-500 font-normal">{{ $transaction->invoiceNumber }}</span>
                @if($transaction->status === 'COMPLETED')
                    <span class="bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400 text-xs px-2 py-0.5 rounded-full uppercase tracking-wide">Success</span>
                @elseif($transaction->status === 'CANCELLED')
                    <span class="bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-400 text-xs px-2 py-0.5 rounded-full uppercase tracking-wide">Void</span>
                @else
                    <span class="bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-400 text-xs px-2 py-0.5 rounded-full uppercase tracking-wide">Hold</span>
                @endif
            </h1>
        </div>
    </div>

    <!-- Info and Actions Bar -->
    <div class="flex justify-between items-center mb-6 no-print">
        <div>
            <p class="text-sm text-slate-500">Waktu Transaksi: 
                <span class="font-bold text-slate-700 dark:text-slate-300">
                    {{ $transaction->date->format('d M Y, H:i') }}
                </span>
            </p>
        </div>
        <div class="flex gap-2">
            @if($transaction->status !== 'CANCELLED')
                <button onclick="if(confirm('Yakin ingin membatalkan transaksi ini?')) window.location.href='#'" 
                    class="bg-white dark:bg-darkCard border border-rose-200 dark:border-rose-900/50 text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/20 px-4 py-2 rounded-lg text-sm font-bold shadow-sm transition-colors flex items-center gap-2">
                    <i class='bx bx-x-circle text-lg'></i> Void / Batalkan
                </button>
            @endif
            <button onclick="window.print()" 
                class="bg-primary hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-bold shadow-md shadow-indigo-500/20 transition-colors flex items-center gap-2">
                <i class='bx bx-printer text-lg'></i> Cetak Struk
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Main Content - Items List -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Items Card -->
            <div class="bg-white dark:bg-darkCard rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">
                <div class="p-6 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
                    <h3 class="text-sm font-bold text-slate-800 dark:text-white flex items-center gap-2">
                        <i class='bx bx-shopping-bag text-primary text-lg'></i> Rincian Belanja
                    </h3>
                    <span class="text-xs text-slate-500">{{ $transaction->items->count() }} Items</span>
                </div>
                
                <table class="w-full text-left text-sm text-slate-600 dark:text-slate-400">
                    <thead class="bg-slate-50 dark:bg-slate-700/50 text-xs uppercase font-semibold text-slate-500 dark:text-slate-400">
                        <tr>
                            <th class="px-6 py-4">Produk</th>
                            <th class="px-6 py-4 text-center">Qty</th>
                            <th class="px-6 py-4 text-right">Harga</th>
                            <th class="px-6 py-4 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @foreach($transaction->items as $item)
                            <tr>
                                <td class="px-6 py-4">
                                    <span class="block font-medium text-slate-800 dark:text-white">{{ $item->product->name }}</span>
                                    <span class="text-xs text-slate-400">SKU: {{ $item->product->sku }}</span>
                                </td>
                                <td class="px-6 py-4 text-center">{{ $item->quantity }}</td>
                                <td class="px-6 py-4 text-right">Rp {{ number_format($item->unitPrice, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 text-right font-semibold">Rp {{ number_format($item->totalPrice, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Totals Section -->
                <div class="bg-slate-50/50 dark:bg-slate-800/30 p-6 border-t border-slate-100 dark:border-slate-700">
                    <div class="flex justify-end mb-2">
                        <div class="w-56 flex justify-between text-sm">
                            <span class="text-slate-500">Subtotal</span>
                            <span class="font-semibold text-slate-700 dark:text-slate-300">
                                Rp {{ number_format($transaction->items->sum('totalPrice'), 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                    <div class="flex justify-end mb-2">
                        <div class="w-56 flex justify-between text-sm">
                            <span class="text-slate-500">Pajak (0%)</span>
                            <span class="font-semibold text-slate-700 dark:text-slate-300">Rp 0</span>
                        </div>
                    </div>
                    <div class="flex justify-end mt-4 pt-4 border-t border-slate-200 dark:border-slate-600">
                        <div class="w-64 flex justify-between items-center">
                            <span class="text-base font-bold text-slate-800 dark:text-white">Total Bayar</span>
                            <span class="text-2xl font-bold text-primary">
                                Rp {{ number_format((float)$transaction->totalAmount, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Sidebar - Payment, Customer, Activity -->
        <div class="lg:col-span-1 space-y-6">
            
            <!-- Payment Info -->
            <div class="bg-white dark:bg-darkCard p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
                <h3 class="text-sm font-bold text-slate-800 dark:text-white mb-4 border-b border-slate-100 dark:border-slate-700 pb-3">Pembayaran</h3>
                
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-slate-500">Metode</span>
                        <span class="text-sm font-bold text-slate-800 dark:text-white flex items-center gap-1">
                            @if($transaction->paymentMethod === 'CASH')
                                <i class='bx bx-money text-emerald-500 text-lg'></i> Tunai (Cash)
                            @elseif($transaction->paymentMethod === 'TRANSFER')
                                <i class='bx bx-transfer text-indigo-500 text-lg'></i> Transfer
                            @elseif($transaction->paymentMethod === 'SUKARELA')
                                <i class='bx bx-wallet text-emerald-500 text-lg'></i> Simpanan Sukarela
                            @else
                                <i class='bx bx-wallet text-amber-500 text-lg'></i> Kredit
                            @endif
                        </span>
                    </div>
                    
                    @if($transaction->note)
                        <div class="pt-3 border-t border-slate-100 dark:border-slate-700">
                            <p class="text-xs text-slate-500 mb-1">Catatan:</p>
                            <p class="text-sm text-slate-700 dark:text-slate-300">{{ $transaction->note }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Customer Info -->
            <div class="bg-white dark:bg-darkCard p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
                <h3 class="text-sm font-bold text-slate-800 dark:text-white mb-4 border-b border-slate-100 dark:border-slate-700 pb-3">Pelanggan</h3>
                
                <div class="flex items-center gap-4 mb-4">
                    @if($transaction->member)
                        <div class="w-12 h-12 rounded-full bg-indigo-100 dark:bg-indigo-500/20 text-primary flex items-center justify-center font-bold text-lg">
                            {{ strtoupper(substr($transaction->member->name, 0, 1)) }}
                        </div>
                        <div>
                            <h5 class="text-sm font-bold text-slate-800 dark:text-white">{{ $transaction->member->name }}</h5>
                            <p class="text-xs text-slate-400">Member - {{ $transaction->member->memberNumber }}</p>
                        </div>
                    @else
                        <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center text-slate-500 font-bold text-lg">G</div>
                        <div>
                            <h5 class="text-sm font-bold text-slate-800 dark:text-white">Guest (Umum)</h5>
                            <p class="text-xs text-slate-400">Non-Member</p>
                        </div>
                    @endif
                </div>
                
                @if(!$transaction->member)
                    <p class="text-xs text-slate-400 italic">Transaksi ini tidak mendapatkan poin loyalty.</p>
                @endif
            </div>

            <!-- Activity Timeline -->
            <div class="bg-white dark:bg-darkCard p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
                <h3 class="text-sm font-bold text-slate-800 dark:text-white mb-4">Aktivitas</h3>
                <div class="space-y-5 pl-2 border-l border-slate-200 dark:border-slate-700 ml-1">
                    @if($transaction->status === 'COMPLETED')
                        <div class="relative pl-5">
                            <div class="absolute -left-[5px] top-1.5 w-2.5 h-2.5 rounded-full bg-emerald-500"></div>
                            <p class="text-xs font-bold text-slate-800 dark:text-white">Pembayaran Sukses</p>
                            <p class="text-xs text-slate-400 mt-0.5">{{ $transaction->updated_at->format('d M, H:i') }}</p>
                        </div>
                    @elseif($transaction->status === 'CANCELLED')
                        <div class="relative pl-5">
                            <div class="absolute -left-[5px] top-1.5 w-2.5 h-2.5 rounded-full bg-rose-500"></div>
                            <p class="text-xs font-bold text-slate-800 dark:text-white">Transaksi Dibatalkan</p>
                            <p class="text-xs text-slate-400 mt-0.5">{{ $transaction->updated_at->format('d M, H:i') }}</p>
                        </div>
                    @endif
                    
                    <div class="relative pl-5">
                        <div class="absolute -left-[5px] top-1.5 w-2.5 h-2.5 rounded-full bg-slate-300 dark:bg-slate-600"></div>
                        <p class="text-xs font-bold text-slate-800 dark:text-white">Order Dibuat</p>
                        <p class="text-xs text-slate-400 mt-0.5">{{ $transaction->created_at->format('d M, H:i') }}</p>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>

<style>
    @media print {
        .no-print { display: none !important; }
    }
</style>
@endsection
