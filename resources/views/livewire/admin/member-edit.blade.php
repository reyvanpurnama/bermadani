<div class="max-w-5xl mx-auto">
    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div class="mb-6 p-4 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-lg">
            <p class="text-sm text-emerald-600 dark:text-emerald-400 font-medium">{{ session('message') }}</p>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-6 p-4 bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800 rounded-lg">
            <p class="text-sm text-rose-600 dark:text-rose-400 font-medium">{{ session('error') }}</p>
        </div>
    @endif

    <form wire:submit.prevent="update">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Sidebar -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Profile Card -->
                <div class="bg-white dark:bg-darkCard p-6 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 text-center relative">
                    <div class="w-24 h-24 mx-auto bg-slate-100 dark:bg-slate-700 rounded-full relative mb-4">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($member->user->name) }}&background=0F52BA&color=fff&size=128"
                            class="w-full h-full rounded-full object-cover border-4 border-white dark:border-darkCard shadow-sm"
                            alt="{{ $member->user->name }}">
                    </div>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">{{ $member->user->name }}</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Bergabung: {{ $member->joinDate->format('d M Y') }}</p>
                </div>

                <!-- Status Card -->
                <div class="bg-white dark:bg-darkCard p-6 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700">
                    <h3 class="text-[13px] font-bold text-slate-800 dark:text-white mb-4">Status Keanggotaan</h3>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-2">Status Akun</label>
                            <select wire:model="status"
                                class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-lg px-3 py-2 text-[13px] outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all dark:text-white cursor-pointer">
                                <option value="ACTIVE">Aktif</option>
                                <option value="INACTIVE">Non-Aktif (Cuti)</option>
                                <option value="SUSPENDED">Dibekukan (Banned)</option>
                            </select>
                            @error('status') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="pt-4 border-t border-slate-100 dark:border-slate-700">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Info Saldo (Read-Only)</p>
                            <div class="flex justify-between items-center text-[12px] mb-1">
                                <span class="text-slate-500">Total Simpanan</span>
                                <span class="font-bold text-slate-800 dark:text-white">Rp {{ number_format($member->totalSimpanan, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between items-center text-[12px]">
                                <span class="text-slate-500">Poin Loyalty</span>
                                <span class="font-bold text-amber-500">{{ number_format($member->points, 0, ',', '.') }}</span>
                            </div>
                            <a href="{{ route('admin.members.show', $member->id) }}"
                                class="block mt-3 text-center text-[11px] text-primary font-bold hover:underline">
                                Lihat Detail Lengkap
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Form -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white dark:bg-darkCard p-6 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-[13px] font-bold text-slate-800 dark:text-white">Data Pribadi</h3>
                        <span class="text-[10px] text-slate-400 italic">*Kolom abu-abu tidak dapat diedit</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Read-only fields -->
                        <div class="md:col-span-2 p-4 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Nomor Anggota</label>
                                <div class="flex items-center gap-2">
                                    <i class='bx bxs-lock-alt text-slate-400'></i>
                                    <span class="text-[13px] font-mono font-bold text-slate-700 dark:text-slate-300">{{ $member->nomorAnggota }}</span>
                                </div>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Tanggal Bergabung</label>
                                <div class="flex items-center gap-2">
                                    <i class='bx bxs-lock-alt text-slate-400'></i>
                                    <span class="text-[13px] font-mono font-bold text-slate-700 dark:text-slate-300">{{ $member->joinDate->format('d-m-Y') }}</span>
                                </div>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Linked User Email</label>
                                <div class="flex items-center gap-2">
                                    <i class='bx bxs-lock-alt text-slate-400'></i>
                                    <span class="text-[13px] font-mono font-bold text-slate-700 dark:text-slate-300">{{ $member->user->email }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Editable fields -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-1.5">Nama Lengkap</label>
                            <input type="text" wire:model="name"
                                class="w-full bg-white dark:bg-darkCard border border-slate-200 dark:border-slate-600 rounded-lg px-3 py-2.5 text-[13px] outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all dark:text-white">
                            @error('name') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-1.5">Nomor Telepon</label>
                            <input type="text" wire:model="phone"
                                class="w-full bg-white dark:bg-darkCard border border-slate-200 dark:border-slate-600 rounded-lg px-3 py-2.5 text-[13px] outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all dark:text-white">
                            @error('phone') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-1.5">Unit Kerja / Prodi</label>
                            <input type="text" wire:model="unitKerja" list="unitKerjaList"
                                class="w-full bg-white dark:bg-darkCard border border-slate-200 dark:border-slate-600 rounded-lg px-3 py-2.5 text-[13px] outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all dark:text-white"
                                placeholder="Contoh: Teknik Informatika">
                            <datalist id="unitKerjaList">
                                @foreach($unitKerjaList as $unit)
                                    <option value="{{ $unit }}">
                                @endforeach
                            </datalist>
                            @error('unitKerja') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-2">Jenis Kelamin</label>
                            <div class="flex gap-4">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" wire:model="gender" value="MALE" class="text-primary focus:ring-primary">
                                    <span class="text-[13px] text-slate-700 dark:text-slate-300">Laki-laki</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" wire:model="gender" value="FEMALE" class="text-primary focus:ring-primary">
                                    <span class="text-[13px] text-slate-700 dark:text-slate-300">Perempuan</span>
                                </label>
                            </div>
                            @error('gender') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-1.5">Alamat Domisili</label>
                            <textarea wire:model="address" rows="3"
                                class="w-full bg-white dark:bg-darkCard border border-slate-200 dark:border-slate-600 rounded-lg px-3 py-2.5 text-[13px] outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all dark:text-white"></textarea>
                            @error('address') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="pt-6 border-t border-slate-100 dark:border-slate-700 flex justify-end gap-3">
                        <a href="{{ route('admin.members.show', $member->id) }}"
                            class="px-5 py-2.5 bg-white dark:bg-darkCard border border-slate-200 dark:border-slate-600 text-slate-600 dark:text-slate-300 rounded-lg text-[13px] font-bold hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                            Batal
                        </a>
                        <button type="submit" wire:loading.attr="disabled"
                            class="px-6 py-2.5 bg-primary hover:bg-indigo-700 text-white rounded-lg text-[13px] font-bold shadow-md shadow-indigo-500/20 transition-all flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                            <span wire:loading.remove wire:target="update">
                                <i class='bx bx-save text-lg'></i> Simpan Perubahan
                            </span>
                            <span wire:loading wire:target="update">
                                <i class='bx bx-loader-alt animate-spin text-lg'></i> Menyimpan...
                            </span>
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>
