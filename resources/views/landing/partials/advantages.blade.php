<!-- 3. OUR STRATEGIC ADVANTAGES SPOTLIGHT (Pattern 3 from Reference) -->
<section id="advantages" class="py-16 sm:py-24 bg-white border-t border-zinc-200/80 relative overflow-hidden">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8">
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-[#155A6B] bg-teal-50 px-3 py-1 rounded-full">
                    <i class='bx bx-award text-xs'></i> KEUNGGULAN UTAMA
                </span>
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-zinc-900 text-apple-headline tracking-tight mt-2">
                    Keunggulan Utama Bermadani.
                </h2>
            </div>
            <p class="text-xs sm:text-sm text-zinc-600 max-w-sm font-medium leading-relaxed">
                Solusi tata kelola ekonomi kampus berlandaskan kejujuran, syariat Islam, dan azas kekeluargaan UMBandung.
            </p>
        </div>

        <!-- Main Container with Dark Background Image & 3 Floating Frosted Glass Cards (Pattern 3 Layout) -->
        <div class="relative rounded-3xl lg:rounded-[2.5rem] shadow-2xl overflow-hidden bg-zinc-950 min-h-[540px] flex flex-col justify-between p-6 sm:p-8 md:p-10">
            
            <!-- Background Image Graphic -->
            <img src="{{ asset('images/hero-landscape.jpeg') }}" alt="Strategic Advantages" class="absolute inset-0 w-full h-full object-cover opacity-35 filter saturate-150">
            
            <!-- Ambient Dark Overlay (Darker for maximum text contrast) -->
            <div class="absolute inset-0 bg-gradient-to-b from-zinc-950/85 via-zinc-950/70 to-zinc-950/90 pointer-events-none"></div>

            <!-- Top Metric Stats Bar (High-Contrast Frosted Glass Cards with Pure White Text) -->
            <div class="relative z-10 grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4 pb-8 border-b border-white/15">
                <div class="p-4 sm:p-5 rounded-2xl bg-zinc-900/80 backdrop-blur-md border border-white/10 flex flex-col items-center justify-center text-center space-y-1 shadow-lg">
                    <p class="text-2xl sm:text-3xl font-black text-white tracking-tight">1,200+</p>
                    <span class="text-[11px] text-zinc-200 font-extrabold uppercase tracking-wider">Civitas Akademika</span>
                </div>
                <div class="p-4 sm:p-5 rounded-2xl bg-zinc-900/80 backdrop-blur-md border border-white/10 flex flex-col items-center justify-center text-center space-y-1 shadow-lg">
                    <p class="text-2xl sm:text-3xl font-black text-white tracking-tight">Rp 0</p>
                    <span class="text-[11px] text-zinc-200 font-extrabold uppercase tracking-wider">Potongan Admin</span>
                </div>
                <div class="p-4 sm:p-5 rounded-2xl bg-zinc-900/80 backdrop-blur-md border border-white/10 flex flex-col items-center justify-center text-center space-y-1 shadow-lg">
                    <p class="text-2xl sm:text-3xl font-black text-white tracking-tight">100%</p>
                    <span class="text-[11px] text-zinc-200 font-extrabold uppercase tracking-wider">Akad Syariah</span>
                </div>
                <div class="p-4 sm:p-5 rounded-2xl bg-zinc-900/80 backdrop-blur-md border border-white/10 flex flex-col items-center justify-center text-center space-y-1 shadow-lg">
                    <p class="text-2xl sm:text-3xl font-black text-white tracking-tight">24/7</p>
                    <span class="text-[11px] text-zinc-200 font-extrabold uppercase tracking-wider">Portal Digital</span>
                </div>
            </div>

            <!-- Bottom 3 Floating Frosted-Glass Cards (Pure White Text & Icons) -->
            <div class="relative z-10 grid grid-cols-1 md:grid-cols-3 gap-5 pt-8">
                
                <!-- Floating Card 1 -->
                <div class="p-6 rounded-2xl bg-white/10 backdrop-blur-xl border border-white/10 text-white shadow-2xl space-y-4 hover:bg-white/15 transition-all duration-300">
                    <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center text-white text-xl font-bold">
                        <i class='bx bx-check-shield'></i>
                    </div>
                    <div class="space-y-2">
                        <h3 class="text-lg font-extrabold tracking-tight text-white">100% Prinsip Syariah</h3>
                        <ul class="space-y-1.5 text-xs text-zinc-100 font-medium">
                            <li class="flex items-center gap-2"><i class='bx bx-check text-white text-sm font-bold'></i> Bebas Riba, Gharar, & Maysir</li>
                            <li class="flex items-center gap-2"><i class='bx bx-check text-white text-sm font-bold'></i> Akad Mudharabah & Murabahah</li>
                            <li class="flex items-center gap-2"><i class='bx bx-check text-white text-sm font-bold'></i> Transparan Bagi Hasil SHU</li>
                        </ul>
                    </div>
                </div>

                <!-- Floating Card 2 -->
                <div class="p-6 rounded-2xl bg-white/10 backdrop-blur-xl border border-white/10 text-white shadow-2xl space-y-4 hover:bg-white/15 transition-all duration-300">
                    <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center text-white text-xl font-bold">
                        <i class='bx bx-devices'></i>
                    </div>
                    <div class="space-y-2">
                        <h3 class="text-lg font-extrabold tracking-tight text-white">Transparansi Digital 24/7</h3>
                        <ul class="space-y-1.5 text-xs text-zinc-100 font-medium">
                            <li class="flex items-center gap-2"><i class='bx bx-check text-white text-sm font-bold'></i> Portal Mandiri Anggota & Supplier</li>
                            <li class="flex items-center gap-2"><i class='bx bx-check text-white text-sm font-bold'></i> Realtime Saldo & Mutasi Digital</li>
                            <li class="flex items-center gap-2"><i class='bx bx-check text-white text-sm font-bold'></i> Riwayat Transaksi POS Akurat</li>
                        </ul>
                    </div>
                </div>

                <!-- Floating Card 3 -->
                <div class="p-6 rounded-2xl bg-white/10 backdrop-blur-xl border border-white/10 text-white shadow-2xl space-y-4 hover:bg-white/15 transition-all duration-300">
                    <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center text-white text-xl font-bold">
                        <i class='bx bx-buildings'></i>
                    </div>
                    <div class="space-y-2">
                        <h3 class="text-lg font-extrabold tracking-tight text-white">Dividen Kembali ke Kampus</h3>
                        <ul class="space-y-1.5 text-xs text-zinc-100 font-medium">
                            <li class="flex items-center gap-2"><i class='bx bx-check text-white text-sm font-bold'></i> SHU Dibagikan Adil Tiap Tahun</li>
                            <li class="flex items-center gap-2"><i class='bx bx-check text-white text-sm font-bold'></i> Dukung Wirausaha Mahasiswa</li>
                            <li class="flex items-center gap-2"><i class='bx bx-check text-white text-sm font-bold'></i> Ekosistem Mandiri UMBandung</li>
                        </ul>
                    </div>
                </div>

            </div>

        </div>

    </div>
</section>
