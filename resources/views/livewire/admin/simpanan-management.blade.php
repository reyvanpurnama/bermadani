<div>
    <!-- Floating Toast Notifications -->
    @if (session()->has('message'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
            class="fixed top-5 right-5 z-50 flex items-center gap-3 bg-emerald-600 text-white px-4 py-3 rounded-2xl shadow-2xl border border-emerald-500 transition-all animate-bounce-short">
            <i class='bx bx-check-circle text-2xl'></i>
            <span class="text-xs font-bold">{{ session('message') }}</span>
            <button @click="show = false" class="text-white/80 hover:text-white ml-2">
                <i class='bx bx-x text-lg'></i>
            </button>
        </div>
    @endif

    @if (session()->has('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
            class="fixed top-5 right-5 z-50 flex items-center gap-3 bg-rose-600 text-white px-4 py-3 rounded-2xl shadow-2xl border border-rose-500 transition-all animate-bounce-short">
            <i class='bx bx-error-circle text-2xl'></i>
            <span class="text-xs font-bold">{{ session('error') }}</span>
            <button @click="show = false" class="text-white/80 hover:text-white ml-2">
                <i class='bx bx-x text-lg'></i>
            </button>
        </div>
    @endif

    <!-- Member Summary -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center bg-white dark:bg-darkCard p-4 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm mb-6 gap-3">
        <div class="flex items-center gap-3">
            <img src="https://ui-avatars.com/api/?name={{ urlencode($member->user->name) }}&background=0F52BA&color=fff"
                class="w-10 h-10 rounded-full" alt="{{ $member->user->name }}">
            <div>
                <h2 class="text-sm font-bold text-slate-900 dark:text-white">{{ $member->user->name }}</h2>
                <p class="text-[11px] text-slate-500">{{ $member->nomorAnggota }} • {{ $member->unitKerja === 'unknown' ? 'Belum Diisi' : ($member->unitKerja ?? '-') }}</p>
            </div>
        </div>
        <div class="flex items-center gap-3 text-right">
            <button wire:click="recalculateMemberBalances" wire:loading.attr="disabled" class="px-3 py-1.5 rounded-lg bg-indigo-50 dark:bg-indigo-950/40 border border-indigo-200 dark:border-indigo-800 text-indigo-700 dark:text-indigo-300 hover:bg-indigo-100 text-xs font-bold transition-all flex items-center gap-1.5 shrink-0" title="Sinkronkan total saldo dari database">
                <i class='bx bx-refresh text-base' wire:loading.remove wire:target="recalculateMemberBalances"></i>
                <i class='bx bx-loader-alt animate-spin text-base' wire:loading wire:target="recalculateMemberBalances"></i>
                <span>Sync Saldo DB</span>
            </button>
            <div>
                <p class="text-[10px] text-slate-400 uppercase tracking-widest font-bold">Total Aset (Grand Total)</p>
                <h3 class="text-xl font-bold text-emerald-600 dark:text-emerald-400">Rp {{ number_format($member->totalSimpanan, 0, ',', '.') }}</h3>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-indigo-50 dark:bg-indigo-900/10 p-4 rounded-xl border border-indigo-100 dark:border-indigo-800/30 flex flex-col justify-between">
            <div>
                <div class="flex justify-between items-start mb-2">
                    <span class="text-[10px] font-bold text-indigo-800 dark:text-indigo-300 uppercase tracking-widest">S. Pokok</span>
                    @if($member->simpananPokok > 0)
                        <span class="px-2 py-0.5 text-[9px] font-bold rounded bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">Lunas</span>
                    @else
                        <span class="px-2 py-0.5 text-[9px] font-bold rounded bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400">Belum Lunas</span>
                    @endif
                </div>
                <h4 class="text-lg font-bold text-slate-800 dark:text-white">Rp {{ number_format($member->simpananPokok, 0, ',', '.') }}</h4>
                <p class="text-[10px] text-slate-500">Sekali Bayar</p>
            </div>
            <button wire:click="openPokokModal" class="mt-3 text-xs font-bold text-primary hover:text-indigo-700 dark:text-indigo-400 inline-flex items-center gap-1">
                <i class='bx bx-plus-circle'></i> {{ $member->simpananPokok > 0 ? 'Tambah Setoran' : 'Setor Simpok' }}
            </button>
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
                <!-- Kartu Kontrol Simpanan Wajib Matrix -->
                <div class="bg-slate-50 dark:bg-slate-800/40 rounded-xl border border-slate-200 dark:border-slate-700 p-5 mb-6">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-5">
                        <div>
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                <i class='bx bx-calendar-check text-blue-600 dark:text-blue-400 text-lg'></i>
                                Kartu Kontrol Simpanan Wajib
                            </h3>
                            <p class="text-xs text-slate-500">Monitoring status setoran bulanan anggota per periode tahun</p>
                        </div>
                        
                        <div class="flex flex-wrap items-center gap-2">
                            <!-- Toggle Sakelar Mode Audit Admin -->
                            <button wire:click="toggleAuditMode" class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 border shadow-sm {{ $auditMode ? 'bg-amber-500 text-white border-amber-600 shadow-amber-500/20' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-300 dark:border-slate-700 hover:bg-slate-100' }}">
                                <i class='bx {{ $auditMode ? 'bx-toggle-right text-lg text-white' : 'bx-toggle-left text-lg text-slate-400' }}'></i>
                                <span>{{ $auditMode ? 'Mode Audit: AKTIF' : 'Mode Audit Admin' }}</span>
                            </button>

                            <div class="flex items-center gap-1.5">
                                <label class="text-xs font-bold text-slate-500">Tahun:</label>
                                <select wire:change="changeYear($event.target.value)" class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-800 dark:text-white rounded-lg px-3 py-1.5 focus:ring-2 focus:ring-primary/20 outline-none cursor-pointer">
                                    @foreach($availableYears as $year)
                                        <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>{{ $year }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                        @foreach($simwaGrid as $month => $data)
                            @php
                                $colorClass = match($data['status']) {
                                    'PAID' => 'bg-emerald-50 border-emerald-200 text-emerald-700 dark:bg-emerald-900/20 dark:border-emerald-800 dark:text-emerald-400',
                                    'UNPAID' => 'bg-rose-50 border-rose-200 text-rose-700 dark:bg-rose-900/20 dark:border-rose-800 dark:text-rose-400',
                                    'FUTURE' => 'bg-slate-100 border-slate-200 text-slate-400 dark:bg-slate-800/50 dark:border-slate-700 dark:text-slate-500',
                                    'NOT_MEMBER' => 'bg-slate-100/60 border-slate-200 text-slate-400 dark:bg-slate-800/30 dark:border-slate-800 dark:text-slate-600',
                                };
                            @endphp
                            <div class="relative border rounded-xl p-3 flex flex-col items-center justify-between text-center min-h-[125px] {{ $colorClass }} transition-all">
                                <div class="w-full text-center">
                                    <span class="text-[10px] font-bold uppercase tracking-wider block mb-1">{{ $data['fullName'] }}</span>
                                    
                                    @if($data['status'] === 'PAID')
                                        <i class='bx bx-check-circle text-2xl text-emerald-600 dark:text-emerald-400 mb-1'></i>
                                        <span class="text-[11px] font-bold block">LUNAS</span>
                                        <span class="text-[9px] text-emerald-600/80 dark:text-emerald-400/80 block mb-1">
                                            {{ $data['paidAmount'] > 0 ? 'Rp '.number_format($data['paidAmount'], 0, ',', '.') : $data['paidDate'] }}
                                        </span>

                                        @if($auditMode)
                                            <!-- Audit Action Controls for PAID Month -->
                                            <div class="mt-2 pt-1 border-t border-emerald-200 dark:border-emerald-800/60 flex flex-col gap-1">
                                                <button wire:click="openEditPeriodModal('{{ $data['periodKey'] }}', '{{ $data['fullName'] }} {{ $selectedYear }}', 'WAJIB')" class="px-1.5 py-0.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded text-[9px] font-bold transition-all w-full flex items-center justify-center gap-1">
                                                    <i class='bx bx-edit'></i> Edit Rp
                                                </button>
                                                <button wire:click="quickToggleWajibUnpaid('{{ $data['periodKey'] }}', '{{ $data['fullName'] }} {{ $selectedYear }}')" onclick="confirm('Tandai bulan {{ $data['fullName'] }} {{ $selectedYear }} sebagai BELUM BAYAR?') || event.stopImmediatePropagation()" class="px-1.5 py-0.5 bg-rose-600 hover:bg-rose-700 text-white rounded text-[9px] font-bold transition-all w-full flex items-center justify-center gap-1">
                                                    <i class='bx bx-x'></i> Batal Lunas
                                                </button>
                                            </div>
                                        @endif

                                    @elseif($data['status'] === 'UNPAID')
                                        <i class='bx bx-x-circle text-2xl text-rose-500 mb-1'></i>
                                        <span class="text-[11px] font-bold block mb-1">BELUM BAYAR</span>

                                        @if($auditMode)
                                            <!-- Audit Action Controls for UNPAID Month -->
                                            <div class="mt-1 flex flex-col gap-1 w-full">
                                                <button wire:click="quickToggleWajibPaid('{{ $data['periodKey'] }}', '{{ $data['fullName'] }} {{ $selectedYear }}')" class="px-2 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded text-[10px] font-bold shadow-sm transition-all w-full flex items-center justify-center gap-1">
                                                    <i class='bx bx-check'></i> 1-Klik Setor
                                                </button>
                                                <button wire:click="openEditPeriodModal('{{ $data['periodKey'] }}', '{{ $data['fullName'] }} {{ $selectedYear }}', 'WAJIB')" class="px-1.5 py-0.5 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 rounded text-[9px] font-bold transition-all w-full">
                                                    Atur Nominal
                                                </button>
                                            </div>
                                        @else
                                            <button wire:click="openWajibModal('{{ $data['fullName'] }} {{ $selectedYear }}', '{{ $data['periodKey'] }}')" class="px-2 py-0.5 bg-rose-600 hover:bg-rose-700 text-white rounded text-[10px] font-bold shadow-sm transition-colors w-full">
                                                + Setor
                                            </button>
                                        @endif

                                    @elseif($data['status'] === 'FUTURE')
                                        <i class='bx bx-time text-2xl text-slate-400 mb-1'></i>
                                        <span class="text-[10px] font-medium block">Belum Waktunya</span>
                                        @if($auditMode)
                                            <button wire:click="quickToggleWajibPaid('{{ $data['periodKey'] }}', '{{ $data['fullName'] }} {{ $selectedYear }}')" class="mt-1 px-1.5 py-0.5 bg-indigo-600 text-white rounded text-[9px] font-bold w-full">
                                                + Bayar Awal
                                            </button>
                                        @endif
                                    @else
                                        <i class='bx bx-minus-circle text-2xl text-slate-300 dark:text-slate-600 mb-1'></i>
                                        <span class="text-[10px] font-medium block">Belum Anggota</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="flex gap-4 mt-4 pt-3 border-t border-slate-200 dark:border-slate-700/60 justify-center flex-wrap">
                        <div class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                            <span class="text-[11px] text-slate-600 dark:text-slate-400 font-medium">Lunas</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                            <span class="text-[11px] text-slate-600 dark:text-slate-400 font-medium">Belum Bayar (Tunggakan)</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-slate-300"></span>
                            <span class="text-[11px] text-slate-600 dark:text-slate-400 font-medium">Belum Waktunya</span>
                        </div>
                    </div>
                </div>

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
                <div class="max-w-md mx-auto py-8">
                    @if($member->simpananPokok == 0)
                        <div class="w-16 h-16 bg-rose-50 dark:bg-rose-900/20 text-rose-600 rounded-full flex items-center justify-center text-3xl mx-auto mb-4">
                            <i class='bx bx-error-circle'></i>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Simpanan Pokok Belum Lunas (Rp 0)</h3>
                        <p class="text-sm text-slate-500 mt-2 mb-6">
                            Anggota ini belum melakukan pembayaran simpanan pokok (Rp 200.000). Silakan catat setoran simpanan pokok di bawah ini.
                        </p>
                        <div class="inline-block bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-6 py-3 rounded-xl mb-6">
                            <span class="text-[10px] uppercase font-bold text-slate-400 tracking-widest">Status Simpanan Pokok</span>
                            <p class="text-2xl font-bold text-rose-600 dark:text-rose-400 mt-1">Belum Terbayar</p>
                        </div>
                        <div>
                            <button wire:click="openPokokModal" class="bg-primary hover:bg-indigo-700 text-white px-6 py-3 rounded-xl text-sm font-bold shadow-lg transition-all flex items-center gap-2 mx-auto">
                                <i class='bx bx-plus-circle text-lg'></i> + Input Setoran Simpanan Pokok
                            </button>
                        </div>
                    @else
                        <div class="w-16 h-16 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 rounded-full flex items-center justify-center text-3xl mx-auto mb-4">
                            <i class='bx bx-check-circle'></i>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Simpanan Pokok Terbayar</h3>
                        <p class="text-sm text-slate-500 mt-2 mb-6">
                            Anggota ini telah melunasi simpanan pokok. Dana ini bersifat tidak dapat ditarik kecuali anggota mengundurkan diri.
                        </p>
                        <div class="inline-block bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-6 py-3 rounded-xl mb-6">
                            <span class="text-[10px] uppercase font-bold text-slate-400 tracking-widest">Saldo Simpanan Pokok</span>
                            <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">Rp {{ number_format($member->simpananPokok, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <button wire:click="openPokokModal" class="bg-slate-800 hover:bg-slate-900 text-white px-5 py-2.5 rounded-lg text-xs font-bold transition-colors inline-flex items-center gap-2">
                                <i class='bx bx-plus-circle'></i> + Tambah Setoran Pokok
                            </button>
                        </div>
                    @endif

                    @if($pokokTransactions->count() > 0)
                        <div class="mt-8 pt-8 border-t border-slate-100 dark:border-slate-700 text-left">
                            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">Riwayat Transaksi Simpanan Pokok</p>
                            <div class="space-y-2">
                                @foreach($pokokTransactions as $trx)
                                    <div class="flex justify-between items-center p-3 bg-slate-50 dark:bg-slate-800/50 rounded-lg text-xs">
                                        <div>
                                            <p class="font-bold text-slate-800 dark:text-white">{{ $trx->notes ?? 'Setoran Simpanan Pokok' }}</p>
                                            <p class="text-[10px] text-slate-400">{{ $trx->created_at->format('d M Y, H:i') }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-bold font-mono text-emerald-600">+ Rp {{ number_format($trx->amount, 0, ',', '.') }}</p>
                                            <p class="text-[10px] text-slate-400">Petugas: {{ $trx->processor->name ?? 'System' }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <!-- Modal: Pokok -->
    @if($showPokokModal)
        <div class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-darkCard w-full max-w-sm rounded-xl p-6 shadow-2xl">
                <h3 class="font-bold text-lg mb-4 dark:text-white">Input Setoran Simpanan Pokok</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="text-xs text-slate-500 block mb-1">Nominal (Rp)</label>
                        <input type="number" wire:model="pokokAmount"
                            class="w-full border rounded-lg p-2 text-sm dark:bg-slate-800 dark:border-slate-600 dark:text-white">
                        @error('pokokAmount') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
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
                    <button wire:click="closePokokModal"
                        class="px-4 py-2 text-sm font-bold text-slate-500 hover:bg-slate-100 rounded-lg">Batal</button>
                    <button wire:click="submitPokok" wire:loading.attr="disabled"
                        class="px-4 py-2 text-sm font-bold text-white bg-primary hover:bg-indigo-700 rounded-lg disabled:opacity-50">
                        <span wire:loading.remove wire:target="submitPokok">Simpan</span>
                        <span wire:loading wire:target="submitPokok">Menyimpan...</span>
                    </button>
        </div>
    @endif

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

    <!-- Modal Audit: Edit Nominal Periode -->
    @if($showEditPeriodModal)
        <div class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 animate-fade-in">
            <div class="bg-white dark:bg-darkCard w-full max-w-sm rounded-2xl p-6 shadow-2xl border border-slate-200 dark:border-slate-700">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-700 pb-3 mb-4">
                    <h3 class="font-bold text-base text-slate-900 dark:text-white flex items-center gap-2">
                        <i class='bx bx-edit text-indigo-600 text-xl'></i>
                        Edit Setoran Periode
                    </h3>
                    <button wire:click="closeEditPeriodModal" class="text-slate-400 hover:text-slate-600 dark:hover:text-white">
                        <i class='bx bx-x text-xl'></i>
                    </button>
                </div>

                <div class="space-y-4">
                    <div class="p-3 bg-indigo-50 dark:bg-indigo-950/40 rounded-xl border border-indigo-100 dark:border-indigo-800/40">
                        <div class="text-[10px] font-bold text-indigo-500 uppercase tracking-wider">Periode Tambungan</div>
                        <div class="font-extrabold text-slate-800 dark:text-white text-sm mt-0.5">{{ $editPeriodMonthName }}</div>
                    </div>

                    <div>
                        <label class="text-xs font-bold text-slate-700 dark:text-slate-300 block mb-1">Nominal Setoran (Rp)</label>
                        <input type="number" wire:model="editPeriodAmount" step="1000"
                            class="w-full border rounded-xl p-2.5 text-sm font-bold dark:bg-slate-800 dark:border-slate-600 dark:text-white focus:ring-2 focus:ring-primary/20 outline-none"
                            placeholder="0">
                        <p class="text-[10px] text-slate-400 mt-1">Masukkan 0 untuk mengosongkan / membatalkan setoran bulan ini.</p>
                        @error('editPeriodAmount') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="text-xs font-bold text-slate-700 dark:text-slate-300 block mb-1">Catatan Audit Admin</label>
                        <textarea wire:model="editPeriodNotes" rows="2"
                            class="w-full border rounded-xl p-2 text-xs dark:bg-slate-800 dark:border-slate-600 dark:text-white focus:ring-2 focus:ring-primary/20 outline-none"
                            placeholder="Catatan penyesuaian audit..."></textarea>
                    </div>
                </div>

                <div class="flex gap-2 justify-end mt-5 pt-3 border-t border-slate-100 dark:border-slate-700">
                    <button wire:click="closeEditPeriodModal"
                        class="px-4 py-2 text-xs font-bold text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl">Batal</button>
                    <button wire:click="saveEditPeriod" wire:loading.attr="disabled"
                        class="px-5 py-2 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-sm flex items-center gap-1.5">
                        <i class='bx bx-check-circle text-base' wire:loading.remove wire:target="saveEditPeriod"></i>
                        <i class='bx bx-loader-alt animate-spin text-base' wire:loading wire:target="saveEditPeriod"></i>
                        <span>Simpan Perubahan</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
