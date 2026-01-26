<div>
    @section('page-title', 'Simpanan Saya')

    {{-- Summary Cards with Hide/Unhide --}}
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-bold text-slate-900 dark:text-white">Ringkasan Simpanan</h2>
        <button wire:click="toggleBalance" class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg transition-colors" title="{{ $showBalance ? 'Sembunyikan Saldo' : 'Tampilkan Saldo' }}">
            <i class='bx {{ $showBalance ? "bx-hide" : "bx-show" }} text-xl'></i>
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 {{ $member->isMemberKoperasi ? 'lg:grid-cols-4' : 'lg:grid-cols-2' }} gap-6 mb-8">
        @if($member->isMemberKoperasi)
        <div class="bg-white dark:bg-darkCard p-5 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-xl flex items-center justify-center">
                    <i class='bx bxs-lock-alt text-2xl'></i>
                </div>
                <div>
                    <p class="text-[11px] text-slate-500 uppercase tracking-wider">S. Pokok</p>
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white">
                        @if($showBalance) Rp {{ number_format($member->simpananPokok ?? 0, 0, ',', '.') }} @else Rp •••••• @endif
                    </h3>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-darkCard p-5 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 text-blue-600 rounded-xl flex items-center justify-center">
                    <i class='bx bxs-calendar text-2xl'></i>
                </div>
                <div>
                    <p class="text-[11px] text-slate-500 uppercase tracking-wider">S. Wajib</p>
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white">
                        @if($showBalance) Rp {{ number_format($member->simpananWajib ?? 0, 0, ',', '.') }} @else Rp •••••• @endif
                    </h3>
                </div>
            </div>
        </div>
        @endif

        <div class="bg-white dark:bg-darkCard p-5 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 rounded-xl flex items-center justify-center">
                    <i class='bx bxs-bank text-2xl'></i>
                </div>
                <div>
                    <p class="text-[11px] text-slate-500 uppercase tracking-wider">S. Sukarela</p>
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white">
                        @if($showBalance) Rp {{ number_format($member->simpananSukarela ?? 0, 0, ',', '.') }} @else Rp •••••• @endif
                    </h3>
                </div>
            </div>
        </div>
        @php
            $totalSimpanan = ($member->simpananPokok ?? 0) + ($member->simpananWajib ?? 0) + ($member->simpananSukarela ?? 0);
        @endphp
        <div class="bg-gradient-to-br from-primary to-blue-700 p-5 rounded-2xl shadow-lg text-white">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                    <i class='bx bxs-wallet text-2xl'></i>
                </div>
                <div>
                    <p class="text-[11px] text-blue-100 uppercase tracking-wider">Total Aset</p>
                    <h3 class="text-lg font-bold">
                        @if($showBalance) Rp {{ number_format($totalSimpanan, 0, ',', '.') }} @else Rp •••••• @endif
                    </h3>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Transfer Button --}}
    <div class="mb-6">
        <a href="{{ route('member.transfer') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-emerald-500 hover:bg-emerald-600 text-white font-bold rounded-xl transition-colors">
            <i class='bx bx-transfer text-xl'></i>
            <span>Transfer Simpanan Sukarela</span>
        </a>
    </div>

    {{-- Filter Tabs --}}
    <div class="bg-white dark:bg-darkCard rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 mb-6">
        <div class="border-b border-slate-100 dark:border-slate-700 px-4">
            <nav class="flex gap-4 overflow-x-auto" aria-label="Tabs">
                <button wire:click="$set('filterType', 'all')" class="py-4 px-2 text-sm font-medium border-b-2 transition-colors whitespace-nowrap {{ $filterType === 'all' ? 'border-primary text-primary' : 'border-transparent text-slate-500 hover:text-slate-700' }}">
                    Semua
                </button>
                @if($member->isMemberKoperasi)
                <button wire:click="$set('filterType', 'POKOK')" class="py-4 px-2 text-sm font-medium border-b-2 transition-colors whitespace-nowrap {{ $filterType === 'POKOK' ? 'border-primary text-primary' : 'border-transparent text-slate-500 hover:text-slate-700' }}">
                    S. Pokok
                </button>
                <button wire:click="$set('filterType', 'WAJIB')" class="py-4 px-2 text-sm font-medium border-b-2 transition-colors whitespace-nowrap {{ $filterType === 'WAJIB' ? 'border-primary text-primary' : 'border-transparent text-slate-500 hover:text-slate-700' }}">
                    S. Wajib
                </button>
                @endif
                <button wire:click="$set('filterType', 'SUKARELA')" class="py-4 px-2 text-sm font-medium border-b-2 transition-colors whitespace-nowrap {{ $filterType === 'SUKARELA' ? 'border-primary text-primary' : 'border-transparent text-slate-500 hover:text-slate-700' }}">
                    S. Sukarela
                </button>
            </nav>
        </div>
    </div>

    {{-- Simpanan List --}}
    <div class="bg-white dark:bg-darkCard rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">
        @if($simpanan->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 dark:bg-slate-800">
                        <tr class="text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                            <th class="px-6 py-4">Tanggal</th>
                            <th class="px-6 py-4">Jenis & Tipe</th>
                            <th class="px-6 py-4 text-right">Nominal</th>
                            <th class="px-6 py-4 text-right">Saldo Akhir</th>
                            <th class="px-6 py-4 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @foreach($simpanan as $item)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors cursor-pointer" 
                                wire:click="viewReceipt({{ $item->id }})"
                            >
                                <td class="px-6 py-4">
                                    <span class="text-sm text-slate-800 dark:text-white">{{ $item->created_at->format('d M Y') }}</span>
                                    <p class="text-[10px] text-slate-400">{{ $item->created_at->format('H:i') }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-bold rounded-full
                                            {{ $item->type === 'POKOK' ? 'bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300' : '' }}
                                            {{ $item->type === 'WAJIB' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' : '' }}
                                            {{ $item->type === 'SUKARELA' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : '' }}
                                        ">
                                            {{ $item->type }}
                                        </span>
                                        <span class="text-sm 
                                            @if($item->transactionType === 'SETOR' || $item->transactionType === 'TRANSFER_IN') 
                                                text-emerald-600 dark:text-emerald-400 
                                            @else 
                                                text-rose-500 
                                            @endif
                                        ">
                                            @switch($item->transactionType)
                                                @case('SETOR') Setoran @break
                                                @case('TARIK') Penarikan @break
                                                @case('TRANSFER_IN') 
                                                    <i class='bx bx-down-arrow-alt'></i> Transfer Masuk
                                                    @if($item->relatedMember)
                                                        <span class="text-[10px] text-slate-400 block">dari {{ $item->relatedMember->name }}</span>
                                                    @endif
                                                    @break
                                                @case('TRANSFER_OUT') 
                                                    <i class='bx bx-up-arrow-alt'></i> Transfer Keluar
                                                    @if($item->relatedMember)
                                                        <span class="text-[10px] text-slate-400 block">ke {{ $item->relatedMember->name }}</span>
                                                    @endif
                                                    @break
                                                @default {{ $item->transactionType }}
                                            @endswitch
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="text-sm font-bold 
                                        @if($item->transactionType === 'SETOR' || $item->transactionType === 'TRANSFER_IN')
                                            text-emerald-600 dark:text-emerald-400
                                        @else
                                            text-rose-500
                                        @endif
                                    ">
                                        {{ in_array($item->transactionType, ['SETOR', 'TRANSFER_IN']) ? '+' : '-' }}Rp {{ number_format($item->amount, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="text-sm font-bold text-slate-800 dark:text-white">Rp {{ number_format($item->balanceAfter, 0, ',', '.') }}</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-bold rounded-full {{ $item->status === 'APPROVED' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : ($item->status === 'PENDING' ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-600' : 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400') }}">
                                        {{ $item->status === 'APPROVED' ? 'Lunas' : $item->status }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-700">
                {{ $simpanan->links() }}
            </div>
        @else
            <div class="p-12 text-center">
                <div class="w-16 h-16 bg-slate-100 dark:bg-slate-700 rounded-full mx-auto mb-4 flex items-center justify-center">
                    <i class='bx bx-wallet text-3xl text-slate-400'></i>
                </div>
                <h4 class="text-lg font-bold text-slate-800 dark:text-white mb-2">Belum Ada Simpanan</h4>
                <p class="text-sm text-slate-500">Riwayat simpanan Anda akan muncul di sini</p>
            </div>
        @endif
    </div>

    {{-- Receipt Modal for All Transactions --}}
    @if($showReceiptModal && $selectedTransfer)
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 overflow-y-auto" wire:click="closeReceipt">
            <div class="bg-white dark:bg-darkCard rounded-2xl shadow-2xl max-w-md w-full my-8" wire:click.stop>
                {{-- Header --}}
                <div class="bg-gradient-to-r from-primary to-blue-600 p-4 sm:p-6 rounded-t-2xl text-white relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-24 h-24 sm:w-32 sm:h-32 bg-white/10 rounded-full blur-3xl -mr-10 -mt-10"></div>
                    <div class="relative z-10 text-center">
                        <div class="w-12 h-12 sm:w-16 sm:h-16 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center mx-auto mb-2 sm:mb-3">
                            <i class='bx bx-check text-3xl sm:text-4xl'></i>
                        </div>
                        <h3 class="text-lg sm:text-xl font-bold">
                            @if(in_array($selectedTransfer->transactionType, ['TRANSFER_IN', 'TRANSFER_OUT']))
                                Transfer Berhasil
                            @elseif($selectedTransfer->transactionType === 'SETOR')
                                Setoran Berhasil
                            @else
                                Penarikan Berhasil
                            @endif
                        </h3>
                        <p class="text-blue-100 text-xs sm:text-sm mt-1">Transaksi telah diproses</p>
                    </div>
                </div>

                {{-- Content --}}
                <div class="p-4 sm:p-6 space-y-3 sm:space-y-4 max-h-[60vh] sm:max-h-none overflow-y-auto">
                    {{-- Reference Number (for transfers only) --}}
                    @if(in_array($selectedTransfer->transactionType, ['TRANSFER_IN', 'TRANSFER_OUT']))
                        <div class="bg-slate-50 dark:bg-slate-800 rounded-xl p-3 sm:p-4 text-center">
                            <p class="text-xs text-slate-500 mb-1">Nomor Referensi</p>
                            <p class="text-base sm:text-lg font-mono font-bold text-slate-900 dark:text-white break-all">{{ $selectedTransfer->transferReference }}</p>
                        </div>
                    @endif

                    {{-- Details --}}
                    <div class="space-y-2 sm:space-y-3">
                        <div class="flex justify-between items-start gap-4 py-2 border-b border-slate-100 dark:border-slate-700">
                            <span class="text-xs sm:text-sm text-slate-500 flex-shrink-0">Tanggal & Waktu</span>
                            <span class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white text-right">
                                {{ $selectedTransfer->created_at->format('d M Y, H:i') }} WIB
                            </span>
                        </div>

                        <div class="flex justify-between items-start gap-4 py-2 border-b border-slate-100 dark:border-slate-700">
                            <span class="text-xs sm:text-sm text-slate-500 flex-shrink-0">Jenis Simpanan</span>
                            <span class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white text-right">
                                Simpanan {{ ucfirst(strtolower($selectedTransfer->type)) }}
                            </span>
                        </div>

                        <div class="flex justify-between items-start gap-4 py-2 border-b border-slate-100 dark:border-slate-700">
                            <span class="text-xs sm:text-sm text-slate-500 flex-shrink-0">Tipe Transaksi</span>
                            <span class="text-xs sm:text-sm font-bold 
                                @if($selectedTransfer->transactionType === 'TRANSFER_IN') text-emerald-600 
                                @elseif($selectedTransfer->transactionType === 'TRANSFER_OUT') text-rose-600 
                                @elseif($selectedTransfer->transactionType === 'SETOR') text-emerald-600
                                @else text-rose-600
                                @endif text-right">
                                @switch($selectedTransfer->transactionType)
                                    @case('SETOR') Setoran @break
                                    @case('TARIK') Penarikan @break
                                    @case('TRANSFER_IN') Transfer Masuk @break
                                    @case('TRANSFER_OUT') Transfer Keluar @break
                                @endswitch
                            </span>
                        </div>

                        {{-- Transfer Details --}}
                        @if($selectedTransfer->transactionType === 'TRANSFER_OUT')
                            <div class="flex justify-between items-start gap-4 py-2 border-b border-slate-100 dark:border-slate-700">
                                <span class="text-xs sm:text-sm text-slate-500 flex-shrink-0">Pengirim</span>
                                <div class="text-right">
                                    <p class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white">{{ $member->name }}</p>
                                    <p class="text-[10px] sm:text-xs text-slate-500">{{ $member->nomorAnggota }}</p>
                                </div>
                            </div>
                            <div class="flex justify-between items-start gap-4 py-2 border-b border-slate-100 dark:border-slate-700">
                                <span class="text-xs sm:text-sm text-slate-500 flex-shrink-0">Penerima</span>
                                <div class="text-right">
                                    <p class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white break-words">{{ $selectedTransfer->relatedMember->name ?? 'Member' }}</p>
                                    <p class="text-[10px] sm:text-xs text-slate-500">{{ $selectedTransfer->relatedMember->nomorAnggota ?? '-' }}</p>
                                </div>
                            </div>
                        @elseif($selectedTransfer->transactionType === 'TRANSFER_IN')
                            <div class="flex justify-between items-start gap-4 py-2 border-b border-slate-100 dark:border-slate-700">
                                <span class="text-xs sm:text-sm text-slate-500 flex-shrink-0">Pengirim</span>
                                <div class="text-right">
                                    <p class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white break-words">{{ $selectedTransfer->relatedMember->name ?? 'Member' }}</p>
                                    <p class="text-[10px] sm:text-xs text-slate-500">{{ $selectedTransfer->relatedMember->nomorAnggota ?? '-' }}</p>
                                </div>
                            </div>
                            <div class="flex justify-between items-start gap-4 py-2 border-b border-slate-100 dark:border-slate-700">
                                <span class="text-xs sm:text-sm text-slate-500 flex-shrink-0">Penerima</span>
                                <div class="text-right">
                                    <p class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white">{{ $member->name }}</p>
                                    <p class="text-[10px] sm:text-xs text-slate-500">{{ $member->nomorAnggota }}</p>
                                </div>
                            </div>
                        @endif

                        <div class="flex justify-between items-start gap-4 py-2 border-b border-slate-100 dark:border-slate-700">
                            <span class="text-xs sm:text-sm text-slate-500 flex-shrink-0">Nominal</span>
                            <span class="text-base sm:text-lg font-bold text-slate-900 dark:text-white text-right">Rp {{ number_format($selectedTransfer->amount, 0, ',', '.') }}</span>
                        </div>

                        @if(in_array($selectedTransfer->transactionType, ['TRANSFER_IN', 'TRANSFER_OUT']))
                            <div class="flex justify-between items-start gap-4 py-2 border-b border-slate-100 dark:border-slate-700">
                                <span class="text-xs sm:text-sm text-slate-500 flex-shrink-0">Biaya Admin</span>
                                <span class="text-xs sm:text-sm font-bold text-emerald-600">GRATIS</span>
                            </div>
                        @endif

                        <div class="flex justify-between items-start gap-4 py-2 border-b border-slate-100 dark:border-slate-700">
                            <span class="text-xs sm:text-sm text-slate-500 flex-shrink-0">Saldo Akhir</span>
                            <span class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white text-right">Rp {{ number_format($selectedTransfer->balanceAfter, 0, ',', '.') }}</span>
                        </div>

                        @if($selectedTransfer->notes)
                            <div class="py-2">
                                <p class="text-[10px] sm:text-xs text-slate-500 mb-1">Catatan</p>
                                <p class="text-xs sm:text-sm text-slate-900 dark:text-white break-words">{{ $selectedTransfer->notes }}</p>
                            </div>
                        @endif

                        <div class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-xl p-2.5 sm:p-3 mt-3 sm:mt-4">
                            <div class="flex items-start gap-2">
                                <i class='bx bx-check-circle text-emerald-600 text-lg sm:text-xl flex-shrink-0'></i>
                                <p class="text-[10px] sm:text-xs text-emerald-700 dark:text-emerald-400">
                                    @if(in_array($selectedTransfer->transactionType, ['TRANSFER_IN', 'TRANSFER_OUT']))
                                        Transfer berhasil diproses dan saldo telah diperbarui
                                    @elseif($selectedTransfer->transactionType === 'SETOR')
                                        Setoran berhasil dicatat dan saldo telah diperbarui
                                    @else
                                        Penarikan berhasil diproses dan saldo telah diperbarui
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="p-4 sm:p-6 border-t border-slate-100 dark:border-slate-700 flex flex-col sm:flex-row gap-2 sm:gap-3">
                    <button wire:click="closeReceipt"
                        class="w-full sm:flex-1 py-2.5 sm:py-3 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 rounded-xl text-sm font-bold transition-colors">
                        Tutup
                    </button>
                    <button onclick="window.print()"
                        class="w-full sm:flex-1 py-2.5 sm:py-3 bg-primary hover:bg-blue-700 text-white rounded-xl text-sm font-bold transition-colors flex items-center justify-center gap-2">
                        <i class='bx bx-printer'></i> Cetak
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
