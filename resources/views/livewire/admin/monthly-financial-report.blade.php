<div class="p-6 space-y-6">
    {{-- Header --}}
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Laporan Keuangan Bulanan</h1>
            <p class="text-sm text-slate-500 mt-1">Generate laporan potongan gaji & SIMWA untuk unit keuangan</p>
        </div>
    </div>

    {{-- Filter Section --}}
    <div class="bg-white dark:bg-darkCard rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 p-6">
        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Pilih Periode</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Bulan</label>
                <select wire:model="selectedMonth" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-lg px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary">
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
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Tahun</label>
                <select wire:model="selectedYear" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-lg px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary">
                    @for ($year = now()->year; $year >= 2020; $year--)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @endfor
                </select>
            </div>

            <div class="flex items-end gap-3">
                <button wire:click="generateReport" 
                    class="flex-1 bg-primary hover:bg-blue-700 text-white font-bold py-2.5 px-6 rounded-lg transition-colors flex items-center justify-center gap-2">
                    <i class='bx bx-file'></i> Generate Laporan
                </button>
                
                @if($showPreview)
                    <button wire:click="downloadPDF" 
                        class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 px-6 rounded-lg transition-colors flex items-center justify-center gap-2">
                        <i class='bx bx-download'></i> Download PDF
                    </button>
                @endif
            </div>
        </div>
    </div>

    {{-- Preview Section --}}
    @if($showPreview && $reportData)
        <div class="bg-white dark:bg-darkCard rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">
            {{-- Summary Cards --}}
            <div class="p-6 border-b border-slate-100 dark:border-slate-700">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Ringkasan</h3>
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div class="bg-slate-50 dark:bg-slate-800 rounded-xl p-4">
                        <p class="text-xs text-slate-500 uppercase font-bold mb-1">Total Member</p>
                        <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ $reportData['summary']['total_members'] }}</p>
                    </div>
                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-4">
                        <p class="text-xs text-blue-600 dark:text-blue-400 uppercase font-bold mb-1">Angsuran</p>
                        <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">Rp {{ number_format($reportData['summary']['total_angsuran'], 0, ',', '.') }}</p>
                    </div>
                    <div class="bg-emerald-50 dark:bg-emerald-900/20 rounded-xl p-4">
                        <p class="text-xs text-emerald-600 dark:text-emerald-400 uppercase font-bold mb-1">SIMWA</p>
                        <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">Rp {{ number_format($reportData['summary']['total_simwa'], 0, ',', '.') }}</p>
                    </div>
                    <div class="bg-amber-50 dark:bg-amber-900/20 rounded-xl p-4">
                        <p class="text-xs text-amber-600 dark:text-amber-400 uppercase font-bold mb-1">Sukarela</p>
                        <p class="text-2xl font-bold text-amber-600 dark:text-amber-400">Rp {{ number_format($reportData['summary']['total_sukarela'], 0, ',', '.') }}</p>
                    </div>
                    <div class="bg-primary/10 rounded-xl p-4">
                        <p class="text-xs text-primary uppercase font-bold mb-1">Grand Total</p>
                        <p class="text-2xl font-bold text-primary">Rp {{ number_format($reportData['summary']['grand_total'], 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50 dark:bg-slate-800 text-xs uppercase font-bold text-slate-500 border-b border-slate-200 dark:border-slate-700">
                        <tr>
                            <th class="px-6 py-4">No</th>
                            <th class="px-6 py-4">Nama Anggota</th>
                            <th class="px-6 py-4 text-center">Angsuran</th>
                            <th class="px-6 py-4 text-center">SIMWA</th>
                            <th class="px-6 py-4 text-center">Sukarela</th>
                            <th class="px-6 py-4 text-right">Total</th>
                            <th class="px-6 py-4 text-center">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @foreach($reportData['items'] as $index => $item)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                <td class="px-6 py-4 font-medium text-slate-900 dark:text-white">{{ $index + 1 }}</td>
                                <td class="px-6 py-4 font-medium text-slate-900 dark:text-white">{{ $item['nama'] }}</td>
                                <td class="px-6 py-4 text-center text-slate-600 dark:text-slate-400">
                                    @if($item['angsuran'] > 0)
                                        <span class="font-bold text-blue-600 dark:text-blue-400">Rp {{ number_format($item['angsuran'], 0, ',', '.') }}</span>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="font-bold text-emerald-600 dark:text-emerald-400">Rp {{ number_format($item['simwa'], 0, ',', '.') }}</span>
                                </td>
                                <td class="px-6 py-4 text-center text-slate-600 dark:text-slate-400">
                                    @if($item['sukarela'] > 0)
                                        <span class="font-bold text-amber-600 dark:text-amber-400">Rp {{ number_format($item['sukarela'], 0, ',', '.') }}</span>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right font-bold text-slate-900 dark:text-white">Rp {{ number_format($item['total'], 0, ',', '.') }}</td>
                                <td class="px-6 py-4 text-center text-xs text-slate-500">
                                    @if($item['has_loan'])
                                        <span class="bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 px-2 py-1 rounded font-bold">
                                            Angsuran ke-{{ $item['tenor_remaining'] }}
                                        </span>
                                    @else
                                        <span class="text-slate-400">SIMWA Only</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        
                        {{-- Total Row --}}
                        <tr class="bg-slate-100 dark:bg-slate-800 font-bold">
                            <td colspan="2" class="px-6 py-4 text-slate-900 dark:text-white uppercase">TOTAL</td>
                            <td class="px-6 py-4 text-center text-blue-600 dark:text-blue-400">Rp {{ number_format($reportData['summary']['total_angsuran'], 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-center text-emerald-600 dark:text-emerald-400">Rp {{ number_format($reportData['summary']['total_simwa'], 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-center text-amber-600 dark:text-amber-400">Rp {{ number_format($reportData['summary']['total_sukarela'], 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-right text-primary text-xl">Rp {{ number_format($reportData['summary']['grand_total'], 0, ',', '.') }}</td>
                            <td class="px-6 py-4"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
