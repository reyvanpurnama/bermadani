<div class="space-y-6">
    <div class="bg-white dark:bg-darkCard border border-slate-100 dark:border-slate-700 rounded-2xl p-4 sm:p-6 shadow-sm">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <a href="{{ route('admin.loans') }}"
                    class="inline-flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 mb-2 transition-colors font-medium">
                    <i class='bx bx-arrow-back text-lg'></i>
                    Kembali ke Pinjaman
                </a>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white">Import Data Angsuran</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Import data tagihan dan angsuran dari BMT Itqan (CSV).</p>
            </div>
            <div class="text-xs text-slate-500 dark:text-slate-400 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-3 py-2 w-full sm:w-auto">
                Format wajib:
                <span class="font-semibold text-slate-600 dark:text-slate-300">NAMA_DEBITUR, PLAFOND_RP, TOTAL, TENOR, ANGSURAN_KE, TANGGAL_CAIR, NO_REKENING</span>
            </div>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 p-4 rounded-xl flex items-start gap-3 border border-emerald-100 dark:border-emerald-900/40">
            <i class='bx bx-check-circle text-xl mt-0.5'></i>
            <div>
                <p class="font-bold">Berhasil!</p>
                <p class="text-sm">{{ session('message') }}</p>
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-rose-50 dark:bg-rose-900/30 text-rose-700 dark:text-rose-300 p-4 rounded-xl flex items-start gap-3 border border-rose-100 dark:border-rose-900/40">
            <i class='bx bx-error-circle text-xl mt-0.5'></i>
            <div>
                <p class="font-bold">Gagal Import!</p>
                <p class="text-sm">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
        <div class="xl:col-span-7 bg-white dark:bg-darkCard p-4 sm:p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
            <h3 class="font-bold text-slate-800 dark:text-white mb-4">Upload File CSV</h3>

            <div class="border-2 border-dashed border-slate-300 dark:border-slate-600 rounded-xl p-6 sm:p-8 text-center hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors relative">
                <input type="file" wire:model.live="file" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">

                <div wire:loading.remove wire:target="file">
                    @if($file)
                        <div class="text-emerald-500 mb-2">
                            <i class='bx bx-file text-4xl'></i>
                        </div>
                        <p class="font-bold text-slate-700 dark:text-white break-words">{{ $file->getClientOriginalName() }}</p>
                        <p class="text-xs text-slate-500 mt-1">File siap diproses</p>
                    @else
                        <div class="text-slate-400 mb-2">
                            <i class='bx bx-cloud-upload text-4xl'></i>
                        </div>
                        <p class="font-bold text-slate-700 dark:text-white">Klik atau seret file CSV ke sini</p>
                        <p class="text-xs text-slate-500 mt-1">Format: CSV (Comma delimited)</p>
                    @endif
                </div>

                <div wire:loading wire:target="file" class="text-slate-500 dark:text-slate-300">
                    <i class='bx bx-loader-alt bx-spin text-3xl mb-2'></i>
                    <p class="text-sm font-semibold">Membaca file...</p>
                </div>
            </div>

            <div class="mt-4 bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 p-3 rounded-lg text-xs" role="alert">
                <p class="font-bold mb-1"><i class='bx bx-info-circle'></i> Tips Import:</p>
                <p>Pastikan header CSV sesuai format wajib dan tidak ada kolom penting yang kosong.</p>
            </div>
        </div>

        <div class="xl:col-span-5 bg-white dark:bg-darkCard p-4 sm:p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
            <h3 class="font-bold text-slate-800 dark:text-white mb-4">Preview Import</h3>

            @if(count($previewData) > 0)
                <div class="grid grid-cols-2 gap-3 mb-5">
                    <div class="bg-slate-50 dark:bg-slate-800 p-3 rounded-lg border border-slate-100 dark:border-slate-700">
                        <p class="text-xs text-slate-500">Total Baris</p>
                        <p class="text-xl font-bold text-slate-800 dark:text-white">{{ count($previewData) }}</p>
                    </div>
                    <div class="bg-emerald-50 dark:bg-emerald-900/20 p-3 rounded-lg border border-emerald-100 dark:border-emerald-900/40">
                        <p class="text-xs text-emerald-600 dark:text-emerald-400">Match Member</p>
                        <p class="text-xl font-bold text-emerald-700 dark:text-emerald-400">
                            {{ collect($previewData)->where('status', 'MATCH')->count() }}
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    <button wire:click="import" wire:loading.attr="disabled"
                        class="w-full bg-primary hover:bg-primary/90 text-white font-bold py-3 px-4 rounded-lg flex items-center justify-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed transition-colors">
                        <i class='bx bx-import' wire:loading.remove></i>
                        <span wire:loading.remove>Proses Import</span>
                        <span wire:loading>Memproses...</span>
                    </button>
                    <button wire:click="$set('file', null); $set('previewData', [])"
                        class="w-full px-4 py-3 border border-slate-200 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-300 font-semibold text-sm transition-colors">
                        Batal
                    </button>
                </div>
            @else
                <div class="text-center py-10 text-slate-400 dark:text-slate-500">
                    <i class='bx bx-spreadsheet text-4xl mb-2'></i>
                    <p class="text-sm">Upload file untuk melihat preview</p>
                </div>
            @endif
        </div>
    </div>

    @if(count($previewData) > 0)
        <div class="md:hidden space-y-3">
            @foreach($previewData as $row)
                <div class="bg-white dark:bg-darkCard rounded-xl border border-slate-100 dark:border-slate-700 p-4 shadow-sm space-y-3">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-sm font-semibold text-slate-800 dark:text-white">{{ $row['raw']['NAMA_DEBITUR'] }}</p>
                            <p class="text-[11px] text-slate-500 mt-0.5">Tgl Cair: {{ $row['raw']['TANGGAL_CAIR'] }}</p>
                        </div>
                        @if($row['status'] == 'MATCH')
                            <span class="bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 text-[10px] px-2 py-1 rounded-full font-bold">MATCH</span>
                        @else
                            <span class="bg-rose-100 dark:bg-rose-900/30 text-rose-700 dark:text-rose-300 text-[10px] px-2 py-1 rounded-full font-bold">NOT FOUND</span>
                        @endif
                    </div>

                    <div class="text-xs space-y-1.5">
                        <p class="text-slate-500">Member: <span class="font-semibold text-slate-700 dark:text-slate-300">{{ $row['member']->name ?? 'Tidak ditemukan' }}</span></p>
                        <p class="text-slate-500">Plafond: <span class="font-semibold text-slate-700 dark:text-slate-300">{{ number_format((float) $row['raw']['PLAFOND_RP'], 0, ',', '.') }}</span></p>
                        <p class="text-slate-500">Total Angsuran: <span class="font-semibold text-primary">{{ number_format((float) $row['raw']['TOTAL'], 0, ',', '.') }}</span></p>
                        <p class="text-slate-500">Angsuran ke-{{ $row['raw']['ANGSURAN_KE'] }} dari {{ $row['raw']['TENOR'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="hidden md:block bg-white dark:bg-darkCard rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">
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
                                <td class="px-4 py-2 font-medium text-slate-700 dark:text-slate-200">{{ $row['raw']['NAMA_DEBITUR'] }}</td>
                                <td class="px-4 py-2">
                                    @if($row['status'] == 'MATCH')
                                        <span class="bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 text-xs px-2 py-1 rounded-full inline-flex items-center gap-1 w-fit">
                                            <i class='bx bx-check'></i> {{ $row['member']->name }}
                                        </span>
                                    @else
                                        <span class="bg-rose-100 dark:bg-rose-900/30 text-rose-700 dark:text-rose-300 text-xs px-2 py-1 rounded-full inline-flex items-center gap-1 w-fit">
                                            <i class='bx bx-x'></i> Not Found
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-2 text-right font-mono text-slate-700 dark:text-slate-200">{{ number_format((float) $row['raw']['PLAFOND_RP'], 0, ',', '.') }}</td>
                                <td class="px-4 py-2 text-right font-mono font-bold text-primary">{{ number_format((float) $row['raw']['TOTAL'], 0, ',', '.') }}</td>
                                <td class="px-4 py-2 text-center text-slate-600 dark:text-slate-300">{{ $row['raw']['ANGSURAN_KE'] }}</td>
                                <td class="px-4 py-2 text-center text-slate-600 dark:text-slate-300">{{ $row['raw']['TENOR'] }}</td>
                                <td class="px-4 py-2 text-slate-500 text-xs">{{ $row['raw']['TANGGAL_CAIR'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
