<div>
<style>
    @media print {
        #sidebar-container, header, .no-print, #theme-toggle, #sidebar-toggle { display: none !important; }
        body, #main-content { margin: 0; padding: 0; width: 100%; background: white; color: black; overflow: visible !important; }
        .md\:ml-\[180px\] { margin-left: 0 !important; }
        #receipt-card { box-shadow: none; border: 2px solid #000; width: 100%; max-width: 100%; margin: 0; page-break-inside: avoid; }
        * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
    }
    .receipt-pattern {
        background-image: radial-gradient(#4F46E5 0.5px, transparent 0.5px);
        background-size: 10px 10px;
        opacity: 0.05;
    }
</style>

<div class="flex flex-col items-center">
    
    {{-- Action Buttons (No Print) --}}
    <div class="w-full max-w-2xl mb-6 flex flex-col sm:flex-row justify-between items-center gap-4 no-print">
        <div class="flex items-center gap-2">
            <span class="flex h-3 w-3 relative">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
            </span>
            <span class="text-sm font-medium text-emerald-600 dark:text-emerald-400">Pembayaran Berhasil Dicatat</span>
        </div>
        <div class="flex gap-2 w-full sm:w-auto">
            <button onclick="window.print()" class="flex-1 sm:flex-none flex items-center justify-center gap-2 bg-white dark:bg-darkCard border border-slate-200 dark:border-slate-600 text-slate-700 dark:text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-slate-50 transition-colors shadow-sm">
                <i class='bx bx-printer'></i> Print
            </button>
            <a href="{{ route('admin.members.simpanan', $member->id) }}" class="flex-1 sm:flex-none flex items-center justify-center gap-2 bg-primary hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-bold shadow-md shadow-indigo-500/20 transition-colors">
                <i class='bx bx-plus'></i> Input Lagi
            </a>
        </div>
    </div>

    {{-- Receipt Card --}}
    <div id="receipt-card" class="w-full max-w-2xl bg-white dark:bg-darkCard rounded-xl shadow-xl border border-slate-200 dark:border-slate-700 overflow-hidden relative">
        
        <div class="h-2 w-full bg-gradient-to-r from-primary to-indigo-400"></div>
        <div class="receipt-pattern absolute inset-0 pointer-events-none"></div>

        <div class="p-8 md:p-10 relative z-10">
            
            {{-- Header --}}
            <div class="flex justify-between items-start mb-8 pb-8 border-b border-dashed border-slate-200 dark:border-slate-600">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-indigo-50 dark:bg-indigo-900/30 rounded-lg flex items-center justify-center text-primary dark:text-indigo-400">
                        <i class='bx bxs-cube-alt text-2xl'></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-slate-900 dark:text-white leading-none">KOPERASI BERMADANI</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Universitas Mercu Buana<br>Telp: (021) 5840-816</p>
                    </div>
                </div>
                <div class="text-right">
                    <h3 class="text-lg font-bold text-slate-400/50 dark:text-slate-600 uppercase tracking-widest">KUITANSI</h3>
                    <p class="text-sm font-mono font-bold text-slate-700 dark:text-slate-300 mt-1">#{{ $payment->receiptNumber }}</p>
                    <p class="text-xs text-slate-500 mt-1">{{ $payment->created_at->translatedFormat('d M Y • H:i') }} WIB</p>
                </div>
            </div>

            {{-- Member & Payment Info --}}
            <div class="flex flex-col sm:flex-row justify-between mb-8 gap-4">
                <div>
                    <p class="text-[10px] uppercase font-bold text-slate-400 tracking-wider mb-1">Diterima Dari</p>
                    <h4 class="text-lg font-bold text-slate-800 dark:text-white">{{ $member->name }}</h4>
                    <p class="text-sm text-slate-500">{{ $member->nomorAnggota }} <span class="mx-1">•</span> {{ $member->unitKerja === 'unknown' ? 'Belum Diisi' : $member->unitKerja }}</p>
                </div>
                <div class="text-left sm:text-right">
                    <p class="text-[10px] uppercase font-bold text-slate-400 tracking-wider mb-1">Metode Pembayaran</p>
                    <div class="flex items-center sm:justify-end gap-2">
                        <span class="bg-indigo-50 dark:bg-indigo-900/20 text-primary dark:text-indigo-300 text-xs font-bold px-2 py-1 rounded border border-indigo-100 dark:border-indigo-800">
                            @if($payment->paymentMethod === 'CASH')
                                <i class='bx bx-money'></i> Tunai
                            @elseif($payment->paymentMethod === 'TRANSFER')
                                <i class='bx bx-transfer'></i> Transfer Bank
                            @else
                                <i class='bx bx-credit-card'></i> Auto Debit
                            @endif
                        </span>
                    </div>
                    @if($payment->referenceNumber)
                        <p class="text-xs text-slate-400 mt-1">Ref: {{ $payment->referenceNumber }}</p>
                    @endif
                </div>
            </div>

            {{-- Payment Items Table --}}
            <div class="mb-8">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 dark:text-slate-400 border-y border-slate-100 dark:border-slate-700">
                        <tr>
                            <th class="py-3 text-left pl-4 rounded-l-lg">Keterangan</th>
                            <th class="py-3 text-right pr-4 rounded-r-lg">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody class="text-slate-700 dark:text-slate-300 divide-y divide-slate-100 dark:divide-slate-700/50">
                        @foreach($relatedPayments as $pay)
                            <tr>
                                <td class="py-4 pl-4">
                                    <p class="font-bold">{{ $pay->bill->typeLabel }}</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-500">
                                        Tagihan Bulan {{ \Carbon\Carbon::createFromFormat('Y-m', $pay->bill->billingMonth)->translatedFormat('F Y') }}
                                    </p>
                                </td>
                                <td class="py-4 pr-4 text-right font-mono">Rp {{ number_format($pay->amount, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Total Summary --}}
            <div class="bg-slate-50 dark:bg-slate-800/50 rounded-xl p-4 border border-slate-100 dark:border-slate-700 mb-8 flex flex-col sm:flex-row justify-between items-center gap-4">
                <div class="text-center sm:text-left">
                    <p class="text-[10px] text-slate-400 uppercase tracking-wider font-bold mb-1">Terbilang</p>
                    <p class="text-sm font-medium italic text-slate-600 dark:text-slate-300">"{{ $totalInWords }} Rupiah"</p>
                </div>
                <div class="text-center sm:text-right">
                    <p class="text-[10px] text-slate-400 uppercase tracking-wider font-bold mb-1">Total Bayar</p>
                    <p class="text-2xl font-black text-primary dark:text-indigo-400">Rp {{ number_format($grandTotal, 0, ',', '.') }}</p>
                </div>
            </div>

            {{-- Footer with QR & Signature --}}
            <div class="flex justify-between items-end mt-12 pt-6">
                <div class="hidden sm:block">
                    <div class="w-16 h-16 bg-white border border-slate-200 p-1">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ $payment->receiptNumber }}" 
                             class="w-full h-full opacity-80" 
                             alt="QR Code">
                    </div>
                    <p class="text-[10px] text-slate-400 mt-2">Validasi Scan QR</p>
                </div>
                
                <div class="text-center">
                    <p class="text-xs font-bold text-slate-500 dark:text-slate-400 mb-16">Petugas / Teller</p>
                    <div class="h-px w-40 bg-slate-300 dark:bg-slate-600 mb-2"></div>
                    <p class="text-sm font-bold text-slate-800 dark:text-white">{{ $processor->name }}</p>
                </div>
            </div>

        </div>
    </div>

    {{-- Footer --}}
    <div class="mt-8 text-center text-xs text-slate-400 no-print">
        &copy; {{ date('Y') }} Koperasi Bermadani. Generated automatically.
    </div>

</div>
</div>
