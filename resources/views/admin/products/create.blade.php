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
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">SKU</label>
                        <input type="text" name="sku" value="{{ old('sku') }}"
                            class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 text-slate-900 dark:text-white text-sm rounded-lg px-4 py-2.5 outline-none focus:ring-2 focus:ring-primary transition-all @error('sku') border-rose-500 @enderror"
                            placeholder="Biarkan kosong untuk auto-generate">
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

                <div class="space-y-6" x-data="{
                                                    buyDisplay: '{{ old('buyPrice') ? number_format(old('buyPrice'), 0, ',', '.') : '' }}',
                                                    buyRaw: '{{ old('buyPrice', '') }}',
                                                    sellDisplay: '{{ old('sellPrice') ? number_format(old('sellPrice'), 0, ',', '.') : '' }}',
                                                    sellRaw: '{{ old('sellPrice', '') }}',
                                                    markupPercent: '',
                                                    profitPercent: '',

                                                    init() {
                                                        if (this.buyRaw && this.sellRaw) {
                                                            this.calculatePercentages();
                                                        }
                                                    },

                                                    formatBuy(e) {
                                                        let val = e.target.value.replace(/\D/g, '');
                                                        this.buyRaw = val;
                                                        this.buyDisplay = val ? new Intl.NumberFormat('id-ID').format(val) : '';
                                                        // If sell price exists, keep it fixed and recalculate percentages (margin changes)
                                                        if (this.sellRaw) {
                                                            this.calculatePercentages();
                                                        }
                                                    },

                                                    formatSell(e) {
                                                        let val = e.target.value.replace(/\D/g, '');
                                                        this.sellRaw = val;
                                                        this.sellDisplay = val ? new Intl.NumberFormat('id-ID').format(val) : '';
                                                        this.calculatePercentages();
                                                    },

                                                    updateFromMarkup() {
                                                        if (!this.buyRaw || !this.markupPercent) return;
                                                        let buy = parseInt(this.buyRaw);
                                                        let markup = parseFloat(this.markupPercent);
                                                        let sell = Math.round(buy * (1 + markup / 100));

                                                        this.sellRaw = sell.toString();
                                                        this.sellDisplay = new Intl.NumberFormat('id-ID').format(sell);

                                                        if (sell !== 0) {
                                                            this.profitPercent = ((sell - buy) / sell * 100).toFixed(2);
                                                        }
                                                    },

                                                    updateFromProfit() {
                                                        if (!this.buyRaw || !this.profitPercent) return;
                                                        if (parseFloat(this.profitPercent) >= 100) return;

                                                        let buy = parseInt(this.buyRaw);
                                                        let profit = parseFloat(this.profitPercent);
                                                        let sell = Math.round(buy / (1 - profit / 100));

                                                        this.sellRaw = sell.toString();
                                                        this.sellDisplay = new Intl.NumberFormat('id-ID').format(sell);

                                                        this.markupPercent = ((sell - buy) / buy * 100).toFixed(2);
                                                    },

                                                    calculatePercentages() {
                                                        if (!this.buyRaw || !this.sellRaw || parseInt(this.buyRaw) === 0) {
                                                            this.markupPercent = '';
                                                            this.profitPercent = '';
                                                            return;
                                                        }
                                                        let buy = parseInt(this.buyRaw);
                                                        let sell = parseInt(this.sellRaw);

                                                        this.markupPercent = ((sell - buy) / buy * 100).toFixed(2);
                                                        if (sell !== 0) {
                                                            this.profitPercent = ((sell - buy) / sell * 100).toFixed(2);
                                                        }
                                                    },

                                                    get profitValue() {
                                                        if (!this.sellRaw || !this.buyRaw) return 0;
                                                        return parseInt(this.sellRaw) - parseInt(this.buyRaw);
                                                    },

                                                    get isLoss() {
                                                        return this.profitValue < 0;
                                                    }
                                                }">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
                            {{-- Left Column: Modal --}}
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Harga Beli
                                    (Modal)</label>
                                <div class="relative group">
                                    <div
                                        class="absolute left-0 top-0 bottom-0 w-10 flex items-center justify-center bg-slate-100 dark:bg-slate-700 border border-r-0 border-slate-200 dark:border-slate-600 rounded-l-lg text-slate-500 text-sm group-focus-within:border-primary group-focus-within:bg-primary/5 group-focus-within:text-primary transition-colors">
                                        Rp</div>
                                    <input type="text" x-model="buyDisplay" @input="formatBuy($event)"
                                        class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 text-slate-900 dark:text-white text-sm rounded-lg pl-12 pr-4 py-2.5 outline-none focus:ring-2 focus:ring-primary transition-all font-medium"
                                        placeholder="0">
                                    <input type="hidden" name="buyPrice" :value="buyRaw">
                                </div>
                                <p class="text-[10px] text-slate-400 mt-1.5 ml-1">Masukkan harga modal barang dari supplier.</p>
                            </div>

                            {{-- Right Column: Jual --}}
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Harga
                                    Jual</label>
                                <div class="relative group">
                                    <div
                                        class="absolute left-0 top-0 bottom-0 w-10 flex items-center justify-center bg-slate-100 dark:bg-slate-700 border border-r-0 border-slate-200 dark:border-slate-600 rounded-l-lg text-slate-500 text-sm group-focus-within:border-primary group-focus-within:bg-primary/5 group-focus-within:text-primary transition-colors">
                                        Rp</div>
                                    <input type="text" x-model="sellDisplay" @input="formatSell($event)" required
                                        class="w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 text-slate-900 dark:text-white text-sm rounded-lg pl-12 pr-4 py-2.5 outline-none focus:ring-2 focus:ring-primary transition-all font-bold text-lg"
                                        :class="isLoss ? 'text-rose-600 border-rose-300 focus:ring-rose-500' : ''"
                                        placeholder="0">
                                    <input type="hidden" name="sellPrice" :value="sellRaw">
                                </div>
                                @error('sellPrice')
                                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                                @enderror

                                {{-- Profit Indicator --}}
                                <div class="mt-2 flex items-center justify-between px-1">
                                    <span class="text-[10px] text-slate-400">Total Profit:</span>
                                    <span class="text-xs font-bold"
                                        :class="isLoss ? 'text-rose-500' : 'text-emerald-600'">
                                        <span x-show="isLoss">-</span>Rp <span
                                            x-text="new Intl.NumberFormat('id-ID').format(Math.abs(profitValue))"></span>
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Calculator Tools (Middle Section) --}}
                        <div class="relative">
                            <div class="absolute inset-0 flex items-center" aria-hidden="true">
                                <div class="w-full border-t border-slate-200 dark:border-slate-700 border-dashed"></div>
                            </div>
                            <div class="relative flex justify-center">
                                <span
                                    class="bg-white dark:bg-darkCard px-2 text-xs text-slate-400 uppercase tracking-wider font-medium">Kalkulator
                                    Margin</span>
                            </div>
                        </div>

                        <div
                            class="bg-indigo-50/50 dark:bg-slate-800/50 rounded-xl p-4 border border-indigo-100 dark:border-slate-700 flex flex-col md:flex-row items-center gap-4 md:gap-8 justify-center">
                            {{-- Markup Input --}}
                            <div class="w-full md:w-auto flex-1 max-w-[200px]">
                                <label class="block text-[10px] font-bold text-indigo-400 uppercase mb-1 text-center">Markup
                                    %</label>
                                <div class="relative">
                                    <input type="number" x-model="markupPercent" @input="updateFromMarkup()" step="0.1"
                                        class="w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 text-slate-900 dark:text-white text-sm rounded-lg px-3 py-2 text-center outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-all shadow-sm"
                                        placeholder="0">
                                    <span
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs font-bold">%</span>
                                </div>
                                <p class="text-[9px] text-slate-400 text-center mt-1">(Dari Modal)</p>
                            </div>

                            <div class="text-slate-300 dark:text-slate-600 hidden md:block">
                                <i class='bx bx-transfer-alt text-xl'></i>
                            </div>

                            {{-- Profit Input --}}
                            <div class="w-full md:w-auto flex-1 max-w-[200px]">
                                <label class="block text-[10px] font-bold text-emerald-500 uppercase mb-1 text-center">Profit
                                    %</label>
                                <div class="relative">
                                    <input type="number" x-model="profitPercent" @input="updateFromProfit()" step="0.1"
                                        class="w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 text-slate-900 dark:text-white text-sm rounded-lg px-3 py-2 text-center outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all shadow-sm"
                                        placeholder="0">
                                    <span
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs font-bold">%</span>
                                </div>
                                <p class="text-[9px] text-slate-400 text-center mt-1">(Dari Jual)</p>
                            </div>
                        </div>

                        <div x-show="isLoss" x-transition
                            class="bg-rose-50 dark:bg-rose-900/10 border border-rose-200 dark:border-rose-800 rounded-lg p-3 flex items-center gap-3">
                            <i class='bx bx-error-circle text-2xl text-rose-500'></i>
                            <p class="text-xs text-rose-600 dark:text-rose-400 font-medium">Hati-hati, harga jual lebih rendah
                                dari modal. Anda mengalami kerugian.</p>
                        </div>
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
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Ambang Batas
                                    Stok
                                    <span class="text-rose-500">*</span></label>
                                <input type="number" name="threshold" value="{{ old('threshold', 10) }}" required min="1"
                                    class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 text-slate-900 dark:text-white text-sm rounded-lg px-4 py-2.5 outline-none focus:ring-2 focus:ring-primary transition-all @error('threshold') border-rose-500 @enderror"
                                    placeholder="10">
                                @error('threshold')
                                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                                @enderror
                                <p class="text-xs text-slate-400 mt-1">Peringatan stok menipis akan muncul saat stok ≤ nilai ini
                                </p>
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