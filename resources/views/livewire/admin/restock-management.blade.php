<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-white">Purchase Orders (PO)</h1>
            <p class="text-[11px] text-slate-500 mt-0.5">Kelola pesanan barang ke supplier & mahasiswa.</p>
        </div>
        <button wire:click="openModal"
            class="bg-primary hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-[13px] font-bold shadow-md shadow-indigo-500/20 transition-colors flex items-center gap-2">
            <i class='bx bx-plus-circle text-lg'></i> Buat PO Baru
        </button>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div
            class="bg-white dark:bg-darkCard p-4 rounded-xl shadow-sm border-l-4 border-l-amber-400 border-y border-r border-slate-100 dark:border-slate-700 flex items-center justify-between">
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Menunggu Barang</p>
                <h4 class="text-lg font-bold text-slate-800 dark:text-white">
                    {{ $requests->where('status', 'ACCEPTED')->count() }} Order</h4>
            </div>
            <div
                class="w-10 h-10 rounded-lg bg-amber-50 dark:bg-amber-500/10 flex items-center justify-center text-amber-500 text-xl">
                <i class='bx bx-time-five'></i></div>
        </div>

        <div
            class="bg-white dark:bg-darkCard p-4 rounded-xl shadow-sm border-l-4 border-l-emerald-500 border-y border-r border-slate-100 dark:border-slate-700 flex items-center justify-between">
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Diterima Hari Ini</p>
                <h4 class="text-lg font-bold text-slate-800 dark:text-white">
                    {{ $requests->where('status', 'COMPLETED')->whereDate('completedAt', today())->count() }} Batch</h4>
            </div>
            <div
                class="w-10 h-10 rounded-lg bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center text-emerald-600 text-xl">
                <i class='bx bx-check-double'></i></div>
        </div>

        <div
            class="bg-white dark:bg-darkCard p-4 rounded-xl shadow-sm border-l-4 border-l-blue-500 border-y border-r border-slate-100 dark:border-slate-700 flex items-center justify-between">
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Estimasi Tagihan</p>
                <h4 class="text-lg font-bold text-slate-800 dark:text-white">-</h4>
            </div>
            <div
                class="w-10 h-10 rounded-lg bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center text-blue-600 text-xl">
                <i class='bx bx-money'></i></div>
        </div>
    </div>

    {{-- Main Table Card --}}
    <div
        class="bg-white dark:bg-darkCard rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">

        {{-- Tab Filter --}}
        <div class="flex border-b border-slate-200 dark:border-slate-700 px-2">
            <button wire:click="$set('status', '')"
                class="px-4 py-3 text-[13px] font-bold {{ $status === '' ? 'text-primary border-b-2 border-primary' : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 font-medium transition-colors' }}">Semua</button>
            <button wire:click="$set('status', 'PENDING')"
                class="px-4 py-3 text-[13px] {{ $status === 'PENDING' ? 'font-bold text-primary border-b-2 border-primary' : 'font-medium text-slate-500 hover:text-slate-700 dark:text-slate-400 transition-colors' }}">Pending
                ({{ $requests->where('status', 'PENDING')->count() }})</button>
            <button wire:click="$set('status', 'COMPLETED')"
                class="px-4 py-3 text-[13px] {{ $status === 'COMPLETED' ? 'font-bold text-primary border-b-2 border-primary' : 'font-medium text-slate-500 hover:text-slate-700 dark:text-slate-400 transition-colors' }}">Selesai</button>
            <button wire:click="$set('status', 'REJECTED')"
                class="px-4 py-3 text-[13px] {{ $status === 'REJECTED' ? 'font-bold text-primary border-b-2 border-primary' : 'font-medium text-slate-500 hover:text-slate-700 dark:text-slate-400 transition-colors' }}">Dibatalkan</button>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600 dark:text-slate-400">
                <thead
                    class="bg-slate-50 dark:bg-slate-800/50 text-[10px] uppercase font-bold text-slate-500 dark:text-slate-400 tracking-widest">
                    <tr>
                        <th class="px-5 py-3">PO ID</th>
                        <th class="px-5 py-3">Supplier</th>
                        <th class="px-5 py-3">Rincian</th>
                        <th class="px-5 py-3">Tanggal Order</th>
                        <th class="px-5 py-3 text-center">Status</th>
                        <th class="px-5 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700 text-[13px]">
                    @forelse($requests as $req)
                        <tr
                            class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors group {{ $req->status == 'COMPLETED' ? 'opacity-60 hover:opacity-100' : '' }}">
                            <td
                                class="px-5 py-4 font-mono {{ $req->status == 'COMPLETED' ? 'text-slate-500' : 'text-indigo-600 dark:text-indigo-400' }} font-bold">
                                #PO-{{ $req->id }}
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-2">
                                    <div
                                        class="w-6 h-6 rounded-full bg-{{ ['emerald', 'blue', 'orange', 'purple'][rand(0, 3)] }}-100 text-{{ ['emerald', 'blue', 'orange', 'purple'][rand(0, 3)] }}-600 flex items-center justify-center text-[10px] font-bold">
                                        {{ strtoupper(substr($req->supplier->businessName ?? 'S', 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-slate-800 dark:text-white leading-none">
                                            {{ $req->supplier->businessName ?? '-' }}</p>
                                        <p class="text-[10px] text-slate-400 mt-0.5">
                                            {{ $req->supplier->supplierCode ?? 'Supplier' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <span class="font-bold text-slate-700 dark:text-slate-300">{{ $req->requestedQty }}
                                    {{ $req->product->unit ?? 'Pcs' }}</span>
                                <p class="text-[10px] text-slate-400">{{ $req->product->name }}</p>
                            </td>
                            <td class="px-5 py-4">
                                {{ $req->created_at->format('d M Y') }}
                            </td>
                            <td class="px-5 py-4 text-center">
                                @if($req->status == 'PENDING')
                                    <span
                                        class="bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide">
                                        Requested
                                    </span>
                                @elseif($req->status == 'ACCEPTED')
                                    <span
                                        class="bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide">
                                        Dikirim
                                    </span>
                                @elseif($req->status == 'COMPLETED')
                                    <span class="text-emerald-600 font-bold text-[11px] flex items-center justify-center gap-1">
                                        <i class='bx bx-check-double'></i> Selesai
                                    </span>
                                @else
                                    <span class="text-slate-400 text-[11px]">{{ $req->status }}</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-center">
                                @if($req->status == 'PENDING')
                                    <button wire:click="cancelRequest({{ $req->id }})" wire:confirm="Batalkan request ini?"
                                        class="text-slate-400 hover:text-rose-500 transition-colors" title="Batalkan">
                                        <i class='bx bx-x-circle text-lg'></i>
                                    </button>
                                @elseif($req->status == 'ACCEPTED')
                                    <button wire:click="openReceiveModal({{ $req->id }})"
                                        class="bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded-md text-[11px] font-bold shadow-sm transition-colors flex items-center justify-center gap-1 mx-auto">
                                        <i class='bx bx-box'></i> Terima
                                    </button>
                                @else
                                    <button class="text-slate-400 hover:text-primary transition-colors">
                                        <i class='bx bx-file text-lg'></i>
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-slate-500">
                                <div class="flex flex-col items-center">
                                    <i class='bx bx-cart text-4xl mb-2 text-slate-300'></i>
                                    <p>Belum ada purchase order.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal Create --}}
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
            <div class="bg-white dark:bg-darkCard rounded-xl shadow-2xl w-full max-w-md overflow-hidden animate-fade-in-up">
                <div class="flex justify-between items-center px-6 py-4 border-b border-slate-200 dark:border-slate-700">
                    <h3 class="font-bold text-lg text-slate-800 dark:text-white">Buat Purchase Order</h3>
                    <button wire:click="$set('showModal', false)"
                        class="text-slate-400 hover:text-slate-600 transition-colors">
                        <i class='bx bx-x text-2xl'></i>
                    </button>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Pilih
                            Produk</label>
                        <select wire:model="productId"
                            class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-primary focus:border-primary text-sm">
                            <option value="">-- Pilih Produk --</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }}
                                    ({{ $product->supplier->businessName ?? '-' }})</option>
                            @endforeach
                        </select>
                        @error('productId') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Jumlah
                            Request</label>
                        <input wire:model="requestedQty" type="number" min="1"
                            class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-primary focus:border-primary text-sm">
                        @error('requestedQty') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Catatan
                            (Opsional)</label>
                        <textarea wire:model="note" rows="2"
                            class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-primary focus:border-primary text-sm"></textarea>
                    </div>
                </div>
                <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 flex justify-end gap-3">
                    <button wire:click="$set('showModal', false)"
                        class="px-4 py-2 text-sm text-slate-600 hover:text-slate-800 font-medium transition-colors">Batal</button>
                    <button wire:click="saveRequest"
                        class="px-4 py-2 text-sm bg-primary hover:bg-indigo-700 text-white rounded-lg font-bold transition-colors flex items-center gap-1">
                        <i class='bx bx-send'></i> Kirim PO
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
                    <div class="bg-blue-50 dark:bg-blue-900/20 text-blue-800 dark:text-blue-300 p-3 rounded-lg text-sm">
                        Pastikan barang fisik sudah diterima. Stok akan otomatis bertambah setelah konfirmasi.
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Jumlah
                            Diterima</label>
                        <input wire:model="confirmedQty" type="number" min="1"
                            class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-primary focus:border-primary text-sm">
                        <p class="text-xs text-slate-400 mt-1">Sesuaikan jika jumlah yang datang berbeda dari request.</p>
                        @error('confirmedQty') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 flex justify-end gap-3">
                    <button wire:click="$set('showReceiveModal', false)"
                        class="px-4 py-2 text-sm text-slate-600 hover:text-slate-800 font-medium transition-colors">Batal</button>
                    <button wire:click="confirmReceive"
                        class="px-4 py-2 text-sm bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-bold transition-colors flex items-center gap-1">
                        <i class='bx bx-check-double'></i> Konfirmasi Terima
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>