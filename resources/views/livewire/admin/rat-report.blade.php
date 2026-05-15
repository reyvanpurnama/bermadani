<div class="px-4 py-6 sm:px-6 lg:px-8 max-w-7xl mx-auto">
    <!-- Header Section -->
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">Laporan Evaluasi RAT</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Rangkuman komprehensif metrik keuangan untuk Rapat Anggota Tahunan.</p>
        </div>
        <div>
            <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-slate-900">
                <i class='bx bx-printer text-lg'></i>
                Cetak / Simpan PDF
            </button>
        </div>
    </div>

    <!-- Metric Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        
        <!-- SIMPANAN -->
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700/60 overflow-hidden relative group">
            <div class="absolute top-0 left-0 w-full h-1 bg-emerald-500"></div>
            <div class="p-6">
                <div class="flex items-center gap-3 mb-5">
                    <div class="p-2 bg-emerald-50 dark:bg-emerald-500/10 rounded-lg">
                        <i class='bx bx-wallet text-emerald-600 dark:text-emerald-400 text-xl'></i>
                    </div>
                    <h2 class="text-base font-semibold text-slate-800 dark:text-slate-100">Posisi Dana Simpanan</h2>
                </div>
                
                <div class="space-y-3">
                    <div class="flex justify-between items-center text-sm border-b border-slate-100 dark:border-slate-700/50 pb-2">
                        <span class="text-slate-500 dark:text-slate-400">Simpanan Pokok</span>
                        <span class="font-medium text-slate-700 dark:text-slate-300">Rp {{ number_format($simpanan['pokok'], 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm border-b border-slate-100 dark:border-slate-700/50 pb-2">
                        <span class="text-slate-500 dark:text-slate-400">Simpanan Wajib</span>
                        <span class="font-medium text-slate-700 dark:text-slate-300">Rp {{ number_format($simpanan['wajib'], 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm border-b border-slate-100 dark:border-slate-700/50 pb-2">
                        <span class="text-slate-500 dark:text-slate-400">Simpanan Sukarela</span>
                        <span class="font-medium text-slate-700 dark:text-slate-300">Rp {{ number_format($simpanan['sukarela'], 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center pt-3">
                        <span class="text-sm font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total</span>
                        <span class="text-lg font-bold text-emerald-600 dark:text-emerald-400">Rp {{ number_format($simpanan['total'], 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- PINJAMAN -->
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700/60 overflow-hidden relative group">
             <div class="absolute top-0 left-0 w-full h-1 bg-blue-500"></div>
             <div class="p-6">
                <div class="flex items-center gap-3 mb-5">
                    <div class="p-2 bg-blue-50 dark:bg-blue-500/10 rounded-lg">
                        <i class='bx bx-credit-card text-blue-600 dark:text-blue-400 text-xl'></i>
                    </div>
                    <h2 class="text-base font-semibold text-slate-800 dark:text-slate-100">Penyaluran Pinjaman</h2>
                </div>

                <div class="space-y-4">
                    <div class="bg-slate-50 dark:bg-slate-900/50 rounded-lg p-3 border border-slate-100 dark:border-slate-700/50 text-center">
                        <span class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Total Tersalurkan</span>
                        <span class="block text-xl font-bold text-slate-800 dark:text-slate-100">Rp {{ number_format($pinjaman['tersalurkan'], 0, ',', '.') }}</span>
                    </div>

                    <div class="space-y-2">
                        <div class="flex justify-between items-end border-b border-slate-100 dark:border-slate-700/50 pb-2">
                            <div>
                                <span class="block text-sm font-medium text-emerald-600 dark:text-emerald-400">Kolektibilitas Lancar</span>
                                <span class="block text-xs text-slate-500 dark:text-slate-400">{{ $pinjaman['lancar_count'] }} Anggota</span>
                            </div>
                            <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Sisa: Rp {{ number_format($pinjaman['lancar_rp'], 0, ',', '.') }}</span>
                        </div>
                        
                        <div class="flex justify-between items-end pb-2">
                            <div>
                                <span class="block text-sm font-medium text-rose-600 dark:text-rose-400">Risiko (NPL {{ $pinjaman['npl_ratio'] }}%)</span>
                                <span class="block text-xs text-rose-500/80">{{ $pinjaman['macet_count'] }} Anggota</span>
                            </div>
                            <span class="text-sm font-medium text-rose-600 dark:text-rose-400">Sisa: Rp {{ number_format($pinjaman['macet_rp'], 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- PAYROLL -->
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700/60 overflow-hidden relative group">
            <div class="absolute top-0 left-0 w-full h-1 bg-purple-500"></div>
            <div class="p-6">
                <div class="flex items-center gap-3 mb-5">
                    <div class="p-2 bg-purple-50 dark:bg-purple-500/10 rounded-lg">
                        <i class='bx bx-money text-purple-600 dark:text-purple-400 text-xl'></i>
                    </div>
                    <h2 class="text-base font-semibold text-slate-800 dark:text-slate-100">Proyeksi Auto-Debet</h2>
                </div>

                <p class="text-xs text-slate-500 dark:text-slate-400 mb-4 leading-relaxed">
                    Estimasi himpunan dana dari sistem potong gaji (SimPANAN) per bulan (di luar angsuran pinjaman).
                </p>

                <div class="space-y-3">
                    <div class="flex justify-between items-center text-sm border-b border-slate-100 dark:border-slate-700/50 pb-2">
                        <span class="text-slate-500 dark:text-slate-400">Simpanan Wajib</span>
                        <span class="font-medium text-slate-700 dark:text-slate-300">Rp {{ number_format($payroll_est['simwa'], 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm border-b border-slate-100 dark:border-slate-700/50 pb-2">
                        <span class="text-slate-500 dark:text-slate-400">Simpanan Sukarela</span>
                        <span class="font-medium text-slate-700 dark:text-slate-300">Rp {{ number_format($payroll_est['sukarela'], 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center pt-3">
                        <span class="text-sm font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Potensi Bulanan</span>
                        <span class="text-lg font-bold text-purple-600 dark:text-purple-400">Rp {{ number_format($payroll_est['total'], 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- DRAF TEXT SECTION -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700/60 overflow-hidden print:hidden mt-8">
        <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700/60 flex items-center gap-3 bg-slate-50/50 dark:bg-slate-800/50">
            <i class='bx bx-copy-alt text-slate-400 text-xl'></i>
            <div>
                <h3 class="font-semibold text-slate-800 dark:text-slate-200">Draf Notulensi Laporan</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400">Salin teks di bawah ini ke dalam dokumen MS Office Word atau Notulensi RAT Anda.</p>
            </div>
        </div>
        <div class="p-6 bg-slate-50 dark:bg-[#0f172a]">
            <textarea readonly rows="12" class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700/80 rounded-lg p-4 font-mono text-sm text-slate-700 dark:text-slate-300 shadow-inner focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none resize-none selection:bg-indigo-300 dark:selection:bg-indigo-900">
LAPORAN EVALUASI SIMPANAN & PINJAMAN
Rapat Anggota Tahunan (RAT)
Koperasi Karyawan UMB Bermadani

--- DATA SIMPANAN ---
Total Pendanaan bersumber dari modal simpanan tercatat sebesar Rp {{ number_format($simpanan['total'], 0, ',', '.') }}, dengan rincian:
- Simpanan Pokok: Rp {{ number_format($simpanan['pokok'], 0, ',', '.') }}
- Simpanan Wajib: Rp {{ number_format($simpanan['wajib'], 0, ',', '.') }}
- Simpanan Sukarela: Rp {{ number_format($simpanan['sukarela'], 0, ',', '.') }}

--- DATA PINJAMAN & KOLEKTIBILITAS ---
Koperasi telah menyalurkan pinjaman dengan akumulasi sebesar Rp {{ number_format($pinjaman['tersalurkan'], 0, ',', '.') }}.
Status pembiayaan saat ini:
- Kol. Lancar: {{ $pinjaman['lancar_count'] }} Anggota (Sisa Nilai Pokok: Rp {{ number_format($pinjaman['lancar_rp'], 0, ',', '.') }})
- Kredit Bermasalah (NPL): {{ $pinjaman['macet_count'] }} Anggota (Sisa Nilai Pokok: Rp {{ number_format($pinjaman['macet_rp'], 0, ',', '.') }})
Rasio NPL tercatat di angka {{ $pinjaman['npl_ratio'] }}%.

--- KEPATUHAN & POTONGAN GAJI ---
Dari sistem auto-debet (potongan gaji) setiap bulannya, koperasi secara rutin menghimpun dana sebesar Rp {{ number_format($payroll_est['total'], 0, ',', '.') }}, yang berasal dari Simpanan Wajib (Rp {{ number_format($payroll_est['simwa'], 0, ',', '.') }}) dan Simpanan Sukarela (Rp {{ number_format($payroll_est['sukarela'], 0, ',', '.') }}).
            </textarea>
        </div>
    </div>
</div>
