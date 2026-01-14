<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Request Masuk (Restock)</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Kelola permintaan restock barang ke supplier</p>
        </div>
        <button wire:click="openModal"
            class="bg-primary hover:bg-indigo-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors">
            <i class='bx bx-plus'></i>
            <span>Buat Request</span>
        </button>
    </div>

    {{-- Filters --}}
    <div
        class="bg-white dark:bg-darkCard p-4 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 flex flex-col md:flex-row gap-4">
        <div class="flex-1">
            <div class="relative">
                <i class='bx bx-search absolute left-3 top-2.5 text-slate-400'></i>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari barang atau supplier..."
                    class="w-full pl-9 pr-4 py-2 bg-slate-50 dark:bg-slate-800 border-none rounded-lg text-sm focus:ring-2 focus:ring-primary">
            </div>
        </div>
        <div class="w-full md:w-48">
            <select wire:model.live="status"
                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border-none rounded-lg text-sm focus:ring-2 focus:ring-primary">
                <option value="">Semua Status</option>
                <option value="PENDING">Pending</option>
                <option value="ACCEPTED">Disetujui</option>
                <option value="COMPLETED">Selesai (Diterima)</option>
                <option value="REJECTED">Ditolak</option>
            </select>
        </div>
    </div>

    {{-- List Data --}}
    <div
        class="bg-white dark:bg-darkCard rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700">
                    <tr>
                        <th class="px-5 py-3 font-semibold text-slate-600 dark:text-slate-300">Tanggal</th>
                        <th class="px-5 py-3 font-semibold text-slate-600 dark:text-slate-300">Barang</th>
                        <th class="px-5 py-3 font-semibold text-slate-600 dark:text-slate-300">Supplier</th>
                        <th class="px-5 py-3 font-semibold text-slate-600 dark:text-slate-300 text-center">Jumlah</th>
                        <th class="px-5 py-3 font-semibold text-slate-600 dark:text-slate-300 text-center">Status</th>
                        <th class="px-5 py-3 font-semibold text-slate-600 dark:text-slate-300 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                    @forelse($requests as $req)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <td class="px-5 py-3 text-slate-500">
                                {{ $req->created_at->format('d M Y') }}
                            </td>
                            <td class="px-5 py-3">
                                <div class="font-medium text-slate-800 dark:text-white">{{ $req->product->name }}</div>
                                <div class="text-xs text-slate-500">{{ $req->product->sku }}</div>
                            </td>
                            <td class="px-5 py-3">
                                <span class="text-slate-600 dark:text-slate-300">{{ $req->supplier->businessName }}</span>
                            </td>
                            <td class="px-5 py-3 text-center">
                                <span class="font-bold text-slate-800 dark:text-white">{{ $req->requestedQty }}</span>
                                @if($req->status == 'COMPLETED' && $req->confirmedQty != $req->requestedQty)
                                    <div class="text-xs text-slate-400 line-through">{{ $req->confirmedQty }}</div>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-center">
                                @if($req->status == 'PENDING')
                                    <span
                                        class="px-2 py-1 rounded-full text-xs font-medium bg-amber-50 text-amber-600 border border-amber-100">Pending</span>
                                @elseif($req->status == 'ACCEPTED')
                                    <span
                                        class="px-2 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-600 border border-blue-100">Disetujui</span>
                                @elseif($req->status == 'COMPLETED')
                                    <span
                                        class="px-2 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-600 border border-emerald-100">Diterima</span>
                                @else
                                    <span
                                        class="px-2 py-1 rounded-full text-xs font-medium bg-rose-50 text-rose-600 border border-rose-100">{{ $req->status }}</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-right">
                                @if($req->status == 'PENDING')
                                    <button wire:click="cancelRequest({{ $req->id }})" wire:confirm="Batalkan request ini?"
                                        class="text-rose-500 hover:text-rose-700 text-xs font-semibold px-2 py-1 hover:bg-rose-50 rounded transition-colors">
                                        Batal
                                    </button>
                                @elseif($req->status == 'ACCEPTED')
                                    <button wire:click="openReceiveModal({{ $req->id }})"
                                        class="bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-semibold px-3 py-1.5 rounded transition-colors flex ml-auto items-center gap-1">
                                        <i class='bx bx-check-double'></i> Terima Barang
                                    </button>
                                @else
                                    <span class="text-slate-400 text-xs">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-slate-500">
                                <div class="flex flex-col items-center">
                                    <i class='bx bx-cart text-4xl mb-2 text-slate-300'></i>
                                    <p>Belum ada request restock.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4 border-t border-slate-200 dark:border-slate-700">
            {{ $requests->links() }}
        </div>
    </div>

    {{-- Modal Create --}}
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
            <div class="bg-white dark:bg-darkCard rounded-xl shadow-2xl w-full max-w-md overflow-hidden animate-fade-in-up">
                <div class="flex justify-between items-center px-6 py-4 border-b border-slate-200 dark:border-slate-700">
                    <h3 class="font-bold text-lg text-slate-800 dark:text-white">Buat Request Restock</h3>
                    <button wire:click="$set('showModal', false)"
                        class="text-slate-400 hover:text-slate-600 transition-colors">
                        <i class='bx bx-x text-2xl'></i>
                    </button>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Pilih
                            Barang</label>
                        <select wire:model="productId"
                            class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-primary focus:border-primary">
                            <option value="">-- Pilih Barang --</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }} (Sup:
                                    {{ $product->supplier->businessName ?? '-' }})</option>
                            @endforeach
                        </select>
                        @error('productId') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Jumlah
                            Request</label>
                        <input wire:model="requestedQty" type="number" min="1"
                            class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-primary focus:border-primary">
                        @error('requestedQty') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Catatan
                            (Opsional)</label>
                        <textarea wire:model="note" rows="2"
                            class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-primary focus:border-primary"></textarea>
                    </div>
                </div>
                <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 flex justify-end gap-3">
                    <button wire:click="$set('showModal', false)"
                        class="px-4 py-2 text-sm text-slate-600 hover:text-slate-800 font-medium transition-colors">Batal</button>
                    <button wire:click="saveRequest"
                        class="px-4 py-2 text-sm bg-primary hover:bg-indigo-700 text-white rounded-lg font-medium transition-colors flex items-center gap-1">
                        <i class='bx bx-send'></i> Kirim Request
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Receive --}}
    @if($showReceiveModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
            <div class="bg-white dark:bg-darkCard rounded-xl shadow-2xl w-full max-w-md overflow-hidden animate-fade-in-up">
                <div class="flex justify-between items-center px-6 py-4 border-b border-slate-200 dark:border-slate-700">
                    <h3 class="font-bold text-lg text-slate-800 dark:text-white">Terima Barang</h3>
                    <button wire:click="$set('showReceiveModal', false)"
                        class="text-slate-400 hover:text-slate-600 transition-colors">
                        <i class='bx bx-x text-2xl'></i>
                    </button>
                </div>
                <div class="p-6 space-y-4">
                    <div class="bg-blue-50 text-blue-800 p-3 rounded-lg text-sm">
                        Pastikan barang fisik sudah diterima. Stok akan otomatis bertambah setelah konfirmasi.
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Jumlah
                            Diterima</label>
                        <input wire:model="confirmedQty" type="number" min="1"
                            class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-primary focus:border-primary">
                        <p class="text-xs text-slate-400 mt-1">Sesuaikan jika jumlah yang datang berbeda dari request.</p>
                        @error('confirmedQty') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 flex justify-end gap-3">
                    <button wire:click="$set('showReceiveModal', false)"
                        class="px-4 py-2 text-sm text-slate-600 hover:text-slate-800 font-medium transition-colors">Batal</button>
                    <button wire:click="confirmReceive"
                        class="px-4 py-2 text-sm bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg font-medium transition-colors flex items-center gap-1">
                        <i class='bx bx-check-double'></i> Konfirmasi Terima
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>