<div class="space-y-6">
    {{-- Wizard Steps --}}
    <x-rat-wizard-steps :currentStep="3" :sessionId="$ratSession?->id" :sessionStatus="$ratSession?->status ?? 'DRAFT'" />

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white dark:bg-darkCard p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="p-2 rounded-lg bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">
                    <i class='bx bx-pie-chart-alt-2 text-2xl'></i>
                </span>
                <h1 class="text-xl font-bold text-slate-800 dark:text-white">Alokasi & Kalkulasi SHU</h1>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400">
                Step 3: Hitung distribusi SHU per anggota berdasarkan Jasa Simpanan & Jasa Usaha, lalu sahkan.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold {{ $ratSession?->isFinalized() ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' }}">
                {{ $ratSession?->status_label ?? 'N/A' }}
            </span>
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
    @if (session()->has('info'))
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 text-blue-700 dark:text-blue-400 px-4 py-3 rounded-xl flex items-center gap-2">
            <i class='bx bx-info-circle text-xl'></i>
            <span class="text-xs font-medium">{{ session('info') }}</span>
        </div>
    @endif

    {{-- 5 Pos SHU Allocation Cards --}}
    @if(!empty($summary))
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            @php
                $allocCards = [
                    ['label' => 'Dana Cadangan', 'amount' => $summary['cadanganPool'] ?? 0, 'pct' => $ratSession?->cadangan_percentage ?? 25, 'color' => 'blue', 'icon' => 'bx-shield-quarter'],
                    ['label' => 'Jasa Simpanan', 'amount' => $summary['jasaSimpananPool'] ?? 0, 'pct' => $ratSession?->jasa_simpanan_percentage ?? 30, 'color' => 'emerald', 'icon' => 'bx-coin-stack'],
                    ['label' => 'Jasa Usaha', 'amount' => $summary['jasaUsahaPool'] ?? 0, 'pct' => $ratSession?->jasa_usaha_percentage ?? 25, 'color' => 'amber', 'icon' => 'bx-shopping-bag'],
                    ['label' => 'Pengurus', 'amount' => $summary['pengurusPool'] ?? 0, 'pct' => $ratSession?->pengurus_percentage ?? 10, 'color' => 'purple', 'icon' => 'bx-user-voice'],
                    ['label' => 'Dana Sosial', 'amount' => $summary['danaSosialPool'] ?? 0, 'pct' => $ratSession?->dana_sosial_percentage ?? 10, 'color' => 'rose', 'icon' => 'bx-heart'],
                ];
            @endphp

            @foreach($allocCards as $card)
                <div class="bg-white dark:bg-darkCard p-4 rounded-2xl shadow-sm border border-{{ $card['color'] }}-200 dark:border-{{ $card['color'] }}-800">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-7 h-7 rounded-lg bg-{{ $card['color'] }}-50 dark:bg-{{ $card['color'] }}-950/40 flex items-center justify-center">
                            <i class='bx {{ $card["icon"] }} text-{{ $card["color"] }}-500 text-sm'></i>
                        </div>
                        <span class="text-[10px] font-bold text-{{ $card['color'] }}-600 dark:text-{{ $card['color'] }}-400 uppercase tracking-wider">{{ $card['label'] }}</span>
                    </div>
                    <h4 class="text-base font-bold text-slate-800 dark:text-white">
                        Rp {{ number_format((float) $card['amount'], 0, ',', '.') }}
                    </h4>
                    <p class="text-[10px] text-{{ $card['color'] }}-500 font-medium mt-0.5">{{ number_format((float) $card['pct'], 1) }}% dari SHU</p>
                </div>
            @endforeach
        </div>

        {{-- Summary Row --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-slate-50 dark:bg-slate-800/50 p-4 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-indigo-50 dark:bg-indigo-950/40 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                    <i class='bx bx-user-check text-lg'></i>
                </div>
                <div>
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Anggota Berhak</p>
                    <h4 class="text-base font-bold text-slate-800 dark:text-white">{{ $summary['eligibleCount'] ?? 0 }} Anggota</h4>
                </div>
            </div>
            <div class="bg-slate-50 dark:bg-slate-800/50 p-4 rounded-2xl shadow-sm border border-emerald-200 dark:border-emerald-800 flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                    <i class='bx bx-wallet text-lg'></i>
                </div>
                <div>
                    <p class="text-[9px] font-bold text-emerald-500 uppercase tracking-widest">Total SHU Dibagikan</p>
                    <h4 class="text-base font-bold text-emerald-600 dark:text-emerald-400">Rp {{ number_format($summary['totalMemberShu'] ?? 0, 0, ',', '.') }}</h4>
                </div>
            </div>
            <div class="bg-slate-50 dark:bg-slate-800/50 p-4 rounded-2xl shadow-sm border border-blue-200 dark:border-blue-800 flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-blue-50 dark:bg-blue-950/40 flex items-center justify-center text-blue-600 dark:text-blue-400">
                    <i class='bx bx-building text-lg'></i>
                </div>
                <div>
                    <p class="text-[9px] font-bold text-blue-500 uppercase tracking-widest">Laba Ditahan</p>
                    <h4 class="text-base font-bold text-blue-600 dark:text-blue-400">Rp {{ number_format($summary['retainedAmount'] ?? 0, 0, ',', '.') }}</h4>
                </div>
            </div>
        </div>
    @endif

    {{-- Calculate Button --}}
    @if(!$ratSession?->isFinalized())
        <div class="bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-indigo-950/20 dark:to-purple-950/20 p-6 rounded-2xl border border-indigo-200 dark:border-indigo-800">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                    <h3 class="text-sm font-bold text-slate-800 dark:text-white flex items-center gap-2">
                        <i class='bx bx-calculator text-indigo-500'></i>
                        Perhitungan SHU Per Anggota
                    </h3>
                    <p class="text-[10px] text-slate-500 mt-1">
                        Klik tombol di bawah untuk menghitung ulang distribusi SHU seluruh anggota berdasarkan konfigurasi saat ini.
                    </p>
                </div>
                <button wire:click="recalculate"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold px-6 py-3 rounded-xl shadow-lg shadow-indigo-600/20 transition-all flex items-center gap-2 whitespace-nowrap">
                    <i class='bx bx-refresh text-lg'></i> Hitung Ulang SHU
                </button>
            </div>
        </div>
    @endif

    {{-- Distribution Table --}}
    <div class="bg-white dark:bg-darkCard p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-bold text-slate-800 dark:text-white flex items-center gap-2">
                <i class='bx bx-table text-base text-slate-400'></i>
                Tabel Distribusi SHU Per Anggota
            </h3>
            <input type="text" wire:model.live.debounce.300ms="searchMember"
                placeholder="Cari Anggota..."
                class="bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs rounded-xl px-4 py-2 text-slate-800 dark:text-white outline-none w-52">
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800 text-slate-500 dark:text-slate-400 uppercase text-[10px] tracking-wider border-b border-slate-200 dark:border-slate-700">
                        <th class="py-3 px-3">No</th>
                        <th class="py-3 px-3">No. Anggota</th>
                        <th class="py-3 px-3">Nama</th>
                        <th class="py-3 px-3 text-right">Simp. Pokok</th>
                        <th class="py-3 px-3 text-right">Simp. Wajib</th>
                        <th class="py-3 px-3 text-right">Total Belanja</th>
                        <th class="py-3 px-3 text-right">Jasa Simpanan</th>
                        <th class="py-3 px-3 text-right">Jasa Usaha</th>
                        <th class="py-3 px-3 text-center">Porsi</th>
                        <th class="py-3 px-3 text-right font-bold">Total SHU</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse($distributions as $index => $dist)
                        @php $member = $dist->member; @endphp
                        <tr wire:key="dist-{{ $dist->id }}" 
                            class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-all {{ (float) $dist->shu_amount <= 0 ? 'opacity-40' : '' }}">
                            <td class="py-2.5 px-3 font-mono text-slate-400">{{ $distributions->firstItem() + $index }}</td>
                            <td class="py-2.5 px-3 font-mono font-bold text-slate-700 dark:text-slate-300">
                                {{ $member?->nomorAnggota ?? '-' }}
                            </td>
                            <td class="py-2.5 px-3 font-bold text-slate-800 dark:text-white">
                                <div>{{ $member?->name ?? '-' }}</div>
                                <div class="text-[9px] text-slate-400">{{ $member?->unitKerja ?? '' }}</div>
                            </td>
                            <td class="py-2.5 px-3 text-right font-mono text-slate-600 dark:text-slate-400">
                                Rp {{ number_format((float) ($dist->simpanan_pokok_snapshot > 0 ? $dist->simpanan_pokok_snapshot : ($member?->simpananPokok ?? 0)), 0, ',', '.') }}
                            </td>
                            <td class="py-2.5 px-3 text-right font-mono text-slate-600 dark:text-slate-400">
                                Rp {{ number_format((float) ($dist->simpanan_wajib_snapshot > 0 ? $dist->simpanan_wajib_snapshot : ($dist->total_simpanan_amount ?? $member?->simpananWajib ?? 0)), 0, ',', '.') }}
                            </td>
                            <td class="py-2.5 px-3 text-right font-mono text-slate-600 dark:text-slate-400">
                                Rp {{ number_format((float) $dist->total_transaksi_amount, 0, ',', '.') }}
                            </td>
                            <td class="py-2.5 px-3 text-right font-mono text-emerald-600 dark:text-emerald-400">
                                Rp {{ number_format((float) $dist->jasa_simpanan_amount, 0, ',', '.') }}
                            </td>
                            <td class="py-2.5 px-3 text-right font-mono text-amber-600 dark:text-amber-400">
                                Rp {{ number_format((float) $dist->jasa_usaha_amount, 0, ',', '.') }}
                            </td>
                            <td class="py-2.5 px-3 text-center font-mono font-bold text-indigo-600 dark:text-indigo-400">
                                {{ number_format((float) $dist->portion_percentage, 3, ',', '.') }}%
                            </td>
                            <td class="py-2.5 px-3 text-right font-mono font-bold text-emerald-600 dark:text-emerald-400 text-sm">
                                Rp {{ number_format((float) $dist->shu_amount, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="py-8 text-center text-slate-400">
                                <div class="flex flex-col items-center gap-2">
                                    <i class='bx bx-calculator text-4xl text-slate-300'></i>
                                    <p>Belum ada data distribusi. Klik <strong>"Hitung Ulang SHU"</strong> untuk memulai perhitungan.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($distributions, 'links'))
            <div class="mt-4">
                {{ $distributions->links() }}
            </div>
        @endif
    </div>

    {{-- Actions --}}
    <div class="bg-white dark:bg-darkCard p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <button wire:click="goBack"
                    class="bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 transition-all flex items-center gap-2">
                    <i class='bx bx-left-arrow-alt text-base'></i> Kembali
                </button>

                @if($ratSession?->isFinalized())
                    <button wire:click="reopenSession"
                        class="bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold px-4 py-2.5 rounded-xl transition-all flex items-center gap-2">
                        <i class='bx bx-edit text-base'></i> Buka Kembali (Draft)
                    </button>
                @endif
            </div>

            <div class="flex items-center gap-2">
                @if(!$ratSession?->isFinalized())
                    <button wire:click="finalizeSession"
                        onclick="return confirm('Apakah Anda yakin? SHU akan dipublikasikan ke portal anggota.')"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-5 py-2.5 rounded-xl shadow-lg shadow-emerald-600/20 transition-all flex items-center gap-2">
                        <i class='bx bx-check-double text-base'></i> Sahkan & Publikasikan SHU
                    </button>
                @else
                    <button wire:click="advanceToDisbursement"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-5 py-2.5 rounded-xl shadow-lg shadow-emerald-600/20 transition-all flex items-center gap-2">
                        Lanjut ke Pencairan SHU
                        <i class='bx bx-right-arrow-alt text-base'></i>
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>
