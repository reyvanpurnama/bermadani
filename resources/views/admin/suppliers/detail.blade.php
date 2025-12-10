@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <a href="{{ route('admin.suppliers') }}" class="text-blue-600 hover:text-blue-800 flex items-center gap-2">
            <i class='bx bx-arrow-back'></i>
            Kembali ke Daftar Supplier
        </a>tell me thats you com in true lalalaa i need you baby lalala u fot tosee  u baby aa soihs 
    </div>
    @php
        $supplier = \App\Models\Supplier::find($supplierId);
    @endphp

    @if(!$supplier)
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
            Supplier tidak ditemukan.
        </div>
    @else
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                <h1 class="text-2xl font-bold text-white">Detail Supplier</h1>
                <p class="text-blue-100 text-sm">Informasi lengkap supplier #{{ $supplier->supplierCode }}</p>
            </div>

            <!-- Content -->
            <div class="p-6">
                <!-- Status Badge -->
                <div class="mb-6 flex flex-wrap items-center gap-3">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold
                        @if($supplier->status === 'ACTIVE') bg-green-100 text-green-800
                        @elseif($supplier->status === 'PENDING') bg-yellow-100 text-yellow-800
                        @elseif($supplier->status === 'REJECTED') bg-red-100 text-red-800
                        @elseif($supplier->status === 'SUSPENDED') bg-gray-100 text-gray-800
                        @endif">
                        {{ $supplier->status }}
                    </span>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold
                        @if($supplier->registrationPaymentStatus === 'VERIFIED') bg-green-100 text-green-800
                        @elseif($supplier->registrationPaymentStatus === 'PENDING_VERIFICATION') bg-yellow-100 text-yellow-800
                        @elseif($supplier->registrationPaymentStatus === 'REJECTED') bg-red-100 text-red-800
                        @else bg-gray-100 text-gray-800
                        @endif">
                        <i class='bx bx-money mr-1'></i>
                        Payment: {{ $supplier->registrationPaymentStatus }}
                    </span>
                </div>

                <!-- Payment Proof Section -->
                @if($supplier->registrationPaymentProof)
                <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <h3 class="text-sm font-semibold text-blue-900 mb-3">Bukti Pembayaran Registrasi</h3>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <img src="{{ asset('storage/' . $supplier->registrationPaymentProof) }}" alt="Bukti Pembayaran" class="w-full h-64 object-contain border border-gray-300 rounded-lg bg-white">
                        </div>
                        <div>
                            <div class="space-y-3">
                                <div>
                                    <p class="text-xs text-gray-600">Nominal:</p>
                                    <p class="text-lg font-bold text-gray-900">Rp {{ number_format($supplier->registrationFee, 0, ',', '.') }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-600">Status:</p>
                                    <p class="text-sm font-semibold text-gray-900">{{ $supplier->registrationPaymentStatus }}</p>
                                </div>
                                @if($supplier->registrationPaymentVerifiedAt)
                                <div>
                                    <p class="text-xs text-gray-600">Diverifikasi:</p>
                                    <p class="text-sm text-gray-900">{{ $supplier->registrationPaymentVerifiedAt->format('d M Y, H:i') }}</p>
                                </div>
                                @endif
                                @if($supplier->registrationPaymentStatus === 'PENDING_VERIFICATION')
                                <div class="flex flex-col gap-2 mt-4">
                                    <form method="POST" action="{{ route('admin.suppliers.verifyPayment', $supplier->id) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center justify-center gap-2">
                                            <i class='bx bx-check-circle'></i>
                                            Verifikasi Pembayaran
                                        </button>
                                    </form>
                                    <button onclick="showRejectPaymentModal({{ $supplier->id }})" class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg flex items-center justify-center gap-2">
                                        <i class='bx bx-x-circle'></i>
                                        Tolak Pembayaran
                                    </button>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Info Grid -->
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-500 mb-1">Kode Supplier</h3>
                        <p class="text-lg font-bold text-gray-900">{{ $supplier->supplierCode }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-500 mb-1">Nama Pemilik</h3>
                        <p class="text-lg text-gray-900">{{ $supplier->ownerName }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-500 mb-1">Nama Bisnis</h3>
                        <p class="text-lg text-gray-900">{{ $supplier->businessName }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-500 mb-1">Email</h3>
                        <p class="text-lg text-gray-900">{{ $supplier->email }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-500 mb-1">Telepon</h3>
                        <p class="text-lg text-gray-900">{{ $supplier->phone }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-500 mb-1">Kategori Produk</h3>
                        <p class="text-lg text-gray-900">{{ $supplier->productCategory ?? '-' }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <h3 class="text-sm font-semibold text-gray-500 mb-1">Alamat</h3>
                        <p class="text-lg text-gray-900">{{ $supplier->address }}</p>
                    </div>
                    @if($supplier->description)
                    <div class="md:col-span-2">
                        <h3 class="text-sm font-semibold text-gray-500 mb-1">Deskripsi</h3>
                        <p class="text-gray-900">{{ $supplier->description }}</p>
                    </div>
                    @endif
                    <div>
                        <h3 class="text-sm font-semibold text-gray-500 mb-1">Produk Aktif</h3>
                        <p class="text-lg font-bold text-gray-900">{{ $supplier->currentActiveProducts }} / {{ $supplier->maxActiveProducts }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-500 mb-1">Tanggal Daftar</h3>
                        <p class="text-lg text-gray-900">{{ $supplier->created_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>

                <!-- Actions -->
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Aksi</h3>
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
                <div class="mt-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <h4 class="text-sm font-semibold text-red-800 mb-2">Alasan Reject:</h4>
                    <p class="text-red-700">{{ $supplier->rejectionReason }}</p>
                </div>
                @endif

                @if($supplier->suspensionReason)
                <div class="mt-6 p-4 bg-orange-50 border border-orange-200 rounded-lg">
                    <h4 class="text-sm font-semibold text-orange-800 mb-2">Alasan Suspend:</h4>
                    <p class="text-orange-700">{{ $supplier->suspensionReason }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Products List -->
        @if($supplier->products->count() > 0)
        <div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Daftar Produk ({{ $supplier->products->count() }})</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Produk</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stok</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Harga</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($supplier->products as $product)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $product->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $product->category->name ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $product->stock }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Rp {{ number_format($product->sellPrice, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs rounded-full
                                    @if($product->status === 'ACTIVE') bg-green-100 text-green-800
                                    @else bg-gray-100 text-gray-800
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
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-semibold mb-4">Reject Supplier</h3>
        <form id="rejectForm" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Alasan Reject</label>
                <textarea name="reason" required rows="4" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Jelaskan alasan reject..."></textarea>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeRejectModal()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    Reject
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Suspend -->
<div id="suspendModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-semibold mb-4">Suspend Supplier</h3>
        <form id="suspendForm" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Alasan Suspend</label>
                <textarea name="reason" required rows="4" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Jelaskan alasan suspend..."></textarea>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeSuspendModal()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700">
                    Suspend
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Reject Payment -->
<div id="rejectPaymentModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-semibold mb-4">Tolak Pembayaran</h3>
        <form id="rejectPaymentForm" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Alasan Penolakan</label>
                <textarea name="reason" required rows="4" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Jelaskan alasan penolakan pembayaran..."></textarea>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeRejectPaymentModal()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    Tolak Pembayaran
                </button>
            </div>
        </form>
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

function showRejectPaymentModal(id) {
    document.getElementById('rejectPaymentForm').action = `/admin/suppliers/${id}/reject-payment`;
    document.getElementById('rejectPaymentModal').classList.remove('hidden');
}

function closeRejectPaymentModal() {
    document.getElementById('rejectPaymentModal').classList.add('hidden');
}
</script>
@endsection
