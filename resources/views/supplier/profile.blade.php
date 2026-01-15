@extends('layouts.supplier')

@section('title', 'Profil Supplier')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="flex items-center gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Profil Saya</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Informasi akun dan bisnis Anda.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Profile Card -->
            <div class="md:col-span-1">
                <div
                    class="bg-white dark:bg-darkCard rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 p-6 text-center">
                    <div
                        class="w-24 h-24 bg-indigo-50 dark:bg-indigo-900/30 rounded-full flex items-center justify-center text-3xl font-bold text-primary mx-auto mb-4">
                        {{ substr(Auth::guard('supplier')->user()->businessName, 0, 1) }}
                    </div>
                    <div class="mb-2">
                        <p class="text-xs text-slate-400 uppercase tracking-wider">Nama Toko</p>
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white">
                            {{ Auth::guard('supplier')->user()->businessName }}</h2>
                    </div>
                    <div class="mb-3">
                        <p class="text-xs text-slate-400 uppercase tracking-wider">Nama Supplier</p>
                        <p class="text-sm font-medium text-slate-700 dark:text-slate-300">
                            {{ Auth::guard('supplier')->user()->ownerName }}</p>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-3">{{ Auth::guard('supplier')->user()->code }}
                    </p>

                    <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-700">
                        <div
                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400">
                            {{ Auth::guard('supplier')->user()->status }}
                        </div>
                    </div>

                    @php
                        $approvedProducts = Auth::guard('supplier')->user()->products()->where('status', 'ACTIVE')->count();
                        $totalProducts = Auth::guard('supplier')->user()->products()->count();
                    @endphp
                    <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-700">
                        <p class="text-xs text-slate-400 uppercase tracking-wider mb-2">Produk Approved</p>
                        <div class="flex items-center justify-center gap-2">
                            <span class="text-2xl font-bold text-primary">{{ $approvedProducts }}</span>
                            <span class="text-sm text-slate-400">/</span>
                            <span class="text-lg text-slate-600 dark:text-slate-400">{{ $totalProducts }}</span>
                        </div>
                        <p class="text-xs text-slate-500 mt-1">produk aktif</p>
                    </div>

                    <!-- Logout Button -->
                    <form action="{{ route('logout') }}" method="POST" class="mt-6">
                        @csrf
                        <button type="submit"
                            class="w-full px-4 py-2.5 bg-rose-50 text-rose-600 dark:bg-rose-900/20 dark:text-rose-400 border border-rose-100 dark:border-rose-900/30 rounded-lg text-sm font-semibold hover:bg-rose-100 dark:hover:bg-rose-900/40 transition-colors flex items-center justify-center gap-2 group">
                            <i class='bx bx-log-out text-lg group-hover:scale-110 transition-transform'></i>
                            Keluar Aplikasi
                        </button>
                    </form>
                </div>
            </div>

            <!-- Details -->
            <div class="md:col-span-2">
                <div
                    class="bg-white dark:bg-darkCard rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 p-6">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider mb-4">Informasi
                        Bisnis</h3>

                    <div class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs text-slate-500 dark:text-slate-400 mb-1">Nama Toko /
                                    Bisnis</label>
                                <p class="text-sm font-medium text-slate-900 dark:text-white">
                                    {{ Auth::guard('supplier')->user()->businessName }}</p>
                            </div>
                            <div>
                                <label class="block text-xs text-slate-500 dark:text-slate-400 mb-1">Nama Supplier
                                    (Pemilik)</label>
                                <p class="text-sm font-medium text-slate-900 dark:text-white">
                                    {{ Auth::guard('supplier')->user()->ownerName }}</p>
                            </div>
                            <div>
                                <label class="block text-xs text-slate-500 dark:text-slate-400 mb-1">Kategori Produk</label>
                                <p class="text-sm font-medium text-slate-900 dark:text-white">
                                    {{ Auth::guard('supplier')->user()->productCategory }}</p>
                            </div>
                            <div>
                                <label class="block text-xs text-slate-500 dark:text-slate-400 mb-1">Email</label>
                                <p class="text-sm font-medium text-slate-900 dark:text-white">
                                    {{ Auth::guard('supplier')->user()->email }}</p>
                            </div>
                            <div>
                                <label class="block text-xs text-slate-500 dark:text-slate-400 mb-1">Nomor Telepon</label>
                                <p class="text-sm font-medium text-slate-900 dark:text-white">
                                    {{ Auth::guard('supplier')->user()->phone }}</p>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs text-slate-500 dark:text-slate-400 mb-1">Alamat</label>
                            <p class="text-sm font-medium text-slate-900 dark:text-white">
                                {{ Auth::guard('supplier')->user()->address }}</p>
                        </div>

                        <div>
                            <label class="block text-xs text-slate-500 dark:text-slate-400 mb-1">Deskripsi Bisnis</label>
                            <p class="text-sm font-medium text-slate-900 dark:text-white">
                                {{ Auth::guard('supplier')->user()->description ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t border-slate-100 dark:border-slate-700 flex justify-end">
                        <button class="text-sm text-primary hover:underline font-medium">Edit Profil</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection