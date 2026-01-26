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
    <div class="bg-card dark:bg-darkCard rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 p-5">
        <div class="flex items-center gap-2 mb-4">
            <div
                class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-500/10 flex items-center justify-center text-indigo-500">
                <i class='bx bx-filter-alt text-lg'></i>
            </div>
            <h3 class="font-bold text-slate-800 dark:text-white">Filter Laporan</h3>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
            <div class="md:col-span-3">
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Bulan</label>
                <div class="relative">
                    <select wire:model="selectedMonth"
                        class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 text-slate-700 dark:text-white rounded-lg px-4 py-2.5 text-sm outline-none focus:ring-1 focus:ring-primary focus:border-primary appearance-none cursor-pointer">
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
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-500">
                        <i class='bx bx-chevron-down text-lg'></i>
                    </div>
                </div>
            </div>

            <div class="md:col-span-3">
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Tahun</label>
                <div class="relative">
                    <select wire:model="selectedYear"
                        class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 text-slate-700 dark:text-white rounded-lg px-4 py-2.5 text-sm outline-none focus:ring-1 focus:ring-primary focus:border-primary appearance-none cursor-pointer">
                        @for ($year = now()->year; $year >= 2020; $year--)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endfor
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-500">
                        <i class='bx bx-chevron-down text-lg'></i>
                    </div>
                </div>
            </div>

            <div class="md:col-span-6 flex items-end gap-3">
                <button wire:click="generateReport"
                    class="flex-1 bg-primary hover:bg-primary/90 text-white font-semibold py-2.5 px-6 rounded-lg transition-all shadow-sm shadow-indigo-500/20 flex items-center justify-center gap-2 group">
                    <i class='bx bx-file text-xl group-hover:scale-110 transition-transform'></i>
                    <span>Generate Laporan</span>
                </button>

                @if($showPreview)
                    <button wire:click="downloadPDF"
                        class="bg-emerald-500 hover:bg-emerald-600 text-white font-semibold py-2.5 px-4 rounded-lg transition-all shadow-sm shadow-emerald-500/20 flex items-center justify-center gap-2 group"
                        title="Laporan lengkap untuk internal koperasi">
                        <i class='bx bx-download text-xl group-hover:scale-110 transition-transform'></i>
                        <span>PDF Internal</span>
                    </button>

                    <button wire:click="downloadSimplePDF"
                        class="bg-amber-500 hover:bg-amber-600 text-white font-semibold py-2.5 px-4 rounded-lg transition-all shadow-sm shadow-amber-500/20 flex items-center justify-center gap-2 group"
                        title="Laporan singkat untuk Unit Keuangan Kampus">
                        <i class='bx bx-building text-xl group-hover:scale-110 transition-transform'></i>
                        <span>PDF Keuangan</span>
                    </button>
                @endif
            </div>
        </div>
    </div>

    {{-- Preview Section --}}
    @if($showPreview && $reportData)
        <div
            class="bg-card dark:bg-darkCard rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">
            {{-- Summary Cards --}}
            <div class="p-5 border-b border-slate-100 dark:border-slate-700 bg-slate-50/30 dark:bg-slate-800/10">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-slate-800 dark:text-white">Ringkasan Laporan</h3>
                    <span class="text-xs font-mono text-slate-500 bg-slate-100 dark:bg-slate-700 px-2 py-1 rounded">
                        Periode: {{ $selectedMonth }}/{{ $selectedYear }}
                    </span>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-7 gap-3">
                    {{-- Total Member --}}
                    <div
                        class="bg-white dark:bg-slate-800 rounded-xl p-3 border border-slate-100 dark:border-slate-700 shadow-sm">
                        <p class="text-[9px] text-slate-400 uppercase font-bold tracking-wider mb-1">Total Member</p>
                        <p class="text-xl font-bold text-slate-800 dark:text-white">
                            {{ $reportData['summary']['total_members'] }}</p>
                    </div>

                    {{-- SIMWA --}}
                    <div
                        class="bg-white dark:bg-slate-800 rounded-xl p-3 border border-emerald-100 dark:border-emerald-900/30 shadow-sm">
                        <p class="text-[9px] text-emerald-500 uppercase font-bold tracking-wider mb-1">Total SIMWA</p>
                        <p class="text-lg font-bold text-slate-800 dark:text-white">Rp
                            {{ number_format($reportData['summary']['total_simwa'], 0, ',', '.') }}</p>
                    </div>

                    {{-- Sukarela --}}
                    <div
                        class="bg-white dark:bg-slate-800 rounded-xl p-3 border border-amber-100 dark:border-amber-900/30 shadow-sm">
                        <p class="text-[9px] text-amber-500 uppercase font-bold tracking-wider mb-1">Total Sukarela</p>
                        <p class="text-lg font-bold text-slate-800 dark:text-white">Rp
                            {{ number_format($reportData['summary']['total_sukarela'], 0, ',', '.') }}</p>
                    </div>

                    {{-- Angsuran Bermadani --}}
                    <div
                        class="bg-white dark:bg-slate-800 rounded-xl p-3 border border-blue-100 dark:border-blue-900/30 shadow-sm">
                        <p class="text-[9px] text-blue-500 uppercase font-bold tracking-wider mb-1">Angs. Bermadani</p>
                        <p class="text-lg font-bold text-slate-800 dark:text-white">Rp
                            {{ number_format($reportData['summary']['total_angsuran_bermadani'], 0, ',', '.') }}</p>
                    </div>

                    {{-- Angsuran BMT ITQAN 1 --}}
                    <div
                        class="bg-white dark:bg-slate-800 rounded-xl p-3 border border-purple-100 dark:border-purple-900/30 shadow-sm">
                        <p class="text-[9px] text-purple-500 uppercase font-bold tracking-wider mb-1">Angs. BMT ITQAN 1</p>
                        <p class="text-lg font-bold text-slate-800 dark:text-white">Rp
                            {{ number_format($reportData['summary']['total_angsuran_bmt_itqan_1'], 0, ',', '.') }}</p>
                    </div>

                    {{-- Angsuran BMT ITQAN 2 --}}
                    <div
                        class="bg-white dark:bg-slate-800 rounded-xl p-3 border border-pink-100 dark:border-pink-900/30 shadow-sm">
                        <p class="text-[9px] text-pink-500 uppercase font-bold tracking-wider mb-1">Angs. BMT ITQAN 2</p>
                        <p class="text-lg font-bold text-slate-800 dark:text-white">Rp
                            {{ number_format($reportData['summary']['total_angsuran_bmt_itqan_2'], 0, ',', '.') }}</p>
                    </div>

                    {{-- Grand Total --}}
                    <div class="bg-primary text-white rounded-xl p-3 shadow-lg shadow-indigo-500/20">
                        <p class="text-[9px] text-indigo-100 uppercase font-bold tracking-wider mb-1">Grand Total</p>
                        <p class="text-xl font-bold">Rp
                            {{ number_format($reportData['summary']['grand_total'], 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead
                        class="bg-slate-50 dark:bg-slate-700/50 text-[10px] uppercase font-bold text-slate-500 dark:text-slate-400 border-b border-slate-100 dark:border-slate-700">
                        <tr>
                            <th class="px-2 py-3 w-8">No</th>
                            <th class="px-2 py-3">Nama</th>
                            <th class="px-2 py-3 text-right">SIMWA</th>
                            <th class="px-2 py-3 text-right">Sukarela</th>
                            <th class="px-2 py-3 text-right">Bermadani</th>
                            <th class="px-1 py-3 text-center">Ke</th>
                            <th class="px-1 py-3 text-center">Tnr</th>
                            <th class="px-2 py-3 text-right">BMT IT 1</th>
                            <th class="px-2 py-3 text-right text-xs">Sim BMT</th>
                            <th class="px-1 py-3 text-center">Ke</th>
                            <th class="px-1 py-3 text-center">Tnr</th>
                            <th class="px-2 py-3 text-right">BMT IT 2</th>
                            <th class="px-2 py-3 text-right text-xs">Sim BMT</th>
                            <th class="px-1 py-3 text-center">Ke</th>
                            <th class="px-1 py-3 text-center">Tnr</th>
                            <th class="px-2 py-3 text-right">Total</th>
                            <th class="px-2 py-3 text-center">Ket</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @foreach($reportData['items'] as $index => $item)
                            <tr class="group hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors text-xs">
                                <td class="px-2 py-2 font-mono text-slate-500">{{ $index + 1 }}</td>
                                <td class="px-2 py-2 font-semibold text-slate-800 dark:text-white">{{ $item['nama'] }}</td>
                                <td class="px-2 py-2 text-right font-mono text-emerald-600 dark:text-emerald-400">
                                    {{ number_format($item['simwa'], 0, ',', '.') }}</td>
                                <td class="px-2 py-2 text-right font-mono">
                                    @if($item['sukarela'] > 0)
                                        <span
                                            class="text-amber-600 dark:text-amber-400">{{ number_format($item['sukarela'], 0, ',', '.') }}</span>
                                    @else
                                        <span class="text-slate-300">-</span>
                                    @endif
                                </td>
                                <td class="px-2 py-2 text-right font-mono">
                                    @if($item['angsuran_bermadani'] > 0)
                                        <span
                                            class="text-blue-600 dark:text-blue-400">{{ number_format($item['angsuran_bermadani'], 0, ',', '.') }}</span>
                                    @else
                                        <span class="text-slate-300">-</span>
                                    @endif
                                </td>
                                <td class="px-1 py-2 text-center text-slate-500">
                                    {{ $item['angsuran_bermadani'] > 0 ? $item['angsuran_ke_bermadani'] : '-' }}</td>
                                <td class="px-1 py-2 text-center text-slate-500">
                                    {{ $item['angsuran_bermadani'] > 0 ? $item['tenor_bermadani'] : '-' }}</td>
                                <td class="px-2 py-2 text-right font-mono">
                                    @if($item['angsuran_bmt_itqan_1'] > 0)
                                        <span class="text-purple-600 dark:text-purple-400">{{ number_format($item['angsuran_bmt_itqan_1'], 0, ',', '.') }}</span>
                                    @else
                                        <span class="text-slate-300">-</span>
                                    @endif
                                </td>
                                <td class="px-2 py-2 text-right font-mono text-xs">
                                    @if($item['simwa_bmt_itqan_1'] > 0)
                                        <span class="text-purple-500 dark:text-purple-300">{{ number_format($item['simwa_bmt_itqan_1'], 0, ',', '.') }}</span>
                                    @else
                                        <span class="text-slate-300">-</span>
                                    @endif
                                </td>
                                <td class="px-1 py-2 text-center text-slate-500">
                                    {{ $item['angsuran_bmt_itqan_1'] > 0 ? $item['angsuran_ke_bmt_itqan_1'] : '-' }}</td>
                                <td class="px-1 py-2 text-center text-slate-500">
                                    {{ $item['angsuran_bmt_itqan_1'] > 0 ? $item['tenor_bmt_itqan_1'] : '-' }}</td>
                                <td class="px-2 py-2 text-right font-mono">
                                    @if($item['angsuran_bmt_itqan_2'] > 0)
                                        <span class="text-pink-600 dark:text-pink-400">{{ number_format($item['angsuran_bmt_itqan_2'], 0, ',', '.') }}</span>
                                    @else
                                        <span class="text-slate-300">-</span>
                                    @endif
                                </td>
                                <td class="px-2 py-2 text-right font-mono text-xs">
                                    @if($item['simwa_bmt_itqan_2'] > 0)
                                        <span class="text-pink-500 dark:text-pink-300">{{ number_format($item['simwa_bmt_itqan_2'], 0, ',', '.') }}</span>
                                    @else
                                        <span class="text-slate-300">-</span>
                                    @endif
                                </td>
                                <td class="px-1 py-2 text-center text-slate-500">
                                    {{ $item['angsuran_bmt_itqan_2'] > 0 ? $item['angsuran_ke_bmt_itqan_2'] : '-' }}</td>
                                <td class="px-1 py-2 text-center text-slate-500">
                                    {{ $item['angsuran_bmt_itqan_2'] > 0 ? $item['tenor_bmt_itqan_2'] : '-' }}</td>
                                <td class="px-2 py-2 text-right font-bold text-slate-800 dark:text-white">
                                    {{ number_format($item['total'], 0, ',', '.') }}</td>
                                <td class="px-2 py-2 text-center">
                                    @if($item['has_loan'])
                                                            @php
                                                                $badges = [];
                                                                if ($item['angsuran_bermadani'] > 0)
                                                                    $badges[] = 'BM';
                                                                if ($item['angsuran_bmt_itqan_1'] > 0)
                                                                    $badges[] = 'IT1';
                                                                if ($item['angsuran_bmt_itqan_2'] > 0)
                                                                    $badges[] = 'IT2';
                                                            @endphp
                                         <span
                                                                class="bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400 px-1.5 py-0.5 rounded text-[9px] font-bold">
                                                                {{ implode('+', $badges) }}
                                                            </span>
                                    @else
                                        <span class="text-[9px] text-slate-400">SIM</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach

                        {{-- Total Row --}}
                        <tr
                            class="bg-slate-50/80 dark:bg-slate-800/80 font-bold border-t-2 border-slate-200 dark:border-slate-600 text-xs">
                            <td colspan="2"
                                class="px-2 py-3 text-slate-900 dark:text-white text-right uppercase tracking-wider">TOTAL
                            </td>
                            <td class="px-2 py-3 text-right text-emerald-600 dark:text-emerald-400">
                                {{ number_format($reportData['summary']['total_simwa'], 0, ',', '.') }}</td>
                            <td class="px-2 py-3 text-right text-amber-600 dark:text-amber-400">
                                {{ number_format($reportData['summary']['total_sukarela'], 0, ',', '.') }}</td>
                            <td class="px-2 py-3 text-right text-blue-600 dark:text-blue-400">
                                {{ number_format($reportData['summary']['total_angsuran_bermadani'], 0, ',', '.') }}</td>
                            <td colspan="2"></td>
                            <td class="px-2 py-3 text-right text-purple-600 dark:text-purple-400">
                                {{ number_format($reportData['summary']['total_angsuran_bmt_itqan_1'], 0, ',', '.') }}</td>
                            <td colspan="2"></td>
                            <td class="px-2 py-3 text-right text-pink-600 dark:text-pink-400">
                                {{ number_format($reportData['summary']['total_angsuran_bmt_itqan_2'], 0, ',', '.') }}</td>
                            <td colspan="2"></td>
                            <td class="px-2 py-3 text-right text-primary text-sm">
                                {{ number_format($reportData['summary']['grand_total'], 0, ',', '.') }}</td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>