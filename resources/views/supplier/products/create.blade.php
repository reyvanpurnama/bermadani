@extends('layouts.supplier')

@section('title', 'Tambah Produk')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('supplier.products.index') }}" class="w-10 h-10 rounded-lg bg-white dark:bg-darkCard shadow-sm border border-slate-100 dark:border-slate-700 flex items-center justify-center text-slate-500 hover:text-primary transition-colors">
            <i class='bx bx-arrow-back text-xl'></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Tambah Produk</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Isi informasi produk baru Anda.</p>
        </div>
    </div>

    <div class="bg-white dark:bg-darkCard rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 p-6">
        <form action="{{ route('supplier.products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="grid grid-cols-1 gap-6">
                <!-- Basic Info -->
                <div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider mb-4">Informasi Dasar</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nama Produk <span class="text-rose-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-lg px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all" placeholder="Contoh: Keripik Pisang Coklat" required>
                            @error('name') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Kategori <span class="text-rose-500">*</span></label>
                            <select name="category_id" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-lg px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all cursor-pointer" required>
                                <option value="">Pilih Kategori</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Deskripsi</label>
                            <textarea name="description" rows="3" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-lg px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all" placeholder="Deskripsi lengkap produk...">{{ old('description') }}</textarea>
                            @error('description') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <hr class="border-slate-100 dark:border-slate-700">

                <!-- Pricing -->
                <div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider mb-4">Harga</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Harga Satuan <span class="text-rose-500">*</span></label>
                            <div class="relative"
                                x-data="{
                                    displayValue: '{{ old('price') ? number_format(old('price'), 0, '', '.') : '' }}',
                                    rawValue: '{{ old('price', '') }}',
                                    formatRupiah(value) {
                                        let number = String(value).replace(/[^0-9]/g, '');
                                        if (number === '') return '';
                                        return number.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
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
                            <p class="text-[11px] text-slate-400 mt-1">Harga yang Anda ajukan. Admin akan menentukan harga jual final.</p>
                            @error('price') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <!-- Image Upload -->
                <hr class="border-slate-100 dark:border-slate-700">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider mb-4">Gambar Produk</h3>
                    <div x-data="{ 
                        preview: null,
                        handleFile(e) {
                            const file = e.target.files[0];
                            if (file) {
                                const reader = new FileReader();
                                reader.onload = (e) => this.preview = e.target.result;
                                reader.readAsDataURL(file);
                            }
                        }
                    }">
                        <div class="border-2 border-dashed border-slate-200 dark:border-slate-600 rounded-lg p-6 text-center hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors cursor-pointer relative"
                            :class="preview ? 'border-primary bg-primary/5' : ''">
                            <input type="file" name="image" accept="image/*" @change="handleFile($event)"
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                            
                            <template x-if="!preview">
                                <div>
                                    <i class='bx bx-image-add text-4xl text-slate-300 mb-2'></i>
                                    <p class="text-sm text-slate-500">Klik atau drag gambar ke sini</p>
                                    <p class="text-xs text-slate-400 mt-1">Maksimal 2MB (JPG, PNG)</p>
                                </div>
                            </template>
                            
                            <template x-if="preview">
                                <div class="flex flex-col items-center">
                                    <img :src="preview" class="max-h-48 rounded-lg shadow-md mb-2">
                                    <p class="text-xs text-primary font-medium">Klik untuk ganti gambar</p>
                                </div>
                            </template>
                        </div>
                        @error('image') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6 pt-6 border-t border-slate-100 dark:border-slate-700">
                    <a href="{{ route('supplier.products.index') }}" class="px-6 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">Batal</a>
                    <button type="submit" class="px-6 py-2.5 text-sm font-bold text-white bg-primary hover:bg-primary-dark rounded-lg transition-colors shadow-lg shadow-primary/30">Simpan Produk</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
