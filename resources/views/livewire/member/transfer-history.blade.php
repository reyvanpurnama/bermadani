<div>
    @section('page-title', 'Riwayat Transfer')

    {{-- Header & Filters --}}
    <div class="bg-white dark:bg-darkCard rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 p-6 mb-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
            <div>
                <h2 class="text-xl font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <i class='bx bx-history text-primary'></i> Riwayat Transfer
                </h2>
                <p class="text-sm text-slate-500 mt-1">Semua transaksi transfer simpanan sukarela</p>
            </div>
            <div class="flex gap-2">
                <button wire:click="setFilter('')"
                    class="px-4 py-2 rounded-lg text-sm font-bold transition-colors {{ $filterType === '' ? 'bg-primary text-white' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-600' }}">
                    Semua
                </button>
                <button wire:click="setFilter('TRANSFER_OUT')"
                    class="px-4 py-2 rounded-lg text-sm font-bold transition-colors {{ $filterType === 'TRANSFER_OUT' ? 'bg-rose-500 text-white' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-600' }}">
                    Keluar
                </button>
                <button wire:click="setFilter('TRANSFER_IN')"
                    class="px-4 py-2 rounded-lg text-sm font-bold transition-colors {{ $filterType === 'TRANSFER_IN' ? 'bg-emerald-500 text-white' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-600' }}">
                    Masuk
                </button>
            </div>
        </div>

        {{-- Search --}}
        <div class="relative">
            <i class='bx bx-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xl'></i>
            <input type="text" wire:model.live.debounce.300ms="search" 
                placeholder="Cari nama, nomor anggota, atau referensi transfer..."
                class="w-full pl-12 pr-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
        </div>
    </div>

    {{-- Transfer List --}}
    <div class="bg-white dark:bg-darkCard rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">
        @if($transfers->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Tipe</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Dari/Ke</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Nominal</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Referensi</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @foreach($transfers as $transfer)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors cursor-pointer" wire:click="viewReceipt({{ $transfer->id }})">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-slate-900 dark:text-white">
                                        {{ $transfer->created_at->format('d M Y') }}
                                    </div>
                                    <div class="text-xs text-slate-500">
                                        {{ $transfer->created_at->format('H:i') }} WIB
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($transfer->transactionType === 'TRANSFER_OUT')
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-rose-50 dark:bg-rose-900/20 text-rose-600 dark:text-rose-400 rounded-full text-xs font-bold">
                                            <i class='bx bx-up-arrow-alt'></i> Keluar
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 rounded-full text-xs font-bold">
                                            <i class='bx bx-down-arrow-alt'></i> Masuk
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-bold text-slate-900 dark:text-white">
                                        {{ $transfer->relatedMember->name ?? 'Member' }}
                                    </div>
                                    <div class="text-xs text-slate-500">
                                        {{ $transfer->relatedMember->nomorAnggota ?? '-' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="text-sm font-bold {{ $transfer->transactionType === 'TRANSFER_IN' ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                                        {{ $transfer->transactionType === 'TRANSFER_IN' ? '+' : '-' }} Rp {{ number_format($transfer->amount, 0, ',', '.') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-xs font-mono text-slate-600 dark:text-slate-400">
                                        {{ $transfer->transferReference }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <button wire:click.stop="viewReceipt({{ $transfer->id }})"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 bg-primary hover:bg-blue-700 text-white rounded-lg text-xs font-bold transition-colors">
                                        <i class='bx bx-receipt'></i> Lihat Struk
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-700">
                {{ $transfers->links() }}
            </div>
        @else
            <div class="p-12 text-center">
                <div class="w-20 h-20 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class='bx bx-transfer text-4xl text-slate-400'></i>
                </div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">Belum Ada Riwayat Transfer</h3>
                <p class="text-sm text-slate-500 mb-6">Transfer pertama kamu akan muncul di sini</p>
                <a href="{{ route('member.transfer') }}" 
                    class="inline-flex items-center gap-2 px-6 py-3 bg-primary hover:bg-blue-700 text-white rounded-xl font-bold transition-colors">
                    <i class='bx bx-plus-circle'></i> Mulai Transfer
                </a>
            </div>
        @endif
    </div>

    {{-- Receipt Modal --}}
    @if($showReceiptModal && $selectedTransfer)
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4" wire:click="closeReceipt">
            <div class="bg-white dark:bg-darkCard rounded-2xl shadow-2xl max-w-md w-full" wire:click.stop>
                {{-- Header --}}
                <div class="bg-gradient-to-r from-primary to-blue-600 p-6 rounded-t-2xl text-white relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full blur-3xl -mr-10 -mt-10"></div>
                    <div class="relative z-10 text-center">
                        <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class='bx bx-check text-4xl'></i>
                        </div>
                        <h3 class="text-xl font-bold">Transfer Berhasil</h3>
                        <p class="text-blue-100 text-sm mt-1">Transaksi telah diproses</p>
                    </div>
                </div>

                {{-- Content --}}
                <div class="p-6 space-y-4">
                    {{-- Reference Number --}}
                    <div class="bg-slate-50 dark:bg-slate-800 rounded-xl p-4 text-center">
                        <p class="text-xs text-slate-500 mb-1">Nomor Referensi</p>
                        <p class="text-lg font-mono font-bold text-slate-900 dark:text-white">{{ $selectedTransfer->transferReference }}</p>
                    </div>

                    {{-- Details --}}
                    <div class="space-y-3">
                        <div class="flex justify-between items-start py-2 border-b border-slate-100 dark:border-slate-700">
                            <span class="text-sm text-slate-500">Tanggal & Waktu</span>
                            <span class="text-sm font-bold text-slate-900 dark:text-white text-right">
                                {{ $selectedTransfer->created_at->format('d M Y, H:i') }} WIB
                            </span>
                        </div>

                        <div class="flex justify-between items-start py-2 border-b border-slate-100 dark:border-slate-700">
                            <span class="text-sm text-slate-500">Tipe Transfer</span>
                            <span class="text-sm font-bold {{ $selectedTransfer->transactionType === 'TRANSFER_IN' ? 'text-emerald-600' : 'text-rose-600' }}">
                                {{ $selectedTransfer->transactionType === 'TRANSFER_IN' ? 'Transfer Masuk' : 'Transfer Keluar' }}
                            </span>
                        </div>

                        @if($selectedTransfer->transactionType === 'TRANSFER_OUT')
                            <div class="flex justify-between items-start py-2 border-b border-slate-100 dark:border-slate-700">
                                <span class="text-sm text-slate-500">Pengirim</span>
                                <div class="text-right">
                                    <p class="text-sm font-bold text-slate-900 dark:text-white">{{ $member->name }}</p>
                                    <p class="text-xs text-slate-500">{{ $member->nomorAnggota }}</p>
                                </div>
                            </div>
                            <div class="flex justify-between items-start py-2 border-b border-slate-100 dark:border-slate-700">
                                <span class="text-sm text-slate-500">Penerima</span>
                                <div class="text-right">
                                    <p class="text-sm font-bold text-slate-900 dark:text-white">{{ $selectedTransfer->relatedMember->name ?? 'Member' }}</p>
                                    <p class="text-xs text-slate-500">{{ $selectedTransfer->relatedMember->nomorAnggota ?? '-' }}</p>
                                </div>
                            </div>
                        @else
                            <div class="flex justify-between items-start py-2 border-b border-slate-100 dark:border-slate-700">
                                <span class="text-sm text-slate-500">Pengirim</span>
                                <div class="text-right">
                                    <p class="text-sm font-bold text-slate-900 dark:text-white">{{ $selectedTransfer->relatedMember->name ?? 'Member' }}</p>
                                    <p class="text-xs text-slate-500">{{ $selectedTransfer->relatedMember->nomorAnggota ?? '-' }}</p>
                                </div>
                            </div>
                            <div class="flex justify-between items-start py-2 border-b border-slate-100 dark:border-slate-700">
                                <span class="text-sm text-slate-500">Penerima</span>
                                <div class="text-right">
                                    <p class="text-sm font-bold text-slate-900 dark:text-white">{{ $member->name }}</p>
                                    <p class="text-xs text-slate-500">{{ $member->nomorAnggota }}</p>
                                </div>
                            </div>
                        @endif

                        <div class="flex justify-between items-start py-2 border-b border-slate-100 dark:border-slate-700">
                            <span class="text-sm text-slate-500">Nominal</span>
                            <span class="text-lg font-bold text-slate-900 dark:text-white">Rp {{ number_format($selectedTransfer->amount, 0, ',', '.') }}</span>
                        </div>

                        <div class="flex justify-between items-start py-2 border-b border-slate-100 dark:border-slate-700">
                            <span class="text-sm text-slate-500">Biaya Admin</span>
                            <span class="text-sm font-bold text-emerald-600">GRATIS</span>
                        </div>

                        @if($selectedTransfer->notes)
                            <div class="py-2">
                                <p class="text-xs text-slate-500 mb-1">Catatan</p>
                                <p class="text-sm text-slate-900 dark:text-white">{{ $selectedTransfer->notes }}</p>
                            </div>
                        @endif

                        <div class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-xl p-3 mt-4">
                            <div class="flex items-start gap-2">
                                <i class='bx bx-check-circle text-emerald-600 text-xl flex-shrink-0'></i>
                                <p class="text-xs text-emerald-700 dark:text-emerald-400">Transfer berhasil diproses dan saldo telah diperbarui</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="p-6 border-t border-slate-100 dark:border-slate-700 flex gap-3">
                    <button wire:click="closeReceipt"
                        class="flex-1 py-3 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 rounded-xl font-bold transition-colors">
                        Tutup
                    </button>
                    <button onclick="window.print()"
                        class="flex-1 py-3 bg-primary hover:bg-blue-700 text-white rounded-xl font-bold transition-colors flex items-center justify-center gap-2">
                        <i class='bx bx-printer'></i> Cetak
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

