<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bermadani - Portal Koperasi & Retail</title>
    
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
        .clip-path-slant { clip-path: polygon(20% 0%, 100% 0, 100% 100%, 0% 100%); }
    </style>
</head>
<body class="bg-secondary text-slate-800 antialiased dark:bg-darkBg dark:text-slate-200 font-sans">

    <nav class="fixed w-full z-50 top-0 start-0 bg-white/90 dark:bg-darkBg/90 backdrop-blur-md border-b border-slate-200 dark:border-slate-800 transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-primary rounded-xl flex items-center justify-center text-white shadow-lg shadow-blue-500/30">
                        <i class='bx bxs-cube-alt text-2xl'></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-slate-900 dark:text-white leading-none">Bermadani</h1>
                        <p class="text-[10px] text-slate-500 uppercase tracking-widest font-semibold mt-0.5">Commerce System</p>
                    </div>
                </div>

                <div class="hidden md:flex items-center space-x-8">
                    <a href="#layanan" class="text-sm font-medium text-slate-600 hover:text-primary dark:text-slate-300 transition-colors">Layanan</a>
                    <a href="webcom.html" class="text-sm font-medium text-slate-600 hover:text-primary dark:text-slate-300 transition-colors">Tentang Kami</a>
                    
                    <button id="theme-toggle" class="p-2 text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-full transition-colors">
                        <i id="theme-icon" class='bx bx-moon text-xl'></i>
                    </button>
                </div>

                <button id="mobile-menu-btn" class="md:hidden p-2 text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors">
                    <i class='bx bx-menu text-2xl'></i>
                </button>
            </div>
        </div>

        <div id="mobile-menu" class="hidden md:hidden bg-white dark:bg-darkBg border-t border-slate-200 dark:border-slate-800 shadow-xl absolute w-full left-0 top-20">
            <div class="flex flex-col p-4 space-y-2">
                <a href="#layanan" class="block px-4 py-3 rounded-lg text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-primary transition-colors">
                    Layanan
                </a>
                <a href="webcom.html" class="block px-4 py-3 rounded-lg text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-primary transition-colors">
                    Tentang Kami
                </a>
                <button id="theme-toggle-mobile" class="w-full text-left px-4 py-3 rounded-lg text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-primary transition-colors flex items-center gap-2">
                    <i class='bx bx-moon text-lg'></i> Ganti Tema
                </button>
            </div>
        </div>
    </nav>

    <section class="relative pt-28 lg:pt-0 min-h-screen flex items-center bg-white dark:bg-darkBg overflow-hidden">
        <div class="absolute right-0 top-0 w-1/2 h-full bg-slate-50 dark:bg-slate-800/50 clip-path-slant hidden lg:block"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full relative z-10 grid lg:grid-cols-2 gap-12 items-center">
            <div class="py-10 lg:py-0 text-center lg:text-left">
                <h1 class="text-4xl lg:text-6xl font-extrabold text-slate-900 dark:text-white mb-6 leading-tight">
                    Satu Akses Untuk <br>
                    <span class="text-primary">Semua Anggota</span>
                </h1>
                <p class="text-lg text-slate-500 dark:text-slate-400 mb-8 max-w-lg mx-auto lg:mx-0 leading-relaxed">
                    Nikmati kemudahan berbelanja di minimarket dan layanan simpan pinjam koperasi dalam satu ekosistem digital yang terintegrasi.
                </p>

                <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start mt-8">
                    <a href="{{ route('login') }}" class="inline-flex justify-center items-center py-4 px-8 text-base font-bold text-center text-white rounded-full bg-primary hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 shadow-xl shadow-blue-500/30 transition-transform hover:-translate-y-1">
                        <i class='bx bxs-log-in-circle text-2xl mr-3'></i> Masuk Sistem
                    </a>

                    <a href="#layanan" class="inline-flex justify-center items-center py-4 px-8 text-base font-bold text-center text-slate-700 bg-white border border-slate-200 rounded-full hover:bg-slate-50 focus:ring-4 focus:ring-slate-100 dark:bg-slate-800 dark:text-white dark:border-slate-700 dark:hover:bg-slate-700 transition-colors">
                        <i class='bx bx-info-circle text-2xl mr-3'></i> Pelajari Dulu
                    </a>
                </div>
            </div>

            <div class="relative block w-full h-[450px] lg:h-full lg:min-h-[500px] mt-12 lg:mt-0">
                <div class="absolute top-10 right-10 w-64 h-64 bg-primary/20 rounded-full blur-3xl"></div>
                <div class="absolute bottom-10 left-10 w-64 h-64 bg-emerald-400/20 rounded-full blur-3xl"></div>
                
                <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full max-w-md px-4">
                    <div class="bg-gradient-to-r from-slate-800 to-slate-900 rounded-2xl p-6 shadow-2xl text-white mb-6 transform -rotate-3 hover:rotate-0 transition-transform duration-500 border border-slate-700/50 relative overflow-hidden group">
                        <div class="absolute top-0 -inset-full h-full w-1/2 z-5 block transform -skew-x-12 bg-gradient-to-r from-transparent to-white opacity-20 group-hover:animate-shine"></div>
                        
                        <div class="flex justify-between items-start mb-8">
                            <i class='bx bxs-chip text-4xl opacity-80'></i>
                            <div class="flex items-center gap-2 opacity-80">
                                <i class='bx bxs-contactless text-2xl'></i>
                                <span class="text-xs font-mono">NFC</span>
                            </div>
                        </div>
                        <p class="font-mono text-base sm:text-lg tracking-widest mb-1">8800 2024 9988 1234</p>
                        <div class="flex justify-between items-end mt-4">
                            <div>
                                <p class="text-[10px] text-slate-400 uppercase tracking-widest mb-0.5">Pemegang Kartu</p>
                                <p class="font-bold uppercase tracking-wide text-sm sm:text-base">Aditya Pratama</p>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <div class="w-6 h-6 bg-white/20 rounded flex items-center justify-center"><i class='bx bxs-cube-alt'></i></div>
                                <span class="font-bold italic text-sm">BERMADANI</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-darkCard rounded-2xl p-5 shadow-xl border border-slate-100 dark:border-slate-700 transform rotate-3 hover:rotate-0 transition-transform duration-500">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="w-10 h-10 rounded-full bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                                <i class='bx bxs-wallet'></i>
                            </div>
                            <div>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 uppercase tracking-wide font-semibold">Total Simpanan</p>
                                <h4 class="font-bold text-lg text-slate-900 dark:text-white">Rp 1.250.000</h4>
                            </div>
                        </div>
                        <div class="flex justify-between text-[10px] text-slate-400 mb-1">
                            <span>Sisa Plafon Belanja</span>
                            <span>75%</span>
                        </div>
                        <div class="w-full bg-slate-100 dark:bg-slate-700 rounded-full h-1.5">
                            <div class="bg-emerald-500 h-1.5 rounded-full" style="width: 75%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="layanan" class="py-20 bg-slate-50 dark:bg-slate-900/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="text-primary font-bold text-sm uppercase tracking-widest">Fasilitas</span>
                <h2 class="text-3xl font-bold text-slate-900 dark:text-white mt-2">Layanan Terintegrasi</h2>
                <p class="text-slate-500 mt-2 max-w-2xl mx-auto">Satu kartu keanggotaan untuk akses berbagai fasilitas di lingkungan komunitas.</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-white dark:bg-darkCard p-6 rounded-2xl shadow-sm hover:shadow-md hover:-translate-y-1 transition-all border border-slate-100 dark:border-slate-700 group">
                    <div class="w-12 h-12 bg-blue-50 dark:bg-blue-900/20 text-primary rounded-xl flex items-center justify-center text-2xl mb-4 group-hover:scale-110 transition-transform">
                        <i class='bx bx-shopping-bag'></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">Minimarket</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed">Belanja kebutuhan harian dengan harga anggota. Bayar pakai saldo simpanan atau QRIS.</p>
                </div>
                <div class="bg-white dark:bg-darkCard p-6 rounded-2xl shadow-sm hover:shadow-md hover:-translate-y-1 transition-all border border-slate-100 dark:border-slate-700 group">
                    <div class="w-12 h-12 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 rounded-xl flex items-center justify-center text-2xl mb-4 group-hover:scale-110 transition-transform">
                        <i class='bx bx-money'></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">Simpan Pinjam</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed">Kelola simpanan wajib/sukarela dan ajukan pembiayaan dengan proses yang transparan.</p>
                </div>
                <div class="bg-white dark:bg-darkCard p-6 rounded-2xl shadow-sm hover:shadow-md hover:-translate-y-1 transition-all border border-slate-100 dark:border-slate-700 group">
                    <div class="w-12 h-12 bg-amber-50 dark:bg-amber-900/20 text-amber-500 rounded-xl flex items-center justify-center text-2xl mb-4 group-hover:scale-110 transition-transform">
                        <i class='bx bx-store'></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">Titip Jual (Supplier)</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed">Program wirausaha untuk anggota dan non anggota. Titip produk Anda di minimarket dan pantau penjualannya.</p>
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-white dark:bg-darkCard border-t border-slate-200 dark:border-slate-800 py-8">
        <div class="max-w-7xl mx-auto px-4 flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-2">
                <div class="w-6 h-6 bg-slate-200 dark:bg-slate-700 rounded flex items-center justify-center text-slate-500 dark:text-slate-300">
                    <i class='bx bxs-cube-alt text-xs'></i>
                </div>
                <span class="text-sm font-bold text-slate-700 dark:text-slate-200">Bermadani</span>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400">© 2025 Koperasi & Retail Management System.</p>
        </div>
    </footer>

    <script>
    // --- VARIABLE SELECTION ---
    const html = document.documentElement;
    const themeIcon = document.getElementById('theme-icon');
    const themeBtnDesktop = document.getElementById('theme-toggle');
    const themeBtnMobile = document.getElementById('theme-toggle-mobile');
    const mobileBtn = document.getElementById('mobile-menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');

    // --- 1. INITIAL CHECK (Saat website dimuat) ---
    // Cek apakah user pernah pilih dark mode atau settingan laptopnya dark
    if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        html.classList.add('dark'); 
        if(themeIcon) themeIcon.classList.replace('bx-moon', 'bx-sun');
    } else {
        html.classList.remove('dark');
        if(themeIcon) themeIcon.classList.replace('bx-sun', 'bx-moon');
    }

    // --- 2. FUNGSI GANTI TEMA (Dipakai tombol desktop & mobile) ---
    function toggleTheme() {
        html.classList.toggle('dark');
        if(html.classList.contains('dark')) {
            if(themeIcon) themeIcon.classList.replace('bx-moon', 'bx-sun');
            localStorage.setItem('color-theme', 'dark');
        } else {
            if(themeIcon) themeIcon.classList.replace('bx-sun', 'bx-moon');
            localStorage.setItem('color-theme', 'light');
        }
    }

    // Pasang Event Listener ke Tombol Desktop
    if(themeBtnDesktop) {
        themeBtnDesktop.addEventListener('click', toggleTheme);
    }

    // Pasang Event Listener ke Tombol Mobile (Kalau ada)
    if(themeBtnMobile) {
        themeBtnMobile.addEventListener('click', toggleTheme);
    }

    // --- 3. LOGIC MOBILE MENU (Hamburger) ---
    if(mobileBtn && mobileMenu) {
        mobileBtn.addEventListener('click', () => {
            // Buka/Tutup Menu
            mobileMenu.classList.toggle('hidden');
            
            // Ganti Icon Hamburger jadi X (Silang) biar keren
            const icon = mobileBtn.querySelector('i');
            if (mobileMenu.classList.contains('hidden')) {
                icon.classList.replace('bx-x', 'bx-menu');
            } else {
                icon.classList.replace('bx-menu', 'bx-x');
            }
        });
    }
</script>
</body>
</html>
