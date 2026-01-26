@extends('layouts.member')

@section('title', 'Profil Saya')

@section('content')
    <div class="px-4 pb-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <div
                class="w-20 h-20 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 mx-auto flex items-center justify-center text-white text-3xl font-bold shadow-lg shadow-indigo-500/30 mb-4">
                {{ strtoupper(substr($member->name, 0, 1)) }}
            </div>
            <h1 class="text-xl font-bold text-slate-800 dark:text-white">{{ $member->name }}</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">{{ $member->nomorAnggota }}</p>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div
                class="mb-6 p-4 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 text-emerald-700 dark:text-emerald-400 text-sm font-medium flex items-center gap-3">
                <i class='bx bx-check-circle text-xl'></i>
                {{ session('success') }}
            </div>
        @endif

        <!-- Profile Form -->
        <div
            class="bg-white dark:bg-darkCard rounded-2xl shadow-sm border border-slate-100 dark:border-white/5 overflow-hidden mb-6">
            <div class="p-4 border-b border-slate-100 dark:border-white/5">
                <h3 class="text-sm font-bold text-slate-800 dark:text-white flex items-center gap-2">
                    <i class='bx bx-user text-primary'></i> Informasi Pribadi
                </h3>
            </div>

            <form action="{{ route('member.profile.update') }}" method="POST" class="p-4 space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label
                        class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-1.5 uppercase tracking-wide">Nama
                        Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', $member->name) }}"
                        class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-white/10 rounded-xl px-4 py-3 text-sm font-medium outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary dark:text-white">
                    @error('name') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label
                        class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-1.5 uppercase tracking-wide">Nomor
                        WhatsApp</label>
                    <input type="text" name="phone" value="{{ old('phone', $member->phone) }}" placeholder="08xxxxxxxxxx"
                        class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-white/10 rounded-xl px-4 py-3 text-sm font-medium outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary dark:text-white">
                    @error('phone') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                    @if(str_starts_with($member->phone, '000'))
                        <p class="text-xs text-amber-600 dark:text-amber-400 mt-1.5 flex items-center gap-1">
                            <i class='bx bx-info-circle'></i> Nomor ini masih dummy, silakan update.
                        </p>
                    @endif
                </div>

                <div>
                    <label
                        class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-1.5 uppercase tracking-wide">Alamat</label>
                    <textarea name="address" rows="2" placeholder="Alamat domisili..."
                        class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-white/10 rounded-xl px-4 py-3 text-sm font-medium outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary dark:text-white resize-none">{{ old('address', $member->address) }}</textarea>
                </div>

                <button type="submit"
                    class="w-full py-3 rounded-xl bg-primary text-white font-bold text-sm shadow-lg shadow-primary/30 hover:bg-indigo-700 transition-all">
                    Simpan Perubahan
                </button>
            </form>
        </div>

        <!-- Password Form -->
        <div
            class="bg-white dark:bg-darkCard rounded-2xl shadow-sm border border-slate-100 dark:border-white/5 overflow-hidden mb-6">
            <div class="p-4 border-b border-slate-100 dark:border-white/5">
                <h3 class="text-sm font-bold text-slate-800 dark:text-white flex items-center gap-2">
                    <i class='bx bx-lock-alt text-rose-500'></i> Ubah Password
                </h3>
            </div>

            <form action="{{ route('member.profile.password') }}" method="POST" class="p-4 space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label
                        class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-1.5 uppercase tracking-wide">Password
                        Lama</label>
                    <input type="password" name="current_password"
                        class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-white/10 rounded-xl px-4 py-3 text-sm font-medium outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary dark:text-white">
                    @error('current_password') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label
                        class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-1.5 uppercase tracking-wide">Password
                        Baru</label>
                    <input type="password" name="password"
                        class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-white/10 rounded-xl px-4 py-3 text-sm font-medium outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary dark:text-white">
                    @error('password') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label
                        class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-1.5 uppercase tracking-wide">Konfirmasi
                        Password Baru</label>
                    <input type="password" name="password_confirmation"
                        class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-white/10 rounded-xl px-4 py-3 text-sm font-medium outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary dark:text-white">
                </div>

                <button type="submit"
                    class="w-full py-3 rounded-xl bg-rose-600 text-white font-bold text-sm shadow-lg shadow-rose-500/30 hover:bg-rose-700 transition-all">
                    Ubah Password
                </button>
            </form>
        </div>

        <!-- Logout -->
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit"
                class="w-full py-3 rounded-xl bg-slate-100 dark:bg-white/5 text-slate-600 dark:text-slate-400 font-bold text-sm hover:bg-slate-200 dark:hover:bg-white/10 transition-all flex items-center justify-center gap-2">
                <i class='bx bx-log-out'></i> Keluar
            </button>
        </form>
    </div>
@endsection