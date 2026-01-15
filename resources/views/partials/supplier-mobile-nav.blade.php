<nav
    class="lg:hidden fixed bottom-0 left-0 w-full bg-white dark:bg-darkCard border-t border-slate-200 dark:border-slate-800 z-50 transition-all duration-300 safe-area-bottom">
    <div class="grid grid-cols-5 h-16 relative">
        {{-- 1. Dasbor --}}
        <a href="{{ route('supplier.dashboard') }}"
            class="flex flex-col items-center justify-center gap-1 {{ request()->routeIs('supplier.dashboard') ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400 hover:text-slate-600 dark:hover:text-slate-300' }}">
            <i
                class='bx {{ request()->routeIs('supplier.dashboard') ? 'bxs-dashboard' : 'bx-dashboard' }} text-2xl mb-0.5'></i>
            <span class="text-[9px] font-medium">Dasbor</span>
        </a>

        {{-- 2. Produk --}}
        <a href="{{ route('supplier.products.index') }}"
            class="flex flex-col items-center justify-center gap-1 {{ request()->routeIs('supplier.products*') ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400 hover:text-slate-600 dark:hover:text-slate-300' }}">
            <i class='bx {{ request()->routeIs('supplier.products*') ? 'bxs-box' : 'bx-box' }} text-2xl mb-0.5'></i>
            <span class="text-[9px] font-medium">Produk</span>
        </a>

        {{-- 3. RESTOCK (Center Priority) --}}
        <div class="relative flex items-center justify-center -top-5">
            <a href="{{ route('supplier.restock') }}"
                class="w-14 h-14 bg-emerald-600 text-white rounded-full flex items-center justify-center shadow-lg shadow-emerald-600/40 border-4 border-slate-50 dark:border-darkBg transform active:scale-95 transition-transform relative group">
                <i class='bx bx-package text-2xl group-hover:scale-110 transition-transform'></i>
                @php
                    $requestedBatchCount = \App\Models\ConsignmentBatch::where('supplierId', auth()->guard('supplier')->id())->where('status', 'REQUESTED')->count();
                @endphp
                @if($requestedBatchCount > 0)
                    <span
                        class="absolute top-0 right-0 w-3 h-3 bg-rose-500 rounded-full border-2 border-white dark:border-darkBg animate-pulse"></span>
                @endif
            </a>
            <span class="absolute -bottom-6 text-[9px] font-bold text-slate-500 dark:text-slate-400">Restock</span>
        </div>

        {{-- 4. Laporan --}}
        <a href="{{ route('supplier.sales') }}"
            class="flex flex-col items-center justify-center gap-1 {{ request()->routeIs('supplier.sales') ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400 hover:text-slate-600 dark:hover:text-slate-300' }}">
            <i
                class='bx {{ request()->routeIs('supplier.sales') ? 'bxs-report' : 'bx-line-chart' }} text-2xl mb-0.5'></i>
            <span class="text-[9px] font-medium">Laporan</span>
        </a>

        {{-- 5. Profil --}}
        <a href="{{ route('supplier.profile') }}"
            class="flex flex-col items-center justify-center gap-1 {{ request()->routeIs('supplier.profile') ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400 hover:text-slate-600 dark:hover:text-slate-300' }}">
            <i
                class='bx {{ request()->routeIs('supplier.profile') ? 'bxs-user-circle' : 'bx-user-circle' }} text-2xl mb-0.5'></i>
            <span class="text-[9px] font-medium">Profil</span>
        </a>
    </div>
</nav>