<div>
    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-white">Manajemen Anggota</h1>
            <p class="text-[11px] text-slate-500 mt-0.5">Kelola data keanggotaan dan kewajiban simpanan bulanan.</p>
        </div>
        <div class="flex gap-2" x-show="activeTab === 'members'" x-data="{ activeTab: @entangle('activeTab') }">
            <button wire:click="openImportModal"
                class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-[12px] font-bold shadow-md shadow-emerald-500/20 transition-colors flex items-center gap-2">
                <i class='bx bx-import text-lg'></i> Import Excel
            </button>
            <button wire:click="downloadSignaturePdf"
                class="bg-white dark:bg-darkCard border border-slate-200 dark:border-slate-600 text-slate-700 dark:text-white hover:bg-slate-50 dark:hover:bg-slate-700 px-4 py-2 rounded-lg text-[12px] font-bold shadow-sm transition-colors flex items-center gap-2">
                <i class='bx bxs-file-pdf'></i> Export PDF TTD
            </button>
            <a href="{{ route('admin.members.create') }}"
                class="bg-primary hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-[12px] font-bold shadow-md shadow-indigo-500/20 transition-colors flex items-center gap-2">
                <i class='bx bx-user-plus text-lg'></i> Anggota Baru
            </a>
        </div>
    </div>

    {{-- Tabs Navigation --}}
    <div class="flex gap-2 mb-6 border-b border-slate-200 dark:border-slate-700">
        <button wire:click="switchTab('members')"
            class="px-4 py-2 text-sm font-semibold transition-colors relative
                       {{ $activeTab === 'members' ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
            <div class="flex items-center gap-2">
                <i class='bx bx-group text-lg'></i>
                Daftar Anggota
            </div>
            @if($activeTab === 'members')
                <div class="absolute bottom-0 left-0 right-0 h-0.5 bg-indigo-600 dark:bg-indigo-400"></div>
            @endif
        </button>
        <button wire:click="switchTab('auto-debit')"
            class="px-4 py-2 text-sm font-semibold transition-colors relative
                       {{ $activeTab === 'auto-debit' ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
            <div class="flex items-center gap-2">
                <i class='bx bx-calendar-check text-lg'></i>
                Kewajiban Bulanan
            </div>
            @if($activeTab === 'auto-debit')
                <div class="absolute bottom-0 left-0 right-0 h-0.5 bg-indigo-600 dark:bg-indigo-400"></div>
            @endif
        </button>
    </div>

    {{-- Members List Tab --}}
    @if($activeTab === 'members')



        {{-- Stats Cards Group --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            {{-- 1. Total Anggota --}}
            <div
                class="bg-white dark:bg-darkCard p-4 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-lg bg-indigo-50 dark:bg-indigo-500/10 flex items-center justify-center text-primary text-xl">
                    <i class='bx bx-group'></i>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Total Anggota</p>
                    <h4 class="text-lg font-bold text-slate-800 dark:text-white">{{ number_format($stats['total']) }}</h4>
                </div>
            </div>

            {{-- 2. Anggota Aktif --}}
            <div
                class="bg-white dark:bg-darkCard p-4 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-lg bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center text-emerald-600 text-xl">
                    <i class='bx bx-user-check'></i>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Anggota Aktif</p>
                    <h4 class="text-lg font-bold text-slate-800 dark:text-white">{{ number_format($stats['active']) }}</h4>
                </div>
            </div>

            {{-- 3 & 4. Loyalty & Asset Overview (Merged Card) --}}
            <div
                class="md:col-span-2 bg-gradient-to-br from-slate-800 to-slate-900 dark:from-slate-800 dark:to-black rounded-xl shadow-sm border border-slate-700 p-4 text-white relative overflow-hidden group">
                <!-- Background Decoration -->
                <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                    <i class='bx bx-diamond text-6xl'></i>
                </div>

                <div class="relative z-10">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3 flex items-center gap-2">
                        <i class='bx bx-wallet text-amber-400'></i> Aset & Poin Loyalty
                    </p>
                    <div class="grid grid-cols-2 gap-4 divide-x divide-slate-700">
                        <div>
                            <p class="text-[9px] text-slate-400 mb-0.5">Total Dana Simpanan</p>
                            <h3 class="text-lg font-bold text-white">Rp
                                {{ number_format($stats['totalSimpanan'] ?? 0, 0, ',', '.') }}
                            </h3>
                        </div>
                        <div class="pl-4">
                            <p class="text-[9px] text-slate-400 mb-0.5">Rata-rata Poin</p>
                            <h3 class="text-lg font-bold text-amber-400">{{ number_format($stats['avgPoints'] ?? 0) }} Pts
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Detailed Simpanan Breakdown (Optional / Secondary) --}}
        <div class="grid grid-cols-3 gap-4 mb-6">
            <div
                class="bg-slate-50 dark:bg-slate-800/50 p-3 rounded-lg border border-slate-100 dark:border-slate-700 flex flex-col justify-center text-center">
                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1">Simpanan Pokok</p>
                <h4 class="text-sm font-bold text-slate-700 dark:text-gray-300">Rp
                    {{ number_format($stats['simpananPokok'] ?? 0, 0, ',', '.') }}
                </h4>
            </div>
            <div
                class="bg-slate-50 dark:bg-slate-800/50 p-3 rounded-lg border border-slate-100 dark:border-slate-700 flex flex-col justify-center text-center">
                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1">Simpanan Wajib</p>
                <h4 class="text-sm font-bold text-slate-700 dark:text-gray-300">Rp
                    {{ number_format($stats['simpananWajib'] ?? 0, 0, ',', '.') }}
                </h4>
            </div>
            <div
                class="bg-slate-50 dark:bg-slate-800/50 p-3 rounded-lg border border-slate-100 dark:border-slate-700 flex flex-col justify-center text-center">
                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1">Simpanan Sukarela</p>
                <h4 class="text-sm font-bold text-slate-700 dark:text-gray-300">Rp
                    {{ number_format($stats['simpananSukarela'] ?? 0, 0, ',', '.') }}
                </h4>
            </div>
        </div>

        {{-- Flash Messages --}}
        @if (session()->has('success'))
            <div
                class="bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30 text-emerald-700 dark:text-emerald-400 px-4 py-3 rounded-lg mb-6 flex items-center gap-2">
                <i class='bx bxs-check-circle text-xl'></i>
                {{ session('success') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div
                class="bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/30 text-rose-700 dark:text-rose-400 px-4 py-3 rounded-lg mb-6 flex items-center gap-2">
                <i class='bx bxs-error-circle text-xl'></i>
                {{ session('error') }}
            </div>
        @endif

        {{-- Filters --}}
        <div
            class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 bg-white dark:bg-darkCard p-4 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 mb-6">
            <div class="relative">
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 block">Cari
                    Anggota</label>
                <div class="relative">
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Nama, No Anggota, Email..."
                        class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 text-[13px] rounded-md pl-9 pr-3 py-2 outline-none focus:ring-1 focus:ring-primary text-slate-700 dark:text-white">
                    <i class='bx bx-search absolute left-3 top-2.5 text-slate-400 text-lg'></i>
                </div>
            </div>
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
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 block">Tanggal
                    Bergabung</label>
                <input type="date" wire:model.live="filterJoinDate"
                    class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 text-[13px] rounded-md px-3 py-2 outline-none focus:ring-1 focus:ring-primary text-slate-700 dark:text-white">
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
                            <th class="px-5 py-3">Anggota</th>
                            <th class="px-5 py-3">Unit / Prodi</th>
                            <th class="px-5 py-3">Status & Tier</th>
                            <th class="px-5 py-3 min-w-[200px]">Portfolio Simpanan</th>
                            <th class="px-5 py-3 text-center">Poin</th>
                            <th class="px-5 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700 text-[13px]">
                        @forelse ($members as $member)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors group">
                                <td class="px-5 py-3.5 align-top">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-9 h-9 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center text-white font-bold">
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
                                    <span
                                        class="block font-medium text-slate-700 dark:text-slate-300">{{ $member->unitKerja === 'unknown' ? 'Belum Diisi' : $member->unitKerja }}</span>
                                    <span class="text-[10px] text-slate-400">{{ $member->email }}</span>
                                </td>
                                <td class="px-5 py-3.5 align-top">
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
                                <td class="px-5 py-3.5 align-top">
                                    <div class="flex flex-col gap-1.5 w-full max-w-[240px]">
                                        <div class="flex justify-between items-center text-[11px]">
                                            <span class="text-slate-500 dark:text-slate-400">Pokok</span>
                                            <span class="font-medium text-slate-700 dark:text-slate-300">Rp
                                                {{ number_format($member->simpananPokok, 0, ',', '.') }}</span>
                                        </div>
                                        <div class="flex justify-between items-center text-[11px]">
                                            <span class="text-slate-500 dark:text-slate-400">Wajib</span>
                                            <span class="font-medium text-slate-700 dark:text-slate-300">Rp
                                                {{ number_format($member->simpananWajib, 0, ',', '.') }}</span>
                                        </div>
                                        <div class="flex justify-between items-center text-[11px]">
                                            <span class="text-slate-500 dark:text-slate-400">Sukarela</span>
                                            <span class="font-medium text-slate-700 dark:text-slate-300">Rp
                                                {{ number_format($member->simpananSukarela, 0, ',', '.') }}</span>
                                        </div>
                                        <div
                                            class="border-t border-slate-100 dark:border-slate-700 pt-1.5 mt-0.5 flex justify-between items-center">
                                            <span class="text-[11px] font-bold text-slate-800 dark:text-white">Total
                                                Simpanan</span>
                                            <span class="text-[12px] font-bold text-primary dark:text-blue-400">Rp
                                                {{ number_format($member->totalSimpanan, 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 text-center align-top font-bold text-amber-500">
                                    {{ number_format($member->points) }}
                                </td>
                                <td class="px-5 py-3.5 text-center align-top">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('admin.members.show', $member->id) }}"
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
                                                class="text-slate-400 hover:text-emerald-500 transition-colors p-1"
                                                title="Aktifkan">
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
    @endif {{-- End Members Tab --}}

    {{-- Auto-Debit Tab --}}
    @if($activeTab === 'auto-debit')
        @livewire('admin.monthly-debit-approval')
    @endif

    {{-- Import Modal --}}
    @if($showImportModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-slate-900 bg-opacity-75 transition-opacity" wire:click="closeImportModal">
                </div>

                <div
                    class="inline-block align-bottom bg-white dark:bg-darkCard rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white dark:bg-darkCard px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="flex items-center gap-3 mb-4">
                            <div
                                class="flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-emerald-100 dark:bg-emerald-500/10">
                                <i class='bx bx-import text-2xl text-emerald-600'></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Import Data Anggota</h3>
                                <p class="text-[11px] text-slate-500">Upload file Excel (.xlsx atau .xls)</p>
                            </div>
                        </div>

                        <div
                            class="border-2 border-dashed border-slate-200 dark:border-slate-600 rounded-lg p-6 text-center">
                            <input type="file" wire:model="importFile" accept=".xlsx,.xls" id="importFile" class="hidden">
                            <label for="importFile" class="cursor-pointer">
                                <div class="flex flex-col items-center gap-2">
                                    <i class='bx bx-cloud-upload text-4xl text-slate-400'></i>
                                    @if($importFile)
                                        <p class="text-sm font-semibold text-slate-700 dark:text-white">
                                            {{ $importFile->getClientOriginalName() }}
                                        </p>
                                        <p class="text-xs text-slate-500">{{ number_format($importFile->getSize() / 1024, 2) }}
                                            KB</p>
                                    @else
                                        <p class="text-sm font-semibold text-slate-700 dark:text-white">Klik untuk pilih file
                                        </p>
                                        <p class="text-xs text-slate-500">atau drag & drop file Excel di sini</p>
                                    @endif
                                </div>
                            </label>
                            @error('importFile')
                                <p class="text-xs text-rose-500 mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <div
                            class="mt-4 bg-blue-50 dark:bg-blue-500/10 border border-blue-200 dark:border-blue-500/30 rounded-lg p-3">
                            <p class="text-[11px] text-blue-700 dark:text-blue-400 font-semibold mb-2">Format Excel yang
                                diharapkan:</p>
                            <ul class="text-[10px] text-blue-600 dark:text-blue-300 list-disc list-inside space-y-1">
                                <li>Kolom 1: NO (nomor urut)</li>
                                <li>Kolom 2: NAMA ANGGOTA</li>
                                <li>Kolom 3: PENDAFTARAN ANGGOTA (tanggal)</li>
                                <li>Kolom 4: SIMPANAN POKOK</li>
                                <li>Kolom 5: TOTAL SIMPANAN WAJIB</li>
                            </ul>
                        </div>

                        @if($importSummary)
                            <div class="mt-4 bg-slate-50 dark:bg-slate-700 rounded-lg p-4">
                                <p class="text-sm font-bold text-slate-700 dark:text-white mb-2">Hasil Import:</p>
                                <div class="space-y-1 text-xs">
                                    <p class="text-emerald-600 dark:text-emerald-400">✓ {{ $importSummary['success'] }} anggota
                                        berhasil ditambahkan</p>
                                    <p class="text-amber-600 dark:text-amber-400">⊘ {{ $importSummary['skipped'] }} anggota
                                        dilewati (duplikat)</p>
                                    <p class="text-rose-600 dark:text-rose-400">✗ {{ $importSummary['errors'] }} error</p>
                                </div>
                                @if(count($importSummary['error_details']) > 0)
                                    <details class="mt-2">
                                        <summary
                                            class="text-xs text-slate-600 dark:text-slate-400 cursor-pointer hover:text-slate-800 dark:hover:text-slate-200">
                                            Detail Error</summary>
                                        <div
                                            class="mt-2 max-h-40 overflow-y-auto bg-white dark:bg-slate-800 rounded p-2 text-[10px] text-slate-600 dark:text-slate-400">
                                            @foreach($importSummary['error_details'] as $error)
                                                <p>{{ $error }}</p>
                                            @endforeach
                                        </div>
                                    </details>
                                @endif
                            </div>
                        @endif
                    </div>

                    <div class="bg-slate-50 dark:bg-slate-800 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                        <button wire:click="importMembers" type="button"
                            class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-emerald-600 text-sm font-bold text-white hover:bg-emerald-700 focus:outline-none sm:ml-3 sm:w-auto disabled:opacity-50"
                            {{ !$importFile ? 'disabled' : '' }} wire:loading.attr="disabled" wire:target="importMembers">
                            <span wire:loading.remove wire:target="importMembers">Import Sekarang</span>
                            <span wire:loading wire:target="importMembers">Importing...</span>
                        </button>
                        <button wire:click="closeImportModal" type="button"
                            class="mt-3 w-full inline-flex justify-center rounded-lg border border-slate-300 dark:border-slate-600 shadow-sm px-4 py-2 bg-white dark:bg-darkCard text-sm font-bold text-slate-700 dark:text-white hover:bg-slate-50 dark:hover:bg-slate-700 focus:outline-none sm:mt-0 sm:w-auto">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>