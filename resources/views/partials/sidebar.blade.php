<aside id="main-sidebar" class="fixed inset-y-0 left-0 bg-white dark:bg-darkCard w-[180px] border-r border-slate-200 dark:border-slate-700 flex flex-col z-50 transition-all duration-300 -translate-x-full md:translate-x-0 shadow-2xl md:shadow-none overflow-hidden group/sidebar">
    <div id="sidebar-header" class="h-16 flex items-center px-4 border-b border-slate-200 dark:border-slate-700 shrink-0 transition-all duration-300">
        <div class="flex items-center gap-2 overflow-hidden whitespace-nowrap">
            <div class="w-6 h-6 bg-primary rounded flex items-center justify-center text-white shrink-0">
                <i class='bx bxs-cube-alt text-xs'></i>
            </div>
            <span class="sidebar-text text-base font-bold tracking-tight text-slate-900 dark:text-white leading-none mt-0.5 transition-opacity duration-300">Bermadani</span>
        </div>
        <button id="sidebar-close" class="md:hidden ml-auto text-slate-500 hover:text-rose-500 transition-colors">
            <i class='bx bx-x text-2xl'></i>
        </button>
    </div>

    <nav class="flex-1 px-2.5 py-3 space-y-0.5 overflow-y-auto custom-scroll overflow-x-hidden">
        @if(auth()->user()->isKasir())
            {{-- KASIR SIDEBAR --}}
            <p class="sidebar-text px-2 text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 mt-1 opacity-80 whitespace-nowrap transition-opacity duration-300">Main</p>
            <a href="{{ route('kasir.dashboard') }}" class="nav-item flex items-center px-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('kasir.dashboard') ? 'bg-indigo-50 dark:bg-indigo-500/10 text-primary dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                <i class='bx bxs-dashboard text-sm mr-2 {{ request()->routeIs('kasir.dashboard') ? 'opacity-100' : 'opacity-70 group-hover:opacity-100' }} transition-opacity shrink-0'></i> 
                <span class="sidebar-text text-xs {{ request()->routeIs('kasir.dashboard') ? 'font-semibold' : 'font-medium' }} transition-opacity duration-300">Dashboard</span>
            </a>

            <p class="sidebar-text px-2 text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 mt-4 opacity-80 whitespace-nowrap transition-opacity duration-300">Apps</p>
            <a href="{{ route('kasir.pos') }}" class="nav-item flex items-center px-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('kasir.pos') ? 'bg-indigo-50 dark:bg-indigo-500/10 text-primary dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                <i class='bx bx-desktop text-sm mr-2 {{ request()->routeIs('kasir.pos') ? 'opacity-100' : 'opacity-70 group-hover:opacity-100' }} transition-opacity shrink-0'></i> 
                <span class="sidebar-text text-xs {{ request()->routeIs('kasir.pos') ? 'font-semibold' : 'font-medium' }} transition-opacity duration-300">POS System</span>
            </a>

            <p class="sidebar-text px-2 text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 mt-4 opacity-80 whitespace-nowrap transition-opacity duration-300">Reports</p>
            <a href="{{ route('kasir.transactions') }}" class="nav-item flex items-center px-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('kasir.transactions*') ? 'bg-indigo-50 dark:bg-indigo-500/10 text-primary dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                <i class='bx bx-receipt text-sm mr-2 {{ request()->routeIs('kasir.transactions*') ? 'opacity-100' : 'opacity-70 group-hover:opacity-100' }} transition-opacity shrink-0'></i> 
                <span class="sidebar-text text-xs {{ request()->routeIs('kasir.transactions*') ? 'font-semibold' : 'font-medium' }} transition-opacity duration-300">Riwayat Transaksi</span>
            </a>
        @else
            {{-- ADMIN SIDEBAR --}}
            <p class="sidebar-text px-2 text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 mt-1 opacity-80 whitespace-nowrap transition-opacity duration-300">Main</p>
            <a href="{{ route('admin.dashboard') }}" class="nav-item flex items-center px-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-50 dark:bg-indigo-500/10 text-primary dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                <i class='bx bxs-dashboard text-sm mr-2 {{ request()->routeIs('admin.dashboard') ? 'opacity-100' : 'opacity-70 group-hover:opacity-100' }} transition-opacity shrink-0'></i> 
                <span class="sidebar-text text-xs {{ request()->routeIs('admin.dashboard') ? 'font-semibold' : 'font-medium' }} transition-opacity duration-300">Dashboard</span>
            </a>

            <p class="sidebar-text px-2 text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 mt-4 opacity-80 whitespace-nowrap transition-opacity duration-300">Apps</p>
            <a href="{{ route('admin.pos') }}" class="nav-item flex items-center px-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('admin.pos') ? 'bg-indigo-50 dark:bg-indigo-500/10 text-primary dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                <i class='bx bx-desktop text-sm mr-2 {{ request()->routeIs('admin.pos') ? 'opacity-100' : 'opacity-70 group-hover:opacity-100' }} transition-opacity shrink-0'></i> 
                <span class="sidebar-text text-xs {{ request()->routeIs('admin.pos') ? 'font-semibold' : 'font-medium' }} transition-opacity duration-300">POS System</span>
            </a>
            <a href="{{ route('admin.manual-transaction') }}" class="nav-item flex items-center px-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('admin.manual-transaction') ? 'bg-indigo-50 dark:bg-indigo-500/10 text-primary dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                <i class='bx bx-wallet text-sm mr-2 {{ request()->routeIs('admin.manual-transaction') ? 'opacity-100' : 'opacity-70 group-hover:opacity-100' }} transition-opacity shrink-0'></i> 
                <span class="sidebar-text text-xs {{ request()->routeIs('admin.manual-transaction') ? 'font-semibold' : 'font-medium' }} transition-opacity duration-300">Transaksi Manual</span>
            </a>

            <p class="sidebar-text px-2 text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 mt-4 opacity-80 whitespace-nowrap transition-opacity duration-300">Inventory</p>
            <a href="{{ route('admin.products') }}" class="nav-item flex items-center px-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('admin.products*') ? 'bg-indigo-50 dark:bg-indigo-500/10 text-primary dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                <i class='bx bx-package text-sm mr-2 {{ request()->routeIs('admin.products*') ? 'opacity-100' : 'opacity-70 group-hover:opacity-100' }} transition-opacity shrink-0'></i> 
                <span class="sidebar-text text-xs {{ request()->routeIs('admin.products*') ? 'font-semibold' : 'font-medium' }} transition-opacity duration-300">Stok Barang</span>
            </a>
            <a href="{{ route('admin.categories') }}" class="nav-item flex items-center px-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('admin.categories*') ? 'bg-indigo-50 dark:bg-indigo-500/10 text-primary dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                <i class='bx bx-category text-sm mr-2 {{ request()->routeIs('admin.categories*') ? 'opacity-100' : 'opacity-70 group-hover:opacity-100' }} transition-opacity shrink-0'></i> 
                <span class="sidebar-text text-xs {{ request()->routeIs('admin.categories*') ? 'font-semibold' : 'font-medium' }} transition-opacity duration-300">Kategori Produk</span>
            </a>

            <p class="sidebar-text px-2 text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 mt-4 opacity-80 whitespace-nowrap transition-opacity duration-300">Reports</p>
            <a href="{{ route('admin.transactions') }}" class="nav-item flex items-center px-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('admin.transactions*') && !request()->routeIs('admin.manual-transaction') ? 'bg-indigo-50 dark:bg-indigo-500/10 text-primary dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                <i class='bx bx-receipt text-sm mr-2 {{ request()->routeIs('admin.transactions*') && !request()->routeIs('admin.manual-transaction') ? 'opacity-100' : 'opacity-70 group-hover:opacity-100' }} transition-opacity shrink-0'></i> 
                <span class="sidebar-text text-xs {{ request()->routeIs('admin.transactions*') && !request()->routeIs('admin.manual-transaction') ? 'font-semibold' : 'font-medium' }} transition-opacity duration-300">Laporan Harian</span>
            </a>

            <p class="sidebar-text px-2 text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 mt-4 opacity-80 whitespace-nowrap transition-opacity duration-300">Koperasi</p>
            <a href="{{ route('admin.members.index') }}" class="nav-item flex items-center px-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('admin.members*') ? 'bg-indigo-50 dark:bg-indigo-500/10 text-primary dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                <i class='bx bx-user-circle text-sm mr-2 {{ request()->routeIs('admin.members*') ? 'opacity-100' : 'opacity-70 group-hover:opacity-100' }} transition-opacity shrink-0'></i> 
                <span class="sidebar-text text-xs {{ request()->routeIs('admin.members*') ? 'font-semibold' : 'font-medium' }} transition-opacity duration-300">Anggota</span>
            </a>
            <a href="{{ route('admin.savings') }}" class="nav-item flex items-center px-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('admin.savings*') && !request()->routeIs('admin.payments*') ? 'bg-indigo-50 dark:bg-indigo-500/10 text-primary dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                <i class='bx bx-wallet text-sm mr-2 {{ request()->routeIs('admin.savings*') && !request()->routeIs('admin.payments*') ? 'opacity-100' : 'opacity-70 group-hover:opacity-100' }} transition-opacity shrink-0'></i> 
                <span class="sidebar-text text-xs {{ request()->routeIs('admin.savings*') && !request()->routeIs('admin.payments*') ? 'font-semibold' : 'font-medium' }} transition-opacity duration-300">Simpanan</span>
            </a>
            <a href="{{ route('admin.payments.create') }}" class="nav-item flex items-center px-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('admin.payments*') ? 'bg-indigo-50 dark:bg-indigo-500/10 text-primary dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                <i class='bx bx-receipt text-sm mr-2 {{ request()->routeIs('admin.payments*') ? 'opacity-100' : 'opacity-70 group-hover:opacity-100' }} transition-opacity shrink-0'></i> 
                <span class="sidebar-text text-xs {{ request()->routeIs('admin.payments*') ? 'font-semibold' : 'font-medium' }} transition-opacity duration-300">Catat Setoran</span>
            </a>
            <a href="{{ route('admin.loans') }}" class="nav-item flex items-center px-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('admin.loans*') ? 'bg-indigo-50 dark:bg-indigo-500/10 text-primary dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                <i class='bx bx-money text-sm mr-2 {{ request()->routeIs('admin.loans*') ? 'opacity-100' : 'opacity-70 group-hover:opacity-100' }} transition-opacity shrink-0'></i> 
                <span class="sidebar-text text-xs {{ request()->routeIs('admin.loans*') ? 'font-semibold' : 'font-medium' }} transition-opacity duration-300">Pinjaman</span>
            </a>

            @if(auth()->user()->isSuperAdmin() || auth()->user()->isDeveloper() || auth()->user()->isAdmin())
            <p class="sidebar-text px-2 text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 mt-4 opacity-80 whitespace-nowrap transition-opacity duration-300">System</p>
            <a href="{{ route('admin.users') }}" class="nav-item flex items-center px-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('admin.users*') ? 'bg-indigo-50 dark:bg-indigo-500/10 text-primary dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                <i class='bx bx-group text-sm mr-2 {{ request()->routeIs('admin.users*') ? 'opacity-100' : 'opacity-70 group-hover:opacity-100' }} transition-opacity shrink-0'></i> 
                <span class="sidebar-text text-xs {{ request()->routeIs('admin.users*') ? 'font-semibold' : 'font-medium' }} transition-opacity duration-300">Kelola User</span>
            </a>
            <a href="{{ route('admin.settings') }}" class="nav-item flex items-center px-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('admin.settings*') ? 'bg-indigo-50 dark:bg-indigo-500/10 text-primary dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                <i class='bx bx-cog text-sm mr-2 {{ request()->routeIs('admin.settings*') ? 'opacity-100' : 'opacity-70 group-hover:opacity-100' }} transition-opacity shrink-0'></i> 
                <span class="sidebar-text text-xs {{ request()->routeIs('admin.settings*') ? 'font-semibold' : 'font-medium' }} transition-opacity duration-300">Pengaturan</span>
            </a>
            <a href="{{ route('admin.activity-logs') }}" class="nav-item flex items-center px-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('admin.activity-logs') ? 'bg-indigo-50 dark:bg-indigo-500/10 text-primary dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                <i class='bx bx-history text-sm mr-2 {{ request()->routeIs('admin.activity-logs') ? 'opacity-100' : 'opacity-70 group-hover:opacity-100' }} transition-opacity shrink-0'></i> 
                <span class="sidebar-text text-xs {{ request()->routeIs('admin.activity-logs') ? 'font-semibold' : 'font-medium' }} transition-opacity duration-300">Activity Log</span>
            </a>
            @endif
        @endif
    </nav>

    <div id="sidebar-collapse-desktop" class="hidden md:flex items-center gap-2 px-4 py-3 border-t border-slate-200 dark:border-slate-700 cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors text-slate-500 hover:text-primary whitespace-nowrap overflow-hidden">
        <i class='bx bx-chevrons-left text-lg transition-transform duration-300 shrink-0'></i>
        <span class="sidebar-text text-xs font-medium transition-opacity duration-300">Ciutkan</span>
    </div>
    <div id="user-profile" class="p-3 border-t border-slate-100 dark:border-slate-700 overflow-hidden transition-all duration-300">
        <div class="flex items-center gap-2 p-2 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors cursor-pointer group whitespace-nowrap">
            <div class="w-7 h-7 rounded-full bg-primary text-white flex items-center justify-center text-[11px] font-bold shrink-0">
                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
            </div>
            <div class="sidebar-text flex-1 overflow-hidden transition-opacity duration-300">
                <h4 class="text-xs font-semibold text-slate-800 dark:text-white truncate">{{ auth()->user()->name }}</h4>
                <p class="text-[9px] text-slate-400 truncate">{{ auth()->user()->role }}</p>
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
