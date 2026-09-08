<nav
    class="lg:hidden fixed bottom-0 left-0 w-full bg-white/80 dark:bg-zinc-900/80 backdrop-blur-xl border-t border-zinc-200/80 dark:border-zinc-800/80 z-50 transition-all duration-300 safe-area-bottom shadow-2xl">
    <div class="grid grid-cols-3 h-[62px] items-center max-w-md mx-auto">
        {{-- 1. Beranda --}}
        <a href="{{ route('supplier.dashboard') }}"
            class="flex flex-col items-center justify-center gap-1 h-full w-full transition-colors {{ request()->routeIs('supplier.dashboard') ? 'text-[#155A6B] dark:text-teal-400 font-bold' : 'text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200' }}">
            <i
                class='bx {{ request()->routeIs('supplier.dashboard') ? 'bxs-dashboard' : 'bx-grid-alt' }} text-[22px]'></i>
            <span class="text-[10px] tracking-tight">Beranda</span>
        </a>

        {{-- 2. Produk --}}
        <a href="{{ route('supplier.products.index') }}"
            class="flex flex-col items-center justify-center gap-1 h-full w-full transition-colors {{ request()->routeIs('supplier.products*') ? 'text-[#155A6B] dark:text-teal-400 font-bold' : 'text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200' }}">
            <i class='bx {{ request()->routeIs('supplier.products*') ? 'bxs-box' : 'bx-box' }} text-[22px]'></i>
            <span class="text-[10px] tracking-tight">Produk Saya</span>
        </a>

        {{-- 3. Profil --}}
        <a href="{{ route('supplier.profile') }}"
            class="flex flex-col items-center justify-center gap-1 h-full w-full transition-colors {{ request()->routeIs('supplier.profile') ? 'text-[#155A6B] dark:text-teal-400 font-bold' : 'text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200' }}">
            <i
                class='bx {{ request()->routeIs('supplier.profile') ? 'bxs-user-circle' : 'bx-user-circle' }} text-[22px]'></i>
            <span class="text-[10px] tracking-tight">Profil</span>
        </a>
    </div>
</nav>