<div>
    @section('page-title', 'Simpanan Saya')

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white dark:bg-darkCard p-5 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-xl flex items-center justify-center">
                    <i class='bx bxs-lock-alt text-2xl'></i>
                </div>
                <div>
                    <p class="text-[11px] text-slate-500 uppercase tracking-wider">S. Pokok</p>
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white">Rp {{ number_format($member->simpananPokok ?? 0, 0, ',', '.') }}</h3>
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
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white">Rp {{ number_format($member->simpananWajib ?? 0, 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-darkCard p-5 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 rounded-xl flex items-center justify-center">
                    <i class='bx bxs-bank text-2xl'></i>
                </div>
                <div>
                    <p class="text-[11px] text-slate-500 uppercase tracking-wider">S. Sukarela</p>
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white">Rp {{ number_format($member->simpananSukarela ?? 0, 0, ',', '.') }}</h3>
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
                    <h3 class="text-lg font-bold">Rp {{ number_format($totalSimpanan, 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Tabs --}}
    <div class="bg-white dark:bg-darkCard rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 mb-6">
        <div class="border-b border-slate-100 dark:border-slate-700 px-4">
            <nav class="flex gap-4 overflow-x-auto" aria-label="Tabs">
                <button wire:click="$set('filterType', 'all')" class="py-4 px-2 text-sm font-medium border-b-2 transition-colors whitespace-nowrap {{ $filterType === 'all' ? 'border-primary text-primary' : 'border-transparent text-slate-500 hover:text-slate-700' }}">
                    Semua
                </button>
                <button wire:click="$set('filterType', 'POKOK')" class="py-4 px-2 text-sm font-medium border-b-2 transition-colors whitespace-nowrap {{ $filterType === 'POKOK' ? 'border-primary text-primary' : 'border-transparent text-slate-500 hover:text-slate-700' }}">
                    S. Pokok
                </button>
                <button wire:click="$set('filterType', 'WAJIB')" class="py-4 px-2 text-sm font-medium border-b-2 transition-colors whitespace-nowrap {{ $filterType === 'WAJIB' ? 'border-primary text-primary' : 'border-transparent text-slate-500 hover:text-slate-700' }}">
                    S. Wajib
                </button>
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
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
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
                                        <span class="text-sm {{ $item->transactionType === 'SETOR' ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-500' }}">
                                            {{ $item->transactionType === 'SETOR' ? 'Setoran' : 'Penarikan' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="text-sm font-bold {{ $item->transactionType === 'SETOR' ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-500' }}">
                                        {{ $item->transactionType === 'SETOR' ? '+' : '-' }}Rp {{ number_format($item->amount, 0, ',', '.') }}
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
</div>
