<div class="space-y-6">
    {{-- Wizard Steps --}}
    <x-rat-wizard-steps :currentStep="1" :sessionId="$session?->id" :sessionStatus="$session?->status ?? 'DRAFT'" />

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white dark:bg-darkCard p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="p-2 rounded-lg bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400">
                    <i class='bx bx-cog text-2xl'></i>
                </span>
                <h1 class="text-xl font-bold text-slate-800 dark:text-white">Konfigurasi Sesi RAT</h1>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400">
                Step 1: Tentukan tahun buku, data keuangan, dan alokasi SHU sesuai AD/ART koperasi.
            </p>
        </div>

        <div class="flex items-center gap-3">
            @if($allSessions->isNotEmpty())
                <div class="relative">
                    <select wire:change="loadSessionById($event.target.value)"
                        class="appearance-none bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white text-xs font-bold rounded-xl px-4 py-2.5 pr-9 outline-none cursor-pointer">
                        <option value="">— Pilih Sesi —</option>
                        @foreach($allSessions as $s)
                            <option value="{{ $s->id }}" {{ $session?->id === $s->id ? 'selected' : '' }}>
                                RAT {{ $s->year }} ({{ $s->status_label }})
                            </option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-500">
                        <i class='bx bx-chevron-down text-base'></i>
                    </div>
                </div>
            @endif

            <button wire:click="createNewSession"
                class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-sm transition-all flex items-center gap-2">
                <i class='bx bx-plus text-base'></i> Sesi Baru
            </button>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if (session()->has('success'))
        <div class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-400 px-4 py-3 rounded-xl flex items-center gap-2">
            <i class='bx bx-check-circle text-xl'></i>
            <span class="text-xs font-medium">{{ session('success') }}</span>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-400 px-4 py-3 rounded-xl flex items-center gap-2">
            <i class='bx bx-error-circle text-xl'></i>
            <span class="text-xs font-medium">{{ session('error') }}</span>
        </div>
    @endif

    {{-- Main Form --}}
    <div class="bg-white dark:bg-darkCard p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
        <h3 class="text-sm font-bold text-slate-800 dark:text-white mb-4 flex items-center gap-2">
            <i class='bx bx-edit text-base text-indigo-500'></i>
            Data Sesi RAT
        </h3>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div>
                <label class="block text-[11px] font-bold text-slate-500 mb-1">Tahun Buku</label>
                <input type="number" wire:model.blur="year" {{ $session && !$session->isEditable() ? 'disabled' : '' }}
                    class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2.5 text-xs font-bold text-slate-800 dark:text-white outline-none disabled:opacity-50">
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-500 mb-1">Tanggal Pelaksanaan RAT</label>
                <input type="date" wire:model.blur="eventDate" {{ $session && !$session->isEditable() ? 'disabled' : '' }}
                    class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2.5 text-xs text-slate-800 dark:text-white outline-none disabled:opacity-50">
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-500 mb-1">Total Laba Bersih (Rp)</label>
                <div class="relative">
                    <input type="number" wire:model.blur="totalNetProfit" {{ $session && !$session->isEditable() ? 'disabled' : '' }}
                        class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2.5 text-xs font-bold text-slate-800 dark:text-white outline-none disabled:opacity-50 pr-10">
                    <button wire:click="fetchFinancialData" title="Auto-fetch dari transaksi keuangan"
                        class="absolute right-2 top-1/2 -translate-y-1/2 text-indigo-500 hover:text-indigo-700 transition-colors">
                        <i class='bx bx-refresh text-lg'></i>
                    </button>
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-emerald-600 mb-1">Total SHU Dibagikan (Rp)</label>
                <input type="number" wire:model.live.debounce.300ms="totalMemberShu" {{ $session && !$session->isEditable() ? 'disabled' : '' }}
                    class="w-full bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-300 dark:border-emerald-700 rounded-xl px-3 py-2.5 text-xs font-bold text-emerald-600 outline-none disabled:opacity-50">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
            <div>
                <label class="block text-[11px] font-bold text-slate-500 mb-1">Judul / Tema RAT</label>
                <input type="text" wire:model.blur="title" {{ $session && !$session->isEditable() ? 'disabled' : '' }}
                    class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2.5 text-xs text-slate-800 dark:text-white outline-none disabled:opacity-50"
                    placeholder="Contoh: RAT Koperasi Bermadani Tahun Buku 2025">
            </div>
            <div>
                <label class="block text-[11px] font-bold text-indigo-600 mb-1">
                    % Alokasi SHU Anggota dari Laba Bersih
                </label>
                <input type="number" wire:model.live.debounce.300ms="memberAllocationPercentage" step="0.01" min="0" max="100" {{ $session && !$session->isEditable() ? 'disabled' : '' }}
                    class="w-full bg-indigo-50 dark:bg-indigo-950/40 border border-indigo-300 dark:border-indigo-700 rounded-xl px-3 py-2.5 text-xs font-bold text-indigo-600 outline-none disabled:opacity-50">
            </div>
        </div>

        <div class="mb-6">
            <label class="block text-[11px] font-bold text-slate-500 mb-1">Catatan / Notulensi</label>
            <textarea wire:model.blur="notes" rows="3" {{ $session && !$session->isEditable() ? 'disabled' : '' }}
                class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2.5 text-xs text-slate-800 dark:text-white outline-none disabled:opacity-50 resize-none"
                placeholder="Catatan keputusan rapat, berita acara, dsb."></textarea>
        </div>
    </div>

    {{-- 5-Pos Alokasi SHU --}}
    <div class="bg-white dark:bg-darkCard p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
        <h3 class="text-sm font-bold text-slate-800 dark:text-white mb-1 flex items-center gap-2">
            <i class='bx bx-pie-chart-alt-2 text-base text-purple-500'></i>
            Alokasi 5 Pos SHU (Sesuai AD/ART)
        </h3>
        <p class="text-[10px] text-slate-400 mb-4">
            Total alokasi harus = 100%. Saat ini: 
            <span class="{{ abs($allocationTotal - 100) <= 0.1 ? 'text-emerald-500 font-bold' : 'text-rose-500 font-bold' }}">
                {{ number_format($allocationTotal, 2) }}%
            </span>
        </p>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            @php
                $allocItems = [
                    ['model' => 'cadanganPercentage', 'label' => 'Dana Cadangan', 'color' => 'blue', 'icon' => 'bx-shield-quarter', 'desc' => 'Modal pemupukan cadangan koperasi'],
                    ['model' => 'jasaSimpananPercentage', 'label' => 'Jasa Simpanan', 'color' => 'emerald', 'icon' => 'bx-coin-stack', 'desc' => 'Proporsional saldo simpanan anggota'],
                    ['model' => 'jasaUsahaPercentage', 'label' => 'Jasa Usaha', 'color' => 'amber', 'icon' => 'bx-shopping-bag', 'desc' => 'Proporsional transaksi belanja anggota'],
                    ['model' => 'pengurusPercentage', 'label' => 'Pengurus', 'color' => 'purple', 'icon' => 'bx-user-voice', 'desc' => 'Honorarium & insentif pengurus'],
                    ['model' => 'danaSosialPercentage', 'label' => 'Dana Sosial', 'color' => 'rose', 'icon' => 'bx-heart', 'desc' => 'Pendidikan & sosial'],
                ];
            @endphp

            @foreach($allocItems as $item)
                <div class="bg-{{ $item['color'] }}-50/50 dark:bg-{{ $item['color'] }}-950/20 border border-{{ $item['color'] }}-200 dark:border-{{ $item['color'] }}-800 rounded-xl p-4">
                    <div class="flex items-center gap-2 mb-2">
                        <i class='bx {{ $item["icon"] }} text-{{ $item["color"] }}-500'></i>
                        <span class="text-[11px] font-bold text-{{ $item['color'] }}-700 dark:text-{{ $item['color'] }}-400">{{ $item['label'] }}</span>
                    </div>
                    <input type="number" wire:model.blur="{{ $item['model'] }}" step="0.01" min="0" max="100"
                        {{ $session && !$session->isEditable() ? 'disabled' : '' }}
                        class="w-full bg-white dark:bg-slate-800 border border-{{ $item['color'] }}-300 dark:border-{{ $item['color'] }}-700 rounded-lg px-3 py-2 text-sm font-bold text-{{ $item['color'] }}-600 outline-none disabled:opacity-50 text-center">
                    <p class="text-[9px] text-slate-400 mt-1.5">{{ $item['desc'] }}</p>
                    @if($totalMemberShu > 0)
                        @php
                            $pctVal = match($item['model']) {
                                'cadanganPercentage' => $cadanganPercentage,
                                'jasaSimpananPercentage' => $jasaSimpananPercentage,
                                'jasaUsahaPercentage' => $jasaUsahaPercentage,
                                'pengurusPercentage' => $pengurusPercentage,
                                'danaSosialPercentage' => $danaSosialPercentage,
                                default => 0,
                            };
                        @endphp
                        <p class="text-[10px] font-bold text-{{ $item['color'] }}-600 dark:text-{{ $item['color'] }}-400 mt-1">
                            = Rp {{ number_format((float) $totalMemberShu * ((float) $pctVal / 100), 0, ',', '.') }}
                        </p>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- KPI Preview --}}
    @if($totalNetProfit > 0 || $totalMemberShu > 0)
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white dark:bg-darkCard p-5 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Laba Bersih</p>
                <h3 class="text-xl font-bold text-slate-800 dark:text-white">
                    Rp {{ number_format((float) $totalNetProfit, 0, ',', '.') }}
                </h3>
            </div>
            <div class="bg-white dark:bg-darkCard p-5 rounded-2xl shadow-sm border border-emerald-200 dark:border-emerald-800">
                <p class="text-[10px] font-bold text-emerald-500 uppercase tracking-widest mb-1">SHU Dibagikan</p>
                <h3 class="text-xl font-bold text-emerald-600 dark:text-emerald-400">
                    Rp {{ number_format((float) $totalMemberShu, 0, ',', '.') }}
                </h3>
                <p class="text-[10px] text-emerald-600/80 font-medium mt-0.5">{{ number_format((float) $memberAllocationPercentage, 2) }}% dari Laba Bersih</p>
            </div>
            <div class="bg-white dark:bg-darkCard p-5 rounded-2xl shadow-sm border border-blue-200 dark:border-blue-800">
                <p class="text-[10px] font-bold text-blue-500 uppercase tracking-widest mb-1">Modal Usaha / Cadangan</p>
                <h3 class="text-xl font-bold text-blue-600 dark:text-blue-400">
                    Rp {{ number_format(max(0, (float) $totalNetProfit - (float) $totalMemberShu), 0, ',', '.') }}
                </h3>
                <p class="text-[10px] text-slate-400 mt-0.5">Ditahan untuk Operasional</p>
            </div>
        </div>
    @endif

    {{-- Actions --}}
    <div class="bg-white dark:bg-darkCard p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <button wire:click="saveSession"
                class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold px-5 py-2.5 rounded-xl shadow-sm transition-all flex items-center gap-2">
                <i class='bx bx-save text-base'></i> Simpan Konfigurasi
            </button>

            @if($session)
                <button wire:click="advanceToEligibility"
                    class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-5 py-2.5 rounded-xl shadow-lg shadow-emerald-600/20 transition-all flex items-center gap-2">
                    Lanjut ke Anggota & Eligibility
                    <i class='bx bx-right-arrow-alt text-base'></i>
                </button>
            @endif
        </div>
    </div>
</div>
