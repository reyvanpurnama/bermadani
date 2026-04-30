<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-white">Operasional Harian Supplier</h1>
            <p class="text-sm text-slate-500">Alur manual non-POS: setor stok, hitung sisa fisik, lalu payout supplier.</p>
        </div>
        <div class="text-xs text-slate-500">
            <p>Omzet dicatat ke kategori: <span class="font-semibold text-emerald-600">Omset Supplier Manual (Non-POS)</span></p>
            <p>Payout dicatat ke kategori: <span class="font-semibold text-rose-600">Pembayaran Supplier Manual (Non-POS)</span></p>
        </div>
    </div>

    <div class="bg-white dark:bg-darkCard border border-slate-200 dark:border-slate-700 rounded-2xl p-2 inline-flex gap-2">
        <button wire:click="setTab('stock-in')"
            class="px-4 py-2 rounded-xl text-sm font-semibold transition {{ $tab === 'stock-in' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            Stok Masuk
        </button>
        <button wire:click="setTab('recap')"
            class="px-4 py-2 rounded-xl text-sm font-semibold transition {{ $tab === 'recap' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            Rekap & Bayar
        </button>
    </div>

    @if($tab === 'stock-in')
        <div class="bg-white dark:bg-darkCard border border-slate-200 dark:border-slate-700 rounded-2xl p-5 space-y-5">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="text-xs font-semibold uppercase tracking-wider text-slate-500">Supplier</label>
                    <select wire:model.live="stockSupplierId"
                        class="mt-1 w-full rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-sm">
                        <option value="">Pilih supplier</option>
                        @foreach($this->suppliers as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->businessName }}</option>
                        @endforeach
                    </select>
                    @error('stockSupplierId') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="text-xs font-semibold uppercase tracking-wider text-slate-500">Tanggal Stok Masuk</label>
                    <input type="date" wire:model="stockDate"
                        class="mt-1 w-full rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-sm">
                    @error('stockDate') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="text-xs font-semibold uppercase tracking-wider text-slate-500">Catatan</label>
                    <input type="text" wire:model="stockNote" placeholder="Opsional: nomor struk / catatan"
                        class="mt-1 w-full rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-sm">
                    @error('stockNote') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="space-y-3">
                @foreach($stockItems as $index => $row)
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-3 p-3 border border-slate-200 dark:border-slate-700 rounded-xl">
                        <div class="md:col-span-5">
                            <label class="text-[11px] font-semibold text-slate-500">Produk</label>
                            <select wire:model="stockItems.{{ $index }}.productId"
                                class="mt-1 w-full rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-sm">
                                <option value="">Pilih produk</option>
                                @foreach($this->availableProducts as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }} (Jual: Rp {{ number_format($product->sellPrice, 0, ',', '.') }})</option>
                                @endforeach
                            </select>
                            @error('stockItems.' . $index . '.productId') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="text-[11px] font-semibold text-slate-500">Qty Masuk</label>
                            <input type="number" min="1" wire:model="stockItems.{{ $index }}.qty"
                                class="mt-1 w-full rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-sm">
                            @error('stockItems.' . $index . '.qty') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="md:col-span-4">
                            <label class="text-[11px] font-semibold text-slate-500">Harga Supplier / pcs</label>
                            <input type="number" min="0" step="0.01" wire:model="stockItems.{{ $index }}.supplierPrice"
                                class="mt-1 w-full rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-sm">
                            @error('stockItems.' . $index . '.supplierPrice') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="md:col-span-1 flex items-end">
                            <button type="button" wire:click="removeStockItem({{ $index }})"
                                class="w-full rounded-xl px-2 py-2 text-rose-600 bg-rose-50 hover:bg-rose-100 dark:bg-rose-900/20 dark:hover:bg-rose-900/30 text-sm font-semibold">
                                Hapus
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="flex flex-col sm:flex-row gap-3">
                <button type="button" wire:click="addStockItem"
                    class="rounded-xl px-4 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-sm font-semibold">
                    + Tambah Produk
                </button>
                <button type="button" wire:click="saveStockIn"
                    class="rounded-xl px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold shadow-sm">
                    Simpan Stok Masuk
                </button>
            </div>
        </div>
    @endif

    @if($tab === 'recap')
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-5">
            <div class="xl:col-span-2 space-y-5">
                <div class="bg-white dark:bg-darkCard border border-slate-200 dark:border-slate-700 rounded-2xl p-5 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-wider text-slate-500">Supplier</label>
                            <select wire:model.live="recapSupplierId"
                                class="mt-1 w-full rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-sm">
                                <option value="">Pilih supplier</option>
                                @foreach($this->suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->businessName }}</option>
                                @endforeach
                            </select>
                            @error('recapSupplierId') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="text-xs font-semibold uppercase tracking-wider text-slate-500">Tanggal Rekap</label>
                            <input type="date" wire:model="recapDate"
                                class="mt-1 w-full rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-sm">
                            @error('recapDate') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="text-xs font-semibold uppercase tracking-wider text-slate-500">Outstanding Supplier</label>
                            <div class="mt-1 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-3 py-2.5 text-sm font-bold text-slate-900 dark:text-white">
                                Rp {{ number_format($this->outstandingPayable, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>

                    <div class="space-y-3">
                        @if(empty($countItems))
                            <div class="rounded-xl border border-dashed border-slate-300 dark:border-slate-700 p-5 text-center text-sm text-slate-500">
                                Tidak ada stok aktif untuk dihitung. Kamu tetap bisa langsung proses payout jika ada hutang supplier.
                            </div>
                        @else
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="text-left text-slate-500 border-b border-slate-200 dark:border-slate-700">
                                            <th class="py-2 pr-2">Batch</th>
                                            <th class="py-2 pr-2">Produk</th>
                                            <th class="py-2 px-2 text-right">Stok Tercatat</th>
                                            <th class="py-2 px-2 text-right">Stok Fisik</th>
                                            <th class="py-2 pl-2 text-right">Terjual (Delta)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($countItems as $index => $row)
                                            @php
                                                $delta = max(0, (int)$row['beforeQty'] - (int)$row['physicalQty']);
                                            @endphp
                                            <tr class="border-b border-slate-100 dark:border-slate-800">
                                                <td class="py-2 pr-2 font-medium text-slate-700 dark:text-slate-200">{{ $row['batchCode'] }}</td>
                                                <td class="py-2 pr-2">{{ $row['productName'] }}</td>
                                                <td class="py-2 px-2 text-right font-semibold">{{ $row['beforeQty'] }}</td>
                                                <td class="py-2 px-2 text-right">
                                                    <input type="number" min="0" max="{{ $row['beforeQty'] }}" wire:model.live="countItems.{{ $index }}.physicalQty"
                                                        class="w-24 rounded-lg border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-right text-sm">
                                                    @error('countItems.' . $index . '.physicalQty') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                                                </td>
                                                <td class="py-2 pl-2 text-right font-bold {{ $delta > 0 ? 'text-emerald-600' : 'text-slate-500' }}">
                                                    {{ $delta }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        <div class="rounded-xl bg-slate-50 dark:bg-slate-800 p-3">
                            <p class="text-[11px] text-slate-500">Total Terjual</p>
                            <p class="text-lg font-bold text-slate-900 dark:text-white">{{ $this->countPreview['soldQty'] }}</p>
                        </div>
                        <div class="rounded-xl bg-emerald-50 dark:bg-emerald-900/20 p-3">
                            <p class="text-[11px] text-emerald-700 dark:text-emerald-400">Omzet Delta</p>
                            <p class="text-lg font-bold text-emerald-700 dark:text-emerald-400">Rp {{ number_format($this->countPreview['omzet'], 0, ',', '.') }}</p>
                        </div>
                        <div class="rounded-xl bg-blue-50 dark:bg-blue-900/20 p-3">
                            <p class="text-[11px] text-blue-700 dark:text-blue-400">Hak Supplier Delta</p>
                            <p class="text-lg font-bold text-blue-700 dark:text-blue-400">Rp {{ number_format($this->countPreview['payable'], 0, ',', '.') }}</p>
                        </div>
                        <div class="rounded-xl bg-indigo-50 dark:bg-indigo-900/20 p-3">
                            <p class="text-[11px] text-indigo-700 dark:text-indigo-400">Margin Delta</p>
                            <p class="text-lg font-bold text-indigo-700 dark:text-indigo-400">Rp {{ number_format($this->countPreview['margin'], 0, ',', '.') }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-wider text-slate-500">Catatan Rekap</label>
                            <textarea wire:model="countNote" rows="2" placeholder="Opsional"
                                class="mt-1 w-full rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-sm"></textarea>
                        </div>
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-wider text-slate-500">Catatan Payout</label>
                            <textarea wire:model="payoutNote" rows="2" placeholder="Opsional"
                                class="mt-1 w-full rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-sm"></textarea>
                        </div>
                    </div>

                    <div class="flex flex-col md:flex-row items-start md:items-end gap-4">
                        <div class="w-full md:w-72">
                            <label class="text-xs font-semibold uppercase tracking-wider text-slate-500">Bayar Sekarang</label>
                            <input type="number" min="0" step="0.01" wire:model="payNowAmount"
                                class="mt-1 w-full rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-sm font-semibold">
                            @error('payNowAmount') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <button type="button" wire:click="refreshPayNowDefault"
                            class="rounded-xl px-4 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-sm font-semibold">
                            Set Full Outstanding
                        </button>
                        <button type="button" wire:click="saveRecapAndPayout"
                            class="rounded-xl px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold shadow-sm">
                            Simpan Rekap & Payout
                        </button>
                    </div>
                </div>
            </div>

            <div class="space-y-5">
                <div class="bg-white dark:bg-darkCard border border-slate-200 dark:border-slate-700 rounded-2xl p-4">
                    <h3 class="font-bold text-slate-900 dark:text-white text-sm mb-3">Riwayat Hitung Fisik</h3>
                    <div class="space-y-2 max-h-80 overflow-y-auto pr-1">
                        @forelse($this->recentCountLogs as $log)
                            <div class="rounded-xl border border-slate-100 dark:border-slate-800 p-3">
                                <p class="text-xs font-semibold text-slate-700 dark:text-slate-200">{{ $log->product?->name ?? '-' }}</p>
                                <p class="text-[11px] text-slate-500">{{ $log->supplier?->businessName ?? '-' }}</p>
                                <p class="text-[11px] text-slate-500">Before {{ $log->beforeQty }} → Fisik {{ $log->physicalQty }} (Terjual {{ $log->soldDeltaQty }})</p>
                                <p class="text-[11px] text-emerald-600 font-semibold">Omzet Delta Rp {{ number_format($log->soldDeltaAmount, 0, ',', '.') }}</p>
                                <p class="text-[10px] text-slate-400 mt-1">{{ optional($log->countedAt)->format('d M Y H:i') }} · {{ $log->user?->name ?? '-' }}</p>
                            </div>
                        @empty
                            <p class="text-xs text-slate-500">Belum ada riwayat hitung.</p>
                        @endforelse
                    </div>
                </div>

                <div class="bg-white dark:bg-darkCard border border-slate-200 dark:border-slate-700 rounded-2xl p-4">
                    <h3 class="font-bold text-slate-900 dark:text-white text-sm mb-3">Riwayat Payout Supplier</h3>
                    <div class="space-y-2 max-h-80 overflow-y-auto pr-1">
                        @forelse($this->recentPayouts as $payout)
                            <div class="rounded-xl border border-slate-100 dark:border-slate-800 p-3">
                                <p class="text-xs font-semibold text-slate-700 dark:text-slate-200">{{ $payout->payoutCode }}</p>
                                <p class="text-[11px] text-slate-500">{{ $payout->supplier?->businessName ?? '-' }}</p>
                                <p class="text-[11px] text-slate-500">Dibayar: <span class="font-semibold text-rose-600">Rp {{ number_format($payout->paidAmount, 0, ',', '.') }}</span></p>
                                <p class="text-[11px] text-slate-500">Sisa Hutang: Rp {{ number_format($payout->outstandingAfter, 0, ',', '.') }}</p>
                                <p class="text-[10px] text-slate-400 mt-1">{{ optional($payout->payoutDate)->format('d M Y') }} · {{ $payout->user?->name ?? '-' }}</p>
                            </div>
                        @empty
                            <p class="text-xs text-slate-500">Belum ada payout.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

