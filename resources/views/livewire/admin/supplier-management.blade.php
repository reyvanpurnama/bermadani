<div>
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-white">Manajemen Supplier</h1>
            <p class="text-[11px] text-slate-500 mt-0.5">Kelola pendaftaran dan status supplier.</p>
        </div>
        <div class="flex gap-2">
            <!-- Actions if needed -->
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-darkCard p-4 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Total Supplier</p>
            <h3 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $stats['total'] }}</h3>
        </div>
        <div class="bg-white dark:bg-darkCard p-4 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Menunggu Approval</p>
            <h3 class="text-2xl font-bold text-amber-500">{{ $stats['pending'] }}</h3>
        </div>
        <div class="bg-white dark:bg-darkCard p-4 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Aktif</p>
            <h3 class="text-2xl font-bold text-emerald-500">{{ $stats['active'] }}</h3>
        </div>
        <div class="bg-white dark:bg-darkCard p-4 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Suspended</p>
            <h3 class="text-2xl font-bold text-rose-500">{{ $stats['suspended'] }}</h3>
        </div>
    </div>

    <!-- Filters -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 bg-white dark:bg-darkCard p-4 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 mb-6">
        <div class="relative col-span-2">
            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 block">Cari</label>
            <div class="relative">
                <i class='bx bx-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400'></i>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Nama, Bisnis, Email, Kode..." class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 text-[13px] rounded-md pl-9 pr-3 py-2 outline-none focus:ring-1 focus:ring-primary text-slate-700 dark:text-white">
            </div>
        </div>
        <div class="relative">
            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 block">Status</label>
            <select wire:model.live="filterStatus" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 text-[13px] rounded-md px-3 py-2 outline-none focus:ring-1 focus:ring-primary text-slate-700 dark:text-white cursor-pointer">
                <option value="">Semua Status</option>
                <option value="PENDING">Pending</option>
                <option value="ACTIVE">Active</option>
                <option value="SUSPENDED">Suspended</option>
                <option value="REJECTED">Rejected</option>
            </select>
        </div>
        <div class="flex items-end">
            <button wire:click="clearFilters" class="w-full bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-600 font-medium py-2 rounded-md text-[13px] transition-colors">
                Reset Filter
            </button>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="bg-emerald-100 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg mb-6 text-sm flex items-center gap-2">
            <i class='bx bxs-check-circle text-xl'></i>
            {{ session('success') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="bg-rose-100 border border-rose-200 text-rose-700 px-4 py-3 rounded-lg mb-6 text-sm flex items-center gap-2">
            <i class='bx bxs-error-circle text-xl'></i>
            {{ session('error') }}
        </div>
    @endif

    <!-- Table -->
    <div class="bg-white dark:bg-darkCard rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-100 dark:border-slate-700">
                    <tr>
                        <th class="px-5 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Supplier</th>
                        <th class="px-5 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Kontak</th>
                        <th class="px-5 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Kategori</th>
                        <th class="px-5 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-center">Status</th>
                        <th class="px-5 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-center">Produk</th>
                        <th class="px-5 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700 text-[13px]">
                    @forelse($suppliers as $supplier)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors group">
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center text-lg font-bold text-primary">
                                        {{ substr($supplier->businessName, 0, 1) }}
                                    </div>
                                    <div>
                                        <h6 class="font-semibold text-slate-800 dark:text-white leading-none">{{ $supplier->businessName }}</h6>
                                        <p class="text-[10px] text-slate-400 mt-0.5">{{ $supplier->code }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="text-slate-600 dark:text-slate-400">
                                    <p class="font-medium text-slate-800 dark:text-white">{{ $supplier->ownerName }}</p>
                                    <p class="text-[11px]">{{ $supplier->phone }}</p>
                                    <p class="text-[11px]">{{ $supplier->email }}</p>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 text-slate-600 dark:text-slate-400">
                                {{ $supplier->productCategory ?? '-' }}
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                @php
                                    $statusClass = match($supplier->status) {
                                        'ACTIVE' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400',
                                        'PENDING' => 'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-400',
                                        'SUSPENDED' => 'bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-400',
                                        'REJECTED' => 'bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-400',
                                        default => 'bg-slate-100 text-slate-700',
                                    };
                                    $statusLabel = match($supplier->status) {
                                        'ACTIVE' => 'Aktif',
                                        'PENDING' => 'Menunggu',
                                        'APPROVED' => 'Disetujui',
                                        'SUSPENDED' => 'Suspended',
                                        'REJECTED' => 'Ditolak',
                                        default => $supplier->status,
                                    };
                                @endphp
                                <span class="{{ $statusClass }} px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-center font-medium">
                                {{ $supplier->currentActiveProducts }} / {{ $supplier->maxActiveProducts }}
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.suppliers.detail', $supplier->id) }}" class="text-slate-400 hover:text-primary transition-colors text-lg" title="Detail">
                                        <i class='bx bx-show'></i>
                                    </a>
                                    
                                    @if($supplier->status === 'PENDING')
                                        <button wire:click="approve({{ $supplier->id }})" class="text-emerald-500 hover:text-emerald-700 transition-colors text-lg" title="Approve">
                                            <i class='bx bx-check-circle'></i>
                                        </button>
                                        <button wire:click="openRejectModal({{ $supplier->id }})" class="text-rose-500 hover:text-rose-700 transition-colors text-lg" title="Reject">
                                            <i class='bx bx-x-circle'></i>
                                        </button>
                                    @endif

                                    @if($supplier->status === 'ACTIVE')
                                        <button wire:click="openSuspendModal({{ $supplier->id }})" class="text-amber-500 hover:text-amber-700 transition-colors text-lg" title="Suspend">
                                            <i class='bx bx-block'></i>
                                        </button>
                                    @endif

                                    @if($supplier->status === 'SUSPENDED')
                                        <button wire:click="activate({{ $supplier->id }})" class="text-emerald-500 hover:text-emerald-700 transition-colors text-lg" title="Activate">
                                            <i class='bx bx-refresh'></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-slate-500">
                                <div class="flex flex-col items-center justify-center">
                                    <i class='bx bx-search-alt text-4xl mb-2 text-slate-300'></i>
                                    <p>Tidak ada supplier ditemukan.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="px-5 py-4 border-t border-slate-100 dark:border-slate-700">
            {{ $suppliers->links() }}
        </div>
    </div>

    <!-- Reject Modal -->
    @if($showRejectModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
        <div class="bg-white dark:bg-darkCard rounded-xl shadow-xl w-full max-w-md p-6 animate-fade-up">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Tolak Supplier</h3>
            <p class="text-sm text-slate-500 mb-4">Apakah Anda yakin ingin menolak supplier ini? Berikan alasan penolakan.</p>
            
            <div class="mb-4">
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Alasan Penolakan</label>
                <textarea wire:model="rejectReason" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-lg p-2 text-sm outline-none focus:ring-2 focus:ring-rose-500" rows="3" placeholder="Contoh: Dokumen tidak lengkap..."></textarea>
                @error('rejectReason') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
            </div>

            <div class="flex justify-end gap-3">
                <button wire:click="closeRejectModal" class="px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">Batal</button>
                <button wire:click="reject" class="px-4 py-2 text-sm font-bold text-white bg-rose-500 hover:bg-rose-600 rounded-lg transition-colors">Tolak Supplier</button>
            </div>
        </div>
    </div>
    @endif

    <!-- Suspend Modal -->
    @if($showSuspendModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
        <div class="bg-white dark:bg-darkCard rounded-xl shadow-xl w-full max-w-md p-6 animate-fade-up">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Suspend Supplier</h3>
            <p class="text-sm text-slate-500 mb-4">Supplier yang disuspend tidak akan bisa login dan produk mereka akan disembunyikan.</p>
            
            <div class="mb-4">
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Alasan Suspend (Opsional)</label>
                <textarea wire:model="suspendReason" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-lg p-2 text-sm outline-none focus:ring-2 focus:ring-amber-500" rows="3" placeholder="Contoh: Melanggar ketentuan..."></textarea>
                @error('suspendReason') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
            </div>

            <div class="flex justify-end gap-3">
                <button wire:click="closeSuspendModal" class="px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">Batal</button>
                <button wire:click="suspend" class="px-4 py-2 text-sm font-bold text-white bg-amber-500 hover:bg-amber-600 rounded-lg transition-colors">Suspend Supplier</button>
            </div>
        </div>
    </div>
    @endif

</div>
