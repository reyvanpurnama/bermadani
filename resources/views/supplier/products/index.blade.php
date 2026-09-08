@extends('layouts.supplier')

@section('title', 'Produk Saya')

@section('content')
    <div class="max-w-6xl mx-auto space-y-6">

        <!-- Top Header & Action Buttons -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-zinc-900 dark:text-white tracking-tight">Katalog Produk Saya</h1>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 font-medium mt-0.5">Kelola daftar dan status pengajuan produk konsinyasi Anda</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('supplier.restock') }}"
                    class="bg-white/80 dark:bg-zinc-900/80 backdrop-blur-xl text-zinc-700 dark:text-zinc-300 border border-zinc-200/80 dark:border-zinc-800/80 px-4 py-2.5 rounded-xl text-xs font-bold hover:border-[#155A6B] transition-all flex items-center gap-2 shadow-sm">
                    <i class='bx bx-history text-base text-[#155A6B] dark:text-teal-400'></i>
                    Riwayat Stok
                </a>
                <a href="{{ route('supplier.products.create') }}"
                    class="bg-[#155A6B] hover:bg-[#166072] text-white px-4 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center gap-2 shadow-md shadow-[#155A6B]/20">
                    <i class='bx bx-plus text-lg'></i>
                    Ajukan Produk Baru
                </a>
            </div>
        </div>

        <!-- Stats Overview Bento Grid -->
        @php
            $supplier = Auth::guard('supplier')->user();
            $totalProducts = $products->total();
            $activeCount = \App\Models\Product::where('supplierId', $supplier->id)
                ->where('approvalStatus', 'APPROVED')
                ->where('isActive', true)
                ->count();
            $pendingCount = \App\Models\Product::where('supplierId', $supplier->id)
                ->where('approvalStatus', 'PENDING')
                ->count();
            $rejectedCount = \App\Models\Product::where('supplierId', $supplier->id)
                ->where('approvalStatus', 'REJECTED')
                ->count();
        @endphp
        
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            <!-- Total Produk -->
            <div class="rounded-2xl bg-white/80 dark:bg-zinc-900/80 backdrop-blur-xl p-4 sm:p-5 border border-zinc-200/80 dark:border-zinc-800/80 shadow-md flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                    <i class='bx bx-box text-xl'></i>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Total SKU</span>
                    <h3 class="text-xl sm:text-2xl font-black text-zinc-900 dark:text-white leading-none mt-0.5">{{ $totalProducts }}</h3>
                </div>
            </div>

            <!-- Aktif / Disetujui -->
            <div class="rounded-2xl bg-white/80 dark:bg-zinc-900/80 backdrop-blur-xl p-4 sm:p-5 border border-zinc-200/80 dark:border-zinc-800/80 shadow-md flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-teal-50 dark:bg-teal-950/40 text-[#155A6B] dark:text-teal-400 flex items-center justify-center shrink-0">
                    <i class='bx bx-check-circle text-xl'></i>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Disetujui</span>
                    <div class="flex items-baseline gap-1 mt-0.5">
                        <h3 class="text-xl sm:text-2xl font-black text-zinc-900 dark:text-white leading-none">{{ $activeCount }}</h3>
                        <span class="text-[10px] text-zinc-400 font-semibold">/{{ $supplier->maxActiveProducts ?? 20 }}</span>
                    </div>
                </div>
            </div>

            <!-- Menunggu Review -->
            <div class="rounded-2xl bg-white/80 dark:bg-zinc-900/80 backdrop-blur-xl p-4 sm:p-5 border border-zinc-200/80 dark:border-zinc-800/80 shadow-md flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                    <i class='bx bx-time-five text-xl'></i>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Menunggu</span>
                    <h3 class="text-xl sm:text-2xl font-black text-zinc-900 dark:text-white leading-none mt-0.5">{{ $pendingCount }}</h3>
                </div>
            </div>

            <!-- Ditolak -->
            <div class="rounded-2xl bg-white/80 dark:bg-zinc-900/80 backdrop-blur-xl p-4 sm:p-5 border border-zinc-200/80 dark:border-zinc-800/80 shadow-md flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0">
                    <i class='bx bx-x-circle text-xl'></i>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Ditolak</span>
                    <h3 class="text-xl sm:text-2xl font-black text-zinc-900 dark:text-white leading-none mt-0.5">{{ $rejectedCount }}</h3>
                </div>
            </div>
        </div>

        <!-- Products List Main Container -->
        <div class="rounded-3xl bg-white/80 dark:bg-zinc-900/80 backdrop-blur-xl border border-zinc-200/80 dark:border-zinc-800/80 shadow-xl overflow-hidden">
            
            <!-- Filter Bar -->
            <div class="p-4 border-b border-zinc-200/60 dark:border-zinc-800/60 flex flex-col sm:flex-row gap-3 justify-between items-center">
                <div class="relative w-full sm:w-72">
                    <i class='bx bx-search absolute left-3.5 top-1/2 -translate-y-1/2 text-zinc-400 text-base'></i>
                    <input type="text" placeholder="Cari nama produk / SKU..."
                        class="w-full bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-200/80 dark:border-zinc-700/80 text-xs font-semibold rounded-xl pl-10 pr-4 py-2.5 outline-none focus:ring-2 focus:ring-[#155A6B]/20 focus:border-[#155A6B] transition-all text-zinc-900 dark:text-white placeholder-zinc-400">
                </div>
                
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <select
                        class="w-full sm:w-auto bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-200/80 dark:border-zinc-700/80 text-xs font-semibold rounded-xl px-3 py-2.5 outline-none focus:ring-2 focus:ring-[#155A6B]/20 focus:border-[#155A6B] cursor-pointer text-zinc-700 dark:text-zinc-300">
                        <option value="">Semua Kategori</option>
                    </select>
                    <select
                        class="w-full sm:w-auto bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-200/80 dark:border-zinc-700/80 text-xs font-semibold rounded-xl px-3 py-2.5 outline-none focus:ring-2 focus:ring-[#155A6B]/20 focus:border-[#155A6B] cursor-pointer text-zinc-700 dark:text-zinc-300">
                        <option value="">Semua Status</option>
                        <option value="active">Disetujui</option>
                        <option value="pending">Menunggu</option>
                        <option value="rejected">Ditolak</option>
                    </select>
                </div>
            </div>

            {{-- Mobile View Cards --}}
            <div class="sm:hidden divide-y divide-zinc-200/60 dark:divide-zinc-800/60">
                @forelse($products as $product)
                    @php
                        $canModify = in_array($product->approvalStatus, ['PENDING', 'REJECTED'], true);
                    @endphp
                    <div class="p-4 hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30 transition-colors">
                        <div class="flex items-start gap-3">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
                                    class="w-14 h-14 rounded-xl object-cover flex-shrink-0 shadow-sm">
                            @else
                                <div class="w-14 h-14 rounded-xl bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-zinc-400 flex-shrink-0">
                                    <i class='bx bx-image text-2xl'></i>
                                </div>
                            @endif
                            
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="min-w-0">
                                        <h6 class="font-bold text-sm text-zinc-900 dark:text-white truncate">{{ $product->name }}</h6>
                                        <span class="inline-block text-[10px] font-extrabold text-[#155A6B] dark:text-teal-400 font-mono mt-0.5">
                                            {{ $product->sku }}
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-1 shrink-0">
                                        @if($canModify)
                                            <a href="{{ route('supplier.products.edit', $product->id) }}"
                                                class="p-1.5 text-zinc-400 hover:text-[#155A6B] dark:hover:text-teal-400 transition-colors">
                                                <i class='bx bx-edit text-lg'></i>
                                            </a>
                                            <form action="{{ route('supplier.products.destroy', $product->id) }}" method="POST"
                                                class="inline" onsubmit="return confirm('Hapus produk ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 text-zinc-400 hover:text-rose-500 transition-colors">
                                                    <i class='bx bx-trash text-lg'></i>
                                                </button>
                                            </form>
                                        @else
                                            <span class="p-1.5 text-zinc-300 dark:text-zinc-600" title="Produk sudah disetujui, dikunci oleh admin">
                                                <i class='bx bx-lock text-lg'></i>
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex items-center justify-between mt-3 text-xs">
                                    <span class="font-extrabold text-zinc-900 dark:text-white">
                                        Rp {{ number_format($product->buyPrice, 0, ',', '.') }}
                                    </span>
                                    
                                    @if($product->approvalStatus === 'PENDING')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 dark:bg-amber-950/50 dark:text-amber-300 border border-amber-200 dark:border-amber-800/50">
                                            <i class='bx bx-time-five'></i> Menunggu
                                        </span>
                                    @elseif($product->approvalStatus === 'REJECTED')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 dark:bg-rose-950/50 dark:text-rose-300 border border-rose-200 dark:border-rose-800/50">
                                            <i class='bx bx-x-circle'></i> Ditolak
                                        </span>
                                    @elseif($product->approvalStatus === 'APPROVED')
                                        @if($product->stock > 0)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-teal-50 text-[#155A6B] dark:bg-teal-950/50 dark:text-teal-300 border border-teal-200 dark:border-teal-800/50">
                                                <i class='bx bx-store'></i> Stok: {{ $product->stock }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400 border border-zinc-200 dark:border-zinc-700">
                                                Stok Habis
                                            </span>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-10 text-center text-zinc-400">
                        <i class='bx bx-box text-5xl mb-2 text-zinc-300 dark:text-zinc-700'></i>
                        <p class="text-xs font-semibold">Belum ada produk diajukan.</p>
                        <a href="{{ route('supplier.products.create') }}" class="text-[#155A6B] dark:text-teal-400 font-bold hover:underline mt-1 inline-block text-xs">
                            Ajukan Produk Pertama Anda
                        </a>
                    </div>
                @endforelse
            </div>

            {{-- Desktop Table View --}}
            <div class="hidden sm:block overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-zinc-50/80 dark:bg-zinc-800/40 border-b border-zinc-200/80 dark:border-zinc-800/80">
                        <tr>
                            <th class="px-6 py-3.5 text-[10px] font-extrabold text-zinc-400 uppercase tracking-wider">Produk</th>
                            <th class="px-6 py-3.5 text-[10px] font-extrabold text-zinc-400 uppercase tracking-wider">Kategori</th>
                            <th class="px-6 py-3.5 text-[10px] font-extrabold text-zinc-400 uppercase tracking-wider">Harga per Unit</th>
                            <th class="px-6 py-3.5 text-[10px] font-extrabold text-zinc-400 uppercase tracking-wider text-center">Stok Minimarket</th>
                            <th class="px-6 py-3.5 text-[10px] font-extrabold text-zinc-400 uppercase tracking-wider text-center">Performa</th>
                            <th class="px-6 py-3.5 text-[10px] font-extrabold text-zinc-400 uppercase tracking-wider text-center">Status</th>
                            <th class="px-6 py-3.5 text-[10px] font-extrabold text-zinc-400 uppercase tracking-wider text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200/60 dark:divide-zinc-800/60 text-xs">
                        @forelse($products as $product)
                            @php
                                $canModify = in_array($product->approvalStatus, ['PENDING', 'REJECTED'], true);
                                $totalSold = (int) ($product->total_sold_qty ?? 0);
                                $totalReturned = (int) ($product->total_returned_qty ?? 0);
                            @endphp
                            <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        @if($product->image)
                                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
                                                class="w-11 h-11 rounded-xl object-cover shrink-0 shadow-sm">
                                        @else
                                            <div class="w-11 h-11 rounded-xl bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-zinc-400 shrink-0">
                                                <i class='bx bx-image text-xl'></i>
                                            </div>
                                        @endif
                                        <div>
                                            <h6 class="font-bold text-zinc-900 dark:text-white">{{ $product->name }}</h6>
                                            <p class="text-[10px] font-extrabold text-[#155A6B] dark:text-teal-400 font-mono mt-0.5">{{ $product->sku }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-semibold text-zinc-600 dark:text-zinc-400">
                                    {{ $product->category->name ?? '-' }}
                                </td>
                                <td class="px-6 py-4 font-extrabold text-zinc-900 dark:text-white">
                                    Rp {{ number_format($product->buyPrice, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($product->approvalStatus === 'APPROVED')
                                        @if($product->stock > 0)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-extrabold {{ $product->stock > 10 ? 'bg-teal-50 text-[#155A6B] dark:bg-teal-950/50 dark:text-teal-300 border border-teal-200 dark:border-teal-800/50' : 'bg-amber-50 text-amber-700 dark:bg-amber-950/50 dark:text-amber-300 border border-amber-200 dark:border-amber-800/50' }}">
                                                {{ $product->stock }} pcs
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-zinc-100 text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400">
                                                0 pcs
                                            </span>
                                        @endif
                                    @else
                                        <span class="text-zinc-400 font-medium text-xs">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($totalSold > 0 || $totalReturned > 0)
                                        <div class="flex items-center justify-center gap-2 text-xs font-semibold">
                                            @if($totalSold > 0)
                                                <span class="text-teal-600 dark:text-teal-400 flex items-center gap-1">
                                                    <i class='bx bx-check-circle'></i> {{ $totalSold }}
                                                </span>
                                            @endif
                                            @if($totalReturned > 0)
                                                @if($totalSold > 0)
                                                    <span class="text-zinc-300 dark:text-zinc-700">•</span>
                                                @endif
                                                <span class="text-rose-600 dark:text-rose-400 flex items-center gap-1">
                                                    <i class='bx bx-undo'></i> {{ $totalReturned }}
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-zinc-400 font-medium text-xs">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($product->approvalStatus === 'PENDING')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 dark:bg-amber-950/50 dark:text-amber-300 border border-amber-200 dark:border-amber-800/50">
                                            <i class='bx bx-time-five text-sm'></i> Menunggu Review
                                        </span>
                                    @elseif($product->approvalStatus === 'REJECTED')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-50 text-rose-700 dark:bg-rose-950/50 dark:text-rose-300 border border-rose-200 dark:border-rose-800/50 cursor-help"
                                            title="Alasan: {{ $product->rejectionReason ?? 'Tidak ada alasan' }}">
                                            <i class='bx bx-x-circle text-sm'></i> Ditolak
                                        </span>
                                    @elseif($product->approvalStatus === 'APPROVED')
                                        @if($product->stock > 0)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-teal-50 text-[#155A6B] dark:bg-teal-950/50 dark:text-teal-300 border border-teal-200 dark:border-teal-800/50">
                                                <i class='bx bx-check text-sm'></i> Disetujui
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400 border border-zinc-200 dark:border-zinc-700">
                                                <i class='bx bx-info-circle text-sm'></i> Stok Habis
                                            </span>
                                        @endif
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400">
                                            Non-aktif
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        @if($canModify)
                                            <a href="{{ route('supplier.products.edit', $product->id) }}"
                                                class="p-1.5 rounded-lg text-zinc-400 hover:text-[#155A6B] dark:hover:text-teal-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors">
                                                <i class='bx bx-edit text-lg'></i>
                                            </a>
                                            <form action="{{ route('supplier.products.destroy', $product->id) }}" method="POST"
                                                class="inline"
                                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 rounded-lg text-zinc-400 hover:text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition-colors">
                                                    <i class='bx bx-trash text-lg'></i>
                                                </button>
                                            </form>
                                        @else
                                            <span class="p-1.5 text-zinc-300 dark:text-zinc-700 cursor-not-allowed" title="Produk sudah disetujui, dikunci oleh admin">
                                                <i class='bx bx-lock text-lg'></i>
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-zinc-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <i class='bx bx-box text-5xl mb-2 text-zinc-300 dark:text-zinc-700'></i>
                                        <p class="text-xs font-semibold">Belum ada produk diajukan.</p>
                                        <a href="{{ route('supplier.products.create') }}" class="text-[#155A6B] dark:text-teal-400 font-bold hover:underline mt-1 text-xs">
                                            Ajukan Produk Pertama Anda
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($products->hasPages())
                <div class="px-6 py-4 border-t border-zinc-200/60 dark:border-zinc-800/60">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
