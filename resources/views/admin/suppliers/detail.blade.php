@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <a href="{{ route('admin.suppliers') }}" class="text-blue-600 hover:text-blue-800 flex items-center gap-2">
            <i class='bx bx-arrow-back'></i>
            Kembali ke Daftar Supplier
        </a>
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
                        <div x-data="{ showModal: false }">
                            <!-- Thumbnail -->
                            <div @click="showModal = true" class="cursor-pointer group relative">
                                <img src="{{ asset('storage/' . $supplier->registrationPaymentProof) }}" 
                                     alt="Bukti Pembayaran" 
                                     class="w-full h-64 object-contain border border-gray-300 rounded-lg bg-white group-hover:opacity-75 transition-opacity">
                                <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                    <span class="bg-black bg-opacity-50 text-white px-3 py-1 rounded-full text-sm flex items-center">
                                        <i class='bx bx-zoom-in mr-1'></i> Lihat Full
                                    </span>
                                </div>
                            </div>

                            <!-- Modal -->
                            <div x-show="showModal" 
                                 style="display: none;"
                                 class="fixed inset-0 z-50 overflow-y-auto" 
                                 aria-labelledby="modal-title" 
                                 role="dialog" 
                                 aria-modal="true">
                                
                                <!-- Backdrop -->
                                <div x-show="showModal"
                                     x-transition:enter="ease-out duration-300"
                                     x-transition:enter-start="opacity-0"
                                     x-transition:enter-end="opacity-100"
                                     x-transition:leave="ease-in duration-200"
                                     x-transition:leave-start="opacity-100"
                                     x-transition:leave-end="opacity-0"
                                     class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity backdrop-blur-sm" 
                                     @click="showModal = false"></div>

                                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                                    <div x-show="showModal"
                                         x-transition:enter="ease-out duration-300"
                                         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                         x-transition:leave="ease-in duration-200"
                                         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                         class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-4xl">
                                        
                                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                                            <div class="flex justify-between items-center mb-4 border-b pb-3">
                                                <h3 class="text-lg font-semibold leading-6 text-gray-900" id="modal-title">
                                                    Bukti Pembayaran
                                                </h3>
                                                <button @click="showModal = false" type="button" class="text-gray-400 hover:text-gray-500 transition-colors rounded-full p-1 hover:bg-gray-100">
                                                    <i class='bx bx-x text-2xl'></i>
                                                </button>
                                            </div>
                                            <div class="mt-2 flex justify-center bg-gray-50 rounded-lg p-2 border border-gray-100">
                                                <img src="{{ asset('storage/' . $supplier->registrationPaymentProof) }}" 
                                                     alt="Bukti Pembayaran Full" 
                                                     class="max-h-[75vh] w-auto object-contain rounded shadow-sm">
                                            </div>
                                            <div class="mt-4 flex justify-end">
                                                <a href="{{ asset('storage/' . $supplier->registrationPaymentProof) }}" 
                                                   target="_blank"
                                                   class="text-sm text-blue-600 hover:text-blue-800 flex items-center gap-1">
                                                    <i class='bx bx-link-external'></i> Buka di tab baru
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
                                <div x-data="{ showVerifyModal: false, showRejectModal: false }" class="flex flex-col gap-2 mt-4">
                                    <!-- Action Buttons -->
                                    <button @click="showVerifyModal = true" type="button" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center justify-center gap-2 transition-colors">
                                        <i class='bx bx-check-circle'></i>
                                        Verifikasi Pembayaran
                                    </button>
                                    
                                    <button @click="showRejectModal = true" type="button" class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg flex items-center justify-center gap-2 transition-colors">
                                        <i class='bx bx-x-circle'></i>
                                        Tolak Pembayaran
                                    </button>

                                    <!-- Verify Modal -->
                                    <div x-show="showVerifyModal" 
                                         style="display: none;"
                                         class="fixed inset-0 z-50 overflow-y-auto" 
                                         aria-labelledby="modal-title" 
                                         role="dialog" 
                                         aria-modal="true">
                                        <div x-show="showVerifyModal"
                                             x-transition:enter="ease-out duration-300"
                                             x-transition:enter-start="opacity-0"
                                             x-transition:enter-end="opacity-100"
                                             x-transition:leave="ease-in duration-200"
                                             x-transition:leave-start="opacity-100"
                                             x-transition:leave-end="opacity-0"
                                             class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity backdrop-blur-sm" 
                                             @click="showVerifyModal = false"></div>

                                        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                                            <div x-show="showVerifyModal"
                                                 x-transition:enter="ease-out duration-300"
                                                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                                 x-transition:leave="ease-in duration-200"
                                                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                                 class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                                                
                                                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                                                    <div class="sm:flex sm:items-start">
                                                        <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                                                            <i class='bx bx-check text-2xl text-green-600'></i>
                                                        </div>
                                                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                                                            <h3 class="text-lg font-semibold leading-6 text-gray-900" id="modal-title">Verifikasi Pembayaran</h3>
                                                            <div class="mt-2">
                                                                <p class="text-sm text-gray-500">Apakah Anda yakin ingin memverifikasi pembayaran ini? Pastikan nominal dan bukti transfer sudah sesuai.</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                                                    <form method="POST" action="{{ route('admin.suppliers.verifyPayment', $supplier->id) }}">
                                                        @csrf
                                                        <button type="submit" class="inline-flex w-full justify-center rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500 sm:ml-3 sm:w-auto">
                                                            Ya, Verifikasi
                                                        </button>
                                                    </form>
                                                    <button @click="showVerifyModal = false" type="button" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">Batal</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Reject Modal -->
                                    <div x-show="showRejectModal" 
                                         style="display: none;"
                                         class="fixed inset-0 z-50 overflow-y-auto" 
                                         aria-labelledby="modal-title" 
                                         role="dialog" 
                                         aria-modal="true">
                                        <div x-show="showRejectModal"
                                             x-transition:enter="ease-out duration-300"
                                             x-transition:enter-start="opacity-0"
                                             x-transition:enter-end="opacity-100"
                                             x-transition:leave="ease-in duration-200"
                                             x-transition:leave-start="opacity-100"
                                             x-transition:leave-end="opacity-0"
                                             class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity backdrop-blur-sm" 
                                             @click="showRejectModal = false"></div>

                                        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                                            <div x-show="showRejectModal"
                                                 x-transition:enter="ease-out duration-300"
                                                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                                 x-transition:leave="ease-in duration-200"
                                                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                                 class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                                                
                                                <form method="POST" action="{{ route('admin.suppliers.rejectPayment', $supplier->id) }}">
                                                    @csrf
                                                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                                                        <div class="sm:flex sm:items-start">
                                                            <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                                                <i class='bx bx-x text-2xl text-red-600'></i>
                                                            </div>
                                                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                                                                <h3 class="text-lg font-semibold leading-6 text-gray-900" id="modal-title">Tolak Pembayaran</h3>
                                                                <div class="mt-2">
                                                                    <p class="text-sm text-gray-500 mb-4">Silakan masukkan alasan penolakan pembayaran ini.</p>
                                                                    <textarea name="reason" rows="3" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-red-600 sm:text-sm sm:leading-6" placeholder="Contoh: Bukti transfer tidak terbaca / Nominal tidak sesuai" required></textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                                                        <button type="submit" class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto">
                                                            Tolak Pembayaran
                                                        </button>
                                                        <button @click="showRejectModal = false" type="button" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">Batal</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
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
                            <button type="button" data-supplier-id="{{ $supplier->id }}" class="btn-reject bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                                <i class='bx bx-x-circle'></i>
                                Reject
                            </button>
                        @elseif($supplier->status === 'ACTIVE')
                            <button type="button" data-supplier-id="{{ $supplier->id }}" class="btn-suspend bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
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
// Event listeners untuk buttons dengan data attributes (modern approach)
document.addEventListener('DOMContentLoaded', function() {
    // Reject button
    const btnReject = document.querySelector('.btn-reject');
    if (btnReject) {
        btnReject.addEventListener('click', function() {
            const id = this.dataset.supplierId;
            document.getElementById('rejectForm').action = `/admin/suppliers/${id}/reject`;
            document.getElementById('rejectModal').classList.remove('hidden');
        });
    }

    // Suspend button
    const btnSuspend = document.querySelector('.btn-suspend');
    if (btnSuspend) {
        btnSuspend.addEventListener('click', function() {
            const id = this.dataset.supplierId;
            document.getElementById('suspendForm').action = `/admin/suppliers/${id}/suspend`;
            document.getElementById('suspendModal').classList.remove('hidden');
        });
    }

    // Reject Payment button
    const btnRejectPayment = document.querySelector('.btn-reject-payment');
    if (btnRejectPayment) {
        btnRejectPayment.addEventListener('click', function() {
            const id = this.dataset.supplierId;
            document.getElementById('rejectPaymentForm').action = `/admin/suppliers/${id}/reject-payment`;
            document.getElementById('rejectPaymentModal').classList.remove('hidden');
        });
    }
});

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
}

function closeSuspendModal() {
    document.getElementById('suspendModal').classList.add('hidden');
}

function closeRejectPaymentModal() {
    document.getElementById('rejectPaymentModal').classList.add('hidden');
}
</script>
@endsection
