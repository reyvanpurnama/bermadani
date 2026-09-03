<div class="p-6 space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Aset Tetap & Penyusutan</h1>
            <p class="text-sm text-slate-500 dark:text-zinc-400">Pencatatan Inventaris Aset Koperasi & Perhitungan Depresiasi Garis Lurus</p>
        </div>
        <button wire:click="openModal" class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-4 py-2 rounded-xl shadow-sm transition-all">
            <i class='bx bx-plus text-lg'></i> Tambah Aset Tetap
        </button>
    </div>

    @if (session()->has('message'))
        <div class="p-4 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 rounded-xl flex items-center gap-2">
            <i class='bx bx-check-circle text-xl'></i>
            <span>{{ session('message') }}</span>
        </div>
    @endif

    <!-- Summary KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-zinc-800 rounded-2xl p-5 border border-slate-200 dark:border-zinc-700 shadow-sm border-l-4 border-l-indigo-500">
            <div class="text-xs text-slate-500 dark:text-zinc-400 uppercase font-semibold">Total Harga Perolehan</div>
            <div class="text-2xl font-bold text-slate-800 dark:text-white mt-1 font-mono">Rp {{ number_format($totalCost, 0, ',', '.') }}</div>
            <div class="text-xs text-slate-400 mt-2">Nilai perolehan awal aset aktif</div>
        </div>
        <div class="bg-white dark:bg-zinc-800 rounded-2xl p-5 border border-slate-200 dark:border-zinc-700 shadow-sm border-l-4 border-l-amber-500">
            <div class="text-xs text-slate-500 dark:text-zinc-400 uppercase font-semibold">Akumulasi Penyusutan</div>
            <div class="text-2xl font-bold text-amber-600 dark:text-amber-400 mt-1 font-mono">Rp {{ number_format($totalDepreciated, 0, ',', '.') }}</div>
            <div class="text-xs text-slate-400 mt-2">Total depresiasi hingga saat ini</div>
        </div>
        <div class="bg-white dark:bg-zinc-800 rounded-2xl p-5 border border-slate-200 dark:border-zinc-700 shadow-sm border-l-4 border-l-emerald-500">
            <div class="text-xs text-slate-500 dark:text-zinc-400 uppercase font-semibold">Nilai Buku Neto (Net Book Value)</div>
            <div class="text-2xl font-bold text-emerald-600 dark:text-emerald-400 mt-1 font-mono">Rp {{ number_format($totalNetBook, 0, ',', '.') }}</div>
            <div class="text-xs text-slate-400 mt-2">Masuk ke Neraca Aset Tidak Lancar</div>
        </div>
    </div>

    <!-- Table Asset List -->
    <div class="bg-white dark:bg-zinc-800 rounded-2xl shadow-sm border border-slate-200 dark:border-zinc-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left whitespace-nowrap">
                <thead class="text-xs text-slate-500 bg-slate-50 dark:bg-zinc-900/50 dark:text-slate-400 uppercase border-b border-slate-200 dark:border-zinc-700">
                    <tr>
                        <th class="px-5 py-4">Nama Aset</th>
                        <th class="px-4 py-4">Kategori</th>
                        <th class="px-4 py-4">Tgl Perolehan</th>
                        <th class="px-4 py-4 text-right">Harga Perolehan</th>
                        <th class="px-4 py-4 text-center">Masa Manfaat</th>
                        <th class="px-4 py-4 text-right">Akum. Penyusutan</th>
                        <th class="px-4 py-4 text-right">Nilai Buku</th>
                        <th class="px-5 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-zinc-700">
                    @forelse($assets as $asset)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-zinc-700/50 transition-colors">
                            <td class="px-5 py-4 font-semibold text-slate-800 dark:text-white">
                                {{ $asset->name }}
                                @if($asset->notes)
                                    <div class="text-xs font-normal text-slate-400">{{ $asset->notes }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-4">
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400">
                                    {{ $asset->category }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-slate-600 dark:text-zinc-300">
                                {{ $asset->acquisition_date ? $asset->acquisition_date->format('d/m/Y') : '-' }}
                            </td>
                            <td class="px-4 py-4 text-right font-mono text-slate-800 dark:text-white font-medium">
                                Rp {{ number_format($asset->acquisition_cost, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-4 text-center text-slate-600 dark:text-zinc-300">
                                {{ $asset->useful_life_months }} Bln ({{ round($asset->useful_life_months / 12, 1) }} Thn)
                            </td>
                            <td class="px-4 py-4 text-right font-mono text-amber-600 dark:text-amber-400">
                                Rp {{ number_format($asset->accumulated_depreciation, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-4 text-right font-mono text-emerald-600 dark:text-emerald-400 font-semibold">
                                Rp {{ number_format($asset->net_book_value, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-4 text-center">
                                <div class="flex justify-center items-center gap-2">
                                    <button wire:click="edit({{ $asset->id }})" class="p-1.5 rounded-lg text-slate-500 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-zinc-700">
                                        <i class='bx bx-edit text-lg'></i>
                                    </button>
                                    <button onclick="confirm('Yakin ingin menghapus aset ini?') || event.stopImmediatePropagation()" wire:click="delete({{ $asset->id }})" class="p-1.5 rounded-lg text-slate-500 hover:text-rose-600 hover:bg-slate-100 dark:hover:bg-zinc-700">
                                        <i class='bx bx-trash text-lg'></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-slate-400">
                                <i class='bx bx-building text-4xl mb-2'></i>
                                <div>Belum ada aset tetap yang dicatat. Klik "Tambah Aset Tetap" di atas.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($assets->hasPages())
            <div class="p-4 border-t border-slate-100 dark:border-zinc-700">
                {{ $assets->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Create / Edit Asset -->
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
            <div class="bg-white dark:bg-zinc-800 rounded-2xl shadow-xl border border-slate-200 dark:border-zinc-700 max-w-lg w-full overflow-hidden">
                <div class="p-5 border-b border-slate-100 dark:border-zinc-700 flex justify-between items-center bg-slate-50 dark:bg-zinc-900/50">
                    <h3 class="font-bold text-slate-800 dark:text-white text-base">
                        {{ $assetId ? 'Edit Aset Tetap' : 'Tambah Aset Tetap Baru' }}
                    </h3>
                    <button wire:click="closeModal" class="text-slate-400 hover:text-slate-600 dark:hover:text-white">
                        <i class='bx bx-x text-2xl'></i>
                    </button>
                </div>
                
                <form wire:submit.prevent="save" class="p-5 space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-zinc-300 uppercase mb-1">Nama Aset</label>
                        <input type="text" wire:model="name" placeholder="e.g. Laptop Asus Core i5 Kantor" class="w-full bg-slate-50 dark:bg-zinc-900 border border-slate-200 dark:border-zinc-700 rounded-xl p-2.5 text-sm text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none">
                        @error('name') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 dark:text-zinc-300 uppercase mb-1">Kategori</label>
                            <select wire:model="category" class="w-full bg-slate-50 dark:bg-zinc-900 border border-slate-200 dark:border-zinc-700 rounded-xl p-2.5 text-sm text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none">
                                <option value="PERALATAN">Peralatan Kantor</option>
                                <option value="KENDARAAN">Kendaraan</option>
                                <option value="BANGUNAN">Bangunan</option>
                                <option value="LAINNYA">Lainnya</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 dark:text-zinc-300 uppercase mb-1">Tgl Perolehan</label>
                            <input type="date" wire:model="acquisition_date" class="w-full bg-slate-50 dark:bg-zinc-900 border border-slate-200 dark:border-zinc-700 rounded-xl p-2.5 text-sm text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none">
                            @error('acquisition_date') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 dark:text-zinc-300 uppercase mb-1">Harga Perolehan (Rp)</label>
                            <input type="number" wire:model="acquisition_cost" class="w-full bg-slate-50 dark:bg-zinc-900 border border-slate-200 dark:border-zinc-700 rounded-xl p-2.5 text-sm font-mono text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none">
                            @error('acquisition_cost') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 dark:text-zinc-300 uppercase mb-1">Masa Manfaat (Bulan)</label>
                            <input type="number" wire:model="useful_life_months" placeholder="60 (5 thn)" class="w-full bg-slate-50 dark:bg-zinc-900 border border-slate-200 dark:border-zinc-700 rounded-xl p-2.5 text-sm text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none">
                            @error('useful_life_months') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-zinc-300 uppercase mb-1">Nilai Residu / Salvage Value (Rp)</label>
                        <input type="number" wire:model="salvage_value" placeholder="0" class="w-full bg-slate-50 dark:bg-zinc-900 border border-slate-200 dark:border-zinc-700 rounded-xl p-2.5 text-sm font-mono text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-zinc-300 uppercase mb-1">Catatan / Spesifikasi</label>
                        <textarea wire:model="notes" rows="2" placeholder="Nomor seri, kondisi, lokasi perolehan..." class="w-full bg-slate-50 dark:bg-zinc-900 border border-slate-200 dark:border-zinc-700 rounded-xl p-2.5 text-sm text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none"></textarea>
                    </div>

                    <div class="flex justify-end gap-3 pt-3">
                        <button type="button" wire:click="closeModal" class="px-4 py-2 rounded-xl text-sm font-medium border border-slate-200 dark:border-zinc-700 text-slate-600 dark:text-zinc-400 hover:bg-slate-50 dark:hover:bg-zinc-700">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl text-sm font-semibold bg-indigo-600 hover:bg-indigo-700 text-white shadow-sm">
                            Simpan Aset
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
