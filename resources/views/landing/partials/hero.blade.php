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
