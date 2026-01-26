<div class="max-w-4xl mx-auto">
    <!-- Flash Messages (Premium Glass) -->
    @if (session()->has('error'))
        <div class="mb-8 p-4 bg-rose-500/10 backdrop-blur-sm border border-rose-500/20 rounded-2xl flex items-center gap-4">
            <div class="w-10 h-10 rounded-full bg-rose-500/20 flex items-center justify-center text-rose-500 shrink-0">
                <i class='bx bx-x text-xl'></i>
            </div>
            <div class="flex-1">
                <h5 class="text-sm font-bold text-rose-600 dark:text-rose-400">Terjadi Kesalahan</h5>
                <p class="text-xs text-rose-600/80 dark:text-rose-400/80 mt-0.5">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    <!-- All Validation Errors -->
    @if ($errors->any())
        <div class="mb-8 p-4 bg-amber-500/10 backdrop-blur-sm border border-amber-500/20 rounded-2xl">
            <div class="flex items-center gap-3 mb-2">
                <i class='bx bx-info-circle text-amber-500 text-lg'></i>
                <span class="text-sm font-bold text-amber-600 dark:text-amber-400">Mohon Periksa Kembali:</span>
            </div>
            <ul class="list-disc list-inside text-xs text-amber-600/80 dark:text-amber-400/80 ml-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Main Card container (Premium Depth) -->
    <div class="bg-white dark:bg-[#1e1e2d] rounded-3xl shadow-2xl border border-slate-100 dark:border-white/5 overflow-hidden relative">
        
        <!-- Decoration Gradient Top -->
        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-violet-500 via-fuchsia-500 to-indigo-500"></div>

        <!-- NEW: Integrated Segmented Stepper -->
        <div class="bg-slate-50 dark:bg-[#151521] border-b border-slate-100 dark:border-white/5 p-2 sm:p-3">
            <div class="grid grid-cols-3 gap-2 sm:gap-4 relative">
                @foreach([1 => 'Biodata', 2 => 'Simpanan', 3 => 'Konfirmasi'] as $step => $label)
                <button wire:click="goToStep({{ $step }})"
                    class="relative overflow-hidden rounded-xl py-3 px-4 flex flex-col sm:flex-row items-center justify-center sm:justify-start gap-3 transition-all duration-300 group outline-none focus:outline-none
                    @if($currentStep === $step) 
                        bg-white dark:bg-slate-800 shadow-lg shadow-indigo-500/5 border border-indigo-100 dark:border-indigo-500/30
                    @elseif($step < $currentStep)
                        bg-emerald-50 dark:bg-emerald-500/5 border border-emerald-100 dark:border-emerald-500/20 opacity-80 hover:opacity-100
                    @else
                        bg-transparent border border-transparent hover:bg-white/50 dark:hover:bg-white/5 opacity-50
                    @endif">
                    
                    <!-- Icon / Number Box -->
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-bold transition-all duration-300
                        @if($currentStep === $step)
                            bg-indigo-600 text-white shadow-md shadow-indigo-600/30
                        @elseif($step < $currentStep)
                            bg-emerald-500 text-white
                        @else
                            bg-slate-200 dark:bg-slate-700 text-slate-500 dark:text-slate-400 group-hover:bg-slate-300 dark:group-hover:bg-slate-600
                        @endif">
                        @if($step < $currentStep) <i class='bx bx-check text-base'></i> @else {{ $step }} @endif
                    </div>

                    <!-- Text -->
                    <div class="text-center sm:text-left">
                        <span class="block text-[10px] sm:text-xs font-bold uppercase tracking-wider
                            @if($currentStep === $step) text-indigo-900 dark:text-white
                            @elseif($step < $currentStep) text-emerald-800 dark:text-emerald-400
                            @else text-slate-500 dark:text-slate-400 @endif">
                            {{ $label }}
                        </span>
                        @if($currentStep === $step)
                            <div class="hidden sm:block h-0.5 w-8 bg-indigo-500 rounded-full mt-1"></div>
                        @endif
                    </div>

                    <!-- Active Glow -->
                    @if($currentStep === $step)
                        <div class="absolute inset-0 bg-indigo-500/5 dark:bg-indigo-500/10 pointer-events-none"></div>
                    @endif
                </button>
                @endforeach
            </div>
        </div>

        <!-- Form Content Area -->
        <div class="p-6 sm:p-10 min-h-[400px]">
            
            <!-- Step 1: Biodata -->
            @if($currentStep === 1)
                <div class="animate-in fade-in slide-in-from-bottom-4 duration-500">
                    <div class="mb-8">
                        <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-2">Data Diri Anggota</h2>
                        <p class="text-slate-500 dark:text-slate-400 text-sm">Masukan informasi dasar anggota baru. Kolom opsional boleh dikosongkan.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2 group">
                            <label class="block text-xs font-bold uppercase text-slate-400 dark:text-slate-500 mb-2 tracking-wide group-focus-within:text-indigo-500 transition-colors">Nama Lengkap</label>
                            <input type="text" wire:model="name"
                                class="w-full bg-slate-50 dark:bg-[#151521] border border-slate-200 dark:border-white/10 rounded-xl px-5 py-4 text-sm font-semibold outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 dark:text-white placeholder-slate-400 transition-all shadow-sm"
                                placeholder="Tulis nama lengkap...">
                            @error('name') <span class="text-xs text-rose-500 mt-2 block font-medium">{{ $message }}</span> @enderror
                        </div>

                        <div class="group">
                            <label class="block text-xs font-bold uppercase text-slate-400 dark:text-slate-500 mb-2 tracking-wide group-focus-within:text-indigo-500 transition-colors">NIM / NIP (Opsional)</label>
                            <input type="text" wire:model="nim"
                                class="w-full bg-slate-50 dark:bg-[#151521] border border-slate-200 dark:border-white/10 rounded-xl px-5 py-4 text-sm font-semibold outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 dark:text-white placeholder-slate-400 transition-all shadow-sm">
                        </div>

                        <div class="group">
                            <label class="block text-xs font-bold uppercase text-slate-400 dark:text-slate-500 mb-2 tracking-wide group-focus-within:text-indigo-500 transition-colors">WhatsApp (Opsional)</label>
                            <input type="text" wire:model="phone"
                                class="w-full bg-slate-50 dark:bg-[#151521] border border-slate-200 dark:border-white/10 rounded-xl px-5 py-4 text-sm font-semibold outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 dark:text-white placeholder-slate-400 transition-all shadow-sm">
                            <p class="text-[10px] text-slate-400 mt-1.5 ml-1">*No dummy otomatis jika kosong</p>
                        </div>

                        <div class="group">
                            <label class="block text-xs font-bold uppercase text-slate-400 dark:text-slate-500 mb-2 tracking-wide">Gender</label>
                            <div class="grid grid-cols-2 gap-3">
                                <label class="cursor-pointer relative">
                                    <input type="radio" wire:model="gender" value="MALE" class="peer sr-only">
                                    <div class="p-3 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-[#151521] text-center transition-all peer-checked:bg-indigo-50 dark:peer-checked:bg-indigo-500/10 peer-checked:border-indigo-500 peer-checked:text-indigo-600 dark:peer-checked:text-indigo-400 text-slate-500 dark:text-slate-400 text-sm font-bold hover:bg-slate-100 dark:hover:bg-white/5">
                                        Laki-laki
                                    </div>
                                </label>
                                <label class="cursor-pointer relative">
                                    <input type="radio" wire:model="gender" value="FEMALE" class="peer sr-only">
                                    <div class="p-3 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-[#151521] text-center transition-all peer-checked:bg-pink-50 dark:peer-checked:bg-pink-500/10 peer-checked:border-pink-500 peer-checked:text-pink-600 dark:peer-checked:text-pink-400 text-slate-500 dark:text-slate-400 text-sm font-bold hover:bg-slate-100 dark:hover:bg-white/5">
                                        Perempuan
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="group">
                            <label class="block text-xs font-bold uppercase text-slate-400 dark:text-slate-500 mb-2 tracking-wide group-focus-within:text-indigo-500 transition-colors">Unit Kerja (Opsional)</label>
                            <input type="text" wire:model="unitKerja" list="unitKerjaList"
                                class="w-full bg-slate-50 dark:bg-[#151521] border border-slate-200 dark:border-white/10 rounded-xl px-5 py-4 text-sm font-semibold outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 dark:text-white placeholder-slate-400 transition-all shadow-sm"
                                placeholder="Pilih atau ketik...">
                            <datalist id="unitKerjaList">
                                @foreach($unitKerjaList as $unit)
                                    <option value="{{ $unit }}">
                                @endforeach
                            </datalist>
                        </div>
                        
                        <div class="md:col-span-2 group">
                            <label class="block text-xs font-bold uppercase text-slate-400 dark:text-slate-500 mb-2 tracking-wide group-focus-within:text-indigo-500 transition-colors">Alamat Domisili (Opsional)</label>
                            <textarea wire:model="address" rows="2"
                                class="w-full bg-slate-50 dark:bg-[#151521] border border-slate-200 dark:border-white/10 rounded-xl px-5 py-4 text-sm font-semibold outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 dark:text-white placeholder-slate-400 transition-all shadow-sm"></textarea>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Step 2: Simpanan -->
            @if($currentStep === 2)
                 <div class="animate-in fade-in slide-in-from-right-8 duration-500">
                    <div class="mb-8 items-center flex justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-2">Setoran Awal</h2>
                            <p class="text-slate-500 dark:text-slate-400 text-sm">Atur nominal simpanan & pembayaran.</p>
                        </div>
                        <div class="px-4 py-2 bg-indigo-50 dark:bg-indigo-500/10 rounded-lg text-indigo-600 dark:text-indigo-400 font-bold text-sm border border-indigo-100 dark:border-indigo-500/20">
                            Total: Rp {{ number_format($this->totalSimpanan, 0, ',', '.') }}
                        </div>
                    </div>

                    <div class="space-y-4">
                        <!-- Card Simpanan Modern -->
                        @foreach([
                            ['key' => 'simpananPokok', 'label' => 'Simpanan Pokok', 'desc' => 'Wajib (Min. 200k)', 'icon' => 'bxs-lock-alt', 'color' => 'indigo', 'min' => 200000],
                            ['key' => 'simpananWajib', 'label' => 'Simpanan Wajib', 'desc' => 'Opsional / Default', 'icon' => 'bxs-calendar', 'color' => 'blue', 'min' => 0],
                            ['key' => 'simpananSukarela', 'label' => 'Simpanan Sukarela', 'desc' => 'Tabungan (Opsional)', 'icon' => 'bxs-wallet', 'color' => 'emerald', 'min' => 0]
                        ] as $item)
                        <div class="group p-1 rounded-2xl bg-white dark:bg-[#151521] border border-slate-200 dark:border-white/5 transition-all hover:border-{{ $item['color'] }}-400 dark:hover:border-{{ $item['color'] }}-500/50 shadow-sm hover:shadow-md"
                             x-data="{
                                 value: @entangle($item['key']),
                                 display: '',
                                 init() { this.display = this.format(this.value); $watch('value', v => this.display = this.format(v)); },
                                 format(v) { return (v || 0).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.'); },
                                 update(e) { let raw = e.target.value.replace(/\./g, '').replace(/[^0-9]/g, ''); this.value = raw ? parseInt(raw) : 0; }
                             }">
                             <div class="flex items-center gap-4 p-4">
                                <div class="w-12 h-12 rounded-xl bg-{{ $item['color'] }}-50 dark:bg-{{ $item['color'] }}-500/10 flex items-center justify-center text-{{ $item['color'] }}-600 dark:text-{{ $item['color'] }}-400 text-xl shadow-inner">
                                    <i class='bx {{ $item["icon"] }}'></i>
                                </div>
                                <div class="flex-1">
                                    <h6 class="text-sm font-bold text-slate-800 dark:text-white">{{ $item['label'] }}</h6>
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 group-hover:text-{{ $item['color'] }}-500">{{ $item['desc'] }}</p>
                                </div>
                                <div class="w-48">
                                    <div class="relative">
                                        <span class="absolute left-4 top-3.5 text-xs font-bold text-slate-400">Rp</span>
                                        <input type="text" x-model="display" @input="update"
                                            class="w-full bg-slate-50 dark:bg-[#1e1e2d] border border-slate-200 dark:border-white/10 rounded-xl pl-10 pr-4 py-3 text-right font-bold text-slate-800 dark:text-white outline-none focus:ring-2 focus:ring-{{ $item['color'] }}-500/20 focus:border-{{ $item['color'] }}-500 transition-all">
                                    </div>
                                </div>
                             </div>
                        </div>
                        @error($item['key']) <p class="text-xs text-rose-500 ml-2 mt-1">{{ $message }}</p> @enderror
                        @endforeach

                        <!-- Upload Area Premium -->
                        <div class="mt-6 p-6 rounded-2xl border-2 border-dashed border-slate-200 dark:border-white/10 bg-slate-50/50 dark:bg-white/5 hover:bg-indigo-50/50 dark:hover:bg-indigo-500/5 hover:border-indigo-300 dark:hover:border-indigo-500/30 transition-all group cursor-pointer relative overflow-hidden">
                            <input type="file" wire:model="buktiTransfer" accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer z-20">
                            
                            <div class="flex flex-col items-center justify-center text-center relative z-10">
                                @if($buktiTransfer)
                                    <div class="w-12 h-12 rounded-full bg-emerald-100 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-2xl mb-2 shadow-lg shadow-emerald-500/20">
                                        <i class='bx bx-check'></i>
                                    </div>
                                    <p class="text-sm font-bold text-emerald-600 dark:text-emerald-400 break-all px-10">{{ $buktiTransfer->getClientOriginalName() }}</p>
                                    <p class="text-xs text-slate-400 mt-1">Klik untuk mengganti file</p>
                                @else
                                    <div class="w-12 h-12 rounded-full bg-slate-200 dark:bg-slate-700 text-slate-400 flex items-center justify-center text-2xl mb-2 group-hover:scale-110 transition-transform">
                                        <i class='bx bx-cloud-upload'></i>
                                    </div>
                                    <p class="text-sm font-bold text-slate-600 dark:text-slate-300">Upload Bukti Transfer</p>
                                    <p class="text-xs text-slate-400 mt-1">Format: JPG, PNG (Max 2MB). Opsional jika nominal 0.</p>
                                @endif
                            </div>
                        </div>
                        @error('buktiTransfer') <p class="text-xs text-rose-500 mt-1 text-center">{{ $message }}</p> @enderror
                    </div>
                </div>
            @endif

            <!-- Step 3: Konfirmasi -->
            @if($currentStep === 3)
                <div class="animate-in fade-in zoom-in duration-500 max-w-2xl mx-auto">
                    <div class="text-center mb-8">
                        <div class="inline-flex items-center justify-center w-20 h-20 rounded-3xl bg-gradient-to-br from-emerald-400 to-emerald-600 shadow-xl shadow-emerald-500/30 text-white text-4xl mb-4">
                            <i class='bx bx-user-check'></i>
                        </div>
                        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Siap Mendaftarkan?</h2>
                        <p class="text-slate-500 dark:text-slate-400">Pastikan semua data sudah sesuai sebelum disimpan.</p>
                    </div>

                    <div class="bg-slate-50 dark:bg-[#151521] rounded-2xl border border-slate-200 dark:border-white/5 p-1">
                        <div class="bg-white dark:bg-[#1e1e2d] rounded-xl p-6 shadow-sm border border-slate-100 dark:border-white/5 mb-1">
                            <h5 class="text-xs font-bold uppercase text-slate-400 tracking-wider mb-4 border-b border-slate-100 dark:border-white/5 pb-2">Data Personal</h5>
                            <div class="grid grid-cols-2 gap-y-4 gap-x-2">
                                <div><p class="text-xs text-slate-500">Nama</p><p class="text-sm font-bold text-slate-800 dark:text-white">{{ $name }}</p></div>
                                <div><p class="text-xs text-slate-500">Telepon</p><p class="text-sm font-bold text-slate-800 dark:text-white">{{ $phone ?: '-' }}</p></div>
                                <div><p class="text-xs text-slate-500">Gender</p><p class="text-sm font-bold text-slate-800 dark:text-white">{{ $gender }}</p></div>
                                <div><p class="text-xs text-slate-500">Unit Kerja</p><p class="text-sm font-bold text-slate-800 dark:text-white">{{ $unitKerja ?: '-' }}</p></div>
                            </div>
                        </div>

                        <div class="bg-white dark:bg-[#1e1e2d] rounded-xl p-6 shadow-sm border border-slate-100 dark:border-white/5">
                            <h5 class="text-xs font-bold uppercase text-slate-400 tracking-wider mb-4 border-b border-slate-100 dark:border-white/5 pb-2">Keuangan</h5>
                             <div class="space-y-3">
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-slate-500">Simpanan Pokok</span>
                                    <span class="font-semibold text-slate-800 dark:text-white">Rp {{ number_format($simpananPokok, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-slate-500">Simpanan Wajib</span>
                                    <span class="font-semibold text-slate-800 dark:text-white">Rp {{ number_format($simpananWajib, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-slate-500">Simpanan Sukarela</span>
                                    <span class="font-semibold text-slate-800 dark:text-white">Rp {{ number_format($simpananSukarela, 0, ',', '.') }}</span>
                                </div>
                                <div class="pt-3 mt-1 border-t border-dashed border-slate-200 dark:border-slate-700 flex justify-between items-center">
                                    <span class="font-bold text-slate-800 dark:text-white">Total Tagihan</span>
                                    <span class="text-lg font-bold text-indigo-600 dark:text-indigo-400">Rp {{ number_format($this->totalSimpanan, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

        </div>

        <!-- Sticky Footer Action -->
        <div class="bg-white dark:bg-[#1e1e2d] border-t border-slate-100 dark:border-white/5 p-4 sm:px-10 flex justify-between items-center">
            <button type="button" wire:click="prevStep"
                class="px-6 py-3 rounded-xl font-bold text-sm text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-white/5 transition-colors disabled:opacity-30 disabled:cursor-not-allowed"
                @if($currentStep === 1) disabled @endif>
                <i class='bx bx-left-arrow-alt align-middle mr-1'></i> Sebelumnya
            </button>

            <button type="button" wire:click="nextStep" wire:loading.attr="disabled"
                class="px-8 py-3 rounded-xl font-bold text-sm text-white shadow-lg shadow-indigo-500/30 transition-all transform active:scale-95 flex items-center gap-2
                @if($currentStep === 3) 
                    bg-gradient-to-r from-emerald-500 to-emerald-600 hover:shadow-emerald-500/40 
                @else 
                    bg-gradient-to-r from-indigo-600 to-violet-600 hover:shadow-indigo-500/40 
                @endif
                disabled:opacity-70 disabled:cursor-wait">
                <span wire:loading.remove wire:target="nextStep" wire:target="submit">
                    @if($currentStep === 3)
                        Konfirmasi & Simpan <i class='bx bx-check-circle align-middle ml-1'></i>
                    @else
                        Lanjut Langkah Berikutnya <i class='bx bx-right-arrow-alt align-middle ml-1'></i>
                    @endif
                </span>
                <span wire:loading wire:target="nextStep" wire:target="submit">
                    <i class='bx bx-loader-alt animate-spin text-lg'></i> Memproses...
                </span>
            </button>
        </div>

    </div>
</div>