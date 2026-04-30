<div class="space-y-6 pb-32 md:pb-8">
    @php
        $countPreview = $this->countPreview;
        $outstanding = $this->outstandingPayable;
        $headerStats = $this->compactHeaderStats;
        $stepperSteps = $this->stepperSteps;
        $currentStep = $this->currentStep;
        $progressText = $this->stepProgressText;
        $step2SoftLocked = $this->step2SoftLocked;
        $stockDraftQty = collect($stockItems)->sum(fn ($item) => (int) ($item['qty'] ?? 0));
        $stockDraftValue = collect($stockItems)->sum(fn ($item) => max(0, (int) ($item['qty'] ?? 0)) * (float) ($item['supplierPrice'] ?? 0));
    @endphp

    <section class="bg-white dark:bg-darkCard border border-slate-100 dark:border-slate-700 rounded-2xl p-4 sm:p-6 shadow-sm space-y-5">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">Supplier Daily Ops</p>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white mt-1">Operasional Supplier Harian</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Alur cepat kasir untuk catat stok masuk, rekap stok fisik, dan proses payout supplier.</p>
            </div>
            <div class="inline-flex items-center gap-2 self-start rounded-xl border border-primary/20 bg-primary/5 px-3 py-2">
                <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-primary text-xs font-bold text-white">{{ $currentStep }}</span>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-primary/80">Progress</p>
                    <p class="text-xs font-semibold text-primary">{{ $progressText }}</p>
                </div>
            </div>
        </div>

        <div class="flex flex-wrap gap-2">
            <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-300 text-xs font-semibold">
                <i class='bx bx-trending-up'></i> Omzet Supplier Manual (Non-POS)
            </span>
            <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-rose-50 text-rose-700 dark:bg-rose-900/20 dark:text-rose-300 text-xs font-semibold">
                <i class='bx bx-money-withdraw'></i> Pembayaran Supplier Manual (Non-POS)
            </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="rounded-xl border border-emerald-100 dark:border-emerald-900 bg-emerald-50/70 dark:bg-emerald-900/20 p-3">
                <p class="text-[10px] font-bold uppercase tracking-widest text-emerald-600">Omzet (Preview)</p>
                <p class="text-base font-bold text-emerald-700 dark:text-emerald-300 mt-1">Rp {{ number_format($headerStats['omzet'], 0, ',', '.') }}</p>
            </div>
            <div class="rounded-xl border border-blue-100 dark:border-blue-900 bg-blue-50/70 dark:bg-blue-900/20 p-3">
                <p class="text-[10px] font-bold uppercase tracking-widest text-blue-600">Hak Supplier</p>
                <p class="text-base font-bold text-blue-700 dark:text-blue-300 mt-1">Rp {{ number_format($headerStats['payable'], 0, ',', '.') }}</p>
            </div>
            <div class="rounded-xl border border-rose-100 dark:border-rose-900 bg-rose-50/70 dark:bg-rose-900/20 p-3">
                <p class="text-[10px] font-bold uppercase tracking-widest text-rose-600">Outstanding</p>
                <p class="text-base font-bold text-rose-700 dark:text-rose-300 mt-1">Rp {{ number_format($headerStats['outstanding'], 0, ',', '.') }}</p>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-100 dark:border-slate-700 p-3 sm:p-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @foreach($stepperSteps as $step)
                    @php
                        $isActive = $step['status'] === 'active';
                        $isCompleted = $step['status'] === 'completed';
                        $isLocked = $step['status'] === 'locked';

                        $cardClass = $isActive
                            ? 'border-primary/30 bg-indigo-50 dark:bg-indigo-900/20'
                            : ($isCompleted
                                ? 'border-emerald-200 bg-emerald-50/70 dark:border-emerald-800 dark:bg-emerald-900/20'
                                : ($isLocked
                                    ? 'border-amber-200 bg-amber-50/70 dark:border-amber-800 dark:bg-amber-900/20'
                                    : 'border-slate-200 dark:border-slate-700 bg-slate-50/60 dark:bg-slate-800/50'));

                        $badgeClass = $isActive
                            ? 'bg-primary text-white'
                            : ($isCompleted
                                ? 'bg-emerald-600 text-white'
                                : ($isLocked
                                    ? 'bg-amber-500 text-white'
                                    : 'bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-200'));

                        $statusText = $isActive ? 'Aktif' : ($isCompleted ? 'Selesai' : ($isLocked ? 'Butuh Supplier' : 'Berikutnya'));
                    @endphp
                    <button type="button" wire:click="setTab('{{ $step['tab'] }}')"
                        class="w-full rounded-xl border px-4 py-3 text-left transition {{ $cardClass }}">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-slate-400">Langkah {{ $step['number'] }}</p>
                                <p class="text-sm font-bold text-slate-900 dark:text-white mt-1">{{ $step['title'] }}</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ $step['instruction'] }}</p>
                            </div>
                            <div class="flex flex-col items-end gap-1">
                                <span class="inline-flex h-6 w-6 items-center justify-center rounded-full text-[11px] font-bold {{ $badgeClass }}">
                                    @if($isCompleted)
                                        <i class='bx bx-check'></i>
                                    @elseif($isLocked)
                                        <i class='bx bx-lock-alt'></i>
                                    @else
                                        {{ $step['number'] }}
                                    @endif
                                </span>
                                <span class="text-[10px] font-bold uppercase tracking-wider {{ $isCompleted ? 'text-emerald-700 dark:text-emerald-300' : ($isLocked ? 'text-amber-700 dark:text-amber-300' : 'text-slate-500 dark:text-slate-300') }}">
                                    {{ $statusText }}
                                </span>
                            </div>
                        </div>
                    </button>
                @endforeach
            </div>
        </div>
    </section>

    @if($tab === 'stock-in')
        <div class="space-y-5">
            <section class="bg-white dark:bg-darkCard border border-slate-100 dark:border-slate-700 rounded-2xl p-4 sm:p-5 space-y-4">
                <div class="flex items-center justify-between gap-3">
                    <h2 class="text-sm font-bold text-slate-900 dark:text-white">Konteks</h2>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Step 1</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-widest text-slate-400 mb-2">Supplier</label>
                        <select wire:model.live="stockSupplierId" class="w-full min-h-[46px] bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 text-sm">
                            <option value="">Pilih supplier</option>
                            @foreach($this->suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->businessName }}</option>
                            @endforeach
                        </select>
                        @error('stockSupplierId') <p class="text-xs text-rose-500 mt-1.5">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-widest text-slate-400 mb-2">Tanggal</label>
                        <input type="date" wire:model="stockDate" class="w-full min-h-[46px] bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 text-sm">
                        @error('stockDate') <p class="text-xs text-rose-500 mt-1.5">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-widest text-slate-400 mb-2">Catatan</label>
                        <input type="text" wire:model="stockNote" placeholder="Opsional" class="w-full min-h-[46px] bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 text-sm">
                        @error('stockNote') <p class="text-xs text-rose-500 mt-1.5">{{ $message }}</p> @enderror
                    </div>
                </div>
            </section>

            <section class="bg-white dark:bg-darkCard border border-slate-100 dark:border-slate-700 rounded-2xl p-4 sm:p-5 space-y-4">
                <div class="flex items-center justify-between gap-3">
                    <h2 class="text-sm font-bold text-slate-900 dark:text-white">Form Stok Masuk</h2>
                    <span class="text-[11px] text-slate-500 dark:text-slate-400">{{ count($stockItems) }} item draft</span>
                </div>

                <div class="space-y-3">
                    @foreach($stockItems as $index => $row)
                        <div class="rounded-xl border border-slate-200 dark:border-slate-700 p-3 sm:p-4 space-y-3 bg-slate-50/40 dark:bg-slate-800/40">
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
                                <div class="md:col-span-6">
                                    <label class="block text-[11px] font-bold uppercase tracking-widest text-slate-400 mb-1.5">Produk</label>
                                    <select wire:model.live="stockItems.{{ $index }}.productId" class="w-full min-h-[44px] bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-3 text-sm">
                                        <option value="">Pilih produk</option>
                                        @foreach($this->availableProducts as $product)
                                            <option value="{{ $product->id }}">{{ $product->name }} · Jual Rp {{ number_format($product->sellPrice, 0, ',', '.') }}</option>
                                        @endforeach
                                    </select>
                                    @error('stockItems.' . $index . '.productId') <p class="text-xs text-rose-500 mt-1.5">{{ $message }}</p> @enderror
                                </div>

                                <div class="md:col-span-3">
                                    <label class="block text-[11px] font-bold uppercase tracking-widest text-slate-400 mb-1.5">Qty Masuk</label>
                                    <input type="number" min="1" wire:model="stockItems.{{ $index }}.qty" class="w-full min-h-[44px] bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-3 text-sm">
                                    @error('stockItems.' . $index . '.qty') <p class="text-xs text-rose-500 mt-1.5">{{ $message }}</p> @enderror
                                </div>

                                <div class="md:col-span-3">
                                    <label class="block text-[11px] font-bold uppercase tracking-widest text-slate-400 mb-1.5">Harga Produk</label>
                                    <input type="number" min="0" step="0.01" placeholder="0" wire:model="stockItems.{{ $index }}.supplierPrice" class="w-full min-h-[44px] bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-3 text-sm">
                                    <p class="text-[10px] text-slate-400 mt-1">Terisi otomatis dari data produk, tetap bisa disesuaikan.</p>
                                    @error('stockItems.' . $index . '.supplierPrice') <p class="text-xs text-rose-500 mt-1.5">{{ $message }}</p> @enderror
                                </div>
                            </div>
                            <button type="button" wire:click="removeStockItem({{ $index }})" class="inline-flex items-center gap-1 text-xs font-semibold text-rose-600 hover:text-rose-700" @disabled(count($stockItems) <= 1)>
                                <i class='bx bx-trash'></i> Hapus Item
                            </button>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="bg-white dark:bg-darkCard border border-slate-100 dark:border-slate-700 rounded-2xl p-4 sm:p-5 space-y-4">
                <h2 class="text-sm font-bold text-slate-900 dark:text-white">Review Draft</h2>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 p-3">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Baris Produk</p>
                        <p class="text-lg font-bold text-slate-900 dark:text-white mt-1">{{ count($stockItems) }}</p>
                    </div>
                    <div class="rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 p-3">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Total Qty</p>
                        <p class="text-lg font-bold text-slate-900 dark:text-white mt-1">{{ number_format($stockDraftQty, 0, ',', '.') }}</p>
                    </div>
                    <div class="rounded-xl border border-blue-100 dark:border-blue-900 bg-blue-50/70 dark:bg-blue-900/20 p-3">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-blue-600">Estimasi Nilai Supplier</p>
                        <p class="text-base font-bold text-blue-700 dark:text-blue-300 mt-1">Rp {{ number_format($stockDraftValue, 0, ',', '.') }}</p>
                    </div>
                </div>
            </section>

            <section class="bg-white dark:bg-darkCard border border-slate-100 dark:border-slate-700 rounded-2xl p-4 sm:p-5 space-y-3">
                <h2 class="text-sm font-bold text-slate-900 dark:text-white">Aksi</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">Simpan akan membuat batch ACTIVE, menambah stok produk, dan mencatat mutasi masuk.</p>

                <div class="hidden md:flex items-center gap-3">
                    <button type="button" wire:click="addStockItem"
                        class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-sm font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800">
                        <i class='bx bx-plus'></i> Tambah Produk
                    </button>
                    <button type="button" wire:click="saveStockIn" wire:loading.attr="disabled" wire:target="saveStockIn"
                        @disabled(! $this->canSubmitStockIn)
                        class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-xl bg-primary hover:bg-primary/90 text-white text-sm font-bold shadow-sm shadow-primary/20 disabled:opacity-60 disabled:cursor-not-allowed">
                        <i class='bx bx-save' wire:loading.remove wire:target="saveStockIn"></i>
                        <span wire:loading.remove wire:target="saveStockIn">Simpan Stok Masuk</span>
                        <span wire:loading wire:target="saveStockIn">Menyimpan...</span>
                    </button>
                </div>
            </section>
        </div>
    @endif

    @if($tab === 'recap')
        <div class="grid grid-cols-1 xl:grid-cols-12 gap-5">
            <div class="xl:col-span-8 space-y-5">
                <section class="bg-white dark:bg-darkCard border border-slate-100 dark:border-slate-700 rounded-2xl p-4 sm:p-5 space-y-4">
                    <div class="flex items-center justify-between gap-3">
                        <h2 class="text-sm font-bold text-slate-900 dark:text-white">Konteks</h2>
                        <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Step 2</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-widest text-slate-400 mb-2">Supplier</label>
                            <select wire:model.live="recapSupplierId" class="w-full min-h-[46px] bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 text-sm">
                                <option value="">Pilih supplier</option>
                                @foreach($this->suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->businessName }}</option>
                                @endforeach
                            </select>
                            @error('recapSupplierId') <p class="text-xs text-rose-500 mt-1.5">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-widest text-slate-400 mb-2">Tanggal</label>
                            <input type="date" wire:model="recapDate" class="w-full min-h-[46px] bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 text-sm">
                            @error('recapDate') <p class="text-xs text-rose-500 mt-1.5">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-widest text-slate-400 mb-2">Sisa Hutang</label>
                            <div class="w-full min-h-[46px] flex items-center bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800 rounded-xl px-3 text-sm font-bold text-rose-700 dark:text-rose-300">
                                Rp {{ number_format($outstanding, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>

                    @if($step2SoftLocked)
                        <div class="rounded-xl border border-amber-200 dark:border-amber-700 bg-amber-50 dark:bg-amber-900/20 px-3 py-2.5 text-xs text-amber-700 dark:text-amber-300 flex items-start gap-2">
                            <i class='bx bx-lock-alt text-sm mt-0.5'></i>
                            <p>Pilih supplier terlebih dahulu agar form hitung fisik, nominal bayar, dan tombol simpan aktif.</p>
                        </div>
                    @endif
                </section>

                <section class="bg-white dark:bg-darkCard border border-slate-100 dark:border-slate-700 rounded-2xl p-4 sm:p-5 space-y-4">
                    <h2 class="text-sm font-bold text-slate-900 dark:text-white">Form Hitung Stok Fisik</h2>

                    @if(!$recapSupplierId)
                        <div class="rounded-xl border border-dashed border-slate-300 dark:border-slate-700 p-6 text-center">
                            <div class="mx-auto mb-2 h-10 w-10 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400">
                                <i class='bx bx-user-check text-xl'></i>
                            </div>
                            <p class="text-sm font-semibold text-slate-600 dark:text-slate-300">Pilih supplier untuk memulai rekap.</p>
                            <p class="text-xs text-slate-500 mt-1">Setelah supplier dipilih, daftar item aktif akan muncul otomatis.</p>
                        </div>
                    @elseif(empty($countItems))
                        <div class="rounded-xl border border-dashed border-slate-300 dark:border-slate-700 p-6 text-center">
                            <div class="mx-auto mb-2 h-10 w-10 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400">
                                <i class='bx bx-package text-xl'></i>
                            </div>
                            <p class="text-sm font-semibold text-slate-600 dark:text-slate-300">Tidak ada stok aktif untuk supplier ini.</p>
                            <p class="text-xs text-slate-500 mt-1">Jika ada outstanding, Anda tetap bisa proses payout di bagian aksi.</p>
                        </div>
                    @else
                        <div class="md:hidden space-y-3">
                            @foreach($countItems as $index => $row)
                                @php $delta = max(0, (int) $row['beforeQty'] - (int) $row['physicalQty']); @endphp
                                <div class="rounded-xl border border-slate-200 dark:border-slate-700 p-3 space-y-2.5">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $row['productName'] }}</p>
                                            <p class="text-[11px] text-slate-500">Batch {{ $row['batchCode'] }}</p>
                                        </div>
                                        <span class="text-xs font-bold {{ $delta > 0 ? 'text-emerald-600' : 'text-slate-500' }}">Delta {{ $delta }}</span>
                                    </div>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <p class="text-[11px] font-bold uppercase tracking-widest text-slate-400 mb-1">Qty Tercatat</p>
                                            <p class="text-sm font-semibold">{{ $row['beforeQty'] }}</p>
                                        </div>
                                        <div>
                                            <label class="block text-[11px] font-bold uppercase tracking-widest text-slate-400 mb-1">Qty Fisik</label>
                                            <input type="number" min="0" max="{{ $row['beforeQty'] }}" wire:model.live="countItems.{{ $index }}.physicalQty"
                                                @disabled($step2SoftLocked)
                                                class="w-full min-h-[42px] bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 text-sm text-right disabled:opacity-60 disabled:cursor-not-allowed">
                                            <p class="text-[10px] text-slate-400 mt-1">Masukkan jumlah fisik aktual di rak.</p>
                                            @error('countItems.' . $index . '.physicalQty') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="hidden md:block overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b border-slate-200 dark:border-slate-700 text-slate-500">
                                        <th class="py-2 pr-2 text-left">Batch</th>
                                        <th class="py-2 pr-2 text-left">Produk</th>
                                        <th class="py-2 px-2 text-right">Qty Tercatat</th>
                                        <th class="py-2 px-2 text-right">Qty Fisik</th>
                                        <th class="py-2 pl-2 text-right">Delta</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($countItems as $index => $row)
                                        @php $delta = max(0, (int) $row['beforeQty'] - (int) $row['physicalQty']); @endphp
                                        <tr class="border-b border-slate-100 dark:border-slate-800">
                                            <td class="py-2 pr-2">{{ $row['batchCode'] }}</td>
                                            <td class="py-2 pr-2 font-medium text-slate-800 dark:text-slate-200">{{ $row['productName'] }}</td>
                                            <td class="py-2 px-2 text-right font-semibold">{{ $row['beforeQty'] }}</td>
                                            <td class="py-2 px-2 text-right">
                                                <input type="number" min="0" max="{{ $row['beforeQty'] }}" wire:model.live="countItems.{{ $index }}.physicalQty"
                                                    @disabled($step2SoftLocked)
                                                    class="w-28 min-h-[38px] bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg px-2 py-1.5 text-sm text-right disabled:opacity-60 disabled:cursor-not-allowed">
                                                <p class="text-[10px] text-slate-400 mt-1">Qty fisik aktual</p>
                                                @error('countItems.' . $index . '.physicalQty') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                                            </td>
                                            <td class="py-2 pl-2 text-right font-bold {{ $delta > 0 ? 'text-emerald-600' : 'text-slate-500' }}">{{ $delta }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </section>

                <section class="bg-white dark:bg-darkCard border border-slate-100 dark:border-slate-700 rounded-2xl p-4 sm:p-5 space-y-4">
                    <h2 class="text-sm font-bold text-slate-900 dark:text-white">Review Angka</h2>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        <div class="rounded-xl border border-slate-100 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 p-3">
                            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Terjual</p>
                            <p class="text-lg font-bold text-slate-900 dark:text-white mt-1">{{ $countPreview['soldQty'] }}</p>
                        </div>
                        <div class="rounded-xl border border-emerald-100 dark:border-emerald-800 bg-emerald-50/70 dark:bg-emerald-900/20 p-3">
                            <p class="text-[11px] font-bold uppercase tracking-wider text-emerald-600">Omzet</p>
                            <p class="text-base font-bold text-emerald-700 dark:text-emerald-300 mt-1">Rp {{ number_format($countPreview['omzet'], 0, ',', '.') }}</p>
                        </div>
                        <div class="rounded-xl border border-blue-100 dark:border-blue-800 bg-blue-50/70 dark:bg-blue-900/20 p-3">
                            <p class="text-[11px] font-bold uppercase tracking-wider text-blue-600">Hak Supplier</p>
                            <p class="text-base font-bold text-blue-700 dark:text-blue-300 mt-1">Rp {{ number_format($countPreview['payable'], 0, ',', '.') }}</p>
                        </div>
                        <div class="rounded-xl border border-indigo-100 dark:border-indigo-800 bg-indigo-50/70 dark:bg-indigo-900/20 p-3">
                            <p class="text-[11px] font-bold uppercase tracking-wider text-indigo-600">Margin</p>
                            <p class="text-base font-bold text-indigo-700 dark:text-indigo-300 mt-1">Rp {{ number_format($countPreview['margin'], 0, ',', '.') }}</p>
                        </div>
                    </div>

                    <div class="rounded-xl border border-rose-100 dark:border-rose-800 bg-rose-50/70 dark:bg-rose-900/20 p-3">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-rose-600">Outstanding Saat Ini</p>
                        <p class="text-base font-bold text-rose-700 dark:text-rose-300 mt-1">Rp {{ number_format($outstanding, 0, ',', '.') }}</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-widest text-slate-400 mb-1.5">Catatan Rekap</label>
                            <textarea wire:model="countNote" rows="2" placeholder="Opsional" @disabled($step2SoftLocked)
                                class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2.5 text-sm disabled:opacity-60 disabled:cursor-not-allowed"></textarea>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-widest text-slate-400 mb-1.5">Catatan Pembayaran</label>
                            <textarea wire:model="payoutNote" rows="2" placeholder="Opsional" @disabled($step2SoftLocked)
                                class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2.5 text-sm disabled:opacity-60 disabled:cursor-not-allowed"></textarea>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-widest text-slate-400 mb-1.5">Nominal Dibayar</label>
                        <div class="flex items-center gap-2 w-full md:max-w-[420px]">
                            <input type="number" min="0" step="0.01" placeholder="0" wire:model="payNowAmount" @disabled($step2SoftLocked)
                                class="w-full md:w-56 min-h-[46px] bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 text-sm font-semibold [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none disabled:opacity-60 disabled:cursor-not-allowed">
                            <button type="button" wire:click="fillPayNowFromSupplierRights" @disabled($step2SoftLocked)
                                class="inline-flex shrink-0 items-center justify-center gap-1.5 min-h-[46px] px-3 rounded-xl border border-blue-200 dark:border-blue-700 bg-blue-50 dark:bg-blue-900/20 text-xs font-semibold text-blue-700 dark:text-blue-300 hover:bg-blue-100 dark:hover:bg-blue-900/30 disabled:opacity-60 disabled:cursor-not-allowed">
                                <i class='bx bx-transfer-alt'></i> Isi Hak
                            </button>
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1">`Isi Hak` pakai hak supplier dari draft. `Set Nominal Penuh` untuk outstanding.</p>
                        @error('payNowAmount') <p class="text-xs text-rose-500 mt-1.5">{{ $message }}</p> @enderror
                    </div>
                </section>

                <section class="bg-white dark:bg-darkCard border border-slate-100 dark:border-slate-700 rounded-2xl p-4 sm:p-5 space-y-3">
                    <h2 class="text-sm font-bold text-slate-900 dark:text-white">Aksi</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Data delta dan outstanding di atas akan dijadikan dasar posting omzet dan payout supplier.</p>

                    <div class="hidden md:flex items-center gap-3">
                        <button type="button" wire:click="refreshPayNowDefault" @disabled($step2SoftLocked)
                            class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-sm font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 disabled:opacity-60 disabled:cursor-not-allowed">
                            <i class='bx bx-reset'></i> Set Nominal Penuh
                        </button>
                        <button type="button" wire:click="saveRecapAndPayout" wire:loading.attr="disabled" wire:target="saveRecapAndPayout"
                            @disabled($step2SoftLocked || ! $this->canSubmitRecap)
                            class="inline-flex items-center justify-center gap-1.5 px-5 py-2.5 rounded-xl bg-primary hover:bg-primary/90 text-white text-sm font-bold shadow-sm shadow-primary/20 disabled:opacity-60 disabled:cursor-not-allowed">
                            <i class='bx bx-save' wire:loading.remove wire:target="saveRecapAndPayout"></i>
                            <span wire:loading.remove wire:target="saveRecapAndPayout">Simpan Rekap & Payout</span>
                            <span wire:loading wire:target="saveRecapAndPayout">Menyimpan...</span>
                        </button>
                    </div>
                </section>
            </div>

            <div class="xl:col-span-4 hidden xl:block space-y-5">
                <section class="bg-white dark:bg-darkCard border border-slate-100 dark:border-slate-700 rounded-2xl p-4">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-3">Riwayat Hitung Fisik</h3>
                    <div class="space-y-2 max-h-[420px] overflow-y-auto pr-1">
                        @forelse($this->recentCountLogs as $log)
                            <div class="rounded-xl border border-slate-100 dark:border-slate-700 p-3">
                                <p class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{ $log->product?->name ?? '-' }}</p>
                                <p class="text-[11px] text-slate-500">{{ $log->supplier?->businessName ?? '-' }}</p>
                                <p class="text-[11px] text-slate-500 mt-1">Tercatat {{ $log->beforeQty }} · Fisik {{ $log->physicalQty }} · Delta {{ $log->soldDeltaQty }}</p>
                                <p class="text-[11px] text-emerald-600 font-semibold mt-1">Omzet Rp {{ number_format($log->soldDeltaAmount, 0, ',', '.') }}</p>
                                <p class="text-[10px] text-slate-400 mt-1">{{ optional($log->countedAt)->format('d M Y H:i') }} · {{ $log->user?->name ?? '-' }}</p>
                            </div>
                        @empty
                            <div class="rounded-xl border border-dashed border-slate-300 dark:border-slate-700 p-4 text-center">
                                <div class="mx-auto mb-2 h-8 w-8 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400">
                                    <i class='bx bx-history text-base'></i>
                                </div>
                                <p class="text-xs font-semibold text-slate-600 dark:text-slate-300">Belum ada riwayat hitung fisik.</p>
                                <p class="text-[11px] text-slate-500 mt-1">Lakukan rekap untuk menampilkan histori di panel ini.</p>
                            </div>
                        @endforelse
                    </div>
                </section>

                <section class="bg-white dark:bg-darkCard border border-slate-100 dark:border-slate-700 rounded-2xl p-4">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-3">Riwayat Pembayaran Supplier</h3>
                    <div class="space-y-2 max-h-[420px] overflow-y-auto pr-1">
                        @forelse($this->recentPayouts as $payment)
                            <div class="rounded-xl border border-slate-100 dark:border-slate-700 p-3">
                                <p class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{ $payment->payoutCode }}</p>
                                <p class="text-[11px] text-slate-500">{{ $payment->supplier?->businessName ?? '-' }}</p>
                                <p class="text-[11px] text-slate-500 mt-1">Dibayar: <span class="font-semibold text-rose-600">Rp {{ number_format($payment->paidAmount, 0, ',', '.') }}</span></p>
                                <p class="text-[11px] text-slate-500">Outstanding: Rp {{ number_format($payment->outstandingAfter, 0, ',', '.') }}</p>
                                <p class="text-[10px] text-slate-400 mt-1">{{ optional($payment->payoutDate)->format('d M Y') }} · {{ $payment->user?->name ?? '-' }}</p>
                            </div>
                        @empty
                            <div class="rounded-xl border border-dashed border-slate-300 dark:border-slate-700 p-4 text-center">
                                <div class="mx-auto mb-2 h-8 w-8 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400">
                                    <i class='bx bx-wallet text-base'></i>
                                </div>
                                <p class="text-xs font-semibold text-slate-600 dark:text-slate-300">Belum ada riwayat payout.</p>
                                <p class="text-[11px] text-slate-500 mt-1">Riwayat akan tampil setelah pembayaran supplier diproses.</p>
                            </div>
                        @endforelse
                    </div>
                </section>
            </div>
        </div>

        <div class="xl:hidden space-y-3">
            <details class="bg-white dark:bg-darkCard border border-slate-100 dark:border-slate-700 rounded-2xl p-4" open>
                <summary class="cursor-pointer text-sm font-bold text-slate-900 dark:text-white">Riwayat Hitung Fisik</summary>
                <div class="mt-3 space-y-2">
                    @forelse($this->recentCountLogs as $log)
                        <div class="rounded-xl border border-slate-100 dark:border-slate-700 p-3">
                            <p class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{ $log->product?->name ?? '-' }}</p>
                            <p class="text-[11px] text-slate-500">Tercatat {{ $log->beforeQty }} · Fisik {{ $log->physicalQty }} · Delta {{ $log->soldDeltaQty }}</p>
                            <p class="text-[10px] text-slate-400 mt-1">{{ optional($log->countedAt)->format('d M Y H:i') }}</p>
                        </div>
                    @empty
                        <div class="rounded-xl border border-dashed border-slate-300 dark:border-slate-700 p-4 text-center text-xs text-slate-500">
                            Belum ada riwayat hitung fisik.
                        </div>
                    @endforelse
                </div>
            </details>

            <details class="bg-white dark:bg-darkCard border border-slate-100 dark:border-slate-700 rounded-2xl p-4">
                <summary class="cursor-pointer text-sm font-bold text-slate-900 dark:text-white">Riwayat Pembayaran Supplier</summary>
                <div class="mt-3 space-y-2">
                    @forelse($this->recentPayouts as $payment)
                        <div class="rounded-xl border border-slate-100 dark:border-slate-700 p-3">
                            <p class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{ $payment->payoutCode }}</p>
                            <p class="text-[11px] text-slate-500">Dibayar: Rp {{ number_format($payment->paidAmount, 0, ',', '.') }}</p>
                            <p class="text-[11px] text-slate-500">Outstanding: Rp {{ number_format($payment->outstandingAfter, 0, ',', '.') }}</p>
                        </div>
                    @empty
                        <div class="rounded-xl border border-dashed border-slate-300 dark:border-slate-700 p-4 text-center text-xs text-slate-500">
                            Belum ada riwayat payout.
                        </div>
                    @endforelse
                </div>
            </details>
        </div>
    @endif

    <div class="md:hidden fixed bottom-0 inset-x-0 z-40 border-t border-slate-200 dark:border-slate-700 bg-white/95 dark:bg-slate-900/95 backdrop-blur p-3 pb-[calc(env(safe-area-inset-bottom)+0.75rem)]">
        @if($tab === 'stock-in')
            <div class="grid grid-cols-2 gap-2">
                <button type="button" wire:click="addStockItem"
                    class="inline-flex items-center justify-center gap-1.5 px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-700 dark:text-slate-200">
                    <i class='bx bx-plus'></i> Tambah Produk
                </button>
                <button type="button" wire:click="saveStockIn" wire:loading.attr="disabled" wire:target="saveStockIn"
                    @disabled(! $this->canSubmitStockIn)
                    class="inline-flex items-center justify-center gap-1.5 px-3 py-2.5 rounded-xl bg-primary text-white text-xs font-bold shadow-sm shadow-primary/20 disabled:opacity-60 disabled:cursor-not-allowed">
                    <i class='bx bx-save' wire:loading.remove wire:target="saveStockIn"></i>
                    <span wire:loading.remove wire:target="saveStockIn">Simpan Stok Masuk</span>
                    <span wire:loading wire:target="saveStockIn">Menyimpan...</span>
                </button>
            </div>
        @else
            <div class="grid grid-cols-2 gap-2">
                <button type="button" wire:click="refreshPayNowDefault" @disabled($step2SoftLocked)
                    class="inline-flex items-center justify-center gap-1.5 px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-700 dark:text-slate-200 disabled:opacity-60 disabled:cursor-not-allowed">
                    <i class='bx bx-reset'></i> Set Nominal Penuh
                </button>
                <button type="button" wire:click="saveRecapAndPayout" wire:loading.attr="disabled" wire:target="saveRecapAndPayout"
                    @disabled($step2SoftLocked || ! $this->canSubmitRecap)
                    class="inline-flex items-center justify-center gap-1.5 px-3 py-2.5 rounded-xl bg-primary text-white text-xs font-bold shadow-sm shadow-primary/20 disabled:opacity-60 disabled:cursor-not-allowed">
                    <i class='bx bx-save' wire:loading.remove wire:target="saveRecapAndPayout"></i>
                    <span wire:loading.remove wire:target="saveRecapAndPayout">Simpan Rekap & Payout</span>
                    <span wire:loading wire:target="saveRecapAndPayout">Menyimpan...</span>
                </button>
            </div>
        @endif
    </div>
</div>
