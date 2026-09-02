<div>
    @section('title', 'Pengembalian Simpanan Anggota Keluar')

    <!-- Header Page -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white flex items-center gap-2">
                <i class='bx bx-user-x text-rose-600 dark:text-rose-400'></i>
                Pengembalian Simpanan Anggota Keluar
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                Kelola daftar anggota berhenti/keluar, kalkulasi sisa simpanan & potongan pinjaman, serta proses pelunasan.
            </p>
        </div>
    </div>

    <!-- Alert Success Flash -->
    @if (session()->has('message'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 flex items-center justify-between shadow-sm animate-fade-in">
            <div class="flex items-center gap-3">
                <i class='bx bx-check-circle text-2xl'></i>
                <span class="font-medium">{{ session('message') }}</span>
            </div>
            <button type="button" @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
                <i class='bx bx-x text-xl'></i>
            </button>
        </div>
    @endif

    <!-- 4 Overview KPI Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-6">
        <!-- Card 1: Total Member Keluar -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Anggota Keluar</span>
                <div class="w-10 h-10 rounded-xl bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 flex items-center justify-center">
                    <i class='bx bx-user-minus text-2xl'></i>
                </div>
            </div>
            <h3 class="text-2xl font-extrabold text-slate-800 dark:text-white">{{ number_format($totalResignedCount) }}</h3>
            <p class="text-xs text-slate-400 mt-1">Status nonaktif / resigned</p>
        </div>

        <!-- Card 2: Total Hak Simpanan Gross -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Hak Simpanan</span>
                <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                    <i class='bx bx-wallet text-2xl'></i>
                </div>
            </div>
            <h3 class="text-xl font-extrabold text-indigo-600 dark:text-indigo-400">Rp {{ number_format($totalSimpananGross, 0, ',', '.') }}</h3>
            <p class="text-xs text-slate-400 mt-1">Simpok + Simwa + Simsuka</p>
        </div>

        <!-- Card 3: Status Pelunasan Selesai -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Sudah Dilunasi</span>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                    <i class='bx bx-check-double text-2xl'></i>
                </div>
            </div>
            <h3 class="text-2xl font-extrabold text-emerald-600 dark:text-emerald-400">{{ number_format($settledCount) }}</h3>
            <p class="text-xs text-slate-400 mt-1">Pengembalian selesai</p>
        </div>

        <!-- Card 4: Pending / Belum Dilunasi -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Belum Dilunasi</span>
                <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 flex items-center justify-center">
                    <i class='bx bx-time-five text-2xl'></i>
                </div>
            </div>
            <h3 class="text-2xl font-extrabold text-amber-600 dark:text-amber-400">{{ number_format($pendingCount) }}</h3>
            <p class="text-xs text-slate-400 mt-1">Menunggu proses pelunasan</p>
        </div>
    </div>

    <!-- Filters & Table Card -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <!-- Filter Header -->
        <div class="p-5 border-b border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <!-- Search -->
            <div class="relative flex-1 max-w-md">
                <i class='bx bx-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-lg'></i>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama, no. anggota, unit kerja..." 
                       class="w-full pl-10 pr-4 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500">
            </div>

            <!-- Filter Status -->
            <div class="flex items-center gap-2">
                <label class="text-xs font-semibold text-slate-500 dark:text-slate-400 shrink-0">Status Pelunasan:</label>
                <select wire:model.live="statusFilter" class="px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-white text-xs font-medium focus:ring-2 focus:ring-indigo-500">
                    <option value="ALL">Semua Status</option>
                    <option value="PENDING">Belum Lunas (Pending)</option>
                    <option value="SETTLED">Sudah Lunas (Settled)</option>
                </select>
            </div>
        </div>

        <!-- Data Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 dark:bg-slate-800/80 text-slate-500 dark:text-slate-400 text-xs uppercase font-semibold border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="px-4 py-3.5">Anggota</th>
                        <th class="px-4 py-3.5">Unit Kerja</th>
                        <th class="px-4 py-3.5 text-right">Total Simpanan</th>
                        <th class="px-4 py-3.5 text-right">Potongan Pinjaman</th>
                        <th class="px-4 py-3.5 text-right">Net Pengembalian</th>
                        <th class="px-4 py-3.5 text-center">Status Pelunasan</th>
                        <th class="px-4 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse($members as $m)
                        @php
                            $simpok = (float) $m->simpananPokok;
                            $simwa = (float) $m->simpananWajib;
                            $simsuka = (float) $m->simpananSukarela;
                            $gross = $simpok + $simwa + $simsuka;

                            $settlement = $m->settlement;
                            $isSettled = $settlement && $settlement->status === 'SETTLED';

                            $loanDeduction = $settlement ? (float)$settlement->loan_deduction : 0;
                            $netRefund = $settlement ? (float)$settlement->net_refund_amount : $gross;
                        @endphp
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-colors">
                            <!-- Member Name & ID -->
                            <td class="px-4 py-3.5">
                                <div class="font-bold text-slate-800 dark:text-white">{{ $m->name }}</div>
                                <div class="text-xs font-mono text-slate-400">#{{ $m->nomorAnggota }}</div>
                            </td>

                            <!-- Unit Kerja -->
                            <td class="px-4 py-3.5 text-xs text-slate-600 dark:text-slate-300">
                                {{ $m->unitKerja ?? '-' }}
                            </td>

                            <!-- Total Simpanan Gross -->
                            <td class="px-4 py-3.5 text-right">
                                <div class="font-bold text-indigo-600 dark:text-indigo-400 text-xs">
                                    Rp {{ number_format($gross, 0, ',', '.') }}
                                </div>
                                <div class="text-[10px] text-slate-400">
                                    P: {{ number_format($simpok/1000) }}k | W: {{ number_format($simwa/1000) }}k
                                </div>
                            </td>

                            <!-- Potongan Pinjaman -->
                            <td class="px-4 py-3.5 text-right text-xs">
                                @if($loanDeduction > 0)
                                    <span class="text-rose-600 dark:text-rose-400 font-semibold">- Rp {{ number_format($loanDeduction, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>

                            <!-- Net Pengembalian -->
                            <td class="px-4 py-3.5 text-right">
                                <div class="font-extrabold text-slate-800 dark:text-white text-sm">
                                    Rp {{ number_format($netRefund, 0, ',', '.') }}
                                </div>
                            </td>

                            <!-- Status Pelunasan -->
                            <td class="px-4 py-3.5 text-center">
                                @if($isSettled)
                                    <span class="px-2.5 py-1 rounded-full bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 text-[11px] font-bold border border-emerald-300 dark:border-emerald-800 inline-flex items-center gap-1">
                                        <i class='bx bx-check-circle'></i> LUNAS
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 text-[11px] font-bold border border-amber-300 dark:border-amber-800 inline-flex items-center gap-1">
                                        <i class='bx bx-time-five'></i> PENDING
                                    </span>
                                @endif
                            </td>

                            <!-- Action Buttons -->
                            <td class="px-4 py-3.5 text-right space-x-1">
                                <button wire:click="openProcessModal({{ $m->id }})" 
                                    class="px-2.5 py-1.5 rounded-lg bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-100 dark:hover:bg-indigo-900/60 text-xs font-semibold transition-colors inline-flex items-center gap-1">
                                    <i class='bx bx-credit-card'></i>
                                    <span>{{ $isSettled ? 'Edit Pelunasan' : 'Proses Bayar' }}</span>
                                </button>

                                @if($isSettled)
                                    <a href="{{ route('admin.members.settlement-pdf', $m->id) }}" target="_blank"
                                       class="px-2.5 py-1.5 rounded-lg bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-100 dark:hover:bg-emerald-900/60 text-xs font-semibold transition-colors inline-flex items-center gap-1">
                                        <i class='bx bx-printer'></i>
                                        <span>Cetak PDF</span>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center text-slate-400 text-xs">
                                Tidak ada data anggota keluar yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="p-4 border-t border-slate-200 dark:border-slate-800">
            {{ $members->links() }}
        </div>
    </div>

    <!-- MODAL PROCESS PAYOUT / SETTLEMENT -->
    @if($showProcessModal && $selectedMember)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm animate-fade-in">
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xl max-w-lg w-full p-6 space-y-5">
                <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white flex items-center gap-2">
                        <i class='bx bx-credit-card text-indigo-600'></i>
                        Proses Pelunasan Simpanan
                    </h3>
                    <button wire:click="closeModals" class="text-slate-400 hover:text-slate-600 dark:hover:text-white">
                        <i class='bx bx-x text-2xl'></i>
                    </button>
                </div>

                <!-- Member Summary Card -->
                <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 text-xs space-y-1">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Nama Anggota:</span>
                        <span class="font-bold text-slate-800 dark:text-white">{{ $selectedMember->name }} (#{{ $selectedMember->nomorAnggota }})</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Total Simpanan (Gross):</span>
                        <span class="font-bold text-indigo-600 dark:text-indigo-400">Rp {{ number_format($selectedMember->simpananPokok + $selectedMember->simpananWajib + $selectedMember->simpananSukarela, 0, ',', '.') }}</span>
                    </div>
                </div>

                <form wire:submit.prevent="processSettlement" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Metode Pembayaran</label>
                        <select wire:model.live="payment_method" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white text-xs">
                            <option value="BANK_TRANSFER">Transfer Bank</option>
                            <option value="CASH">Tunai / Cash</option>
                        </select>
                    </div>

                    @if($payment_method === 'BANK_TRANSFER')
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Bank</label>
                                <input type="text" wire:model.defer="bank_name" placeholder="Contoh: Bank BCA" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white text-xs">
                                @error('bank_name') <span class="text-[10px] text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">No. Rekening</label>
                                <input type="text" wire:model.defer="bank_account_number" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white text-xs font-mono">
                                @error('bank_account_number') <span class="text-[10px] text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-span-2">
                                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Atas Nama Rekening</label>
                                <input type="text" wire:model.defer="bank_account_holder" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white text-xs">
                                @error('bank_account_holder') <span class="text-[10px] text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    @endif

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Tanggal Pelunasan</label>
                        <input type="date" wire:model.defer="settled_at" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white text-xs">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Catatan / Keterangan</label>
                        <textarea wire:model.defer="notes" rows="2" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white text-xs"></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-3 border-t border-slate-200 dark:border-slate-800">
                        <button type="button" wire:click="closeModals" class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 text-xs font-semibold">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold shadow-sm flex items-center gap-1.5">
                            <i class='bx bx-check-circle text-base'></i>
                            <span>Konfirmasi Pelunasan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
