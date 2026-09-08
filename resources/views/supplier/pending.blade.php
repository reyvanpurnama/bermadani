<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menunggu Persetujuan - {{ config('cooperative.short_name', 'Bermadani') }}</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=SF+Pro+Display:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: { sans: ['-apple-system', 'BlinkMacSystemFont', '"SF Pro Display"', '"Plus Jakarta Sans"', 'Inter', 'sans-serif'] },
                    colors: {
                        primary: '#155A6B',
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
        .animate-pulse-slow { animation: pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite; }
    </style>
</head>
<body class="bg-[#f5f5f7] dark:bg-[#09090b] text-zinc-900 dark:text-zinc-100 font-sans min-h-screen flex items-center justify-center p-4 sm:p-6 selection:bg-[#155A6B] selection:text-white">

    <button id="theme-toggle" class="absolute top-6 right-6 p-2 text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors">
        <i id="theme-icon" class='bx bx-moon text-2xl'></i>
    </button>

    <div class="bg-white/80 dark:bg-zinc-900/80 backdrop-blur-2xl w-full max-w-lg rounded-3xl shadow-2xl border border-zinc-200/80 dark:border-zinc-800/80 overflow-hidden relative">
        
        <div class="absolute top-0 left-0 w-full h-36 bg-gradient-to-br from-[#155A6B] via-[#166072] to-[#1a6b80]"></div>
        <div class="absolute top-0 left-0 w-full h-36 opacity-15 bg-[url('/images/hero-landscape.jpeg')] bg-cover bg-center pointer-events-none"></div>

        <div class="relative z-10 px-6 sm:px-10 pb-10 pt-16 text-center">
            
            <div class="w-24 h-24 bg-white dark:bg-zinc-800 rounded-3xl shadow-2xl flex items-center justify-center mx-auto mb-6 relative border border-white/40 dark:border-zinc-700">
                <div class="absolute inset-0 bg-[#155A6B]/10 rounded-3xl animate-pulse-slow"></div>
                <i class='bx bx-time-five text-5xl text-[#155A6B] dark:text-teal-400 relative z-10'></i>
                <div class="absolute -right-2 -top-2 bg-amber-400 text-zinc-900 text-[10px] font-extrabold px-2.5 py-1 rounded-full shadow-md uppercase tracking-wider">
                    Verifikasi
                </div>
            </div>

            <h1 class="text-2xl sm:text-3xl font-black text-zinc-900 dark:text-white mb-2 tracking-tight">Pendaftaran Berhasil!</h1>
            <p class="text-zinc-500 dark:text-zinc-400 text-xs sm:text-sm leading-relaxed mb-8 font-medium">
                Terima kasih telah mendaftar sebagai Mitra Supplier UMBandung. <br class="hidden sm:block">
                Saat ini berkas pendaftaran Anda sedang ditinjau oleh Pengurus Koperasi.
            </p>

            <div class="bg-zinc-50/80 dark:bg-zinc-800/50 rounded-2xl p-6 border border-zinc-200/80 dark:border-zinc-700/50 mb-8">
                <h5 class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-widest mb-4 text-left">Status Verifikasi Akun</h5>
                
                <div class="relative flex items-start justify-between">
                    <div class="absolute top-3.5 left-0 w-full h-0.5 bg-zinc-200 dark:bg-zinc-700 -z-10"></div>
                    
                    <div class="flex flex-col items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-teal-500 text-white flex items-center justify-center text-sm shadow-md ring-4 ring-white dark:ring-zinc-900">
                            <i class='bx bx-check text-lg font-bold'></i>
                        </div>
                        <span class="text-[10px] font-bold text-teal-600 dark:text-teal-400">Terkirim</span>
                    </div>

                    <div class="flex flex-col items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-amber-400 text-zinc-900 flex items-center justify-center text-sm shadow-md ring-4 ring-white dark:ring-zinc-900 animate-bounce">
                            <i class='bx bx-loader-alt bx-spin text-lg font-bold'></i>
                        </div>
                        <span class="text-[10px] font-extrabold text-zinc-900 dark:text-white bg-white dark:bg-zinc-800 px-2 py-0.5 rounded-full shadow-sm">Review</span>
                    </div>

                    <div class="flex flex-col items-center gap-2 opacity-40">
                        <div class="w-8 h-8 rounded-full bg-zinc-300 dark:bg-zinc-700 text-zinc-600 dark:text-zinc-400 flex items-center justify-center text-sm ring-4 ring-white dark:ring-zinc-900">
                            <i class='bx bxs-lock-alt text-base'></i>
                        </div>
                        <span class="text-[10px] font-semibold text-zinc-500">Aktif</span>
                    </div>
                </div>
            </div>

            <div class="bg-teal-50/60 dark:bg-teal-950/30 border border-teal-200/80 dark:border-teal-800/40 rounded-2xl p-4 mb-6">
                <div class="flex items-start gap-3">
                    <i class='bx bx-info-circle text-[#155A6B] dark:text-teal-400 text-xl flex-shrink-0 mt-0.5'></i>
                    <div class="text-left">
                        <h5 class="text-xs font-bold text-[#155A6B] dark:text-teal-300 mb-1">Informasi Verifikasi</h5>
                        <p class="text-xs text-zinc-600 dark:text-zinc-300 leading-relaxed font-medium">
                            Admin Koperasi sedang mengecek bukti pembayaran dan data usaha Anda. Proses peninjauan membutuhkan waktu maksimal 1x24 jam kerja.
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex flex-col gap-3">
                <a href="{{ route('home') }}" class="w-full py-3.5 px-6 rounded-full bg-[#155A6B] hover:bg-[#1a6b80] text-white text-xs font-extrabold shadow-lg shadow-[#155A6B]/25 transition-all flex items-center justify-center gap-2">
                    <i class='bx bx-home-alt text-base'></i> Kembali ke Beranda
                </a>
                <button onclick="document.getElementById('logout-form').submit()" class="w-full py-3 px-6 rounded-full text-xs font-bold text-zinc-500 hover:text-rose-600 transition-colors flex items-center justify-center gap-1.5">
                    <i class='bx bx-log-out text-sm'></i> Keluar Akun
                </button>
            </div>
        </div>
    </div>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
        @csrf
    </form>

    <script>
        const themeToggleBtn = document.getElementById('theme-toggle');
        const themeIcon = document.getElementById('theme-icon');
        const html = document.documentElement;

        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            html.classList.add('dark');
            themeIcon.classList.replace('bx-moon', 'bx-sun');
        }

        themeToggleBtn.addEventListener('click', function () {
            html.classList.toggle('dark');
            const isDark = html.classList.contains('dark');
            themeIcon.classList.replace(isDark ? 'bx-moon' : 'bx-sun', isDark ? 'bx-sun' : 'bx-moon');
            localStorage.setItem('color-theme', isDark ? 'dark' : 'light');
        });
    </script>
