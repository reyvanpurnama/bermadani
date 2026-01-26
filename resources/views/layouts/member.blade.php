<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Member Area') - Bermadani</title>

    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

    <!-- Scripts -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @livewireStyles

    <!-- Config -->
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: { sans: ['Outfit', 'sans-serif'] },
                    colors: {
                        primary: '#6366f1', // Indigo
                        darkBg: '#0f172a',  // Slate 900
                        darkCard: '#1e293b', // Slate 800
                    }
                }
            }
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
    class="bg-slate-50 dark:bg-darkBg text-slate-800 dark:text-slate-100 min-h-screen font-sans selection:bg-primary/30">

    <!-- Top Bar (Optional, mostly for Branding) -->
    <div
        class="fixed top-0 left-0 w-full z-40 bg-white/80 dark:bg-darkCard/80 backdrop-blur-md px-4 py-3 border-b border-slate-200 dark:border-white/5 flex justify-between items-center lg:hidden">
        <div class="flex items-center gap-2">
            <div
                class="w-8 h-8 rounded-lg bg-gradient-to-tr from-primary to-purple-500 flex items-center justify-center text-white font-bold">
                B</div>
            <span class="font-bold text-lg tracking-tight">Bermadani</span>
        </div>
        <div class="flex gap-3">
            <!-- Notification Icon could go here -->
            <button class="w-8 h-8 rounded-full bg-slate-100 dark:bg-white/5 flex items-center justify-center">
                <i class='bx bx-bell text-xl'></i>
            </button>
        </div>
    </div>

    <!-- Main Content -->
    <div class="pt-16 pb-24 lg:pt-8 lg:px-8 max-w-md mx-auto lg:max-w-4xl min-h-screen relative">
        @yield('content')
    </div>

    <!-- Bottom Navigation (Mobile Only/Primary Nav) -->
    <div
        class="fixed bottom-0 left-0 w-full z-50 bg-white/90 dark:bg-darkCard/90 backdrop-blur-lg border-t border-slate-200 dark:border-white/5 pb-safe">
        <div class="flex justify-around items-center max-w-md mx-auto lg:max-w-4xl px-2 py-2">

            <!-- Home -->
            <a href="{{ route('member.dashboard') }}"
                class="flex flex-col items-center gap-1 p-2 rounded-xl transition-all {{ request()->routeIs('member.dashboard') ? 'text-primary' : 'text-slate-400 hover:text-slate-600 dark:hover:text-slate-300' }}">
                <i
                    class='bx {{ request()->routeIs('member.dashboard') ? 'bxs-home-smile' : 'bx-home-smile' }} text-2xl'></i>
                <span class="text-[10px] font-medium">Beranda</span>
            </a>

            <!-- History -->
            <a href="{{ route('member.transactions') }}"
                class="flex flex-col items-center gap-1 p-2 rounded-xl transition-all {{ request()->routeIs('member.transactions*') ? 'text-primary' : 'text-slate-400 hover:text-slate-600 dark:hover:text-slate-300' }}">
                <i
                    class='bx {{ request()->routeIs('member.transactions*') ? 'bxs-receipt' : 'bx-receipt' }} text-2xl'></i>
                <span class="text-[10px] font-medium">Riwayat</span>
            </a>

            <!-- Scan (Center Floating) -->
            <div class="relative -top-5">
                <button
                    class="w-14 h-14 rounded-full bg-gradient-to-tr from-primary to-purple-600 shadow-lg shadow-primary/40 flex items-center justify-center text-white transform transition-transform active:scale-95 border-4 border-white dark:border-darkBg">
                    <i class='bx bx-qr-scan text-2xl'></i>
                </button>
            </div>

            <!-- Promotion / Shop -->
            <a href="#"
                class="flex flex-col items-center gap-1 p-2 rounded-xl transition-all {{ request()->routeIs('member.shop') ? 'text-primary' : 'text-slate-400 hover:text-slate-600 dark:hover:text-slate-300' }}">
                <i class='bx {{ request()->routeIs('member.shop') ? 'bxs-store' : 'bx-store' }} text-2xl'></i>
                <span class="text-[10px] font-medium">Belanja</span>
            </a>

            <!-- Profile -->
            <a href="{{ route('member.profile') }}"
                class="flex flex-col items-center gap-1 p-2 rounded-xl transition-all {{ request()->routeIs('member.profile*') ? 'text-primary' : 'text-slate-400 hover:text-slate-600 dark:hover:text-slate-300' }}">
                <i
                    class='bx {{ request()->routeIs('member.profile*') ? 'bxs-user-circle' : 'bx-user-circle' }} text-2xl'></i>
                <span class="text-[10px] font-medium">Akun</span>
            </a>

        </div>
    </div>

    @livewireScripts
</body>

</html>