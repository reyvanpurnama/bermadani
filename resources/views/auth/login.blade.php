<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Masuk Portal - Nexus Kampus</title>
    
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
        .animate-fade-up { animation: fadeUp 0.6s ease-out forwards; }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="bg-white dark:bg-darkBg font-sans h-screen overflow-hidden">

    <div class="w-full h-full flex">
        
        {{-- Left Side - Hero Section --}}
        <div class="hidden lg:flex w-1/2 relative items-center justify-center bg-slate-900">
            <img src="https://images.unsplash.com/photo-1541339907198-e08756dedf3f?q=80&w=2070&auto=format&fit=crop" 
                 class="absolute inset-0 w-full h-full object-cover opacity-40 mix-blend-overlay" alt="Campus Library">
            
            <div class="absolute inset-0 bg-gradient-to-t from-primary/90 to-slate-900/80"></div>

            <div class="relative z-10 p-16 text-white max-w-xl">
                <div class="mb-8 flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/20 backdrop-blur-md rounded-xl flex items-center justify-center">
                        <i class='bx bxs-cube-alt text-2xl'></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold tracking-tight leading-none">NEXUS</h1>
                        <p class="text-[10px] uppercase tracking-widest font-semibold opacity-80">Commerce System</p>
                    </div>
                </div>
                
                <h2 class="text-4xl font-bold mb-6 leading-tight">
                    "Satu Kartu, <br> <span class="text-yellow-400">Ribuan Kemudahan.</span>"
                </h2>
                <p class="text-blue-100 text-lg leading-relaxed mb-8">
                    Nikmati kemudahan transaksi di minimarket kampus dan akses layanan simpan pinjam koperasi dalam satu genggaman.
                </p>

                <div class="flex gap-6">
                    <div class="flex items-center gap-2 text-sm font-medium opacity-80">
                        <i class='bx bxs-check-shield text-xl'></i> Aman
                    </div>
                    <div class="flex items-center gap-2 text-sm font-medium opacity-80">
                        <i class='bx bxs-zap text-xl'></i> Cepat
                    </div>
                    <div class="flex items-center gap-2 text-sm font-medium opacity-80">
                        <i class='bx bxs-data text-xl'></i> Transparan
                    </div>
                </div>
            </div>
            
            <div class="absolute bottom-8 left-16 text-xs text-white/40">
                &copy; 2025 Digital Campus Initiative
            </div>
        </div>

        {{-- Right Side - Login Form --}}
        <div class="w-full lg:w-1/2 flex flex-col justify-center items-center p-8 bg-white dark:bg-darkBg relative">
            
            <a href="{{ route('home') }}" class="absolute top-8 left-8 flex items-center gap-2 text-sm text-slate-500 hover:text-primary transition-colors font-medium">
                <i class='bx bx-arrow-back'></i> Kembali
            </a>

            <button id="theme-toggle" class="absolute top-8 right-8 p-2 text-slate-400 hover:text-slate-600 dark:hover:text-white transition-colors">
                <i id="theme-icon" class='bx bx-moon text-2xl'></i>
            </button>

            <div class="w-full max-w-sm animate-fade-up">
                
                <div class="text-center mb-10">
                    <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-blue-50 dark:bg-blue-900/20 text-primary mb-4 shadow-sm">
                        <i class='bx bxs-user-circle text-3xl'></i>
                    </div>
                    <h1 class="text-2xl font-bold text-slate-900 dark:text-white mb-2">Portal Anggota</h1>
                    <p class="text-slate-500 dark:text-slate-400 text-sm">Masuk untuk mengakses layanan.</p>
                </div>

                @if ($errors->any())
                    <div class="bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800 rounded-xl p-4 mb-6">
                        <div class="flex items-center gap-2 text-rose-600 dark:text-rose-400">
                            <i class='bx bx-error-circle text-xl'></i>
                            <span class="text-sm font-medium">{{ $errors->first() }}</span>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf
                    
                    <div>
                        <label for="email" class="block mb-2 text-sm font-bold text-slate-700 dark:text-slate-300">
                            ID Pengguna
                        </label>
                        <div class="relative group">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400 transition-colors group-focus-within:text-primary">
                                <i class='bx bxs-id-card text-xl'></i>
                            </span>
                            <input type="email" 
                                   id="email"
                                   name="email" 
                                   value="{{ old('email') }}"
                                   class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-sm rounded-xl focus:ring-2 focus:ring-primary/50 focus:border-primary block pl-11 p-3.5 outline-none transition-all placeholder-slate-400" 
                                   placeholder="username atau email..." 
                                   required
                                   autofocus>
                        </div>
                    </div>

                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <label for="password" class="block text-sm font-bold text-slate-700 dark:text-slate-300">Kata Sandi</label>
                            <a href="#" class="text-xs font-semibold text-primary hover:underline">Lupa sandi?</a>
                        </div>
                        <div class="relative" x-data="{ show: false }">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                                <i class='bx bxs-lock-alt text-xl'></i>
                            </span>
                            <input :type="show ? 'text' : 'password'" 
                                   id="password"
                                   name="password" 
                                   class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-sm rounded-xl focus:ring-2 focus:ring-primary/50 focus:border-primary block pl-11 p-3.5 outline-none transition-all placeholder-slate-400" 
                                   placeholder="••••••••" 
                                   required>
                            <span @click="show = !show" class="absolute inset-y-0 right-0 flex items-center pr-4 cursor-pointer text-slate-400 hover:text-slate-600 transition-colors">
                                <i class='bx text-xl' :class="show ? 'bx-show' : 'bx-hide'"></i>
                            </span>
                        </div>
                    </div>

                    <button type="submit" class="w-full text-white bg-primary hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-bold rounded-xl text-sm px-5 py-4 text-center shadow-lg shadow-blue-500/20 transition-all hover:-translate-y-0.5 flex items-center justify-center gap-2 group">
                        Masuk Sistem 
                        <i class='bx bx-right-arrow-alt text-lg group-hover:translate-x-1 transition-transform'></i>
                    </button>

                </form>

                <div class="mt-8 pt-6 border-t border-slate-100 dark:border-slate-800 text-center space-y-3">
                    <p class="text-xs text-slate-400">
                        Belum punya akun? <a href="#" class="text-primary font-bold hover:underline">Daftar Anggota Baru</a>
                    </p>
                    <div class="flex justify-center gap-4">
                        <a href="#" class="text-[11px] font-semibold text-slate-500 hover:text-slate-800 dark:hover:text-white transition-colors">Panduan</a>
                        <span class="text-slate-300">•</span>
                        <a href="#" class="text-[11px] font-semibold text-slate-500 hover:text-slate-800 dark:hover:text-white transition-colors">Privasi</a>
                        <span class="text-slate-300">•</span>
                        <a href="#" class="text-[11px] font-semibold text-slate-500 hover:text-slate-800 dark:hover:text-white transition-colors">Bantuan</a>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script>
        // Dark Mode Logic
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
