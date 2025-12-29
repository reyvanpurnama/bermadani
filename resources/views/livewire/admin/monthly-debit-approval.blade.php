<div class="p-6">
    @if($viewMode === 'list')
        <!-- LIST VIEW: Yearly Overview -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div>
                <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Riwayat Auto-Debit</h2>
                <p class="text-slate-600 dark:text-slate-400 mt-1">Overview tagihan simpanan wajib per tahun</p>
            </div>
            
            <!-- Year Filter -->
            <div class="flex items-center gap-3">
                <label for="yearFilter" class="text-sm font-medium text-slate-600 dark:text-slate-400">Tahun:</label>
                <select wire:model.live="filterYear" id="yearFilter" 
                        class="bg-white dark:bg-darkCard border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white text-sm rounded-xl focus:ring-indigo-500 focus:border-indigo-500 block p-2.5 min-w-[100px]">
                    @for($y = date('Y'); $y >= 2020; $y--)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endfor
                </select>
            </div>
        </div>

        <!-- Months Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($this->monthlyHistory as $month)
                <div wire:click="selectMonth('{{ $month['date'] }}')" 
                     class="group relative bg-white dark:bg-darkCard rounded-2xl p-6 border border-slate-200 dark:border-slate-700 shadow-sm hover:shadow-md transition-all cursor-pointer hover:border-indigo-300 dark:hover:border-indigo-700">
                    
                    <!-- Status Badge -->
                    <div class="absolute top-4 right-4">
                        @if($month['status'] === 'COMPLETED')
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-500/20">
                                <i class='bx bx-check-double'></i> Lunas
                            </span>
                        @elseif($month['status'] === 'PENDING')
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-100 dark:border-amber-500/20">
                                <i class='bx bx-time'></i> Pending
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-600">
                                <i class='bx bx-minus'></i> Kosong
                            </span>
                        @endif
                    </div>

                    <!-- Month Name -->
                    <h3 class="text-xl font-bold text-slate-800 dark:text-white mb-1 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                        {{ $month['monthName'] }}
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 font-mono mb-4">{{ $month['date'] }}</p>

                    <!-- Stats -->
                    <div class="space-y-3">
                        <div class="flex justify-between items-end">
                            <span class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider font-bold">Total</span>
                            <span class="text-lg font-bold text-slate-800 dark:text-white">
                                Rp {{ number_format($month['totalAmount'], 0, ',', '.') }}
                            </span>
                        </div>
                        
                        @if($month['status'] !== 'EMPTY')
                            <div class="pt-3 border-t border-slate-100 dark:border-slate-700 flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
                                @if($month['status'] === 'COMPLETED')
                                    <i class='bx bx-check-circle text-emerald-500 text-sm'></i>
                                    @if($month['approver'])
                                        <span class="truncate">Approved by {{ Str::limit($month['approver'], 15) }}</span>
                                    @else
                                        <span>Selesai</span>
                                    @endif
                                @else
                                    <i class='bx bx-loader-circle text-amber-500 text-sm'></i>
                                    <span>Menunggu persetujuan</span>
                                @endif
                            </div>
                        @else
                            <div class="pt-3 border-t border-slate-100 dark:border-slate-700 text-xs text-slate-400 italic">
                                Belum ada data
                            </div>
                        @endif
                    </div>
                    
                    <!-- Hover Action -->
                    <div class="absolute bottom-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity">
                        <div class="w-8 h-8 bg-indigo-50 dark:bg-indigo-500/20 rounded-full flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                            <i class='bx bx-right-arrow-alt text-xl'></i>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    @else
        <!-- DETAIL VIEW: Single Month Management -->
        <!-- Header with Back Button -->
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center gap-4">
                <button wire:click="setViewMode('list')" class="p-2 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-xl text-slate-500 dark:text-slate-400 transition-colors group">
                    <i class='bx bx-arrow-back text-2xl group-hover:-translate-x-1 transition-transform'></i>
                </button>
                <div>
                    <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Rincian Tagihan Bulan</h2>
                    <p class="text-slate-600 dark:text-slate-400 mt-1">
                        {{ \Carbon\Carbon::createFromFormat('Y-m', $selectedMonth)->format('F Y') }}
                    </p>
                </div>
            </div>
            
            <!-- Month Navigation (Keep existing logic but maybe simplify) -->
            <div class="flex items-center gap-2 bg-white dark:bg-darkCard p-1.5 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700">
                <button wire:click="prevMonth" class="p-2 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg text-slate-600 dark:text-slate-400 transition-colors">
                    <i class='bx bx-chevron-left text-xl'></i>
                </button>
                <span class="px-3 font-bold text-slate-800 dark:text-white text-sm">
                    {{ \Carbon\Carbon::createFromFormat('Y-m', $selectedMonth)->format('M Y') }}
                </span>
                <button wire:click="nextMonth" class="p-2 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg text-slate-600 dark:text-slate-400 transition-colors">
                    <i class='bx bx-chevron-right text-xl'></i>
                </button>
            </div>
        </div>

        <!-- Flash Messages -->
        @if (session()->has('success'))
            <div class="mb-6 p-4 bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30 text-emerald-700 dark:text-emerald-400 rounded-xl flex items-center gap-3 shadow-sm">
                <i class='bx bxs-check-circle text-2xl'></i>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-6 p-4 bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/30 text-rose-700 dark:text-rose-400 rounded-xl flex items-center gap-3 shadow-sm">
                <i class='bx bxs-error-circle text-2xl'></i>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
        @endif

        <!-- HERO SECTION: Dynamic State -->
        @if($this->debitStatus === 'COMPLETED')
            <!-- STATE: COMPLETED (Lunas) -->
            <div class="bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl shadow-lg p-8 text-center text-white mb-8 relative overflow-hidden">
                <div class="absolute top-0 right-0 -mt-10 -mr-10 w-64 h-64 bg-white opacity-10 rounded-full blur-3xl"></div>
                <div class="absolute bottom-0 left-0 -mb-10 -ml-10 w-64 h-64 bg-black opacity-10 rounded-full blur-3xl"></div>
                
                <div class="relative z-10 flex flex-col items-center justify-center">
                    <div class="w-20 h-20 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center mb-6 shadow-inner border border-white/30">
                        <i class='bx bx-check text-5xl text-white'></i>
                    </div>
                    <h3 class="text-3xl font-bold mb-2">Bulan {{ \Carbon\Carbon::createFromFormat('Y-m', $selectedMonth)->format('F') }} Lunas!</h3>
                    <p class="text-emerald-100 text-lg mb-8 max-w-xl mx-auto">Seluruh tagihan simpanan wajib untuk periode ini telah berhasil diproses dan disetujui.</p>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 w-full max-w-3xl mx-auto">
                        <div class="bg-white/10 backdrop-blur-md rounded-xl p-4 border border-white/20">
                            <p class="text-emerald-100 text-xs uppercase font-bold tracking-wider mb-1">Total Terhimpun</p>
                            <p class="text-2xl font-bold">Rp {{ number_format($stats['totalAmount'], 0, ',', '.') }}</p>
                        </div>
                        <div class="bg-white/10 backdrop-blur-md rounded-xl p-4 border border-white/20">
                            <p class="text-emerald-100 text-xs uppercase font-bold tracking-wider mb-1">Transaksi Sukses</p>
                            <p class="text-2xl font-bold">{{ $stats['approved'] }}</p>
                        </div>
                        <div class="bg-white/10 backdrop-blur-md rounded-xl p-4 border border-white/20">
                            <p class="text-emerald-100 text-xs uppercase font-bold tracking-wider mb-1">Pending</p>
                            <p class="text-2xl font-bold">0</p>
                        </div>
                    </div>
                </div>
            </div>

        @elseif($this->debitStatus === 'EMPTY')
            @php
                $date = \Carbon\Carbon::createFromFormat('Y-m', $selectedMonth);
                $isPast = $date->endOfMonth()->isPast();
            @endphp

            @if($isPast)
                <!-- STATE: EMPTY PAST (Archived/No Data) -->
                <div class="bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-200 dark:border-slate-700 p-10 text-center mb-8">
                    <div class="w-24 h-24 bg-slate-200 dark:bg-slate-700 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class='bx bx-archive text-5xl text-slate-400'></i>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-600 dark:text-slate-300 mb-3">Periode Telah Berlalu</h3>
                    <p class="text-slate-500 dark:text-slate-400 max-w-lg mx-auto">
                        Tidak ada data transaksi simpanan wajib untuk periode <span class="font-bold">{{ $date->format('F Y') }}</span>.
                        <br>Pembuatan tagihan untuk periode lampau dinonaktifkan.
                    </p>
                </div>
            @else
                <!-- STATE: EMPTY FUTURE/CURRENT (Ready to Generate) -->
                <div class="bg-white dark:bg-darkCard rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-10 text-center mb-8">
                    <div class="w-24 h-24 bg-indigo-50 dark:bg-indigo-500/10 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class='bx bx-calendar-plus text-5xl text-indigo-600 dark:text-indigo-400'></i>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-800 dark:text-white mb-3">Tagihan Belum Dibuat</h3>
                    <p class="text-slate-500 dark:text-slate-400 max-w-lg mx-auto mb-8">
                        Belum ada data tagihan simpanan wajib untuk periode <span class="font-bold text-slate-800 dark:text-white">{{ \Carbon\Carbon::createFromFormat('Y-m', $selectedMonth)->format('F Y') }}</span>. 
                        Klik tombol di bawah untuk memproses tagihan otomatis bagi seluruh anggota aktif.
                    </p>
                    
                    <button wire:click="generateDebit" 
                            wire:loading.attr="disabled"
                            wire:confirm="Generate auto-debit untuk bulan {{ \Carbon\Carbon::createFromFormat('Y-m', $selectedMonth)->format('F Y') }}?"
                            class="px-8 py-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold text-lg shadow-lg shadow-indigo-500/30 transition-all transform hover:-translate-y-1 flex items-center justify-center gap-3 mx-auto disabled:opacity-50 disabled:cursor-not-allowed">
                        <span wire:loading.remove wire:target="generateDebit">
                            <i class='bx bx-rocket'></i> Generate Tagihan Sekarang
                        </span>
                        <span wire:loading wire:target="generateDebit" class="flex items-center gap-2">
                            <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Sedang Memproses...
                        </span>
                    </button>
                </div>
            @endif

        @else
            <!-- STATE: PENDING (Action Needed) -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-8">
                <!-- Action Card -->
                <div class="lg:col-span-3 bg-white dark:bg-darkCard rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 flex flex-col sm:flex-row items-center justify-between gap-6">
                    <div class="flex items-center gap-5">
                        <div class="w-16 h-16 bg-amber-50 dark:bg-amber-500/10 rounded-2xl flex items-center justify-center flex-shrink-0">
                            <i class='bx bx-time-five text-3xl text-amber-500'></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-slate-800 dark:text-white mb-1">Menunggu Persetujuan</h3>
                            <p class="text-slate-500 dark:text-slate-400 text-sm">
                                Terdapat <span class="font-bold text-amber-500">{{ $stats['pending'] }}</span> transaksi yang perlu direview.
                            </p>
                        </div>
                    </div>
                    <div class="flex gap-3 w-full sm:w-auto">
                        <button wire:click="approveAll" 
                                wire:confirm="Yakin ingin menyetujui SEMUA transaksi pending?"
                                class="flex-1 sm:flex-none px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold shadow-lg shadow-emerald-500/20 transition-colors flex items-center justify-center gap-2">
                            <i class='bx bx-check-double text-xl'></i> Approve All
                        </button>
                    </div>
                </div>

                <!-- Summary Card -->
                <div class="bg-indigo-600 rounded-2xl shadow-lg shadow-indigo-500/20 p-6 text-white flex flex-col justify-center relative overflow-hidden">
                    <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white opacity-10 rounded-full blur-xl"></div>
                    <p class="text-indigo-200 text-xs font-bold uppercase tracking-wider mb-1">Total Tagihan</p>
                    <h3 class="text-2xl font-bold">Rp {{ number_format($stats['totalAmount'], 0, ',', '.') }}</h3>
                    <div class="mt-4 pt-4 border-t border-white/20 flex justify-between items-center text-sm">
                        <span class="text-indigo-200">Progress</span>
                        <span class="font-bold">{{ round(($stats['approved'] / ($stats['approved'] + $stats['pending'])) * 100) }}%</span>
                    </div>
                </div>
            </div>

        @endif

        @if($this->debitStatus !== 'EMPTY')
            <!-- Bulk Actions Bar (Only visible when items selected AND status is PENDING) -->
            @if(count($selectedTransactions) > 0 && $this->debitStatus === 'PENDING')
                <div class="fixed bottom-6 left-1/2 transform -translate-x-1/2 bg-slate-900 text-white px-6 py-3 rounded-full shadow-2xl z-50 flex items-center gap-4 animate-in slide-in-from-bottom-4 fade-in duration-300">
                    <span class="font-bold text-sm"><span class="text-indigo-400">{{ count($selectedTransactions) }}</span> terpilih</span>
                    <div class="h-4 w-px bg-slate-700"></div>
                    <button wire:click="approveSelected" class="hover:text-emerald-400 transition-colors font-medium text-sm flex items-center gap-1">
                        <i class='bx bx-check'></i> Approve
                    </button>
                    <button wire:click="rejectSelected" wire:confirm="Tolak transaksi terpilih?" class="hover:text-rose-400 transition-colors font-medium text-sm flex items-center gap-1">
                        <i class='bx bx-x'></i> Reject
                    </button>
                    <button wire:click="$set('selectedTransactions', [])" class="ml-2 text-slate-500 hover:text-white transition-colors">
                        <i class='bx bx-x-circle'></i>
                    </button>
                </div>
            @endif

            <!-- Transactions Table -->
            <div class="bg-white dark:bg-darkCard rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-700">
                            <tr>
                                @if($this->debitStatus === 'PENDING')
                                    <th class="px-6 py-4 text-left w-16">
                                        <input type="checkbox" wire:model.live="selectAll" 
                                               class="w-4 h-4 text-indigo-600 rounded focus:ring-indigo-500 border-slate-300 dark:border-slate-600 dark:bg-slate-700 cursor-pointer">
                                    </th>
                                @endif
                                <th class="px-6 py-4 text-left text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Anggota</th>
                                <th class="px-6 py-4 text-left text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Unit Kerja</th>
                                <th class="px-6 py-4 text-left text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Jumlah Tagihan</th>
                                <th class="px-6 py-4 text-left text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Saldo Simpanan</th>
                                <th class="px-6 py-4 text-left text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Status Pembayaran</th>
                                <th class="px-6 py-4 text-left text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Tanggal Bayar</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                            @forelse($transactions as $transaction)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors group {{ in_array($transaction->id, $selectedTransactions) ? 'bg-indigo-50/50 dark:bg-indigo-900/10' : '' }}">
                                    @if($this->debitStatus === 'PENDING')
                                        <td class="px-6 py-4">
                                            <input type="checkbox" wire:model.live="selectedTransactions" value="{{ $transaction->id }}"
                                                   class="w-4 h-4 text-indigo-600 rounded focus:ring-indigo-500 border-slate-300 dark:border-slate-600 dark:bg-slate-700 cursor-pointer">
                                        </td>
                                    @endif
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-bold text-xs">
                                                {{ substr($transaction->member->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="text-sm font-bold text-slate-800 dark:text-white">{{ $transaction->member->name }}</div>
                                                <div class="text-[11px] text-slate-500 dark:text-slate-400 font-mono">
                                                    No. Anggota: {{ $transaction->member->nomorAnggota }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-sm text-slate-600 dark:text-slate-400">
                                            {{ $transaction->member->unitKerja ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-sm font-bold text-slate-800 dark:text-white">
                                            Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-sm text-emerald-600 dark:text-emerald-400 font-semibold">
                                            Rp {{ number_format($transaction->balanceAfter, 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($transaction->status === 'PENDING')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-100 dark:border-amber-500/20">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                                Menunggu
                                            </span>
                                        @elseif($transaction->status === 'APPROVED')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-500/20">
                                                <i class='bx bx-check'></i> Lunas
                                            </span>
                                        @elseif($transaction->status === 'REJECTED')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-100 dark:border-rose-500/20">
                                                <i class='bx bx-x'></i> Ditolak
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400 font-mono">
                                        {{ $transaction->created_at->format('d/m/Y') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $this->debitStatus === 'PENDING' ? '7' : '6' }}" class="px-6 py-12 text-center text-slate-400">
                                        <i class='bx bx-inbox text-4xl mb-2 text-slate-300 dark:text-slate-600'></i>
                                        <p>Belum ada data tagihan.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($transactions->hasPages())
                    <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-700">
                        {{ $transactions->links() }}
                    </div>
                @endif
            </div>
        @endif
    @endif
</div>
