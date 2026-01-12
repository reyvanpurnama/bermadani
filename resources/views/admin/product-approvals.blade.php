@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Persetujuan Produk</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Kelola ajuan produk konsinyasi dari supplier</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="text-sm text-gray-600 dark:text-gray-400">
                Total Pending: <strong class="text-amber-600 dark:text-amber-400">{{ $pendingCount }}</strong>
            </span>
        </div>
    </div>

    {{-- Filter Tabs --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
        <div class="border-b border-gray-200 dark:border-gray-700">
            <div class="flex flex-wrap gap-2 p-4">
                <a href="{{ route('admin.product-approvals', ['status' => 'pending'] + request()->except('status')) }}" 
                   class="px-4 py-2 text-sm font-semibold rounded-lg transition-all {{ $status === 'pending' ? 'bg-amber-500 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-amber-50 dark:hover:bg-amber-900/20' }}">
                    <i class='bx bx-time-five mr-1'></i> Pending ({{ $pendingCount }})
                </a>
                <a href="{{ route('admin.product-approvals', ['status' => 'approved'] + request()->except('status')) }}" 
                   class="px-4 py-2 text-sm font-semibold rounded-lg transition-all {{ $status === 'approved' ? 'bg-emerald-500 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-emerald-50 dark:hover:bg-emerald-900/20' }}">
                    <i class='bx bx-check-circle mr-1'></i> Disetujui ({{ $approvedCount }})
                </a>
                <a href="{{ route('admin.product-approvals', ['status' => 'rejected'] + request()->except('status')) }}" 
                   class="px-4 py-2 text-sm font-semibold rounded-lg transition-all {{ $status === 'rejected' ? 'bg-rose-500 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-rose-50 dark:hover:bg-rose-900/20' }}">
                    <i class='bx bx-x-circle mr-1'></i> Ditolak ({{ $rejectedCount }})
                </a>
                <a href="{{ route('admin.product-approvals') }}" 
                   class="px-4 py-2 text-sm font-semibold rounded-lg transition-all {{ !request()->has('status') ? 'bg-blue-500 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-blue-900/20' }}">
                    <i class='bx bx-list-ul mr-1'></i> Semua
                </a>
            </div>
        </div>

        {{-- Filters --}}
        <div class="p-4 bg-gray-50 dark:bg-gray-800/50">
            <form method="GET" action="{{ route('admin.product-approvals') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <input type="hidden" name="status" value="{{ $status }}">
                
                <div class="relative">
                    <i class='bx bx-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400'></i>
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Cari nama produk..." 
                           class="w-full pl-10 pr-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                </div>

                <select name="supplier_id" 
                        class="px-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="">Semua Supplier</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                            {{ $supplier->businessName }}
                        </option>
                    @endforeach
                </select>

                <select name="category_id" 
                        class="px-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>

                <div class="flex gap-2">
                    <button type="submit" 
                            class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors">
                        <i class='bx bx-filter-alt'></i> Filter
                    </button>
                    <a href="{{ route('admin.product-approvals', ['status' => $status]) }}" 
                       class="px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-semibold rounded-lg transition-colors">
                        <i class='bx bx-reset'></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Products Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        @if($products->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase">Produk</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase">Supplier</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase">Kategori</th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-gray-600 dark:text-gray-300 uppercase">Harga</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-gray-600 dark:text-gray-300 uppercase">Stok</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-gray-600 dark:text-gray-300 uppercase">Tanggal</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-gray-600 dark:text-gray-300 uppercase">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-gray-600 dark:text-gray-300 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($products as $product)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @if($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}" 
                                             class="w-14 h-14 rounded-lg object-cover border border-gray-200 dark:border-gray-600 cursor-pointer hover:scale-110 transition-transform"
                                             onclick="showImageModal('{{ asset('storage/' . $product->image) }}')"
                                             alt="{{ $product->name }}">
                                    @else
                                        <div class="w-14 h-14 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-400">
                                            <i class='bx bx-image text-2xl'></i>
                                        </div>
                                    @endif
                                    <div>
                                        <h6 class="font-semibold text-gray-900 dark:text-white text-sm">{{ $product->name }}</h6>
                                        <p class="text-xs text-gray-500">{{ $product->sku }}</p>
                                        @if($product->isDraft)
                                            <span class="inline-block mt-1 px-2 py-0.5 rounded text-xs font-bold bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">
                                                DRAFT
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.suppliers.detail', $product->supplier->id) }}" 
                                   class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:underline">
                                    {{ $product->supplier->businessName ?? '-' }}
                                </a>
                                <p class="text-xs text-gray-500">{{ $product->supplier->supplierCode ?? '-' }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                {{ $product->category->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-right text-sm font-semibold text-gray-900 dark:text-white">
                                Rp {{ number_format($product->sellPrice, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold 
                                    {{ $product->stock > $product->threshold ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400' }}">
                                    {{ $product->stock }} pcs
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center text-xs text-gray-500">
                                {{ $product->created_at->format('d M Y') }}<br>
                                {{ $product->created_at->format('H:i') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($product->approvalStatus === 'APPROVED')
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">
                                        <i class='bx bx-check-circle'></i> Disetujui
                                    </span>
                                @elseif($product->approvalStatus === 'REJECTED')
                                    <div class="flex flex-col items-center gap-1">
                                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400">
                                            <i class='bx bx-x-circle'></i> Ditolak
                                        </span>
                                        @if($product->rejectionReason)
                                            <button onclick="alert('{{ $product->rejectionReason }}')" 
                                                    class="text-xs text-rose-600 dark:text-rose-400 hover:underline">
                                                Lihat alasan
                                            </button>
                                        @endif
                                    </div>
                                @else
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">
                                        <i class='bx bx-time-five'></i> Pending
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if($product->approvalStatus === 'PENDING')
                                    <div class="flex justify-end gap-2">
                                        <form action="{{ route('admin.products.approve', $product->id) }}" 
                                              method="POST" 
                                              class="inline"
                                              onsubmit="return confirm('Setujui produk {{ $product->name }}?')">
                                            @csrf
                                            <button type="submit" 
                                                    class="px-3 py-1.5 bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-semibold rounded-lg transition-colors"
                                                    title="Approve">
                                                <i class='bx bx-check'></i> Setujui
                                            </button>
                                        </form>
                                        <button onclick="openRejectModal({{ $product->id }}, '{{ $product->name }}')" 
                                                class="px-3 py-1.5 bg-rose-500 hover:bg-rose-600 text-white text-xs font-semibold rounded-lg transition-colors"
                                                title="Reject">
                                            <i class='bx bx-x'></i> Tolak
                                        </button>
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400">-</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($products->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $products->appends(request()->query())->links() }}
            </div>
            @endif
        @else
            <div class="p-12 text-center text-gray-400">
                <i class='bx bx-package text-6xl mb-3'></i>
                <p class="text-lg font-semibold">Tidak ada produk</p>
                <p class="text-sm mt-1">Belum ada produk dengan status ini</p>
            </div>
        @endif
    </div>
</div>

{{-- Modal Image Preview --}}
<div id="imageModal" class="hidden fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50" onclick="closeImageModal()">
    <div class="relative max-w-4xl max-h-[90vh] p-4">
        <button onclick="closeImageModal()" class="absolute top-2 right-2 w-10 h-10 bg-white rounded-full flex items-center justify-center text-gray-700 hover:bg-gray-200 transition-colors">
            <i class='bx bx-x text-2xl'></i>
        </button>
        <img id="modalImage" src="" class="max-w-full max-h-[85vh] rounded-lg shadow-2xl" onclick="event.stopPropagation()">
    </div>
</div>

{{-- Modal Reject --}}
<div id="rejectModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 max-w-md w-full mx-4 shadow-2xl">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-12 h-12 rounded-lg bg-rose-100 dark:bg-rose-900/30 flex items-center justify-center text-rose-600 dark:text-rose-400">
                <i class='bx bx-x-circle text-2xl'></i>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Tolak Produk</h3>
                <p class="text-xs text-gray-500">Berikan alasan penolakan</p>
            </div>
        </div>

        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3 mb-4">
            <p class="text-sm text-gray-600 dark:text-gray-400">Produk: <strong id="rejectProductName" class="text-gray-900 dark:text-white"></strong></p>
        </div>

        <form id="rejectForm" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Alasan Penolakan <span class="text-rose-500">*</span>
                </label>
                <textarea name="reason" 
                          required 
                          rows="4" 
                          class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-rose-500 outline-none resize-none" 
                          placeholder="Contoh: Foto produk tidak jelas, deskripsi kurang lengkap, harga tidak sesuai, dll"></textarea>
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" 
                        onclick="closeRejectModal()" 
                        class="px-4 py-2.5 text-sm font-semibold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors">
                    Batal
                </button>
                <button type="submit" 
                        class="px-4 py-2.5 text-sm font-semibold text-white bg-rose-500 hover:bg-rose-600 rounded-lg transition-colors">
                    <i class='bx bx-x-circle'></i> Tolak Produk
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function showImageModal(imageSrc) {
    document.getElementById('modalImage').src = imageSrc;
    document.getElementById('imageModal').classList.remove('hidden');
}

function closeImageModal() {
    document.getElementById('imageModal').classList.add('hidden');
}

function openRejectModal(productId, productName) {
    document.getElementById('rejectProductName').textContent = productName;
    document.getElementById('rejectForm').action = `/admin/products/${productId}/reject`;
    document.getElementById('rejectModal').classList.remove('hidden');
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('rejectModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeRejectModal();
    }
});
</script>

@endsection
