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
        [x-cloak] { display: none !important; }

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

    <!-- 1. CORE PRODUCTS & SERVICES SHOWCASE (Pattern 1 from Reference) -->
    <section id="features" class="py-16 sm:py-24 bg-white text-zinc-900 border-t border-zinc-200 relative overflow-hidden">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 relative z-10">
            
            <div class="mb-10 sm:mb-14 flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div>
                    <span class="inline-flex items-center gap-1.5 text-[10px] font-extrabold uppercase tracking-wider text-[#155A6B] bg-teal-50 border border-teal-200 px-3 py-1 rounded-full mb-3">
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

            <!-- Interactive Split Showcase Card Container (Pattern 1 Layout) -->
            <div class="bg-zinc-50/90 rounded-3xl sm:rounded-[2.5rem] border border-zinc-200/90 p-6 sm:p-10 shadow-lg" x-data="{ activeTab: 'mart' }">
                <div class="flex flex-col lg:flex-row gap-8 items-stretch w-full">
                    
                    <!-- Left Side: Interactive Row Selectors (5/12 width) -->
                    <div class="w-full lg:w-5/12 flex-shrink-0 flex flex-col justify-center space-y-4">
                        
                        <!-- Selector 1: Bermadani Mart -->
                        <button @click="activeTab = 'mart'" 
                            :class="activeTab === 'mart' ? 'bg-[#155A6B] text-white border-[#155A6B] shadow-md scale-[1.02]' : 'bg-white text-zinc-800 border-zinc-200 hover:border-zinc-300 hover:bg-zinc-100/80'"
                            class="w-full text-left p-5 rounded-2xl border transition-all duration-300 flex items-center justify-between group cursor-pointer">
                            <div class="space-y-1 pr-4">
                                <span :class="activeTab === 'mart' ? 'text-teal-200 bg-white/20' : 'text-[#155A6B] bg-teal-50 border border-teal-200'" class="inline-flex items-center gap-1.5 text-[10px] font-extrabold uppercase tracking-wider px-2.5 py-0.5 rounded-full">
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
                            :class="activeTab === 'savings' ? 'bg-[#155A6B] text-white border-[#155A6B] shadow-md scale-[1.02]' : 'bg-white text-zinc-800 border-zinc-200 hover:border-zinc-300 hover:bg-zinc-100/80'"
                            class="w-full text-left p-5 rounded-2xl border transition-all duration-300 flex items-center justify-between group cursor-pointer">
                            <div class="space-y-1 pr-4">
                                <span :class="activeTab === 'savings' ? 'text-teal-200 bg-white/20' : 'text-blue-600 bg-blue-50 border border-blue-200'" class="inline-flex items-center gap-1.5 text-[10px] font-extrabold uppercase tracking-wider px-2.5 py-0.5 rounded-full">
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
                            :class="activeTab === 'shu' ? 'bg-[#155A6B] text-white border-[#155A6B] shadow-md scale-[1.02]' : 'bg-white text-zinc-800 border-zinc-200 hover:border-zinc-300 hover:bg-zinc-100/80'"
                            class="w-full text-left p-5 rounded-2xl border transition-all duration-300 flex items-center justify-between group cursor-pointer">
                            <div class="space-y-1 pr-4">
                                <span :class="activeTab === 'shu' ? 'text-teal-200 bg-white/20' : 'text-purple-700 bg-purple-50 border border-purple-200'" class="inline-flex items-center gap-1.5 text-[10px] font-extrabold uppercase tracking-wider px-2.5 py-0.5 rounded-full">
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
                    <div class="w-full lg:w-7/12 flex-grow min-w-0 bg-white rounded-2xl border border-zinc-200/80 p-6 sm:p-8 flex flex-col justify-between shadow-sm relative overflow-hidden">
                        
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
                            <div class="w-full aspect-[16/9] rounded-xl bg-zinc-100 border border-zinc-200/60 overflow-hidden shadow-inner">
                                <img src="{{ asset('images/bento/bento-mart-43.jpeg') }}" alt="Bermadani Mart" class="w-full h-full object-cover">
                            </div>

                            <!-- Key Metric Pills -->
                            <div class="grid grid-cols-2 gap-3 pt-2">
                                <div class="p-3 bg-zinc-50 rounded-xl border border-zinc-200/60">
                                    <p class="text-[10px] font-extrabold uppercase text-zinc-500">Benefit Utama</p>
                                    <p class="text-xs font-bold text-zinc-800 mt-0.5">Diskon Khusus Anggota</p>
                                </div>
                                <div class="p-3 bg-zinc-50 rounded-xl border border-zinc-200/60">
                                    <p class="text-[10px] font-extrabold uppercase text-zinc-500">Transparansi POS</p>
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
                            <div class="w-full aspect-[16/9] rounded-xl bg-zinc-100 border border-zinc-200/60 overflow-hidden shadow-inner">
                                <img src="{{ asset('images/bento/bento-savings-43.jpeg') }}" alt="Simpanan Syariah" class="w-full h-full object-cover">
                            </div>

                            <!-- Key Metric Pills -->
                            <div class="grid grid-cols-2 gap-3 pt-2">
                                <div class="p-3 bg-zinc-50 rounded-xl border border-zinc-200/60">
                                    <p class="text-[10px] font-extrabold uppercase text-zinc-500">Biaya Administrasi</p>
                                    <p class="text-xs font-bold text-emerald-600 mt-0.5">Rp 0 / Bebas Potongan</p>
                                </div>
                                <div class="p-3 bg-zinc-50 rounded-xl border border-zinc-200/60">
                                    <p class="text-[10px] font-extrabold uppercase text-zinc-500">Akad Syariah</p>
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
                            <div class="w-full aspect-[16/9] rounded-xl bg-zinc-100 border border-zinc-200/60 overflow-hidden shadow-inner">
                                <img src="{{ asset('images/bento/bento-shu-43.jpeg') }}" alt="Bagi Hasil SHU" class="w-full h-full object-cover">
                            </div>

                            <!-- Key Metric Pills -->
                            <div class="grid grid-cols-2 gap-3 pt-2">
                                <div class="p-3 bg-zinc-50 rounded-xl border border-zinc-200/60">
                                    <p class="text-[10px] font-extrabold uppercase text-zinc-500">Pembagian SHU</p>
                                    <p class="text-xs font-bold text-[#155A6B] mt-0.5">Tiap Akhir Tahun Buku</p>
                                </div>
                                <div class="p-3 bg-zinc-50 rounded-xl border border-zinc-200/60">
                                    <p class="text-[10px] font-extrabold uppercase text-zinc-500">Audit Laporan</p>
                                    <p class="text-xs font-bold text-zinc-800 mt-0.5">Transparan di Dashboard</p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- 2. EQUIPMENT & PRODUCT SOURCING FOR SUPPLIERS (Pattern 2 from Reference) -->
    <section id="supplier" class="py-16 sm:py-24 bg-[#f5f5f7] relative overflow-hidden border-t border-zinc-200">
        <div class="max-w-6xl mx-auto px-4 sm:px-6">
            
            <div class="mb-10 sm:mb-14 flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div>
                    <span class="inline-flex items-center gap-1.5 text-[10px] font-extrabold uppercase tracking-wider text-amber-700 bg-amber-50 border border-amber-200 px-3 py-1 rounded-full mb-3">
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
                <div class="rounded-3xl bg-white border border-zinc-200/90 p-6 shadow-sm hover:shadow-xl hover:border-amber-500/40 transition-all duration-300 flex flex-col justify-between space-y-6 group">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-amber-700 bg-amber-50 border border-amber-200 px-3 py-1 rounded-full">
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

                    <!-- Crisp Bottom 3D Frame (Unobscured 100% full visual) -->
                    <div class="w-full aspect-[4/3] rounded-2xl bg-zinc-100 border border-zinc-200/60 overflow-hidden shadow-inner group-hover:scale-[1.02] transition-transform duration-500">
                        <img src="{{ asset('images/bento/bento-supplier-43.jpeg') }}" alt="Pendaftaran SKU Supplier" class="w-full h-full object-cover">
                    </div>
                </div>

                <!-- Card 2: Step 02 -->
                <div class="rounded-3xl bg-white border border-zinc-200/90 p-6 shadow-sm hover:shadow-xl hover:border-teal-500/40 transition-all duration-300 flex flex-col justify-between space-y-6 group">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-[#155A6B] bg-teal-50 border border-teal-200 px-3 py-1 rounded-full">
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

                    <!-- Crisp Bottom 3D Frame -->
                    <div class="w-full aspect-[4/3] rounded-2xl bg-zinc-100 border border-zinc-200/60 overflow-hidden shadow-inner group-hover:scale-[1.02] transition-transform duration-500">
                        <img src="{{ asset('images/bento/bento-mart-43.jpeg') }}" alt="Penjualan POS Kampus" class="w-full h-full object-cover">
                    </div>
                </div>

                <!-- Card 3: Step 03 -->
                <div class="rounded-3xl bg-white border border-zinc-200/90 p-6 shadow-sm hover:shadow-xl hover:border-purple-500/40 transition-all duration-300 flex flex-col justify-between space-y-6 group">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-purple-700 bg-purple-50 border border-purple-200 px-3 py-1 rounded-full">
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

                    <!-- Crisp Bottom 3D Frame -->
                    <div class="w-full aspect-[4/3] rounded-2xl bg-zinc-100 border border-zinc-200/60 overflow-hidden shadow-inner group-hover:scale-[1.02] transition-transform duration-500">
                        <img src="{{ asset('images/bento/bento-portal-43.jpeg') }}" alt="Dashboard Sales Supplier" class="w-full h-full object-cover">
                    </div>
                </div>

            </div>

        </div>
    </section>

    <!-- 3. OUR STRATEGIC ADVANTAGES SPOTLIGHT (Pattern 3 from Reference) -->
    <section id="advantages" class="py-16 sm:py-24 bg-white border-t border-zinc-200 relative overflow-hidden">
        <div class="max-w-6xl mx-auto px-4 sm:px-6">
            
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8">
                <div>
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-[#155A6B] bg-teal-50 border border-teal-200 px-3 py-1 rounded-full">
                        OUR STRATEGIC ADVANTAGES
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
            <div class="relative rounded-3xl lg:rounded-[2.5rem] border border-zinc-200/80 shadow-2xl overflow-hidden bg-zinc-950 min-h-[520px] flex flex-col justify-end p-6 sm:p-8 md:p-10">
                
                <!-- Background Image Graphic -->
                <img src="{{ asset('images/hero-landscape.jpeg') }}" alt="Strategic Advantages" class="absolute inset-0 w-full h-full object-cover opacity-35 filter saturate-150">
                
                <!-- Subtle Ambient Dark Gradient -->
                <div class="absolute inset-0 bg-gradient-to-t from-zinc-950 via-zinc-950/40 to-transparent"></div>

                <!-- Bottom 3 Floating Frosted-Glass Cards -->
                <div class="relative z-10 grid grid-cols-1 md:grid-cols-3 gap-5">
                    
                    <!-- Floating Card 1 -->
                    <div class="p-6 rounded-2xl bg-white/10 backdrop-blur-xl border border-white/20 text-white shadow-2xl space-y-4 hover:bg-white/15 transition-all duration-300">
                        <div class="w-10 h-10 rounded-xl bg-teal-500/20 border border-teal-400/30 flex items-center justify-center text-teal-300 text-xl font-bold">
                            <i class='bx bx-check-shield'></i>
                        </div>
                        <div class="space-y-2">
                            <h3 class="text-lg font-extrabold tracking-tight">100% Prinsip Syariah</h3>
                            <ul class="space-y-1.5 text-xs text-zinc-200 font-medium">
                                <li class="flex items-center gap-2"><i class='bx bx-check text-teal-400 text-sm'></i> Bebas Riba, Gharar, & Maysir</li>
                                <li class="flex items-center gap-2"><i class='bx bx-check text-teal-400 text-sm'></i> Akad Mudharabah & Murabahah</li>
                                <li class="flex items-center gap-2"><i class='bx bx-check text-teal-400 text-sm'></i> Transparan Bagi Hasil SHU</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Floating Card 2 -->
                    <div class="p-6 rounded-2xl bg-white/10 backdrop-blur-xl border border-white/20 text-white shadow-2xl space-y-4 hover:bg-white/15 transition-all duration-300">
                        <div class="w-10 h-10 rounded-xl bg-blue-500/20 border border-blue-400/30 flex items-center justify-center text-blue-300 text-xl font-bold">
                            <i class='bx bx-devices'></i>
                        </div>
                        <div class="space-y-2">
                            <h3 class="text-lg font-extrabold tracking-tight">Transparansi Digital 24/7</h3>
                            <ul class="space-y-1.5 text-xs text-zinc-200 font-medium">
                                <li class="flex items-center gap-2"><i class='bx bx-check text-blue-400 text-sm'></i> Portal Mandiri Anggota & Supplier</li>
                                <li class="flex items-center gap-2"><i class='bx bx-check text-blue-400 text-sm'></i> Realtime Saldo & Mutasi Digital</li>
                                <li class="flex items-center gap-2"><i class='bx bx-check text-blue-400 text-sm'></i> Riwayat Transaksi POS Akurat</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Floating Card 3 -->
                    <div class="p-6 rounded-2xl bg-white/10 backdrop-blur-xl border border-white/20 text-white shadow-2xl space-y-4 hover:bg-white/15 transition-all duration-300">
                        <div class="w-10 h-10 rounded-xl bg-purple-500/20 border border-purple-400/30 flex items-center justify-center text-purple-300 text-xl font-bold">
                            <i class='bx bx-buildings'></i>
                        </div>
                        <div class="space-y-2">
                            <h3 class="text-lg font-extrabold tracking-tight">Dividen Kembali ke Kampus</h3>
                            <ul class="space-y-1.5 text-xs text-zinc-200 font-medium">
                                <li class="flex items-center gap-2"><i class='bx bx-check text-purple-400 text-sm'></i> SHU Dibagikan Adil Tiap Tahun</li>
                                <li class="flex items-center gap-2"><i class='bx bx-check text-purple-400 text-sm'></i> Dukung Wirausaha Mahasiswa</li>
                                <li class="flex items-center gap-2"><i class='bx bx-check text-purple-400 text-sm'></i> Ekosistem Mandiri UMBandung</li>
                            </ul>
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
