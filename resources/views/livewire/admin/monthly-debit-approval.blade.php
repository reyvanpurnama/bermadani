<div class="p-6">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-800 dark:text-white mb-2">Persetujuan Auto-Debit Simpanan Wajib</h2>
        <p class="text-slate-600 dark:text-slate-400">Review dan setujui transaksi auto-debit simpanan wajib bulanan</p>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="mb-4 p-4 bg-green-50 dark:bg-green-500/10 border border-green-200 dark:border-green-500/30 text-green-700 dark:text-green-400 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 p-4 bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/30 text-red-700 dark:text-red-400 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-yellow-50 dark:bg-yellow-500/10 border border-yellow-200 dark:border-yellow-500/30 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-yellow-600 dark:text-yellow-400 mb-1">Pending</p>
                    <p class="text-2xl font-bold text-yellow-700 dark:text-yellow-500">{{ $stats['pending'] }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-500/20 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-green-50 dark:bg-green-500/10 border border-green-200 dark:border-green-500/30 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-green-600 dark:text-green-400 mb-1">Approved</p>
                    <p class="text-2xl font-bold text-green-700 dark:text-green-500">{{ $stats['approved'] }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 dark:bg-green-500/20 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-blue-50 dark:bg-blue-500/10 border border-blue-200 dark:border-blue-500/30 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-blue-600 dark:text-blue-400 mb-1">Total Amount</p>
                    <p class="text-2xl font-bold text-blue-700 dark:text-blue-500">Rp {{ number_format($stats['totalAmount'], 0, ',', '.') }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-500/20 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Actions -->
    <div class="bg-white dark:bg-darkCard rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 mb-6">
        <div class="p-4 space-y-4">
            <!-- Top Row: Month selector + Generate button -->
            <div class="flex items-end justify-between">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Pilih Bulan</label>
                    <input type="month" wire:model.live="selectedMonth" 
                           class="px-3 py-2 border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <button wire:click="generateDebit" 
                        wire:loading.attr="disabled"
                        wire:confirm="Generate auto-debit untuk bulan ini? Akan membuat transaksi PENDING untuk semua member aktif."
                        class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg wire:loading.remove wire:target="generateDebit" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    <svg wire:loading wire:target="generateDebit" class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span wire:loading.remove wire:target="generateDebit">Generate Auto-Debit</span>
                    <span wire:loading wire:target="generateDebit">Processing...</span>
                </button>
            </div>

            <!-- Bottom Row: Approval actions -->
            <div class="flex items-center justify-between border-t border-slate-200 dark:border-slate-700 pt-4">
                <div class="text-sm text-slate-600 dark:text-slate-400">
                    <span class="font-semibold text-slate-900 dark:text-white">{{ count($selectedTransactions) }}</span> transaksi dipilih
                </div>

                <div class="flex gap-2">
                    <button wire:click="approveAll" 
                            wire:confirm="Yakin ingin menyetujui SEMUA transaksi pending?"
                            class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
                            @if($stats['pending'] == 0) disabled @endif>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Approve All
                </button>

                <button wire:click="approveSelected" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
                        @if(empty($selectedTransactions)) disabled @endif>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Approve Selected
                </button>

                <button wire:click="rejectSelected" 
                        wire:confirm="Yakin ingin menolak transaksi terpilih?"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
                        @if(empty($selectedTransactions)) disabled @endif>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Reject Selected
                </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Warning message -->
    @if (session()->has('warning'))
        <div class="mb-4 p-4 bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/30 text-amber-700 dark:text-amber-400 rounded-lg flex items-start gap-3">
            <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            <span>{{ session('warning') }}</span>
        </div>
    @endif

    <!-- Transactions Table -->
    <div class="bg-white dark:bg-darkCard rounded-lg shadow-sm border border-slate-200 dark:border-slate-700">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700">
                    <tr>
                        <th class="px-4 py-3 text-left">
                            <input type="checkbox" wire:model.live="selectAll" 
                                   class="w-4 h-4 text-blue-600 rounded focus:ring-2 focus:ring-blue-500 dark:bg-slate-700 dark:border-slate-600">
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase">Tanggal</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase">Member</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase">No. Anggota</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase">Amount</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase">Balance After</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                    @forelse($transactions as $transaction)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                            <td class="px-4 py-3">
                                <input type="checkbox" wire:model.live="selectedTransactions" value="{{ $transaction->id }}"
                                       class="w-4 h-4 text-blue-600 rounded focus:ring-2 focus:ring-blue-500 dark:bg-slate-700 dark:border-slate-600">
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-600 dark:text-slate-400">
                                {{ $transaction->created_at->format('d M Y') }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-sm font-medium text-slate-800 dark:text-white">{{ $transaction->member->name }}</div>
                                <div class="text-xs text-slate-500 dark:text-slate-400">{{ $transaction->member->user->email }}</div>
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-600 dark:text-slate-400">
                                {{ $transaction->member->nomorAnggota }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-sm font-semibold text-green-600 dark:text-green-400">
                                    Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-sm text-slate-600 dark:text-slate-400">
                                    Rp {{ number_format($transaction->balanceAfter, 0, ',', '.') }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-500/20 text-yellow-800 dark:text-yellow-400">
                                    {{ $transaction->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-slate-500 dark:text-slate-400">
                                <svg class="w-12 h-12 mx-auto mb-2 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Tidak ada transaksi pending untuk bulan ini
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($transactions->hasPages())
            <div class="px-4 py-3 border-t border-slate-200 dark:border-slate-700">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>
</div>
