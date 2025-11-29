@extends('layouts.admin')

@section('content')
<div class="max-w-3xl">
    {{-- Header --}}
    <div class="mb-6">
        <div class="flex items-center gap-3 mb-2">
            <a href="{{ route('admin.products') }}" class="text-slate-400 hover:text-slate-600 dark:hover:text-white transition-colors">
                <i class='bx bx-arrow-back text-2xl'></i>
            </a>
            <h1 class="text-xl font-bold text-slate-900 dark:text-white">Tambah Produk Baru</h1>
        </div>
        <p class="text-[11px] text-slate-500 ml-11">Lengkapi informasi produk di bawah ini.</p>
    </div>

    {{-- Form --}}
    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="bg-white dark:bg-darkCard rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 p-6 space-y-6">
        @csrf

        {{-- Basic Info --}}
        <div class="space-y-4">
            <h3 class="text-sm font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wide">Informasi Dasar</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Nama Produk <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 text-slate-900 dark:text-white text-sm rounded-lg px-4 py-2.5 outline-none focus:ring-2 focus:ring-primary transition-all @error('name') border-rose-500 @enderror"
                           placeholder="Contoh: Indomie Goreng">
                    @error('name')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">SKU <span class="text-rose-500">*</span></label>
                    <input type="text" name="sku" value="{{ old('sku') }}" required
                           class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 text-slate-900 dark:text-white text-sm rounded-lg px-4 py-2.5 outline-none focus:ring-2 focus:ring-primary transition-all @error('sku') border-rose-500 @enderror"
                           placeholder="Contoh: FD-001">
                    @error('sku')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Kategori <span class="text-rose-500">*</span></label>
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
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Harga Jual <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 text-sm">Rp</span>
                        <input type="number" name="sellPrice" value="{{ old('sellPrice') }}" required min="0" step="100"
                               class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 text-slate-900 dark:text-white text-sm rounded-lg pl-11 pr-4 py-2.5 outline-none focus:ring-2 focus:ring-primary transition-all @error('sellPrice') border-rose-500 @enderror"
                               placeholder="0">
                    </div>
                    @error('sellPrice')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Harga Beli</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 text-sm">Rp</span>
                        <input type="number" name="buyPrice" value="{{ old('buyPrice', 0) }}" min="0" step="100"
                               class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 text-slate-900 dark:text-white text-sm rounded-lg pl-11 pr-4 py-2.5 outline-none focus:ring-2 focus:ring-primary transition-all"
                               placeholder="0">
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Stok Awal <span class="text-rose-500">*</span></label>
                    <input type="number" name="stock" value="{{ old('stock', 0) }}" required min="0"
                           class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 text-slate-900 dark:text-white text-sm rounded-lg px-4 py-2.5 outline-none focus:ring-2 focus:ring-primary transition-all @error('stock') border-rose-500 @enderror"
                           placeholder="0">
                    @error('stock')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Ambang Batas Stok <span class="text-rose-500">*</span></label>
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
            <a href="{{ route('admin.products') }}" class="px-6 py-2.5 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-600 font-medium rounded-lg text-sm transition-colors">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 bg-primary hover:bg-indigo-700 text-white font-bold rounded-lg text-sm shadow-lg shadow-indigo-500/20 transition-all flex items-center gap-2">
                <i class='bx bx-save'></i> Simpan Produk
            </button>
        </div>
    </form>
</div>
@endsection
