@extends('layouts.supplier')

@section('title', 'Edit Produk')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('supplier.products.index') }}" class="w-10 h-10 rounded-lg bg-white dark:bg-darkCard shadow-sm border border-slate-100 dark:border-slate-700 flex items-center justify-center text-slate-500 hover:text-primary transition-colors">
            <i class='bx bx-arrow-back text-xl'></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Edit Produk</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Perbarui informasi produk Anda.</p>
        </div>
    </div>

    <div class="bg-white dark:bg-darkCard rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 p-6">
        <form action="{{ route('supplier.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 gap-6">
                <!-- Basic Info -->
                <div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider mb-4">Informasi Dasar</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nama Produk <span class="text-rose-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $product->name) }}" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-lg px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all" required>
                            @error('name') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Kategori <span class="text-rose-500">*</span></label>
                            <select name="category_id" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-lg px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all cursor-pointer" required>
                                <option value="">Pilih Kategori</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $product->categoryId) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Stok <span class="text-rose-500">*</span></label>
                            <input type="number" name="stock" value="{{ old('stock', $product->stock) }}" min="0" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-lg px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all" required>
                            @error('stock') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Deskripsi</label>
                            <textarea name="description" rows="3" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-lg px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">{{ old('description', $product->description) }}</textarea>
                            @error('description') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <hr class="border-slate-100 dark:border-slate-700">

                <!-- Pricing & Status -->
                <div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider mb-4">Harga & Status</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Harga Ajuan (Per Unit) <span class="text-rose-500">*</span></label>
                            <div class="relative"
                                x-data="{
                                    displayValue: '',
                                    rawValue: '{{ old('price', (int)$product->buyPrice) }}',
                                    formatRupiah(value) {
                                        let number = String(value).replace(/[^0-9]/g, '');
                                        if (number === '') return '';
                                        return number.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                                    },
                                    init() {
                                        if (this.rawValue && this.rawValue !== '0') {
                                            this.displayValue = this.formatRupiah(this.rawValue);
                                        }
                                    }
                                }">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm">Rp</span>
                                <input type="hidden" name="price" x-bind:value="rawValue">
                                <input type="text" 
                                    x-model="displayValue"
                                    placeholder="0"
                                    class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-lg pl-10 pr-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all" 
                                    required
                                    x-on:input="
                                        let cleanValue = String($el.value).replace(/[^0-9]/g, '');
                                        if (cleanValue === '') {
                                            displayValue = '';
                                            rawValue = '';
                                            return;
                                        }
                                        displayValue = formatRupiah(cleanValue);
                                        rawValue = cleanValue;
                                    ">
                            </div>
                            <p class="text-xs text-slate-500 mt-1">Harga yang Anda ajukan. Admin akan menentukan harga jual final.</p>
                            @error('price') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Status Approval</label>
                            <div class="mt-2">
                                @if($product->approvalStatus === 'PENDING')
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400">
                                        <i class='bx bx-time mr-1.5'></i> Menunggu Review Admin
                                    </span>
                                @elseif($product->approvalStatus === 'APPROVED')
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400">
                                        <i class='bx bx-check-circle mr-1.5'></i> Disetujui
                                    </span>
                                    @if($product->sellPrice)
                                        <p class="text-xs text-slate-500 mt-2">Harga jual: <span class="font-bold">Rp {{ number_format($product->sellPrice, 0, ',', '.') }}</span></p>
                                    @endif
                                @elseif($product->approvalStatus === 'REJECTED')
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-400">
                                        <i class='bx bx-x-circle mr-1.5'></i> Ditolak
                                    </span>
                                    @if($product->rejectionReason)
                                        <p class="text-xs text-rose-500 mt-2">Alasan: {{ $product->rejectionReason }}</p>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6 pt-6 border-t border-slate-100 dark:border-slate-700">
                    <a href="{{ route('supplier.products.index') }}" class="px-6 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">Batal</a>
                    <button type="submit" class="px-6 py-2.5 text-sm font-bold text-white bg-primary hover:bg-primary-dark rounded-lg transition-colors shadow-lg shadow-primary/30">Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
