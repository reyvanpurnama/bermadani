@extends('layouts.supplier')

@section('title', 'Permintaan Restock')

@section('content')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Permintaan Restock</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Kelola permintaan penambahan stok barang.</p>
    </div>
    <div class="flex gap-2">
        <button class="bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2">
            <i class='bx bx-plus'></i>
            Buat Permintaan
        </button>
    </div>
</div>

<div class="bg-white dark:bg-darkCard rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 p-8 text-center">
    <div class="w-16 h-16 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-4">
        <i class='bx bx-archive-in text-3xl text-slate-400'></i>
    </div>
    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">Fitur Segera Hadir</h3>
    <p class="text-slate-500 dark:text-slate-400 max-w-md mx-auto">
        Fitur permintaan restock sedang dalam pengembangan. Saat ini Anda dapat menghubungi admin koperasi secara langsung untuk koordinasi penambahan stok.
    </p>
</div>
@endsection
