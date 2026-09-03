<div class="p-6 space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white">CALK Builder</h1>
            <p class="text-sm text-slate-500 dark:text-zinc-400">Penyusunan Catatan Atas Laporan Keuangan (CALK) Terstruktur</p>
        </div>
        <div class="flex items-center gap-3">
            <select wire:model.live="fiscalYear" class="bg-white dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 text-slate-700 dark:text-zinc-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 outline-none shadow-sm">
                @for ($y = date('Y'); $y >= 2024; $y--)
                    <option value="{{ $y }}">Tahun Buku {{ $y }}</option>
                @endfor
            </select>
            <button wire:click="save" class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg px-5 py-2 shadow-sm font-semibold transition-all">
                <i class='bx bx-save text-lg'></i> Simpan CALK
            </button>
            <a href="{{ route('admin.reports.calk.pdf', ['year' => $fiscalYear]) }}" target="_blank" class="flex items-center gap-2 bg-white dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 text-slate-700 dark:text-zinc-300 hover:bg-slate-50 dark:hover:bg-zinc-700 rounded-lg px-4 py-2 shadow-sm font-medium transition-all">
                <i class='bx bxs-file-pdf text-red-500 text-lg'></i> Cetak PDF
            </a>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="p-4 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 rounded-xl flex items-center gap-2">
            <i class='bx bx-check-circle text-xl'></i>
            <span>{{ session('message') }}</span>
        </div>
    @endif

    <!-- Multi-Step Wizard Stepper -->
    <div class="grid grid-cols-2 md:grid-cols-6 gap-2">
        @php
            $steps = [
                1 => ['title' => '1. Profil', 'icon' => 'bx-building'],
                2 => ['title' => '2. Kebijakan', 'icon' => 'bx-book-bookmark'],
                3 => ['title' => '3. Kas & Bank', 'icon' => 'bx-money'],
                4 => ['title' => '4. Piutang', 'icon' => 'bx-wallet'],
                5 => ['title' => '5. Simpanan', 'icon' => 'bx-vault'],
                6 => ['title' => '6. Syariah', 'icon' => 'bx-check-shield'],
            ];
        @endphp
        @foreach($steps as $num => $step)
            <button wire:click="setStep({{ $num }})" class="p-3 rounded-xl border text-left flex items-center gap-2 transition-all {{ $activeStep === $num ? 'bg-indigo-600 text-white border-indigo-600 shadow-md font-semibold' : 'bg-white dark:bg-zinc-800 border-slate-200 dark:border-zinc-700 text-slate-600 dark:text-zinc-300 hover:bg-slate-50 dark:hover:bg-zinc-700' }}">
                <i class='bx {{ $step['icon'] }} text-lg'></i>
                <span class="text-xs truncate">{{ $step['title'] }}</span>
            </button>
        @endforeach
    </div>

    <!-- Step Content Card -->
    <div class="bg-white dark:bg-zinc-800 rounded-2xl shadow-sm border border-slate-200 dark:border-zinc-700 p-6">
        
        <!-- STEP 1: Profil Koperasi -->
        @if($activeStep === 1)
            <div class="space-y-5">
                <div class="border-b border-slate-100 dark:border-zinc-700 pb-3">
                    <h2 class="text-lg font-bold text-slate-800 dark:text-white">BAB I. Gambaran Umum Koperasi</h2>
                    <p class="text-xs text-slate-500">Isi profil legalitas dan kegiatan utama operasional koperasi.</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-zinc-300 mb-2">1.1 Pendirian dan Legalitas Koperasi</label>
                    <textarea wire:model="content.bab1_profil" rows="4" class="w-full bg-slate-50 dark:bg-zinc-900 border border-slate-200 dark:border-zinc-700 rounded-xl p-3 text-sm text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-zinc-300 mb-2">1.2 Kegiatan Utama Usaha</label>
                    <textarea wire:model="content.bab1_kegiatan" rows="4" class="w-full bg-slate-50 dark:bg-zinc-900 border border-slate-200 dark:border-zinc-700 rounded-xl p-3 text-sm text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none"></textarea>
                </div>
            </div>
        @endif

        <!-- STEP 2: Kebijakan Akuntansi -->
        @if($activeStep === 2)
            <div class="space-y-5">
                <div class="border-b border-slate-100 dark:border-zinc-700 pb-3">
                    <h2 class="text-lg font-bold text-slate-800 dark:text-white">BAB II. Kebijakan Akuntansi Significant</h2>
                    <p class="text-xs text-slate-500">Dasar penyusunan laporan keuangan dan standar akuntansi yang digunakan.</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-zinc-300 mb-2">2.1 Dasar Penyusunan & Standar Akuntansi</label>
                    <textarea wire:model="content.bab2_kebijakan" rows="5" class="w-full bg-slate-50 dark:bg-zinc-900 border border-slate-200 dark:border-zinc-700 rounded-xl p-3 text-sm text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none"></textarea>
                </div>
            </div>
        @endif

        <!-- STEP 3: Kas & Bank -->
        @if($activeStep === 3)
            <div class="space-y-5">
                <div class="border-b border-slate-100 dark:border-zinc-700 pb-3">
                    <h2 class="text-lg font-bold text-slate-800 dark:text-white">BAB III. Rincian Kas dan Setara Kas</h2>
                    <p class="text-xs text-slate-500">Breakdown alokasi kas tunai brankas dan saldo di rekening bank.</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-zinc-300 mb-2">Kas Tunai Brankas (Rp)</label>
                        <input type="number" wire:model="content.bab3_kas_tunai" class="w-full bg-slate-50 dark:bg-zinc-900 border border-slate-200 dark:border-zinc-700 rounded-xl p-3 text-sm font-mono text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-zinc-300 mb-2">Bank Syariah / Operasional (Rp)</label>
                        <input type="number" wire:model="content.bab3_bank" class="w-full bg-slate-50 dark:bg-zinc-900 border border-slate-200 dark:border-zinc-700 rounded-xl p-3 text-sm font-mono text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                </div>
                <div class="p-4 bg-slate-50 dark:bg-zinc-900/50 rounded-xl border border-slate-200 dark:border-zinc-700 flex justify-between items-center text-sm font-bold text-slate-800 dark:text-white">
                    <span>Total Kas & Setara Kas (Auto Neraca):</span>
                    <span class="font-mono text-indigo-600 dark:text-indigo-400">Rp {{ number_format($neraca['kas'] ?? 0, 0, ',', '.') }}</span>
                </div>
            </div>
        @endif

        <!-- STEP 4: Piutang Pembiayaan -->
        @if($activeStep === 4)
            <div class="space-y-5">
                <div class="border-b border-slate-100 dark:border-zinc-700 pb-3">
                    <h2 class="text-lg font-bold text-slate-800 dark:text-white">BAB IV. Piutang Pembiayaan Syariah</h2>
                    <p class="text-xs text-slate-500">Breakdown saldo piutang pembiayaan internal vs BMT Itqan.</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-zinc-300 mb-2">Pembiayaan Bermadani (Rp)</label>
                        <input type="number" wire:model="content.bab4_bermadani" class="w-full bg-slate-50 dark:bg-zinc-900 border border-slate-200 dark:border-zinc-700 rounded-xl p-3 text-sm font-mono text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-zinc-300 mb-2">Pembiayaan BMT Itqan (Rp)</label>
                        <input type="number" wire:model="content.bab4_bmt" class="w-full bg-slate-50 dark:bg-zinc-900 border border-slate-200 dark:border-zinc-700 rounded-xl p-3 text-sm font-mono text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                </div>
                <div class="p-4 bg-slate-50 dark:bg-zinc-900/50 rounded-xl border border-slate-200 dark:border-zinc-700 flex justify-between items-center text-sm font-bold text-slate-800 dark:text-white">
                    <span>Total Piutang Pembiayaan (Auto Neraca):</span>
                    <span class="font-mono text-indigo-600 dark:text-indigo-400">Rp {{ number_format($neraca['piutang_pembiayaan'] ?? 0, 0, ',', '.') }}</span>
                </div>
            </div>
        @endif

        <!-- STEP 5: Simpanan Anggota -->
        @if($activeStep === 5)
            <div class="space-y-5">
                <div class="border-b border-slate-100 dark:border-zinc-700 pb-3">
                    <h2 class="text-lg font-bold text-slate-800 dark:text-white">BAB V. Simpanan Anggota & Akad</h2>
                    <p class="text-xs text-slate-500">Ringkasan simpanan terambil langsung dari data Neraca real-time.</p>
                </div>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between p-3 bg-slate-50 dark:bg-zinc-900 rounded-xl border border-slate-100 dark:border-zinc-700">
                        <div>
                            <div class="font-semibold text-slate-800 dark:text-white">Simpanan Pokok</div>
                            <div class="text-xs text-slate-400">Akad: Syirkah / Ekuitas</div>
                        </div>
                        <div class="font-mono font-bold text-slate-800 dark:text-white">Rp {{ number_format($neraca['simpanan_pokok'] ?? 0, 0, ',', '.') }}</div>
                    </div>
                    <div class="flex justify-between p-3 bg-slate-50 dark:bg-zinc-900 rounded-xl border border-slate-100 dark:border-zinc-700">
                        <div>
                            <div class="font-semibold text-slate-800 dark:text-white">Simpanan Wajib</div>
                            <div class="text-xs text-slate-400">Akad: Syirkah / Ekuitas</div>
                        </div>
                        <div class="font-mono font-bold text-slate-800 dark:text-white">Rp {{ number_format($neraca['simpanan_wajib'] ?? 0, 0, ',', '.') }}</div>
                    </div>
                    <div class="flex justify-between p-3 bg-slate-50 dark:bg-zinc-900 rounded-xl border border-slate-100 dark:border-zinc-700">
                        <div>
                            <div class="font-semibold text-slate-800 dark:text-white">Simpanan Sukarela</div>
                            <div class="text-xs text-slate-400">Akad: Wadiah Yad Dhomanah (Titipan)</div>
                        </div>
                        <div class="font-mono font-bold text-slate-800 dark:text-white">Rp {{ number_format($neraca['simpanan_anggota'] ?? 0, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
        @endif

        <!-- STEP 6: Kepatuhan Syariah -->
        @if($activeStep === 6)
            <div class="space-y-5">
                <div class="border-b border-slate-100 dark:border-zinc-700 pb-3">
                    <h2 class="text-lg font-bold text-slate-800 dark:text-white">BAB VI. Kepatuhan Syariah & Opini DPS</h2>
                    <p class="text-xs text-slate-500">Pernyataan kesesuaian syariah dari Dewan Pengawas Syariah (DPS).</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-zinc-300 mb-2">6.1 Narasi Opini Dewan Pengawas Syariah (DPS)</label>
                    <textarea wire:model="content.bab6_opini_dps" rows="5" class="w-full bg-slate-50 dark:bg-zinc-900 border border-slate-200 dark:border-zinc-700 rounded-xl p-3 text-sm text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none"></textarea>
                </div>
            </div>
        @endif

        <!-- Stepper Navigation Controls -->
        <div class="mt-8 pt-4 border-t border-slate-100 dark:border-zinc-700 flex justify-between items-center">
            <button @if($activeStep === 1) disabled @endif wire:click="setStep({{ max(1, $activeStep - 1) }})" class="px-4 py-2 rounded-xl text-sm font-medium border border-slate-200 dark:border-zinc-700 text-slate-600 dark:text-zinc-400 disabled:opacity-40 disabled:cursor-not-allowed hover:bg-slate-50 dark:hover:bg-zinc-700">
                <i class='bx bx-chevron-left'></i> Sebelumnya
            </button>
            <div class="text-xs text-slate-400">Langkah {{ $activeStep }} dari 6</div>
            <button @if($activeStep === 6) disabled @endif wire:click="setStep({{ min(6, $activeStep + 1) }})" class="px-4 py-2 rounded-xl text-sm font-medium bg-slate-800 dark:bg-zinc-700 text-white disabled:opacity-40 disabled:cursor-not-allowed hover:bg-slate-700">
                Berikutnya <i class='bx bx-chevron-right'></i>
            </button>
        </div>

    </div>
</div>
