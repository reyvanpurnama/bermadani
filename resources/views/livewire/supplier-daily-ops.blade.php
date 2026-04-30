<div class="space-y-6 pb-28 md:pb-8">
    @php
        $summary = $this->selectedDateSummary;
        $roster = collect($this->visibleSupplierRoster);
        $detail = $this->selectedSupplierDailyDetail;
        $selectedSupplier = $this->selectedSupplier;
        $countPreview = $this->countPreview;
        $outstanding = $this->outstandingPayable;
        $isLocked = (bool) ($detail['lockStatus'] ?? false);
        $isFinalized = (bool) ($summary['isFinalized'] ?? false);
        $mobileView = $this->mobileView;
    @endphp

    <section class="bg-white dark:bg-darkCard border border-slate-100 dark:border-slate-700 rounded-2xl p-4 sm:p-6 shadow-sm space-y-4">
        <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">Supplier Daily Ops</p>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white mt-1">Roster Operasional Supplier Per Tanggal</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Fokus satu tanggal: pantau semua supplier, input cepat, dan audit status harian dalam satu layar.</p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <button type="button" wire:click="navigateDate(-1)"
                    class="inline-flex items-center justify-center h-10 w-10 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800">
                    <i class='bx bx-chevron-left text-lg'></i>
                </button>
                <input type="date" wire:model.live="selectedDate"
                    class="h-10 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 text-sm text-slate-700 dark:text-white">
                <button type="button" wire:click="navigateDate(1)"
                    class="inline-flex items-center justify-center h-10 w-10 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800">
                    <i class='bx bx-chevron-right text-lg'></i>
                </button>

                @if(!$isFinalized)
                    <button type="button" wire:click="finalizeDate"
                        class="inline-flex items-center gap-1.5 h-10 px-4 rounded-xl border border-emerald-200 dark:border-emerald-700 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-300 text-xs font-bold uppercase tracking-wider">
                        <i class='bx bx-check-circle'></i> Finalisasi Tanggal
                    </button>
                @elseif($this->canReopenFinalization)
                    <button type="button" wire:click="reopenDate"
                        class="inline-flex items-center gap-1.5 h-10 px-4 rounded-xl border border-amber-200 dark:border-amber-700 bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-300 text-xs font-bold uppercase tracking-wider">
                        <i class='bx bx-undo'></i> Buka Finalisasi
                    </button>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 gap-3">
            <div class="rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 p-3">
                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Supplier</p>
                <p class="text-lg font-bold text-slate-900 dark:text-white mt-1">{{ $summary['supplierTotal'] }}</p>
                <p class="text-[11px] text-slate-500">Diproses: {{ $summary['processedSuppliers'] }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 p-3">
                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Qty Masuk / Terjual</p>
                <p class="text-sm font-bold text-slate-900 dark:text-white mt-1">{{ number_format($summary['stockInQty']) }} / {{ number_format($summary['soldQty']) }}</p>
                <p class="text-[11px] text-slate-500">Tidak kirim: {{ $summary['noDeliverySuppliers'] }}</p>
            </div>
            <div class="rounded-xl border border-emerald-100 dark:border-emerald-900 bg-emerald-50/70 dark:bg-emerald-900/20 p-3">
                <p class="text-[10px] font-bold uppercase tracking-widest text-emerald-600">Omzet Supplier Ops</p>
                <p class="text-sm font-bold text-emerald-700 dark:text-emerald-300 mt-1">Rp {{ number_format($summary['incomeSupplierOps'], 0, ',', '.') }}</p>
            </div>
            <div class="rounded-xl border border-rose-100 dark:border-rose-900 bg-rose-50/70 dark:bg-rose-900/20 p-3">
                <p class="text-[10px] font-bold uppercase tracking-widest text-rose-600">Payout Supplier Ops</p>
                <p class="text-sm font-bold text-rose-700 dark:text-rose-300 mt-1">Rp {{ number_format($summary['expenseSupplierOps'], 0, ',', '.') }}</p>
            </div>
            <div class="rounded-xl border border-blue-100 dark:border-blue-900 bg-blue-50/70 dark:bg-blue-900/20 p-3">
                <p class="text-[10px] font-bold uppercase tracking-widest text-blue-600">Net Harian</p>
                <p class="text-sm font-bold {{ $summary['netSupplierOps'] >= 0 ? 'text-blue-700 dark:text-blue-300' : 'text-rose-700 dark:text-rose-300' }} mt-1">Rp {{ number_format($summary['netSupplierOps'], 0, ',', '.') }}</p>
            </div>
        </div>

        <div class="xl:hidden rounded-xl border border-slate-200 dark:border-slate-700 p-1 grid grid-cols-2 gap-1 bg-slate-50 dark:bg-slate-800/60">
            <button type="button" wire:click="setMobileView('roster')"
                class="h-10 rounded-lg text-xs font-bold transition-colors {{ $mobileView === 'roster' ? 'bg-white dark:bg-slate-700 text-primary shadow-sm' : 'text-slate-600 dark:text-slate-300' }}">
                Roster
            </button>
            <button type="button" wire:click="setMobileView('detail')"
                class="h-10 rounded-lg text-xs font-bold transition-colors {{ $mobileView === 'detail' ? 'bg-white dark:bg-slate-700 text-primary shadow-sm' : 'text-slate-600 dark:text-slate-300' }}">
                Detail {{ $selectedSupplier ? '· ' . \Illuminate\Support\Str::limit($selectedSupplier->businessName, 12) : '' }}
            </button>
        </div>
    </section>

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-5">
        <section class="xl:col-span-7 bg-white dark:bg-darkCard border border-slate-100 dark:border-slate-700 rounded-2xl shadow-sm overflow-hidden {{ $mobileView === 'detail' ? 'hidden xl:block' : '' }}">
            <div class="px-4 sm:px-5 py-4 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between gap-3">
                <div>
                    <h2 class="text-sm font-bold text-slate-900 dark:text-white">Roster Supplier {{ \Illuminate\Support\Carbon::parse($selectedDate)->translatedFormat('d M Y') }}</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Klik supplier untuk buka panel detail input/rekap/payout.</p>
                </div>
                <span class="text-[10px] font-bold uppercase tracking-wider {{ $isFinalized ? 'text-emerald-600' : 'text-amber-600' }}">
                    {{ $isFinalized ? 'Tanggal Finalized' : 'Belum Finalized' }}
                </span>
            </div>

            <div class="px-4 sm:px-5 py-3 border-b border-slate-100 dark:border-slate-700 grid grid-cols-1 md:grid-cols-3 gap-2">
                <div class="md:col-span-2 relative">
                    <i class='bx bx-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400'></i>
                    <input type="text" wire:model.live.debounce.250ms="supplierSearch" placeholder="Cari supplier..."
                        class="w-full h-10 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl pl-9 pr-3 text-sm text-slate-700 dark:text-white">
                </div>
                <div>
                    <select wire:model.live="rosterStatusFilter"
                        class="w-full h-10 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 text-sm text-slate-700 dark:text-white">
                        <option value="all">Semua Status</option>
                        <option value="pending">Belum Diproses</option>
                        <option value="stock_in">Stok Masuk</option>
                        <option value="recap">Rekap</option>
                        <option value="payout_partial">Payout Parsial</option>
                        <option value="locked">Lunas (Locked)</option>
                        <option value="no_delivery">Tidak Kirim</option>
                    </select>
                </div>
            </div>

            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-xs text-left text-slate-600 dark:text-slate-300">
                    <thead class="bg-slate-50 dark:bg-slate-800/60 border-b border-slate-100 dark:border-slate-700 uppercase tracking-wider text-slate-400 font-bold">
                        <tr>
                            <th class="px-4 py-2.5">Supplier</th>
                            <th class="px-3 py-2.5">Status</th>
                            <th class="px-3 py-2.5 text-right">Item/Qty Masuk</th>
                            <th class="px-3 py-2.5 text-right">Qty Terjual</th>
                            <th class="px-3 py-2.5 text-right">Hak Supplier</th>
                            <th class="px-3 py-2.5 text-right">Payout Hari Ini</th>
                            <th class="px-3 py-2.5 text-right">Outstanding</th>
                            <th class="px-4 py-2.5 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse($roster as $row)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors {{ $row['isSelected'] ? 'bg-indigo-50/70 dark:bg-indigo-900/10' : '' }}">
                                <td class="px-4 py-3 align-top">
                                    <p class="font-semibold text-slate-800 dark:text-white text-sm">{{ $row['supplierName'] }}</p>
                                    @if($row['locked'])
                                        <p class="text-[10px] text-emerald-600 font-semibold mt-0.5">Locked</p>
                                    @endif
                                </td>
                                <td class="px-3 py-3 align-top">
                                    <span class="inline-flex px-2 py-0.5 rounded-full border text-[10px] font-bold {{ $row['statusClass'] }}">{{ $row['statusLabel'] }}</span>
                                </td>
                                <td class="px-3 py-3 align-top text-right font-semibold">{{ $row['stockInItems'] }} / {{ $row['stockInQty'] }}</td>
                                <td class="px-3 py-3 align-top text-right font-semibold">{{ $row['soldQty'] }}</td>
                                <td class="px-3 py-3 align-top text-right font-semibold text-blue-700 dark:text-blue-300">Rp {{ number_format($row['payableToday'], 0, ',', '.') }}</td>
                                <td class="px-3 py-3 align-top text-right font-semibold text-rose-700 dark:text-rose-300">Rp {{ number_format($row['payoutToday'], 0, ',', '.') }}</td>
                                <td class="px-3 py-3 align-top text-right font-semibold">Rp {{ number_format($row['outstandingCurrent'], 0, ',', '.') }}</td>
                                <td class="px-4 py-3 align-top text-center">
                                    <div class="inline-flex items-center gap-1">
                                        <button type="button" wire:click="selectSupplier({{ $row['supplierId'] }}, 'stock-in')" class="px-2 py-1 rounded-lg border border-slate-200 dark:border-slate-700 text-[10px] font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800">Stok</button>
                                        <button type="button" wire:click="selectSupplier({{ $row['supplierId'] }}, 'recap')" class="px-2 py-1 rounded-lg border border-slate-200 dark:border-slate-700 text-[10px] font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800">Rekap</button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-8 text-center text-slate-400">Belum ada supplier aktif.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="md:hidden p-4 space-y-2">
                @forelse($roster as $row)
                    <button type="button" wire:click="selectSupplier({{ $row['supplierId'] }})"
                        class="w-full text-left rounded-xl border p-3.5 min-h-[84px] {{ $row['isSelected'] ? 'border-primary bg-indigo-50 dark:bg-indigo-900/20' : 'border-slate-200 dark:border-slate-700' }}">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $row['supplierName'] }}</p>
                                <p class="text-[11px] text-slate-500 mt-0.5">Masuk {{ $row['stockInQty'] }} · Terjual {{ $row['soldQty'] }} · Outstanding Rp {{ number_format($row['outstandingCurrent'], 0, ',', '.') }}</p>
                            </div>
                            <span class="inline-flex px-2 py-0.5 rounded-full border text-[10px] font-bold {{ $row['statusClass'] }}">{{ $row['statusLabel'] }}</span>
                        </div>
                    </button>
                @empty
                    <div class="rounded-xl border border-dashed border-slate-300 dark:border-slate-700 p-4 text-center text-xs text-slate-500">Belum ada supplier aktif.</div>
                @endforelse
            </div>
        </section>

        <section class="xl:col-span-5 space-y-4 {{ $mobileView === 'roster' ? 'hidden xl:block' : '' }}">
            <div class="bg-white dark:bg-darkCard border border-slate-100 dark:border-slate-700 rounded-2xl shadow-sm p-4 sm:p-5">
                @if(!$selectedSupplier)
                    <div class="text-center py-10">
                        <div class="mx-auto mb-3 w-12 h-12 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400">
                            <i class='bx bx-store text-2xl'></i>
                        </div>
                        <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">Pilih supplier dari roster</p>
                        <p class="text-xs text-slate-500 mt-1">Panel ini akan menampilkan form stok masuk, rekap, payout, dan riwayat tanggal aktif.</p>
                    </div>
                @else
                    <div class="space-y-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-slate-400">Detail Supplier</p>
                                <h3 class="text-lg font-bold text-slate-900 dark:text-white mt-1">{{ $selectedSupplier->businessName }}</h3>
                                <p class="text-xs text-slate-500">Tanggal kerja: {{ \Illuminate\Support\Carbon::parse($selectedDate)->translatedFormat('d F Y') }}</p>
                            </div>
                            <button type="button" wire:click="setMobileView('roster')"
                                class="xl:hidden inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 text-[11px] font-bold text-slate-600 dark:text-slate-300">
                                <i class='bx bx-arrow-back'></i> Roster
                            </button>
                            <div class="flex items-center gap-2">
                                <button type="button" wire:click="setTab('stock-in')" class="px-2.5 py-1 rounded-lg text-[11px] font-bold {{ $tab === 'stock-in' ? 'bg-primary text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300' }}">Stok</button>
                                <button type="button" wire:click="setTab('recap')" class="px-2.5 py-1 rounded-lg text-[11px] font-bold {{ $tab === 'recap' ? 'bg-primary text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300' }}">Rekap</button>
                            </div>
                        </div>

                        @if($isLocked)
                            <div class="rounded-xl border border-emerald-200 dark:border-emerald-700 bg-emerald-50 dark:bg-emerald-900/20 px-3 py-2.5 text-xs text-emerald-700 dark:text-emerald-300 flex items-start gap-2">
                                <i class='bx bx-lock-alt text-sm mt-0.5'></i>
                                <p>Data supplier tanggal ini sudah lunas (locked). Form menjadi read-only.</p>
                            </div>
                        @endif

                        @if($tab === 'stock-in')
                            <div class="space-y-3">
                                <div class="flex items-center justify-between gap-2">
                                    <p class="text-sm font-bold text-slate-800 dark:text-white">Input Stok Masuk</p>
                                    <button type="button" wire:click="copyPreviousDayDraft" @disabled($isLocked)
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg border border-blue-200 dark:border-blue-700 bg-blue-50 dark:bg-blue-900/20 text-[11px] font-bold text-blue-700 dark:text-blue-300 disabled:opacity-50 disabled:cursor-not-allowed">
                                        <i class='bx bx-copy'></i> Copy Draft H-1
                                    </button>
                                </div>

                                <div class="space-y-2">
                                    @foreach($stockItems as $index => $row)
                                        <div class="rounded-xl border border-slate-200 dark:border-slate-700 p-3 space-y-2">
                                            <select wire:model.live="stockItems.{{ $index }}.productId" @disabled($isLocked)
                                                class="w-full min-h-[42px] bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg px-3 text-sm disabled:opacity-60">
                                                <option value="">Pilih produk</option>
                                                @foreach($this->availableProducts as $product)
                                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                                @endforeach
                                            </select>
                                            <div class="grid grid-cols-2 gap-2">
                                                <input type="number" min="1" wire:model="stockItems.{{ $index }}.qty" @disabled($isLocked)
                                                    placeholder="Qty" class="w-full min-h-[42px] bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg px-3 text-sm disabled:opacity-60">
                                                <input type="number" min="0" step="0.01" placeholder="0" wire:model="stockItems.{{ $index }}.supplierPrice" @disabled($isLocked)
                                                    class="w-full min-h-[42px] bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg px-3 text-sm disabled:opacity-60 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                                            </div>
                                            @if(count($stockItems) > 1)
                                                <button type="button" wire:click="removeStockItem({{ $index }})" @disabled($isLocked)
                                                    class="text-[11px] font-semibold text-rose-600 hover:text-rose-700 disabled:opacity-50">Hapus</button>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>

                                <div class="flex items-center gap-2">
                                    <button type="button" wire:click="addStockItem" @disabled($isLocked)
                                        class="inline-flex items-center gap-1 px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-600 dark:text-slate-300 disabled:opacity-50">
                                        <i class='bx bx-plus'></i> Tambah
                                    </button>
                                    <button type="button" wire:click="saveStockIn" wire:loading.attr="disabled" wire:target="saveStockIn" @disabled($isLocked || ! $this->canSubmitStockIn)
                                        class="inline-flex items-center gap-1 px-3 py-2 rounded-lg bg-primary text-white text-xs font-bold disabled:opacity-50">
                                        <i class='bx bx-save' wire:loading.remove wire:target="saveStockIn"></i>
                                        <span wire:loading.remove wire:target="saveStockIn">Simpan Stok</span>
                                        <span wire:loading wire:target="saveStockIn">Menyimpan...</span>
                                    </button>
                                </div>
                            </div>
                        @else
                            <div class="space-y-3">
                                <p class="text-sm font-bold text-slate-800 dark:text-white">Rekap & Payout</p>

                                @if(empty($countItems))
                                    <div class="rounded-xl border border-dashed border-slate-300 dark:border-slate-700 p-4 text-center text-xs text-slate-500">
                                        Tidak ada stok aktif untuk direkap.
                                    </div>
                                @else
                                    <div class="space-y-2 max-h-56 overflow-y-auto pr-1">
                                        @foreach($countItems as $index => $row)
                                            @php $delta = max(0, (int) $row['beforeQty'] - (int) $row['physicalQty']); @endphp
                                            <div class="rounded-xl border border-slate-200 dark:border-slate-700 p-2.5">
                                                <p class="text-xs font-semibold text-slate-800 dark:text-slate-200">{{ $row['productName'] }}</p>
                                                <div class="mt-1 grid grid-cols-3 gap-2 items-end">
                                                    <div>
                                                        <p class="text-[10px] text-slate-400">Tercatat</p>
                                                        <p class="text-xs font-bold">{{ $row['beforeQty'] }}</p>
                                                    </div>
                                                    <div>
                                                        <p class="text-[10px] text-slate-400">Fisik</p>
                                                        <div class="flex items-center gap-1">
                                                            <button type="button" wire:click="decrementPhysicalQty({{ $index }})" @disabled($isLocked)
                                                                class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-md border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 disabled:opacity-50 disabled:cursor-not-allowed">
                                                                <i class='bx bx-minus text-sm'></i>
                                                            </button>
                                                            <input type="number" min="0" max="{{ $row['beforeQty'] }}" wire:model.live="countItems.{{ $index }}.physicalQty" @disabled($isLocked)
                                                                class="w-full h-8 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-md px-2 text-xs text-center font-semibold disabled:opacity-60 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                                                            <button type="button" wire:click="incrementPhysicalQty({{ $index }})" @disabled($isLocked)
                                                                class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-md border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 disabled:opacity-50 disabled:cursor-not-allowed">
                                                                <i class='bx bx-plus text-sm'></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="text-right">
                                                        <p class="text-[10px] text-slate-400">Delta</p>
                                                        <p class="text-xs font-bold {{ $delta > 0 ? 'text-emerald-600' : 'text-slate-500' }}">{{ $delta }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                <div class="grid grid-cols-2 gap-2 text-xs">
                                    <div class="rounded-lg bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-800 p-2">
                                        <p class="text-emerald-600 font-bold uppercase text-[10px]">Omzet</p>
                                        <p class="font-bold text-emerald-700 dark:text-emerald-300 mt-0.5">Rp {{ number_format($countPreview['omzet'], 0, ',', '.') }}</p>
                                    </div>
                                    <div class="rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800 p-2">
                                        <p class="text-blue-600 font-bold uppercase text-[10px]">Hak Supplier</p>
                                        <p class="font-bold text-blue-700 dark:text-blue-300 mt-0.5">Rp {{ number_format($countPreview['payable'], 0, ',', '.') }}</p>
                                    </div>
                                    <div class="rounded-lg bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 p-2">
                                        <p class="text-slate-500 font-bold uppercase text-[10px]">Terjual</p>
                                        <p class="font-bold text-slate-800 dark:text-slate-100 mt-0.5">{{ $countPreview['soldQty'] }}</p>
                                    </div>
                                    <div class="rounded-lg bg-rose-50 dark:bg-rose-900/20 border border-rose-100 dark:border-rose-800 p-2">
                                        <p class="text-rose-600 font-bold uppercase text-[10px]">Outstanding</p>
                                        <p class="font-bold text-rose-700 dark:text-rose-300 mt-0.5">Rp {{ number_format($outstanding, 0, ',', '.') }}</p>
                                    </div>
                                    <div class="col-span-2 rounded-lg bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-100 dark:border-indigo-800 p-2.5 text-center">
                                        <p class="text-indigo-600 font-bold uppercase text-[10px]">Keuntungan</p>
                                        <p class="font-bold text-indigo-700 dark:text-indigo-300 mt-0.5">Rp {{ number_format($countPreview['margin'], 0, ',', '.') }}</p>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-[11px] font-bold uppercase tracking-widest text-slate-400 mb-1.5">Nominal Dibayar</label>
                                    <div class="flex items-center gap-2">
                                        <input type="number" min="0" step="0.01" placeholder="0" wire:model="payNowAmount" @disabled($isLocked)
                                            class="w-full min-h-[42px] bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg px-3 text-sm font-semibold disabled:opacity-60 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                                        <button type="button" wire:click="fillPayNowFromSupplierRights" @disabled($isLocked)
                                            class="inline-flex shrink-0 items-center gap-1 px-2.5 min-h-[42px] rounded-lg border border-blue-200 dark:border-blue-700 bg-blue-50 dark:bg-blue-900/20 text-[11px] font-bold text-blue-700 dark:text-blue-300 disabled:opacity-50">
                                            <i class='bx bx-transfer-alt'></i> Isi Hak
                                        </button>
                                    </div>
                                    <div class="mt-2 flex items-center gap-2">
                                        <button type="button" wire:click="refreshPayNowDefault" @disabled($isLocked)
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 text-[11px] font-bold text-slate-600 dark:text-slate-300 disabled:opacity-50">
                                            <i class='bx bx-reset'></i> Set Nominal Penuh
                                        </button>
                                        <button type="button" wire:click="saveRecapAndPayout" wire:loading.attr="disabled" wire:target="saveRecapAndPayout" @disabled($isLocked || ! $this->canSubmitRecap)
                                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-primary text-white text-[11px] font-bold disabled:opacity-50">
                                            <i class='bx bx-save' wire:loading.remove wire:target="saveRecapAndPayout"></i>
                                            <span wire:loading.remove wire:target="saveRecapAndPayout">Simpan Rekap & Payout</span>
                                            <span wire:loading wire:target="saveRecapAndPayout">Menyimpan...</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            @if($selectedSupplier)
                <div class="bg-white dark:bg-darkCard border border-slate-100 dark:border-slate-700 rounded-2xl shadow-sm p-4 sm:p-5 space-y-3">
                    <h4 class="text-sm font-bold text-slate-900 dark:text-white">Riwayat Tanggal Ini</h4>

                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1">Batch Stok Masuk</p>
                        <div class="space-y-1.5 max-h-32 overflow-y-auto pr-1">
                            @forelse($detail['todayBatches'] as $batch)
                                <div class="rounded-lg border border-slate-200 dark:border-slate-700 p-2 text-xs">
                                    <p class="font-semibold text-slate-800 dark:text-slate-200">{{ $batch->batchCode }}</p>
                                    <p class="text-slate-500">{{ $batch->items->count() }} item · Qty {{ $batch->items->sum('receivedQty') }}</p>
                                </div>
                            @empty
                                <p class="text-xs text-slate-500">Belum ada batch masuk.</p>
                            @endforelse
                        </div>
                    </div>

                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1">Payout Hari Ini</p>
                        <div class="space-y-1.5 max-h-32 overflow-y-auto pr-1">
                            @forelse($detail['todayPayouts'] as $payout)
                                <div class="rounded-lg border border-slate-200 dark:border-slate-700 p-2 text-xs">
                                    <p class="font-semibold text-slate-800 dark:text-slate-200">{{ $payout->payoutCode }}</p>
                                    <p class="text-slate-500">Dibayar Rp {{ number_format($payout->paidAmount, 0, ',', '.') }}</p>
                                </div>
                            @empty
                                <p class="text-xs text-slate-500">Belum ada payout tanggal ini.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            @endif
        </section>
    </div>
</div>
