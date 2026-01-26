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
            <p class="text-sm font-bold text-amber-700 dark:text-amber-400 mb-2">Perhatian:</p>
            <ul class="list-disc list-inside text-xs text-amber-600 dark:text-amber-400">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Progress Steps (3 Steps) -->
    <div class="mb-8">
        <div class="flex items-center justify-between relative">
            <div
                class="absolute left-0 top-1/2 -translate-y-1/2 w-full h-1 bg-slate-200 dark:bg-slate-700 -z-10 rounded-full">
            </div>
            <div class="absolute left-0 top-1/2 -translate-y-1/2 h-1 bg-primary -z-10 rounded-full transition-all duration-500"
                style="width: {{ (($currentStep - 1) / 2) * 100 }}%"></div>

            @foreach([1 => 'Biodata', 2 => 'Simpanan', 3 => 'Konfirmasi'] as $step => $label)
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

    <!-- Form Container -->
    <div
        class="bg-white dark:bg-darkCard rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">

        <!-- Step 1: Biodata -->
        @if($currentStep === 1)
            <div class="p-8">
                <div class="text-center mb-6">
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">Data Diri Anggota</h2>
                    <p class="text-xs text-slate-500">Lengkapi identitas calon anggota koperasi.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="md:col-span-2">
                        <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-1.5">Nama
                            Lengkap</label>
                        <input type="text" wire:model="name"
                            class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-lg px-4 py-3 text-[13px] outline-none focus:ring-2 focus:ring-primary/20 dark:text-white placeholder-slate-400"
                            placeholder="Sesuai KTP/KTM">
                        @error('name') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-1.5">NIM /
                            NIP</label>
                        <input type="text" wire:model="nim"
                            class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-lg px-4 py-3 text-[13px] outline-none focus:ring-2 focus:ring-primary/20 dark:text-white placeholder-slate-400">
                        @error('nim') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-1.5">No.
                            WhatsApp</label>
                        <input type="text" wire:model="phone"
                            class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-lg px-4 py-3 text-[13px] outline-none focus:ring-2 focus:ring-primary/20 dark:text-white placeholder-slate-400">
                        @error('phone') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-1.5">Jenis
                            Kelamin</label>
                        <div class="flex gap-4 mt-2">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" wire:model="gender" value="MALE"
                                    class="text-primary focus:ring-primary">
                                <span class="text-[13px] text-slate-700 dark:text-slate-300">Laki-laki</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" wire:model="gender" value="FEMALE"
                                    class="text-primary focus:ring-primary">
                                <span class="text-[13px] text-slate-700 dark:text-slate-300">Perempuan</span>
                            </label>
                        </div>
                        @error('gender') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-1.5">Unit Kerja /
                            Prodi</label>
                        <input type="text" wire:model="unitKerja" list="unitKerjaList"
                            class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-lg px-4 py-3 text-[13px] outline-none focus:ring-2 focus:ring-primary/20 dark:text-white placeholder-slate-400"
                            placeholder="Contoh: Teknik Informatika">
                        <datalist id="unitKerjaList">
                            @foreach($unitKerjaList as $unit)
                                <option value="{{ $unit }}">
                            @endforeach
                        </datalist>
                        @error('unitKerja') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-1.5">Alamat
                            Domisili</label>
                        <textarea wire:model="address" rows="2"
                            class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-lg px-4 py-3 text-[13px] outline-none focus:ring-2 focus:ring-primary/20 dark:text-white placeholder-slate-400"></textarea>
                        @error('address') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
        @endif

        <!-- Step 2: Simpanan & Pembayaran -->
        @if($currentStep === 2)
            <div class="p-8">
                <div class="text-center mb-6">
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">Setoran Awal</h2>
                    <p class="text-xs text-slate-500">Tentukan nominal setoran awal anggota.</p>
                </div>

                <div class="space-y-4">
                    <!-- Simpanan Pokok -->
                    <div
                        class="p-4 bg-indigo-50 dark:bg-indigo-900/10 border border-indigo-100 dark:border-indigo-800 rounded-xl flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-full bg-white dark:bg-slate-800 flex items-center justify-center text-indigo-600 shadow-sm border border-indigo-100 dark:border-indigo-900">
                                <i class='bx bxs-lock-alt'></i>
                            </div>
                            <div>
                                <h6 class="text-[13px] font-bold text-slate-800 dark:text-white">Simpanan Pokok</h6>
                                <p class="text-[10px] text-slate-500">Wajib (Min. Rp 200.000)</p>
                            </div>
                        </div>
                        <div class="w-40 relative">
                            <span class="absolute left-3 top-2.5 text-xs text-slate-500 font-bold">Rp</span>
                            <input type="number" wire:model.live="simpananPokok" min="200000" step="1000"
                                class="w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-lg pl-8 pr-3 py-2 text-[13px] font-bold text-right outline-none focus:border-indigo-500 dark:text-white">
                        </div>
                    </div>
                    @error('simpananPokok') <span class="text-xs text-rose-500 mt-1 ml-1">{{ $message }}</span> @enderror

                    <!-- Simpanan Wajib -->
                    <div
                        class="p-4 bg-blue-50 dark:bg-blue-900/10 border border-blue-100 dark:border-blue-800 rounded-xl flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-full bg-white dark:bg-slate-800 flex items-center justify-center text-blue-600 shadow-sm border border-blue-100 dark:border-blue-900">
                                <i class='bx bxs-calendar'></i>
                            </div>
                            <div>
                                <h6 class="text-[13px] font-bold text-slate-800 dark:text-white">Simpanan Wajib</h6>
                                <p class="text-[10px] text-slate-500">Opsional (Bisa Rp 0, bayar nanti)</p>
                            </div>
                        </div>
                        <div class="w-40 relative">
                            <span class="absolute left-3 top-2.5 text-xs text-slate-500 font-bold">Rp</span>
                            <input type="number" wire:model.live="simpananWajib" min="0" step="1000"
                                class="w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-lg pl-8 pr-3 py-2 text-[13px] font-bold text-right outline-none focus:border-blue-500 dark:text-white">
                        </div>
                    </div>
                    @error('simpananWajib') <span class="text-xs text-rose-500 mt-1 ml-1">{{ $message }}</span> @enderror

                    <!-- Simpanan Sukarela -->
                    <div
                        class="p-4 bg-emerald-50 dark:bg-emerald-900/10 border border-emerald-100 dark:border-emerald-800 rounded-xl flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-full bg-white dark:bg-slate-800 flex items-center justify-center text-emerald-600 shadow-sm border border-emerald-100 dark:border-emerald-900">
                                <i class='bx bxs-wallet'></i>
                            </div>
                            <div>
                                <h6 class="text-[13px] font-bold text-slate-800 dark:text-white">Simpanan Sukarela</h6>
                                <p class="text-[10px] text-slate-500">Tabungan Tambahan (Opsional)</p>
                            </div>
                        </div>
                        <div class="w-40 relative">
                            <span class="absolute left-3 top-2.5 text-xs text-slate-500 font-bold">Rp</span>
                            <input type="number" wire:model.live="simpananSukarela" min="0" step="1000"
                                class="w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-lg pl-8 pr-3 py-2 text-[13px] font-bold text-right outline-none focus:border-emerald-500 dark:text-white">
                        </div>
                    </div>
                    @error('simpananSukarela') <span class="text-xs text-rose-500 mt-1 ml-1">{{ $message }}</span> @enderror

                    <!-- Bukti Transfer -->
                    <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-700">
                        <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-1.5">Upload Bukti
                            Transfer (Opsional)</label>
                        <div class="relative group">
                            <input type="file" wire:model="buktiTransfer" accept="image/*"
                                class="block w-full text-[12px] text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-[11px] file:font-bold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 dark:file:bg-slate-700 dark:file:text-white cursor-pointer">
                        </div>
                        <p class="text-[10px] text-slate-400 mt-1">*Jika nominal > 0, mohon lampirkan bukti transfer.</p>
                        @error('buktiTransfer') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror

                        @if($buktiTransfer)
                            <div
                                class="mt-2 text-xs text-emerald-600 flex items-center gap-1.5 font-medium animate-in fade-in slide-in-from-left-2">
                                <i class='bx bx-check-double text-lg'></i> File siap:
                                {{ $buktiTransfer->getClientOriginalName() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <!-- Step 3: Review & Konfirmasi -->
        @if($currentStep === 3)
            <div class="p-8">
                <div class="text-center mb-6">
                    <div
                        class="w-16 h-16 bg-emerald-100 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 rounded-full flex items-center justify-center text-3xl mx-auto mb-3 animate-in zoom-in">
                        <i class='bx bx-check'></i>
                    </div>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">Siap Didaftarkan</h2>
                    <p class="text-xs text-slate-500">Pastikan data berikut sudah benar.</p>
                </div>

                <div class="space-y-4">
                    <div class="bg-slate-50 dark:bg-slate-800 rounded-xl p-5 border border-slate-200 dark:border-slate-700">
                        <h4 class="text-[10px] uppercase font-bold text-slate-400 tracking-widest mb-3">Identitas Anggota
                        </h4>
                        <div class="space-y-2">
                            <div class="flex justify-between text-[13px]">
                                <span class="text-slate-500">Nama Lengkap</span>
                                <span class="font-bold text-slate-800 dark:text-white">{{ $name }}</span>
                            </div>
                            <div class="flex justify-between text-[13px]">
                                <span class="text-slate-500">NIM / NIP</span>
                                <span class="font-bold text-slate-800 dark:text-white">{{ $nim ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between text-[13px]">
                                <span class="text-slate-500">No. Telepon</span>
                                <span class="font-bold text-slate-800 dark:text-white">{{ $phone }}</span>
                            </div>
                            <div class="flex justify-between text-[13px]">
                                <span class="text-slate-500">Unit Kerja</span>
                                <span class="font-bold text-slate-800 dark:text-white">{{ $unitKerja ?? '-' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-slate-50 dark:bg-slate-800 rounded-xl p-5 border border-slate-200 dark:border-slate-700">
                        <h4 class="text-[10px] uppercase font-bold text-slate-400 tracking-widest mb-3">Rincian Pembayaran
                        </h4>
                        <div class="space-y-2 border-b border-slate-200 dark:border-slate-700 pb-3 mb-3">
                            <div class="flex justify-between text-[13px]">
                                <span class="text-slate-500">Simpanan Pokok</span>
                                <span class="font-medium">Rp {{ number_format($simpananPokok, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-[13px]">
                                <span class="text-slate-500">Simpanan Wajib</span>
                                <span class="font-medium">Rp {{ number_format($simpananWajib, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-[13px]">
                                <span class="text-slate-500">Simpanan Sukarela</span>
                                <span class="font-medium">Rp {{ number_format($simpananSukarela, 0, ',', '.') }}</span>
                            </div>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-bold text-slate-800 dark:text-white">Total Tagihan Awal</span>
                            <span class="text-lg font-bold text-primary">Rp
                                {{ number_format($this->totalSimpanan, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div
                        class="bg-indigo-50 dark:bg-indigo-900/10 rounded-xl p-4 flex gap-3 text-indigo-700 dark:text-indigo-300">
                        <i class='bx bx-info-circle text-xl mt-0.5'></i>
                        <div class="text-xs leading-relaxed">
                            <p class="font-bold mb-1">Informasi Akun Login</p>
                            <p>Akun login akan dibuat otomatis menggunakan Nomor Anggota sebagai username/email (format:
                                <strong>[NoAnggota]@bermadani.id</strong>) dan password default: <strong>password</strong>.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Footer Buttons -->
        <div
            class="px-8 py-5 bg-slate-50 dark:bg-slate-800/50 border-t border-slate-100 dark:border-slate-700 flex justify-between items-center">
            @if($currentStep >= 2)
                <button type="button" wire:click="prevStep"
                    class="px-5 py-2.5 rounded-lg border border-slate-200 dark:border-slate-600 text-slate-600 dark:text-slate-300 text-[13px] font-bold hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                    Kembali
                </button>
            @else
                <div></div>
            @endif

            <button type="button" wire:click="nextStep" wire:loading.attr="disabled" class="px-6 py-2.5 rounded-lg text-white text-[13px] font-bold shadow-lg transition-all flex items-center gap-2
                    @if($currentStep === 3) bg-emerald-600 hover:bg-emerald-700 shadow-emerald-500/30
                    @else bg-primary hover:bg-indigo-700 shadow-indigo-500/30
                    @endif
                    disabled:opacity-50 disabled:cursor-not-allowed">
                <span wire:loading.remove wire:target="nextStep" wire:target="submit">
                    @if($currentStep === 3)
                        <i class='bx bx-check-circle text-lg'></i> Simpan Anggota
                    @else
                        Lanjut <i class='bx bx-right-arrow-alt text-lg'></i>
                    @endif
                </span>
                <span wire:loading wire:target="nextStep" wire:target="submit">
                    <i class='bx bx-loader-alt animate-spin text-lg'></i> Memproses...
                </span>
            </button>
        </div>

    </div>
</div>