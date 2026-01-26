<div class="space-y-6">
    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">Member Retail</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Daftar pelanggan minimarket (Non-Anggota Koperasi).</p>
        </div>
        
        <div class="flex items-center gap-2">
             <div class="px-3 py-1 bg-white dark:bg-darkCard rounded-full border border-slate-200 dark:border-slate-700 shadow-sm text-[10px] font-bold text-slate-500">
                Total: {{ $members->total() }} Members
             </div>
        </div>
    </div>

    {{-- Notification --}}
    @if (session()->has('message'))
        <div class="p-4 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-xl flex items-center gap-3 animate-in fade-in slide-in-from-top-2">
            <i class='bx bx-check-circle text-emerald-600 text-xl'></i>
            <p class="text-sm text-emerald-600 dark:text-emerald-400 font-bold">{{ session('message') }}</p>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="p-4 bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800 rounded-xl flex items-center gap-3 animate-in fade-in slide-in-from-top-2">
            <i class='bx bx-x-circle text-rose-600 text-xl'></i>
            <p class="text-sm text-rose-600 dark:text-rose-400 font-bold">{{ session('error') }}</p>
        </div>
    @endif

    {{-- Filters --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 bg-white dark:bg-darkCard p-1.5 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
        <div class="relative group">
            <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                <i class='bx bx-search text-slate-400 text-lg group-focus-within:text-primary transition-colors'></i>
            </div>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama, nomor member..."
                class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl pl-10 pr-4 py-2.5 text-sm focus:ring-2 focus:ring-primary/20 text-slate-700 dark:text-white placeholder-slate-400 transition-all">
        </div>
        
        <div class="relative">
            <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                <i class='bx bxs-medal text-slate-400 text-lg'></i>
            </div>
            <select wire:model.live="filterTier"
                class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl pl-10 pr-4 py-2.5 text-sm focus:ring-2 focus:ring-primary/20 text-slate-700 dark:text-white cursor-pointer appearance-none">
                <option value="">Semua Tier Loyalty</option>
                <option value="BRONZE">Bronze Tier</option>
                <option value="SILVER">Silver Tier</option>
                <option value="GOLD">Gold Tier</option>
                <option value="PLATINUM">Platinum Tier</option>
            </select>
            <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none">
                <i class='bx bx-chevron-down text-slate-400'></i>
            </div>
        </div>

        {{-- Add Member Button --}}
        <button wire:click="$set('showCreateModal', true)" class="flex items-center justify-center gap-2 bg-slate-900 text-white rounded-xl px-4 py-2.5 text-xs font-bold hover:bg-black transition-colors shadow-lg shadow-slate-900/20 active:scale-95 transform">
            <i class='bx bx-plus text-base'></i> Tambah
        </button>
    </div>

    {{-- Table --}}
    <div class="bg-white dark:bg-darkCard rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600 dark:text-slate-400">
                <thead class="bg-slate-50/80 dark:bg-slate-800/80 backdrop-blur-sm border-b border-slate-100 dark:border-slate-700">
                    <tr>
                        <th class="px-6 py-4 text-[10px] uppercase font-bold text-slate-400 tracking-wider">Member</th>
                        <th class="px-6 py-4 text-[10px] uppercase font-bold text-slate-400 tracking-wider">Kontak & Status</th>
                        <th class="px-6 py-4 text-[10px] uppercase font-bold text-slate-400 tracking-wider text-right">Saldo Bermadani</th>
                        <th class="px-6 py-4 text-[10px] uppercase font-bold text-slate-400 tracking-wider text-center">Loyalty</th>
                        <th class="px-6 py-4 text-[10px] uppercase font-bold text-slate-400 tracking-wider text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse ($members as $member)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors group">
                            <td class="px-6 py-4 align-top">
                                <div class="flex items-center gap-4">
                                    <div class="relative">
                                        <div class="w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center text-slate-600 dark:text-slate-300 font-bold shadow-sm border border-slate-200 dark:border-slate-600 group-hover:border-primary group-hover:text-primary transition-colors">
                                            {{ strtoupper(substr($member->name, 0, 1)) }}
                                        </div>
                                         <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-white dark:bg-darkCard rounded-full flex items-center justify-center">
                                            <div class="w-2 h-2 rounded-full {{ $member->status === 'ACTIVE' ? 'bg-emerald-500' : 'bg-rose-500' }}"></div>
                                        </div>
                                    </div>
                                    <div>
                                        <h6 class="font-bold text-slate-900 dark:text-white leading-tight group-hover:text-primary transition-colors">
                                            {{ $member->name }}
                                        </h6>
                                        <p class="text-[10px] text-slate-400 mt-0.5 font-mono">{{ $member->nomorAnggota }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 align-top">
                                <div class="flex flex-col gap-1.5">
                                    <div class="flex items-center gap-2 text-[11px] text-slate-500 dark:text-slate-400">
                                        <i class='bx bx-phone text-slate-400'></i> {{ $member->phone ?? '-' }}
                                    </div>
                                    <span class="text-[10px] font-bold uppercase tracking-wider
                                        {{ $member->status === 'ACTIVE' ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                                        {{ $member->status }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 align-top text-right">
                                <div class="flex flex-col items-end gap-1">
                                    <span class="font-medium text-slate-700 dark:text-slate-200 text-sm">
                                        Rp {{ number_format($member->simpananSukarela, 0, ',', '.') }}
                                    </span>
                                    @if($member->sukarela_payment_method === 'SALARY_DEDUCTION')
                                        <div class="flex items-center gap-1.5 text-[10px] text-emerald-600 dark:text-emerald-400/80 font-medium opacity-80">
                                            <i class='bx bxs-check-circle text-xs'></i> Autodebet
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 align-top text-center">
                                <div class="flex flex-col items-center gap-1.5">
                                    <span class="text-sm font-medium text-slate-700 dark:text-slate-200">{{ number_format($member->points) }} <span class="text-xs text-slate-400">pts</span></span>
                                    <span class="text-[10px] font-bold tracking-widest uppercase
                                        @if($member->tier === 'PLATINUM') text-purple-600 dark:text-purple-400
                                        @elseif($member->tier === 'GOLD') text-amber-600 dark:text-amber-400
                                        @elseif($member->tier === 'SILVER') text-slate-500 dark:text-slate-400
                                        @else text-orange-700 dark:text-orange-400/80
                                        @endif">
                                        {{ $member->tier }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 align-top text-center">
                                <a href="{{ route('admin.retail-members.show', $member->id) }}"
                                    class="inline-flex items-center gap-1 text-xs font-medium text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-white px-3 py-1.5 rounded-lg transition-colors border border-transparent hover:border-slate-200 dark:hover:border-slate-700 hover:bg-white dark:hover:bg-slate-800">
                                    Detail <i class='bx bx-chevron-right'></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-slate-400">
                                    <div class="w-16 h-16 bg-slate-50 dark:bg-slate-800 rounded-full flex items-center justify-center mb-4">
                                        <i class='bx bx-search text-3xl opacity-50'></i>
                                    </div>
                                    <h3 class="font-bold text-slate-600 dark:text-slate-300">Tidak ada member ditemukan</h3>
                                    <p class="text-xs mt-1 max-w-xs">Coba ubah kata kunci pencarian atau filter tier Anda.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/30">
            {{ $members->links() }}
        </div>
    </div>

    {{-- Create Modal --}}
    @if($showCreateModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm"
        x-data x-on:keydown.escape.window="$wire.set('showCreateModal', false)">
        
        <div class="bg-white dark:bg-darkCard w-full max-w-md rounded-2xl shadow-2xl border border-slate-100 dark:border-slate-700 overflow-hidden animate-in fade-in zoom-in-95 duration-200">
            <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center bg-slate-50/50 dark:bg-slate-800/50">
                <h3 class="font-bold text-slate-900 dark:text-white">Tambah Member Retail</h3>
                <button wire:click="$set('showCreateModal', false)" class="text-slate-400 hover:text-rose-500 transition-colors">
                    <i class='bx bx-x text-2xl'></i>
                </button>
            </div>
            
            <div class="p-6">
                <form wire:submit="createMember" class="space-y-4">
                    <div>
                        <label class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider mb-1.5">Nama Lengkap</label>
                        <input type="text" wire:model="newName" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-slate-900/10 dark:focus:ring-white/10 dark:text-white placeholder-slate-400" placeholder="Contoh: Budi Santoso">
                        @error('newName') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider mb-1.5">No. WhatsApp</label>
                        <input type="text" wire:model="newPhone" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-slate-900/10 dark:focus:ring-white/10 dark:text-white placeholder-slate-400" placeholder="Contoh: 08123456789">
                        <p class="text-[10px] text-slate-400 mt-1">*Nomor ini akan digunakan sebagai login ID.</p>
                        @error('newPhone') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider mb-1.5">Unit Kerja (Opsional)</label>
                        <input type="text" wire:model="newUnitKerja" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-slate-900/10 dark:focus:ring-white/10 dark:text-white placeholder-slate-400" placeholder="Contoh: Staff Keuangan">
                    </div>

                    <div class="pt-4 flex gap-3">
                        <button type="button" wire:click="$set('showCreateModal', false)" class="flex-1 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 text-xs font-bold hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="flex-1 py-2.5 rounded-xl bg-slate-900 hover:bg-black text-white text-xs font-bold shadow-lg shadow-slate-900/20 transition-all transform active:scale-95">
                            Simpan Member
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>