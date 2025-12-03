<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-800 dark:text-white">Riwayat Transaksi Manual</h1>
            <p class="text-sm text-slate-500">Semua catatan pemasukan & pengeluaran operasional</p>
        </div>
        <a href="{{ route('admin.manual-transaction') }}" class="bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 px-4 py-2 rounded-lg text-sm font-medium hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
            <i class='bx bx-arrow-back mr-1'></i> Kembali
        </a>
    </div>

    {{-- Filters --}}
    <div class="bg-card dark:bg-darkCard rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 p-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-2">
                <div class="relative">
                    <i class='bx bx-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400'></i>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari keterangan atau kategori..." class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-lg pl-10 pr-4 py-2 text-sm outline-none focus:border-primary dark:text-white">
                </div>
            </div>
            <div>
                <select wire:model.live="typeFilter" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-lg px-3 py-2 text-sm outline-none focus:border-primary dark:text-white cursor-pointer">
                    <option value="">Semua Tipe</option>
                    <option value="INCOME">Pemasukan</option>
                    <option value="EXPENSE">Pengeluaran</option>
                </select>
            </div>
            <div>
                <input type="date" wire:model.live="dateFilter" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-lg px-3 py-2 text-sm outline-none focus:border-primary dark:text-white">
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-card dark:bg-darkCard rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-100 dark:border-slate-700 text-xs uppercase text-slate-500 font-semibold">
                        <th class="px-6 py-4">Tanggal</th>
                        <th class="px-6 py-4">Kategori</th>
                        <th class="px-6 py-4">Keterangan</th>
                        <th class="px-6 py-4 text-right">Nominal</th>
                        <th class="px-6 py-4 text-center">Bukti</th>
                        <th class="px-6 py-4 text-center">Oleh</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse($transactions as $trx)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors text-sm text-slate-600 dark:text-slate-300">
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $trx->transactionDate->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium {{ $trx->type === 'INCOME' ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-900/20 dark:text-emerald-400' : 'bg-rose-50 text-rose-600 dark:bg-rose-900/20 dark:text-rose-400' }}">
                                    <i class='bx {{ $trx->type === 'INCOME' ? 'bx-trending-up' : 'bx-trending-down' }}'></i>
                                    {{ $trx->category }}
                                </span>
                            </td>
                            <td class="px-6 py-4 max-w-xs truncate" title="{{ $trx->description }}">
                                {{ $trx->description ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-right font-bold {{ $trx->type === 'INCOME' ? 'text-emerald-600' : 'text-rose-600' }}">
                                {{ $trx->type === 'INCOME' ? '+' : '-' }} Rp {{ number_format($trx->amount, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($trx->proofFile)
                                    <button 
                                        type="button" 
                                        onclick="showProof('{{ asset('storage/' . $trx->proofFile) }}')"
                                        class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 transition-colors"
                                        title="Lihat bukti"
                                    >
                                        <i class='bx bx-image-alt text-lg'></i>
                                    </button>
                                @else
                                    <span class="text-slate-300">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center text-xs">
                                {{ $trx->user?->name ?? 'Unknown' }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('admin.manual-transaction.detail', $trx->id) }}" class="text-slate-400 hover:text-indigo-600 transition-colors">
                                    <i class='bx bx-chevron-right text-xl'></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                <i class='bx bx-search-alt text-4xl mb-2'></i>
                                <p>Tidak ada transaksi ditemukan</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-700">
            {{ $transactions->links() }}
        </div>
    </div>

    {{-- Modal Preview Bukti --}}
    <div id="proofModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm z-50 hidden items-center justify-center p-4" onclick="closeProof()">
        <div class="relative max-w-4xl w-full" onclick="event.stopPropagation()">
            <button onclick="closeProof()" class="absolute -top-10 right-0 text-white hover:text-rose-400 transition-colors">
                <i class='bx bx-x text-4xl'></i>
            </button>
            <img id="proofImage" src="" class="w-full h-auto max-h-[90vh] object-contain rounded-xl shadow-2xl">
        </div>
    </div>

    @push('scripts')
    <script>
        function showProof(url) {
            document.getElementById('proofImage').src = url;
            document.getElementById('proofModal').classList.remove('hidden');
            document.getElementById('proofModal').classList.add('flex');
        }

        function closeProof() {
            document.getElementById('proofModal').classList.add('hidden');
            document.getElementById('proofModal').classList.remove('flex');
        }

        // Close with ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeProof();
        });
    </script>
    @endpush
</div>
