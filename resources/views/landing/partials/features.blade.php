<!-- 1. CORE PRODUCTS & SERVICES SHOWCASE (Pattern 1 from Reference & Micro-UI Skill) -->
<section id="features" class="py-16 sm:py-24 bg-white text-zinc-900 border-t border-zinc-200/80 relative overflow-hidden">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 relative z-10">
        
        <div class="mb-10 sm:mb-14 flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <span class="inline-flex items-center gap-1.5 text-[10px] font-extrabold uppercase tracking-wider text-[#155A6B] bg-teal-50 px-3 py-1 rounded-full mb-3">
                    <i class='bx bx-grid-alt text-xs'></i> EKOSISTEM LAYANAN ANGGOTA
                </span>
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-apple-headline text-zinc-900 tracking-tight">
                    Fasilitas Anggota Bermadani.
                </h2>
            </div>
            <p class="text-xs sm:text-sm text-zinc-600 max-w-md font-medium leading-relaxed">
                Semua layanan dirancang transparan, adil, dan berbasis syariah untuk mendukung keberdayaan ekonomi civitas akademika UMBandung.
            </p>
        </div>

        <!-- Interactive Split Showcase Card Container (Borderless Modern Aesthetics) -->
        <div class="bg-zinc-100/70 rounded-3xl sm:rounded-[2.5rem] p-6 sm:p-10 shadow-sm" x-data="{ activeTab: 'mart' }">
            <div class="flex flex-col lg:flex-row gap-8 items-stretch w-full">
                
                <!-- Left Side: Interactive Row Selectors (5/12 width) -->
                <div class="w-full lg:w-5/12 flex-shrink-0 flex flex-col justify-center space-y-3.5">
                    
                    <!-- Selector 1: Bermadani Mart -->
                    <button @click="activeTab = 'mart'" 
                        :class="activeTab === 'mart' ? 'bg-[#155A6B] text-white shadow-md scale-[1.02]' : 'bg-white text-zinc-800 shadow-sm hover:shadow-md hover:bg-white'"
                        class="w-full text-left p-4 sm:p-5 rounded-2xl transition-all duration-300 flex items-center justify-between group cursor-pointer">
                        <div class="space-y-1 pr-3">
                            <span :class="activeTab === 'mart' ? 'text-teal-200 bg-white/20' : 'text-[#155A6B] bg-teal-50'" class="inline-flex items-center gap-1.5 text-[10px] font-extrabold uppercase tracking-wider px-2.5 py-0.5 rounded-full">
                                <i class='bx bx-shopping-bag'></i> Minimarket Kampus
                            </span>
                            <h3 class="text-base sm:text-lg font-bold tracking-tight">Bermadani Mart & Retail</h3>
                            <p :class="activeTab === 'mart' ? 'text-teal-100' : 'text-zinc-500'" class="text-xs font-medium line-clamp-2">
                                Belanja harian berpoin dividen SHU & harga promo khusus anggota.
                            </p>
                        </div>
                        <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center transition-transform duration-300"
                            :class="activeTab === 'mart' ? 'bg-white/20 text-white' : 'bg-zinc-100 text-zinc-400 group-hover:text-zinc-600'">
                            <i class='bx bx-chevron-right text-xl'></i>
                        </div>
                    </button>

                    <!-- Selector 2: Simpanan Syariah -->
                    <button @click="activeTab = 'savings'" 
                        :class="activeTab === 'savings' ? 'bg-[#155A6B] text-white shadow-md scale-[1.02]' : 'bg-white text-zinc-800 shadow-sm hover:shadow-md hover:bg-white'"
                        class="w-full text-left p-4 sm:p-5 rounded-2xl transition-all duration-300 flex items-center justify-between group cursor-pointer">
                        <div class="space-y-1 pr-3">
                            <span :class="activeTab === 'savings' ? 'text-teal-200 bg-white/20' : 'text-emerald-700 bg-emerald-50'" class="inline-flex items-center gap-1.5 text-[10px] font-extrabold uppercase tracking-wider px-2.5 py-0.5 rounded-full">
                                <i class='bx bx-vault'></i> 0% Admin Siluman
                            </span>
                            <h3 class="text-base sm:text-lg font-bold tracking-tight">Simpanan Syariah Amanah</h3>
                            <p :class="activeTab === 'savings' ? 'text-teal-100' : 'text-zinc-500'" class="text-xs font-medium line-clamp-2">
                                Simpanan Pokok & Wajib berbasis akad syariah tanpa potongan misterius.
                            </p>
                        </div>
                        <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center transition-transform duration-300"
                            :class="activeTab === 'savings' ? 'bg-white/20 text-white' : 'bg-zinc-100 text-zinc-400 group-hover:text-zinc-600'">
                            <i class='bx bx-chevron-right text-xl'></i>
                        </div>
                    </button>

                    <!-- Selector 3: Dividen SHU -->
                    <button @click="activeTab = 'shu'" 
                        :class="activeTab === 'shu' ? 'bg-[#155A6B] text-white shadow-md scale-[1.02]' : 'bg-white text-zinc-800 shadow-sm hover:shadow-md hover:bg-white'"
                        class="w-full text-left p-4 sm:p-5 rounded-2xl transition-all duration-300 flex items-center justify-between group cursor-pointer">
                        <div class="space-y-1 pr-3">
                            <span :class="activeTab === 'shu' ? 'text-teal-200 bg-white/20' : 'text-purple-700 bg-purple-50'" class="inline-flex items-center gap-1.5 text-[10px] font-extrabold uppercase tracking-wider px-2.5 py-0.5 rounded-full">
                                <i class='bx bx-pie-chart-alt-2'></i> Dividen & SHU
                            </span>
                            <h3 class="text-base sm:text-lg font-bold tracking-tight">Bagi Hasil Keuntungan Toko</h3>
                            <p :class="activeTab === 'shu' ? 'text-teal-100' : 'text-zinc-500'" class="text-xs font-medium line-clamp-2">
                                Keuntungan hasil minimarket dikembalikan secara adil ke seluruh anggota.
                            </p>
                        </div>
                        <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center transition-transform duration-300"
                            :class="activeTab === 'shu' ? 'bg-white/20 text-white' : 'bg-zinc-100 text-zinc-400 group-hover:text-zinc-600'">
                            <i class='bx bx-chevron-right text-xl'></i>
                        </div>
                    </button>

                    <!-- Selector 4: Kartu Anggota Digital -->
                    <button @click="activeTab = 'card'" 
                        :class="activeTab === 'card' ? 'bg-[#155A6B] text-white shadow-md scale-[1.02]' : 'bg-white text-zinc-800 shadow-sm hover:shadow-md hover:bg-white'"
                        class="w-full text-left p-4 sm:p-5 rounded-2xl transition-all duration-300 flex items-center justify-between group cursor-pointer">
                        <div class="space-y-1 pr-3">
                            <span :class="activeTab === 'card' ? 'text-teal-200 bg-white/20' : 'text-amber-700 bg-amber-50'" class="inline-flex items-center gap-1.5 text-[10px] font-extrabold uppercase tracking-wider px-2.5 py-0.5 rounded-full">
                                <i class='bx bx-qr-scan'></i> Akses 24/7
                            </span>
                            <h3 class="text-base sm:text-lg font-bold tracking-tight">Kartu Anggota Digital QR</h3>
                            <p :class="activeTab === 'card' ? 'text-teal-100' : 'text-zinc-500'" class="text-xs font-medium line-clamp-2">
                                Akses kartu digital untuk belanja cepat & cek mutasi dari smartphone.
                            </p>
                        </div>
                        <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center transition-transform duration-300"
                            :class="activeTab === 'card' ? 'bg-white/20 text-white' : 'bg-zinc-100 text-zinc-400 group-hover:text-zinc-600'">
                            <i class='bx bx-chevron-right text-xl'></i>
                        </div>
                    </button>

                </div>

                <!-- Right Side: Dynamic Feature Spotlight Display (7/12 width) -->
                <div class="w-full lg:w-7/12 flex-grow min-w-0 bg-white rounded-2xl p-6 sm:p-8 flex flex-col justify-between shadow-md relative overflow-hidden">
                    
                    <!-- Tab Content 1: Mart -->
                    <div x-show="activeTab === 'mart'" 
                         x-transition:enter="transition ease-out duration-300" 
                         x-transition:enter-start="opacity-0 translate-y-2" 
                         x-transition:enter-end="opacity-100 translate-y-0" 
                         class="space-y-6 flex flex-col h-full justify-between min-w-0">
                        <div class="space-y-3">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-[#155A6B]"></span>
                                <span class="text-[10px] font-extrabold uppercase tracking-wider text-zinc-400">Minimarket Kampus</span>
                            </div>
                            <h3 class="text-xl sm:text-2xl font-black text-zinc-900 tracking-tight">Belanja Harian Kampus, Poin Langsung Jadi Dividen.</h3>
                            <p class="text-xs sm:text-sm text-zinc-600 leading-relaxed font-medium">
                                Setiap transaksi belanja kebutuhan harian civitas akademika di Bermadani Mart akan mencatat poin transaksi yang terakumulasi menjadi pembagian Sisa Hasil Usaha (SHU) setiap tahunnya.
                            </p>
                        </div>
                        
                        <!-- Pure CSS Micro-UI POS Receipt Widget -->
                        <div class="w-full p-4 sm:p-5 rounded-2xl bg-zinc-50 border border-zinc-200/60 shadow-sm space-y-3">
                            <div class="flex items-center justify-between text-xs pb-2.5 border-b border-zinc-200/80">
                                <div class="flex items-center gap-2">
                                    <i class='bx bx-receipt text-[#155A6B] text-lg'></i>
                                    <span class="font-bold text-zinc-800">Nota Transaksi POS #BM-9924</span>
                                </div>
                                <span class="text-[10px] font-bold text-emerald-700 bg-emerald-100/80 px-2.5 py-0.5 rounded-full">+65 Poin SHU</span>
                            </div>
                            <div class="space-y-2 text-xs text-zinc-600">
                                <div class="flex justify-between items-center">
                                    <span>Air Mineral 600ml (x2)</span>
                                    <span class="font-bold text-zinc-800">Rp 6.000</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span>Roti Bakery Kampus</span>
                                    <span class="font-bold text-zinc-800">Rp 8.500</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span>Notebook A5 UMB</span>
                                    <span class="font-bold text-zinc-800">Rp 12.000</span>
                                </div>
                            </div>
                            <div class="pt-2.5 border-t border-dashed border-zinc-300 flex justify-between items-center text-xs">
                                <span class="font-extrabold text-zinc-900">Total Belanja Anggota</span>
                                <span class="font-extrabold text-[#155A6B] text-base">Rp 26.500</span>
                            </div>
                        </div>

                        <!-- Key Metric Pills -->
                        <div class="grid grid-cols-2 gap-3 pt-1">
                            <div class="p-3.5 bg-zinc-50 rounded-xl">
                                <p class="text-[10px] font-extrabold uppercase text-zinc-400">Benefit Utama</p>
                                <p class="text-xs font-bold text-zinc-800 mt-0.5">Diskon Khusus Anggota</p>
                            </div>
                            <div class="p-3.5 bg-zinc-50 rounded-xl">
                                <p class="text-[10px] font-extrabold uppercase text-zinc-400">Transparansi POS</p>
                                <p class="text-xs font-bold text-zinc-800 mt-0.5">Nota Digital di Portal</p>
                            </div>
                        </div>
                    </div>

                    <!-- Tab Content 2: Savings -->
                    <div x-show="activeTab === 'savings'" 
                         x-transition:enter="transition ease-out duration-300" 
                         x-transition:enter-start="opacity-0 translate-y-2" 
                         x-transition:enter-end="opacity-100 translate-y-0" 
                         class="space-y-6 flex flex-col h-full justify-between min-w-0" 
                         x-cloak style="display: none;">
                        <div class="space-y-3">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                <span class="text-[10px] font-extrabold uppercase tracking-wider text-zinc-400">Simpanan Syariah</span>
                            </div>
                            <h3 class="text-xl sm:text-2xl font-black text-zinc-900 tracking-tight">Nabung Syariah Amanah, Zero Potongan Misterius.</h3>
                            <p class="text-xs sm:text-sm text-zinc-600 leading-relaxed font-medium">
                                Simpanan Pokok dan Simpanan Wajib dikelola dengan prinsip transparansi syariah. Seluruh saldo tercatat secara mutlak di portal anggota dan dapat diperiksa kapan saja tanpa biaya potongan bulanan.
                            </p>
                        </div>

                        <!-- Pure CSS Micro-UI Sharia Ledger Balance Widget -->
                        <div class="w-full p-4 sm:p-5 rounded-2xl bg-gradient-to-br from-emerald-50/60 via-zinc-50 to-teal-50/40 border border-emerald-100 shadow-sm space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] font-extrabold uppercase text-emerald-800 bg-emerald-100/70 px-2.5 py-0.5 rounded-full">Saldo Ledger Anggota</span>
                                <span class="text-[10px] font-bold text-emerald-700 bg-white px-2 py-0.5 rounded-full border border-emerald-200/60">Akad Wadi'ah</span>
                            </div>
                            <div>
                                <p class="text-2xl font-extrabold text-zinc-900 tracking-tight">Rp 1.450.000</p>
                                <p class="text-[11px] text-zinc-500 font-medium mt-0.5">Simpanan Pokok (Rp 100k) + Simpanan Wajib</p>
                            </div>
                            <div class="flex items-center gap-1.5 text-xs font-bold text-emerald-700 pt-2 border-t border-emerald-200/60">
                                <i class='bx bx-check-shield text-base text-emerald-600'></i>
                                <span>Rp 0 Biaya Administrasi Bulanan (Bebas Potongan)</span>
                            </div>
                        </div>

                        <!-- Key Metric Pills -->
                        <div class="grid grid-cols-2 gap-3 pt-1">
                            <div class="p-3.5 bg-zinc-50 rounded-xl">
                                <p class="text-[10px] font-extrabold uppercase text-zinc-400">Biaya Administrasi</p>
                                <p class="text-xs font-bold text-emerald-600 mt-0.5">Rp 0 / Bebas Potongan</p>
                            </div>
                            <div class="p-3.5 bg-zinc-50 rounded-xl">
                                <p class="text-[10px] font-extrabold uppercase text-zinc-400">Akad Syariah</p>
                                <p class="text-xs font-bold text-zinc-800 mt-0.5">Wadi'ah & Mudharabah</p>
                            </div>
                        </div>
                    </div>

                    <!-- Tab Content 3: SHU -->
                    <div x-show="activeTab === 'shu'" 
                         x-transition:enter="transition ease-out duration-300" 
                         x-transition:enter-start="opacity-0 translate-y-2" 
                         x-transition:enter-end="opacity-100 translate-y-0" 
                         class="space-y-6 flex flex-col h-full justify-between min-w-0" 
                         x-cloak style="display: none;">
                        <div class="space-y-3">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-purple-600"></span>
                                <span class="text-[10px] font-extrabold uppercase tracking-wider text-zinc-400">Bagi Hasil Dividen</span>
                            </div>
                            <h3 class="text-xl sm:text-2xl font-black text-zinc-900 tracking-tight">Keuntungan Usaha Minimarket Dikembalikan ke Anggota.</h3>
                            <p class="text-xs sm:text-sm text-zinc-600 leading-relaxed font-medium">
                                Berbeda dari minimarket komersial biasa, seluruh dividen hasil usaha Bermadani Mart dibagikan kembali secara proporsional kepada civitas akademika yang aktif berbelanja dan menyimpan dana.
                            </p>
                        </div>

                        <!-- Pure CSS Micro-UI Annual SHU Dividends Widget -->
                        <div class="w-full p-4 sm:p-5 rounded-2xl bg-white border border-purple-100 shadow-sm space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] font-extrabold uppercase text-purple-700 bg-purple-50 px-2.5 py-0.5 rounded-full">Akumulasi SHU 2026</span>
                                <span class="text-xs font-bold text-purple-700">Tahun Buku 2025</span>
                            </div>
                            <div class="space-y-2.5">
                                <div>
                                    <div class="flex justify-between text-xs mb-1">
                                        <span class="font-medium text-zinc-600">Dividen Transaksi Belanja</span>
                                        <span class="font-extrabold text-purple-900">Rp 320.000</span>
                                    </div>
                                    <div class="w-full bg-zinc-100 h-2 rounded-full overflow-hidden">
                                        <div class="bg-purple-600 h-full rounded-full" style="width: 75%;"></div>
                                    </div>
                                </div>
                                <div>
                                    <div class="flex justify-between text-xs mb-1">
                                        <span class="font-medium text-zinc-600">Bagi Hasil Simpanan Wajib</span>
                                        <span class="font-extrabold text-[#155A6B]">Rp 180.000</span>
                                    </div>
                                    <div class="w-full bg-zinc-100 h-2 rounded-full overflow-hidden">
                                        <div class="bg-[#155A6B] h-full rounded-full" style="width: 50%;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Key Metric Pills -->
                        <div class="grid grid-cols-2 gap-3 pt-1">
                            <div class="p-3.5 bg-zinc-50 rounded-xl">
                                <p class="text-[10px] font-extrabold uppercase text-zinc-400">Pembagian SHU</p>
                                <p class="text-xs font-bold text-[#155A6B] mt-0.5">Tiap Akhir Tahun Buku</p>
                            </div>
                            <div class="p-3.5 bg-zinc-50 rounded-xl">
                                <p class="text-[10px] font-extrabold uppercase text-zinc-400">Audit Laporan</p>
                                <p class="text-xs font-bold text-zinc-800 mt-0.5">Transparan di Dashboard</p>
                            </div>
                        </div>
                    </div>

                    <!-- Tab Content 4: Card -->
                    <div x-show="activeTab === 'card'" 
                         x-transition:enter="transition ease-out duration-300" 
                         x-transition:enter-start="opacity-0 translate-y-2" 
                         x-transition:enter-end="opacity-100 translate-y-0" 
                         class="space-y-6 flex flex-col h-full justify-between min-w-0" 
                         x-cloak style="display: none;">
                        <div class="space-y-3">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                <span class="text-[10px] font-extrabold uppercase tracking-wider text-zinc-400">Kartu Anggota Digital</span>
                            </div>
                            <h3 class="text-xl sm:text-2xl font-black text-zinc-900 tracking-tight">Kartu Identitas Digital, Transaksi Cepat & Praktis.</h3>
                            <p class="text-xs sm:text-sm text-zinc-600 leading-relaxed font-medium">
                                Tunjukkan QR Code Kartu Anggota Digital kamu di kasir Bermadani Mart untuk mendapatkan diskon khusus dan pencatatan poin dividen otomatis.
                            </p>
                        </div>

                        <!-- Pure CSS Micro-UI Digital Member QR Card Widget -->
                        <div class="w-full p-5 rounded-2xl bg-gradient-to-br from-[#155A6B] to-teal-900 text-white shadow-lg relative overflow-hidden">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-lg bg-white/20 flex items-center justify-center text-xs font-black">B</div>
                                    <span class="text-xs font-bold tracking-wider uppercase opacity-90">Koperasi Bermadani</span>
                                </div>
                                <span class="text-[10px] font-semibold bg-emerald-500/25 text-emerald-300 px-2.5 py-0.5 rounded-full border border-emerald-400/30">ANGGOTA AKTIF</span>
                            </div>
                            <div class="flex items-end justify-between">
                                <div>
                                    <p class="text-[10px] uppercase text-teal-200 font-medium">Civitas UMBandung</p>
                                    <p class="text-base font-extrabold tracking-tight text-white mt-0.5">Ahmad Fauzi</p>
                                    <p class="text-[10px] text-teal-200/90 mt-0.5">NIM: 220104089</p>
                                </div>
                                <div class="w-12 h-12 bg-white rounded-xl p-1.5 flex items-center justify-center shadow-md">
                                    <i class='bx bx-qr-scan text-3xl text-zinc-900'></i>
                                </div>
                            </div>
                        </div>

                        <!-- Key Metric Pills -->
                        <div class="grid grid-cols-2 gap-3 pt-1">
                            <div class="p-3.5 bg-zinc-50 rounded-xl">
                                <p class="text-[10px] font-extrabold uppercase text-zinc-400">Scan Kasir</p>
                                <p class="text-xs font-bold text-zinc-800 mt-0.5">Quick QR Checkout</p>
                            </div>
                            <div class="p-3.5 bg-zinc-50 rounded-xl">
                                <p class="text-[10px] font-extrabold uppercase text-zinc-400">Portal Mobile</p>
                                <p class="text-xs font-bold text-zinc-800 mt-0.5">Akses Mutasi 24/7</p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
</section>
