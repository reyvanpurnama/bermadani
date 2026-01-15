<div class="space-y-6">

    {{-- Tab Filter --}}
    <div class="flex border-b border-slate-200 dark:border-slate-700">
        <button wire:click="setStatus('PENDING')"
            class="px-4 py-2 text-[13px] {{ $status === 'PENDING' ? 'font-bold text-primary border-b-2 border-primary' : 'font-medium text-slate-500 hover:text-slate-700 dark:text-slate-400 transition-colors' }}">
            Pending Review
            @if($counts['pending'] > 0)
                <span class="ml-1 bg-indigo-100 text-indigo-700 px-1.5 rounded text-[10px]">{{ $counts['pending'] }}</span>
            @endif
        </button>
        <button wire:click="setStatus('APPROVED')"
            class="px-4 py-2 text-[13px] {{ $status === 'APPROVED' ? 'font-bold text-primary border-b-2 border-primary' : 'font-medium text-slate-500 hover:text-slate-700 dark:text-slate-400 transition-colors' }}">
            Disetujui
        </button>
        <button wire:click="setStatus('REJECTED')"
            class="px-4 py-2 text-[13px] {{ $status === 'REJECTED' ? 'font-bold text-primary border-b-2 border-primary' : 'font-medium text-slate-500 hover:text-slate-700 dark:text-slate-400 transition-colors' }}">
            Ditolak
        </button>
    </div>

    {{-- Table --}}
    <div class="bg-white dark:bg-darkCard rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600 dark:text-slate-400">
                <thead class="bg-slate-50 dark:bg-slate-800/50 text-[10px] uppercase font-bold text-slate-500 dark:text-slate-400 tracking-widest">
                    <tr>
                        <th class="px-5 py-3">Produk</th>
                        <th class="px-5 py-3">Supplier</th>
                        <th class="px-5 py-3">Harga Pengajuan</th>
                        <th class="px-5 py-3">Stok</th>
                        <th class="px-5 py-3">Tanggal</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700 text-[13px]">
                    @forelse($products as $product)
                        <tr wire:click="openDetail({{ $product->id }})" class="hover:bg-indigo-50/30 dark:hover:bg-indigo-900/10 transition-colors group cursor-pointer">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded bg-slate-100 dark:bg-slate-700 flex items-center justify-center text-xl shrink-0">
                                        📦
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <h6 class="font-bold text-slate-900 dark:text-white leading-none">{{ $product->name }}</h6>
                                            @if($product->approvalStatus === 'PENDING' && $product->created_at->diffInHours() < 24)
                                                <span class="w-2 h-2 rounded-full bg-rose-500 animate-pulse" title="Baru"></span>
                                            @endif
                                        </div>
                                        <p class="text-[10px] text-slate-400 mt-1">{{ $product->sku }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                @if($product->supplier)
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-[10px] font-bold">
                                        {{ strtoupper(substr($product->supplier->businessName ?? 'S', 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-slate-800 dark:text-white leading-none">{{ $product->supplier->businessName }}</p>
                                        <p class="text-[10px] text-slate-400">{{ $product->supplier->supplierType ?? 'Supplier' }}</p>
                                    </div>
                                </div>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 font-medium text-slate-800 dark:text-white">
                                @if($status === 'PENDING')
                                    <div class="text-[13px]">
                                        <div class="text-slate-500 text-[11px]">Harga Ajuan</div>
                                        <div class="font-bold">Rp {{ number_format($product->buyPrice ?? 0, 0, ',', '.') }}</div>
                                    </div>
                                @else
                                    Rp {{ number_format($product->sellPrice ?? 0, 0, ',', '.') }}
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                {{ $product->stock }} Pcs
                            </td>
                            <td class="px-5 py-4 text-slate-500">
                                {{ $product->created_at->diffForHumans() }}
                            </td>
                            <td class="px-5 py-4 text-right">
                                @if($status === 'PENDING')
                                    <button class="text-indigo-600 hover:text-indigo-800 font-bold text-[12px] bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 rounded-lg transition-colors">
                                        Review
                                    </button>
                                @elseif($status === 'APPROVED')
                                    <span class="text-emerald-600 text-[11px] font-bold flex items-center justify-end gap-1">
                                        <i class='bx bx-check-circle'></i> Disetujui
                                    </span>
                                @else
                                    <span class="text-rose-500 text-[11px] font-bold flex items-center justify-end gap-1">
                                        <i class='bx bx-x-circle'></i> Ditolak
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-slate-500">
                                <div class="flex flex-col items-center">
                                    <i class='bx bx-package text-4xl mb-2 text-slate-300'></i>
                                    <p>Tidak ada produk dengan status ini.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($products->hasPages())
        <div class="px-5 py-3 border-t border-slate-100 dark:border-slate-700">
            {{ $products->links() }}
        </div>
        @endif
    </div>

    {{-- Detail Modal --}}
    @if($showModal && $selectedProduct)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4 overflow-y-auto">
        <div class="bg-white dark:bg-darkCard rounded-xl shadow-2xl w-full max-w-3xl overflow-hidden animate-fade-in-up my-8">
            
            {{-- Header --}}
            <div class="flex justify-between items-center px-6 py-4 border-b border-slate-200 dark:border-slate-700">
                <h3 class="font-bold text-lg text-slate-800 dark:text-white">Detail Pengajuan #{{ $selectedProduct->id }}</h3>
                <button wire:click="closeModal" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <i class='bx bx-x text-2xl'></i>
                </button>
            </div>

            <div class="p-6 grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                {{-- Left: Product Info --}}
                <div class="lg:col-span-2 space-y-4">
                    <div class="flex gap-4">
                        <div class="w-24 h-24 bg-slate-100 dark:bg-slate-700 rounded-lg flex items-center justify-center text-5xl shadow-inner shrink-0">
                            📦
                        </div>
                        <div class="flex-1">
                            <div class="flex justify-between items-start mb-2">
                                <h2 class="text-lg font-bold text-slate-900 dark:text-white">{{ $selectedProduct->name }}</h2>
                                <span class="bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide">
                                    {{ $selectedProduct->approvalStatus }}
                                </span>
                            </div>
                            <div class="flex gap-2 mb-3 flex-wrap">
                                <span class="text-[11px] bg-slate-100 dark:bg-slate-700 px-2 py-1 rounded text-slate-600 dark:text-slate-300">{{ $selectedProduct->sku }}</span>
                                <span class="text-[11px] bg-slate-100 dark:bg-slate-700 px-2 py-1 rounded text-slate-600 dark:text-slate-300">Stok: {{ $selectedProduct->stock }} Pcs</span>
                            </div>
                            <p class="text-[13px] text-slate-500 dark:text-slate-400 leading-relaxed">
                                {{ $selectedProduct->description ?? 'Tidak ada deskripsi.' }}
                            </p>
                        </div>
                    </div>

                    {{-- Supplier Info --}}
                    @if($selectedProduct->supplier)
                    <div class="bg-slate-50 dark:bg-slate-800 p-4 rounded-lg flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold">
                                {{ strtoupper(substr($selectedProduct->supplier->businessName ?? 'S', 0, 1)) }}
                            </div>
                            <div>
                                <h5 class="text-[13px] font-bold text-slate-800 dark:text-white">{{ $selectedProduct->supplier->businessName }}</h5>
                                <p class="text-[11px] text-slate-500">{{ $selectedProduct->supplier->supplierType ?? 'Supplier' }}</p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Right: Price Analysis & Actions --}}
                <div class="lg:col-span-1 space-y-4">
                    
                    {{-- Price Section --}}
                    <div class="bg-white dark:bg-slate-800/50 p-4 rounded-lg border border-slate-200 dark:border-slate-700">
                        <h3 class="text-[13px] font-bold text-slate-800 dark:text-white mb-3">Analisa Harga</h3>
                        
                        <div class="space-y-3">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-500 mb-1.5">Harga Jual</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-3 flex items-center text-[13px] text-slate-400">Rp</span>
                                    <input wire:model.live="sellPrice" type="text" id="sellPriceInput"
                                        placeholder="0"
                                        class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-lg pl-9 pr-3 py-2 text-[14px] font-bold text-slate-800 dark:text-white outline-none focus:border-primary"
                                        {{ $status !== 'PENDING' ? 'disabled' : '' }}
                                        x-data="{
                                            formatRupiah(value) {
                                                let number = value.replace(/[^0-9]/g, '');
                                                if (number === '') return '';
                                                return number.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                                            }
                                        }"
                                        x-on:input="
                                            let input = $el;
                                            let cursorPos = input.selectionStart;
                                            let oldLength = input.value.length;
                                            let formatted = formatRupiah(input.value);
                                            input.value = formatted;
                                            @this.set('sellPrice', input.value.replace(/\./g, ''));
                                            let newLength = formatted.length;
                                            let diff = newLength - oldLength;
                                            input.setSelectionRange(cursorPos + diff, cursorPos + diff);
                                        ">
                                </div>
                            </div>

                            <div class="flex justify-between items-center text-[12px]">
                                <span class="text-slate-500">Harga Beli</span>
                                <span class="font-bold text-slate-700 dark:text-slate-300">Rp {{ number_format($selectedProduct->buyPrice ?? 0, 0, ',', '.') }}</span>
                            </div>

                            @php
                                $margin = ($sellPrice && $selectedProduct->buyPrice && $selectedProduct->buyPrice > 0) 
                                    ? (($sellPrice - $selectedProduct->buyPrice) / $selectedProduct->buyPrice) * 100 
                                    : 0;
                                $marginAmount = $sellPrice - ($selectedProduct->buyPrice ?? 0);
                            @endphp
                            <div class="flex justify-between items-center text-[12px]">
                                <span class="text-slate-500">Margin</span>
                                <span class="font-bold {{ $margin > 15 ? 'text-emerald-600' : ($margin > 5 ? 'text-amber-600' : 'text-rose-600') }}">
                                    {{ number_format($margin, 1) }}% (Rp {{ number_format($marginAmount, 0, ',', '.') }})
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Action Buttons (Only for Pending) --}}
                    @if($status === 'PENDING')
                    <div class="space-y-3">
                        <button wire:click="approve" 
                            class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-bold text-[13px] shadow-lg shadow-emerald-500/20 transition-all flex items-center justify-center gap-2">
                            <i class='bx bx-check-circle text-lg'></i> Setujui & Masukkan Stok
                        </button>
                        
                        <button wire:click="reject" 
                            class="w-full py-3 bg-white dark:bg-slate-800 border border-rose-200 dark:border-rose-900/50 text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-900/20 rounded-lg font-bold text-[13px] transition-all flex items-center justify-center gap-2">
                            <i class='bx bx-x-circle text-lg'></i> Tolak Pengajuan
                        </button>

                        <div>
                            <label class="block text-[11px] font-bold text-slate-500 mb-1.5">Catatan Admin (Wajib untuk Tolak)</label>
                            <textarea wire:model="adminNote" rows="2" 
                                class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-lg p-2 text-[12px] outline-none focus:border-primary placeholder-slate-400" 
                                placeholder="Alasan penolakan atau catatan revisi..."></textarea>
                            @error('adminNote') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    @else
                        {{-- Show rejection reason if rejected --}}
                        @if($status === 'REJECTED' && $selectedProduct->rejectionReason)
                        <div class="bg-rose-50 dark:bg-rose-900/20 p-3 rounded-lg border border-rose-100 dark:border-rose-800/30">
                            <p class="text-[11px] font-bold text-rose-600 mb-1">Alasan Penolakan:</p>
                            <p class="text-[12px] text-rose-700 dark:text-rose-300">{{ $selectedProduct->rejectionReason }}</p>
                        </div>
                        @endif
                    @endif
                </div>

            </div>
        </div>
    </div>
    @endif

</div>
