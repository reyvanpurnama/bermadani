<div class="space-y-6">
    <div class="flex flex-col gap-4">
        <div>
            <a href="{{ route('admin.reports.monthly-financial') }}"
                class="inline-flex items-center gap-2 text-sm text-slate-500 hover:text-slate-700 mb-3 transition-colors font-medium">
                <i class='bx bx-arrow-back text-lg'></i> Kembali ke Laporan
            </a>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Import Data Angsuran</h1>
            <p class="text-xs text-slate-500 mt-1">Import data tagihan & angsuran dari BMT Itqan (CSV)</p>
        </div>
    </div>

    <!-- Alert Success/Error -->
    @if (session()->has('message'))
        <div class="bg-emerald-50 text-emerald-600 p-4 rounded-lg flex items-center gap-3 border border-emerald-100">
            <i class='bx bx-check-circle text-xl'></i>
            <div>
                <p class="font-bold">Berhasil!</p>
                <p class="text-sm">{{ session('message') }}</p>
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-50 text-red-600 p-4 rounded-lg flex items-center gap-3 border border-red-100">
            <i class='bx bx-error-circle text-xl'></i>
            <div>
                <p class="font-bold">Gagal Import!</p>
                <p class="text-sm">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Upload Section -->
        <div class="bg-white dark:bg-darkCard p-6 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700">
            <h3 class="font-bold text-slate-800 dark:text-white mb-4">Upload File CSV</h3>

            <div
                class="border-2 border-dashed border-slate-300 dark:border-slate-600 rounded-xl p-8 text-center hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors relative">
                <input type="file" wire:model.live="file"
                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">

                @if($file)
                    <div class="text-emerald-500 mb-2">
                        <i class='bx bx-file text-4xl'></i>
                    </div>
                    <p class="font-bold text-slate-700 dark:text-white">{{ $file->getClientOriginalName() }}</p>
                    <p class="text-xs text-slate-500">Siap diproses</p>
                @else
                    <div class="text-slate-400 mb-2">
                        <i class='bx bx-cloud-upload text-4xl'></i>
                    </div>
                    <p class="font-bold text-slate-700 dark:text-white">Klik atau seret file CSV ke sini</p>
                    <p class="text-xs text-slate-500 mt-1">Format: CSV (Comma delimited)</p>
                @endif
            </div>

            <div class="mt-4 bg-blue-50 text-blue-700 p-3 rounded-lg text-xs" role="alert">
                <p class="font-bold mb-1"><i class='bx bx-info-circle'></i> Format Kolom Wajib:</p>
                <p>NAMA_DEBITUR, PLAFOND_RP, TOTAL, TENOR, ANGSURAN_KE, TANGGAL_CAIR, NO_REKENING</p>
            </div>
        </div>

        <!-- Preview Stats -->
        <div class="bg-white dark:bg-darkCard p-6 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700">
            <h3 class="font-bold text-slate-800 dark:text-white mb-4">Preview Import</h3>

            @if(count($previewData) > 0)
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div class="bg-slate-50 dark:bg-slate-800 p-3 rounded-lg">
                        <p class="text-xs text-slate-500">Total Baris</p>
                        <p class="text-xl font-bold text-slate-800 dark:text-white">{{ count($previewData) }}</p>
                    </div>
                    <div class="bg-emerald-50 dark:bg-emerald-900/20 p-3 rounded-lg">
                        <p class="text-xs text-emerald-600 dark:text-emerald-400">Match Member</p>
                        <p class="text-xl font-bold text-emerald-700 dark:text-emerald-400">
                            {{ collect($previewData)->where('status', 'MATCH')->count() }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <button wire:click="import" wire:loading.attr="disabled"
                        class="flex-1 bg-primary hover:bg-primary/90 text-white font-bold py-3 px-4 rounded-lg flex items-center justify-center gap-2">
                        <i class='bx bx-import' wire:loading.remove></i>
                        <span wire:loading.remove>Proses Import</span>
                        <span wire:loading>Memproses...</span>
                    </button>
                    <button wire:click="$set('file', null); $set('previewData', [])"
                        class="px-4 py-3 border border-slate-200 rounded-lg hover:bg-slate-50 text-slate-600">
                        Batal
                    </button>
                </div>
            @else
                <div class="text-center py-10 text-slate-400">
                    <i class='bx bx-spreadsheet text-4xl mb-2'></i>
                    <p>Upload file untuk melihat preview</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Preview Table -->
    @if(count($previewData) > 0)
        <div
            class="bg-white dark:bg-darkCard rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">
            <div class="p-4 border-b border-slate-100 dark:border-slate-700 font-bold text-slate-800 dark:text-white">
                Detail Data
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50 dark:bg-slate-800 text-xs uppercase font-bold text-slate-500">
                        <tr>
                            <th class="px-4 py-3">Nama (CSV)</th>
                            <th class="px-4 py-3">Member Match?</th>
                            <th class="px-4 py-3 text-right">Plafond</th>
                            <th class="px-4 py-3 text-right">Total Angsuran</th>
                            <th class="px-4 py-3 text-center">Ke</th>
                            <th class="px-4 py-3 text-center">Tenor</th>
                            <th class="px-4 py-3">Tgl Cair</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @foreach($previewData as $row)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50">
                                <td class="px-4 py-2 font-medium">{{ $row['raw']['NAMA_DEBITUR'] }}</td>
                                <td class="px-4 py-2">
                                    @if($row['status'] == 'MATCH')
                                        <span
                                            class="bg-emerald-100 text-emerald-700 text-xs px-2 py-1 rounded-full flex items-center gap-1 w-fit">
                                            <i class='bx bx-check'></i> {{ $row['member']->name }}
                                        </span>
                                    @else
                                        <span
                                            class="bg-red-100 text-red-700 text-xs px-2 py-1 rounded-full flex items-center gap-1 w-fit">
                                            <i class='bx bx-x'></i> Not Found
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-2 text-right font-mono">
                                    {{ number_format((float) $row['raw']['PLAFOND_RP'], 0, ',', '.') }}</td>
                                <td class="px-4 py-2 text-right font-mono font-bold text-primary">
                                    {{ number_format((float) $row['raw']['TOTAL'], 0, ',', '.') }}</td>
                                <td class="px-4 py-2 text-center">{{ $row['raw']['ANGSURAN_KE'] }}</td>
                                <td class="px-4 py-2 text-center">{{ $row['raw']['TENOR'] }}</td>
                                <td class="px-4 py-2 text-slate-500 text-xs">{{ $row['raw']['TANGGAL_CAIR'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>