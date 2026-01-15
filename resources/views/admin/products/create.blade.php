@extends('layouts.admin')

@section('content')
    <div class="max-w-3xl">
        {{-- Header --}}
        <div class="mb-6">
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route('admin.products') }}"
                    class="text-slate-400 hover:text-slate-600 dark:hover:text-white transition-colors">
                    <i class='bx bx-arrow-back text-2xl'></i>
                </a>
                <h1 class="text-xl font-bold text-slate-900 dark:text-white">Tambah Produk Baru</h1>
            </div>
            <p class="text-[11px] text-slate-500 ml-11">Lengkapi informasi produk di bawah ini.</p>
        </div>

        {{-- Form --}}
        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data"
            class="bg-white dark:bg-darkCard rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 p-6 space-y-6">
            @csrf

            {{-- Basic Info --}}
            <div class="space-y-4">
                <h3 class="text-sm font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wide">Informasi Dasar
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Nama Produk <span
                                class="text-rose-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                            class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 text-slate-900 dark:text-white text-sm rounded-lg px-4 py-2.5 outline-none focus:ring-2 focus:ring-primary transition-all @error('name') border-rose-500 @enderror"
                            placeholder="Contoh: Indomie Goreng">
                        @error('name')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">SKU <span
                                class="text-rose-500">*</span></label>
                        <input type="text" name="sku" value="{{ old('sku') }}" required
                            class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 text-slate-900 dark:text-white text-sm rounded-lg px-4 py-2.5 outline-none focus:ring-2 focus:ring-primary transition-all @error('sku') border-rose-500 @enderror"
                            placeholder="Contoh: FD-001">
                        @error('sku')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Kategori <span
                            class="text-rose-500">*</span></label>
                    <select name="categoryId" required
                        class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 text-slate-900 dark:text-white text-sm rounded-lg px-4 py-2.5 outline-none focus:ring-2 focus:ring-primary transition-all @error('categoryId') border-rose-500 @enderror">
                        <option value="">Pilih Kategori</option>
                        @foreach(App\Models\Category::orderBy('name')->get() as $category)
                            <option value="{{ $category->id }}" {{ old('categoryId') == $category->id ? 'selected' : '' }}>
                                {{ $category->icon }} {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('categoryId')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Deskripsi</label>
                    <textarea name="description" rows="3"
                        class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 text-slate-900 dark:text-white text-sm rounded-lg px-4 py-2.5 outline-none focus:ring-2 focus:ring-primary transition-all resize-none"
                        placeholder="Deskripsi produk (opsional)">{{ old('description') }}</textarea>
                </div>
            </div>

            {{-- Pricing --}}
            <div class="space-y-4 pt-4 border-t border-slate-100 dark:border-slate-700">
                <h3 class="text-sm font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wide">Harga & Stok</h3>

                <div class="space-y-4" x-data="{
                            mode: 'manual',
                            buyDisplay: '{{ old('buyPrice') ? number_format(old('buyPrice'), 0, ',', '.') : '' }}',
                            buyRaw: '{{ old('buyPrice', '') }}',
                            sellDisplay: '{{ old('sellPrice') ? number_format(old('sellPrice'), 0, ',', '.') : '' }}',
                            sellRaw: '{{ old('sellPrice', '') }}',
                            percent: 30,

                            formatBuy(e) {
                                let val = e.target.value.replace(/\D/g, '');
                                this.buyRaw = val;
                                this.buyDisplay = val ? new Intl.NumberFormat('id-ID').format(val) : '';
                                this.recalculate();
                            },
                            formatSell(e) {
                                let val = e.target.value.replace(/\D/g, '');
                                this.sellRaw = val;
                                this.sellDisplay = val ? new Intl.NumberFormat('id-ID').format(val) : '';
                            },
                            recalculate() {
                                if (!this.buyRaw || parseInt(this.buyRaw) === 0) return;
                                let buy = parseInt(this.buyRaw);
                                let sell = 0;

                                if (this.mode === 'markup') {
                                    sell = Math.round(buy * (1 + this.percent / 100));
                                } else if (this.mode === 'profit') {
                                    if (this.percent >= 100) return;
                                    sell = Math.round(buy / (1 - this.percent / 100));
                                } else {
                                    return;
                                }

                                this.sellRaw = sell.toString();
                                this.sellDisplay = new Intl.NumberFormat('id-ID').format(sell);
                            },
                            setMode(m) {
                                this.mode = m;
                                if (m !== 'manual') this.recalculate();
                            },
                            get profit() {
                                if (!this.sellRaw || !this.buyRaw) return null;
                                return parseInt(this.sellRaw) - parseInt(this.buyRaw);
                            },
                            get marginPercent() {
                                if (!this.buyRaw || parseInt(this.buyRaw) === 0) return null;
                                return ((parseInt(this.sellRaw) - parseInt(this.buyRaw)) / parseInt(this.buyRaw) * 100).toFixed(1);
                            },
                            get isLoss() {
                                return this.profit !== null && this.profit < 0;
                            },
                            get hasValidPrices() {
                                return this.sellRaw && this.buyRaw && parseInt(this.buyRaw) > 0;
                            }
                        }">

                    {{-- Mode Switcher --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Mode
                            Penghitungan</label>
                        <div class="flex gap-2">
                            <button type="button" @click="setMode('manual')"
                                class="flex-1 py-2 px-3 text-xs font-medium rounded-lg border-2 transition-all"
                                :class="mode === 'manual' ? 'bg-primary text-white border-primary' : 'bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-600 hover:border-primary'">
                                ✏️ Manual
                            </button>
                            <button type="button" @click="setMode('markup')"
                                class="flex-1 py-2 px-3 text-xs font-medium rounded-lg border-2 transition-all"
                                :class="mode === 'markup' ? 'bg-primary text-white border-primary' : 'bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-600 hover:border-primary'">
                                📈 Markup %
                            </button>
                            <button type="button" @click="setMode('profit')"
                                class="flex-1 py-2 px-3 text-xs font-medium rounded-lg border-2 transition-all"
                                :class="mode === 'profit' ? 'bg-primary text-white border-primary' : 'bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-600 hover:border-primary'">
                                💰 Profit %
                            </button>
                        </div>
                        <p class="text-[10px] text-slate-400 mt-1.5" x-show="mode === 'manual'">Input harga jual secara
                            langsung</p>
                        <p class="text-[10px] text-slate-400 mt-1.5" x-show="mode === 'markup'">Hitung dari % tambahan harga
                            beli (markup dari modal)</p>
                        <p class="text-[10px] text-slate-400 mt-1.5" x-show="mode === 'profit'">Hitung dari % keuntungan
                            target (profit dari harga jual)</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Harga Beli --}}
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Harga Beli
                                (Modal)</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 text-sm">Rp</span>
                                <input type="text" x-model="buyDisplay" @input="formatBuy($event)"
                                    class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 text-slate-900 dark:text-white text-sm rounded-lg pl-11 pr-4 py-2.5 outline-none focus:ring-2 focus:ring-primary transition-all"
                                    placeholder="0">
                                <input type="hidden" name="buyPrice" :value="buyRaw">
                            </div>
                        </div>

                        {{-- Percentage Input (for Markup/Profit modes) --}}
                        <div x-show="mode !== 'manual'" x-transition>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                                <span x-text="mode === 'markup' ? 'Markup' : 'Profit Target'"></span> (%)
                            </label>
                            <div class="relative">
                                <input type="number" x-model="percent" @input="recalculate()" min="1" max="99" step="1"
                                    class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 text-slate-900 dark:text-white text-sm rounded-lg px-4 py-2.5 outline-none focus:ring-2 focus:ring-primary transition-all"
                                    placeholder="30">
                                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm">%</span>
                            </div>
                        </div>
                    </div>

                    {{-- Harga Jual --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            Harga Jual <span class="text-rose-500">*</span>
                            <span x-show="mode !== 'manual'"
                                class="text-[10px] text-slate-400 ml-1">(auto-calculated)</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 text-sm">Rp</span>
                            <input type="text" x-model="sellDisplay" @input="formatSell($event)" required
                                :readonly="mode !== 'manual'"
                                class="w-full border text-sm rounded-lg pl-11 pr-4 py-2.5 outline-none focus:ring-2 focus:ring-primary transition-all @error('sellPrice') border-rose-500 @enderror"
                                :class="[
                                        mode !== 'manual' ? 'bg-slate-100 dark:bg-slate-700 cursor-not-allowed' : 'bg-slate-50 dark:bg-slate-800',
                                        isLoss ? 'border-rose-500 ring-2 ring-rose-500/20 text-rose-600' : 'border-slate-200 dark:border-slate-600 text-slate-900 dark:text-white'
                                    ]" placeholder="0">
                            <input type="hidden" name="sellPrice" :value="sellRaw">
                        </div>
                        @error('sellPrice')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Profit & Margin Display --}}
                    <template x-if="hasValidPrices">
                        <div class="p-3 rounded-lg"
                            :class="isLoss ? 'bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-700' : 'bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-700'">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <i class='bx text-lg'
                                        :class="isLoss ? 'bx-trending-down text-rose-500' : 'bx-trending-up text-emerald-500'"></i>
                                    <span class="text-xs font-medium"
                                        :class="isLoss ? 'text-rose-700 dark:text-rose-400' : 'text-emerald-700 dark:text-emerald-400'">
                                        <span x-text="isLoss ? 'Rugi' : 'Untung'"></span>:
                                        <span class="font-bold">Rp <span
                                                x-text="new Intl.NumberFormat('id-ID').format(Math.abs(profit))"></span></span>
                                        <span class="ml-1">(<span x-text="marginPercent"></span>% dari modal)</span>
                                    </span>
                                </div>
                                <span x-show="isLoss" class="text-[10px] text-rose-500 dark:text-rose-400">
                                    Yakin mau rugi? 😅
                                </span>
                                <span x-show="!isLoss" class="text-[10px] text-emerald-500 dark:text-emerald-400">
                                    Margin sehat 👍
                                </span>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Stok Awal --}}
                    <div x-data="{
                                    display: '{{ old('stock') ? number_format(old('stock'), 0, ',', '.') : '' }}',
                                    raw: '{{ old('stock', '') }}',
                                    format(e) {
                                        let val = e.target.value.replace(/\D/g, '');
                                        this.raw = val;
                                        this.display = val ? new Intl.NumberFormat('id-ID').format(val) : '';
                                    }
                                }">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Stok Awal <span
                                class="text-rose-500">*</span></label>
                        <input type="text" x-model="display" @input="format($event)" required
                            class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 text-slate-900 dark:text-white text-sm rounded-lg px-4 py-2.5 outline-none focus:ring-2 focus:ring-primary transition-all @error('stock') border-rose-500 @enderror"
                            placeholder="0">
                        <input type="hidden" name="stock" :value="raw">
                        @error('stock')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Ambang Batas Stok
                            <span class="text-rose-500">*</span></label>
                        <input type="number" name="threshold" value="{{ old('threshold', 10) }}" required min="1"
                            class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 text-slate-900 dark:text-white text-sm rounded-lg px-4 py-2.5 outline-none focus:ring-2 focus:ring-primary transition-all @error('threshold') border-rose-500 @enderror"
                            placeholder="10">
                        @error('threshold')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-slate-400 mt-1">Peringatan stok menipis akan muncul saat stok ≤ nilai ini</p>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-700">
                <a href="{{ route('admin.products') }}"
                    class="px-6 py-2.5 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-600 font-medium rounded-lg text-sm transition-colors">
                    Batal
                </a>
                <button type="submit"
                    class="px-6 py-2.5 bg-primary hover:bg-indigo-700 text-white font-bold rounded-lg text-sm shadow-lg shadow-indigo-500/20 transition-all flex items-center gap-2">
                    <i class='bx bx-save'></i> Simpan Produk
                </button>
            </div>
        </form>
    </div>
@endsection