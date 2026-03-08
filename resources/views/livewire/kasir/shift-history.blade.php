<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">Riwayat Shift Saya</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Rekap semua shift yang pernah kamu kerjakan</p>
        </div>
        <a href="{{ route('kasir.dashboard') }}"
            class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-xl transition-colors">
            <i class='bx bx-arrow-back text-lg'></i> Dashboard
        </a>
    </div>

    {{-- Lifetime Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-darkCard rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center shrink-0">
                    <i class='bx bx-time text-indigo-600 dark:text-indigo-400 text-xl'></i>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Total Jam Kerja</p>
                    <p class="text-lg font-bold text-slate-900 dark:text-white">
                        {{ $lifetimeStats['total_hours'] }}j {{ $lifetimeStats['total_minutes_rem'] }}m
                    </p>
                    <p class="text-[10px] text-slate-400">{{ $lifetimeStats['total_shifts'] }} shift total</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-darkCard rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center shrink-0">
                    <i class='bx bx-trending-up text-emerald-600 dark:text-emerald-400 text-xl'></i>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Total Penjualan</p>
                    <p class="text-base font-bold text-emerald-600 dark:text-emerald-400">
                        Rp {{ number_format($lifetimeStats['total_sales'], 0, ',', '.') }}
                    </p>
                    <p class="text-[10px] text-slate-400">{{ number_format($lifetimeStats['total_transactions']) }} transaksi</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-darkCard rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center shrink-0">
                    <i class='bx bx-bar-chart-alt-2 text-blue-600 dark:text-blue-400 text-xl'></i>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Rata-rata/Shift</p>
                    <p class="text-base font-bold text-slate-900 dark:text-white">
                        Rp {{ number_format($lifetimeStats['avg_sales_per_shift'], 0, ',', '.') }}
                    </p>
                    <p class="text-[10px] text-slate-400">per shift selesai</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-darkCard rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 p-4">
            <div class="flex items-center gap-3">
                @php $diff = $lifetimeStats['total_difference']; @endphp
                <div class="w-10 h-10 rounded-xl {{ $diff >= 0 ? 'bg-emerald-100 dark:bg-emerald-900/30' : 'bg-rose-100 dark:bg-rose-900/30' }} flex items-center justify-center shrink-0">
                    <i class='bx bx-wallet {{ $diff >= 0 ? "text-emerald-600 dark:text-emerald-400" : "text-rose-600 dark:text-rose-400" }} text-xl'></i>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Total Selisih Saldo</p>
                    <p class="text-base font-bold {{ $diff >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                        {{ $diff >= 0 ? '+' : '' }}Rp {{ number_format($diff, 0, ',', '.') }}
                    </p>
                    <p class="text-[10px] text-slate-400">akumulasi semua shift</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Shift History Table --}}
    <div class="bg-white dark:bg-darkCard rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">
        <div class="p-5 border-b border-slate-100 dark:border-slate-700">
            <h3 class="font-bold text-slate-800 dark:text-white">Daftar Shift</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 dark:bg-slate-700/50">
                    <tr>
                        <th class="px-5 py-3 text-left text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Tanggal</th>
                        <th class="px-5 py-3 text-left text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Check In</th>
                        <th class="px-5 py-3 text-left text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Check Out</th>
                        <th class="px-5 py-3 text-center text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Durasi</th>
                        <th class="px-5 py-3 text-right text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Modal Awal</th>
                        <th class="px-5 py-3 text-right text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Penjualan</th>
                        <th class="px-5 py-3 text-center text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Transaksi</th>
                        <th class="px-5 py-3 text-right text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Selisih Saldo</th>
                        <th class="px-5 py-3 text-center text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
                        <th class="px-5 py-3 text-center text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Detail</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse($shifts as $shift)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                            <td class="px-5 py-4 text-sm font-medium text-slate-900 dark:text-white">
                                {{ $shift->check_in_at->format('d M Y') }}
                            </td>
                            <td class="px-5 py-4 text-sm text-slate-600 dark:text-slate-400">
                                {{ $shift->check_in_at->format('H:i') }}
                            </td>
                            <td class="px-5 py-4 text-sm text-slate-600 dark:text-slate-400">
                                {{ $shift->check_out_at ? $shift->check_out_at->format('H:i') : '-' }}
                            </td>
                            <td class="px-5 py-4 text-center">
                                <span class="text-sm font-medium text-slate-700 dark:text-slate-300">
                                    {{ $shift->status === 'CLOSED' ? $shift->duration : '— sedang berlangsung' }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right text-sm text-slate-700 dark:text-slate-300">
                                Rp {{ number_format($shift->opening_cash, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-4 text-right text-sm font-semibold text-emerald-600 dark:text-emerald-400">
                                Rp {{ number_format($shift->total_sales, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                                    {{ $shift->total_transactions }} trx
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right text-sm">
                                @if($shift->status === 'CLOSED')
                                    <span class="font-semibold {{ $shift->difference >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                                        {{ $shift->difference >= 0 ? '+' : '' }}Rp {{ number_format($shift->difference, 0, ',', '.') }}
                                    </span>
                                @else
                                    <span class="text-slate-400 text-xs">Shift aktif</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-center">
                                @if($shift->status === 'OPEN')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Aktif
                                    </span>
                                @elseif($shift->difference >= 0)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-400">
                                        <i class='bx bx-check text-sm'></i> Selesai
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400">
                                        <i class='bx bx-error text-sm'></i> Selisih
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-center">
                                @if($shift->status === 'CLOSED')
                                    <button wire:click="viewDetail({{ $shift->id }})"
                                        class="p-1.5 text-primary hover:bg-indigo-50 dark:hover:bg-indigo-900/20 rounded-xl transition-colors" title="Lihat Detail">
                                        <i class='bx bx-show text-lg'></i>
                                    </button>
                                @else
                                    <span class="text-slate-300 dark:text-slate-600 text-xs">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-5 py-16 text-center">
                                <i class='bx bx-time-five text-4xl text-slate-300 dark:text-slate-600 mb-2'></i>
                                <p class="text-sm text-slate-400">Belum ada riwayat shift</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($shifts->hasPages())
            <div class="px-5 py-4 border-t border-slate-100 dark:border-slate-700">
                {{ $shifts->links() }}
            </div>
        @endif
    </div>

    {{-- Shift Detail Modal --}}
    @if($showDetailModal && $this->selectedShift)
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4" wire:click.self="closeModal">
        <div class="bg-white dark:bg-darkCard rounded-2xl w-full max-w-3xl max-h-[90vh] overflow-hidden shadow-2xl border border-slate-100 dark:border-slate-700">
            {{-- Modal Header --}}
            <div class="bg-gradient-to-r from-primary to-indigo-600 p-5 text-white flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold">Detail Shift</h3>
                    <p class="text-indigo-200 text-xs mt-0.5">
                        {{ $this->selectedShift->check_in_at->format('d M Y') }} •
                        {{ $this->selectedShift->check_in_at->format('H:i') }} – {{ $this->selectedShift->check_out_at?->format('H:i') ?? '-' }}
                        ({{ $this->selectedShift->duration }})
                    </p>
                </div>
                <button wire:click="closeModal" class="p-1.5 hover:bg-white/20 rounded-xl transition-colors">
                    <i class='bx bx-x text-2xl'></i>
                </button>
            </div>

            <div class="p-5 overflow-y-auto max-h-[calc(90vh-180px)] space-y-5">
                {{-- Summary Cards --}}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    <div class="bg-slate-50 dark:bg-slate-800 rounded-xl p-3 text-center">
                        <p class="text-[10px] text-slate-400 uppercase font-bold">Modal Awal</p>
                        <p class="font-bold text-slate-900 dark:text-white text-sm mt-1">Rp {{ number_format($this->selectedShift->opening_cash, 0, ',', '.') }}</p>
                    </div>
                    <div class="bg-emerald-50 dark:bg-emerald-900/20 rounded-xl p-3 text-center">
                        <p class="text-[10px] text-emerald-600 dark:text-emerald-400 uppercase font-bold">Total Penjualan</p>
                        <p class="font-bold text-emerald-700 dark:text-emerald-300 text-sm mt-1">Rp {{ number_format($this->selectedShift->total_sales, 0, ',', '.') }}</p>
                    </div>
                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-3 text-center">
                        <p class="text-[10px] text-blue-600 dark:text-blue-400 uppercase font-bold">Penjualan Tunai</p>
                        <p class="font-bold text-blue-700 dark:text-blue-300 text-sm mt-1">Rp {{ number_format($this->selectedShift->total_cash_sales, 0, ',', '.') }}</p>
                    </div>
                    <div class="{{ $this->selectedShift->difference >= 0 ? 'bg-emerald-50 dark:bg-emerald-900/20' : 'bg-rose-50 dark:bg-rose-900/20' }} rounded-xl p-3 text-center">
                        <p class="text-[10px] {{ $this->selectedShift->difference >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }} uppercase font-bold">Selisih Saldo</p>
                        <p class="font-bold text-sm mt-1 {{ $this->selectedShift->difference >= 0 ? 'text-emerald-700 dark:text-emerald-300' : 'text-rose-700 dark:text-rose-300' }}">
                            {{ $this->selectedShift->difference >= 0 ? '+' : '' }}Rp {{ number_format($this->selectedShift->difference, 0, ',', '.') }}
                        </p>
                    </div>
                </div>

                @if($this->selectedShift->note)
                    <div class="p-3 bg-amber-50 dark:bg-amber-900/20 rounded-xl border border-amber-100 dark:border-amber-800/30">
                        <p class="text-[10px] font-bold text-amber-600 dark:text-amber-400 uppercase mb-1">Catatan Shift</p>
                        <p class="text-sm text-amber-800 dark:text-amber-200">{{ $this->selectedShift->note }}</p>
                    </div>
                @endif

                {{-- Transactions in shift --}}
                <div>
                    <h4 class="font-bold text-sm text-slate-800 dark:text-white mb-3">
                        Transaksi dalam Shift
                        <span class="ml-1 text-xs font-medium text-slate-400">({{ count($shiftTransactions) }} transaksi)</span>
                    </h4>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-slate-50 dark:bg-slate-800">
                                <tr>
                                    <th class="px-3 py-2.5 text-left text-[10px] font-bold text-slate-500 uppercase">Invoice</th>
                                    <th class="px-3 py-2.5 text-left text-[10px] font-bold text-slate-500 uppercase">Waktu</th>
                                    <th class="px-3 py-2.5 text-left text-[10px] font-bold text-slate-500 uppercase">Pelanggan</th>
                                    <th class="px-3 py-2.5 text-center text-[10px] font-bold text-slate-500 uppercase">Item</th>
                                    <th class="px-3 py-2.5 text-center text-[10px] font-bold text-slate-500 uppercase">Bayar</th>
                                    <th class="px-3 py-2.5 text-right text-[10px] font-bold text-slate-500 uppercase">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                                @forelse($shiftTransactions as $trx)
                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30">
                                        <td class="px-3 py-2.5 font-mono text-xs text-slate-600 dark:text-slate-400">{{ $trx->invoiceNumber }}</td>
                                        <td class="px-3 py-2.5 text-slate-500">{{ $trx->date->format('H:i') }}</td>
                                        <td class="px-3 py-2.5 text-slate-700 dark:text-slate-300">{{ $trx->member->name ?? 'Guest' }}</td>
                                        <td class="px-3 py-2.5 text-center text-slate-500">{{ $trx->items->count() }}</td>
                                        <td class="px-3 py-2.5 text-center">
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $trx->paymentMethod === 'CASH' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' }}">
                                                {{ $trx->paymentMethod }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-2.5 text-right font-semibold text-slate-900 dark:text-white">
                                            Rp {{ number_format($trx->totalAmount, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-3 py-8 text-center text-slate-400 text-sm">Tidak ada transaksi dalam shift ini</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="px-5 py-4 border-t border-slate-100 dark:border-slate-700 flex justify-end">
                <button wire:click="closeModal"
                    class="px-4 py-2 text-sm font-medium text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-xl transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
