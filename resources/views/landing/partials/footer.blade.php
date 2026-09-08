<!-- Apple Minimalist Footer -->
<footer class="bg-[#f5f5f7] text-zinc-500 py-8 sm:py-12 border-t border-zinc-200/80 text-xs">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 flex flex-col sm:flex-row items-center justify-between gap-6 text-center sm:text-left">
        <div class="flex flex-col sm:flex-row items-center gap-3">
            <!-- Official Brand Logo (Same as Headbar) -->
            <img src="{{ asset('images/logo-koperasi.png') }}" alt="Logo Koperasi Bermadani" class="w-8 h-8 object-contain flex-shrink-0">
            <div>
                <p class="font-bold text-zinc-800 text-xs sm:text-sm mb-0.5">{{ coop_config('legal_name', 'Koperasi Konsumen Syariah Berkah Solusi Madani') }}</p>
                <p class="text-[11px] sm:text-xs text-zinc-500">Universitas Muhammadiyah Bandung (UMBandung) — Jl. Soekarno-Hatta No.752, Bandung</p>
            </div>
        </div>
        <div class="flex items-center gap-6 font-semibold">
            <a href="{{ route('login') }}" class="hover:text-[#155A6B] transition-colors">Portal Anggota</a>
            <a href="{{ route('supplier.register') }}" class="hover:text-[#155A6B] transition-colors">Daftar Supplier</a>
        </div>
    </div>
</footer>
