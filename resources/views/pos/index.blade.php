<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS System - Koperasi UMB</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdn.tailwindcss.com"></script>
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
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        .dark ::-webkit-scrollbar-thumb { background: #475569; }
        
        .custom-scroll::-webkit-scrollbar { width: 4px; }
        .custom-scroll::-webkit-scrollbar-track { background: transparent; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        .dark .custom-scroll::-webkit-scrollbar-thumb { background: #475569; }
        
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="bg-page text-slate-800 antialiased dark:bg-darkPage dark:text-slate-200 overflow-hidden h-screen flex transition-colors duration-300">

    @livewire('pos-custom')

    @livewireScripts
    <script>
        // Theme toggle
        const themeToggle = document.getElementById('theme-toggle');
        const themeIcon = document.getElementById('theme-icon');
        const html = document.documentElement;

        if (localStorage.getItem('theme') === 'dark') {
            html.classList.add('dark');
            themeIcon.classList.replace('bx-moon', 'bx-sun');
        }

        themeToggle?.addEventListener('click', () => {
            html.classList.toggle('dark');
            if (html.classList.contains('dark')) {
                localStorage.setItem('theme', 'dark');
                themeIcon.classList.replace('bx-moon', 'bx-sun');
            } else {
                localStorage.setItem('theme', 'light');
                themeIcon.classList.replace('bx-sun', 'bx-moon');
            }
        });

        // Mobile cart toggle
        const toggleCartBtn = document.getElementById('toggle-cart-btn');
        const closeCartBtn = document.getElementById('close-cart-btn');
        const posCart = document.getElementById('pos-cart');

        toggleCartBtn?.addEventListener('click', () => {
            posCart.classList.remove('translate-y-full');
        });

        closeCartBtn?.addEventListener('click', () => {
            posCart.classList.add('translate-y-full');
        });
    </script>
</body>
</html>
