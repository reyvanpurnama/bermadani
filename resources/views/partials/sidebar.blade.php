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
        <div class="workspace-switcher-wrapper px-2.5 pt-3 pb-1">
            <div class="workspace-switcher bg-slate-100 dark:bg-slate-800 p-1 rounded-lg flex text-[10px] font-bold relative">
                <button @click="setWorkspace('retail')"
                    :class="workspace === 'retail' ? 'bg-white dark:bg-slate-700 text-slate-800 dark:text-white shadow-sm' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'"
                    class="workspace-switch-btn flex-1 py-1.5 rounded-md transition-all flex items-center justify-center gap-1.5 z-10 text-center">
                    <i class='bx bx-shopping-bag'></i> <span class="sidebar-text truncate">Retail</span>
                </button>
                <button @click="setWorkspace('core')"
                    :class="workspace === 'core' ? 'bg-white dark:bg-slate-700 text-slate-800 dark:text-white shadow-sm' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'"
                    class="workspace-switch-btn flex-1 py-1.5 rounded-md transition-all flex items-center justify-center gap-1.5 z-10 text-center">
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

            <a href="{{ route('kasir.pos') }}"
                class="nav-item flex items-center px-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('kasir.pos') ? 'bg-indigo-50 dark:bg-indigo-500/10 text-primary dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                <i
                    class='bx bx-desktop text-sm mr-2 {{ request()->routeIs('kasir.pos') ? 'opacity-100' : 'opacity-70 group-hover:opacity-100' }} transition-opacity shrink-0'></i>
                <span
                    class="sidebar-text text-xs {{ request()->routeIs('kasir.pos') ? 'font-semibold' : 'font-medium' }} transition-opacity duration-300">POS
                    System</span>
            </a>

            <a href="{{ route('kasir.transactions') }}"
                class="nav-item flex items-center px-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('kasir.transactions*') ? 'bg-indigo-50 dark:bg-indigo-500/10 text-primary dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                <i
                    class='bx bx-receipt text-sm mr-2 {{ request()->routeIs('kasir.transactions*') ? 'opacity-100' : 'opacity-70 group-hover:opacity-100' }} transition-opacity shrink-0'></i>
                <span
                    class="sidebar-text text-xs {{ request()->routeIs('kasir.transactions*') ? 'font-semibold' : 'font-medium' }} transition-opacity duration-300">Riwayat
                    Transaksi</span>
            </a>

            <a href="{{ route('kasir.shift-history') }}"
                class="nav-item flex items-center px-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('kasir.shift-history') ? 'bg-indigo-50 dark:bg-indigo-500/10 text-primary dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                <i
                    class='bx bx-time-five text-sm mr-2 {{ request()->routeIs('kasir.shift-history') ? 'opacity-100' : 'opacity-70 group-hover:opacity-100' }} transition-opacity shrink-0'></i>
                <span
                    class="sidebar-text text-xs {{ request()->routeIs('kasir.shift-history') ? 'font-semibold' : 'font-medium' }} transition-opacity duration-300">Riwayat
                    Shift</span>
            </a>

            <p class="sidebar-text px-2 text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 mt-3 opacity-80 whitespace-nowrap">Supplier</p>

            <a href="{{ route('kasir.terima-barang') }}"
                class="nav-item flex items-center justify-between px-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('kasir.terima-barang') ? 'bg-indigo-50 dark:bg-indigo-500/10 text-primary dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                <div class="flex items-center">
                    <i class='bx bx-package text-sm mr-2 {{ request()->routeIs('kasir.terima-barang') ? 'opacity-100' : 'opacity-70 group-hover:opacity-100' }} transition-opacity shrink-0'></i>
                    <span class="sidebar-text text-xs {{ request()->routeIs('kasir.terima-barang') ? 'font-semibold' : 'font-medium' }} transition-opacity duration-300">Terima Barang</span>
                </div>
                @php $pendingBatch = \App\Models\ConsignmentBatch::where('status','REQUESTED')->count(); @endphp
                @if($pendingBatch > 0)
                <span class="sidebar-text text-[10px] bg-blue-500 text-white font-bold px-1.5 py-0.5 rounded-full leading-none">{{ $pendingBatch }}</span>
                @endif
            </a>

            <a href="{{ route('kasir.laporan-supplier') }}"
                class="nav-item flex items-center px-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('kasir.laporan-supplier') ? 'bg-indigo-50 dark:bg-indigo-500/10 text-primary dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                <i class='bx bx-bar-chart-alt-2 text-sm mr-2 {{ request()->routeIs('kasir.laporan-supplier') ? 'opacity-100' : 'opacity-70 group-hover:opacity-100' }} transition-opacity shrink-0'></i>
                <span class="sidebar-text text-xs {{ request()->routeIs('kasir.laporan-supplier') ? 'font-semibold' : 'font-medium' }} transition-opacity duration-300">Laporan Supplier</span>
            </a>

            <a href="{{ route('kasir.supplier-daily-ops') }}"
                class="nav-item flex items-center px-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('kasir.supplier-daily-ops') ? 'bg-indigo-50 dark:bg-indigo-500/10 text-primary dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                <i class='bx bx-notepad text-sm mr-2 {{ request()->routeIs('kasir.supplier-daily-ops') ? 'opacity-100' : 'opacity-70 group-hover:opacity-100' }} transition-opacity shrink-0'></i>
                <span class="sidebar-text text-xs {{ request()->routeIs('kasir.supplier-daily-ops') ? 'font-semibold' : 'font-medium' }} transition-opacity duration-300">Operasional Supplier</span>
            </a>
        @else
            {{-- ADMIN --}}

            {{-- DASHBOARD (ALWAYS VISIBLE) --}}
            <a href="{{ route('admin.dashboard') }}"
                class="nav-item flex items-center px-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-50 dark:bg-indigo-500/10 text-primary dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                <i
                    class='bx bxs-dashboard text-sm mr-2 {{ request()->routeIs('admin.dashboard') ? 'opacity-100' : 'opacity-70 group-hover:opacity-100' }} transition-opacity shrink-0'></i>
                <span
                    class="sidebar-text text-xs {{ request()->routeIs('admin.dashboard') ? 'font-semibold' : 'font-medium' }} transition-opacity duration-300">Dashboard</span>
            </a>

            {{-- WORKSPACE: RETAIL --}}
            <div x-show="workspace === 'retail'" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-x-[-10px]" x-transition:enter-end="opacity-100 translate-x-0"
                class="mt-2 space-y-0.5">

                <a href="{{ route('admin.pos') }}"
                    class="nav-item flex items-center px-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('admin.pos') ? 'bg-indigo-50 dark:bg-indigo-500/10 text-primary dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                    <i class='bx bx-desktop text-sm mr-2 opacity-70 group-hover:opacity-100'></i>
                    <span class="sidebar-text text-xs font-medium transition-opacity duration-300">POS System</span>
                </a>

                @php
                    $pendingProductsCount = \App\Models\Product::where('approvalStatus', 'PENDING')->count();
                    $actionableConsignmentBatches = \App\Models\ConsignmentBatch::whereIn('status', ['REQUESTED', 'PENDING_SETTLEMENT'])->count();
                @endphp

                <a href="{{ route('admin.products') }}"
                    class="nav-item flex items-center justify-between px-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('admin.products*', 'admin.product-review') ? 'bg-indigo-50 dark:bg-indigo-500/10 text-primary dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                    <div class="flex items-center">
                        <i class='bx bx-package text-sm mr-2 opacity-70 group-hover:opacity-100'></i>
                        <span class="sidebar-text text-xs font-medium transition-opacity duration-300">Produk</span>
                    </div>
                    @if($pendingProductsCount > 0)
                        <span
                            class="sidebar-text bg-rose-500 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-full min-w-[18px] text-center">{{ $pendingProductsCount }}</span>
                    @endif
                </a>

                <a href="{{ route('admin.stock-mutation') }}"
                    class="nav-item flex items-center px-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('admin.stock-mutation', 'admin.stock-adjustment') ? 'bg-indigo-50 dark:bg-indigo-500/10 text-primary dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                    <i class='bx bx-transfer-alt text-sm mr-2 opacity-70 group-hover:opacity-100'></i>
                    <span class="sidebar-text text-xs font-medium transition-opacity duration-300">Inventori</span>
                </a>

                <a href="{{ route('admin.suppliers') }}"
                    class="nav-item flex items-center justify-between px-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('admin.suppliers*', 'admin.consignment-batches', 'admin.consignment-report', 'admin.supplier-daily-ops') ? 'bg-indigo-50 dark:bg-indigo-500/10 text-primary dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                    <div class="flex items-center">
                        <i class='bx bx-store-alt text-sm mr-2 opacity-70 group-hover:opacity-100'></i>
                        <span class="sidebar-text text-xs font-medium transition-opacity duration-300">Suplai</span>
                    </div>
                    @if($actionableConsignmentBatches > 0)
                        <span
                            class="sidebar-text bg-rose-500 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-full min-w-[18px] text-center">{{ $actionableConsignmentBatches }}</span>
                    @endif
                </a>

                <a href="{{ route('admin.supplier-daily-ops') }}"
                    class="nav-item flex items-center px-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('admin.supplier-daily-ops') ? 'bg-indigo-50 dark:bg-indigo-500/10 text-primary dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                    <i class='bx bx-notepad text-sm mr-2 opacity-70 group-hover:opacity-100'></i>
                    <span class="sidebar-text text-xs font-medium transition-opacity duration-300">Operasional Supplier</span>
                </a>

                <a href="{{ route('admin.transactions') }}"
                    class="nav-item flex items-center px-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('admin.transactions*', 'admin.manual-transaction', 'admin.reports.balance-sheet') ? 'bg-indigo-50 dark:bg-indigo-500/10 text-primary dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                    <i class='bx bx-receipt text-sm mr-2 opacity-70 group-hover:opacity-100'></i>
                    <span class="sidebar-text text-xs font-medium transition-opacity duration-300">Keuangan</span>
                </a>

                <a href="{{ route('admin.kasir-history') }}"
                    class="nav-item flex items-center px-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('admin.kasir-history') ? 'bg-indigo-50 dark:bg-indigo-500/10 text-primary dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                    <i class='bx bx-user-check text-sm mr-2 opacity-70 group-hover:opacity-100'></i>
                    <span class="sidebar-text text-xs font-medium transition-opacity duration-300">Monitoring Kasir</span>
                </a>

                <a href="{{ route('admin.retail-members.index') }}"
                    class="nav-item flex items-center px-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('admin.retail-members*') ? 'bg-indigo-50 dark:bg-indigo-500/10 text-primary dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                    <i class='bx bx-user-pin text-sm mr-2 opacity-70 group-hover:opacity-100'></i>
                    <span class="sidebar-text text-xs font-medium transition-opacity duration-300">Member Retail</span>
                </a>

            </div>

            {{-- WORKSPACE: CORE (KOPERASI) --}}
            <div x-show="workspace === 'core'" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-x-[10px]" x-transition:enter-end="opacity-100 translate-x-0"
                style="display: none;" class="mt-2 space-y-0.5">

                <a href="{{ route('admin.members.index') }}"
                    class="nav-item flex items-center px-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('admin.members*') ? 'bg-indigo-50 dark:bg-indigo-500/10 text-primary dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                    <i class='bx bx-group text-sm mr-2 opacity-70 group-hover:opacity-100'></i>
                    <span class="sidebar-text text-xs font-medium transition-opacity duration-300">Anggota Koperasi</span>
                </a>

                <a href="{{ route('admin.reports.monthly-financial') }}"
                    class="nav-item flex items-center px-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('admin.reports.monthly-financial', 'admin.manual-transaction') ? 'bg-indigo-50 dark:bg-indigo-500/10 text-primary dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                    <i class='bx bx-file text-sm mr-2 opacity-70 group-hover:opacity-100'></i>
                    <span class="sidebar-text text-xs font-medium transition-opacity duration-300">Payroll</span>
                </a>

                <a href="{{ route('admin.loans') }}"
                    class="nav-item flex items-center px-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('admin.loans*') ? 'bg-indigo-50 dark:bg-indigo-500/10 text-primary dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                    <i class='bx bx-money text-sm mr-2 opacity-70 group-hover:opacity-100'></i>
                    <span class="sidebar-text text-xs font-medium transition-opacity duration-300">Pinjaman Anggota</span>
                </a>

                <a href="{{ route('admin.rat-report') }}"
                    class="nav-item flex items-center px-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('admin.rat-report') ? 'bg-indigo-50 dark:bg-indigo-500/10 text-primary dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                    <i class='bx bx-pie-chart-alt-2 text-sm mr-2 opacity-70 group-hover:opacity-100'></i>
                    <span class="sidebar-text text-xs font-medium transition-opacity duration-300">Laporan RAT</span>
                </a>

                <a href="{{ route('admin.rat-detail') }}"
                    class="nav-item flex items-center px-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('admin.rat-detail') ? 'bg-indigo-50 dark:bg-indigo-500/10 text-primary dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                    <i class='bx bx-spreadsheet text-sm mr-2 opacity-70 group-hover:opacity-100'></i>
                    <span class="sidebar-text text-xs font-medium transition-opacity duration-300">RAT Akuntansi</span>
                </a>
            </div>

            {{-- SYSTEM (ADMIN ONLY) --}}
            @if(auth()->user()->isSuperAdmin() || auth()->user()->isDeveloper() || auth()->user()->isAdmin())
                <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800">
                    <p
                        class="sidebar-text px-2 text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 opacity-80 whitespace-nowrap">
                        System
                    </p>

                    @if(auth()->user()->isDeveloper())
                        <a href="{{ route('developer.work-logs') }}"
                            class="nav-item flex items-center px-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('developer.work-logs') ? 'bg-indigo-50 dark:bg-indigo-500/10 text-primary dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                            <i class='bx bx-task text-sm mr-2 opacity-70 group-hover:opacity-100'></i>
                            <span class="sidebar-text text-xs font-medium transition-opacity duration-300">Log Jam Kerja</span>
                        </a>
                    @endif

                    <a href="{{ route('admin.developer-payroll') }}"
                        class="nav-item flex items-center px-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('admin.developer-payroll') ? 'bg-indigo-50 dark:bg-indigo-500/10 text-primary dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                        <i class='bx bx-time-five text-sm mr-2 opacity-70 group-hover:opacity-100'></i>
                        <span class="sidebar-text text-xs font-medium transition-opacity duration-300">Payroll Developer</span>
                    </a>

                    <a href="{{ route('admin.activity-logs') }}"
                        class="nav-item flex items-center px-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('admin.activity-logs') ? 'bg-indigo-50 dark:bg-indigo-500/10 text-primary dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                        <i class='bx bx-history text-sm mr-2 opacity-70 group-hover:opacity-100'></i>
                        <span class="sidebar-text text-xs font-medium transition-opacity duration-300">Activity Logs</span>
                    </a>

                    <a href="{{ route('admin.users') }}"
                        class="nav-item flex items-center px-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('admin.users*') ? 'bg-indigo-50 dark:bg-indigo-500/10 text-primary dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                        <i class='bx bx-bg-group text-sm mr-2 opacity-70 group-hover:opacity-100'></i>
                        <span class="sidebar-text text-xs font-medium transition-opacity duration-300">Settings & Logs</span>
                    </a>
                </div>
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
