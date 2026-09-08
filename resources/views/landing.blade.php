<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ coop_config('legal_name', 'Koperasi Konsumen Syariah Berkah Solusi Madani') }} — UMBandung</title>

    <!-- Google Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=SF+Pro+Display:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "SF Pro Display", "Plus Jakarta Sans", sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        .text-apple-headline {
            letter-spacing: -0.035em;
            line-height: 1.04;
        }

        .apple-btn-blue {
            background-color: #155A6B;
            color: #ffffff;
            border-radius: 980px;
            box-shadow: 0 4px 14px 0 rgba(21, 90, 107, 0.3);
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .apple-btn-blue:hover {
            background-color: #1a6b80;
            transform: translateY(-1px);
            box-shadow: 0 6px 18px 0 rgba(21, 90, 107, 0.4);
            color: #ffffff;
        }

        /* Glassmorphism CTA Primary (Hero Button - 0% Opacity) */
        .glass-cta-primary {
            background: rgba(255, 255, 255, 0) !important;
            backdrop-filter: blur(24px) saturate(180%) !important;
            -webkit-backdrop-filter: blur(24px) saturate(180%) !important;
            border: 1.5px solid rgba(255, 255, 255, 0.9) !important;
            border-radius: 980px !important;
            box-shadow: 
                inset 0 1.5px 2px 0 rgba(255, 255, 255, 0.8),
                0 10px 30px -4px rgba(0, 0, 0, 0.12) !important;
            color: #0f172a !important;
            font-weight: 700 !important;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1) !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
        }

        .glass-cta-primary:hover {
            background: rgba(255, 255, 255, 0.2) !important;
            border-color: #ffffff !important;
            transform: translateY(-2px) scale(1.03) !important;
            box-shadow: 
                inset 0 2px 4px 0 rgba(255, 255, 255, 1),
                0 16px 36px -4px rgba(0, 0, 0, 0.18) !important;
            color: #155A6B !important;
        }

        /* Glassmorphism CTA Secondary (0% Opacity) */
        .glass-cta-secondary {
            background: rgba(255, 255, 255, 0) !important;
            backdrop-filter: blur(24px) saturate(180%) !important;
            -webkit-backdrop-filter: blur(24px) saturate(180%) !important;
            border: 1.5px solid rgba(255, 255, 255, 0.7) !important;
            border-radius: 980px !important;
            box-shadow: 
                inset 0 1.5px 2px 0 rgba(255, 255, 255, 0.6),
                0 8px 24px -4px rgba(0, 0, 0, 0.08) !important;
            color: #1e293b !important;
            font-weight: 700 !important;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1) !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
        }

        .glass-cta-secondary:hover {
            background: rgba(255, 255, 255, 0.15) !important;
            border-color: rgba(255, 255, 255, 0.95) !important;
            transform: translateY(-2px) scale(1.03) !important;
            box-shadow: 
                inset 0 2px 3px 0 rgba(255, 255, 255, 0.9),
                0 12px 30px -4px rgba(0, 0, 0, 0.14) !important;
            color: #0f172a !important;
        }

        .apple-tile {
            border-radius: 28px;
            transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .apple-tile:hover {
            transform: scale(1.015);
        }
    </style>
</head>
<body class="bg-[#f5f5f7] text-zinc-900 selection:bg-[#155A6B] selection:text-white transition-colors duration-300">

    <!-- Apple Frosted Navigation Bar -->
    <header class="fixed top-0 left-0 right-0 z-50 bg-white/80 backdrop-blur-md border-b border-zinc-200/80 transition-colors duration-300">
        <div class="max-w-6xl mx-auto px-6 h-12 flex items-center justify-between text-xs">
            
            <!-- Official Brand Logo -->
            <a href="{{ route('home') }}" class="flex items-center gap-2.5 font-semibold tracking-tight text-zinc-900 hover:opacity-80 transition-opacity">
                <img src="{{ asset('images/logo-koperasi.png') }}" alt="Logo Koperasi Bermadani" class="w-6 h-6 object-contain">
                <span>{{ coop_config('short_name', 'Bermadani') }} — UMBandung</span>
            </a>

            <!-- Nav Links -->
            <nav class="hidden md:flex items-center gap-8 text-zinc-500 font-medium">
                <a href="#overview" class="hover:text-zinc-900 transition-colors">Ikhtisar</a>
                <a href="#features" class="hover:text-zinc-900 transition-colors">Layanan Syariah</a>
                <a href="#supplier" class="hover:text-zinc-900 transition-colors">Mitra Supplier</a>
                <a href="#faq" class="hover:text-zinc-900 transition-colors">FAQ</a>
            </nav>

            <!-- Actions -->
            <div class="flex items-center gap-3">
                <!-- Portal CTA Button -->
                <a href="{{ route('login') }}" class="apple-btn-blue px-3.5 py-1.5 font-medium text-xs shadow-sm">
                    Portal Anggota
                </a>
            </div>

        </div>
    </header>

    <!-- STEVE JOBS / FIGMA DUAL HERO SECTION (Left-Aligned Text & Dual Landscape/Portrait Graphics) -->
    <section id="overview" class="min-h-[90dvh] pt-24 pb-16 sm:pt-32 sm:pb-24 md:pt-36 md:pb-28 text-left bg-[#f5f5f7] overflow-hidden relative border-b border-zinc-200 flex items-center">
        <!-- Mobile 9:16 Portrait Hero Background Graphic -->
        <div class="absolute inset-0 bg-[url('/images/hero-portrait.jpeg')] bg-cover bg-center md:hidden opacity-100 pointer-events-none transition-opacity duration-500"></div>

        <!-- Desktop 16:9 Landscape Hero Background Graphic -->
        <div class="absolute inset-0 bg-[url('/images/hero-landscape.jpeg')] bg-cover bg-right hidden md:block opacity-100 pointer-events-none transition-opacity duration-500"></div>

        <div class="max-w-6xl mx-auto px-4 sm:px-6 md:px-8 relative z-10 w-full">
            <div class="max-w-xl lg:max-w-2xl space-y-4 sm:space-y-6">
                
                <!-- Headline (Figma Style) -->
                <h1 class="text-3xl sm:text-5xl md:text-6xl lg:text-7xl font-extrabold text-zinc-900 text-apple-headline tracking-tight break-words leading-[1.08] sm:leading-[1.04]">
                    {{ coop_config('name', 'Koperasi Bermadani') }}
                </h1>

                <!-- Value Proposition Sub-Headline (Teal Color from Figma) -->
                <p class="text-lg sm:text-2xl md:text-3xl lg:text-4xl font-bold text-[#155A6B] leading-snug">
                    Didesain untuk Ekonomi Kampus UMBandung.
                </p>

                <!-- Human Benefit Subtext -->
                <p class="text-xs sm:text-base md:text-lg text-zinc-600 font-normal leading-relaxed max-w-lg sm:max-w-xl">
                    Wadah resmi civitas akademika UMBandung. Belanja harian di Bermadani Mart, simpanan syariah amanah, dan bagi hasil SHU yang kembali ke kantong kamu.
                </p>

                <!-- Action Button (Left-Aligned Glassmorphism CTA from Figma) -->
                <div class="pt-4 sm:pt-6 flex justify-start">
                    <a href="{{ route('login') }}" class="glass-cta-primary px-7 py-3.5 sm:px-9 sm:py-4 text-sm sm:text-base font-bold gap-2">
                        <span>Masuk Portal</span>
                        <i class='bx bx-right-arrow-alt text-lg sm:text-xl'></i>
                    </a>
                </div>

            </div>
        </div>
    </section>

    <!-- STEVE JOBS BENTO GRID TILES (Full Dual Light Aesthetics) -->
    <section id="features" class="py-16 sm:py-24 bg-white text-zinc-900 border-t border-zinc-200 relative overflow-hidden">
        
        <div class="max-w-6xl mx-auto px-4 sm:px-6 relative z-10">
            
            <div class="mb-10 sm:mb-14 text-center md:text-left">
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-apple-headline text-zinc-900">
                    Fasilitas Anggota Bermadani.
                </h2>
                <p class="text-xs sm:text-sm text-zinc-600 mt-2 max-w-xl">
                    Semua layanan dirancang transparan, adil, dan berbasis syariah untuk mendukung keberdayaan ekonomi civitas akademika UMBandung.
                </p>
            </div>

            <!-- Clean Unobscured Feature Cards Grid (Clear Image Frames + Separate Text Below) -->
            <div class="mt-8 sm:mt-12 space-y-6">
                <!-- Top Row: 2 Major Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <!-- Card 1: Bermadani Mart -->
                    <div class="rounded-3xl bg-zinc-50/80 border border-zinc-200/80 p-6 sm:p-7 shadow-sm hover:shadow-xl hover:border-[#155A6B]/40 transition-all duration-300 flex flex-col justify-between space-y-6 group">
                        <!-- Top Crisp 3D Image Frame (No text overlay, 100% clean image) -->
                        <div class="w-full aspect-[4/3] rounded-2xl bg-zinc-100 border border-zinc-200/60 overflow-hidden shadow-inner group-hover:scale-[1.02] transition-transform duration-500">
                            <img src="{{ asset('images/bento/bento-mart-43.jpeg') }}" alt="Bermadani Mart & Retail" class="w-full h-full object-cover">
                        </div>

                        <!-- Bottom Content -->
                        <div class="space-y-3">
                            <span class="inline-flex items-center gap-1.5 text-[10px] font-extrabold uppercase tracking-wider text-[#155A6B] bg-teal-50 border border-teal-200 px-3 py-1 rounded-full">
                                <i class='bx bx-shopping-bag text-sm'></i> Minimarket & Retail
                            </span>
                            <h3 class="text-xl sm:text-2xl font-black text-zinc-900 tracking-tight leading-tight">
                                Belanja Harian, Untungnya Balik ke Kamu.
                            </h3>
                            <p class="text-xs sm:text-sm text-zinc-600 font-medium leading-relaxed">
                                Belanja kebutuhan harian di minimarket UMBandung. Dapatkan harga khusus anggota & setiap rupiah transaksi diakumulasikan jadi dividen SHU.
                            </p>
                        </div>
                    </div>

                    <!-- Card 2: Simpanan Syariah -->
                    <div class="rounded-3xl bg-zinc-50/80 border border-zinc-200/80 p-6 sm:p-7 shadow-sm hover:shadow-xl hover:border-blue-500/40 transition-all duration-300 flex flex-col justify-between space-y-6 group">
                        <!-- Top Crisp 3D Image Frame -->
                        <div class="w-full aspect-[4/3] rounded-2xl bg-zinc-100 border border-zinc-200/60 overflow-hidden shadow-inner group-hover:scale-[1.02] transition-transform duration-500">
                            <img src="{{ asset('images/bento/bento-savings-43.jpeg') }}" alt="Simpanan Syariah Amanah" class="w-full h-full object-cover">
                        </div>

                        <!-- Bottom Content -->
                        <div class="space-y-3">
                            <span class="inline-flex items-center gap-1.5 text-[10px] font-extrabold uppercase tracking-wider text-blue-600 bg-blue-50 border border-blue-200 px-3 py-1 rounded-full">
                                <i class='bx bx-vault text-sm'></i> Simpanan Syariah
                            </span>
                            <h3 class="text-xl sm:text-2xl font-black text-zinc-900 tracking-tight leading-tight">
                                Nabung Amanah Tanpa Biaya Admin Siluman.
                            </h3>
                            <p class="text-xs sm:text-sm text-zinc-600 font-medium leading-relaxed">
                                Simpanan Pokok & Wajib berbasis akad Syariah. Bebas potongan bulanan misterius, tercatat transparan, & dipantau langsung dari portal.
                            </p>
                        </div>
                    </div>

                </div>

                <!-- Bottom Row: 3 Secondary Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                    
                    <!-- Card 3: Bagi Hasil SHU -->
                    <div class="rounded-3xl bg-zinc-50/80 border border-zinc-200/80 p-5 sm:p-6 shadow-sm hover:shadow-xl hover:border-teal-500/40 transition-all duration-300 flex flex-col justify-between space-y-5 group">
                        <div class="w-full aspect-[4/3] rounded-2xl bg-zinc-100 border border-zinc-200/60 overflow-hidden shadow-inner group-hover:scale-[1.02] transition-transform duration-500">
                            <img src="{{ asset('images/bento/bento-shu-43.jpeg') }}" alt="Bagi Hasil SHU Koperasi" class="w-full h-full object-cover">
                        </div>

                        <div class="space-y-2.5">
                            <span class="inline-flex items-center gap-1.5 text-[10px] font-extrabold uppercase tracking-wider text-[#155A6B] bg-teal-50 border border-teal-200 px-2.5 py-0.5 rounded-full">
                                <i class='bx bx-pie-chart-alt-2 text-xs'></i> Dividen & SHU
                            </span>
                            <h3 class="text-lg font-extrabold text-zinc-900 tracking-tight leading-snug">
                                Keuntungan Toko Dibagi ke Anggota.
                            </h3>
                            <p class="text-xs text-zinc-600 font-medium leading-relaxed">
                                Keuntungan usaha minimarket dikembalikan secara adil & proporsional ke seluruh anggota aktif.
                            </p>
                        </div>
                    </div>

                    <!-- Card 4: Titip Jual Supplier -->
                    <div class="rounded-3xl bg-zinc-50/80 border border-zinc-200/80 p-5 sm:p-6 shadow-sm hover:shadow-xl hover:border-amber-500/40 transition-all duration-300 flex flex-col justify-between space-y-5 group">
                        <div class="w-full aspect-[4/3] rounded-2xl bg-zinc-100 border border-zinc-200/60 overflow-hidden shadow-inner group-hover:scale-[1.02] transition-transform duration-500">
                            <img src="{{ asset('images/bento/bento-supplier-43.jpeg') }}" alt="Konsinyasi Supplier UMKM" class="w-full h-full object-cover">
                        </div>

                        <div class="space-y-2.5">
                            <span class="inline-flex items-center gap-1.5 text-[10px] font-extrabold uppercase tracking-wider text-amber-700 bg-amber-50 border border-amber-200 px-2.5 py-0.5 rounded-full">
                                <i class='bx bx-store-alt text-xs'></i> Konsinyasi UMKM
                            </span>
                            <h3 class="text-lg font-extrabold text-zinc-900 tracking-tight leading-snug">
                                Pajang Produk di Gerai Kampus.
                            </h3>
                            <p class="text-xs text-zinc-600 font-medium leading-relaxed">
                                Wadah wirausaha mahasiswa & UMKM. Titip barang dan pantau omzet penjualan harian secara digital.
                            </p>
                        </div>
                    </div>

                    <!-- Card 5: Portal 24/7 -->
                    <div class="rounded-3xl bg-zinc-50/80 border border-zinc-200/80 p-5 sm:p-6 shadow-sm hover:shadow-xl hover:border-purple-500/40 transition-all duration-300 flex flex-col justify-between space-y-5 group">
                        <div class="w-full aspect-[4/3] rounded-2xl bg-zinc-100 border border-zinc-200/60 overflow-hidden shadow-inner group-hover:scale-[1.02] transition-transform duration-500">
                            <img src="{{ asset('images/bento/bento-portal-43.jpeg') }}" alt="Portal Digital 24/7" class="w-full h-full object-cover">
                        </div>

                        <div class="space-y-2.5">
                            <span class="inline-flex items-center gap-1.5 text-[10px] font-extrabold uppercase tracking-wider text-purple-700 bg-purple-50 border border-purple-200 px-2.5 py-0.5 rounded-full">
                                <i class='bx bx-devices text-xs'></i> Portal Digital 24/7
                            </span>
                            <h3 class="text-lg font-extrabold text-zinc-900 tracking-tight leading-snug">
                                Satu Akun Akses Serba Bisa.
                            </h3>
                            <p class="text-xs text-zinc-600 font-medium leading-relaxed">
                                Pantau simpanan, poin belanja, hingga pencairan SHU dalam satu platform portal anggota yang cepat.
                            </p>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </section>

    <!-- Supplier Banner Section (Clean Split Layout: 100% Unobscured Image + Right Text Box) -->
    <section id="supplier" class="py-16 sm:py-24 bg-[#f5f5f7] relative overflow-hidden border-t border-zinc-200">
        <div class="max-w-6xl mx-auto px-4 sm:px-6">
            
            <div class="bg-white rounded-3xl sm:rounded-[2.5rem] border border-zinc-200/80 shadow-xl overflow-hidden grid grid-cols-1 lg:grid-cols-12 gap-0">
                <!-- Left Column: Unobscured 3D Image (7 cols) -->
                <div class="lg:col-span-7 aspect-[4/3] lg:aspect-auto w-full relative bg-zinc-100 overflow-hidden min-h-[300px] lg:min-h-[420px]">
                    <img src="{{ asset('images/bento/bento-supplier-43.jpeg') }}" alt="Mitra Supplier UMBandung" class="w-full h-full object-cover">
                </div>

                <!-- Right Column: Clean Content & CTA (5 cols) -->
                <div class="lg:col-span-5 p-8 sm:p-10 lg:p-12 flex flex-col justify-center space-y-6 bg-zinc-50/50">
                    <span class="inline-flex items-center gap-1.5 text-xs font-extrabold uppercase tracking-wider text-amber-700 bg-amber-50 border border-amber-200/80 px-3 py-1 rounded-full w-fit">
                        <i class='bx bx-store-alt text-sm'></i> Program Kemitraan UMKM
                    </span>
                    <h2 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-zinc-900 text-apple-headline tracking-tight leading-tight">
                        Dapatkan Ribuan Pembeli di Kampus UMBandung.
                    </h2>
                    <p class="text-xs sm:text-sm text-zinc-600 font-medium leading-relaxed">
                        Jangkau mahasiswa, dosen, dan staf UMBandung setiap hari. Pantau stok barang laku dan omzet harian secara transparan dari Dashboard Supplier.
                    </p>
                    <div class="pt-2">
                        <a href="{{ route('supplier.register') }}" class="apple-btn-blue px-6 py-3.5 text-sm font-bold w-full sm:w-auto inline-flex items-center justify-center gap-2 shadow-md">
                            <span>Daftar Supplier UMKM</span>
                            <i class='bx bx-right-arrow-alt text-xl'></i>
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- FAQ Accordion (Apple Minimalist Style) -->
    <section id="faq" class="py-16 sm:py-24 bg-white border-t border-zinc-200">
        <div class="max-w-4xl mx-auto px-4 sm:px-6">
            
            <div class="mb-8 sm:mb-12 text-center md:text-left">
                <h2 class="text-2xl sm:text-3xl md:text-4xl font-extrabold text-zinc-900 tracking-tight">Frequently Asked Questions</h2>
            </div>

            <div class="divide-y divide-zinc-200 border-t border-b border-zinc-200">
                
                <details class="group py-4 sm:py-6 cursor-pointer">
                    <summary class="flex justify-between items-center font-bold text-sm sm:text-base text-zinc-900 list-none gap-4">
                        <span>Siapa saja yang bisa mendaftar menjadi anggota Koperasi Bermadani?</span>
                        <i class='bx bx-plus text-lg sm:text-xl text-zinc-500 group-open:rotate-45 transition-transform flex-shrink-0'></i>
                    </summary>
                    <p class="mt-3 sm:mt-4 text-xs sm:text-sm text-zinc-600 leading-relaxed">
                        Seluruh Civitas Academica Universitas Muhammadiyah Bandung (UMBandung) meliputi mahasiswa terdaftar, dosen, serta staf/karyawan kampus.
                    </p>
                </details>

                <details class="group py-4 sm:py-6 cursor-pointer">
                    <summary class="flex justify-between items-center font-bold text-sm sm:text-base text-zinc-900 list-none gap-4">
                        <span>Apakah non-anggota bisa berbelanja di Bermadani Mart?</span>
                        <i class='bx bx-plus text-lg sm:text-xl text-zinc-500 group-open:rotate-45 transition-transform flex-shrink-0'></i>
                    </summary>
                    <p class="mt-3 sm:mt-4 text-xs sm:text-sm text-zinc-600 leading-relaxed">
                        Bisa. Bermadani Mart terbuka untuk umum. Namun, Anggota terdaftar akan memperoleh harga promo khusus anggota dan akumulasi poin dividen SHU.
                    </p>
                </details>

                <details class="group py-4 sm:py-6 cursor-pointer">
                    <summary class="flex justify-between items-center font-bold text-sm sm:text-base text-zinc-900 list-none gap-4">
                        <span>Bagaimana mekanisme titip jual barang untuk Supplier UMKM?</span>
                        <i class='bx bx-plus text-lg sm:text-xl text-zinc-500 group-open:rotate-45 transition-transform flex-shrink-0'></i>
                    </summary>
                    <p class="mt-3 sm:mt-4 text-xs sm:text-sm text-zinc-600 leading-relaxed">
                        Calon supplier dapat mengisi formulir pendaftaran supplier, melakukan verifikasi produk dengan pengurus, dan memantau laporan barang laku serta pencairan dana secara transparan melalui Dashboard Supplier.
                    </p>
                </details>

            </div>

        </div>
    </section>

    <!-- Apple Minimalist Footer -->
    <footer class="bg-[#f5f5f7] text-zinc-500 py-8 sm:py-12 border-t border-zinc-200 text-xs">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 flex flex-col sm:flex-row items-center justify-between gap-4 text-center sm:text-left">
            <div>
                <p class="font-semibold text-zinc-700 mb-0.5">{{ coop_config('legal_name', 'Koperasi Konsumen Syariah Berkah Solusi Madani') }}</p>
                <p class="text-[11px] sm:text-xs">Universitas Muhammadiyah Bandung (UMBandung) — Jl. Soekarno-Hatta No.752, Bandung</p>
            </div>
            <div class="flex items-center gap-6">
                <a href="{{ route('login') }}" class="hover:text-zinc-900 transition-colors">Portal Anggota</a>
                <a href="{{ route('supplier.register') }}" class="hover:text-zinc-900 transition-colors">Supplier</a>
            </div>
        </div>
    </footer>
</body>
</html>
