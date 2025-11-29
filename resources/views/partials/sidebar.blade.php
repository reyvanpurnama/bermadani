<aside id="main-sidebar" class="fixed inset-y-0 left-0 bg-card dark:bg-darkCard w-[180px] border-r border-slate-200 dark:border-slate-700 flex flex-col z-50 transition-all duration-300 -translate-x-full md:translate-x-0 shadow-2xl md:shadow-none overflow-hidden group/sidebar">
    <div id="sidebar-header" class="h-16 flex items-center px-4 border-b border-slate-200 dark:border-slate-700 shrink-0 transition-all duration-300">
        <div class="flex items-center gap-3 overflow-hidden whitespace-nowrap">
            <div class="w-6 h-6 bg-primary rounded flex items-center justify-center text-white shrink-0">
                <i class='bx bxs-cube-alt text-xs'></i>
            </div>
            <span class="sidebar-text text-base font-bold tracking-tight text-slate-900 dark:text-white leading-none mt-0.5 transition-opacity duration-300">Koperasi</span>
        </div>
        <button id="sidebar-close" class="md:hidden ml-auto text-slate-500 hover:text-rose-500 transition-colors">
            <i class='bx bx-x text-2xl'></i>
        </button>
    </div>

    <nav class="flex-1 px-2.5 py-3 space-y-0.5 overflow-y-auto custom-scroll overflow-x-hidden">
        <p class="sidebar-text px-2 text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 mt-1 opacity-80 whitespace-nowrap transition-opacity duration-300">Utama</p>
        <a href="{{ route('admin.dashboard') }}" class="nav-item flex items-center gap-3 px-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('admin.dashboard') ? 'bg-slate-50 dark:bg-slate-700/40 text-primary dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
            <i class='bx bxs-dashboard text-sm {{ request()->routeIs('admin.dashboard') ? 'opacity-100' : 'opacity-70 group-hover:opacity-100' }} transition-opacity shrink-0'></i> 
            <span class="sidebar-text text-xs {{ request()->routeIs('admin.dashboard') ? 'font-semibold' : 'font-medium' }} transition-opacity duration-300">Dasbor</span>
        </a>

        <p class="sidebar-text px-2 text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 mt-4 opacity-80 whitespace-nowrap transition-opacity duration-300">Aplikasi</p>
        <a href="{{ route('admin.pos') }}" class="nav-item flex items-center gap-3 px-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('admin.pos') ? 'bg-slate-50 dark:bg-slate-700/40 text-primary dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
            <i class='bx bx-desktop text-sm {{ request()->routeIs('admin.pos') ? 'opacity-100' : 'opacity-70 group-hover:opacity-100' }} transition-opacity shrink-0'></i> 
            <span class="sidebar-text text-xs {{ request()->routeIs('admin.pos') ? 'font-semibold' : 'font-medium' }} transition-opacity duration-300">Sistem POS</span>
        </a>
        <a href="{{ route('admin.products') }}" class="nav-item flex items-center gap-3 px-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('admin.products*') ? 'bg-slate-50 dark:bg-slate-700/40 text-primary dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
            <i class='bx bx-package text-sm {{ request()->routeIs('admin.products*') ? 'opacity-100' : 'opacity-70 group-hover:opacity-100' }} transition-opacity shrink-0'></i> 
            <span class="sidebar-text text-xs {{ request()->routeIs('admin.products*') ? 'font-semibold' : 'font-medium' }} transition-opacity duration-300">Inventaris</span>
        </a>
        <a href="{{ route('admin.categories') }}" class="nav-item flex items-center gap-3 px-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('admin.categories*') ? 'bg-slate-50 dark:bg-slate-700/40 text-primary dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
            <i class='bx bx-category text-sm {{ request()->routeIs('admin.categories*') ? 'opacity-100' : 'opacity-70 group-hover:opacity-100' }} transition-opacity shrink-0'></i> 
            <span class="sidebar-text text-xs {{ request()->routeIs('admin.categories*') ? 'font-semibold' : 'font-medium' }} transition-opacity duration-300">Kategori</span>
        </a>
        <a href="{{ route('admin.members') }}" class="nav-item flex items-center gap-3 px-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('admin.members*') ? 'bg-slate-50 dark:bg-slate-700/40 text-primary dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
            <i class='bx bx-user text-sm {{ request()->routeIs('admin.members*') ? 'opacity-100' : 'opacity-70 group-hover:opacity-100' }} transition-opacity shrink-0'></i> 
            <span class="sidebar-text text-xs {{ request()->routeIs('admin.members*') ? 'font-semibold' : 'font-medium' }} transition-opacity duration-300">Anggota</span>
        </a>
        <a href="{{ route('admin.transactions') }}" class="nav-item flex items-center gap-3 px-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('admin.transactions*') ? 'bg-slate-50 dark:bg-slate-700/40 text-primary dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
            <i class='bx bx-receipt text-sm {{ request()->routeIs('admin.transactions*') ? 'opacity-100' : 'opacity-70 group-hover:opacity-100' }} transition-opacity shrink-0'></i> 
            <span class="sidebar-text text-xs {{ request()->routeIs('admin.transactions*') ? 'font-semibold' : 'font-medium' }} transition-opacity duration-300">Transaksi</span>
        </a>

        <p class="sidebar-text px-2 text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 mt-4 opacity-80 whitespace-nowrap transition-opacity duration-300">Keuangan</p>
        <a href="{{ route('admin.savings') }}" class="nav-item flex items-center gap-3 px-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('admin.savings*') ? 'bg-slate-50 dark:bg-slate-700/40 text-primary dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
            <i class='bx bx-wallet text-sm {{ request()->routeIs('admin.savings*') ? 'opacity-100' : 'opacity-70 group-hover:opacity-100' }} transition-opacity shrink-0'></i> 
            <span class="sidebar-text text-xs {{ request()->routeIs('admin.savings*') ? 'font-semibold' : 'font-medium' }} transition-opacity duration-300">Simpanan</span>
        </a>
        <a href="{{ route('admin.loans') }}" class="nav-item flex items-center gap-3 px-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('admin.loans*') ? 'bg-slate-50 dark:bg-slate-700/40 text-primary dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
            <i class='bx bx-money text-sm {{ request()->routeIs('admin.loans*') ? 'opacity-100' : 'opacity-70 group-hover:opacity-100' }} transition-opacity shrink-0'></i> 
            <span class="sidebar-text text-xs {{ request()->routeIs('admin.loans*') ? 'font-semibold' : 'font-medium' }} transition-opacity duration-300">Pinjaman</span>
        </a>

        @if(auth()->user()->isSuperAdmin() || auth()->user()->isDeveloper())
        <p class="sidebar-text px-2 text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 mt-4 opacity-80 whitespace-nowrap transition-opacity duration-300">Sistem</p>
        <a href="{{ route('admin.users') }}" class="nav-item flex items-center gap-3 px-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('admin.users*') ? 'bg-slate-50 dark:bg-slate-700/40 text-primary dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
            <i class='bx bx-group text-sm {{ request()->routeIs('admin.users*') ? 'opacity-100' : 'opacity-70 group-hover:opacity-100' }} transition-opacity shrink-0'></i> 
            <span class="sidebar-text text-xs {{ request()->routeIs('admin.users*') ? 'font-semibold' : 'font-medium' }} transition-opacity duration-300">Pengguna</span>
        </a>
        <a href="{{ route('admin.suppliers') }}" class="nav-item flex items-center gap-3 px-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('admin.suppliers*') ? 'bg-slate-50 dark:bg-slate-700/40 text-primary dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
            <i class='bx bx-store text-sm {{ request()->routeIs('admin.suppliers*') ? 'opacity-100' : 'opacity-70 group-hover:opacity-100' }} transition-opacity shrink-0'></i> 
            <span class="sidebar-text text-xs {{ request()->routeIs('admin.suppliers*') ? 'font-semibold' : 'font-medium' }} transition-opacity duration-300">Supplier</span>
        </a>
        <a href="{{ route('admin.settings') }}" class="nav-item flex items-center gap-3 px-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('admin.settings*') ? 'bg-slate-50 dark:bg-slate-700/40 text-primary dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
            <i class='bx bx-cog text-sm {{ request()->routeIs('admin.settings*') ? 'opacity-100' : 'opacity-70 group-hover:opacity-100' }} transition-opacity shrink-0'></i> 
            <span class="sidebar-text text-xs {{ request()->routeIs('admin.settings*') ? 'font-semibold' : 'font-medium' }} transition-opacity duration-300">Pengaturan</span>
        </a>
        @endif
    </nav>

    <div id="sidebar-collapse-desktop" class="hidden md:flex items-center gap-3 px-4 py-3 border-t border-slate-200 dark:border-slate-700 cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors text-slate-500 hover:text-primary whitespace-nowrap overflow-hidden">
        <i class='bx bx-chevrons-left text-xl transition-transform duration-300 shrink-0'></i>
        <span class="sidebar-text text-xs font-medium transition-opacity duration-300">Ciutkan</span>
    </div>

    <div id="user-profile" class="p-2.5 border-t border-slate-100 dark:border-slate-700 overflow-hidden transition-all duration-300">
        <div class="flex items-center gap-3 p-1.5 rounded-md hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors cursor-pointer group whitespace-nowrap">
            <div class="w-6 h-6 rounded-full bg-primary text-white flex items-center justify-center text-xs font-bold shrink-0">
                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
            </div>
            <div class="sidebar-text flex-1 overflow-hidden transition-opacity duration-300">
                <h4 class="text-xs font-semibold text-slate-800 dark:text-white truncate">{{ auth()->user()->name }}</h4>
                <p class="text-[9px] text-slate-400 truncate">{{ auth()->user()->role }}</p>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="sidebar-text">
                @csrf
                <button type="submit" class="text-slate-400 hover:text-rose-500 transition-colors" title="Logout">
                    <i class='bx bx-log-out text-sm'></i>
                </button>
            </form>
        </div>
    </div>
</aside>
