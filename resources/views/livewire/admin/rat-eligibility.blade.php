<div class="space-y-6">
    {{-- Wizard Steps --}}
    <x-rat-wizard-steps :currentStep="2" :sessionId="$ratSession?->id" :sessionStatus="$ratSession?->status ?? 'DRAFT'" />

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white dark:bg-darkCard p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="p-2 rounded-lg bg-purple-50 dark:bg-purple-500/10 text-purple-600 dark:text-purple-400">
                    <i class='bx bx-group text-2xl'></i>
                </span>
                <h1 class="text-xl font-bold text-slate-800 dark:text-white">Anggota & Eligibility</h1>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400">
                Step 2: Tentukan tanggal cutoff keanggotaan dan atur anggota yang berhak mendapat SHU.
            </p>
        </div>
        <div>
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold {{ $ratSession?->isFinalized() ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' }}">
                RAT {{ $ratSession?->year }} — {{ $ratSession?->status_label ?? 'N/A' }}
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

    {{-- Summary Cards --}}
    @if(!empty($summary))
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-slate-50 dark:bg-slate-800/50 p-4 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-indigo-50 dark:bg-indigo-950/40 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                    <i class='bx bx-user-check text-lg'></i>
                </div>
                <div>
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest leading-none mb-1">Anggota Layak SHU</p>
                    <h4 class="text-base font-bold text-slate-800 dark:text-white leading-tight">
                        {{ $summary['eligibleCount'] ?? 0 }} <span class="text-slate-400 font-normal text-xs">/ {{ $summary['totalMembers'] ?? 0 }}</span>
                    </h4>
                </div>
            </div>

            <div class="bg-slate-50 dark:bg-slate-800/50 p-4 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-amber-50 dark:bg-amber-950/40 flex items-center justify-center text-amber-500 dark:text-amber-400">
                    <i class='bx bx-coin-stack text-lg'></i>
                </div>
                <div>
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest leading-none mb-1">Total Simp. Pokok</p>
                    <h4 class="text-base font-bold text-slate-800 dark:text-white leading-tight">
                        Rp {{ number_format($summary['totalSimpok'] ?? 0, 0, ',', '.') }}
                    </h4>
                </div>
            </div>

            <div class="bg-slate-50 dark:bg-slate-800/50 p-4 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-purple-50 dark:bg-purple-950/40 flex items-center justify-center text-purple-600 dark:text-purple-400">
                    <i class='bx bx-wallet text-lg'></i>
                </div>
                <div>
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest leading-none mb-1">Total Simp. Wajib</p>
                    <h4 class="text-base font-bold text-slate-800 dark:text-white leading-tight">
                        Rp {{ number_format($summary['totalSimwa'] ?? 0, 0, ',', '.') }}
                    </h4>
                </div>
            </div>

            <div class="bg-slate-50 dark:bg-slate-800/50 p-4 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                    <i class='bx bx-cabinet text-lg'></i>
                </div>
                <div>
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest leading-none mb-1">Total Simpanan Gabungan</p>
                    <h4 class="text-base font-bold text-emerald-600 dark:text-emerald-400 leading-tight">
                        Rp {{ number_format($summary['totalSimpanan'] ?? 0, 0, ',', '.') }}
                    </h4>
                </div>
            </div>
        </div>
    @endif

    {{-- Filter & Config --}}
    <div class="bg-white dark:bg-darkCard p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
            <div>
                <label class="block text-[11px] font-bold text-slate-500 mb-1">Tanggal Gabung Maksimal (Cutoff)</label>
                <input type="date" wire:model.blur="joinDateCutoff" {{ $ratSession?->isFinalized() ? 'disabled' : '' }}
                    class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2.5 text-xs text-slate-800 dark:text-white outline-none disabled:opacity-50">
                <p class="text-[9px] text-slate-400 mt-1">Anggota yang bergabung setelah tanggal ini otomatis tidak berhak SHU</p>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-500 mb-1">Cari Anggota</label>
                <input type="text" wire:model.live.debounce.300ms="searchMember"
                    placeholder="Nama / No. Anggota / Unit Kerja..."
                    class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2.5 text-xs text-slate-800 dark:text-white outline-none">
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-500 mb-1">Filter Status</label>
                <select wire:model.live="filterStatus"
                    class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2.5 text-xs text-slate-800 dark:text-white outline-none">
                    <option value="ALL">Semua Anggota</option>
                    <option value="ACTIVE">Aktif Saja</option>
                    <option value="INACTIVE">Non-aktif Saja</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Members Table --}}
    <div class="bg-white dark:bg-darkCard p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800 text-slate-500 dark:text-slate-400 uppercase text-[10px] tracking-wider border-b border-slate-200 dark:border-slate-700">
                        <th class="py-3 px-3">No</th>
                        <th class="py-3 px-3">No. Anggota</th>
                        <th class="py-3 px-3">Nama</th>
                        <th class="py-3 px-3">Unit Kerja</th>
                        <th class="py-3 px-3">Status</th>
                        <th class="py-3 px-3">Tgl Gabung</th>
                        <th class="py-3 px-3 text-right">Simp. Pokok</th>
                        <th class="py-3 px-3 text-right">Simp. Wajib</th>
                        <th class="py-3 px-3 text-center">Berhak SHU?</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse($members as $index => $member)
                        @php
                            $isEligible = app(\App\Services\ShuCalculationService::class)->isMemberEligible(
                                $member->id,
                                $member->joinDate?->format('Y-m-d H:i:s'),
                                $member->status,
                                $joinDateCutoff,
                                $excludedMemberIds,
                                $includedMemberIds
                            );
                        @endphp
                        <tr wire:key="member-{{ $member->id }}"
                            class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-all {{ !$isEligible ? 'opacity-40 bg-slate-50/30 dark:bg-slate-800/10' : '' }}">
                            <td class="py-2.5 px-3 font-mono text-slate-400">{{ $members->firstItem() + $index }}</td>
                            <td class="py-2.5 px-3 font-mono font-bold text-slate-700 dark:text-slate-300">
                                {{ $member->nomorAnggota ?? '-' }}
                            </td>
                            <td class="py-2.5 px-3 font-bold text-slate-800 dark:text-white">
                                {{ $member->name }}
                            </td>
                            <td class="py-2.5 px-3 text-slate-500">{{ $member->unitKerja ?? '-' }}</td>
                            <td class="py-2.5 px-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold {{ $member->status === 'ACTIVE' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400' }}">
                                    {{ $member->status === 'ACTIVE' ? 'Aktif' : 'Non-aktif' }}
                                </span>
                            </td>
                            <td class="py-2.5 px-3 text-slate-500 text-[10px]">
                                {{ $member->joinDate?->format('d/m/Y') ?? '-' }}
                            </td>
                            <td class="py-2.5 px-3 text-right font-mono text-slate-700 dark:text-slate-300">
                                Rp {{ number_format((float) $member->simpananPokok, 0, ',', '.') }}
                            </td>
                            <td class="py-2.5 px-3 text-right font-mono text-slate-700 dark:text-slate-300">
                                Rp {{ number_format((float) $member->simpananWajib, 0, ',', '.') }}
                            </td>
                            <td class="py-2.5 px-3 text-center">
                                <input type="checkbox" 
                                    wire:click="toggleMemberExclusion({{ $member->id }})"
                                    {{ $isEligible ? 'checked' : '' }}
                                    {{ $ratSession?->isFinalized() ? 'disabled' : '' }}
                                    class="rounded border-slate-300 dark:border-slate-700 text-indigo-600 focus:ring-indigo-500 cursor-pointer disabled:cursor-not-allowed w-4 h-4">
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-8 text-center text-slate-400">Tidak ada data anggota.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $members->links() }}
        </div>
    </div>

    {{-- Actions --}}
    <div class="bg-white dark:bg-darkCard p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <button wire:click="goBack"
                    class="bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 transition-all flex items-center gap-2">
                    <i class='bx bx-left-arrow-alt text-base'></i> Kembali
                </button>

                <button wire:click="saveEligibility"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold px-5 py-2.5 rounded-xl shadow-sm transition-all flex items-center gap-2">
                    <i class='bx bx-save text-base'></i> Simpan Eligibility
                </button>
            </div>

            <button wire:click="advanceToAllocation"
                class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-5 py-2.5 rounded-xl shadow-lg shadow-emerald-600/20 transition-all flex items-center gap-2">
                Lanjut ke Alokasi & Kalkulasi SHU
                <i class='bx bx-right-arrow-alt text-base'></i>
            </button>
        </div>
    </div>
</div>
