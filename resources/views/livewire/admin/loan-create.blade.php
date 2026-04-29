<div class="space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-800 dark:text-white">Input Pinjaman Baru</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Catat pinjaman anggota dengan simulasi angsuran real-time.</p>
        </div>
        <a href="{{ route('admin.loans') }}"
            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-sm font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors w-full sm:w-auto">
            <i class='bx bx-arrow-back text-base'></i>
            Kembali ke Daftar
        </a>
    </div>

    @if ($errors->any())
        <div class="p-4 bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/30 text-rose-700 dark:text-rose-300 rounded-xl">
            <div class="flex items-start gap-3">
                <i class='bx bxs-error-circle text-xl mt-0.5'></i>
                <div>
                    <p class="text-sm font-bold">Periksa input form terlebih dahulu:</p>
                    <ul class="mt-1 text-xs list-disc list-inside space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    @if (session()->has('success'))
        <div class="p-4 bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30 text-emerald-700 dark:text-emerald-300 rounded-xl flex items-start gap-3">
            <i class='bx bxs-check-circle text-xl mt-0.5'></i>
            <p class="text-sm font-semibold leading-relaxed">{{ session('success') }}</p>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="p-4 bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/30 text-rose-700 dark:text-rose-300 rounded-xl flex items-start gap-3">
            <i class='bx bxs-error-circle text-xl mt-0.5'></i>
            <p class="text-sm font-semibold leading-relaxed">{{ session('error') }}</p>
        </div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
        <div class="xl:col-span-8 space-y-6">
            <form wire:submit.prevent="createLoan" class="space-y-6">
                <div class="bg-white dark:bg-darkCard rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-4 sm:p-6">
                    <div class="flex items-center justify-between gap-2 mb-4">
                        <h2 class="text-sm font-bold text-slate-800 dark:text-white">Pilih Anggota</h2>
                        <span class="text-[11px] font-semibold text-slate-500 dark:text-slate-400">Wajib diisi</span>
                    </div>

                    @if(!$member_id)
                        <div class="relative">
                            <div class="absolute inset-y-0 left-3 flex items-center text-slate-400 pointer-events-none">
                                <i class='bx bx-search text-lg'></i>
                            </div>
                            <input type="text" wire:model.live.debounce.300ms="search"
                                class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-white focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none"
                                placeholder="Ketik nama atau nomor anggota...">

                            @if(count($members) > 0)
                                <div class="absolute z-20 mt-2 w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-lg overflow-hidden">
                                    <ul class="max-h-64 overflow-y-auto custom-scroll">
                                        @foreach($members as $m)
                                            <li wire:click="selectMember({{ $m->id }})"
                                                class="px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-700 cursor-pointer border-b border-slate-100 dark:border-slate-700 last:border-0">
                                                <p class="text-sm font-semibold text-slate-800 dark:text-white">{{ $m->name }}</p>
                                                <p class="text-xs text-slate-500">{{ $m->nomorAnggota }}{{ $m->position ? ' • ' . $m->position : '' }}</p>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 p-4 bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-800 rounded-xl">
                            <div>
                                <p class="text-sm font-bold text-slate-800 dark:text-white">{{ $selectedMember?->name }}</p>
                                <p class="text-xs text-slate-500">{{ $selectedMember?->nomorAnggota }}{{ $selectedMember?->unitKerja ? ' • ' . $selectedMember->unitKerja : '' }}</p>
                            </div>
                            <button type="button" wire:click="$set('member_id', null); $set('search', '')"
                                class="inline-flex items-center justify-center gap-1 px-3 py-2 text-xs font-semibold rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700">
                                <i class='bx bx-refresh'></i>
                                Ganti Anggota
                            </button>
                        </div>
                    @endif

                    @error('member_id')
                        <p class="text-xs text-rose-500 mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <div class="bg-white dark:bg-darkCard rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-4 sm:p-6">
                    <h3 class="text-sm font-bold text-slate-800 dark:text-white mb-4">Detail Pinjaman</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 sm:gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2">Sumber Pinjaman</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <label class="cursor-pointer">
                                    <input type="radio" wire:model.live="loanSource" value="BERMADANI" class="peer sr-only">
                                    <div class="px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 text-sm font-semibold text-slate-600 dark:text-slate-300 bg-slate-50 dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors peer-checked:border-primary peer-checked:bg-indigo-50 dark:peer-checked:bg-indigo-900/20 peer-checked:text-primary dark:peer-checked:text-indigo-300">
                                        <i class='bx bx-building-house mr-1'></i> Koperasi Bermadani
                                    </div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" wire:model.live="loanSource" value="BMT_ITQAN" class="peer sr-only">
                                    <div class="px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 text-sm font-semibold text-slate-600 dark:text-slate-300 bg-slate-50 dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors peer-checked:border-primary peer-checked:bg-indigo-50 dark:peer-checked:bg-indigo-900/20 peer-checked:text-primary dark:peer-checked:text-indigo-300">
                                        <i class='bx bx-landscape mr-1'></i> BMT ITQAN
                                    </div>
                                </label>
                            </div>
                            @error('loanSource')
                                <p class="text-xs text-rose-500 mt-1.5">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1.5">Plafon Pinjaman (Rp)</label>
                            <input type="number" step="1000" wire:model.live.debounce.400ms="amount"
                                class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-white focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none"
                                placeholder="10000000">
                            @error('amount')
                                <p class="text-xs text-rose-500 mt-1.5">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1.5">Tenor (Bulan)</label>
                            <input type="number" min="1" wire:model.live.debounce.400ms="tenor"
                                class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-white focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none"
                                placeholder="12">
                            @error('tenor')
                                <p class="text-xs text-rose-500 mt-1.5">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1.5">Margin (%)</label>
                            <input type="number" step="0.1" min="0" wire:model.live.debounce.400ms="interestRate"
                                class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-white focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none"
                                placeholder="0">
                            <p class="text-[11px] text-slate-400 mt-1">Isi 0 bila tanpa margin.</p>
                        </div>

                        @if($loanSource === 'BMT_ITQAN')
                            <div>
                                <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1.5">Titipan Simwa (Rp)</label>
                                <input type="number" step="1000" min="0" wire:model.live.debounce.400ms="simwa_amount"
                                    class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-white focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none"
                                    placeholder="15000">
                            </div>
                        @endif

                        <div>
                            <div class="flex items-center justify-between mb-1.5 gap-2">
                                <label class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest">Angsuran per Bulan (Rp)</label>
                                @if($monthlyPaymentOverridden)
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300">Manual</span>
                                @else
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300">Auto</span>
                                @endif
                            </div>
                            <input type="number" min="1" wire:model.live.debounce.400ms="monthlyPayment"
                                class="w-full px-3 py-2.5 bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-700 rounded-lg text-sm font-bold text-indigo-700 dark:text-indigo-300 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none"
                                placeholder="0">
                            <div class="mt-1.5 flex items-center justify-between gap-2">
                                <p class="text-[11px] text-slate-400">Dihitung otomatis, dapat disesuaikan manual.</p>
                                @if($monthlyPaymentOverridden)
                                    <button type="button" wire:click="resetMonthlyToAuto"
                                        class="text-[11px] font-semibold text-primary hover:underline whitespace-nowrap">
                                        Gunakan hitung otomatis
                                    </button>
                                @endif
                            </div>
                            @error('monthlyPayment')
                                <p class="text-xs text-rose-500 mt-1.5">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1.5">Tanggal Mulai Potong</label>
                            <input type="date" wire:model="startDate"
                                class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-white focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
                            @error('startDate')
                                <p class="text-xs text-rose-500 mt-1.5">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1.5">Tujuan Pinjaman</label>
                            <input type="text" wire:model="purpose"
                                class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-white focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none"
                                placeholder="Contoh: Biaya pendidikan / renovasi rumah">
                            @error('purpose')
                                <p class="text-xs text-rose-500 mt-1.5">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1.5">Catatan Tambahan</label>
                            <textarea wire:model="description" rows="3"
                                class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-white focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none"
                                placeholder="Opsional"></textarea>
                        </div>
                    </div>

                    <div class="mt-6 pt-5 border-t border-slate-100 dark:border-slate-700 grid grid-cols-1 sm:grid-cols-2 gap-2 sm:gap-3 sm:justify-end">
                        <a href="{{ route('admin.loans') }}"
                            class="inline-flex items-center justify-center px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 text-sm font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors w-full">
                            Batal
                        </a>
                        <button type="submit" wire:loading.attr="disabled"
                            class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg bg-primary hover:bg-primary/90 text-white text-sm font-bold shadow-sm disabled:opacity-60 disabled:cursor-not-allowed transition-colors w-full">
                            <i class='bx bx-save text-base' wire:loading.remove></i>
                            <span wire:loading.remove>Simpan & Aktifkan</span>
                            <span wire:loading>Menyimpan...</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="xl:col-span-4 space-y-4">
            <div class="bg-white dark:bg-darkCard rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-4 sm:p-5 xl:sticky xl:top-6">
                <h3 class="text-sm font-bold text-slate-800 dark:text-white mb-4">Ringkasan Simulasi</h3>

                <div class="mb-4 p-3 rounded-lg bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700">
                    <p class="text-[11px] font-bold text-slate-500 uppercase tracking-widest">Anggota</p>
                    @if($selectedMember)
                        <p class="text-sm font-semibold text-slate-800 dark:text-white mt-1">{{ $selectedMember->name }}</p>
                        <p class="text-xs text-slate-500">{{ $selectedMember->nomorAnggota }}</p>
                    @else
                        <p class="text-xs text-slate-400 mt-1">Belum memilih anggota</p>
                    @endif
                </div>

                <div class="space-y-2.5 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Pokok Pinjaman</span>
                        <span class="font-semibold text-slate-800 dark:text-white">Rp {{ number_format($simulation['baseAmount'], 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Margin ({{ number_format($simulation['interestRate'], 1) }}%)</span>
                        <span class="font-semibold text-slate-800 dark:text-white">Rp {{ number_format($simulation['interestAmount'], 0, ',', '.') }}</span>
                    </div>
                    @if($loanSource === 'BMT_ITQAN')
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Simwa BMT / bulan</span>
                            <span class="font-semibold text-slate-800 dark:text-white">Rp {{ number_format($simulation['simwa'], 0, ',', '.') }}</span>
                        </div>
                    @endif
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Tenor</span>
                        <span class="font-semibold text-slate-800 dark:text-white">{{ $simulation['tenor'] > 0 ? $simulation['tenor'] . ' bulan' : '-' }}</span>
                    </div>
                </div>

                <div class="my-4 border-t border-slate-100 dark:border-slate-700"></div>

                <div class="space-y-2.5">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-500">Total Hutang</span>
                        <span class="font-bold text-slate-900 dark:text-white">Rp {{ number_format($simulation['totalDebt'], 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-500">Angsuran Rekomendasi</span>
                        <span class="font-bold text-primary">Rp {{ number_format($simulation['calculatedMonthly'], 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-500">Angsuran Final</span>
                        <span class="font-bold {{ $monthlyPaymentOverridden ? 'text-amber-600 dark:text-amber-300' : 'text-emerald-600 dark:text-emerald-300' }}">
                            Rp {{ number_format($simulation['effectiveMonthly'], 0, ',', '.') }}
                        </span>
                    </div>
                </div>

                <div class="mt-4 p-3 rounded-lg {{ $monthlyPaymentOverridden ? 'bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800' : 'bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800' }}">
                    @if($monthlyPaymentOverridden)
                        <p class="text-xs font-semibold text-amber-700 dark:text-amber-300">Angsuran final menggunakan nilai manual.</p>
                    @else
                        <p class="text-xs font-semibold text-emerald-700 dark:text-emerald-300">Angsuran final mengikuti kalkulasi otomatis.</p>
                    @endif
                </div>

                <div class="mt-4 text-xs text-slate-500">
                    <p>Tanggal akhir estimasi: <span class="font-semibold text-slate-700 dark:text-slate-300">{{ $simulation['endDate'] ?? '-' }}</span></p>
                </div>
            </div>

            <div class="bg-white dark:bg-darkCard rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-4 sm:p-5">
                <h4 class="text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">Catatan Operasional</h4>
                <ul class="space-y-1.5 text-xs text-slate-500">
                    <li>Pastikan anggota yang dipilih sudah benar sebelum submit.</li>
                    <li>Gunakan override hanya jika ada kebijakan khusus.</li>
                    <li>Status pinjaman akan langsung aktif setelah disimpan.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
