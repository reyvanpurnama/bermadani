<!-- 2. EQUIPMENT & PRODUCT SOURCING FOR SUPPLIERS (Pattern 2 from Reference) -->
<section id="supplier" class="py-16 sm:py-24 bg-[#f5f5f7] relative overflow-hidden border-t border-zinc-200/80">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        
        <div class="mb-10 sm:mb-14 flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <span class="inline-flex items-center gap-1.5 text-[10px] font-extrabold uppercase tracking-wider text-amber-700 bg-amber-50 px-3 py-1 rounded-full mb-3">
                    <i class='bx bx-store-alt text-xs'></i> KEMITRAAN SUPPLIER UMKM
                </span>
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-apple-headline text-zinc-900 tracking-tight">
                    Alur Titip Jual Gerai Kampus.
                </h2>
            </div>
            <div class="space-y-2 max-w-md">
                <p class="text-xs sm:text-sm text-zinc-600 font-medium leading-relaxed">
                    Wadah wirausaha bagi mahasiswa, dosen, dan UMKM lokal untuk menjangkau ribuan pembeli di kampus UMBandung.
                </p>
                <a href="{{ route('supplier.register') }}" class="inline-flex items-center gap-2 text-xs font-bold text-[#155A6B] hover:underline">
                    <span>Daftar Jadi Supplier Sekarang</span>
                    <i class='bx bx-right-arrow-alt text-base'></i>
                </a>
            </div>
        </div>

        <!-- 3 Column Vertical Step Cards Layout (Pattern 2 Layout) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <!-- Card 1: Step 01 -->
            <div class="rounded-3xl bg-white p-6 sm:p-7 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col justify-between space-y-6 group">
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-amber-700 bg-amber-50 px-3 py-1 rounded-full">
                            LANGKAH 01
                        </span>
                        <span class="text-[10px] font-bold text-zinc-400">Verifikasi SKU</span>
                    </div>
                    
                    <div class="space-y-1.5">
                        <h3 class="text-xl font-bold text-zinc-900 tracking-tight">Pendaftaran SKU Digital</h3>
                        <p class="text-xs text-zinc-600 font-medium leading-relaxed">
                            Buat akun supplier online, upload detail & foto produk, dan tentukan harga jual konsinyasi dengan mudah.
                        </p>
                    </div>
                </div>

                <!-- Pure CSS Micro-UI Mockup SKU Registration Card -->
                <div class="w-full p-4 rounded-2xl bg-gradient-to-br from-amber-50/60 via-zinc-50 to-orange-50/30 border border-amber-100/80 shadow-sm space-y-2.5 group-hover:scale-[1.02] transition-transform duration-500">
                    <div class="flex items-center justify-between">
                        <span class="text-[9px] font-extrabold uppercase text-amber-800 bg-amber-100 px-2 py-0.5 rounded-full">SKU #8821 ACTIVE</span>
                        <span class="text-[10px] text-zinc-400 font-medium">Verified SKU</span>
                    </div>
                    <div class="space-y-0.5">
                        <p class="text-xs font-extrabold text-zinc-900 tracking-tight">Kopi Susu Aren 250ml</p>
                        <p class="text-[10px] text-zinc-500 font-medium">Produk Wirausaha Mahasiswa</p>
                    </div>
                    <div class="pt-2 border-t border-amber-200/60 flex justify-between items-center text-xs">
                        <span class="text-[10px] font-semibold text-zinc-600">Harga Konsinyasi</span>
                        <span class="font-extrabold text-amber-700">Rp 15.000</span>
                    </div>
                </div>
            </div>

            <!-- Card 2: Step 02 -->
            <div class="rounded-3xl bg-white p-6 sm:p-7 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col justify-between space-y-6 group">
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-[#155A6B] bg-teal-50 px-3 py-1 rounded-full">
                            LANGKAH 02
                        </span>
                        <span class="text-[10px] font-bold text-zinc-400">Gerai Kampus</span>
                    </div>
                    
                    <div class="space-y-1.5">
                        <h3 class="text-xl font-bold text-zinc-900 tracking-tight">Penjualan via POS Digital</h3>
                        <p class="text-xs text-zinc-600 font-medium leading-relaxed">
                            Produk dipajang di etalase fisik Bermadani Mart dan di-scan secara otomatis oleh kasir POS toko.
                        </p>
                    </div>
                </div>

                <!-- Pure CSS Micro-UI Mockup POS Barcode Scanner Widget -->
                <div class="w-full p-4 rounded-2xl bg-gradient-to-br from-teal-50/60 via-zinc-50 to-emerald-50/30 border border-teal-100/80 shadow-sm space-y-2.5 group-hover:scale-[1.02] transition-transform duration-500">
                    <div class="flex items-center justify-between">
                        <span class="text-[9px] font-extrabold uppercase text-[#155A6B] bg-teal-100 px-2 py-0.5 rounded-full">Etalase Toko POS</span>
                        <span class="text-[10px] font-bold text-emerald-700 bg-white px-2 py-0.5 rounded-full">Live Scan</span>
                    </div>
                    <div class="py-1 text-center bg-white rounded-xl border border-zinc-100 shadow-2xs">
                        <p class="font-mono text-xs font-bold text-zinc-800 tracking-widest">|||| |||||| |||| |||</p>
                        <p class="text-[9px] text-zinc-400 font-mono">BARCODE #899120401</p>
                    </div>
                    <div class="pt-1.5 border-t border-teal-200/60 flex justify-between items-center text-xs">
                        <span class="text-[10px] font-semibold text-zinc-600">Terjual Hari Ini</span>
                        <span class="font-extrabold text-[#155A6B]">42 Botol (Laris)</span>
                    </div>
                </div>
            </div>

            <!-- Card 3: Step 03 -->
            <div class="rounded-3xl bg-white p-6 sm:p-7 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col justify-between space-y-6 group">
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-purple-700 bg-purple-50 px-3 py-1 rounded-full">
                            LANGKAH 03
                        </span>
                        <span class="text-[10px] font-bold text-zinc-400">Dashboard 24/7</span>
                    </div>
                    
                    <div class="space-y-1.5">
                        <h3 class="text-xl font-bold text-zinc-900 tracking-tight">Pantau Sales & Cairkan Omzet</h3>
                        <p class="text-xs text-zinc-600 font-medium leading-relaxed">
                            Pantau kuantitas barang laku realtime dari portal supplier & lakukan pencairan dana kapan saja.
                        </p>
                    </div>
                </div>

                <!-- Pure CSS Micro-UI Mockup Supplier Sales Dashboard Widget -->
                <div class="w-full p-4 rounded-2xl bg-gradient-to-br from-purple-50/60 via-zinc-50 to-indigo-50/30 border border-purple-100/80 shadow-sm space-y-2.5 group-hover:scale-[1.02] transition-transform duration-500">
                    <div class="flex items-center justify-between">
                        <span class="text-[9px] font-extrabold uppercase text-purple-800 bg-purple-100 px-2 py-0.5 rounded-full">Saldo Omzet Clean</span>
                        <span class="text-[10px] text-zinc-400 font-medium">Realtime Payout</span>
                    </div>
                    <div>
                        <p class="text-xl font-extrabold text-zinc-900 tracking-tight">Rp 630.000</p>
                        <p class="text-[10px] text-zinc-500 font-medium mt-0.5">Penjualan Bersih Terkumpul</p>
                    </div>
                    <div class="pt-2 border-t border-purple-200/60 flex justify-between items-center text-xs">
                        <span class="text-[10px] font-bold text-purple-700">Pencairan Dana</span>
                        <span class="text-[10px] font-extrabold text-white bg-purple-700 px-2.5 py-1 rounded-lg">Cairkan Ke Rekening</span>
                    </div>
                </div>
            </div>

        </div>

    </div>
</section>
