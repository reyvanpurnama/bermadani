<nav
    class="lg:hidden fixed bottom-0 left-0 w-full bg-white dark:bg-darkCard border-t border-slate-200 dark:border-slate-800 z-50 transition-all duration-300 safe-area-bottom">
    <div class="grid grid-cols-5 h-[60px] items-center">
        {{-- 1. Dasbor --}}
        <a href="{{ route('supplier.dashboard') }}"
            class="flex flex-col items-center justify-center gap-1 h-full w-full {{ request()->routeIs('supplier.dashboard') ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400 hover:text-slate-600 dark:hover:text-slate-300' }}">
            <i
                class='bx {{ request()->routeIs('supplier.dashboard') ? 'bxs-dashboard' : 'bx-grid-alt' }} text-[22px] mb-0.5'></i>
            <span class="text-[10px] font-medium tracking-wide">Dasbor</span>
        </a>

        {{-- 2. Produk --}}
        <a href="{{ route('supplier.products.index') }}"
            class="flex flex-col items-center justify-center gap-1 h-full w-full {{ request()->routeIs('supplier.products*') ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400 hover:text-slate-600 dark:hover:text-slate-300' }}">
            <i class='bx {{ request()->routeIs('supplier.products*') ? 'bxs-box' : 'bx-box' }} text-[22px] mb-0.5'></i>
            <span class="text-[10px] font-medium tracking-wide">Produk</span>
        </a>

        {{-- 3. Restock --}}
        <a href="{{ route('supplier.restock') }}"
            class="flex flex-col items-center justify-center gap-1 h-full w-full relative {{ request()->routeIs('supplier.restock') ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400 hover:text-slate-600 dark:hover:text-slate-300' }}">
            <div class="relative">
                <i
                    class='bx {{ request()->routeIs('supplier.restock') ? 'bxs-package' : 'bx-package' }} text-[22px] mb-0.5'></i>
                @php
                    $requestedBatchCount = \App\Models\ConsignmentBatch::where('supplierId', auth()->guard('supplier')->id())->where('status', 'REQUESTED')->count();
                @endphp
                @if($requestedBatchCount > 0)
                    <span
                        class="absolute -top-1 -right-1 w-2.5 h-2.5 bg-rose-500 rounded-full border-2 border-white dark:border-darkCard"></span>
                @endif
            </div>
            <span class="text-[10px] font-medium tracking-wide">Restock</span>
        </a>

        {{-- 4. Laporan --}}
        <a href="{{ route('supplier.sales') }}"
            class="flex flex-col items-center justify-center gap-1 h-full w-full {{ request()->routeIs('supplier.sales') ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400 hover:text-slate-600 dark:hover:text-slate-300' }}">
            <i
                class='bx {{ request()->routeIs('supplier.sales') ? 'bxs-report' : 'bx-line-chart' }} text-[22px] mb-0.5'></i>
            <span class="text-[10px] font-medium tracking-wide">Laporan</span>
        </a>

        {{-- 5. Profil --}}
        <a href="{{ route('supplier.profile') }}"
            class="flex flex-col items-center justify-center gap-1 h-full w-full {{ request()->routeIs('supplier.profile') ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400 hover:text-slate-600 dark:hover:text-slate-300' }}">
            <i
                class='bx {{ request()->routeIs('supplier.profile') ? 'bxs-user-circle' : 'bx-user-circle' }} text-[22px] mb-0.5'></i>
            <span class="text-[10px] font-medium tracking-wide">Profil</span>
        </a>
    </div>
</nav>