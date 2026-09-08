<!-- STEVE JOBS / FIGMA DUAL HERO SECTION (Left-Aligned Text & Dual Landscape/Portrait Graphics) -->
<section id="overview" class="min-h-[92dvh] pt-24 pb-16 sm:pt-32 sm:pb-24 md:pt-36 md:pb-28 text-left bg-[#f5f5f7] overflow-hidden relative border-b border-zinc-200/80 flex items-center">
    <!-- Mobile 9:16 Portrait Hero Background Graphic -->
    <div class="absolute inset-0 bg-[url('/images/hero-portrait.jpeg')] bg-cover bg-center md:hidden opacity-100 pointer-events-none transition-opacity duration-500"></div>

    <!-- Desktop 16:9 Landscape Hero Background Graphic -->
    <div class="absolute inset-0 bg-[url('/images/hero-landscape.jpeg')] bg-cover bg-right hidden md:block opacity-100 pointer-events-none transition-opacity duration-500"></div>

    <!-- Readability Soft Gradient Overlay -->
    <div class="absolute inset-0 bg-gradient-to-r from-[#f5f5f7] via-[#f5f5f7]/90 to-transparent md:via-[#f5f5f7]/80 md:to-transparent pointer-events-none"></div>

    <div class="max-w-6xl mx-auto px-4 sm:px-6 md:px-8 relative z-10 w-full">
        <div class="max-w-xl lg:max-w-2xl space-y-4 sm:space-y-6">
            
            <!-- Top Announcement Pill Badge -->
            <div>
                <span class="inline-flex items-center gap-2 text-xs font-extrabold tracking-wide uppercase text-[#155A6B] bg-[#155A6B]/10 px-3.5 py-1.5 rounded-full shadow-2xs">
                    <i class='bx bx-check-shield text-base text-[#155A6B]'></i>
                    <span>Ekosistem Ekonomi Syariah Kampus UMBandung</span>
                </span>
            </div>

            <!-- Headline -->
            <h1 class="text-3xl sm:text-5xl md:text-6xl lg:text-7xl font-extrabold text-zinc-900 text-apple-headline tracking-tight break-words leading-[1.08] sm:leading-[1.04]">
                {{ coop_config('name', 'Koperasi Bermadani') }}
            </h1>

            <!-- Value Proposition Sub-Headline -->
            <p class="text-lg sm:text-2xl md:text-3xl lg:text-4xl font-bold text-[#155A6B] leading-snug">
                Didesain untuk Ekonomi Kampus UMBandung.
            </p>

            <!-- Human Benefit Subtext -->
            <p class="text-xs sm:text-base md:text-lg text-zinc-600 font-normal leading-relaxed max-w-lg sm:max-w-xl">
                Wadah resmi civitas akademika UMBandung. Belanja harian berpoin di Bermadani Mart, simpanan syariah amanah bebas potongan, dan bagi hasil SHU yang kembali ke kantong kamu.
            </p>

            <!-- Action Buttons (Dual Glassmorphism CTAs) -->
            <div class="pt-3 sm:pt-5 flex flex-wrap items-center gap-3 sm:gap-4">
                <a href="{{ route('login') }}" class="glass-cta-primary px-7 py-3.5 sm:px-9 sm:py-4 text-sm sm:text-base font-bold gap-2">
                    <span>Masuk Portal Anggota</span>
                    <i class='bx bx-right-arrow-alt text-lg sm:text-xl'></i>
                </a>
                
                <a href="{{ route('supplier.register') }}" class="glass-cta-secondary px-6 py-3.5 sm:px-7 sm:py-4 text-sm sm:text-base font-bold gap-2">
                    <i class='bx bx-store-alt text-lg'></i>
                    <span>Daftar Supplier UMKM</span>
                </a>
            </div>

            <!-- Trust & Feature Badges -->
            <div class="pt-4 sm:pt-6 flex flex-wrap items-center gap-x-6 gap-y-2 border-t border-zinc-200/60 text-xs font-semibold text-zinc-600">
                <div class="flex items-center gap-1.5">
                    <i class='bx bx-check-circle text-emerald-600 text-base'></i>
                    <span>0% Admin Siluman</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <i class='bx bx-check-circle text-[#155A6B] text-base'></i>
                    <span>Dividen SHU Transparan</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <i class='bx bx-check-circle text-purple-600 text-base'></i>
                    <span>Kasir POS 24/7</span>
                </div>
            </div>

        </div>
    </div>
</section>

