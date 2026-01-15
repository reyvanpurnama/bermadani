<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Supplier Dashboard') - Koperasi UMB</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @livewireStyles

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        primary: '#4F46E5',
                        secondary: '#F8FAFC',
                        darkBg: '#0f172a',
                        darkCard: '#1e293b'
                    }
                }
            }
        }
    </script>

    <style>
        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        .dark ::-webkit-scrollbar-thumb {
            background: #475569;
        }

        .apexcharts-tooltip {
            z-index: 100 !important;
        }

        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="bg-secondary text-slate-800 antialiased dark:bg-darkBg dark:text-slate-200 transition-colors duration-300"
    x-data="{ sidebarOpen: false, sidebarCollapsed: localStorage.getItem('supplier-sidebar-collapsed') === 'true' }">

    <div id="sidebar-overlay"
        class="fixed inset-0 bg-black/50 z-40 hidden opacity-0 transition-opacity duration-300 backdrop-blur-sm"
        :class="{'hidden': !sidebarOpen, 'opacity-0': !sidebarOpen, 'opacity-100': sidebarOpen}"
        @click="sidebarOpen = false"></div>

    <!-- Sidebar -->
    <aside id="supplier-sidebar"
        class="fixed inset-y-0 left-0 bg-white dark:bg-darkCard border-r border-slate-200 dark:border-slate-700 flex flex-col z-50 transition-all duration-300 shadow-2xl md:shadow-none overflow-hidden"
        :class="{'-translate-x-full': !sidebarOpen, 'translate-x-0': sidebarOpen, 'md:translate-x-0': true, 'w-[180px]': !sidebarCollapsed, 'w-[70px]': sidebarCollapsed}">

        <div id="supplier-sidebar-header"
            class="h-16 flex items-center px-4 border-b border-slate-200 dark:border-slate-700 shrink-0 transition-all duration-300"
            :class="{'justify-center': sidebarCollapsed}">
            <div class="flex items-center gap-2 overflow-hidden whitespace-nowrap">
                <div class="w-6 h-6 bg-emerald-600 rounded flex items-center justify-center text-white shrink-0">
                    <i class='bx bxs-store-alt text-xs'></i>
                </div>
                <span
                    class="supplier-sidebar-text text-base font-bold tracking-tight text-slate-900 dark:text-white leading-none mt-0.5 transition-opacity duration-300"
                    :class="{'hidden': sidebarCollapsed}">Mitra<span class="text-emerald-600">Panel</span></span>
            </div>
            <button id="supplier-sidebar-close"
                class="md:hidden ml-auto text-slate-500 hover:text-rose-500 transition-colors"
                @click="sidebarOpen = false">
                <i class='bx bx-x text-2xl'></i>
            </button>
        </div>

        <nav class="flex-1 px-2.5 py-3 space-y-0.5 overflow-y-auto overflow-x-hidden">
            <p class="supplier-sidebar-text px-2 text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 mt-1 opacity-80 whitespace-nowrap transition-opacity duration-300"
                :class="{'hidden': sidebarCollapsed}">Ringkasan</p>

            <a href="{{ route('supplier.dashboard') }}"
                class="supplier-nav-item flex items-center gap-3 px-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('supplier.dashboard') ? 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}"
                :class="{'justify-center': sidebarCollapsed}">
                <i class='bx bxs-dashboard text-sm opacity-70 group-hover:opacity-100 transition-opacity shrink-0'></i>
                <span class="supplier-sidebar-text text-xs font-medium transition-opacity duration-300"
                    :class="{'hidden': sidebarCollapsed}">Dasbor</span>
            </a>

            <p class="supplier-sidebar-text px-2 text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 mt-4 opacity-80 whitespace-nowrap transition-opacity duration-300"
                :class="{'hidden': sidebarCollapsed}">Kelola</p>

            <a href="{{ route('supplier.products.index') }}"
                class="supplier-nav-item flex items-center gap-3 px-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('supplier.products*') ? 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}"
                :class="{'justify-center': sidebarCollapsed}">
                <i class='bx bx-box text-sm opacity-70 group-hover:opacity-100 transition-opacity shrink-0'></i>
                <span class="supplier-sidebar-text text-xs font-medium transition-opacity duration-300"
                    :class="{'hidden': sidebarCollapsed}">Produk Saya</span>
            </a>

            <a href="{{ route('supplier.sales') }}"
                class="supplier-nav-item flex items-center gap-3 px-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('supplier.sales') ? 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}"
                :class="{'justify-center': sidebarCollapsed}">
                <i class='bx bx-line-chart text-sm opacity-70 group-hover:opacity-100 transition-opacity shrink-0'></i>
                <span class="supplier-sidebar-text text-xs font-medium transition-opacity duration-300"
                    :class="{'hidden': sidebarCollapsed}">Laporan Penjualan</span>
            </a>

            <a href="{{ route('supplier.restock') }}"
                class="supplier-nav-item flex items-center gap-3 px-2 py-1.5 rounded-md transition-all group whitespace-nowrap {{ request()->routeIs('supplier.restock') ? 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}"
                :class="{'justify-center': sidebarCollapsed}">
                <i class='bx bx-package text-sm opacity-70 group-hover:opacity-100 transition-opacity shrink-0'></i>
                <span class="supplier-sidebar-text text-xs font-medium transition-opacity duration-300"
                    :class="{'hidden': sidebarCollapsed}">Batch Konsinyasi</span>
                @php
                    $requestedBatchCount = \App\Models\ConsignmentBatch::where('supplierId', auth()->guard('supplier')->id())->where('status', 'REQUESTED')->count();
                @endphp
                @if($requestedBatchCount > 0)
                    <span class="bg-blue-500 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-full animate-pulse"
                        :class="{'hidden': sidebarCollapsed}">{{ $requestedBatchCount }}</span>
                @endif
            </a>


        </nav>

        <div id="supplier-sidebar-collapse"
            class="hidden md:flex items-center gap-3 px-4 py-3 border-t border-slate-200 dark:border-slate-700 cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors text-slate-500 hover:text-emerald-600 whitespace-nowrap overflow-hidden"
            @click="sidebarCollapsed = !sidebarCollapsed; localStorage.setItem('supplier-sidebar-collapsed', sidebarCollapsed)"
            :class="{'justify-center': sidebarCollapsed}">
            <i class='bx bx-chevrons-left text-xl transition-transform duration-300 shrink-0'
                :class="{'rotate-180': sidebarCollapsed}"></i>
            <span class="supplier-sidebar-text text-xs font-medium transition-opacity duration-300"
                :class="{'hidden': sidebarCollapsed}">Ciutkan</span>
        </div>

        <div id="supplier-user-profile"
            class="p-2.5 border-t border-slate-100 dark:border-slate-700 overflow-hidden transition-all duration-300"
            :class="{'justify-center': sidebarCollapsed}">
            <div class="flex items-center gap-3 p-1.5 rounded-md hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors cursor-pointer group whitespace-nowrap"
                onclick="document.getElementById('logout-form').submit()">
                <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=10b981&color=fff"
                    class="w-6 h-6 rounded-full object-cover ring-1 ring-slate-200 dark:ring-slate-600 shrink-0">
                <div class="supplier-sidebar-text flex-1 overflow-hidden transition-opacity duration-300"
                    :class="{'hidden': sidebarCollapsed}">
                    <h4 class="text-xs font-semibold text-slate-800 dark:text-white truncate">{{ auth()->user()->name }}
                    </h4>
                    <p class="text-[9px] text-slate-400 truncate">Logout</p>
                </div>
            </div>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                @csrf
            </form>
        </div>
    </aside>

    <div id="main-content" class="min-h-screen flex flex-col transition-all duration-300"
        :class="{'md:ml-[180px]': !sidebarCollapsed, 'md:ml-[70px]': sidebarCollapsed}">

        <header
            class="h-16 bg-white dark:bg-darkCard border-b border-slate-200 dark:border-slate-700 flex items-center justify-between px-6 sticky top-0 z-20 transition-colors duration-300">
            <div class="flex items-center gap-4">
                <button id="sidebar-toggle"
                    class="md:hidden w-[34px] h-[34px] flex items-center justify-center text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-md transition-all"
                    @click="sidebarOpen = true">
                    <i class='bx bx-menu text-[20px]'></i>
                </button>
                <div>
                    <h1 class="text-[14px] font-bold text-slate-800 dark:text-white">
                        @yield('header-title', 'Dasbor Mitra')</h1>
                    <p class="text-[10px] text-slate-500">
                        @yield('header-subtitle', 'Ringkasan performa penjualan produk Anda')</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button id="theme-toggle"
                    class="w-[34px] h-[34px] flex items-center justify-center text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-md transition-all focus:outline-none group">
                    <i id="theme-icon"
                        class='bx bx-moon text-[20px] group-hover:text-emerald-600 transition-colors'></i>
                </button>
                <a href="{{ route('supplier.notifications') }}"
                    class="w-[34px] h-[34px] flex items-center justify-center relative text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-md transition-all group">
                    <i class='bx bx-bell text-[20px] group-hover:text-emerald-600 transition-colors'></i>
                    @if($unreadCount > 0)
                        <span
                            class="absolute top-1 right-1 w-2 h-2 bg-rose-500 rounded-full border-2 border-white dark:border-darkCard"></span>
                    @endif
                </a>
            </div>
        </header>

        <main class="flex-1 p-6 space-y-6 overflow-y-auto">
            @yield('content')
        </main>
    </div>

    @livewireScripts
    <script>
        // Dark Mode Logic
        const themeToggleBtn = document.getElementById('theme-toggle');
        const themeIcon = document.getElementById('theme-icon');
        const html = document.documentElement;
        let isDark = false;

        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            html.classList.add('dark');
            themeIcon.classList.replace('bx-moon', 'bx-sun');
            isDark = true;
        }

        themeToggleBtn.addEventListener('click', function () {
            html.classList.toggle('dark');
            isDark = html.classList.contains('dark');
            themeIcon.classList.replace(isDark ? 'bx-moon' : 'bx-sun', isDark ? 'bx-sun' : 'bx-moon');
            localStorage.setItem('color-theme', isDark ? 'dark' : 'light');
            if (typeof updateCharts === 'function') {
                updateCharts(isDark ? 'dark' : 'light');
            }
        });
    </script>
    @stack('scripts')
</body>

</html>