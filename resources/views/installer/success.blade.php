@extends('installer.layout')

@section('title', 'Installation Complete')

@section('content')
<div class="bg-slate-800/80 rounded-2xl border border-slate-700 p-8 shadow-xl text-center max-w-xl mx-auto">
    <div class="w-20 h-20 bg-emerald-500/20 text-emerald-400 rounded-full flex items-center justify-center mx-auto mb-6 ring-8 ring-emerald-500/10">
        <i class='bx bx-party text-5xl'></i>
    </div>

    <h2 class="text-2xl font-bold text-white mb-2">
        Instalasi Berhasil Selesai! 🎉
    </h2>
    <p class="text-slate-300 text-sm mb-6">
        Aplikasi <span class="font-bold text-white">{{ config('cooperative.name') }}</span> telah berhasil di-setup dan dikunci.
    </p>

    <div class="p-4 rounded-xl bg-slate-900/80 border border-slate-700 text-left text-xs space-y-2 mb-8 font-mono">
        <div class="flex justify-between border-b border-slate-800 pb-2">
            <span class="text-slate-400">URL Login Admin:</span>
            <span class="text-blue-400 font-bold">{{ route('login') }}</span>
        </div>
        <div class="flex justify-between border-b border-slate-800 pb-2">
            <span class="text-slate-400">Email Super Admin:</span>
            <span class="text-emerald-400 font-bold">{{ session('admin_email', 'admin@koperasi.id') }}</span>
        </div>
        <div class="flex justify-between">
            <span class="text-slate-400">Status Installer:</span>
            <span class="text-slate-300">Locked (<code class="text-amber-400">storage/installed</code>)</span>
        </div>
    </div>

    <a href="{{ route('login') }}" class="w-full inline-flex items-center justify-center gap-2 px-8 py-3.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-sm transition-all shadow-lg shadow-blue-600/30">
        <span>Masuk ke Portal Login Admin</span>
        <i class='bx bx-log-in-circle text-xl'></i>
    </a>
</div>
@endsection
