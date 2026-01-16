@extends('layouts.supplier')

@section('title', 'Batch Konsinyasi')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-white">Batch Konsinyasi</h1>
            <p class="text-[11px] text-slate-500 mt-0.5">Kelola permintaan dan pengiriman barang konsinyasi</p>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        @if($requestedCount > 0)
        <div class="bg-white dark:bg-darkCard p-4 rounded-xl shadow-sm border-l-4 border-l-blue-500 border border-blue-100 dark:border-blue-500/30 animate-pulse">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center text-blue-600 dark:text-blue-400">
                    <i class='bx bx-package text-xl'></i>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-blue-600 uppercase tracking-wider">Perlu Dikirim</p>
                    <h3 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $requestedCount }}</h3>
                </div>
            </div>
        </div>
        @else
        <div class="bg-white dark:bg-darkCard p-4 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-slate-50 dark:bg-slate-700 flex items-center justify-center text-slate-400">
                    <i class='bx bx-package text-xl'></i>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Perlu Dikirim</p>
                    <h3 class="text-2xl font-bold text-slate-900 dark:text-white">0</h3>
                </div>
            </div>
        </div>
        @endif

        <div class="bg-white dark:bg-darkCard p-4 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-indigo-50 dark:bg-indigo-500/10 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                    <i class='bx bx-store text-xl'></i>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Sedang Dijual</p>
                    <h3 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $activeCount }}</h3>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-darkCard p-4 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-amber-50 dark:bg-amber-500/10 flex items-center justify-center text-amber-600 dark:text-amber-400">
                    <i class='bx bx-wallet text-xl'></i>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Siap Bayar</p>
                    <h3 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $pendingSettlementCount }}</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- Batch List --}}
    <div class="bg-white dark:bg-darkCard rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-100 dark:border-slate-700">
                    <tr>
                        <th class="px-6 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Batch</th>
                        <th class="px-6 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Produk</th>
                        <th class="px-6 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Qty</th>
                        <th class="px-6 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Status</th>
                        <th class="px-6 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Estimasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse($batches as $batch)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                        <td class="px-6 py-4">
                            <div>
                                <h6 class="font-bold text-slate-900 dark:text-white text-[13px]">#{{ $batch->batchCode }}</h6>
                                <p class="text-[10px] text-slate-500">{{ $batch->created_at->format('d M Y H:i') }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @foreach($batch->items->take(2) as $item)
                                <div class="text-[12px] text-slate-700 dark:text-slate-300">
                                    {{ $item->product->name ?? '-' }}
                                </div>
                            @endforeach
                            @if($batch->items->count() > 2)
                                <span class="text-[10px] text-slate-400">+{{ $batch->items->count() - 2 }} lainnya</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="font-bold text-slate-900 dark:text-white">{{ $batch->items->sum('initialQty') }}</span>
                            <span class="text-[10px] text-slate-500">pcs</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($batch->status === 'REQUESTED')
                                <span class="bg-blue-50 text-blue-600 dark:bg-blue-500/20 dark:text-blue-400 px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wide inline-flex items-center gap-1 animate-pulse">
                                    <i class='bx bx-time-five'></i> Perlu Dikirim
                                </span>
                            @elseif($batch->status === 'ACTIVE')
                                <span class="bg-indigo-50 text-indigo-600 dark:bg-indigo-500/20 dark:text-indigo-400 px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wide inline-flex items-center gap-1">
                                    <i class='bx bx-store'></i> Aktif
                                </span>
                            @elseif($batch->status === 'PENDING_SETTLEMENT')
                                <span class="bg-amber-50 text-amber-600 dark:bg-amber-500/20 dark:text-amber-400 px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wide inline-flex items-center gap-1">
                                    <i class='bx bx-wallet'></i> Siap Bayar
                                </span>
                            @elseif($batch->status === 'SETTLED')
                                <span class="bg-emerald-50 text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wide inline-flex items-center gap-1">
                                    <i class='bx bx-check-circle'></i> Lunas
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <span class="font-bold text-emerald-600 dark:text-emerald-400 text-[13px]">
                                Rp {{ number_format($batch->payableAmount ?? 0, 0, ',', '.') }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-16 h-16 bg-slate-100 dark:bg-slate-700 rounded-full flex items-center justify-center text-3xl text-slate-300 dark:text-slate-600 mb-4">
                                    <i class='bx bx-archive-in'></i>
                                </div>
                                <p class="font-medium">Belum ada batch konsinyasi</p>
                                <p class="text-[12px] mt-1">Batch akan muncul ketika koperasi meminta stok produk Anda</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($batches->hasPages())
        <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-700">
            {{ $batches->links() }}
        </div>
        @endif
    </div>

    {{-- Info Box for REQUESTED batches --}}
    @if($requestedCount > 0)
    <div class="bg-blue-50 dark:bg-blue-500/10 border border-blue-200 dark:border-blue-500/30 rounded-xl p-4">
        <div class="flex gap-3">
            <div class="flex-shrink-0">
                <i class='bx bx-info-circle text-2xl text-blue-600 dark:text-blue-400'></i>
            </div>
            <div>
                <h4 class="font-bold text-blue-800 dark:text-blue-300 text-[14px]">Ada {{ $requestedCount }} permintaan stok menunggu!</h4>
                <p class="text-[12px] text-blue-700 dark:text-blue-400 mt-1">
                    Silakan kirim barang ke Koperasi UMB sesuai jumlah yang diminta. Setelah barang diterima, status akan berubah menjadi "Aktif".
                </p>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
