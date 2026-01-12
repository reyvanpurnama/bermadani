<div>
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-white">Log Jam Kerja</h1>
            <p class="text-[11px] text-slate-500 mt-0.5">Catat dan kelola jam kerja Anda sebagai developer.</p>
        </div>
        <button wire:click="openForm"
            class="bg-primary hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-[12px] font-bold shadow-md shadow-indigo-500/20 transition-colors flex items-center gap-2">
            <i class='bx bx-plus text-lg'></i> Tambah Log
        </button>
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

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        <div class="bg-white dark:bg-darkCard p-4 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700">
            <div class="flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-lg bg-indigo-50 dark:bg-indigo-500/10 flex items-center justify-center text-primary text-xl">
                    <i class='bx bx-time-five'></i>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Total Jam</p>
                    <h4 class="text-lg font-bold text-slate-800 dark:text-white">
                        {{ number_format($stats['totalHours'], 1) }}
                    </h4>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-darkCard p-4 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700">
            <div class="flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-lg bg-slate-50 dark:bg-slate-700/50 flex items-center justify-center text-slate-500 text-xl">
                    <i class='bx bx-wallet'></i>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Total</p>
                    <h4 class="text-lg font-bold text-slate-800 dark:text-white">Rp
                        {{ number_format($stats['totalAmount'], 0, ',', '.') }}
                    </h4>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-darkCard p-4 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700">
            <div class="flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-lg bg-amber-50 dark:bg-amber-500/10 flex items-center justify-center text-amber-500 text-xl">
                    <i class='bx bx-hourglass'></i>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Pending</p>
                    <h4 class="text-lg font-bold text-amber-600 dark:text-amber-400">Rp
                        {{ number_format($stats['pending'], 0, ',', '.') }}
                    </h4>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-darkCard p-4 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700">
            <div class="flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-lg bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center text-emerald-600 text-xl">
                    <i class='bx bx-check-circle'></i>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Approved</p>
                    <h4 class="text-lg font-bold text-emerald-600 dark:text-emerald-400">Rp
                        {{ number_format($stats['approved'], 0, ',', '.') }}
                    </h4>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-darkCard p-4 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700">
            <div class="flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-lg bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center text-blue-600 text-xl">
                    <i class='bx bx-money'></i>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Dibayar</p>
                    <h4 class="text-lg font-bold text-blue-600 dark:text-blue-400">Rp
                        {{ number_format($stats['paid'], 0, ',', '.') }}
                    </h4>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div
        class="flex flex-wrap gap-4 mb-6 bg-white dark:bg-darkCard p-4 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700">
        <div>
            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 block">Bulan</label>
            <select wire:model.live="filterMonth"
                class="bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 text-[13px] rounded-md px-3 py-2 outline-none focus:ring-1 focus:ring-primary text-slate-700 dark:text-white cursor-pointer">
                @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}">{{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}</option>
                @endfor
            </select>
        </div>
        <div>
            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 block">Tahun</label>
            <select wire:model.live="filterYear"
                class="bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 text-[13px] rounded-md px-3 py-2 outline-none focus:ring-1 focus:ring-primary text-slate-700 dark:text-white cursor-pointer">
                @for($y = date('Y'); $y >= 2024; $y--)
                    <option value="{{ $y }}">{{ $y }}</option>
                @endfor
            </select>
        </div>
        <div>
            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 block">Developer</label>
            <select wire:model.live="filterDeveloper"
                class="bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 text-[13px] rounded-md px-3 py-2 outline-none focus:ring-1 focus:ring-primary text-slate-700 dark:text-white cursor-pointer">
                <option value="">Semua Developer</option>
                @foreach($developerNames as $name)
                    <option value="{{ $name }}">{{ $name }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex items-end">
            <div class="text-xs text-slate-500 bg-slate-50 dark:bg-slate-800 px-3 py-2 rounded-md">
                <i class='bx bx-info-circle'></i> Rate: <span class="font-bold text-primary">Rp 6.000/jam</span>
            </div>
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
                        <th class="px-5 py-3">Developer</th>
                        <th class="px-5 py-3">Tanggal</th>
                        <th class="px-5 py-3">Jam</th>
                        <th class="px-5 py-3">Durasi</th>
                        <th class="px-5 py-3">Deskripsi</th>
                        <th class="px-5 py-3">Bayaran</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700 text-[13px]">
                    @forelse ($logs as $log)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-2">
                                    <div
                                        class="w-7 h-7 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center text-white font-bold text-[10px]">
                                        {{ strtoupper(substr($log->developerName ?? 'X', 0, 1)) }}
                                    </div>
                                    <span
                                        class="font-medium text-slate-800 dark:text-white">{{ $log->developerName ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-3 font-medium text-slate-800 dark:text-white">
                                {{ $log->date->translatedFormat('d M Y') }}
                                <span
                                    class="block text-[10px] text-slate-400">{{ $log->date->translatedFormat('l') }}</span>
                            </td>
                            <td class="px-5 py-3 text-xs font-mono">
                                @if($log->startTime && $log->endTime)
                                    {{ \Carbon\Carbon::parse($log->startTime)->format('H:i') }} -
                                    {{ \Carbon\Carbon::parse($log->endTime)->format('H:i') }}
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 font-bold text-slate-800 dark:text-white">
                                {{ number_format($log->hoursWorked, 1) }} jam
                            </td>
                            <td class="px-5 py-3 max-w-[200px] truncate" title="{{ $log->description }}">
                                {{ $log->description }}
                            </td>
                            <td class="px-5 py-3 font-bold text-primary dark:text-indigo-400">
                                Rp {{ number_format($log->totalAmount, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-3">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase
                                            @if($log->status === 'PENDING') bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400 border border-amber-100 dark:border-amber-500/20
                                            @elseif($log->status === 'APPROVED') bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-500/20
                                            @elseif($log->status === 'PAID') bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400 border border-blue-100 dark:border-blue-500/20
                                            @else bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-400 border border-rose-100 dark:border-rose-500/20
                                            @endif">
                                    {{ $log->statusLabel }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-center">
                                @if($log->status === 'PENDING')
                                    <div class="flex items-center justify-center gap-2">
                                        <button wire:click="editLog({{ $log->id }})"
                                            class="text-slate-400 hover:text-indigo-600 transition-colors p-1" title="Edit">
                                            <i class='bx bx-edit text-lg'></i>
                                        </button>
                                        <button wire:click="deleteLog({{ $log->id }})" wire:confirm="Yakin hapus log ini?"
                                            class="text-slate-400 hover:text-rose-500 transition-colors p-1" title="Hapus">
                                            <i class='bx bx-trash text-lg'></i>
                                        </button>
                                    </div>
                                @else
                                    <span class="text-slate-300 dark:text-slate-600">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-8 text-center text-slate-500">
                                <i class='bx bx-calendar-x text-4xl mb-2'></i>
                                <p>Belum ada log kerja untuk periode ini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="px-5 py-4 border-t border-slate-100 dark:border-slate-700">
                {{ $logs->links() }}
            </div>
        @endif
    </div>

    {{-- Add/Edit Modal --}}
    @if($showForm)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-slate-900 bg-opacity-75 transition-opacity" wire:click="closeForm"></div>

                <div
                    class="inline-block align-bottom bg-white dark:bg-darkCard rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form wire:submit="save">
                        <div class="bg-white dark:bg-darkCard px-6 pt-6 pb-4">
                            <div class="flex items-center gap-3 mb-6">
                                <div
                                    class="flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 dark:bg-indigo-500/10">
                                    <i class='bx bx-time-five text-2xl text-indigo-600'></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">
                                        {{ $editingId ? 'Edit Log Kerja' : 'Tambah Log Kerja' }}
                                    </h3>
                                    <p class="text-[11px] text-slate-500">Catat jam kerja Anda</p>
                                </div>
                            </div>

                            <div class="space-y-4">
                                {{-- Developer Name --}}
                                <div>
                                    <label
                                        class="text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-1 block">Nama
                                        Developer *</label>
                                    <input type="text" wire:model="developerName" placeholder="Contoh: M. REYVAN PURNAMA"
                                        list="developer-names-list"
                                        class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 text-sm rounded-lg px-3 py-2.5 outline-none focus:ring-2 focus:ring-indigo-500 text-slate-700 dark:text-white">
                                    <datalist id="developer-names-list">
                                        @foreach($developerNames as $name)
                                            <option value="{{ $name }}">
                                        @endforeach
                                    </datalist>
                                    @error('developerName') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span>
                                    @enderror
                                </div>

                                {{-- Date --}}
                                <div>
                                    <label
                                        class="text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-1 block">Tanggal
                                        *</label>
                                    <input type="date" wire:model="date"
                                        class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 text-sm rounded-lg px-3 py-2.5 outline-none focus:ring-2 focus:ring-indigo-500 text-slate-700 dark:text-white">
                                    @error('date') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                                </div>

                                {{-- Time Range --}}
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label
                                            class="text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-1 block">Jam
                                            Mulai</label>
                                        <input type="time" wire:model="startTime"
                                            class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 text-sm rounded-lg px-3 py-2.5 outline-none focus:ring-2 focus:ring-indigo-500 text-slate-700 dark:text-white">
                                    </div>
                                    <div>
                                        <label
                                            class="text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-1 block">Jam
                                            Selesai</label>
                                        <input type="time" wire:model="endTime"
                                            class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 text-sm rounded-lg px-3 py-2.5 outline-none focus:ring-2 focus:ring-indigo-500 text-slate-700 dark:text-white">
                                    </div>
                                </div>

                                {{-- Hours Worked --}}
                                <div>
                                    <label
                                        class="text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-1 block">Durasi
                                        (Jam) *</label>
                                    <input type="number" step="0.5" min="0.5" max="24" wire:model="hoursWorked"
                                        placeholder="Contoh: 6"
                                        class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 text-sm rounded-lg px-3 py-2.5 outline-none focus:ring-2 focus:ring-indigo-500 text-slate-700 dark:text-white">
                                    @error('hoursWorked') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span>
                                    @enderror
                                    <p class="text-[10px] text-slate-400 mt-1">
                                        Estimasi bayaran: <span class="font-bold text-primary">Rp
                                            {{ number_format(($hoursWorked ?: 0) * 6000, 0, ',', '.') }}</span>
                                    </p>
                                </div>

                                {{-- Description --}}
                                <div>
                                    <label
                                        class="text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-1 block">Deskripsi
                                        Kegiatan *</label>
                                    <textarea wire:model="description" rows="3"
                                        placeholder="Jelaskan kegiatan yang dikerjakan..."
                                        class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 text-sm rounded-lg px-3 py-2.5 outline-none focus:ring-2 focus:ring-indigo-500 text-slate-700 dark:text-white resize-none"></textarea>
                                    @error('description') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="bg-slate-50 dark:bg-slate-800 px-6 py-4 flex flex-row-reverse gap-2">
                            <button type="submit"
                                class="px-5 py-2.5 bg-indigo-600 text-white rounded-lg font-bold text-sm shadow-md shadow-indigo-500/20 hover:bg-indigo-700 transition-colors">
                                {{ $editingId ? 'Simpan Perubahan' : 'Simpan Log' }}
                            </button>
                            <button type="button" wire:click="closeForm"
                                class="px-5 py-2.5 bg-white dark:bg-darkCard border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-white rounded-lg font-bold text-sm hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>