<div>
    @if (session()->has('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
            class="mb-4 bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-900/50 text-emerald-700 dark:text-emerald-400 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('info'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
            class="mb-4 bg-blue-50 dark:bg-blue-500/10 border border-blue-200 dark:border-blue-900/50 text-blue-700 dark:text-blue-400 px-4 py-3 rounded-lg">
            {{ session('info') }}
        </div>
    @endif

    <div class="mb-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-800 dark:text-white flex items-center gap-2">
                    <i class='bx bx-receipt text-primary'></i> Riwayat Transaksi
                </h1>
                <p class="text-sm text-slate-500 mt-1">Kelola dan pantau seluruh transaksi penjualan</p>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white dark:bg-darkCard p-6 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-1">Total Transaksi</p>
                    <h3 class="text-3xl font-bold text-slate-800 dark:text-white">{{ number_format($stats['total_transactions']) }} <span class="text-base font-normal text-slate-400">Struk</span></h3>
                </div>
                <div class="w-14 h-14 bg-indigo-50 dark:bg-indigo-500/10 rounded-full flex items-center justify-center">
                    <i class='bx bx-receipt text-3xl text-primary'></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-darkCard p-6 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-1">Omzet Hari Ini</p>
                    <h3 class="text-3xl font-bold text-emerald-600 dark:text-emerald-400">Rp {{ number_format($stats['today_revenue'], 0, ',', '.') }}</h3>
                </div>
                <div class="w-14 h-14 bg-emerald-50 dark:bg-emerald-500/10 rounded-full flex items-center justify-center">
                    <i class='bx bx-trending-up text-3xl text-emerald-600'></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-darkCard p-6 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-1">Rata-rata Basket</p>
                    <h3 class="text-3xl font-bold text-slate-800 dark:text-white">Rp {{ number_format($stats['average_basket'], 0, ',', '.') }}</h3>
                </div>
                <div class="w-14 h-14 bg-amber-50 dark:bg-amber-500/10 rounded-full flex items-center justify-center">
                    <i class='bx bx-shopping-bag text-3xl text-amber-600'></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Card -->
    <div class="bg-white dark:bg-darkCard rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">
        
        <!-- Search and Actions Bar -->
        <div class="p-4 border-b border-slate-100 dark:border-slate-700 flex flex-wrap gap-3 items-center justify-between">
            <div class="flex-1 min-w-[200px] max-w-md">
                <div class="relative">
                    <i class='bx bx-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xl'></i>
                    <input wire:model.live.debounce.300ms="search" type="text" 
                        placeholder="Cari nomor struk..."
                        class="w-full pl-10 pr-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-lg outline-none focus:ring-2 focus:ring-primary/20 text-sm text-slate-700 dark:text-white">
                </div>
            </div>

            <div class="flex gap-2 items-center flex-wrap">
                <!-- Date Range Filters -->
                <input wire:model.live="dateFrom" type="date" 
                    class="bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 text-sm rounded-lg px-3 py-2 outline-none text-slate-700 dark:text-white cursor-pointer">
                
                <span class="text-slate-400">—</span>
                
                <input wire:model.live="dateTo" type="date" 
                    class="bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 text-sm rounded-lg px-3 py-2 outline-none text-slate-700 dark:text-white cursor-pointer">

                <button wire:click="exportTransactions" 
                    class="bg-emerald-500 hover:bg-emerald-600 text-white px-4 py-2 rounded-lg text-sm font-bold shadow-md transition-colors flex items-center gap-2">
                    <i class='bx bx-download text-lg'></i> Export
                </button>
            </div>
        </div>

        <!-- Filter Dropdowns -->
        <div class="p-4 border-b border-slate-100 dark:border-slate-700 flex flex-wrap gap-3">
            <select wire:model.live="statusFilter" 
                class="bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 text-sm rounded-lg px-3 py-2 outline-none text-slate-700 dark:text-white cursor-pointer min-w-[140px]">
                <option value="">Semua Status</option>
                <option value="COMPLETED">Berhasil</option>
                <option value="CANCELLED">Void / Batal</option>
                <option value="PENDING">Hold</option>
            </select>

            <select wire:model.live="paymentFilter" 
                class="bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 text-sm rounded-lg px-3 py-2 outline-none text-slate-700 dark:text-white cursor-pointer min-w-[140px]">
                <option value="">Semua Pembayaran</option>
                <option value="CASH">Cash (Tunai)</option>
                <option value="TRANSFER">Transfer</option>
                <option value="CREDIT">Simpanan Anggota</option>
            </select>
        </div>

        <!-- Transactions Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600 dark:text-slate-400">
                <thead class="bg-slate-50 dark:bg-slate-700/50 text-xs uppercase font-semibold text-slate-500 dark:text-slate-400">
                    <tr>
                        <th class="px-6 py-4">No. Struk</th>
                        <th class="px-6 py-4">Waktu</th>
                        <th class="px-6 py-4">Pelanggan</th>
                        <th class="px-6 py-4">Total Belanja</th>
                        <th class="px-6 py-4 text-center">Metode</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse($transactions as $transaction)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors group {{ $transaction->status === 'CANCELLED' ? 'opacity-70' : '' }}">
                            <td class="px-6 py-4 font-mono font-semibold {{ $transaction->status === 'CANCELLED' ? 'text-slate-500 line-through' : 'text-indigo-600 dark:text-indigo-400' }}">
                                {{ $transaction->invoiceNumber }}
                            </td>
                            <td class="px-6 py-4 text-slate-500">
                                {{ $transaction->date->format('H:i') }} 
                                <span class="text-xs text-slate-400 block">{{ $transaction->date->format('d M y') }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    @if($transaction->member)
                                        <div class="w-6 h-6 rounded-full bg-indigo-100 text-primary flex items-center justify-center text-[10px] font-bold">
                                            {{ strtoupper(substr($transaction->member->name, 0, 1)) }}
                                        </div>
                                        <span class="text-slate-700 dark:text-slate-200">{{ $transaction->member->name }}</span>
                                    @else
                                        <div class="w-6 h-6 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-[10px] font-bold">G</div>
                                        <span class="text-slate-700 dark:text-slate-200">Guest (Umum)</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 font-bold {{ $transaction->status === 'CANCELLED' ? 'text-slate-500 line-through' : 'text-slate-800 dark:text-white' }}">
                                Rp {{ number_format($transaction->totalAmount, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($transaction->status !== 'CANCELLED')
                                    @if($transaction->paymentMethod === 'CASH')
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded border border-slate-200 dark:border-slate-600 text-xs font-medium text-slate-600 dark:text-slate-300">
                                            <i class='bx bx-money text-emerald-500'></i> Cash
                                        </span>
                                    @elseif($transaction->paymentMethod === 'TRANSFER')
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded border border-indigo-200 dark:border-indigo-900 text-xs font-medium text-indigo-600 dark:text-indigo-300 bg-indigo-50 dark:bg-indigo-900/20">
                                            <i class='bx bx-transfer'></i> Transfer
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded border border-amber-200 dark:border-amber-900 text-xs font-medium text-amber-600 dark:text-amber-300 bg-amber-50 dark:bg-amber-900/20">
                                            <i class='bx bx-wallet'></i> Credit
                                        </span>
                                    @endif
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($transaction->status === 'COMPLETED')
                                    <span class="bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400 px-3 py-1 rounded-full text-xs font-bold uppercase">Success</span>
                                @elseif($transaction->status === 'CANCELLED')
                                    <span class="bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-400 px-3 py-1 rounded-full text-xs font-bold uppercase">Void</span>
                                @else
                                    <span class="bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-400 px-3 py-1 rounded-full text-xs font-bold uppercase">Hold</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('transaksi.detail', $transaction->id) }}" 
                                    class="text-slate-400 hover:text-primary transition-colors">
                                    <i class='bx bx-show text-xl'></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-slate-400">
                                    <i class='bx bx-receipt text-6xl mb-3 opacity-20'></i>
                                    <p class="font-medium">Tidak ada transaksi ditemukan</p>
                                    <p class="text-xs mt-1">Coba ubah filter atau lakukan transaksi baru di POS</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($transactions->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-700 flex items-center justify-between">
                <p class="text-xs text-slate-500">
                    Menampilkan {{ $transactions->firstItem() }}-{{ $transactions->lastItem() }} dari {{ $transactions->total() }} transaksi
                </p>
                <div class="flex gap-2">
                    @if ($transactions->onFirstPage())
                        <button disabled class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 text-slate-300 text-sm cursor-not-allowed">
                            <i class='bx bx-chevron-left'></i>
                        </button>
                    @else
                        <button wire:click="previousPage" class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 text-slate-500 text-sm hover:bg-slate-50">
                            <i class='bx bx-chevron-left'></i>
                        </button>
                    @endif

                    @if ($transactions->hasMorePages())
                        <button wire:click="nextPage" class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 text-slate-500 text-sm hover:bg-slate-50">
                            <i class='bx bx-chevron-right'></i>
                        </button>
                    @else
                        <button disabled class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 text-slate-300 text-sm cursor-not-allowed">
                            <i class='bx bx-chevron-right'></i>
                        </button>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