</body>
</html>
                <form method="POST" action="{{ route('logout') }}" style="display: none;" id="logoutForm">
                    @csrf
                </form>
                
                <button onclick="document.getElementById('logoutForm').submit()" class="w-full py-3 bg-primary hover:bg-blue-700 text-white font-semibold rounded-xl transition-colors shadow-sm flex items-center justify-center gap-2">
                    <i class='bx bx-log-out text-lg'></i> Keluar
                </button>
                
                <p class="text-xs text-slate-400 mt-2 text-center">
                    Butuh bantuan prioritas? 
                    <a href="https://wa.me/6287123456789" class="text-primary font-bold hover:underline" target="_blank">Hubungi Admin via WhatsApp</a>
                </p>
            </div>

        </div>
    </div>

    <script>
        const themeToggleBtn = document.getElementById('theme-toggle');
        const themeIcon = document.getElementById('theme-icon');
        const html = document.documentElement;

        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            html.classList.add('dark'); themeIcon.classList.replace('bx-moon', 'bx-sun');
        }

        themeToggleBtn.addEventListener('click', function() {
            html.classList.toggle('dark');
            if (html.classList.contains('dark')) {
                themeIcon.classList.replace('bx-moon', 'bx-sun'); localStorage.setItem('color-theme', 'dark');
            } else {
                themeIcon.classList.replace('bx-sun', 'bx-moon'); localStorage.setItem('color-theme', 'light');
            }
        });
    </script>
</body>
</html>
