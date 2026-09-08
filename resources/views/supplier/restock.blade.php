@extends('layouts.supplier')

@section('title', 'Batch Konsinyasi')

@section('content')
    <div class="max-w-6xl mx-auto space-y-6">

        {{-- Top Navigation Header --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <a href="{{ route('supplier.dashboard') }}" 
                    class="inline-flex items-center gap-1.5 text-xs font-bold text-zinc-500 hover:text-[#155A6B] dark:text-zinc-400 dark:hover:text-teal-400 mb-1.5 transition-colors group">
                    <i class='bx bx-arrow-back text-base group-hover:-translate-x-1 transition-transform'></i>
                    Kembali ke Dasbor
                </a>
                <h1 class="text-2xl font-black text-zinc-900 dark:text-white tracking-tight">Riwayat Stok & Batch Pengiriman</h1>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 font-medium mt-0.5">Daftar batch konsinyasi dan histori pengiriman barang Anda ke minimarket</p>
            </div>
            <a href="{{ route('supplier.restock.create') }}"
                class="inline-flex items-center gap-2 bg-[#155A6B] hover:bg-[#166072] text-white font-bold px-4 py-2.5 rounded-xl text-xs transition-all shadow-md shadow-[#155A6B]/20">
                <i class='bx bx-package text-lg'></i> Buat Request Pengiriman
            </a>
        </div>

        @if(session('success'))
        <div class="bg-teal-50 dark:bg-teal-950/40 border border-teal-200 dark:border-teal-800 text-teal-800 dark:text-teal-300 px-4 py-3 rounded-2xl flex items-center gap-3">
            <i class='bx bx-check-circle text-xl text-[#155A6B] dark:text-teal-400'></i>
            <span class="text-xs font-semibold">{{ session('success') }}</span>
        </div>
        @endif

        @if(session('error'))
        <div class="bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 px-4 py-3 rounded-2xl flex items-center gap-3">
            <i class='bx bx-error-circle text-xl text-rose-600 dark:text-rose-400'></i>
            <span class="text-xs font-semibold">{{ session('error') }}</span>
        </div>
        @endif

        {{-- Info Box for REQUESTED batches --}}
        @if(isset($requestedCount) && $requestedCount > 0)
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-950/30 dark:to-indigo-950/30 border border-blue-200/80 dark:border-blue-800/50 rounded-2xl p-4 sm:p-5 shadow-sm">
                <div class="flex gap-3.5 items-start">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                        <i class='bx bx-truck text-2xl'></i>
                    </div>
                    <div>
                        <h4 class="font-extrabold text-blue-900 dark:text-blue-200 text-sm">Ada {{ $requestedCount }} permintaan stok menunggu pengiriman!</h4>
                        <p class="text-xs text-blue-700 dark:text-blue-300 mt-1 font-medium leading-relaxed">
                            Silakan kirim barang ke minimarket {{ config('cooperative.name') }} sesuai jumlah yang diminta. Setelah tim minimarket mengkonfirmasi penerimaan, status batch akan otomatis berubah menjadi "Aktif".
                        </p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Batch List Main Container --}}
        <div class="rounded-3xl bg-white/80 dark:bg-zinc-900/80 backdrop-blur-xl border border-zinc-200/80 dark:border-zinc-800/80 shadow-xl overflow-hidden">

            {{-- Mobile Card View --}}
            <div class="sm:hidden divide-y divide-zinc-200/60 dark:divide-zinc-800/60">
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
                                    <h6 class="font-bold text-zinc-900 dark:text-white text-sm">#{{ $batch->batchCode }}</h6>
                                    @if($batch->status === 'REQUESTED')
                                        <span class="bg-blue-50 text-blue-700 dark:bg-blue-950/50 dark:text-blue-300 border border-blue-200 dark:border-blue-800/50 px-2 py-0.5 rounded-full text-[9px] font-extrabold uppercase animate-pulse">
                                            Perlu Kirim
                                        </span>
                                    @elseif($batch->status === 'ACTIVE')
                                        <span class="bg-indigo-50 text-indigo-700 dark:bg-indigo-950/50 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800/50 px-2 py-0.5 rounded-full text-[9px] font-extrabold uppercase">
                                            Aktif
                                        </span>
                                    @elseif($batch->status === 'PENDING_SETTLEMENT')
                                        <span class="bg-amber-50 text-amber-700 dark:bg-amber-950/50 dark:text-amber-300 border border-amber-200 dark:border-amber-800/50 px-2 py-0.5 rounded-full text-[9px] font-extrabold uppercase">
                                            Siap Cair
                                        </span>
                                    @elseif($batch->status === 'SETTLED')
                                        <span class="bg-teal-50 text-teal-700 dark:bg-teal-950/50 dark:text-teal-300 border border-teal-200 dark:border-teal-800/50 px-2 py-0.5 rounded-full text-[9px] font-extrabold uppercase">
                                            ✓ Lunas
                                        </span>
                                    @endif
                                </div>
                                <p class="text-[10px] font-semibold text-zinc-400 mt-0.5">{{ $batch->created_at->format('d M Y H:i') }}</p>
                            </div>
                            <span class="font-black text-[#155A6B] dark:text-teal-400 text-sm">
                                Rp {{ number_format($batch->payableAmount ?? 0, 0, ',', '.') }}
                            </span>
                        </div>

                        {{-- Product List Snippet --}}
                        <div class="flex flex-wrap gap-2 my-2.5">
                            @foreach($batch->items->take(3) as $item)
                                <div class="flex items-center gap-2 bg-zinc-50 dark:bg-zinc-800/60 rounded-xl px-2.5 py-1 border border-zinc-200/50 dark:border-zinc-700/50">
                                    @if(!empty($item->product->image))
                                        <img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product->name }}"
                                            class="w-7 h-7 rounded-lg object-cover">
                                    @else
                                        <div class="w-7 h-7 rounded-lg bg-zinc-200 dark:bg-zinc-700 flex items-center justify-center text-zinc-400">
                                            <i class='bx bx-image text-xs'></i>
                                        </div>
                                    @endif
                                    <span class="text-[11px] font-bold text-zinc-700 dark:text-zinc-300 max-w-[110px] truncate">{{ $item->product->name ?? '-' }}</span>
                                </div>
                            @endforeach
                            @if($batch->items->count() > 3)
                                <span class="text-[10px] text-zinc-400 font-bold self-center">+{{ $batch->items->count() - 3 }} lainnya</span>
                            @endif
                        </div>

                        <div class="flex items-center gap-3 text-[11px] pt-1">
                            <span class="text-zinc-500 font-medium">Diminta: <strong class="text-zinc-900 dark:text-white font-extrabold">{{ $totalRequested }}</strong></span>
                            @if($batch->status !== 'REQUESTED')
                                @php
                                    $totalSold = $batch->items->sum('soldQty');
                                    $totalReturned = $batch->items->sum('returnedQty');
                                    $totalRemaining = $batch->items->sum('remainingQty');
                                @endphp
                                <span class="text-zinc-500 font-medium">Terjual: <strong class="text-teal-600 font-extrabold">{{ $totalSold }}</strong></span>
                                @if($totalReturned > 0)
                                    <span class="text-zinc-500 font-medium">Retur: <strong class="text-rose-600 font-extrabold">{{ $totalReturned }}</strong></span>
                                @endif
                                <span class="text-zinc-500 font-medium">Sisa: <strong class="text-blue-600 font-extrabold">{{ $totalRemaining }}</strong></span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="p-10 text-center text-zinc-400">
                        <i class='bx bx-archive-in text-5xl mb-2 text-zinc-300 dark:text-zinc-700'></i>
                        <p class="text-xs font-semibold">Belum ada riwayat batch pengiriman.</p>
                    </div>
                @endforelse
            </div>

            {{-- Desktop Table View --}}
            <div class="hidden sm:block overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-zinc-50/80 dark:bg-zinc-800/40 border-b border-zinc-200/80 dark:border-zinc-800/80">
                        <tr>
                            <th class="px-6 py-3.5 text-[10px] font-extrabold text-zinc-400 uppercase tracking-wider">Kode Batch</th>
                            <th class="px-6 py-3.5 text-[10px] font-extrabold text-zinc-400 uppercase tracking-wider">Produk</th>
                            <th class="px-6 py-3.5 text-[10px] font-extrabold text-zinc-400 uppercase tracking-wider text-center">Diminta</th>
                            <th class="px-6 py-3.5 text-[10px] font-extrabold text-zinc-400 uppercase tracking-wider text-center">Terjual</th>
                            <th class="px-6 py-3.5 text-[10px] font-extrabold text-zinc-400 uppercase tracking-wider text-center">Retur</th>
                            <th class="px-6 py-3.5 text-[10px] font-extrabold text-zinc-400 uppercase tracking-wider text-center">Sisa</th>
                            <th class="px-6 py-3.5 text-[10px] font-extrabold text-zinc-400 uppercase tracking-wider text-center">Status</th>
                            <th class="px-6 py-3.5 text-[10px] font-extrabold text-zinc-400 uppercase tracking-wider text-right">Pendapatan Anda</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200/60 dark:divide-zinc-800/60 text-xs">
                        @forelse($batches as $batch)
                            <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30 transition-colors">
                                <td class="px-6 py-4">
                                    <div>
                                        <h6 class="font-bold text-zinc-900 dark:text-white text-xs">#{{ $batch->batchCode }}</h6>
                                        <p class="text-[10px] font-semibold text-zinc-400 mt-0.5">{{ $batch->created_at->format('d M Y H:i') }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @foreach($batch->items->take(2) as $item)
                                        <div class="font-semibold text-zinc-800 dark:text-zinc-200">
                                            {{ $item->product->name ?? '-' }}
                                        </div>
                                    @endforeach
                                    @if($batch->items->count() > 2)
                                        <span class="text-[10px] font-bold text-zinc-400">+{{ $batch->items->count() - 2 }} lainnya</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center font-extrabold text-zinc-900 dark:text-white">
                                    @php
                                        $totalRequested = $batch->items->sum('initialQty');
                                        $totalDamaged = $batch->items->sum('damagedQty');
                                        $hasDiscrepancy = $totalDamaged > 0;
                                    @endphp
                                    {{ $totalRequested }}
                                    @if($hasDiscrepancy)
                                        <div class="text-[9px] font-extrabold text-rose-600 dark:text-rose-400 mt-0.5" title="Rusak/hilang">
                                            -{{ $totalDamaged }} selisih
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @php $totalSold = $batch->items->sum('soldQty'); @endphp
                                    @if($batch->status === 'REQUESTED')
                                        <span class="text-zinc-400 font-medium text-xs">-</span>
                                    @else
                                        <span class="font-extrabold text-teal-600 dark:text-teal-400">{{ $totalSold }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @php $totalReturned = $batch->items->sum('returnedQty'); @endphp
                                    @if($batch->status === 'REQUESTED')
                                        <span class="text-zinc-400 font-medium text-xs">-</span>
                                    @elseif($totalReturned > 0)
                                        <span class="font-extrabold text-rose-600 dark:text-rose-400">{{ $totalReturned }}</span>
                                    @else
                                        <span class="text-zinc-400 font-semibold">0</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @php $totalRemaining = $batch->items->sum('remainingQty'); @endphp
                                    @if($batch->status === 'REQUESTED')
                                        <span class="text-zinc-400 font-medium text-xs">-</span>
                                    @else
                                        <span class="font-extrabold text-blue-600 dark:text-blue-400">{{ $totalRemaining }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($batch->status === 'REQUESTED')
                                        <span class="inline-flex items-center gap-1 bg-blue-50 text-blue-700 dark:bg-blue-950/50 dark:text-blue-300 border border-blue-200 dark:border-blue-800/50 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase animate-pulse">
                                            <i class='bx bx-time-five'></i> Perlu Kirim
                                        </span>
                                    @elseif($batch->status === 'ACTIVE')
                                        <span class="inline-flex items-center gap-1 bg-indigo-50 text-indigo-700 dark:bg-indigo-950/50 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800/50 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase">
                                            <i class='bx bx-store'></i> Aktif
                                        </span>
                                    @elseif($batch->status === 'PENDING_SETTLEMENT')
                                        <span class="inline-flex items-center gap-1 bg-amber-50 text-amber-700 dark:bg-amber-950/50 dark:text-amber-300 border border-amber-200 dark:border-amber-800/50 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase">
                                            <i class='bx bx-wallet'></i> Siap Cair
                                        </span>
                                    @elseif($batch->status === 'SETTLED')
                                        <div class="flex flex-col items-center gap-0.5">
                                            <span class="inline-flex items-center gap-1 bg-teal-50 text-teal-700 dark:bg-teal-950/50 dark:text-teal-300 border border-teal-200 dark:border-teal-800/50 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase">
                                                <i class='bx bx-check-circle'></i> Lunas
                                            </span>
                                            @if($batch->settledAt)
                                                <span class="text-[9px] text-zinc-400 font-semibold">{{ $batch->settledAt->format('d M Y H:i') }}</span>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right font-extrabold text-[#155A6B] dark:text-teal-400">
                                    Rp {{ number_format($batch->payableAmount ?? 0, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-zinc-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <i class='bx bx-archive-in text-5xl mb-2 text-zinc-300 dark:text-zinc-700'></i>
                                        <p class="text-xs font-semibold">Belum ada batch konsinyasi.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($batches->hasPages())
                <div class="px-6 py-4 border-t border-zinc-200/60 dark:border-zinc-800/60">
                    {{ $batches->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
