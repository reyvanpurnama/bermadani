@extends('layouts.admin')

@section('title', 'Detail Supplier')
@section('page-title', 'Detail Supplier')
@section('main-class', 'p-4 sm:p-6')

@section('content')
@if(!$supplier)
    <div class="mx-auto max-w-3xl">
        <a href="{{ route('admin.suppliers') }}"
            class="mb-5 inline-flex items-center gap-2 text-sm font-semibold text-slate-500 hover:text-primary dark:text-slate-400 dark:hover:text-white transition-colors">
            <i class='bx bx-arrow-back text-lg'></i>
            Kembali ke daftar supplier
        </a>

        <div class="bg-white dark:bg-darkCard rounded-2xl shadow-sm border border-rose-100 dark:border-rose-900/50 p-8 text-center">
            <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-rose-50 text-rose-500 dark:bg-rose-500/10 dark:text-rose-400">
                <i class='bx bx-store-alt text-3xl'></i>
            </div>
            <h1 class="text-lg font-bold text-slate-900 dark:text-white">Supplier tidak ditemukan</h1>
            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Data supplier ini mungkin sudah dihapus atau ID tidak valid.</p>
        </div>
    </div>
@else
    @php
        $products = $supplier->products ?? collect();
        $productLimit = max((int) ($supplier->maxActiveProducts ?? 0), 1);
        $currentActiveProducts = (int) ($supplier->currentActiveProducts ?? $products->where('status', 'ACTIVE')->count());
        $usagePercent = min(100, round(($currentActiveProducts / $productLimit) * 100));

        $statusConfig = match($supplier->status) {
            'ACTIVE' => [
                'label' => 'Aktif',
                'icon' => 'bx-check-circle',
                'badge' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-400',
                'ring' => 'ring-emerald-200 dark:ring-emerald-500/20',
            ],
            'PENDING' => [
                'label' => 'Menunggu Approval',
                'icon' => 'bx-time-five',
                'badge' => 'bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-400',
                'ring' => 'ring-amber-200 dark:ring-amber-500/20',
            ],
            'APPROVED' => [
                'label' => 'Disetujui',
                'icon' => 'bx-like',
                'badge' => 'bg-blue-100 text-blue-700 dark:bg-blue-500/15 dark:text-blue-400',
                'ring' => 'ring-blue-200 dark:ring-blue-500/20',
            ],
            'SUSPENDED' => [
                'label' => 'Ditangguhkan',
                'icon' => 'bx-block',
                'badge' => 'bg-rose-100 text-rose-700 dark:bg-rose-500/15 dark:text-rose-400',
                'ring' => 'ring-rose-200 dark:ring-rose-500/20',
            ],
            'REJECTED' => [
                'label' => 'Ditolak',
                'icon' => 'bx-x-circle',
                'badge' => 'bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300',
                'ring' => 'ring-slate-200 dark:ring-slate-600',
            ],
            default => [
                'label' => $supplier->status ?? 'Unknown',
                'icon' => 'bx-info-circle',
                'badge' => 'bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300',
                'ring' => 'ring-slate-200 dark:ring-slate-600',
            ],
        };

        $paymentConfig = match($supplier->registrationPaymentStatus) {
            'VERIFIED' => [
                'label' => 'Pembayaran Terverifikasi',
                'short' => 'Terverifikasi',
                'icon' => 'bx-badge-check',
                'badge' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-400',
            ],
            'PENDING_VERIFICATION' => [
                'label' => 'Menunggu Verifikasi Pembayaran',
                'short' => 'Menunggu Verifikasi',
                'icon' => 'bx-time-five',
                'badge' => 'bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-400',
            ],
            'REJECTED' => [
                'label' => 'Pembayaran Ditolak',
                'short' => 'Ditolak',
                'icon' => 'bx-x-circle',
                'badge' => 'bg-rose-100 text-rose-700 dark:bg-rose-500/15 dark:text-rose-400',
            ],
            'UNPAID' => [
                'label' => 'Belum Dibayar',
                'short' => 'Belum Dibayar',
                'icon' => 'bx-wallet',
                'badge' => 'bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300',
            ],
            default => [
                'label' => $supplier->registrationPaymentStatus ?? 'Unknown',
                'short' => $supplier->registrationPaymentStatus ?? 'Unknown',
                'icon' => 'bx-wallet',
                'badge' => 'bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300',
            ],
        };

        $initial = strtoupper(substr($supplier->businessName ?: $supplier->ownerName ?: 'S', 0, 1));
    @endphp

    <div x-data="{
            paymentPreviewOpen: false,
            verifyPaymentOpen: false,
            rejectPaymentOpen: false,
            rejectSupplierOpen: false,
            suspendSupplierOpen: false,
        }"
        class="space-y-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex items-start gap-4">
                <a href="{{ route('admin.suppliers') }}"
                    class="mt-1 flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-slate-500 shadow-sm border border-slate-100 hover:text-primary hover:border-indigo-200 dark:bg-darkCard dark:border-slate-700 dark:text-slate-400 dark:hover:text-white transition-colors">
                    <i class='bx bx-arrow-back text-xl'></i>
                </a>
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider ring-1 {{ $statusConfig['badge'] }} {{ $statusConfig['ring'] }}">
                            <i class='bx {{ $statusConfig['icon'] }} text-sm'></i>
                            {{ $statusConfig['label'] }}
                        </span>
                        <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider {{ $paymentConfig['badge'] }}">
                            <i class='bx {{ $paymentConfig['icon'] }} text-sm'></i>
                            {{ $paymentConfig['short'] }}
                        </span>
                    </div>
                    <h1 class="mt-2 text-2xl font-black tracking-tight text-slate-950 dark:text-white sm:text-3xl">
                        {{ $supplier->businessName }}
                    </h1>
                    <div class="mt-2 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-slate-500 dark:text-slate-400">
                        <span class="inline-flex items-center gap-1 font-mono font-semibold text-slate-600 dark:text-slate-300">
                            <i class='bx bx-barcode'></i>
                            {{ $supplier->code }}
                        </span>
                        <span class="hidden h-1 w-1 rounded-full bg-slate-300 dark:bg-slate-600 sm:inline-block"></span>
                        <span class="inline-flex items-center gap-1">
                            <i class='bx bx-calendar'></i>
                            Bergabung {{ optional($supplier->created_at)->format('d M Y, H:i') ?? '-' }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap gap-2">
                @if($supplier->registrationPaymentStatus === 'PENDING_VERIFICATION')
                    <button type="button" @click="verifyPaymentOpen = true"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-bold text-white shadow-lg shadow-emerald-500/20 hover:bg-emerald-700 transition-colors">
                        <i class='bx bx-check-shield text-lg'></i>
                        Verifikasi Pembayaran
                    </button>
                    <button type="button" @click="rejectPaymentOpen = true"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-white px-4 py-2.5 text-sm font-bold text-rose-600 shadow-sm border border-rose-200 hover:bg-rose-50 dark:bg-darkCard dark:border-rose-900/50 dark:text-rose-400 dark:hover:bg-rose-500/10 transition-colors">
                        <i class='bx bx-x-circle text-lg'></i>
                        Tolak Pembayaran
                    </button>
                @endif

                @if($supplier->status === 'PENDING')
                    <form method="POST" action="{{ route('admin.suppliers.approve', $supplier->id) }}">
                        @csrf
                        <button type="submit"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-4 py-2.5 text-sm font-bold text-white shadow-lg shadow-indigo-500/20 hover:bg-indigo-700 transition-colors">
                            <i class='bx bx-check-circle text-lg'></i>
                            Approve Supplier
                        </button>
                    </form>
                    <button type="button" @click="rejectSupplierOpen = true"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-white px-4 py-2.5 text-sm font-bold text-rose-600 shadow-sm border border-rose-200 hover:bg-rose-50 dark:bg-darkCard dark:border-rose-900/50 dark:text-rose-400 dark:hover:bg-rose-500/10 transition-colors">
                        <i class='bx bx-x text-lg'></i>
                        Reject
                    </button>
                @elseif($supplier->status === 'ACTIVE')
                    <button type="button" @click="suspendSupplierOpen = true"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-white px-4 py-2.5 text-sm font-bold text-amber-600 shadow-sm border border-amber-200 hover:bg-amber-50 dark:bg-darkCard dark:border-amber-900/50 dark:text-amber-400 dark:hover:bg-amber-500/10 transition-colors">
                        <i class='bx bx-pause-circle text-lg'></i>
                        Suspend
                    </button>
                @elseif($supplier->status === 'SUSPENDED')
                    <form method="POST" action="{{ route('admin.suppliers.activate', $supplier->id) }}">
                        @csrf
                        <button type="submit"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-bold text-white shadow-lg shadow-emerald-500/20 hover:bg-emerald-700 transition-colors">
                            <i class='bx bx-refresh text-lg'></i>
                            Aktifkan Kembali
                        </button>
                    </form>
                @endif
            </div>
        </div>

        @if (session()->has('success'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700 dark:border-emerald-500/20 dark:bg-emerald-500/10 dark:text-emerald-400 flex items-center gap-2">
                <i class='bx bxs-check-circle text-xl'></i>
                {{ session('success') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-700 dark:border-rose-500/20 dark:bg-rose-500/10 dark:text-rose-400 flex items-center gap-2">
                <i class='bx bxs-error-circle text-xl'></i>
                {{ session('error') }}
            </div>
        @endif

        <section class="relative overflow-hidden rounded-3xl bg-slate-950 shadow-xl shadow-slate-900/10 dark:shadow-black/20">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(79,70,229,0.35),_transparent_34%),radial-gradient(circle_at_bottom_right,_rgba(16,185,129,0.22),_transparent_32%)]"></div>
            <div class="absolute -right-12 -top-12 h-48 w-48 rounded-full bg-white/10 blur-2xl"></div>
            <div class="relative grid gap-6 p-6 lg:grid-cols-[1fr_320px] lg:p-7">
                <div class="flex flex-col gap-5 sm:flex-row sm:items-center">
                    <div class="flex h-20 w-20 shrink-0 items-center justify-center rounded-3xl bg-white/10 text-4xl font-black text-white ring-1 ring-white/15 shadow-2xl">
                        {{ $initial }}
                    </div>
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.25em] text-indigo-200">Profil Mitra Supplier</p>
                        <h2 class="mt-2 text-3xl font-black tracking-tight text-white sm:text-4xl">{{ $supplier->businessName }}</h2>
                        <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-300">
                            {{ $supplier->description ?: 'Belum ada deskripsi bisnis. Lengkapi data ini jika supplier membutuhkan konteks tambahan untuk audit atau review produk.' }}
                        </p>
                    </div>
                </div>

                <div class="rounded-2xl bg-white/10 p-4 ring-1 ring-white/10 backdrop-blur">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-300">Kapasitas Produk</p>
                            <p class="mt-1 text-2xl font-black text-white">{{ $currentActiveProducts }} / {{ $supplier->maxActiveProducts ?? 0 }}</p>
                        </div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white/10 text-indigo-100">
                            <i class='bx bx-package text-2xl'></i>
                        </div>
                    </div>
                    <div class="mt-4 h-2 rounded-full bg-white/10 overflow-hidden">
                        <div class="h-full rounded-full bg-gradient-to-r from-indigo-300 to-emerald-300" style="width: {{ $usagePercent }}%"></div>
                    </div>
                    <p class="mt-2 text-[11px] text-slate-300">{{ $usagePercent }}% dari limit produk aktif saat ini.</p>
                </div>
            </div>
        </section>

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-2xl bg-white p-4 shadow-sm border border-slate-100 dark:bg-darkCard dark:border-slate-700">
                <div class="flex items-center justify-between gap-3">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Status Akun</p>
                    <i class='bx {{ $statusConfig['icon'] }} text-xl text-primary'></i>
                </div>
                <p class="mt-3 text-lg font-black text-slate-900 dark:text-white">{{ $statusConfig['label'] }}</p>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">isActive: {{ $supplier->isActive ? 'Ya' : 'Tidak' }}</p>
            </div>

            <div class="rounded-2xl bg-white p-4 shadow-sm border border-slate-100 dark:bg-darkCard dark:border-slate-700">
                <div class="flex items-center justify-between gap-3">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Registrasi</p>
                    <i class='bx bx-receipt text-xl text-emerald-500'></i>
                </div>
                <p class="mt-3 text-lg font-black text-slate-900 dark:text-white">Rp {{ number_format((float) ($supplier->registrationFee ?? 0), 0, ',', '.') }}</p>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ $paymentConfig['label'] }}</p>
            </div>

            <div class="rounded-2xl bg-white p-4 shadow-sm border border-slate-100 dark:bg-darkCard dark:border-slate-700">
                <div class="flex items-center justify-between gap-3">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Total Produk</p>
                    <i class='bx bx-box text-xl text-amber-500'></i>
                </div>
                <p class="mt-3 text-lg font-black text-slate-900 dark:text-white">{{ $products->count() }} SKU</p>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ $products->where('approvalStatus', 'APPROVED')->count() }} produk approved</p>
            </div>

            <div class="rounded-2xl bg-white p-4 shadow-sm border border-slate-100 dark:bg-darkCard dark:border-slate-700">
                <div class="flex items-center justify-between gap-3">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Kontak</p>
                    <i class='bx bx-phone-call text-xl text-sky-500'></i>
                </div>
                <p class="mt-3 truncate text-lg font-black text-slate-900 dark:text-white">{{ $supplier->phone }}</p>
                <p class="mt-1 truncate text-xs text-slate-500 dark:text-slate-400">{{ $supplier->email }}</p>
            </div>
        </section>

        <section class="grid gap-6 xl:grid-cols-[1fr_380px]">
            <div class="space-y-6">
                <div class="grid gap-6 lg:grid-cols-2">
                    <div class="rounded-2xl bg-white p-5 shadow-sm border border-slate-100 dark:bg-darkCard dark:border-slate-700">
                        <div class="mb-5 flex items-center justify-between gap-3 border-b border-slate-100 pb-4 dark:border-slate-700">
                            <div>
                                <h3 class="text-sm font-black text-slate-900 dark:text-white">Informasi Bisnis</h3>
                                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Identitas toko dan kategori produk.</p>
                            </div>
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-50 text-primary dark:bg-indigo-500/10">
                                <i class='bx bx-store text-xl'></i>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Nama Bisnis</p>
                                <p class="mt-1 text-sm font-bold text-slate-900 dark:text-white">{{ $supplier->businessName }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Kategori Produk</p>
                                <p class="mt-1 text-sm text-slate-700 dark:text-slate-300">{{ $supplier->productCategory ?: '-' }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Alamat</p>
                                <p class="mt-1 text-sm leading-6 text-slate-700 dark:text-slate-300">{{ $supplier->address ?: '-' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl bg-white p-5 shadow-sm border border-slate-100 dark:bg-darkCard dark:border-slate-700">
                        <div class="mb-5 flex items-center justify-between gap-3 border-b border-slate-100 pb-4 dark:border-slate-700">
                            <div>
                                <h3 class="text-sm font-black text-slate-900 dark:text-white">Pemilik & Kontak</h3>
                                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Data PIC supplier untuk follow-up.</p>
                            </div>
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400">
                                <i class='bx bx-user-voice text-xl'></i>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Nama Pemilik</p>
                                <p class="mt-1 text-sm font-bold text-slate-900 dark:text-white">{{ $supplier->ownerName }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Email</p>
                                <a href="mailto:{{ $supplier->email }}" class="mt-1 inline-flex max-w-full items-center gap-2 text-sm font-semibold text-primary hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300">
                                    <i class='bx bx-envelope'></i>
                                    <span class="truncate">{{ $supplier->email }}</span>
                                </a>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Telepon</p>
                                <a href="tel:{{ $supplier->phone }}" class="mt-1 inline-flex items-center gap-2 text-sm font-semibold text-slate-800 hover:text-primary dark:text-white dark:hover:text-indigo-300">
                                    <i class='bx bx-phone'></i>
                                    {{ $supplier->phone }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                @if($supplier->rejectedReason || $supplier->suspensionReason)
                    <div class="grid gap-4 lg:grid-cols-2">
                        @if($supplier->rejectedReason)
                            <div class="rounded-2xl border border-rose-200 bg-rose-50 p-5 dark:border-rose-500/20 dark:bg-rose-500/10">
                                <div class="flex items-start gap-3">
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-rose-500 dark:bg-rose-500/10 dark:text-rose-400">
                                        <i class='bx bx-message-error text-xl'></i>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-black text-rose-800 dark:text-rose-300">Alasan Reject</h4>
                                        <p class="mt-1 text-sm leading-6 text-rose-700 dark:text-rose-200">{{ $supplier->rejectedReason }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if($supplier->suspensionReason)
                            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 dark:border-amber-500/20 dark:bg-amber-500/10">
                                <div class="flex items-start gap-3">
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-amber-500 dark:bg-amber-500/10 dark:text-amber-400">
                                        <i class='bx bx-block text-xl'></i>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-black text-amber-800 dark:text-amber-300">Alasan Suspend</h4>
                                        <p class="mt-1 text-sm leading-6 text-amber-700 dark:text-amber-200">{{ $supplier->suspensionReason }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                <div class="rounded-2xl bg-white shadow-sm border border-slate-100 dark:bg-darkCard dark:border-slate-700 overflow-hidden">
                    <div class="flex flex-col gap-3 border-b border-slate-100 p-5 dark:border-slate-700 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h3 class="text-sm font-black text-slate-900 dark:text-white">Daftar Produk</h3>
                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ $products->count() }} produk terhubung ke supplier ini.</p>
                        </div>
                        <span class="inline-flex w-fit items-center gap-1.5 rounded-full bg-slate-100 px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                            <i class='bx bx-package'></i>
                            {{ $currentActiveProducts }} aktif
                        </span>
                    </div>

                    @if($products->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full min-w-[760px] text-left text-sm text-slate-600 dark:text-slate-400">
                                <thead class="bg-slate-50 text-[10px] font-bold uppercase tracking-widest text-slate-400 dark:bg-slate-800/60 dark:text-slate-500">
                                    <tr>
                                        <th class="px-5 py-3">Produk</th>
                                        <th class="px-5 py-3">Kategori</th>
                                        <th class="px-5 py-3 text-center">Stok</th>
                                        <th class="px-5 py-3 text-right">Harga Jual</th>
                                        <th class="px-5 py-3 text-right">Margin</th>
                                        <th class="px-5 py-3 text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                                    @foreach($products as $product)
                                        @php
                                            $productStatusClass = $product->status === 'ACTIVE'
                                                ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-400'
                                                : 'bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300';

                                            $approvalClass = match($product->approvalStatus) {
                                                'APPROVED' => 'bg-blue-100 text-blue-700 dark:bg-blue-500/15 dark:text-blue-400',
                                                'PENDING' => 'bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-400',
                                                'REJECTED' => 'bg-rose-100 text-rose-700 dark:bg-rose-500/15 dark:text-rose-400',
                                                default => 'bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300',
                                            };
                                        @endphp
                                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors">
                                            <td class="px-5 py-4">
                                                <div class="flex items-center gap-3">
                                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400">
                                                        <i class='bx bx-cube text-xl'></i>
                                                    </div>
                                                    <div class="min-w-0">
                                                        <p class="truncate font-bold text-slate-900 dark:text-white">{{ $product->name }}</p>
                                                        <p class="mt-0.5 text-[11px] text-slate-400">SKU: {{ $product->sku ?: '-' }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-5 py-4 text-slate-600 dark:text-slate-300">{{ $product->category->name ?? '-' }}</td>
                                            <td class="px-5 py-4 text-center font-bold text-slate-900 dark:text-white">{{ $product->stock }}</td>
                                            <td class="px-5 py-4 text-right font-bold text-slate-900 dark:text-white">Rp {{ number_format((float) $product->sellPrice, 0, ',', '.') }}</td>
                                            <td class="px-5 py-4 text-right font-semibold text-emerald-600 dark:text-emerald-400">Rp {{ number_format((float) $product->margin, 0, ',', '.') }}</td>
                                            <td class="px-5 py-4">
                                                <div class="flex flex-col items-center gap-1">
                                                    <span class="rounded-full px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide {{ $productStatusClass }}">{{ $product->status ?? '-' }}</span>
                                                    <span class="rounded-full px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide {{ $approvalClass }}">{{ $product->approvalStatus ?? 'N/A' }}</span>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-10 text-center">
                            <div class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400 dark:bg-slate-800 dark:text-slate-500">
                                <i class='bx bx-package text-3xl'></i>
                            </div>
                            <h4 class="text-sm font-bold text-slate-900 dark:text-white">Belum ada produk</h4>
                            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Produk supplier akan tampil di sini setelah dibuat atau disetujui.</p>
                        </div>
                    @endif
                </div>
            </div>

            <aside class="space-y-6">
                <div class="rounded-2xl bg-white p-5 shadow-sm border border-slate-100 dark:bg-darkCard dark:border-slate-700">
                    <div class="mb-5 flex items-center justify-between gap-3 border-b border-slate-100 pb-4 dark:border-slate-700">
                        <div>
                            <h3 class="text-sm font-black text-slate-900 dark:text-white">Pembayaran Registrasi</h3>
                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Validasi bukti transfer supplier.</p>
                        </div>
                        <span class="rounded-full px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide {{ $paymentConfig['badge'] }}">{{ $paymentConfig['short'] }}</span>
                    </div>

                    @if($supplier->registrationPaymentProof)
                        <button type="button" @click="paymentPreviewOpen = true" class="group relative w-full overflow-hidden rounded-2xl border border-slate-200 bg-slate-50 dark:border-slate-700 dark:bg-slate-800/70">
                            <img src="{{ asset('storage/' . $supplier->registrationPaymentProof) }}"
                                alt="Bukti Pembayaran Registrasi"
                                class="h-56 w-full object-contain p-3 transition duration-300 group-hover:scale-[1.02]">
                            <div class="absolute inset-0 flex items-center justify-center bg-slate-950/0 opacity-0 transition-all duration-300 group-hover:bg-slate-950/35 group-hover:opacity-100">
                                <span class="inline-flex items-center gap-2 rounded-full bg-white px-3 py-1.5 text-xs font-bold text-slate-900 shadow-lg">
                                    <i class='bx bx-zoom-in text-lg'></i>
                                    Lihat bukti
                                </span>
                            </div>
                        </button>
                    @else
                        <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 p-8 text-center dark:border-slate-700 dark:bg-slate-800/50">
                            <i class='bx bx-image-alt text-4xl text-slate-300 dark:text-slate-600'></i>
                            <p class="mt-2 text-sm font-bold text-slate-700 dark:text-slate-300">Belum ada bukti pembayaran</p>
                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Supplier belum mengunggah file bukti registrasi.</p>
                        </div>
                    @endif

                    <div class="mt-5 space-y-3">
                        <div class="flex items-center justify-between gap-4 rounded-xl bg-slate-50 px-3 py-2.5 dark:bg-slate-800/70">
                            <span class="text-xs text-slate-500 dark:text-slate-400">Nominal</span>
                            <span class="text-sm font-black text-slate-900 dark:text-white">Rp {{ number_format((float) ($supplier->registrationFee ?? 0), 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between gap-4 rounded-xl bg-slate-50 px-3 py-2.5 dark:bg-slate-800/70">
                            <span class="text-xs text-slate-500 dark:text-slate-400">Status</span>
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300">{{ $paymentConfig['label'] }}</span>
                        </div>
                        <div class="flex items-center justify-between gap-4 rounded-xl bg-slate-50 px-3 py-2.5 dark:bg-slate-800/70">
                            <span class="text-xs text-slate-500 dark:text-slate-400">Diverifikasi</span>
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300">{{ optional($supplier->registrationPaymentVerifiedAt)->format('d M Y, H:i') ?? '-' }}</span>
                        </div>
                    </div>

                    @if($supplier->registrationPaymentStatus === 'PENDING_VERIFICATION')
                        <div class="mt-5 grid gap-2">
                            <button type="button" @click="verifyPaymentOpen = true"
                                class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-emerald-700 transition-colors">
                                <i class='bx bx-check-shield text-lg'></i>
                                Verifikasi Pembayaran
                            </button>
                            <button type="button" @click="rejectPaymentOpen = true"
                                class="inline-flex items-center justify-center gap-2 rounded-xl bg-rose-50 px-4 py-2.5 text-sm font-bold text-rose-600 hover:bg-rose-100 dark:bg-rose-500/10 dark:text-rose-400 dark:hover:bg-rose-500/15 transition-colors">
                                <i class='bx bx-x-circle text-lg'></i>
                                Tolak Pembayaran
                            </button>
                        </div>
                    @endif
                </div>

                <div class="rounded-2xl bg-white p-5 shadow-sm border border-slate-100 dark:bg-darkCard dark:border-slate-700">
                    <div class="mb-5 border-b border-slate-100 pb-4 dark:border-slate-700">
                        <h3 class="text-sm font-black text-slate-900 dark:text-white">Panel Aksi</h3>
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Aksi administratif mengikuti status supplier saat ini.</p>
                    </div>

                    <div class="space-y-3">
                        @if($supplier->status === 'PENDING')
                            <form method="POST" action="{{ route('admin.suppliers.approve', $supplier->id) }}">
                                @csrf
                                <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-xl bg-primary px-4 py-3 text-sm font-bold text-white hover:bg-indigo-700 transition-colors">
                                    <i class='bx bx-check-circle text-lg'></i>
                                    Approve Supplier
                                </button>
                            </form>
                            <button type="button" @click="rejectSupplierOpen = true" class="flex w-full items-center justify-center gap-2 rounded-xl bg-rose-50 px-4 py-3 text-sm font-bold text-rose-600 hover:bg-rose-100 dark:bg-rose-500/10 dark:text-rose-400 dark:hover:bg-rose-500/15 transition-colors">
                                <i class='bx bx-x-circle text-lg'></i>
                                Reject Supplier
                            </button>
                        @elseif($supplier->status === 'ACTIVE')
                            <button type="button" @click="suspendSupplierOpen = true" class="flex w-full items-center justify-center gap-2 rounded-xl bg-amber-50 px-4 py-3 text-sm font-bold text-amber-600 hover:bg-amber-100 dark:bg-amber-500/10 dark:text-amber-400 dark:hover:bg-amber-500/15 transition-colors">
                                <i class='bx bx-pause-circle text-lg'></i>
                                Suspend Supplier
                            </button>
                        @elseif($supplier->status === 'SUSPENDED')
                            <form method="POST" action="{{ route('admin.suppliers.activate', $supplier->id) }}">
                                @csrf
                                <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4 py-3 text-sm font-bold text-white hover:bg-emerald-700 transition-colors">
                                    <i class='bx bx-refresh text-lg'></i>
                                    Aktifkan Kembali
                                </button>
                            </form>
                        @else
                            <div class="rounded-xl bg-slate-50 p-4 text-center dark:bg-slate-800/70">
                                <i class='bx bx-info-circle text-2xl text-slate-400'></i>
                                <p class="mt-1 text-sm font-bold text-slate-700 dark:text-slate-300">Tidak ada aksi tersedia</p>
                                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Status saat ini tidak membutuhkan aksi lanjutan.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </aside>
        </section>

        @if($supplier->registrationPaymentProof)
            <div x-cloak x-show="paymentPreviewOpen" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
                <div x-show="paymentPreviewOpen" x-transition.opacity class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm" @click="paymentPreviewOpen = false"></div>
                <div class="flex min-h-full items-center justify-center p-4">
                    <div x-show="paymentPreviewOpen" x-transition class="relative w-full max-w-5xl overflow-hidden rounded-2xl bg-white shadow-2xl border border-slate-200 dark:bg-darkCard dark:border-slate-700">
                        <div class="flex items-center justify-between gap-4 border-b border-slate-100 px-5 py-4 dark:border-slate-700">
                            <div>
                                <h3 class="text-sm font-black text-slate-900 dark:text-white">Bukti Pembayaran Registrasi</h3>
                                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ $supplier->businessName }} - {{ $supplier->code }}</p>
                            </div>
                            <button type="button" @click="paymentPreviewOpen = false" class="flex h-9 w-9 items-center justify-center rounded-full text-slate-400 hover:bg-slate-100 hover:text-slate-700 dark:hover:bg-slate-800 dark:hover:text-white transition-colors">
                                <i class='bx bx-x text-2xl'></i>
                            </button>
                        </div>
                        <div class="bg-slate-50 p-4 dark:bg-slate-900/50">
                            <img src="{{ asset('storage/' . $supplier->registrationPaymentProof) }}" alt="Bukti Pembayaran Full" class="mx-auto max-h-[74vh] w-auto rounded-xl object-contain shadow-sm">
                        </div>
                        <div class="flex justify-end border-t border-slate-100 px-5 py-4 dark:border-slate-700">
                            <a href="{{ asset('storage/' . $supplier->registrationPaymentProof) }}" target="_blank" class="inline-flex items-center gap-2 rounded-xl bg-primary px-4 py-2 text-sm font-bold text-white hover:bg-indigo-700 transition-colors">
                                <i class='bx bx-link-external text-lg'></i>
                                Buka di tab baru
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div x-cloak x-show="verifyPaymentOpen" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div x-show="verifyPaymentOpen" x-transition.opacity class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm" @click="verifyPaymentOpen = false"></div>
            <div class="flex min-h-full items-center justify-center p-4">
                <div x-show="verifyPaymentOpen" x-transition class="relative w-full max-w-lg overflow-hidden rounded-2xl bg-white shadow-2xl border border-slate-200 dark:bg-darkCard dark:border-slate-700">
                    <div class="p-6">
                        <div class="flex items-start gap-4">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400">
                                <i class='bx bx-check-shield text-2xl'></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-black text-slate-900 dark:text-white">Verifikasi Pembayaran?</h3>
                                <p class="mt-2 text-sm leading-6 text-slate-500 dark:text-slate-400">Pastikan nominal dan bukti transfer sudah sesuai sebelum supplier lanjut ke tahap approval data.</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col-reverse gap-2 bg-slate-50 px-6 py-4 dark:bg-slate-800/60 sm:flex-row sm:justify-end">
                        <button type="button" @click="verifyPaymentOpen = false" class="rounded-xl px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-200 dark:text-slate-300 dark:hover:bg-slate-700 transition-colors">Batal</button>
                        <form method="POST" action="{{ route('admin.suppliers.verifyPayment', $supplier->id) }}">
                            @csrf
                            <button type="submit" class="w-full rounded-xl bg-emerald-600 px-4 py-2 text-sm font-bold text-white hover:bg-emerald-700 transition-colors sm:w-auto">Ya, Verifikasi</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div x-cloak x-show="rejectPaymentOpen" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div x-show="rejectPaymentOpen" x-transition.opacity class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm" @click="rejectPaymentOpen = false"></div>
            <div class="flex min-h-full items-center justify-center p-4">
                <form x-show="rejectPaymentOpen" x-transition method="POST" action="{{ route('admin.suppliers.rejectPayment', $supplier->id) }}" class="relative w-full max-w-lg overflow-hidden rounded-2xl bg-white shadow-2xl border border-slate-200 dark:bg-darkCard dark:border-slate-700">
                    @csrf
                    <div class="p-6">
                        <div class="flex items-start gap-4">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-400">
                                <i class='bx bx-x-circle text-2xl'></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <h3 class="text-lg font-black text-slate-900 dark:text-white">Tolak Pembayaran</h3>
                                <p class="mt-2 text-sm leading-6 text-slate-500 dark:text-slate-400">Masukkan alasan supaya supplier paham apa yang perlu diperbaiki.</p>
                                <label class="mt-5 block text-xs font-bold text-slate-700 dark:text-slate-300">Alasan Penolakan</label>
                                <textarea name="reason" required rows="4" class="mt-2 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-900 outline-none focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20 dark:border-slate-600 dark:bg-slate-800 dark:text-white" placeholder="Contoh: Bukti transfer tidak terbaca atau nominal tidak sesuai."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col-reverse gap-2 bg-slate-50 px-6 py-4 dark:bg-slate-800/60 sm:flex-row sm:justify-end">
                        <button type="button" @click="rejectPaymentOpen = false" class="rounded-xl px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-200 dark:text-slate-300 dark:hover:bg-slate-700 transition-colors">Batal</button>
                        <button type="submit" class="rounded-xl bg-rose-600 px-4 py-2 text-sm font-bold text-white hover:bg-rose-700 transition-colors">Tolak Pembayaran</button>
                    </div>
                </form>
            </div>
        </div>

        <div x-cloak x-show="rejectSupplierOpen" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div x-show="rejectSupplierOpen" x-transition.opacity class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm" @click="rejectSupplierOpen = false"></div>
            <div class="flex min-h-full items-center justify-center p-4">
                <form x-show="rejectSupplierOpen" x-transition method="POST" action="{{ route('admin.suppliers.reject', $supplier->id) }}" class="relative w-full max-w-lg overflow-hidden rounded-2xl bg-white shadow-2xl border border-slate-200 dark:bg-darkCard dark:border-slate-700">
                    @csrf
                    <div class="p-6">
                        <div class="flex items-start gap-4">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-400">
                                <i class='bx bx-user-x text-2xl'></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <h3 class="text-lg font-black text-slate-900 dark:text-white">Reject Supplier</h3>
                                <p class="mt-2 text-sm leading-6 text-slate-500 dark:text-slate-400">Supplier akan berstatus ditolak dan alasan ini disimpan di data supplier.</p>
                                <label class="mt-5 block text-xs font-bold text-slate-700 dark:text-slate-300">Alasan Reject</label>
                                <textarea name="reason" required rows="4" class="mt-2 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-900 outline-none focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20 dark:border-slate-600 dark:bg-slate-800 dark:text-white" placeholder="Jelaskan alasan reject supplier..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col-reverse gap-2 bg-slate-50 px-6 py-4 dark:bg-slate-800/60 sm:flex-row sm:justify-end">
                        <button type="button" @click="rejectSupplierOpen = false" class="rounded-xl px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-200 dark:text-slate-300 dark:hover:bg-slate-700 transition-colors">Batal</button>
                        <button type="submit" class="rounded-xl bg-rose-600 px-4 py-2 text-sm font-bold text-white hover:bg-rose-700 transition-colors">Reject Supplier</button>
                    </div>
                </form>
            </div>
        </div>

        <div x-cloak x-show="suspendSupplierOpen" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div x-show="suspendSupplierOpen" x-transition.opacity class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm" @click="suspendSupplierOpen = false"></div>
            <div class="flex min-h-full items-center justify-center p-4">
                <form x-show="suspendSupplierOpen" x-transition method="POST" action="{{ route('admin.suppliers.suspend', $supplier->id) }}" class="relative w-full max-w-lg overflow-hidden rounded-2xl bg-white shadow-2xl border border-slate-200 dark:bg-darkCard dark:border-slate-700">
                    @csrf
                    <div class="p-6">
                        <div class="flex items-start gap-4">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400">
                                <i class='bx bx-pause-circle text-2xl'></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <h3 class="text-lg font-black text-slate-900 dark:text-white">Suspend Supplier</h3>
                                <p class="mt-2 text-sm leading-6 text-slate-500 dark:text-slate-400">Supplier akan ditangguhkan sampai diaktifkan kembali oleh admin.</p>
                                <label class="mt-5 block text-xs font-bold text-slate-700 dark:text-slate-300">Alasan Suspend</label>
                                <textarea name="reason" rows="4" class="mt-2 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-900 outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 dark:border-slate-600 dark:bg-slate-800 dark:text-white" placeholder="Jelaskan alasan suspend..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col-reverse gap-2 bg-slate-50 px-6 py-4 dark:bg-slate-800/60 sm:flex-row sm:justify-end">
                        <button type="button" @click="suspendSupplierOpen = false" class="rounded-xl px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-200 dark:text-slate-300 dark:hover:bg-slate-700 transition-colors">Batal</button>
                        <button type="submit" class="rounded-xl bg-amber-600 px-4 py-2 text-sm font-bold text-white hover:bg-amber-700 transition-colors">Suspend Supplier</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
@endsection
