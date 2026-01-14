<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') - Koperasi UMB</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    @livewireStyles

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        primary: '#4F46E5',
                        page: '#E2E8F0',
                        card: '#F8FAFC',
                        darkPage: '#18181B',
                        darkCard: '#27272A',
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

        .custom-scroll::-webkit-scrollbar {
            width: 4px;
        }

        .custom-scroll::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scroll::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        .dark .custom-scroll::-webkit-scrollbar-thumb {
            background: #475569;
        }

        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        [x-cloak] {
            display: none !important;
        }
    </style>

    @stack('styles')
</head>

<body
    class="bg-page text-slate-800 antialiased dark:bg-darkPage dark:text-slate-200 overflow-hidden h-screen flex transition-colors duration-300">

    {{-- Sidebar Overlay (Mobile) --}}
    <div id="sidebar-overlay"
        class="fixed inset-0 bg-black/50 z-40 hidden opacity-0 transition-opacity duration-300 backdrop-blur-sm"></div>

    {{-- Sidebar --}}
    @include('partials.sidebar')

    {{-- Main Content --}}
    <div id="main-content"
        class="md:ml-[180px] flex-1 flex flex-col h-full min-w-0 relative transition-all duration-300">
        {{-- Top Navbar --}}
        @hasSection('hide-navbar')
        @else
            <header
                class="h-16 bg-card dark:bg-darkCard border-b border-slate-200 dark:border-slate-700 flex items-center justify-between px-4 shrink-0">
                <div class="flex items-center gap-3">
                    <button id="sidebar-toggle" class="md:hidden text-slate-500 hover:text-primary transition-colors">
                        <i class='bx bx-menu text-2xl'></i>
                    </button>
                    <h1 class="text-lg font-bold text-slate-800 dark:text-white">@yield('page-title', 'Dashboard')</h1>
                </div>
                <div class="flex items-center gap-2">
                    <button
                        class="w-8 h-8 flex items-center justify-center text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-full transition-colors relative">
                        <i class='bx bx-bell text-lg'></i>
                        <span class="absolute top-1 right-1 w-2 h-2 bg-rose-500 rounded-full"></span>
                    </button>
                    <button id="theme-toggle"
                        class="w-8 h-8 flex items-center justify-center text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-full transition-colors">
                        <i id="theme-icon" class='bx bx-moon text-lg'></i>
                    </button>
                </div>
            </header>
        @endif

        {{-- Page Content --}}
        <main class="flex-1 overflow-y-auto @yield('main-class', 'p-6') custom-scroll">
            @yield('content')
            {{ $slot ?? '' }}
        </main>
    </div>

    {{-- Toast Notifications --}}
    <div id="toast-container" class="fixed top-4 right-4 z-[100] space-y-2"></div>

    @livewireScripts

    <script>
        // Theme Toggle
        const themeToggle = document.getElementById('theme-toggle');
        const themeIcon = document.getElementById('theme-icon');
        const html = document.documentElement;

        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            html.classList.add('dark');
            if (themeIcon) themeIcon.classList.replace('bx-moon', 'bx-sun');
        }

        themeToggle?.addEventListener('click', function () {
            html.classList.toggle('dark');
            const isDark = html.classList.contains('dark');
            themeIcon.classList.replace(isDark ? 'bx-moon' : 'bx-sun', isDark ? 'bx-sun' : 'bx-moon');
            localStorage.setItem('color-theme', isDark ? 'dark' : 'light');
        });

        // Sidebar Toggle
        const sidebar = document.getElementById('main-sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        const toggleBtn = document.getElementById('sidebar-toggle');
        const closeBtn = document.getElementById('sidebar-close');
        const collapseBtn = document.getElementById('sidebar-collapse-desktop');
        const mainContent = document.getElementById('main-content');
        const sidebarTexts = document.querySelectorAll('.sidebar-text');
        const collapseIcon = collapseBtn?.querySelector('i');

        function openSidebar() {
            sidebar?.classList.remove('-translate-x-full');
            overlay?.classList.remove('hidden');
            setTimeout(() => overlay?.classList.remove('opacity-0'), 10);
        }

        function closeSidebar() {
            sidebar?.classList.add('-translate-x-full');
            overlay?.classList.add('opacity-0');
            setTimeout(() => overlay?.classList.add('hidden'), 300);
        }

        function toggleCollapse() {
            const isCollapsed = sidebar?.classList.contains('w-20');
            const header = document.getElementById('sidebar-header');
            const navItems = document.querySelectorAll('.nav-item');
            const collapseContainer = document.getElementById('sidebar-collapse-desktop');
            const userProfile = document.getElementById('user-profile');
            const bottomBar = document.getElementById('pos-bottom-bar');

            if (isCollapsed) {
                sidebar?.classList.remove('w-20');
                sidebar?.classList.add('w-[180px]');
                mainContent?.classList.remove('md:ml-20');
                mainContent?.classList.add('md:ml-[180px]');
                if (bottomBar) {
                    bottomBar.classList.remove('md:ml-20');
                    bottomBar.classList.add('md:ml-[180px]');
                }

                sidebarTexts.forEach(el => {
                    el.classList.remove('hidden', 'opacity-0', 'w-0');
                });
                collapseIcon?.classList.remove('rotate-180');

                localStorage.setItem('sidebar-collapsed', 'false');
            } else {
                sidebar?.classList.remove('w-[180px]');
                sidebar?.classList.add('w-20');
                mainContent?.classList.remove('md:ml-[180px]');
                mainContent?.classList.add('md:ml-20');
                if (bottomBar) {
                    bottomBar.classList.remove('md:ml-[180px]');
                    bottomBar.classList.add('md:ml-20');
                }

                sidebarTexts.forEach(el => {
                    el.classList.add('opacity-0', 'w-0');
                    setTimeout(() => el.classList.add('hidden'), 300);
                });
                collapseIcon?.classList.add('rotate-180');

                localStorage.setItem('sidebar-collapsed', 'true');
            }
        }

        // Init Sidebar State
        const savedState = localStorage.getItem('sidebar-collapsed');
        if (savedState === 'true') {
            sidebar?.classList.remove('w-[180px]');
            sidebar?.classList.add('w-20');
            mainContent?.classList.remove('md:ml-[180px]');
            mainContent?.classList.add('md:ml-20');
            const bottomBar = document.getElementById('pos-bottom-bar');
            if (bottomBar) {
                bottomBar.classList.remove('md:ml-[180px]');
                bottomBar.classList.add('md:ml-20');
            }

            sidebarTexts.forEach(el => {
                el.classList.add('opacity-0', 'w-0', 'hidden');
            });
            collapseIcon?.classList.add('rotate-180');
        }

        toggleBtn?.addEventListener('click', openSidebar);
        closeBtn?.addEventListener('click', closeSidebar);
        overlay?.addEventListener('click', closeSidebar);
        collapseBtn?.addEventListener('click', toggleCollapse);

        // Toast Function
        window.showToast = function (message, type = 'success') {
            const container = document.getElementById('toast-container');
            const colors = {
                success: 'bg-emerald-600',
                error: 'bg-rose-600',
                warning: 'bg-amber-600',
                info: 'bg-blue-600'
            };
            const icons = {
                success: 'bx-check-circle',
                error: 'bx-x-circle',
                warning: 'bx-error',
                info: 'bx-info-circle'
            };

            const toast = document.createElement('div');
            toast.className = `${colors[type]} text-white px-4 py-3 rounded-xl shadow-lg flex items-center gap-2 transform translate-x-full transition-transform duration-300`;
            toast.innerHTML = `<i class='bx ${icons[type]} text-xl'></i><span class="text-sm font-medium">${message}</span>`;

            container.appendChild(toast);
            setTimeout(() => toast.classList.remove('translate-x-full'), 10);
            setTimeout(() => {
                toast.classList.add('translate-x-full');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        };

        // Listen for Livewire events
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('notify', (data) => {
                showToast(data[0].message, data[0].type);
            });
        });
    </script>

    @stack('scripts')
</body>

</html>