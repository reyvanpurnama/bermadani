<div>
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-white">Manajemen Supplier</h1>
            <p class="text-[11px] text-slate-500 mt-0.5">Kelola pendaftaran dan status supplier.</p>
        </div>
        <div class="flex gap-2">
            @php
                $actionableBatches = \App\Models\ConsignmentBatch::whereIn('status', ['REQUESTED', 'PENDING_SETTLEMENT'])->count();
            @endphp
            <a href="{{ route('admin.consignment-batches') }}" class="bg-white dark:bg-darkCard border border-slate-200 dark:border-slate-600 text-slate-700 dark:text-white hover:bg-slate-50 dark:hover:bg-slate-700 px-4 py-2 rounded-lg text-[13px] font-medium shadow-sm transition-colors flex items-center gap-2 relative">
                <i class='bx bx-notepad'></i> Batch Konsinyasi
                @if($actionableBatches > 0)
                    <span class="absolute -top-1 -right-1 w-3 h-3 bg-rose-500 rounded-full animate-pulse"></span>
                @endif
            </a>
            <a href="{{ route('admin.consignment-report') }}" class="bg-white dark:bg-darkCard border border-slate-200 dark:border-slate-600 text-slate-700 dark:text-white hover:bg-slate-50 dark:hover:bg-slate-700 px-4 py-2 rounded-lg text-[13px] font-medium shadow-sm transition-colors flex items-center gap-2">
                <i class='bx bx-file'></i> Laporan
            </a>
            <button wire:click="openCreateModal" class="bg-primary hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-[13px] font-bold shadow-md shadow-indigo-500/20 transition-colors flex items-center gap-2">
                <i class='bx bx-plus text-lg'></i> Tambah Supplier
            </button>
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
                                    <button wire:click="openDetailModal({{ $supplier->id }})" class="text-slate-400 hover:text-primary transition-colors text-lg" title="Detail">
                                        <i class='bx bx-show'></i>
                                    </button>
                                    
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

    <!-- Create Supplier Modal -->
    @if($showCreateModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm" wire:click="closeCreateModal">
        <div @click.stop class="bg-white dark:bg-darkCard rounded-xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto animate-fade-up">
            <!-- Header -->
            <div class="bg-gradient-to-r from-primary to-indigo-600 px-6 py-4 flex justify-between items-center rounded-t-xl">
                <div>
                    <h3 class="text-xl font-bold text-white">Buat Akun Supplier Baru</h3>
                    <p class="text-indigo-100 text-sm">Akun langsung aktif tanpa proses approval</p>
                </div>
                <button wire:click="closeCreateModal" class="text-white hover:bg-white/20 rounded-full p-2 transition-colors">
                    <i class='bx bx-x text-2xl'></i>
                </button>
            </div>

            <!-- Form -->
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    {{-- Nama Pemilik --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Pemilik <span class="text-rose-500">*</span></label>
                        <input wire:model="createOwnerName" type="text" placeholder="Nama lengkap pemilik" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-lg px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary text-slate-700 dark:text-white">
                        @error('createOwnerName') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Nama Bisnis --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Bisnis <span class="text-rose-500">*</span></label>
                        <input wire:model="createBusinessName" type="text" placeholder="Nama usaha / toko" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-lg px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary text-slate-700 dark:text-white">
                        @error('createBusinessName') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Email <span class="text-rose-500">*</span></label>
                        <input wire:model="createEmail" type="email" placeholder="email@supplier.com" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-lg px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary text-slate-700 dark:text-white">
                        @error('createEmail') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Phone --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">No. Telepon <span class="text-rose-500">*</span></label>
                        <input wire:model="createPhone" type="text" placeholder="08xxxxxxxxxx" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-lg px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary text-slate-700 dark:text-white">
                        @error('createPhone') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Kategori Produk --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Kategori Produk</label>
                        <select wire:model="createProductCategory" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-lg px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary text-slate-700 dark:text-white cursor-pointer">
                            <option value="">Pilih Kategori</option>
                            <option value="Makanan">Makanan</option>
                            <option value="Minuman">Minuman</option>
                            <option value="Snack">Snack</option>
                            <option value="ATK">ATK</option>
                            <option value="Kebutuhan Harian">Kebutuhan Harian</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                        @error('createProductCategory') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Max Products --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Limit Produk Aktif</label>
                        <input wire:model="createMaxProducts" type="number" min="1" max="50" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-lg px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary text-slate-700 dark:text-white">
                        @error('createMaxProducts') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Alamat (full width) --}}
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Alamat <span class="text-rose-500">*</span></label>
                        <textarea wire:model="createAddress" rows="2" placeholder="Alamat lengkap supplier" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-lg px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary text-slate-700 dark:text-white"></textarea>
                        @error('createAddress') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Deskripsi (full width) --}}
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Deskripsi (Opsional)</label>
                        <textarea wire:model="createDescription" rows="2" placeholder="Deskripsi singkat tentang supplier" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-lg px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary text-slate-700 dark:text-white"></textarea>
                        @error('createDescription') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>

                {{-- Password Section --}}
                <div class="mt-5 p-4 bg-amber-50 dark:bg-amber-900/10 border border-amber-200 dark:border-amber-800 rounded-lg">
                    <h4 class="text-xs font-bold text-amber-700 dark:text-amber-400 uppercase tracking-widest mb-3 flex items-center gap-2">
                        <i class='bx bx-lock-alt'></i> Kredensial Login
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Email Login</label>
                            <input type="text" disabled :value="$wire.createEmail || 'Otomatis dari email di atas'" class="w-full bg-slate-100 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg px-3 py-2.5 text-sm text-slate-500 dark:text-slate-400 cursor-not-allowed">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Password <span class="text-rose-500">*</span></label>
                            <input wire:model="createPassword" type="text" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-lg px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary text-slate-700 dark:text-white">
                            @error('createPassword') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                            <p class="text-[10px] text-slate-400 mt-1">Default: 12345678. Supplier bisa ubah setelah login.</p>
                        </div>
                    </div>
                </div>

                {{-- Info Banner --}}
                <div class="mt-4 p-3 bg-blue-50 dark:bg-blue-900/10 border border-blue-200 dark:border-blue-800 rounded-lg flex items-start gap-3">
                    <i class='bx bx-info-circle text-blue-500 text-xl mt-0.5'></i>
                    <div class="text-xs text-blue-700 dark:text-blue-400">
                        <p class="font-semibold mb-1">Akun yang dibuat oleh admin:</p>
                        <ul class="list-disc list-inside space-y-0.5">
                            <li>Langsung berstatus <strong>AKTIF</strong> (tanpa approval)</li>
                            <li>Biaya registrasi <strong>Rp 0</strong> (gratis)</li>
                            <li>Supplier bisa langsung login dan upload produk</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Footer Actions -->
            <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 border-t border-slate-200 dark:border-slate-700 flex justify-end gap-3 rounded-b-xl">
                <button wire:click="closeCreateModal" class="px-4 py-2.5 text-sm font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-lg transition-colors">
                    Batal
                </button>
                <button wire:click="createSupplier" wire:loading.attr="disabled" class="px-6 py-2.5 text-sm font-bold text-white bg-primary hover:bg-indigo-700 rounded-lg transition-colors flex items-center gap-2 disabled:opacity-50">
                    <span wire:loading.remove wire:target="createSupplier"><i class='bx bx-check'></i> Buat Akun Supplier</span>
                    <span wire:loading wire:target="createSupplier"><i class='bx bx-loader-alt bx-spin'></i> Memproses...</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    <!-- Detail Modal -->
    @if($showDetailModal && $selectedSupplier)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm" wire:click="closeDetailModal">
        <div @click.stop class="bg-white dark:bg-darkCard rounded-xl shadow-xl w-full max-w-3xl max-h-[90vh] overflow-y-auto animate-fade-up">
            <!-- Header -->
            <div class="sticky top-0 bg-gradient-to-r from-primary to-indigo-600 px-6 py-4 flex justify-between items-center">
                <div>
                    <h3 class="text-xl font-bold text-white">Detail Supplier</h3>
                    <p class="text-indigo-100 text-sm">{{ $selectedSupplier->code }}</p>
                </div>
                <button wire:click="closeDetailModal" class="text-white hover:bg-white/20 rounded-full p-2 transition-colors">
                    <i class='bx bx-x text-2xl'></i>
                </button>
            </div>

            <!-- Content -->
            <div class="p-6">
                <!-- Status Badges -->
                <div class="flex flex-wrap gap-3 mb-6">
                    @php
                        $statusClass = match($selectedSupplier->status) {
                            'ACTIVE' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400',
                            'PENDING' => 'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-400',
                            'SUSPENDED' => 'bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-400',
                            'REJECTED' => 'bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-400',
                            default => 'bg-slate-100 text-slate-700',
                        };
                        $paymentClass = match($selectedSupplier->registrationPaymentStatus) {
                            'VERIFIED' => 'bg-emerald-100 text-emerald-700',
                            'PENDING_VERIFICATION' => 'bg-amber-100 text-amber-700',
                            'REJECTED' => 'bg-rose-100 text-rose-700',
                            default => 'bg-slate-100 text-slate-700',
                        };
                    @endphp
                    <span class="{{ $statusClass }} px-3 py-1 rounded-full text-xs font-bold uppercase">
                        {{ $selectedSupplier->status }}
                    </span>
                    <span class="{{ $paymentClass }} px-3 py-1 rounded-full text-xs font-bold uppercase">
                        <i class='bx bx-money mr-1'></i> {{ $selectedSupplier->registrationPaymentStatus }}
                    </span>
                </div>

                <!-- Info Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Informasi Bisnis</h4>
                        <div class="space-y-3">
                            <div>
                                <p class="text-xs text-slate-500">Nama Bisnis</p>
                                <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $selectedSupplier->businessName }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500">Kategori Produk</p>
                                <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $selectedSupplier->productCategory ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500">Alamat</p>
                                <p class="text-sm text-slate-700 dark:text-slate-300">{{ $selectedSupplier->address }}</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Informasi Pemilik</h4>
                        <div class="space-y-3">
                            <div>
                                <p class="text-xs text-slate-500">Nama Pemilik</p>
                                <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $selectedSupplier->ownerName }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500">Email</p>
                                <p class="text-sm text-slate-700 dark:text-slate-300">{{ $selectedSupplier->email }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500">Telepon</p>
                                <p class="text-sm text-slate-700 dark:text-slate-300">{{ $selectedSupplier->phone }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Proof -->
                @if($selectedSupplier->registrationPaymentProof)
                <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Bukti Pembayaran</h4>
                    <div class="flex items-center gap-4">
                        <img src="{{ asset('storage/' . $selectedSupplier->registrationPaymentProof) }}" 
                             alt="Bukti Pembayaran" 
                             class="w-32 h-32 object-contain border border-slate-200 rounded-lg bg-white cursor-pointer hover:opacity-75 transition-opacity"
                             onclick="window.open(this.src, '_blank')">
                        <div>
                            <p class="text-xs text-slate-500 mb-1">Nominal</p>
                            <p class="text-lg font-bold text-slate-900 dark:text-white">Rp {{ number_format($selectedSupplier->registrationFee, 0, ',', '.') }}</p>
                            @if($selectedSupplier->registrationPaymentVerifiedAt)
                            <p class="text-xs text-slate-500 mt-2">Diverifikasi: {{ $selectedSupplier->registrationPaymentVerifiedAt->format('d M Y, H:i') }}</p>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                <!-- Products Summary -->
                <div class="p-4 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-lg">
                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Produk</h4>
                    <p class="text-2xl font-bold text-slate-900 dark:text-white">
                        {{ $selectedSupplier->currentActiveProducts }} / {{ $selectedSupplier->maxActiveProducts }}
                    </p>
                    <p class="text-xs text-slate-500 mt-1">Produk aktif dari limit maksimal</p>
                </div>

                @if($selectedSupplier->description)
                <div class="mt-6">
                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Deskripsi</h4>
                    <p class="text-sm text-slate-700 dark:text-slate-300">{{ $selectedSupplier->description }}</p>
                </div>
                @endif
            </div>

            <!-- Footer Actions -->
            <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 border-t border-slate-200 dark:border-slate-700 flex justify-end gap-3">
                <button wire:click="closeDetailModal" class="px-4 py-2 text-sm font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-lg transition-colors">
                    Tutup
                </button>
                <a href="{{ route('admin.suppliers.detail', $selectedSupplier->id) }}" class="px-4 py-2 text-sm font-bold text-white bg-primary hover:bg-indigo-700 rounded-lg transition-colors flex items-center gap-2">
                    <i class='bx bx-edit-alt'></i> Lihat Lengkap & Edit
                </a>
            </div>
        </div>
    </div>
    @endif

</div>
