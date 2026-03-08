@extends('layouts.supplier')

@section('title', 'Batch Konsinyasi')

@section('content')
    <div class="space-y-6">
        {{-- Header with Back Button --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <a href="{{ route('supplier.dashboard') }}" 
                    class="inline-flex items-center gap-1.5 text-xs text-slate-500 hover:text-emerald-600 dark:text-slate-400 dark:hover:text-emerald-400 mb-2 transition-colors group">
                    <i class='bx bx-arrow-back text-base group-hover:-translate-x-1 transition-transform'></i>
                    Kembali ke Dashboard
                </a>
                <h1 class="text-xl font-bold text-slate-900 dark:text-white">Riwayat Stok & Pengiriman</h1>
                <p class="text-[11px] text-slate-500 mt-0.5">Daftar batch konsinyasi dan histori pengiriman barang Anda</p>
            </div>
            <a href="{{ route('supplier.restock.create') }}"
                class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-4 py-2.5 rounded-xl text-sm transition-colors shadow-sm shadow-emerald-500/20">
                <i class='bx bx-package text-lg'></i> Kirim Barang Hari Ini
            </a>
        </div>

        @if(session('success'))
        <div class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-400 px-4 py-3 rounded-xl flex items-center gap-3">
            <i class='bx bx-check-circle text-xl'></i>
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
        @endif

        {{-- Batch List --}}
        <div
            class="bg-white dark:bg-darkCard rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">

            {{-- Mobile Card View --}}
            <div class="sm:hidden divide-y divide-slate-100 dark:divide-slate-700">
                @forelse($batches as $batch)
                    @php
                        $totalRequested = $batch->items->sum('initialQty');
                        $totalReceived = $batch->items->sum('receivedQty');
                        $totalDamaged = $batch->items->sum('damagedQty');
                        $hasDiscrepancy = $totalDamaged > 0;
                    @endphp
                    <div class="p-4">
                        <div class="flex items-start justify-between gap-3 mb-2">
                            <div>
                                <div class="flex items-center gap-2">
                                    <h6 class="font-bold text-slate-900 dark:text-white text-[13px]">#{{ $batch->batchCode }}
                                    </h6>
                                    @if($batch->status === 'REQUESTED')
                                        <span
                                            class="bg-blue-50 text-blue-600 dark:bg-blue-500/20 dark:text-blue-400 px-2 py-0.5 rounded text-[9px] font-bold uppercase animate-pulse">
                                            Perlu Dikirim
                                        </span>
                                    @elseif($batch->status === 'ACTIVE')
                                        <span
                                            class="bg-indigo-50 text-indigo-600 dark:bg-indigo-500/20 dark:text-indigo-400 px-2 py-0.5 rounded text-[9px] font-bold uppercase">
                                            Aktif
                                        </span>
                                    @elseif($batch->status === 'PENDING_SETTLEMENT')
                                        <span
                                            class="bg-amber-50 text-amber-600 dark:bg-amber-500/20 dark:text-amber-400 px-2 py-0.5 rounded text-[9px] font-bold uppercase">
                                            Siap Bayar
                                        </span>
                                    @elseif($batch->status === 'SETTLED')
                                        <span
                                            class="bg-emerald-50 text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 px-2 py-0.5 rounded text-[9px] font-bold uppercase"
                                            title="Dibayar {{ $batch->settledAt ? $batch->settledAt->format('d M Y H:i') : '-' }}">
                                            ✓ Lunas
                                        </span>
                                    @endif
                                </div>
                                <p class="text-[10px] text-slate-500 mt-0.5">{{ $batch->created_at->format('d M Y H:i') }}</p>
                            </div>
                            <span class="font-bold text-emerald-600 dark:text-emerald-400 text-[13px]">
                                Rp {{ number_format($batch->payableAmount ?? 0, 0, ',', '.') }}
                            </span>
                        </div>
                        {{-- Product list with images --}}
                        <div class="flex flex-wrap gap-2 mb-2">
                            @foreach($batch->items->take(3) as $item)
                                <div class="flex items-center gap-2 bg-slate-50 dark:bg-slate-800 rounded-lg px-2 py-1">
                                    @if($item->product->image)
                                        <img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product->name }}"
                                            class="w-8 h-8 rounded object-cover">
                                    @else
                                        <div
                                            class="w-8 h-8 rounded bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-slate-400">
                                            <i class='bx bx-image text-sm'></i>
                                        </div>
                                    @endif
                                    <span
                                        class="text-[11px] text-slate-700 dark:text-slate-300 max-w-[100px] truncate">{{ $item->product->name ?? '-' }}</span>
                                </div>
                            @endforeach
                            @if($batch->items->count() > 3)
                                <span class="text-[10px] text-slate-400 self-center">+{{ $batch->items->count() - 3 }}
                                    lainnya</span>
                            @endif
                        </div>
                        <div class="flex items-center gap-4 text-[11px]">
                            <span class="text-slate-500">Diminta: <strong
                                    class="text-slate-900 dark:text-white">{{ $totalRequested }}</strong></span>
                            @if($batch->status !== 'REQUESTED')
                                @php
                                    $totalSold = $batch->items->sum('soldQty');
                                    $totalReturned = $batch->items->sum('returnedQty');
                                    $totalRemaining = $batch->items->sum('remainingQty');
                                @endphp
                                <span class="text-slate-500">Terjual:
                                    <strong class="text-emerald-600">{{ $totalSold }}</strong>
                                </span>
                                @if($totalReturned > 0)
                                    <span class="text-slate-500">Retur:
                                        <strong class="text-rose-600">{{ $totalReturned }}</strong>
                                    </span>
                                @endif
                                <span class="text-slate-500">Sisa:
                                    <strong class="text-blue-600">{{ $totalRemaining }}</strong>
                                </span>
                                @if($hasDiscrepancy)
                                    <span class="text-red-600 font-bold">(-{{ $totalDamaged }} selisih)</span>
                                @endif
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-500 dark:text-slate-400">
                        <div
                            class="w-14 h-14 bg-slate-100 dark:bg-slate-700 rounded-full flex items-center justify-center text-2xl text-slate-300 dark:text-slate-600 mb-3 mx-auto">
                            <i class='bx bx-archive-in'></i>
                        </div>
                        <p class="font-medium">Belum ada batch konsinyasi</p>
                        <p class="text-[11px] mt-1">Batch akan muncul ketika koperasi meminta stok produk Anda</p>
                    </div>
                @endforelse
            </div>

            <div class="hidden sm:block overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-100 dark:border-slate-700">
                        <tr>
                            <th class="px-6 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Batch</th>
                            <th class="px-6 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Produk</th>
                            <th class="px-6 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">
                                Diminta</th>
                            <th class="px-6 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">
                                Terjual</th>
                            <th class="px-6 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">
                                Retur</th>
                            <th class="px-6 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">
                                Sisa</th>
                            <th class="px-6 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">
                                Status</th>
                            <th class="px-6 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">
                                Pendapatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse($batches as $batch)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div>
                                        <h6 class="font-bold text-slate-900 dark:text-white text-[13px]">
                                            #{{ $batch->batchCode }}</h6>
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
                                    @php
                                        $totalRequested = $batch->items->sum('initialQty');
                                        $totalDamaged = $batch->items->sum('damagedQty');
                                        $hasDiscrepancy = $totalDamaged > 0;
                                    @endphp
                                    <span class="font-bold text-slate-900 dark:text-white">{{ $totalRequested }}</span>
                                    @if($hasDiscrepancy)
                                        <div class="text-[9px] text-red-600 dark:text-red-400 mt-0.5" title="Rusak/hilang/tidak layak jual">
                                            -{{ $totalDamaged }} selisih
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @php
                                        $totalSold = $batch->items->sum('soldQty');
                                    @endphp
                                    @if($batch->status === 'REQUESTED')
                                        <span class="text-[11px] text-slate-400 italic">-</span>
                                    @else
                                        <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ $totalSold }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @php
                                        $totalReturned = $batch->items->sum('returnedQty');
                                    @endphp
                                    @if($batch->status === 'REQUESTED')
                                        <span class="text-[11px] text-slate-400 italic">-</span>
                                    @elseif($totalReturned > 0)
                                        <span class="font-bold text-rose-600 dark:text-rose-400">{{ $totalReturned }}</span>
                                    @else
                                        <span class="text-slate-400">0</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @php
                                        $totalRemaining = $batch->items->sum('remainingQty');
                                    @endphp
                                    @if($batch->status === 'REQUESTED')
                                        <span class="text-[11px] text-slate-400 italic">-</span>
                                    @else
                                        <span class="font-bold text-blue-600 dark:text-blue-400">{{ $totalRemaining }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($batch->status === 'REQUESTED')
                                        <span
                                            class="bg-blue-50 text-blue-600 dark:bg-blue-500/20 dark:text-blue-400 px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wide inline-flex items-center gap-1 animate-pulse">
                                            <i class='bx bx-time-five'></i> Perlu Dikirim
                                        </span>
                                    @elseif($batch->status === 'ACTIVE')
                                        <span
                                            class="bg-indigo-50 text-indigo-600 dark:bg-indigo-500/20 dark:text-indigo-400 px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wide inline-flex items-center gap-1">
                                            <i class='bx bx-store'></i> Aktif
                                        </span>
                                    @elseif($batch->status === 'PENDING_SETTLEMENT')
                                        <span
                                            class="bg-amber-50 text-amber-600 dark:bg-amber-500/20 dark:text-amber-400 px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wide inline-flex items-center gap-1">
                                            <i class='bx bx-wallet'></i> Siap Bayar
                                        </span>
                                    @elseif($batch->status === 'SETTLED')
                                        <div class="flex flex-col items-center gap-0.5">
                                            <span
                                                class="bg-emerald-50 text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wide inline-flex items-center gap-1">
                                                <i class='bx bx-check-circle'></i> Lunas
                                            </span>
                                            @if($batch->settledAt)
                                                <span class="text-[9px] text-slate-400 italic">{{ $batch->settledAt->format('d M Y H:i') }}</span>
                                            @endif
                                        </div>
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
                                <td colspan="8" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <div
                                            class="w-16 h-16 bg-slate-100 dark:bg-slate-700 rounded-full flex items-center justify-center text-3xl text-slate-300 dark:text-slate-600 mb-4">
                                            <i class='bx bx-archive-in'></i>
                                        </div>
                                        <p class="font-medium">Belum ada batch konsinyasi</p>
                                        <p class="text-[12px] mt-1">Batch akan muncul ketika koperasi meminta stok produk Anda
                                        </p>
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
                        <h4 class="font-bold text-blue-800 dark:text-blue-300 text-[14px]">Ada {{ $requestedCount }} permintaan
                            stok menunggu!</h4>
                        <p class="text-[12px] text-blue-700 dark:text-blue-400 mt-1">
                            Silakan kirim barang ke Koperasi UMB sesuai jumlah yang diminta. Setelah barang diterima, status
                            akan berubah menjadi "Aktif".
                        </p>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection