<div>
    {{-- Flash Message --}}
    @if (session()->has('message'))
        <div class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-xl p-4 mb-6 flex items-center gap-3">
            <i class='bx bx-check-circle text-2xl text-emerald-600 dark:text-emerald-400'></i>
            <span class="text-sm font-medium text-emerald-700 dark:text-emerald-400">{{ session('message') }}</span>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800 rounded-xl p-4 mb-6 flex items-center gap-3">
            <i class='bx bx-error-circle text-2xl text-rose-600 dark:text-rose-400'></i>
            <span class="text-sm font-medium text-rose-700 dark:text-rose-400">{{ session('error') }}</span>
        </div>
    @endif

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-white">Import Produk Minimarket</h1>
            <p class="text-[11px] text-slate-500 mt-0.5">Upload CSV, preview, lalu import ke inventaris (TOKO).</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.products') }}"
                class="bg-white dark:bg-darkCard border border-slate-200 dark:border-slate-600 text-slate-700 dark:text-white hover:bg-slate-50 dark:hover:bg-slate-700 px-4 py-2 rounded-lg text-[13px] font-medium shadow-sm transition-colors flex items-center gap-2">
                <i class='bx bx-arrow-back'></i> Kembali
            </a>
        </div>
    </div>

    {{-- Upload Box --}}
    <div class="bg-white dark:bg-darkCard rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 p-5 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-slate-500 mb-2 uppercase tracking-widest">File CSV</label>
                <input type="file" wire:model="file" accept=".csv,.txt"
                    class="block w-full text-sm text-slate-700 dark:text-slate-200 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 dark:file:bg-slate-700 dark:file:text-slate-100" />
                @error('file')
                    <div class="mt-2 text-xs text-rose-600">{{ $message }}</div>
                @enderror

                @if(!empty($importErrors))
                    <div class="mt-3 text-xs text-rose-600 space-y-1">
                        @foreach($importErrors as $err)
                            <div>- {{ $err }}</div>
                        @endforeach
                    </div>
                @endif

                <div wire:loading wire:target="file" class="mt-3 text-xs text-slate-500">Memproses file...</div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 mb-2 uppercase tracking-widest">Ringkasan</label>
                <div class="text-sm text-slate-700 dark:text-slate-200 space-y-1">
                    <div>Total baris: <span class="font-semibold">{{ $importStats['total_rows'] }}</span></div>
                    <div>Preview: <span class="font-semibold">{{ $importStats['preview_rows'] }}</span></div>
                    <div>Sukses: <span class="font-semibold">{{ $importStats['success'] }}</span></div>
                    <div>Skip duplikat: <span class="font-semibold">{{ $importStats['skipped_duplicates'] }}</span></div>
                    <div>Gagal: <span class="font-semibold">{{ $importStats['failed'] }}</span></div>
                </div>
            </div>
        </div>

        <div class="mt-5 flex justify-end">
            <button wire:click="import" wire:loading.attr="disabled" wire:target="import"
                class="bg-primary hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-[13px] font-bold shadow-md shadow-indigo-500/20 transition-colors flex items-center gap-2 disabled:opacity-60">
                <i class='bx bx-import text-lg'></i>
                <span wire:loading.remove wire:target="import">Import</span>
                <span wire:loading wire:target="import">Mengimpor...</span>
            </button>
        </div>

        <p class="mt-3 text-[11px] text-slate-500">Duplikat ditentukan dari nama produk (create-only). Kolom SEDUH disimpan sebagai catatan di deskripsi.</p>
    </div>

    {{-- Preview Table --}}
    @if(!empty($previewData))
        <div class="bg-white dark:bg-darkCard rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">
            <div class="p-4 border-b border-slate-100 dark:border-slate-700">
                <div class="text-sm font-bold text-slate-800 dark:text-slate-100">Preview (maks. 50 baris)</div>
                <div class="text-[11px] text-slate-500 mt-1">Cek harga & status sebelum import.</div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 dark:bg-slate-800/40">
                        <tr class="text-left text-[11px] text-slate-500 uppercase tracking-widest">
                            <th class="px-4 py-3">Barang</th>
                            <th class="px-4 py-3">Modal</th>
                            <th class="px-4 py-3">Harga Jual</th>
                            <th class="px-4 py-3">Stok</th>
                            <th class="px-4 py-3">Margin%</th>
                            <th class="px-4 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @foreach($previewData as $row)
                            <tr class="text-slate-700 dark:text-slate-200">
                                <td class="px-4 py-3">
                                    <div class="font-semibold">{{ $row['name'] }}</div>
                                    @if(!empty($row['description']))
                                        <div class="text-[11px] text-slate-500">{{ $row['description'] }}</div>
                                    @endif
                                    @if(!empty($row['warning']))
                                        <div class="text-[11px] text-amber-600">{{ $row['warning'] }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3">Rp {{ number_format($row['buyPrice'] ?? 0, 0, ',', '.') }}</td>
                                <td class="px-4 py-3">Rp {{ number_format($row['sellPrice'] ?? 0, 0, ',', '.') }}</td>
                                <td class="px-4 py-3">{{ $row['stock'] ?? 0 }}</td>
                                <td class="px-4 py-3">{{ $row['marginPercent'] !== null ? number_format($row['marginPercent'], 2, ',', '.') : '-' }}</td>
                                <td class="px-4 py-3">
                                    @php($status = $row['status'] ?? 'OK')
                                    @if($status === 'DUPLICATE')
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200 text-[11px] font-bold">
                                            <i class='bx bx-copy'></i> Duplikat
                                        </span>
                                    @elseif($status === 'INVALID')
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-rose-100 dark:bg-rose-900/30 text-rose-700 dark:text-rose-300 text-[11px] font-bold">
                                            <i class='bx bx-x-circle'></i> Invalid
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 text-[11px] font-bold">
                                            <i class='bx bx-check'></i> OK
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
