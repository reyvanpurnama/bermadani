<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center gap-3 mb-2">
        <a href="{{ route('admin.products') }}" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-500 transition-colors">
            <i class='bx bx-arrow-back text-xl'></i>
        </a>
        <div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-white">Approval Produk</h1>
            <p class="text-[11px] text-slate-500 mt-0.5">Tinjau pengajuan produk baru dari supplier.</p>
        </div>
    </div>

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
                                    @if($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-10 h-10 rounded object-cover shrink-0">
                                    @else
                                        <div class="w-10 h-10 rounded bg-slate-100 dark:bg-slate-700 flex items-center justify-center text-xl shrink-0">
                                            📦
                                        </div>
                                    @endif
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
                                @if($status === 'APPROVED' && $product->approvedAt)
                                    <div class="text-[11px]">
                                        <span class="text-emerald-600">Disetujui</span>
                                        <span class="block">{{ $product->approvedAt->diffForHumans() }}</span>
                                    </div>
                                @elseif($status === 'REJECTED' && $product->updated_at)
                                    <div class="text-[11px]">
                                        <span class="text-rose-500">Ditolak</span>
                                        <span class="block">{{ $product->updated_at->diffForHumans() }}</span>
                                    </div>
                                @else
                                    <div class="text-[11px]">
                                        <span class="text-slate-400">Diajukan</span>
                                        <span class="block">{{ $product->created_at->diffForHumans() }}</span>
                                    </div>
                                @endif
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
            <div class="flex justify-between items-center px-4 sm:px-6 py-3 sm:py-4 border-b border-slate-200 dark:border-slate-700">
                <h3 class="font-bold text-base sm:text-lg text-slate-800 dark:text-white">Detail Pengajuan #{{ $selectedProduct->id }}</h3>
                <button wire:click="closeModal" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <i class='bx bx-x text-2xl'></i>
                </button>
            </div>

            <div class="p-4 sm:p-6 grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6 max-h-[70vh] sm:max-h-[75vh] overflow-y-auto">
                
                {{-- Left: Product Info --}}
                <div class="lg:col-span-2 space-y-4">
                    <div class="flex flex-col sm:flex-row gap-4">
                        @if($selectedProduct->image)
                            <img src="{{ asset('storage/' . $selectedProduct->image) }}" alt="{{ $selectedProduct->name }}" class="w-full sm:w-24 h-48 sm:h-24 rounded-lg object-cover shadow-md shrink-0">
                        @else
                            <div class="w-full sm:w-24 h-48 sm:h-24 bg-slate-100 dark:bg-slate-700 rounded-lg flex items-center justify-center text-5xl shadow-inner shrink-0">
                                📦
                            </div>
                        @endif
                        <div class="flex-1">
                            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-2 mb-2">
                                <h2 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white">{{ $selectedProduct->name }}</h2>
                                <span class="bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide w-fit">
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
                {{-- Right: Analysis & Decision --}}
                <div class="lg:col-span-1 space-y-5" 
                    x-data="{
                        buyRaw: '{{ floatval($selectedProduct->buyPrice ?? 0) }}',
                        sellDisplay: '{{ $sellPrice ? number_format($sellPrice, 0, ',', '.') : '' }}',
                        sellRaw: '{{ $sellPrice ?? '' }}',
                        markupPercent: '',
                        profitPercent: '',
                        showRejectReason: false,

                        init() {
                            if (this.buyRaw && this.sellRaw) {
                                this.calculatePercentages();
                            }
                        },

                        formatRupiah(value) {
                            let number = String(value).replace(/[^0-9]/g, '');
                            if (number === '') return '';
                            return new Intl.NumberFormat('id-ID').format(number);
                        },

                        formatSell(e) {
                            let val = e.target.value.replace(/\D/g, '');
                            this.sellRaw = val;
                            this.sellDisplay = val ? this.formatRupiah(val) : '';
                            this.calculatePercentages();
                        },
                        
                        syncToServer() {
                            @this.set('sellPrice', this.sellRaw);
                        },

                        updateFromMarkup() {
                            if (!this.buyRaw || !this.markupPercent) return;
                            let buy = parseFloat(this.buyRaw);
                            let markup = parseFloat(this.markupPercent);
                            let sell = Math.round(buy * (1 + markup / 100));

                            this.sellRaw = sell.toString();
                            this.sellDisplay = this.formatRupiah(sell);
                            this.calculatePercentages();
                            this.syncToServer();
                            
                            if (sell !== 0) {
                                this.profitPercent = ((sell - buy) / sell * 100).toFixed(2);
                            }
                        },

                        updateFromProfit() {
                            if (!this.buyRaw || !this.profitPercent) return;
                            if (parseFloat(this.profitPercent) >= 100) return;

                            let buy = parseFloat(this.buyRaw);
                            let profit = parseFloat(this.profitPercent);
                            let sell = Math.round(buy / (1 - profit / 100));

                            this.sellRaw = sell.toString();
                            this.sellDisplay = this.formatRupiah(sell);
                            this.calculatePercentages();
                            this.syncToServer();

                            this.markupPercent = ((sell - buy) / buy * 100).toFixed(2);
                        },

                        calculatePercentages() {
                            if (!this.buyRaw || !this.sellRaw || parseFloat(this.buyRaw) === 0) {
                                this.markupPercent = '';
                                this.profitPercent = '';
                                return;
                            }
                            let buy = parseFloat(this.buyRaw);
                            let sell = parseFloat(this.sellRaw);

                            this.markupPercent = ((sell - buy) / buy * 100).toFixed(2);
                            if (sell !== 0) {
                                this.profitPercent = ((sell - buy) / sell * 100).toFixed(2);
                            }
                        },

                        get calculatedProfit() {
                            if (!this.sellRaw || !this.buyRaw) return 0;
                            return parseFloat(this.sellRaw) - parseFloat(this.buyRaw);
                        },

                        get isLoss() {
                            return this.calculatedProfit < 0;
                        },

                        get marginHealth() {
                            if (!this.profitPercent) return 'neutral';
                            let margin = parseFloat(this.profitPercent);
                            if (margin < 0) return 'loss';
                            if (margin < 10) return 'warning';
                            return 'healthy';
                        },

                        get healthColor() {
                            switch(this.marginHealth) {
                                case 'loss': return 'bg-rose-500';
                                case 'warning': return 'bg-amber-500';
                                case 'healthy': return 'bg-emerald-500';
                                default: return 'bg-slate-300';
                            }
                        },

                        toggleReject() {
                            this.showRejectReason = !this.showRejectReason;
                        }
                    }">
                    
                    <h3 class="font-bold text-sm text-slate-800 dark:text-white border-b border-slate-100 dark:border-slate-700 pb-2">
                        Analisis & Keputusan
                    </h3>

                    @if($status === 'PENDING')
                        {{-- 1. Context: Supplier Data (Immutable) --}}
                        <div class="bg-slate-50 dark:bg-slate-800/50 p-4 rounded-xl border border-slate-200 dark:border-slate-700 text-center">
                            <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Harga Beli (Supplier)</h4>
                            <p class="text-2xl font-bold text-slate-700 dark:text-slate-200 font-mono">
                                Rp {{ number_format($selectedProduct->buyPrice ?? 0, 0, ',', '.') }}
                                <span class="text-base text-slate-400 font-normal">/ unit</span>
                            </p>
                        </div>

                        {{-- 2. Decision: Pricing Strategy --}}
                        <div class="space-y-4">
                            {{-- Input Harga Jual --}}
                            <div wire:ignore>
                                <label class="block text-[11px] font-bold text-indigo-600 dark:text-indigo-400 uppercase mb-1">Tetapkan Harga Jual</label>
                                <div class="relative group">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm font-bold text-slate-400 group-focus-within:text-indigo-500 transition-colors">Rp</span>
                                    <input type="text" 
                                        x-model="sellDisplay" 
                                        @input="formatSell($event)"
                                        @blur="syncToServer()"
                                        placeholder="0"
                                        class="w-full bg-white dark:bg-slate-900 border-2 border-indigo-100 dark:border-indigo-500/30 rounded-xl pl-10 pr-4 py-3 text-lg font-bold text-slate-800 dark:text-white outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all shadow-sm"
                                        :class="isLoss ? 'border-rose-300 text-rose-600 focus:border-rose-500 focus:ring-rose-500/20' : ''">
                                </div>

                                {{-- Simple Profit/Loss Text --}}
                                <div class="flex justify-between items-center mt-2 px-1">
                                    <span class="text-[11px] font-medium text-slate-500">Estimasi Laba per Unit:</span>
                                    <span class="text-sm font-bold" :class="isLoss ? 'text-rose-500' : 'text-emerald-600'">
                                        <span x-show="isLoss">-</span>Rp <span x-text="new Intl.NumberFormat('id-ID').format(Math.abs(calculatedProfit))"></span>
                                    </span>
                                </div>
                            </div>

                            {{-- Calculator Tools --}}
                            <div class="grid grid-cols-2 gap-3 bg-slate-50 dark:bg-slate-800/30 p-3 rounded-lg border border-slate-100 dark:border-slate-700/50">
                                {{-- Markup --}}
                                <div>
                                    <div class="relative">
                                        <input type="number" x-model="markupPercent" @input="updateFromMarkup()" step="0.1"
                                            class="w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-lg px-2 py-1.5 text-xs text-center font-bold text-slate-700 dark:text-slate-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none"
                                            placeholder="0">
                                        <span class="absolute right-2 top-1/2 -translate-y-1/2 text-[9px] text-slate-400 font-bold">%</span>
                                    </div>
                                    <p class="text-[9px] text-slate-400 text-center mt-1">Markup (Dari Harga Beli)</p>
                                </div>
                                {{-- Margin --}}
                                <div>
                                    <div class="relative">
                                        <input type="number" x-model="profitPercent" @input="updateFromProfit()" step="0.1"
                                            class="w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-lg px-2 py-1.5 text-xs text-center font-bold text-slate-700 dark:text-slate-300 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none"
                                            placeholder="0">
                                        <span class="absolute right-2 top-1/2 -translate-y-1/2 text-[9px] text-slate-400 font-bold">%</span>
                                    </div>
                                    <p class="text-[9px] text-slate-400 text-center mt-1">Margin (Dari Harga Jual)</p>
                                </div>
                            </div>
                        </div>

                        {{-- 3. Actions --}}
                        <div class="pt-4 border-t border-slate-100 dark:border-slate-700">
                            <div class="grid grid-cols-2 gap-3 mb-3">
                                <button wire:click="reject" 
                                    x-show="showRejectReason"
                                    class="col-span-2 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-lg font-bold text-[13px] shadow-lg shadow-rose-500/20 transition-all flex items-center justify-center gap-2">
                                    <i class='bx bx-paper-plane'></i> Kirim Penolakan
                                </button>

                                <button @click="toggleReject()" 
                                    x-show="!showRejectReason"
                                    class="py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 text-slate-600 dark:text-slate-300 hover:bg-rose-50 dark:hover:bg-rose-900/10 hover:text-rose-600 hover:border-rose-200 rounded-lg font-bold text-[13px] transition-all">
                                    Tolak
                                </button>
                                
                                <button wire:click="approve" 
                                    x-show="!showRejectReason"
                                    x-bind:disabled="!sellRaw || parseInt(sellRaw) <= 0"
                                    class="py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-bold text-[13px] shadow-lg shadow-indigo-500/20 transition-all flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                                    <i class='bx bx-check'></i> Setujui
                                </button>
                            </div>

                            {{-- Rejection Note (Toggled) --}}
                            <div x-show="showRejectReason" x-transition class="space-y-2">
                                <textarea wire:model="adminNote" rows="2" 
                                    class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-lg p-3 text-[13px] outline-none focus:border-rose-400 placeholder-slate-400 resize-none" 
                                    placeholder="Tulis alasan penolakan untuk supplier..."></textarea>
                                @error('adminNote') <span class="text-xs text-rose-500 block">{{ $message }}</span> @enderror
                                
                                <button @click="toggleReject()" class="text-[11px] text-slate-400 hover:text-slate-600 underline w-full text-center">Batal, kembali ke approval</button>
                            </div>
                        </div>

                    @else
                        {{-- Read-Only View for Approved/Rejected --}}
                        <div class="bg-indigo-50/50 dark:bg-indigo-900/10 p-5 rounded-xl border border-indigo-100 dark:border-indigo-800/30 text-center">
                            @if($status === 'APPROVED')
                                <div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center text-2xl mx-auto mb-3">
                                    <i class='bx bx-check'></i>
                                </div>
                                <h4 class="font-bold text-slate-800 dark:text-white">Produk Disetujui</h4>
                                <p class="text-[12px] text-slate-500 mt-1">Produk ini sudah aktif di katalog.</p>
                                <div class="mt-4 pt-4 border-t border-indigo-100 dark:border-indigo-800/30 grid grid-cols-2 gap-4 text-left">
                                    <div>
                                        <p class="text-[10px] text-slate-400 uppercase">Harga Beli</p>
                                        <p class="font-bold text-slate-700 dark:text-white">Rp {{ number_format($selectedProduct->buyPrice ?? 0, 0, ',', '.') }}</p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] text-slate-400 uppercase">Harga Jual</p>
                                        <p class="font-bold text-indigo-600 dark:text-indigo-400">Rp {{ number_format($sellPrice ?? 0, 0, ',', '.') }}</p>
                                    </div>
                                </div>
                            @else
                                <div class="w-12 h-12 bg-rose-100 text-rose-600 rounded-full flex items-center justify-center text-2xl mx-auto mb-3">
                                    <i class='bx bx-x'></i>
                                </div>
                                <h4 class="font-bold text-slate-800 dark:text-white">Produk Ditolak</h4>
                                <p class="text-[12px] text-slate-500 mt-1">Pengajuan ini telah ditolak.</p>
                                @if($selectedProduct->rejectionReason)
                                    <div class="mt-4 bg-white dark:bg-slate-800 p-3 rounded-lg border border-rose-100 dark:border-rose-900/30 text-left">
                                        <p class="text-[11px] text-rose-500 font-bold mb-1">Alasan:</p>
                                        <p class="text-[12px] text-slate-600 dark:text-slate-300 italic">"{{ $selectedProduct->rejectionReason }}"</p>
                                    </div>
                                @endif
                            @endif
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
    @endif

</div>
