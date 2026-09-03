<div>
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Laporan Keuangan</h1>
            <p class="text-slate-500 dark:text-zinc-400">KSPPS Berkah Madani</p>
        </div>
        <div class="flex items-center gap-3">
            <select wire:model.live="selectedYear" class="bg-white dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 text-slate-700 dark:text-zinc-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent outline-none shadow-sm transition-all">
                @for ($year = date('Y'); $year >= 2024; $year--)
                    <option value="{{ $year }}">{{ $year }}</option>
                @endfor
            </select>
            <button class="flex items-center gap-2 bg-white dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 hover:bg-slate-50 dark:hover:bg-zinc-700 text-slate-700 dark:text-zinc-300 rounded-lg px-4 py-2 shadow-sm transition-all font-medium">
                <i class='bx bxs-file-pdf text-red-500 text-lg'></i>
                Export PDF
            </button>
        </div>
    </div>

    <!-- Tab Navigation -->
    <div class="flex flex-wrap gap-2 mb-6">
        <button wire:click="setTab('neraca')" class="flex items-center gap-2 px-4 py-2.5 rounded-full text-sm font-medium transition-all {{ $activeTab === 'neraca' ? 'bg-indigo-600 text-white shadow-md' : 'bg-slate-100 dark:bg-zinc-800 text-slate-600 dark:text-zinc-400 hover:bg-slate-200 dark:hover:bg-zinc-700' }}">
            <i class='bx bx-spreadsheet text-lg'></i> Neraca
        </button>
        <button wire:click="setTab('shu')" class="flex items-center gap-2 px-4 py-2.5 rounded-full text-sm font-medium transition-all {{ $activeTab === 'shu' ? 'bg-indigo-600 text-white shadow-md' : 'bg-slate-100 dark:bg-zinc-800 text-slate-600 dark:text-zinc-400 hover:bg-slate-200 dark:hover:bg-zinc-700' }}">
            <i class='bx bx-wallet text-lg'></i> Laporan SHU
        </button>
        <button wire:click="setTab('perubahan_ekuitas')" class="flex items-center gap-2 px-4 py-2.5 rounded-full text-sm font-medium transition-all {{ $activeTab === 'perubahan_ekuitas' ? 'bg-indigo-600 text-white shadow-md' : 'bg-slate-100 dark:bg-zinc-800 text-slate-600 dark:text-zinc-400 hover:bg-slate-200 dark:hover:bg-zinc-700' }}">
            <i class='bx bx-trending-up text-lg'></i> Perubahan Ekuitas
        </button>
        <button wire:click="setTab('arus_kas')" class="flex items-center gap-2 px-4 py-2.5 rounded-full text-sm font-medium transition-all {{ $activeTab === 'arus_kas' ? 'bg-indigo-600 text-white shadow-md' : 'bg-slate-100 dark:bg-zinc-800 text-slate-600 dark:text-zinc-400 hover:bg-slate-200 dark:hover:bg-zinc-700' }}">
            <i class='bx bx-transfer text-lg'></i> Arus Kas
        </button>
    </div>

    @if($isLoading)
        <div class="flex justify-center items-center py-20">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
        </div>
    @else
        <!-- Content Area -->
        <div class="bg-white dark:bg-zinc-800 rounded-2xl shadow-sm border border-slate-200 dark:border-zinc-700 overflow-hidden relative">
            
            @php
                $formatRp = function($val) {
                    $formatted = number_format(abs($val), 0, ',', '.');
                    if ($val < 0) {
                        return '<span class="text-red-500 dark:text-red-400 font-medium">(Rp ' . $formatted . ')</span>';
                    }
                    return 'Rp ' . $formatted;
                };
            @endphp

            {{-- NERACA TAB --}}
            @if($activeTab === 'neraca')
                @php
                    $neraca = $reportData['neraca'] ?? [];
                    $totalAset = $neraca['total_aset'] ?? 0;
                    $totalLiabilitasEkuitas = $neraca['total_liabilitas_ekuitas'] ?? 0;
                    $isBalanced = round($totalAset, 2) === round($totalLiabilitasEkuitas, 2);
                @endphp
                
                <div class="p-6 border-b border-slate-100 dark:border-zinc-700 flex justify-between items-center bg-slate-50/50 dark:bg-zinc-800/50">
                    <div>
                        <h2 class="text-lg font-bold text-slate-800 dark:text-white uppercase tracking-wide">Neraca (Laporan Posisi Keuangan)</h2>
                        <p class="text-sm text-slate-500 dark:text-zinc-400 mt-1">Per 31 Desember {{ $selectedYear }}</p>
                    </div>
                    <div>
                        @if($isBalanced)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 text-xs font-bold border border-emerald-200 dark:border-emerald-800/50">
                                BALANCED <i class='bx bx-check-circle text-base'></i>
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md bg-rose-50 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 text-xs font-bold border border-rose-200 dark:border-rose-800/50">
                                IMBALANCED <i class='bx bx-error text-base'></i>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="p-6 grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Left Column: ASET -->
                    <div>
                        <h3 class="text-md font-bold text-slate-700 dark:text-zinc-300 mb-4 border-b border-slate-200 dark:border-zinc-700 pb-2">ASET</h3>
                        
                        <div class="space-y-4">
                            <!-- Aset Lancar -->
                            <div>
                                <h4 class="text-sm font-semibold text-slate-600 dark:text-zinc-400 mb-2">Aset Lancar</h4>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between py-1">
                                        <span class="text-slate-600 dark:text-zinc-400">Kas & Setara Kas</span>
                                        <span class="font-mono">{!! $formatRp($neraca['kas'] ?? 0) !!}</span>
                                    </div>
                                    <div class="flex justify-between py-1">
                                        <span class="text-slate-600 dark:text-zinc-400">Piutang Pembiayaan</span>
                                        <span class="font-mono">{!! $formatRp($neraca['piutang_pembiayaan'] ?? 0) !!}</span>
                                    </div>
                                    <div class="flex justify-between py-1">
                                        <span class="text-slate-600 dark:text-zinc-400 ml-4">Cadangan Kerugian Piutang</span>
                                        <span class="font-mono">{!! $formatRp(-abs($neraca['cadangan_kerugian'] ?? 0)) !!}</span>
                                    </div>
                                    <div class="flex justify-between py-1">
                                        <span class="text-slate-600 dark:text-zinc-400">Piutang Lain-lain</span>
                                        <span class="font-mono">{!! $formatRp($neraca['piutang_lain'] ?? 0) !!}</span>
                                    </div>
                                    <div class="flex justify-between py-1">
                                        <span class="text-slate-600 dark:text-zinc-400">Persediaan</span>
                                        <span class="font-mono">{!! $formatRp($neraca['persediaan'] ?? 0) !!}</span>
                                    </div>
                                    <div class="flex justify-between py-2 border-t border-slate-100 dark:border-zinc-700 font-semibold text-slate-700 dark:text-zinc-300">
                                        <span>Total Aset Lancar</span>
                                        <span class="font-mono">{!! $formatRp($neraca['total_aset_lancar'] ?? 0) !!}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Aset Tidak Lancar -->
                            <div>
                                <h4 class="text-sm font-semibold text-slate-600 dark:text-zinc-400 mb-2">Aset Tidak Lancar</h4>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between py-1">
                                        <span class="text-slate-600 dark:text-zinc-400">Aset Tetap (Neto)</span>
                                        <span class="font-mono">{!! $formatRp($neraca['aset_tetap'] ?? 0) !!}</span>
                                    </div>
                                    <div class="flex justify-between py-1">
                                        <span class="text-slate-600 dark:text-zinc-400">Aset Lainnya</span>
                                        <span class="font-mono">{!! $formatRp($neraca['aset_lainnya'] ?? 0) !!}</span>
                                    </div>
                                    <div class="flex justify-between py-2 border-t border-slate-100 dark:border-zinc-700 font-semibold text-slate-700 dark:text-zinc-300">
                                        <span>Total Aset Tidak Lancar</span>
                                        <span class="font-mono">{!! $formatRp($neraca['total_aset_tidak_lancar'] ?? 0) !!}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-6 flex justify-between p-3 bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-100 dark:border-indigo-800/30 rounded-lg font-bold text-indigo-900 dark:text-indigo-300">
                            <span>TOTAL ASET</span>
                            <span class="font-mono text-base">{!! $formatRp($totalAset) !!}</span>
                        </div>
                    </div>

                    <!-- Right Column: LIABILITAS & EKUITAS -->
                    <div>
                        <h3 class="text-md font-bold text-slate-700 dark:text-zinc-300 mb-4 border-b border-slate-200 dark:border-zinc-700 pb-2">LIABILITAS & EKUITAS</h3>
                        
                        <div class="space-y-4">
                            <!-- Liabilitas -->
                            <div>
                                <h4 class="text-sm font-semibold text-slate-600 dark:text-zinc-400 mb-2">Liabilitas</h4>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between py-1">
                                        <span class="text-slate-600 dark:text-zinc-400">Simpanan Anggota (Wadiah)</span>
                                        <span class="font-mono">{!! $formatRp($neraca['simpanan_anggota'] ?? 0) !!}</span>
                                    </div>
                                    <div class="flex justify-between py-1">
                                        <span class="text-slate-600 dark:text-zinc-400">Utang Lain-lain</span>
                                        <span class="font-mono">{!! $formatRp($neraca['utang_lain'] ?? 0) !!}</span>
                                    </div>
                                    <div class="flex justify-between py-2 border-t border-slate-100 dark:border-zinc-700 font-semibold text-slate-700 dark:text-zinc-300">
                                        <span>Total Liabilitas</span>
                                        <span class="font-mono">{!! $formatRp($neraca['total_liabilitas'] ?? 0) !!}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Ekuitas -->
                            <div>
                                <h4 class="text-sm font-semibold text-slate-600 dark:text-zinc-400 mb-2">Ekuitas</h4>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between py-1">
                                        <span class="text-slate-600 dark:text-zinc-400">Simpanan Pokok</span>
                                        <span class="font-mono">{!! $formatRp($neraca['simpanan_pokok'] ?? 0) !!}</span>
                                    </div>
                                    <div class="flex justify-between py-1">
                                        <span class="text-slate-600 dark:text-zinc-400">Simpanan Wajib</span>
                                        <span class="font-mono">{!! $formatRp($neraca['simpanan_wajib'] ?? 0) !!}</span>
                                    </div>
                                    <div class="flex justify-between py-1">
                                        <span class="text-slate-600 dark:text-zinc-400">Cadangan</span>
                                        <span class="font-mono">{!! $formatRp($neraca['cadangan'] ?? 0) !!}</span>
                                    </div>
                                    <div class="flex justify-between py-1">
                                        <span class="text-slate-600 dark:text-zinc-400">SHU Tahun Berjalan</span>
                                        <span class="font-mono">{!! $formatRp($neraca['shu_berjalan'] ?? 0) !!}</span>
                                    </div>
                                    <div class="flex justify-between py-2 border-t border-slate-100 dark:border-zinc-700 font-semibold text-slate-700 dark:text-zinc-300">
                                        <span>Total Ekuitas</span>
                                        <span class="font-mono">{!! $formatRp($neraca['total_ekuitas'] ?? 0) !!}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-6 flex justify-between p-3 bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-100 dark:border-indigo-800/30 rounded-lg font-bold text-indigo-900 dark:text-indigo-300">
                            <span>TOTAL LIABILITAS & EKUITAS</span>
                            <span class="font-mono text-base">{!! $formatRp($totalLiabilitasEkuitas) !!}</span>
                        </div>
                    </div>
                </div>
            @endif

            {{-- LAPORAN SHU TAB --}}
            @if($activeTab === 'shu')
                @php
                    $shu = $reportData['shu'] ?? [];
                    $totalPendapatan = $shu['total_pendapatan'] ?? 0;
                    $totalBeban = $shu['total_beban'] ?? 0;
                    $shuBersih = $shu['shu_bersih'] ?? 0;
                @endphp
                <div class="p-6 border-b border-slate-100 dark:border-zinc-700 flex justify-between items-center bg-slate-50/50 dark:bg-zinc-800/50">
                    <div>
                        <h2 class="text-lg font-bold text-slate-800 dark:text-white uppercase tracking-wide">Laporan Sisa Hasil Usaha (SHU)</h2>
                        <p class="text-sm text-slate-500 dark:text-zinc-400 mt-1">Untuk Tahun yang Berakhir 31 Desember {{ $selectedYear }}</p>
                    </div>
                </div>
                
                <div class="p-6 max-w-3xl mx-auto">
                    <!-- Pendapatan -->
                    <div class="mb-6">
                        <h3 class="text-sm font-bold text-slate-700 dark:text-zinc-300 uppercase mb-3 bg-slate-100 dark:bg-zinc-800/80 p-2 rounded">PENDAPATAN</h3>
                        <div class="space-y-2 text-sm pl-4 pr-2">
                            <div class="flex justify-between py-1">
                                <span class="text-slate-600 dark:text-zinc-400">Margin Pembiayaan</span>
                                <span class="font-mono">{!! $formatRp($shu['margin_pembiayaan'] ?? 0) !!}</span>
                            </div>
                            <div class="flex justify-between py-1">
                                <span class="text-slate-600 dark:text-zinc-400">Pendapatan Administrasi</span>
                                <span class="font-mono">{!! $formatRp($shu['pendapatan_administrasi'] ?? 0) !!}</span>
                            </div>
                            <div class="flex justify-between py-1">
                                <span class="text-slate-600 dark:text-zinc-400">Pendapatan Lain-lain</span>
                                <span class="font-mono">{!! $formatRp($shu['pendapatan_lain'] ?? 0) !!}</span>
                            </div>
                        </div>
                        <div class="mt-3 pt-2 border-t border-slate-200 dark:border-zinc-700 flex justify-between font-bold text-slate-800 dark:text-white pl-4 pr-2">
                            <span>TOTAL PENDAPATAN</span>
                            <span class="font-mono">{!! $formatRp($totalPendapatan) !!}</span>
                        </div>
                    </div>

                    <!-- Beban -->
                    <div class="mb-6">
                        <h3 class="text-sm font-bold text-slate-700 dark:text-zinc-300 uppercase mb-3 bg-slate-100 dark:bg-zinc-800/80 p-2 rounded">BEBAN OPERASIONAL</h3>
                        <div class="space-y-2 text-sm pl-4 pr-2">
                            <div class="flex justify-between py-1">
                                <span class="text-slate-600 dark:text-zinc-400">Beban Gaji & Tunjangan</span>
                                <span class="font-mono">{!! $formatRp($shu['beban_gaji'] ?? 0) !!}</span>
                            </div>
                            <div class="flex justify-between py-1">
                                <span class="text-slate-600 dark:text-zinc-400">Beban ATK & Cetakan</span>
                                <span class="font-mono">{!! $formatRp($shu['beban_atk'] ?? 0) !!}</span>
                            </div>
                            <div class="flex justify-between py-1">
                                <span class="text-slate-600 dark:text-zinc-400">Beban Listrik, Air, Telepon</span>
                                <span class="font-mono">{!! $formatRp($shu['beban_listrik'] ?? 0) !!}</span>
                            </div>
                            <div class="flex justify-between py-1">
                                <span class="text-slate-600 dark:text-zinc-400">Beban Penyusutan</span>
                                <span class="font-mono">{!! $formatRp($shu['beban_penyusutan'] ?? 0) !!}</span>
                            </div>
                            <div class="flex justify-between py-1">
                                <span class="text-slate-600 dark:text-zinc-400">Beban Lain-lain</span>
                                <span class="font-mono">{!! $formatRp($shu['beban_lain'] ?? 0) !!}</span>
                            </div>
                        </div>
                        <div class="mt-3 pt-2 border-t border-slate-200 dark:border-zinc-700 flex justify-between font-bold text-slate-800 dark:text-white pl-4 pr-2">
                            <span>TOTAL BEBAN OPERASIONAL</span>
                            <span class="font-mono">{!! $formatRp($totalBeban) !!}</span>
                        </div>
                    </div>

                    <!-- SHU Bersih -->
                    <div class="mt-8 flex justify-between p-4 bg-emerald-50 dark:bg-emerald-900/20 border-2 border-emerald-200 dark:border-emerald-800/50 rounded-xl font-bold text-emerald-900 dark:text-emerald-400">
                        <span class="text-lg">SISA HASIL USAHA (SHU) BERSIH</span>
                        <span class="font-mono text-xl">{!! $formatRp($shuBersih) !!}</span>
                    </div>
                </div>
            @endif

            {{-- PERUBAHAN EKUITAS TAB --}}
            @if($activeTab === 'perubahan_ekuitas')
                @php
                    $ekuitas = $reportData['perubahan_ekuitas'] ?? [];
                    // Fallback to empty default rows if no data provided in array
                    $rows = $ekuitas['rows'] ?? [
                        ['uraian' => 'Saldo Awal', 'pokok' => 0, 'wajib' => 0, 'cadangan' => 0, 'shu' => 0, 'total' => 0],
                        ['uraian' => 'Penambahan', 'pokok' => 0, 'wajib' => 0, 'cadangan' => 0, 'shu' => 0, 'total' => 0],
                        ['uraian' => 'Pengurangan', 'pokok' => 0, 'wajib' => 0, 'cadangan' => 0, 'shu' => 0, 'total' => 0],
                        ['uraian' => 'Saldo Akhir', 'pokok' => 0, 'wajib' => 0, 'cadangan' => 0, 'shu' => 0, 'total' => 0],
                    ];
                @endphp
                <div class="p-6 border-b border-slate-100 dark:border-zinc-700 flex justify-between items-center bg-slate-50/50 dark:bg-zinc-800/50">
                    <div>
                        <h2 class="text-lg font-bold text-slate-800 dark:text-white uppercase tracking-wide">Laporan Perubahan Ekuitas</h2>
                        <p class="text-sm text-slate-500 dark:text-zinc-400 mt-1">Untuk Tahun yang Berakhir 31 Desember {{ $selectedYear }}</p>
                    </div>
                </div>

                <div class="p-6 overflow-x-auto">
                    <table class="w-full text-sm text-left whitespace-nowrap">
                        <thead class="text-xs text-slate-600 dark:text-zinc-300 uppercase bg-slate-100 dark:bg-zinc-800">
                            <tr>
                                <th scope="col" class="px-6 py-4 rounded-tl-lg font-bold text-slate-500">Uraian</th>
                                <th scope="col" class="px-4 py-4 text-right font-bold text-slate-500">Simpanan Pokok</th>
                                <th scope="col" class="px-4 py-4 text-right font-bold text-slate-500">Simpanan Wajib</th>
                                <th scope="col" class="px-4 py-4 text-right font-bold text-slate-500">Cadangan</th>
                                <th scope="col" class="px-4 py-4 text-right font-bold text-slate-500">SHU Berjalan</th>
                                <th scope="col" class="px-6 py-4 rounded-tr-lg text-right font-bold text-slate-500">Total Ekuitas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rows as $index => $row)
                                @php
                                    $isLast = $loop->last;
                                @endphp
                                <tr class="border-b dark:border-zinc-700 hover:bg-slate-50 dark:hover:bg-zinc-800/50 transition-colors {{ $isLast ? 'bg-amber-50/30 dark:bg-amber-900/10 font-bold border-t-2 border-slate-300 dark:border-zinc-600' : 'text-slate-600 dark:text-zinc-300' }}">
                                    <td class="px-6 py-4 font-medium text-slate-800 dark:text-white">{{ $row['uraian'] ?? '' }}</td>
                                    <td class="px-4 py-4 font-mono text-right">{!! $formatRp($row['pokok'] ?? 0) !!}</td>
                                    <td class="px-4 py-4 font-mono text-right">{!! $formatRp($row['wajib'] ?? 0) !!}</td>
                                    <td class="px-4 py-4 font-mono text-right">{!! $formatRp($row['cadangan'] ?? 0) !!}</td>
                                    <td class="px-4 py-4 font-mono text-right">{!! $formatRp($row['shu'] ?? 0) !!}</td>
                                    <td class="px-6 py-4 font-mono text-right {{ $isLast ? 'text-indigo-700 dark:text-indigo-400' : '' }}">{!! $formatRp($row['total'] ?? 0) !!}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            {{-- ARUS KAS TAB --}}
            @if($activeTab === 'arus_kas')
                @php
                    $aruskas = $reportData['arus_kas'] ?? [];
                @endphp
                <div class="p-6 border-b border-slate-100 dark:border-zinc-700 flex justify-between items-center bg-slate-50/50 dark:bg-zinc-800/50">
                    <div>
                        <h2 class="text-lg font-bold text-slate-800 dark:text-white uppercase tracking-wide">Laporan Arus Kas</h2>
                        <p class="text-sm text-slate-500 dark:text-zinc-400 mt-1">Untuk Tahun yang Berakhir 31 Desember {{ $selectedYear }}</p>
                    </div>
                </div>

                <div class="p-6 max-w-3xl mx-auto space-y-8">
                    <!-- Operasi -->
                    <div>
                        <h3 class="text-sm font-bold text-slate-700 dark:text-zinc-300 uppercase mb-3 bg-slate-100 dark:bg-zinc-800/80 p-2 rounded">ARUS KAS DARI AKTIVITAS OPERASI</h3>
                        <div class="space-y-2 text-sm pl-4 pr-2">
                            <div class="flex justify-between py-1">
                                <span class="text-slate-600 dark:text-zinc-400">Penerimaan dari Margin/Bagi Hasil</span>
                                <span class="font-mono">{!! $formatRp($aruskas['penerimaan_margin'] ?? 0) !!}</span>
                            </div>
                            <div class="flex justify-between py-1">
                                <span class="text-slate-600 dark:text-zinc-400">Pembayaran Beban Operasional</span>
                                <span class="font-mono">{!! $formatRp(-abs($aruskas['pembayaran_beban'] ?? 0)) !!}</span>
                            </div>
                            <div class="flex justify-between py-1">
                                <span class="text-slate-600 dark:text-zinc-400">Kenaikan/Penurunan Piutang Pembiayaan</span>
                                <span class="font-mono">{!! $formatRp($aruskas['perubahan_piutang'] ?? 0) !!}</span>
                            </div>
                            <div class="flex justify-between py-1">
                                <span class="text-slate-600 dark:text-zinc-400">Kenaikan/Penurunan Simpanan Wadiah</span>
                                <span class="font-mono">{!! $formatRp($aruskas['perubahan_simpanan'] ?? 0) !!}</span>
                            </div>
                        </div>
                        <div class="mt-3 pt-2 border-t border-slate-200 dark:border-zinc-700 flex justify-between font-bold text-slate-800 dark:text-white pl-4 pr-2">
                            <span>Kas Bersih dari Aktivitas Operasi</span>
                            <span class="font-mono">{!! $formatRp($aruskas['total_operasi'] ?? 0) !!}</span>
                        </div>
                    </div>

                    <!-- Investasi -->
                    <div>
                        <h3 class="text-sm font-bold text-slate-700 dark:text-zinc-300 uppercase mb-3 bg-slate-100 dark:bg-zinc-800/80 p-2 rounded">ARUS KAS DARI AKTIVITAS INVESTASI</h3>
                        <div class="space-y-2 text-sm pl-4 pr-2">
                            <div class="flex justify-between py-1">
                                <span class="text-slate-600 dark:text-zinc-400">Perolehan Aset Tetap</span>
                                <span class="font-mono">{!! $formatRp(-abs($aruskas['perolehan_aset'] ?? 0)) !!}</span>
                            </div>
                            <div class="flex justify-between py-1">
                                <span class="text-slate-600 dark:text-zinc-400">Penerimaan Penjualan Aset</span>
                                <span class="font-mono">{!! $formatRp($aruskas['penjualan_aset'] ?? 0) !!}</span>
                            </div>
                        </div>
                        <div class="mt-3 pt-2 border-t border-slate-200 dark:border-zinc-700 flex justify-between font-bold text-slate-800 dark:text-white pl-4 pr-2">
                            <span>Kas Bersih dari Aktivitas Investasi</span>
                            <span class="font-mono">{!! $formatRp($aruskas['total_investasi'] ?? 0) !!}</span>
                        </div>
                    </div>

                    <!-- Pendanaan -->
                    <div>
                        <h3 class="text-sm font-bold text-slate-700 dark:text-zinc-300 uppercase mb-3 bg-slate-100 dark:bg-zinc-800/80 p-2 rounded">ARUS KAS DARI AKTIVITAS PENDANAAN</h3>
                        <div class="space-y-2 text-sm pl-4 pr-2">
                            <div class="flex justify-between py-1">
                                <span class="text-slate-600 dark:text-zinc-400">Penerimaan Simpanan Pokok & Wajib</span>
                                <span class="font-mono">{!! $formatRp($aruskas['penerimaan_modal'] ?? 0) !!}</span>
                            </div>
                            <div class="flex justify-between py-1">
                                <span class="text-slate-600 dark:text-zinc-400">Pembagian SHU</span>
                                <span class="font-mono">{!! $formatRp(-abs($aruskas['pembagian_shu'] ?? 0)) !!}</span>
                            </div>
                        </div>
                        <div class="mt-3 pt-2 border-t border-slate-200 dark:border-zinc-700 flex justify-between font-bold text-slate-800 dark:text-white pl-4 pr-2">
                            <span>Kas Bersih dari Aktivitas Pendanaan</span>
                            <span class="font-mono">{!! $formatRp($aruskas['total_pendanaan'] ?? 0) !!}</span>
                        </div>
                    </div>

                    <!-- Rekap -->
                    <div class="mt-8 bg-slate-50 dark:bg-zinc-800/80 rounded-xl p-4 border border-slate-200 dark:border-zinc-700 space-y-3">
                        <div class="flex justify-between font-medium text-slate-700 dark:text-zinc-300">
                            <span>KENAIKAN / (PENURUNAN) KAS BERSIH</span>
                            <span class="font-mono">{!! $formatRp($aruskas['perubahan_kas'] ?? 0) !!}</span>
                        </div>
                        <div class="flex justify-between font-medium text-slate-700 dark:text-zinc-300">
                            <span>KAS AWAL TAHUN</span>
                            <span class="font-mono">{!! $formatRp($aruskas['kas_awal'] ?? 0) !!}</span>
                        </div>
                        <div class="flex justify-between pt-3 border-t-2 border-slate-300 dark:border-zinc-600 font-bold text-amber-900 dark:text-amber-400 text-lg">
                            <span>KAS AKHIR TAHUN</span>
                            <span class="font-mono">{!! $formatRp($aruskas['kas_akhir'] ?? 0) !!}</span>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    @endif
</div>
