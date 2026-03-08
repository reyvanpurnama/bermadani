@extends('layouts.supplier')

@section('title', 'Kirim Barang Harian')

@section('content')
<div class="space-y-6" x-data="{
    items: [{ productId: '', qty: 1 }],
    addItem() { this.items.push({ productId: '', qty: 1 }) },
    removeItem(i) { if (this.items.length > 1) this.items.splice(i, 1) }
}">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <a href="{{ route('supplier.restock') }}"
                class="inline-flex items-center gap-1.5 text-xs text-slate-500 hover:text-emerald-600 dark:text-slate-400 dark:hover:text-emerald-400 mb-2 transition-colors group">
                <i class='bx bx-arrow-back text-base group-hover:-translate-x-1 transition-transform'></i>
                Kembali ke Riwayat
            </a>
            <h1 class="text-xl font-bold text-slate-900 dark:text-white">📦 Kirim Barang Hari Ini</h1>
            <p class="text-[11px] text-slate-500 mt-0.5">Daftarkan barang yang akan Anda titipkan ke toko hari ini. Kasir akan mengkonfirmasi penerimaan.</p>
        </div>
    </div>

    @if(session('error'))
    <div class="bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-400 px-4 py-3 rounded-xl flex items-center gap-3">
        <i class='bx bx-error-circle text-xl'></i>
        <span class="text-sm">{{ session('error') }}</span>
    </div>
    @endif

    <form action="{{ route('supplier.restock.store') }}" method="POST">
        @csrf

        <div class="bg-white dark:bg-darkCard rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 p-6 space-y-5">

            {{-- Info Banner --}}
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4 flex items-start gap-3">
                <i class='bx bx-info-circle text-blue-500 text-xl mt-0.5'></i>
                <div class="text-sm">
                    <p class="font-semibold text-blue-700 dark:text-blue-400">Cara kerja pengiriman harian</p>
                    <p class="text-blue-600 dark:text-blue-300 text-xs mt-1">
                        1. Isi form → kirim request → kasir akan menerima & mengkonfirmasi jumlah fisik<br>
                        2. Jika ada selisih saat kasir terima, Anda akan mendapat notifikasi otomatis<br>
                        3. Hanya produk yang sudah disetujui admin yang bisa dikirim
                    </p>
                </div>
            </div>

            {{-- Item List --}}
            <div class="space-y-3">
                <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Daftar Produk yang Dikirim</label>

                <template x-for="(item, index) in items" :key="index">
                    <div class="flex gap-3 items-start bg-slate-50 dark:bg-slate-800/50 rounded-xl p-3">
                        <div class="flex-1">
                            <select :name="`items[${index}][productId]`" x-model="item.productId"
                                class="w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500"
                                required>
                                <option value="">-- Pilih Produk --</option>
                                @foreach($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }} (Harga jual: Rp {{ number_format($product->sellPrice, 0, ',', '.') }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="w-28">
                            <input type="number" :name="`items[${index}][qty]`" x-model="item.qty"
                                min="1" max="9999" placeholder="Qty"
                                class="w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-xl px-3 py-2.5 text-sm text-center font-bold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500"
                                required>
                        </div>
                        <button type="button" @click="removeItem(index)"
                            x-show="items.length > 1"
                            class="mt-0.5 w-9 h-9 flex items-center justify-center rounded-xl text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-900/20 hover:text-rose-600 transition-colors flex-shrink-0">
                            <i class='bx bx-trash text-lg'></i>
                        </button>
                    </div>
                </template>

                <button type="button" @click="addItem()"
                    class="w-full border-2 border-dashed border-slate-200 dark:border-slate-700 hover:border-emerald-400 dark:hover:border-emerald-500 text-slate-400 hover:text-emerald-600 rounded-xl py-3 text-sm font-medium transition-colors flex items-center justify-center gap-2">
                    <i class='bx bx-plus text-lg'></i> Tambah Produk
                </button>
            </div>

            @error('items')
            <p class="text-rose-500 text-xs flex items-center gap-1"><i class='bx bx-error-circle'></i> {{ $message }}</p>
            @enderror

            {{-- Note --}}
            <div>
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Catatan (Opsional)</label>
                <textarea name="note" rows="2" placeholder="Misal: barang dikirim jam 08.00, ada 2 varian donat baru..."
                    class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 resize-none">{{ old('note') }}</textarea>
            </div>

            {{-- Actions --}}
            <div class="flex gap-3 pt-2">
                <a href="{{ route('supplier.restock') }}"
                    class="flex-1 sm:flex-none bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 font-medium py-2.5 px-6 rounded-xl transition-colors text-sm text-center">
                    Batal
                </a>
                <button type="submit"
                    class="flex-1 sm:flex-none bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 px-6 rounded-xl transition-colors text-sm flex items-center justify-center gap-2">
                    <i class='bx bx-send text-lg'></i> Kirim Request
                </button>
            </div>
        </div>
    </form>

</div>
@endsection
