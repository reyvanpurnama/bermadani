<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Portal Anggota') - Koperasi UMB</title>

    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

    <!-- Scripts -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @livewireStyles

    <!-- Config - Koperasi uses Emerald as primary -->
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: { sans: ['Outfit', 'sans-serif'] },
                    colors: {
                        primary: '#10b981', // Emerald-500 (Koperasi Theme)
                        darkBg: '#0f172a',  // Slate 900
                        darkCard: '#1e293b', // Slate 800
                    }
                }
            }
        }
    </script>

    <!-- Dark Mode Init -->
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark')
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>

    <style>
        /* Mobile optimization */
        body {
            -webkit-tap-highlight-color: transparent;
        }

        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>

<body
    class="bg-slate-50 dark:bg-darkBg text-slate-800 dark:text-slate-100 min-h-screen font-sans selection:bg-emerald-500/30"
    x-data="{ 
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

    <!-- Top Bar - Unified Branding -->
    <div
        class="fixed top-0 left-0 w-full z-40 bg-white/80 dark:bg-darkCard/80 backdrop-blur-md px-4 py-3 border-b border-slate-200 dark:border-white/5 flex justify-between items-center lg:hidden">
        <div class="flex items-center gap-2">
            <div
                class="w-8 h-8 rounded-lg bg-gradient-to-tr from-primary to-purple-500 flex items-center justify-center text-white font-bold">
                B</div>
            <span class="font-bold text-lg tracking-tight">Bermadani</span>
        </div>
        <div class="flex gap-3">
            <button @click="toggleTheme()"
                class="w-8 h-8 rounded-full bg-slate-100 dark:bg-white/5 flex items-center justify-center transition-colors text-slate-600 dark:text-yellow-400">
                <i class='bx text-xl' :class="darkMode ? 'bx-sun' : 'bx-moon'"></i>
            </button>
            <button class="w-8 h-8 rounded-full bg-slate-100 dark:bg-white/5 flex items-center justify-center">
                <i class='bx bx-bell text-xl'></i>
            </button>
        </div>
    </div>

    <!-- Main Content -->
    <div class="pt-20 pb-28 px-4 lg:pt-8 lg:px-8 max-w-md mx-auto lg:max-w-4xl min-h-screen relative">
        @yield('content')
        {{ $slot ?? '' }}
    </div>

    <!-- Bottom Navigation - Koperasi Style -->
    <div
        class="fixed bottom-0 left-0 w-full z-50 bg-white/95 dark:bg-darkCard/95 backdrop-blur-lg border-t border-emerald-200 dark:border-slate-700 pb-safe">
        <div class="flex justify-around items-center max-w-md mx-auto lg:max-w-4xl px-2 py-2">

            <!-- Home -->
            <a href="{{ route('member.dashboard') }}"
                class="flex flex-col items-center gap-1 p-2 rounded-xl transition-all {{ request()->routeIs('member.dashboard') ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400 hover:text-slate-600 dark:hover:text-slate-300' }}">
                <i
                    class='bx {{ request()->routeIs('member.dashboard') ? 'bxs-home-smile' : 'bx-home-smile' }} text-2xl'></i>
                <span class="text-[10px] font-medium">Beranda</span>
            </a>

            <!-- Simpanan -->
            <a href="{{ route('member.simpanan') }}"
                class="flex flex-col items-center gap-1 p-2 rounded-xl transition-all {{ request()->routeIs('member.simpanan*') ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400 hover:text-slate-600 dark:hover:text-slate-300' }}">
                <i class='bx {{ request()->routeIs('member.simpanan*') ? 'bxs-wallet' : 'bx-wallet' }} text-2xl'></i>
                <span class="text-[10px] font-medium">Simpanan</span>
            </a>

            <!-- Transfer (Center Floating) -->
            <div class="relative -top-5">
                <a href="{{ route('member.transfer') }}"
                    class="w-14 h-14 rounded-full bg-gradient-to-tr from-emerald-500 to-teal-500 shadow-lg shadow-emerald-500/40 flex items-center justify-center text-white transform transition-transform active:scale-95 border-4 border-white dark:border-darkBg">
                    <i class='bx bx-transfer text-2xl'></i>
                </a>
            </div>

            <!-- Transactions -->
            <a href="{{ route('member.transactions') }}"
                class="flex flex-col items-center gap-1 p-2 rounded-xl transition-all {{ request()->routeIs('member.transactions*') ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400 hover:text-slate-600 dark:hover:text-slate-300' }}">
                <i
                    class='bx {{ request()->routeIs('member.transactions*') ? 'bxs-receipt' : 'bx-receipt' }} text-2xl'></i>
                <span class="text-[10px] font-medium">Riwayat</span>
            </a>

            <!-- Profile -->
            <a href="{{ route('member.profile') }}"
                class="flex flex-col items-center gap-1 p-2 rounded-xl transition-all {{ request()->routeIs('member.profile*') ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400 hover:text-slate-600 dark:hover:text-slate-300' }}">
                <i
                    class='bx {{ request()->routeIs('member.profile*') ? 'bxs-user-circle' : 'bx-user-circle' }} text-2xl'></i>
                <span class="text-[10px] font-medium">Akun</span>
            </a>

        </div>
    </div>

    @livewireScripts
</body>

</html>