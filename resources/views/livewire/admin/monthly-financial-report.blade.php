<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Laporan Keuangan Bulanan</h1>
            <p class="text-xs text-slate-500 mt-1">Generate laporan potongan gaji & SIMWA untuk unit keuangan</p>
        </div>
        <a href="{{ route('admin.reports.balance-sheet') }}" class="bg-white dark:bg-darkCard border border-slate-200 dark:border-slate-600 hover:bg-slate-50 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-white px-4 py-2 rounded-lg text-sm font-bold shadow-sm transition-colors flex items-center gap-2">
            <i class='bx bx-spreadsheet text-lg'></i>
            Lihat Neraca Saldo
        </a>
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
                        class="flex-1 bg-emerald-500 hover:bg-emerald-600 text-white font-semibold py-2.5 px-6 rounded-lg transition-all shadow-sm shadow-emerald-500/20 flex items-center justify-center gap-2 group">
                        <i class='bx bx-download text-xl group-hover:scale-110 transition-transform'></i>
                        <span>Download PDF</span>
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

                <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                    {{-- Total Member --}}
                    <div
                        class="bg-white dark:bg-slate-800 rounded-xl p-4 border border-slate-100 dark:border-slate-700 shadow-sm relative group overflow-hidden">
                        <div class="absolute right-0 top-0 p-3 opacity-10 group-hover:opacity-20 transition-opacity">
                            <i class='bx bx-group text-4xl text-slate-800 dark:text-white'></i>
                        </div>
                        <p class="text-[10px] text-slate-400 uppercase font-bold tracking-wider mb-1">Total Member</p>
                        <p class="text-2xl font-bold text-slate-800 dark:text-white">
                            {{ $reportData['summary']['total_members'] }}</p>
                        <p class="text-[10px] text-slate-400 mt-1">Anggota Terdaftar</p>
                    </div>

                    {{-- SIMWA --}}
                    <div
                        class="bg-white dark:bg-slate-800 rounded-xl p-4 border border-emerald-100 dark:border-emerald-900/30 shadow-sm relative group overflow-hidden">
                        <div class="absolute right-0 top-0 p-3 opacity-10 group-hover:opacity-20 transition-opacity">
                            <i class='bx bx-wallet text-4xl text-emerald-500'></i>
                        </div>
                        <p class="text-[10px] text-emerald-500 dark:text-emerald-400 uppercase font-bold tracking-wider mb-1">
                            Total SIMWA</p>
                        <p class="text-xl font-bold text-slate-800 dark:text-white">Rp
                            {{ number_format($reportData['summary']['total_simwa'], 0, ',', '.') }}</p>
                        <div class="w-full bg-emerald-100 dark:bg-emerald-900/30 h-1 mt-2 rounded-full">
                            <div class="bg-emerald-500 h-1 rounded-full" style="width: 100%"></div>
                        </div>
                    </div>

                    {{-- Sukarela --}}
                    <div
                        class="bg-white dark:bg-slate-800 rounded-xl p-4 border border-amber-100 dark:border-amber-900/30 shadow-sm relative group overflow-hidden">
                        <div class="absolute right-0 top-0 p-3 opacity-10 group-hover:opacity-20 transition-opacity">
                            <i class='bx bx-donate-heart text-4xl text-amber-500'></i>
                        </div>
                        <p class="text-[10px] text-amber-500 dark:text-amber-400 uppercase font-bold tracking-wider mb-1">
                            Total Sukarela</p>
                        <p class="text-xl font-bold text-slate-800 dark:text-white">Rp
                            {{ number_format($reportData['summary']['total_sukarela'], 0, ',', '.') }}</p>
                        <div class="w-full bg-amber-100 dark:bg-amber-900/30 h-1 mt-2 rounded-full">
                            <div class="bg-amber-500 h-1 rounded-full" style="width: 100%"></div>
                        </div>
                    </div>

                    {{-- Angsuran Bermadani --}}
                    <div
                        class="bg-white dark:bg-slate-800 rounded-xl p-4 border border-blue-100 dark:border-blue-900/30 shadow-sm relative group overflow-hidden">
                        <div class="absolute right-0 top-0 p-3 opacity-10 group-hover:opacity-20 transition-opacity">
                            <i class='bx bx-money text-4xl text-blue-500'></i>
                        </div>
                        <p class="text-[10px] text-blue-500 dark:text-blue-400 uppercase font-bold tracking-wider mb-1">
                            Angsuran Bermadani</p>
                        <p class="text-xl font-bold text-slate-800 dark:text-white">Rp
                            {{ number_format($reportData['summary']['total_angsuran_bermadani'], 0, ',', '.') }}</p>
                        <div class="w-full bg-blue-100 dark:bg-blue-900/30 h-1 mt-2 rounded-full">
                            <div class="bg-blue-500 h-1 rounded-full" style="width: 100%"></div>
                        </div>
                    </div>

                    {{-- Angsuran BMT ITQAN --}}
                    <div
                        class="bg-white dark:bg-slate-800 rounded-xl p-4 border border-purple-100 dark:border-purple-900/30 shadow-sm relative group overflow-hidden">
                        <div class="absolute right-0 top-0 p-3 opacity-10 group-hover:opacity-20 transition-opacity">
                            <i class='bx bx-bank text-4xl text-purple-500'></i>
                        </div>
                        <p class="text-[10px] text-purple-500 dark:text-purple-400 uppercase font-bold tracking-wider mb-1">
                            Angsuran BMT ITQAN</p>
                        <p class="text-xl font-bold text-slate-800 dark:text-white">Rp
                            {{ number_format($reportData['summary']['total_angsuran_bmt_itqan'], 0, ',', '.') }}</p>
                        <div class="w-full bg-purple-100 dark:bg-purple-900/30 h-1 mt-2 rounded-full">
                            <div class="bg-purple-500 h-1 rounded-full" style="width: 100%"></div>
                        </div>
                    </div>

                    {{-- Grand Total --}}
                    <div
                        class="bg-primary text-white rounded-xl p-4 shadow-lg shadow-indigo-500/20 relative group overflow-hidden">
                        <div class="absolute right-0 top-0 p-3 opacity-10 group-hover:opacity-20 transition-opacity">
                            <i class='bx bx-calculator text-4xl text-white'></i>
                        </div>
                        <p class="text-[10px] text-indigo-100 uppercase font-bold tracking-wider mb-1">Grand Total</p>
                        <p class="text-2xl font-bold">Rp
                            {{ number_format($reportData['summary']['grand_total'], 0, ',', '.') }}</p>
                        <p class="text-[10px] text-indigo-200 mt-1">Total Potongan Gaji</p>
                    </div>
                </div>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead
                        class="bg-slate-50 dark:bg-slate-700/50 text-[11px] uppercase font-bold text-slate-500 dark:text-slate-400 border-b border-slate-100 dark:border-slate-700">
                        <tr>
                            <th class="px-4 py-4 w-12">No</th>
                            <th class="px-4 py-4">Nama Anggota</th>
                            <th class="px-4 py-4 text-center">SIMWA</th>
                            <th class="px-4 py-4 text-center">Sukarela</th>
                            <th class="px-4 py-4 text-center">Angs. Bermadani</th>
                            <th class="px-4 py-4 text-center">Angs. BMT ITQAN</th>
                            <th class="px-4 py-4 text-right">Total</th>
                            <th class="px-4 py-4 text-center">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @foreach($reportData['items'] as $index => $item)
                            <tr class="group hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                                <td class="px-4 py-4 font-mono text-xs text-slate-500 group-hover:text-primary transition-colors">
                                    {{ $index + 1 }}</td>
                                <td class="px-4 py-4">
                                    <div class="font-semibold text-slate-800 dark:text-white">{{ $item['nama'] }}</div>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <div class="font-mono font-medium text-emerald-600 dark:text-emerald-400">Rp
                                        {{ number_format($item['simwa'], 0, ',', '.') }}</div>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    @if($item['sukarela'] > 0)
                                        <div class="font-mono font-medium text-amber-600 dark:text-amber-400">Rp
                                            {{ number_format($item['sukarela'], 0, ',', '.') }}</div>
                                    @else
                                        <span class="text-slate-300 dark:text-slate-600">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-center">
                                    @if($item['angsuran_bermadani'] > 0)
                                        <div class="font-mono font-medium text-blue-600 dark:text-blue-400">Rp
                                            {{ number_format($item['angsuran_bermadani'], 0, ',', '.') }}</div>
                                    @else
                                        <span class="text-slate-300 dark:text-slate-600">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-center">
                                    @if($item['angsuran_bmt_itqan'] > 0)
                                        <div class="font-mono font-medium text-purple-600 dark:text-purple-400">Rp
                                            {{ number_format($item['angsuran_bmt_itqan'], 0, ',', '.') }}</div>
                                    @else
                                        <span class="text-slate-300 dark:text-slate-600">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-right">
                                    <div class="font-bold text-slate-800 dark:text-white">Rp
                                        {{ number_format($item['total'], 0, ',', '.') }}</div>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    @if($item['has_loan'])
                                        @if($item['angsuran_bmt_itqan'] > 0 && $item['angsuran_bermadani'] > 0)
                                            <span class="bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400 px-2 py-1 rounded-md text-[10px] font-bold uppercase tracking-wide border border-indigo-100 dark:border-indigo-900/30">
                                                BM+BMT
                                            </span>
                                        @elseif($item['angsuran_bmt_itqan'] > 0)
                                            <span class="bg-purple-50 dark:bg-purple-900/20 text-purple-600 dark:text-purple-400 px-2 py-1 rounded-md text-[10px] font-bold uppercase tracking-wide border border-purple-100 dark:border-purple-900/30">
                                                BMT ITQAN
                                            </span>
                                        @else
                                            <span class="bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 px-2 py-1 rounded-md text-[10px] font-bold uppercase tracking-wide border border-blue-100 dark:border-blue-900/30">
                                                Bermadani
                                            </span>
                                        @endif
                                    @else
                                        <span class="text-[10px] text-slate-400 font-medium uppercase tracking-wide">
                                            SIMWA Only
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach

                        {{-- Total Row --}}
                        <tr class="bg-slate-50/80 dark:bg-slate-800/80 font-bold border-t-2 border-slate-200 dark:border-slate-600">
                            <td colspan="2" class="px-4 py-4 text-slate-900 dark:text-white text-right uppercase tracking-wider text-xs">
                                TOTAL PERIODE INI</td>
                            <td class="px-4 py-4 text-center text-emerald-600 dark:text-emerald-400">Rp
                                {{ number_format($reportData['summary']['total_simwa'], 0, ',', '.') }}</td>
                            <td class="px-4 py-4 text-center text-amber-600 dark:text-amber-400">Rp
                                {{ number_format($reportData['summary']['total_sukarela'], 0, ',', '.') }}</td>
                            <td class="px-4 py-4 text-center text-blue-600 dark:text-blue-400">Rp
                                {{ number_format($reportData['summary']['total_angsuran_bermadani'], 0, ',', '.') }}</td>
                            <td class="px-4 py-4 text-center text-purple-600 dark:text-purple-400">Rp
                                {{ number_format($reportData['summary']['total_angsuran_bmt_itqan'], 0, ',', '.') }}</td>
                            <td class="px-4 py-4 text-right text-primary text-base">Rp
                                {{ number_format($reportData['summary']['grand_total'], 0, ',', '.') }}</td>
                            <td class="px-4 py-4"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>