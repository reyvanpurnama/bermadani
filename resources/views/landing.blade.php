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

            <!-- Bento Grid 6 Columns Layout (Floating Text over 3D Images - No Dark Overlays) -->
            <div class="mt-8 sm:mt-12 grid grid-cols-1 gap-4 sm:gap-6 lg:grid-cols-6">
                
                <!-- Card 1: Bermadani Mart (3 Cols) -->
                <div class="group relative flex flex-col justify-end overflow-hidden rounded-3xl max-lg:rounded-t-3xl lg:rounded-tl-[2.5rem] bg-white border border-zinc-200/80 shadow-sm hover:border-zinc-300 hover:shadow-xl transition-all duration-500 min-h-[22rem] sm:min-h-[26rem] p-6 sm:p-8 lg:col-span-3">
                    <!-- 3D Illustration Background -->
                    <div class="absolute inset-0 bg-[url('/images/bento/bento-mart-43.jpeg')] bg-cover bg-center group-hover:scale-105 transition-transform duration-700"></div>
                    
                    <!-- Soft White Gradient for Maximum Text Legibility (No Dark Overlay) -->
                    <div class="absolute inset-0 bg-gradient-to-t from-white/90 via-white/40 to-transparent pointer-events-none"></div>

                    <!-- Text Floating Content -->
                    <div class="relative z-10">
                        <h3 class="text-xl sm:text-2xl font-bold tracking-tight text-zinc-900">
                            Belanja Harian, Untungnya Balik ke Kamu.
                        </h3>
                        <p class="mt-1.5 text-xs sm:text-sm text-zinc-700 font-medium leading-relaxed max-w-xl">
                            Belanja kebutuhan harian di minimarket UMBandung. Dapatkan harga khusus anggota & setiap rupiah transaksi diakumulasikan jadi dividen kamu.
                        </p>
                    </div>
                </div>

                <!-- Card 2: Simpanan Syariah (3 Cols) -->
                <div class="group relative flex flex-col justify-end overflow-hidden rounded-3xl lg:rounded-tr-[2.5rem] bg-white border border-zinc-200/80 shadow-sm hover:border-zinc-300 hover:shadow-xl transition-all duration-500 min-h-[22rem] sm:min-h-[26rem] p-6 sm:p-8 lg:col-span-3">
                    <!-- 3D Illustration Background -->
                    <div class="absolute inset-0 bg-[url('/images/bento/bento-savings-43.jpeg')] bg-cover bg-center group-hover:scale-105 transition-transform duration-700"></div>
                    
                    <!-- Soft White Gradient for Maximum Text Legibility (No Dark Overlay) -->
                    <div class="absolute inset-0 bg-gradient-to-t from-white/90 via-white/40 to-transparent pointer-events-none"></div>

                    <!-- Text Floating Content -->
                    <div class="relative z-10">
                        <h3 class="text-xl sm:text-2xl font-bold tracking-tight text-zinc-900">
                            Nabung Amanah Tanpa Biaya Admin Siluman.
                        </h3>
                        <p class="mt-1.5 text-xs sm:text-sm text-zinc-700 font-medium leading-relaxed max-w-xl">
                            Simpanan Pokok & Wajib berbasis akad Syariah. Bebas potongan bulanan misterius, tercatat transparan, & dipantau langsung dari portal.
                        </p>
                    </div>
                </div>

                <!-- Card 3: Bagi Hasil SHU (2 Cols) -->
                <div class="group relative flex flex-col justify-end overflow-hidden rounded-3xl lg:rounded-bl-[2.5rem] bg-white border border-zinc-200/80 shadow-sm hover:border-zinc-300 hover:shadow-xl transition-all duration-500 min-h-[20rem] sm:min-h-[24rem] p-6 sm:p-8 lg:col-span-2">
                    <!-- 3D Illustration Background -->
                    <div class="absolute inset-0 bg-[url('/images/bento/bento-shu-43.jpeg')] bg-cover bg-center group-hover:scale-105 transition-transform duration-700"></div>
                    
                    <!-- Soft White Gradient for Maximum Text Legibility (No Dark Overlay) -->
                    <div class="absolute inset-0 bg-gradient-to-t from-white/90 via-white/40 to-transparent pointer-events-none"></div>

                    <!-- Text Floating Content -->
                    <div class="relative z-10">
                        <h3 class="text-lg sm:text-xl font-bold tracking-tight text-zinc-900">
                            Keuntungan Toko Dibagi ke Anggota.
                        </h3>
                        <p class="mt-1.5 text-xs sm:text-sm text-zinc-700 font-medium leading-relaxed">
                            Keuntungan usaha minimarket dikembalikan secara adil & proporsional ke seluruh anggota aktif.
                        </p>
                    </div>
                </div>

                <!-- Card 4: Titip Jual Supplier (2 Cols) -->
                <div class="group relative flex flex-col justify-end overflow-hidden rounded-3xl bg-white border border-zinc-200/80 shadow-sm hover:border-zinc-300 hover:shadow-xl transition-all duration-500 min-h-[20rem] sm:min-h-[24rem] p-6 sm:p-8 lg:col-span-2">
                    <!-- 3D Illustration Background -->
                    <div class="absolute inset-0 bg-[url('/images/bento/bento-supplier-43.jpeg')] bg-cover bg-center group-hover:scale-105 transition-transform duration-700"></div>
                    
                    <!-- Soft White Gradient for Maximum Text Legibility (No Dark Overlay) -->
                    <div class="absolute inset-0 bg-gradient-to-t from-white/90 via-white/40 to-transparent pointer-events-none"></div>

                    <!-- Text Floating Content -->
                    <div class="relative z-10">
                        <h3 class="text-lg sm:text-xl font-bold tracking-tight text-zinc-900">
                            Pajang Produk di Gerai Kampus.
                        </h3>
                        <p class="mt-1.5 text-xs sm:text-sm text-zinc-700 font-medium leading-relaxed">
                            Wadah wirausaha mahasiswa & UMKM. Titip barang dan pantau omzet penjualan harian secara digital.
                        </p>
                    </div>
                </div>

                <!-- Card 5: Portal Keanggotaan (2 Cols) -->
                <div class="group relative flex flex-col justify-end overflow-hidden rounded-3xl max-lg:rounded-b-3xl lg:rounded-br-[2.5rem] bg-white border border-zinc-200/80 shadow-sm hover:border-zinc-300 hover:shadow-xl transition-all duration-500 min-h-[20rem] sm:min-h-[24rem] p-6 sm:p-8 lg:col-span-2">
                    <!-- 3D Illustration Background -->
                    <div class="absolute inset-0 bg-[url('/images/bento/bento-portal-43.jpeg')] bg-cover bg-center group-hover:scale-105 transition-transform duration-700"></div>
                    
                    <!-- Soft White Gradient for Maximum Text Legibility (No Dark Overlay) -->
                    <div class="absolute inset-0 bg-gradient-to-t from-white/90 via-white/40 to-transparent pointer-events-none"></div>

                    <!-- Text Floating Content -->
                    <div class="relative z-10">
                        <h3 class="text-lg sm:text-xl font-bold tracking-tight text-zinc-900">
                            Satu Akun Akses Serba Bisa.
                        </h3>
                        <p class="mt-1.5 text-xs sm:text-sm text-zinc-700 font-medium leading-relaxed">
                            Pantau simpanan, poin belanja, hingga pencairan SHU dalam satu platform portal anggota yang cepat.
                        </p>
                    </div>
                </div>

            </div>

        </div>
    </section>

    <!-- Supplier Banner Section (Full 2400x1792 Uncropped Image + Floating Glassmorphism Overlay) -->
    <section id="supplier" class="py-16 sm:py-24 bg-[#f5f5f7] relative overflow-hidden border-t border-zinc-200">
        <div class="max-w-6xl mx-auto px-4 sm:px-6">
            
            <!-- 3D Storefront Image Container (Strict 2400x1792 / 4:3 Uncropped Aspect Ratio) -->
            <div class="relative overflow-hidden rounded-3xl lg:rounded-[2.5rem] border border-zinc-200/80 shadow-2xl bg-white aspect-[2400/1792] w-full flex items-center justify-center p-4 sm:p-8 md:p-12">
                
                <!-- Full 2400x1792 Uncropped Image Background -->
                <img src="{{ asset('images/bento/bento-supplier.jpeg') }}" alt="Mitra Supplier UMBandung" class="absolute inset-0 w-full h-full object-cover pointer-events-none">

                <!-- Ambient Subtle Overlay for Depth -->
                <div class="absolute inset-0 bg-black/5 pointer-events-none"></div>

                <!-- Glassmorphism Card Overlay (Pure 0% Opacity Frosted Glass) -->
                <div class="relative z-10 w-full p-6 sm:p-10 md:p-14 rounded-2xl sm:rounded-3xl bg-white/0 backdrop-blur-2xl border border-white/80 shadow-2xl shadow-black/10 transition-all duration-500 hover:bg-white/10">
                    
                    <!-- Content Layout -->
                    <div class="flex flex-col md:flex-row items-center justify-between gap-6 sm:gap-10 text-center md:text-left">
                        
                        <div class="space-y-3.5 max-w-2xl">
                            <h2 class="text-3xl sm:text-4xl md:text-5xl font-extrabold text-zinc-900 text-apple-headline tracking-tight leading-tight">
                                Dapatkan Ribuan Pembeli di Kampus UMBandung.
                            </h2>
                            <p class="text-sm sm:text-base md:text-lg text-zinc-800 leading-relaxed font-semibold max-w-xl">
                                Jangkau mahasiswa, dosen, dan staf UMBandung setiap hari. Pantau stok barang laku dan omzet harian secara transparan dari Dashboard Supplier.
                            </p>
                        </div>

                        <!-- Supplier CTA Button -->
                        <div class="w-full md:w-auto flex-shrink-0">
                            <a href="{{ route('supplier.register') }}" class="w-full md:w-auto glass-cta-primary px-8 sm:px-10 py-4 sm:py-5 font-bold text-sm sm:text-base shadow-xl flex items-center justify-center gap-3 group/btn">
                                <span>Daftar Supplier UMKM</span>
                                <i class='bx bx-right-arrow-alt text-xl sm:text-2xl group-hover/btn:translate-x-1.5 transition-transform duration-300'></i>
                            </a>
                        </div>

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
