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
