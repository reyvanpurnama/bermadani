<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Supplier Dashboard') - {{ coop_config('name') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=SF+Pro+Display:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
                    fontFamily: { sans: ['-apple-system', 'BlinkMacSystemFont', '"SF Pro Display"', '"Plus Jakarta Sans"', 'Inter', 'sans-serif'] },
                    colors: {
                        primary: '#155A6B',
                        primaryHover: '#1a6b80',
                        secondary: '#f5f5f7',
                        darkBg: '#09090b',
                        darkCard: '#18181b'
                    }
                }
            }
        }
    </script>

    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "SF Pro Display", "Plus Jakarta Sans", "Inter", sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: #d4d4d8;
            border-radius: 10px;
        }

        .dark ::-webkit-scrollbar-thumb {
            background: #3f3f46;
        }

        .apexcharts-tooltip {
            z-index: 100 !important;
            border-radius: 12px !important;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1) !important;
        }

        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="bg-[#f5f5f7] text-zinc-800 antialiased dark:bg-[#09090b] dark:text-zinc-200 transition-colors duration-300 selection:bg-[#155A6B] selection:text-white"
    x-data="{ sidebarOpen: false, sidebarCollapsed: localStorage.getItem('supplier-sidebar-collapsed') === 'true' }">

    <div id="sidebar-overlay"
        class="fixed inset-0 bg-black/40 z-40 hidden opacity-0 transition-opacity duration-300 backdrop-blur-md"
        :class="{'hidden': !sidebarOpen, 'opacity-0': !sidebarOpen, 'opacity-100': sidebarOpen}"
        @click="sidebarOpen = false"></div>

    <!-- Apple Frosted Glass Sidebar -->
    <aside id="supplier-sidebar"
        class="hidden lg:flex fixed inset-y-0 left-0 bg-white/80 dark:bg-zinc-900/80 backdrop-blur-2xl border-r border-zinc-200/80 dark:border-zinc-800/80 flex-col z-50 transition-all duration-300 shadow-xl md:shadow-none overflow-hidden"
        :class="{'-translate-x-full': !sidebarOpen, 'translate-x-0': sidebarOpen, 'md:translate-x-0': true, 'w-[200px]': !sidebarCollapsed, 'w-[72px]': sidebarCollapsed}">

        <!-- Sidebar Brand Header -->
        <div id="supplier-sidebar-header"
            class="h-16 flex items-center px-4 border-b border-zinc-200/80 dark:border-zinc-800/80 shrink-0 transition-all duration-300"
            :class="{'justify-center': sidebarCollapsed}">
            <div class="flex items-center gap-2.5 overflow-hidden whitespace-nowrap">
                <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-[#155A6B] to-teal-500 flex items-center justify-center text-white shrink-0 shadow-md shadow-[#155A6B]/20">
                    <i class='bx bxs-store-alt text-base'></i>
                </div>
                <span
                    class="supplier-sidebar-text text-sm font-extrabold tracking-tight text-zinc-900 dark:text-white leading-none transition-opacity duration-300"
                    :class="{'hidden': sidebarCollapsed}">Mitra<span class="text-[#155A6B] dark:text-teal-400">Panel</span></span>
            </div>
            <button id="supplier-sidebar-close"
                class="md:hidden ml-auto text-zinc-400 hover:text-rose-500 transition-colors"
                @click="sidebarOpen = false">
                <i class='bx bx-x text-2xl'></i>
            </button>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto overflow-x-hidden">
            <p class="supplier-sidebar-text px-2 text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider mb-2 mt-1 opacity-90 whitespace-nowrap transition-opacity duration-300"
                :class="{'hidden': sidebarCollapsed}">Ringkasan</p>

            <a href="{{ route('supplier.dashboard') }}"
                class="supplier-nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 group whitespace-nowrap {{ request()->routeIs('supplier.dashboard') ? 'bg-[#155A6B] text-white shadow-md shadow-[#155A6B]/25 font-semibold' : 'text-zinc-500 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800/60 hover:text-zinc-900 dark:hover:text-white' }}"
                :class="{'justify-center px-2': sidebarCollapsed}">
                <i class='bx bxs-dashboard text-lg shrink-0'></i>
                <span class="supplier-sidebar-text text-xs transition-opacity duration-300"
                    :class="{'hidden': sidebarCollapsed}">Dasbor</span>
            </a>

            <p class="supplier-sidebar-text px-2 text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider mb-2 mt-5 opacity-90 whitespace-nowrap transition-opacity duration-300"
                :class="{'hidden': sidebarCollapsed}">Katalog & Penjualan</p>

            <a href="{{ route('supplier.products.index') }}"
                class="supplier-nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 group whitespace-nowrap {{ request()->routeIs('supplier.products*') ? 'bg-[#155A6B] text-white shadow-md shadow-[#155A6B]/25 font-semibold' : 'text-zinc-500 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800/60 hover:text-zinc-900 dark:hover:text-white' }}"
                :class="{'justify-center px-2': sidebarCollapsed}">
                <i class='bx bx-box text-lg shrink-0'></i>
                <span class="supplier-sidebar-text text-xs transition-opacity duration-300"
                    :class="{'hidden': sidebarCollapsed}">Produk Saya</span>
            </a>

            <!-- Akun Section -->
            <p class="supplier-sidebar-text px-2 text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider mb-2 mt-5 opacity-90 whitespace-nowrap transition-opacity duration-300"
                :class="{'hidden': sidebarCollapsed}">Pengaturan</p>

            <a href="{{ route('supplier.profile') }}"
                class="supplier-nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 group whitespace-nowrap {{ request()->routeIs('supplier.profile') ? 'bg-[#155A6B] text-white shadow-md shadow-[#155A6B]/25 font-semibold' : 'text-zinc-500 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800/60 hover:text-zinc-900 dark:hover:text-white' }}"
                :class="{'justify-center px-2': sidebarCollapsed}">
                <i class='bx bx-user-circle text-lg shrink-0'></i>
                <span class="supplier-sidebar-text text-xs transition-opacity duration-300"
                    :class="{'hidden': sidebarCollapsed}">Profil Saya</span>
            </a>

        </nav>

        <!-- Sidebar Collapse Toggle -->
        <div id="supplier-sidebar-collapse"
            class="hidden md:flex items-center gap-3 px-4 py-3 border-t border-zinc-200/80 dark:border-zinc-800/80 cursor-pointer hover:bg-zinc-100 dark:hover:bg-zinc-800/50 transition-colors text-zinc-500 hover:text-[#155A6B] dark:hover:text-teal-400 whitespace-nowrap overflow-hidden"
            @click="sidebarCollapsed = !sidebarCollapsed; localStorage.setItem('supplier-sidebar-collapsed', sidebarCollapsed)"
            :class="{'justify-center': sidebarCollapsed}">
            <i class='bx bx-chevrons-left text-xl transition-transform duration-300 shrink-0'
                :class="{'rotate-180': sidebarCollapsed}"></i>
            <span class="supplier-sidebar-text text-xs font-medium transition-opacity duration-300"
                :class="{'hidden': sidebarCollapsed}">Ciutkan</span>
        </div>

        <!-- Sidebar User Profile Footer -->
        <div id="supplier-user-profile"
            class="p-3 border-t border-zinc-200/80 dark:border-zinc-800/80 overflow-hidden transition-all duration-300"
            :class="{'justify-center': sidebarCollapsed}">
            <div class="flex items-center gap-3 p-2 rounded-xl hover:bg-zinc-100 dark:hover:bg-zinc-800/60 transition-colors cursor-pointer group whitespace-nowrap"
                onclick="document.getElementById('logout-form').submit()">
                <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=155A6B&color=fff"
                    class="w-7 h-7 rounded-full object-cover ring-2 ring-white dark:ring-zinc-700 shadow-sm shrink-0">
                <div class="supplier-sidebar-text flex-1 overflow-hidden transition-opacity duration-300"
                    :class="{'hidden': sidebarCollapsed}">
                    <h4 class="text-xs font-bold text-zinc-900 dark:text-white truncate">{{ auth()->user()->name }}</h4>
                    <p class="text-[10px] text-zinc-400 truncate flex items-center gap-1">
                        <i class='bx bx-log-out text-rose-500'></i> Keluar
                    </p>
                </div>
            </div>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                @csrf
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div id="main-content" class="min-h-screen flex flex-col transition-all duration-300"
        :class="{'md:ml-[200px]': !sidebarCollapsed, 'md:ml-[72px]': sidebarCollapsed}">

        @php
            $unreadCount = \App\Models\SupplierNotification::where('supplierId', auth()->guard('supplier')->id())
                ->where('isRead', false)
                ->count();
        @endphp

        <!-- Apple Frosted Top Navigation Bar -->
        <header
            class="h-16 bg-white/80 dark:bg-zinc-900/80 backdrop-blur-md border-b border-zinc-200/80 dark:border-zinc-800/80 flex items-center justify-between px-4 sm:px-8 sticky top-0 z-30 transition-colors duration-300">
            <div class="flex items-center gap-3">
                <div>
                    <h1 class="text-sm sm:text-base font-extrabold text-zinc-900 dark:text-white tracking-tight">
                        @yield('header-title', 'Dasbor Mitra')</h1>
                    <p class="text-[11px] text-zinc-500 dark:text-zinc-400 font-medium">
                        @yield('header-subtitle', 'Ringkasan performa penjualan produk Anda')</p>
                </div>
            </div>

            <!-- Topbar Actions -->
            <div class="flex items-center gap-2">
                <button id="theme-toggle"
                    class="w-9 h-9 flex items-center justify-center text-zinc-500 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800/80 rounded-xl transition-all focus:outline-none group">
                    <i id="theme-icon"
                        class='bx bx-moon text-xl group-hover:text-[#155A6B] dark:group-hover:text-teal-400 transition-colors'></i>
                </button>
                <a href="{{ route('supplier.notifications') }}"
                    class="w-9 h-9 flex items-center justify-center relative text-zinc-500 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800/80 rounded-xl transition-all group">
                    <i class='bx bx-bell text-xl group-hover:text-[#155A6B] dark:group-hover:text-teal-400 transition-colors'></i>
                    @if($unreadCount > 0)
                        <span
                            class="absolute top-1.5 right-1.5 w-2 h-2 bg-rose-500 rounded-full border-2 border-white dark:border-zinc-900"></span>
                    @endif
                </a>
            </div>
        </header>

        <main class="flex-1 p-4 sm:p-6 lg:p-8 space-y-6 overflow-y-auto pb-24 lg:pb-8">
            @yield('content')
            {{ $slot ?? '' }}
        </main>
    </div>

    <!-- Mobile Bottom Navigation -->
    @include('partials.supplier-mobile-nav')

    @livewireScripts
    <script>
        // Dark Mode Toggle Script
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