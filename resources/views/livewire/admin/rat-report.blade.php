<div class="px-4 py-6 sm:px-6 lg:px-8 max-w-7xl mx-auto">
    <!-- Header Section -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900 dark:text-gray-100">Laporan Rapat Anggota Tahunan (RAT) {{ $currentYear }}</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Ringkasan keuangan dan pencapaian koperasi tahun berjalan.</p>
        </div>
        <div>
            <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 print:hidden">
                <i class='bx bx-printer'></i>
                Cetak Laporan
            </button>
        </div>
    </div>

    <!-- Metric Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        
        <!-- SIMPANAN -->
        <div class="bg-white dark:bg-gray-800 rounded shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700 font-semibold text-gray-800 dark:text-gray-200 flex items-center gap-2">
                <i class='bx bx-wallet text-gray-500'></i> Dana Simpanan
            </div>
            <div class="p-4 space-y-3">
                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-600 dark:text-gray-400">Simpanan Pokok</span>
                    <span class="font-medium text-gray-800 dark:text-gray-200">Rp {{ number_format($simpanan['pokok'], 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-600 dark:text-gray-400">Simpanan Wajib</span>
                    <span class="font-medium text-gray-800 dark:text-gray-200">Rp {{ number_format($simpanan['wajib'], 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-600 dark:text-gray-400">Simpanan Sukarela</span>
                    <span class="font-medium text-gray-800 dark:text-gray-200">Rp {{ number_format($simpanan['sukarela'], 0, ',', '.') }}</span>
                </div>
                <div class="border-t border-gray-200 dark:border-gray-700 pt-2 mt-2 flex justify-between items-center">
                    <span class="font-semibold text-gray-800 dark:text-gray-200">Total</span>
                    <span class="font-bold text-gray-900 dark:text-gray-100">Rp {{ number_format($simpanan['total'], 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- PINJAMAN -->
        <div class="bg-white dark:bg-gray-800 rounded shadow-sm border border-gray-200 dark:border-gray-700">
             <div class="p-4 border-b border-gray-200 dark:border-gray-700 font-semibold text-gray-800 dark:text-gray-200 flex items-center gap-2">
                <i class='bx bx-credit-card text-gray-500'></i> Penyaluran Pinjaman
            </div>
            <div class="p-4 space-y-3">
                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-600 dark:text-gray-400">Total Tersalurkan</span>
                    <span class="font-medium text-gray-800 dark:text-gray-200">Rp {{ number_format($pinjaman['tersalurkan'], 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-600 dark:text-gray-400">Kolektibilitas Lancar</span>
                    <span class="font-medium text-gray-800 dark:text-gray-200">{{ $pinjaman['lancar_count'] }} Anggota</span>
                </div>
                <div class="flex justify-between items-center text-sm">
                    <span class="text-red-600 dark:text-red-400">Risiko Macet / NPL</span>
                    <span class="font-medium text-red-600 dark:text-red-400">{{ $pinjaman['macet_count'] }} Anggota</span>
                </div>
                <div class="border-t border-gray-200 dark:border-gray-700 pt-2 mt-2 flex justify-between items-center">
                    <span class="font-semibold text-gray-800 dark:text-gray-200">Rasio NPL</span>
                    <span class="font-bold text-gray-900 dark:text-gray-100">{{ $pinjaman['npl_ratio'] }}%</span>
                </div>
            </div>
        </div>

        <!-- PAYROLL -->
        <div class="bg-white dark:bg-gray-800 rounded shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700 font-semibold text-gray-800 dark:text-gray-200 flex items-center gap-2">
                <i class='bx bx-check-shield text-gray-500'></i> Potensi Auto-Debet
            </div>
            <div class="p-4 space-y-3">
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Penghimpunan bulanan dari potong gaji.</p>
                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-600 dark:text-gray-400">Simpanan Wajib</span>
                    <span class="font-medium text-gray-800 dark:text-gray-200">Rp {{ number_format($payroll_est['simwa'], 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-600 dark:text-gray-400">Simpanan Sukarela</span>
                    <span class="font-medium text-gray-800 dark:text-gray-200">Rp {{ number_format($payroll_est['sukarela'], 0, ',', '.') }}</span>
                </div>
                <div class="border-t border-gray-200 dark:border-gray-700 pt-2 mt-2 flex justify-between items-center">
                    <span class="font-semibold text-gray-800 dark:text-gray-200">Estimasi / Bulan</span>
                    <span class="font-bold text-gray-900 dark:text-gray-100">Rp {{ number_format($payroll_est['total'], 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Report Table -->
    <div class="bg-white dark:bg-gray-800 rounded shadow-sm border border-gray-200 dark:border-gray-700 mb-6 overflow-hidden">
        <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
            <h3 class="font-semibold text-gray-800 dark:text-gray-200">Laporan Bulanan SIMPAN PINJAM (Tahun {{ $currentYear }})</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left align-middle">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Bulan</th>
                        <th class="px-4 py-3 font-semibold text-right">Setoran Simpanan</th>
                        <th class="px-4 py-3 font-semibold text-right">Penarikan Simpanan</th>
                        <th class="px-4 py-3 font-semibold text-right">Penyaluran Pinjaman</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @php 
                        $totalSetoran = 0; 
                        $totalPenarikan = 0; 
                        $totalPinjaman = 0; 
                    @endphp
                    @foreach ($monthlyData as $data)
                    @php 
                        $totalSetoran += $data['setoran']; 
                        $totalPenarikan += $data['penarikan']; 
                        $totalPinjaman += $data['pinjaman']; 
                    @endphp
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $data['month_name'] }}</td>
                        <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">Rp {{ number_format($data['setoran'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">Rp {{ number_format($data['penarikan'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">Rp {{ number_format($data['pinjaman'], 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 dark:bg-gray-700/50 font-semibold text-gray-900 dark:text-white border-t-2 border-gray-300 dark:border-gray-600">
                    <tr>
                        <td class="px-4 py-3">TOTAL</td>
                        <td class="px-4 py-3 text-right">Rp {{ number_format($totalSetoran, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right">Rp {{ number_format($totalPenarikan, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right">Rp {{ number_format($totalPinjaman, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- DRAF TEXT SECTION -->
    <div class="bg-white dark:bg-gray-800 rounded shadow-sm border border-gray-200 dark:border-gray-700 print:hidden mt-6">
        <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
            <h3 class="font-semibold text-gray-800 dark:text-gray-200">Format Salinan Notulensi</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Draf ini dapat langsung disalin ke dalam dokumen Word laporan cetak Anda.</p>
        </div>
        <div class="p-4">
            <textarea readonly rows="12" class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded p-3 font-mono text-sm text-gray-800 dark:text-gray-200 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
LAPORAN EVALUASI SIMPANAN & PINJAMAN
Rapat Anggota Tahunan (RAT) Tahun {{ $currentYear }}
Koperasi Karyawan UMB Bermadani

--- DATA SIMPANAN ---
Total Pendanaan bersumber dari modal simpanan tercatat sebesar Rp {{ number_format($simpanan['total'], 0, ',', '.') }}, dengan rincian:
- Simpanan Pokok: Rp {{ number_format($simpanan['pokok'], 0, ',', '.') }}
- Simpanan Wajib: Rp {{ number_format($simpanan['wajib'], 0, ',', '.') }}
- Simpanan Sukarela: Rp {{ number_format($simpanan['sukarela'], 0, ',', '.') }}

Sepanjang tahun berjalan ({{ $currentYear }}), total dana simpanan yang masuk sebesar Rp {{ number_format($totalSetoran, 0, ',', '.') }} dan total penarikan sebesar Rp {{ number_format($totalPenarikan, 0, ',', '.') }}.

--- DATA PINJAMAN & KOLEKTIBILITAS ---
Koperasi telah menyalurkan pinjaman dengan akumulasi sebesar Rp {{ number_format($pinjaman['tersalurkan'], 0, ',', '.') }} dan pada tahun berjalan tersalurkan sebesar Rp {{ number_format($totalPinjaman, 0, ',', '.') }}.
Status pembiayaan saat ini:
- Kol. Lancar: {{ $pinjaman['lancar_count'] }} Anggota (Sisa Nilai Pokok: Rp {{ number_format($pinjaman['lancar_rp'], 0, ',', '.') }})
- Kredit Bermasalah (NPL): {{ $pinjaman['macet_count'] }} Anggota (Sisa Nilai Pokok: Rp {{ number_format($pinjaman['macet_rp'], 0, ',', '.') }})
Rasio NPL tercatat di angka {{ $pinjaman['npl_ratio'] }}%.

--- PEMASUKAN RUTIN BULANAN ---
Melalui program potong gaji bulanan, koperasi menghimpun estimasi penerimaan rutin sebesar Rp {{ number_format($payroll_est['total'], 0, ',', '.') }}, berdasarkan:
- Simpanan Wajib: Rp {{ number_format($payroll_est['simwa'], 0, ',', '.') }}
- Simpanan Sukarela: Rp {{ number_format($payroll_est['sukarela'], 0, ',', '.') }}
            </textarea>
        </div>
    </div>
</div>
