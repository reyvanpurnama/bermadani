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
