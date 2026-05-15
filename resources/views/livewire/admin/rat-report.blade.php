<div>
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Laporan Evaluasi RAT</h1>
            <p class="text-sm text-gray-600">Rangkuman Kesehatan Keuangan, Simpanan, Pinjaman & Potongan Gaji</p>
        </div>
        <div>
            <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Cetak / Simpan PDF
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- SIMPANAN CARD -->
        <div class="bg-white rounded-lg shadow p-6 border-t-4 border-green-500">
            <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Dana Simpanan
            </h2>
            <div class="space-y-3">
                <div class="flex justify-between items-center text-sm border-b pb-1">
                    <span class="text-gray-600">Simpanan Pokok</span>
                    <span class="font-medium text-gray-800">Rp {{ number_format($simpanan['pokok'], 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center text-sm border-b pb-1">
                    <span class="text-gray-600">Simpanan Wajib</span>
                    <span class="font-medium text-gray-800">Rp {{ number_format($simpanan['wajib'], 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center text-sm border-b pb-1">
                    <span class="text-gray-600">Simpanan Sukarela</span>
                    <span class="font-medium text-gray-800">Rp {{ number_format($simpanan['sukarela'], 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center text-lg mt-4 pt-2 font-bold !border-t-2">
                    <span class="text-gray-800">Total</span>
                    <span class="text-green-600">Rp {{ number_format($simpanan['total'], 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- PINJAMAN CARD -->
        <div class="bg-white rounded-lg shadow p-6 border-t-4 border-blue-500">
            <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                </svg>
                Penyaluran Pinjaman
            </h2>
            <div class="space-y-3">
                <div class="flex flex-col text-sm border-b pb-2">
                    <span class="text-gray-600 text-xs uppercase font-bold tracking-wider mb-1">Total Disalurkan</span>
                    <span class="font-bold text-gray-800 text-lg">Rp {{ number_format($pinjaman['tersalurkan'], 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center text-sm border-b pb-1 mt-2">
                    <span class="text-emerald-600 font-medium">Kolektibilitas Lancar</span>
                    <div class="text-right">
                        <span class="font-bold block">{{ $pinjaman['lancar_count'] }} Anggota</span>
                        <span class="text-xs text-gray-500">Sisa: Rp {{ number_format($pinjaman['lancar_rp'], 0, ',', '.') }}</span>
                    </div>
                </div>
                <div class="flex justify-between items-center text-sm pb-1">
                    <span class="text-red-600 font-medium">Bermasalah / NPL ({{ $pinjaman['npl_ratio'] }}%)</span>
                    <div class="text-right">
                        <span class="font-bold block text-red-600">{{ $pinjaman['macet_count'] }} Anggota</span>
                        <span class="text-xs text-red-500">Sisa: Rp {{ number_format($pinjaman['macet_rp'], 0, ',', '.') }}</span>
                    </div>
                </div>
                
                @if($pinjaman['npl_ratio'] > 5)
                <div class="bg-red-50 text-red-700 p-2 rounded text-xs mt-2 border border-red-200">
                    <strong>Peringatan RAT:</strong> Rasio NPL di atas 5%. Evaluasi kemampuan bayar anggota!
                </div>
                @else
                <div class="bg-emerald-50 text-emerald-700 p-2 rounded text-xs mt-2 border border-emerald-200">
                    <strong>Status RAT:</strong> Rasio NPL sehat (di bawah 5%).
                </div>
                @endif
            </div>
        </div>

        <!-- PAYROLL CARD -->
        <div class="bg-white rounded-lg shadow p-6 border-t-4 border-purple-500">
            <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 11-1 0 .5.5 0 011 0zm5 5a.5.5 0 11-1 0 .5.5 0 011 0z" />
                </svg>
                Proyeksi Potong Gaji (Bulan)
            </h2>
            <div class="space-y-3 pb-2 border-b">
                <p class="text-xs text-gray-500 mb-3" style="line-height: 1.4;">
                    Data ini menunjukkan nilai akumulasi potongan gaji anggota per bulan untuk setoran simpanan, di luar angsuran pinjaman.
                </p>
                <div class="flex justify-between items-center text-sm border-b pb-1">
                    <span class="text-gray-600">Simwa (Tagihan Otomatis)</span>
                    <span class="font-medium text-gray-800">Rp {{ number_format($payroll_est['simwa'], 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center text-sm border-b pb-1">
                    <span class="text-gray-600">Sukarela (Potong Gaji)</span>
                    <span class="font-medium text-gray-800">Rp {{ number_format($payroll_est['sukarela'], 0, ',', '.') }}</span>
                </div>
            </div>
            
            <div class="flex justify-between items-center text-lg mt-4 font-bold text-purple-700">
                <span>Total / Bulan</span>
                <span>Rp {{ number_format($payroll_est['total'], 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    <!-- DRAF TEKS COPY PASTE RAT -->
    <div class="bg-gray-50 rounded-lg p-6 border print:hidden mt-8">
        <h3 class="font-bold text-gray-800 mb-2 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
            </svg>
            Draf Narasi Laporan RAT (Salin ke MS Word)
        </h3>
        <p class="text-sm text-gray-600 mb-4">Blok teks di bawah ini lalu salin-tempel (copy-paste) ke Microsoft Word Anda untuk dilampirkan pada materi Rapat Anggota Tahunan.</p>
        
        <div class="bg-white p-4 border rounded shadow-sm text-sm font-mono whitespace-pre-wrap text-gray-700 selection:bg-blue-200">
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
- Lancar: {{ $pinjaman['lancar_count'] }} Anggota (Sisa Nilai Pokok: Rp {{ number_format($pinjaman['lancar_rp'], 0, ',', '.') }})
- Kredit Bermasalah (NPL): {{ $pinjaman['macet_count'] }} Anggota (Sisa Nilai Pokok: Rp {{ number_format($pinjaman['macet_rp'], 0, ',', '.') }})
Rasio NPL tercatat di angka {{ $pinjaman['npl_ratio'] }}%.

--- KEPATUHAN & POTONGAN GAJI ---
Dari sistem auto-debet (potongan gaji) setiap bulannya, koperasi secara rutin menghimpun dana sebesar Rp {{ number_format($payroll_est['total'], 0, ',', '.') }}, yang berasal dari Simpanan Wajib (Rp {{ number_format($payroll_est['simwa'], 0, ',', '.') }}) dan Simpanan Sukarela (Rp {{ number_format($payroll_est['sukarela'], 0, ',', '.') }}).
        </div>
    </div>
</div>
