<div>
    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div class="mb-6 p-4 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-lg">
            <p class="text-sm text-emerald-600 dark:text-emerald-400 font-medium">{{ session('message') }}</p>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-6 p-4 bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800 rounded-lg">
            <p class="text-sm text-rose-600 dark:text-rose-400 font-medium">{{ session('error') }}</p>
        </div>
    @endif

    <!-- Member Summary -->
    <div class="flex justify-between items-center bg-white dark:bg-darkCard p-4 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm mb-6">
        <div class="flex items-center gap-3">
            <img src="https://ui-avatars.com/api/?name={{ urlencode($member->user->name) }}&background=0F52BA&color=fff"
                class="w-10 h-10 rounded-full" alt="{{ $member->user->name }}">
            <div>
                <h2 class="text-sm font-bold text-slate-900 dark:text-white">{{ $member->user->name }}</h2>
                <p class="text-[11px] text-slate-500">{{ $member->nomorAnggota }} • {{ $member->unitKerja === 'unknown' ? 'Belum Diisi' : ($member->unitKerja ?? '-') }}</p>
            </div>
        </div>
        <div class="text-right">
            <p class="text-[10px] text-slate-400 uppercase tracking-widest font-bold">Total Aset (Grand Total)</p>
            <h3 class="text-xl font-bold text-emerald-600 dark:text-emerald-400">Rp {{ number_format($member->totalSimpanan, 0, ',', '.') }}</h3>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-indigo-50 dark:bg-indigo-900/10 p-4 rounded-xl border border-indigo-100 dark:border-indigo-800/30">
            <div class="flex justify-between items-start mb-2">
                <span class="text-[10px] font-bold text-indigo-800 dark:text-indigo-300 uppercase tracking-widest">S. Pokok</span>
                <i class='bx bxs-lock-alt text-indigo-400 text-lg'></i>
            </div>
            <h4 class="text-lg font-bold text-slate-800 dark:text-white">Rp {{ number_format($member->simpananPokok, 0, ',', '.') }}</h4>
            <p class="text-[10px] text-slate-500">Sekali Bayar</p>
        </div>
        <div class="bg-blue-50 dark:bg-blue-900/10 p-4 rounded-xl border border-blue-100 dark:border-blue-800/30">
            <div class="flex justify-between items-start mb-2">
                <span class="text-[10px] font-bold text-blue-800 dark:text-blue-300 uppercase tracking-widest">S. Wajib</span>
                <i class='bx bxs-calendar text-blue-400 text-lg'></i>
            </div>
            <h4 class="text-lg font-bold text-slate-800 dark:text-white">Rp {{ number_format($member->simpananWajib, 0, ',', '.') }}</h4>
            <p class="text-[10px] text-slate-500">Akumulasi Bulanan</p>
        </div>
        <div class="bg-emerald-50 dark:bg-emerald-900/10 p-4 rounded-xl border border-emerald-100 dark:border-emerald-800/30">
            <div class="flex justify-between items-start mb-2">
                <span class="text-[10px] font-bold text-emerald-800 dark:text-emerald-300 uppercase tracking-widest">S. Sukarela</span>
                <i class='bx bxs-wallet text-emerald-400 text-lg'></i>
            </div>
            <h4 class="text-lg font-bold text-slate-800 dark:text-white">Rp {{ number_format($member->simpananSukarela, 0, ',', '.') }}</h4>
            <p class="text-[10px] text-slate-500">Liquid / Bisa Ditarik</p>
        </div>
    </div>

    <!-- Pelunasan Tagihan Bulanan (Migrasi dari payments/create) -->
    <div class="bg-white dark:bg-darkCard rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden mb-6">
        <div class="p-4 sm:p-5 border-b border-slate-100 dark:border-slate-700">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">Pelunasan Tagihan Simpanan</h3>
                    <p class="text-xs text-slate-500 mt-1">Pilih tagihan bulanan yang belum lunas, lalu catat pembayarannya.</p>
                </div>
                <span class="text-[10px] bg-amber-50 text-amber-700 dark:bg-amber-900/20 dark:text-amber-300 px-2 py-1 rounded border border-amber-100 dark:border-amber-800 font-bold uppercase">
                    {{ count($unpaidBills) }} Tagihan
                </span>
            </div>
        </div>

        @if(count($unpaidBills) > 0)
            <div class="p-4 sm:p-5 space-y-5">
                <div class="overflow-x-auto rounded-lg border border-slate-200 dark:border-slate-700">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50 dark:bg-slate-800/60 text-[10px] uppercase font-bold text-slate-500 tracking-wider">
                            <tr>
                                <th class="px-4 py-3 w-10">
                                    <input type="checkbox"
                                        wire:click="toggleAllPaymentBills"
                                        {{ count($selectedBills) === count($unpaidBills) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-primary focus:ring-primary w-4 h-4 cursor-pointer">
                                </th>
                                <th class="px-4 py-3">Periode</th>
                                <th class="px-4 py-3">Jenis</th>
                                <th class="px-4 py-3 text-right">Nominal</th>
                                <th class="px-4 py-3 text-right">Sisa</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700 text-[13px]">
                            @foreach($unpaidBills as $bill)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 {{ in_array($bill['id'], $selectedBills) ? 'bg-indigo-50 dark:bg-indigo-900/20' : '' }}">
                                    <td class="px-4 py-3">
                                        <input type="checkbox"
                                            value="{{ $bill['id'] }}"
                                            wire:model.live="selectedBills"
                                            class="rounded border-gray-300 text-primary focus:ring-primary w-4 h-4 cursor-pointer">
                                    </td>
                                    <td class="px-4 py-3 font-medium text-slate-800 dark:text-white">{{ $bill['billingMonthFormatted'] }}</td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-0.5 text-[10px] font-bold rounded border
                                            {{ $bill['type'] === 'WAJIB' ? 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300 border-blue-100 dark:border-blue-800' : '' }}
                                            {{ $bill['type'] === 'POKOK' ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300 border-indigo-100 dark:border-indigo-800' : '' }}
                                            {{ $bill['type'] === 'SUKARELA' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300 border-emerald-100 dark:border-emerald-800' : '' }}">
                                            {{ $bill['typeLabel'] }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right text-slate-500">Rp {{ number_format($bill['amount'], 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-right font-bold text-rose-600 dark:text-rose-400">Rp {{ number_format($bill['remainingAmount'], 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @error('selectedBills') <p class="text-xs text-rose-500 -mt-2">{{ $message }}</p> @enderror

                <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
                    <div class="xl:col-span-2 space-y-4">
                        <div class="bg-slate-50 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-700 rounded-xl p-4">
                            <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2">Metode Pembayaran</label>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                                <label class="cursor-pointer">
                                    <input type="radio" wire:model.live="paymentMethod" value="CASH" class="peer sr-only">
                                    <div class="px-3 py-2.5 rounded-lg text-center border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold text-slate-600 dark:text-slate-300 peer-checked:border-primary peer-checked:text-primary dark:peer-checked:text-indigo-300">
                                        Tunai
                                    </div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" wire:model.live="paymentMethod" value="TRANSFER" class="peer sr-only">
                                    <div class="px-3 py-2.5 rounded-lg text-center border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold text-slate-600 dark:text-slate-300 peer-checked:border-primary peer-checked:text-primary dark:peer-checked:text-indigo-300">
                                        Transfer
                                    </div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" wire:model.live="paymentMethod" value="AUTO_DEBIT" class="peer sr-only">
                                    <div class="px-3 py-2.5 rounded-lg text-center border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold text-slate-600 dark:text-slate-300 peer-checked:border-primary peer-checked:text-primary dark:peer-checked:text-indigo-300">
                                        Potong Gaji
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1.5">Tanggal Bayar</label>
                                <input type="date" wire:model="paymentDate"
                                    class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-white focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
                                @error('paymentDate') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                            </div>

                            @if($paymentMethod === 'TRANSFER')
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1.5">Nomor Referensi</label>
                                    <input type="text" wire:model="referenceNumber" placeholder="TRF-12345"
                                        class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-white focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
                                    @error('referenceNumber') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div class="sm:col-span-2">
                                    <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1.5">Bukti Transfer</label>
                                    <input type="file" wire:model="paymentProofAttachment"
                                        class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-primary file:text-white hover:file:bg-indigo-700">
                                    @error('paymentProofAttachment') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                                </div>
                            @endif
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1.5">Catatan (Opsional)</label>
                            <textarea wire:model="paymentNotes" rows="2"
                                class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-white focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none resize-none"></textarea>
                        </div>
                    </div>

                    <div class="bg-primary text-white rounded-xl p-4 flex flex-col justify-between">
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-widest text-indigo-100">Total Pelunasan</p>
                            <p class="text-2xl font-black mt-1">Rp {{ number_format($paymentTotalAmount, 0, ',', '.') }}</p>
                            <p class="text-xs text-indigo-100 mt-1">{{ $paymentItemsCount }} tagihan dipilih</p>
                        </div>
                        <button wire:click="processBillPayment"
                            wire:loading.attr="disabled"
                            {{ count($selectedBills) === 0 ? 'disabled' : '' }}
                            class="mt-4 w-full py-2.5 bg-white text-primary font-bold rounded-lg hover:bg-indigo-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                            <span wire:loading.remove wire:target="processBillPayment">Catat Pembayaran</span>
                            <span wire:loading wire:target="processBillPayment">Menyimpan...</span>
                        </button>
                    </div>
                </div>
            </div>
        @else
            <div class="p-10 text-center text-slate-400">
                <i class='bx bx-check-circle text-4xl mb-2 text-emerald-500'></i>
                <p class="text-sm font-medium text-slate-600 dark:text-slate-300">Semua tagihan simpanan sudah lunas.</p>
                <p class="text-xs mt-1">Tidak ada pembayaran tagihan yang perlu dicatat.</p>
            </div>
        @endif
    </div>

    <!-- Tabs -->
    <div class="bg-white dark:bg-darkCard rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden min-h-[400px]">
        <div class="flex border-b border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/20">
            <button wire:click="switchTab('wajib')"
                class="flex-1 py-3 text-[12px] {{ $activeTab === 'wajib' ? 'font-bold text-primary border-b-2 border-primary bg-white dark:bg-darkCard' : 'font-medium text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-white border-b-2 border-transparent' }} transition-colors">
                Simpanan Wajib (Bulanan)
            </button>
            <button wire:click="switchTab('sukarela')"
                class="flex-1 py-3 text-[12px] {{ $activeTab === 'sukarela' ? 'font-bold text-primary border-b-2 border-primary bg-white dark:bg-darkCard' : 'font-medium text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-white border-b-2 border-transparent' }} transition-colors">
                Simpanan Sukarela (Tabungan)
            </button>
            <button wire:click="switchTab('pokok')"
                class="flex-1 py-3 text-[12px] {{ $activeTab === 'pokok' ? 'font-bold text-primary border-b-2 border-primary bg-white dark:bg-darkCard' : 'font-medium text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-white border-b-2 border-transparent' }} transition-colors">
                Simpanan Pokok
            </button>
        </div>

        <!-- Tab Content: Wajib -->
        @if($activeTab === 'wajib')
            <div class="p-6">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg text-blue-600">
                            <i class='bx bx-calendar-check text-2xl'></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Saldo Wajib</p>
                            <h3 class="text-base font-bold text-slate-800 dark:text-white">Rp {{ number_format($member->simpananWajib, 0, ',', '.') }}</h3>
                            <p class="text-[10px] text-slate-500">Total akumulasi setoran bulanan</p>
                        </div>
                    </div>
                    <button wire:click="openWajibModal"
                        class="bg-primary hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg text-[13px] font-bold shadow-md transition-colors flex items-center gap-2">
                        <i class='bx bx-plus-circle'></i> Input Setoran Wajib
                    </button>
                </div>

                <div class="border border-slate-200 dark:border-slate-700 rounded-xl overflow-hidden">
                    <table class="w-full text-left text-sm text-slate-600 dark:text-slate-400">
                        <thead class="bg-slate-50 dark:bg-slate-800/50 text-[10px] uppercase font-bold text-slate-500 dark:text-slate-400 tracking-widest">
                            <tr>
                                <th class="px-5 py-3">Tanggal</th>
                                <th class="px-5 py-3">Keterangan</th>
                                <th class="px-5 py-3">Nominal</th>
                                <th class="px-5 py-3">Petugas</th>
                                <th class="px-5 py-3">Saldo</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700 text-[13px]">
                            @forelse($wajibTransactions as $trx)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50">
                                    <td class="px-5 py-3">{{ $trx->created_at->format('d M Y, H:i') }}</td>
                                    <td class="px-5 py-3 text-slate-500">{{ $trx->notes }}</td>
                                    <td class="px-5 py-3 font-mono text-emerald-600">+ Rp {{ number_format($trx->amount, 0, ',', '.') }}</td>
                                    <td class="px-5 py-3 text-[11px]">{{ $trx->processor->name ?? 'System' }}</td>
                                    <td class="px-5 py-3 font-mono">Rp {{ number_format($trx->balanceAfter, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-8 text-center text-slate-400">
                                        <i class='bx bx-receipt text-3xl mb-2'></i>
                                        <p>Belum ada transaksi</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($wajibTransactions->hasPages())
                    <div class="mt-4">
                        {{ $wajibTransactions->links() }}
                    </div>
                @endif
            </div>
        @endif

        <!-- Tab Content: Sukarela -->
        @if($activeTab === 'sukarela')
            <div class="p-6">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-emerald-50 dark:bg-emerald-900/20 rounded-lg text-emerald-600">
                            <i class='bx bx-wallet text-2xl'></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Saldo Tersedia</p>
                            <h3 class="text-2xl font-bold text-slate-800 dark:text-white">Rp {{ number_format($member->simpananSukarela, 0, ',', '.') }}</h3>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <button wire:click="openTarikModal"
                            class="bg-white dark:bg-darkCard border border-rose-200 dark:border-rose-900 text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/20 px-4 py-2 rounded-lg text-[13px] font-bold transition-colors flex items-center gap-2">
                            <i class='bx bx-money-withdraw'></i> Tarik Tunai
                        </button>
                        <button wire:click="openSetorModal"
                            class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-[13px] font-bold shadow-md transition-colors flex items-center gap-2">
                            <i class='bx bx-plus-circle'></i> Setor Tunai
                        </button>
                    </div>
                </div>

                <div class="border border-slate-200 dark:border-slate-700 rounded-xl overflow-hidden">
                    <table class="w-full text-left text-sm text-slate-600 dark:text-slate-400">
                        <thead class="bg-slate-50 dark:bg-slate-800/50 text-[10px] uppercase font-bold text-slate-500 dark:text-slate-400 tracking-widest">
                            <tr>
                                <th class="px-5 py-3">Waktu</th>
                                <th class="px-5 py-3">Tipe</th>
                                <th class="px-5 py-3">Keterangan</th>
                                <th class="px-5 py-3 text-right">Nominal</th>
                                <th class="px-5 py-3 text-right">Saldo Akhir</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700 text-[13px]">
                            @forelse($sukarelaTransactions as $trx)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50">
                                    <td class="px-5 py-3">{{ $trx->created_at->format('d M, H:i') }}</td>
                                    <td class="px-5 py-3">
                                        <span class="font-bold {{ $trx->transactionType === 'SETOR' ? 'text-emerald-600' : 'text-rose-500' }}">
                                            {{ $trx->transactionTypeLabel }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3 text-slate-500">{{ $trx->notes }}</td>
                                    <td class="px-5 py-3 text-right font-bold {{ $trx->transactionType === 'SETOR' ? 'text-emerald-600' : 'text-rose-500' }}">
                                        {{ $trx->transactionType === 'SETOR' ? '+' : '-' }}Rp {{ number_format($trx->amount, 0, ',', '.') }}
                                    </td>
                                    <td class="px-5 py-3 text-right font-mono">Rp {{ number_format($trx->balanceAfter, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-8 text-center text-slate-400">
                                        <i class='bx bx-wallet text-3xl mb-2'></i>
                                        <p>Belum ada transaksi</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($sukarelaTransactions->hasPages())
                    <div class="mt-4">
                        {{ $sukarelaTransactions->links() }}
                    </div>
                @endif
            </div>
        @endif

        <!-- Tab Content: Pokok -->
        @if($activeTab === 'pokok')
            <div class="p-6 text-center">
                <div class="max-w-md mx-auto py-10">
                    <div class="w-16 h-16 bg-indigo-50 dark:bg-indigo-900/20 text-primary rounded-full flex items-center justify-center text-3xl mx-auto mb-4">
                        <i class='bx bxs-lock'></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">Simpanan Pokok Terbayar</h3>
                    <p class="text-sm text-slate-500 mt-2 mb-6">
                        Anggota ini telah melunasi simpanan pokok pada saat pendaftaran. Dana ini tidak dapat ditarik kecuali anggota mengundurkan diri.
                    </p>
                    <div class="inline-block bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-6 py-3 rounded-xl">
                        <span class="text-[10px] uppercase font-bold text-slate-400 tracking-widest">Nominal</span>
                        <p class="text-2xl font-bold text-slate-800 dark:text-white mt-1">Rp {{ number_format($member->simpananPokok, 0, ',', '.') }}</p>
                    </div>

                    @if($pokokTransactions->count() > 0)
                        <div class="mt-8 pt-8 border-t border-slate-100 dark:border-slate-700">
                            <p class="text-xs text-slate-400 mb-2">Riwayat Transaksi</p>
                            @foreach($pokokTransactions as $trx)
                                <div class="text-xs text-slate-600 dark:text-slate-400">
                                    {{ $trx->created_at->format('d M Y') }} - Rp {{ number_format($trx->amount, 0, ',', '.') }}
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <!-- Modal: Wajib -->
    @if($showWajibModal)
        <div class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-darkCard w-full max-w-sm rounded-xl p-6 shadow-2xl">
                <h3 class="font-bold text-lg mb-4 dark:text-white">Input Setoran Wajib</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="text-xs text-slate-500 block mb-1">Nominal (Rp)</label>
                        <input type="number" wire:model="wajibAmount"
                            class="w-full border rounded-lg p-2 text-sm dark:bg-slate-800 dark:border-slate-600 dark:text-white">
                        @error('wajibAmount') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="text-xs text-slate-500 block mb-1">Keterangan (Opsional)</label>
                        <textarea wire:model="notes" rows="2"
                            class="w-full border rounded-lg p-2 text-sm dark:bg-slate-800 dark:border-slate-600 dark:text-white"></textarea>
                    </div>
                    <div>
                        <label class="text-xs text-slate-500 block mb-1">Bukti Transfer (Opsional)</label>
                        <input type="file" wire:model="buktiTransfer" accept="image/*"
                            class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200">
                        @error('buktiTransfer') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="flex gap-2 justify-end mt-4">
                    <button wire:click="closeWajibModal"
                        class="px-4 py-2 text-sm font-bold text-slate-500 hover:bg-slate-100 rounded-lg">Batal</button>
                    <button wire:click="submitWajib" wire:loading.attr="disabled"
                        class="px-4 py-2 text-sm font-bold text-white bg-primary hover:bg-indigo-700 rounded-lg disabled:opacity-50">
                        <span wire:loading.remove wire:target="submitWajib">Simpan</span>
                        <span wire:loading wire:target="submitWajib">Menyimpan...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal: Setor -->
    @if($showSetorModal)
        <div class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-darkCard w-full max-w-sm rounded-xl p-6 shadow-2xl">
                <h3 class="font-bold text-lg mb-4 dark:text-white">Setor Tunai Sukarela</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="text-xs text-slate-500 block mb-1">Nominal (Rp)</label>
                        <input type="number" wire:model="setorAmount"
                            class="w-full border rounded-lg p-2 text-sm dark:bg-slate-800 dark:border-slate-600 dark:text-white"
                            placeholder="0">
                        @error('setorAmount') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="text-xs text-slate-500 block mb-1">Keterangan (Opsional)</label>
                        <textarea wire:model="notes" rows="2"
                            class="w-full border rounded-lg p-2 text-sm dark:bg-slate-800 dark:border-slate-600 dark:text-white"></textarea>
                    </div>
                    <div>
                        <label class="text-xs text-slate-500 block mb-1">Bukti Transfer (Opsional)</label>
                        <input type="file" wire:model="buktiTransfer" accept="image/*"
                            class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200">
                        @error('buktiTransfer') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="flex gap-2 justify-end mt-4">
                    <button wire:click="closeSetorModal"
                        class="px-4 py-2 text-sm font-bold text-slate-500 hover:bg-slate-100 rounded-lg">Batal</button>
                    <button wire:click="submitSetor" wire:loading.attr="disabled"
                        class="px-4 py-2 text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg disabled:opacity-50">
                        <span wire:loading.remove wire:target="submitSetor">Setor</span>
                        <span wire:loading wire:target="submitSetor">Menyimpan...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal: Tarik -->
    @if($showTarikModal)
        <div class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-darkCard w-full max-w-sm rounded-xl p-6 shadow-2xl">
                <h3 class="font-bold text-lg mb-4 text-rose-600">Tarik Dana Sukarela</h3>
                
                <p class="text-xs text-slate-500 mb-4">Saldo Tersedia: <strong>Rp {{ number_format($member->simpananSukarela, 0, ',', '.') }}</strong></p>
                
                <div class="space-y-4">
                    <div>
                        <label class="text-xs text-slate-500 block mb-1">Nominal Penarikan (Rp)</label>
                        <input type="number" wire:model="tarikAmount"
                            class="w-full border rounded-lg p-2 text-sm dark:bg-slate-800 dark:border-slate-600 dark:text-white"
                            placeholder="0">
                        @error('tarikAmount') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="text-xs text-slate-500 block mb-1">Alasan Penarikan</label>
                        <textarea wire:model="notes" rows="2"
                            class="w-full border rounded-lg p-2 text-sm dark:bg-slate-800 dark:border-slate-600 dark:text-white"></textarea>
                    </div>
                </div>

                <div class="flex gap-2 justify-end mt-4">
                    <button wire:click="closeTarikModal"
                        class="px-4 py-2 text-sm font-bold text-slate-500 hover:bg-slate-100 rounded-lg">Batal</button>
                    <button wire:click="submitTarik" wire:loading.attr="disabled"
                        class="px-4 py-2 text-sm font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-lg disabled:opacity-50">
                        <span wire:loading.remove wire:target="submitTarik">Proses Penarikan</span>
                        <span wire:loading wire:target="submitTarik">Memproses...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
