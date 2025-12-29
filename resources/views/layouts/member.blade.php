<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Member Area') - Bermadani</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdn.tailwindcss.com"></script>
    @livewireStyles

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] },
                    colors: {
                        primary: '#0F52BA',
                        secondary: '#F8FAFC',
                        darkBg: '#0f172a',
                        darkCard: '#1e293b'
                    }
                }
            }
        }
    </script>

    <style>
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        .dark ::-webkit-scrollbar-thumb { background: #475569; }
        [x-cloak] { display: none !important; }
    </style>
    
    @stack('styles')
</head>
<body class="bg-secondary text-slate-800 antialiased dark:bg-darkBg dark:text-slate-200 transition-colors duration-300">

    {{-- Sidebar --}}
    @include('partials.member-sidebar')

    {{-- Main Content --}}
    <div class="lg:ml-[240px] min-h-screen flex flex-col transition-all duration-300">
        
        {{-- Header --}}
        <header class="h-16 bg-white dark:bg-darkCard border-b border-slate-200 dark:border-slate-700 flex items-center justify-between px-6 sticky top-0 z-20">
            <div class="flex items-center gap-4">
                <button id="mobile-menu-btn" class="lg:hidden p-2 text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg">
                    <i class='bx bx-menu text-2xl'></i>
                </button>
                <h1 class="text-lg font-bold text-slate-800 dark:text-white">@yield('page-title', 'Dashboard')</h1>
            </div>
            <div class="flex items-center gap-3">
                <button id="theme-toggle" class="p-2 text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-full transition-colors">
                    <i id="theme-icon" class='bx bx-moon text-xl'></i>
                </button>
            </div>
        </header>

        {{-- Page Content --}}
        <main class="flex-1 p-6 lg:p-8 space-y-6 overflow-y-auto">
            @yield('content')
            {{ $slot ?? '' }}
        </main>
    </div>

    @livewireScripts

    <script>
        // Theme Toggle
        const themeToggle = document.getElementById('theme-toggle');
        const themeIcon = document.getElementById('theme-icon');
        const html = document.documentElement;

        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            html.classList.add('dark');
            if(themeIcon) themeIcon.classList.replace('bx-moon', 'bx-sun');
        }

        themeToggle?.addEventListener('click', function() {
            html.classList.toggle('dark');
            const isDark = html.classList.contains('dark');
            themeIcon.classList.replace(isDark ? 'bx-moon' : 'bx-sun', isDark ? 'bx-sun' : 'bx-moon');
            localStorage.setItem('color-theme', isDark ? 'dark' : 'light');
        });

        // Mobile Menu
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const sidebar = document.getElementById('member-sidebar');
        const sidebarOverlay = document.getElementById('sidebar-overlay');

        mobileMenuBtn?.addEventListener('click', () => {
            sidebar?.classList.toggle('-translate-x-full');
            sidebarOverlay?.classList.toggle('hidden');
        });

        sidebarOverlay?.addEventListener('click', () => {
            sidebar?.classList.add('-translate-x-full');
            sidebarOverlay?.classList.add('hidden');
        });
    </script>
    
    @stack('scripts')
</body>
</html>
