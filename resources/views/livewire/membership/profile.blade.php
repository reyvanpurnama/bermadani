<div>
    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-xl font-bold text-slate-800 dark:text-white">Profil Saya</h1>
        <p class="text-sm text-slate-500">Kelola informasi akun kamu</p>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div
            class="bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30 text-emerald-700 dark:text-emerald-400 px-4 py-3 rounded-xl mb-6 flex items-center gap-2">
            <i class='bx bxs-check-circle text-xl'></i>
            {{ session('success') }}
        </div>
    @endif

    {{-- Profile Card --}}
    <div class="bg-white dark:bg-darkCard rounded-2xl p-6 border border-slate-100 dark:border-slate-700 shadow-sm mb-6">
        <div class="flex items-center gap-4 mb-6">
            <div
                class="w-16 h-16 rounded-full bg-gradient-to-br from-primary to-purple-500 flex items-center justify-center text-white text-2xl font-bold">
                {{ strtoupper(substr($member->name ?? 'M', 0, 1)) }}
            </div>
            <div>
                <h2 class="text-lg font-bold text-slate-800 dark:text-white">{{ $member->name ?? '-' }}</h2>
                <p class="text-sm text-slate-500 font-mono">{{ $member->nomorAnggota ?? '-' }}</p>
                <span
                    class="inline-flex items-center gap-1 px-2 py-0.5 mt-1 rounded-full text-[10px] font-bold bg-primary/10 text-primary">
                    <i class='bx bxs-medal'></i> {{ $member->tier ?? 'BRONZE' }}
                </span>
            </div>
        </div>

        <div class="space-y-4">
            <div>
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 block">Email</label>
                <p class="text-sm text-slate-700 dark:text-slate-300">{{ $member->email ?? '-' }}</p>
            </div>
            <div>
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 block">No.
                    Telepon</label>
                <input type="text" wire:model="phone"
                    class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-lg px-3 py-2 text-sm text-slate-700 dark:text-white focus:ring-2 focus:ring-primary outline-none">
                @error('phone') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 block">Alamat</label>
                <textarea wire:model="address" rows="2"
                    class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-lg px-3 py-2 text-sm text-slate-700 dark:text-white focus:ring-2 focus:ring-primary outline-none resize-none"></textarea>
                @error('address') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 block">Unit Kerja /
                    Institusi</label>
                <input type="text" wire:model="unitKerja"
                    class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-lg px-3 py-2 text-sm text-slate-700 dark:text-white focus:ring-2 focus:ring-primary outline-none">
                @error('unitKerja') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
            </div>
            <button wire:click="updateProfile"
                class="w-full bg-primary text-white font-bold py-2.5 rounded-lg hover:bg-indigo-700 transition-colors">
                Simpan Perubahan
            </button>
        </div>
    </div>

    {{-- Change Password --}}
    <div class="bg-white dark:bg-darkCard rounded-2xl p-6 border border-slate-100 dark:border-slate-700 shadow-sm">
        <h3 class="font-bold text-slate-800 dark:text-white mb-4 flex items-center gap-2">
            <i class='bx bxs-lock-alt text-primary'></i> Ubah Password
        </h3>
        <div class="space-y-4">
            <div>
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 block">Password Saat
                    Ini</label>
                <input type="password" wire:model="currentPassword"
                    class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-lg px-3 py-2 text-sm text-slate-700 dark:text-white focus:ring-2 focus:ring-primary outline-none">
                @error('currentPassword') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 block">Password
                    Baru</label>
                <input type="password" wire:model="newPassword"
                    class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-lg px-3 py-2 text-sm text-slate-700 dark:text-white focus:ring-2 focus:ring-primary outline-none">
                @error('newPassword') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 block">Konfirmasi
                    Password</label>
                <input type="password" wire:model="newPassword_confirmation"
                    class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-lg px-3 py-2 text-sm text-slate-700 dark:text-white focus:ring-2 focus:ring-primary outline-none">
            </div>
            <button wire:click="updatePassword"
                class="w-full bg-slate-800 dark:bg-slate-700 text-white font-bold py-2.5 rounded-lg hover:bg-slate-900 dark:hover:bg-slate-600 transition-colors">
                Ubah Password
            </button>
        </div>
    </div>

    {{-- Logout Button --}}
    <form action="{{ route('logout') }}" method="POST" class="mt-6">
        @csrf
        <button type="submit"
            class="w-full flex items-center justify-center gap-2 bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/30 text-rose-600 dark:text-rose-400 font-bold py-3 rounded-xl hover:bg-rose-100 dark:hover:bg-rose-500/20 transition-colors">
            <i class='bx bx-log-out text-lg'></i> Keluar
        </button>
    </form>
</div>