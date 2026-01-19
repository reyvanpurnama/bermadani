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
                x-transition:enter-start="opacity-0 translate-x-[-10px]" x-transition:enter-end="opacity-100 translate-x-0"
                class="space-y-1 mt-4">

                {{-- Retail Ops Group --}}
                <div x-data="{ open: {{ request()->routeIs('admin.pos') ? 'true' : 'false' }} }" class="space-y-1">
                    <button @click="open = !open"
                        class="w-full flex items-center justify-between px-2 py-1.5 text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 transition-colors group">
                        <span class="text-[10px] font-bold uppercase tracking-widest px-2 sidebar-text">Retail Ops</span>
                        <i class="bx bx-chevron-down text-sm transition-transform duration-200 sidebar-text"
                            :class="open ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="open" x-collapse style="display: none;">
                        <a href="{{ route('admin.pos') }}"
                            class="nav-item flex items-center px-4 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('admin.pos') ? 'bg-indigo-50 dark:bg-indigo-500/10 text-primary dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                            <i
                                class='bx bx-desktop text-sm mr-2 {{ request()->routeIs('admin.pos') ? 'opacity-100' : 'opacity-70 group-hover:opacity-100' }} transition-opacity shrink-0'></i>
                            <span
                                class="sidebar-text text-xs {{ request()->routeIs('admin.pos') ? 'font-semibold' : 'font-medium' }} transition-opacity duration-300">POS
                                System</span>
                        </a>
                    </div>
                </div>

                {{-- Inventory Group --}}
                @php
                    $pendingProductsCount = \App\Models\Product::where('approvalStatus', 'PENDING')->count();
                    $approvedProductsCount = \App\Models\Product::where('approvalStatus', 'APPROVED')
                        ->where(function($q) {
                            // Produk yang belum siap dijual (stock 0 atau status bukan ACTIVE)
                            $q->where('stock', '<=', 0)
                              ->orWhere('status', '!=', 'ACTIVE');
                        })
                        ->whereDoesntHave('consignmentItems', function($q) {
                            $q->whereHas('batch', function($b) {
                                // Exclude produk konsinyasi yang punya batch (apapun statusnya)
                                // Karena action-nya di halaman Batch Konsinyasi, bukan Katalog Produk
                                $b->whereIn('status', ['REQUESTED', 'ACTIVE', 'PENDING_SETTLEMENT']);
                            });
                        })
                        ->whereDoesntHave('restockRequests', function($q) {
                            $q->whereIn('status', ['PENDING', 'COMPLETED']);
                        })
                        ->count();
                    $pendingSuppliersCount = \App\Models\Supplier::where('status', 'PENDING')->count();
                    $hasPendingInventory = $pendingProductsCount > 0 || $pendingSuppliersCount > 0 || $approvedProductsCount > 0;
                @endphp
                <div x-data="{ open: {{ request()->routeIs('admin.products*', 'admin.stock*', 'admin.restock*', 'admin.suppliers*', 'admin.product-review') ? 'true' : 'false' }} }"
                    class="space-y-1">
                    <button @click="open = !open"
                        class="w-full flex items-center justify-between px-2 py-1.5 text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 transition-colors group">
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-bold uppercase tracking-widest px-2 sidebar-text">Inventory</span>
                            @if($hasPendingInventory)
                                <span class="w-2 h-2 bg-rose-500 rounded-full animate-pulse"></span>
                            @endif
                        </div>
                        <i class="bx bx-chevron-down text-sm transition-transform duration-200 sidebar-text"
                            :class="open ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="open" x-collapse style="display: none;">
                        <a href="{{ route('admin.products') }}"
                            class="nav-item flex items-center justify-between pl-4 pr-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('admin.products*') ? 'text-primary dark:text-indigo-400 font-semibold bg-indigo-50/50 dark:bg-indigo-500/5' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                            <div class="flex items-center">
                                <i class='bx bx-package text-sm mr-2 opacity-70'></i>
                                <span class="sidebar-text text-xs transition-opacity duration-300">Katalog Produk</span>
                            </div>
                            @if($approvedProductsCount > 0)
                                <span
                                    class="bg-rose-500 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-full min-w-[18px] text-center">{{ $approvedProductsCount }}</span>
                            @endif
                        </a>
                        <a href="{{ route('admin.stock-mutation') }}"
                            class="nav-item flex items-center pl-4 pr-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('admin.stock-mutation') ? 'text-primary dark:text-indigo-400 font-semibold bg-indigo-50/50 dark:bg-indigo-500/5' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                            <i class='bx bx-transfer-alt text-sm mr-2 opacity-70'></i>
                            <span class="sidebar-text text-xs transition-opacity duration-300">Riwayat Stok</span>
                        </a>
                        <a href="{{ route('admin.stock-adjustment') }}"
                            class="nav-item flex items-center pl-4 pr-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('admin.stock-adjustment') ? 'text-primary dark:text-indigo-400 font-semibold bg-indigo-50/50 dark:bg-indigo-500/5' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                            <i class='bx bx-slider-alt text-sm mr-2 opacity-70'></i>
                            <span class="sidebar-text text-xs transition-opacity duration-300">Stock Opname</span>
                        </a>
                        <a href="{{ route('admin.product-review') }}"
                            class="nav-item flex items-center justify-between pl-4 pr-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('admin.product-review') ? 'text-primary dark:text-indigo-400 font-semibold bg-indigo-50/50 dark:bg-indigo-500/5' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                            <div class="flex items-center">
                                <i class='bx bx-check-shield text-sm mr-2 opacity-70'></i>
                                <span class="sidebar-text text-xs transition-opacity duration-300">Approval Produk</span>
                            </div>
                            @if($pendingProductsCount > 0)
                                <span
                                    class="bg-rose-500 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-full min-w-[18px] text-center">{{ $pendingProductsCount }}</span>
                            @endif
                        </a>
                        <a href="{{ route('admin.suppliers') }}"
                            class="nav-item flex items-center justify-between pl-4 pr-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('admin.suppliers*') ? 'text-primary dark:text-indigo-400 font-semibold bg-indigo-50/50 dark:bg-indigo-500/5' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                            <div class="flex items-center">
                                <i class='bx bx-store-alt text-sm mr-2 opacity-70'></i>
                                <span class="sidebar-text text-xs transition-opacity duration-300">Supplier</span>
                            </div>
                            @if($pendingSuppliersCount > 0)
                                <span
                                    class="bg-rose-500 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-full min-w-[18px] text-center">{{ $pendingSuppliersCount }}</span>
                            @endif
                        </a>
                    </div>
                </div>

                {{-- Konsinyasi Group --}}
                @php
                    $activeConsignmentBatches = \App\Models\ConsignmentBatch::where('status', 'ACTIVE')->count();
                @endphp
                <div x-data="{ open: {{ request()->routeIs('admin.consignment*') ? 'true' : 'false' }} }" class="space-y-1">
                    <button @click="open = !open"
                        class="w-full flex items-center justify-between px-2 py-1.5 text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 transition-colors group">
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-bold uppercase tracking-widest px-2 sidebar-text">Konsinyasi</span>
                            @if($activeConsignmentBatches > 0)
                                <span class="w-2 h-2 bg-indigo-500 rounded-full animate-pulse"></span>
                            @endif
                        </div>
                        <i class="bx bx-chevron-down text-sm transition-transform duration-200 sidebar-text"
                            :class="open ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="open" x-collapse style="display: none;">
                        <a href="{{ route('admin.consignment-batches') }}"
                            class="nav-item flex items-center justify-between pl-4 pr-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('admin.consignment-batches') ? 'text-primary dark:text-indigo-400 font-semibold bg-indigo-50/50 dark:bg-indigo-500/5' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                            <div class="flex items-center">
                                <i class='bx bx-notepad text-sm mr-2 opacity-70'></i>
                                <span class="sidebar-text text-xs transition-opacity duration-300">Daftar Batch</span>
                            </div>
                            @if($activeConsignmentBatches > 0)
                                <span class="bg-indigo-500 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-full min-w-[18px] text-center">{{ $activeConsignmentBatches }}</span>
                            @endif
                        </a>
                        <a href="{{ route('admin.consignment-report') }}"
                            class="nav-item flex items-center pl-4 pr-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('admin.consignment-report') ? 'text-primary dark:text-indigo-400 font-semibold bg-indigo-50/50 dark:bg-indigo-500/5' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                            <i class='bx bx-file text-sm mr-2 opacity-70'></i>
                            <span class="sidebar-text text-xs transition-opacity duration-300">Laporan</span>
                        </a>
                    </div>
                </div>

                {{-- Keuangan Group (Single but kept for structure) --}}
                <div x-data="{ open: {{ request()->routeIs('admin.manual-transaction') ? 'true' : 'false' }} }"
                    class="space-y-1">
                    <button @click="open = !open"
                        class="w-full flex items-center justify-between px-2 py-1.5 text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 transition-colors group">
                        <span class="text-[10px] font-bold uppercase tracking-widest px-2 sidebar-text">Keuangan</span>
                        <i class="bx bx-chevron-down text-sm transition-transform duration-200 sidebar-text"
                            :class="open ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="open" x-collapse style="display: none;">
                        <a href="{{ route('admin.manual-transaction', ['unit' => 'BISNIS']) }}"
                            class="nav-item flex items-center pl-4 pr-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->fullUrlIs(route('admin.manual-transaction', ['unit' => 'BISNIS'])) ? 'text-primary dark:text-indigo-400 font-semibold bg-indigo-50/50 dark:bg-indigo-500/5' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                            <i class='bx bx-transfer text-sm mr-2 opacity-70'></i>
                            <span class="sidebar-text text-xs transition-opacity duration-300">Catat Keuangan</span>
                        </a>
                    </div>
                </div>

                {{-- Laporan Group --}}
                <div x-data="{ open: {{ request()->routeIs('admin.transactions*') ? 'true' : 'false' }} }"
                    class="space-y-1">
                    <button @click="open = !open"
                        class="w-full flex items-center justify-between px-2 py-1.5 text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 transition-colors group">
                        <span class="text-[10px] font-bold uppercase tracking-widest px-2 sidebar-text">Laporan</span>
                        <i class="bx bx-chevron-down text-sm transition-transform duration-200 sidebar-text"
                            :class="open ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="open" x-collapse style="display: none;">
                        <a href="{{ route('admin.transactions') }}"
                            class="nav-item flex items-center pl-4 pr-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('admin.transactions*') && !request()->routeIs('admin.manual-transaction') ? 'text-primary dark:text-indigo-400 font-semibold bg-indigo-50/50 dark:bg-indigo-500/5' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                            <i class='bx bx-receipt text-sm mr-2 opacity-70'></i>
                            <span class="sidebar-text text-xs transition-opacity duration-300">Riwayat Penjualan</span>
                        </a>
                    </div>
                </div>

            </div>

            {{-- ADMIN: CORE (KOPERASI) MENU --}}
            {{-- ADMIN: CORE (KOPERASI) MENU --}}
            <div x-show="workspace === 'core'" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-x-[10px]" x-transition:enter-end="opacity-100 translate-x-0"
                style="display: none;" class="space-y-1 mt-4">

                {{-- Keanggotaan Group --}}
                <div x-data="{ open: {{ request()->routeIs('admin.members*') ? 'true' : 'false' }} }" class="space-y-1">
                    <button @click="open = !open"
                        class="w-full flex items-center justify-between px-2 py-1.5 text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 transition-colors group">
                        <span class="text-[10px] font-bold uppercase tracking-widest px-2 sidebar-text">Keanggotaan</span>
                        <i class="bx bx-chevron-down text-sm transition-transform duration-200 sidebar-text"
                            :class="open ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="open" x-collapse style="display: none;">
                        <a href="{{ route('admin.members.index') }}"
                            class="nav-item flex items-center pl-4 pr-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('admin.members*') ? 'text-primary dark:text-indigo-400 font-semibold bg-indigo-50/50 dark:bg-indigo-500/5' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                            <i class='bx bx-user-circle text-sm mr-2 opacity-70'></i>
                            <span class="sidebar-text text-xs transition-opacity duration-300">Anggota</span>
                        </a>
                    </div>
                </div>

                {{-- Simpan Pinjam Group --}}
                <div x-data="{ open: {{ request()->routeIs('admin.savings*', 'admin.loans*', 'admin.payments*') ? 'true' : 'false' }} }"
                    class="space-y-1">
                    <button @click="open = !open"
                        class="w-full flex items-center justify-between px-2 py-1.5 text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 transition-colors group">
                        <span class="text-[10px] font-bold uppercase tracking-widest px-2 sidebar-text">Simpan Pinjam</span>
                        <i class="bx bx-chevron-down text-sm transition-transform duration-200 sidebar-text"
                            :class="open ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="open" x-collapse style="display: none;">
                        <a href="{{ route('admin.savings') }}"
                            class="nav-item flex items-center pl-4 pr-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('admin.savings*') && !request()->routeIs('admin.payments*') ? 'text-primary dark:text-indigo-400 font-semibold bg-indigo-50/50 dark:bg-indigo-500/5' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                            <i class='bx bx-wallet text-sm mr-2 opacity-70'></i>
                            <span class="sidebar-text text-xs transition-opacity duration-300">Simpanan</span>
                        </a>
                        <a href="{{ route('admin.loans') }}"
                            class="nav-item flex items-center pl-4 pr-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('admin.loans*') ? 'text-primary dark:text-indigo-400 font-semibold bg-indigo-50/50 dark:bg-indigo-500/5' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                            <i class='bx bx-money text-sm mr-2 opacity-70'></i>
                            <span class="sidebar-text text-xs transition-opacity duration-300">Pinjaman</span>
                        </a>
                        <a href="{{ route('admin.payments.create') }}"
                            class="nav-item flex items-center pl-4 pr-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('admin.payments*') ? 'text-primary dark:text-indigo-400 font-semibold bg-indigo-50/50 dark:bg-indigo-500/5' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                            <i class='bx bx-plus-circle text-sm mr-2 opacity-70'></i>
                            <span class="sidebar-text text-xs transition-opacity duration-300">Catat Setoran</span>
                        </a>
                    </div>
                </div>

                {{-- Keuangan Group --}}
                <div x-data="{ open: {{ request()->routeIs('admin.manual-transaction', 'admin.reports*') ? 'true' : 'false' }} }"
                    class="space-y-1">
                    <button @click="open = !open"
                        class="w-full flex items-center justify-between px-2 py-1.5 text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 transition-colors group">
                        <span class="text-[10px] font-bold uppercase tracking-widest px-2 sidebar-text">Keuangan</span>
                        <i class="bx bx-chevron-down text-sm transition-transform duration-200 sidebar-text"
                            :class="open ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="open" x-collapse style="display: none;">
                        <a href="{{ route('admin.manual-transaction', ['unit' => 'KOPERASI']) }}"
                            class="nav-item flex items-center pl-4 pr-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->fullUrlIs(route('admin.manual-transaction', ['unit' => 'KOPERASI'])) ? 'text-primary dark:text-indigo-400 font-semibold bg-indigo-50/50 dark:bg-indigo-500/5' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                            <i class='bx bx-transfer text-sm mr-2 opacity-70'></i>
                            <span class="sidebar-text text-xs transition-opacity duration-300">Catat Keuangan</span>
                        </a>
                        <a href="{{ route('admin.reports.monthly-financial') }}"
                            class="nav-item flex items-center pl-4 pr-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('admin.reports.monthly-financial') ? 'text-primary dark:text-indigo-400 font-semibold bg-indigo-50/50 dark:bg-indigo-500/5' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                            <i class='bx bx-file text-sm mr-2 opacity-70'></i>
                            <span class="sidebar-text text-xs transition-opacity duration-300">Laporan Bulanan</span>
                        </a>
                    </div>
                </div>

            </div>

            @if(auth()->user()->isSuperAdmin() || auth()->user()->isDeveloper() || auth()->user()->isAdmin())
                <div class="mt-4 px-2">
                    <div
                        x-data="{ open: {{ request()->routeIs('admin.users*', 'admin.settings*', 'admin.activity-logs', 'admin.kasir-history', 'admin.developer-payroll') ? 'true' : 'false' }} }">
                        <button @click="open = !open"
                            class="w-full flex items-center justify-between py-1.5 text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 transition-colors group">
                            <span class="text-[10px] font-bold uppercase tracking-widest px-0 sidebar-text">System</span>
                            <i class="bx bx-chevron-down text-sm transition-transform duration-200 sidebar-text"
                                :class="open ? 'rotate-180' : ''"></i>
                        </button>
                        <div x-show="open" x-collapse style="display: none;" class="space-y-1 mt-1">
                            <a href="{{ route('admin.users') }}"
                                class="nav-item flex items-center pl-4 pr-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('admin.users*') ? 'text-primary dark:text-indigo-400 font-semibold bg-indigo-50/50 dark:bg-indigo-500/5' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                                <i class='bx bx-group text-sm mr-2 opacity-70'></i>
                                <span class="sidebar-text text-xs transition-opacity duration-300">Kelola User</span>
                            </a>
                            <a href="{{ route('admin.settings') }}"
                                class="nav-item flex items-center pl-4 pr-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('admin.settings*') ? 'text-primary dark:text-indigo-400 font-semibold bg-indigo-50/50 dark:bg-indigo-500/5' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                                <i class='bx bx-cog text-sm mr-2 opacity-70'></i>
                                <span class="sidebar-text text-xs transition-opacity duration-300">Pengaturan</span>
                            </a>
                            <a href="{{ route('admin.activity-logs') }}"
                                class="nav-item flex items-center pl-4 pr-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('admin.activity-logs') ? 'text-primary dark:text-indigo-400 font-semibold bg-indigo-50/50 dark:bg-indigo-500/5' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                                <i class='bx bx-history text-sm mr-2 opacity-70'></i>
                                <span class="sidebar-text text-xs transition-opacity duration-300">Activity Log</span>
                            </a>
                            <a href="{{ route('admin.kasir-history') }}"
                                class="nav-item flex items-center pl-4 pr-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('admin.kasir-history') ? 'text-primary dark:text-indigo-400 font-semibold bg-indigo-50/50 dark:bg-indigo-500/5' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                                <i class='bx bx-user-check text-sm mr-2 opacity-70'></i>
                                <span class="sidebar-text text-xs transition-opacity duration-300">Riwayat Kasir</span>
                            </a>
                            <a href="{{ route('admin.developer-payroll') }}"
                                class="nav-item flex items-center pl-4 pr-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('admin.developer-payroll') ? 'text-primary dark:text-indigo-400 font-semibold bg-indigo-50/50 dark:bg-indigo-500/5' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                                <i class='bx bx-money text-sm mr-2 opacity-70'></i>
                                <span class="sidebar-text text-xs transition-opacity duration-300">Payroll Dev</span>
                            </a>
                        </div>
                    </div>
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