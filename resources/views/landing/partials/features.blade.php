<!-- 1. CORE PRODUCTS & SERVICES SHOWCASE (Pattern 1 from Reference) -->
<section id="features" class="py-16 sm:py-24 bg-white text-zinc-900 border-t border-zinc-200 relative overflow-hidden">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 relative z-10">
        
        <div class="mb-10 sm:mb-14 flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <span class="inline-flex items-center gap-1.5 text-[10px] font-extrabold uppercase tracking-wider text-[#155A6B] bg-teal-50 px-3 py-1 rounded-full mb-3">
                    CORE PRODUCTS & SERVICES
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
                <div class="w-full lg:w-5/12 flex-shrink-0 flex flex-col justify-center space-y-4">
                    
                    <!-- Selector 1: Bermadani Mart -->
                    <button @click="activeTab = 'mart'" 
                        :class="activeTab === 'mart' ? 'bg-[#155A6B] text-white shadow-md scale-[1.02]' : 'bg-white text-zinc-800 shadow-sm hover:shadow-md hover:bg-white'"
                        class="w-full text-left p-5 rounded-2xl transition-all duration-300 flex items-center justify-between group cursor-pointer">
                        <div class="space-y-1 pr-4">
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
                        class="w-full text-left p-5 rounded-2xl transition-all duration-300 flex items-center justify-between group cursor-pointer">
                        <div class="space-y-1 pr-4">
                            <span :class="activeTab === 'savings' ? 'text-teal-200 bg-white/20' : 'text-blue-600 bg-blue-50'" class="inline-flex items-center gap-1.5 text-[10px] font-extrabold uppercase tracking-wider px-2.5 py-0.5 rounded-full">
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
                        class="w-full text-left p-5 rounded-2xl transition-all duration-300 flex items-center justify-between group cursor-pointer">
                        <div class="space-y-1 pr-4">
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
                            <h3 class="text-xl sm:text-2xl font-black text-zinc-900 tracking-tight">Belanja Harian Kampus, Poin Langsung Jadi Dividen.</h3>
                            <p class="text-xs sm:text-sm text-zinc-600 leading-relaxed font-medium">
                                Setiap transaksi belanja kebutuhan harian civitas akademika di Bermadani Mart akan mencatat poin transaksi yang terakumulasi menjadi pembagian Sisa Hasil Usaha (SHU) setiap tahunnya.
                            </p>
                        </div>
                        
                        <!-- Unobscured Crisp 3D Image Container -->
                        <div class="w-full aspect-[16/9] rounded-xl bg-zinc-100/50 overflow-hidden shadow-inner">
                            <img src="{{ asset('images/bento/bento-mart-43.jpeg') }}" alt="Bermadani Mart" class="w-full h-full object-cover">
                        </div>

                        <!-- Key Metric Pills -->
                        <div class="grid grid-cols-2 gap-3 pt-2">
                            <div class="p-3.5 bg-zinc-50/80 rounded-xl">
                                <p class="text-[10px] font-extrabold uppercase text-zinc-400">Benefit Utama</p>
                                <p class="text-xs font-bold text-zinc-800 mt-0.5">Diskon Khusus Anggota</p>
                            </div>
                            <div class="p-3.5 bg-zinc-50/80 rounded-xl">
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
                            <h3 class="text-xl sm:text-2xl font-black text-zinc-900 tracking-tight">Nabung Syariah Amanah, Zero Potongan Misterius.</h3>
                            <p class="text-xs sm:text-sm text-zinc-600 leading-relaxed font-medium">
                                Simpanan Pokok dan Simpanan Wajib dikelola dengan prinsip transparansi syariah. Seluruh saldo tercatat secara mutlak di portal anggota dan dapat diperiksa kapan saja tanpa biaya potongan bulanan.
                            </p>
                        </div>

                        <!-- Unobscured Crisp 3D Image Container -->
                        <div class="w-full aspect-[16/9] rounded-xl bg-zinc-100/50 overflow-hidden shadow-inner">
                            <img src="{{ asset('images/bento/bento-savings-43.jpeg') }}" alt="Simpanan Syariah" class="w-full h-full object-cover">
                        </div>

                        <!-- Key Metric Pills -->
                        <div class="grid grid-cols-2 gap-3 pt-2">
                            <div class="p-3.5 bg-zinc-50/80 rounded-xl">
                                <p class="text-[10px] font-extrabold uppercase text-zinc-400">Biaya Administrasi</p>
                                <p class="text-xs font-bold text-emerald-600 mt-0.5">Rp 0 / Bebas Potongan</p>
                            </div>
                            <div class="p-3.5 bg-zinc-50/80 rounded-xl">
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
                            <h3 class="text-xl sm:text-2xl font-black text-zinc-900 tracking-tight">Keuntungan Usaha Minimarket Dikembalikan ke Anggota.</h3>
                            <p class="text-xs sm:text-sm text-zinc-600 leading-relaxed font-medium">
                                Berbeda dari minimarket komersial biasa, seluruh dividen hasil usaha Bermadani Mart dibagikan kembali secara proporsional kepada civitas akademika yang aktif berbelanja dan menyimpan dana.
                            </p>
                        </div>

                        <!-- Unobscured Crisp 3D Image Container -->
                        <div class="w-full aspect-[16/9] rounded-xl bg-zinc-100/50 overflow-hidden shadow-inner">
                            <img src="{{ asset('images/bento/bento-shu-43.jpeg') }}" alt="Bagi Hasil SHU" class="w-full h-full object-cover">
                        </div>

                        <!-- Key Metric Pills -->
                        <div class="grid grid-cols-2 gap-3 pt-2">
                            <div class="p-3.5 bg-zinc-50/80 rounded-xl">
                                <p class="text-[10px] font-extrabold uppercase text-zinc-400">Pembagian SHU</p>
                                <p class="text-xs font-bold text-[#155A6B] mt-0.5">Tiap Akhir Tahun Buku</p>
                            </div>
                            <div class="p-3.5 bg-zinc-50/80 rounded-xl">
                                <p class="text-[10px] font-extrabold uppercase text-zinc-400">Audit Laporan</p>
                                <p class="text-xs font-bold text-zinc-800 mt-0.5">Transparan di Dashboard</p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
</section>
