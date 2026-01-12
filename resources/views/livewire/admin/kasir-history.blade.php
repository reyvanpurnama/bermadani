<div class="min-h-screen">
    {{-- Header --}}
    <div class="mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Riwayat Kasir</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Monitoring aktivitas dan performa kasir</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs text-gray-500">Last updated: {{ now()->format('H:i') }}</span>
                <button wire:click="loadTodaySummary" class="p-2 text-gray-500 hover:text-primary hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                    <i class='bx bx-refresh text-xl'></i>
                </button>
            </div>
        </div>
    </div>

    {{-- Today Summary Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center">
                    <i class='bx bx-user-check text-emerald-600 dark:text-emerald-400 text-xl'></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Shift Aktif</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $todaySummary['activeShifts'] ?? 0 }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                    <i class='bx bx-check-circle text-blue-600 dark:text-blue-400 text-xl'></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Shift Selesai</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $todaySummary['completedShifts'] ?? 0 }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                    <i class='bx bx-receipt text-purple-600 dark:text-purple-400 text-xl'></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Transaksi</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $todaySummary['totalTransactions'] ?? 0 }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center">
                    <i class='bx bx-money text-amber-600 dark:text-amber-400 text-xl'></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Total Penjualan</p>
                    <p class="text-lg font-bold text-gray-900 dark:text-white">Rp {{ number_format($todaySummary['totalSales'] ?? 0, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                    <i class='bx bx-wallet text-green-600 dark:text-green-400 text-xl'></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Tunai</p>
                    <p class="text-lg font-bold text-gray-900 dark:text-white">Rp {{ number_format($todaySummary['cashSales'] ?? 0, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
                    <i class='bx bx-credit-card text-indigo-600 dark:text-indigo-400 text-xl'></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Non-Tunai</p>
                    <p class="text-lg font-bold text-gray-900 dark:text-white">Rp {{ number_format($todaySummary['nonCashSales'] ?? 0, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Kasir</label>
                <select wire:model.live="selectedKasir" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary outline-none">
                    <option value="">Semua Kasir</option>
                    @foreach($kasirList as $kasir)
                        <option value="{{ $kasir->id }}">{{ $kasir->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Dari Tanggal</label>
                <input type="date" wire:model.live="dateFrom" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary outline-none">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Sampai Tanggal</label>
                <input type="date" wire:model.live="dateTo" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary outline-none">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Status Shift</label>
                <select wire:model.live="shiftStatus" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary outline-none">
                    <option value="">Semua Status</option>
                    <option value="OPEN">Aktif</option>
                    <option value="CLOSED">Selesai</option>
                </select>
            </div>
            <div class="flex items-end">
                <button wire:click="resetFilters" class="w-full px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors">
                    <i class='bx bx-reset mr-1'></i> Reset
                </button>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="border-b border-gray-200 dark:border-gray-700">
            <div class="flex gap-1 p-2">
                <button wire:click="setTab('shifts')" class="px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ $activeTab === 'shifts' ? 'bg-primary text-white' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    <i class='bx bx-time-five mr-1'></i> Riwayat Shift
                </button>
                <button wire:click="setTab('transactions')" class="px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ $activeTab === 'transactions' ? 'bg-primary text-white' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    <i class='bx bx-receipt mr-1'></i> Transaksi
                </button>
                <button wire:click="setTab('summary')" class="px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ $activeTab === 'summary' ? 'bg-primary text-white' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    <i class='bx bx-bar-chart-alt-2 mr-1'></i> Performa Kasir
                </button>
                <button wire:click="setTab('activity')" class="px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ $activeTab === 'activity' ? 'bg-primary text-white' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    <i class='bx bx-history mr-1'></i> Activity Log
                </button>
            </div>
        </div>

        {{-- Tab Content --}}
        <div class="p-4">
            {{-- Shifts Tab --}}
            @if($activeTab === 'shifts')
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase">Kasir</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase">Check In</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase">Check Out</th>
                                <th class="px-4 py-3 text-right text-xs font-bold text-gray-600 dark:text-gray-300 uppercase">Modal</th>
                                <th class="px-4 py-3 text-right text-xs font-bold text-gray-600 dark:text-gray-300 uppercase">Total Sales</th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-gray-600 dark:text-gray-300 uppercase">Transaksi</th>
                                <th class="px-4 py-3 text-right text-xs font-bold text-gray-600 dark:text-gray-300 uppercase">Selisih</th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-gray-600 dark:text-gray-300 uppercase">Status</th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-gray-600 dark:text-gray-300 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($shifts as $shift)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2">
                                            <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-sm">
                                                {{ substr($shift->user->name ?? 'N', 0, 1) }}
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-900 dark:text-white text-sm">{{ $shift->user->name ?? '-' }}</p>
                                                <p class="text-xs text-gray-500">{{ $shift->user->role ?? '-' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                        {{ $shift->check_in_at ? $shift->check_in_at->format('d M Y H:i') : '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                        {{ $shift->check_out_at ? $shift->check_out_at->format('d M Y H:i') : '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-right font-medium text-gray-900 dark:text-white">
                                        Rp {{ number_format($shift->opening_cash, 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-right font-medium text-emerald-600 dark:text-emerald-400">
                                        Rp {{ number_format($shift->total_sales, 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                                            {{ $shift->total_transactions }} trx
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-right">
                                        @if($shift->status === 'CLOSED')
                                            <span class="font-medium {{ $shift->difference >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                                                {{ $shift->difference >= 0 ? '+' : '' }}Rp {{ number_format($shift->difference, 0, ',', '.') }}
                                            </span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if($shift->status === 'OPEN')
                                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Aktif
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">
                                                Selesai
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <button wire:click="viewShiftDetail({{ $shift->id }})" class="p-1.5 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors" title="Detail">
                                            <i class='bx bx-show text-lg'></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-4 py-12 text-center text-gray-400">
                                        <i class='bx bx-time-five text-4xl mb-2'></i>
                                        <p>Belum ada data shift</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $shifts->links() }}
                </div>
            @endif

            {{-- Transactions Tab --}}
            @if($activeTab === 'transactions')
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase">Invoice</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase">Kasir</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase">Member</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase">Waktu</th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-gray-600 dark:text-gray-300 uppercase">Items</th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-gray-600 dark:text-gray-300 uppercase">Pembayaran</th>
                                <th class="px-4 py-3 text-right text-xs font-bold text-gray-600 dark:text-gray-300 uppercase">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($transactions as $trx)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                    <td class="px-4 py-3">
                                        <span class="font-mono text-sm text-gray-900 dark:text-white">{{ $trx->invoiceNumber }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                        {{ $trx->user->name ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                        {{ $trx->member->name ?? 'Non-Member' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                        {{ $trx->date->format('d M Y H:i') }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-bold bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400">
                                            {{ $trx->items->count() }} item
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold {{ $trx->paymentMethod === 'CASH' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' }}">
                                            {{ $trx->paymentMethod }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right font-semibold text-gray-900 dark:text-white">
                                        Rp {{ number_format($trx->totalAmount, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-12 text-center text-gray-400">
                                        <i class='bx bx-receipt text-4xl mb-2'></i>
                                        <p>Belum ada transaksi</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $transactions->links() }}
                </div>
            @endif

            {{-- Summary Tab --}}
            @if($activeTab === 'summary')
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase">Kasir</th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-gray-600 dark:text-gray-300 uppercase">Total Shift</th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-gray-600 dark:text-gray-300 uppercase">Shift Selesai</th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-gray-600 dark:text-gray-300 uppercase">Total Transaksi</th>
                                <th class="px-4 py-3 text-right text-xs font-bold text-gray-600 dark:text-gray-300 uppercase">Total Penjualan</th>
                                <th class="px-4 py-3 text-right text-xs font-bold text-gray-600 dark:text-gray-300 uppercase">Total Selisih</th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-gray-600 dark:text-gray-300 uppercase">Akurasi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($kasirSummary as $kasir)
                                @php
                                    $accuracy = $kasir->closed_shifts > 0 
                                        ? 100 - (abs($kasir->total_difference_sum ?? 0) / max(($kasir->total_sales_sum ?? 1), 1) * 100) 
                                        : 0;
                                    $accuracy = max(0, min(100, $accuracy));
                                @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2">
                                            <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold">
                                                {{ substr($kasir->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-900 dark:text-white">{{ $kasir->name }}</p>
                                                <p class="text-xs text-gray-500">{{ $kasir->role }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                                            {{ $kasir->total_shifts }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">
                                            {{ $kasir->closed_shifts }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400">
                                            {{ $kasir->total_transactions }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right font-semibold text-emerald-600 dark:text-emerald-400">
                                        Rp {{ number_format($kasir->total_sales_sum ?? 0, 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <span class="font-medium {{ ($kasir->total_difference_sum ?? 0) >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                                            {{ ($kasir->total_difference_sum ?? 0) >= 0 ? '+' : '' }}Rp {{ number_format($kasir->total_difference_sum ?? 0, 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <div class="w-20 bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                                                <div class="h-2 rounded-full {{ $accuracy >= 95 ? 'bg-emerald-500' : ($accuracy >= 80 ? 'bg-amber-500' : 'bg-rose-500') }}" style="width: {{ $accuracy }}%"></div>
                                            </div>
                                            <span class="text-xs font-medium text-gray-600 dark:text-gray-400">{{ number_format($accuracy, 1) }}%</span>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-12 text-center text-gray-400">
                                        <i class='bx bx-bar-chart-alt-2 text-4xl mb-2'></i>
                                        <p>Belum ada data performa</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endif

            {{-- Activity Log Tab --}}
            @if($activeTab === 'activity')
                <div class="space-y-3">
                    @forelse($activityLogs as $log)
                        <div class="flex items-start gap-3 p-3 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0
                                @if($log->action === 'LOGIN') bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400
                                @elseif($log->action === 'LOGOUT') bg-rose-100 text-rose-600 dark:bg-rose-900/30 dark:text-rose-400
                                @elseif($log->action === 'CHECK_IN') bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400
                                @elseif($log->action === 'CHECK_OUT') bg-purple-100 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400
                                @elseif($log->action === 'CREATE') bg-amber-100 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400
                                @else bg-gray-100 text-gray-600 dark:bg-gray-600 dark:text-gray-400
                                @endif
                            ">
                                @if($log->action === 'LOGIN')
                                    <i class='bx bx-log-in text-lg'></i>
                                @elseif($log->action === 'LOGOUT')
                                    <i class='bx bx-log-out text-lg'></i>
                                @elseif($log->action === 'CHECK_IN')
                                    <i class='bx bx-play-circle text-lg'></i>
                                @elseif($log->action === 'CHECK_OUT')
                                    <i class='bx bx-stop-circle text-lg'></i>
                                @elseif($log->action === 'CREATE')
                                    <i class='bx bx-plus-circle text-lg'></i>
                                @else
                                    <i class='bx bx-history text-lg'></i>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="font-medium text-gray-900 dark:text-white text-sm">{{ $log->user->name ?? 'System' }}</span>
                                    <span class="px-2 py-0.5 rounded text-xs font-bold {{ $log->action === 'LOGIN' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : ($log->action === 'LOGOUT' ? 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400' : 'bg-gray-100 text-gray-700 dark:bg-gray-600 dark:text-gray-300') }}">
                                        {{ $log->action }}
                                    </span>
                                    <span class="px-2 py-0.5 rounded text-xs bg-gray-100 text-gray-600 dark:bg-gray-600 dark:text-gray-400">
                                        {{ $log->module }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-400 truncate">{{ $log->description }}</p>
                                <p class="text-xs text-gray-400 mt-1">{{ $log->created_at->format('d M Y H:i:s') }} • {{ $log->ip_address }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12 text-gray-400">
                            <i class='bx bx-history text-4xl mb-2'></i>
                            <p>Belum ada activity log</p>
                        </div>
                    @endforelse
                </div>
                <div class="mt-4">
                    {{ $activityLogs->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Shift Detail Modal --}}
    @if($showDetailModal && $selectedShift)
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4" wire:click.self="closeDetailModal">
            <div class="bg-white dark:bg-gray-800 rounded-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden shadow-2xl">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Detail Shift</h3>
                            <p class="text-sm text-gray-500">{{ $selectedShift->user->name ?? '-' }} • {{ $selectedShift->check_in_at->format('d M Y') }}</p>
                        </div>
                        <button wire:click="closeDetailModal" class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                            <i class='bx bx-x text-2xl'></i>
                        </button>
                    </div>
                </div>

                <div class="p-6 overflow-y-auto max-h-[calc(90vh-200px)]">
                    {{-- Shift Info --}}
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3">
                            <p class="text-xs text-gray-500 dark:text-gray-400">Check In</p>
                            <p class="font-semibold text-gray-900 dark:text-white">{{ $selectedShift->check_in_at->format('H:i') }}</p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3">
                            <p class="text-xs text-gray-500 dark:text-gray-400">Check Out</p>
                            <p class="font-semibold text-gray-900 dark:text-white">{{ $selectedShift->check_out_at ? $selectedShift->check_out_at->format('H:i') : '-' }}</p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3">
                            <p class="text-xs text-gray-500 dark:text-gray-400">Modal Awal</p>
                            <p class="font-semibold text-gray-900 dark:text-white">Rp {{ number_format($selectedShift->opening_cash, 0, ',', '.') }}</p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3">
                            <p class="text-xs text-gray-500 dark:text-gray-400">Status</p>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold {{ $selectedShift->status === 'OPEN' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-gray-100 text-gray-600 dark:bg-gray-600 dark:text-gray-400' }}">
                                {{ $selectedShift->status === 'OPEN' ? 'Aktif' : 'Selesai' }}
                            </span>
                        </div>
                    </div>

                    @if($selectedShift->status === 'CLOSED')
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                            <div class="bg-emerald-50 dark:bg-emerald-900/20 rounded-lg p-3">
                                <p class="text-xs text-emerald-600 dark:text-emerald-400">Total Sales</p>
                                <p class="font-bold text-emerald-700 dark:text-emerald-300">Rp {{ number_format($selectedShift->total_sales, 0, ',', '.') }}</p>
                            </div>
                            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-3">
                                <p class="text-xs text-blue-600 dark:text-blue-400">Cash Sales</p>
                                <p class="font-bold text-blue-700 dark:text-blue-300">Rp {{ number_format($selectedShift->total_cash_sales, 0, ',', '.') }}</p>
                            </div>
                            <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-3">
                                <p class="text-xs text-purple-600 dark:text-purple-400">Kas Akhir</p>
                                <p class="font-bold text-purple-700 dark:text-purple-300">Rp {{ number_format($selectedShift->closing_cash, 0, ',', '.') }}</p>
                            </div>
                            <div class="{{ $selectedShift->difference >= 0 ? 'bg-emerald-50 dark:bg-emerald-900/20' : 'bg-rose-50 dark:bg-rose-900/20' }} rounded-lg p-3">
                                <p class="text-xs {{ $selectedShift->difference >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">Selisih</p>
                                <p class="font-bold {{ $selectedShift->difference >= 0 ? 'text-emerald-700 dark:text-emerald-300' : 'text-rose-700 dark:text-rose-300' }}">
                                    {{ $selectedShift->difference >= 0 ? '+' : '' }}Rp {{ number_format($selectedShift->difference, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>
                    @endif

                    {{-- Transactions in Shift --}}
                    <h4 class="font-semibold text-gray-900 dark:text-white mb-3">Transaksi dalam Shift ({{ count($shiftTransactions) }})</h4>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 dark:bg-gray-700/50">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-bold text-gray-600 dark:text-gray-300">Invoice</th>
                                    <th class="px-3 py-2 text-left text-xs font-bold text-gray-600 dark:text-gray-300">Waktu</th>
                                    <th class="px-3 py-2 text-center text-xs font-bold text-gray-600 dark:text-gray-300">Items</th>
                                    <th class="px-3 py-2 text-center text-xs font-bold text-gray-600 dark:text-gray-300">Bayar</th>
                                    <th class="px-3 py-2 text-right text-xs font-bold text-gray-600 dark:text-gray-300">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($shiftTransactions as $trx)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                        <td class="px-3 py-2 font-mono text-xs">{{ $trx->invoiceNumber }}</td>
                                        <td class="px-3 py-2 text-gray-600 dark:text-gray-400">{{ $trx->date->format('H:i') }}</td>
                                        <td class="px-3 py-2 text-center">{{ $trx->items->count() }}</td>
                                        <td class="px-3 py-2 text-center">
                                            <span class="px-2 py-0.5 rounded text-xs font-bold {{ $trx->paymentMethod === 'CASH' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' }}">
                                                {{ $trx->paymentMethod }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-2 text-right font-medium">Rp {{ number_format($trx->totalAmount, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-3 py-8 text-center text-gray-400">
                                            Tidak ada transaksi dalam shift ini
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($selectedShift->note)
                        <div class="mt-4 p-3 bg-amber-50 dark:bg-amber-900/20 rounded-lg">
                            <p class="text-xs font-medium text-amber-600 dark:text-amber-400 mb-1">Catatan:</p>
                            <p class="text-sm text-amber-800 dark:text-amber-200">{{ $selectedShift->note }}</p>
                        </div>
                    @endif
                </div>

                <div class="p-4 border-t border-gray-200 dark:border-gray-700 flex justify-end">
                    <button wire:click="closeDetailModal" class="px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
