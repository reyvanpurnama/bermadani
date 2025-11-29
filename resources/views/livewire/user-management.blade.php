<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-800 dark:text-white">Manajemen Pengguna</h1>
            <p class="text-sm text-slate-500">Kelola akses pengguna sistem</p>
        </div>
        @if($canManage)
        <button wire:click="openCreateModal" class="bg-primary hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium text-sm flex items-center gap-2 transition-colors shadow-lg shadow-indigo-500/30 w-fit">
            <i class='bx bx-plus text-lg'></i> Tambah User
        </button>
        @endif
    </div>

    {{-- Filters --}}
    <div class="bg-card dark:bg-darkCard rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 p-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            {{-- Search --}}
            <div class="md:col-span-2">
                <div class="relative">
                    <i class='bx bx-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400'></i>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama atau email..." 
                        class="w-full pl-10 pr-4 py-2 rounded-lg border border-slate-200 dark:border-slate-600 dark:bg-slate-800 focus:ring-primary focus:border-primary text-sm">
                </div>
            </div>

            {{-- Filter Role --}}
            <div>
                <select wire:model.live="filterRole" class="w-full px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-600 dark:bg-slate-800 focus:ring-primary focus:border-primary text-sm">
                    <option value="">Semua Role</option>
                    <option value="SUPER_ADMIN">Super Admin</option>
                    <option value="ADMIN">Admin</option>
                    <option value="KASIR">Kasir</option>
                </select>
            </div>

            {{-- Filter Status --}}
            <div>
                <select wire:model.live="filterStatus" class="w-full px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-600 dark:bg-slate-800 focus:ring-primary focus:border-primary text-sm">
                    <option value="">Semua Status</option>
                    <option value="1">Aktif</option>
                    <option value="0">Nonaktif</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Users Table --}}
    <div class="bg-card dark:bg-darkCard rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 dark:bg-slate-800 border-b border-slate-100 dark:border-slate-700">
                    <tr>
                        <th class="text-left px-4 py-3 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">User</th>
                        <th class="text-left px-4 py-3 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Role</th>
                        <th class="text-left px-4 py-3 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
                        <th class="text-left px-4 py-3 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Login Terakhir</th>
                        @if($canManage)
                        <th class="text-center px-4 py-3 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse($users as $user)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-primary to-indigo-600 flex items-center justify-center text-white font-bold text-sm">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>
                                <div>
                                    <h6 class="font-semibold text-slate-800 dark:text-white text-sm">{{ $user->name }}</h6>
                                    <p class="text-xs text-slate-500">{{ $user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            @php
                                $roleColors = [
                                    'SUPER_ADMIN' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400',
                                    'DEVELOPER' => 'bg-cyan-100 text-cyan-700 dark:bg-cyan-900/30 dark:text-cyan-400',
                                    'ADMIN' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                                    'KASIR' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
                                    'SUPPLIER' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
                                ];
                                $roleLabels = [
                                    'SUPER_ADMIN' => 'Super Admin',
                                    'DEVELOPER' => 'Developer',
                                    'ADMIN' => 'Admin',
                                    'KASIR' => 'Kasir',
                                    'SUPPLIER' => 'Supplier',
                                ];
                            @endphp
                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $roleColors[$user->role] ?? 'bg-slate-100 text-slate-700' }}">
                                {{ $roleLabels[$user->role] ?? $user->role }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            @if($canManage && !in_array($user->role, ['SUPER_ADMIN', 'DEVELOPER']) && $user->id !== auth()->id())
                            <button wire:click="toggleStatus({{ $user->id }})" 
                                class="px-2 py-1 rounded-full text-xs font-medium transition-colors {{ $user->isActive ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 hover:bg-emerald-200' : 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400 hover:bg-rose-200' }}">
                                {{ $user->isActive ? 'Aktif' : 'Nonaktif' }}
                            </button>
                            @else
                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $user->isActive ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400' }}">
                                {{ $user->isActive ? 'Aktif' : 'Nonaktif' }}
                            </span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-sm text-slate-600 dark:text-slate-400">
                                @if($user->lastLoginAt)
                                    {{ $user->lastLoginAt->diffForHumans() }}
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </span>
                        </td>
                        @if($canManage)
                        <td class="px-4 py-3 text-center">
                            @if(!in_array($user->role, ['SUPER_ADMIN', 'DEVELOPER']) && $user->id !== auth()->id())
                            <div class="flex items-center justify-center gap-1">
                                <button wire:click="openEditModal({{ $user->id }})" class="p-2 text-slate-500 hover:text-primary hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg transition-colors" title="Edit">
                                    <i class='bx bx-edit text-lg'></i>
                                </button>
                                <button wire:click="confirmDelete({{ $user->id }})" class="p-2 text-slate-500 hover:text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-900/20 rounded-lg transition-colors" title="Hapus">
                                    <i class='bx bx-trash text-lg'></i>
                                </button>
                            </div>
                            @else
                            <span class="text-xs text-slate-400">-</span>
                            @endif
                        </td>
                        @endif
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ $canManage ? 5 : 4 }}" class="px-4 py-12 text-center text-slate-400">
                            <i class='bx bx-user-x text-4xl'></i>
                            <p class="mt-2">Tidak ada user ditemukan</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($users->hasPages())
        <div class="px-4 py-3 border-t border-slate-100 dark:border-slate-700">
            {{ $users->links() }}
        </div>
        @endif
    </div>

    {{-- Create/Edit Modal --}}
    @if($showModal)
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-darkCard rounded-2xl shadow-2xl w-full max-w-md overflow-hidden" wire:click.away="$set('showModal', false)">
            <div class="bg-gradient-to-r from-primary to-indigo-600 p-5 text-white">
                <h2 class="text-lg font-bold">{{ $editMode ? 'Edit User' : 'Tambah User Baru' }}</h2>
                <p class="text-white/80 text-sm">{{ $editMode ? 'Perbarui data user' : 'Isi data untuk membuat user baru' }}</p>
            </div>
            <form wire:submit="save" class="p-5 space-y-4">
                {{-- Name --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nama Lengkap</label>
                    <input type="text" wire:model="name" 
                        class="w-full px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-600 dark:bg-slate-800 focus:ring-primary focus:border-primary text-sm @error('name') border-rose-500 @enderror"
                        placeholder="Masukkan nama lengkap">
                    @error('name') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Email</label>
                    <input type="email" wire:model="email" 
                        class="w-full px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-600 dark:bg-slate-800 focus:ring-primary focus:border-primary text-sm @error('email') border-rose-500 @enderror"
                        placeholder="email@example.com">
                    @error('email') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Password --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                        Password
                        @if($editMode)
                        <span class="text-slate-400 font-normal">(Kosongkan jika tidak diubah)</span>
                        @endif
                    </label>
                    <input type="password" wire:model="password" 
                        class="w-full px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-600 dark:bg-slate-800 focus:ring-primary focus:border-primary text-sm @error('password') border-rose-500 @enderror"
                        placeholder="{{ $editMode ? '••••••••' : 'Minimal 6 karakter' }}">
                    @error('password') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Role --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Role</label>
                    <select wire:model="role" 
                        class="w-full px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-600 dark:bg-slate-800 focus:ring-primary focus:border-primary text-sm @error('role') border-rose-500 @enderror">
                        <option value="KASIR">Kasir</option>
                        <option value="ADMIN">Admin</option>
                    </select>
                    @error('role') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Status --}}
                <div class="flex items-center gap-3">
                    <input type="checkbox" wire:model="isActive" id="isActive" 
                        class="w-4 h-4 rounded border-slate-300 text-primary focus:ring-primary">
                    <label for="isActive" class="text-sm text-slate-700 dark:text-slate-300">User Aktif</label>
                </div>

                {{-- Actions --}}
                <div class="flex gap-3 pt-2">
                    <button type="button" wire:click="$set('showModal', false)" 
                        class="flex-1 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 font-medium py-2 px-4 rounded-lg transition-colors">
                        Batal
                    </button>
                    <button type="submit" 
                        class="flex-1 bg-primary hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-lg transition-colors flex items-center justify-center gap-2">
                        <i class='bx {{ $editMode ? "bx-save" : "bx-plus" }}'></i>
                        {{ $editMode ? 'Simpan' : 'Tambah' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Delete Confirmation Modal --}}
    @if($showDeleteModal && $userToDelete)
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-darkCard rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden">
            <div class="p-6 text-center">
                <div class="w-16 h-16 bg-rose-100 dark:bg-rose-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class='bx bx-trash text-3xl text-rose-600 dark:text-rose-400'></i>
                </div>
                <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-2">Hapus User?</h3>
                <p class="text-sm text-slate-500 mb-6">
                    Anda yakin ingin menghapus user <strong>{{ $userToDelete->name }}</strong>? Tindakan ini tidak dapat dibatalkan.
                </p>
                <div class="flex gap-3">
                    <button wire:click="$set('showDeleteModal', false)" 
                        class="flex-1 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 font-medium py-2 px-4 rounded-lg transition-colors">
                        Batal
                    </button>
                    <button wire:click="delete" 
                        class="flex-1 bg-rose-500 hover:bg-rose-600 text-white font-medium py-2 px-4 rounded-lg transition-colors flex items-center justify-center gap-2">
                        <i class='bx bx-trash'></i> Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
