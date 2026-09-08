<div x-data="{ showBalance: {{ $showBalance ? 'true' : 'false' }} }" class="space-y-6">
    @section('title', 'Simpanan Saya')

    {{-- Header Banner --}}
    <div class="bg-white/80 dark:bg-zinc-900/80 backdrop-blur-xl border border-zinc-200/80 dark:border-zinc-800 rounded-3xl p-6 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="p-2 rounded-xl bg-[#155A6B]/10 dark:bg-emerald-400/20 text-[#155A6B] dark:text-emerald-400 font-bold">
                    <i class='bx bxs-wallet text-xl'></i>
                </span>
                <h2 class="font-heading font-extrabold text-xl text-zinc-900 dark:text-white">Portofolio Simpanan Saya</h2>
            </div>
            <p class="text-xs text-zinc-500 dark:text-zinc-400">
                Akumulasi saldo simpanan Pokok, Wajib, dan Sukarela serta mutasi transaksi riil.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <button @click="showBalance = !showBalance; $wire.toggleBalance()" 
                class="px-4 py-2 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-200 rounded-xl text-xs font-bold transition-all flex items-center gap-2">
                <i class='bx text-base' :class="showBalance ? 'bx-hide' : 'bx-show'"></i>
                <span x-text="showBalance ? 'Sembunyikan Saldo' : 'Tampilkan Saldo'"></span>
            </button>
            <a href="{{ route('member.transfer') }}" class="px-4 py-2 bg-[#155A6B] hover:bg-[#0E3E4B] text-white rounded-xl text-xs font-bold shadow-md transition-all flex items-center gap-2">
                <i class='bx bx-transfer text-base'></i> Transfer Sukarela
            </a>
        </div>
    </div>

    {{-- Settlement Receipt PDF Card for Resigned/Settled Member --}}
    @if(optional($member->settlement)->id)
        <div class="p-6 rounded-3xl bg-gradient-to-r from-[#155A6B] to-emerald-600 text-white shadow-xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border border-emerald-400/30">
            <div>
                <span class="px-3 py-0.5 rounded-full text-[10px] font-extrabold bg-white/20 uppercase tracking-wider text-emerald-100 backdrop-blur-md">Pencairan & Pengembalian Simpanan</span>
                <h3 class="font-heading font-extrabold text-lg mt-1 text-white">Berita Acara Pengembalian Simpanan</h3>
                <p class="text-xs text-emerald-100 mt-0.5">Total Pengembalian Bersih: <strong class="font-mono text-white text-sm">Rp {{ number_format($member->settlement->net_refund_amount ?? 0, 0, ',', '.') }}</strong> ({{ optional($member->settlement->settled_at)->format('d M Y') ?? '-' }})</p>
            </div>
            <a href="{{ route('member.my-settlement-pdf') }}" target="_blank" class="px-4 py-2.5 bg-white text-[#155A6B] hover:bg-emerald-50 rounded-2xl text-xs font-extrabold shadow-md transition-all flex items-center gap-2 shrink-0">
                <i class='bx bxs-file-pdf text-lg text-rose-600'></i> Unduh Dokumen PDF
            </a>
        </div>
    @endif

    {{-- 4-Bento Metric Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @if($member->isMemberKoperasi)
        {{-- Simpanan Pokok --}}
        <div class="bg-white/80 dark:bg-zinc-900/80 backdrop-blur-xl p-5 rounded-3xl border border-zinc-200/80 dark:border-zinc-800 shadow-sm">
            <div class="flex items-center gap-3.5">
                <div class="w-11 h-11 bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 rounded-2xl flex items-center justify-center shrink-0 text-xl">
                    <i class='bx bxs-lock-alt'></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] font-extrabold text-zinc-400 uppercase tracking-wider">Simpanan Pokok</p>
                    <h3 class="font-heading text-lg font-extrabold text-zinc-900 dark:text-white truncate">
                        <span x-show="showBalance">Rp {{ number_format($member->simpananPokok ?? 0, 0, ',', '.') }}</span>
                        <span x-show="!showBalance" style="display: none;">Rp ••••••</span>
                    </h3>
                </div>
            </div>
        </div>

        {{-- Simpanan Wajib --}}
        <div class="bg-white/80 dark:bg-zinc-900/80 backdrop-blur-xl p-5 rounded-3xl border border-zinc-200/80 dark:border-zinc-800 shadow-sm">
            <div class="flex items-center gap-3.5">
                <div class="w-11 h-11 bg-blue-500/10 dark:bg-blue-400/20 text-blue-600 dark:text-blue-400 rounded-2xl flex items-center justify-center shrink-0 text-xl">
                    <i class='bx bxs-calendar'></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] font-extrabold text-zinc-400 uppercase tracking-wider">Simpanan Wajib</p>
                    <h3 class="font-heading text-lg font-extrabold text-zinc-900 dark:text-white truncate">
                        <span x-show="showBalance">Rp {{ number_format($member->simpananWajib ?? 0, 0, ',', '.') }}</span>
                        <span x-show="!showBalance" style="display: none;">Rp ••••••</span>
                    </h3>
                </div>
            </div>
        </div>
        @endif

        {{-- Simpanan Sukarela --}}
        <div class="bg-white/80 dark:bg-zinc-900/80 backdrop-blur-xl p-5 rounded-3xl border border-zinc-200/80 dark:border-zinc-800 shadow-sm">
            <div class="flex items-center gap-3.5">
                <div class="w-11 h-11 bg-emerald-500/10 dark:bg-emerald-400/20 text-emerald-600 dark:text-emerald-400 rounded-2xl flex items-center justify-center shrink-0 text-xl">
                    <i class='bx bxs-bank'></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] font-extrabold text-zinc-400 uppercase tracking-wider">Simpanan Sukarela</p>
                    <h3 class="font-heading text-lg font-extrabold text-zinc-900 dark:text-white truncate">
                        <span x-show="showBalance">Rp {{ number_format($member->simpananSukarela ?? 0, 0, ',', '.') }}</span>
                        <span x-show="!showBalance" style="display: none;">Rp ••••••</span>
                    </h3>
                </div>
            </div>
        </div>

        {{-- Total Simpanan Card --}}
        @php
            $totalSimpanan = ($member->simpananPokok ?? 0) + ($member->simpananWajib ?? 0) + ($member->simpananSukarela ?? 0);
        @endphp
        <div class="bg-gradient-to-br from-[#155A6B] to-emerald-600 p-5 rounded-3xl shadow-lg text-white border border-emerald-400/30">
            <div class="flex items-center gap-3.5">
                <div class="w-11 h-11 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center shrink-0 text-xl text-white">
                    <i class='bx bxs-wallet'></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] font-extrabold text-emerald-100 uppercase tracking-wider">Total Simpanan</p>
                    <h3 class="font-heading text-lg font-extrabold truncate text-white">
                        <span x-show="showBalance">Rp {{ number_format($totalSimpanan, 0, ',', '.') }}</span>
                        <span x-show="!showBalance" style="display: none;">Rp ••••••</span>
                    </h3>
                </div>
            </div>
        </div>
    </div>

    {{-- Kartu Kontrol Simpanan Wajib --}}
    @if($member->isMemberKoperasi && ($filterType === '' || $filterType === 'all' || $filterType === 'WAJIB'))
        <div class="bg-white/80 dark:bg-zinc-900/80 backdrop-blur-xl rounded-3xl border border-zinc-200/80 dark:border-zinc-800 p-6 shadow-sm">
            <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
                <div>
                    <h3 class="font-heading font-extrabold text-base text-zinc-900 dark:text-white flex items-center gap-2">
                        <i class='bx bx-calendar-check text-blue-500 text-xl'></i> Kartu Kontrol Setoran Simpanan Wajib
                    </h3>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">Status pelunasan simpanan wajib bulanan per periode tahun buku.</p>
                </div>
                
                {{-- Year Selector --}}
                <div class="flex items-center bg-zinc-100 dark:bg-zinc-800 rounded-2xl p-1 border border-zinc-200/60 dark:border-zinc-700/60">
                    <button wire:click="$set('selectedYear', {{ $selectedYear - 1 }})" class="w-8 h-8 flex items-center justify-center text-zinc-500 hover:text-zinc-900 dark:hover:text-white hover:bg-white dark:hover:bg-zinc-700 rounded-xl transition-all">
                        <i class='bx bx-chevron-left text-lg'></i>
                    </button>
                    <span class="px-4 font-mono font-extrabold text-xs text-zinc-900 dark:text-white">{{ $selectedYear }}</span>
                    <button wire:click="$set('selectedYear', {{ $selectedYear + 1 }})" class="w-8 h-8 flex items-center justify-center text-zinc-500 hover:text-zinc-900 dark:hover:text-white hover:bg-white dark:hover:bg-zinc-700 rounded-xl transition-all" {{ $selectedYear >= date('Y') ? 'disabled opacity-50' : '' }}>
                        <i class='bx bx-chevron-right text-lg'></i>
                    </button>
                </div>
            </div>

            {{-- Grid Status --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-6 gap-3">
                @foreach($this->simwaGrid as $month => $data)
                    @php
                        $colorClass = match($data['status']) {
                            'PAID' => 'bg-emerald-500/10 border-emerald-500/30 text-emerald-600 dark:text-emerald-400',
                            'UNPAID' => 'bg-rose-500/10 border-rose-500/30 text-rose-600 dark:text-rose-400',
                            'FUTURE' => 'bg-zinc-100/80 border-zinc-200 text-zinc-400 dark:bg-zinc-800/40 dark:border-zinc-800 dark:text-zinc-500',
                            'NOT_MEMBER' => 'bg-zinc-50 border-zinc-100 text-zinc-300 dark:bg-zinc-800/20 dark:border-zinc-800 dark:text-zinc-600',
                        };
                        
                        $iconClass = match($data['status']) {
                            'PAID' => 'bx-check-circle',
                            'UNPAID' => 'bx-x-circle',
                            'FUTURE' => 'bx-time',
                            'NOT_MEMBER' => 'bx-block',
                        };
                    @endphp
                    <div class="border rounded-2xl p-3 flex flex-col items-center justify-center text-center transition-all {{ $colorClass }}">
                        <span class="text-[10px] font-extrabold uppercase tracking-wider mb-1">{{ $data['monthName'] }}</span>
                        <i class='bx {{ $iconClass }} text-2xl mb-1'></i>
                        <span class="text-[9px] font-extrabold">
                            @if($data['status'] === 'PAID') LUNAS
                            @elseif($data['status'] === 'UNPAID') BELUM
                            @elseif($data['status'] === 'FUTURE') -
                            @else N/A @endif
                        </span>
                    </div>
                @endforeach
            </div>

            {{-- Legend --}}
            <div class="flex gap-6 mt-6 justify-center flex-wrap">
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full bg-emerald-500"></div>
                    <span class="text-[11px] font-semibold text-zinc-500">Lunas</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full bg-rose-500"></div>
                    <span class="text-[11px] font-semibold text-zinc-500">Belum Bayar</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full bg-zinc-300 dark:bg-zinc-700"></div>
                    <span class="text-[11px] font-semibold text-zinc-500">Belum Waktunya</span>
                </div>
            </div>
        </div>
    @endif

    {{-- Filter Tabs --}}
    <div class="bg-white/80 dark:bg-zinc-900/80 backdrop-blur-xl rounded-2xl border border-zinc-200/80 dark:border-zinc-800 p-1.5 flex items-center gap-2 overflow-x-auto">
        <button wire:click="$set('filterType', 'all')" class="px-4 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ $filterType === 'all' ? 'bg-[#155A6B] text-white shadow-sm' : 'text-zinc-500 hover:text-zinc-900 dark:hover:text-white' }}">
            Semua Jenis
        </button>
        @if($member->isMemberKoperasi)
        <button wire:click="$set('filterType', 'POKOK')" class="px-4 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ $filterType === 'POKOK' ? 'bg-[#155A6B] text-white shadow-sm' : 'text-zinc-500 hover:text-zinc-900 dark:hover:text-white' }}">
            Simpanan Pokok
        </button>
        <button wire:click="$set('filterType', 'WAJIB')" class="px-4 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ $filterType === 'WAJIB' ? 'bg-[#155A6B] text-white shadow-sm' : 'text-zinc-500 hover:text-zinc-900 dark:hover:text-white' }}">
            Simpanan Wajib
        </button>
        @endif
        <button wire:click="$set('filterType', 'SUKARELA')" class="px-4 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ $filterType === 'SUKARELA' ? 'bg-[#155A6B] text-white shadow-sm' : 'text-zinc-500 hover:text-zinc-900 dark:hover:text-white' }}">
            Simpanan Sukarela
        </button>
    </div>

    {{-- Mutations History Table --}}
    <div class="bg-white/80 dark:bg-zinc-900/80 backdrop-blur-xl rounded-3xl border border-zinc-200/80 dark:border-zinc-800 overflow-hidden shadow-sm">
        @if($simpanan->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-zinc-50/70 dark:bg-zinc-800/50 border-b border-zinc-200/80 dark:border-zinc-800">
                        <tr class="text-[10px] font-extrabold text-zinc-400 uppercase tracking-wider">
                            <th class="px-6 py-3.5">Tanggal</th>
                            <th class="px-6 py-3.5">Jenis & Tipe Mutasi</th>
                            <th class="px-6 py-3.5 text-right">Nominal</th>
                            <th class="px-6 py-3.5 text-right">Saldo Akhir</th>
                            <th class="px-6 py-3.5 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200/60 dark:divide-zinc-800 text-xs">
                        @foreach($simpanan as $item)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/40 transition-colors cursor-pointer" 
                                wire:click="viewReceipt({{ $item->id }})">
                                <td class="px-6 py-4">
                                    <span class="font-bold text-zinc-900 dark:text-white">{{ $item->created_at->format('d M Y') }}</span>
                                    <p class="text-[10px] text-zinc-400 font-mono">{{ $item->created_at->format('H:i') }} WIB</p>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <span class="px-2.5 py-0.5 text-[10px] font-extrabold rounded-full uppercase tracking-wider
                                            {{ $item->type === 'POKOK' ? 'bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300' : '' }}
                                            {{ $item->type === 'WAJIB' ? 'bg-blue-500/10 text-blue-600 dark:text-blue-400' : '' }}
                                            {{ $item->type === 'SUKARELA' ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' : '' }}
                                        ">
                                            {{ $item->type }}
                                        </span>
                                        <span class="font-bold 
                                            @if($item->transactionType === 'SETOR' || $item->transactionType === 'TRANSFER_IN') 
                                                text-emerald-600 dark:text-emerald-400 
                                            @else 
                                                text-rose-500 
                                            @endif
                                        ">
                                            @switch($item->transactionType)
                                                @case('SETOR') Setoran @break
                                                @case('TARIK') Penarikan @break
                                                @case('TRANSFER_IN') Transfer Masuk @break
                                                @case('TRANSFER_OUT') Transfer Keluar @break
                                                @default {{ $item->transactionType }}
                                            @endswitch
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="font-mono font-extrabold text-sm
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
                                    <span class="font-mono font-extrabold text-zinc-900 dark:text-white">Rp {{ number_format($item->balanceAfter, 0, ',', '.') }}</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-2.5 py-0.5 text-[10px] font-extrabold rounded-full uppercase tracking-wider {{ $item->status === 'APPROVED' ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' : ($item->status === 'PENDING' ? 'bg-amber-500/10 text-amber-600' : 'bg-rose-500/10 text-rose-600') }}">
                                        {{ $item->status === 'APPROVED' ? 'Lunas' : $item->status }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-zinc-200/80 dark:border-zinc-800">
                {{ $simpanan->links() }}
            </div>
        @else
            <div class="p-12 text-center">
                <div class="w-14 h-14 bg-zinc-100 dark:bg-zinc-800 rounded-full mx-auto mb-3 flex items-center justify-center text-zinc-400 text-2xl">
                    <i class='bx bx-wallet'></i>
                </div>
                <h4 class="font-heading font-extrabold text-base text-zinc-900 dark:text-white mb-1">Belum Ada Mutasi Simpanan</h4>
                <p class="text-xs text-zinc-500">Riwayat transaksi simpanan Anda akan tercatat di sini.</p>
            </div>
        @endif
    </div>

    {{-- Receipt Modal --}}
    @if($showReceiptModal && $selectedTransfer)
        <div class="fixed inset-0 bg-black/60 backdrop-blur-md z-50 flex items-center justify-center p-4 overflow-y-auto" wire:click="closeReceipt">
            <div class="bg-white dark:bg-zinc-900 rounded-3xl shadow-2xl max-w-md w-full my-8 border border-zinc-200 dark:border-zinc-800" wire:click.stop>
                {{-- Header --}}
                <div class="bg-gradient-to-r from-[#155A6B] to-emerald-600 p-6 rounded-t-3xl text-white relative overflow-hidden">
                    <div class="relative z-10 text-center">
                        <div class="w-14 h-14 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center mx-auto mb-3 text-white text-3xl shadow-sm">
                            <i class='bx bx-check'></i>
                        </div>
                        <h3 class="font-heading font-extrabold text-xl">
                            @if(in_array($selectedTransfer->transactionType, ['TRANSFER_IN', 'TRANSFER_OUT']))
                                Transfer Berhasil
                            @elseif($selectedTransfer->transactionType === 'SETOR')
                                Setoran Berhasil
                            @else
                                Penarikan Berhasil
                            @endif
                        </h3>
                        <p class="text-emerald-100 text-xs mt-0.5">Bukti Transaksi Resmi Koperasi</p>
                    </div>
                </div>

                {{-- Content --}}
                <div class="p-6 space-y-4">
                    <div class="bg-zinc-50 dark:bg-zinc-800/50 rounded-2xl p-4 text-center border border-zinc-200/50 dark:border-zinc-700/50">
                        <p class="text-[10px] text-zinc-400 uppercase font-extrabold tracking-wider mb-1">Nominal Transaksi</p>
                        <p class="font-mono text-2xl font-extrabold text-zinc-900 dark:text-white">
                            Rp {{ number_format($selectedTransfer->amount, 0, ',', '.') }}
                        </p>
                    </div>

                    <div class="space-y-2.5 text-xs">
                        <div class="flex justify-between py-1.5 border-b border-zinc-100 dark:border-zinc-800">
                            <span class="text-zinc-500">Tanggal & Waktu</span>
                            <span class="font-bold text-zinc-900 dark:text-white">{{ $selectedTransfer->created_at->format('d M Y, H:i') }} WIB</span>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-zinc-100 dark:border-zinc-800">
                            <span class="text-zinc-500">Jenis Simpanan</span>
                            <span class="font-bold text-zinc-900 dark:text-white">Simpanan {{ ucfirst(strtolower($selectedTransfer->type)) }}</span>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-zinc-100 dark:border-zinc-800">
                            <span class="text-zinc-500">Saldo Akhir</span>
                            <span class="font-mono font-bold text-emerald-600 dark:text-emerald-400">Rp {{ number_format($selectedTransfer->balanceAfter, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <div class="p-6 border-t border-zinc-100 dark:border-zinc-800 flex gap-3">
                    <button wire:click="closeReceipt" class="flex-1 py-3 bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 font-extrabold rounded-2xl text-xs">
                        Tutup
                    </button>
                    <button onclick="window.print()" class="flex-1 py-3 bg-[#155A6B] text-white font-extrabold rounded-2xl text-xs shadow-md flex items-center justify-center gap-1.5">
                        <i class='bx bx-printer text-base'></i> Cetak
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
