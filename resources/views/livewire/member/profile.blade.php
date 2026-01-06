<div>
    @section('page-title', 'Profil & Keamanan')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Profile Info Card --}}
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-darkCard rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 p-6">
                <div class="text-center mb-6">
                    <div class="w-24 h-24 bg-gradient-to-br from-primary to-blue-700 rounded-full mx-auto mb-4 flex items-center justify-center text-white text-3xl font-bold shadow-lg">
                        {{ substr($member->name ?? 'M', 0, 1) }}
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">{{ $member->name ?? 'Member' }}</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400">{{ $member->email ?? '-' }}</p>
                    <div class="mt-3">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-bold rounded-full {{ $member->tier === 'PLATINUM' ? 'bg-purple-100 text-purple-700' : ($member->tier === 'GOLD' ? 'bg-amber-100 text-amber-700' : ($member->tier === 'SILVER' ? 'bg-slate-200 text-slate-700' : 'bg-orange-100 text-orange-700')) }}">
                            <i class='bx bxs-medal'></i> {{ $member->tier ?? 'Bronze' }}
                        </span>
                    </div>
                </div>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between py-2 border-b border-slate-100 dark:border-slate-700">
                        <span class="text-slate-500 dark:text-slate-400">Nomor Anggota</span>
                        <span class="font-mono font-bold text-slate-800 dark:text-white">{{ $member->nomorAnggota ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-slate-100 dark:border-slate-700">
                        <span class="text-slate-500 dark:text-slate-400">Bergabung Sejak</span>
                        <span class="font-bold text-slate-800 dark:text-white">{{ $member->created_at?->format('d M Y') ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span class="text-slate-500 dark:text-slate-400">Total Poin</span>
                        <span class="font-bold text-amber-500">{{ number_format($member->points ?? 0) }} Pts</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Edit Forms --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Profile Form --}}
            <div class="bg-white dark:bg-darkCard rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 p-6">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-6 flex items-center gap-2">
                    <i class='bx bxs-user-detail text-primary'></i> Informasi Profil
                </h3>
                <form wire:submit="updateProfile">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Nama Lengkap</label>
                            <input type="text" wire:model="name" class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary bg-white dark:bg-slate-800 text-slate-800 dark:text-white transition-colors" placeholder="Nama lengkap">
                            @error('name') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Email</label>
                            <input type="email" wire:model="email" class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-600 rounded-xl bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400 cursor-not-allowed" readonly>
                            <p class="text-[10px] text-slate-400 mt-1">Email tidak dapat diubah</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Nomor Telepon</label>
                            <input type="text" wire:model="phone" class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary bg-white dark:bg-slate-800 text-slate-800 dark:text-white transition-colors" placeholder="08xxxxxxxxxx">
                            @error('phone') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Tanggal Lahir</label>
                            <input type="date" wire:model="birthDate" class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary bg-white dark:bg-slate-800 text-slate-800 dark:text-white transition-colors">
                            @error('birthDate') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Alamat</label>
                        <textarea wire:model="address" rows="3" class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary bg-white dark:bg-slate-800 text-slate-800 dark:text-white resize-none transition-colors" placeholder="Alamat lengkap"></textarea>
                        @error('address') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                    </div>
                    <button type="submit" class="px-6 py-2.5 bg-primary text-white font-bold rounded-xl hover:bg-blue-700 transition-colors flex items-center gap-2">
                        <i class='bx bx-save'></i> Simpan Perubahan
                    </button>
                </form>
            </div>

            {{-- Password Form --}}
            <div class="bg-white dark:bg-darkCard rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 p-6">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-6 flex items-center gap-2">
                    <i class='bx bxs-lock-alt text-amber-500'></i> Ubah Password
                </h3>
                <form wire:submit="updatePassword">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Password Lama</label>
                            <input type="password" wire:model="currentPassword" class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary bg-white dark:bg-slate-800 text-slate-800 dark:text-white transition-colors" placeholder="••••••••">
                            @error('currentPassword') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Password Baru</label>
                            <input type="password" wire:model="newPassword" class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary bg-white dark:bg-slate-800 text-slate-800 dark:text-white transition-colors" placeholder="••••••••">
                            @error('newPassword') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Konfirmasi Password</label>
                            <input type="password" wire:model="newPassword_confirmation" class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary bg-white dark:bg-slate-800 text-slate-800 dark:text-white transition-colors" placeholder="••••••••">
                        </div>
                    </div>
                    <button type="submit" class="px-6 py-2.5 bg-amber-500 text-white font-bold rounded-xl hover:bg-amber-600 transition-colors flex items-center gap-2">
                        <i class='bx bx-key'></i> Ubah Password
                    </button>
                </form>
            </div>
        </div>
    </div>

    @if(session()->has('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" class="fixed bottom-4 right-4 bg-emerald-500 text-white px-6 py-3 rounded-xl shadow-lg flex items-center gap-2 z-50">
            <i class='bx bx-check-circle'></i> {{ session('success') }}
        </div>
    @endif

    @if(session()->has('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" class="fixed bottom-4 right-4 bg-rose-500 text-white px-6 py-3 rounded-xl shadow-lg flex items-center gap-2 z-50">
            <i class='bx bx-error-circle'></i> {{ session('error') }}
        </div>
    @endif
</div>