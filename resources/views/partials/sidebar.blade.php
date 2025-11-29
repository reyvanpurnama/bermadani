<aside id="main-sidebar" class="fixed inset-y-0 left-0 bg-white dark:bg-darkCard w-64 border-r border-slate-200 dark:border-slate-700 flex flex-col z-50 transition-all duration-300 -translate-x-full md:translate-x-0 shadow-2xl md:shadow-none overflow-hidden group/sidebar">
    <div id="sidebar-header" class="h-16 flex items-center px-6 border-b border-slate-200 dark:border-slate-700 shrink-0 transition-all duration-300">
        <div class="flex items-center gap-3 overflow-hidden whitespace-nowrap">
            <div class="w-8 h-8 bg-primary rounded-lg flex items-center justify-center text-white shadow-lg shadow-indigo-500/30 shrink-0">
                <i class='bx bxs-cube-alt text-xl'></i>
            </div>
            <span class="sidebar-text text-lg font-bold tracking-tight text-slate-900 dark:text-white leading-none transition-opacity duration-300">Bermadani <span class="text-primary">Admin</span></span>
        </div>
        <button id="sidebar-close" class="md:hidden ml-auto text-slate-500 hover:text-rose-500 transition-colors">
            <i class='bx bx-x text-2xl'></i>
        </button>
    </div>

    <nav class="flex-1 px-4 py-4 space-y-1 overflow-y-auto custom-scroll overflow-x-hidden">
        @if(auth()->user()->isKasir())
            {{-- KASIR SIDEBAR --}}
            <p class="sidebar-text px-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 mt-2 opacity-80 whitespace-nowrap transition-opacity duration-300">Main</p>
            <a href="{{ route('kasir.dashboard') }}" class="nav-item flex items-center px-4 py-2.5 rounded-xl transition-all group whitespace-nowrap {{ request()->routeIs('kasir.dashboard') ? 'bg-indigo-50 dark:bg-indigo-500/10 text-primary dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 hover:bg-indigo-50 dark:hover:bg-indigo-500/10 hover:text-primary dark:hover:text-indigo-400' }}">
                <i class='bx bxs-dashboard text-lg mr-3 {{ request()->routeIs('kasir.dashboard') ? 'opacity-100' : 'opacity-70 group-hover:opacity-100' }} transition-opacity shrink-0'></i> 
                <span class="sidebar-text text-sm {{ request()->routeIs('kasir.dashboard') ? 'font-semibold' : 'font-medium' }} transition-opacity duration-300">Dashboard</span>
            </a>

            <p class="sidebar-text px-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 mt-6 opacity-80 whitespace-nowrap transition-opacity duration-300">Apps</p>
            <a href="{{ route('kasir.pos') }}" class="nav-item flex items-center px-4 py-2.5 rounded-xl transition-all group whitespace-nowrap {{ request()->routeIs('kasir.pos') ? 'bg-indigo-50 dark:bg-indigo-500/10 text-primary dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 hover:bg-indigo-50 dark:hover:bg-indigo-500/10 hover:text-primary dark:hover:text-indigo-400' }}">
                <i class='bx bx-desktop text-lg mr-3 {{ request()->routeIs('kasir.pos') ? 'opacity-100' : 'opacity-70 group-hover:opacity-100' }} transition-opacity shrink-0'></i> 
                <span class="sidebar-text text-sm {{ request()->routeIs('kasir.pos') ? 'font-semibold' : 'font-medium' }} transition-opacity duration-300">POS System</span>
            </a>

            <p class="sidebar-text px-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 mt-6 opacity-80 whitespace-nowrap transition-opacity duration-300">Reports</p>
            <a href="{{ route('kasir.transactions') }}" class="nav-item flex items-center px-4 py-2.5 rounded-xl transition-all group whitespace-nowrap {{ request()->routeIs('kasir.transactions*') ? 'bg-indigo-50 dark:bg-indigo-500/10 text-primary dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 hover:bg-indigo-50 dark:hover:bg-indigo-500/10 hover:text-primary dark:hover:text-indigo-400' }}">
                <i class='bx bx-receipt text-lg mr-3 {{ request()->routeIs('kasir.transactions*') ? 'opacity-100' : 'opacity-70 group-hover:opacity-100' }} transition-opacity shrink-0'></i> 
                <span class="sidebar-text text-sm {{ request()->routeIs('kasir.transactions*') ? 'font-semibold' : 'font-medium' }} transition-opacity duration-300">Transaksi Saya</span>
            </a>
        @else
            {{-- ADMIN SIDEBAR --}}
            <p class="sidebar-text px-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 mt-2 opacity-80 whitespace-nowrap transition-opacity duration-300">Main</p>
            <a href="{{ route('admin.dashboard') }}" class="nav-item flex items-center px-4 py-2.5 rounded-xl transition-all group whitespace-nowrap {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-50 dark:bg-indigo-500/10 text-primary dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 hover:bg-indigo-50 dark:hover:bg-indigo-500/10 hover:text-primary dark:hover:text-indigo-400' }}">
                <i class='bx bxs-dashboard text-lg mr-3 {{ request()->routeIs('admin.dashboard') ? 'opacity-100' : 'opacity-70 group-hover:opacity-100' }} transition-opacity shrink-0'></i> 
                <span class="sidebar-text text-sm {{ request()->routeIs('admin.dashboard') ? 'font-semibold' : 'font-medium' }} transition-opacity duration-300">Dashboard</span>
            </a>

            <p class="sidebar-text px-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 mt-6 opacity-80 whitespace-nowrap transition-opacity duration-300">Apps</p>
            <a href="{{ route('admin.pos') }}" class="nav-item flex items-center px-4 py-2.5 rounded-xl transition-all group whitespace-nowrap {{ request()->routeIs('admin.pos') ? 'bg-indigo-50 dark:bg-indigo-500/10 text-primary dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 hover:bg-indigo-50 dark:hover:bg-indigo-500/10 hover:text-primary dark:hover:text-indigo-400' }}">
                <i class='bx bx-desktop text-lg mr-3 {{ request()->routeIs('admin.pos') ? 'opacity-100' : 'opacity-70 group-hover:opacity-100' }} transition-opacity shrink-0'></i> 
                <span class="sidebar-text text-sm {{ request()->routeIs('admin.pos') ? 'font-semibold' : 'font-medium' }} transition-opacity duration-300">POS System</span>
            </a>
            <a href="{{ route('admin.manual-transaction') }}" class="nav-item flex items-center px-4 py-2.5 rounded-xl transition-all group whitespace-nowrap {{ request()->routeIs('admin.manual-transaction') ? 'bg-indigo-50 dark:bg-indigo-500/10 text-primary dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 hover:bg-indigo-50 dark:hover:bg-indigo-500/10 hover:text-primary dark:hover:text-indigo-400' }}">
                <i class='bx bx-wallet text-lg mr-3 {{ request()->routeIs('admin.manual-transaction') ? 'opacity-100' : 'opacity-70 group-hover:opacity-100' }} transition-opacity shrink-0'></i> 
                <span class="sidebar-text text-sm {{ request()->routeIs('admin.manual-transaction') ? 'font-semibold' : 'font-medium' }} transition-opacity duration-300">Transaksi Manual</span>
            </a>

            <p class="sidebar-text px-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 mt-6 opacity-80 whitespace-nowrap transition-opacity duration-300">Inventory</p>
            <a href="{{ route('admin.products') }}" class="nav-item flex items-center px-4 py-2.5 rounded-xl transition-all group whitespace-nowrap {{ request()->routeIs('admin.products*') ? 'bg-indigo-50 dark:bg-indigo-500/10 text-primary dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 hover:bg-indigo-50 dark:hover:bg-indigo-500/10 hover:text-primary dark:hover:text-indigo-400' }}">
                <i class='bx bx-package text-lg mr-3 {{ request()->routeIs('admin.products*') ? 'opacity-100' : 'opacity-70 group-hover:opacity-100' }} transition-opacity shrink-0'></i> 
                <span class="sidebar-text text-sm {{ request()->routeIs('admin.products*') ? 'font-semibold' : 'font-medium' }} transition-opacity duration-300">Stok Barang</span>
            </a>
            <a href="{{ route('admin.categories') }}" class="nav-item flex items-center px-4 py-2.5 rounded-xl transition-all group whitespace-nowrap {{ request()->routeIs('admin.categories*') ? 'bg-indigo-50 dark:bg-indigo-500/10 text-primary dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 hover:bg-indigo-50 dark:hover:bg-indigo-500/10 hover:text-primary dark:hover:text-indigo-400' }}">
                <i class='bx bx-category text-lg mr-3 {{ request()->routeIs('admin.categories*') ? 'opacity-100' : 'opacity-70 group-hover:opacity-100' }} transition-opacity shrink-0'></i> 
                <span class="sidebar-text text-sm {{ request()->routeIs('admin.categories*') ? 'font-semibold' : 'font-medium' }} transition-opacity duration-300">Kategori Produk</span>
            </a>

            <p class="sidebar-text px-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 mt-6 opacity-80 whitespace-nowrap transition-opacity duration-300">Reports</p>
            <a href="{{ route('admin.transactions') }}" class="nav-item flex items-center px-4 py-2.5 rounded-xl transition-all group whitespace-nowrap {{ request()->routeIs('admin.transactions*') && !request()->routeIs('admin.manual-transaction') ? 'bg-indigo-50 dark:bg-indigo-500/10 text-primary dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 hover:bg-indigo-50 dark:hover:bg-indigo-500/10 hover:text-primary dark:hover:text-indigo-400' }}">
                <i class='bx bx-receipt text-lg mr-3 {{ request()->routeIs('admin.transactions*') && !request()->routeIs('admin.manual-transaction') ? 'opacity-100' : 'opacity-70 group-hover:opacity-100' }} transition-opacity shrink-0'></i> 
                <span class="sidebar-text text-sm {{ request()->routeIs('admin.transactions*') && !request()->routeIs('admin.manual-transaction') ? 'font-semibold' : 'font-medium' }} transition-opacity duration-300">Laporan Harian</span>
            </a>

            @if(auth()->user()->isSuperAdmin() || auth()->user()->isDeveloper() || auth()->user()->isAdmin())
            <p class="sidebar-text px-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 mt-6 opacity-80 whitespace-nowrap transition-opacity duration-300">System</p>
            <a href="{{ route('admin.users') }}" class="nav-item flex items-center px-4 py-2.5 rounded-xl transition-all group whitespace-nowrap {{ request()->routeIs('admin.users*') ? 'bg-indigo-50 dark:bg-indigo-500/10 text-primary dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 hover:bg-indigo-50 dark:hover:bg-indigo-500/10 hover:text-primary dark:hover:text-indigo-400' }}">
                <i class='bx bx-group text-lg mr-3 {{ request()->routeIs('admin.users*') ? 'opacity-100' : 'opacity-70 group-hover:opacity-100' }} transition-opacity shrink-0'></i> 
                <span class="sidebar-text text-sm {{ request()->routeIs('admin.users*') ? 'font-semibold' : 'font-medium' }} transition-opacity duration-300">Kelola User</span>
            </a>
            <a href="{{ route('admin.settings') }}" class="nav-item flex items-center px-4 py-2.5 rounded-xl transition-all group whitespace-nowrap {{ request()->routeIs('admin.settings*') ? 'bg-indigo-50 dark:bg-indigo-500/10 text-primary dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 hover:bg-indigo-50 dark:hover:bg-indigo-500/10 hover:text-primary dark:hover:text-indigo-400' }}">
                <i class='bx bx-cog text-lg mr-3 {{ request()->routeIs('admin.settings*') ? 'opacity-100' : 'opacity-70 group-hover:opacity-100' }} transition-opacity shrink-0'></i> 
                <span class="sidebar-text text-sm {{ request()->routeIs('admin.settings*') ? 'font-semibold' : 'font-medium' }} transition-opacity duration-300">Pengaturan</span>
            </a>
            <a href="{{ route('admin.activity-logs') }}" class="nav-item flex items-center px-4 py-2.5 rounded-xl transition-all group whitespace-nowrap {{ request()->routeIs('admin.activity-logs') ? 'bg-indigo-50 dark:bg-indigo-500/10 text-primary dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 hover:bg-indigo-50 dark:hover:bg-indigo-500/10 hover:text-primary dark:hover:text-indigo-400' }}">
                <i class='bx bx-history text-lg mr-3 {{ request()->routeIs('admin.activity-logs') ? 'opacity-100' : 'opacity-70 group-hover:opacity-100' }} transition-opacity shrink-0'></i> 
                <span class="sidebar-text text-sm {{ request()->routeIs('admin.activity-logs') ? 'font-semibold' : 'font-medium' }} transition-opacity duration-300">Activity Log</span>
            </a>
            @endif
        @endif
    </nav>

    <div id="sidebar-collapse-desktop" class="hidden md:flex items-center gap-3 px-6 py-4 border-t border-slate-200 dark:border-slate-700 cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors text-slate-500 hover:text-primary whitespace-nowrap overflow-hidden">
        <i class='bx bx-chevrons-left text-xl transition-transform duration-300 shrink-0'></i>
        <span class="sidebar-text text-xs font-medium transition-opacity duration-300">Ciutkan</span>
    </div>
    <div id="user-profile" class="p-4 border-t border-slate-100 dark:border-slate-700 overflow-hidden transition-all duration-300">
        <div class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors cursor-pointer group whitespace-nowrap">
            <div class="w-9 h-9 rounded-full bg-primary text-white flex items-center justify-center text-sm font-bold ring-2 ring-slate-200 dark:ring-slate-600 shrink-0">
                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
            </div>
            <div class="sidebar-text flex-1 overflow-hidden transition-opacity duration-300">
                <h4 class="text-sm font-semibold text-slate-800 dark:text-white truncate">{{ auth()->user()->name }}</h4>
                <p class="text-[10px] text-slate-400 truncate">{{ auth()->user()->role }}</p>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="sidebar-text">
                @csrf
                <button type="submit" class="text-slate-400 group-hover:text-rose-500 transition-colors" title="Logout">
                    <i class='bx bx-log-out'></i>
                </button>
            </form>
        </div>
    </div>
</aside>
