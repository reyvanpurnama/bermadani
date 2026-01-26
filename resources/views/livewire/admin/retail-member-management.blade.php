<div>
    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-white">Member Retail</h1>
            <p class="text-[11px] text-slate-500 mt-0.5">Daftar pelanggan minimarket (Non-Anggota Koperasi).</p>
        </div>
    </div>

    {{-- Filters --}}
    <div
        class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-white dark:bg-darkCard p-4 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 mb-6">
        <div class="relative">
            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 block">Cari
                Member</label>
            <div class="relative">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Nama, No Member, HP..."
                    class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 text-[13px] rounded-md pl-9 pr-3 py-2 outline-none focus:ring-1 focus:ring-primary text-slate-700 dark:text-white">
                <i class='bx bx-search absolute left-3 top-2.5 text-slate-400 text-lg'></i>
            </div>
        </div>
        <div class="relative">
            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 block">Tier
                Loyalty</label>
            <select wire:model.live="filterTier"
                class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 text-[13px] rounded-md px-3 py-2 outline-none focus:ring-1 focus:ring-primary text-slate-700 dark:text-white cursor-pointer">
                <option value="">Semua Tier</option>
                <option value="BRONZE">Bronze</option>
                <option value="SILVER">Silver</option>
                <option value="GOLD">Gold</option>
                <option value="PLATINUM">Platinum</option>
            </select>
        </div>
    </div>

    {{-- Table --}}
    <div
        class="bg-white dark:bg-darkCard rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600 dark:text-slate-400">
                <thead
                    class="bg-slate-50 dark:bg-slate-800/50 text-[10px] uppercase font-bold text-slate-500 dark:text-slate-400 tracking-widest">
                    <tr>
                        <th class="px-5 py-3">Member</th>
                        <th class="px-5 py-3">Kontak & Status</th>
                        <th class="px-5 py-3 text-right">Saldo Bermadani</th>
                        <th class="px-5 py-3 text-center">Poin & Tier</th>
                        <th class="px-5 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700 text-[13px]">
                    @forelse ($members as $member)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors group">
                            <td class="px-5 py-3.5 align-top">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-9 h-9 rounded-full bg-gradient-to-br from-emerald-500 to-teal-500 flex items-center justify-center text-white font-bold">
                                        {{ strtoupper(substr($member->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <h6 class="font-bold text-slate-900 dark:text-white leading-none">
                                            {{ $member->name }}
                                        </h6>
                                        <p class="text-[10px] text-slate-400 mt-1 font-mono">{{ $member->nomorAnggota }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 align-top">
                                <div class="flex flex-col gap-1">
                                    <span class="text-[11px] text-slate-500">{{ $member->phone ?? '-' }}</span>
                                    <span class="text-[10px] text-slate-400">{{ $member->email }}</span>
                                    <span class="inline-flex w-fit items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase border mt-1
                                            @if($member->status === 'ACTIVE') bg-emerald-50 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 border-emerald-100 dark:border-emerald-800
                                            @else bg-rose-50 dark:bg-rose-500/20 text-rose-600 dark:text-rose-400 border-rose-100 dark:border-rose-800
                                            @endif">
                                        {{ $member->status === 'ACTIVE' ? 'Aktif' : 'Non-Aktif' }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 align-top text-right">
                                <span class="font-bold text-slate-800 dark:text-white text-sm">
                                    Rp {{ number_format($member->simpananSukarela, 0, ',', '.') }}
                                </span>
                                <p class="text-[10px] text-slate-400 mt-0.5">Simpanan Sukarela</p>
                            </td>
                            <td class="px-5 py-3.5 text-center align-top">
                                <div class="flex flex-col items-center gap-1">
                                    <span
                                        class="text-sm font-bold text-amber-500">{{ number_format($member->points) }}</span>
                                    <span class="inline-flex w-fit items-center gap-1 px-1.5 py-[2px] rounded text-[9px] font-bold border
                                            @if($member->tier === 'PLATINUM') bg-purple-100 dark:bg-purple-500/20 text-purple-600 dark:text-purple-400 border-purple-200
                                            @elseif($member->tier === 'GOLD') bg-amber-100 dark:bg-amber-500/20 text-amber-600 dark:text-amber-400 border-amber-200
                                            @elseif($member->tier === 'SILVER') bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 border-slate-200
                                            @else bg-orange-100 dark:bg-orange-500/20 text-orange-600 dark:text-orange-400 border-orange-200
                                            @endif">
                                        {{ $member->tier }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 text-center align-top">
                                <a href="{{ route('admin.retail-members.show', $member->id) }}"
                                    class="inline-flex items-center gap-1 text-xs font-bold text-primary hover:text-indigo-700 bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 rounded-lg transition-colors">
                                    <i class='bx bx-show'></i> Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-8 text-center text-slate-500">
                                <i class='bx bx-shopping-bag text-4xl mb-2 opacity-50'></i>
                                <p>Tidak ada member retail ditemukan.</p>
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