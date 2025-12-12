<div>
    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-white">Daftar Anggota</h1>
            <p class="text-[11px] text-slate-500 mt-0.5">Kelola data keanggotaan, simpanan, dan poin.</p>
        </div>
        <div class="flex gap-2">
            <button wire:click="$dispatch('export-csv')"
                class="bg-white dark:bg-darkCard border border-slate-200 dark:border-slate-600 text-slate-700 dark:text-white hover:bg-slate-50 dark:hover:bg-slate-700 px-4 py-2 rounded-lg text-[12px] font-bold shadow-sm transition-colors flex items-center gap-2">
                <i class='bx bx-export'></i> Export CSV
            </button>
            <a href="{{ route('admin.members.create') }}"
                class="bg-primary hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-[12px] font-bold shadow-md shadow-indigo-500/20 transition-colors flex items-center gap-2">
                <i class='bx bx-user-plus text-lg'></i> Anggota Baru
            </a>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-darkCard p-4 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-indigo-50 dark:bg-indigo-500/10 flex items-center justify-center text-primary text-xl">
                <i class='bx bx-group'></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Total Anggota</p>
                <h4 class="text-lg font-bold text-slate-800 dark:text-white">{{ number_format($stats['total']) }}</h4>
            </div>
        </div>

        <div class="bg-white dark:bg-darkCard p-4 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center text-emerald-600 text-xl">
                <i class='bx bx-user-check'></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Anggota Aktif</p>
                <h4 class="text-lg font-bold text-slate-800 dark:text-white">{{ number_format($stats['active']) }}</h4>
            </div>
        </div>

        <div class="bg-white dark:bg-darkCard p-4 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-amber-50 dark:bg-amber-500/10 flex items-center justify-center text-amber-500 text-xl">
                <i class='bx bx-wallet'></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Dana Terhimpun</p>
                <h4 class="text-lg font-bold text-slate-800 dark:text-white">Rp {{ number_format($stats['totalSimpanan'] ?? 0, 0, ',', '.') }}</h4>
            </div>
        </div>

        <div class="bg-white dark:bg-darkCard p-4 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center text-blue-600 text-xl">
                <i class='bx bx-gift'></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Rata-rata Poin</p>
                <h4 class="text-lg font-bold text-slate-800 dark:text-white">{{ number_format($stats['avgPoints'] ?? 0) }} Pts</h4>
            </div>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if (session()->has('success'))
        <div class="bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30 text-emerald-700 dark:text-emerald-400 px-4 py-3 rounded-lg mb-6 flex items-center gap-2">
            <i class='bx bxs-check-circle text-xl'></i>
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/30 text-rose-700 dark:text-rose-400 px-4 py-3 rounded-lg mb-6 flex items-center gap-2">
            <i class='bx bxs-error-circle text-xl'></i>
            {{ session('error') }}
        </div>
    @endif

    {{-- Filters --}}
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 bg-white dark:bg-darkCard p-4 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 mb-6">
        <div class="relative">
            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 block">Status</label>
            <select wire:model.live="filterStatus"
                class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 text-[13px] rounded-md px-3 py-2 outline-none focus:ring-1 focus:ring-primary text-slate-700 dark:text-white cursor-pointer">
                <option value="">Semua Status</option>
                <option value="ACTIVE">Aktif</option>
                <option value="INACTIVE">Non-Aktif</option>
                <option value="SUSPENDED">Dibekukan</option>
            </select>
        </div>

        <div class="relative">
            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 block">Tier</label>
            <select wire:model.live="filterTier"
                class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 text-[13px] rounded-md px-3 py-2 outline-none focus:ring-1 focus:ring-primary text-slate-700 dark:text-white cursor-pointer">
                <option value="">Semua Tier</option>
                <option value="BRONZE">Bronze</option>
                <option value="SILVER">Silver</option>
                <option value="GOLD">Gold</option>
                <option value="PLATINUM">Platinum</option>
            </select>
        </div>

        <div class="relative">
            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 block">Unit Kerja</label>
            <select wire:model.live="filterUnitKerja"
                class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 text-[13px] rounded-md px-3 py-2 outline-none focus:ring-1 focus:ring-primary text-slate-700 dark:text-white cursor-pointer">
                <option value="">Semua Unit</option>
                @foreach ($unitKerjaList as $unit)
                    <option value="{{ $unit }}">{{ $unit }}</option>
                @endforeach
            </select>
        </div>

        <div class="relative">
            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 block">Tanggal Bergabung</label>
            <input type="date" wire:model.live="filterJoinDate"
                class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 text-[13px] rounded-md px-3 py-2 outline-none focus:ring-1 focus:ring-primary text-slate-700 dark:text-white">
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white dark:bg-darkCard rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600 dark:text-slate-400">
                <thead class="bg-slate-50 dark:bg-slate-800/50 text-[10px] uppercase font-bold text-slate-500 dark:text-slate-400 tracking-widest">
                    <tr>
                        <th class="px-5 py-3">Anggota</th>
                        <th class="px-5 py-3">Unit / Prodi</th>
                        <th class="px-5 py-3">Status & Tier</th>
                        <th class="px-5 py-3 text-right">Total Simpanan</th>
                        <th class="px-5 py-3 text-center">Poin</th>
                        <th class="px-5 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700 text-[13px]">
                    @forelse ($members as $member)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors group">
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center text-white font-bold">
                                        {{ strtoupper(substr($member->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <h6 class="font-bold text-slate-900 dark:text-white leading-none">{{ $member->name }}</h6>
                                        <p class="text-[10px] text-slate-400 mt-1 font-mono">{{ $member->nomorAnggota }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="block font-medium text-slate-700 dark:text-slate-300">{{ $member->unitKerja }}</span>
                                <span class="text-[10px] text-slate-400">{{ $member->email }}</span>
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex flex-col gap-1">
                                    <span class="inline-flex w-fit items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase border
                                        @if($member->status === 'ACTIVE') bg-emerald-50 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 border-emerald-100 dark:border-emerald-800
                                        @elseif($member->status === 'INACTIVE') bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-600
                                        @else bg-rose-50 dark:bg-rose-500/20 text-rose-600 dark:text-rose-400 border-rose-100 dark:border-rose-800
                                        @endif">
                                        {{ $member->status === 'ACTIVE' ? 'Aktif' : ($member->status === 'INACTIVE' ? 'Non-Aktif' : 'Dibekukan') }}
                                    </span>
                                    <span class="inline-flex w-fit items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold border
                                        @if($member->tier === 'PLATINUM') bg-purple-100 dark:bg-purple-500/20 text-purple-600 dark:text-purple-400 border-purple-200 dark:border-purple-800
                                        @elseif($member->tier === 'GOLD') bg-amber-100 dark:bg-amber-500/20 text-amber-600 dark:text-amber-400 border-amber-200 dark:border-amber-800
                                        @elseif($member->tier === 'SILVER') bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-600
                                        @else bg-orange-100 dark:bg-orange-500/20 text-orange-600 dark:text-orange-400 border-orange-200 dark:border-orange-800
                                        @endif">
                                        <i class='bx bxs-medal'></i> {{ $member->tier }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <p class="font-bold text-slate-900 dark:text-white">Rp {{ number_format($member->totalSimpanan, 0, ',', '.') }}</p>
                                <div class="text-[10px] text-slate-400 mt-0.5 flex justify-end gap-2">
                                    <span title="Pokok">P: {{ number_format($member->simpananPokok / 1000) }}k</span>
                                    <span title="Wajib">W: {{ number_format($member->simpananWajib / 1000) }}k</span>
                                    <span title="Sukarela">S: {{ number_format($member->simpananSukarela / 1000) }}k</span>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 text-center font-bold text-amber-500">
                                {{ number_format($member->points) }}
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.members.detail', $member->id) }}" 
                                        class="text-slate-400 hover:text-primary transition-colors p-1" title="Detail">
                                        <i class='bx bx-show text-lg'></i>
                                    </a>
                                    <a href="{{ route('admin.members.edit', $member->id) }}" 
                                        class="text-slate-400 hover:text-indigo-600 transition-colors p-1" title="Edit">
                                        <i class='bx bx-edit text-lg'></i>
                                    </a>
                                    @if($member->status === 'ACTIVE')
                                        <button wire:click="suspendMember({{ $member->id }})" 
                                            class="text-slate-400 hover:text-rose-500 transition-colors p-1" title="Bekukan">
                                            <i class='bx bx-block text-lg'></i>
                                        </button>
                                    @else
                                        <button wire:click="activateMember({{ $member->id }})" 
                                            class="text-slate-400 hover:text-emerald-500 transition-colors p-1" title="Aktifkan">
                                            <i class='bx bx-check-circle text-lg'></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-slate-500">
                                <i class='bx bx-user-x text-4xl mb-2'></i>
                                <p>Tidak ada data anggota.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="px-5 py-4 border-t border-slate-100 dark:border-slate-700">
            {{ $members->links() }}
        </div>
    </div>
</div>
