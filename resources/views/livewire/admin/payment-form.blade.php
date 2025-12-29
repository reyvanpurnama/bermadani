<div>
    {{-- Page Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Input Pembayaran Simpanan</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Catat pembayaran simpanan dari anggota</p>
    </div>

    {{-- Flash Messages --}}
    @if (session()->has('error'))
        <div class="mb-6 p-4 bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/30 text-rose-700 dark:text-rose-400 rounded-xl flex items-center gap-3 shadow-sm">
            <i class='bx bxs-error-circle text-2xl'></i>
            <span class="font-medium">{{ session('error') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Main Form Column --}}
        <div class="lg:col-span-2 space-y-6">
            
            {{-- Section: Pilih Anggota --}}
            <div class="bg-white dark:bg-darkCard rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
                <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2">
                    Pilih Anggota
                </label>
                <div class="relative">
                    <select wire:model.live="selectedMemberId"
                            class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-white focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-colors cursor-pointer appearance-none">
                        <option value="">-- Cari Nama / No. Anggota --</option>
                        @foreach($members as $member)
                            <option value="{{ $member->id }}">
                                {{ $member->name }} - {{ $member->nomorAnggota }} ({{ $member->unitKerja }})
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-4 flex items-center pointer-events-none text-slate-500">
                        <i class='bx bx-chevron-down text-xl'></i>
                    </div>
                </div>
                @error('selectedMemberId')
                    <p class="text-xs text-rose-500 mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            {{-- Section: Tabel Tagihan --}}
            @if(count($unpaidBills) > 0)
                <div class="bg-white dark:bg-darkCard rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                    <div class="p-6 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
                        <h3 class="text-sm font-bold text-slate-800 dark:text-white flex items-center gap-2">
                            <i class='bx bx-list-check text-lg text-primary'></i> Tagihan Belum Lunas
                        </h3>
                        <span class="text-[10px] bg-rose-50 text-rose-600 dark:bg-rose-900/20 px-2 py-1 rounded font-bold uppercase border border-rose-100 dark:border-rose-900">
                            {{ count($unpaidBills) }} Tunggakan
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-slate-50 dark:bg-slate-800/50 text-[10px] uppercase font-bold text-slate-500 dark:text-slate-400">
                                <tr>
                                    <th class="px-6 py-3 w-10">
                                        <input type="checkbox" 
                                               wire:click="toggleAllBills"
                                               {{ count($selectedBills) === count($unpaidBills) ? 'checked' : '' }}
                                               class="rounded border-gray-300 text-primary focus:ring-primary w-4 h-4 cursor-pointer">
                                    </th>
                                    <th class="px-6 py-3">Bulan</th>
                                    <th class="px-6 py-3">Jenis</th>
                                    <th class="px-6 py-3 text-right">Tagihan</th>
                                    <th class="px-6 py-3 text-right">Sisa Bayar</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-700 text-[13px]">
                                @foreach($unpaidBills as $bill)
                                    <tr class="group hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors {{ in_array($bill['id'], $selectedBills) ? 'bg-indigo-50 dark:bg-indigo-900/20' : '' }}">
                                        <td class="px-6 py-4">
                                            <input type="checkbox" 
                                                   value="{{ $bill['id'] }}" 
                                                   wire:model.live="selectedBills"
                                                   class="rounded border-gray-300 text-primary focus:ring-primary w-4 h-4 cursor-pointer">
                                        </td>
                                        <td class="px-6 py-4 font-medium text-slate-800 dark:text-white">
                                            {{ $bill['billingMonthFormatted'] }}
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($bill['type'] === 'WAJIB')
                                                <span class="px-2 py-0.5 text-[10px] font-bold rounded bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300 border border-blue-100 dark:border-blue-800">S. WAJIB</span>
                                            @elseif($bill['type'] === 'POKOK')
                                                <span class="px-2 py-0.5 text-[10px] font-bold rounded bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300 border border-indigo-100 dark:border-indigo-800">S. POKOK</span>
                                            @else
                                                <span class="px-2 py-0.5 text-[10px] font-bold rounded bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300 border border-emerald-100 dark:border-emerald-800">S. SUKARELA</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-right text-slate-500">
                                            Rp {{ number_format($bill['amount'], 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <div class="flex items-center justify-end gap-2">
                                                <span class="font-bold text-rose-500">
                                                    Rp {{ number_format($bill['remainingAmount'], 0, ',', '.') }}
                                                </span>
                                                @if($bill['paymentStatus'] === 'PARTIAL')
                                                    <span class="px-1.5 py-0.5 text-[9px] font-bold rounded bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300 border border-amber-100 dark:border-amber-800">CICILAN</span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @elseif($selectedMemberId)
                <div class="bg-white dark:bg-darkCard rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-12 text-center">
                    <i class='bx bx-check-circle text-6xl text-emerald-500 mb-4'></i>
                    <p class="text-slate-500 dark:text-slate-400 font-medium">Tidak ada tunggakan untuk anggota ini</p>
                </div>
            @endif

            {{-- Section: Detail Pembayaran --}}
            @if(count($selectedBills) > 0)
                <div class="bg-white dark:bg-darkCard rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
                    <h3 class="text-sm font-bold text-slate-800 dark:text-white mb-6 flex items-center gap-2">
                        <i class='bx bx-money text-lg text-primary'></i> Detail Transaksi
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        {{-- Metode Pembayaran --}}
                        <div class="md:col-span-2">
                            <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-3">Metode Pembayaran</label>
                            <div class="grid grid-cols-3 gap-3">
                                <label class="cursor-pointer group">
                                    <input type="radio" wire:model.live="paymentMethod" value="CASH" class="peer sr-only">
                                    <div class="px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 text-center hover:bg-slate-50 dark:hover:bg-slate-800 peer-checked:border-primary peer-checked:bg-indigo-50 dark:peer-checked:bg-indigo-900/20 peer-checked:text-primary dark:peer-checked:text-indigo-400 transition-all">
                                        <i class='bx bx-money text-xl mb-1 block'></i>
                                        <span class="text-xs font-bold">Tunai</span>
                                    </div>
                                </label>
                                <label class="cursor-pointer group">
                                    <input type="radio" wire:model.live="paymentMethod" value="TRANSFER" class="peer sr-only">
                                    <div class="px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 text-center hover:bg-slate-50 dark:hover:bg-slate-800 peer-checked:border-primary peer-checked:bg-indigo-50 dark:peer-checked:bg-indigo-900/20 peer-checked:text-primary dark:peer-checked:text-indigo-400 transition-all">
                                        <i class='bx bx-transfer text-xl mb-1 block'></i>
                                        <span class="text-xs font-bold">Transfer</span>
                                    </div>
                                </label>
                                <label class="cursor-pointer group">
                                    <input type="radio" wire:model.live="paymentMethod" value="AUTO_DEBIT" class="peer sr-only">
                                    <div class="px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 text-center hover:bg-slate-50 dark:hover:bg-slate-800 peer-checked:border-primary peer-checked:bg-indigo-50 dark:peer-checked:bg-indigo-900/20 peer-checked:text-primary dark:peer-checked:text-indigo-400 transition-all">
                                        <i class='bx bx-credit-card text-xl mb-1 block'></i>
                                        <span class="text-xs font-bold">Potong Gaji</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        {{-- Tanggal Pembayaran --}}
                        <div>
                            <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1.5">Tanggal Bayar</label>
                            <input type="date" wire:model="paymentDate"
                                   class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-white focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-colors">
                            @error('paymentDate')
                                <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Transfer Fields --}}
                        @if($paymentMethod === 'TRANSFER')
                            <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6 p-4 bg-indigo-50/50 dark:bg-indigo-900/10 rounded-xl border border-indigo-100 dark:border-indigo-800/30">
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1.5">
                                        Nomor Referensi <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="text" wire:model="referenceNumber" placeholder="Contoh: TRF-12345678"
                                           class="w-full px-3 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-white focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-colors">
                                    @error('referenceNumber')
                                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1.5">
                                        Bukti Transfer <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="file" wire:model="proofAttachment"
                                           class="block w-full text-[11px] text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-[11px] file:font-bold file:bg-primary file:text-white hover:file:bg-indigo-700 cursor-pointer">
                                    @error('proofAttachment')
                                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                                    @enderror
                                    <div wire:loading wire:target="proofAttachment" class="text-xs text-primary mt-1 flex items-center gap-1">
                                        <i class='bx bx-loader-alt bx-spin'></i> Uploading...
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Catatan --}}
                        <div class="md:col-span-2">
                            <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1.5">Catatan (Opsional)</label>
                            <textarea wire:model="notes" rows="2" placeholder="Keterangan tambahan..."
                                      class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-white focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-colors resize-none"></textarea>
                        </div>

                    </div>
                </div>
            @endif

        </div>

        {{-- Summary Sidebar --}}
        <div class="lg:col-span-1">
            <div class="sticky top-6 space-y-6">
                
                {{-- Total Summary Card --}}
                <div class="bg-primary text-white rounded-xl shadow-xl shadow-indigo-500/20 p-6 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-white opacity-10 rounded-bl-full -mr-10 -mt-10 pointer-events-none"></div>
                    
                    <h3 class="text-indigo-100 text-[10px] font-bold uppercase tracking-widest mb-1">Total Pembayaran</h3>
                    <div class="flex items-baseline gap-1 mb-6">
                        <span class="text-lg opacity-80">Rp</span>
                        <span class="text-4xl font-extrabold tracking-tight">{{ number_format($totalAmount, 0, ',', '.') }}</span>
                    </div>

                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between text-xs text-indigo-100 border-b border-indigo-400/30 pb-2">
                            <span>Item Dipilih</span>
                            <span class="font-bold">{{ $itemsCount }} Tagihan</span>
                        </div>
                        <div class="flex justify-between text-xs text-indigo-100">
                            <span>Admin Fee</span>
                            <span class="font-bold">Rp 0</span>
                        </div>
                    </div>

                    <button wire:click="processPayment" 
                            wire:loading.attr="disabled"
                            {{ count($selectedBills) === 0 ? 'disabled' : '' }}
                            class="w-full py-3.5 bg-white text-primary font-bold rounded-xl shadow-sm hover:bg-indigo-50 disabled:opacity-50 disabled:cursor-not-allowed transition-all flex items-center justify-center gap-2 group">
                        <span wire:loading.remove wire:target="processPayment">Proses Pembayaran</span>
                        <span wire:loading wire:target="processPayment">
                            <i class='bx bx-loader-alt bx-spin text-xl'></i> Memproses...
                        </span>
                        <i wire:loading.remove wire:target="processPayment" class='bx bx-right-arrow-alt text-xl group-hover:translate-x-1 transition-transform'></i>
                    </button>
                </div>

                {{-- Info Box --}}
                <div class="bg-amber-50 dark:bg-amber-500/10 border border-amber-100 dark:border-amber-500/20 rounded-xl p-4 flex gap-3">
                    <i class='bx bx-info-circle text-amber-500 text-xl shrink-0 mt-0.5'></i>
                    <div>
                        <h4 class="text-xs font-bold text-amber-800 dark:text-amber-400 mb-1">Informasi</h4>
                        <p class="text-[11px] text-amber-700 dark:text-amber-300 leading-relaxed">
                            Pastikan data tagihan yang dipilih sudah sesuai. Kuitansi akan terbit otomatis setelah pembayaran sukses.
                        </p>
                    </div>
                </div>

            </div>
        </div>

    </div>

</div>
