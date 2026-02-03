<div class="space-y-6">
    {{-- Header --}}
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Laporan Keuangan Bulanan</h1>
            <p class="text-xs text-slate-500 mt-1">Generate laporan potongan gaji & SIMWA untuk unit keuangan</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.loans.import') }}"
                class="bg-indigo-50 dark:bg-indigo-900/30 border border-indigo-200 dark:border-indigo-700 hover:bg-indigo-100 dark:hover:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300 px-4 py-2 rounded-lg text-sm font-bold shadow-sm transition-colors flex items-center gap-2">
                <i class='bx bx-import text-lg'></i>
                Import Angsuran BMT Itqan
            </a>
            <a href="{{ route('admin.reports.balance-sheet') }}"
                class="bg-white dark:bg-darkCard border border-slate-200 dark:border-slate-600 hover:bg-slate-50 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-white px-4 py-2 rounded-lg text-sm font-bold shadow-sm transition-colors flex items-center gap-2">
                <i class='bx bx-spreadsheet text-lg'></i>
                Lihat Neraca Saldo
            </a>
        </div>
    </div>

    {{-- Filter Section --}}
    {{-- Modern Filter & Action Bar --}}
    <div class="bg-white dark:bg-darkCard rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">
        <div class="p-5 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            {{-- Left: Selection Group --}}
            <div class="flex flex-1 flex-col sm:flex-row items-end gap-4">
                <div class="w-full sm:w-48">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">Periode Bulan</label>
                    <div class="relative group">
                        <select wire:model="selectedMonth"
                            class="w-full bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-white rounded-xl px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary appearance-none cursor-pointer transition-all">
                            <option value="01">Januari</option>
                            <option value="02">Februari</option>
                            <option value="03">Maret</option>
                            <option value="04">April</option>
                            <option value="05">Mei</option>
                            <option value="06">Juni</option>
                            <option value="07">Juli</option>
                            <option value="08">Agustus</option>
                            <option value="09">September</option>
                            <option value="10">Oktober</option>
                            <option value="11">November</option>
                            <option value="12">Desember</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400">
                            <i class='bx bx-chevron-down text-lg'></i>
                        </div>
                    </div>
                </div>

                <div class="w-full sm:w-32">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">Tahun</label>
                    <div class="relative group">
                        <select wire:model="selectedYear"
                            class="w-full bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-white rounded-xl px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary appearance-none cursor-pointer transition-all">
                            @for ($year = now()->year; $year >= 2020; $year--)
                                <option value="{{ $year }}">{{ $year }}</option>
                            @endfor
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400">
                            <i class='bx bx-chevron-down text-lg'></i>
                        </div>
                    </div>
                </div>

                <button wire:click="generateReport"
                    class="w-full sm:w-auto bg-primary hover:bg-primary/90 text-white font-bold py-2.5 px-6 rounded-xl transition-all shadow-lg shadow-primary/20 flex items-center justify-center gap-2 group whitespace-nowrap">
                    <i class='bx bx-refresh text-xl group-hover:rotate-180 transition-transform duration-700'></i>
                    <span>Tampilkan Data</span>
                </button>
            </div>

            {{-- Right: Export Actions (Only if preview shown) --}}
            @if($showPreview)
                <div class="flex items-center gap-4 bg-slate-50 dark:bg-slate-800/50 p-3 rounded-2xl border border-slate-100 dark:border-slate-700/50 self-start lg:self-center">
                    <div class="pr-3 border-r border-slate-200 dark:border-slate-700 hidden sm:block">
                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest leading-none mb-1">Cetak Laporan</p>
                        <p class="text-[10px] text-slate-500 font-medium leading-none">Pilih format PDF</p>
                    </div>
                    
                    <div class="flex gap-2">
                        <button wire:click="downloadPDF"
                            class="flex items-center gap-2 px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-bold rounded-xl transition-all shadow-md shadow-emerald-500/10 group"
                            title="Laporan lengkap untuk internal koperasi">
                            <i class='bx bxs-file-pdf text-lg'></i>
                            <span>PDF Internal</span>
                        </button>

                        <button wire:click="downloadSimplePDF"
                            class="flex items-center gap-2 px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold rounded-xl transition-all shadow-md shadow-amber-500/10 group"
                            title="Laporan singkat untuk Unit Keuangan Kampus">
                            <i class='bx bxs-institution text-lg'></i>
                            <span>PDF Keuangan</span>
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Preview Section --}}
    @if($showPreview && $reportData)
        <div class="space-y-6">
            {{-- Summary Cards Grid --}}
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                {{-- Total Member --}}
                <div
                    class="bg-white dark:bg-darkCard p-4 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm flex flex-col justify-between h-full">
                    <div class="text-slate-500 text-xs font-bold uppercase tracking-wider mb-2">Total Member</div>
                    <div class="flex items-end justify-between">
                        <span
                            class="text-2xl font-bold text-slate-800 dark:text-white">{{ $reportData['summary']['total_members'] }}</span>
                        <i class='bx bx-user text-2xl text-slate-200'></i>
                    </div>
                </div>

                {{-- Grand Total --}}
                <div
                    class="bg-primary text-white p-4 rounded-xl shadow-lg shadow-indigo-500/20 flex flex-col justify-between h-full col-span-2 md:col-span-2">
                    <div class="text-indigo-100 text-xs font-bold uppercase tracking-wider mb-2">Grand Total Potongan</div>
                    <div class="flex items-end justify-between">
                        <span class="text-3xl font-bold tracking-tight">Rp
                            {{ number_format($reportData['summary']['grand_total'], 0, ',', '.') }}</span>
                        <i class='bx bx-wallet text-3xl text-indigo-300'></i>
                    </div>
                </div>

                {{-- Simwa Total --}}
                <div
                    class="bg-white dark:bg-darkCard p-4 rounded-xl border border-emerald-100 dark:border-emerald-900/40 shadow-sm flex flex-col justify-between h-full">
                    <div class="text-emerald-600 dark:text-emerald-400 text-xs font-bold uppercase tracking-wider mb-2">
                        Total Simwa</div>
                    <div class="text-lg font-bold text-slate-800 dark:text-white">Rp
                        {{ number_format($reportData['summary']['total_simwa'], 0, ',', '.') }}</div>
                </div>

                {{-- Sukarela Total --}}
                <div
                    class="bg-white dark:bg-darkCard p-4 rounded-xl border border-amber-100 dark:border-amber-900/40 shadow-sm flex flex-col justify-between h-full">
                    <div class="text-amber-600 dark:text-amber-400 text-xs font-bold uppercase tracking-wider mb-2">Total
                        Sukarela</div>
                    <div class="text-lg font-bold text-slate-800 dark:text-white">Rp
                        {{ number_format($reportData['summary']['total_sukarela'], 0, ',', '.') }}</div>
                </div>

                {{-- Bermadani Total --}}
                <div
                    class="bg-white dark:bg-darkCard p-4 rounded-xl border border-blue-100 dark:border-blue-900/40 shadow-sm flex flex-col justify-between h-full">
                    <div class="text-blue-600 dark:text-blue-400 text-xs font-bold uppercase tracking-wider mb-2">Angsuran
                        Bermadani</div>
                    <div class="text-lg font-bold text-slate-800 dark:text-white">Rp
                        {{ number_format($reportData['summary']['total_angsuran_bermadani'], 0, ',', '.') }}</div>
                </div>
            </div>

            {{-- Advanced Report Table --}}
            <div
                class="bg-white dark:bg-darkCard rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden flex flex-col">
                <div
                    class="p-4 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center bg-slate-50/50 dark:bg-slate-800/10">
                    <h3 class="font-bold text-slate-800 dark:text-white">Detail Potongan Per Anggota</h3>
                    <div class="flex gap-2">
                        <span
                            class="text-xs font-mono text-slate-500 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 px-2 py-1 rounded">
                            {{ count($reportData['items']) }} Rows
                        </span>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead
                            class="bg-slate-50 dark:bg-slate-800 text-[10px] uppercase font-bold text-slate-500 dark:text-slate-400 sticky top-0 z-10 shadow-sm">
                            <tr>
                                <th
                                    class="px-3 py-3 w-10 border-b border-r border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 sticky left-0 z-20">
                                    No</th>
                                <th
                                    class="px-3 py-3 max-w-[150px] truncate border-b border-r border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 sticky left-10 z-20">
                                    Nama Anggota</th>

                                {{-- Simpanan Group --}}
                                <th colspan="2"
                                    class="px-2 py-1 text-center border-b border-r border-slate-200 dark:border-slate-700 bg-emerald-50/30 dark:bg-emerald-900/10 text-emerald-700">
                                    Simpanan</th>

                                {{-- Bermadani Group --}}
                                <th colspan="3"
                                    class="px-2 py-1 text-center border-b border-r border-slate-200 dark:border-slate-700 bg-blue-50/30 dark:bg-blue-900/10 text-blue-700">
                                    Internal (Bermadani)</th>

                                {{-- BMT 1 Group --}}
                                <th colspan="4"
                                    class="px-2 py-1 text-center border-b border-r border-slate-200 dark:border-slate-700 bg-purple-50/30 dark:bg-purple-900/10 text-purple-700">
                                    BMT Itqan 1</th>

                                {{-- BMT 2 Group --}}
                                <th colspan="4"
                                    class="px-2 py-1 text-center border-b border-r border-slate-200 dark:border-slate-700 bg-pink-50/30 dark:bg-pink-900/10 text-pink-700">
                                    BMT Itqan 2</th>

                                <th rowspan="2"
                                    class="px-3 py-3 text-right bg-slate-100 dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 min-w-[120px]">
                                    Total Potongan</th>
                            </tr>
                            <tr>
                                {{-- Empty for sticky columns --}}
                                <th
                                    class="border-b border-r border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 sticky left-0 z-20">
                                </th>
                                <th
                                    class="border-b border-r border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 sticky left-10 z-20">
                                </th>

                                {{-- Sub Headers --}}
                                <th
                                    class="px-2 py-2 text-right border-b border-r border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800">
                                    Wajib</th>
                                <th
                                    class="px-2 py-2 text-right border-b border-r border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800">
                                    Sukarela</th>

                                <th
                                    class="px-2 py-2 text-right border-b border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800">
                                    Angsuran</th>
                                <th
                                    class="px-1 py-2 text-center text-[9px] border-b border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 w-8">
                                    Ke</th>
                                <th
                                    class="px-1 py-2 text-center text-[9px] border-b border-r border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 w-8">
                                    Tnr</th>

                                <th
                                    class="px-2 py-2 text-right border-b border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800">
                                    Angsuran</th>
                                <th
                                    class="px-2 py-2 text-right border-b border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 text-[9px]">
                                    Simwa</th>
                                <th
                                    class="px-1 py-2 text-center text-[9px] border-b border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 w-8">
                                    Ke</th>
                                <th
                                    class="px-1 py-2 text-center text-[9px] border-b border-r border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 w-8">
                                    Tnr</th>

                                <th
                                    class="px-2 py-2 text-right border-b border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800">
                                    Angsuran</th>
                                <th
                                    class="px-2 py-2 text-right border-b border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 text-[9px]">
                                    Simwa</th>
                                <th
                                    class="px-1 py-2 text-center text-[9px] border-b border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 w-8">
                                    Ke</th>
                                <th
                                    class="px-1 py-2 text-center text-[9px] border-b border-r border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 w-8">
                                    Tnr</th>


                            </tr>
                        </thead>
                        <tbody class="text-xs divide-y divide-slate-100 dark:divide-slate-700">
                            @foreach($reportData['items'] as $index => $item)
                                <tr
                                    class="hover:bg-indigo-50/30 dark:hover:bg-slate-700/50 transition-colors {{ $index % 2 == 0 ? 'bg-white dark:bg-darkCard' : 'bg-slate-50/30 dark:bg-slate-800/20' }}">
                                    <td
                                        class="px-3 py-2 font-mono text-slate-500 border-r border-slate-100 dark:border-slate-700 sticky left-0 bg-inherit z-10">
                                        {{ $index + 1 }}</td>
                                    <td
                                        class="px-3 py-2 font-semibold text-slate-800 dark:text-white border-r border-slate-100 dark:border-slate-700 sticky left-10 bg-inherit z-10">
                                        <div class="flex items-center gap-1.5 max-w-[150px]">
                                            <span class="truncate" title="{{ $item['nama'] }}">{{ $item['nama'] }}</span>
                                            @if($item['has_loan'])
                                                <span class="inline-block w-1.5 h-1.5 rounded-full bg-indigo-500 shrink-0"></span>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- Simpanan --}}
                                    <td class="px-2 py-2 text-right font-mono text-emerald-600 dark:text-emerald-400">
                                        {{ number_format($item['simwa'], 0, ',', '.') }}</td>
                                    <td
                                        class="px-2 py-2 text-right font-mono border-r border-slate-100 dark:border-slate-700 {{ $item['sukarela'] > 0 ? 'text-amber-600 dark:text-amber-400 font-bold' : 'text-slate-300' }}">
                                        {{ $item['sukarela'] > 0 ? number_format($item['sukarela'], 0, ',', '.') : '-' }}
                                    </td>

                                    {{-- Bermadani --}}
                                    <td
                                        class="px-2 py-2 text-right font-mono {{ $item['angsuran_bermadani'] > 0 ? 'text-blue-600 dark:text-blue-400 font-bold' : 'text-slate-300' }}">
                                        {{ $item['angsuran_bermadani'] > 0 ? number_format($item['angsuran_bermadani'], 0, ',', '.') : '-' }}
                                    </td>
                                    <td class="px-1 py-2 text-center text-slate-400 text-[10px]">
                                        {{ $item['angsuran_bermadani'] > 0 ? $item['angsuran_ke_bermadani'] : '' }}</td>
                                    <td
                                        class="px-1 py-2 text-center text-slate-400 text-[10px] border-r border-slate-100 dark:border-slate-700">
                                        {{ $item['angsuran_bermadani'] > 0 ? $item['tenor_bermadani'] : '' }}</td>

                                    {{-- BMT 1 --}}
                                    <td
                                        class="px-2 py-2 text-right font-mono {{ $item['angsuran_bmt_itqan_1'] > 0 ? 'text-purple-600 dark:text-purple-400 font-bold' : 'text-slate-300' }}">
                                        {{ $item['angsuran_bmt_itqan_1'] > 0 ? number_format($item['angsuran_bmt_itqan_1'], 0, ',', '.') : '-' }}
                                    </td>
                                    <td class="px-2 py-2 text-right font-mono text-[10px] text-purple-400/80">
                                        {{ $item['simwa_bmt_itqan_1'] > 0 ? number_format($item['simwa_bmt_itqan_1'], 0, ',', '.') : '' }}
                                    </td>
                                    <td class="px-1 py-2 text-center text-slate-400 text-[10px]">
                                        {{ $item['angsuran_bmt_itqan_1'] > 0 ? $item['angsuran_ke_bmt_itqan_1'] : '' }}</td>
                                    <td
                                        class="px-1 py-2 text-center text-slate-400 text-[10px] border-r border-slate-100 dark:border-slate-700">
                                        {{ $item['angsuran_bmt_itqan_1'] > 0 ? $item['tenor_bmt_itqan_1'] : '' }}</td>

                                    {{-- BMT 2 --}}
                                    <td
                                        class="px-2 py-2 text-right font-mono {{ $item['angsuran_bmt_itqan_2'] > 0 ? 'text-pink-600 dark:text-pink-400 font-bold' : 'text-slate-300' }}">
                                        {{ $item['angsuran_bmt_itqan_2'] > 0 ? number_format($item['angsuran_bmt_itqan_2'], 0, ',', '.') : '-' }}
                                    </td>
                                    <td class="px-2 py-2 text-right font-mono text-[10px] text-pink-400/80">
                                        {{ $item['simwa_bmt_itqan_2'] > 0 ? number_format($item['simwa_bmt_itqan_2'], 0, ',', '.') : '' }}
                                    </td>
                                    <td class="px-1 py-2 text-center text-slate-400 text-[10px]">
                                        {{ $item['angsuran_bmt_itqan_2'] > 0 ? $item['angsuran_ke_bmt_itqan_2'] : '' }}</td>
                                    <td
                                        class="px-1 py-2 text-center text-slate-400 text-[10px] border-r border-slate-100 dark:border-slate-700">
                                        {{ $item['angsuran_bmt_itqan_2'] > 0 ? $item['tenor_bmt_itqan_2'] : '' }}</td>

                                    {{-- TOTAL --}}
                                    <td
                                        class="px-3 py-2 text-right font-bold text-slate-800 dark:text-white bg-slate-50/50 dark:bg-slate-800/30">
                                        {{ number_format($item['total'], 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot
                            class="bg-slate-100 dark:bg-slate-800 font-bold text-xs sticky bottom-0 z-10 shadow-[0_-2px_4px_rgba(0,0,0,0.1)]">
                            <tr>
                                <td colspan="2" class="px-3 py-3 text-right uppercase text-slate-500">Total Keseluruhan</td>
                                <td class="px-2 py-3 text-right text-emerald-600">
                                    {{ number_format($reportData['summary']['total_simwa'], 0, ',', '.') }}</td>
                                <td class="px-2 py-3 text-right text-amber-600">
                                    {{ number_format($reportData['summary']['total_sukarela'], 0, ',', '.') }}</td>
                                <td class="px-2 py-3 text-right text-blue-600">
                                    {{ number_format($reportData['summary']['total_angsuran_bermadani'], 0, ',', '.') }}
                                </td>
                                <td colspan="2"></td>
                                <td class="px-2 py-3 text-right text-purple-600">
                                    {{ number_format($reportData['summary']['total_angsuran_bmt_itqan_1'], 0, ',', '.') }}
                                </td>
                                <td colspan="3"></td>
                                <td class="px-2 py-3 text-right text-pink-600">
                                    {{ number_format($reportData['summary']['total_angsuran_bmt_itqan_2'], 0, ',', '.') }}
                                </td>
                                <td colspan="3"></td>
                                <td class="px-3 py-3 text-right text-primary text-sm">
                                    {{ number_format($reportData['summary']['grand_total'], 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>