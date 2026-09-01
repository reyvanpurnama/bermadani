@extends('installer.layout')

@section('title', 'Koperasi & Super Admin Setup')

@section('content')
<div class="bg-slate-800/80 rounded-2xl border border-slate-700 p-8 shadow-xl">
    <div class="mb-6">
        <h2 class="text-xl font-bold text-white flex items-center gap-2">
            <i class='bx bx-building-house text-blue-400 text-2xl'></i>
            3. Identitas Koperasi & Akun Super Admin
        </h2>
        <p class="text-slate-400 text-sm mt-1">
            Atur nama koperasi milik klien dan kredensial akun Super Admin pertama.
        </p>
    </div>

    @if ($errors->any())
        <div class="mb-6 p-4 rounded-xl bg-rose-950/60 border border-rose-700 text-rose-300 text-sm space-y-1">
            @foreach ($errors->all() as $error)
                <p class="flex items-center gap-2"><i class='bx bx-error-circle'></i> {{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form action="{{ route('installer.process') }}" method="POST" id="installForm" class="space-y-6">
        @csrf

        <!-- Section 1: Identitas Koperasi -->
        <div class="p-5 rounded-xl bg-slate-900/60 border border-slate-700/80 space-y-4">
            <h3 class="text-sm font-bold text-blue-400 uppercase tracking-wider flex items-center gap-2 border-b border-slate-700 pb-2">
                <i class='bx bx-store'></i> Identitas Koperasi Client
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Nama Koperasi (Umum)</label>
                    <input type="text" name="coop_name" value="{{ old('coop_name', 'Koperasi Sejahtera Mandiri') }}" required 
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-700 bg-slate-900 text-white text-sm focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Nama Singkat / Brand</label>
                    <input type="text" name="coop_short_name" value="{{ old('coop_short_name', 'KSM') }}" required 
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-700 bg-slate-900 text-white text-sm focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Email Domain Koperasi</label>
                    <input type="text" name="coop_email_domain" value="{{ old('coop_email_domain', 'ksm.co.id') }}" required 
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-700 bg-slate-900 text-white text-sm focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
        </div>

        <!-- Section 2: Super Admin Account -->
        <div class="p-5 rounded-xl bg-slate-900/60 border border-slate-700/80 space-y-4">
            <h3 class="text-sm font-bold text-emerald-400 uppercase tracking-wider flex items-center gap-2 border-b border-slate-700 pb-2">
                <i class='bx bx-user-check'></i> Akun Super Admin Utama
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Nama Administrator</label>
                    <input type="text" name="admin_name" value="{{ old('admin_name', 'Administrator Utama') }}" required 
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-700 bg-slate-900 text-white text-sm focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Email Admin (Login)</label>
                    <input type="email" name="admin_email" value="{{ old('admin_email', 'admin@koperasi.id') }}" required 
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-700 bg-slate-900 text-white text-sm focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Password (Min. 8 karakter)</label>
                    <input type="password" name="admin_password" required minlength="8" 
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-700 bg-slate-900 text-white text-sm focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Konfirmasi Password</label>
                    <input type="password" name="admin_password_confirmation" required minlength="8" 
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-700 bg-slate-900 text-white text-sm focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
        </div>

        <div class="flex items-center justify-between pt-4 border-t border-slate-700">
            <a href="{{ route('installer.step2') }}" class="text-xs text-slate-400 hover:text-white transition-colors">
                ← Kembali ke Database
            </a>

            <button type="submit" id="btnSubmitInstall" onclick="this.disabled=true; this.innerHTML='<i class=bx bx-loader-alt animate-spin></i> Menginstal Database & Setting...'; this.form.submit();"
                    class="px-8 py-3.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-sm transition-all shadow-lg shadow-emerald-600/30 flex items-center gap-2">
                <span>Proses & Install Sekarang</span>
                <i class='bx bx-rocket text-xl'></i>
            </button>
        </div>
    </form>
</div>
@endsection
