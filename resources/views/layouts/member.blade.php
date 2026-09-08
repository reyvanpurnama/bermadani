<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Portal Anggota') - {{ coop_config('legal_name', 'Koperasi Bermadani') }}</title>

    <!-- Google Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Outfit:wght@400;500;600;700;800&family=SF+Pro+Display:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('styles')

    <!-- Dark Mode Init -->
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "SF Pro Display", "Plus Jakarta Sans", sans-serif;
            -webkit-font-smoothing: antialiased;
            -webkit-tap-highlight-color: transparent;
        }

        .font-heading {
            font-family: 'Outfit', 'Plus Jakarta Sans', sans-serif;
        }

        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>

<body class="bg-[#f5f5f7] dark:bg-[#09090b] text-zinc-800 dark:text-zinc-200 min-h-screen font-sans selection:bg-[#155A6B] selection:text-white transition-colors duration-300 relative overflow-x-hidden"
    x-data="{ 
        sidebarOpen: false, 
        sidebarCollapsed: localStorage.getItem('member-sidebar-collapsed') === 'true',
        darkMode: localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches),
        toggleTheme() {
            this.darkMode = !this.darkMode;
            if (this.darkMode) {
                document.documentElement.classList.add('dark');
                localStorage.theme = 'dark';
            } else {
                document.documentElement.classList.remove('dark');
                localStorage.theme = 'light';
            }
        }
    }">

    <!-- 1. Apple Frosted Glass Desktop Sidebar -->
    <aside id="member-sidebar"
        class="hidden lg:flex fixed inset-y-0 left-0 bg-white/80 dark:bg-zinc-900/80 backdrop-blur-2xl border-r border-zinc-200/80 dark:border-zinc-800/80 flex-col z-50 transition-all duration-300 shadow-xl lg:shadow-none overflow-hidden"
        :class="{'w-[220px]': !sidebarCollapsed, 'w-[72px]': sidebarCollapsed}">

        <!-- Sidebar Header / Brand Logo -->
        <div class="h-16 flex items-center px-4 border-b border-zinc-200/80 dark:border-zinc-800/80 shrink-0 transition-all duration-300"
            :class="{'justify-center': sidebarCollapsed}">
            <a href="{{ route('member.dashboard') }}" class="flex items-center gap-3 overflow-hidden whitespace-nowrap">
                <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-[#155A6B] to-emerald-500 flex items-center justify-center text-white shrink-0 shadow-md shadow-[#155A6B]/20">
                    <img src="{{ asset('images/logo-koperasi.png') }}" alt="Logo Koperasi" class="w-5 h-5 object-contain">
                </div>
                <div class="flex flex-col transition-opacity duration-300" :class="{'hidden': sidebarCollapsed}">
                    <span class="font-heading font-extrabold text-sm tracking-tight text-zinc-900 dark:text-white leading-none">
                        {{ coop_config('short_name', 'Bermadani') }}
                    </span>
                    <span class="text-[10px] font-bold text-[#155A6B] dark:text-emerald-400 tracking-wider uppercase mt-0.5">
                        Portal Anggota
                    </span>
                </div>
            </a>
        </div>

        <!-- Sidebar Navigation Items -->
        <nav class="flex-1 px-3 py-4 space-y-1.5 overflow-y-auto hide-scrollbar">
            <p class="px-2 text-[10px] font-extrabold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider mb-2 mt-1 opacity-90 whitespace-nowrap transition-opacity duration-300"
                :class="{'hidden': sidebarCollapsed}">Ringkasan</p>

            <!-- Dashboard -->
            <a href="{{ route('member.dashboard') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 group whitespace-nowrap {{ request()->routeIs('member.dashboard') ? 'bg-[#155A6B] text-white shadow-md shadow-[#155A6B]/25 font-semibold' : 'text-zinc-500 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800/60 hover:text-zinc-900 dark:hover:text-white' }}"
                :class="{'justify-center px-2': sidebarCollapsed}">
                <i class='bx {{ request()->routeIs('member.dashboard') ? 'bxs-dashboard' : 'bx-dashboard' }} text-xl shrink-0'></i>
                <span class="text-xs transition-opacity duration-300" :class="{'hidden': sidebarCollapsed}">Beranda</span>
            </a>

            <!-- Simpanan -->
            <p class="px-2 text-[10px] font-extrabold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider mb-2 mt-5 opacity-90 whitespace-nowrap transition-opacity duration-300"
                :class="{'hidden': sidebarCollapsed}">Keuangan Syariah</p>

            <a href="{{ route('member.simpanan') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 group whitespace-nowrap {{ request()->routeIs('member.simpanan*') ? 'bg-[#155A6B] text-white shadow-md shadow-[#155A6B]/25 font-semibold' : 'text-zinc-500 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800/60 hover:text-zinc-900 dark:hover:text-white' }}"
                :class="{'justify-center px-2': sidebarCollapsed}">
                <i class='bx {{ request()->routeIs('member.simpanan*') ? 'bxs-wallet' : 'bx-wallet' }} text-xl shrink-0'></i>
                <span class="text-xs transition-opacity duration-300" :class="{'hidden': sidebarCollapsed}">Simpanan Saya</span>
            </a>

            <!-- Pinjaman / Pembiayaan -->
            <a href="{{ route('member.loans') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 group whitespace-nowrap {{ request()->routeIs('member.loans*') ? 'bg-[#155A6B] text-white shadow-md shadow-[#155A6B]/25 font-semibold' : 'text-zinc-500 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800/60 hover:text-zinc-900 dark:hover:text-white' }}"
                :class="{'justify-center px-2': sidebarCollapsed}">
                <i class='bx {{ request()->routeIs('member.loans*') ? 'bxs-bank' : 'bx-bank' }} text-xl shrink-0'></i>
                <span class="text-xs transition-opacity duration-300" :class="{'hidden': sidebarCollapsed}">Pembiayaan</span>
            </a>

            <!-- Transaksi -->
            <a href="{{ route('member.transactions') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 group whitespace-nowrap {{ request()->routeIs('member.transactions*') ? 'bg-[#155A6B] text-white shadow-md shadow-[#155A6B]/25 font-semibold' : 'text-zinc-500 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800/60 hover:text-zinc-900 dark:hover:text-white' }}"
                :class="{'justify-center px-2': sidebarCollapsed}">
                <i class='bx {{ request()->routeIs('member.transactions*') ? 'bxs-receipt' : 'bx-receipt' }} text-xl shrink-0'></i>
                <span class="text-xs transition-opacity duration-300" :class="{'hidden': sidebarCollapsed}">Riwayat Transaksi</span>
            </a>

            <!-- Akun & Pengaturan -->
            <p class="px-2 text-[10px] font-extrabold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider mb-2 mt-5 opacity-90 whitespace-nowrap transition-opacity duration-300"
                :class="{'hidden': sidebarCollapsed}">Pengaturan</p>

            <a href="{{ route('member.profile') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 group whitespace-nowrap {{ request()->routeIs('member.profile*') ? 'bg-[#155A6B] text-white shadow-md shadow-[#155A6B]/25 font-semibold' : 'text-zinc-500 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800/60 hover:text-zinc-900 dark:hover:text-white' }}"
                :class="{'justify-center px-2': sidebarCollapsed}">
                <i class='bx {{ request()->routeIs('member.profile*') ? 'bxs-user-badge' : 'bx-user-badge' }} text-xl shrink-0'></i>
                <span class="text-xs transition-opacity duration-300" :class="{'hidden': sidebarCollapsed}">Profil & Identitas</span>
            </a>
        </nav>

        <!-- Sidebar Collapse Button (Desktop) -->
        <div class="hidden lg:flex items-center gap-3 px-4 py-3 border-t border-zinc-200/80 dark:border-zinc-800/80 cursor-pointer hover:bg-zinc-100 dark:hover:bg-zinc-800/50 transition-colors text-zinc-500 hover:text-[#155A6B] dark:hover:text-emerald-400 whitespace-nowrap overflow-hidden"
            @click="sidebarCollapsed = !sidebarCollapsed; localStorage.setItem('member-sidebar-collapsed', sidebarCollapsed)"
            :class="{'justify-center': sidebarCollapsed}">
            <i class='bx bx-chevrons-left text-xl transition-transform duration-300 shrink-0'
                :class="{'rotate-180': sidebarCollapsed}"></i>
            <span class="text-xs font-semibold transition-opacity duration-300"
                :class="{'hidden': sidebarCollapsed}">Ciutkan Sidebar</span>
        </div>
    </aside>

    <!-- 2. Apple Top Navigation Header -->
    <header class="fixed top-0 inset-x-0 h-16 bg-white/80 dark:bg-zinc-900/80 backdrop-blur-2xl border-b border-zinc-200/80 dark:border-zinc-800/80 z-40 transition-all duration-300 flex items-center justify-between px-4 sm:px-6"
        :class="{'lg:pl-[236px]': !sidebarCollapsed, 'lg:pl-[88px]': sidebarCollapsed}">
        
        <!-- Left: Brand / Title -->
        <div class="flex items-center gap-3">
            <div class="flex items-center gap-2">
                <div class="lg:hidden w-7 h-7 rounded-lg bg-gradient-to-tr from-[#155A6B] to-emerald-500 flex items-center justify-center text-white shrink-0 shadow-sm">
                    <img src="{{ asset('images/logo-koperasi.png') }}" alt="Logo Koperasi" class="w-4 h-4 object-contain">
                </div>
                <h1 class="font-heading font-extrabold text-base sm:text-lg text-zinc-900 dark:text-white tracking-tight">
                    @yield('title', 'Portal Anggota')
                </h1>
            </div>
        </div>

        <!-- Right: Theme Switcher, Notifications, User Menu -->
        <div class="flex items-center gap-2 sm:gap-3">
            <!-- Theme Switcher Button -->
            <button @click="toggleTheme()"
                class="w-9 h-9 rounded-full bg-zinc-100 dark:bg-zinc-800/80 hover:bg-zinc-200 dark:hover:bg-zinc-700 flex items-center justify-center transition-all text-zinc-600 dark:text-amber-400">
                <i class='bx text-lg' :class="darkMode ? 'bx-sun' : 'bx-moon'"></i>
            </button>

            <!-- User Menu Pill -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="flex items-center gap-2.5 p-1 sm:pr-3 rounded-full hover:bg-zinc-100 dark:hover:bg-zinc-800/60 transition-all border border-zinc-200/60 dark:border-zinc-800">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-[#155A6B] to-emerald-500 text-white font-bold flex items-center justify-center text-xs shadow-sm">
                        {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                    </div>
                    <span class="hidden sm:inline text-xs font-bold text-zinc-800 dark:text-zinc-200 max-w-[120px] truncate">
                        {{ auth()->user()->name ?? 'Anggota' }}
                    </span>
                    <i class='bx bx-chevron-down text-zinc-400 text-sm hidden sm:inline'></i>
                </button>

                <div x-show="open" @click.away="open = false" x-cloak
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="absolute right-0 mt-2 w-48 bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl border border-zinc-200/80 dark:border-zinc-800 py-1.5 z-50 text-xs">
                    
                    <div class="px-4 py-2.5 border-b border-zinc-100 dark:border-zinc-800">
                        <p class="font-bold text-zinc-900 dark:text-white truncate">{{ auth()->user()->name ?? 'Anggota Civitas' }}</p>
                        <p class="text-[10px] text-zinc-400 truncate">{{ auth()->user()->email }}</p>
                    </div>

                    <a href="{{ route('member.profile') }}" class="flex items-center gap-2 px-4 py-2 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors">
                        <i class='bx bx-user-circle text-base text-[#155A6B] dark:text-emerald-400'></i>
                        <span>Profil Saya</span>
                    </a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-2 px-4 py-2 text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/30 transition-colors text-left font-semibold">
                            <i class='bx bx-log-out text-base'></i>
                            <span>Keluar Sesi</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <!-- 3. Main Content Container -->
    <main class="pt-20 pb-28 px-4 sm:px-6 lg:px-8 transition-all duration-300 min-h-screen"
        :class="{'lg:pl-[236px]': !sidebarCollapsed, 'lg:pl-[88px]': sidebarCollapsed}">
        
        <div class="max-w-7xl mx-auto">
            @php
                $currentMember = auth()->check() ? \App\Models\Member::where('userId', auth()->id())->first() : null;
                $isInactiveMember = $currentMember && in_array(strtoupper($currentMember->status ?? ''), ['INACTIVE', 'RESIGNED', 'SUSPENDED', 'NONAKTIF', 'KELUAR']);
            @endphp

            @if($isInactiveMember)
                <div class="mb-6 p-4 rounded-2xl bg-amber-500/10 dark:bg-amber-500/20 border border-amber-500/30 flex items-start gap-3 backdrop-blur-sm shadow-sm">
                    <div class="p-2 bg-amber-500 text-white rounded-xl shrink-0 mt-0.5 shadow-md shadow-amber-500/20">
                        <i class='bx bx-info-circle text-xl'></i>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center justify-between">
                            <h4 class="text-xs font-extrabold text-amber-800 dark:text-amber-300 uppercase tracking-wider">Mode Keanggotaan Non-Aktif / Anggota Keluar</h4>
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-amber-200 dark:bg-amber-900/60 text-amber-900 dark:text-amber-200">READ-ONLY</span>
                        </div>
                        <p class="text-xs text-zinc-600 dark:text-zinc-300 mt-1 leading-relaxed">
                            Akun Anda dalam status <strong>{{ $currentMember->status }}</strong>. Anda dapat melihat riwayat simpanan, mutasi transaksi terdahulu, dan mengunduh bukti pengembalian simpanan. Pengajuan pinjaman/transfer baru dinonaktifkan.
                        </p>
                    </div>
                </div>
            @endif

            @yield('content')
            {{ $slot ?? '' }}
        </div>
    </main>

    <!-- 4. Floating Mobile Bottom Navigation Bar (Visible < lg) -->
    <nav class="fixed bottom-0 inset-x-0 z-40 bg-white/90 dark:bg-zinc-900/90 backdrop-blur-2xl border-t border-zinc-200/80 dark:border-zinc-800/80 lg:hidden pb-safe">
        <div class="flex justify-around items-center max-w-md mx-auto px-3 py-2">

            <!-- Beranda -->
            <a href="{{ route('member.dashboard') }}"
                class="flex flex-col items-center gap-1 px-3 py-1.5 rounded-xl transition-all {{ request()->routeIs('member.dashboard') ? 'text-[#155A6B] dark:text-emerald-400 font-bold' : 'text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300' }}">
                <i class='bx {{ request()->routeIs('member.dashboard') ? 'bxs-dashboard' : 'bx-dashboard' }} text-xl'></i>
                <span class="text-[10px] font-semibold">Beranda</span>
            </a>

            <!-- Simpanan -->
            <a href="{{ route('member.simpanan') }}"
                class="flex flex-col items-center gap-1 px-3 py-1.5 rounded-xl transition-all {{ request()->routeIs('member.simpanan*') ? 'text-[#155A6B] dark:text-emerald-400 font-bold' : 'text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300' }}">
                <i class='bx {{ request()->routeIs('member.simpanan*') ? 'bxs-wallet' : 'bx-wallet' }} text-xl'></i>
                <span class="text-[10px] font-semibold">Simpanan</span>
            </a>

            <!-- Pembiayaan Floating Button -->
            <div class="relative -top-4">
                <a href="{{ route('member.loans') }}"
                    class="w-13 h-13 rounded-full bg-gradient-to-tr from-[#155A6B] to-emerald-500 shadow-lg shadow-[#155A6B]/30 flex items-center justify-center text-white transform transition-transform active:scale-95 border-3 border-white dark:border-zinc-900">
                    <i class='bx bx-bank text-xl'></i>
                </a>
            </div>

            <!-- Transaksi -->
            <a href="{{ route('member.transactions') }}"
                class="flex flex-col items-center gap-1 px-3 py-1.5 rounded-xl transition-all {{ request()->routeIs('member.transactions*') ? 'text-[#155A6B] dark:text-emerald-400 font-bold' : 'text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300' }}">
                <i class='bx {{ request()->routeIs('member.transactions*') ? 'bxs-receipt' : 'bx-receipt' }} text-xl'></i>
                <span class="text-[10px] font-semibold">Riwayat</span>
            </a>

            <!-- Akun -->
            <a href="{{ route('member.profile') }}"
                class="flex flex-col items-center gap-1 px-3 py-1.5 rounded-xl transition-all {{ request()->routeIs('member.profile*') ? 'text-[#155A6B] dark:text-emerald-400 font-bold' : 'text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300' }}">
                <i class='bx {{ request()->routeIs('member.profile*') ? 'bxs-user-badge' : 'bx-user-badge' }} text-xl'></i>
                <span class="text-[10px] font-semibold">Akun</span>
            </a>

        </div>
    </nav>

    @livewireScripts
</body>

</html>