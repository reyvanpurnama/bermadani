@extends('layouts.admin')

@section('title', 'Detail Supplier')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <a href="{{ route('admin.suppliers') }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 flex items-center gap-2">
            <i class='bx bx-arrow-back'></i>
            Kembali ke Daftar Supplier
        </a>
    </div>

    @php
        $supplier = \App\Models\Supplier::find($supplierId);
    @endphp

    @if(!$supplier)
        <div class="bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/30 text-red-700 dark:text-red-400 px-4 py-3 rounded-lg">
            Supplier tidak ditemukan.
        </div>
    @else
        <div class="bg-white dark:bg-darkCard rounded-lg shadow-sm border border-gray-200 dark:border-slate-700 overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 dark:from-blue-700 dark:to-blue-800 px-6 py-4">
                <h1 class="text-2xl font-bold text-white">Detail Supplier</h1>
                <p class="text-blue-100 dark:text-blue-200 text-sm">Informasi lengkap supplier #{{ $supplier->code }}</p>
            </div>

            <!-- Content -->
            <div class="p-6">
                <!-- Status Badge -->
                <div class="mb-6">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold
                        @if($supplier->status === 'ACTIVE') bg-green-100 dark:bg-green-500/20 text-green-800 dark:text-green-400
                        @elseif($supplier->status === 'PENDING') bg-yellow-100 dark:bg-yellow-500/20 text-yellow-800 dark:text-yellow-400
                        @elseif($supplier->status === 'REJECTED') bg-red-100 dark:bg-red-500/20 text-red-800 dark:text-red-400
                        @elseif($supplier->status === 'SUSPENDED') bg-gray-100 dark:bg-gray-500/20 text-gray-800 dark:text-gray-400
                        @endif">
                        {{ $supplier->status }}
                    </span>
                </div>

                <!-- Info Grid -->
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 mb-1">Kode Supplier</h3>
                        <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $supplier->code }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 mb-1">Nama Pemilik</h3>
                        <p class="text-lg text-gray-900 dark:text-gray-200">{{ $supplier->ownerName }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 mb-1">Nama Bisnis</h3>
                        <p class="text-lg text-gray-900 dark:text-gray-200">{{ $supplier->businessName }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 mb-1">Email</h3>
                        <p class="text-lg text-gray-900 dark:text-gray-200">{{ $supplier->email }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 mb-1">Telepon</h3>
                        <p class="text-lg text-gray-900 dark:text-gray-200">{{ $supplier->phone }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 mb-1">Kategori Produk</h3>
                        <p class="text-lg text-gray-900 dark:text-gray-200">{{ $supplier->productCategory ?? '-' }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 mb-1">Alamat</h3>
                        <p class="text-lg text-gray-900 dark:text-gray-200">{{ $supplier->address }}</p>
                    </div>
                    @if($supplier->description)
                    <div class="md:col-span-2">
                        <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 mb-1">Deskripsi</h3>
                        <p class="text-gray-900 dark:text-gray-200">{{ $supplier->description }}</p>
                    </div>
                    @endif
                    <div>
                        <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 mb-1">Produk Aktif</h3>
                        <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $supplier->currentActiveProducts }} / {{ $supplier->maxActiveProducts }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 mb-1">Tanggal Daftar</h3>
                        <p class="text-lg text-gray-900 dark:text-gray-200">{{ $supplier->created_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>

                <!-- Registration Payment Section -->
                <div class="mt-8 pt-6 border-t border-gray-200 dark:border-slate-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Bukti Pendaftaran</h3>
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="text-sm font-semibold text-gray-500 dark:text-gray-400 mb-2">Biaya Pendaftaran</h4>
                            <p class="text-lg font-bold text-gray-900 dark:text-white">Rp {{ number_format($supplier->registrationFee ?? 0, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-semibold text-gray-500 dark:text-gray-400 mb-2">Status Pembayaran</h4>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold
                                @if($supplier->registrationPaymentStatus === 'COMPLETED') bg-green-100 dark:bg-green-500/20 text-green-800 dark:text-green-400
                                @elseif($supplier->registrationPaymentStatus === 'PENDING') bg-yellow-100 dark:bg-yellow-500/20 text-yellow-800 dark:text-yellow-400
                                @elseif($supplier->registrationPaymentStatus === 'REJECTED') bg-red-100 dark:bg-red-500/20 text-red-800 dark:text-red-400
                                @else bg-gray-100 dark:bg-gray-500/20 text-gray-800 dark:text-gray-400
                                @endif">
                                {{ $supplier->registrationPaymentStatus ?? 'PENDING' }}
                            </span>
                        </div>
                    </div>

                    @if($supplier->registrationPaymentProof)
                    <div class="mt-4">
                        <h4 class="text-sm font-semibold text-gray-500 dark:text-gray-400 mb-3">Bukti Transfer</h4>
                        <div class="flex items-center gap-4">
                            <img src="{{ asset('storage/' . $supplier->registrationPaymentProof) }}" alt="Bukti Transfer" class="h-40 w-40 object-cover rounded-lg border border-gray-200 dark:border-slate-700 cursor-pointer hover:opacity-75 transition-opacity" onclick="showPaymentProofModal('{{ asset('storage/' . $supplier->registrationPaymentProof) }}')">
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Klik gambar untuk melihat detail</p>
                                <a href="{{ asset('storage/' . $supplier->registrationPaymentProof) }}" download class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
                                    <i class='bx bx-download'></i>
                                    Download
                                </a>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="mt-4 p-4 bg-yellow-50 dark:bg-yellow-500/10 border border-yellow-200 dark:border-yellow-500/30 rounded-lg">
                        <p class="text-sm text-yellow-800 dark:text-yellow-400">Belum ada bukti transfer.</p>
                    </div>
                    @endif

                    @if($supplier->registrationPaymentVerifiedAt)
                    <div class="mt-4 p-4 bg-green-50 dark:bg-green-500/10 border border-green-200 dark:border-green-500/30 rounded-lg">
                        <h4 class="text-sm font-semibold text-green-800 dark:text-green-400 mb-2">Verifikasi Pembayaran</h4>
                        <p class="text-sm text-green-700 dark:text-green-300">Diverifikasi oleh: <span class="font-semibold">{{ \App\Models\User::find($supplier->registrationPaymentVerifiedBy)?->name ?? 'Admin' }}</span></p>
                        <p class="text-sm text-green-700 dark:text-green-300">Tanggal: <span class="font-semibold">{{ $supplier->registrationPaymentVerifiedAt->format('d M Y, H:i') }}</span></p>
                    </div>
                    @endif
                </div>

                <!-- Actions -->
                <div class="mt-8 pt-6 border-t border-gray-200 dark:border-slate-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Aksi</h3>
                    <div class="flex flex-wrap gap-3">
                        @if($supplier->status === 'PENDING')
                            <form method="POST" action="{{ route('admin.suppliers.approve', $supplier->id) }}" class="inline">
                                @csrf
                                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                                    <i class='bx bx-check-circle'></i>
                                    Approve
                                </button>
                            </form>
                            <button onclick="showRejectModal({{ $supplier->id }})" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                                <i class='bx bx-x-circle'></i>
                                Reject
                            </button>
                        @elseif($supplier->status === 'ACTIVE')
                            <button onclick="showSuspendModal({{ $supplier->id }})" class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                                <i class='bx bx-pause-circle'></i>
                                Suspend
                            </button>
                        @elseif($supplier->status === 'SUSPENDED')
                            <form method="POST" action="{{ route('admin.suppliers.activate', $supplier->id) }}" class="inline">
                                @csrf
                                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                                    <i class='bx bx-check-circle'></i>
                                    Aktifkan Kembali
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                @if($supplier->rejectionReason)
                <div class="mt-6 p-4 bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/30 rounded-lg">
                    <h4 class="text-sm font-semibold text-red-800 dark:text-red-400 mb-2">Alasan Reject:</h4>
                    <p class="text-red-700 dark:text-red-300">{{ $supplier->rejectionReason }}</p>
                </div>
                @endif

                @if($supplier->suspensionReason)
                <div class="mt-6 p-4 bg-orange-50 dark:bg-orange-500/10 border border-orange-200 dark:border-orange-500/30 rounded-lg">
                    <h4 class="text-sm font-semibold text-orange-800 dark:text-orange-400 mb-2">Alasan Suspend:</h4>
                    <p class="text-orange-700 dark:text-orange-300">{{ $supplier->suspensionReason }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Products List -->
        @if($supplier->products->count() > 0)
        <div class="mt-6 bg-white dark:bg-darkCard rounded-lg shadow-sm border border-gray-200 dark:border-slate-700 overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 dark:bg-slate-800/50 border-b border-gray-200 dark:border-slate-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Daftar Produk ({{ $supplier->products->count() }})</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
                    <thead class="bg-gray-50 dark:bg-slate-800/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Nama Produk</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Kategori</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Stok</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Harga</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-darkCard divide-y divide-gray-200 dark:divide-slate-700">
                        @foreach($supplier->products as $product)
                        <tr class="hover:bg-gray-50 dark:hover:bg-slate-800/30">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">{{ $product->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">{{ $product->category->name ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">{{ $product->stock }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">Rp {{ number_format($product->sellPrice, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs rounded-full
                                    @if($product->status === 'ACTIVE') bg-green-100 dark:bg-green-500/20 text-green-800 dark:text-green-400
                                    @else bg-gray-100 dark:bg-gray-500/20 text-gray-800 dark:text-gray-400
                                    @endif">
                                    {{ $product->status }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    @endif
</div>

<!-- Modal Reject -->
<div id="rejectModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white dark:bg-darkCard rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Reject Supplier</h3>
        <form id="rejectForm" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Alasan Reject</label>
                <textarea name="reason" required rows="4" class="w-full border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-gray-900 dark:text-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 outline-none" placeholder="Jelaskan alasan reject..."></textarea>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeRejectModal()" class="px-4 py-2 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-800">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg">
                    Reject
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Suspend -->
<div id="suspendModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white dark:bg-darkCard rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Suspend Supplier</h3>
        <form id="suspendForm" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Alasan Suspend</label>
                <textarea name="reason" required rows="4" class="w-full border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-gray-900 dark:text-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 outline-none" placeholder="Jelaskan alasan suspend..."></textarea>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeSuspendModal()" class="px-4 py-2 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-800">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg">
                    Suspend
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Payment Proof -->
<div id="paymentProofModal" class="hidden fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 p-4">
    <div class="relative bg-white dark:bg-darkCard rounded-lg overflow-hidden max-w-2xl w-full">
        <button onclick="closePaymentProofModal()" class="absolute top-4 right-4 bg-gray-700 hover:bg-gray-800 text-white p-2 rounded-lg z-10">
            <i class='bx bx-x text-2xl'></i>
        </button>
        <img id="paymentProofImg" src="" alt="Bukti Transfer" class="w-full h-auto">
    </div>
</div>

<script>
function showRejectModal(id) {
    document.getElementById('rejectForm').action = `/admin/suppliers/${id}/reject`;
    document.getElementById('rejectModal').classList.remove('hidden');
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
}

function showSuspendModal(id) {
    document.getElementById('suspendForm').action = `/admin/suppliers/${id}/suspend`;
    document.getElementById('suspendModal').classList.remove('hidden');
}

function closeSuspendModal() {
    document.getElementById('suspendModal').classList.add('hidden');
}

function showPaymentProofModal(imageUrl) {
    document.getElementById('paymentProofImg').src = imageUrl;
    document.getElementById('paymentProofModal').classList.remove('hidden');
}

function closePaymentProofModal() {
    document.getElementById('paymentProofModal').classList.add('hidden');
}
</script>
@endsection
