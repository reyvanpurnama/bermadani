<nav
    class="lg:hidden fixed bottom-0 left-0 w-full bg-white dark:bg-darkCard border-t border-slate-200 dark:border-slate-800 z-50 transition-all duration-300">
    <div class="grid grid-cols-5 h-16 relative">
        {{-- 1. Beranda --}}
        <a href="{{ route('member.dashboard') }}"
            class="flex flex-col items-center justify-center gap-1 {{ request()->routeIs('member.dashboard') ? 'text-primary' : 'text-slate-400 hover:text-slate-600 dark:hover:text-slate-300' }}">
            <i class='bx {{ request()->routeIs('member.dashboard') ? 'bxs-home' : 'bx-home' }} text-2xl mb-0.5'></i>
            <span class="text-[10px] font-medium">Beranda</span>
        </a>

        {{-- 2. Belanja --}}
        <a href="{{ route('member.products') }}"
            class="flex flex-col items-center justify-center gap-1 {{ request()->routeIs('member.products') ? 'text-primary' : 'text-slate-400 hover:text-slate-600 dark:hover:text-slate-300' }}">
            <i class='bx {{ request()->routeIs('member.products') ? 'bxs-store' : 'bx-store' }} text-2xl mb-0.5'></i>
            <span class="text-[10px] font-medium">Belanja</span>
        </a>

        {{-- 3. SCAN / PAY (Center) --}}
        <div class="relative flex items-center justify-center -top-5">
            <a href="#"
                class="w-14 h-14 bg-primary text-white rounded-full flex items-center justify-center shadow-lg shadow-blue-500/40 border-4 border-slate-50 dark:border-darkBg transform active:scale-95 transition-transform"
                onclick="alert('Fitur Scan QRIS segera hadir!')">
                <i class='bx bx-qr-scan text-2xl'></i>
            </a>
        </div>

        {{-- 4. Riwayat --}}
        <a href="{{ route('member.transactions') }}"
            class="flex flex-col items-center justify-center gap-1 {{ request()->routeIs('member.transactions') ? 'text-primary' : 'text-slate-400 hover:text-slate-600 dark:hover:text-slate-300' }}">
            <i
                class='bx {{ request()->routeIs('member.transactions') ? 'bxs-receipt' : 'bx-receipt' }} text-2xl mb-0.5'></i>
            <span class="text-[10px] font-medium">Riwayat</span>
        </a>

        {{-- 5. Profil --}}
        <a href="{{ route('member.profile') }}"
            class="flex flex-col items-center justify-center gap-1 {{ request()->routeIs('member.profile') ? 'text-primary' : 'text-slate-400 hover:text-slate-600 dark:hover:text-slate-300' }}">
            <i class='bx {{ request()->routeIs('member.profile') ? 'bxs-user' : 'bx-user' }} text-2xl mb-0.5'></i>
            <span class="text-[10px] font-medium">Profil</span>
        </a>
    </div>
</nav>