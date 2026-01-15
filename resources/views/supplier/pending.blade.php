<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menunggu Persetujuan - Bermadani</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdn.tailwindcss.com"></script>

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
        .animate-pulse-slow { animation: pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite; }
    </style>
</head>
<body class="bg-slate-50 dark:bg-darkBg font-sans min-h-screen flex items-center justify-center p-6">

    <button id="theme-toggle" class="absolute top-6 right-6 p-2 text-slate-400 hover:text-slate-600 dark:hover:text-white transition-colors">
        <i id="theme-icon" class='bx bx-moon text-2xl'></i>
    </button>

    <div class="bg-white dark:bg-darkCard w-full max-w-lg rounded-3xl shadow-2xl border border-slate-100 dark:border-slate-800 overflow-hidden relative">
        
        <div class="absolute top-0 left-0 w-full h-32 bg-gradient-to-r from-primary to-blue-600"></div>
        <div class="absolute top-0 left-0 w-full h-32 opacity-20" style="background-image: radial-gradient(#fff 1px, transparent 1px); background-size: 20px 20px;"></div>

        <div class="relative z-10 px-8 pb-10 pt-16 text-center">
            
            <div class="w-24 h-24 bg-white dark:bg-slate-800 rounded-2xl shadow-xl flex items-center justify-center mx-auto mb-6 relative">
                <div class="absolute inset-0 bg-blue-50 dark:bg-blue-900/20 rounded-2xl animate-pulse-slow"></div>
                <i class='bx bx-time-five text-5xl text-primary relative z-10'></i>
                <div class="absolute -right-2 -top-2 bg-amber-400 text-white text-xs font-bold px-2 py-1 rounded-full shadow-md">
                    Review
                </div>
            </div>

            <h1 class="text-2xl font-bold text-slate-900 dark:text-white mb-3">Pendaftaran Berhasil!</h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm leading-relaxed mb-8">
                Terima kasih telah mendaftar sebagai Mitra Supplier. <br class="hidden sm:block">
                Saat ini data Anda sedang dalam proses verifikasi oleh Admin Koperasi.
            </p>

            <div class="bg-slate-50 dark:bg-slate-800/50 rounded-xl p-6 border border-slate-100 dark:border-slate-700/50 mb-8">
                <h5 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4 text-left">Status Aplikasi</h5>
                
                <div class="relative flex items-start justify-between">
                    <div class="absolute top-3 left-0 w-full h-0.5 bg-slate-200 dark:bg-slate-700 -z-10"></div>
                    
                    <div class="flex flex-col items-center gap-2">
                        <div class="w-7 h-7 rounded-full bg-emerald-500 text-white flex items-center justify-center text-sm shadow-md ring-4 ring-white dark:ring-darkCard">
                            <i class='bx bx-check'></i>
                        </div>
                        <span class="text-[10px] font-semibold text-emerald-600 dark:text-emerald-400">Terkirim</span>
                    </div>

                    <div class="flex flex-col items-center gap-2">
                        <div class="w-7 h-7 rounded-full bg-amber-500 text-white flex items-center justify-center text-sm shadow-md ring-4 ring-white dark:ring-darkCard animate-bounce">
                            <i class='bx bx-loader-alt bx-spin'></i>
                        </div>
                        <span class="text-[10px] font-bold text-slate-800 dark:text-white bg-white dark:bg-darkCard px-2 rounded-full">Verifikasi</span>
                    </div>

                    <div class="flex flex-col items-center gap-2 opacity-40">
                        <div class="w-7 h-7 rounded-full bg-slate-300 dark:bg-slate-600 text-white flex items-center justify-center text-sm ring-4 ring-white dark:ring-darkCard">
                            <i class='bx bxs-lock-alt'></i>
                        </div>
                        <span class="text-[10px] font-semibold text-slate-500">Aktif</span>
                    </div>
                </div>
            </div>

            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800/50 rounded-xl p-4 mb-6">
                <div class="flex items-start gap-3">
                    <i class='bx bx-info-circle text-blue-600 dark:text-blue-400 text-xl flex-shrink-0 mt-0.5'></i>
                    <div class="text-left">
                        <h5 class="text-xs font-bold text-blue-700 dark:text-blue-300 mb-1">Hampir Selesai!</h5>
                        <p class="text-xs text-blue-600 dark:text-blue-400 leading-relaxed">
                            Admin kami sedang meninjau pendaftaran dan pembayaran Anda. Mohon tunggu maksimal 1-2 hari kerja hingga akun Anda siap digunakan.
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex flex-col gap-3">
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
