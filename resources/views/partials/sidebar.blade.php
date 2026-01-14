<aside id="main-sidebar" x-data="{ 
        workspace: localStorage.getItem('sidebar_workspace') || 'retail',
        setWorkspace(val) {
            this.workspace = val;
            localStorage.setItem('sidebar_workspace', val);
        }
    }"
    class="fixed inset-y-0 left-0 bg-white dark:bg-darkCard w-[180px] border-r border-slate-200 dark:border-slate-700 flex flex-col z-50 transition-all duration-300 -translate-x-full md:translate-x-0 shadow-2xl md:shadow-none overflow-hidden group/sidebar">

    {{-- Header --}}
    <div id="sidebar-header"
        class="h-16 flex items-center px-4 border-b border-slate-200 dark:border-slate-700 shrink-0 transition-all duration-300">
        <div class="flex items-center gap-2 overflow-hidden whitespace-nowrap">
            <div class="w-6 h-6 bg-primary rounded flex items-center justify-center text-white shrink-0">
                <i class='bx bxs-cube-alt text-xs'></i>
            </div>
            <span
                class="sidebar-text text-base font-bold tracking-tight text-slate-900 dark:text-white leading-none mt-0.5 transition-opacity duration-300">Bermadani</span>
        </div>
        <button id="sidebar-close" class="md:hidden ml-auto text-slate-500 hover:text-rose-500 transition-colors">
            <i class='bx bx-x text-2xl'></i>
        </button>
    </div>

    {{-- Workspace Switcher (Admin Only) --}}
    @if(!auth()->user()->isKasir())
        <div class="px-2.5 pt-3 pb-1">
            <div class="bg-slate-100 dark:bg-slate-800 p-1 rounded-lg flex text-[10px] font-bold relative">
                <button @click="setWorkspace('retail')"
                    :class="workspace === 'retail' ? 'bg-white dark:bg-slate-700 text-slate-800 dark:text-white shadow-sm' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'"
                    class="flex-1 py-1.5 rounded-md transition-all flex items-center justify-center gap-1.5 z-10 text-center">
                    <i class='bx bx-shopping-bag'></i> <span class="sidebar-text truncate">Retail</span>
                </button>
                <button @click="setWorkspace('core')"
                    :class="workspace === 'core' ? 'bg-white dark:bg-slate-700 text-slate-800 dark:text-white shadow-sm' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'"
                    class="flex-1 py-1.5 rounded-md transition-all flex items-center justify-center gap-1.5 z-10 text-center">
                    <i class='bx bx-building-house'></i> <span class="sidebar-text truncate">Koperasi</span>
                </button>
            </div>
        </div>
    @endif

    <nav class="flex-1 px-2.5 py-2 space-y-0.5 overflow-y-auto custom-scroll overflow-x-hidden">
        @if(auth()->user()->isKasir())
            {{-- KASIR: SIMPLE VIEW (Unchanged) --}}
            <p
                class="sidebar-text px-2 text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 mt-1 opacity-80 whitespace-nowrap">
                Main</p>
            <a href="{{ route('kasir.dashboard') }}"
                class="nav-item flex items-center px-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('kasir.dashboard') ? 'bg-indigo-50 dark:bg-indigo-500/10 text-primary dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                <i
                    class='bx bxs-dashboard text-sm mr-2 {{ request()->routeIs('kasir.dashboard') ? 'opacity-100' : 'opacity-70 group-hover:opacity-100' }} transition-opacity shrink-0'></i>
                <span
                    class="sidebar-text text-xs {{ request()->routeIs('kasir.dashboard') ? 'font-semibold' : 'font-medium' }} transition-opacity duration-300">Dashboard</span>
            </a>

            <p
                class="sidebar-text px-2 text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 mt-4 opacity-80 whitespace-nowrap">
                Apps</p>
            <a href="{{ route('kasir.pos') }}"
                class="nav-item flex items-center px-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('kasir.pos') ? 'bg-indigo-50 dark:bg-indigo-500/10 text-primary dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                <i
                    class='bx bx-desktop text-sm mr-2 {{ request()->routeIs('kasir.pos') ? 'opacity-100' : 'opacity-70 group-hover:opacity-100' }} transition-opacity shrink-0'></i>
                <span
                    class="sidebar-text text-xs {{ request()->routeIs('kasir.pos') ? 'font-semibold' : 'font-medium' }} transition-opacity duration-300">POS
                    System</span>
            </a>

            <p
                class="sidebar-text px-2 text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 mt-4 opacity-80 whitespace-nowrap">
                Reports</p>
            <a href="{{ route('kasir.transactions') }}"
                class="nav-item flex items-center px-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('kasir.transactions*') ? 'bg-indigo-50 dark:bg-indigo-500/10 text-primary dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                <i
                    class='bx bx-receipt text-sm mr-2 {{ request()->routeIs('kasir.transactions*') ? 'opacity-100' : 'opacity-70 group-hover:opacity-100' }} transition-opacity shrink-0'></i>
                <span
                    class="sidebar-text text-xs {{ request()->routeIs('kasir.transactions*') ? 'font-semibold' : 'font-medium' }} transition-opacity duration-300">Riwayat
                    Transaksi</span>
            </a>
        @else
            {{-- ADMIN: DASHBOARD (ALWAYS VISIBLE) --}}
            <a href="{{ route('admin.dashboard') }}"
                class="nav-item flex items-center px-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-50 dark:bg-indigo-500/10 text-primary dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                <i
                    class='bx bxs-dashboard text-sm mr-2 {{ request()->routeIs('admin.dashboard') ? 'opacity-100' : 'opacity-70 group-hover:opacity-100' }} transition-opacity shrink-0'></i>
                <span
                    class="sidebar-text text-xs {{ request()->routeIs('admin.dashboard') ? 'font-semibold' : 'font-medium' }} transition-opacity duration-300">Dashboard</span>
            </a>

            {{-- NERACA SALDO (CONSOLIDATED) --}}
            <a href="{{ route('admin.reports.balance-sheet') }}"
                class="nav-item flex items-center px-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('admin.reports.balance-sheet') ? 'bg-indigo-50 dark:bg-indigo-500/10 text-primary dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                <i
                    class='bx bx-spreadsheet text-sm mr-2 {{ request()->routeIs('admin.reports.balance-sheet') ? 'opacity-100' : 'opacity-70 group-hover:opacity-100' }} transition-opacity shrink-0'></i>
                <span
                    class="sidebar-text text-xs {{ request()->routeIs('admin.reports.balance-sheet') ? 'font-semibold' : 'font-medium' }} transition-opacity duration-300">Neraca</span>
            </a>

            {{-- DEVELOPER: WORK LOG MENU --}}
            @if(auth()->user()->isDeveloper())
                <a href="{{ route('developer.work-logs') }}"
                    class="nav-item flex items-center px-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('developer.work-logs') ? 'bg-indigo-50 dark:bg-indigo-500/10 text-primary dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                    <i
                        class='bx bx-time-five text-sm mr-2 {{ request()->routeIs('developer.work-logs') ? 'opacity-100' : 'opacity-70 group-hover:opacity-100' }} transition-opacity shrink-0'></i>
                    <span
                        class="sidebar-text text-xs {{ request()->routeIs('developer.work-logs') ? 'font-semibold' : 'font-medium' }} transition-opacity duration-300">Log
                        Kerja</span>
                </a>
            @endif

            <div x-show="workspace === 'retail'" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-x-[-10px]" x-transition:enter-end="opacity-100 translate-x-0">
                <p
                    class="sidebar-text px-2 text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 mt-4 opacity-80 whitespace-nowrap">
                    Retail Ops</p>
                <a href="{{ route('admin.pos') }}"
                    class="nav-item flex items-center px-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('admin.pos') ? 'bg-indigo-50 dark:bg-indigo-500/10 text-primary dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                    <i
                        class='bx bx-desktop text-sm mr-2 {{ request()->routeIs('admin.pos') ? 'opacity-100' : 'opacity-70 group-hover:opacity-100' }} transition-opacity shrink-0'></i>
                    <span
                        class="sidebar-text text-xs {{ request()->routeIs('admin.pos') ? 'font-semibold' : 'font-medium' }} transition-opacity duration-300">POS
                        System</span>
                </a>

                <p
                    class="sidebar-text px-2 text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 mt-4 opacity-80 whitespace-nowrap">
                    Inventory</p>
                <a href="{{ route('admin.products') }}"
                    class="nav-item flex items-center px-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('admin.products*') ? 'bg-indigo-50 dark:bg-indigo-500/10 text-primary dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                    <i
                        class='bx bx-package text-sm mr-2 {{ request()->routeIs('admin.products*') ? 'opacity-100' : 'opacity-70 group-hover:opacity-100' }} transition-opacity shrink-0'></i>
                    <span
                        class="sidebar-text text-xs {{ request()->routeIs('admin.products*') ? 'font-semibold' : 'font-medium' }} transition-opacity duration-300">Stok
                        Barang</span>
                </a>
                <a href="{{ route('admin.stock-mutation') }}"
                    class="nav-item flex items-center px-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('admin.stock-mutation') ? 'bg-indigo-50 dark:bg-indigo-500/10 text-primary dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                    <i
                        class='bx bx-transfer-alt text-sm mr-2 {{ request()->routeIs('admin.stock-mutation') ? 'opacity-100' : 'opacity-70 group-hover:opacity-100' }} transition-opacity shrink-0'></i>
                    <span
                        class="sidebar-text text-xs {{ request()->routeIs('admin.stock-mutation') ? 'font-semibold' : 'font-medium' }} transition-opacity duration-300">Mutasi
                        Stok</span>
                </a>
                <a href="{{ route('admin.restock-requests') }}"
                    class="nav-item flex items-center px-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('admin.restock-requests') ? 'bg-indigo-50 dark:bg-indigo-500/10 text-primary dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                    <i
                        class='bx bx-cart-download text-sm mr-2 {{ request()->routeIs('admin.restock-requests') ? 'opacity-100' : 'opacity-70 group-hover:opacity-100' }} transition-opacity shrink-0'></i>
                    <span
                        class="sidebar-text text-xs {{ request()->routeIs('admin.restock-requests') ? 'font-semibold' : 'font-medium' }} transition-opacity duration-300">Request
                        Masuk</span>
                </a>
                <a href="{{ route('admin.stock-adjustment') }}"
                    class="nav-item flex items-center px-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('admin.stock-adjustment') ? 'bg-indigo-50 dark:bg-indigo-500/10 text-primary dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                    <i
                        class='bx bx-slider-alt text-sm mr-2 {{ request()->routeIs('admin.stock-adjustment') ? 'opacity-100' : 'opacity-70 group-hover:opacity-100' }} transition-opacity shrink-0'></i>
                    <span
                        class="sidebar-text text-xs {{ request()->routeIs('admin.stock-adjustment') ? 'font-semibold' : 'font-medium' }} transition-opacity duration-300">Penyesuaian</span>
                </a>
                <a href="{{ route('admin.product-review') }}"
                    class="nav-item flex items-center px-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('admin.product-review') ? 'bg-indigo-50 dark:bg-indigo-500/10 text-primary dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                    <i
                        class='bx bx-check-shield text-sm mr-2 {{ request()->routeIs('admin.product-review') ? 'opacity-100' : 'opacity-70 group-hover:opacity-100' }} transition-opacity shrink-0'></i>
                    <span
                        class="sidebar-text text-xs {{ request()->routeIs('admin.product-review') ? 'font-semibold' : 'font-medium' }} transition-opacity duration-300">Review
                        Produk</span>
                </a>
                <a href="{{ route('admin.suppliers') }}"
                    class="nav-item flex items-center px-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('admin.suppliers*') ? 'bg-indigo-50 dark:bg-indigo-500/10 text-primary dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                    <i
                        class='bx bx-store-alt text-sm mr-2 {{ request()->routeIs('admin.suppliers*') ? 'opacity-100' : 'opacity-70 group-hover:opacity-100' }} transition-opacity shrink-0'></i>
                    <span
                        class="sidebar-text text-xs {{ request()->routeIs('admin.suppliers*') ? 'font-semibold' : 'font-medium' }} transition-opacity duration-300">Supplier</span>
                </a>

                <p
                    class="sidebar-text px-2 text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 mt-4 opacity-80 whitespace-nowrap">
                    Konsinyasi</p>
                <a href="{{ route('admin.consignment-batches') }}"
                    class="nav-item flex items-center px-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('admin.consignment-batches') ? 'bg-indigo-50 dark:bg-indigo-500/10 text-primary dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                    <i
                        class='bx bx-notepad text-sm mr-2 {{ request()->routeIs('admin.consignment-batches') ? 'opacity-100' : 'opacity-70 group-hover:opacity-100' }} transition-opacity shrink-0'></i>
                    <span
                        class="sidebar-text text-xs {{ request()->routeIs('admin.consignment-batches') ? 'font-semibold' : 'font-medium' }} transition-opacity duration-300">Daftar
                        Batch</span>
                </a>

                <p
                    class="sidebar-text px-2 text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 mt-4 opacity-80 whitespace-nowrap">
                    Keuangan</p>
                <a href="{{ route('admin.manual-transaction', ['unit' => 'BISNIS']) }}"
                    class="nav-item flex items-center px-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->fullUrlIs(route('admin.manual-transaction', ['unit' => 'BISNIS'])) ? 'bg-indigo-50 dark:bg-indigo-500/10 text-primary dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                    <i
                        class='bx bx-transfer text-sm mr-2 {{ request()->fullUrlIs(route('admin.manual-transaction', ['unit' => 'BISNIS'])) ? 'opacity-100' : 'opacity-70 group-hover:opacity-100' }} transition-opacity shrink-0'></i>
                    <span
                        class="sidebar-text text-xs {{ request()->fullUrlIs(route('admin.manual-transaction', ['unit' => 'BISNIS'])) ? 'font-semibold' : 'font-medium' }} transition-opacity duration-300">Catat
                        Keuangan</span>
                </a>

                <p
                    class="sidebar-text px-2 text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 mt-4 opacity-80 whitespace-nowrap">
                    Laporan</p>
                <a href="{{ route('admin.transactions') }}"
                    class="nav-item flex items-center px-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('admin.transactions*') && !request()->routeIs('admin.manual-transaction') ? 'bg-indigo-50 dark:bg-indigo-500/10 text-primary dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                    <i
                        class='bx bx-receipt text-sm mr-2 {{ request()->routeIs('admin.transactions*') && !request()->routeIs('admin.manual-transaction') ? 'opacity-100' : 'opacity-70 group-hover:opacity-100' }} transition-opacity shrink-0'></i>
                    <span
                        class="sidebar-text text-xs {{ request()->routeIs('admin.transactions*') && !request()->routeIs('admin.manual-transaction') ? 'font-semibold' : 'font-medium' }} transition-opacity duration-300">Riwayat
                        Penjualan</span>
                </a>
            </div>

            {{-- ADMIN: CORE (KOPERASI) MENU --}}
            <div x-show="workspace === 'core'" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-x-[10px]" x-transition:enter-end="opacity-100 translate-x-0"
                style="display: none;">
                <p
                    class="sidebar-text px-2 text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 mt-4 opacity-80 whitespace-nowrap">
                    Keanggotaan</p>
                <a href="{{ route('admin.members.index') }}"
                    class="nav-item flex items-center px-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('admin.members*') ? 'bg-indigo-50 dark:bg-indigo-500/10 text-primary dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                    <i
                        class='bx bx-user-circle text-sm mr-2 {{ request()->routeIs('admin.members*') ? 'opacity-100' : 'opacity-70 group-hover:opacity-100' }} transition-opacity shrink-0'></i>
                    <span
                        class="sidebar-text text-xs {{ request()->routeIs('admin.members*') ? 'font-semibold' : 'font-medium' }} transition-opacity duration-300">Anggota</span>
                </a>

                <p
                    class="sidebar-text px-2 text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 mt-4 opacity-80 whitespace-nowrap">
                    Simpan Pinjam</p>
                <a href="{{ route('admin.savings') }}"
                    class="nav-item flex items-center px-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('admin.savings*') && !request()->routeIs('admin.payments*') ? 'bg-indigo-50 dark:bg-indigo-500/10 text-primary dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                    <i
                        class='bx bx-wallet text-sm mr-2 {{ request()->routeIs('admin.savings*') && !request()->routeIs('admin.payments*') ? 'opacity-100' : 'opacity-70 group-hover:opacity-100' }} transition-opacity shrink-0'></i>
                    <span
                        class="sidebar-text text-xs {{ request()->routeIs('admin.savings*') && !request()->routeIs('admin.payments*') ? 'font-semibold' : 'font-medium' }} transition-opacity duration-300">Simpanan</span>
                </a>
                <a href="{{ route('admin.loans') }}"
                    class="nav-item flex items-center px-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('admin.loans*') ? 'bg-indigo-50 dark:bg-indigo-500/10 text-primary dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                    <i
                        class='bx bx-money text-sm mr-2 {{ request()->routeIs('admin.loans*') ? 'opacity-100' : 'opacity-70 group-hover:opacity-100' }} transition-opacity shrink-0'></i>
                    <span
                        class="sidebar-text text-xs {{ request()->routeIs('admin.loans*') ? 'font-semibold' : 'font-medium' }} transition-opacity duration-300">Pinjaman</span>
                </a>
                <a href="{{ route('admin.payments.create') }}"
                    class="nav-item flex items-center px-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('admin.payments*') ? 'bg-indigo-50 dark:bg-indigo-500/10 text-primary dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                    <i
                        class='bx bx-plus-circle text-sm mr-2 {{ request()->routeIs('admin.payments*') ? 'opacity-100' : 'opacity-70 group-hover:opacity-100' }} transition-opacity shrink-0'></i>
                    <span
                        class="sidebar-text text-xs {{ request()->routeIs('admin.payments*') ? 'font-semibold' : 'font-medium' }} transition-opacity duration-300">Catat
                        Setoran</span>
                </a>

                <p
                    class="sidebar-text px-2 text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 mt-4 opacity-80 whitespace-nowrap">
                    Keuangan</p>
                <a href="{{ route('admin.manual-transaction', ['unit' => 'KOPERASI']) }}"
                    class="nav-item flex items-center px-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->fullUrlIs(route('admin.manual-transaction', ['unit' => 'KOPERASI'])) ? 'bg-indigo-50 dark:bg-indigo-500/10 text-primary dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                    <i
                        class='bx bx-transfer text-sm mr-2 {{ request()->fullUrlIs(route('admin.manual-transaction', ['unit' => 'KOPERASI'])) ? 'opacity-100' : 'opacity-70 group-hover:opacity-100' }} transition-opacity shrink-0'></i>
                    <span
                        class="sidebar-text text-xs {{ request()->fullUrlIs(route('admin.manual-transaction', ['unit' => 'KOPERASI'])) ? 'font-semibold' : 'font-medium' }} transition-opacity duration-300">Catat
                        Keuangan</span>
                </a>
                <a href="{{ route('admin.reports.monthly-financial') }}"
                    class="nav-item flex items-center px-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('admin.reports.monthly-financial') ? 'bg-indigo-50 dark:bg-indigo-500/10 text-primary dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                    <i
                        class='bx bx-file text-sm mr-2 {{ request()->routeIs('admin.reports.monthly-financial') ? 'opacity-100' : 'opacity-70 group-hover:opacity-100' }} transition-opacity shrink-0'></i>
                    <span
                        class="sidebar-text text-xs {{ request()->routeIs('admin.reports.monthly-financial') ? 'font-semibold' : 'font-medium' }} transition-opacity duration-300">Laporan
                        Bulanan</span>
                </a>
            </div>

            @if(auth()->user()->isSuperAdmin() || auth()->user()->isDeveloper() || auth()->user()->isAdmin())
                <p
                    class="sidebar-text px-2 text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 mt-4 opacity-80 whitespace-nowrap">
                    System</p>
                <a href="{{ route('admin.users') }}"
                    class="nav-item flex items-center px-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('admin.users*') ? 'bg-indigo-50 dark:bg-indigo-500/10 text-primary dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                    <i
                        class='bx bx-group text-sm mr-2 {{ request()->routeIs('admin.users*') ? 'opacity-100' : 'opacity-70 group-hover:opacity-100' }} transition-opacity shrink-0'></i>
                    <span
                        class="sidebar-text text-xs {{ request()->routeIs('admin.users*') ? 'font-semibold' : 'font-medium' }} transition-opacity duration-300">Kelola
                        User</span>
                </a>
                <a href="{{ route('admin.settings') }}"
                    class="nav-item flex items-center px-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('admin.settings*') ? 'bg-indigo-50 dark:bg-indigo-500/10 text-primary dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                    <i
                        class='bx bx-cog text-sm mr-2 {{ request()->routeIs('admin.settings*') ? 'opacity-100' : 'opacity-70 group-hover:opacity-100' }} transition-opacity shrink-0'></i>
                    <span
                        class="sidebar-text text-xs {{ request()->routeIs('admin.settings*') ? 'font-semibold' : 'font-medium' }} transition-opacity duration-300">Pengaturan</span>
                </a>
                <a href="{{ route('admin.activity-logs') }}"
                    class="nav-item flex items-center px-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('admin.activity-logs') ? 'bg-indigo-50 dark:bg-indigo-500/10 text-primary dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                    <i
                        class='bx bx-history text-sm mr-2 {{ request()->routeIs('admin.activity-logs') ? 'opacity-100' : 'opacity-70 group-hover:opacity-100' }} transition-opacity shrink-0'></i>
                    <span
                        class="sidebar-text text-xs {{ request()->routeIs('admin.activity-logs') ? 'font-semibold' : 'font-medium' }} transition-opacity duration-300">Activity
                        Log</span>
                </a>
                <a href="{{ route('admin.kasir-history') }}"
                    class="nav-item flex items-center px-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('admin.kasir-history') ? 'bg-indigo-50 dark:bg-indigo-500/10 text-primary dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                    <i
                        class='bx bx-user-check text-sm mr-2 {{ request()->routeIs('admin.kasir-history') ? 'opacity-100' : 'opacity-70 group-hover:opacity-100' }} transition-opacity shrink-0'></i>
                    <span
                        class="sidebar-text text-xs {{ request()->routeIs('admin.kasir-history') ? 'font-semibold' : 'font-medium' }} transition-opacity duration-300">Riwayat
                        Kasir</span>
                </a>
                <a href="{{ route('admin.developer-payroll') }}"
                    class="nav-item flex items-center px-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('admin.developer-payroll') ? 'bg-indigo-50 dark:bg-indigo-500/10 text-primary dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                    <i
                        class='bx bx-money text-sm mr-2 {{ request()->routeIs('admin.developer-payroll') ? 'opacity-100' : 'opacity-70 group-hover:opacity-100' }} transition-opacity shrink-0'></i>
                    <span
                        class="sidebar-text text-xs {{ request()->routeIs('admin.developer-payroll') ? 'font-semibold' : 'font-medium' }} transition-opacity duration-300">Payroll
                        Dev</span>
                </a>
            @endif
        @endif
    </nav>

    <div id="sidebar-collapse-desktop"
        class="hidden md:flex items-center gap-2 px-4 py-3 border-t border-slate-200 dark:border-slate-700 cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors text-slate-500 hover:text-primary whitespace-nowrap overflow-hidden">
        <i class='bx bx-chevrons-left text-lg transition-transform duration-300 shrink-0'></i>
        <span class="sidebar-text text-xs font-medium transition-opacity duration-300">Ciutkan</span>
    </div>
    <div id="user-profile"
        class="p-3 border-t border-slate-100 dark:border-slate-700 overflow-hidden transition-all duration-300">
        <div
            class="flex items-center gap-2 p-2 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors cursor-pointer group whitespace-nowrap">
            <div
                class="w-7 h-7 rounded-full bg-primary text-white flex items-center justify-center text-[11px] font-bold shrink-0">
                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
            </div>
            <div class="sidebar-text flex-1 overflow-hidden transition-opacity duration-300">
                <h4 class="text-xs font-semibold text-slate-800 dark:text-white truncate">{{ auth()->user()->name }}
                </h4>
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