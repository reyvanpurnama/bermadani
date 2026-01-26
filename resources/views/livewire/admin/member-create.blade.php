<div class="max-w-4xl mx-auto">
    <!-- Flash Messages -->
    @if (session()->has('error'))
        <div class="mb-6 p-4 bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800 rounded-lg">
            <p class="text-sm text-rose-600 dark:text-rose-400 font-medium">{{ session('error') }}</p>
        </div>
    @endif

    <!-- All Validation Errors -->
    @if ($errors->any())
        <div class="mb-6 p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg">
            <p class="text-sm font-bold text-amber-700 dark:text-amber-400 mb-2">Validation Errors:</p>
            <ul class="list-disc list-inside text-xs text-amber-600 dark:text-amber-400">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Progress Steps -->
    <div class="mb-8">
        <div class="flex items-center justify-between relative">
            <div class="absolute left-0 top-1/2 -translate-y-1/2 w-full h-1 bg-slate-200 dark:bg-slate-700 -z-10 rounded-full"></div>
            <div class="absolute left-0 top-1/2 -translate-y-1/2 h-1 bg-primary -z-10 rounded-full transition-all duration-500"
                style="width: {{ (($currentStep - 1) / 3) * 100 }}%"></div>

            @foreach([1 => 'Akun', 2 => 'Biodata', 3 => 'Simpanan', 4 => 'Review'] as $step => $label)
                <div class="flex flex-col items-center gap-2 cursor-pointer" wire:click="goToStep({{ $step }})">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs ring-4 ring-white dark:ring-darkBg transition-all
                        @if($step < $currentStep) bg-emerald-500 text-white
                        @elseif($step === $currentStep) bg-primary text-white
                        @else bg-slate-200 dark:bg-slate-700 text-slate-500
                        @endif">
                        @if($step < $currentStep)
                            <i class='bx bx-check text-lg'></i>
                        @else
                            {{ $step }}
                        @endif
                    </div>
                    <span class="text-[10px] font-bold uppercase tracking-wider bg-white dark:bg-darkBg px-1
                        @if($step === $currentStep) text-primary @else text-slate-400 @endif">
                        {{ $label }}
                    </span>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white dark:bg-darkCard rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">
        
        <!-- Step 1: Account -->
        @if($currentStep === 1)
            <div class="p-8">
                <div class="text-center mb-8">
                    <div class="w-12 h-12 bg-indigo-50 dark:bg-indigo-900/20 text-primary rounded-xl flex items-center justify-center text-2xl mx-auto mb-3">
                        <i class='bx bx-user-plus'></i>
                    </div>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">Buat Akun Anggota Baru</h2>
                    <p class="text-xs text-slate-500">Masukkan email dan password untuk akun baru.</p>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-1.5">Email</label>
                        <input type="email" wire:model="email"
                            class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-lg px-3 py-2.5 text-[13px] outline-none focus:border-primary dark:text-white"
                            placeholder="email@kampus.ac.id">
                        @error('email') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-1.5">Password</label>
                        <input type="password" wire:model="password"
                            class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-lg px-3 py-2.5 text-[13px] outline-none focus:border-primary dark:text-white"
                            placeholder="Minimal 6 karakter">
                        @error('password') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                        <p class="text-[10px] text-slate-400 mt-1">Default password: 12345678</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Step 2: Personal Info -->
        @if($currentStep === 2)
            <div class="p-8">
                <div class="text-center mb-6">
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">Data Diri Anggota</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="md:col-span-2">
                        <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-1.5">Nama Lengkap</label>
                        <input type="text" wire:model="name"
                            class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-lg px-3 py-2.5 text-[13px] outline-none focus:border-primary dark:text-white"
                            placeholder="Sesuai KTP/KTM">
                        @error('name') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-1.5">NIM / NIP</label>
                        <input type="text" wire:model="nim"
                            class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-lg px-3 py-2.5 text-[13px] outline-none focus:border-primary dark:text-white">
                        @error('nim') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-1.5">No. Telepon (WA)</label>
                        <input type="text" wire:model="phone"
                            class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-lg px-3 py-2.5 text-[13px] outline-none focus:border-primary dark:text-white">
                        @error('phone') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-1.5">Jenis Kelamin</label>
                        <div class="flex gap-4 mt-2">
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

                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-1.5">Unit Kerja / Prodi</label>
                        <input type="text" wire:model="unitKerja" list="unitKerjaList"
                            class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-lg px-3 py-2.5 text-[13px] outline-none focus:border-primary dark:text-white"
                            placeholder="Contoh: Teknik Informatika">
                        <datalist id="unitKerjaList">
                            @foreach($unitKerjaList as $unit)
                                <option value="{{ $unit }}">
                            @endforeach
                        </datalist>
                        @error('unitKerja') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-1.5">Alamat Domisili</label>
                        <textarea wire:model="address" rows="2"
                            class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-lg px-3 py-2.5 text-[13px] outline-none focus:border-primary dark:text-white"></textarea>
                        @error('address') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
        @endif

        <!-- Step 3: Simpanan -->
        @if($currentStep === 3)
            <div class="p-8">
                <div class="text-center mb-6">
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">Setoran Awal Simpanan</h2>
                    <p class="text-xs text-slate-500">Wajib diisi untuk aktivasi keanggotaan.</p>
                </div>

                <div class="space-y-4">
                    <!-- Simpanan Pokok -->
                    <div class="p-4 bg-indigo-50 dark:bg-indigo-900/10 border border-indigo-100 dark:border-indigo-800 rounded-xl flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-white dark:bg-slate-800 flex items-center justify-center text-indigo-600 shadow-sm">
                                <i class='bx bxs-lock-alt'></i>
                            </div>
                            <div>
                                <h6 class="text-[13px] font-bold text-slate-800 dark:text-white">Simpanan Pokok</h6>
                                <p class="text-[10px] text-slate-500">Wajib (Sekali Bayar)</p>
                            </div>
                        </div>
                        <div class="w-40 relative">
                            <span class="absolute left-3 top-2.5 text-xs text-slate-500 font-bold">Rp</span>
                            <input type="number" wire:model.live="simpananPokok"
                                class="w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-lg pl-8 pr-3 py-2 text-[13px] font-bold text-right outline-none focus:border-primary dark:text-white">
                        </div>
                    </div>
                    @error('simpananPokok') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror

                    <!-- Simpanan Wajib -->
                    <div class="p-4 bg-blue-50 dark:bg-blue-900/10 border border-blue-100 dark:border-blue-800 rounded-xl flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-white dark:bg-slate-800 flex items-center justify-center text-blue-600 shadow-sm">
                                <i class='bx bxs-calendar'></i>
                            </div>
                            <div>
                                <h6 class="text-[13px] font-bold text-slate-800 dark:text-white">Simpanan Wajib</h6>
                                <p class="text-[10px] text-slate-500">Wajib (Bulanan)</p>
                            </div>
                        </div>
                        <div class="w-40 relative">
                            <span class="absolute left-3 top-2.5 text-xs text-slate-500 font-bold">Rp</span>
                            <input type="number" wire:model.live="simpananWajib"
                                class="w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-lg pl-8 pr-3 py-2 text-[13px] font-bold text-right outline-none focus:border-primary dark:text-white">
                        </div>
                    </div>
                    @error('simpananWajib') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror

                    <!-- Simpanan Sukarela -->
                    <div class="p-4 bg-emerald-50 dark:bg-emerald-900/10 border border-emerald-100 dark:border-emerald-800 rounded-xl flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-white dark:bg-slate-800 flex items-center justify-center text-emerald-600 shadow-sm">
                                <i class='bx bxs-wallet'></i>
                            </div>
                            <div>
                                <h6 class="text-[13px] font-bold text-slate-800 dark:text-white">Simpanan Sukarela</h6>
                                <p class="text-[10px] text-slate-500">Opsional (Tabungan)</p>
                            </div>
                        </div>
                        <div class="w-40 relative">
                            <span class="absolute left-3 top-2.5 text-xs text-slate-500 font-bold">Rp</span>
                            <input type="number" wire:model.live="simpananSukarela"
                                class="w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-lg pl-8 pr-3 py-2 text-[13px] font-bold text-right outline-none focus:border-primary dark:text-white">
                        </div>
                    </div>
                    @error('simpananSukarela') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror

                    <!-- Bukti Transfer -->
                    <div class="mt-4">
                        <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-1.5">Bukti Transfer (Total Tagihan)</label>
                        <input type="file" wire:model="buktiTransfer" accept="image/*"
                            class="block w-full text-[12px] text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-[11px] file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 dark:file:bg-slate-700 dark:file:text-white">
                        @error('buktiTransfer') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                        @if($buktiTransfer)
                            <div class="mt-2 text-xs text-emerald-600">
                                <i class='bx bx-check-circle'></i> File dipilih: {{ $buktiTransfer->getClientOriginalName() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <!-- Step 4: Review -->
        @if($currentStep === 4)
            <div class="p-8">
                <div class="text-center mb-6">
                    <div class="w-14 h-14 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center text-3xl mx-auto mb-3">
                        <i class='bx bx-check'></i>
                    </div>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">Siap Didaftarkan</h2>
                    <p class="text-xs text-slate-500">Pastikan data berikut sudah benar.</p>
                </div>

                <div class="bg-slate-50 dark:bg-slate-800 rounded-xl p-6 border border-slate-200 dark:border-slate-700 space-y-4">
                    <div class="flex justify-between border-b border-slate-200 dark:border-slate-600 pb-2">
                        <span class="text-xs text-slate-500">Nama Anggota</span>
                        <span class="text-xs font-bold text-slate-800 dark:text-white">{{ $name }}</span>
                    </div>
                    <div class="flex justify-between border-b border-slate-200 dark:border-slate-600 pb-2">
                        <span class="text-xs text-slate-500">NIM / NIP</span>
                        <span class="text-xs font-bold text-slate-800 dark:text-white">{{ $nim ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between border-b border-slate-200 dark:border-slate-600 pb-2">
                        <span class="text-xs text-slate-500">No. Telepon</span>
                        <span class="text-xs font-bold text-slate-800 dark:text-white">{{ $phone }}</span>
                    </div>
                    <div class="flex justify-between border-b border-slate-200 dark:border-slate-600 pb-2">
                        <span class="text-xs text-slate-500">Unit Kerja</span>
                        <span class="text-xs font-bold text-slate-800 dark:text-white">{{ $unitKerja ?? '-' }}</span>
                    </div>

                    <div class="pt-2">
                        <p class="text-[10px] uppercase font-bold text-slate-400 tracking-widest mb-2">Rincian Tagihan Awal</p>
                        <div class="space-y-1">
                            <div class="flex justify-between text-xs">
                                <span class="text-slate-500">Simpanan Pokok</span>
                                <span class="font-medium">Rp {{ number_format($simpananPokok, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-xs">
                                <span class="text-slate-500">Simpanan Wajib</span>
                                <span class="font-medium">Rp {{ number_format($simpananWajib, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-xs">
                                <span class="text-slate-500">Simpanan Sukarela</span>
                                <span class="font-medium">Rp {{ number_format($simpananSukarela, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between items-center pt-2 mt-2 border-t border-slate-200 dark:border-slate-600">
                                <span class="text-sm font-bold text-slate-800 dark:text-white">Total Bayar</span>
                                <span class="text-lg font-bold text-primary">Rp {{ number_format($totalSimpanan, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Footer Buttons -->
        <div class="px-8 py-5 bg-slate-50 dark:bg-slate-800/50 border-t border-slate-100 dark:border-slate-700 flex justify-between items-center">
            @if($currentStep === 3)
                <div class="text-right flex-1">
                    <span class="text-[10px] text-slate-500 uppercase font-bold mr-2">Total Tagihan:</span>
                    <span class="text-lg font-bold text-slate-800 dark:text-white">Rp {{ number_format($totalSimpanan, 0, ',', '.') }}</span>
                </div>
            @else
                <div class="flex-1"></div>
            @endif

            <div class="flex gap-3">
                @if($currentStep > 1)
                    <button type="button" wire:click="prevStep"
                        class="px-5 py-2.5 rounded-lg border border-slate-200 dark:border-slate-600 text-slate-600 dark:text-slate-300 text-[13px] font-bold hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                        Kembali
                    </button>
                @endif
                <button type="button" wire:click="nextStep" wire:loading.attr="disabled"
                    class="px-6 py-2.5 rounded-lg text-white text-[13px] font-bold shadow-lg transition-all flex items-center gap-2
                        @if($currentStep === 4) bg-emerald-600 hover:bg-emerald-700 shadow-emerald-500/30
                        @else bg-primary hover:bg-indigo-700 shadow-indigo-500/30
                        @endif
                        disabled:opacity-50 disabled:cursor-not-allowed">
                    <span wire:loading.remove wire:target="nextStep">
                        @if($currentStep === 4)
                            <i class='bx bx-check-circle text-lg'></i> Simpan Anggota
                        @else
                            Lanjut <i class='bx bx-right-arrow-alt text-lg'></i>
                        @endif
                    </span>
                    <span wire:loading wire:target="nextStep">
                        <i class='bx bx-loader-alt animate-spin text-lg'></i> Memproses...
                    </span>
                </button>
            </div>
        </div>

    </div>
</div>
