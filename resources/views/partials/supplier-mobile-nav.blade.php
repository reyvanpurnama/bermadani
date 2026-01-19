<nav
    class="lg:hidden fixed bottom-0 left-0 w-full bg-white dark:bg-darkCard border-t border-slate-200 dark:border-slate-800 z-50 transition-all duration-300 safe-area-bottom">
    <div class="grid grid-cols-3 h-[60px] items-center">
        {{-- 1. Beranda --}}
        <a href="{{ route('supplier.dashboard') }}"
            class="flex flex-col items-center justify-center gap-1 h-full w-full {{ request()->routeIs('supplier.dashboard') ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400 hover:text-slate-600 dark:hover:text-slate-300' }}">
            <i
                class='bx {{ request()->routeIs('supplier.dashboard') ? 'bxs-dashboard' : 'bx-grid-alt' }} text-[24px] mb-0.5'></i>
            <span class="text-[10px] font-medium tracking-wide">Beranda</span>
        </a>

        {{-- 2. Produk --}}
        <a href="{{ route('supplier.products.index') }}"
            class="flex flex-col items-center justify-center gap-1 h-full w-full {{ request()->routeIs('supplier.products*') ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400 hover:text-slate-600 dark:hover:text-slate-300' }}">
            <i class='bx {{ request()->routeIs('supplier.products*') ? 'bxs-box' : 'bx-box' }} text-[24px] mb-0.5'></i>
            <span class="text-[10px] font-medium tracking-wide">Produk</span>
        </a>

        {{-- 3. Profil --}}
        <a href="{{ route('supplier.profile') }}"
            class="flex flex-col items-center justify-center gap-1 h-full w-full {{ request()->routeIs('supplier.profile') ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400 hover:text-slate-600 dark:hover:text-slate-300' }}">
            <i
                class='bx {{ request()->routeIs('supplier.profile') ? 'bxs-user-circle' : 'bx-user-circle' }} text-[24px] mb-0.5'></i>
            <span class="text-[10px] font-medium tracking-wide">Profil</span>
        </a>
    </div>
</nav>