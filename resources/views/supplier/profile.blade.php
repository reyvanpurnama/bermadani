@extends('layouts.supplier')

@section('title', 'Profil Saya')

@section('content')
@php
    $supplier = Auth::guard('supplier')->user();
    $activeProductsCount = $supplier ? $supplier->products()->where('approvalStatus', 'APPROVED')->where('isActive', true)->count() : 0;
    $totalProductsCount = $supplier ? $supplier->products()->count() : 0;
    $maxProducts = ($supplier && $supplier->maxActiveProducts) ? $supplier->maxActiveProducts : 20;
    $capacityPercentage = min(100, round(($activeProductsCount / max(1, $maxProducts)) * 100));
@endphp

<div class="max-w-6xl mx-auto space-y-6">

    <!-- Top Navigation Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-zinc-900 dark:text-white tracking-tight">Profil Mitra Supplier</h1>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 font-medium mt-0.5">Informasi akun bisnis, kontak, dan rekening pencairan dana Anda</p>
        </div>
        
        <div class="flex items-center gap-2">
            <button onclick="document.getElementById('editInfoModal').showModal()"
                class="bg-white/80 dark:bg-zinc-900/80 backdrop-blur-xl text-zinc-700 dark:text-zinc-300 border border-zinc-200/80 dark:border-zinc-800/80 px-4 py-2.5 rounded-xl text-xs font-bold hover:border-[#155A6B] transition-all flex items-center gap-2 shadow-sm">
                <i class='bx bx-edit text-base text-[#155A6B] dark:text-teal-400'></i>
                Edit Informasi
            </button>

            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit"
                    class="bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 border border-rose-200/60 dark:border-rose-900/40 px-4 py-2.5 rounded-xl text-xs font-bold hover:bg-rose-100 dark:hover:bg-rose-900/60 transition-all flex items-center gap-2">
                    <i class='bx bx-log-out text-base'></i>
                    Keluar
                </button>
            </form>
        </div>
    </div>

    <!-- Profile Hero Card Header -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-[#155A6B] via-[#166072] to-[#1a6b80] p-6 sm:p-8 text-white shadow-xl shadow-[#155A6B]/20">
        <!-- Background Decorative Watermark Icon -->
        <i class='bx bx-store-alt absolute -bottom-10 -right-10 text-[180px] text-white/10 transform rotate-12 pointer-events-none'></i>

        <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-5">
                <!-- Avatar Initial Box -->
                <div class="relative shrink-0">
                    <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl bg-white/15 backdrop-blur-md border-2 border-white/30 flex items-center justify-center text-3xl sm:text-4xl font-black text-white shadow-inner">
                        {{ strtoupper(substr($supplier->businessName ?? 'S', 0, 1)) }}
                    </div>
                    <span class="absolute -bottom-1 -right-1 w-6 h-6 rounded-full bg-emerald-500 border-2 border-[#155A6B] flex items-center justify-center text-white text-xs shadow-md" title="Akun Aktif">
                        <i class='bx bx-check'></i>
                    </span>
                </div>

                <!-- Title & Identity -->
                <div class="space-y-1.5">
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <h2 class="text-2xl sm:text-3xl font-black tracking-tight text-white">{{ $supplier->businessName ?? 'Nama Toko' }}</h2>
                        <span class="bg-white/20 backdrop-blur-md border border-white/25 px-3 py-0.5 rounded-full text-[11px] font-bold text-teal-100 font-mono">
                            {{ $supplier->code ?? 'SUP-PARTNER' }}
                        </span>
                    </div>
                    <p class="text-sm font-medium text-teal-100 flex items-center gap-2">
                        <i class='bx bx-user text-base text-teal-200'></i> Pemilik: <span class="font-bold text-white">{{ $supplier->ownerName ?? '-' }}</span>
                    </p>
                    <div class="flex items-center gap-2 pt-1 flex-wrap">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/20 border border-emerald-400/30 text-emerald-200 backdrop-blur-md">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            Status: {{ $supplier->statusLabel ?? 'Aktif' }}
                        </span>
                        @if(!empty($supplier->productCategory))
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-white/10 border border-white/15 text-teal-100 backdrop-blur-md">
                                <i class='bx bx-category'></i> {{ $supplier->productCategory }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Header Quick Stats Chips -->
            <div class="grid grid-cols-2 gap-3 w-full md:w-auto shrink-0 pt-4 md:pt-0 border-t md:border-t-0 border-white/10">
                <div class="bg-white/10 backdrop-blur-md border border-white/15 rounded-2xl p-4 min-w-[120px]">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-teal-200 block">Katalog SKU</span>
                    <span class="text-2xl font-black text-white leading-tight block mt-0.5">{{ $activeProductsCount }}</span>
                    <span class="text-[10px] text-teal-100 font-medium">dari {{ $totalProductsCount }} diajukan</span>
                </div>
                <div class="bg-white/10 backdrop-blur-md border border-white/15 rounded-2xl p-4 min-w-[120px]">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-teal-200 block">Batas Kuota</span>
                    <span class="text-2xl font-black text-white leading-tight block mt-0.5">{{ $maxProducts }}</span>
                    <span class="text-[10px] text-teal-100 font-medium">Maksimum SKU</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Grid Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Column 1 & 2: Informasi Toko & Kontak (2 Cols on Large Screen) -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Card 1: Rincian Bisnis & Identitas -->
            <div class="rounded-3xl bg-white/80 dark:bg-zinc-900/80 backdrop-blur-xl p-6 border border-zinc-200/80 dark:border-zinc-800/80 shadow-md">
                <div class="flex items-center gap-2 mb-5 pb-3 border-b border-zinc-200/60 dark:border-zinc-800/60">
                    <div class="w-8 h-8 rounded-lg bg-teal-50 dark:bg-teal-950/50 text-[#155A6B] dark:text-teal-400 flex items-center justify-center">
                        <i class='bx bx-store-alt text-lg'></i>
                    </div>
                    <h3 class="text-base font-extrabold text-zinc-900 dark:text-white tracking-tight">Detail Usaha & Identitas</h3>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <span class="text-xs font-bold text-zinc-400 uppercase tracking-wider block mb-1">Nama Toko / Brand</span>
                        <p class="text-sm font-black text-zinc-900 dark:text-white flex items-center gap-2">
                            <i class='bx bx-buildings text-[#155A6B] dark:text-teal-400'></i>
                            {{ $supplier->businessName ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <span class="text-xs font-bold text-zinc-400 uppercase tracking-wider block mb-1">Nama Pemilik (Owner)</span>
                        <p class="text-sm font-black text-zinc-900 dark:text-white flex items-center gap-2">
                            <i class='bx bx-user-pin text-[#155A6B] dark:text-teal-400'></i>
                            {{ $supplier->ownerName ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <span class="text-xs font-bold text-zinc-400 uppercase tracking-wider block mb-1">Kode Kode Mitra</span>
                        <p class="text-sm font-extrabold text-[#155A6B] dark:text-teal-400 font-mono">
                            {{ $supplier->code ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <span class="text-xs font-bold text-zinc-400 uppercase tracking-wider block mb-1">Kategori Produk</span>
                        <p class="text-sm font-bold text-zinc-900 dark:text-white">
                            {{ $supplier->productCategory ?? 'Umum / Makanan' }}
                        </p>
                    </div>
                </div>

                @if(!empty($supplier->description))
                    <div class="mt-5 pt-4 border-t border-zinc-100 dark:border-zinc-800">
                        <span class="text-xs font-bold text-zinc-400 uppercase tracking-wider block mb-1.5">Deskripsi Singkat Usaha</span>
                        <div class="p-3.5 rounded-2xl bg-zinc-50 dark:bg-zinc-800/40 border border-zinc-200/60 dark:border-zinc-800/60 text-xs text-zinc-600 dark:text-zinc-300 leading-relaxed font-medium">
                            "{{ $supplier->description }}"
                        </div>
                    </div>
                @endif
            </div>

            <!-- Card 2: Informasi Kontak & Alamat -->
            <div class="rounded-3xl bg-white/80 dark:bg-zinc-900/80 backdrop-blur-xl p-6 border border-zinc-200/80 dark:border-zinc-800/80 shadow-md">
                <div class="flex items-center gap-2 mb-5 pb-3 border-b border-zinc-200/60 dark:border-zinc-800/60">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                        <i class='bx bx-map-pin text-lg'></i>
                    </div>
                    <h3 class="text-base font-extrabold text-zinc-900 dark:text-white tracking-tight">Kontak & Lokasi Operasional</h3>
                </div>

                <div class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Email -->
                        <div class="p-4 rounded-2xl bg-zinc-50/80 dark:bg-zinc-800/30 border border-zinc-200/60 dark:border-zinc-800/60 flex items-start gap-3">
                            <div class="w-9 h-9 rounded-xl bg-teal-50 dark:bg-teal-950/50 text-[#155A6B] dark:text-teal-400 flex items-center justify-center shrink-0">
                                <i class='bx bx-envelope text-lg'></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Email Resmi</span>
                                <p class="text-xs font-extrabold text-zinc-900 dark:text-white truncate mt-0.5">{{ $supplier->email ?? '-' }}</p>
                            </div>
                        </div>

                        <!-- Phone / WA -->
                        <div class="p-4 rounded-2xl bg-zinc-50/80 dark:bg-zinc-800/30 border border-zinc-200/60 dark:border-zinc-800/60 flex items-start gap-3">
                            <div class="w-9 h-9 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                                <i class='bx bx-phone-call text-lg'></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">No. Telepon / WhatsApp</span>
                                <p class="text-xs font-extrabold text-zinc-900 dark:text-white mt-0.5">{{ $supplier->phone ?? '-' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Alamat Lengkap -->
                    <div class="p-4 rounded-2xl bg-zinc-50/80 dark:bg-zinc-800/30 border border-zinc-200/60 dark:border-zinc-800/60 flex items-start gap-3">
                        <div class="w-9 h-9 rounded-xl bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                            <i class='bx bx-navigation text-lg'></i>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Alamat Usaha / Gudang</span>
                            <p class="text-xs font-semibold text-zinc-800 dark:text-zinc-200 mt-0.5 leading-relaxed">{{ $supplier->address ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Column 3 (Right Column): Bank Account & Kuota Stats -->
        <div class="space-y-6">

            <!-- Bank Account Card (Digital Card Motif) -->
            <div class="rounded-3xl bg-white/80 dark:bg-zinc-900/80 backdrop-blur-xl p-6 border border-zinc-200/80 dark:border-zinc-800/80 shadow-md">
                <div class="flex items-center justify-between mb-4 pb-3 border-b border-zinc-200/60 dark:border-zinc-800/60">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                            <i class='bx bx-credit-card text-lg'></i>
                        </div>
                        <h3 class="text-base font-extrabold text-zinc-900 dark:text-white tracking-tight">Rekening Pencairan</h3>
                    </div>
                    <span class="text-[10px] font-extrabold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800/50 px-2 py-0.5 rounded-full">
                        Aktif
                    </span>
                </div>

                <!-- Digital Bank Card Motif -->
                <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-zinc-900 via-zinc-800 to-zinc-950 p-5 text-white shadow-lg space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-black uppercase tracking-widest text-zinc-400 font-mono">
                            {{ $supplier->bankName ?? 'BANK TRANSFER' }}
                        </span>
                        <i class='bx bx-[#155A6B] bx-chip text-2xl text-amber-400'></i>
                    </div>

                    <div class="pt-2">
                        <span class="text-[10px] font-bold text-zinc-500 uppercase tracking-wider block mb-0.5">Nomor Rekening</span>
                        <p class="text-lg font-black font-mono tracking-widest text-white">
                            {{ !empty($supplier->bankAccountNumber) ? chunk_split($supplier->bankAccountNumber, 4, ' ') : 'Belum diisi' }}
                        </p>
                    </div>

                    <div class="flex items-center justify-between pt-1 border-t border-zinc-700/60">
                        <div>
                            <span class="text-[9px] font-bold text-zinc-500 uppercase tracking-wider block">Atas Nama (Owner)</span>
                            <p class="text-xs font-extrabold text-zinc-200 uppercase tracking-wide">
                                {{ $supplier->bankAccountHolderName ?? $supplier->ownerName ?? '-' }}
                            </p>
                        </div>
                        <i class='bx bx-check-circle text-xl text-teal-400'></i>
                    </div>
                </div>

                <p class="text-[11px] text-zinc-500 dark:text-zinc-400 mt-3 font-medium flex items-center gap-1.5">
                    <i class='bx bx-info-circle text-teal-500 text-sm shrink-0'></i>
                    Uang hasil penjualan konsinyasi ditransfer langsung ke rekening di atas.
                </p>
            </div>

            <!-- Kuota SKU Card -->
            <div class="rounded-3xl bg-white/80 dark:bg-zinc-900/80 backdrop-blur-xl p-6 border border-zinc-200/80 dark:border-zinc-800/80 shadow-md space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 flex items-center justify-center">
                            <i class='bx bx-package text-lg'></i>
                        </div>
                        <h3 class="text-sm font-extrabold text-zinc-900 dark:text-white tracking-tight">Kapasitas Produk</h3>
                    </div>
                    <span class="text-xs font-black text-zinc-900 dark:text-white">
                        {{ $activeProductsCount }}/{{ $maxProducts }} SKU
                    </span>
                </div>

                <!-- Progress Bar -->
                <div>
                    <div class="w-full h-2.5 rounded-full bg-zinc-100 dark:bg-zinc-800 overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-[#155A6B] to-teal-400 rounded-full transition-all duration-500" style="width: {{ $capacityPercentage }}%"></div>
                    </div>
                    <div class="flex justify-between items-center text-[10px] font-bold text-zinc-400 mt-1.5">
                        <span>{{ $capacityPercentage }}% Terpakai</span>
                        <span>Sisa: {{ max(0, $maxProducts - $activeProductsCount) }} SKU</span>
                    </div>
                </div>

                <a href="{{ route('supplier.products.create') }}" 
                    class="w-full flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200/80 dark:border-zinc-700/80 text-xs font-bold text-zinc-700 dark:text-zinc-300 hover:border-[#155A6B] hover:text-[#155A6B] transition-all">
                    <i class='bx bx-plus text-base'></i>
                    Ajukan Produk Baru
                </a>
            </div>

        </div>

    </div>

</div>

<!-- Modal Informasi Edit Profil -->
<dialog id="editInfoModal" class="modal rounded-3xl backdrop:bg-zinc-900/60 backdrop:backdrop-blur-sm p-0 max-w-md w-full bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white shadow-2xl border border-zinc-200 dark:border-zinc-800">
    <div class="p-6 space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-black tracking-tight flex items-center gap-2">
                <i class='bx bx-edit text-[#155A6B] dark:text-teal-400'></i> Update Profil & Rekening
            </h3>
            <form method="dialog">
                <button class="w-8 h-8 rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-500 flex items-center justify-center hover:bg-zinc-200 transition-colors">
                    <i class='bx bx-x text-xl'></i>
                </button>
            </form>
        </div>

        <p class="text-xs text-zinc-600 dark:text-zinc-300 leading-relaxed font-medium">
            Untuk menjaga keamanan rekening dan identitas toko konsinyasi, perubahan data profil (seperti nama rekening bank atau nama pemilik) diverifikasi secara manual oleh pengurus Koperasi.
        </p>

        <div class="p-4 rounded-2xl bg-teal-50 dark:bg-teal-950/40 border border-teal-200/60 dark:border-teal-800/40 space-y-2">
            <span class="text-[11px] font-bold text-[#155A6B] dark:text-teal-300 uppercase tracking-wider block">Hubungi Admin Koperasi</span>
            <p class="text-xs text-zinc-700 dark:text-zinc-200 font-medium">Silakan hubungi WhatsApp Sekretariat Koperasi untuk pengajuan perubahan data profil Anda.</p>
        </div>

        <div class="flex gap-2 pt-2">
            <a href="https://wa.me/6281234567890?text=Halo%20Admin%20Koperasi,%20saya%20supplier%20{{ urlencode($supplier->businessName ?? 'Mitra') }}%20({{ $supplier->code ?? '' }})%20ingin%20mengajukan%20update%20data%20profil." 
                target="_blank"
                class="flex-1 py-3 px-4 bg-[#155A6B] hover:bg-[#166072] text-white rounded-xl text-xs font-bold text-center transition-all flex items-center justify-center gap-2 shadow-md shadow-[#155A6B]/20">
                <i class='bx bxl-whatsapp text-lg'></i> Chat WhatsApp Admin
            </a>
            <form method="dialog" class="inline">
                <button class="py-3 px-4 bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 rounded-xl text-xs font-bold hover:bg-zinc-200 transition-colors">
                    Tutup
                </button>
            </form>
        </div>
    </div>
</dialog>

@endsection